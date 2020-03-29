<?php
/**
 * The template for displaying account page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/account/account.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! is_user_logged_in() ) {
	printf( __( 'You must <strong><a href="%s">Login<a/></strong>.', 'wp-hotel-booking' ), wp_login_url( hb_get_account_url() ) );

	return;
}

// list orders
hb_get_template( 'account/bookings.php' );