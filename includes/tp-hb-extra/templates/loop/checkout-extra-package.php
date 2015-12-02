<?php

/**
 * template extra cart
 */

?>

<tr class="hb_checkout_item package" data-time-key="<?php echo esc_attr( $room->search_key ) ?>" data-package-id="<?php echo esc_attr( $package->ID ); ?>" data-room-id="<?php echo esc_attr( $room->ID ); ?>">

	<td colspan="2"><?php printf( '%s', $package->title ) ?></td>

	<td>
		<?php printf( '%s', $package->quantity ) ?>
	</td>

	<td colspan="2">
		<?php //printf( '%s', $package->description ); ?>
	</td>

	<td class="hb_gross_total">
		<?php echo hb_format_price( $package->price ) ?>
	</td>

</tr>