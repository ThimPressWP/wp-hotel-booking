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
	 * @return array|float|int|string
	 * @since  1.9.10
	 * @version 1.0.1
	 */
	public static function sanitize_params_submitted( $value, string $type_content = 'text' ) {
		$value = wp_unslash( $value );

		if ( is_string( $value ) ) {
			switch ( $type_content ) {
				case 'html':
					$value = wp_kses_post( $value );
					break;
				case 'textarea':
					$value = sanitize_textarea_field( $value );
					break;
				case 'key':
					$value = sanitize_key( $value );
					break;
				case 'int':
					$value = (int) $value;
					break;
				case 'float':
					$value = (float) $value;
					break;
				default:
					if ( is_callable( $type_content ) ) {
						$value = call_user_func( $type_content, $value );
					} else {
						$value = sanitize_text_field( $value );
					}
			}
		} elseif ( is_array( $value ) ) {
			foreach ( $value as $k => $v ) {
				unset( $value[ $k ] );
				$value[ sanitize_text_field( $k ) ] = self::sanitize_params_submitted( $v, $type_content );
			}
		}

		return $value;
	}

	/**
	 * Get value by key from $_REQUEST
	 *
	 * @param string $key
	 * @param mixed $default
	 * @param string $sanitize_type
	 * @param string $method
	 *
	 * @since 2.1.3
	 * @version 1.0.0
	 * @return array|float|int|string
	 */
	public static function get_param( string $key, $default = '', string $sanitize_type = 'text', string $method = '' ) {
		switch ( strtolower( $method ) ) {
			case 'post':
				$values = $_POST ?? [];
				break;
			case 'get':
				$values = $_GET ?? [];
				break;
			default:
				$values = $_REQUEST ?? [];
		}

		return self::sanitize_params_submitted( $values[ $key ] ?? $default, $sanitize_type );
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

	/**
	 * Format list array string when query IN mysql
	 *
	 * @param array $arr
	 * @param string $format
	 *
	 * @return string
	 */
	public static function db_format_array( array $arr, string $format = '%d' ): string {
		$arr_formatted = array_fill( 0, sizeof( $arr ), $format );

		return join( ',', $arr_formatted );
	}
}
