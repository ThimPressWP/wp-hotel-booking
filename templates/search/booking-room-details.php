<?php

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

?>
<div class="hb-booking-room-details">
    <span class="hb_search_room_item_detail_price_close">
        <i class="fa fa-times"></i>
    </span>
	<?php $details = $room->get_booking_room_details(); ?>
    <table class="hb_search_room_pricing_price">
        <tbody>
		<?php foreach ( $details as $day => $info ): ?>
            <tr>
                <td class="hb_search_item_day"><?php printf( '%s', hb_date_to_name( $day ) ) ?></td>
                <td class="hb_search_item_total_description">
					<?php printf( 'x%d %s', $info['count'], __( 'Night', 'wp-hotel-booking' ) ) ?>
                </td>
                <td class="hb_search_item_price">
					<?php echo hb_format_price( round( $info['price'], 2 ) ); ?>
                </td>
            </tr>
		<?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <td class="hb_search_item_total_bold">
				<?php _e( 'Total', 'wp-hotel-booking' ) ?>
            </td>
            <td class="hb_search_item_total_description">
				<?php
				if ( hb_price_including_tax() ) {
					_e( '* vat is included', 'wp-hotel-booking' );
				} else {
					_e( '* vat is not included yet', 'wp-hotel-booking' );
				}
				?>
            </td>
            <td class="hb_search_item_price">
				<?php echo hb_format_price( $room->amount_singular ); ?>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
