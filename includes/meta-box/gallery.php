<?php
    $meta_value = get_post_meta($post->ID, $field['name'], true);
    $upload_dir = wp_upload_dir();
    $upload_base_url = $upload_dir['baseurl'];
?>
<div class="hb-form-field-input">
    <ul>
        <?php if( $meta_value ): foreach ($meta_value as $key => $src): ?>
            <li class="attachment">
                <div class="attachment-preview">
                    <div class="thumbnail">
                        <div class="centered">
                        	<img src="<?php echo untrailingslashit($upload_base_url).$src ?>" />
                            <input type="hidden" name="<?php echo $field['name'] ?>[]" value="<?php echo esc_attr($src);?>" />
                        </div>
                    </div>
                </div>
                <a class="dashicons dashicons-trash" title="<?php _e( 'Remove this image', 'tp-hotel-booking' );?>"></a>
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