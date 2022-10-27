<?php
/**
 * Template for displaying header of setup wizard.
 *
 * @author  ThimPres
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta name="viewport" content="width=device-width"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php esc_html_e( 'Hotel Booking &rsaquo; Setup Wizard', 'wp-hotel-booking' ); ?></title>
	<?php wp_print_scripts( 'wphb-setup' ); ?>
	<?php do_action( 'admin_print_styles' ); ?>
	<?php do_action( 'admin_print_scripts' ); ?>
	<?php //do_action( 'admin_head' ); ?>

</head>
<body class="wphb-setup wp-core-ui js">
<div id="content">
	<div class="logo">
		<a href="javascript:void(0)">
			<?php $logoUrl = WPHB_PLUGIN_URL . '/assets/images/icon-128x128.png'; ?>
			<img src="<?php echo esc_url_raw( $logoUrl ); ?>">
		</a>
	</div>
