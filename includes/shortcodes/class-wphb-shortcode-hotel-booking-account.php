<?php
/**
 * WP Hotel Booking account form shortcode.
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

class WPHB_Shortcode_Hotel_Booking_Account extends WPHB_Shortcodes {

	public $shortcode = 'hotel_booking_account';

	public function __construct() {
		parent::__construct();
	}

	function add_shortcode( $atts, $content = null ) {
		$template = apply_filters( 'hotel_booking_account_template', 'account/account.php' );
		ob_start();
		do_action( 'hb_wrapper_start' );
		hb_get_template( $template, $atts );
		do_action( 'hb_wrapper_end' );
		return ob_get_clean();
	}

}

new WPHB_Shortcode_Hotel_Booking_Account();
