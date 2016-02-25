<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class HB_Payment_Gateway_Stripe
 */
class HB_Payment_Gateway_Offline_Payment extends HB_Payment_Gateway_Base{
    /**
     * @var array
     */
    protected $_settings = array();

    function __construct(){
        parent::__construct();
        $this->_slug = 'offline-payment';
        $this->_title = __( 'Offline Payment', 'tp-hotel-booking' );
        $this->_description = __( 'Pay on arrival', 'tp-hotel-booking' );
        $this->_settings = HB_Settings::instance()->get('offline-payment');
        $this->init();
    }

    /**
     * Init hooks
     */
    function init(){
        add_action( 'hb_payment_gateway_settings_' . $this->slug, array( $this, 'admin_settings' ) );
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
        if( get_post_meta( $booking_id, '_hb_method', true ) == 'offline-payment' ) {
            _e( '<br />(<small>Pay on arrival</small>)', 'tp-hotel-booking' );
        }
    }

    /**
     * Print admin settings
     *
     * @param $gateway
     */
    function admin_settings( $gateway ){
        $template = TP_Hotel_Booking::instance()->locate( 'includes/admin/views/settings/offline-payment.php' );
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
    function process_checkout( $booking_id = null, $customer_id = null ){
        $booking = HB_Booking::instance( $booking_id );
        if( $booking ){
            $booking->update_status( 'processing' );
        }

        $settings = HB_Settings::instance()->get('offline-payment');
        $email_subject = ! empty( $settings['email_subject'] ) ? $settings['email_subject'] : false;
        $email_content = ! empty( $settings['email_content'] ) ? $settings['email_content'] : false;
        $to = get_post_meta( $customer_id, '_hb_email', true );

        if( ! $email_subject || ! $email_content ) {
            return array(
                'result'    => 'fail'
            );
        } else {
            // empty cart
            TP_Hotel_Booking::instance()->cart->empty_cart();

            hb_add_message( sprintf( __( 'Thank you! Your booking has been placed. Please check your email %s to view booking details', 'tp-hotel-booking' ), $to ) );
            return array(
                'result'    => 'success',
                'redirect'  => '?hotel-booking-offline-payment=1'
            );
        }

    }

    function form(){
        echo _e( ' Pay on Arrival', 'tp-hotel-booking' );
    }
}
