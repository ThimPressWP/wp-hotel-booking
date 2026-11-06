<?php
/**
 * WP Hotel Booking booking hook.
 *
 * @version     2.3.5
 * @author      ThimPress
 * @package     WP_Hotel_Booking/Hooks
 * @category    Hooks
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

/**
 * Hook
 */
add_action( 'hotel_booking_create_booking', 'hotel_booking_create_booking', 10, 1 );
add_action( 'hb_booking_status_changed', 'hotel_booking_create_booking', 10, 1 );
if ( ! function_exists( 'hotel_booking_create_booking' ) ) {
	function hotel_booking_create_booking( $booking_id ) {
		$booking_status = get_post_status( $booking_id );
		if ( $booking_status === 'hb-pending' ) {
			wp_clear_scheduled_hook( 'hotel_booking_change_cancel_booking_status', array( $booking_id ) );
			$time = hb_settings()->get( 'cancel_payment', 12 ) * HOUR_IN_SECONDS;
			wp_schedule_single_event( time() + $time, 'hotel_booking_change_cancel_booking_status', array( $booking_id ) );
		}
	}
}

// change booking status pending => status
add_action( 'hotel_booking_change_cancel_booking_status', 'hotel_booking_change_cancel_booking_status', 10, 1 );
if ( ! function_exists( 'hotel_booking_change_cancel_booking_status' ) ) {
	function hotel_booking_change_cancel_booking_status( $booking_id ) {
		$booking_status = get_post_status( $booking_id );
		$oder_woo_id    = get_post_meta( $booking_id, '_hb_woo_order_id', true );

		if ( $booking_status === 'hb-pending' ) {
			wp_update_post(
				array(
					'ID'          => $booking_id,
					'post_status' => 'hb-cancelled',
				)
			);

			// cancel order woocommerce
			if ( class_exists( 'WooCommerce' ) ) {
				$order = new WC_Order( $oder_woo_id );
				$order->update_status( 'wc-cancelled', 'order_note' );
			}
		}
	}
}

/**
 * filter email from
 */
if ( ! function_exists( 'hb_wp_mail_from' ) ) {
	function hb_wp_mail_from( $email ) {
		global $hb_settings;
		if ( $email = $hb_settings->get( 'email_general_from_email', get_option( 'admin_email' ) ) ) {
			if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
				return $email;
			}
		}

		return $email;
	}
}

if ( ! function_exists( 'hb_wp_mail_from_name' ) ) {
	function hb_wp_mail_from_name( $name ) {
		global $hb_settings;
		if ( $name = $hb_settings->get( 'email_general_from_name' ) ) {
			return $name;
		}

		return $name;
	}
}


/**
 * Filter content type to text/html for email
 *
 * @return string
 */
if ( ! function_exists( 'hb_set_html_content_type' ) ) {

	function hb_set_html_content_type() {
		return 'text/html';
	}
}

/**
 * Place order process send email
 * admin and cusomer
 */
add_action( 'hb_place_order', 'hb_customer_place_order_email', 10, 2 );
if ( ! function_exists( 'hb_customer_place_order_email' ) ) {
	/**
	 * hb_customer_place_order_email
	 *
	 * @param array $return
	 * @param null  $booking_id
	 *
	 * @return bool|void
	 */
	function hb_customer_place_order_email( $return = array(), $booking_id = null ) {
		if ( ! $booking_id || ! isset( $return['result'] ) || $return['result'] !== 'success' ) {
			return;
		}

		$settings = WPHB_Settings::instance();
		$booking  = WPHB_Booking::instance( $booking_id );

		$format  = $settings->get( 'email_new_booking_format', 'html' );
		$headers = 'Content-Type: ' . ( $format == 'html' ? 'text/html' : 'text/plain' ) . "\r\n";
		// set mail from email
		add_filter( 'wp_mail_from', 'hb_wp_mail_from' );
		// set mail from name
		add_filter( 'wp_mail_from_name', 'hb_wp_mail_from_name' );
		add_filter( 'wp_mail_content_type', 'hb_set_html_content_type' );

		// customer place order email
		$customer_email_subject      = __( 'Booking pending', 'wp-hotel-booking' );
		$customer_email_heading      = __( 'Your booking is pending', 'wp-hotel-booking' );
		$customer_email_heading_desc = __( 'Your booking is pending until the payment is completed', 'wp-hotel-booking' );

		$customer_body = hb_get_template_content(
			'emails/booking-accepted.php',
			array(
				'booking'            => $booking,
				'email_heading'      => $customer_email_heading,
				'email_heading_desc' => $customer_email_heading_desc,
			)
		);

		if ( ! $customer_body ) {
			return;
		}

		$customer_send = wp_mail( $booking->customer_email, $customer_email_subject, $customer_body, $headers );

		// admin place order email
		$admin_email              = $settings->get( 'email_new_booking_recipients', get_option( 'admin_email' ) );
		$admin_subject            = __( '[{site_title}] New customer booking ({booking_number}) - {booking_date}', 'wp-hotel-booking' );
		$admin_email_heading      = __( 'New customer booking', 'wp-hotel-booking' );
		$admin_email_heading_desc = __( 'You have a new booking room', 'wp-hotel-booking' );

		$find = array(
			'booking-date'   => '{booking_date}',
			'booking-number' => '{booking_number}',
			'site-title'     => '{site_title}',
		);

		$date_format = get_option( 'date_format' );
		$replace     = array(
			'booking-date'   => date_i18n( $date_format, strtotime( date( 'd.m.Y' ) ) ),
			'booking-number' => $booking->get_booking_number(),
			'site-title'     => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
		);

		$subject = str_replace( $find, $replace, $admin_subject );

		$body = hb_get_template_content(
			'emails/admin/admin-new-booking.php',
			array(
				'booking'            => $booking,
				'options'            => $settings,
				'email_heading'      => $admin_email_heading,
				'email_heading_desc' => $admin_email_heading_desc,
			)
		);

		if ( ! $body ) {
			return;
		}

		$admin_send = wp_mail( $admin_email, $subject, $body, $headers );

		return ( $customer_send && $admin_send );
	}
}
add_action( 'hb_booking_status_changed', 'hb_customer_email_order_changes_status', 10, 3 );
if ( ! function_exists( 'hb_customer_email_order_changes_status' ) ) {
	// Send customer when completed
	function hb_customer_email_order_changes_status( $booking_id = null, $old_status = null, $new_status = null ) {
		if ( ! ( $booking_id || $new_status ) ) {
			return;
		}

		if ( $new_status == 'completed' ) {
			// send customer email
			hb_new_customer_booking_email( $booking_id );

			// send admin uer
			$enable = hb_settings()->get( 'email_new_booking_enable' );
			if ( $enable ) {
				hb_completed_booking_email( $booking_id );
			}
		} elseif ( $new_status == 'cancelled' ) {
			// send customer email
			hb_cancel_customer_booking_email( $booking_id );

			// send admin uer
			$enable = hb_settings()->get( 'email_cancel_booking_enable' );
			if ( $enable ) {
				hb_cancel_booking_email( $booking_id );
			}
		}
	}
}

