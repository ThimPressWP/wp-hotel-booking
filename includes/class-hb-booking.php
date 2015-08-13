<?php

/**
 * Class HB_Booking
 */
class HB_Booking{

    /**
     * @var array
     */
    protected static $_instance = array();

    /**
     * Store post object
     *
     * @var WP_Post
     */
    public $post = null;

    /**
     * @var null
     */
    private $_customer = null;

    /**
     * @var array
     */
    private $_booking_info = array();

    /**
     * Constructor
     *
     * @param $post
     */
    function __construct( $post ){
        if( is_numeric( $post ) && $post && get_post_type( $post ) == 'hb_booking') {
            $this->post = get_post( $post );
        }elseif( $post instanceof WP_Post || is_object( $post ) ){
            $this->post = $post;
        }
        if( empty( $this->post ) ){
            $this->post = hb_create_empty_post();
        }

        if( ! empty( $this->post->ID ) ){
            $this->load_customer();
        }
    }

    /**
     * Load customer meta data
     *
     * @access private
     */
    private function load_customer(){
        $customer_id = get_post_meta( $this->post->ID, '_hb_customer_id', true );
        $this->_customer = get_post( $customer_id );
        if( $this->_customer && $this->_customer->ID ){
            $customer_data = get_post_meta( $this->_customer->ID );
            $this->_customer->data = $customer_data;
        }
    }

    /**
     * Set customer meta data
     *
     * @param $customer
     * @return null|object|stdClass
     */
    function set_customer( $customer ){
        if( empty( $this->_customer ) ){
            $this->_customer = hb_create_empty_post();
        }
        if( is_numeric( $customer ) ){
            $this->_customer = get_post( intval( $customer ) );
        }else{
            if( func_num_args() > 1 ){
                $this->_customer->{$customer} = func_get_arg(1);
            }else {
                $this->_customer = (object)$customer;
            }
        }
        return $this->_customer;
    }

    /**
     * Set booking information
     *
     * @param $info
     */
    function set_booking_info( $info ){
        if( func_num_args() > 1 ){
            $this->_booking_info[ $info ] = func_get_arg(1);
        }else {
            $this->_booking_info = (array)$info;
        }
    }

    /**
     * Update booking and relevant data
     *
     * @return mixed
     */
    function update(){
        $post_data = get_object_vars($this->post);

        // ensure the post_type is correct
        $post_data['post_type']     = 'hb_booking';
        $post_data['post_status']   = 'publish';
        if ($this->post->ID) {
            $booking_id = wp_update_post($post_data);
        } else {
            $booking_id = wp_insert_post($post_data, true);
            $this->post->ID = $booking_id;
        }
        if( $booking_id ){
            //update_post_meta( $booking_id, '_hb_customer_id', $customer_id );
            foreach( $this->_booking_info as $meta_key => $v ){
                update_post_meta( $booking_id, $meta_key, $v );
            }
        }
        return $this->post->ID;
    }

    /**
     * Get an instance of HB_Booking by post ID or WP_Post object
     *
     * @param $booking
     * @return HB_Booking
     */
    static function instance( $booking ){
        $post = $booking;
        if( $booking instanceof WP_Post ){
            $id = $booking->ID;
        }elseif( is_object( $booking ) && isset( $booking->ID ) ){
            $id = $booking->ID;
        }else{
            $id = $booking;
        }
        if( empty( self::$_instance[ $id ] ) ){
            self::$_instance[ $id ] = new self( $post );
        }
        return self::$_instance[ $id ];
    }
}