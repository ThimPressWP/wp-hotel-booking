<?php
/**
 * WP Hotel Booking Paypal.
 *
 * @version       1.9.6
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Classes
 * @category      Classes
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

/**
 * Class WPHB_Payment_Gateway_Paypal
 */
class WPHB_Payment_Gateway_Paypal extends WPHB_Payment_Gateway_Base {
	protected $_slug = 'paypal';

	/**
	 * @var null
	 */
	protected $paypal_live_url = null;

	/**
	 * @var null
	 */
	protected $paypal_sandbox_url = null;

	/**
	 * @var null
	 */
	protected $paypal_payment_live_url = null;

	/**
	 * @var null
	 */
	protected $paypal_payment_sandbox_url = null;

	/**
	 * @var null
	 */
	protected $paypal_nvp_api_live_url = null;

	/**
	 * @var null
	 */
	protected $paypal_nvp_api_sandbox_url = null;

	/**
	 * @var null
	 */
	protected $paypal_client_secret = null;
	/**
	 * @var null
	 */
	protected $paypal_client_id = null;
	/**
	 * PayPal webhook ID used to verify REST webhook signatures.
	 *
	 * @var string|null
	 */
	protected $paypal_webhook_id = null;
	/**
	 * @var null
	 */
	protected $api_sandbox_url = 'https://api-m.sandbox.paypal.com/';
	/**
	 * @var string
	 */
	protected $api_live_url = 'https://api-m.paypal.com/';
	/**
	 * @var string|null
	 */
	protected $api_url = null;

	/**
	 * @var array
	 */
	protected $_settings = array();

	/**
	 * Construction
	 */
	function __construct() {
		$this->_title       = __( 'Paypal', 'wp-hotel-booking' );
		$this->_description = __( 'Pay with Paypal', 'wp-hotel-booking' );
		$this->_settings    = WPHB_Settings::instance()->get( 'paypal' );

		parent::__construct();

		$this->paypal_live_url            = 'https://www.paypal.com/';
		$this->paypal_sandbox_url         = 'https://www.sandbox.paypal.com/';
		$this->paypal_payment_live_url    = 'https://www.paypal.com/cgi-bin/webscr';
		$this->paypal_payment_sandbox_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		$this->paypal_nvp_api_live_url    = 'https://api-3t.paypal.com/nvp';
		$this->paypal_nvp_api_sandbox_url = 'https://api-3t.sandbox.paypal.com/nvp';
		$settings                         = $this->_settings;
		if ( ! empty( $this->_settings['use_paypal_rest'] ) && $settings['use_paypal_rest'] == 'on' ) {
			$this->paypal_client_id     = $settings['app_client_id'];
			$this->paypal_client_secret = $settings['app_client_secret'];
			$this->paypal_webhook_id    = ! empty( $settings['webhook_id'] ) ? $settings['webhook_id'] : '';
		}
		$this->api_url = ( ! empty( $settings['sandbox'] ) && $settings['sandbox'] == 'on' ) ? $this->api_sandbox_url : $this->api_live_url;

		$this->init();
	}

	/**
	 * Init hooks
	 */
	function init() {
		add_action( 'hb_payment_gateway_form_' . $this->slug, array( $this, 'form' ) );
		add_action( 'hb_do_checkout_' . $this->_slug, array( $this, 'process_checkout' ) );

		add_action( 'hb_manage_booking_column_total', array( $this, 'column_total_content' ), 10, 3 );
		add_filter( 'hb_payment_method_title_paypal', array( $this, 'payment_method_title' ) );
		hb_register_web_hook( 'paypal-standard', 'hotel-booking-paypal-standard' );
		if ( ! empty( $this->_settings['use_paypal_rest'] ) && $this->_settings['use_paypal_rest'] == 'on' ) {
			$this->capture_payment_for_order();
			// Modern, reliable server-to-server payment confirmation (replaces IPN for REST mode)
			// is handled by WPHB_REST_Paypal_Webhook_Controller, which delegates to
			// $this->handle_webhook_request().
		} else {
			add_action( 'hb_do_transaction_paypal-standard', array( $this, 'process_booking_paypal_standard' ) );
			add_action( 'hb_web_hook_hotel-booking-paypal-standard', array( $this, 'web_hook_process_paypal_standard' ) );
		}
	}

