<?php
defined( 'ABSPATH' ) || exit;

class WPHB_Shortcode_Hotel_Booking_Filter extends WPHB_Shortcodes {

	public $shortcode = 'hotel_booking_filter';

	public function __construct() {
		parent::__construct();
	}

	function add_shortcode( $atts, $content = null ) {
		$atts = wp_parse_args(
			$atts,
			array()
		);

		$template_args['atts'] = $atts;
		$template              = apply_filters( 'hotel_booking_filter_shortcode_template', 'search/v2/search-filter-v2.php' );

		ob_start();
		do_action( 'hb_wrapper_start' );
		hb_get_template( $template, $template_args );
		do_action( 'hb_wrapper_end' );

		return ob_get_clean();
	}
}

new WPHB_Shortcode_Hotel_Booking_Filter();
