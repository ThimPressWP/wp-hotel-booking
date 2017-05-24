<?php
/**
 * @Author: ducnvtt
 * @Date  :   2016-04-14 10:46:27
 * @Last  Modified by:   ducnvtt
 * @Last  Modified time: 2016-04-15 16:55:46
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
?>

<h2 class="section-title"><?php echo __( 'Booking ', 'wp-hotel-booking' ) . $booking->get_booking_number(); ?></h2>
<table class="width-100 booking_details" cellspacing="0" cellpadding="0">
    <tr>
        <th><?php _e( 'Room', 'wp-hotel-booking' ) ?></th>
        <th><?php _e( 'Check in', 'wp-hotel-booking' ) ?></th>
        <th><?php _e( 'Check out', 'wp-hotel-booking' ) ?></th>
        <th><?php _e( '#', 'wp-hotel-booking' ) ?></th>
        <th><?php _e( 'Price', 'wp-hotel-booking' ) ?></th>
    </tr>
	<?php $items = hb_get_order_items( $booking->id );
	foreach ( $items as $k => $item ) :
		?>

        <tr>
            <td><?php printf( '%s', $item->order_item_name ) ?></td>
            <td><?php printf( '%s', date_i18n( hb_get_date_format(), hb_get_order_item_meta( $item->order_item_id, 'check_in_date', true ) ) ) ?></td>
            <td><?php printf( '%s', date_i18n( hb_get_date_format(), hb_get_order_item_meta( $item->order_item_id, 'check_out_date', true ) ) ) ?></td>
            <td><?php printf( '%s', hb_get_order_item_meta( $item->order_item_id, 'qty', true ) ) ?></td>
            <td><?php printf( '%s', hb_format_price( hb_get_order_item_meta( $item->order_item_id, 'subtotal', true ), hb_get_currency_symbol( $booking->currency ) ) ) ?></td>
        </tr>

		<?php do_action( 'hotel_booking_email_after_room_item', $item, $booking ); ?>
	<?php endforeach; ?>
    <tr>
        <td colspan="4"><b><?php _e( 'Subtotal', 'wp-hotel-booking' ) ?></b></td>
        <td><?php printf( '%s', hb_format_price( $booking->sub_total(), hb_get_currency_symbol( $booking->currency ) ) ) ?></td>
    </tr>
    <tr>
        <td colspan="4"><b><?php _e( 'Payment method', 'wp-hotel-booking' ) ?></b></td>
        <td><?php echo esc_html( $booking->method_title ) ?></td>
    </tr>
    <tr>
        <td colspan="4"><b><?php _e( 'Total', 'wp-hotel-booking' ) ?></b></td>
        <td><?php printf( '%s', hb_format_price( $booking->total(), hb_get_currency_symbol( $booking->currency ) ) ) ?></td>
    </tr>
</table>

<?php if ( $booking->content ) : ?>
    <h2><?php _e( 'Addition Information', 'wp-hotel-booking' ); ?></h2>
    <p><?php printf( '%s', $booking->content ) ?></p>
<?php endif; ?>
