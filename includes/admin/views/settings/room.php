<?php
$settings = hb_settings();
?>
<h3><?php _e( 'Catalog settings', 'tp-hotel-booking' );?></h3>
<p class="description">
    <?php _e( 'Catalog settings display column number and image size used in room list ( archive page, related room )', 'tp-hotel-booking' );?>
</p>
<table class="form-table">
    <tr>
        <th><?php _e( 'Number of column display catalog page', 'tp-hotel-booking' );?></th>
        <td>
            <input type="number" name="<?php echo $settings->get_field_name('catalog_number_column');?>" value="<?php echo $settings->get('catalog_number_column', 4);?>" />
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Catalog images size', 'tp-hotel-booking' );?></th>
        <td>
            <input type="number" name="<?php echo $settings->get_field_name('catalog_image_width'); ?>" value="<?php echo $settings->get('catalog_image_width', 270);?>" size="4" min="0"/>
            x
            <input type="number" name="<?php echo $settings->get_field_name('catalog_image_height'); ?>" value="<?php echo $settings->get('catalog_image_height', 270);?>" size="4" min="0"/>
            px
        </td>
    </tr>
</table>
<h3><?php _e( 'Room settings', 'tp-hotel-booking' );?></h3>
<p class="description">
    <?php _e( 'Room settings display column number and image size used in gallery single page', 'tp-hotel-booking' );?>
</p>
<table class="form-table">
    <tr>
        <th><?php _e( 'Room images size gallery', 'tp-hotel-booking' );?></th>
        <td>
            <input type="number" name="<?php echo $settings->get_field_name('room_image_gallery_width'); ?>" value="<?php echo $settings->get('room_image_gallery_width', 270);?>" size="4" min="0"/>
            x
            <input type="number" name="<?php echo $settings->get_field_name('room_image_gallery_height'); ?>" value="<?php echo $settings->get('room_image_gallery_height', 270);?>" size="4" min="0"/>
            px
        </td>
    </tr>
</table>
