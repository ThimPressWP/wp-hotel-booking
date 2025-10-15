<?php
/**
 * Template for add to cart form
 */

defined( 'ABSPATH' ) || exit();

if ( ! isset( $room ) ) {
	return;
}
$room_qty = hb_get_request( 'room_qty', 1 );
?>

<div class="wpdb-room-tmpl-add-to-cart" style="display: none">
	<form action="" name="hb-search-results"
			class="hb-search-room-results hotel-booking-search hotel-booking-single-room-action">

		<div class="hb-booking-room-form-head">
			<p class="description">
				<span class="wphb-room-dates-checked"></span>
			</p>
		</div>

		<div class="hb-search-results-form-container">
			<div class="hb-booking-room-form-field hb-form-field-input">
				<?php if ( ! get_option( 'tp_hotel_booking_single_purchase' ) ) { ?>
					<label><?php echo __( 'Select number of room', 'wp-hotel-booking' ); ?></label>
					<div class="wphb-max-qty">
						<?php _e( 'Max quantity can book:', 'wp-hotel-booking' ); ?> <span class="qty-max">1</span>
					</div>
					<div>
						<input name="hb-num-of-rooms" class="number_room_select" type="number" min="1" step="1" value="<?php echo esc_attr( $room_qty ) ?>">
					</div>
				<?php } ?>
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
