<?php
/**
 * Admin View: Paypal setting view.
 *
 * @version     1.9.7
 * @package     WP_Hotel_Booking/Views
 * @category    Views
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

$settings = WPHB_Settings::instance();
$paypal   = $settings->get( 'paypal' );
$paypal   = wp_parse_args(
	$paypal,
	array(
		'enable'            => 'on',
		'email'             => '',
		'sandbox'           => 'off',
		'sandbox_email'     => '',
		'app_client_id'     => '',
		'app_client_secret' => '',
		'use_paypal_rest'   => 'off',
	)
);

$field_name = $settings->get_field_name( 'paypal' );
?>
<table class="form-table">
	<tr>
		<th><?php _e( 'Enable', 'wp-hotel-booking' ); ?></th>
		<td>
			<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>[enable]" value="off" />
			<input type="checkbox" name="<?php echo esc_attr( $field_name ); ?>[enable]" <?php checked( $paypal['enable'] == 'on' ? 1 : 0, 1 ); ?> value="on" />
		</td>
	</tr>
	<tr>
		<th><?php _e( 'Paypal email', 'wp-hotel-booking' ); ?></th>
		<td>
			<input type="email" class="regular-text" name="<?php echo esc_attr( $field_name ); ?>[email]" value="<?php echo esc_attr( $paypal['email'] ); ?>" />
		</td>
	</tr>
	<tr>
		<th><?php _e( 'Sandbox Mode', 'wp-hotel-booking' ); ?></th>
		<td>
			<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>[sandbox]" value="off" />
			<input type="checkbox" name="<?php echo esc_attr( $field_name ); ?>[sandbox]" <?php checked( $paypal['sandbox'] == 'on' ? 1 : 0, 1 ); ?> value="on" />
		</td>
	</tr>
	<tr>
		<th><?php _e( 'Paypal sandbox email', 'wp-hotel-booking' ); ?></th>
		<td>
			<input type="email" class="regular-text" name="<?php echo esc_attr( $field_name ); ?>[sandbox_email]" value="<?php echo esc_attr( $paypal['sandbox_email'] ); ?>" />
		</td>
	</tr>
	<tr>
		<th><?php _e( 'Use PayPal REST API', 'wp-hotel-booking' ); ?></th>
		<td>
			<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>[use_paypal_rest]" value="off" />
			<input type="checkbox" name="<?php echo esc_attr( $field_name ); ?>[use_paypal_rest]" <?php checked( $paypal['use_paypal_rest'] == 'on' ? 1 : 0, 1 ); ?> value="on" />
			<span class="description"><?php esc_html_e( 'Recommendations! The old standard will not be supported in 2024/01/31.', 'wp-hotel-booking' ); ?></span>
		</td>
	</tr>
	<tr>
		<th><?php _e( 'Client ID', 'wp-hotel-booking' ); ?></th>
		<td>
			<input type="text" class="regular-text" name="<?php echo esc_attr( $field_name ); ?>[app_client_id]" value="<?php echo esc_attr( $paypal['app_client_id'] ); ?>" />
			<p class="description">
			<?php
			echo sprintf(
				__( 'How to get <a href="%s" target="_blank">Client ID</a>', 'learnpress' ),
				'https://developer.paypal.com/api/rest/#link-getclientidandclientsecret'
			)
			?>
			</p>
		</td>
	</tr>
	<tr>
		<th><?php _e( 'Client Secret', 'wp-hotel-booking' ); ?></th>
		<td>
			<input type="text" class="regular-text" name="<?php echo esc_attr( $field_name ); ?>[app_client_secret]" value="<?php echo esc_attr( $paypal['app_client_secret'] ); ?>" />
			<p class="description">
			<?php
			echo sprintf(
				__( 'How to get <a href="%s" target="_blank">Client Secret</a>', 'learnpress' ),
				'https://developer.paypal.com/api/rest/#link-getclientidandclientsecret'
			)
			?>
			</p>
		</td>
	</tr>
</table>

