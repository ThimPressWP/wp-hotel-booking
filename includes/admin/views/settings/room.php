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
        <th><?php _e( 'Number of post display in page', 'tp-hotel-booking' );?></th>
        <td>
            <input type="number" name="<?php echo $settings->get_field_name('posts_per_page'); ?>" value="<?php echo $settings->get('posts_per_page', 8);?>" size="8" min="0"/>
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
    <tr>
        <th><?php _e( 'Display rating', 'tp-hotel-booking' );?></th>
        <td>
            <input type="hidden" name="<?php echo $settings->get_field_name('catalog_display_rating');?>" value="0" />
            <input type="checkbox" name="<?php echo $settings->get_field_name('catalog_display_rating');?>" <?php checked( $settings->get('catalog_display_rating') ? 1 : 0, 1 );?> value="1"/>
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
    <tr>
        <th><?php _e( 'Room images thumbnail', 'tp-hotel-booking' );?></th>
        <td>
            <input type="number" name="<?php echo $settings->get_field_name('room_thumbnail_width'); ?>" value="<?php echo $settings->get('room_thumbnail_width', 150);?>" size="4" min="0"/>
            x
            <input type="number" name="<?php echo $settings->get_field_name('room_thumbnail_height'); ?>" value="<?php echo $settings->get('room_thumbnail_height', 150);?>" size="4" min="0"/>
            px
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Display pricing plans', 'tp-hotel-booking' );?></th>
        <td>
            <input type="hidden" name="<?php echo $settings->get_field_name('display_pricing_plans');?>" value="0" />
            <input type="checkbox" name="<?php echo $settings->get_field_name('display_pricing_plans');?>" <?php checked( $settings->get('display_pricing_plans') ? 1 : 0, 1 );?> value="1" />
        </td>
    </tr>
</table>

<h3 class="description"><?php _e( 'Room Ratings', 'tp-hotel-booking' );?></h3>
<table class="form-table">
    <tr>
        <th><?php _e( 'Enable ratings on reviews', 'tp-hotel-booking' );?></th>
        <td>
            <input type="hidden" name="<?php echo $settings->get_field_name('enable_review_rating');?>" value="0" />
            <input type="checkbox" name="<?php echo $settings->get_field_name('enable_review_rating');?>" <?php checked( $settings->get('enable_review_rating') ? 1 : 0, 1 );?> value="1" onchange="jQuery('.enable_ratings_on_reviews').toggleClass('hide-if-js', ! this.checked );" />
        </td>
    </tr>
    <tr class="enable_ratings_on_reviews<?php echo $settings->get('enable_ratings_on_reviews') ? '' : ' hide-if-js';?>">
        <th><?php _e( 'Ratings are required to leave a review', 'tp-hotel-booking' );?></th>
        <td>
            <input type="hidden" name="<?php echo $settings->get_field_name('review_rating_required');?>" value="0" />
            <input type="checkbox" name="<?php echo $settings->get_field_name('review_rating_required');?>" <?php checked( $settings->get('review_rating_required') ? 1 : 0, 1 );?> value="1" />
        </td>
    </tr>
</table>

<h3 class="description"><?php _e( 'Gallery images', 'tp-hotel-booking' );?></h3>
<table class="form-table">
    <tr>
        <th><?php _e( 'Enable gallery lightbox', 'tp-hotel-booking' );?></th>
        <td>
            <input type="hidden" name="<?php echo $settings->get_field_name('enable_gallery');?>" value="0" />
            <input type="checkbox" name="<?php echo $settings->get_field_name('enable_gallery');?>" <?php checked( $settings->get('enable_gallery') ? 1 : 0, 1 );?> value="1"/>
        </td>
    </tr>
</table>