<?php
/**
 * The template for displaying calendar pricing.
 *
 * @author  ThimPress
 * @package WP-Hotel-Booking/Templates
 * @version 2.2.2
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
if ( empty( $room ) ) {
	$room_id = get_the_ID();
	$room    = function_exists( 'wphb_get_room' ) ? wphb_get_room( $room_id ) : ( class_exists( 'WPHB_Room' ) ? WPHB_Room::instance( $room_id ) : null );
} else {
	$room_id = is_object( $room ) && isset( $room->ID ) ? $room->ID : ( is_numeric( $room ) ? absint( $room ) : get_the_ID() );
}

if ( empty( $room_id ) ) {
	return;
}

$room_blocked_dates = function_exists( 'hb_get_room_blocked_dates' ) ? hb_get_room_blocked_dates( $room_id ) : array();
?>
<div class="wphb-room-calendar-pricing-wrap">
	<div class="wphb-room-calendar-pricing" data-room-id="<?php echo esc_attr( $room_id ); ?>" data-block-dates="<?php echo esc_attr( wp_json_encode( $room_blocked_dates ) ); ?>"></div>
	<div class="wphb-room-calendar-pricing-buttons" style="display: none">
		<button class="hb_button hb-btn-cancel"><?php esc_html_e( 'Cancel', 'wp-hotel-booking' ); ?></button>
		<button class="hb_button hb-btn-apply"><?php esc_html_e( 'Apply', 'wp-hotel-booking' ); ?></button>
	</div>
</div>
