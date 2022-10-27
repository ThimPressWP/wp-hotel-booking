<?php
/**
 * The template for displaying additional information in checkout page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/checkout/addition-information.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit; ?>

<div class="hb-addition-information">
	<div class="hb-col-padding hb-col-border">
		<h4><?php _e( 'Additional Information', 'wp-hotel-booking' ); ?></h4>
		<textarea name="addition_information"></textarea>
	</div>
</div>
