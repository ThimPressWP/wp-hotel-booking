<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-25 13:37:05
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-26 16:29:24
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>

<?php if ( ! extension_loaded( 'simplexml' ) ) : ?>

	<p class="description"><?php _e( 'Please enable "simpleXml" php extendsion to import.', 'tp-hotel-booking-importer' ); ?></p>

<?php else: ?>

	<?php if ( isset( $_SESSION['hbip_import_errors'] ) ) : ?>
		<div class="notice error">
			<?php foreach ( $_SESSION['hbip_import_errors'] as $message ) : ?>

				<p>
					<strong><?php printf( '%s', $message ) ?></strong>
				</p>

			<?php endforeach; unset( $_SESSION['hbip_import_errors'] ); ?>
		</div>
	<?php endif; ?>
	<p class="description"><?php _e( 'This will import all of your rooms, bookings, coupons, users, pricing plan, block special date, additonal packages if exists in export file.', 'tp-hotel-booking-importer' ); ?></p>
	<?php wp_import_upload_form( 'admin.php?import=tp-hotel-tools&amp;tab=import' ) ?>

<?php endif; ?>
