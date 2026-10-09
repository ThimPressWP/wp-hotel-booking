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
	 * @param null $order
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
		$booking_id     = WP_Hotel_Booking::instance()->cart->booking_id;
		$booking        = $booking_id ? WPHB_Booking::instance( $booking_id ) : null;
		$customer_email = $booking_info['_hb_customer_email'] ?? hb_get_request( 'email' );

		// Lightweight availability re-validation before placing order to prevent overbooking
		$cart          = WP_Hotel_Booking::instance()->cart;
		$cart_contents = ( $cart && method_exists( $cart, 'get_cart_contents' ) ) ? $cart->get_cart_contents() : ( $cart->cart_contents ?? array() );
		if ( ! empty( $cart_contents ) && function_exists( 'hotel_booking_get_room_available' ) ) {
			$resuming_id = ( $booking && $this->is_booking_resumable( $booking, $customer_email ) ) ? (int) $booking_id : 0;
			foreach ( $cart_contents as $cart_item ) {
				if ( ! empty( $cart_item->parent_id ) ) {
					continue; // Skip extra packages
				}
				$product_id = absint( $cart_item->product_id ?? 0 );
				$quantity   = absint( $cart_item->quantity ?? 1 );
				$check_in   = $cart_item->check_in_date ?? '';
				$check_out  = $cart_item->check_out_date ?? '';

				if ( ! $product_id || ! $quantity || empty( $check_in ) || empty( $check_out ) ) {
					continue; // Skip if basic dates/product are empty to prevent blocking checkout unexpectedly
				}

				$available_qty = hotel_booking_get_room_available(
					$product_id,
					array(
						'check_in_date'  => $check_in,
						'check_out_date' => $check_out,
						'excerpt'        => $resuming_id ? array( $resuming_id ) : array( 0 ),
					)
				);

				$room_title = get_the_title( $product_id );
				if ( is_numeric( $available_qty ) ) {
					$available_qty = (int) $available_qty;
					if ( $available_qty <= 0 ) {
						throw new Exception( sprintf( __( 'Sorry, "%s" is no longer available for the selected dates. Please update your cart.', 'wp-hotel-booking' ), $room_title ) );
					} elseif ( $quantity > $available_qty ) {
						throw new Exception( sprintf( __( 'Sorry, only %1$d room(s) available for "%2$s" on the selected dates, but your cart has %3$d.', 'wp-hotel-booking' ), $available_qty, $room_title, $quantity ) );
					}
				} elseif ( is_wp_error( $available_qty ) && false !== stripos( $available_qty->get_error_message(), 'not available' ) ) {
					throw new Exception( sprintf( __( 'Sorry, "%s" is no longer available for the selected dates. Please update your cart.', 'wp-hotel-booking' ), $room_title ) );
				}
			}
		}

		// Re-verify coupon validity before placing order to prevent expired or over-limit discounts
		$coupon_id = ! empty( WP_Hotel_Booking::instance()->cart->coupon ) ? absint( WP_Hotel_Booking::instance()->cart->coupon ) : 0;
		if ( $coupon_id && WPHB_Settings::instance()->get( 'enable_coupon' ) && class_exists( 'HB_Coupon' ) ) {
			$coupon = HB_Coupon::instance( $coupon_id );
			$today  = strtotime( gmdate( 'm/d/Y' ) );
			$active = ( $coupon && ! empty( $coupon->post->ID ) && function_exists( 'hb_get_coupons_active' ) )
				? hb_get_coupons_active( $today, $coupon->coupon_code )
				: false;

			$validation = ( $active && $coupon ) ? $coupon->validate() : array( 'is_valid' => false, 'message' => __( 'Coupon code is invalid or has expired.', 'wp-hotel-booking' ) );

			if ( empty( $validation['is_valid'] ) ) {
				WP_Hotel_Booking::instance()->cart->set_customer( 'coupon', null );
				WP_Hotel_Booking::instance()->cart->refresh();
				$coupon_message = ! empty( $validation['message'] ) ? $validation['message'] : __( 'Coupon is no longer valid. Please check your order details.', 'wp-hotel-booking' );
				throw new Exception( $coupon_message );
			}
		}

		if ( $booking && $this->is_booking_resumable( $booking, $customer_email ) ) {
			$booking_info['ID']           = $booking_id;
			$booking_info['post_content'] = isset( $_REQUEST['addition_information'] ) ? sanitize_textarea_field( wp_unslash( $_REQUEST['addition_information'] ) ) : '';
			$booking->set_booking_info( $booking_info );
			// update booking info meta post
			$booking_id = $booking->update( $order_items );
		} else {
			// Invalidate any unverified or stale booking references in session
			if ( WP_Hotel_Booking::instance()->cart->booking_sessions ) {
				WP_Hotel_Booking::instance()->cart->booking_sessions->set( 'booking_id', null );
				WP_Hotel_Booking::instance()->cart->booking_sessions->set( 'booking_key', null );
			}
			$booking_id = hb_create_booking( $booking_info, $order_items );
			// initialize Booking object
			$booking = WPHB_Booking::instance( $booking_id );

			// Securely bind the newly created booking ID and secret token to the current session
			if ( $booking_id && ! is_wp_error( $booking_id ) && ! empty( $booking->booking_key ) ) {
				WP_Hotel_Booking::instance()->cart->set_booking( 'booking_id', $booking_id );
				WP_Hotel_Booking::instance()->cart->set_booking( 'booking_key', $booking->booking_key );
			}
		}

		if ( $booking_id && ! is_wp_error( $booking_id ) ) {
			// update booking info meta post
			WPHB_Booking::instance( $booking_id )->update_room_booking( $booking_id );
		}

		do_action( 'hb_new_booking', $booking_id );
		return $booking_id;
	}

	/**
	 * Verify if an existing booking can be safely resumed by the current user/session.
	 *
	 * Prevents IDOR and booking overwrite vulnerabilities by enforcing strict ownership:
	 * - Only allows bookings currently in 'pending' status (never cancelled).
	 * - For logged-in users: the booking's user_id must strictly match the current user ID.
	 * - For guest checkouts: the booking must belong to a guest (user_id = 0), the session
	 *   must contain the matching cryptographic booking_key, and customer email must match.
	 *
	 * @param int|WPHB_Booking $booking Booking ID or WPHB_Booking instance.
	 * @param string           $customer_email Optional customer email to verify for guests.
	 *
	 * @return bool True if the current user/session is verified to resume this booking, false otherwise.
	 */
	public function is_booking_resumable( $booking, $customer_email = '' ) {
		if ( is_numeric( $booking ) ) {
			$booking_id = absint( $booking );
			if ( ! $booking_id ) {
				return false;
			}
			$booking = WPHB_Booking::instance( $booking_id );
		}

		if ( ! is_object( $booking ) || empty( $booking->post ) || empty( $booking->post->ID ) || ! $booking->has_status( 'pending' ) ) {
			return false;
		}

		if ( is_user_logged_in() ) {
			return (int) $booking->user_id === (int) get_current_user_id();
		}

		// Guest verification: booking must belong to guest, session must hold matching booking_key, and email must match.
		$cart = WP_Hotel_Booking::instance()->cart;
		$session_booking_key = ( $cart && $cart->booking_sessions ) ? $cart->booking_sessions->get( 'booking_key' ) : null;
		$booking_key         = (string) $booking->booking_key;

		if ( (int) $booking->user_id !== 0 || empty( $session_booking_key ) || empty( $booking_key ) ) {
			return false;
		}

		if ( ! hash_equals( $booking_key, (string) $session_booking_key ) ) {
			return false;
		}

		if ( empty( $customer_email ) ) {
			$customer_email = hb_get_request( 'email' );
		}

		$customer_email = strtolower( trim( (string) $customer_email ) );
		$booking_email  = strtolower( trim( (string) $booking->customer_email ) );

		return ( ! empty( $customer_email ) && $customer_email === $booking_email );
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
			// Verify the checkout nonce to prevent CSRF (the form emits this field).
			if ( ! isset( $_POST['hb_customer_place_order_field'] )
				|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['hb_customer_place_order_field'] ) ), 'hb_customer_place_order' ) ) {
				throw new Exception( __( 'Security check failed. Please reload the page and try again.', 'wp-hotel-booking' ) );
			}

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
					'title'      => isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '',
					'first_name' => isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '',
					'last_name'  => isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '',
					'email'      => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
					'phone'      => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
					'address'    => isset( $_POST['address'] ) ? sanitize_text_field( wp_unslash( $_POST['address'] ) ) : '',
					'city'       => isset( $_POST['city'] ) ? sanitize_text_field( wp_unslash( $_POST['city'] ) ) : '',
					'state'      => isset( $_POST['state'] ) ? sanitize_text_field( wp_unslash( $_POST['state'] ) ) : '',
					'country'    => isset( $_POST['country'] ) ? sanitize_text_field( wp_unslash( $_POST['country'] ) ) : '',
					'postcode'   => isset( $_POST['postal_code'] ) ? sanitize_text_field( wp_unslash( $_POST['postal_code'] ) ) : '',
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
