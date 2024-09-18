<?php
/**
 * Template for displaying single search available.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking-room/single-search-available.php.
 *
 * @author  ThimPress
 * @package  WP-Hotel-Booking/Booking-Room/Templates
 * @version  1.7.2
 * @deprecated 2.1.3 Replaced to check-dates-available.php
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

global $post;
if ( ! $post || ! is_single( $post->ID ) || get_post_type( $post->ID ) !== 'hb_room' ) {
	return;
}

$block_id    = get_post_meta( $post->ID, 'hb_blocked_id', true );
$dates_block = get_post_meta( $block_id, 'hb_blocked_time' );
?>

<div id="hotel_booking_room_hidden"></div>
<!--Single search form-->
<script type="text/html" id="tmpl-hb-room-load-form">
	<h2><?php printf( '%s', $post->post_title ); ?></h2>
	<div class="wphb-room-tmpl-dates-available">
		<form action="POST" name="hb-search-single-room" class="hb-search-room-results hotel-booking-search hotel-booking-single-room-action">
			<div class="hb-booking-room-form-head">
				<p class="description"><?php _e( 'Please set arrival date and departure date before check available.', 'wp-hotel-booking' ); ?></p>
			</div>

			<div class="hb-search-results-form-container">
				<div class="hb-booking-room-form-group">
					<div class="hb-booking-room-form-field hb-form-field-input">
						<input type="text" name="check_in_date" value=""
								placeholder="<?php _e( 'Arrival Date', 'wp-hotel-booking' ); ?>" autocomplete="off"/>
					</div>
				</div>
				<div class="hb-booking-room-form-group">
					<div class="hb-booking-room-form-field hb-form-field-input">
						<input type="text" name="check_out_date" value=""
								placeholder="<?php _e( 'Departure Date', 'wp-hotel-booking' ); ?>" autocomplete="off"/>
					</div>
				</div>
				<div class="hb-booking-room-form-group">
					<input type="hidden" name="room-name" value="<?php printf( '%s', $post->post_title ); ?>" />
					<input type="hidden" name="room-id" value="<?php printf( '%s', $post->ID ); ?>" />
					<input type="hidden" name="action" value="hotel_booking_single_check_room_available"/>
					<?php wp_nonce_field( 'hb_booking_single_room_check_nonce_action', 'hb-booking-single-room-check-nonce-action' ); ?>
					<input type="hidden" name="wpbh-dates-block" value="<?php echo htmlentities2( json_encode( $dates_block ) ); ?>">
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
					<?php _e( 'Please select number of room and packages (optional)', 'wp-hotel-booking' ); ?>
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
				tp_hb_extra_get_template( 'loop/extra-search-room.php', array( 'post_id' => $post->ID ) );
				?>
			</div>

			<div class="hb-booking-room-form-footer">
				<button href="#" data-template="hb-room-load-form"
					class="hb_previous_step hb_button"><?php _e( 'Previous', 'wp-hotel-booking' ); ?></button>
				<button type="submit" class="hb_button">
                    <span class="dashicons dashicons-update hide wphb-icon"></span>
                    <?php _e( 'Add To Cart', 'wp-hotel-booking' ); ?>
                </button>
				<input type="hidden" name="action" value="hotel_booking_ajax_add_to_cart" />
				<input type="hidden" name="is_single" value="1" />
				<?php wp_nonce_field( 'hb_booking_nonce_action', 'nonce' ); ?>
			</div>
		</form>
	</div>
</script>
