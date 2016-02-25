<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class HB_Shortcode_Hotel_Booking_Cart extends HB_Shortcodes
{

	public $shortcode = 'hotel_booking_cart';

	public function __construct()
	{
		parent::__construct();
	}

	function add_shortcode( $atts, $content = null )
	{
        $template = apply_filters( 'tp_hotel_booking_cart_tpl', 'cart.php' );
        ob_start();
        do_action( 'hb_wrapper_start' );
        hb_get_template( 'shortcodes/'.$template, $atts );
        do_action( 'hb_wrapper_end' );
        $output = ob_get_clean();
        return $output;
	}

}

new HB_Shortcode_Hotel_Booking_Cart();