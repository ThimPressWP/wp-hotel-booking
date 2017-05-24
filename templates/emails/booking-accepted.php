<?php
/*
 * @Author : leehld
 * @Date   : 5/24/2017
 * @Last Modified by: leehld
 * @Last Modified time: 5/24/2017
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

// email heading
hb_get_template( 'emails/email-header.php',
	array(
		'email_heading'      => $email_heading,
		'email_heading_desc' => $email_heading_desc
	)
);

// booking details
hb_get_template( 'emails/booking-details.php', array( 'booking' => $booking ) );

// customer details
hb_get_template( 'emails/customer-details.php', array( 'booking' => $booking ) );

// email footer
hb_get_template( 'emails/email-footer.php', array( 'booking' => $booking ) );
