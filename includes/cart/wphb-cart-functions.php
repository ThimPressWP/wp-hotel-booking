<?php
/**
 * @Author: ducnvtt
 * @Date  :   2016-04-11 08:27:22
 * @Last  Modified by:   ducnvtt
 * @Last  Modified time: 2016-04-11 08:35:07
 */

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

// generate cart item id
if ( !function_exists( 'hb_generate_cart_item_id' ) ) {
	function hb_generate_cart_item_id( $params = array() ) {
		$cart_id = array();
		foreach ( $params as $key => $param ) {
			if ( is_array( $param ) ) {
				$cart_id[] = $key . hb_generate_cart_item_id( $param );
			} else {
				$cart_id[] = $key . $param;
			}
		}

		return md5( implode( '', $cart_id ) );
	}
}

/**
 * Get WPHB_Cart instance
 *
 * @param null $prop
 *
 * @return bool|WPHB_Cart|mixed
 */
if ( !function_exists( 'hb_get_cart' ) ) {

	function hb_get_cart( $prop = null ) {
		return WPHB_Cart::instance( $prop );
	}
}

/**
 * Generate an unique string
 *
 * @return mixed
 */
if ( !function_exists( 'hb_uniqid' ) ) {

	function hb_uniqid() {
		$hash = str_replace( '.', '', microtime( true ) . uniqid() );
		return apply_filters( 'hb_generate_unique_hash', $hash );
	}
}

/**
 * Get cart description
 *
 * @return string
 */
if ( !function_exists( 'hb_get_cart_description' ) ) {

	function hb_get_cart_description() {
		$cart        = WPHB_Cart::instance();
		$description = array();
		foreach ( $cart->get_rooms() as $room ) {
			$description[] = sprintf( '%s (x %d)', $room->name, $room->quantity );
		}
		return join( ', ', $description );
	}
}

/**
 * Get check out return URL
 *
 * @return mixed
 */
if ( !function_exists( 'hb_get_return_url' ) ) {

	function hb_get_return_url() {
		$url = hb_get_checkout_url();
		return apply_filters( 'hb_return_url', $url );
	}
}

/**
 * @param      $date
 * @param bool $code
 *
 * @return bool
 */
if ( !function_exists( 'hb_get_coupons_active' ) ) {

	function hb_get_coupons_active( $date, $code = false ) {

		$coupons = false;
		$enable  = WPHB_Settings::instance()->get( 'enable_coupon' );

		if ( $enable && $code ) {
			$args = array(
				'post_type'      => 'hb_coupon',
				'posts_per_page' => 999,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => '_hb_coupon_date_from_timestamp',
						'compare' => '<=',
						'value'   => $date
					),
					array(
						'key'     => '_hb_coupon_date_to_timestamp',
						'compare' => '>=',
						'value'   => $date
					)
				)
			);

			if ( $coupons = get_posts( $args ) ) {
				$found = false;
				foreach ( $coupons as $coupon ) {
					if ( strcmp( $coupon->post_title, $code ) == 0 ) {
						$coupons = $coupon;
						$found   = true;
						break;
					}
				}
				if ( !$found ) {
					$coupons = false;
				}
			}
		}
		return $coupons;
	}
}
