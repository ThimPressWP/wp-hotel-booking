<?php

/**
 * Class HB_Payment_Gateway_Authorize_Sim
 */
class HB_Payment_Gateway_Authorize_Sim extends HB_Payment_Gateway_Base{

    /**
     * production URL
     * @var null
     */
    protected $_production_authorize_url = null;

    /**
     * sandbox URL
     * @var null
     */
    protected $_sandbox_authorize_url = null;

    /**
     * current authorize using
     * @var null
     */
    protected $_authorize_url = null;

    /**
     * API Login ID
     * @var null
     */
    protected $_api_login_id = null;

    /**
     * transaction key
     * @var null
     */
    protected $_transaction_key = null;

    /**
     * secret key
     * @var null
     */
    protected $_secret_key = null;

    /**
     * @var array
     */
    protected $_settings = array();

    /**
     * Construction
     */
    function __construct(){
        parent::__construct();
        $this->_slug = 'authorize';
        $this->_title = __( 'Authorize', 'tp-hotel-booking' );
        $this->_description = __( 'Pay with Authorize.net', 'tp-hotel-booking' );
        $this->_settings = HB_Settings::instance()->get('authorize');

        $this->_api_login_id = isset($this->_settings['api_login_id']) ? $this->_settings['api_login_id'] : '';
        $this->_transaction_key = isset($this->_settings['transaction_key']) ? $this->_settings['transaction_key'] : '';
        $this->_secret_key = isset($this->_settings['secret_key']) ? $this->_settings['secret_key'] : '';

        $this->_production_authorize_url        = 'http://secure.authorize.net/gateway/transact.dll';
        $this->_sandbox_authorize_url           = 'http://test.secure.authorize.net/gateway/transact.dll';

        if( $this->_settings['sandbox'] === 'on' )
            $this->_authorize_url = $this->_sandbox_authorize_url ;
        else
            $this->_authorize_url = $this->_production_authorize_url ;

        $this->init();
    }

    /**
     * Init hooks
     */
    function init(){
        add_action( 'hb_payment_gateway_settings_' . $this->slug, array( $this, 'admin_settings' ) );
        add_action( 'hb_payment_gateway_form_' . $this->slug, array( $this, 'form' ) );
    }

    /**
     * Get payment method title
     *
     * @return mixed
     */
    function payment_method_title(){
        return $this->_description;
    }

    function form(){
        echo _e( 'Pay with Authorize', 'tp-hotel-booking');
    }

    /**
     * Handle a completed payment
     *
     * @param HB_Booking
     * @param Authorize IPN params
     */
    protected function payment_status_completed( $booking, $request ) {

    }

    /**
     * Handle a pending payment
     *
     * @param  HB_Booking
     * @param Authorize IPN params
     */
    protected function payment_status_pending( $booking, $request ) {
        $this->payment_status_completed( $booking, $request );
    }

    /**
     * @param HB_Booking
     * @param string $txn_id
     * @param string $note - not use
     */
    function payment_complete( $booking, $txn_id = '', $note = '' ){
        $booking->payment_complete( $txn_id );
    }

    /**
     * Get Authorize checkout url
     *
     * @param $booking_id
     * @return string
     */
    protected function _get_authorize_basic_checkout_url(  $booking_id ){
        $booking    = HB_Booking::instance( $booking_id );

        $customer = hb_get_customer( get_transient('hb_current_customer_' . session_id ()) );
        $args = array (
            'x_login'       => $this->_settings['api_login_id'],
            'x_version'     => '3.1',
            'x_show_form'   => 'PAYMENT_FORM',
            'x_method'      => 'CC',
            'x_amount'      => '100',
            'x_currency_code'   => hb_get_currency(),
            'amount'   => round( hb_get_cart_total( ! hb_get_request( 'pay_all' ) ), 2 ),
            'quantity' => '1',
        );

        $requets = wp_remote_post( $this->_authorize_url, array( 'body' => $args ) );

        if( is_wp_error( $requets ) ) return;

        echo '<pre>';
        print_r( $requets['body'] ); die();

        return site_url() . '?authorizbook=' . $booking->post->ID;

    }

    /**
     * Process checkout
     *
     * @param null $booking_id
     * @return array
     */
    function process_checkout( $booking_id = null ){
        return array(
            'result'    => 'success',
            'redirect'  => $this->_get_authorize_basic_checkout_url(  $booking_id  )
        );
    }

    /**
     * Print admin settings page
     *
     * @param $gateway
     */
    function admin_settings( $gateway ){
        $template = TP_Hotel_Booking::instance()->locate( 'includes/admin/views/settings/authorize-sim.php' );
        include_once $template;
    }

    /**
     * @return bool
     */
    function is_enable(){
        return empty( $this->_settings['enable'] ) || $this->_settings['enable'] == 'on';
    }
}