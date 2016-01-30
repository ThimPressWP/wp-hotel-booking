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
    public $_customer = null;

    /**
     * @var array
     */
    private $_booking_info = array();

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
        if ( is_numeric( $post ) && $post && get_post_type( $post ) == 'hb_booking') {
            $this->post = get_post( $post );
        } else if( $post instanceof WP_Post || is_object( $post ) ){
            $this->post = $post;
        }
        if ( empty( $this->post ) ){
            $this->post = hb_create_empty_post( array( 'post_status' => 'hb-pending' ) );
        }

        $this->id = $this->post->ID;
        if ( $this->id ) {
            $this->_customer = $this->load_customer();
        }
    }

    function __get( $key ){
        if( ! isset( $this->{$key} ) ){
            return get_post_meta( $this->id, '_hb_' . $key, true );
        }
    }

    /**
     * Load customer meta data
     *
     * @access private
     */
    private function load_customer(){
        $customer_id = get_post_meta( $this->id, '_hb_customer_id', true );
        return $customer_id ? HB_Customer::instance( $customer_id ) : null;

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
            $this->_booking_info = array_merge( $this->_booking_info, (array)$info );
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
        $post_data['post_type']                 = 'hb_booking';
        $post_data['post_content_filtered']     = $post_data['post_content'];
        $post_data['post_excerpt']              = $post_data['post_content'];
        if ( $this->post->ID ) {
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
        $this->id = $this->post->ID;
        return $this->post->ID;
    }

    function get_post_meta( $meta_key = null, $val = null, $unique = true ) {
        if ( ! $this->post->ID || ! $meta_key ) {
            return $val;
        }

        return get_post_meta( $this->post->ID, $meta_key, $unique );
    }

    // room book item
    function save_room( $params = array(), $booking_id )
    {
        $itemOfOrderId = wp_insert_post( array(
                    'post_title'    => sprintf( 'Room order in %1$s to %2$s', $params['_hb_check_in_date'], $params['_hb_check_out_date']),
                    'post_content'  => '',
                    'post_status'   => 'publish',
                    'post_type'     => 'hb_booking_item'
            ) );

        add_post_meta( $itemOfOrderId, '_hb_booking_id', $booking_id );

        foreach ($params as $key => $value) {
            add_post_meta( $itemOfOrderId, $key, $value );
        }
    }

    /**
     * Get current status of booking
     *
     * @return mixed
     */
    public function get_status() {
        $this->post->post_status = get_post_status( $this->id );
        return apply_filters( 'hb_order_get_status', 'hb-' === substr( $this->post->post_status, 0, 3 ) ? substr( $this->post->post_status, 3 ) : $this->post->post_status, $this );
    }

    /**
     * Checks to see if current booking has status as passed
     *
     * @param $status
     * @return mixed
     */
    public function has_status( $status ) {
        return apply_filters( 'hb_booking_has_status', ( is_array( $status ) && in_array( $this->get_status(), $status ) ) || $this->get_status() === $status ? true : false, $this, $status );
    }

    /**
     * Updates booking to new status if needed
     *
     * @param string $new_status
     */
    function update_status( $new_status = 'pending' ){
        // Standardise status names.
        $new_status = 'hb-' === substr( $new_status, 0, 3 ) ? substr( $new_status, 3 ) : $new_status;
        $old_status = $this->get_status();

        if ( $new_status !== $old_status || ! in_array( $this->post_status, array_keys( hb_get_booking_statuses() ) ) ) {

            // Update the order
            wp_update_post( array( 'ID' => $this->id, 'post_status' => 'hb-' . $new_status ) );
            $this->post_status = 'hb-' . $new_status;

            // Status was changed
            do_action( 'hb_booking_status_' . $new_status, $this->id );
            do_action( 'hb_booking_status_' . $old_status . '_to_' . $new_status, $this->id );
            do_action( 'hb_booking_status_changed', $this->id, $old_status, $new_status );

            switch ( $new_status ) {

                case 'completed' :
                    $payment_complete_date = get_post_meta( $this->post->ID, '_hb_booking_payment_completed', true  );
                    if( ! $payment_complete_date )
                    {
                        add_post_meta( $this->post->ID, '_hb_booking_payment_completed', current_time( 'mysql' ) );
                    }
                    break;

                case 'processing' :

                    break;
            }
        }
    }

    /**
     * Format booking number id
     * @return string
     */
    function get_booking_number(){
        return '#' . sprintf( "%'.010d", $this->id );
    }

    /**
     * Mark booking as complete
     *
     * @param string - transaction ID provided payment gateway
     */
    function payment_complete( $transaction_id = '' ){
        do_action( 'hb_pre_payment_complete', $this->id );

        delete_transient( 'booking_awaiting_payment' );

        $valid_booking_statuses = apply_filters( 'hb_valid_order_statuses_for_payment_complete', array( 'pending' ), $this );

        if ( $this->id && $this->has_status( $valid_booking_statuses ) ) {

            $this->update_status( 'completed' );

            if ( ! empty( $transaction_id ) ) {
                add_post_meta( $this->id, '_transaction_id', $transaction_id, true );
            }

            do_action( 'hb_payment_complete', $this->id );
        }else{
            do_action( 'hb_payment_complete_order_status_' . $this->get_status(), $this->id );
        }
    }

    /**
     * Get checkout booking success url
     *
     * @return mixed
     */
    public function get_checkout_booking_received_url() {
        $received_url = hb_get_page_permalink( 'search' );
        return apply_filters( 'hb_get_checkout_booking_received_url', $received_url, $this );
    }

    function get_booking_rooms_params( $booking_id = null )
    {
        if( $booking_id == null )
            $booking_id = $this->id;

        return get_post_meta( $booking_id, '_hb_booking_params', true );
    }

    /**
     * get_cart_params
     * @return cart object
     * @since  1.0.4
     */
    function get_cart_params()
    {
        return get_post_meta( $this->id, '_hb_booking_cart_params', true );
    }

    /**
     * Get an instance of HB_Booking by post ID or WP_Post object
     *
     * @param $booking
     * @return HB_Booking
     */
    static function instance( $booking ) {
        $post = $booking;
        if( $booking instanceof WP_Post ){
            $id = $booking->ID;
        } elseif( is_object( $booking ) && isset( $booking->ID ) ){
            $id = $booking->ID;
        } else {
            $id = $booking;
        }

        if( empty( self::$_instance[ $id ] ) ){
            self::$_instance[ $id ] = new self( $post );
        }
        return self::$_instance[ $id ];
    }
}
