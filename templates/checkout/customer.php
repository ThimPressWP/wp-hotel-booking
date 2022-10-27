<?php
/**
 * The template for displaying customer in checkout page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/checkout/customer.php.
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

<h3><?php _e( 'Customer Details', 'wp-hotel-booking' ); ?></h3>

<div class="hb-customer clearfix">
	<?php hb_get_template( 'checkout/customer-existing.php', array( 'customer' => $customer ) ); ?>
	<?php hb_get_template( 'checkout/customer-new.php', array( 'customer' => $customer ) ); ?>
</div>
<div class="hb-col-margin"></div>
