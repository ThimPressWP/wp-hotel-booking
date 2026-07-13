<?php

/**
 * The template for displaying confirm actions.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/confirm.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit(); ?>

<div id="hotel-booking-confirm">
	<?php esc_html_e( 'Confirm', 'wp-hotel-booking' ); ?>
	<form name="hb-search-form">
		<input type="hidden" name="hotel-booking" value="complete">
		<p>
			<button type="submit"><?php esc_html_e( 'Finish', 'wp-hotel-booking' ); ?></button>
		</p>
	</form>
</div>
