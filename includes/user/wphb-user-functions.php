<?php
/**
 * WP Hotel Booking user functions.
 *
 * @version     1.9.6
 * @author      ThimPress
 * @package     WP_Hotel_Booking/Functions
 * @category    Core Functions
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

// get user
if ( ! function_exists( 'hb_get_user' ) ) {

	function hb_get_user( $user = null ) {
		return WPHB_User::get_user( $user );
	}
}

// get current user
if ( ! function_exists( 'hb_get_current_user' ) ) {

	function hb_get_current_user() {
		global $hb_curent_user;

		if ( ! $hb_curent_user ) {
			$hb_curent_user = WPHB_User::get_current_user();
		}
		return $hb_curent_user;
	}
}
