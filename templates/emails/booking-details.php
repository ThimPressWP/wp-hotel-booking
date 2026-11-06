<?php
/**
 * The template for displaying booking details in email.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/emails/booking-details.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 2.3.5
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
	<?php
	$payment_method_title = $booking->method_title;
	if ( empty( $payment_method_title ) ) {
		$method_slug = $booking->method;
		if ( ! empty( $method_slug ) ) {
			$gateways = hb_get_payment_gateways();
			if ( isset( $gateways[ $method_slug ] ) && ! empty( $gateways[ $method_slug ]->title ) ) {
				$payment_method_title = $gateways[ $method_slug ]->title;
			} else {
				$payment_method_title = ucfirst( str_replace( array( '-', '_' ), ' ', $method_slug ) );
			}
		}
	}
	if ( empty( $payment_method_title ) ) {
		$payment_method_title = __( 'N/A', 'wp-hotel-booking' );
	}
	?>
	<tr>
		<td colspan="4"><b><?php esc_html_e( 'Payment method', 'wp-hotel-booking' ); ?></b></td>
		<td><?php echo esc_html( $payment_method_title ); ?></td>
	</tr>
	<tr>
		<td colspan="4"><b><?php esc_html_e( 'Total', 'wp-hotel-booking' ); ?></b></td>
		<td><?php printf( '%s', hb_format_price( $booking->total(), hb_get_currency_symbol( $booking->currency ) ) ); ?></td>
	</tr>
	<?php
	$advance_payment = floatval( $booking->advance_payment );
	$total           = floatval( $booking->total() );
	if ( $advance_payment > 0 && $advance_payment < $total ) {
		$remaining_balance = $total - $advance_payment;
		$status            = method_exists( $booking, 'get_status' ) ? $booking->get_status() : ( $booking->status ?? '' );
		$is_pending        = ( 'pending' === $status );
		?>
		<tr>
			<td colspan="4">
				<b>
					<?php
					if ( $is_pending ) {
						esc_html_e( 'Advance Payment (To pay)', 'wp-hotel-booking' );
					} else {
						esc_html_e( 'Advance Payment (Paid)', 'wp-hotel-booking' );
					}
					?>
				</b>
			</td>
			<td>
				<?php printf( '%s', hb_format_price( $advance_payment, hb_get_currency_symbol( $booking->currency ) ) ); ?>
				<?php if ( $is_pending ) : ?>
					<br><small style="color: #d32f2f;"><?php esc_html_e( 'Awaiting payment confirmation', 'wp-hotel-booking' ); ?></small>
				<?php endif; ?>
			</td>
		</tr>
		<?php if ( $remaining_balance > 0 ) { ?>
		<tr>
			<td colspan="4"><b><?php esc_html_e( 'Remaining Balance (Due on arrival)', 'wp-hotel-booking' ); ?></b></td>
			<td><strong style="color: #2e7d32;"><?php printf( '%s', hb_format_price( $remaining_balance, hb_get_currency_symbol( $booking->currency ) ) ); ?></strong></td>
		</tr>
		<?php } ?>
	<?php } ?>

</table>

<?php if ( $booking->content ) { ?>

	<h2><?php esc_html_e( 'Additional Information', 'wp-hotel-booking' ); ?></h2>
	<p><?php printf( '%s', $booking->content ); ?></p>

<?php } ?>