/**
 * Customer confirmation email when a booking changes to Processing
 * (e.g. a deposit/advance payment has been paid online).
 *
 * Disabled by default: Settings > Emails > Processing Booking.
 *
 * Listen on transition_post_status so it works both when gateways use
 * WPHB_Booking::update_status() and when add-ons call wp_update_post() directly.
 * Sending is deferred to "shutdown" so all booking meta is saved before the email is rendered.
 */
add_action( 'transition_post_status', 'hb_queue_customer_processing_booking_email', 10, 3 );
add_action( 'hb_booking_status_processing', 'hb_queue_customer_processing_booking_email_by_id', 10, 1 );
if ( ! function_exists( 'hb_queue_customer_processing_booking_email_by_id' ) ) {
	/**
	 * Also listen to the direct WP Hotel Booking hook for gateways (e.g. Stripe, PayPal).
	 *
	 * @param int $booking_id
	 */
	function hb_queue_customer_processing_booking_email_by_id( $booking_id ) {
		$booking_id = absint( $booking_id );
		if ( ! $booking_id || 'hb_booking' !== get_post_type( $booking_id ) ) {
			return;
		}

		if ( ! hb_settings()->get( 'email_processing_booking_enable', 0 ) ) {
			return;
		}

		global $hb_processing_email_queue;
		if ( ! is_array( $hb_processing_email_queue ) ) {
			$hb_processing_email_queue = array();
			add_action( 'shutdown', 'hb_send_queued_customer_processing_booking_emails' );
		}

		$hb_processing_email_queue[ $booking_id ] = $booking_id;
	}
}
if ( ! function_exists( 'hb_queue_customer_processing_booking_email' ) ) {
	/**
	 * @param string  $new_status
	 * @param string  $old_status
	 * @param WP_Post $post
	 */
	function hb_queue_customer_processing_booking_email( $new_status, $old_status, $post ) {
		if ( ! $post instanceof WP_Post || 'hb_booking' !== $post->post_type ) {
			return;
		}

		if ( 'hb-processing' !== $new_status || $new_status === $old_status ) {
			return;
		}

		if ( ! hb_settings()->get( 'email_processing_booking_enable', 0 ) ) {
			return;
		}

		global $hb_processing_email_queue;
		if ( ! is_array( $hb_processing_email_queue ) ) {
			$hb_processing_email_queue = array();
			add_action( 'shutdown', 'hb_send_queued_customer_processing_booking_emails' );
		}

		$hb_processing_email_queue[ $post->ID ] = $post->ID;
	}
}

if ( ! function_exists( 'hb_send_queued_customer_processing_booking_emails' ) ) {
	function hb_send_queued_customer_processing_booking_emails() {
		global $hb_processing_email_queue;
		if ( empty( $hb_processing_email_queue ) || ! is_array( $hb_processing_email_queue ) ) {
			return;
		}

		$queue                     = $hb_processing_email_queue;
		$hb_processing_email_queue = array();

		foreach ( $queue as $booking_id ) {
			hb_maybe_send_customer_processing_booking_email( $booking_id );
		}
	}
}

