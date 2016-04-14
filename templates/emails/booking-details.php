<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-14 10:46:27
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-14 15:10:40
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>

<h4><?php _e( 'Booking Details', 'tp-hotel-booking' ) ?></h4>
<table>
	<tr>
		<th><?php _e( 'Item', 'tp-hotel-booking' ) ?></th>
		<th><?php _e( 'Check in', 'tp-hotel-booking' ) ?></th>
		<th><?php _e( 'Check out', 'tp-hotel-booking' ) ?></th>
		<th><?php _e( '#', 'tp-hotel-booking' ) ?>#</th>
		<th><?php _e( 'Price', 'tp-hotel-booking' ) ?></th>
	</tr>
	<?php $rooms = hb_get_order_items( $booking->id ); foreach ( $items as $k => $item ) : ?>

		<tr>
			<td><?php printf( '%s', $item->order_item_name ) ?></td>
			<td><?php printf( '%s', date_i18n( hb_get_date_format(), hb_get_order_item_meta( $item->order_item_id, 'check_in_date', true ) ) ) ?></td>
			<td><?php printf( '%s', date_i18n( hb_get_date_format(), hb_get_order_item_meta( $item->order_item_id, 'check_out_date', true ) ) ) ?></td>
			<td><?php printf( '%s', hb_get_order_item_meta( $item->order_item_id, 'qty', true ) ) ?></td>
			<td><?php printf( '%s', hb_format_price( hb_get_order_item_meta( $item->order_item_id, 'subtotal', true ), hb_get_currency_symbol( $booking->currency ) ) ) ?></td>
		</tr>

		<?php do_action( 'hotel_booking_email_after_room_item', $room, $hb_booking ); ?>
	<?php endforeach; ?>
	<tr>
		<td colspan="2"><b><?php _e( 'Subtotal', 'tp-hotel-booking' ) ?></b></td>
		<td><?php printf( '%s', hb_format_price( $booking->sub_total(), hb_get_currency_symbol( $booking->currency ) ) ) ?></td>
	</tr>
	<tr>
		<td colspan="2"><b><?php _e( 'Total', 'tp-hotel-booking' ) ?></b></td>
		<td><?php printf( '%s', hb_format_price( $booking->total(), hb_get_currency_symbol( $booking->currency ) ) ) ?></td>
	</tr>
</table>

<?php if ( $booking->content ) : ?>
	<h4><?php _e( 'Addition Infomation', 'tp-hotel-booking' ); ?></h4>
	<p><?php printf( '%s', $booking->content ) ?></p>
<?php endif; ?>
