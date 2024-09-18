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

$block_id    = get_post_meta( $room->ID, 'hb_blocked_id', true );
$dates_block = get_post_meta( $block_id, 'hb_blocked_time' );
?>

<div id="hotel_booking_room_hidden">
	<h2><?php printf( '%s', $room->post_title ); ?></h2>
	<div class="wphb-room-tmpl-dates-available">
		<form action="POST" name="hb-search-single-room" class="hb-search-room-results hotel-booking-search hotel-booking-single-room-action">
			<div class="hb-booking-room-form-head">
				<p class="description"><?php _e( 'Please set arrival date and departure date before check available.', 'wp-hotel-booking' ); ?></p>
			</div>

			<div class="hb-search-results-form-container">
				<div class="hb-booking-room-form-group">
					<div class="hb-booking-room-form-field hb-form-field-input">
						<input type="text" name="check_in_date" value=""
								placeholder="<?php _e( 'Arrival Date', 'wp-hotel-booking' ); ?>"/>
					</div>
				</div>
				<div class="hb-booking-room-form-group">
					<div class="hb-booking-room-form-field hb-form-field-input">
						<input type="text" name="check_out_date" value=""
								placeholder="<?php _e( 'Departure Date', 'wp-hotel-booking' ); ?>"/>
					</div>
				</div>
				<div class="hb-booking-room-form-group">
					<input type="hidden" name="room-name" value="<?php printf( '%s', $room->post_title ); ?>" />
					<input type="hidden" name="room-id" value="<?php printf( '%s', $room->ID ); ?>" />
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
	<div class="wpdb-room-tmpl-add-to-cart" style="display: none">
		<form action="" name="hb-search-results"
				class="hb-search-room-results hotel-booking-search hotel-booking-single-room-action">

			<div class="hb-booking-room-form-head">
				<p class="description">
                    <span class="wphb-room-dates-checked"></span>
				</p>
			</div>

			<div class="hb-search-results-form-container">
				<div class="hb-booking-room-form-group">
					<div class="hb-booking-room-form-field hb-form-field-input">
						<?php if ( ! get_option( 'tp_hotel_booking_single_purchase' ) ) { ?>
							<label>
								<?php echo __( 'Select number of room', 'wp-hotel-booking' ); ?>
								<select name="hb-num-of-rooms" class="number_room_select">
									<option value="1">1</option>
								</select>
							</label>
						<?php } ?>
					</div>
				</div>
				<?php
				wphb_get_template_no_override(
					'single-room/search/extra-check-dates-room.php',
					[ 'post_id' => $room->ID ]
				);
				?>
			</div>
			<div class="hb-booking-room-form-footer">
				<button href="#" data-template="hb-room-load-form"
						class="hb_previous_step hb_button">
					<?php _e( 'Previous', 'wp-hotel-booking' ); ?>
				</button>
				<button type="submit" class="hb_button">
					<span class="dashicons dashicons-update hide wphb-icon"></span>
					<?php _e( 'Add To Cart', 'wp-hotel-booking' ); ?>
				</button>
				<input type="hidden" name="action" value="hotel_booking_ajax_add_to_cart" />
				<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'hb_booking_nonce_action' ); ?>" />
				<input type="hidden" name="from-check-dates-room" value="1" />
			</div>
		</form>
	</div>
</div>
