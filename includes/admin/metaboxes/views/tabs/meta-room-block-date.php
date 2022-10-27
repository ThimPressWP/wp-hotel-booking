<?php
/**
 * Admin View: Pricing talbe view.
 *
 * @version     1.9.7
 * @package     WP_Hotel_Booking/Views
 * @category    Views
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

$room_id = $post->ID;
if ( empty( $room_id ) ) {
	return;
}

?>
<div class="wrap" id="tp_hotel_booking_block_date">
	<h1><?php _e( 'Block Special Date', 'wp-hotel-booking' ); ?></h1>
	<div id="calender_block"></div>
	<button class="button button-primary update_block"><?php _e( 'Update', 'wp-hotel-booking' ); ?></button>
</div>
