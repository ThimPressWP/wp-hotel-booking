<?php
if ( ! isset( $room ) ) {
	return;
}
?>
<li class="hb_search_max_child">
    <label><?php _e( 'Max Children:', 'wp-hotel-booking' ); ?></label>
    <div><?php echo esc_html( $room->max_child ); ?></div>
</li>
