<?php
/**
 * WP Hotel Booking checkout shortcode.
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

class WPHB_Shortcode_Hotel_Booking_Checkout extends WPHB_Shortcodes {

	public $shortcode = 'hotel_booking_checkout';

	public function __construct() {
		parent::__construct();
	}

	function add_shortcode( $atts, $content = null ) {
		$customer              = new stdClass();
		$customer->title       = '';
		$customer->first_name  = '';
		$customer->last_name   = '';
		$customer->email       = '';
		$customer->address     = '';
		$customer->state       = '';
		$customer->city        = '';
		$customer->postal_code = '';
		$customer->country     = '';
		$customer->phone       = '';
		$customer->fax         = '';

		if ( is_user_logged_in() ) {
			$user = WPHB_User::get_current_user();

			$customer->title       = $user->user->billing_title;
			$customer->first_name  = $user->user->billing_first_name;
			$customer->last_name   = $user->user->billing_last_name;
			$customer->email       = $user->user->user_email;
			$customer->address     = $user->user->billing_address;
			$customer->state       = $user->user->billing_state;
			$customer->city        = $user->user->billing_city;
			$customer->postal_code = $user->user->billing_postcode;
			$customer->country     = $user->user->billing_country;
			$customer->phone       = $user->user->billing_phone;
		}

		$template      = apply_filters( 'hotel_booking_checkout_tpl', 'checkout/checkout.php' );
		$template_args = apply_filters( 'hotel_booking_checkout_tpl_template_args', array( 'customer' => $customer ) );
		ob_start();
		do_action( 'hb_wrapper_start' );
		hb_get_template( $template, $template_args );
		do_action( 'hb_wrapper_end' );
		return ob_get_clean();
	}

}

new WPHB_Shortcode_Hotel_Booking_Checkout();
