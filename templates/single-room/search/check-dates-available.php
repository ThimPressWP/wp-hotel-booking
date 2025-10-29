<?php
/**
 * Template for displaying single search available.
 *
 * @since 2.1.3
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit();

if ( ! isset( $room ) ) {
	return;
}
$room_id     = $room->ID;
$block_id    = get_post_meta( $room_id, 'hb_blocked_id', true );
$dates_block = get_post_meta( $block_id, 'hb_blocked_time' );
$max_adult   = (int) get_post_meta( $room_id, '_hb_room_capacity_adult', true );
$max_adult   = $max_adult > 0 ? $max_adult : 1;
$max_child   = (int) get_post_meta( $room_id, '_hb_max_child_per_room', true );
?>

<div id="hotel_booking_room_hidden" style="display: none">
	<h2><?php printf( '%s', $room->post_title ); ?></h2>
	<div class="wphb-room-tmpl-dates-available">
		<form action="POST" name="hb-search-single-room" class="hb-search-room-results hotel-booking-search hotel-booking-single-room-action">
			<div class="hb-booking-room-form-head">
				<p class="description"><?php _e( 'Please set check-in date and check-out date before check available.', 'wp-hotel-booking' ); ?></p>
			</div>

			<div class="hb-search-results-form-container">
				<div class="hb-booking-room-form-group">
					<div class="hb-booking-room-form-field hb-form-field-input">
						<input type="text" name="check_in_date" value="" placeholder="<?php _e( 'Check-in Date', 'wp-hotel-booking' ); ?>"/>
						<input type="text" name="select-date-range" style="display:none;" data-hidden="1" placeholder="<?php _e( 'Select Dates', 'wp-hotel-booking' ); ?>">
					</div>
				</div>
				<div class="hb-booking-room-form-group">
					<div class="hb-booking-room-form-field hb-form-field-input">
						<input type="text" name="check_out_date" value="" placeholder="<?php _e( 'Check-out Date', 'wp-hotel-booking' ); ?>"/>
					</div>
				</div>
				<div class="hb-booking-room-form-group">
					<div class="hb-booking-room-form-field hb-form-field-input">
						<input type="number" name="adult_qty" value=""
								placeholder="<?php _e( 'Adult', 'wp-hotel-booking' ); ?>"
								min="1" max="<?php echo esc_attr( $max_adult ); ?>" />
					</div>
				</div>
				<div class="hb-booking-room-form-group">
					<div class="hb-booking-room-form-field hb-form-field-input">
						<input type="number" name="child_qty" value=""
								placeholder="<?php _e( 'Children', 'wp-hotel-booking' ); ?>"
								min="0" max="<?php echo esc_attr( $max_child ); ?>" />
					</div>
				</div>
				<div class="hb-booking-room-form-group">
					<input type="hidden" name="room-name" value="<?php printf( '%s', $room->post_title ); ?>" />
					<input type="hidden" name="room-id" value="<?php printf( '%s', $room_id ); ?>" />
					<input type="hidden" name="action" value="hotel_booking_single_check_room_available"/>
					<input type="hidden" name="wpbh-dates-block" value="<?php echo htmlentities2( json_encode( $dates_block ) ); ?>">
					<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'hb_booking_nonce_action' ); ?>" />
					<button type="submit" class="hb_button">
						<span class="dashicons dashicons-update hide wphb-icon"></span>
						<?php _e( 'Check Available', 'wp-hotel-booking' ); ?>
					</button>
				</div>
			</div>
		</form>
	</div>
	<?php wphb_get_template_no_override( 'single-room/search/add-to-cart.php', compact( 'room' ) ); ?>
</div>
