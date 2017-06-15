<?php
/**
 * @Author: ducnvtt
 * @Date  :   2016-04-14 11:14:34
 * @Last  Modified by:   ducnvtt
 * @Last  Modified time: 2016-04-14 15:02:21
 */

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

// email heading
hb_get_template( 'emails/email-header.php',
	array(
		'email_heading'      => __( 'Thanks for your booking', 'wp-hotel-booking' ),
		'email_heading_desc' => __( 'Thank you for making reservation at our hotel. We will try our best to bring the best service. Good luck and see you soon!', 'wp-hotel-booking' )
	)
);

// booking items
hb_get_template( 'emails/booking-details.php', array( 'booking' => $booking, 'options' => $options ) );

// customer details
hb_get_template( 'emails/customer-details.php', array( 'booking' => $booking, 'options' => $options ) );

// email footer
hb_get_template( 'emails/email-footer.php', array( 'booking' => $booking, 'options' => $options ) );