if ( ! function_exists( 'hb_maybe_send_customer_processing_booking_email' ) ) {
	/**
	 * Check conditions then send the Processing confirmation to the customer (once per booking).
	 *
	 * @param int $booking_id
	 *
	 * @return bool
	 */
	function hb_maybe_send_customer_processing_booking_email( $booking_id ) {
		$booking_id = absint( $booking_id );
		if ( ! $booking_id || 'hb_booking' !== get_post_type( $booking_id ) ) {
			return false;
		}

		if ( ! hb_settings()->get( 'email_processing_booking_enable', 0 ) ) {
			return false;
		}

		// Status may have changed again in the same request (e.g. directly to completed).
		clean_post_cache( $booking_id );
		if ( 'hb-processing' !== get_post_status( $booking_id ) ) {
			return false;
		}

		// Send only once per booking.
		if ( get_post_meta( $booking_id, '_hb_customer_processing_email_sent', true ) ) {
			return false;
		}

		// "Pay on arrival" also sets Processing but nothing has been paid yet.
		$method           = get_post_meta( $booking_id, '_hb_method', true );
		$excluded_methods = apply_filters( 'hb_processing_email_excluded_methods', array( 'offline-payment' ), $booking_id );
		if ( $method && in_array( $method, (array) $excluded_methods, true ) ) {
			return false;
		}

		if ( ! apply_filters( 'hb_send_customer_processing_booking_email', true, $booking_id ) ) {
			return false;
		}

		$sent = hb_customer_processing_booking_email( $booking_id );
		if ( $sent ) {
			update_post_meta( $booking_id, '_hb_customer_processing_email_sent', current_time( 'mysql' ) );
		}

		return $sent;
	}
}

if ( ! function_exists( 'hb_customer_processing_booking_email' ) ) {
	/**
	 * Send the Processing booking confirmation email.
	 *
	 * @param int    $booking_id
	 * @param string $recipient Optional. Defaults to the customer email.
	 *
	 * @return bool
	 */
	function hb_customer_processing_booking_email( $booking_id = null, $recipient = '' ) {
		if ( ! $booking_id ) {
			return false;
		}

		$settings = hb_settings();
		$booking  = WPHB_Booking::instance( $booking_id );
		$to       = $recipient ? $recipient : $booking->customer_email;

		if ( ! $to || ! is_email( $to ) ) {
			return false;
		}

		$subject      = $settings->get( 'email_processing_booking_subject', '[{site_title}] Booking confirmed ({booking_number}) - {booking_date}' );
		$heading      = $settings->get( 'email_processing_booking_heading', __( 'Your booking is confirmed', 'wp-hotel-booking' ) );
		$heading_desc = $settings->get( 'email_processing_booking_heading_desc', __( 'We have received your deposit. The remaining balance is payable on arrival.', 'wp-hotel-booking' ) );

		$find        = array(
			'booking-date'   => '{booking_date}',
			'booking-number' => '{booking_number}',
			'site-title'     => '{site_title}',
		);
		$date_format = get_option( 'date_format' );
		$replace     = array(
			'booking-date'   => date_i18n( $date_format, strtotime( date( 'd.m.Y' ) ) ),
			'booking-number' => $booking->get_booking_number(),
			'site-title'     => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
		);
		$subject     = str_replace( $find, $replace, $subject );
		$subject     = apply_filters( 'hb_customer_processing_booking_email_subject', $subject, $booking, $to );

		$email_content = hb_get_template_content(
			'emails/booking-accepted.php',
			array(
				'booking'            => $booking,
				'options'            => $settings,
				'email_heading'      => $heading,
				'email_heading_desc' => $heading_desc,
			)
		);

		if ( ! $email_content ) {
			return false;
		}

		$headers   = array();
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		add_filter( 'wp_mail_from', 'hb_wp_mail_from' );
		add_filter( 'wp_mail_from_name', 'hb_wp_mail_from_name' );
		add_filter( 'wp_mail_content_type', 'hb_set_html_content_type' );

		$send = wp_mail( $to, $subject, stripslashes( $email_content ), $headers );

		remove_filter( 'wp_mail_content_type', 'hb_set_html_content_type' );
		remove_filter( 'wp_mail_from', 'hb_wp_mail_from' );
		remove_filter( 'wp_mail_from_name', 'hb_wp_mail_from_name' );

		do_action( 'hb_customer_processing_booking_email_sent', $booking_id, $to, $send );

		return (bool) $send;
	}
}

/**
 * Send email to admin after customer booked room
 *
 * @param int $booking_id
 */
