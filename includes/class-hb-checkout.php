<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

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
     * Creates temp new booking if needed
     *
     * @return mixed|WP_Error
     * @throws Exception
     */
    function create_booking( $order = null ){
        global $hb_settings;
        if ( ! $order ) {
            $order = $this->payment_method;
        }

        // generate transaction
        $transaction = TP_Hotel_Booking::instance()->cart->generate_transaction( $order );
        // allow hook
        $booking_info = apply_filters( 'hotel_booking_checkout_booking_info', $transaction->booking_info, $transaction );
        $order_items = apply_filters( 'hotel_booking_checkout_booking_order_items', $transaction->order_items, $transaction );

        if( TP_Hotel_Booking::instance()->cart->cart_items_count === 0 ) {
            hb_send_json( array(
                    'result'        => 'fail',
                    'message'       => __( 'Your cart is empty.', 'tp-hotel-booking' )
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
            // update booking info meta post
            $booking_id = $booking->update();
        } else {
            $booking_id = hb_create_booking( $booking_info );
            // initialize Booking object
            $booking = HB_Booking::instance( $booking_id );
        }

        // // add order item
        // if ( $booking_id ) {

        // }
        // if ( $booking_id ) {
        //     delete_post_meta( $booking_id, '_hb_room_id' );
        //     if( $transaction->rooms )
        //     {
        //         foreach( $transaction->rooms as $room_options ) {
        //             $num_of_rooms = $room_options['_hb_quantity'];
        //             // insert multiple meta value
        //             for( $i = 0; $i < $num_of_rooms; $i ++ ) {
        //                 add_post_meta( $booking_id, '_hb_room_id', $room_options['_hb_id'] );
        //                 // create post save item of order
        //                 $booking->save_room( $room_options, $booking_id );
        //             }

        //         }
        //     }

        //     // cart_contents
        //     $booking_params = apply_filters( 'hotel_booking_booking_params', TP_Hotel_Booking::instance()->cart->cart_contents );
        //     // add_post_meta( $booking_id, '_hb_booking_params', $booking_params ); // old version 1.0.3 or less
        //     add_post_meta( $booking_id, '_hb_booking_cart_params', $booking_params );
        // }
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

        if ( ! is_user_logged_in() && ! hb_settings()->get( 'guest_checkout' ) ) {
            throw new Exception( __( 'You have to Login to process checkout.', 'tp-hotel-booking' ) );
        }

        $payment_method = hb_get_user_payment_method( hb_get_request( 'hb-payment-method' ) );

        if( ! $payment_method ){
            throw new Exception( __( 'The payment method is not available', 'tp-hotel-booking' ) );
        }

        $this->payment_method = $payment_method;
        $booking_id = $this->create_booking();
        if( $booking_id ) {
            // if total > 0
            if ( TP_Hotel_Booking::instance()->cart->needs_payment() ) {
                $result = $payment_method->process_checkout( $booking_id );
            } else {
                if ( empty($booking) ) {
                    $booking = HB_Booking::instance($booking_id);
                }
                // No payment was required for order
                $booking->payment_complete();
                $return_url = $booking->get_checkout_booking_received_url();
                $result = array(
                    'result'    => 'success',
                    'redirect'  => apply_filters( 'hb_checkout_no_payment_needed_redirect', $return_url, $booking )
                );
            }
        } else {
            die( __('can not create booking', 'tp-hotel-booking') );
        }

        if ( ! empty( $result['result'] ) && $result['result'] == 'success' ) {
            TP_Hotel_Booking::instance()->cart->empty_cart();

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