<?php


if ( ! isset( $field ) ) {
	return;
}
?>
	<div class="hb-form-field-image form-field">
		<label><?php echo esc_html( $field['title'] ); ?></label>
		<?php
		$image_id            = $field['value'];
		$image_full_url      = wp_get_attachment_image_url( $image_id, 'full' );
		$image_thumbnail_url = wp_get_attachment_image_url( $image_id, 'thumbnail' );
		?>
		<div class="hb-image-info">
			<div class="hb-image-inner">
				<div class="hb-image-preview">
					<img src="<?php echo esc_url_raw( $image_thumbnail_url ); ?>">
				</div>
				<div class="hb-image-control">
					<input type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>"
							value="<?php echo esc_attr( $image_id ); ?>" readonly/>
					<input type="text" id="<?php echo esc_attr( $field['id'] ); ?>"
							value="<?php echo esc_attr( $image_full_url ); ?>" readonly/>
					<button type="button" href="#"
							class="button button-secondary hb-image-add"><?php esc_html_e( 'Select Image', 'wp-hotel-booking' ); ?></button>
					<button type="button" href="#"
							class="button button-secondary hb-image-remove"><?php esc_html_e( 'Remove', 'wp-hotel-booking' ); ?></button>
				</div>
			</div>
			<?php
			if ( ! empty( $field['description'] ) ) {
				?>
				<p class="description"><?php echo $field['description']; ?></p>
				<?php
			}
			?>
		</div>
	</div>
<?php
if ( ! did_action( 'wp_enqueue_media' ) ) {
	wp_enqueue_media();
}
