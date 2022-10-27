<?php
/**
 * Template for displaying finish step.
 *
 * @author  ThimPres
 * @version 2.0.0
 */

defined( 'ABSPATH' ) or exit;
?>
<h2><?php _e( 'Finish', 'wp-hotel-booking' ); ?></h2>

<p><?php _e( 'WP Hotel Booking is ready to go!', 'wp-hotel-booking' ); ?></p>

<p class="finish-buttons">
	<a class="button" href="https://docspress.thimpress.com/wp-hotel-booking/" target="_blank">
		<?php _e( 'View Documentation', 'wp-hotel-booking' ); ?>
	</a>

	<a class="button" href="<?php echo esc_url_raw( admin_url( 'edit.php?post_type=hb_room' ) ); ?>">
		<?php _e( 'Back to Dashboard', 'wp-hotel-booking' ); ?>
	</a>
</p>
