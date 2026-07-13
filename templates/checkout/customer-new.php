<?php
/**
 * The template for displaying new customer form in checkout page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/checkout/customer-new.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

/**
 * @var $customer
 */
?>

<div class="hb-order-new-customer" id="hb-order-new-customer">
	<div class="hb-col-padding hb-col-border">
		<h4><?php esc_html_e( 'New Customer', 'wp-hotel-booking' ); ?></h4>
		<ul class="hb-form-table col-2">
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php esc_html_e( 'Title', 'wp-hotel-booking' ); ?>
					<span class="hb-required">*</span> </label>

				<div class="hb-form-field-input">
					<?php
					hb_dropdown_titles(
						array(
							'selected' => $customer->title,
							'required' => true,
						)
					);
					?>
				</div>
			</li>
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php esc_html_e( 'First name', 'wp-hotel-booking' ); ?>
					<span class="hb-required">*</span></label>

				<div class="hb-form-field-input">
					<input type="text" name="first_name" value="<?php echo esc_attr( $customer->first_name ); ?>"
							placeholder="<?php esc_html_e( 'First name', 'wp-hotel-booking' ); ?>" required/>
				</div>
			</li>
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php esc_html_e( 'Last name', 'wp-hotel-booking' ); ?>
					<span class="hb-required">*</span></label>

				<div class="hb-form-field-input">
					<input type="text" name="last_name" value="<?php echo esc_attr( $customer->last_name ); ?>"
							placeholder="<?php esc_html_e( 'Last name', 'wp-hotel-booking' ); ?>" required/>
				</div>
			</li>
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php esc_html_e( 'Address', 'wp-hotel-booking' ); ?>
					<span class="hb-required">*</span></label>

				<div class="hb-form-field-input">
					<input type="text" name="address" value="<?php echo esc_attr( $customer->address ); ?>"
							placeholder="<?php esc_html_e( 'Address', 'wp-hotel-booking' ); ?>" required/>
				</div>
			</li>
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php esc_html_e( 'City', 'wp-hotel-booking' ); ?>
					<span class="hb-required">*</span></label>

				<div class="hb-form-field-input">
					<input type="text" name="city" value="<?php echo esc_attr( $customer->city ); ?>"
							placeholder="<?php esc_html_e( 'City', 'wp-hotel-booking' ); ?>" required/>
				</div>
			</li>
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php esc_html_e( 'State', 'wp-hotel-booking' ); ?>
					<span class="hb-required">*</span></label>

				<div class="hb-form-field-input">
					<input type="text" name="state" value="<?php echo esc_attr( $customer->state ); ?>"
							placeholder="<?php esc_html_e( 'State', 'wp-hotel-booking' ); ?>" required/>
				</div>
			</li>
		</ul>
		<ul class="hb-form-table col-2">
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php esc_html_e( 'Postal Code', 'wp-hotel-booking' ); ?>
					<span class="hb-required">*</span></label>

				<div class="hb-form-field-input">
					<input type="text" name="postal_code" value="<?php echo esc_attr( $customer->postal_code ); ?>"
							placeholder="<?php esc_html_e( 'Postal code', 'wp-hotel-booking' ); ?>" required/>
				</div>
			</li>
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php esc_html_e( 'Country', 'wp-hotel-booking' ); ?>
					<span class="hb-required">*</span></label>

				<div class="hb-form-field-input">
					<?php
					hb_dropdown_countries(
						array(
							'name'             => 'country',
							'show_option_none' => esc_html__( 'Country', 'wp-hotel-booking' ),
							'selected'         => $customer->country,
							'required'         => true,
						)
					);
					?>
				</div>
			</li>
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php esc_html_e( 'Phone', 'wp-hotel-booking' ); ?>
					<span class="hb-required">*</span></label>

				<div class="hb-form-field-input">
					<input type="text" name="phone" value="<?php echo esc_attr( $customer->phone ); ?>"
							placeholder="<?php esc_html_e( 'Phone Number', 'wp-hotel-booking' ); ?>" required/>
				</div>
			</li>
			<li class="hb-form-field">
				<label class="hb-form-field-label"><?php esc_html_e( 'Email', 'wp-hotel-booking' ); ?>
					<span class="hb-required">*</span></label>

				<div class="hb-form-field-input">
					<input type="email" name="email" value="<?php echo esc_attr( $customer->email ); ?>"
							placeholder="<?php esc_html_e( 'Email address', 'wp-hotel-booking' ); ?>" required/>
				</div>
			</li>
		</ul>
		<input type="hidden" name="existing-customer-id" value=""/>
	</div>
</div>
