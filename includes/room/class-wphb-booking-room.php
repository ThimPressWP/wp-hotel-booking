<?php
/**
 * HB_Report_Room
 *
 * @author   ThimPress
 * @package  WP-Hotel-Booking/Booking-Room/Classes
 * @version  1.7.2
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_Hotel_Booking_Room_Extension' ) ) {
	/**
	 * Class WP_Hotel_Booking_Room_Extension
	 */
	class WP_Hotel_Booking_Room_Extension {

		/**
		 * @var null
		 */
		private static $instance = null;

		/**
		 * WP_Hotel_Booking_Room_Extension constructor.
		 */
		public function __construct() {
			$this->init();
		}

		/**
		 * Init.
		 */
		public function init() {

			add_action( 'hotel_booking_single_room_title', array( $this, 'single_add_button' ), 9 );

			add_action( 'wp_footer', array( $this, 'wp_footer' ) );
			// enqueue script
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

			add_action( 'wp_ajax_check_room_availabel', array( $this, 'check_room_availabel' ) );
			add_action( 'wp_ajax_nopriv_check_room_availabel', array( $this, 'check_room_availabel' ) );

			add_filter( 'hotel_booking_add_to_cart_results', array( $this, 'add_to_cart_redirect' ), 10, 2 );

			add_action(
				'wp_ajax_hotel_booking_single_check_room_available',
				array(
					$this,
					'hotel_booking_single_check_room_available',
				)
			);
			add_action(
				'wp_ajax_nopriv_hotel_booking_single_check_room_available',
				array(
					$this,
					'hotel_booking_single_check_room_available',
				)
			);
		}

		/**
		 * Single search button.
		 */
		public function single_add_button() {
			ob_start();
			hb_get_template( 'single-room/buttons/search.php' );
			$html = ob_get_clean();
			echo $html;
		}

		/**
		 * WP Footer.
		 */
		public function wp_footer() {
			$html = array();
			ob_start();
			// search form.
			hb_get_template( 'single-room/search/search-available.php' );
			// book form.
			hb_get_template( 'single-room/search/book-room.php' );
			$html[] = ob_get_clean();
			echo implode( '', $html );
		}

		/**
		 * Enqueue script.
		 */
		public function enqueue() {

			$dependencies = array(
				'jquery',
				'jquery-ui-sortable',
				'jquery-ui-datepicker',
				'wp-util',
				'wp-api-fetch',
			);

			wp_enqueue_script( 'jquery-ui-datepicker' );
			// magnific popup
			wp_enqueue_style( 'wp-hotel-booking-magnific-popup-css', WPHB_PLUGIN_URL . '/includes/libraries/magnific-popup/css/magnific-popup.css', array(), WPHB_VERSION );
			wp_enqueue_script( 'wp-hotel-booking-magnific-popup-js', WPHB_PLUGIN_URL . '/includes/libraries/magnific-popup/js/jquery.magnific-popup.min.js', $dependencies );
			wp_register_script( 'wp-hotel-booking-single-room-js', WPHB_PLUGIN_URL . '/assets/js/booking-single-room.js', $dependencies );
			wp_enqueue_style( 'wp-hotel-booking-single-room-css', WPHB_PLUGIN_URL . '/assets/css/booking-single-room.css', array(), WPHB_VERSION );

			$l10n = apply_filters(
				'hote_booking_blocked_days_l10n',
				array(
					'blocked_days'  => wp_hotel_booking_blocked_days(),
					'external_link' => is_singular( 'hb_room' ) ? get_post_meta( get_the_ID(), '_hb_external_link', true ) : '',
				)
			);
			wp_localize_script( 'wp-hotel-booking-single-room-js', 'Hotel_Booking_Blocked_Days', $l10n );
		}

		/**
		 * Check room available.
		 */
		public function check_room_availabel() {
			// ajax referer
			if ( ! isset( $_POST['check-room-availabel-nonce'] ) || ! check_ajax_referer( 'check_room_availabel_nonce', 'check-room-availabel-nonce' ) ) {
				return;
			}

			$room_id = false;
			if ( isset( $_POST['hotel_booking_room_id'] ) && is_numeric( $_POST['hotel_booking_room_id'] ) ) {
				$room_id = absint( $_POST['hotel_booking_room_id'] );
			}

			$check_in_date = isset( $_POST['hotel_booking_room_check_in_timestamp'] ) ? sanitize_text_field( $_POST['hotel_booking_room_check_in_timestamp'] ) : '';
			$check_in_date = (int) $check_in_date + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );

			$check_out_date = isset( $_POST['hotel_booking_room_check_out_timestamp'] ) ? sanitize_text_field( $_POST['hotel_booking_room_check_out_timestamp'] ) : '';
			$check_out_date = (int) $check_out_date + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );

			$args = apply_filters(
				'hotel_booking_query_room_available',
				array(
					'room_id'        => $room_id,
					'check_in_date'  => $check_in_date,
					'check_out_date' => $check_out_date,
				)
			);
			// get available room qty
			$available = hotel_booking_get_qty( $args );

			if ( ! is_wp_error( $available ) ) {
				wp_send_json(
					array(
						'status'         => true,
						'qty'            => $available,
						'check_in_date'  => date( 'm/d/Y', $check_in_date ),
						'check_out_date' => date( 'm/d/Y', $check_out_date ),
					)
				);
				die();
			} else {
				wp_send_json(
					array(
						'status'  => false,
						'message' => $available->get_error_message(),
					)
				);
				die();
			}
		}

		/**
		 * @param $param
		 * @param $room
		 *
		 * @return mixed
		 */
		public function add_to_cart_redirect( $param, $room ) {
			if ( isset( $param['status'] ) && $param['status'] === 'success' && isset( $_POST['is_single'] ) && $_POST['is_single'] ) {
				$param['redirect'] = add_query_arg( 'no-cache', uniqid(), hb_get_cart_url() );
			}

			return $param;
		}

		/**
		 * Check room available.
		 */
		public function hotel_booking_single_check_room_available() {

			if ( ! isset( $_POST['hb-booking-single-room-check-nonce-action'] ) || ! wp_verify_nonce( $_POST['hb-booking-single-room-check-nonce-action'], 'hb_booking_single_room_check_nonce_action' ) ) {
				return;
			}

			$errors = array();

			if ( ! isset( $_POST['room-id'] ) || ! is_numeric( $_POST['check_in_date_timestamp'] ) ) {
				$errors[] = __( 'Check in id is required.', 'wp-hotel-booking' );
			} else {
				$room_id = absint( $_POST['room-id'] );
			}

			if ( ! isset( $_POST['room-name'] ) ) {
				$errors[] = __( 'Check in name is required.', 'wp-hotel-booking' );
			} else {
				$room_name = sanitize_text_field( $_POST['room-name'] );
			}

			if ( ! isset( $_POST['check_in_date'] ) || ! isset( $_POST['check_in_date_timestamp'] ) || ! is_numeric( $_POST['check_in_date_timestamp'] ) ) {
				$errors[] = __( 'Check in date is required.', 'wp-hotel-booking' );
			} else {
				$checkindate_text = sanitize_text_field( $_POST['check_in_date'] );
				$checkindate      = absint( $_POST['check_in_date_timestamp'] );
			}

			if ( ! isset( $_POST['check_out_date_timestamp'] ) || ! is_numeric( $_POST['check_out_date_timestamp'] ) ) {
				$errors[] = __( 'Check out date is required.', 'wp-hotel-booking' );
			} else {
				$checkoutdate_text = sanitize_text_field( $_POST['check_out_date'] );
				$checkoutdate      = absint( $_POST['check_out_date_timestamp'] );
			}
			// valid request and require field
			if ( empty( $errors ) ) {
				$qty = hotel_booking_get_room_available(
					$room_id,
					array(
						'check_in_date'  => $checkindate,
						'check_out_date' => $checkoutdate,
					)
				);

				if ( $qty && ! is_wp_error( $qty ) ) {

					// room has been found
					wp_send_json(
						array(
							'status'              => true,
							'check_in_date_text'  => $checkindate_text,
							'check_out_date_text' => $checkoutdate_text,
							'check_in_date'       => date( 'm/d/Y', $checkindate ),
							'check_out_date'      => date( 'm/d/Y', $checkoutdate ),
							'room_id'             => $room_id,
							'room_name'           => $room_name,
							'qty'                 => $qty,
						)
					);
				} else {
					$errors[] = sprintf( __( 'No room found in %1$s and %2$s', 'wp-hotel-booking' ), $checkindate_text, $checkoutdate_text );
				}
			}

			// input is not pass validate, sanitize
			wp_send_json(
				array(
					'status'   => false,
					'messages' => $errors,
				)
			);
		}

		/**
		 * @return null|WP_Hotel_Booking_Room_Extension
		 */
		public static function instance() {
			if ( ! self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
	WP_Hotel_Booking_Room_Extension::instance();
}
