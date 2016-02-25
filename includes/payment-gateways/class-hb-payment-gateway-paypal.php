<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

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
        $this->_slug = 'paypal';
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

    /**
     * Init hooks
     */
    function init(){
        add_action( 'hb_payment_gateway_settings_' . $this->slug, array( $this, 'admin_settings' ) );
        add_action( 'hb_payment_gateway_form_' . $this->slug, array( $this, 'form' ) );
        add_action( 'hb_do_checkout_' . $this->_slug, array( $this, 'process_checkout' ) );
        add_action( 'hb_do_transaction_paypal-standard', array( $this, 'process_booking_paypal_standard' ) );
        add_action( 'hb_web_hook_hotel-booking-paypal-standard', array( $this, 'web_hook_process_paypal_standard' ) );
        add_action( 'hb_manage_booing_column_total', array( $this, 'column_total_content' ), 10, 3 );
        add_filter( 'hb_payment_method_title_paypal', array( $this, 'payment_method_title' ) );
        hb_register_web_hook( 'paypal-standard', 'hotel-booking-paypal-standard' );
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
     * Display text in total column
     *
     * @param $booking_id
     * @param $total
     * @param $total_with_currency
     */
    function column_total_content( $booking_id, $total, $total_with_currency ){
        if( $total && get_post_meta( $booking_id, '_hb_method', true ) == 'paypal-standard' ) {
            $advance_payment = get_post_meta($booking_id, '_hb_advance_payment', true);
            printf(__('<br /><small>(Paid %s%% of %s via %s)</small>', 'tp-hotel-booking'), round( $advance_payment / $total, 2 ) * 100, $total_with_currency, 'Paypal' );
        }
    }


    function form(){
        echo _e( 'Pay with Paypal', 'tp-hotel-booking');
    }

    /**
     * @return bool
     */
    function process_booking_paypal_standard(){
        //return;
        if( ! empty( $_REQUEST['hb-transaction-method'] ) && ( 'paypal-standard' == $_REQUEST['hb-transaction-method'] ) ) {
            $cart = HB_Cart::instance();
            $cart->empty_cart();

            wp_redirect( get_site_url() );
            exit();
            // if we have a paypal-nonce in $_REQUEST that meaning user has clicked go back to our site after finished the transaction
            // so, create a new order
            if( ! empty( $_REQUEST['paypal-nonce'] ) && wp_verify_nonce( sanitize_text_field( $_REQUEST['paypal-nonce'] ), 'hb-paypal-nonce' )  ) {
                if ( !empty( $_REQUEST['tx'] ) ) //if PDT is enabled
                    $transaction_id = $_REQUEST['tx'];
                else if ( !empty( $_REQUEST['txn_id'] ) ) //if PDT is not enabled
                    $transaction_id = $_REQUEST['txn_id'];
                else
                    $transaction_id = NULL;

                if ( !empty( $_REQUEST['cm'] ) )
                    $transient_transaction_id = $_REQUEST['cm'];
                else if ( !empty( $_REQUEST['custom'] ) )
                    $transient_transaction_id = $_REQUEST['custom'];
                else
                    $transient_transaction_id = NULL;

                if ( !empty( $_REQUEST['st'] ) ) //if PDT is enabled
                    $transaction_status = $_REQUEST['st'];
                else if ( !empty( $_REQUEST['payment_status'] ) ) //if PDT is not enabled
                    $transaction_status = $_REQUEST['payment_status'];
                else
                    $transaction_status = NULL;
                if ( ! empty( $transaction_id ) && ! empty( $transient_transaction_id ) && ! empty( $transaction_status ) ) {

                    try {
                        //If the transient still exists, delete it and add the official transaction

                        if ( $transaction_object = hb_get_transient_transaction( 'hbps', $transient_transaction_id ) ) {
                            hb_delete_transient_transaction( 'hbps', $transient_transaction_id  );
                        }
                    }
                    catch ( Exception $e ) {
                        return false;
                    }
                } else if ( is_null( $transaction_id ) && is_null( $transient_transaction_id ) && is_null( $transaction_status ) ) {
                }
            }
        }

        wp_redirect( get_site_url() );
    }

    /**
     * Web hook to process booking with Paypal IPN
     * @param $request
     */
    function web_hook_process_paypal_standard( $request ){
        $payload['cmd'] = '_notify-validate';
        foreach( $_POST as $key => $value ) {
            $payload[$key] = stripslashes( $value );
        }
        $paypal_api_url = ! empty( $_REQUEST['test_ipn'] ) ? $this->paypal_payment_sandbox_url : $this->paypal_payment_live_url;
        $response = wp_remote_post( $paypal_api_url, array( 'body' => $payload ) );
        $body = wp_remote_retrieve_body( $response );
        if ( 'VERIFIED' === $body ) {
            if ( ! empty( $request['txn_type'] ) ) {

                switch ( $request['txn_type'] ) {
                    case 'web_accept':
                        if ( ! empty( $request['custom'] ) && ( $booking = $this->get_booking( $request['custom'] ) ) ) {
                            $request['payment_status'] = strtolower( $request['payment_status'] );

                            if ( isset( $request['test_ipn'] ) && 1 == $request['test_ipn'] && 'pending' == $request['payment_status'] ) {
                                $request['payment_status'] = 'completed';
                            }
                            if ( method_exists( $this, 'payment_status_' . $request['payment_status'] ) ) {
                                call_user_func( array( $this, 'payment_status_' . $request['payment_status'] ), $booking, $request );
                            }
                        }
                        break;

                }
            }
        }
    }

    function get_booking( $raw_custom ){
        $raw_custom = stripslashes( $raw_custom );
        if ( ( $custom = json_decode( $raw_custom ) ) && is_object( $custom ) ) {
            $booking_id  = $custom->booking_id;
            $booking_key = $custom->booking_key;

            // Fallback to serialized data if safe. This is @deprecated in 2.3.11
        } elseif ( preg_match( '/^a:2:{/', $raw_custom ) && ! preg_match( '/[CO]:\+?[0-9]+:"/', $raw_custom ) && ( $custom = maybe_unserialize( $raw_custom ) ) ) {
            $booking_id  = $custom[0];
            $booking_key = $custom[1];

            // Nothing was found
        } else {
            _e( 'Error: Booking ID and key were not found in "custom".', 'tp-hotel-booking' );
            return false;
        }

        if ( ! $booking = HB_Booking::instance( $booking_id ) ) {
            $booking_id = hb_get_booking_id_by_key( $booking_key );
            $booking    = HB_Booking::instance( $booking_id );
        }

        if ( ! $booking || $booking->booking_key !== $booking_key ) {
            printf( __( 'Error: Booking Keys do not match %s and %s.', 'tp-hotel-booking' ) , $booking->booking_key, $booking_key );
            return false;
        }
        return $booking;
    }

    /**
     * Handle a completed payment
     *
     * @param HB_Booking
     * @param Paypal IPN params
     */
    protected function payment_status_completed( $booking, $request ) {
        // Booking status is already completed
        if ( $booking->has_status( 'completed' ) ) {
            exit;
        }

        if ( 'completed' === $request['payment_status'] ) {
            if( (float)$booking->total === (float)$request['payment_gross'] )
            {
                $this->payment_complete( $booking, ( ! empty( $request['txn_id'] ) ? $request['txn_id'] : '' ), __( 'IPN payment completed', 'tp-hotel-booking' ) );
            }
            else
            {
                $booking->update_status( 'processing' );
            }
            // save paypal fee
            if ( ! empty( $request['mc_fee'] ) ) {
                update_post_meta( $booking->post->id, 'PayPal Transaction Fee', $request['mc_fee'] );
            }

        } else {

        }

    }

    /**
     * Handle a pending payment
     *
     * @param  HB_Booking
     * @param Paypal IPN params
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
     * Retrieve order by paypal txn_id
     *
     * @param $txn_id
     * @return int
     */
    function get_order_id( $txn_id ){

        $args = array(
            'meta_key'    => '_hb_method_id',
            'meta_value'  => $txn_id,
            'numberposts' => 1, //we should only have one, so limit to 1
        );

        $bookings = hb_get_bookings( $args );
        if( $bookings ) foreach( $bookings as $booking ){
            return $booking->ID;
        }
        return 0;
    }

    /**
     * Get Paypal checkout url
     *
     * @param $booking_id
     * @return string
     */
    protected function _get_paypal_basic_checkout_url(  $booking_id ){

        $paypal = HB_Settings::instance()->get( 'paypal' );

        //$user = hb_get_current_user();
        // $customer = hb_get_customer( get_transient('hb_current_customer_' . session_id ()) );
        $customer = hb_get_customer( TP_Hotel_Booking::instance()->cart->customer_id );
        $paypal_args = array (
            'cmd'      => '_xclick',
            'amount'   => round( TP_Hotel_Booking::instance()->cart->hb_get_cart_total( ! hb_get_request( 'pay_all' ) ), 2 ),
            'quantity' => '1',
        );

        $booking    = HB_Booking::instance( $booking_id );
        $advance_payment = hb_get_advance_payment();
        $pay_all = hb_get_request( 'pay_all' );

        $nonce = wp_create_nonce( 'hb-paypal-nonce' );
        $paypal_email = $paypal['sandbox'] === 'on' ? $paypal['sandbox_email'] : $paypal['email'];
        $custom = array( 'booking_id' => $booking->id, 'booking_key' => $booking->booking_key );
        if( $advance_payment && ! $pay_all ){
            $custom['advance_payment'] = $advance_payment;
        }
        $query = array(
            'business'      => $paypal_email,
            'item_name'     => hb_get_cart_description(),
            'return'        => add_query_arg( array( 'hb-transaction-method' => 'paypal-standard', 'paypal-nonce' => $nonce ), hb_get_return_url() ),
            'currency_code' => hb_get_currency(),
            'notify_url'    => get_site_url() . '/?' . hb_get_web_hook( 'paypal-standard' ) . '=1',
            'no_note'       => '1',
            'shipping'      => '0',
            'email'         => $customer->data['email'],
            'rm'            => '2',
            'cancel_return' => hb_get_return_url(),
            'custom'        => json_encode( $custom ),
            'no_shipping'   => '1'
        );

        $query = array_merge( $paypal_args, $query );

        $query = apply_filters( 'hb_paypal_standard_query', $query );

        $paypal_payment_url = ( $paypal['sandbox'] === 'on' ? $this->paypal_payment_sandbox_url : $this->paypal_payment_live_url ) . '?' .  http_build_query( $query );

        return $paypal_payment_url;
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
            'redirect'  => $this->_get_paypal_basic_checkout_url( $booking_id  )
        );
    }

    /**
     * Print admin settings page
     *
     * @param $gateway
     */
    function admin_settings( $gateway ){
        $template = TP_Hotel_Booking::instance()->locate( 'includes/admin/views/settings/paypal.php' );
        include_once $template;
    }

    /**
     * @return bool
     */
    function is_enable(){
        return ! empty( $this->_settings['enable'] ) && $this->_settings['enable'] == 'on';
    }
}

add_filter( 'hb_payment_gateways', 'hotel_booking_payment_paypal' );
if( ! function_exists( 'hotel_booking_payment_paypal' ) )
{
    function hotel_booking_payment_paypal( $payments )
    {
        if( array_key_exists( 'paypal', $payments ) )
            return $payments;

        $payments[ 'paypal' ] = new HB_Payment_Gateway_Paypal();
        return $payments;
    }
}