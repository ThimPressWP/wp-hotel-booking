<?php

class HB_Shortcode_Hotel_Booking_Checkout extends HB_Shortcodes
{

	public $shortcode = 'hotel_booking_checkout';

	public function __construct()
	{
		parent::__construct();
	}

	function add_shortcode( $atts, $content = null )
	{
        if( is_user_logged_in() ){
            global $current_user;
            get_currentuserinfo();

            $template_args['customer'] = hb_get_customer( $current_user->user_email );

        }else{
            $template_args['customer'] = hb_create_empty_post();
            $template_args['customer']->data = array(
                'title'             => '',
                'first_name'        => '',
                'last_name'         => '',
                'address'           => '',
                'city'              => '',
                'state'             => '',
                'postal_code'       => '',
                'country'           => '',
                'phone'             => '',
                'fax'               => ''
            );
        }

        $template = apply_filters( 'tp_hotel_booking_checkout_tpl', 'checkout.php' );
        $template_args = apply_filters( 'tp_hotel_booking_checkout_tpl_template_args', $template_args );
        ob_start();
        do_action( 'hb_wrapper_start' );
        hb_get_template( 'shortcodes/'.$template, $template_args );
        do_action( 'hb_wrapper_end' );
        $output = ob_get_clean();
        return $output;
	}

}

new HB_Shortcode_Hotel_Booking_Checkout();