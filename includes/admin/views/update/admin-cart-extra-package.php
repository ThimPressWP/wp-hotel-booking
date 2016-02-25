<?php
/**
 * template extra admin cart
 * @since  1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<tr class="hb_checkout_item package booking-table-row">

	<td colspan="1"></td>

	<td colspan="1" style="text-align: center">
		<?php echo esc_html( $package->quantity ); ?>
	</td>

	<td colspan="3" class="hb_table_center" style="text-align: center">
		<?php printf( '%s', $package->product_data->title ) ?>
	</td>

	<td class="hb_gross_total" colspan="1">
		<?php printf( '%s', hb_format_price( $package->amount_singular_exclude_tax, hb_get_currency_symbol( $booking->currency ) ) ) ?>
	</td>

</tr>
