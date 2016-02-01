<?php

/**
 * Class HB_Customer
 */
class HB_Customer{

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
     * Order id
     *
     * @var int
     */
    public $id = 0;

    /**
     * Order Status
     *
     * @var string
     */
    public $post_status                 = '';

    /**
     * Constructor
     *
     * @param $post
     */
    function __construct( $post ){
        if ( is_numeric( $post ) && $post && get_post_type( $post ) == 'hb_customer') {
            $this->post = get_post( $post );
        } else if( $post instanceof WP_Post || is_object( $post ) ){
            $this->post = $post;
        }

        if ( $this->post ) {
            $this->id = $this->post->ID;
        }
    }

    function __get( $key ){
        if( ! isset( $this->{$key} ) ){
            return get_post_meta( $this->id, '_hb_' . $key, true );
        }
    }

    function get( $meta_key = null, $default = null, $unique = true ){
        if ( ! $meta_key || ! $this->id ) {
            return $default;
        }
        return get_post_meta( $this->id, $meta_key, $unique );
    }

    /**
     * Get an instance of HB_Customer by post ID or WP_Post object
     *
     * @param $booking
     * @return HB_Customer
     */
    static function instance( $customer ){
        $post = $customer;
        if( $customer instanceof WP_Post ){
            $id = $customer->ID;
        }elseif( is_object( $customer ) && isset( $customer->ID ) ){
            $id = $customer->ID;
        }else{
            $id = $customer;
        }
        if( empty( self::$_instance[ $id ] ) ){
            self::$_instance[ $id ] = new self( $post );
        }
        return self::$_instance[ $id ];
    }
}