<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class HB_Shortcode_Hotel_Booking_Cart extends HB_Shortcodes {

    public $shortcode = 'hotel_booking_cart';

    public function __construct() {
        parent::__construct();
    }

    function add_shortcode( $atts, $content = null ) {
        $template = apply_filters( 'hotel_booking_cart_template', 'cart/cart.php' );
        ob_start();
        do_action( 'hb_wrapper_start' );
        wphb_get_template( $template, $atts );
        do_action( 'hb_wrapper_end' );
        return ob_get_clean();
    }

}

new HB_Shortcode_Hotel_Booking_Cart();
