<?php

add_action( 'hb_before_search_result', 'hb_enqueue_lightbox_assets' );
add_action( 'hb_lightbox_assets_lightbox2', 'hb_lightbox_assets_lightbox2' );
add_action( 'hb_lightbox_assets_fancyBox', 'hb_lightbox_assets_fancyBox' );

add_action( 'hb_wrapper_start', 'hb_display_message' );

// single-room.php hook template
add_action( 'hotel_booking_before_main_content', 'hotel_booking_before_main_content' );
add_action( 'hotel_booking_after_main_content', 'hotel_booking_after_main_content' );
add_action( 'hotel_booking_sidebar', 'hotel_booking_sidebar' );