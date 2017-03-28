<?php

/**
 * Class HB_Payment_Gateway_Stripe
 */
class HB_Payment_Gateway_Stripe extends WPHB_Payment_Gateway_Base{

    /**
     * @var array
     */
    protected $_settings = array();

    public $slug = 'stripe';

    protected $_api_endpoint = '';

    /**
     * protected strip secret
     */
    protected $_stripe_secret = null;

    /**
     * protected strip secret
     */
    protected $_stripe_publish = null;

    protected $_stripe = null;

    function __construct(){
        parent::__construct();
        $this->_title = __( 'Stripe', 'wp-hotel-booking-stripe' );
        $this->_description = __( 'Pay with Stripe', 'wp-hotel-booking-stripe' );
        $this->_settings = maybe_unserialize(WPHB_Settings::instance()->get('stripe'));

        $debug = ( ! isset($this->_settings['test_mode']) || $this->_settings['test_mode'] === 'on' ) ? true : false;
        if( ! isset($this->_settings['test_secret_key']) || ! $this->_settings['test_secret_key'] )
            $this->_settings['test_secret_key'] = 'sk_test_NRayUQ1DIth4X091iEH9qzaq';

        if( ! isset($this->_settings['test_publish_key']) || ! $this->_settings['test_publish_key'] )
            $this->_settings['test_publish_key'] = 'pk_test_HHukcwWCsD7qDFWKKpKdJeOT';

        if( ! isset($this->_settings['live_secret_key']) || ! $this->_settings['live_secret_key'] )
            $this->_settings['live_secret_key'] = 'pk_test_HHukcwWCsD7qDFWKKpKdJeOT';

        if( ! isset($this->_settings['live_publish_key']) || ! $this->_settings['live_publish_key'] )
            $this->_settings['live_publish_key'] = 'pk_live_n5AVJxHj8XSFV4HsPIaiFgo3';

        $this->_stripe_secret = $debug ? $this->_settings['test_secret_key'] : $this->_settings['live_secret_key'];
        $this->_stripe_publish = ( ! isset($this->_settings['test_mode']) || $this->_settings['test_mode'] === 'on' ) ? $this->_settings['test_publish_key'] : $this->_settings['live_publish_key'];

        add_action( 'wp_footer', array($this, 'global_js') );
        $this->_api_endpoint = 'https://api.stripe.com/v1';
        $this->init();
    }

    function init(){
        add_action( 'hb_payment_gateway_form_' . $this->slug, array( $this, 'form' ) );
    }

    function admin_settings(){
        include_once TP_HB_STRIPE_DIR . '/inc/views/strip-settings.php';
    }

    function is_enable(){
        return ! empty( $this->_settings['enable'] ) && $this->_settings['enable'] == 'on';
    }

    function process_checkout( $booking_id = null )
    {
        $cart = WPHB_Cart::instance();
        $book = HB_Booking::instance( $booking_id );

        $cus_id = $this->add_customer( $booking_id );

        $advance_pay = (float)$cart->get_advance_payment();

        $request = array(
                'amount'        => round( $advance_pay * 100 ),
                'currency'      => hb_get_currency(),
                'customer'      => $cus_id,
                'description'   => sprintf(
                    __( '%s - Order %s', 'wp-hotel-booking-stripe' ),
                    esc_html( get_bloginfo( 'name' ) ),
                    $book->get_booking_number()
                )
            );

        $response = $this->stripe_request( $request );

        if( is_wp_error( $response ) ) {
            $return = array( 'result' => 'error', 'message' => sprintf( __( '%s. Please try again', 'wp-hotel-booking-stripe' ), $response->get_error_message() ) );
        } else {
            if( $response->id )
            {
                if( (float)$advance_pay === (float)$book->total ) {
                    $book->update_status( 'completed' );
                } else {
                    $book->update_status( 'processing' );
                }
                WP_Hotel_Booking::instance()->cart->empty_cart();
                $return = array(
                    'result'    => 'success',
                    'redirect'  => hb_get_return_url()
                );
            }
            else
            {
                $return = array( 'result' => 'error', 'message' => __( 'Please try again', 'wp-hotel-booking-stripe' ) );
            }
        }

        return $return;
    }

