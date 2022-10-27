<?php
/**
 * WP Hotel Booking checkout.
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
 * Class WPHB_Checkout
 */
class WPHB_Checkout {

	/**
	 * @var WPHB_Checkout object instance
	 * @access protected
	 */
	protected static $_instance = null;

	/**
	 * Payment method
	 *
	 * @var string
	 */
	public $payment_method = '';

	/**
	 * Constructor
	 */
	function __construct() {
	}

	/**
	 * Creates temp new booking if needed
	 *
	 * @return mixed|WP_Error
	 * @throws Exception
	 */
	function create_booking( $order = null ) {
		global $hb_settings;
		if ( ! $order ) {
			$order = $this->payment_method;
		}

		// generate transaction
		$transaction = WP_Hotel_Booking::instance()->cart->generate_transaction( $order );
		// allow hook
		$booking_info = apply_filters( 'hotel_booking_checkout_booking_info', $transaction->booking_info, $transaction );
		$order_items  = apply_filters( 'hotel_booking_checkout_booking_order_items', $transaction->order_items, $transaction );

		if ( WP_Hotel_Booking::instance()->cart->cart_items_count === 0 ) {
			hb_send_json(
				array(
					'result'  => 'fail',
					'message' => __( 'Your cart is empty.', 'wp-hotel-booking' ),
				)
			);
			throw new Exception( sprintf( __( 'Sorry, your session has expired. <a href="%s">Return to homepage</a>', 'wp-hotel-booking' ), home_url() ) );
		}

		// load booking id from sessions
		$booking_id = WP_Hotel_Booking::instance()->cart->booking_id;

		// Resume the unpaid order if its pending
		if ( $booking_id && ( $booking = WPHB_Booking::instance( $booking_id ) ) && $booking->post->ID && $booking->has_status( array( 'pending', 'cancelled' ) ) ) {
			$booking_info['ID']           = $booking_id;
			$booking_info['post_content'] = hb_get_request( 'addition_information' );
			$booking->set_booking_info( $booking_info );
			// update booking info meta post
			$booking_id = $booking->update( $order_items );
		} else {
			$booking_id = hb_create_booking( $booking_info, $order_items );
			// initialize Booking object
			$booking = WPHB_Booking::instance( $booking_id );
		}
		
		if ( $booking_id ) {
			// update booking info meta post
			WPHB_Booking::instance( $booking_id )->update_room_booking( $booking_id );
		}

		do_action( 'hb_new_booking', $booking_id );
		return $booking_id;
	}

	/**
	 * Process checkout
	 *
	 * @throws Exception
	 */
	function process_checkout() {
		if ( strtolower( sanitize_text_field( $_SERVER['REQUEST_METHOD'] ?? '' ) ) != 'post' ) {
			return;
		}

		try {
			if ( ! is_user_logged_in() && ! hb_settings()->get( 'guest_checkout' ) ) {
				throw new Exception( __( 'You have to Login to process checkout.', 'wp-hotel-booking' ) );
			}

			// payment method
			$payment_method = hb_get_user_payment_method( hb_get_request( 'hb-payment-method' ) );

			if ( ! $payment_method ) {
				throw new Exception( __( 'The payment method is not available', 'wp-hotel-booking' ) );
			}

			$this->payment_method = $payment_method;
			$booking_id           = $this->create_booking();

			if ( $booking_id ) {
				// if total > 0
				if ( WP_Hotel_Booking::instance()->cart->needs_payment() ) {
					$result = $this->payment_method->process_checkout( $booking_id );
				} else {
					if ( empty( $booking ) ) {
						$booking = WPHB_Booking::instance( $booking_id );
					}
					// No payment was required for order
					$booking->payment_complete();
					$return_url = $booking->get_checkout_booking_received_url();
					$result     = array(
						'result'   => 'success',
						'redirect' => apply_filters( 'hb_checkout_no_payment_needed_redirect', $return_url, $booking ),
					);
				}
				$data_customer = array(
					'title'      => isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '',
					'first_name' => isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '',
					'last_name'  => isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '',
					'email'      => isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '',
					'phone'      => isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '',
					'address'    => isset( $_POST['address'] ) ? sanitize_text_field( $_POST['address'] ) : '',
					'city'       => isset( $_POST['city'] ) ? sanitize_text_field( $_POST['city'] ) : '',
					'state'      => isset( $_POST['state'] ) ? sanitize_text_field( $_POST['state'] ) : '',
					'country'    => isset( $_POST['country'] ) ? sanitize_text_field( $_POST['country'] ) : '',
					'postcode'   => isset( $_POST['postal_code'] ) ? sanitize_text_field( $_POST['postal_code'] ) : '',
				);
				$this->process_customter( $data_customer );

			} else {
				hb_send_json(
					array(
						'result'   => 'success',
						'redirect' => __( 'can not create booking', 'wp-hotel-booking' ),
					)
				);
			}

			if ( ! empty( $result['result'] ) && $result['result'] == 'success' ) {
				if ( strpos( $result['redirect'], 'confirm' ) == false ) {
					WP_Hotel_Booking::instance()->cart->empty_cart();
				}

				$result = apply_filters( 'hb_payment_successful_result', $result, $booking_id );

				do_action( 'hb_place_order', $result, $booking_id );
				if ( hb_is_ajax() ) {
					hb_send_json( $result );
					exit;
				} else {
					wp_redirect( $result['redirect'] );
					exit;
				}
			}
		} catch ( Exception $e ) {
			hb_send_json(
				array(
					'result'  => 'fail',
					'message' => $e->getMessage(),
				)
			);
		}

	}

	/**
	 * It takes an array of data, and updates the user's billing information with it
	 *
	 * @param data The data to be processed.
	 *
	 * @return the value of the  variable.
	 */
	public function process_customter( $data ) {
		if ( empty( $data ) ) {
			return;
		}
		$user_id = get_current_user_id();
		$default = array(
			'title'      => '',
			'first_name' => '',
			'last_name'  => '',
			'email'      => '',
			'phone'      => '',
			'address'    => '',
			'city'       => '',
			'state'      => '',
			'country'    => '',
			'postcode'   => '',
			'fax'        => '',
		);
		$data    = wp_parse_args( $data, $default );
		foreach ( $data as $key => $value ) {
			update_user_meta( $user_id, 'billing_' . $key, $value );
		}
	}

	/**
	 * Get unique instance for this object
	 *
	 * @return WPHB_Checkout
	 */
	static function instance() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

}
