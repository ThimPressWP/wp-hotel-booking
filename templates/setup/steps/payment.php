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
<h2><?php _e( 'Payment', 'wp-hotel-booking' ); ?></h2>

<p class="large-text"><?php _e( 'WP Hotel can accept both online and offline payments. Additional payment addons can be installed later.', 'wp-hotel-booking' ); ?></p>

<ul class="browse-payments">
	<?php foreach ( $payments as $slug => $payment ) { ?>
		<li class="payment payment-<?php echo $slug; ?>">
			<h3 class="payment-name">
				<?php if ( ! empty( $payment['icon'] ) ) { ?>
					<img src="<?php echo $payment['icon']; ?>">
				<?php } else { ?>
					<?php echo $payment['name']; ?>
				<?php } ?>
			</h3>
			<?php if ( ! empty( $payment['desc'] ) ) { ?>
				<p class="payment-desc"><?php echo $payment['desc']; ?></p>
			<?php } ?>
			<div class="payment-settings">
				<?php call_user_func( $payment['callback'] ); ?>
			</div>
		</li>
	<?php } ?>
</ul>
