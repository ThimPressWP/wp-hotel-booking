<?php
/**
 * The template for displaying loop room price in archive room page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/loop/price.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
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
$datetime 		= new \DateTime('NOW');
$tomorrow 		= new \DateTime('tomorrow');
$format 		= get_option('date_format');
$check_in_date  = hb_get_request( 'check_in_date', $datetime->format($format));
$check_out_date = hb_get_request( 'check_out_date', $tomorrow->format($format));

$room = \WPHB_Room::instance(
    get_the_ID(),
    array(
        'check_in_date'  => $check_in_date,
        'check_out_date' => $check_out_date,
    )
);
error_log($check_in_date);
if ( ! $room ) {
	return;
}
$booking_room_details = $room->get_booking_room_details();
$pricings             = [];
if ( ! empty( $booking_room_details ) ) {
	foreach ( $booking_room_details as $day_on_week => $day ) {
		$pricings[] = $day['price'];
	}
}
if ( ! empty( $pricings ) ) {
	$min = min( $pricings );
	$max = max( $pricings );
	?>

	<div class="price">
		<span class="title-price"><?php _e( 'Price from', 'wp-hotel-booking' ); ?></span>

		<?php if ( $price_display === 'max' ) { ?>
			<span class="price_value price_max"><?php echo hb_format_price( $max ); ?></span>

		<?php } elseif ( $price_display === 'min_to_max' && $min !== $max ) { ?>
			<span class="price_value price_min_to_max">
				<?php echo hb_format_price( $min ); ?> - <?php echo hb_format_price( $max ); ?>
			</span>

		<?php } else { ?>
			<span class="price_value price_min"><?php echo hb_format_price( $min ); ?></span>
		<?php } ?>

		<span class="unit"><?php _e( 'Night', 'wp-hotel-booking' ); ?></span>
	</div>
<?php } ?>
