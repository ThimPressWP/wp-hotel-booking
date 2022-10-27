<?php
/**
 * WP Hotel Booking abstract product.
 *
 * @version       1.9.6
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Classes
 * @category      Classes
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

abstract class WPHB_Product_Abstract {

	function __construct( $params = null ) {

	}

	function amount_include_tax() {

	}

	function amount_exclude_tax() {

	}

}
