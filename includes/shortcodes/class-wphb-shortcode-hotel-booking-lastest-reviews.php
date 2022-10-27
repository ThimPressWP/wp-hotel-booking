<?php
/**
 * WP Hotel Booking latest review shortcode.
 *
 * @version       1.9.6
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Classes/Shortcode
 * @category      Classes
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

class WPHB_Shortcode_Hotel_Booking_Lastest_Reviews extends WPHB_Shortcodes {

	public $shortcode = 'hotel_booking_lastest_reviews';

	public function __construct() {
		parent::__construct();
	}

	function add_shortcode( $atts, $content = null ) {
		$number = isset( $atts['number'] ) ? $atts['number'] : 5;
		$args   = array(
			'post_type'      => 'hb_room',
			'meta_key'       => 'arveger_rating_last_modify',
			'posts_per_page' => $number,
			'order'          => 'DESC',
			'orderby'        => array( 'meta_value_num' => 'DESC' ),
		);
		$query  = new WP_Query( $args );

		if ( $query->have_posts() ) :
			hb_get_template(
				'shortcodes/lastest_reviews.php',
				array(
					'atts'  => $atts,
					'query' => $query,
				)
			);
		endif;
	}

}

new WPHB_Shortcode_Hotel_Booking_Lastest_Reviews();
