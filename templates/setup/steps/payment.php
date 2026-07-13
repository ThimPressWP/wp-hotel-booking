<?php
/**
 * Template for displaying payments of setup wizard.
 *
 * @author  ThimPres
 * @version 2.0.0
 */

defined( 'ABSPATH' ) or exit;

$wizard   = WPHB_Setup_Wizard::instance();
$payments = $wizard->get_payments();

?>
<h2><?php esc_html_e( 'Payment', 'wp-hotel-booking' ); ?></h2>

<p class="large-text"><?php esc_html_e( 'WP Hotel can accept both online and offline payments. Additional payment addons can be installed later.', 'wp-hotel-booking' ); ?></p>

<ul class="browse-payments">
	<?php foreach ( $payments as $slug => $payment ) { ?>
		<li class="payment payment-<?php echo esc_attr( $slug ); ?>">
			<h3 class="payment-name">
				<?php if ( ! empty( $payment['icon'] ) ) { ?>
					<img src="<?php echo esc_url( $payment['icon'] ); ?>">
				<?php } else { ?>
					<?php echo esc_html( $payment['name'] ); ?>
				<?php } ?>
			</h3>
			<?php if ( ! empty( $payment['desc'] ) ) { ?>
				<p class="payment-desc"><?php echo esc_html( $payment['desc'] ); ?></p>
			<?php } ?>
			<div class="payment-settings">
				<?php call_user_func( $payment['callback'] ); ?>
			</div>
		</li>
	<?php } ?>
</ul>
