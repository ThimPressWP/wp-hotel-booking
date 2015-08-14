<?php
$settings = HB_Settings::instance();
$field_name = $settings->get_field_name('lightbox');
$lightbox = $settings->get('lightbox');
$lightbox = wp_parse_args(
    $lightbox,
    array(
        'lightbox'    => ''
    )
);
$lightboxs = hb_get_support_lightboxs();
?>
<table class="form-table">
    <tr>
        <th><?php _e( 'Lightbox', 'tp-hotel-booking' );?></th>
        <td>
            <select name="<?php echo $field_name;?>[lightbox]">
                <option value=""><?php _e( 'None', 'tp-hotel-booking' );?></option>
                <?php if( $lightboxs ): foreach( $lightboxs as $slug => $name ){?>
                <option value="<?php echo $slug;?>" <?php selected( $slug == $lightbox['lightbox']);?>><?php echo $name;?></option>
                <?php } endif;?>
            </select>
        </td>
    </tr>
</table>