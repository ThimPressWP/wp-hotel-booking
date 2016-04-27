<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-25 13:37:05
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-27 16:12:14
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
$bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
$size = size_format( $bytes );
$upload_dir = wp_upload_dir();

?>
<div class="wrap">
	<p class="description"><?php _e( 'This will import all of your rooms, bookings, coupons, users, pricing plan, block special date, additonal packages if exists in export file.', 'tp-hotel-booking-importer' ); ?></p>
	<?php
	if ( isset( $_SESSION['hbip_import_flash_messages'] ) ) :
		foreach ( $_SESSION['hbip_import_flash_messages'] as $type => $message ) :

			if ( empty( $message ) ) continue; ?>
			<div class="notice  <?php echo esc_attr( $type ) ?>">
			<?php endforeach; unset( $_SESSION['hbip_import_flash_messages'] ); ?>
		<?php endif; ?>
			<p class="description"><?php _e( 'This will import all of your rooms, bookings, coupons, users, pricing plan, block special date, additonal packages if exists in export file.', 'tp-hotel-booking-importer' ); ?></p>
			<form enctype="multipart/form-data" method="post" class="wp-upload-form">
				<p>
					<strong><?php printf( '%s', implode( '', $message ) ) ?></strong>
				</p>
			</div>

		<?php endforeach; unset( $_SESSION['hbip_import_flash_messages'] );
	endif;
	wp_import_upload_form( 'admin.php?import=hbip_importer&amp;step=1' )
	?>
</div>