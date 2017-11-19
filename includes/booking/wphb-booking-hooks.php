<?php
/**
 * @Author: ducnvtt
 * @Date  :   2016-03-31 15:40:31
 * @Last  Modified by:   someone
 * @Last  Modified time: 2016-05-11 16:45:23
 */

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
		global $wpdb;

		$booking_status = get_post_status( $booking_id );
		if ( $booking_status === 'hb-pending' ) {
			wp_update_post( array(
				'ID'          => $booking_id,
				'post_status' => 'hb-cancelled'
			) );
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
	 * @param null $booking_id
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
		$headers = "Content-Type: " . ( $format == 'html' ? 'text/html' : 'text/plain' ) . "\r\n";
		// set mail from email
		add_filter( 'wp_mail_from', 'hb_wp_mail_from' );
		// set mail from name
		add_filter( 'wp_mail_from_name', 'hb_wp_mail_from_name' );
		add_filter( 'wp_mail_content_type', 'hb_set_html_content_type' );


		// customer place order email
		$customer_email_subject      = __( 'Booking pending', 'wp-hotel-booking' );
		$customer_email_heading      = __( 'Your booking is pending', 'wp-hotel-booking' );
		$customer_email_heading_desc = __( 'Your booking is pending until the payment is completed', 'wp-hotel-booking' );


		$customer_body = hb_get_template_content( 'emails/booking-accepted.php', array(
			'booking'            => $booking,
			'email_heading'      => $customer_email_heading,
			'email_heading_desc' => $customer_email_heading_desc
		) );

		if ( ! $customer_body ) {
			return;
		}

		$customer_send = wp_mail( $booking->customer_email, $customer_email_subject, $customer_body, $headers );


		// admin place order email
		$admin_email              = $settings->get( 'email_new_booking_recipients', get_option( 'admin_email' ) );
		$admin_subject            = '[{site_title}] New customer booking ({booking_number}) - {booking_date}';
		$admin_email_heading      = __( 'New customer booking', 'wp-hotel-booking' );
		$admin_email_heading_desc = __( 'You have a new booking room', 'wp-hotel-booking' );

		$find = array(
			'booking-date'   => '{booking_date}',
			'booking-number' => '{booking_number}',
			'site-title'     => '{site_title}'
		);

		$replace = array(
			'booking-date'   => date_i18n( 'd.m.Y', strtotime( date( 'd.m.Y' ) ) ),
			'booking-number' => $booking->get_booking_number(),
			'site-title'     => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES )
		);

		$subject = str_replace( $find, $replace, $admin_subject );

		$body = hb_get_template_content( 'emails/admin/admin-new-booking.php', array(
			'booking'            => $booking,
			'options'            => $settings,
			'email_heading'      => $admin_email_heading,
			'email_heading_desc' => $admin_email_heading_desc
		) );

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
				hb_new_booking_email( $booking_id );
			}
		} else if ( $new_status == 'cancelled' ) {
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
 * Send email to admin after customer booked room
 *
 * @param int $booking_id
 */
if ( ! function_exists( 'hb_new_booking_email' ) ) {

	function hb_new_booking_email( $booking_id = null ) {
		if ( ! $booking_id ) {
			return;
		}
		$settings = WPHB_Settings::instance();
		$booking  = WPHB_Booking::instance( $booking_id );

		$to                 = $settings->get( 'email_new_booking_recipients', get_option( 'admin_email' ) );
		$subject            = $settings->get( 'email_new_booking_subject', '[{site_title}] Reservation completed ({booking_number}) - {booking_date}' );
		$email_heading      = $settings->get( 'email_new_booking_heading', __( 'New Booking Payment', 'wp-hotel-booking' ) );
		$email_heading_desc = $settings->get( 'email_new_booking_heading_desc', __( 'The customer has completed the transaction', 'wp-hotel-booking' ) );
		$format             = $settings->get( 'email_new_booking_format', 'html' );

		$find = array(
			'booking-date'   => '{booking_date}',
			'booking-number' => '{booking_number}',
			'site-title'     => '{site_title}'
		);

		$replace = array(
			'booking-date'   => date_i18n( 'd.m.Y', strtotime( date( 'd.m.Y' ) ) ),
			'booking-number' => $booking->get_booking_number(),
			'site-title'     => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES )
		);

		$subject = str_replace( $find, $replace, $subject );

		$body = hb_get_template_content( 'emails/admin/admin-new-booking.php', array(
			'booking'            => $booking,
			'options'            => $settings,
			'email_heading'      => $email_heading,
			'email_heading_desc' => $email_heading_desc
		) );

		if ( ! $body ) {
			return;
		}

		$headers = "Content-Type: " . ( $format == 'html' ? 'text/html' : 'text/plain' ) . "\r\n";
		$send    = wp_mail( $to, $subject, $body, $headers );
		// if ( $fo = fopen( WPHB_PLUGIN_PATH . '/new-booking.html', 'w+' ) ) {
		//     fwrite( $fo, $body );
		//     fclose($fo);
		// }
		return $send;
	}
}

