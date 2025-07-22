<?php
if ( ! isset( $room ) ) {
	return;
}
$price_display = apply_filters( 'hotel_booking_loop_room_price_display_style', WPHB_Settings::instance()->get( 'price_display' ) );
$booking_room_details = $room->get_booking_room_details();
$pricings             = [];
if ( ! empty( $booking_room_details ) ) {
	foreach ( $booking_room_details as $day_on_week => $day ) {
		$pricings[] = $day['price'];
	}
}
if ( empty( $pricings ) ) {
	$price = hb_format_price( $room->get_price() );
} else {
	$min = min( $pricings );
	$max = max( $pricings );
	if ( $price_display === 'max' ) {
		$price = hb_format_price( $max );
	} elseif ( $price_display === 'min_to_max' ) {
		$price = hb_format_price( $min ) . ' - ' . hb_format_price( $max );
	} else {
		$price = hb_format_price( $min );
	}
}
?>
<li class="hb_search_price">
	<label><?php _e( 'Price:', 'wp-hotel-booking' ); ?></label>
	<span
			class="hb_search_item_price"><?php echo $price; ?></span>
	<div class="hb_view_price">
		<a href=""
			class="hb-view-booking-room-details"><?php _e( '(View price breakdown)', 'wp-hotel-booking' ); ?></a>
		<?php hb_get_template( 'search/booking-room-details.php', array( 'room' => $room ) ); ?>
	</div>
</li>
