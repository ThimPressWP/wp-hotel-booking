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

    protected $_messages = null;

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
        $this->_title = __( 'Authorize', 'tp-hotel-booking-authorize-sim' );
        $this->_description = __( 'Pay with Authorize.net', 'tp-hotel-booking-authorize-sim' );
        $this->_settings = HB_Settings::instance()->get('authorize');

        $this->_api_login_id = isset($this->_settings['api_login_id']) ? $this->_settings['api_login_id'] : '8u33RVeK';
        $this->_transaction_key = isset($this->_settings['transaction_key']) ? $this->_settings['transaction_key'] : '36zHT3e446Hha7X8';
        $this->_secret_key = isset($this->_settings['secret_key']) ? $this->_settings['secret_key'] : '';

        $this->_production_authorize_url        = 'https://secure.authorize.net/gateway/transact.dll';
        $this->_sandbox_authorize_url           = 'https://test.authorize.net/gateway/transact.dll';

        if( $this->_settings['sandbox'] === 'on' )
            $this->_authorize_url = $this->_sandbox_authorize_url ;
        else
            $this->_authorize_url = $this->_production_authorize_url ;

        $this->_messages = array(
                1 => __( 'This transaction has been approved.', 'tp-hotel-booking-authorize-sim' ),
                2 => __( 'This transaction has been declined.', 'tp-hotel-booking-authorize-sim' ),
                3 => __( 'There has been an error processing this transaction.', 'tp-hotel-booking-authorize-sim' ),
                4 => __( ' This transaction is being held for review.', 'tp-hotel-booking-authorize-sim' )
            );

        $this->init();
        /**
         * checkout authorize hook template.
         */
        add_filter( 'hotel_booking_checkout_tpl', array( $this, 'checkout_order_pay' ) );

        /**
         * template args hook
         */
        add_filter( 'hotel_booking_checkout_tpl_template_args', array( $this, 'checkout_order_pay_args' ) );

        /**
         * order-pay confirm. only authorize
         */
        add_action( 'hotel_booking_order_pay_after', array( $this, 'authorize_form' ) );
    }

    /**
     * Init hooks
     */
    function init(){
        /**
         * settings form, frontend payment select form
         */
        add_action( 'hb_payment_gateway_form_' . $this->slug, array( $this, 'form' ) );
        $this->payment_callback();
    }

    function payment_callback()
    {
        ob_start();
        if( ! isset($_POST) )
            return;

        if( ! isset( $_POST['x_response_code'] ) )
            return;

        if( isset( $_POST['x_response_reason_text'] ) )
            hb_add_message( $_POST['x_response_reason_text'] );

        $code = 0;
        if( isset( $_POST['x_response_code'] ) && array_key_exists( (int)$_POST['x_response_code'], $this->_messages) )
            $code = (int)$_POST['x_response_code'];

        $amout = 0;
        if( isset($_POST['x_amount']) )
            $amout = (float)$_POST['x_amount'];

        if( ! isset( $_POST['x_invoice_num'] ) )
            return;

        $id = (int)$_POST['x_invoice_num'];
        $book = HB_Booking::instance( $id );

        if( $code === 1 ) {
            if( (float)$book->total === (float)$amout )
                $status = 'completed';
            else
                $status = 'processing';
        } else {
            $status = 'pending';
        }

        $book->update_status( $status );
        TP_Hotel_Booking::instance()->cart->empty_cart();
        wp_redirect( hb_get_checkout_url() ); exit();
    }

    function checkout_order_pay( $tpl )
    {
        if( ! empty( $_GET['hb-order-pay'] ) &&
            ! empty( $_GET['hb-order-pay-nonce'] ) &&
            wp_verify_nonce( $_GET['hb-order-pay-nonce'], 'hb-order-pay-nonce' ) )
        {
            $tpl = 'checkout/order-pay.php';
        }
        else
        {
            $tpl = 'checkout/checkout.php';
        }
        return $tpl;
    }

    /**
     * template args
     * @param  [$args] extend hook
     * @return [array]       [description]
     */
    function checkout_order_pay_args( $args )
    {
        if( ! empty( $_GET['hb-order-pay'] ) &&
            ! empty( $_GET['hb-order-pay-nonce'] ) &&
            wp_verify_nonce( sanitize_text_field( $_GET['hb-order-pay-nonce'] ), 'hb-order-pay-nonce' ) )
        {
            $args = array( 'booking_id' => absint( $_GET['hb-order-pay'] ) );
        }

        return $args;
    }

    /**
     * generate form html submit authorize
     * @return [html] [form]
     */
    function authorize_form()
    {
        if( empty( $_GET['hb-order-pay'] ) ||
            empty( $_GET['hb-order-pay-nonce'] ) ||
            ! wp_verify_nonce( sanitize_text_field( $_GET['hb-order-pay-nonce'] ), 'hb-order-pay-nonce' ) )
            return;

        $book_id = absint( $_GET['hb-order-pay'] );
        $book = HB_Booking::instance( $book_id );

        // $customer = HB_Customer::instance( TP_Hotel_Booking::instance()->cart->customer_id );//$book->_customer->data;
        $time = time();
        $nonce = wp_create_nonce( 'replay-pay-nonce' );

        /**
         * hb_get_currency() is requirement to generate $fingerprint variable
         */
        if (function_exists('hash_hmac')) {
            $fingerprint = hash_hmac(
                    "md5",
                    $this->_api_login_id . "^" . $book_id . "^" . $time . "^" . $book->advance_payment . "^" . hb_get_currency(),
                    $this->_transaction_key
                );
        }
        else
        {
            $fingerprint = bin2hex(mhash(MHASH_MD5, $this->_api_login_id . "^" . $book_id . "^" . $time . "^" . $book->advance_payment . "^" . hb_get_currency(), $this->_transaction_key));
        }
        // 4007000000027
        $authorize_args = array(
            'x_login'                  => $this->_api_login_id,
            'x_amount'                 => $book->advance_payment,
            'x_currency_code'          => hb_get_currency(),
            'x_invoice_num'            => $book_id,
            'x_relay_response'         => 'FALSE',
            'x_relay_url'              => add_query_arg(
                array('replay-pay' => $book_id, 'replay-pay-nonce' => $nonce ),
                hb_get_return_url()
            ),
            'x_fp_sequence'            => $book_id,
            'x_fp_hash'                => $fingerprint,
            'x_show_form'              => 'PAYMENT_FORM',
            'x_version'                => '3.1',
            'x_fp_timestamp'           => $time,
            'x_first_name'             => $book->customer_first_name,
            'x_last_name'              => $book->customer_last_name,
            'x_address'                => $book->customer_address,
            'x_country'                => $book->customer_country,
            'x_state'                  => $book->customer_state,
            'x_city'                   => $book->customer_city,
            'x_zip'                    => $book->customer_postal_code,
            'x_phone'                  => $book->customer_phone,
            'x_email'                  => $book->customer_email,
            'x_type'                   => 'AUTH_CAPTURE',
            'x_cancel_url'             => hb_get_return_url(),
            'x_email_customer'         => 'TRUE',
            'x_cancel_url_text'        => __( 'Cancel Payment', 'tp-hotel-booking-authorize-sim' ),
            'x_receipt_link_method'    => 'POST',
            'x_receipt_link_text'      => __( 'Click here to return our homepage', 'tp-hotel-booking-authorize-sim' ),
            'x_receipt_link_URL'       => hb_get_return_url(),
        );

        if( $this->_settings['sandbox'] === 'on' )
            $authorize_args['x_test_request'] = 'TRUE';
        else
            $authorize_args['x_test_request'] = 'FALSE';
    ?>
        <form id="tp_hotel_booking_order_pay" action="<?php echo esc_url( $this->_authorize_url ); ?>" method="POST">
            <?php foreach( $authorize_args as $name => $val ): ?>
                <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $val ) ?>" />
            <?php endforeach; ?>
            <button type="submit"><?php _e( 'Pay with Authorize.net', 'tp-hotel-booking-authorize-sim' ) ?></button>
        </form>
        <script type="text/javascript">
            (function($){
                $('#tp_hotel_booking_order_pay').submit();
            })(jQuery);
        </script>
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

    /**
     * display when selected payment method is authorize.net sim
     * @return [html] [description]
     */
    function form(){
        echo _e( 'Pay with Authorize', 'tp-hotel-booking-authorize-sim');
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
    function admin_settings( ){
        $template = TP_HB_AUTHORIZE_DIR . '/inc/views/authorize-sim-settings.php';
        include_once $template;
    }

    /**
     * @return bool
     */
    function is_enable(){
        return empty( $this->_settings['enable'] ) || $this->_settings['enable'] == 'on';
    }
}
