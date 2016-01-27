<?php

/**
 * Class HB_Checkout
 */
class HB_Checkout{

    /**
     * @var HB_Checkout object instance
     * @access protected
     */
    static protected $_instance = null;

    /**
     * Payment method
     *
     * @var string
     */
    public $payment_method = '';

    /**
     * Constructor
     */
    function __construct(){
        //
    }

    /**
     * Create new customer for checkout if needed
     *
     * @return int
     */
    function create_customer(){
        $customer_info = array(
            'ID'            => hb_get_request( 'existing-customer-id' ),
            'title'         => hb_get_request( 'title' ),
            'first_name'    => hb_get_request( 'first_name' ),
            'last_name'     => hb_get_request( 'last_name' ),
            'address'       => hb_get_request( 'address' ),
            'city'          => hb_get_request( 'city' ),
            'state'         => hb_get_request( 'state' ),
            'postal_code'   => hb_get_request( 'postal_code' ),
            'country'       => hb_get_request( 'country' ),
            'phone'         => hb_get_request( 'phone' ),
            'email'         => hb_get_request( 'email' ),
            'fax'           => hb_get_request( 'fax' ),
        );
        $customer_id = hb_update_customer_info( $customer_info );

        // set transient for current customer in one hour
        // set_transient( 'hb_current_customer_' . session_id(), $customer_id, HOUR_IN_SECONDS );
        // set cart customer
        TP_Hotel_Booking::instance()->cart->set_customer( 'customer_id', $customer_id );
        return $this->_customer = $customer_id;
    }

    /**
     * Creates temp new booking if needed
     *
     * @return mixed|WP_Error
     * @throws Exception
     */
    function create_booking(){
        global $hb_settings;
        // $customer_id = get_transient( 'hb_current_customer_' . session_id() );
        $customer_id = TP_Hotel_Booking::instance()->cart->customer_id;
        if( ! $customer_id ) {
            $customer_id = $this->create_customer();
        }

        if( TP_Hotel_Booking::instance()->cart->cart_items_count === 0 ) {
            hb_send_json( array(
                    'result'        => 'fail',
                    'message'       => __( 'Your cart is empty', 'tp-hotel-booking' )
                ) );
            throw new Exception( sprintf( __( 'Sorry, your session has expired. <a href="%s">Return to homepage</a>', 'tp-hotel-booking' ), home_url() ) );
        }

        // load booking id from sessions
        $booking_id = TP_Hotel_Booking::instance()->cart->booking_id;

        // Resume the unpaid order if its pending
        if ( $booking_id && ( $booking = HB_Booking::instance( $booking_id ) ) && $booking->post->ID && $booking->has_status( array( 'pending', 'failed' ) ) ) {
            $booking_data['ID'] = $booking_id;
            $booking_data['post_content'] = hb_get_request( 'addition_information' );
            $booking->set_booking_info( $booking_data );
        } else {
            $booking_id = hb_create_booking();
            // initialize Booking object
            $booking = HB_Booking::instance( $booking_id );
        }

        // generate transaction
        $transaction = TP_Hotel_Booking::instance()->cart->generate_transaction( $customer_id, $this->payment_method );

        // booking meta data
        $booking_info = array();
        if( ! empty( $transaction->booking_info ) ) {
            $booking_info = $transaction->booking_info;
        }
        // allow hook
        $booking_info = apply_filters( 'tp_hotel_booking_checkout_booking_info', $booking_info, $transaction );
        $booking->set_booking_info(
            $booking_info
        );
        // update booking info meta post
        $booking_id = $booking->update();
        if ( $booking_id ) {
            delete_post_meta( $booking_id, '_hb_room_id' );
            if( $transaction->rooms )
            {
                foreach( $transaction->rooms as $room_options ){
                    $num_of_rooms = $room_options['_hb_quantity'];
                    // insert multiple meta value
                    for( $i = 0; $i < $num_of_rooms; $i ++ ) {
                        add_post_meta( $booking_id, '_hb_room_id', $room_options['_hb_id'] );
                        // create post save item of order
                        $booking->save_room( $room_options, $booking_id );
                    }

                }
            }

            // cart_contents
            $booking_params = apply_filters( 'hotel_booking_booking_params', TP_Hotel_Booking::instance()->cart->cart_contents );
            // add_post_meta( $booking_id, '_hb_booking_params', $booking_params ); // old version 1.0.3 or less
            add_post_meta( $booking_id, '_hb_booking_cart_params', $booking_params );
        }
        do_action( 'hb_new_booking', $booking_id );
        return $booking_id;
    }

    /**
     * Process checkout
     *
     * @throws Exception
     */
    function process_checkout(){
        if( strtolower( $_SERVER['REQUEST_METHOD'] ) != 'post' ){
            return;
        }

        $payment_method = hb_get_user_payment_method( hb_get_request( 'hb-payment-method' ) );

        if( ! $payment_method ){
            throw new Exception( __( 'The payment method is not available', 'tp-hotel-booking' ) );
        }

        $customer_id = $this->create_customer();

        $this->payment_method = $payment_method;
        if( $customer_id ) {
            $booking_id = $this->create_booking();
            if( $booking_id ) {
                // if total > 0
                if ( HB_Cart::instance()->needs_payment() ) {
                    $result = $payment_method->process_checkout( $booking_id, $customer_id );
                } else {
                    if ( empty($booking) ) {
                        $booking = HB_Booking::instance($booking_id);
                    }
                    // No payment was required for order
                    $booking->payment_complete();
                    TP_Hotel_Booking::instance()->cart->empty_cart();
                    $return_url = $booking->get_checkout_booking_received_url();
                    hb_send_json( array(
                        'result' 	=> 'success',
                        'redirect'  => apply_filters( 'hb_checkout_no_payment_needed_redirect', $return_url, $booking )
                    ) );
                }
            }else{
                die( __('can not create booking', 'tp-hotel-booking') );
            }
        }

        if ( ! empty( $result['result'] ) && $result['result'] == 'success' ) {

            $result = apply_filters( 'hb_payment_successful_result', $result );

            do_action( 'hb_place_order', $result );
            if ( hb_is_ajax() ) {
                hb_send_json( $result );
                exit;
            } else {
                wp_redirect( $result['redirect'] );
                exit;
            }

        }
    }

    /**
     * Get unique instance for this object
     *
     * @return HB_Checkout
     */
    static function instance(){
        if( empty( self::$_instance ) ){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}