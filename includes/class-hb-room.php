<?php

/**
 * Class HB_Room
 */
class HB_Room{
    /**
     * @var array
     */
    protected static $_instance = array();

    /**
     * @var null
     */
    protected $_plans = null;

    /**
     * @var null|WP_Post
     */
    public $post = null;

    /**
     * @var array
     */
    protected $_external_data = array();

    /**
     * @var int
     */
    protected $_room_details_total = 0;

    /**
    * @return setting
    */
    protected $_settings;

    /**
     * Constructor
     *
     * @param $post
     */
    function __construct( $post ){
        if( is_numeric( $post ) && $post && get_post_type( $post ) == 'hb_room') {
            $this->post = get_post( $post );
        }elseif( $post instanceof WP_Post || is_object( $post ) ){
            $this->post = $post;
        }
        if( empty( $this->post ) ){
            $this->post = hb_create_empty_post();
        }
        global $hb_settings;
        if( ! $this->_settings )
            $this->_settings = $hb_settings;
    }

    /**
     * Set extra data form room
     *
     * @param $key
     * @param null $value
     * @return $this
     */
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

    /**
     * Get extra data of room
     *
     * @param $key
     * @return bool
     */
    function get_data( $key ){
        return ! empty( $this->_external_data[ $key ] ) ? $this->_external_data[ $key ] : false;
    }

