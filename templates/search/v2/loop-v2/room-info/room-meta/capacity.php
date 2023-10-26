<?php
if ( ! isset( $room ) ) {
	return;
}
?>
<li class="hb_search_capacity">
	<label><?php _e( 'Capacity:', 'wp-hotel-booking' ); ?></label>
	<div class=""><?php echo esc_html( $room->capacity ); ?></div>
</li>
