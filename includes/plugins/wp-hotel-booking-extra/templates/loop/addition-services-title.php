<?php
/**<?php
 * /**
 * The template for displaying extra package in mini cart page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking-extra/loop/addition-services-title.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Extra/Templates
 * @version 1.9.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;
?>

<?php
/**
 * @var $page
 */
?>
<tr class="hb_addition_services_title" data-cart-id="<?php echo esc_attr( $cart_id ); ?>" style="background-color: #FFFFFF;">
	<td colspan="<?php echo ! empty( $page === 'checkout' ) ? 8 : 9; ?>">
		<?php _e( 'Addition Services', 'wp-hotel-booking' ); ?>
	</td>
</tr>
