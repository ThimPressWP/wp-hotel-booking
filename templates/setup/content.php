<?php
/**
 * Template for displaying content of setup wizard.
 *
 * @author  ThimPres
 * @version 1.0.0
 */

defined( 'ABSPATH' ) or exit;
$wizard = WPHB_Setup_Wizard::instance();

if ( ! isset( $steps ) ) {
	return;
}
?>

<div id="main">
	<div class="wphb-setup-nav">
		<ul class="wphb-setup-steps">
			<?php foreach ( $steps as $key => $step ) { ?>
				<li class="<?php echo esc_attr( $wizard->get_current_step() == $key ? 'active' : '' ); ?>">
					<span><?php echo esc_html( $step['title'] ); ?></span>
				</li>
			<?php } ?>
		</ul>
		<div class="wphb-setup-progress">
			<div class="active"
				style="width: <?php echo intval( ( $wizard->get_step_position() + 1 ) / sizeof( $steps ) * 100 ); ?>%;">
			</div>
		</div>
	</div>
	<form id="wphb-setup-form" class="wphb-setup-content" name="wphb-setup" method="post">
		<?php
		$step = $wizard->get_current_step( false );
		?>
		<input type="hidden" name="wphb-setup-nonce"
			value="<?php echo wp_create_nonce( 'wphb-setup-step-' . $step['slug'] ); ?>">
		<input type="hidden" name="wphb-setup-step"
			value="<?php echo esc_attr( $step['slug'] ); ?>">
		<div class="wphb-setup-detail">
			<?php call_user_func( $step['callback'] ); ?>
		</div>
		<?php if ( ! $wizard->is_last_step() ) { ?>
			<div class="buttons">
				<?php if ( ! $wizard->is_first_step() && ! ( array_key_exists( 'back_button', $step ) && $step['back_button'] === false ) ) { ?>
					<a class="button button-prev" href="<?php echo esc_url_raw( $wizard->get_prev_url() ); ?>">
						<?php
						if ( ! empty( $step['next_button'] ) ) {
							echo $step['back_button'];
						} else {
							_e( 'Back', 'wp-hotel-booking' );
						}
						?>
					</a>
				<?php } ?>
				<?php if ( ! $wizard->is_last_step() && ! ( array_key_exists( 'next_button', $step ) && $step['next_button'] === false ) ) { ?>
					<a class="button button-next button-primary" href="<?php echo esc_url_raw( $wizard->get_next_url() ); ?>">
						<?php
						if ( ! empty( $step['next_button'] ) ) {
							echo $step['next_button'];
						} else {
							_e( 'Continue', 'wp-hotel-booking' );
						}
						?>
					</a>
				<?php } else { ?>
					<a class="button button-finish">
						<?php _e( 'Finish', 'wp-hotel-booking' ); ?>
					</a>
				<?php } ?>
			</div>
		<?php } ?>
	</form>
	<span class="icon-loading"></span>
</div>
