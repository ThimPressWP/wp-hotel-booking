<?php
/**
 * WP Hotel Booking webhooks.
 *
 * @version       1.9.6
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Webhooks
 * @category      Webhooks
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'hb_register_web_hook' ) ) {
	/**
	 * @param $key
	 * @param $param
	 */
	function hb_register_web_hook( $key, $param ) {
		if ( ! $key ) {
			return;
		}
		if ( empty( $GLOBALS['wp-hotel-booking']['web_hooks'] ) ) {
			$GLOBALS['wp-hotel-booking']['web_hooks'] = array();
		}
		$GLOBALS['wp-hotel-booking']['web_hooks'][ $key ] = $param;
		do_action( 'hb_register_web_hook', $key, $param );
	}
}

if ( ! function_exists( 'hb_get_web_hooks' ) ) {
	/**
	 * @return mixed
	 */
	function hb_get_web_hooks() {
		$web_hooks = empty( $GLOBALS['wp-hotel-booking']['web_hooks'] ) ? array() : (array) $GLOBALS['wp-hotel-booking']['web_hooks'];

		return apply_filters( 'hb_web_hooks', $web_hooks );
	}
}

if ( ! function_exists( 'hb_get_web_hook' ) ) {
	/**
	 * @param $key
	 *
	 * @return mixed
	 */
	function hb_get_web_hook( $key ) {
		$web_hooks = hb_get_web_hooks();
		$web_hook  = empty( $web_hooks[ $key ] ) ? false : $web_hooks[ $key ];

		return apply_filters( 'hb_web_hook', $web_hook, $key );
	}
}

if ( ! function_exists( 'hb_process_web_hooks' ) ) {
	/**
	 * Process webhooks
	 */
	function hb_process_web_hooks() {
		// Grab registered web_hooks
		$web_hooks           = hb_get_web_hooks();
		$web_hooks_processed = false;
		// Loop through them and init callbacks

		foreach ( $web_hooks as $key => $param ) {
			if ( ! empty( $_REQUEST[ $param ] ) ) {
				$web_hooks_processed           = true;
				$request_scheme                = is_ssl() ? 'https://' : 'http://';
				$requested_web_hook_url        = untrailingslashit( $request_scheme . esc_url_raw( $_SERVER['HTTP_HOST'] ?? '' ) ) . esc_url_raw( $_SERVER['REQUEST_URI'] ?? '' );
				$parsed_requested_web_hook_url = wp_parse_url( $requested_web_hook_url );
				$required_web_hook_url         = add_query_arg( $param, '1', trailingslashit( get_site_url() ) ); // add the slash to make sure we match
				$parsed_required_web_hook_url  = wp_parse_url( $required_web_hook_url );
				$web_hook_diff                 = array_diff_assoc( $parsed_requested_web_hook_url, $parsed_required_web_hook_url );

				if ( empty( $web_hook_diff ) ) { // No differences in the requested webhook and the required webhook
					do_action( 'hb_web_hook_' . $param, $_REQUEST );
				}
				break; // we can stop processing here... no need to continue the foreach since we can only handle one webhook at a time
			}
		}
		if ( $web_hooks_processed ) {
			do_action( 'hb_web_hooks_processed' );
			wp_die( __( 'WP Hotel Booking webhook process Complete', 'wp-hotel-booking' ), __( 'WP Hotel Booking webhook process Complete', 'wp-hotel-booking' ), array( 'response' => 200 ) );
		}
	}
}

add_action( 'wp', 'hb_process_web_hooks' );
