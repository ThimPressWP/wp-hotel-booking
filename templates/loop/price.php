<?php
/**
 * Loop Price
 *
 * @author 		ThimPress
 * @package 	Tp-hotel-booking/Templates
 * @version     1.1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $hb_settings;
$price_display = apply_filters( 'hotel_booking_loop_room_price_display_style', $hb_settings->get('price_display') );

$prices = hb_room_get_selected_plan( get_the_ID() );
$prices = isset( $prices->prices ) ? $prices->prices : array();

?>
<?php if( $prices ): ?>
	<?php
		$min = current($prices);
		$max = end($prices);
	?>
	<div class="price">
		<span class="title-price"><?php _e( 'Price', 'tp-hotel-booking' ); ?></span>
		<?php if( $price_display === 'max' ): ?>

			<span class="price_value price_max"><?php echo hb_format_price( $max ) ?></span>

		<?php elseif( $price_display === 'min_to_max' && $min !== $max ): ?>

			<span class="price_value price_min_to_max">
				<?php echo hb_format_price( $min ) ?>
				-
				<?php echo hb_format_price( $max ) ?>
			</span>

		<?php else: ?>

			<span class="price_value price_min"><?php echo hb_format_price( $min ) ?></span>

		<?php endif; ?>
		<span class="unit"><?php _e( 'Night', 'tp-hotel-booking' ); ?></span>
	</div>
<?php endif; ?>