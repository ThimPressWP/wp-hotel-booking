<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    $meta_value = get_post_meta($post->ID, $field['name'], true);
    $upload_dir = wp_upload_dir();
    $upload_base_url = $upload_dir['baseurl'];
?>
<div class="hb-form-field-input">
    <ul>
        <?php if( $meta_value ): foreach ($meta_value as $key => $id): ?>
            <li class="attachment">
                <div class="attachment-preview">
                    <div class="thumbnail">
                        <div class="centered">
                        	<?php echo wp_get_attachment_image( $id, 'thumbnail' ); ?>
                            <input type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>[]" value="<?php echo esc_attr($id); ?>" />
                        </div>
                    </div>
                </div>
                <a class="dashicons dashicons-trash" title="<?php _e( 'Remove this image', 'wp-hotel-booking' ); ?>"></a>
            </li>
        <?php endforeach; endif; ?>
        <li class="attachment add-new">
            <div class="attachment-preview">
                <div class="thumbnail">
                    <div class="dashicons-plus dashicons">
                    </div>
                </div>
            </div>
        </li>
    </ul>
</div>