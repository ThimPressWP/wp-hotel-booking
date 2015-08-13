<?php

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
        $this->_title = __( 'Offline Payment', 'tp-hotel-booking' );
        $this->_description = __( '', 'tp-hotel-booking' );
        $this->_settings = HB_Settings::instance()->get('stripe');
        $this->init();
    }

    function init(){
        add_action( 'hb_payment_gateway_settings_' . $this->slug, array( $this, 'admin_settings' ) );
        add_action( 'hb_payment_gateway_form_' . $this->slug, array( $this, 'form' ) );
    }

    function admin_settings( $gateway ){
        $template = TP_Hotel_Booking::instance()->locate( 'includes/admin/views/settings/offline-payment.php' );
        include_once $template;
    }

    function is_enable(){
        return ! empty( $this->_settings['enable'] ) && $this->_settings['enable'] == 'on';
    }

    function process_checkout( $customer_id = null ){
        return array(
            'result'    => 'success',
            'redirect'  => 'http://24h.com.vn'
        );
    }

    function form(){
        echo _e( 'Pay when arrive');
    }
}