if ( ! function_exists( 'hb_completed_booking_email' ) ) {

	function hb_completed_booking_email( $booking_id = null ) {
		if ( ! $booking_id ) {
			return;
		}
		$settings = WPHB_Settings::instance();
		$booking  = WPHB_Booking::instance( $booking_id );

		$to                 = $settings->get( 'email_new_booking_recipients', get_option( 'admin_email' ) );
		$subject            = $settings->get( 'email_new_booking_subject', '[{site_title}] Reservation completed ({booking_number}) - {booking_date}' );
		$email_heading      = $settings->get( 'email_new_booking_heading', __( 'New Booking Completed', 'wp-hotel-booking' ) );
		$email_heading_desc = $settings->get( 'email_new_booking_heading_desc', __( 'The customer has completed the transaction', 'wp-hotel-booking' ) );
		$format             = $settings->get( 'email_new_booking_format', 'html' );

		$find = array(
			'booking-date'   => '{booking_date}',
			'booking-number' => '{booking_number}',
			'site-title'     => '{site_title}',
		);

		$date_format = get_option( 'date_format' );
		$replace     = array(
			'booking-date'   => date_i18n( $date_format, strtotime( date( 'd.m.Y' ) ) ),
			'booking-number' => $booking->get_booking_number(),
			'site-title'     => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
		);

		$subject = str_replace( $find, $replace, $subject );

		$body = hb_get_template_content(
			'emails/admin/admin-new-booking.php',
			array(
				'booking'            => $booking,
				'options'            => $settings,
				'email_heading'      => $email_heading,
				'email_heading_desc' => $email_heading_desc,
			)
		);

		if ( ! $body ) {
			return;
		}

		$headers = 'Content-Type: ' . ( $format == 'html' ? 'text/html' : 'text/plain' ) . "\r\n";
		$send    = wp_mail( $to, $subject, $body, $headers );

		return $send;
	}
}

// send mail to customer when have new booking
if ( ! function_exists( 'hb_new_customer_booking_email' ) ) {
	function hb_new_customer_booking_email( $booking_id = null, $recipient = '' ) {
		if ( ! $booking_id ) {
			return false;
		}

		$booking       = WPHB_Booking::instance( $booking_id );
		$email_subject = hb_settings()->get( 'email_general_subject', __( 'Reservation', 'wp-hotel-booking' ) );

		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		// set mail from email
		add_filter( 'wp_mail_from', 'hb_wp_mail_from' );
		// set mail from name
		add_filter( 'wp_mail_from_name', 'hb_wp_mail_from_name' );
		add_filter( 'wp_mail_content_type', 'hb_set_html_content_type' );

		$email_content = hb_get_template_content(
			'emails/customer-booking.php',
			array(
				'booking' => $booking,
				'options' => hb_settings(),
			)
		);

		$find = array(
			'booking-date'   => '{booking_date}',
			'booking-number' => '{booking_number}',
			'site-title'     => '{site_title}',
		);

		$date_format = get_option( 'date_format' );
		$replace     = array(
			'booking-date'   => date_i18n( $date_format, strtotime( date( 'd.m.Y' ) ) ),
			'booking-number' => $booking->get_booking_number(),
			'site-title'     => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
		);

		$email_subject = str_replace( $find, $replace, $email_subject );

		$to   = ( $recipient && is_email( $recipient ) ) ? $recipient : $booking->customer_email;
		$send = wp_mail( $to, $email_subject, stripslashes( $email_content ), $headers );

		remove_filter( 'wp_mail_content_type', 'hb_set_html_content_type' );

		return (bool) $send;
	}
}

/**
 * Send email to admin after booking has been marked cancelled.
 *
 * @param int $booking_id
 */
if ( ! function_exists( 'hb_cancel_booking_email' ) ) {

	function hb_cancel_booking_email( $booking_id = null ) {
		if ( ! $booking_id ) {
			return;
		}
		$settings = WPHB_Settings::instance();
		$booking  = WPHB_Booking::instance( $booking_id );

		$to                 = $settings->get( 'email_cancel_booking_recipients', get_option( 'admin_email' ) );
		$subject            = $settings->get( 'email_cancel_booking_subject', '[{site_title}] Cancelled Reservation  ({booking_number}) - {booking_date}' );
		$email_heading      = $settings->get( 'email_cancel_booking_heading', __( 'Cancelled booking', 'wp-hotel-booking' ) );
		$email_heading_desc = $settings->get( 'email_cancel_booking_heading_desc', __( 'Booking has been marked cancelled', 'wp-hotel-booking' ) );
		$format             = $settings->get( 'email_cancel_booking_format', 'html' );

		$find = array(
			'booking-date'   => '{booking_date}',
			'booking-number' => '{booking_number}',
			'site-title'     => '{site_title}',
		);
		$date_format = get_option( 'date_format' );
		$replace     = array(
			'booking-date'   => date_i18n( $date_format, strtotime( date( 'd.m.Y' ) ) ),
			'booking-number' => $booking->get_booking_number(),
			'site-title'     => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
		);

		$subject = str_replace( $find, $replace, $subject );

		$body = hb_get_template_content(
			'emails/admin/admin-cancel-booking.php',
			array(
				'booking'            => $booking,
				'options'            => $settings,
				'email_heading'      => $email_heading,
				'email_heading_desc' => $email_heading_desc,
			)
		);

		if ( ! $body ) {
			return;
		}

		$headers = 'Content-Type: ' . ( $format == 'html' ? 'text/html' : 'text/plain' ) . "\r\n";
		$send    = wp_mail( $to, $subject, $body, $headers );
		// if ( $fo = fopen( WPHB_PLUGIN_PATH . '/new-booking.html', 'w+' ) ) {
		// fwrite( $fo, $body );
		// fclose($fo);
		// }
		return $send;
	}
}

