<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-25 12:01:51
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-04 13:20:26
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
global $post;
$hb_booking = HB_Booking::instance( $post->ID );
// $rooms = $hb_booking->get_cart_post_type( 'hb_room' );
$rooms = hb_get_order_items( $post->ID );
?>
<style type="text/css">
	#hb-booking-items .inside{
		padding: 0;
		margin: 0;
	}
</style>
<div id="booking_items">

	<table cellpadding="0" cellspacing="0" class="booking_item_table">
		<thead>
			<tr>
				<th class="center">
					<input type="checkbox" id="booking-item-checkall" />
				</th>
				<th class="name left">
					<?php _e( 'Item', 'tp-hotel-booking' ); ?>
				</th>
				<th class="checkin_checkout center">
					<?php _e( 'Checkin - Checkout', 'tp-hotel-booking' ) ?>
				</th>
				<th class="night center">
					<?php _e( 'Night', 'tp-hotel-booking' ); ?>
				</th>
				<th class="qty center">
					<?php _e( 'Qty', 'tp-hotel-booking' ); ?>
				</th>
				<th class="total center">
					<?php _e( 'Total', 'tp-hotel-booking' ); ?>
				</th>
				<th class="total center actions">
					<?php _e( 'Actions', 'tp-hotel-booking' ) ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $rooms as $k => $room ) : ?>
				<?php do_action( 'hotel_booking_before_room_item', $room, $hb_booking ); ?>

				<tr>
					<td class="center">
						<input type="checkbox" name="book_item[]" value="<?php echo esc_attr( $room->order_item_id ) ?>" />
					</td>
					<td class="name left">
						<?php printf( '<a href="%s">%s</a>', get_edit_post_link( hb_get_order_item_meta( $room->order_item_id, 'product_id', true ) ), $room->order_item_name ) ?>
					</td>
					<td class="checkin_checkout center">
						<?php printf( '%s - %s', date_i18n( hb_get_date_format(), hb_get_order_item_meta( $room->order_item_id, 'check_in_date', true ) ), date_i18n( hb_get_date_format(), hb_get_order_item_meta( $room->order_item_id, 'check_out_date', true ) ) ) ?>
					</td>
					<td class="night center">
						<?php printf( '%d', hb_count_nights_two_dates( hb_get_order_item_meta( $room->order_item_id, 'check_out_date', true ), hb_get_order_item_meta( $room->order_item_id, 'check_in_date', true )) ) ?>
					</td>
					<td class="qty center">
						<?php printf( '%s', hb_get_order_item_meta( $room->order_item_id, 'qty', true ) ) ?>
					</td>
					<td class="total center">
						<?php printf( '%s', hb_format_price( hb_get_order_item_meta( $room->order_item_id, 'subtotal', true ), hb_get_currency_symbol( $hb_booking->currency ) ) ); ?>
					</td>
					<td class="actions">
						<a href="#" class="edit" data-id="<?php echo esc_attr( $room->order_item_id ) ?>"><i class="fa fa-pencil"></i></a>
						<a href="#" class="remove" data-id="<?php echo esc_attr( $room->order_item_id ) ?>"><i class="fa fa-times-circle"></i></a>
					</td>
				</tr>

				<?php do_action( 'hotel_booking_after_room_item', $room, $hb_booking ); ?>

			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<th class="left" colspan="3">
					<select id="actions">
						<option><?php _e( 'Delete select item(s)', 'tp-hotel-booking' ); ?></option>
					</select>
					<a href="#" class="button button-primary" id="action_sync"><?php _e( 'Sync', 'tp-hotel-booking' ); ?></a>
				</th>
				<th class="right" colspan="4">
					<?php if ( ! $hb_booking->coupon ) : ?>

						<a href="#" class="button" id="add_coupon"><?php _e( 'Add Coupon', 'tp-hotel-booking' ); ?></a>

					<?php else: ?>

						<a href="#" class="button" id="remove_coupon"><?php _e( 'Remove Coupon', 'tp-hotel-booking' ); ?></a>

					<?php endif; ?>
					<a href="#" class="button" id="add_room_item"><?php _e( 'Add Room Item', 'tp-hotel-booking' ); ?></a>
				</th>
			</tr>
		</tfoot>
	</table>

	<table class="booking_item_table_cost">
		<tbody>
			<?php if ( $hb_booking->coupon ) : ?>
				<tr class="coupon">
					<td class="center">
						<?php printf( __( 'Coupon(<a href="%s">%s</a>)', 'tp-hotel-booking' ), get_edit_post_link( $hb_booking->coupon['id'] ), $hb_booking->coupon['code'] ) ?>
					</td>
					<td class="coupon_discount">
						<?php printf( '-%s', hb_format_price( $hb_booking->coupon['value'], hb_get_currency_symbol( $hb_booking->currency ) ) ); ?>
					</td>
				</tr>
			<?php endif; ?>
			<tr>
				<td class="center">
					<?php _e( 'Sub Total', 'tp-hotel-booking' ) ?>
				</td>
				<td class="subtotal">
					<?php printf( '%s', hb_format_price( hb_booking_subtotal( $hb_booking->id ), hb_get_currency_symbol( $hb_booking->currency ) ) ); ?>
				</td>
			</tr>
			<tr>
				<td class="center">
					<?php _e( 'Tax', 'tp-hotel-booking' ) ?>
				</td>
				<td class="tax">
					<?php //printf( '%s', apply_filters( 'hotel_booking_admin_book_details', abs( $hb_booking->tax * 100 ) . '%', $hb_booking ) ); ?>
					<?php printf( '%s', apply_filters( 'hotel_booking_admin_book_details', hb_format_price( hb_booking_tax_total( $hb_booking->id ), hb_get_currency_symbol( $hb_booking->currency ) ), $hb_booking ) ); ?>
				</td>
			</tr>
			<tr>
				<td class="center">
					<?php _e( 'Grand Total', 'tp-hotel-booking' ) ?>
				</td>
				<td class="grand_total">
					<?php printf( '%s', hb_format_price( hb_booking_total( $hb_booking->id ), hb_get_currency_symbol( $hb_booking->currency ) ) ) ?>
				</td>
			</tr>
		</tbody>
	</table>

