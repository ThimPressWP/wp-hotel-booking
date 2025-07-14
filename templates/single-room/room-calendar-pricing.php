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
if ( ! isset( $room ) ) {
	return;
}
?>
<div class="wphb-room-calendar-pricing-wrap">
	<div class="wphb-room-calendar-pricing" data-room-id="<?php echo esc_attr( $room->ID ); ?>"></div>
	<div class="wphb-room-calendar-pricing-buttons" style="display: none">
		<button class="hb_button hb-btn-cancel"><?php esc_html_e( 'Cancel', 'wp-hotel-booking' ); ?></button>
		<button class="hb_button hb-btn-apply"><?php esc_html_e( 'Apply', 'wp-hotel-booking' ); ?></button>
	</div>
</div>
