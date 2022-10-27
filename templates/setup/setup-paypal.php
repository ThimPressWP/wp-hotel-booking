<?php
/**
 * Template for displaying paypal settings of setup wizard.
 *
 * @author  ThimPres
 * @version 2.0.0
 */

defined( 'ABSPATH' ) or exit;

$settings = hb_settings()->get( 'tp_hotel_booking_paypal', '' );
?>
<table>
	<tr>
		<th><?php _e( 'Paypal Email', 'wp-hotel-booking' ); ?></th>
		<td>
			<input class="regular-text" type="email" name="settings[paypal][email]" id="settings-paypal-email" value="<?php echo $settings['email'] ?? ''; ?>">
			<p class="description">
				<?php _e( 'Your Paypal email in live mode.', 'wp-hotel-booking' ); ?>
			</p>
			<input type="hidden" name="settings[paypal][enable]" value="on"/>
		</td>
	</tr>

	<tr>
		<th><?php _e( 'Currency', 'wp-hotel-booking' ); ?></th>
		<td>
			<select id="currency" name="settings[currency][currency]" class="wphb-select-2">
			<?php
				$payment_currencies = hb_payment_currencies();
			if ( $payment_currencies ) {
				foreach ( $payment_currencies as $code => $symbol ) {
					?>
						<option value="<?php echo $code; ?>" data-symbol="<?php echo hb_get_currency_symbol( $code ); ?>" <?php selected( $code == 'USD' ); ?>><?php echo $symbol; ?></option>
					<?php
				}
			}
			?>
			</select>
		</td>
	</tr>
</table>

<input type="hidden" name="settings[currency][price_currency_position]" value="left"/>
<input type="hidden" name="settings[currency][price_thousands_separator]" value=","/>
<input type="hidden" name="settings[currency][price_decimals_separator	]" value="."/>
<input type="hidden" name="settings[currency][price_number_of_decimal]" value="2"/>

<script>
	jQuery(function ($) {
		$('#settings-paypal-email').focus();
	})
</script>
