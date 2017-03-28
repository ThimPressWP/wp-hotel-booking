<?php
/**
 * @Author: ducnvtt
 * @Date  :   2016-04-11 13:52:26
 * @Last  Modified by:   ducnvtt
 * @Last  Modified time: 2016-04-11 15:17:46
 */

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

if ( !is_user_logged_in() ) {
	printf( __( 'You must <strong><a href="%s">Login<a/></strong>.', 'wp-hotel-booking' ), wp_login_url( wphb_get_account_url() ) );
	return;
}

// list orders
wphb_get_template( 'account/bookings.php' );

// user info
wphb_get_template( 'account/user-info.php' );
