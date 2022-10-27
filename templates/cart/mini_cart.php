<?php
/**
 * The template for displaying mini cart.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/cart/mini_cart.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6.1
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

/**
 * @var $cart WPHB_Cart
 */
$cart  = WP_Hotel_Booking::instance()->cart;
$rooms = $cart->get_rooms();
?>

<?php
if ( $rooms ) {
	foreach ( $rooms as $key => $room ) {
		if ( $cart_item = $cart->get_cart_item( $key ) ) {
			hb_get_template(
				'loop/mini-cart-loop.php',
				array(
					'cart_id' => $key,
					'room'    => $room,
				)
			);
		}
	}
	?>

	<div class="hb_mini_cart_footer">

		<a href="<?php echo esc_url( add_query_arg( 'no-cache', uniqid(), hb_get_checkout_url() ) ); ?>"
		   class="hb_button hb_checkout"><?php _e( 'Check Out', 'wp-hotel-booking' ); ?></a>
		<a href="<?php echo esc_url( add_query_arg( 'no-cache', uniqid(), hb_get_cart_url() ) ); ?>"
		   class="hb_button hb_view_cart"><?php _e( 'View Cart', 'wp-hotel-booking' ); ?></a>

	</div>

<?php } else { ?>

	<p class="hb_mini_cart_empty"><?php _e( 'Your cart is empty!', 'wp-hotel-booking' ); ?></p>

<?php } ?>
