<?php 
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

$check_in_date  = $room->get_data('check_in_date') ?? date( 'Y/m/d' );
$check_out_date = $room->get_data( 'check_out_date' ) ?? date( 'Y/m/d', strtotime( '+1 day' ) );
$adults         = hb_get_request( 'adults', 1 );
$children       = hb_get_request( 'max_child', 0 );
$room_qty       = $room->get_data( 'quantity' ) ?? 1;

$available_qty = hotel_booking_get_room_available(
	$room_id,
	array(
		'check_in_date'  => $check_in_date,
		'check_out_date' => $check_out_date,
	)
);
$error_message = '';
if ( is_wp_error( $available_qty ) ) {
	$error_message = $available_qty->get_error_message();
	$available_qty = 0;
}
$room_extra  = HB_Room_Extra::instance( $room_id );
$extra_items = $room_extra->get_extra();
$extra_price = 0;
if ( ! empty( $extra_items ) ) {
	foreach ( $extra_items as $extra_id => $extra ) {
		if ( $extra->required ) {
			$extra_package = hotel_booking_get_product_class( $extra_id,
				array(
					'product_id'     => $extra_id,
					'check_in_date'  => $check_in_date,
					'check_out_date' => $check_out_date,
					'quantity'       => 1,
				)
			);
			$extra_package_price = $extra_package ? $extra_package->get_price_package() : 0;
			$extra_price += $extra_package_price;
		}
	}
}

$include_tax = hb_price_including_tax() ? (float) WPHB_Settings::instance()->get( 'tax' ) : 0;
// $total_price = $room->amount_singular + $extra_price * ( 1 + $include_tax / 100 );
$total_price = $room->amount_singular + $extra_price;

?>
<div id="hotel_booking_room_hidden">
	<div class="wphb-room-tmpl-dates-available">
		<div class="hb-booking-room-form-header" >
			<div class="hb-booking-room-form-head">
				<p class="description"><?php _e( 'Booking form', 'wp-hotel-booking' ); ?></p>
			</div>
			<?php 
				if ( ! isset( $is_elementor ) ) {
					do_action( 'hotel_booking_loop_room_price' );
				}
				
				if ( $error_message ) {  // show error
					echo $error_message; 
				} 
			?>
		</div>
		<form action="POST" name="hb-search-single-room" class="hb-search-room-results hotel-booking-search hotel-booking-single-room-action">
			<div class="hb-search-results-form-container">
				<div class="hb-booking-room-form-group">
					<label><?php esc_html_e( 'Check-in Date', 'wp-hotel-booking' ); ?></label>
					<div class="hb-booking-room-form-field hb-form-field-input">
						<input type="text" name="check_in_date" value="<?php echo esc_attr( $check_in_date ); ?>" placeholder="<?php _e( 'Check-in Date', 'wp-hotel-booking' ); ?>"/>
						<input type="text" name="select-date-range" hidden style="display:none;" data-hidden="1" placeholder="<?php _e( 'Select Dates', 'wp-hotel-booking' ); ?>">
					</div>
				</div>
				<div class="hb-booking-room-form-group">
					<label><?php esc_html_e( 'Check-out Date', 'wp-hotel-booking' ); ?></label>
					<div class="hb-booking-room-form-field hb-form-field-input">
						<input type="text" name="check_out_date" value="<?php echo esc_attr( $check_out_date ); ?>" placeholder="<?php _e( 'Check-out Date', 'wp-hotel-booking' ); ?>"/>
					</div>
				</div>
				<div class="hb-booking-room-form-group">
					<label><?php esc_html_e( 'Adults', 'wp-hotel-booking' ); ?></label>
					<div class="hb-booking-room-form-field hb-form-field-input">
						<input type="number" name="adult_qty" value="<?php echo esc_attr( $adults ); ?>"
								placeholder="<?php _e( 'Adult', 'wp-hotel-booking' ); ?>"
								min="1" max="<?php echo esc_attr( $max_adult ); ?>" />
					</div>
				</div>
				<div class="hb-booking-room-form-group">
					<label><?php esc_html_e( 'Children', 'wp-hotel-booking' ); ?></label>
					<div class="hb-booking-room-form-field hb-form-field-input">
						<input type="number" name="child_qty" value="<?php echo esc_attr( $children ); ?>"
								placeholder="<?php _e( 'Children', 'wp-hotel-booking' ); ?>"
								min="0" max="<?php echo esc_attr( $max_child ); ?>" />
					</div>
				</div>
				<div class="hb-booking-room-form-group">
						<label><?php esc_html_e( 'Room(s)', 'wp-hotel-booking' ); ?></label>
						<div class="wphb-max-qty">
							<?php _e( 'Max:', 'wp-hotel-booking' ); ?> <span class="qty-max"><?php echo esc_html( $available_qty ); ?></span>
						</div>
						<div class="hb-booking-room-form-field hb-form-field-input">
							<input name="hb-num-of-rooms" class="number_room_select" type="number" min="1" step="1" max="<?php echo esc_html( $available_qty ); ?>" value="<?php echo esc_attr( $room_qty ) ?>">
						</div>
				</div>
				<div class="hb-booking-room-form-field">
					<?php
						wphb_get_template_no_override(
							'single-room/search/extra-check-dates-room.php',
							[ 'post_id' => $room->ID ]
						);
					?>
				</div>
				<div class="hb-booking-room-form-group hb-room-price">
					<div class="hb-total-price">
						<span class="hb-total-price-text">
							<?php esc_html_e( 'Total: ', 'wp-hotel-booking' ); ?>
						</span>
						<span class="hb-total-price-value">
							<?php echo esc_html( hb_format_price( $total_price, true, false ) ); ?>
						</span>
					</div>
	                <div class="hb_view_price hb-room-content">
	                    <a href="javascript:void(0)" class="hb-single-room-price-details">
	                    	<span class="dashicons dashicons-update hide wphb-icon"></span>
	                    	<?php esc_html_e( 'View details', 'wp-hotel-booking' ); ?>
	                    </a>
	                </div>
				</div>
				<div class="hb-booking-room-form-group">
					<input type="hidden" name="room-name" value="<?php printf( '%s', $room->post_title ); ?>" />
					<input type="hidden" name="room-id" value="<?php printf( '%s', $room_id ); ?>" />
					<input type="hidden" name="action" value="hotel_booking_ajax_add_to_cart"/>
					<input type="hidden" name="wpbh-dates-block" value="<?php echo htmlentities2( json_encode( $dates_block ) ); ?>">
					<input type="hidden" name="from-check-dates-room" value="1" />
					<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'hb_booking_nonce_action' ); ?>" />
					<button type="submit" class="hb_button">
						<span class="dashicons dashicons-update hide wphb-icon"></span>
						<?php _e( 'Book room', 'wp-hotel-booking' ); ?>
					</button>
				</div>
				<div class="wphb-single-room-loading-overlay hidden" ><div class="wphb-single-room-loading-spinner"></div></div>
			</div>
		</form>
	</div>
</div>