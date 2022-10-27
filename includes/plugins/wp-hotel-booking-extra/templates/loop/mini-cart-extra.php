<?php
/**
 * The template for displaying extra package in mini cart page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking-extra/loop/mini-cart-extra.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Extra/Templates
 * @version 1.9.7.4
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<?php
/**
 * @var $packages
 */
if ( $packages ) {
	?>
	<div class="hb_mini_cart_price_packages">
		<label><?php _e( 'Addition Services:', 'wp-hotel-booking' ); ?></label>
		<ul>
			<?php foreach ( $packages as $cart ) { ?>
				<li>
					<div class="hb_package_title">
						<a href="#"><?php printf( '%s (%s)', apply_filters( 'hb_mini_cart_extra_name', $cart->product_data->title, $cart->product_id ), hb_format_price( $cart->amount_singular ) ); ?></a>
						<?php if ( ! get_post_meta( $cart->product_id, 'tp_hb_extra_room_required' ) ) { ?>
							<span>(<?php printf( 'x%s', $cart->quantity ); ?>)
							<a href="#" class="hb_package_remove"
							   data-cart-id="<?php echo esc_attr( $cart->cart_id ); ?>"><i class="fa fa-times"></i></a>
						</span>
						<?php } ?>
					</div>
				</li>
			<?php } ?>
		</ul>
	</div>
<?php } ?>