// send mail to customer when booking has been marked cancelled
if ( ! function_exists( 'hb_cancel_customer_booking_email' ) ) {
	function hb_cancel_customer_booking_email( $booking_id = null, $recipient = '' ) {
		if ( ! $booking_id ) {
			return false;
		}

		$booking = WPHB_Booking::instance( $booking_id );
		$subject = hb_settings()->get( 'email_cancel_booking_subject', 'Cancelled Reservation' );
		$find    = array(
			'booking-date'   => '{booking_date}',
			'booking-number' => '{booking_number}',
			'site-title'     => '{site_title}',
		);
		$date_format = get_option( 'date_format' );
		$replace     = array(
			'booking-date'   => date_i18n( $date_format, strtotime( date( 'd.m.Y' ) ) ),
			'booking-number' => $booking->get_booking_number(),
			'site-title'     => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
		);

		$subject = str_replace( $find, $replace, $subject );

		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		// set mail from email
		add_filter( 'wp_mail_from', 'hb_wp_mail_from' );
		// set mail from name
		add_filter( 'wp_mail_from_name', 'hb_wp_mail_from_name' );
		add_filter( 'wp_mail_content_type', 'hb_set_html_content_type' );

		$email_content = hb_get_template_content(
			'emails/customer-cancelled.php',
			array(
				'booking' => $booking,
				'options' => hb_settings(),
			)
		);

		$to   = ( $recipient && is_email( $recipient ) ) ? $recipient : $booking->customer_email;
		$send = wp_mail( $to, $subject, stripslashes( $email_content ), $headers );
		// if ( $fo = fopen( WPHB_PLUGIN_PATH . '/customer-booking.html', 'w+' ) ) {
		// fwrite( $fo, $email_content );
		// fclose($fo);
		// }
		remove_filter( 'wp_mail_content_type', 'hb_set_html_content_type' );

		return (bool) $send;
	}
}

add_action( 'admin_head', 'hb_menu_booking_count' );

if ( ! function_exists( 'hb_menu_booking_count' ) ) {
	/**
	 *
	 */
	function hb_menu_booking_count() {
		global $submenu;

		if ( isset( $submenu['tp_hotel_booking'] ) ) {
			$order_count = hb_get_processing_booking_count();

			// Add count if user has access.
			if ( $order_count ) {
				foreach ( $submenu['tp_hotel_booking'] as $key => $menu_item ) {
					if ( 0 === strpos( $menu_item[0], _x( 'Bookings', 'Admin menu name', 'wp-hotel-booking' ) ) ) {
						$submenu['tp_hotel_booking'][ $key ][0] .= ' <span class="awaiting-mod update-plugins count-' . esc_attr( $order_count ) . '"><span class="processing-count">' . number_format_i18n( $order_count ) . '</span></span>'; // WPCS: override ok.
						break;
					}
				}
			}
		}
	}
}

if ( ! function_exists( 'hb_get_processing_booking_count' ) ) {
	/**
	 * @return int
	 */
	function hb_get_processing_booking_count() {
		$query = array(
			'post_type'   => 'hb_booking',
			'post_status' => array(
				'hb-pending',
				'hb-processing',
			),
		);

		$booking = new WP_Query( $query );

		return $booking->post_count;
	}
}

if ( ! function_exists( 'hb_get_email_settings_by_status' ) ) {
	/**
	 * Get dynamic email subject, heading, and heading description from plugin settings by status.
	 *
	 * @param string $status 'processing', 'completed', 'cancelled', 'admin_new'
	 * @return array
	 */
	function hb_get_email_settings_by_status( $status = 'processing' ) {
		$settings   = hb_settings();
		$site_title = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$date_fmt   = get_option( 'date_format' );
		$today      = date_i18n( $date_fmt );

		$find    = array( '{site_title}', '{booking_number}', '{booking_date}' );
		$replace = array( $site_title, '#SAMPLE-1001', $today );

		$subject      = '';
		$heading      = '';
		$heading_desc = '';

		switch ( $status ) {
			case 'completed':
				$subject = $settings->get( 'email_general_subject' );
				if ( empty( $subject ) ) {
					$subject = $settings->get( 'email_new_booking_subject' );
				}
				if ( empty( $subject ) ) {
					$subject = __( 'Reservation', 'wp-hotel-booking' );
				}

				$heading = $settings->get( 'email_new_booking_heading' );
				if ( empty( $heading ) ) {
					$heading = __( 'Completed Payment', 'wp-hotel-booking' );
				}

				$heading_desc = $settings->get( 'email_new_booking_heading_desc' );
				if ( empty( $heading_desc ) ) {
					$heading_desc = __( 'The customer has completed the transaction', 'wp-hotel-booking' );
				}
				break;

			case 'cancelled':
				$subject = $settings->get( 'email_cancel_booking_subject' );
				if ( empty( $subject ) ) {
					$subject = '[{site_title}] Cancelled Reservation ({booking_number}) - {booking_date}';
				}

				$heading = $settings->get( 'email_cancel_booking_heading' );
				if ( empty( $heading ) ) {
					$heading = __( 'Cancelled booking', 'wp-hotel-booking' );
				}

				$heading_desc = $settings->get( 'email_cancel_booking_heading_desc' );
				if ( empty( $heading_desc ) ) {
					$heading_desc = __( 'Booking has been marked cancelled', 'wp-hotel-booking' );
				}
				break;

			case 'admin_new':
				$subject = $settings->get( 'email_new_booking_subject' );
				if ( empty( $subject ) ) {
					$subject = '[{site_title}] Reservation completed ({booking_number}) - {booking_date}';
				}

				$heading = $settings->get( 'email_new_booking_heading' );
				if ( empty( $heading ) ) {
					$heading = __( 'Completed Payment', 'wp-hotel-booking' );
				}

				$heading_desc = $settings->get( 'email_new_booking_heading_desc' );
				if ( empty( $heading_desc ) ) {
					$heading_desc = __( 'The customer has completed the transaction', 'wp-hotel-booking' );
				}
				break;

			case 'processing':
			default:
				$subject = $settings->get( 'email_processing_booking_subject' );
				if ( empty( $subject ) ) {
					$subject = '[{site_title}] Booking confirmed ({booking_number}) - {booking_date}';
				}

				$heading = $settings->get( 'email_processing_booking_heading' );
				if ( empty( $heading ) ) {
					$heading = __( 'Your booking is confirmed', 'wp-hotel-booking' );
				}

				$heading_desc = $settings->get( 'email_processing_booking_heading_desc' );
				if ( empty( $heading_desc ) ) {
					$heading_desc = __( 'We have received your deposit. The remaining balance is payable on arrival.', 'wp-hotel-booking' );
				}
				break;
		}

		return array(
			'subject'      => str_replace( $find, $replace, $subject ),
			'heading'      => str_replace( $find, $replace, $heading ),
			'heading_desc' => str_replace( $find, $replace, $heading_desc ),
		);
	}
}

