<?php
/**
 * Template Booking Details
 * @since  1.1
 */

// customer information
$customer = $booking->_customer; // HB_Customer::instance( $booking->customer_id );

// booking details cart params
$cart_params = $booking->get_cart_params();

?>
<!--Customer Information-->
<?php TP_Hotel_Booking::instance()->_include( 'includes/admin/views/update/customer.php', true, array( 'customer' => $customer ) ) ?>

<!--Payment Details-->
<?php TP_Hotel_Booking::instance()->_include( 'includes/admin/views/update/payment.php', true, array( 'booking' => $booking ) ) ?>

<!--Booking Details-->
<?php TP_Hotel_Booking::instance()->_include( 'includes/admin/views/update/cart.php', true, array( 'cart_params' => $cart_params, 'booking' => $booking ) ) ?>

<!--Additions Details-->
<?php TP_Hotel_Booking::instance()->_include( 'includes/admin/views/update/additions.php', true, array( 'booking' => $booking ) ) ?>
