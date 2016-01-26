<?php
/**
 * Class HB_Room
 */
class HB_Room extends HB_Product_Room_Base
{
    /**
     * @var array
     */
    protected static $_instance = array();

    /**
     * @var null|WP_Post
     */
    public $post = null;

    /**
    * reivew detail
    * @return null or array
    */
    public $_review_details = null;

    /**
     * Constructor
     *
     * @param $post
     */
    function __construct( $post, $options = null ) {
        if( is_numeric( $post ) && $post && get_post_type( $post ) == 'hb_room' ) {
            $this->post = get_post( $post );
        }else if ( $post instanceof WP_Post || is_object( $post ) ){
            $this->post = $post;
        }
        if ( empty( $this->post ) ) {
            $this->post = hb_create_empty_post();
        }
        global $hb_settings;
        if( ! $this->_settings )
            $this->_settings = $hb_settings;

        if( $options )
            $this->set_data( $options );

        parent::__construct( $this->post->ID, $options );

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

    // total include tax
    function amount_include_tax( $qty = 0 ) {
        return apply_filters( 'hotel_booking_cart_room_item_total_include_tax', $this->total_tax, $this );
    }

    // total exclude tax
    function amount_exclude_tax( $qty = 0 ) {
        return apply_filters( 'hotel_booking_cart_room_item_total_exclude_tax', $this->total, $this );
    }

    function amount( $qty = 0 ) {
        $amount = hb_price_including_tax() ? $this->amount_include_tax( $qty ) : $this->amount_exclude_tax( $qty );
        return apply_filters( 'hotel_booking_cart_room_item_amount', $amount, $this );
    }

    function amount_singular_exclude_tax()
    {
        return apply_filters( 'hotel_booking_room_singular_total_exclude_tax', $this->amount_singular_exclude_tax, $this );
    }

    function amount_singular_include_tax()
    {
        return apply_filters( 'hotel_booking_room_singular_total_include_tax', $this->amount_singular_include_tax, $this );
    }

    function amount_singular()
    {
        $amount = hb_price_including_tax() ? $this->amount_singular_include_tax() : $this->amount_singular_exclude_tax();
        return apply_filters( 'hotel_booking_room_amount_singular', $amount, $this );
    }

    /**
     * Get unique instance of HB_Room
     *
     * @param $room
     * @return mixed
     */
    static function instance( $room, $options = null ){
        $post = $room;
        if( $room instanceof WP_Post ){
            $id = $room->ID;
        }elseif( is_object( $room ) && isset( $room->ID ) ){
            $id = $room->ID;
        }else{
            $id = $room;
        }

        if( empty( self::$_instance[ $id ] ) ){
            return self::$_instance[ $id ] = new self( $post, $options );
        }
        else
        {
            $room = self::$_instance[ $id ];

            if( isset($options['check_in_date'], $options['check_out_date'])
                && ( ($options['check_in_date'] !== $room->check_in_date) || ($options['check_out_date'] !== $room->check_out_date) )
                || $room->quantity === false || ( ! isset($options['quantity']) || $room->quantity != $options['quantity'] )
                || ( ! isset($options['extra_packages']) || $options['extra_packages'] != $room->extra_packages )
            )
            {
                return new self( $post, $options );
            }
        }
        return self::$_instance[ $id ];
    }

}
