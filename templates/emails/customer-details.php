<?php
/**
 * @Author: ducnvtt
 * @Date  :   2016-04-14 10:38:17
 * @Last  Modified by:   ducnvtt
 * @Last  Modified time: 2016-04-15 16:56:13
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
?>
<h2 class="section-title"><?php _e( 'Customer details', 'wp-hotel-booking' ) ?></h2>
<ul>
    <li><strong><?php echo esc_html__( 'Customer Name:', 'wp-hotel-booking' ); ?></strong>
        <span><?php printf( '%s', hb_get_customer_fullname( $booking->id ) ) ?></span>
    </li>
    <li><strong><?php echo esc_html__( 'Email address:', 'wp-hotel-booking' ); ?></strong>
        <a href="mailto:<?php echo esc_attr( $booking->customer_email ); ?>"><?php echo esc_html( $booking->customer_email ); ?></a>
    </li>
    <li><strong><?php echo esc_html__( 'Phone:', 'wp-hotel-booking' ); ?></strong>
        <span class="text"><?php echo esc_html( $booking->customer_phone );; ?></span>
    </li>
</ul>


<h2 class="section-title"><?php _e( 'Billing address', 'wp-hotel-booking' ) ?></h2>
<ul>
    <li><strong><?php echo esc_html__( 'Address:', 'wp-hotel-booking' ); ?></strong>
        <span>
			<?php printf( '%s', $booking->customer_address ) ?><br>
			<?php printf( '%s', $booking->customer_city ) ?><br>
			<?php printf( '%s', $booking->customer_state ) ?><br>
			<?php printf( '%s', $booking->customer_country ) ?><br>
        </span>
    </li>
    <li><strong><?php echo esc_html__( 'Postal Code:', 'wp-hotel-booking' ); ?></strong>
        <span><?php echo esc_html( $booking->customer_postal_code ) ?></span>
    </li>
	<?php if ( $booking->post->post_content ) : ?>
        <li><strong><?php echo esc_html__( 'Addition Information:', 'wp-hotel-booking' ); ?></strong>
            <span><?php echo esc_html( $booking->post->post_content ) ?></span>
        </li>
	<?php endif; ?>
</ul>