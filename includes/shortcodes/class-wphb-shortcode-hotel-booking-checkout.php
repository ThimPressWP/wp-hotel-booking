<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WPHB_Shortcode_Hotel_Booking_Checkout extends WPHB_Shortcodes {

    public $shortcode = 'hotel_booking_checkout';

    public function __construct() {
        parent::__construct();
    }

    function add_shortcode( $atts, $content = null ) {
        $customer = new stdClass;
        $customer->title = '';
        $customer->first_name = '';
        $customer->last_name = '';
        $customer->email = '';
        $customer->address = '';
        $customer->state = '';
        $customer->city = '';
        $customer->postal_code = '';
        $customer->country = '';
        $customer->phone = '';
        $customer->fax = '';

        if ( is_user_logged_in() ) {
            $user = WPHB_User::get_current_user();

            $customer->title = $user->title;
            $customer->first_name = $user->first_name;
            $customer->last_name = $user->last_name;
            $customer->email = $user->email;
            $customer->address = $user->address;
            $customer->state = $user->state;
            $customer->city = $user->city;
            $customer->postal_code = $user->postal_code;
            $customer->country = $user->country;
            $customer->phone = $user->phone;
            $customer->fax = $user->fax;
        }

        $template = apply_filters( 'hotel_booking_checkout_tpl', 'checkout/checkout.php' );
        $template_args = apply_filters( 'hotel_booking_checkout_tpl_template_args', array( 'customer' => $customer ) );
        ob_start();
        do_action( 'hb_wrapper_start' );
        hb_get_template( $template, $template_args );
        do_action( 'hb_wrapper_end' );
        return ob_get_clean();
    }

}

new WPHB_Shortcode_Hotel_Booking_Checkout();
