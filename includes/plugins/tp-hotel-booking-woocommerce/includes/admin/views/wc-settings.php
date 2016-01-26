<?php
/**
 * View for displaying the settings in admin
 *
 * @author  ThimPress
 * @package Views
 * @version 0.9
 */
?>
<h3><?php _e( 'WooCommerce', 'hb-woocommerce'); ?></h3>
<p class="description"><?php _e( 'Settings for WooCommerce addon','hb-woocommerce' ); ?></p>
<table class="form-table">
	<tr>
		<th><?php _e( 'Enable', 'hb-woocommerce' ); ?></th>
		<td>
			<input type="hidden" name="<?php echo $this->get_field_name( 'wc_enable' ); ?>" value="no" />
			<label>
				<input type="checkbox" name="<?php echo $this->get_field_name( 'wc_enable' ); ?>" <?php checked( HB_Settings::instance()->get( 'wc_enable' ) == 'yes' ); ?> value="yes" />
			</label>
			<p class="description"><?php _e( 'Check this option to enable make booking payments via WooCommerce', 'hb-woocommerce' ); ?></p>
		</td>
	</tr>
</table>