<?php
/**<?php
 * /**
 * The template for displaying extra package in cart page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking-extra/loop/cart-extra-package.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Extra/Templates
 * @version 1.9.7.4
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<tr class="hb_checkout_item package" data-cart-id="<?php echo esc_attr( $cart_id ); ?>"
	data-parent-id="<?php echo esc_attr( $package->parent_id ); ?>">

	<td colspan="<?php echo is_hb_cart() ? 2 : 1; ?>">
		<?php if ( is_hb_cart() && ! get_post_meta( $package->product_id, 'tp_hb_extra_room_required' ) ) { ?>
			<a href="#" class="hb_package_remove" data-cart-id="<?php echo esc_attr( $cart_id ); ?>"
			   data-parent-id="<?php echo esc_attr( $package->parent_id ); ?>"><i class="fa fa-times"></i></a>
		<?php } ?>
	</td>

	<td>
		<?php printf( '%s', $package->quantity ); ?>
	</td>

	<td colspan="4">
		<?php printf( '%s', apply_filters( 'hb_cart_extra_name', $package->product_data->title, $package->product_id ) ); ?>
	</td>

	<td class="hb_gross_total" style="text-align: center;">
		<?php echo hb_format_price( $package->amount_exclude_tax ); ?>
	</td>
</tr>
