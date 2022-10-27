<?php
/**
 * The template for displaying cart page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/cart/cart.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.9.7.5
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

/**
 * @var $cart WPHB_Cart
 */
$cart = WP_Hotel_Booking::instance()->cart;

global $hb_settings;

?>

<?php if ( $cart->cart_items_count != 0 ) { ?>
	<div id="hotel-booking-cart">

		<form id="hb-cart-form" method="post">

			<h3><?php _e( 'Cart', 'wp-hotel-booking' ); ?></h3>

			<table class="hb_table">
				<thead>
				<tr>
					<th>&nbsp;</th>
					<th class="hb_room_type"><?php _e( 'Room type', 'wp-hotel-booking' ); ?></th>
					<th class="hb_capacity"><?php _e( 'Capacity', 'wp-hotel-booking' ); ?></th>
					<th class="hb_quantity"><?php _e( 'Quantity', 'wp-hotel-booking' ); ?></th>
					<th class="hb_check_in"><?php _e( 'Check - in', 'wp-hotel-booking' ); ?></th>
					<th class="hb_check_out"><?php _e( 'Check - out', 'wp-hotel-booking' ); ?></th>
					<th class="hb_night"><?php _e( 'Night', 'wp-hotel-booking' ); ?></th>
					<th class="hb_deposit"><?php _e( 'Deposit Payment', 'wp-hotel-booking' ); ?></th>
					<th class="hb_gross_total"><?php _e( 'Gross Total', 'wp-hotel-booking' ); ?></th>
				</tr>
				</thead>
				<?php if ( $rooms = $cart->get_rooms() ) { ?>
					<?php foreach ( $rooms as $cart_id => $room ) { ?>
						<?php
						/**
						 * @var $room WPHB_Room
						 */
						$enable = get_post_meta( $room->ID, '_hb_enable_deposit', true );
						if ( ( $num_of_rooms = (int) $room->get_data( 'quantity' ) ) == 0 ) {
							continue;
						}
						$cart_extra = $cart->get_extra_packages( $cart_id );
						// check deposit each room
						$type_deposit = get_post_meta( $room->ID, '_hb_deposit_type', true );
						if ( $type_deposit == 'percent' ) {
							$deposit = get_post_meta( $room->ID, '_hb_deposit_amount', true ) . '%';
						} elseif ( $type_deposit == 'fixed' ) {
							$deposit = hb_format_price( get_post_meta( $room->ID, '_hb_deposit_amount', true ) );
						}

						?>

						<tr class="hb_checkout_item" data-cart-id="<?php echo esc_attr( $cart_id ); ?>">
							<td<?php echo defined( 'WPHB_EXTRA_FILE' ) && $cart_extra ? ' rowspan="' . ( count( $cart_extra ) + 2 ) . '"' : ''; ?>>
								<a href="javascript:void(0)" class="hb_remove_cart_item"
								   data-cart-id="<?php echo esc_attr( $cart_id ); ?>">
									<i class="fa fa-times"></i>
								</a>
							</td>
							<td class="hb_room_type">
								<a href="<?php echo get_permalink( $room->ID ); ?>"><?php echo apply_filters( 'hb_cart_room_name', $room->name, $room->ID ); ?><?php // printf( '%s', $room->capacity_title ? ' (' . $room->capacity_title . ')' : '' ); ?></a>
							</td>
							<td class="hb_capacity">
								<span><?php echo esc_html( sprintf( _n( '%d adult', '%d adults', $room->capacity, 'wp-hotel-booking' ), $room->capacity ) ); ?></span>
								<span><?php echo esc_html( sprintf( _n( '%d child', '%d child', $room->max_child, 'wp-hotel-booking' ), $room->max_child ) ); ?></span>
							</td>
							<td class="hb_quantity">
								<p><?php echo esc_html( $num_of_rooms ); ?></p>
							</td>
							<td class="hb_check_in"><?php echo date_i18n( hb_get_date_format(), strtotime( $room->get_data( 'check_in_date' ) ) ); ?></td>
							<td class="hb_check_out"><?php echo date_i18n( hb_get_date_format(), strtotime( $room->get_data( 'check_out_date' ) ) ); ?></td>
							<td class="hb_night"><?php echo hb_count_nights_two_dates( $room->get_data( 'check_out_date' ), $room->get_data( 'check_in_date' ) ); ?></td>
							<td class="hb_deposit"><?php echo $enable == 1 ? $deposit : __( 'Disable', 'wp-hotel-booking' ); ?></td>
							<td class="hb_gross_total">
								<?php echo hb_format_price( $room->total ); ?>
							</td>
						</tr>
						<?php do_action( 'hotel_booking_cart_after_item', $room, $cart_id ); ?>
					<?php } ?>

				<?php } ?>

				<?php do_action( 'hotel_booking_before_cart_total' ); ?>

				<tr class="hb_sub_total">
					<td colspan="9"><?php _e( 'Sub Total', 'wp-hotel-booking' ); ?>
						<span class="hb-align-right hb_sub_total_value">
								<?php echo hb_format_price( $cart->sub_total ); ?>
							</span>
					</td>
				</tr>
				<?php if ( $tax = hb_get_tax_settings() ) { ?>
					<tr class="hb_advance_tax">
						<td colspan="9">
							<?php _e( 'Tax', 'wp-hotel-booking' ); ?>
							<?php if ( $tax < 0 ) { ?>
								<span><?php printf( __( '(price including tax)', 'wp-hotel-booking' ) ); ?></span>
							<?php } ?>
							<span class="hb-align-right"><?php echo apply_filters( 'hotel_booking_cart_tax_display', abs( $tax * 100 ) . '%' ); ?></span>
						</td>
					</tr>
				<?php } ?>
				<tr class="hb_advance_grand_total">
					<td colspan="9">
						<?php _e( 'Grand Total', 'wp-hotel-booking' ); ?>
						<span class="hb-align-right hb_grand_total_value"><?php echo hb_format_price( $cart->total ); ?></span>
					</td>
				</tr>
					<?php $advance_payment = ''; ?>
					<?php if ( $advance_payment = $cart->advance_payment ) : ?>
						<tr class="hb_advance_payment">
							<td colspan="9">
								<?php printf( __( 'Advance Payment', 'wp-hotel-booking' ) ); ?>
								<span class="hb-align-right hb_advance_payment_value"><?php echo hb_format_price( $advance_payment ); ?></span>
							</td>
						</tr>
					<?php endif; ?>
				<tr>
					<?php wp_nonce_field( 'hb_cart_field', 'hb_cart_field' ); ?>
				</tr>
			</table>
			<p>
				<a href="<?php echo hb_get_checkout_url(); ?>"
				   class="hb_button hb_checkout"><?php _e( 'Check Out', 'wp-hotel-booking' ); ?></a>
			</p>
		</form>
	</div>

<?php } else { ?>

	<div class="hb-message message">
		<div class="hb-message-content">
			<?php _e( 'Your cart is empty!', 'wp-hotel-booking' ); ?>
		</div>
	</div>

<?php } ?>
