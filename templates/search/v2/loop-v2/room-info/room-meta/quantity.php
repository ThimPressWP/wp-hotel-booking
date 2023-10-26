<?php
if ( ! isset( $room ) ) {
	return;
}

$single_purchase = get_option( 'tp_hotel_booking_single_purchase' );
?>
<?php if ( ! $single_purchase ) { ?>
	<li class="hb_search_quantity">
		<label><?php _e( 'Quantity: ', 'wp-hotel-booking' ); ?></label>
		<div>
			<?php
			hb_dropdown_numbers(
				array(
					'name'             => 'hb-num-of-rooms',
					'min'              => 1,
					'show_option_none' => __( 'Select', 'wp-hotel-booking' ),
					'max'              => $room->post->available_rooms,
					'class'            => 'number_room_select',
				)
			);
			?>
		</div>
	</li>
<?php } else { ?>
	<select name="hb-num-of-rooms" class="number_room_select" style="display: none;">
		<option value="1">1</option>
	</select>
<?php } ?>
