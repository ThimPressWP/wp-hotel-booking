<?php
/**
 * WP Hotel Booking Helpers.
 *
 * @since         1.9.10
 * @package       WP_Hotel_Booking/Classes
 * @category      Classes
 * @author        tungnx
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

/**
 * Class WPHB_Helpers
 */
class WPHB_Helpers {
	public static function sanitize_params_submit( $value ) {
		if ( is_string( $value ) ) {
			$value = sanitize_text_field( $value );
		} elseif ( is_array( $value ) ) {
			foreach ( $value as $k => $v ) {
				$value[ $k ] = call_user_func( array( __CLASS__, 'sanitize_params_submit' ), $v );
			}
		}

		return $value;
	}
}
