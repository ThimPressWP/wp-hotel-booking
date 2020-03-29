<?php
/**
 * The template for displaying cancelled booking mail.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/emails/customer-cancelled.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.9
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

// email heading
hb_get_template( 'emails/email-header.php',
	array(
		'email_heading'      => __( 'Cancelled booking', 'wp-hotel-booking' ),
		'email_heading_desc' => __( 'Booking has been marked cancelled', 'wp-hotel-booking' )
	)
);

// booking items
hb_get_template( 'emails/booking-details.php', array( 'booking' => $booking, 'options' => $options ) );

// customer details
hb_get_template( 'emails/customer-details.php', array( 'booking' => $booking, 'options' => $options ) );

// email footer
hb_get_template( 'emails/email-footer.php', array( 'booking' => $booking, 'options' => $options ) );
