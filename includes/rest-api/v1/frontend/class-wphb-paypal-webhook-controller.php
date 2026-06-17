<?php
/**
 * Class WPHB_REST_Paypal_Webhook_Controller
 *
 * Frontend REST listener for PayPal REST webhooks
 * ( endpoint: /wp-json/wphb/v1/paypal/webhook ).
 *
 * Transport layer only: signature verification and booking updates are
 * delegated to the PayPal gateway, which owns the PayPal credentials and
 * REST API logic.
 *
 * @package WP_Hotel_Booking/REST
 */

defined( 'ABSPATH' ) || exit;

class WPHB_REST_Paypal_Webhook_Controller extends WPHB_Abstract_REST_Controller {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->namespace = 'wphb/v1';
		$this->rest_base = 'paypal';
		parent::__construct();
	}

	/**
	 * Register routes API.
	 */
	public function register_routes() {
		$this->routes = array(
			'webhook' => array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'process_webhook' ),
					// Authentication for this endpoint is the PayPal signature verification, not a WP user.
					'permission_callback' => '__return_true',
				),
			),
		);

		parent::register_routes();
	}

	/**
	 * Receive a PayPal webhook and hand it to the PayPal gateway for verification/processing.
	 *
	 * @param WP_REST_Request $request The incoming webhook request.
	 *
	 * @return WP_REST_Response
	 */
	public function process_webhook( WP_REST_Request $request ) {
		$gateways = hb_get_payment_gateways();
		$paypal   = isset( $gateways['paypal'] ) ? $gateways['paypal'] : null;

		if ( ! $paypal || ! method_exists( $paypal, 'handle_webhook_request' ) ) {
			return new WP_REST_Response( array( 'status' => 'unavailable' ), 404 );
		}

		return $paypal->handle_webhook_request( $request );
	}
}
