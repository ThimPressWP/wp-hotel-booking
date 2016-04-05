<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-25 12:01:51
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-06 14:40:19
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
global $post;
$hb_booking = HB_Booking::instance( $post->ID );
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
<!-- Room order item layout -->
<script type="text/html" id="tmpl-hb-room-item">
	<div class="hb_modal">
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
		<# if ( typeof data.extras !== 'undefined' && Object.keys( data.extras ).length() != 0  ) { #>
			<# for ( var i = 0; i < Object.keys( data.extras ).length(); i++ ) { #>
				<# var item = data.extras[i]; #>
				<tr>
					<td class="center">
						<input type="checkbox" name="book_item[]" value="{{ item.ID }}">
					</td>
					<td class="name" colspan="3">{{{ item.name }}}</td>
					<td class="qty">1</td><td class="total">{{{ item.total }}}</td>
					<td class="actions">
						<a href="#" class="edit" data-id="{{ item.ID }}"><i class="fa fa-pencil"></i></a>
						<a href="#" class="remove" data-id="{{ item.ID }}"><i class="fa fa-times-circle"></i></a>
					</td>
				</tr>
			<# } #>

		<# } #>
	</div>
	<div class="hb_modal_overlay"></div>
</script>
<!-- Room order item layout -->

<!--Footer cost booking order item-->
<script type="text/html" id="tmpl-hb-room-table-cost">
	<div class="hb_modal">
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
	</div>
	<div class="hb_modal_overlay"></div>
</script>
<!--Footer cost booking order item-->

<!--Add new or edit oder item-->
<script type="text/html" id="tmpl-hb-add-room">
	<div class="hb_modal">
		<form name="booking-room-item" class="booking-room-item">
			<div class="form_head">
				<h1>
					<# if ( typeof data.modal_title !== 'undefined' ) { #>

						{{{ data.modal_title }}}

					<# } else { #>

						<?php _e( 'Add new item', 'tp-hotel-booking' ) ?>

					<# } #>
				</h1>
				<button class="hb_modal_close dashicons dashicons-no-alt"></button>
			</div>

			<div class="section_line">
				<# if ( typeof data.post_type === 'undefined' || data.post_type === 'hb_room' ) { #>
					<div class="section">
						<select name="room_id" class="booking_search_room_items">
							<# if ( typeof data.room !== 'undefined' ) { #>

								<option value="{{ data.room.ID }}" selected>{{ data.room.post_title }}</option>

							<# } #>
						</select>
					</div>
					<div class="section">
						<input type="text" name="check_in_date" class="check_in_date" value="{{ data.check_in_date }}" placeholder="<?php esc_attr_e( 'Check in', 'tp-hotel-booking' ); ?>" />
						<input type="hidden" name="check_in_date_timestamp" value="{{ data.check_in_date_timestamp }}" />
						<input type="text" name="check_out_date" class="check_out_date" value="{{ data.check_out_date }}" placeholder="<?php esc_attr_e( 'Check out', 'tp-hotel-booking' ); ?>" />
						<input type="hidden" name="check_out_date_timestamp" value="{{ data.check_out_date_timestamp }}" />
					</div>
				<# } #>
				<div class="section">
					<# if ( typeof data.qty !== 'undefined' ) { #>
						<select name="qty">
							<option value="0"><?php _e( 'Quantity' ) ?></option>
							<# for ( var i = 1; i <= data.qty; i++ ) { #>

								<# if ( data.qty_selected == i ) { #>
									<option value="{{ i }}" selected>{{ i }}</option>
								<# } else { #>
									<option value="{{ i }}">{{ i }}</option>
								<# } #>

							<# } #>
						</select>
					<# } #>
				</div>
			</div>

			<# if ( typeof data.extras !== 'undefined' && Object.keys( data.extras ).length() != 0 ) { #>

				<div class="section_line">

					<# console.debug( data.extras ) #>

				</div>

			<# } #>

			<div class="form_footer">
				<?php wp_nonce_field( 'hotel_admin_check_room_available', 'hotel-admin-check-room-available' ); ?>
				<input type="hidden" name="order_item_id" value="{{ data.order_item_id }}" />
				<a href="#" class="button check_available{{ data.class }}"><?php _e( 'Check Available', 'tp-hotel-booking' ); ?></a>
				<button type="reset" class="button hb_modal_close"><?php _e( 'Close', 'tp-hotel-booking' ) ?></button>
				<button type="submit" class="button button-primary hb_form_submit"><?php _e( 'Add', 'tp-hotel-booking' ); ?></button>
			</div>
		</form>
	</div>
	<div class="hb_modal_overlay"></div>
</script>
<!--Add new or edit oder item-->

<!--Confirm-->
<script type="text/html" id="tmpl-hb-confirm">
	<div class="hb_modal">
		<form>
			<div class="form_head">
				<h1>
					<?php _e( 'Do you want to do this?', 'tp-hotel-booking' ); ?>
				</h1>
				<button class="hb_modal_close dashicons dashicons-no-alt"></button>
			</div>
			<div class="form_footer center">
				<input type="hidden" name="order_item_id" value="{{ data.order_item_id }}" />
				<input type="hidden" name="action" value="{{ data.action }}">
				<button type="reset" class="button hb_modal_close"><?php _e( 'No', 'tp-hotel-booking' ) ?></button>
				<button type="submit" class="button button-primary hb_form_submit"><?php _e( 'Yes', 'tp-hotel-booking' ); ?></button>
			</div>
		</form>
	</div>
	<div class="hb_modal_overlay"></div>
</script>
<!--Confirm-->

<!--Qty-->
<script type="text/html" id="tmpl-hb-qty">
	<# if ( typeof data.qty !== 'undefined' ) { #>
		<select name="qty">
			<option value="0"><?php _e( 'Quantity' ) ?></option>
			<# for ( var i = 1; i <= data.qty; i++ ) { #>

				<# if ( data.qty_selected == i ) { #>
					<option value="{{ i }}" selected>{{ i }}</option>
				<# } else { #>
					<option value="{{ i }}">{{ i }}</option>
				<# } #>

			<# } #>
		</select>
	<# } #>
</script>
<!--Qty-->
