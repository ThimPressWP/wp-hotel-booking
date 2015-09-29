<?php
$settings = hb_settings();
?>
<!-- Email Sender Options block -->
<h3><?php _e( 'Catalog settings', 'tp-hotel-booking' );?></h3>
<p class="description"><?php _e( 'Catalog settings', 'tp-hotel-booking' );?></p>
<table class="form-table">
    <tr>
        <th><?php _e( 'Number of column display catalog page', 'tp-hotel-booking' );?></th>
        <td>
            <input type="number" name="<?php echo $settings->get_field_name('catalog_number_column');?>" value="<?php echo $settings->get('catalog_number_column', 4);?>" />
        </td>
    </tr>
</table>