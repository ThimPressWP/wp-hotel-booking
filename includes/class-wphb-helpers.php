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
	/**
	 * Sanitize string and array
	 *
	 * @param array|string $value
	 *
	 * @return array|string
	 * @since  1.9.10
	 * @author tungnx
	 */
	public static function sanitize_params_submitted( $value, $type_content = 'text' ) {
		$value = wp_unslash( $value );

		if ( is_string( $value ) ) {
			switch ( $type_content ) {
				case 'html':
					$value = wp_kses_post( $value );
					break;
				case 'textarea':
					$value = sanitize_textarea_field( $value );
					break;
				default:
					$value = sanitize_text_field( $value );
			}
		} elseif ( is_array( $value ) ) {
			foreach ( $value as $k => $v ) {
				$value[ $k ] = self::sanitize_params_submitted( $v, $type_content );
			}
		}

		return $value;
	}

	/**
	 * Print string
	 *
	 * @param $string
	 *
	 * @return void
	 */
	public static function print( $string ) {
		printf( '%s', $string );
	}
}
