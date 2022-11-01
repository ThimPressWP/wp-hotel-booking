<?php
/**
 * The template for displaying select room extra after select room.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/search/select-extra.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.9.5
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

$cart_id = hb_get_request( 'cart_id' );
$room_id = hb_get_request( 'room_id' );

$extra_product = HB_Room_Extra::instance( $room_id );
$room_extra    = $extra_product->get_extra();

$cart      = WPHB_Cart::instance();
$cart_item = $cart->get_cart_item( $cart_id ); ?>

<form class="hb-select-extra-results" name="hb-select-extra-results">
	<?php if ( $room_extra ) { ?>
		<?php do_action( 'hotel_booking_before_select_extra', $room_id ); ?>
		<ul class="list-room-extra">
			<?php foreach ( $room_extra as $key => $extra ) { ?>
				<li data-price="<?php echo esc_attr( $extra->amount_singular ); ?>">
					<input type="<?php echo $extra->required ? 'hidden' : 'checkbox'; ?>"
						   name="hb_optional_quantity_selected[<?php echo esc_attr( $extra->ID ); ?>]"
						   class="hb_optional_quantity_selected"
						   id="<?php echo esc_attr( 'hb-ex-room-' . $extra->ID . '-' . $key ) ?>" <?php echo $extra->required ? 'checked="checked" value="on"' : ''; ?>
						   data-id = "<?php echo esc_attr( $extra->ID ); ?>"
					/>
					<div class="hb_package_title">
						<label
							for="<?php echo esc_attr( 'hb-ex-room-' . $extra->ID . '-' . $key ) ?>"><?php printf( '%s', $extra->title ) ?></label>
						<div class="hb_extra_detail_price">
							<?php //if ( ! $extra->required ) { ?>
							<?php if ( $extra->respondent === 'number' ) { ?>
								<input type="number" step="1" min="1"
									   name="hb_optional_quantity[<?php echo esc_attr( $extra->ID ); ?>]"
									   value="1" class="hb_optional_quantity"/>
							<?php } else { ?>
								<input type="hidden" step="1" min="1"
									   name="hb_optional_quantity[<?php echo esc_attr( $extra->ID ); ?>]"
									   value="1"/>
							<?php } ?>
							<?php //} ?>
							<label>
								<strong><?php echo $extra->price; ?></strong>
								<small><?php printf( '/ %s', $extra->respondent_name ? $extra->respondent_name : __( 'Package', 'wp-hotel-booking' ) ) ?></small>
							</label>
						</div>
					</div>
					<p class="description"><?php printf( '%s', $extra->description ) ?></p>
				</li>
			<?php } ?>
		</ul>
		<input type="hidden" name="action" value="hotel_booking_add_extra_to_cart"/>
		<input type="hidden" name="cart_id" value="<?php echo esc_attr( $cart_id ); ?>"/>
		<?php wp_nonce_field( 'hb_select_extra_nonce_action', 'nonce' ); ?>
		<?php do_action( 'hotel_booking_after_select_extra', $room_id ); ?>
		<a href="javascript:history.go(-1)"
		   class="hb_button hb_button_secondary"><?php _e( 'Back to search', 'wp-hotel-booking' ); ?></a>

		<button type="submit" class="hb_button"><?php _e( 'Next step', 'wp-hotel-booking' ); ?></button>
	<?php } else {
		?>
		<p><?php _e( 'There is no extra option of this room', 'wp-hotel-booking' ); ?></p>

		<a href="javascript:history.go(-1)"
		   class="hb_button hb_button_secondary"><?php _e( 'Back to search', 'wp-hotel-booking' ); ?></a>
	<?php } ?>
</form>