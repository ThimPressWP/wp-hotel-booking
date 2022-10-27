<?php
/**
 * Template for displaying single search button.
 *
 * @author  ThimPress
 * @package  WP-Hotel-Booking/Booking-Room/Templates
 * @version  1.7.2
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

global $hb_room;

/**
 * check option external link roooms
 */
$external_link = get_post_meta( $hb_room->ID, '_hb_external_link', true );
?>

<a href="<?php echo $external_link ?? '#'; ?>" <?php echo ! empty( $external_link ) ? 'target="_blank"' : ''; ?> data-id="<?php echo esc_attr( $hb_room->ID ); ?>" data-name="<?php echo esc_attr( $hb_room->name ); ?>"
   class="hb_button hb_primary" id="hb_room_load_booking_form">
	<?php _e( 'Check Availability This Room', 'wp-hotel-booking' ); ?>
</a>
