<?php

/**
 * Class HB_Payment_Gateway_Paypal
 */
class HB_Payment_Gateway_Paypal extends HB_Payment_Gateway_Base{
    /**
     * @var null
     */
    protected $paypal_live_url              = null;

    /**
     * @var null
     */
    protected $paypal_sandbox_url           = null;

    /**
     * @var null
     */
    protected $paypal_payment_live_url      = null;

    /**
     * @var null
     */
    protected $paypal_payment_sandbox_url   = null;

    /**
     * @var null
     */
    protected $paypal_nvp_api_live_url      = null;

    /**
     * @var null
     */
    protected $paypal_vnp_api_sandbox_url   = null;

    /**
     * @var array
     */
    protected $_settings = array();

    /**
     * Construction
     */
    function __construct(){
        parent::__construct();
        $this->_title = __( 'Paypal', 'tp-hotel-booking' );
        $this->_description = __( 'Pay with Paypal', 'tp-hotel-booking' );
        $this->_settings = HB_Settings::instance()->get('paypal');

        $this->paypal_live_url              = 'https://www.paypal.com/';
        $this->paypal_sandbox_url           = 'https://www.sandbox.paypal.com/';
        $this->paypal_payment_live_url      = 'https://www.paypal.com/cgi-bin/webscr';
        $this->paypal_payment_sandbox_url   = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        $this->paypal_nvp_api_live_url      = 'https://api-3t.paypal.com/nvp';
        $this->paypal_nvp_api_sandbox_url   = 'https://api-3t.sandbox.paypal.com/nvp';

        $this->init();
    }

    function init(){
        add_action( 'hb_payment_gateway_settings_' . $this->slug, array( $this, 'admin_settings' ) );
        add_action( 'hb_payment_gateway_form_' . $this->slug, array( $this, 'form' ) );
        add_action( 'hb_do_checkout_' . $this->_slug, array( $this, 'process_checkout' ) );
    }

    function form(){
        echo 'ádasdasdsadsadsad';
    }

    protected function _get_paypal_basic_checkout_url(){

        $paypal = HB_Settings::instance()->get( 'paypal' );

        $user = hb_get_current_user();

        $paypal_args = array (
            'cmd'      => '_xclick',
            'amount'   => hb_get_cart_total(),
            'quantity' => '1',
        );

        $transaction    = hb_generate_transaction_object();
        $temp_id        = hb_uniqid();
        //learn_press_set_transient_transaction( 'lpps', $temp_id, $user->ID, $transaction );

        $nonce = wp_create_nonce( 'hb-paypal-nonce' );
        $paypal_email = $paypal['sandbox'] ? $paypal['sandbox_email'] : $paypal['email'];
        $query = array(
            'business'      => $paypal_email,
            'item_name'     => hb_get_cart_description(),
            'return'        => add_query_arg( array( 'hb-transaction-method' => 'paypal-standard', 'paypal-nonce' => $nonce ), hb_get_return_url() ),
            'currency_code' => hb_get_currency(),
            'notify_url'    => get_site_url() . '/?' . hb_get_web_hook( 'paypal-standard' ) . '=1',//get_site_url() . '/?learn-press-transaction-method=paypal-standard',
            'no_note'       => '1',
            'shipping'      => '0',
            'email'         => $user->user_email,
            'rm'            => '2',
            'cancel_return' => hb_get_return_url(),
            'custom'        => $temp_id,
            'no_shipping'   => '1'
        );

        $query = array_merge( $paypal_args, $query );
        $query = apply_filters( 'hb_paypal_standard_query', $query );

        $paypal_payment_url = ( $paypal['sandbox'] ? $this->paypal_payment_sandbox_url : $this->paypal_payment_live_url ) . '?' .  http_build_query( $query );
        return $paypal_payment_url;
    }
    function process_checkout(){
        return array(
            'result'    => 'success',
            'redirect'  => $this->_get_paypal_basic_checkout_url()
        );
    }

    function admin_settings( $gateway ){
        $template = TP_Hotel_Booking::instance()->locate( 'includes/admin/views/settings/paypal.php' );
        include_once $template;
    }

    function is_enable(){
        return ! empty( $this->_settings['enable'] ) && $this->_settings['enable'] == 'on';
    }
}