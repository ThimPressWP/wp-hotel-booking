<?php
/**
 * View for displaying the settings in admin
 *
 * @author  ThimPress
 * @package Views
 * @version 1.1.4
 */
?>
<h3><?php _e( 'WooCommerce', 'wp-hotel-booking-woocommerce');?></h3>
<p class="description"><?php _e( 'Settings for WooCommerce addon', 'wp-hotel-booking-woocommerce' );?></p>
<table class="form-table">
	<tr>
		<th><?php _e( 'Enable', 'wp-hotel-booking-woocommerce' );?></th>
		<td>
			<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'wc_enable' ) ); ?>" value="no" />
			<label>
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'wc_enable' ) ); ?>" <?php checked( HB_Settings::instance()->get( 'wc_enable' ) == 'yes' );?> value="yes" />
			</label>
			<p class="description"><?php _e( 'Check this option to enable make booking payments via WooCommerce', 'wp-hotel-booking-woocommerce' );?></p>
		</td>
	</tr>
</table>