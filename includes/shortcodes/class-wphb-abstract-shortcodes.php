<?php
/**
 * WP Hotel Booking abstract shortcode.
 *
 * @version       1.9.6
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Classes/Shortcode
 * @category      Classes
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

/**
 * Class HB_Shortcodes
 */
abstract class WPHB_Shortcodes {

	// shortcode name
	protected $shortcode = null;

	function __construct() {
		add_shortcode( $this->shortcode, array( $this, 'add_shortcode' ) );
	}

	function add_shortcode( $atts, $content = null ) {

	}

}
