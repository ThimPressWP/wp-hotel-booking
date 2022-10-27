<?php
/**
 * The template for displaying admin new booking email.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/emails/admin/admin-new-booking.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

/**
 * @var $email_heading
 * @var $email_heading_desc
 */

// email heading
hb_get_template(
	'emails/email-header.php',
	array(
		'email_heading'      => $email_heading,
		'email_heading_desc' => $email_heading_desc,
	)
);

// booking details
hb_get_template(
	'emails/booking-details.php',
	array(
		'booking' => $booking,
		'options' => $options,
	)
);

// customer details
hb_get_template(
	'emails/customer-details.php',
	array(
		'booking' => $booking,
		'options' => $options,
	)
);

// email footer
hb_get_template(
	'emails/email-footer.php',
	array(
		'booking' => $booking,
		'options' => $options,
	)
);