	/**
	 * Get payment method title
	 *
	 * @return mixed
	 */
	function payment_method_title() {
		return $this->_description;
	}

	/**
	 * Display text in total column
	 *
	 * @param int    $booking_id          Booking ID.
	 * @param float  $total               Booking total.
	 * @param string $total_with_currency Formatted booking total.
	 *
	 * @return void
	 */
	function column_total_content( $booking_id, $total, $total_with_currency ) {
		if ( $total && get_post_meta( $booking_id, '_hb_method', true ) == 'paypal-standard' ) {
			$advance_payment = get_post_meta( $booking_id, '_hb_advance_payment', true );
			printf( __( '<br /><small>(Paid %1$s%% of %2$s via %3$s)</small>', 'wp-hotel-booking' ), round( $advance_payment / $total, 2 ) * 100, $total_with_currency, 'Paypal' );
		}
	}

	/**
	 * Render the frontend payment method label.
	 *
	 * @return void
	 */
	function form() {
		_e( 'Pay with Paypal', 'wp-hotel-booking' );
	}

	/**
	 * Handle the customer return request after PayPal Standard checkout.
	 *
	 * @return void
	 */
	function process_booking_paypal_standard() {
		if ( ! empty( $_REQUEST['hb-transaction-method'] ) && ( 'paypal-standard' == sanitize_text_field( wp_unslash( $_REQUEST['hb-transaction-method'] ) ) ) ) {
			$cart = WPHB_Cart::instance();
			$cart->empty_cart();

			wp_redirect( get_site_url() );
			exit();
		}

		wp_redirect( get_site_url() );
		exit();
	}

	/**
	 * Process a PayPal Standard IPN webhook.
	 *
	 * The IPN payload is verified by posting the original request body back to
	 * the configured PayPal environment before any booking status is changed.
	 *
	 * @param array $request IPN request data.
	 *
	 * @return void
	 */
	function web_hook_process_paypal_standard( $request ) {

		$request        = wp_unslash( $request );
		$payload        = wp_unslash( $_POST );
		$payload['cmd'] = '_notify-validate';
		$paypal_api_url = $this->is_paypal_sandbox_enabled() ? $this->paypal_payment_sandbox_url : $this->paypal_payment_live_url;

		$params   = array(
			'body'        => $payload,
			'timeout'     => 60,
			'httpversion' => '1.1',
			'compress'    => false,
			'decompress'  => false,
			'user-agent'  => 'HotelBooking',
		);
		$response = wp_safe_remote_post( $paypal_api_url, $params );
		if ( is_wp_error( $response ) ) {
			return;
		}

		$body = wp_remote_retrieve_body( $response );

		if ( 'VERIFIED' === $body ) {
			if ( ! empty( $request['txn_type'] ) ) {
				$txn_type = is_scalar( $request['txn_type'] ) ? sanitize_key( $request['txn_type'] ) : '';

				switch ( $txn_type ) {
					case 'web_accept':
						if ( ! empty( $request['custom'] ) && is_scalar( $request['custom'] ) && ( $booking = $this->get_booking( $request['custom'] ) ) ) {
							$request['payment_status'] = ( ! empty( $request['payment_status'] ) && is_scalar( $request['payment_status'] ) ) ? sanitize_key( $request['payment_status'] ) : '';

							if ( method_exists( $this, 'payment_status_' . $request['payment_status'] ) ) {
								call_user_func(
									array(
										$this,
										'payment_status_' . $request['payment_status'],
									),
									$booking,
									$request
								);
							}
						}
						break;

				}
			}
		}
	}

