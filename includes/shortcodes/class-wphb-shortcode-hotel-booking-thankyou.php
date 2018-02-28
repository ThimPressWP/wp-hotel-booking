<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPHB_Shortcode_Hotel_Booking_Thankyou extends WPHB_Shortcodes {

	public $shortcode = 'hotel_booking_thankyou';

	public function __construct() {
		parent::__construct();
	}

	function add_shortcode( $atts, $content = null ) {

		$template      = apply_filters( 'hotel_booking_thankyou_tpl', 'checkout/thank-you.php' );
		$template_args = apply_filters( 'hotel_booking_checkout_tpl_template_args', array(
			'booking_id'  => '',
			'booking_key' => ''
		) );
		ob_start();
		do_action( 'hb_wrapper_start' );
		hb_get_template( $template, $template_args );
		do_action( 'hb_wrapper_end' );

		return ob_get_clean();
	}

}

new WPHB_Shortcode_Hotel_Booking_Thankyou();
