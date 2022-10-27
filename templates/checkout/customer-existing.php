<?php
/**
 * The template for displaying existing customer form in checkout page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/checkout/customer-existing.php.
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
 * @var $cart WPHB_Cart
 */
$cart = WP_Hotel_Booking::instance()->cart;
?>

<?php if ( ! is_user_logged_in() ) { ?>

	<div class="hb-order-existing-customer" data-label="<?php esc_attr_e( '-Or-', 'wp-hotel-booking' ); ?>">
		<div class="hb-col-padding hb-col-border">
			<h4><?php _e( 'Existing customer?', 'wp-hotel-booking' ); ?></h4>
			<ul class="hb-form-table">
				<li class="hb-form-field">
					<label class="hb-form-field-label"><?php _e( 'Email', 'wp-hotel-booking' ); ?></label>
					<div class="hb-form-field-input">
						<input type="email" name="existing-customer-email"
							   value="<?php echo esc_attr( $cart->customer_email ); ?>"
							   placeholder="<?php _e( 'Your email here', 'wp-hotel-booking' ); ?>"/>
					</div>
				</li>
				<li>
					<button type="button" id="fetch-customer-info"><?php _e( 'Apply', 'wp-hotel-booking' ); ?></button>
				</li>
			</ul>
		</div>
	</div>

<?php } ?>
