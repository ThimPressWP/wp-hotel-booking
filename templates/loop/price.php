<?php
/**
 * The template for displaying loop room price in archive room page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/loop/price.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 2.3.5
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

global $hb_settings;
/**
 * @var $hb_settings WPHB_Settings
 */
$price_display  = apply_filters( 'hotel_booking_loop_room_price_display_style', $hb_settings->get( 'price_display' ) );
$check_in_date  = hb_get_request( 'check_in_date' );
$check_out_date = hb_get_request( 'check_out_date' );
$pricings       = [];

if ( $check_in_date && $check_out_date ) {
	$room = \WPHB_Room::instance(
		get_the_ID(),
		array(
			'check_in_date'  => $check_in_date,
			'check_out_date' => $check_out_date,
		)
	);
	if ( ! $room ) {
		return;
	}
	$booking_room_details = $room->get_booking_room_details();
	if ( ! empty( $booking_room_details ) ) {
		foreach ( $booking_room_details as $day_on_week => $day ) {
			$count      = ! empty( $day['count'] ) ? (int) $day['count'] : 1;
			$pricings[] = $day['price'] / $count;
		}
	}
} else {
	$plan_prices = hb_room_get_selected_plan( get_the_ID() );
	$prices      = $plan_prices && isset( $plan_prices->prices ) ? $plan_prices->prices : array();
	if ( ! empty( $prices ) ) {
		$min_price  = is_numeric( min( $prices ) ) ? (float) min( $prices ) : 0;
		$max_price  = is_numeric( max( $prices ) ) ? (float) max( $prices ) : 0;
		$tax_rate   = hb_price_including_tax() ? hb_get_tax_settings() : 0;
		$pricings[] = $min_price + ( $min_price * $tax_rate );
		$pricings[] = $max_price + ( $max_price * $tax_rate );
	}
}

if ( ! empty( $pricings ) ) {
	$min = min( $pricings );
	$max = max( $pricings );
	?>

	<div class="price">
		<span class="title-price"><?php esc_html_e( 'Price from', 'wp-hotel-booking' ); ?></span>

		<?php if ( $price_display === 'max' ) { ?>
			<span class="price_value price_max"><?php echo wp_kses_post( hb_format_price( $max ) ); ?></span>

		<?php } elseif ( $price_display === 'min_to_max' && $min !== $max ) { ?>
			<span class="price_value price_min_to_max">
				<?php echo wp_kses_post( hb_format_price( $min ) ); ?> - <?php echo wp_kses_post( hb_format_price( $max ) ); ?>
			</span>

		<?php } else { ?>
			<span class="price_value price_min"><?php echo wp_kses_post( hb_format_price( $min ) ); ?></span>
		<?php } ?>

		<span class="unit"><?php esc_html_e( 'night', 'wp-hotel-booking' ); ?></span>

		<?php if( hb_price_including_tax() ) : ?>
			<span class="tax-text"><?php esc_html_e( '(incl tax)', 'wp-hotel-booking' ); ?></span>
		<?php endif; ?>
	</div>
<?php } ?>
