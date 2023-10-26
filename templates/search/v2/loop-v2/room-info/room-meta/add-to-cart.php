<?php
if ( ! isset( $room ) ) {
	return;
}
?>
<?php do_action( 'hotel_booking_loop_before_btn_select_room', $room->post->ID ); ?>
<li class="hb_search_add_to_cart">
	<button class="hb_add_to_cart"><?php _e( 'Select this room', 'wp-hotel-booking' ); ?></button>
</li>
