<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class HB_Payment_Gateway_Stripe
 */
class HB_Payment_Gateway_Offline_Payment extends WPHB_Payment_Gateway_Base{
    /**
     * @var array
     */
    protected $_settings = array();

    function __construct(){
        parent::__construct();
        $this->_slug = 'offline-payment';
        $this->_title = __( 'Offline Payment', 'wp-hotel-booking' );
        $this->_description = __( 'Pay on arrival', 'wp-hotel-booking' );
        $this->_settings = WPHB_Settings::instance()->get('offline-payment');
        $this->init();
    }

    /**
     * Init hooks
     */
    function init(){
        add_action( 'hb_payment_gateway_form_' . $this->slug, array( $this, 'form' ) );
        add_filter( 'hb_payment_method_title_offline-payment', array( $this, 'payment_method_title' ) );
    }

    /**
     * Payment method title
     *
     * @return mixed
     */
    function payment_method_title(){
        return $this->_description;
    }

    /**
     * Print the text in total column
     *
     * @param $booking_id
     * @param $total
     * @param $total_with_currency
     */
    function column_total_content( $booking_id, $total, $total_with_currency ){
        $booking = HB_Booking::instance( $booking_id );
        if( $booking->method === 'offline-payment' ) {
            _e( '<br />(<small>Pay on arrival</small>)', 'wp-hotel-booking' );
        }
    }

    /**
     * Print admin settings
     *
     * @param $gateway
     */
    function admin_settings(){
        $template = WP_Hotel_Booking::instance()->locate( 'includes/gateways/offline-payment/views/settings.php' );
        include_once $template;
    }

    /**
     * Check to see if this payment is enable
     *
     * @return bool
     */
    function is_enable(){
        return ! empty( $this->_settings['enable'] ) && $this->_settings['enable'] == 'on';
    }

    /**
     * Process checkout booking
     *
     * @param null $booking_id
     * @return array
     */
    function process_checkout( $booking_id = null ){
        $booking = HB_Booking::instance( $booking_id );
        if( $booking ){
            $booking->update_status( 'processing' );
        }

        wphb_add_message( sprintf( __( 'Thank you! Your booking has been placed. Please check your email %s to view booking details', 'wp-hotel-booking' ), $booking->customer_email ) );
        return array(
            'result'    => 'success',
            'redirect'  => add_query_arg( 'hotel-booking-offline-payment', 1, wphb_get_checkout_url() )
        );

    }

    function form(){
        echo _e( ' Pay on Arrival', 'wp-hotel-booking' );
    }
}
