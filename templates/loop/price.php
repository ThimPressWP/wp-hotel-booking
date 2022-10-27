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
$price_display = apply_filters( 'hotel_booking_loop_room_price_display_style', $hb_settings->get( 'price_display' ) );
$prices        = hb_room_get_selected_plan( get_the_ID() );
$prices        = isset( $prices->prices ) ? $prices->prices : array();
?>

<?php
if ( $prices ) {
	$min_price = is_numeric( min( $prices ) ) ? min( $prices ) : 0;
	$max_price = is_numeric( max( $prices ) ) ? max( $prices ) : 0;
	$min       = $min_price + ( hb_price_including_tax() ? ( $min_price * hb_get_tax_settings() ) : 0 );
	$max       = $max_price + ( hb_price_including_tax() ? ( $max_price * hb_get_tax_settings() ) : 0 );
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
