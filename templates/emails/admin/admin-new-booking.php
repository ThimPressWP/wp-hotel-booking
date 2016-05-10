<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-14 10:37:32
 * @Last Modified by:   someone
 * @Last Modified time: 2016-05-11 16:42:47
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
var_dump(1); die();
// email heading
hb_get_template( 'emails/email-header.php',
	array(
		'email_heading' => $email_heading,
		'email_heading_desc'	=> $email_heading_desc
	)
);

// customer details
hb_get_template( 'emails/customer-details.php', array( 'booking' => $booking, 'options' => $options ) );

// booking items
hb_get_template( 'emails/booking-items.php', array( 'booking' => $booking, 'options' => $options ) );

// email footer
hb_get_template( 'emails/email-footer.php', array( 'booking' => $booking, 'options' => $options ) );
