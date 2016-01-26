<?php
/**
 * mini cart extra package
 *
 * @author 		ThimPress
 * @package 	Tp-hotel-booking/Templates
 * @version     1.0
 */

?>

<?php if ( $extra_packages ) : ?>

	<div class="hb_mini_cart_price_packages">
		<label><?php _e( 'Addition Services:', 'tp-hotel-booking' ) ?></label>
		<ul>
			<?php foreach ( $extra_packages as $cart ) : ?>
				<?php $package = HB_Extra_Package::instance( $cart['package_id'], $check_in, $check_out, $room_quantity, $cart['quantity'] ); ?>
				<li>
					<div class="hb_package_title">
						<a href="#"><?php printf( '%s (%s)', $package->title, hb_format_price( $package->amount_regular ) ) ?></a>
						<span>
							(<?php printf( 'x%s', $package->quantity ) ?>)
							<a href="#" class="hb_package_remove" data-cart-id="<?php echo esc_attr( $cart['cart_id'] ) ?>"><i class="fa fa-times"></i></a>
						</span>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

<?php endif; ?>
