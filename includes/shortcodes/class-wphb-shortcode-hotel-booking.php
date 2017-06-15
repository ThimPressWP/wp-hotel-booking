<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPHB_Shortcode_Hotel_Booking extends WPHB_Shortcodes {

	public $shortcode = 'hotel_booking';

	public function __construct() {
		parent::__construct();
	}

	function add_shortcode( $atts, $content = null ) {
		if ( ! class_exists( 'WPHB_Room' ) ) {
			WP_Hotel_Booking::instance()->_include( 'includes/class-wphb-room.php' );
		}

		$start_date = hb_get_request( 'hb_check_in_date' );
		if ( $start_date ) {
			$start_date = date( 'm/d/Y', $start_date );
		}

		$end_date = hb_get_request( 'hb_check_out_date' );
		if ( $end_date ) {
			$end_date = date( 'm/d/Y', $end_date );
		}
		$adults    = hb_get_request( 'adults', 1 );
		$max_child = hb_get_request( 'max_child', 0 );

		$atts = wp_parse_args(
			$atts, array(
				'check_in_date'  => $start_date,
				'check_out_date' => $end_date,
				'adults'         => $adults,
				'max_child'      => $max_child,
				'search_page'    => null
			)
		);

		$page = hb_get_request( 'hotel-booking' );

		$template      = 'search/search.php';
		$template_args = array();

		// find the url for form action
		$search_permalink = '';
		if ( $search_page = $atts['search_page'] ) {
			if ( is_numeric( $search_page ) ) {
				$search_permalink = get_the_permalink( $search_page );
			} else {
				$search_permalink = $search_page;
			}
		} else {
			$search_permalink = hb_get_url();
		}
		$template_args['search_page'] = $search_permalink;
		/**
		 * Add argument use in shortcode display
		 */
		$template_args['atts'] = $atts;

		/**
		 * Display the template based on current step
		 */
		switch ( $page ) {
			case 'results':
				if ( ! isset( $atts['page'] ) || $atts['page'] !== 'results' ) {
					break;
				}

				$template                 = 'search/results.php';
				$template_args['results'] = hb_search_rooms(
					array(
						'check_in_date'  => $start_date,
						'check_out_date' => $end_date,
						'adults'         => $adults,
						'max_child'      => $max_child
					)
				);
				break;
			default:
				$template = 'search/search.php';
				break;
		}
		$template = apply_filters( 'hotel_booking_shortcode_template', $template );
		ob_start();
		do_action( 'hb_wrapper_start' );
		hb_get_template( $template, $template_args );
		do_action( 'hb_wrapper_end' );

		return ob_get_clean();
	}

}

new WPHB_Shortcode_Hotel_Booking();
