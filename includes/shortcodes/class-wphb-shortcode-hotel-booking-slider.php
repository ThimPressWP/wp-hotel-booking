<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class WPHB_Shortcode_Hotel_Booking_Slider extends WPHB_Shortcodes {

    public $shortcode = 'hotel_booking_slider';

    public function __construct() {
        parent::__construct();
    }

    function add_shortcode( $atts, $content = null ) {
        $number_rooms = isset( $atts['rooms'] ) ? (int) $atts['rooms'] : 10;
        // $posts = get_terms( 'hb_room_type', array('hide_empty' => 0)); gallery of room_type taxonmy change to gallery of room post_type

        $args = array(
            'post_type' => 'hb_room',
            'posts_per_page' => $number_rooms,
            'orderby' => 'date',
            'order' => 'DESC',
                // 'meta_key'          => '_hb_gallery'
        );
        $query = new WP_Query( $args );

        if ( $query->have_posts() ):
            hb_get_template( 'shortcodes/carousel.php', array( 'atts' => $atts, 'query' => $query ) );
        endif;
    }

}

new WPHB_Shortcode_Hotel_Booking_Slider();
