<?php
/**
 * mini cart extra package
 *
 * @author 		ThimPress
 * @package 	wp-hotel-booking/templates
 * @version     1.0
 */

?>

<?php if ( $packages ) : ?>

	<div class="hb_mini_cart_price_packages">
		<label><?php _e( 'Addition Services:', 'wp-hotel-booking' ) ?></label>
		<ul>
			<?php foreach ( $packages as $cart ) : ?>
				<li>
					<div class="hb_package_title">
						<a href="#"><?php printf( '%s (%s)', $cart->product_data->title, hb_format_price( $cart->amount_singular ) ) ?></a>
						<span>
							(<?php printf( 'x%s', $cart->quantity ) ?>)
							<a href="#" class="hb_package_remove" data-cart-id="<?php echo esc_attr( $cart->cart_id ) ?>"><i class="fa fa-times"></i></a>
						</span>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

<?php endif; ?>