if ( ! function_exists( 'hb_get_sample_demo_email_content' ) ) {
	/**
	 * Generate sample demo email HTML with example booking data.
	 *
	 * @param string $status    'processing', 'completed', 'cancelled', 'admin_new'
	 * @param string $recipient
	 *
	 * @return string
	 */
	function hb_get_sample_demo_email_content( $status = 'processing', $recipient = '' ) {
		$currency = hb_get_currency();
		$symbol   = hb_get_currency_symbol( $currency );

		$date_format = get_option( 'date_format' );
		$check_in    = date_i18n( $date_format, strtotime( '+1 day' ) );
		$check_out   = date_i18n( $date_format, strtotime( '+4 days' ) );

		$total_amount   = 400.00;
		$deposit_amount = 100.00;
		$balance_amount = 300.00;

		$email_meta   = hb_get_email_settings_by_status( $status );
		$heading      = $email_meta['heading'];
		$heading_desc = $email_meta['heading_desc'];

		// 1. Header
		$header = hb_get_template_content(
			'emails/email-header.php',
			array(
				'email_heading'      => $heading,
				'email_heading_desc' => $heading_desc,
			)
		);

		// 2. Sample Booking Details Table
		ob_start();
		?>
		<h2 class="section-title" style="color: #1e293b; font-size: 16px; margin: 15px 0 10px; font-weight: 700; text-align: left;"><?php echo esc_html__( 'Booking #SAMPLE-1001', 'wp-hotel-booking' ); ?></h2>

		<table class="width-100 booking_details" cellspacing="0" cellpadding="0" style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 13px;">
			<thead>
				<tr style="background-color: #f8fafc;">
					<th style="padding: 10px 12px; border: 1px solid #e2e8f0; text-align: left; font-weight: 600; color: #334155;"><?php esc_html_e( 'Room', 'wp-hotel-booking' ); ?></th>
					<th style="padding: 10px 12px; border: 1px solid #e2e8f0; text-align: left; font-weight: 600; color: #334155;"><?php esc_html_e( 'Check in', 'wp-hotel-booking' ); ?></th>
					<th style="padding: 10px 12px; border: 1px solid #e2e8f0; text-align: left; font-weight: 600; color: #334155;"><?php esc_html_e( 'Check out', 'wp-hotel-booking' ); ?></th>
					<th style="padding: 10px 12px; border: 1px solid #e2e8f0; text-align: center; font-weight: 600; color: #334155;"><?php esc_html_e( '#', 'wp-hotel-booking' ); ?></th>
					<th style="padding: 10px 12px; border: 1px solid #e2e8f0; text-align: right; font-weight: 600; color: #334155;"><?php esc_html_e( 'Price', 'wp-hotel-booking' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="padding: 10px 12px; border: 1px solid #e2e8f0; text-align: left;"><strong><?php esc_html_e( 'Deluxe Suite Room', 'wp-hotel-booking' ); ?></strong></td>
					<td style="padding: 10px 12px; border: 1px solid #e2e8f0; text-align: left;"><?php echo esc_html( $check_in ); ?></td>
					<td style="padding: 10px 12px; border: 1px solid #e2e8f0; text-align: left;"><?php echo esc_html( $check_out ); ?></td>
					<td style="padding: 10px 12px; border: 1px solid #e2e8f0; text-align: center;">1</td>
					<td style="padding: 10px 12px; border: 1px solid #e2e8f0; text-align: right;"><?php echo wp_kses_post( hb_format_price( $total_amount, $symbol ) ); ?></td>
				</tr>
				<tr>
					<td colspan="4" style="padding: 10px 12px; border: 1px solid #e2e8f0; text-align: left;"><b><?php esc_html_e( 'Subtotal', 'wp-hotel-booking' ); ?></b></td>
					<td style="padding: 10px 12px; border: 1px solid #e2e8f0; text-align: right;"><?php echo wp_kses_post( hb_format_price( $total_amount, $symbol ) ); ?></td>
				</tr>
				<tr>
					<td colspan="4" style="padding: 10px 12px; border: 1px solid #e2e8f0; text-align: left;"><b><?php esc_html_e( 'Payment method', 'wp-hotel-booking' ); ?></b></td>
					<td style="padding: 10px 12px; border: 1px solid #e2e8f0; text-align: right;"><?php esc_html_e( 'Stripe Payment', 'wp-hotel-booking' ); ?></td>
				</tr>
				<tr>
					<td colspan="4" style="padding: 10px 12px; border: 1px solid #e2e8f0; text-align: left;"><b><?php esc_html_e( 'Total', 'wp-hotel-booking' ); ?></b></td>
					<td style="padding: 10px 12px; border: 1px solid #e2e8f0; text-align: right; font-weight: 700;"><?php echo wp_kses_post( hb_format_price( $total_amount, $symbol ) ); ?></td>
				</tr>
				<?php if ( 'processing' === $status ) : ?>
					<tr style="background-color: #f8fafc;">
						<td colspan="4" style="padding: 10px 12px; border: 1px solid #e2e8f0; text-align: left;"><b><?php esc_html_e( 'Advance Payment', 'wp-hotel-booking' ); ?></b></td>
						<td style="padding: 10px 12px; border: 1px solid #e2e8f0; text-align: right;"><?php echo wp_kses_post( hb_format_price( $deposit_amount, $symbol ) ); ?></td>
					</tr>
					<tr style="background-color: #f0fdf4;">
						<td colspan="4" style="padding: 10px 12px; border: 1px solid #bbf7d0; text-align: left; color: #166534;"><b><?php esc_html_e( 'Remaining Balance (Due on arrival)', 'wp-hotel-booking' ); ?></b></td>
						<td style="padding: 10px 12px; border: 1px solid #bbf7d0; text-align: right;"><strong style="color: #166534; font-size: 14px;"><?php echo wp_kses_post( hb_format_price( $balance_amount, $symbol ) ); ?></strong></td>
					</tr>
				<?php elseif ( 'completed' === $status ) : ?>
					<tr style="background-color: #f0fdf4;">
						<td colspan="4" style="padding: 10px 12px; border: 1px solid #bbf7d0; text-align: left; color: #166534;"><b><?php esc_html_e( 'Payment Status', 'wp-hotel-booking' ); ?></b></td>
						<td style="padding: 10px 12px; border: 1px solid #bbf7d0; text-align: right;"><strong style="color: #166534;"><?php esc_html_e( 'Paid in Full (100%)', 'wp-hotel-booking' ); ?></strong></td>
					</tr>
				<?php elseif ( 'cancelled' === $status ) : ?>
					<tr style="background-color: #fef2f2;">
						<td colspan="4" style="padding: 10px 12px; border: 1px solid #fecaca; text-align: left; color: #991b1b;"><b><?php esc_html_e( 'Booking Status', 'wp-hotel-booking' ); ?></b></td>
						<td style="padding: 10px 12px; border: 1px solid #fecaca; text-align: right;"><strong style="color: #991b1b;"><?php esc_html_e( 'Cancelled', 'wp-hotel-booking' ); ?></strong></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>

		<h2 class="section-title" style="color: #1e293b; font-size: 16px; margin: 20px 0 10px; font-weight: 700; text-align: left;"><?php esc_html_e( 'Customer details', 'wp-hotel-booking' ); ?></h2>
		<ul style="list-style: none; padding: 0; margin: 0 0 20px 0; line-height: 1.8; text-align: left;">
			<li><strong><?php echo esc_html__( 'Customer Name:', 'wp-hotel-booking' ); ?></strong> <span>John Doe</span></li>
			<li><strong><?php echo esc_html__( 'Email address:', 'wp-hotel-booking' ); ?></strong> <a href="mailto:<?php echo esc_attr( $recipient ? $recipient : 'customer@example.com' ); ?>"><?php echo esc_html( $recipient ? $recipient : 'customer@example.com' ); ?></a></li>
			<li><strong><?php echo esc_html__( 'Phone:', 'wp-hotel-booking' ); ?></strong> <span class="text">+1 (555) 234-5678</span></li>
		</ul>

		<h2 class="section-title" style="color: #1e293b; font-size: 16px; margin: 20px 0 10px; font-weight: 700; text-align: left;"><?php esc_html_e( 'Billing address', 'wp-hotel-booking' ); ?></h2>
		<ul style="list-style: none; padding: 0; margin: 0; line-height: 1.8; text-align: left;">
			<li><strong><?php echo esc_html__( 'Address:', 'wp-hotel-booking' ); ?></strong>
				<span>123 Grand Hotel Ave, Suite 100<br>Rome, RM, Italy</span>
			</li>
			<li><strong><?php echo esc_html__( 'Postal Code:', 'wp-hotel-booking' ); ?></strong> <span>00100</span></li>
		</ul>
		<?php
		$body = ob_get_clean();

		// 3. Footer
		$footer = hb_get_template_content( 'emails/email-footer.php' );

		return $header . $body . $footer;
	}
}

if ( ! function_exists( 'hb_send_unified_test_email' ) ) {
	/**
	 * Send sample test email for any status to recipient, with standardized subject: Test Email -> <Status>
	 *
	 * @param string $status
	 * @param string $recipient
	 *
	 * @return bool
	 */
	function hb_send_unified_test_email( $status = 'processing', $recipient = '' ) {
		$status = sanitize_key( $status );
		if ( ! in_array( $status, array( 'processing', 'completed', 'cancelled', 'admin_new' ), true ) ) {
			$status = 'processing';
		}

		$status_labels = array(
			'completed'  => __( 'Customer - Completed booking', 'wp-hotel-booking' ),
			'processing' => __( 'Customer - Processing booking', 'wp-hotel-booking' ),
			'cancelled'  => __( 'Customer - Cancelled booking', 'wp-hotel-booking' ),
			'admin_new'  => __( 'Admin - New booking completed', 'wp-hotel-booking' ),
		);
		$status_label  = isset( $status_labels[ $status ] ) ? $status_labels[ $status ] : ucfirst( $status );
		$admin_email   = get_option( 'admin_email' );
		$to           = ( $recipient && is_email( $recipient ) ) ? $recipient : $admin_email;

		$email_meta = hb_get_email_settings_by_status( $status );
		$subject    = sprintf( '[Test] %s', $email_meta['subject'] );

		$content = hb_get_sample_demo_email_content( $status, $to );

		$headers   = array();
		$headers[] = 'Content-Type: text/html; charset=UTF-8';

		add_filter( 'wp_mail_from', 'hb_wp_mail_from' );
		add_filter( 'wp_mail_from_name', 'hb_wp_mail_from_name' );
		add_filter( 'wp_mail_content_type', 'hb_set_html_content_type' );

		$sent = wp_mail( $to, $subject, stripslashes( $content ), $headers );

		remove_filter( 'wp_mail_content_type', 'hb_set_html_content_type' );
		remove_filter( 'wp_mail_from', 'hb_wp_mail_from' );
		remove_filter( 'wp_mail_from_name', 'hb_wp_mail_from_name' );

		return (bool) $sent;
	}
}

/**
 * AJAX handler for live email preview in admin settings
 */
add_action( 'wp_ajax_wphb_render_email_preview', 'hb_ajax_render_email_preview' );
if ( ! function_exists( 'hb_ajax_render_email_preview' ) ) {
	function hb_ajax_render_email_preview() {
		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'manage_hb_booking' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'wp-hotel-booking' ) );
		}

		$status  = isset( $_REQUEST['status'] ) ? sanitize_key( $_REQUEST['status'] ) : 'processing';
		$content = hb_get_sample_demo_email_content( $status );

		header( 'Content-Type: text/html; charset=UTF-8' );
		echo stripslashes( $content );
		exit;
	}
}

