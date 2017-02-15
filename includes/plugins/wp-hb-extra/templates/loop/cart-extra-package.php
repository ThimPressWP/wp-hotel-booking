<?php
/**
 * template extra cart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>

<tr class="hb_checkout_item package" data-cart-id="<?php echo esc_attr( $cart_id ) ?>" data-parent-id="<?php echo esc_attr( $package->parent_id ) ?>">

	<td colspan="<?php echo is_hb_cart() ? 2 : 1 ?>">
		<?php if( is_hb_cart() ): ?>
			<a href="#" class="hb_package_remove" data-cart-id="<?php echo esc_attr( $cart_id ) ?>" data-parent-id="<?php echo esc_attr( $package->parent_id ) ?>"><i class="fa fa-times"></i></a>
		<?php endif; ?>
	</td>

	<td>
		<?php if ( is_hb_cart() ) : ?>
			<?php if( $input = apply_filters( 'hb_extra_cart_input', $package->product_data->respondent ) ): ?>
				<input type="number" min="1" value="<?php echo esc_attr( $package->quantity ); ?>" name="hotel_booking_cart[<?php echo esc_attr( $cart_id ); ?>]"/>
			<?php else: ?>
				<?php printf( '%s', $package->quantity ) ?>
				<input type="hidden" value="<?php echo esc_attr( $package->quantity ); ?>" name="hotel_booking_cart[<?php echo esc_attr( $cart_id ); ?>]"/>
			<?php endif; ?>
		<?php else: ?>
			<?php printf( '%s', $package->quantity ) ?>
		<?php endif; ?>
	</td>

	<td colspan="3">
		<?php printf( '%s', $package->product_data->title ) ?>
	</td>

	<td class="hb_gross_total" style="text-align: center;">
		<?php echo hb_format_price( $package->amount_exclude_tax ) ?>
	</td>

</tr>
