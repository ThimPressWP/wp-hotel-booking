<?php

/**
 * Class HB_Cart
 *
 * Simple Cart object for now. Maybe need to expand later
 */
class HB_Cart{
    /**
     * @var bool
     */
    private static $instance = false;

    protected $_options = array();

    /**
     * Construction
     */
    function __construct(){
        if( self::$instance ) return;

        if( !session_id() ) session_start();
        if( empty( $_SESSION['hb_cart'] ) ){
            $_SESSION['hb_cart'] = array(
                'cart_id'   => $this->generate_cart_id(),
                'products'  => array()
            );
        }
    }

    /**
     * Magic function for get a property
     *
     * @param string
     * @return mixed
     */
    function __get( $key ){
        $value = null;
        switch( $key ){
            case 'total_rooms':
                $value = $this->get_total_rooms();
                break;
            case 'total_nights':
                $value = $this->get_total_nights();
                break;
            case 'check_in_date':
            case 'check_out_date':
                $value = $this->get_option( $key );
                break;
            case 'sub_total':
                $value = $this->get_sub_total();
                break;
            case 'total':
                $value = $this->get_total();
        }
        return $value;
    }

    function set_option( $name, $value = null ){
        if( is_array( $name ) ){
            foreach( $name as $k => $v ){
                $this->set_option( $k, $v );
            }
        }else {
            $this->_options[$name] = $value;
        }
        return $this;
    }

    /**
     * Get total rooms
     *
     * @return int
     */
    function get_total_rooms(){
        $total_rooms = 0;
        if( $rooms = $this->get_products() ){
            foreach( $rooms as $id => $num_of_rooms ){
                $total_rooms += intval( $num_of_rooms );
            }
        }
        return apply_filters( 'hb_cart_total_rooms', $total_rooms );
    }

    /**
     * Get total nights
     *
     * @return int
     */
    function get_total_nights(){
        $start_date = $this->get_option( 'check_in_date' );
        $end_date = $this->get_option( 'check_out_date' );
        $total_nights = hb_count_nights_two_dates( $end_date, $start_date );
        return apply_filters( 'hb_cart_total_nights', $total_nights );
    }

    function get_option( $name ){
        return ! empty( $this->_options[ $name ] ) ? $this->_options[ $name ] : false;
    }

    function get_cart_id(){
        return $_SESSION['hb_cart']['cart_id'];
    }

    function get_products(){
        return $_SESSION['hb_cart']['products'];
    }

    function get_sub_total(){
        $sub_total = 0;
        if( $rooms = $this->get_rooms() ) foreach( $rooms as $room_id => $room ) {
            $sub_total += $room->get_total( $this->check_in_date, $room->check_out_date, $room->get_data( 'num_of_rooms' ), false );
        }

        return apply_filters( 'hb_cart_sub_total', $sub_total );
        $total_nights = hb_count_nights_two_dates( $end_date, $start_date );
        $tax = hb_get_tax_settings();
        if( $tax > 0 ) {
            $grand_total = $total + $total * $tax;
        }else{
            $grand_total = $total;
        }

        $sub_total = 0;
        $products = $this->get_products();
        if( $products ) foreach( $products as $product ){
            $sub_total += learn_press_is_free_course( $product['id'] ) ? 0 : floatval( learn_press_get_course_price( $product['id'] ) );
        }
        learn_press_format_price( $sub_total );
        return apply_filters( 'learn_press_get_cart_subtotal', $sub_total, $this->get_cart_id() );
    }

    function get_total(){
        $sub_total  = $this->get_sub_total();
        $total      = $sub_total;
        return apply_filters( 'learn_press_get_cart_total', $total, $this->get_cart_id() );
    }

    function get_rooms( ){
        $rooms = array();
        if( $_rooms = $this->get_products() ){

            foreach( $_rooms as $room ){
                $rooms[ $room['id'] ] = HB_Room::instance( $room['id'] );
                $rooms[ $room['id'] ]->set_data( 'num_of_rooms', $room['quantity'] );
            }
        }
        return $rooms;
    }

    function generate_cart_id(){
        return md5( time() );
    }

    function add_to_cart( $room_id, $quantity = 1 ){
        $price = 6.9;
        $_SESSION['hb_cart']['products'][$room_id] = array(
            'id'        => $room_id,
            'quantity'  => $quantity,
            'price'     => $price
        );
        return $this;
    }

    function empty_cart(){
        unset( $_SESSION['hb_cart']['products'] );
        return $this;
    }

    function destroy(){
        unset( $_SESSION['hb_cart'] );
    }

    static function instance( $prop = false, $args = false ){
        if( !self::$instance ){
            self::$instance = new self();
        }
        $ins = self::$instance;
        if( $prop ) {
            $prop = 'get_' . $prop;
        }
        return $prop && is_callable( array( $ins, $prop ) ) ? call_user_func_array( array( $ins, $prop ), (array)$args ) : $ins;
    }
}
if( !is_admin() ) {
    $GLOBALS['hb_cart'] = HB_Cart::instance();
}

function hb_get_cart( $prop = null ){
    return HB_Cart::instance( $prop );
}


function hb_get_cart_total(){
    return 999.99;
}

function hb_generate_transaction_object(){
    return new stdClass();
}

function hb_uniqid(){
    $hash = str_replace( '.', '', microtime( true ) . uniqid() );
    return apply_filters( 'hb_generate_unique_hash', $hash );
}

function hb_get_cart_description(){
    return 'Hotel Booking';
}


function hb_get_return_url(){
    $url = '';
    return apply_filters( 'hb_return_url', $url );
}
