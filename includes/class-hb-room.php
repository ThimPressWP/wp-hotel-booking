<?php
class HB_Room{
    protected static $_instance = array();
    protected $_plans = null;
    public $post = null;
    protected $_external_data = array();
    protected $_room_details_total = 0;

    function __construct( $post ){
        if( is_numeric( $post ) ) {
            $this->post = get_post( $post );
        }elseif( $post instanceof WP_Post || is_object( $post ) ){
            $this->post = $post;
        }
    }

    function set_data( $key, $value = null ){
        if( is_array( $key ) ){
            foreach( $key as $k => $v ){
                $this->set_data( $k, $v );
            }
        }else {
            $this->_external_data[$key] = $value;
        }
        return $this;
    }

    function get_data( $key ){
        return ! empty( $this->_external_data[ $key ] ) ? $this->_external_data[ $key ] : false;
    }

    function __get( $key ){
        static $fields = array();
        $return = '';
        switch( $key ){
            case 'room_type':
                $return = intval( get_post_meta( $this->post->ID, '_hb_room_type', true ) );
                break;
            case 'name':
                $return = get_the_title( $this->post->ID );
                break;
            case 'capacity':
            case 'capacity_title':
                $term_id = get_post_meta( $this->post->ID, '_hb_room_capacity', true );
                if( $key == 'capacity_title' ) {
                    $term = get_term( $term_id, 'hb_room_capacity' );
                    $return = $term->name;
                }else{
                    $return = get_option( 'hb_taxonomy_capacity_' . $term_id );
                }
                break;
            case 'capacity_id':
                $return = get_post_meta( $this->post->ID, '_hb_room_capacity', true );
                break;
            case 'thumbnail':
                if( has_post_thumbnail( $this->post->ID ) ){
                    $return = get_the_post_thumbnail( $this->post->ID, 'thumbnail' );
                }else{
                    $return = '<img src="" />';
                }
                break;
            case 'max_child':
                $return = get_post_meta( $this->post->ID, '_hb_max_child_per_room', true );
                break;

            case 'dropdown_room':
                $max_rooms = get_post_meta( $this->post->ID, '_hb_num_of_rooms', true );
                $return = '<select name="hb-num-of-rooms[' . $this->post->ID . ']">';
                $return .= '<option value="0">' . __( '--Select--', 'tp-hotel-booking' ) . '</option>';
                for( $i = 1; $i <= $max_rooms; $i++ ){
                    $return .= sprintf( '<option value="%1$d">%1$d</option>', $i );
                }
                $return .= '</select>';
                break;
            case 'num_of_rooms':
                $return = $this->get_data( 'num_of_rooms' );
                break;
            case 'room_details_total':
                $return = $this->_room_details_total;
                break;
            case 'price_table':
                $return = 'why i am here?';
        }
        return $return;
    }
    function get_booking_room_details(){
        $details = array();
        $room_details_total = 0;
        $start_date = $this->get_data( 'check_in_date' );
        $end_date = $this->get_data( 'check_out_date' );

        $start_date_to_time = strtotime( $start_date );
        $end_date_to_time = strtotime( $end_date );

        $nights = hb_count_nights_two_dates( $end_date, $start_date );
        for( $i = 0; $i < $nights; $i++ ){
            $c_date = $start_date_to_time + $i * DAY_IN_SECONDS * 24;
            $date = date('w', $c_date );
            if( ! $details[ $date ] ){
                $details[ $date ] = array(
                    'count' => 0,
                    'price' => 0
                );
            }
            $details[ $date ]['count'] ++;
            $details[ $date ]['price'] = $this->get_total( $c_date, 1, 1 );
            $room_details_total +=  $details[ $date ]['count'] * $details[ $date ]['price'];
        }
        $this->_room_details_total = $room_details_total;
        return $details;
    }
    function get_price( $date = null, $including_tax = true ){
        $tax = 0;
        if( $including_tax ){
            $settings = HB_Settings::instance();
            if( ! $settings->get( 'price_including_tax' ) ) {
                $tax = $settings->get('tax');
                $tax = $tax / 100;
            }
        }
        if( ! $date ) $date = time();
        elseif( is_string( $date ) ){
            $date = @strtotime( $date );
        }
        //echo "[get_price=" . date( 'w', $date ) . "]";

        $plans = $this->get_pricing_plans();
        if( ! $plans ) $return = '';
        if( sizeof( $plans ) == 1 ){
            $regular_plan = $plans[0];
        }else{
            $regular_plan = array_pop( $plans );
        }
        $selected_plan = null;
        if( $plans ){
            foreach( $plans as $plan ){
                $start_plan = get_post_meta( $plan->ID, '_hb_pricing_plan_start', true );
                $end_plan = get_post_meta( $plan->ID, '_hb_pricing_plan_end', true );
                $start_time_plan = @strtotime( $start_plan );
                $end_time_plan = @strtotime( $end_plan );
                if( $date >= $start_time_plan && $date <= $end_time_plan ){
                    $selected_plan = $plan;
                    break;
                }
            }
        }
        if( ! $selected_plan ){
            $selected_plan = $regular_plan;
        }
        if( $selected_plan ){
            $prices = get_post_meta( $selected_plan->ID, '_hb_pricing_plan_prices', true );
            if( $prices ){
                $return = $prices[ $this->capacity_id ][ date( 'w', $date ) ];
                $return = $return + $return * $tax;
            }
        }
        //print_r($prices);
        return floatval( $return );
    }
    function get_total( $from, $to, $num_of_rooms = 1, $including_tax = true ){
        $nights = 0;
        $total = 0;
        if( ! is_numeric( $from ) ){
            $from_time = strtotime( $from );
        }else{
            $from_time = $from;
        }
        if( ! is_numeric( $to ) ){
            $to_time = strtotime( $to );
        }else{
            if( $to >= DAY_IN_SECONDS ){
                $to_time = $to;
            }else{
                $nights = $to;
            }
        }
        if( ! $nights ){
            $nights = hb_count_nights_two_dates( $to_time, $from_time );
        }
        $from = mktime( 0, 0, 0, date( 'm', $from_time ), date( 'd', $from_time ), date( 'Y', $from_time ) );
        for( $i = 0; $i < $nights; $i++ ){
            $total_per_night = $this->get_price( $from + $i * DAY_IN_SECONDS, $including_tax );
            $total += $total_per_night * $num_of_rooms;
        }
        return $total;
    }
    function get_pricing_plans(){
        if( ! $this->_plans ) {
            $plans = get_posts(
                array(
                    'post_type' => 'hb_pricing_plan',
                    'posts_per_page' => 9999,
                    'meta_query' => array(
                        array(
                            'key' => '_hb_pricing_plan_room',
                            'value' => $this->room_type
                        )
                    )
                )
            );
            $this->_plans = $plans;
        }
        return $this->_plans;
    }

    static function instance( $room ){
        $post = $room;
        if( $room instanceof WP_Post ){
            $id = $room->ID;
        }elseif( is_object( $room ) && isset( $room->ID ) ){
            $id = $room->ID;
        }else{
            $id = $room;
        }
        if( empty( self::$_instance[ $id ] ) ){
            self::$_instance[ $id ] = new self( $post );
        }
        return self::$_instance[ $id ];
    }
}