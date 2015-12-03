<?php

/**
 * template extra cart
 */

?>

<tr class="hb_checkout_item package" data-time-key="<?php echo esc_attr( $room->search_key ) ?>" data-package-id="<?php echo esc_attr( $package->ID ); ?>" data-room-id="<?php echo esc_attr( $room->ID ); ?>">

	<td colspan="<?php echo is_hb_cart() ? 2 : 1 ?>">
		<?php if( is_hb_cart() ): ?>
			<a href="#" class="hb_package_remove" data-package="<?php echo esc_attr( $package->ID ) ?>"><i class="fa fa-times"></i></a>
		<?php endif; ?>
	</td>

	<td>
		<?php if( $input = apply_filters( 'tp_hb_extra_cart_input', $package->respondent ) ): ?>
			<input type="number" min="1" value="<?php echo esc_attr( $package->quantity ); ?>" name="hotel_booking_cart_package[<?php echo esc_attr( $room->search_key ); ?>][<?php echo esc_attr( $room->ID ); ?>][<?php echo esc_attr( $package->ID ); ?>]"/>
		<?php else: ?>
			<?php printf( '%s', $package->quantity ) ?>
		<?php endif; ?>

	</td>

	<td colspan="3">
		<?php printf( '%s', $package->title ) ?>
	</td>

	<td class="hb_gross_total">
		<?php echo hb_format_price( $package->price ) ?>
	</td>

</tr>