    public function add_customer( $booking_id = null, $customer = null ) {

        $booking = HB_Booking::instance( $booking_id );

        $user_id = $booking->user_id;
        $cus_id = null;
        if ( $user_id ) {
            $cus_id =get_user_meta( $user_id, 'tp-hotel-booking-stripe-id', true );
        } else {
            global $wpdb;
            $sql = $wpdb->prepare("
                    SELECT pm.meta_value FROM $wpdb->postmeta AS pm
                        INNER JOIN $wpdb->postmeta AS pm2 ON pm2.post_id = pm.post_id
                    WHERE
                        pm.meta_key = %s
                        AND pm.meta_value = %s
                        LIMIT 1
                ", 'tp-hotel-booking-stripe-id', $booking->customer_email );
            $cus_id = $wpdb->get_var( $sql );
        }

        if ( ! $cus_id ) {
            // create customer
            try
            {
                $params = array(
                        'description'   => sprintf( '%s %s', __( 'Donor for', 'tp-donate' ), $booking->customer_email ),
                        'source'        => sanitize_text_field( $_POST['id'] ) // token get by stripe.js
                    );
                $response = $this->stripe_request( $params, 'customers' );
                $cus_id = $response->id;
                if ( $user_id ) {
                    update_user_meta( $user_id, 'tp-hotel-booking-stripe-id', $cus_id  );
                } else {
                    update_post_meta( $booking_id, 'tp-hotel-booking-stripe-id', $cus_id );
                }
                add_post_meta( $customer, 'tp-hotel-booking-stripe-id', $cus_id );
                return $cus_id;
            }
            catch( Exception $e )
            {
                return new WP_Error( 'tp-hotel-booking-stripe-error', sprintf( '%s', $e->getMessage() ) );
            }
        }

        return $cus_id;
    }

    public function stripe_request( $request, $api = 'charges' ) {
        $response = wp_remote_post( $this->_api_endpoint . '/' . $api, array(
                'method'        => 'POST',
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode( $this->_stripe_secret . ':' )
                ),
                'body'          => $request,
                'timeout'       => 70,
                'sslverify'     => false,
                'user-agent'    => 'TP Hotel Booking ' . HB_VERSION
        ));

        if ( is_wp_error($response) ) {
            return new WP_Error( 'stripe_error', __('There was a problem connecting to the payment gateway.', 'wp-hotel-booking-stripe') );
        }
        if( empty($response['body']) ) {
            return new WP_Error( 'stripe_error', __('Empty response.', 'wp-hotel-booking-stripe') );
        }

        $parsed_response = json_decode( $response['body'] );
        // Handle response
        if ( ! empty( $parsed_response->error ) ) {
            return new WP_Error( 'stripe_error', $parsed_response->error->message );
        } elseif ( empty( $parsed_response->id ) ) {
            return new WP_Error( 'stripe_error', __('Invalid response.', 'wp-hotel-booking-stripe') );
        }

        return $parsed_response;
    }

    public function global_js()
    {
        echo '<script type="text/javascript">
            TPBooking_Payment_Stripe = {};
            TPBooking_Payment_Stripe.stripe_secret = "'.$this->_stripe_secret.'";
            TPBooking_Payment_Stripe.stripe_publish = "'.$this->_stripe_publish.'";
        </script>';
    }

    function form(){
        echo _e( 'Pay with Credit card', 'wp-hotel-booking-stripe' );
    }
}

add_filter( 'hb_payment_gateways', 'hotel_booking_payment_stripe' );
if( ! function_exists( 'hotel_booking_payment_stripe' ) )
{
    function hotel_booking_payment_stripe( $payments )
    {
        if( array_key_exists( 'stripe', $payments ) )
            return $payments;

        $payments[ 'stripe' ] = new HB_Payment_Gateway_Stripe();
        return $payments;
    }
}