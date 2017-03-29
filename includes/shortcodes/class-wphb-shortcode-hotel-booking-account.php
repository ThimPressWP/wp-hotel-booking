<?php

/**
 * @Author: ducnvtt
 * @Date:   2016-04-11 13:49:45
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-11 14:14:51
 */
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

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