// send mail to customer when have new booking
if ( ! function_exists( 'hb_new_customer_booking_email' ) ) {
	function hb_new_customer_booking_email( $booking_id = null ) {
		if ( ! $booking_id ) {
			return;
		}

		$booking       = WPHB_Booking::instance( $booking_id );
		$email_subject = hb_settings()->get( 'email_general_subject', __( 'Reservation', 'wp-hotel-booking' ) );

		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		// set mail from email
		add_filter( 'wp_mail_from', 'hb_wp_mail_from' );
		// set mail from name
		add_filter( 'wp_mail_from_name', 'hb_wp_mail_from_name' );
		add_filter( 'wp_mail_content_type', 'hb_set_html_content_type' );

		$email_content = hb_get_template_content( 'emails/customer-booking.php', array(
			'booking' => $booking,
			'options' => hb_settings()
		) );

		wp_mail( $booking->customer_email, $email_subject, stripslashes( $email_content ), $headers );
		// if ( $fo = fopen( WPHB_PLUGIN_PATH . '/customer-booking.html', 'w+' ) ) {
		//     fwrite( $fo, $email_content );
		//     fclose($fo);
		// }
		remove_filter( 'wp_mail_content_type', 'hb_set_html_content_type' );
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
			'site-title'     => '{site_title}'
		);

		$replace = array(
			'booking-date'   => date_i18n( 'd.m.Y', strtotime( date( 'd.m.Y' ) ) ),
			'booking-number' => $booking->get_booking_number(),
			'site-title'     => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES )
		);

		$subject = str_replace( $find, $replace, $subject );

		$body = hb_get_template_content( 'emails/admin/admin-cancel-booking.php', array(
			'booking'            => $booking,
			'options'            => $settings,
			'email_heading'      => $email_heading,
			'email_heading_desc' => $email_heading_desc
		) );

		if ( ! $body ) {
			return;
		}

		$headers = "Content-Type: " . ( $format == 'html' ? 'text/html' : 'text/plain' ) . "\r\n";
		$send    = wp_mail( $to, $subject, $body, $headers );
		// if ( $fo = fopen( WPHB_PLUGIN_PATH . '/new-booking.html', 'w+' ) ) {
		//     fwrite( $fo, $body );
		//     fclose($fo);
		// }
		return $send;
	}
}

// send mail to customer when booking has been marked cancelled
if ( ! function_exists( 'hb_cancel_customer_booking_email' ) ) {
	function hb_cancel_customer_booking_email( $booking_id = null ) {
		if ( ! $booking_id ) {
			return;
		}

		$booking       = WPHB_Booking::instance( $booking_id );
		$email_subject = hb_settings()->get( 'email_cancel_booking_subject', 'Cancelled Reservation' );

		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		// set mail from email
		add_filter( 'wp_mail_from', 'hb_wp_mail_from' );
		// set mail from name
		add_filter( 'wp_mail_from_name', 'hb_wp_mail_from_name' );
		add_filter( 'wp_mail_content_type', 'hb_set_html_content_type' );

		$email_content = hb_get_template_content( 'emails/customer-cancelled.php', array(
			'booking' => $booking,
			'options' => hb_settings()
		) );

		wp_mail( $booking->customer_email, $email_subject, stripslashes( $email_content ), $headers );
		// if ( $fo = fopen( WPHB_PLUGIN_PATH . '/customer-booking.html', 'w+' ) ) {
		//     fwrite( $fo, $email_content );
		//     fclose($fo);
		// }
		remove_filter( 'wp_mail_content_type', 'hb_set_html_content_type' );
	}
}
