<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPHB_Shortcode_Hotel_Booking_Best_Reviews extends WPHB_Shortcodes {

	public $shortcode = 'hotel_booking_best_reviews';

	public function __construct() {
		parent::__construct();
	}

	function add_shortcode( $atts, $content = null ) {
		$number = isset( $atts['number'] ) ? $atts['number'] : 5;
		$args = array(
			'post_type' => 'hb_room',
			'meta_key' => 'arveger_rating',
			'posts_per_page' => $number,
			'order' => 'DESC',
			'orderby' => array( 'meta_value_num' => 'DESC' )
		);
		$query = new WP_Query( $args );

		if ( $query->have_posts() ):
			hb_get_template( 'shortcodes/best_reviews.php', array( 'atts' => $atts, 'query' => $query ) );
		endif;
	}

}

new WPHB_Shortcode_Hotel_Booking_Best_Reviews();
