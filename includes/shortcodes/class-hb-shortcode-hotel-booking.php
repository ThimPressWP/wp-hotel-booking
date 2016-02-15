<?php

class HB_Shortcode_Hotel_Booking extends HB_Shortcodes
{

	public $shortcode = 'hotel_booking';

	public function __construct()
	{
		parent::__construct();
	}

	function add_shortcode( $atts, $content = null )
	{
		if( ! class_exists( 'HB_Room' ) ){
            TP_Hotel_Booking::instance()->_include( 'includes/class-hb-room.php' );
        }
        $start_date     = hb_get_request( 'check_in_date' );
        $end_date       = hb_get_request( 'check_out_date' );
        $adults         = hb_get_request( 'adults' );
        $max_child      = hb_get_request( 'max_child' );

        $atts = wp_parse_args(
            $atts,
            array(
                'check_in_date'     => $start_date,
                'check_out_date'    => $end_date,
                'adults'            => hb_get_request( 'adults' ),
                'max_child'         => hb_get_request( 'max_child' ),
                'search_page'       => null
            )
        );

        $page = hb_get_request( 'hotel-booking' );

        $template = 'search-room.php';
        $template_args = array();

        // find the url for form action
        $search_permalink = '';
        if( $search_page = $atts['search_page'] ){
            if( is_numeric( $search_page ) ){
                $search_permalink = get_the_permalink( $search_page );
            }else{
                $search_permalink = $search_page;
            }
        }else{
            global $post;
            if( $post && ( $post_id = get_the_ID() ) && is_page( $post_id ) ){
                $search_permalink = get_the_permalink( $post_id );
            }
        }
        $template_args['search_page'] = $search_permalink;
        /**
         * Add argument use in shortcode display
         */
        $template_args['atts']         = $atts;

        /**
         * Display the template based on current step
         */
        switch( $page ){
            case 'results':
                if( ! isset( $atts['page'] ) || $atts['page'] !== 'results' )
                    break;

                $template = 'results.php';
                $template_args['results']   = hb_search_rooms(
                    array(
                        'check_in_date'     => $start_date,
                        'check_out_date'    => $end_date,
                        'adults'            => $adults,
                        'max_child'         => $max_child
                    )
                );
                break;
            case 'cart':
                if( ! isset( $atts['page'] ) || $atts['page'] !== 'cart' )
                    break;
                $template = 'cart.php';
                break;
            case 'checkout':
                if( ! isset( $atts['page'] ) || $atts['page'] !== 'checkout' )
                    break;
                if( is_user_logged_in() ) {
                    global $current_user;
                    get_currentuserinfo();

                    $template_args['customer'] = hb_get_customer( $current_user->user_email );

                } else {
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
                $template = 'checkout.php';
                break;
            case 'confirm':
                if( ! isset( $atts['page'] ) || $atts['page'] !== 'confirm' )
                    break;
                $template = 'confirm.php';
                break;
            case 'complete':
                if( ! isset( $atts['page'] ) || $atts['page'] !== 'complete' )
                    break;
                $template = 'message.php';
                break;
            default:
                $template = 'search-room.php';
                break;
        }

        $template = apply_filters( 'tp_hotel_booking_shortcode_tpl', $template );
        ob_start();
        do_action( 'hb_wrapper_start' );
        hb_get_template( 'shortcodes/' . $template, $template_args );
        do_action( 'hb_wrapper_end' );
        $output = ob_get_clean();
        return $output;
	}

}

new HB_Shortcode_Hotel_Booking();
