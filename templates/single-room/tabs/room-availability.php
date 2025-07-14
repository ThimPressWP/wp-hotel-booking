<?php 
/**
 * The template for displaying single room faqs.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/single-room/tabs/room-available.php.
 *
 * @author  ThimPress
 * @package WP-Hotel-Booking/Templates
 * @version 2.2.1
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
if ( ! $room ) {
	return;
}
?>
<div class="wphb-room-availability">
	<div class="wphb-room-calendar" data-room-id="<?php echo esc_attr( $room->ID ); ?>"></div>
	<div class="wphb-room-buttons">
		<button class="hb_button wphb-cancel-selected-date"><?php esc_html_e( 'Cancel', 'wp-hotel-booking' ) ?></button>
		<button class="hb_button wphb-check-selected-date"><?php esc_html_e( 'Apply', 'wp-hotel-booking' ) ?></button>
	</div>
</div>