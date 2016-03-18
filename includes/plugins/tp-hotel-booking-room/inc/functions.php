<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-18 15:32:23
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-03-21 16:10:34
 */

if( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! function_exists( 'hotel_booking_get_qty' ) ) {

	/**
	 * hotel_booking_get_qty function
	 * @param  array  $args room_id, check_in_date, check_out_date
	 * @return quantity room available
	 */
	function hotel_booking_get_qty( $args = array() ) {

		$args = wp_parse_args( $args, array(
				'room_id'	=> null,
				'check_in_date'	=> null,
				'check_out_date'	=> null
			) );

		if ( ! $args['room_id'] ) {
			return new WP_Error( 'room_id_invalid', __( 'Room is invalid.', 'tp-hotel-booking-room' ) );
		}

		if ( ! $args['check_in_date'] ) {
			return new WP_Error( 'check_in_date_invalid', __( 'Check in date is invalid.', 'tp-hotel-booking-room' ) );
		}

		if ( ! $args['check_out_date'] ) {
			return new WP_Error( 'check_out_date_invalid', __( 'Check out date is invalid.', 'tp-hotel-booking-room' ) );
		}

		global $wpdb;

		// booking status
	    $booking_status = $wpdb->prepare("
	            (
	                SELECT booking.post_status
	                FROM {$wpdb->posts} booking
	                WHERE
	                    booking.post_type = %s
	                    AND bk.meta_value = booking.ID
	            )
	        ", 'hb_booking' );
	    // room booking item
		$query = $wpdb->prepare("
		        SELECT count(room.ID)
	            FROM {$wpdb->posts} room
	            INNER JOIN {$wpdb->postmeta} bm ON bm.post_id = room.ID AND bm.meta_key = %s
	            INNER JOIN {$wpdb->postmeta} bi ON bi.post_id = room.ID AND bi.meta_key = %s
	            INNER JOIN {$wpdb->postmeta} bo ON bo.post_id = room.ID AND bo.meta_key = %s
	            INNER JOIN {$wpdb->postmeta} bk ON bk.post_id = room.ID AND bk.meta_key = %s
	            WHERE
	                room.post_type = %s
	                AND bm.meta_value = %d
	                AND (
		                ( bi.meta_value <= %d AND bo.meta_value >= %d )
		                OR ( bi.meta_value >= %d AND bi.meta_value < %d )
		                OR ( bo.meta_value > %d AND bo.meta_value <= %d )
	                )
	                AND {$booking_status} IN ( %s, %s, %s )
		    ", '_hb_id', '_hb_check_in_date', '_hb_check_out_date', '_hb_booking_id', 'hb_booking_item', $args['room_id'],
		        $args['check_in_date'], $args['check_out_date'],
		        $args['check_in_date'], $args['check_out_date'],
		        $args['check_in_date'], $args['check_out_date'],
		        'hb-pending', 'hb-processing', 'hb-completed'
        );

		$total = get_post_meta( $args['room_id'], '_hb_num_of_rooms', true );
        $unavailable = $wpdb->get_var( $query );

        return $total > $unavailable ? $total - $unavailable : new WP_Error( 'no_room_found', __( 'No room found.', 'tp-hotel-booking-room' ) );
	}

}
