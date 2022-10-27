<?php
/**
 * Template for displaying footer of setup wizard.
 *
 * @author  ThimPres
 * @package LearnPress/Admin/Views
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'admin_print_footer_scripts' );
//do_action( 'admin_footer' );
?>

<footer>
	<p><?php printf( __( 'WP Hotel Booking %s. Designed by @ThimPress.', 'wp-hotel-booking' ), WPHB_VERSION ); ?></p>
	<p><a class="button-dashboard-page" href="<?php echo admin_url( 'index.php' ); ?>"><?php _e( 'Back to Dashboard', 'wp-hotel-booking' ); ?></a></p>
</footer>
</div>
</body>
</html>
