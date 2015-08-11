<?php

/**
 * Class HB_Payment_Gateway_Stripe
 */
class HB_Payment_Gateway_Stripe extends HB_Payment_Gateway_Base{
    /**
     * @var array
     */
    protected $_settings = array();

    function __construct(){
        parent::__construct();
        $this->_title = __( 'Stripe', 'tp-hotel-booking' );
        $this->_description = __( 'Pay with credit card', 'tp-hotel-booking' );
        $this->_settings = HB_Settings::instance()->get('stripe');
        $this->init();
    }

    function init(){
        add_action( 'hb_payment_gateway_settings_stripe', array( $this, 'admin_settings' ) );
    }

    function admin_settings( $gateway ){
        $template = TP_Hotel_Booking::instance()->locate( 'includes/admin/views/settings/stripe.php' );
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
}