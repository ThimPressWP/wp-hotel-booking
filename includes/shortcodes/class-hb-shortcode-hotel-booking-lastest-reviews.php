<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class HB_Shortcode_Hotel_Booking_Lastest_Reviews extends HB_Shortcodes {

    public $shortcode = 'hotel_booking_lastest_reviews';

    public function __construct() {
        parent::__construct();
    }

    function add_shortcode( $atts, $content = null ) {
        $number = isset( $atts['number'] ) ? $atts['number'] : 5;
        $args = array(
            'post_type' => 'hb_room',
            'meta_key' => 'arveger_rating_last_modify',
            'posts_per_page' => $number,
            'order' => 'DESC',
            'orderby' => array( 'meta_value_num' => 'DESC' )
        );
        $query = new WP_Query( $args );

        if ( $query->have_posts() ):
            wphb_get_template( 'shortcodes/lastest_reviews.php', array( 'atts' => $atts, 'query' => $query ) );
        endif;
    }

}

new HB_Shortcode_Hotel_Booking_Lastest_Reviews();