    /**
     * Magic function to get a variable of room
     *
     * @param $key
     * @return int|string
     */
    function __get( $key ){
        static $fields = array();
        $return = '';
        switch( $key ){
            case 'room_type':
                // $return = intval( get_post_meta( $this->post->ID, '_hb_room_type', true ) );
                $terms = get_the_terms( $this->post->ID, 'hb_room_type' );
                $return = array();
                foreach ($terms as $key => $term) {
                    $return[] = $term->term_id;
                }
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
            case 'addition_information':
                $return = get_post_meta( $this->post->ID, '_hb_room_addition_information', true );
                break;
            case 'thumbnail':
                if( has_post_thumbnail( $this->post->ID ) ){
                    $return = get_the_post_thumbnail( $this->post->ID, 'thumbnail' );
                }else{
                    $gallery = get_post_meta( $this->post->ID, '_hb_gallery', true );
                    if( $gallery )
                    {
                        $attachment_id = array_shift($gallery);
                        $return = wp_get_attachment_image( $attachment_id, 'thumbnail' );
                    }
                    else
                    {
                        $return = '<img src="'.HB_PLUGIN_URL . '/includes/carousel/default.png'.'" alt="'.$this->post->post_title.'"/>';
                    }
                }
                break;
            case 'gallery':
                $return = $this->get_galleries();
                break;
            case 'max_child':
                $return = get_post_meta( $this->post->ID, '_hb_max_child_per_room', true );
                break;

            case 'dropdown_room':
                $max_rooms = get_post_meta( $this->post->ID, '_hb_num_of_rooms', true );
                $return = '<select name="hb-num-of-rooms[' . $this->post->ID . ']">';
                $return .= '<option value="0">' . __( 'Select', 'tp-hotel-booking' ) . '</option>';
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

    function get_galleries( $with_featured = true ){
        $gallery = array();
        if( $with_featured && $thumb_id = get_post_thumbnail_id( $this->post->ID ) ) {
            $featured_thumb = wp_get_attachment_image_src( $thumb_id, 'thumbnail' );
            $featured_full = wp_get_attachment_image_src( $thumb_id, 'full' );
            $alt = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
            $gallery[] = array(
                'id'    => $thumb_id,
                'src'   => $featured_full[0],
                'thumb' => $featured_thumb[0],
                'alt'   => $alt ? $alt : get_the_title( $thumb_id )
            );
        }

        $galleries = get_post_meta( $this->post->ID, '_hb_gallery', true );
        if( ! $galleries )
            return $gallery;

        foreach( $galleries as $thumb_id ){
            $alt = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );

            $w = $this->_settings->get('room_thumbnail_width', 150);
            $h = $this->_settings->get('room_thumbnail_width', 150);

            $size = apply_filters( 'hotel_booking_room_thumbnail_size', array( 'width' => $w, 'height' => $h ) );
            $thumb = $this->renderImage( $thumb_id, $size);

            $w = $this->_settings->get('room_image_gallery_width', 1000);
            $h = $this->_settings->get('room_image_gallery_height', 667);
            $size = apply_filters( 'hotel_booking_room_gallery_size', array( 'width' => $w, 'height' => $h ) );
            $full = $this->renderImage( $thumb_id, $size);;
            $alt = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
            $gallery[] = array(
                'id'    => $thumb_id,
                'src'   => $full,
                'thumb' => $thumb,
                'alt'   => $alt ? $alt : get_the_title( $thumb_id )
            );
        }
        return $gallery;
    }

    /**
     * @return array
     */
    function get_booking_room_details(){
        $details = array();
        $room_details_total = 0;
        $start_date = $this->get_data( 'check_in_date' );
        $end_date = $this->get_data( 'check_out_date' );

        $start_date_to_time = strtotime( $start_date );
        $end_date_to_time = strtotime( $end_date );

        $nights = hb_count_nights_two_dates( $end_date, $start_date );
        for( $i = 0; $i < $nights; $i++ ){
            $c_date = $start_date_to_time + $i * DAY_IN_SECONDS;
            $date = date('w', $c_date );
            if( ! isset( $details[ $date ] ) ){
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

    /**
     * Get room price based on plan settings
     *
     * @param null $date
     * @param bool $including_tax
     * @return float
     */
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

    /**
     * Get total price of room
     *
     * @param $from
     * @param $to
     * @param int $num_of_rooms
     * @param bool $including_tax
     * @return float|int
     */
    function get_total( $from = null, $to = null, $num_of_rooms = 1, $including_tax = true ){
        $nights = 0;
        $total = 0;
        if( is_null( $from ) && is_null( $to ) ){
            $to_time = intval( $this->get_data( 'check_out_date' ) );
            $from_time = intval( $this->get_data( 'check_in_date' ) );
        }else {
            if (!is_numeric($from)) {
                $from_time = strtotime($from);
            } else {
                $from_time = $from;
            }
            if (!is_numeric($to)) {
                $to_time = strtotime($to);
            } else {
                if ($to >= DAY_IN_SECONDS) {
                    $to_time = $to;
                } else {
                    $nights = $to;
                }
            }
        }
        if( ! $num_of_rooms ){
            $num_of_rooms = intval( $this->get_data( 'num_of_rooms' ) );
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

    /**
     * Get list of pricing plan of this room type
     * @return null
     */
    function get_pricing_plans(){
        if( ! $this->_plans ) {
            $plans = get_posts(
                array(
                    'post_type' => 'hb_pricing_plan',
                    'posts_per_page' => 9999,
                    'meta_query' => array(
                        array(
                            'key' => '_hb_pricing_plan_room',
                            'value' => $this->post->ID
                        )
                    )
                )
            );
            $this->_plans = $plans;
        }
        return $this->_plans;
    }

    function get_related_rooms()
    {
        $room_types = get_the_terms( $this->post->ID, 'hb_room_type' );
        $room_capacity = (int)get_post_meta( $this->post->ID, '_hb_room_capacity', true );
        $max_adults_per_room = (int)get_post_meta( $this->post->ID, '_hb_max_adults_per_room', true );
        $max_child_per_room = (int)get_post_meta( $this->post->ID, '_hb_max_child_per_room', true );

        $taxonomis = array();
        foreach ($room_types as $key => $tax) {
            $taxonomis[] = $tax->term_id;
        }
        $args = array(
                'post_type'     => 'hb_room',
                'status'        => 'publish',
                'meta_query'    => array(
                        array(
                            'key'       => '_hb_max_adults_per_room',
                            'value'     => $max_adults_per_room,
                            'compare'   => '>=',
                        ),
                        array(
                            'key'       => '_hb_max_child_per_room',
                            'value'     => $max_child_per_room,
                            'compare'   => '>='
                        ),
                    ),
                'tax_query' => array(
                        array(
                            'taxonomy' => 'hb_room_type',
                            'field'    => 'term_id',
                            'terms'    => $taxonomis
                        ),
                    ),
                'post__not_in'  => array( $this->post->ID )
            );
        $query = new WP_Query( $args );
        wp_reset_postdata();
        return $query;
    }

    /**
     * Get reviews count for a room
     *
     * @return mixed
     */
    function get_review_count() {
        global $wpdb;
        $transient_name = rand().'hb_review_count_' . $this->post->ID;
        if ( false === ( $count = get_transient( $transient_name ) ) ) {
            $count = $wpdb->get_var( $wpdb->prepare("
				SELECT COUNT(*) FROM $wpdb->comments
				WHERE comment_parent = 0
				AND comment_post_ID = %d
				AND comment_approved = '1'
			", $this->post->ID ) );

            //set_transient( $transient_name, $count, DAY_IN_SECONDS * 30 );
        }

        return apply_filters( 'hb_room_review_count', $count, $this );
    }

    function getImage( $type = 'catalog', $attachID = false, $echo = true )
    {
        if( $type === 'catalog' )
        {
            return $this->get_catalog( $attachID = false, $echo = true );
        }
        return $this->get_thumbnail( $attachID = false, $echo = true );
    }

    /**
    * get thumbnail
    * @return html or array atts
    */
    function get_thumbnail( $attachID = false, $echo = true )
    {
        $w = $this->_settings->get('room_thumbnail_width', 150);
        $h = $this->_settings->get('room_thumbnail_height', 150);

        $size = apply_filters( 'hotel_booking_room_thumbnail_size', array( 'width' => $w, 'height' => $h ) );

        if( $attachID == false )
            $attachID = get_post_thumbnail_id( $this->post->ID );

        $image = $this->renderImage( $attachID, $size, false );

        if( $echo && $image )
        {
            if( is_array($image) )
            {
                echo sprintf('<img src="%1$s" width="%2$s" height="%3$s" />', $image[0], $image[1], $image[2]);
            }
            else
            {
                sprintf('<img src="%1$s" width="%2$s" height="%3$s" />', $image, $w, $h);
            }
        }
        else
        {
            return $image;
        }
    }

    function get_catalog( $attachID = false, $echo = true )
    {
        $w = $this->_settings->get('catalog_image_width', 270);
        $h = $this->_settings->get('catalog_image_height', 270);

        $size = apply_filters( 'hotel_booking_room_gallery_size', array( 'width' => $w, 'height' => $h ) );

        if( $attachID == false )
            $attachID = get_post_thumbnail_id( $this->post->ID );

        $image = $this->renderImage( $attachID, $size, false );

        if( $echo && $image )
        {
            if( is_array($image) )
            {
                echo sprintf('<img src="%1$s" width="%2$s" height="%3$s" />', $image[0], $image[1], $image[2]);
            }
            else
            {
                sprintf('<img src="%1$s" width="%2$s" height="%3$s" />', $image, $w, $h);
            }
        }
        else
        {
            return $image;
        }
    }

    function renderImage( $attachID = null, $size = array(), $src = true )
    {
        $resizer = HB_Reizer::getInstance();

        return $image = $resizer->process(
                $attachID,
                $size,
                $src
            );
    }

    static function hb_setup_room_data( $post )
    {
        unset( $GLOBALS['hb_room'] );

        if ( is_int( $post ) )
            $post = get_post( $post );

        if( ! $post )
            $post = $GLOBALS['post'];

        if ( empty( $post->post_type ) || ! in_array( $post->post_type, array( 'hb_room' ) ) )
            return;

        return $GLOBALS['hb_room'] = HB_Room::instance($post);
    }

    /**
     * Get unique instance of HB_Room
     *
     * @param $room
     * @return mixed
     */
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
