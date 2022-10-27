<?php
/**
 * The template for displaying admin metabox single room.
 *
 * @version     1.9.7
 * @package     WP_Hotel_Booking/Views
 * @category    Views
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( empty( $room_metabox ) || empty( $post ) ) {
	return;
}
$post_id = $post->ID;
?>
<div class="wphb-meta-box__inner" >
	<div class="wphb-meta-box__room-tab">
		<ul class="wphb-meta-box__room-tab__tabs">
			<?php
			foreach ( $room_metabox->metabox( $post_id ) as $key => $tab ) {
				if ( $key === 'author' && ! is_super_admin() ) {
					continue;
				}

				$class_tab = '';

				if ( isset( $tab['class'] ) ) {
					$class_tab = implode( ' ', (array) $tab['class'] );
				}
				?>
				<li class="<?php echo esc_attr( $key ); ?>_options <?php echo esc_attr( $key ); ?>_tab <?php echo esc_attr( $class_tab ); ?>">
					<a href="<?php echo esc_attr( $tab['target'] ); ?>">
						<?php if ( isset( $tab['icon'] ) ) : ?>
							<i class="<?php echo esc_attr( $tab['icon'] ); ?>"></i>
						<?php endif; ?>
						<span><?php echo esc_html( $tab['label'] ); ?></span>
					</a>
				</li>
			<?php } ?>
		</ul>
		<div class="wphb-meta-box__room-tab__content">
			<?php foreach ( $room_metabox->metabox( $post_id ) as $key => $tab_content ) { ?>
				<?php
				if ( $key === 'author' && ! is_super_admin() ) {
					continue;
				}
				?>
				<?php if ( isset( $tab_content['content'] ) ) { ?>
				<div id="<?php echo esc_attr( $tab_content['target'] ); ?>" 
					class="wphb-meta-box-room-panels <?php echo isset( $tab_content['wrapper_class'] ) ? $tab_content['wrapper_class'] : ''; ?>">
					<?php
					foreach ( $tab_content['content'] as $meta_key => $field ) {

						if ( is_object( $field ) ) {
							echo $field->render( $post );
							continue;
						}

						$wrapper_class = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';

						echo '<div class="form-field ' . $field['name'] . ' ' . $wrapper_class . '">';
						if ( isset( $field['label'] ) && $field['label'] != '' ) {
							echo '<label class="hb-form-field-label">' . $field['label'] . '</label>';
						}
						if ( $room_metabox->has_post_meta( $post->ID, $field['name'] ) ) {
							$field['std'] = get_post_meta( $post->ID, $room_metabox->meta_key_prefix . $field['name'], true );
						}
							$field['name'] = $room_metabox->meta_key_prefix . $field['name'];
						if ( empty( $field['id'] ) ) {
							$field['id'] = sanitize_title( $field['name'] );
						}
						echo '<div class="hb-form-field-input">';
							$tmpl = WP_Hotel_Booking::instance()->locate( "includes/admin/metaboxes/views/fields/{$field['type']}.php" );
							require $tmpl;
						if ( ! empty( $field['desc'] ) ) {
							printf( '<p class="description">%s</p>', $field['desc'] );
						}
						if ( ! empty( $field['edit_option'] ) ) {
							printf(
								'<a href="%s" class="edit_meta" target="_blank">%s</a>', 
								add_query_arg( 
									$field['edit_option'], 
									admin_url( $field['edit_option']['admin_url'] ) 
								),
								$field['text_edit'] ?? esc_html__( 'Edit', 'wp-hotel-booking' ) 
							);
						}
						echo '</div>';
						echo '</div>';
					}
					?>
				</div>
					<?php
				}
			}
			?>
		</div>
	</div>
</div>
