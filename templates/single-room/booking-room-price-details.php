<?php
/**
 * The template for displaying search room booking room details.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/search/booking-room-details.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
if ( ! $room || ! $room->ID ) {
	return;
}

$room_id        = $room->ID;
$include_tax    = hb_price_including_tax() ? (float) WPHB_Settings::instance()->get( 'tax' ) : 0;
$extra_pricing  = array();
$check_in_date  = $room->get_data( 'check_in_date' );
$check_out_date = $room->get_data( 'check_out_date' );
$quantity       = $room->get_data( 'quantity' );

$total_extra_price = 0;
if ( ! empty( $extra_info) ) {
	foreach ( $extra_info as $extra_id => $extra ) {
		$extra_package = hotel_booking_get_product_class( $extra_id,
			array(
				'product_id'     => $extra_id,
				'check_in_date'  => $check_in_date,
				'check_out_date' => $check_out_date,
				'quantity'       => $extra['quantity'],
			)
		);

		$extra_price     = $extra_package ? $extra_package->get_price_package() : 0;
		$extra_pricing[] = array(
			'title' => get_the_title( $extra_id ),
			'price' => $extra_price,
		);
		$total_extra_price += $extra_price;
	}
}
?>

<div class="hb-booking-room-details active">
	<span class="hb_search_room_item_detail_price_close">
		<i class="fa fa-times"></i>
	</span>

	<?php $details = $room->get_booking_room_details(); ?>
	<table class="hb_search_room_pricing_price">
		<tbody>
		<?php foreach ( $details as $day => $info ) { ?>
			<tr>
				<td class="hb_search_item_day"><?php printf( '%s', hb_date_to_name( $day ) ); ?></td>
				<td class="hb_search_item_total_description">
					<?php printf( 'x%d %s', $info['count'], __( 'Night', 'wp-hotel-booking' ) ); ?>
				</td>
				<td class="hb_search_item_price">
					<?php echo hb_format_price( round( $info['price'] * $quantity, 2 ) ); ?>
				</td>
			</tr>
		<?php } ?>
		<?php if ( ! empty( $extra_pricing ) ): ?>
			<?php foreach ( $extra_pricing as $extra ): ?>
				<tr>
					<td class="hb_search_item_day" colspan="2"><?php echo esc_html( $extra['title'] ); ?></td>
					<td class="hb_search_item_price">
						<?php echo hb_format_price( round( $extra['price'], 2 ) ); ?>
					</td>
				</tr>
			<?php endforeach ?>
		<?php endif; ?>
		</tbody>
		<tfoot>
		<tr>
			<td class="hb_search_item_total_bold">
				<?php _e( 'Total', 'wp-hotel-booking' ); ?>
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
				<?php echo hb_format_price( $room->amount_singular * $quantity + $total_extra_price ); ?>
			</td>
		</tr>
		</tfoot>
	</table>
</div>
