<?php
if ( ! isset( $room ) ) {
	return;
}
?>
<li class="hb_search_price">
    <label><?php _e( 'Price:', 'wp-hotel-booking' ); ?></label>
    <span
            class="hb_search_item_price"><?php echo hb_format_price( $room->get_price() ); ?></span>
    <div class="hb_view_price">
        <a href=""
           class="hb-view-booking-room-details"><?php _e( '(View price breakdown)', 'wp-hotel-booking' ); ?></a>
		<?php hb_get_template( 'search/booking-room-details.php', array( 'room' => $room ) ); ?>
    </div>
</li>
