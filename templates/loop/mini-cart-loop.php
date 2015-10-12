<div class="hb_mini_cart_item" data-search-key="<?php echo $room->in_to_out; ?>" data-id="<?php echo $room->ID ?>">

	<div class="hb_mini_cart_top">

		<h4><?php printf( '%s (%s)', $room->name, $room->capacity_title ) ?></h4>
		<span class="hb_mini_cart_remove"><i class="fa fa-times"></i></span>
	</div>

	<div class="hb_mini_cart_number">

		<label><?php _e( 'Number of room: ', 'tp-hotel-booking' ); ?></label>
		<span><?php printf( '%s', $room->quantity );  ?></span>

	</div>

	<div class="hb_mini_cart_number">

		<label><?php _e( 'Price: ', 'tp-hotel-booking' ); ?></label>
		<span><?php printf( '%s', hb_format_price( $room->total ) ) ?></span>

	</div>
</div>