	/**
	 * Resolve a booking from the PayPal custom payload.
	 *
	 * @param string $raw_custom Raw PayPal custom payload.
	 *
	 * @return WPHB_Booking|false Booking instance on success, false on failure.
	 */
	function get_booking( $raw_custom ) {
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
			_e( 'Error: Booking ID and key were not found in "custom".', 'wp-hotel-booking' );

			return false;
		}

		if ( ! $booking = WPHB_Booking::instance( $booking_id ) ) {
			$booking_id = hb_get_booking_id_by_key( $booking_key );
			$booking    = WPHB_Booking::instance( $booking_id );
		}

		if ( ! $booking || $booking->booking_key !== $booking_key ) {
			printf( __( 'Error: Booking Keys do not match %1$s and %2$s.', 'wp-hotel-booking' ), $booking->booking_key, $booking_key );

			return false;
		}

		return $booking;
	}

	/**
	 * Handle a completed payment
	 *
	 * @param WPHB_Booking $booking Booking instance.
	 * @param array        $request PayPal IPN request data.
	 *
	 * @return void
	 */
	protected function payment_status_completed( $booking, $request ) {
		// Booking status is already completed
		if ( $booking->has_status( 'completed' ) ) {
			exit;
		}

		if ( 'completed' === $request['payment_status'] ) {
			if ( ! $this->validate_paypal_standard_ipn( $booking, $request ) ) {
				return;
			}

			$payment_gross = $this->get_paypal_payment_gross( $request );
			$txn_id        = sanitize_text_field( $request['txn_id'] );

			if ( (float) $booking->total === (float) $payment_gross ) {
				$this->payment_complete( $booking, $txn_id, __( 'IPN payment completed', 'wp-hotel-booking' ) );
			} else {
				$booking->update_status( 'processing' );
			}
			// save paypal fee
			if ( ! empty( $request['mc_fee'] ) ) {
				update_post_meta( $booking->id, 'PayPal Transaction Fee', $request['mc_fee'] );
			}
		} else {

		}
	}

	/**
	 * Handle a pending payment
	 *
	 * @param WPHB_Booking $booking Booking instance.
	 * @param array        $request PayPal IPN request data.
	 *
	 * @return void
	 */
	protected function payment_status_pending( $booking, $request ) {
		$this->payment_status_completed( $booking, $request );
	}

	/**
	 * Mark the booking payment as complete.
	 *
	 * @param WPHB_Booking $booking Booking instance.
	 * @param string       $txn_id  PayPal transaction ID.
	 * @param string       $note    Payment note. Unused.
	 *
	 * @return void
	 */
	function payment_complete( $booking, $txn_id = '', $note = '' ) {
		$booking->payment_complete( $txn_id );
	}

	/**
	 * Retrieve order by paypal txn_id
	 *
	 * @param string $txn_id PayPal transaction ID.
	 *
	 * @return int Booking ID, or 0 when no booking is found.
	 */
	function get_order_id( $txn_id ) {

		$args     = array(
			'meta_key'    => '_transaction_id',
			'meta_value'  => $txn_id,
			'numberposts' => 1, // we should only have one, so limit to 1
		);
		$bookings = hb_get_bookings( $args );
		if ( $bookings ) {
			foreach ( $bookings as $booking ) {
				return $booking->ID;
			}
		}

		return 0;
	}

	/**
	 * Check whether PayPal sandbox mode is enabled.
	 *
	 * @return bool
	 */
	protected function is_paypal_sandbox_enabled() {
		return ! empty( $this->_settings['sandbox'] ) && 'on' === $this->_settings['sandbox'];
	}

	/**
	 * Get the expected PayPal receiver email for the active environment.
	 *
	 * @return string PayPal account email.
	 */
	protected function get_paypal_receiver_email() {
		$settings = wp_parse_args(
			$this->_settings,
			array(
				'email'         => '',
				'sandbox'       => 'off',
				'sandbox_email' => '',
			)
		);

		return $this->is_paypal_sandbox_enabled() ? $settings['sandbox_email'] : $settings['email'];
	}

	/**
	 * Get the gross payment amount from the IPN payload.
	 *
	 * @param array $request PayPal IPN request data.
	 *
	 * @return string|int|float|null Gross payment amount, or null when missing.
	 */
	protected function get_paypal_payment_gross( $request ) {
		if ( isset( $request['mc_gross'] ) && is_scalar( $request['mc_gross'] ) && is_numeric( $request['mc_gross'] ) ) {
			return $request['mc_gross'];
		}

		if ( isset( $request['payment_gross'] ) && is_scalar( $request['payment_gross'] ) && is_numeric( $request['payment_gross'] ) ) {
			return $request['payment_gross'];
		}

		return null;
	}

	/**
	 * Validate the PayPal Standard IPN payload before completing a booking.
	 *
	 * @param WPHB_Booking $booking Booking instance.
	 * @param array        $request PayPal IPN request data.
	 *
	 * @return bool True when the IPN matches the booking and PayPal settings.
	 */
	protected function validate_paypal_standard_ipn( $booking, $request ) {
		if ( 'paypal-standard' !== $booking->get_post_meta( '_hb_method', '', true ) ) {
			return false;
		}

		if ( ! empty( $request['test_ipn'] ) && ! $this->is_paypal_sandbox_enabled() ) {
			return false;
		}

		$expected_receiver_email = sanitize_email( $this->get_paypal_receiver_email() );
		$receiver_email          = '';
		if ( ! empty( $request['receiver_email'] ) && is_scalar( $request['receiver_email'] ) ) {
			$receiver_email = sanitize_email( $request['receiver_email'] );
		} elseif ( ! empty( $request['business'] ) && is_scalar( $request['business'] ) ) {
			$receiver_email = sanitize_email( $request['business'] );
		}

		if ( empty( $expected_receiver_email ) || empty( $receiver_email ) || 0 !== strcasecmp( $expected_receiver_email, $receiver_email ) ) {
			return false;
		}

		$expected_currency = $booking->get_post_meta( '_hb_currency', '', true );
		if ( empty( $expected_currency ) ) {
			$expected_currency = hb_get_currency();
		}

		if ( empty( $request['mc_currency'] ) || ! is_scalar( $request['mc_currency'] ) || strtoupper( sanitize_text_field( $request['mc_currency'] ) ) !== strtoupper( sanitize_text_field( $expected_currency ) ) ) {
			return false;
		}

		if ( null === $this->get_paypal_payment_gross( $request ) ) {
			return false;
		}

		if ( empty( $request['txn_id'] ) || ! is_scalar( $request['txn_id'] ) ) {
			return false;
		}

		$txn_id = sanitize_text_field( $request['txn_id'] );
		if ( $this->get_order_id( $txn_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Request a PayPal REST API access token.
	 *
	 * @return stdClass PayPal token response.
	 * @throws Exception When credentials or response data are invalid.
	 */
	public function get_paypal_access_token() {
		if ( empty( $this->paypal_client_id ) ) {
			throw new Exception( 'Paypal App Client id is required.', 'wp-hotel-booking' );
		}
		if ( empty( $this->paypal_client_secret ) ) {
			throw new Exception( 'Paypal App Client secret is required.', 'wp-hotel-booking' );
		}
		$params         = array( 'grant_type' => 'client_credentials' );
		$response       = wp_remote_post(
			$this->api_url . 'v1/oauth2/token',
			array(
				'body'    => $params,
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( $this->paypal_client_id . ':' . $this->paypal_client_secret ),
				),
				'timeout' => 60,
			)
		);
		$data_token_str = wp_remote_retrieve_body( $response );
		$data_token     = json_decode( $data_token_str );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			throw new Exception( json_last_error_msg() );
		}
		return $data_token;
	}

	/**
	 * Create a PayPal REST checkout order and return the approval URL.
	 *
	 * @param int   $booking_id Booking ID.
	 * @param float $amount     Amount to charge.
	 *
	 * @return string PayPal approval URL.
	 * @throws Exception When booking, token, or checkout response data are invalid.
	 */
	public function get_app_payment_url( $booking_id, $amount ) {
		$checkout_url = '';
		if ( ! $booking_id ) {
			throw new Exception( 'Invalid booking.', 'wp-hotel-booking' );
		}
		$booking      = WPHB_Booking::instance( $booking_id );
		$success_url  = add_query_arg( 'paypay_express_checkout', 1, hb_get_thank_you_url( $booking_id, $booking->booking_key ) );
		$cancel_url   = hb_get_checkout_url();
		$booking_data = array(
			'intent'         => 'CAPTURE',
			'purchase_units' => array(
				array(
					'amount'    => array(
						'currency_code' => hb_get_currency(),
						'value'         => number_format( $amount, 2 ),
					),
					'custom_id' => $booking_id,
				),
			),
			'payment_source' => array(
				'paypal' => array(
					'experience_context' => array(
						'payment_method_preference' => 'UNRESTRICTED',
						'brand_name'                => get_bloginfo(),
						'landing_page'              => 'LOGIN',
						'user_action'               => 'PAY_NOW',
						'return_url'                => $success_url,
						'cancel_url'                => $cancel_url,
					),
				),
			),
		);
		$booking_data = apply_filters( 'wp-hotel-booking/paypal-rest/args', $booking_data, $booking_id );
		$data_token   = $this->get_paypal_access_token();
		if ( ! isset( $data_token->access_token ) || ! isset( $data_token->token_type ) ) {
			throw new Exception( __( 'Invalid Paypal access token', 'wp-hotel-booking' ) );
		}
		$response   = wp_remote_post(
			$this->api_url . 'v2/checkout/orders',
			array(
				'body'    => json_encode( $booking_data ),
				'headers' => array(
					'Authorization' => $data_token->token_type . ' ' . $data_token->access_token,
					'Content-Type'  => 'application/json',
				),
				'timeout' => 60,
			)
		);
			$result = json_decode( wp_remote_retrieve_body( $response ) );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			throw new Exception( json_last_error_msg() );
		}

		if ( empty( $result->links ) ) {
			throw new Exception( __( 'Invalid Paypal checkout url', 'wp-hotel-booking' ) );
		}

		foreach ( $result->links as $link ) {
			if ( $link->rel === 'payer-action' ) {
				$checkout_url = $link->href;
				break;
			}
		}

		if ( empty( $checkout_url ) ) {
			throw new Exception( __( 'Invalid Paypal checkout url', 'wp-hotel-booking' ) );
		}

		return $checkout_url;
	}

	/**
	 * Capture a PayPal REST checkout order after the customer returns.
	 *
	 * @return void
	 */
	public function capture_payment_for_order() {
		if ( ! isset( $_GET['paypay_express_checkout'] ) ) {
			return;
		}
		$paypal_order_id = hb_get_request( 'token' );
		if ( empty( $paypal_order_id ) ) {
			return;
		}
		try {
			$data_token = $this->get_paypal_access_token();
			if ( ! isset( $data_token->access_token ) || ! isset( $data_token->token_type ) ) {
				throw new Exception( __( 'Invalid Paypal access token', 'wp-hotel-booking' ) );
			}

			$response = wp_remote_post(
				$this->api_url . 'v2/checkout/orders/' . $paypal_order_id . '/capture',
				array(
					'headers' => array(
						'Content-Type'  => 'application/json',
						'Authorization' => $data_token->token_type . ' ' . $data_token->access_token,
					),
					'timeout' => 60,
				)
			);

			if ( $response['response']['code'] === 201 ) {
				$body        = wp_remote_retrieve_body( $response );
				$transaction = json_decode( $body );
				if ( json_last_error() !== JSON_ERROR_NONE ) {
					throw new Exception( json_last_error_msg() );
				}
				if ( $transaction->status === 'COMPLETED' ) {
					$booking_id  = $transaction->purchase_units[0]->payments->captures[0]->custom_id;
					$booking     = WPHB_Booking::instance( $booking_id );
					$paid_amount = $transaction->purchase_units[0]->payments->captures[0]->amount->value;
					if ( (float) $paid_amount == (float) $booking->total() ) {
						$booking->update_status( 'completed' );
					} else {
						$booking->update_status( 'processing' );
					}
				}
			}
		} catch ( Throwable $e ) {
			error_log( __METHOD__ . $e->getMessage() );
		}
	}

	/**
	 * Process a verified PayPal REST webhook.
	 *
	 * Modern replacement for the legacy IPN flow. The listener route is registered by
	 * WPHB_REST_Paypal_Webhook_Controller ( /wp-json/wphb/v1/paypal/webhook ), which
	 * delegates here; we verify the signature with PayPal before changing any booking status.
	 *
	 * @param WP_REST_Request $request Incoming webhook request.
	 *
	 * @return WP_REST_Response
	 */
	public function handle_webhook_request( WP_REST_Request $request ) {
		$event = json_decode( $request->get_body() );

		if ( ! is_object( $event ) || empty( $event->event_type ) || empty( $event->resource ) ) {
			return new WP_REST_Response( array( 'status' => 'ignored' ), 200 );
		}

		// Signature verification is the authentication for this endpoint.
		if ( ! $this->verify_webhook_signature( $event, $request ) ) {
			return new WP_REST_Response( array( 'status' => 'invalid_signature' ), 401 );
		}

		$resource = $event->resource;

		// We set custom_id = booking_id when creating the PayPal order.
		$booking_id = isset( $resource->custom_id ) ? absint( $resource->custom_id ) : 0;
		if ( ! $booking_id && isset( $resource->purchase_units[0]->custom_id ) ) {
			$booking_id = absint( $resource->purchase_units[0]->custom_id );
		}
		if ( ! $booking_id ) {
			return new WP_REST_Response( array( 'status' => 'no_booking' ), 200 );
		}

		$booking = WPHB_Booking::instance( $booking_id );
		if ( ! $booking || ! $booking->id ) {
			return new WP_REST_Response( array( 'status' => 'no_booking' ), 200 );
		}

		// Idempotency: PayPal retries deliveries, so ignore events already handled.
		$event_id = isset( $event->id ) ? sanitize_text_field( $event->id ) : '';
		if ( $event_id ) {
			$processed = (array) get_post_meta( $booking->id, '_hb_paypal_webhook_events', true );
			if ( in_array( $event_id, $processed, true ) ) {
				return new WP_REST_Response( array( 'status' => 'duplicate' ), 200 );
			}
		}

		switch ( $event->event_type ) {
			case 'PAYMENT.CAPTURE.COMPLETED':
				if ( $booking->has_status( 'completed' ) ) {
					break;
				}

				// Re-validate currency against the booking (a valid signature only proves PayPal sent it).
				$currency          = isset( $resource->amount->currency_code ) ? strtoupper( sanitize_text_field( $resource->amount->currency_code ) ) : '';
				$expected_currency = strtoupper( $booking->get_post_meta( '_hb_currency', hb_get_currency(), true ) );
				if ( $currency && $expected_currency && $currency !== $expected_currency ) {
					break;
				}

				$amount = isset( $resource->amount->value ) ? (float) $resource->amount->value : null;
				$txn_id = isset( $resource->id ) ? sanitize_text_field( $resource->id ) : '';

				// Mirror capture_payment_for_order(): full amount completes, partial keeps processing.
				if ( null !== $amount && (float) $booking->total() === $amount ) {
					$booking->payment_complete( $txn_id );
				} else {
					$booking->update_status( 'processing' );
				}
				break;

			case 'PAYMENT.CAPTURE.REFUNDED':
			case 'PAYMENT.CAPTURE.REVERSED':
				$booking->update_status( 'cancelled' );
				break;
		}

		if ( $event_id ) {
			$processed   = (array) get_post_meta( $booking->id, '_hb_paypal_webhook_events', true );
			$processed[] = $event_id;
			update_post_meta( $booking->id, '_hb_paypal_webhook_events', array_slice( array_values( array_unique( $processed ) ), -50 ) );
		}

		return new WP_REST_Response( array( 'status' => 'ok' ), 200 );
	}

	/**
	 * Verify a PayPal REST webhook signature via the PayPal API.
	 *
	 * @param object          $event   Decoded webhook event payload.
	 * @param WP_REST_Request $request Incoming webhook request (supplies the PayPal transmission headers).
	 *
	 * @return bool True only when PayPal returns verification_status === SUCCESS.
	 */
	protected function verify_webhook_signature( $event, WP_REST_Request $request ) {
		if ( empty( $this->paypal_webhook_id ) ) {
			return false;
		}

		$cert_url = (string) $request->get_header( 'paypal-cert-url' );
		// SSRF / cert-spoof guard: the certificate must be served from a PayPal host.
		$cert_host = wp_parse_url( $cert_url, PHP_URL_HOST );
		if ( ! $cert_host || ! preg_match( '/(^|\.)paypal\.com$/i', $cert_host ) ) {
			return false;
		}

		$token = $this->get_access_token_cached();
		if ( '' === $token ) {
			return false;
		}

		$body = array(
			'auth_algo'         => (string) $request->get_header( 'paypal-auth-algo' ),
			'cert_url'          => $cert_url,
			'transmission_id'   => (string) $request->get_header( 'paypal-transmission-id' ),
			'transmission_sig'  => (string) $request->get_header( 'paypal-transmission-sig' ),
			'transmission_time' => (string) $request->get_header( 'paypal-transmission-time' ),
			'webhook_id'        => $this->paypal_webhook_id,
			'webhook_event'     => $event,
		);

		foreach ( array( 'auth_algo', 'transmission_id', 'transmission_sig', 'transmission_time' ) as $required ) {
			if ( empty( $body[ $required ] ) ) {
				return false;
			}
		}

		$response = wp_remote_post(
			$this->api_url . 'v1/notifications/verify-webhook-signature',
			array(
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $token,
				),
				'body'    => wp_json_encode( $body ),
				'timeout' => 60,
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$result = json_decode( wp_remote_retrieve_body( $response ) );

		return isset( $result->verification_status ) && 'SUCCESS' === $result->verification_status;
	}

	/**
	 * Get a cached PayPal REST access token.
	 *
	 * Caches per environment + credentials so webhook verification does not request
	 * a new token on every notification (PayPal can deliver/retry many events).
	 *
	 * @return string Access token, or '' on failure.
	 */
	protected function get_access_token_cached() {
		$cache_key = 'wphb_pp_token_' . md5( $this->api_url . '|' . $this->paypal_client_id . '|' . $this->paypal_client_secret );
		$cached    = get_transient( $cache_key );
		if ( is_string( $cached ) && '' !== $cached ) {
			return $cached;
		}

		try {
			$data_token = $this->get_paypal_access_token();
		} catch ( Throwable $e ) {
			error_log( __METHOD__ . ': ' . $e->getMessage() );

			return '';
		}

		if ( empty( $data_token->access_token ) ) {
			return '';
		}

		$expires_in = isset( $data_token->expires_in ) ? max( 60, (int) $data_token->expires_in - 60 ) : 300;
		set_transient( $cache_key, $data_token->access_token, $expires_in );

		return $data_token->access_token;
	}

	/**
	 * Get PayPal Standard checkout URL.
	 *
	 * @param int $booking_id Booking ID.
	 *
	 * @return string PayPal checkout URL.
	 */
	protected function _get_paypal_basic_checkout_url( $booking_id ) {

		$paypal = WPHB_Settings::instance()->get( 'paypal' );

		$paypal_args = array(
			'cmd'      => '_xclick',
			'amount'   => round( WP_Hotel_Booking::instance()->cart->hb_get_cart_total( ! hb_get_request( 'pay_all' ) ), 2 ),
			'quantity' => '1',
		);

		$booking         = WPHB_Booking::instance( $booking_id );
		$advance_payment = hb_get_advance_payment();
		$pay_all         = hb_get_request( 'pay_all' );

		$nonce        = wp_create_nonce( 'hb-paypal-nonce' );
		$paypal_email = $paypal['sandbox'] === 'on' ? $paypal['sandbox_email'] : $paypal['email'];
		$custom       = array(
			'booking_id'  => $booking_id,
			'booking_key' => $booking->booking_key,
		);
		if ( $advance_payment && ! $pay_all ) {
			$custom['advance_payment'] = $advance_payment;
		}

		$query = array(
			'business'      => $paypal_email,
			'item_name'     => hb_get_cart_description(),
			'return'        => hb_get_thank_you_url( $booking_id, $booking->booking_key ),
			'currency_code' => hb_get_currency(),
			'notify_url'    => get_site_url() . '/?' . hb_get_web_hook( 'paypal-standard' ) . '=1',
			'no_note'       => '1',
			'shipping'      => '0',
			'email'         => $booking->customer_email,
			'rm'            => '2',
			'cancel_return' => hb_get_return_url(),
			'custom'        => json_encode( $custom ),
			'no_shipping'   => '1',
		);

		$query = array_merge( $paypal_args, $query );

		$query = apply_filters( 'hb_paypal_standard_query', $query );

		$paypal_payment_url = ( $paypal['sandbox'] === 'on' ? $this->paypal_payment_sandbox_url : $this->paypal_payment_live_url ) . '?' . http_build_query( $query );

		return $paypal_payment_url;
	}

	/**
	 * Process checkout and return the gateway redirect response.
	 *
	 * @param int|null $booking_id Booking ID.
	 *
	 * @return array Checkout result and redirect URL.
	 */
	function process_checkout( $booking_id = null ) {
		if ( $this->_settings['use_paypal_rest'] == 'on' ) {
			$cart        = WPHB_Cart::instance();
			$advance_pay = $cart->get_advance_payment();
			$cart_total  = $cart->get_total();
			if ( ! hb_get_request( 'pay_all' ) ) {
				// when advance pay setting = 0%, amount is cart total
				$amount_total = $advance_pay > 0 ? $advance_pay : $cart_total;
			} else {
				$amount_total = $cart_total;
			}
			return array(
				'result'   => 'success',
				'redirect' => $this->get_app_payment_url( $booking_id, floatval( $amount_total ) ),
			);
		} else {
			return array(
				'result'   => 'success',
				'redirect' => $this->_get_paypal_basic_checkout_url( $booking_id ),
			);
		}
	}

	/**
	 * Print admin settings page.
	 *
	 * @return void
	 */
	function admin_settings() {
		$template = WP_Hotel_Booking::instance()->locate( 'includes/gateways/paypal/views/settings.php' );
		include_once $template;
	}

	/**
	 * Check whether this gateway is enabled.
	 *
	 * @return bool
	 */
	function is_enable() {
		return ! empty( $this->_settings['enable'] ) && $this->_settings['enable'] == 'on';
	}
}

add_filter( 'hb_payment_gateways', 'hotel_booking_payment_paypal' );
if ( ! function_exists( 'hotel_booking_payment_paypal' ) ) {
	/**
	 * Register the PayPal gateway.
	 *
	 * @param array $payments Registered payment gateways.
	 *
	 * @return array Registered payment gateways.
	 */
	function hotel_booking_payment_paypal( $payments ) {
		if ( array_key_exists( 'paypal', $payments ) ) {
			return $payments;
		}

		$payments['paypal'] = new WPHB_Payment_Gateway_Paypal();

		return $payments;
	}
}
