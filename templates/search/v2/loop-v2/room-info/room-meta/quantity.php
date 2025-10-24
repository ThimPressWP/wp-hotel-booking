<?php
if ( ! isset( $room ) ) {
	return;
}
?>
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
