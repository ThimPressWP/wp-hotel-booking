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
			//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

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
			global $post;

			if ( $post && is_singular( 'hb_room' ) ) {
				do_action( 'wphb/check-single-room/layout', $post );
			}
		}

		/**
		 * Enqueue script.
		 */
		/*public function enqueue() {
			$ver = WPHB_VERSION;
			$min = '.min';
			if ( WPHB_Settings::is_debug() ) {
				$min = '';
				$ver = time();
			}

			wp_register_style(
				'flatpickr-css',
				'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
				[],
				'1.0.0'
			);

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
			wp_register_script(
				'wpdb-single-room-js',
				WPHB_PLUGIN_URL . "/assets/dist/js/frontend/wphb-single-room{$min}.js",
				$dependencies,
				$ver,
				[ 'strategy' => 'defer' ]
			);
			wp_enqueue_style( 'wp-hotel-booking-single-room-css', WPHB_PLUGIN_URL . '/assets/css/booking-single-room.css', array(), WPHB_VERSION );

			$l10n = apply_filters(
				'hote_booking_blocked_days_l10n',
				array(
					'blocked_days'  => wp_hotel_booking_blocked_days(),
					'external_link' => is_singular( 'hb_room' ) ? get_post_meta( get_the_ID(), '_hb_external_link', true ) : '',
					'timezone'      => get_option( 'gmt_offset' ),
					'user_id'       => get_current_user_id(),
					'nonce'         => wp_create_nonce( 'wp_rest' ),
				)
			);
			wp_localize_script( 'wpdb-single-room-js', 'Hotel_Booking_Blocked_Days', $l10n );
		}*/

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
			if ( isset( $param['status'] ) && $param['status'] === 'success' ) {
				$param['redirect'] = hb_get_cart_url();
			}

			return $param;
		}

		/**
		 * Check room available.
		 */
        public function hotel_booking_single_check_room_available() {
            $res = new WPHB_REST_Response();

            try {
                $nonce = WPHB_Helpers::get_param('nonce');
                $room_id = WPHB_Helpers::get_param('room-id', '', 'int');
                $check_in_date_str = WPHB_Helpers::get_param('check_in_date');
                $check_out_date_str = WPHB_Helpers::get_param('check_out_date');

                if (!wp_verify_nonce($nonce, 'hb_booking_nonce_action') || empty($room_id)) {
                    throw new Exception(__('Invalid request', 'wp-hotel-booking'));
                }

                if (empty($check_in_date_str) || empty($check_out_date_str)) {
                    throw new Exception(__('Check in date and check out date is required.', 'wp-hotel-booking'));
                }

                $room = get_post($room_id);
                if (!$room || $room->post_type !== WPHB_ROOM_CT) {
                    throw new Exception(__('Room not found', 'wp-hotel-booking'));
                }

                // Get room availability for requested dates
                $room_instance = WPHB_Room::instance($room_id);
                $existing_bookings = $room_instance->get_dates_available();

                // Check availability for date range
                $check_in_timestamp = strtotime($check_in_date_str);
                $check_out_timestamp = strtotime($check_out_date_str);
                $date_check = $check_in_timestamp;

                $is_available = true;
                while ($date_check < $check_out_timestamp) {
                    if (isset($existing_bookings[$date_check]) && $existing_bookings[$date_check] <= 0) {
                        $is_available = false;
                        break;
                    }
                    $date_check = strtotime('+1 day', $date_check);
                }

                if (!$is_available) {
                    throw new Exception(__('This room is not available.', 'wp-hotel-booking'));
                }

                // Get final room quantity
                $qty = hotel_booking_get_room_available(
                    $room_id,
                    array(
                        'check_in_date' => $check_in_date_str,
                        'check_out_date' => $check_out_date_str,
                    )
                );

                if (is_wp_error($qty)) {
                    throw new Exception($qty->get_error_message());
                }

                $check_in_date = new WPHB_Datetime($check_in_date_str);
                $check_out_date = new WPHB_Datetime($check_out_date_str);
                $dates_checked = sprintf(
                    '%s: %s - %s',
                    __('Dates', 'wp-hotel-booking'),
                    $check_in_date->format(WPHB_Datetime::I18N_FORMAT),
                    $check_out_date->format(WPHB_Datetime::I18N_FORMAT)
                );

                ob_start();
                wphb_get_template_no_override('single-room/search/add-to-cart.php', compact('room'));
                $html_add_to_cart = ob_get_clean();

                $res->status = 'success';
                $res->data = array(
                    'dates_booked' => $dates_checked,
                    'html_extra' => $html_add_to_cart,
                    'room_id' => $room_id,
                    'qty' => $qty,
                );
                wp_send_json($res, 200, JSON_UNESCAPED_SLASHES);

            } catch (Throwable $e) {
                $res->message = $e->getMessage();
            }

            wp_send_json($res);
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
