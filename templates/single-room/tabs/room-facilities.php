<?php
/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( empty( $facilities ) ) {
	return;
}
?>
<div class="_hb_room_facility">
	<?php foreach ( $facilities as $facility ) : ?>
        <div class="__hb_room_facility__detail">
            <h3 class="__hb_room_facility__label">
				<?php echo esc_html( $facility['label'] ); ?>
            </h3>
			<?php
			if ( $facility['attr'] ) {
				?>
                <div class="__hb_room_facility__attr">
					<?php
					$attrs = $facility['attr'];
					foreach ( $attrs as $attr ) {
						?>
                        <div class="facility_attr">
							<?php
							if ( ! empty( $attr['image'] ) ) {
								$image_id  = $attr['image'];
								$image_url = wp_get_attachment_thumb_url( $image_id );
								?>
                                <div class="facility_attr__icon">
                                    <img src="<?php echo esc_url_raw( $image_url ); ?>" alt="">
                                </div>
								<?php
							}
							if ( ! empty( $attr['label'] ) ) {
								?>
                                <div class="facility_attr__label">
									<?php echo esc_html( $attr['label'] ); ?>
                                </div>
								<?php
							}
							?>
                        </div>
						<?php
					}
					?>
                </div>
				<?php
			}
			?>
        </div>
		<?php
//		echo '<pre>';
//		print_r( $facility );
//		echo '</pre>';
		?>
	<?php endforeach; ?>
</div>
