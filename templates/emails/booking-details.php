<?php
/**
 * The template for displaying booking details in email.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/emails/booking-details.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit(); ?>

<h2 class="section-title"><?php echo __( 'Booking ', 'wp-hotel-booking' ) . $booking->get_booking_number(); ?></h2>

<table class="width-100 booking_details" cellspacing="0" cellpadding="0">
	<tr>
		<th><?php esc_html_e( 'Room', 'wp-hotel-booking' ); ?></th>
		<th><?php esc_html_e( 'Check in', 'wp-hotel-booking' ); ?></th>
		<th><?php esc_html_e( 'Check out', 'wp-hotel-booking' ); ?></th>
		<th><?php esc_html_e( '#', 'wp-hotel-booking' ); ?></th>
		<th><?php esc_html_e( 'Price', 'wp-hotel-booking' ); ?></th>
	</tr>

	<?php $items = hb_get_order_items( $booking->id ); ?>

	<?php foreach ( $items as $k => $item ) { ?>
		<tr>
			<td><?php printf( '%s', $item->order_item_name ); ?></td>
			<td><?php printf( '%s', date_i18n( hb_get_date_format(), hb_get_order_item_meta( $item->order_item_id, 'check_in_date', true ) ) ); ?></td>
			<td><?php printf( '%s', date_i18n( hb_get_date_format(), hb_get_order_item_meta( $item->order_item_id, 'check_out_date', true ) ) ); ?></td>
			<td><?php printf( '%s', hb_get_order_item_meta( $item->order_item_id, 'qty', true ) ); ?></td>
			<td><?php printf( '%s', hb_format_price( hb_get_order_item_meta( $item->order_item_id, 'subtotal', true ), hb_get_currency_symbol( $booking->currency ) ) ); ?></td>
		</tr>

		<?php do_action( 'hotel_booking_email_after_room_item', $item, $booking ); ?>
	<?php } ?>

	<tr>
		<td colspan="4"><b><?php esc_html_e( 'Subtotal', 'wp-hotel-booking' ); ?></b></td>
		<td><?php printf( '%s', hb_format_price( $booking->sub_total(), hb_get_currency_symbol( $booking->currency ) ) ); ?></td>
	</tr>
	<tr>
		<td colspan="4"><b><?php esc_html_e( 'Payment method', 'wp-hotel-booking' ); ?></b></td>
		<td><?php echo ! empty( $booking->method_title ) ? esc_html( $booking->method_title ) : __( 'Offline Payment', 'wp-hotel-booking' ); ?></td>
	</tr>
	<tr>
		<td colspan="4"><b><?php esc_html_e( 'Total', 'wp-hotel-booking' ); ?></b></td>
		<td><?php printf( '%s', hb_format_price( $booking->total(), hb_get_currency_symbol( $booking->currency ) ) ); ?></td>
	</tr>
	<?php if ( $advance_payment = $booking->advance_payment ) { ?>
		<tr>
			<td colspan="4"><b><?php esc_html_e( 'Advance Payment', 'wp-hotel-booking' ); ?></b></td>
			<td><?php printf( '%s', hb_format_price( $advance_payment, hb_get_currency_symbol( $booking->currency ) ) ); ?></td>
		</tr>
	<?php } ?>

</table>

<?php if ( $booking->content ) { ?>

	<h2><?php esc_html_e( 'Additional Information', 'wp-hotel-booking' ); ?></h2>
	<p><?php printf( '%s', $booking->content ); ?></p>

<?php } ?>
