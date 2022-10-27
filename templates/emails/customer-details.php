<?php
/**
 * The template for displaying email customer details.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/emails/customer-details.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
?>

<h2 class="section-title"><?php _e( 'Customer details', 'wp-hotel-booking' ); ?></h2>
<ul>
	<li><strong><?php echo esc_html__( 'Customer Name:', 'wp-hotel-booking' ); ?></strong>
		<span><?php printf( '%s', hb_get_customer_fullname( $booking->id ) ); ?></span>
	</li>
	<li><strong><?php echo esc_html__( 'Email address:', 'wp-hotel-booking' ); ?></strong>
		<a href="mailto:<?php echo esc_attr( $booking->customer_email ); ?>"><?php echo esc_html( $booking->customer_email ); ?></a>
	</li>
	<li><strong><?php echo esc_html__( 'Phone:', 'wp-hotel-booking' ); ?></strong>
		<span class="text">
		<?php
		echo esc_html( $booking->customer_phone );
		?>
 </span>
	</li>
</ul>

<h2 class="section-title"><?php _e( 'Billing address', 'wp-hotel-booking' ); ?></h2>
<ul>
	<li><strong><?php echo esc_html__( 'Address:', 'wp-hotel-booking' ); ?></strong>
		<span>
			<?php printf( '%s', $booking->customer_address ); ?><br>
			<?php printf( '%s', $booking->customer_city ); ?><br>
			<?php printf( '%s', $booking->customer_state ); ?><br>
			<?php printf( '%s', $booking->customer_country ); ?><br>
		</span>
	</li>
	<li>
		<strong><?php echo esc_html__( 'Postal Code:', 'wp-hotel-booking' ); ?></strong>
		<span><?php echo esc_html( $booking->customer_postal_code ); ?></span>
	</li>
	<?php if ( $booking->post->post_content ) { ?>
		<li><strong><?php echo esc_html__( 'Additional Information:', 'wp-hotel-booking' ); ?></strong>
			<span><?php echo esc_html( $booking->post->post_content ); ?></span>
		</li>
	<?php } ?>
</ul>
