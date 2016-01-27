<?php
/**
 * template extra admin cart
 * @since  1.1
 */

?>

<tr class="hb_checkout_item package">

	<td colspan="4"></td>

	<td colspan="4">
		<?php echo $package->quantity; ?>
	</td>

	<td colspan="12" class="hb_table_center">
		<?php printf( '%s', $package->product_data->title ) ?>
	</td>

	<td class="hb_gross_total" colspan="4">
		<?php echo hb_format_price( $package->amount_singular_exclude_tax, hb_get_currency_symbol( $booking->currency ) ) ?>
	</td>

</tr>