/**
 * AJAX handler for unified test email dispatcher
 */
add_action( 'wp_ajax_wphb_send_unified_test_email', 'hb_ajax_send_unified_test_email' );
if ( ! function_exists( 'hb_ajax_send_unified_test_email' ) ) {
	function hb_ajax_send_unified_test_email() {
		check_ajax_referer( 'wphb_unified_test_email_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'manage_hb_booking' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'wp-hotel-booking' ) ) );
		}

		$status    = isset( $_POST['status'] ) ? sanitize_key( $_POST['status'] ) : 'processing';
		$recipient = isset( $_POST['recipient'] ) ? sanitize_email( wp_unslash( $_POST['recipient'] ) ) : '';

		if ( ! $recipient || ! is_email( $recipient ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid recipient email address.', 'wp-hotel-booking' ) ) );
		}

		$sent = hb_send_unified_test_email( $status, $recipient );

		if ( $sent ) {
			$status_labels = array(
				'completed'  => __( 'Customer - Completed booking', 'wp-hotel-booking' ),
				'processing' => __( 'Customer - Processing booking', 'wp-hotel-booking' ),
				'cancelled'  => __( 'Customer - Cancelled booking', 'wp-hotel-booking' ),
				'admin_new'  => __( 'Admin - New booking completed', 'wp-hotel-booking' ),
			);
			$label         = isset( $status_labels[ $status ] ) ? $status_labels[ $status ] : ucfirst( $status );

			wp_send_json_success(
				array(
					'message' => sprintf(
						__( 'Sample test email (%s) was successfully sent to %s.', 'wp-hotel-booking' ),
						$label,
						$recipient
					),
				)
			);
		} else {
			wp_send_json_error(
				array(
					'message' => __( 'wp_mail() returned false. Please verify your SMTP settings in WP Mail SMTP or Aruba SMTP.', 'wp-hotel-booking' ),
				)
			);
		}
	}
}
