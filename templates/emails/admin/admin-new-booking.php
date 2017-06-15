<?php
/**
 * @Author: ducnvtt
 * @Date  :   2016-04-14 10:37:32
 * @Last  Modified by:   someone
 * @Last  Modified time: 2016-05-11 16:42:47
 */

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

// email heading
hb_get_template( 'emails/email-header.php',
	array(
		'email_heading'      => $email_heading,
		'email_heading_desc' => $email_heading_desc
	)
);

// booking details
hb_get_template( 'emails/booking-details.php', array( 'booking' => $booking, 'options' => $options ) );

// customer details
hb_get_template( 'emails/customer-details.php', array( 'booking' => $booking, 'options' => $options ) );

// email footer
hb_get_template( 'emails/email-footer.php', array( 'booking' => $booking, 'options' => $options ) );
