<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-06 16:40:46
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-08 13:27:40
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>

<!--Template JS-->
<!--Add new or edit oder item-->
<script type="text/html" id="tmpl-hb-add-room">
	<div class="hb_modal">
		<form name="booking-room-item" class="booking-room-item">
			<div class="form_head">
				<h1>
					<# if ( typeof data.modal_title !== 'undefined' ) { #>

						{{{ data.modal_title }}}

					<# } else { #>

						<?php _e( 'Add new item', 'wp-hotel-booking' ) ?>

					<# } #>
				</h1>
				<button class="hb_modal_close dashicons dashicons-no-alt"></button>
			</div>

			<div class="section_line">
				<# if ( typeof data.post_type === 'undefined' || data.post_type === 'hb_room' ) { #>
					<div class="section">
						<select name="product_id" class="booking_search_room_items">
							<# if ( typeof data.room !== 'undefined' ) { #>

								<option value="{{ data.room.ID }}" selected>{{ data.room.post_title }}</option>

							<# } #>
						</select>
					</div>
					<div class="section">
						<input type="text" name="check_in_date" class="check_in_date" value="{{ data.check_in_date }}" placeholder="<?php esc_attr_e( 'Check in', 'wp-hotel-booking' ); ?>" />
						<input type="hidden" name="check_in_date_timestamp" value="{{ data.check_in_date_timestamp }}" />
						<input type="text" name="check_out_date" class="check_out_date" value="{{ data.check_out_date }}" placeholder="<?php esc_attr_e( 'Check out', 'wp-hotel-booking' ); ?>" />
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

			<# if ( typeof data.sub_items !== 'undefined' ) { #>

				<div class="section_line">
					<h4><?php _e( 'Extra Packages', 'wp-hotel-booking' ); ?></h4>
					<ul>
						<# var sub_items_length = data.sub_items.length; #>
						<# for ( var i = 0; i < sub_items_length; i++ ) { #>
							<# var item = data.sub_items[i]; #>
							<li>
								<div class="section">
									<label>
										<# if ( item.selected === true ) { #>
											<input type="checkbox" name="sub_items[{{ item.ID }}][checked]" checked />
											<input type="hidden" name="sub_items[{{ item.ID }}][order_item_id]" value="{{ item.order_item_id }}" />
										<# } else { #>
											<input type="checkbox" name="sub_items[{{ item.ID }}][checked]" />
										<# } #>
										{{ item.title }}
									</label>
								</div>
								<# if ( item.respondent === 'number' ) { #>
									<div class="section">
										<?php _e( 'Quantity', 'wp-hotel-booking' ); ?>
										<input name="sub_items[{{ item.ID }}][qty]" type="number" step="1" min="0" value="{{ item.qty }}" />
									</div>
								<# } else { #>
									<input name="sub_items[{{ item.ID }}][qty]" type="hidden" value="{{ item.qty }}" />
								<# } #>
							</li>
						<# } #>
					</ul>

				</div>

			<# } #>

			<div class="form_footer">
				<?php wp_nonce_field( 'hotel_admin_check_room_available', 'hotel-admin-check-room-available' ); ?>
				<input type="hidden" name="order_id" value="{{ data.order_id }}" />
				<input type="hidden" name="order_item_id" value="{{ data.order_item_id }}" />
				<# if ( typeof data.post_type === 'undefined' || data.post_type === 'hb_room' ) { #>
					<a href="#" class="button check_available{{ data.class }}"><?php _e( 'Check Available', 'wp-hotel-booking' ); ?></a>
				<# } #>
				<input type="hidden" name="order_item_type" value="{{ data.order_item_type }}" />
				<input type="hidden" name="action" value="hotel_booking_admin_add_order_item" />
				<button type="reset" class="button hb_modal_close"><?php _e( 'Close', 'wp-hotel-booking' ) ?></button>
				<button type="submit" class="button button-primary hb_form_submit"><?php _e( 'Add', 'wp-hotel-booking' ); ?></button>
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
					<# if ( data.message ) { #>
						{{{ data.message }}}
					<# } else { #>
						<?php _e( 'Do you want to do this?', 'wp-hotel-booking' ); ?>
					<# } #>
				</h1>
				<button class="hb_modal_close dashicons dashicons-no-alt"></button>
			</div>
			<div class="form_footer center">
				<input type="hidden" name="order_id" value="{{ data.order_id }}" />
				<# if ( typeof data.order_item_id === 'object' ) { #>
					<# for( var i = 0; i < Object.keys( data.order_item_id ).length; i++ ) { #>

						<input type="hidden" name="order_item_id[]" value="{{ data.order_item_id[i] }}" />

					<# } #>
				<# } else { #>
					<input type="hidden" name="order_item_id" value="{{ data.order_item_id }}" />
				<# } #>
				<input type="hidden" name="action" value="{{ data.action }}">
				<input type="hidden" name="coupon_id" value="{{ data.coupon_id }}" />
				<?php wp_nonce_field( 'hotel-booking-confirm', 'hotel_booking_confirm' ); ?>
				<button type="reset" class="button hb_modal_close"><?php _e( 'No', 'wp-hotel-booking' ) ?></button>
				<button type="submit" class="button button-primary hb_form_submit"><?php _e( 'Yes', 'wp-hotel-booking' ); ?></button>
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

<!--Coupons-->
<script type="text/html" id="tmpl-hb-coupons">
	<div class="hb_modal">
		<form name="booking-room-item" class="booking-room-item">
			<div class="form_head">
				<h1>
					<# if ( typeof data.coupon_code !== 'undefined' ) { #>

						{{{ data.coupon_code }}}

					<# } else { #>

						<?php _e( 'Add new coupon', 'wp-hotel-booking' ) ?>

					<# } #>
				</h1>
				<button class="hb_modal_close dashicons dashicons-no-alt"></button>
			</div>

			<div class="section_line">
				<# if ( typeof data.post_type === 'undefined' || data.post_type === 'hb_room' ) { #>
					<div class="section">
						<select name="coupon_id" class="booking_coupon_code">
							<# if ( typeof data.room !== 'undefined' ) { #>

								<option value="{{ data.room.ID }}" selected>{{ data.room.post_title }}</option>

							<# } #>
						</select>
					</div>
				<# } #>
			</div>

			<# if ( typeof data.extras !== 'undefined' && Object.keys( data.extras ).length() != 0 ) { #>

				<div class="section_line">

					<# console.debug( data.extras ) #>

				</div>

			<# } #>

			<div class="form_footer">
				<?php wp_nonce_field( 'hotel_admin_get_coupon_available', 'hotel-admin-get-coupon-available' ); ?>
				<input type="hidden" name="order_id" value="{{ data.order_id }}" />
				<!-- <input type="hidden" name="coupon_id" value="{{ data.coupon_id }}" /> -->
				<input type="hidden" name="action" value="hotel_booking_add_coupon_to_order" />
				<button type="reset" class="button hb_modal_close"><?php _e( 'Close', 'wp-hotel-booking' ) ?></button>
				<button type="submit" class="button button-primary hb_form_submit"><?php _e( 'Add', 'wp-hotel-booking' ); ?></button>
			</div>
		</form>
	</div>
	<div class="hb_modal_overlay"></div>
</script>
<!--Coupons-->