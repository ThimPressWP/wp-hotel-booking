<?php

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

$rooms = WP_Hotel_Booking::instance()->cart->get_rooms();
?>
<?php if ( $rooms ): ?>

	<?php foreach ( $rooms as $key => $room ): ?>

		<?php if ( $cart_item = WP_Hotel_Booking::instance()->cart->get_cart_item( $key ) ) : ?>
			<?php hb_get_template( 'loop/mini-cart-loop.php', array( 'cart_id' => $key, 'room' => $room ) ); ?>
		<?php endif; ?>

	<?php endforeach; ?>

    <div class="hb_mini_cart_footer">

        <a href="<?php echo esc_url( hb_get_checkout_url() ); ?>" class="hb_button hb_checkout"><?php _e( 'Check Out', 'wp-hotel-booking' ); ?></a>
        <a href="<?php echo esc_url( hb_get_cart_url() ); ?>" class="hb_button hb_view_cart"><?php _e( 'View Cart', 'wp-hotel-booking' ); ?></a>

    </div>

<?php else: ?>

    <p class="hb_mini_cart_empty"><?php _e( 'Your cart is empty!', 'wp-hotel-booking' ); ?></p>

<?php endif; ?>
