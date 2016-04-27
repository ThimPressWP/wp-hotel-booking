<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-25 13:37:05
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-27 14:09:45
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
$size = size_format( $bytes );
$upload_dir = wp_upload_dir();

?>

<?php if ( ! extension_loaded( 'simplexml' ) ) : ?>

	<p class="description"><?php _e( 'Please enable "simpleXml" php extendsion to import.', 'tp-hotel-booking-importer' ); ?></p>

<?php else: ?>
	<?php if ( ! empty( $upload_dir['error'] ) ) : ?>
		<div class="error"><p><?php _e( 'Before you can upload your import file, you will need to fix the following error:', 'tp-hotel-booking-importer' ); ?></p>
	    <p><strong><?php echo $upload_dir['error']; ?></strong></p></div>
	<?php else : ?>
		<?php if ( isset( $_SESSION['hbip_import_flash_messages'] ) ) : ?>
			<?php foreach ( $_SESSION['hbip_import_flash_messages'] as $type => $message ) : ?>

				<?php if ( empty( $message ) ) continue; ?>
				<div class="notice  <?php echo esc_attr( $type ) ?>">
					<p>
						<strong><?php printf( '%s', implode( '', $message ) ) ?></strong>
					</p>
				</div>

			<?php endforeach; unset( $_SESSION['hbip_import_flash_messages'] ); ?>
		<?php endif; ?>
			<p class="description"><?php _e( 'This will import all of your rooms, bookings, coupons, users, pricing plan, block special date, additonal packages if exists in export file.', 'tp-hotel-booking-importer' ); ?></p>
			<form enctype="multipart/form-data" method="post" class="wp-upload-form">
				<p>
					<label for="upload"><?php _e( 'Choose a file from your computer:' ); ?></label> (<?php printf( __( 'Maximum size: %s', 'tp-hotel-booking-importer' ), $size ); ?>)
					<input type="file" id="upload" name="import" size="25" />
					<input type="hidden" name="action" value="save" />
					<input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
				</p>
				<?php wp_nonce_field( 'hbip-import-upload', 'hbip-import-upload' ); ?>
				<?php submit_button( __('Upload file and import', 'tp-hotel-booking-importer'), 'primary' ); ?>
			</form>
		<?php endif; ?>
<?php endif; ?>
