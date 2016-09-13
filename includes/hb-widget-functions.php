<?php
if ( !defined( 'ABSPATH' ) ) {
    exit();
}

add_action( 'widgets_init', 'hotel_booking_widget_init' );
if ( !function_exists( 'hotel_booking_widget_init' ) ) {

    function hotel_booking_widget_init() {
        register_widget( 'HB_Widget_Search' );
        register_widget( 'HB_Widget_Room_Carousel' );
        register_widget( 'HB_Widget_Best_Reviews' );
        register_widget( 'HB_Widget_Lastest_Reviews' );
        register_widget( 'HB_Widget_Mini_Cart' );
    }

}
