<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-31 15:40:31
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-03-31 15:40:49
 */

/**
 * Hook
 */
add_action( 'hotel_booking_create_booking', 'hotel_booking_create_booking', 10, 1 );
add_action( 'hb_booking_status_changed', 'hotel_booking_create_booking', 10, 1 );
if ( ! function_exists( 'hotel_booking_create_booking' ) ) {
    function hotel_booking_create_booking( $booking_id ) {
        $booking_status = get_post_status( $booking_id );
        if ( $booking_status === 'hb-pending' ) {
            wp_clear_scheduled_hook( 'hotel_booking_change_cancel_booking_status', array( $booking_id ) );
            $time = hb_settings()->get( 'cancel_payment', 12 ) * HOUR_IN_SECONDS;
            wp_schedule_single_event( time() + $time, 'hotel_booking_change_cancel_booking_status', array( $booking_id ) );
        }
    }
}

// change booking status pending => status
add_action( 'hotel_booking_change_cancel_booking_status', 'hotel_booking_change_cancel_booking_status', 10, 1 );
if ( ! function_exists( 'hotel_booking_change_cancel_booking_status' ) ) {
    function hotel_booking_change_cancel_booking_status( $booking_id ) {
        global $wpdb;

        $booking_status = get_post_status( $booking_id );
        if ( $booking_status === 'hb-pending' ) {
            wp_update_post( array(
                    'ID'                => $booking_id,
                    'post_status'       => 'hb-cancelled'
                ) );
        }
    }
}
