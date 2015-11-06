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
        add_filter( 'tp_hotel_booking_checkout_tpl', array( $this, 'checkout_order_pay' ) );
        add_filter( 'tp_hotel_booking_checkout_tpl_template_args', array( $this, 'checkout_order_pay_args' ) );
        add_action( 'tp_hotel_booking_order_pay_after', array( $this, 'authorize_form' ) );
    }

    /**
     * Init hooks
     */
    function init(){
        add_action( 'hb_payment_gateway_settings_' . $this->slug, array( $this, 'admin_settings' ) );
        add_action( 'hb_payment_gateway_form_' . $this->slug, array( $this, 'form' ) );
    }

    function checkout_order_pay( $tpl )
    {
        if( ! empty( $_GET['hb-order-pay'] ) &&
            ! empty( $_GET['hb-order-pay-nonce'] ) &&
            wp_verify_nonce( $_GET['hb-order-pay-nonce'], 'hb-order-pay-nonce' ) )
        {
            $tpl = 'order-pay.php';
        }
        else
        {
            $tpl = 'checkout.php';
        }
        return $tpl;
    }

    function checkout_order_pay_args( $args )
    {
        if( ! empty( $_GET['hb-order-pay'] ) &&
            ! empty( $_GET['hb-order-pay-nonce'] ) &&
            wp_verify_nonce( $_GET['hb-order-pay-nonce'], 'hb-order-pay-nonce' ) )
        {
            $args = array( 'booking_id' => absint( $_GET['hb-order-pay'] ) );
        }

        return $args;
    }

    function authorize_form()
    {
        if( empty( $_GET['hb-order-pay'] ) ||
            empty( $_GET['hb-order-pay-nonce'] ) ||
            ! wp_verify_nonce( $_GET['hb-order-pay-nonce'], 'hb-order-pay-nonce' ) )
            return;

        $book_id = absint( $_GET['hb-order-pay'] );
        $book = HB_Booking::instance( $book_id );

        $customer = $book->_customer->data;

        $nonce = wp_create_nonce( 'replay-pay-nonce' );
        $authorize_args = array(
            'x_login'                  => $this->_api_login_id,
            'x_amount'                 => $book->order_total,
            'x_currency_code'          => hb_get_currency(),
            'x_invoice_num'            => $book_id,
            'x_relay_response'         => "TRUE",
            'x_relay_url'              => add_query_arg( array( 'replay-pay' => $book_id, 'replay-pay-nonce' => $nonce ) , hb_get_page_permalink( 'checkout' ) ),
            'x_fp_sequence'            => $book_id,
            // 'x_fp_hash'                => $fingerprint,
            'x_show_form'              => 'PAYMENT_FORM',
            'x_version'                => '3.1',
            'x_fp_timestamp'           => strtotime( get_the_time( 'F j, Y g:i a', $book_id ) ),
            'x_first_name'             => isset($customer['_hb_first_name']) ? $customer['_hb_first_name'][0] : '',
            'x_last_name'              => isset($customer['_hb_last_name']) ? $customer['_hb_last_name'][0] : '',
            'x_address'                => isset($customer['_hb_address']) ? $customer['_hb_address'][0] : '',
            'x_country'                => isset($customer['_hb_country']) ? $customer['_hb_country'][0] : '',
            'x_state'                  => isset($customer['_hb_state']) ? $customer['_hb_state'][0] : '',
            'x_city'                   => isset($customer['_hb_city']) ? $customer['_hb_city'][0] : '',
            'x_zip'                    => isset($customer['_hb_postal_code']) ? $customer['_hb_postal_code'][0] : '',
            'x_phone'                  => isset($customer['_hb_phone']) ? $customer['_hb_phone'][0] : '',
            'x_email'                  => isset($customer['_hb_email']) ? $customer['_hb_email'][0] : '',
            // 'x_company'                => $order->billing_company ,
            // 'x_ship_to_first_name'     => $order->shipping_first_name ,
            // 'x_ship_to_last_name'      => $order->shipping_last_name ,
            // 'x_ship_to_company'        => $order->shipping_company ,
            // 'x_ship_to_address'        => $order->shipping_address_1 .' '. $order->shipping_address_2,
            // 'x_ship_to_country'        => $order->shipping_country,
            // 'x_ship_to_state'          => $order->shipping_state,
            // 'x_ship_to_city'           => $order->shipping_city,
            // 'x_ship_to_zip'            => $order->shipping_postcode,
            'x_cancel_url'             => site_url(),
            'x_cancel_url_text'        => __( 'Cancel Payment', 'tp-hotel-booking' )
        );

        if( $this->_settings['sandbox'] === 'on' )
            $authorize_args['x_test_request'] = TRUE;
        else
            $authorize_args['x_test_request'] = FALSE;
        ?>

        <form action="<?php echo esc_url( $this->authorize_url ); ?>" method="POST">
            <?php foreach( $authorize_args as $name => $val ): ?>
                <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $val ) ?>" />
            <?php endforeach; ?>
            <button type="submit"><?php _e( 'Pay with Authorize.net', 'tp-hotel-booking' ) ?></button>
        </form>

        <?php
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
        $nonce = wp_create_nonce( 'hb-order-pay-nonce' );
        return add_query_arg(
            array( 'hb-order-pay' => $booking_id, 'hb-order-pay-nonce' => $nonce ),
            hb_get_page_permalink( 'checkout' ) );
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