</div>

<!--Template JS-->
<script type="text/html" id="tmpl-room-item">
	<tr>
		<td class="center">
			<input type="checkbox" name="book_item[]" value="{{ data.ID }}" />
		</td>
		<td class="name left">
			{{{ data.post_title }}}
		</td>
		<td class="type center">
			{{{ data.type }}}
		</td>
		<td class="cost center">
			{{{ data.cost }}}
		</td>
		<td class="qty center">
			{{{ data.qty }}}
		</td>
		<td class="total center">
			{{{ data.total }}}
		</td>
		<td class="actions">
			<a href="#" class="edit" data-id="{{ data.ID }}"><i class="fa fa-pencil"></i></a>
			<a href="#" class="remove" data-id="{{ data.ID }}"><i class="fa fa-times-circle"></i></a>
		</td>
	</tr>
</script>

<script type="text/html" id="tmpl-room-table-cost">
	<table class="booking_item_table_cost">
		<tbody>
			<# if ( typeof data.coupon !== 'undefined' ) #>
				<tr class="coupon">
					<td class="center">
						{{{ data.coupon.code }}}
					</td>
					<td class="coupon_discount">
						{{{ data.coupon.discount }}}
					</td>
				</tr>
			<# } #>
			<tr>
				<td class="center">
					<?php _e( 'Sub Total', 'tp-hotel-booking' ) ?>
				</td>
				<td class="subtotal">
					{{{ data.sub_total }}}
				</td>
			</tr>
			<tr>
				<td class="center">
					<?php _e( 'Tax', 'tp-hotel-booking' ) ?>
				</td>
				<td class="tax">
					{{{ data.tax }}}
				</td>
			</tr>
			<tr>
				<td class="center">
					<?php _e( 'Grand Total', 'tp-hotel-booking' ) ?>
				</td>
				<td class="grand_total">
					{{{ data.grand_total }}}
				</td>
			</tr>
		</tbody>
	</table>
</script>
<!--End Template JS-->