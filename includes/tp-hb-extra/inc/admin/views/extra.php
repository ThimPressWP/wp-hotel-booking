<?php
/**
 * use hb_settings function get field name
 * HB_Settings save auto
 * else customize save function
 */

global $hb_extra_settings;
$extras = $hb_extra_settings->get_extra();
$field_name = TP_HB_OPTION_NAME;
$extra_types = tp_hb_extra_type();
$respondent = array();
foreach ( $extra_types as $key => $value ) {
    $respondent[] = array( 'text' => $value, 'value' => $key );
}
?>

<!-- Email Sender Options block -->
<h3><?php _e( 'Extra room', 'tp-hb-extra' ); ?></h3>
<p class="description"><?php _e( 'Adding room\'s services packages with detail price for every service', 'tp-hb-extra' ); ?></p>
<form action="" class="tp_extra_form_field_settings" method="POST">
    <div id="tp_extra_form">
        <div class="tp_extra_form_head">
            <h3><?php _e( 'Extra Options', 'tp-hb-extra' ); ?></h3>
        </div>
        <?php if( $extras ): ?>
            <?php foreach( $extras as $k => $post ): ?>

                <div class="tp_extra_form_fields">
                    <div class="name">
                        <h4><?php _e( 'Name', 'tp-hb-extra' ); ?></h4>
                        <input type="text" name="<?php echo esc_attr( $field_name ); ?>[<?php echo esc_attr( $post->ID ); ?>][name]" value="<?php echo esc_attr( $post->post_title ); ?>"/>
                    </div>
                    <div class="desc">
                        <h4><?php _e( 'Description', 'tp-hb-extra' ); ?></h4>
                        <textarea name="<?php echo esc_attr( $field_name ) ?>[<?php echo esc_attr( $post->ID ); ?>][desc]"><?php printf( '%s', $post->post_content ) ?></textarea>
                    </div>
                    <div class="price">
                        <h4><?php _e( 'Price', 'tp-hb-extra' ); ?></h4>
                        <input type="number" step="any" name="<?php echo esc_attr( $field_name ); ?>[<?php echo esc_attr( $post->ID ); ?>][price]" value="<?php echo esc_attr( get_post_meta( $post->ID, 'tp_hb_extra_room_price', true ) ); ?>"/>
                        <span>/</span>
                        <input type="text" name="<?php echo esc_attr( $field_name ); ?>[<?php echo esc_attr( $post->ID ); ?>][respondent_name]" value="<?php echo esc_attr( get_post_meta( $post->ID, 'tp_hb_extra_room_respondent_name', true ) ); ?>" placeholder="<?php esc_attr_e( 'Package', 'tp-hb-extra' ) ?>"/>
                    </div>
                    <div class="type">
                        <h4><?php _e( 'Price Type', 'tp-hb-extra' ); ?></h4>
                        <?php tp_hb_extra_select( $field_name . '['.$post->ID.'][respondent]', array( 'options' => $respondent ), get_post_meta( $post->ID, 'tp_hb_extra_room_respondent', true ) ) ; ?>
                    </div>
                    <div class="remove">
                        <a data-id="<?php echo esc_attr( $post->ID ); ?>" class="button remove_button"><?php esc_attr_e( 'Remove', 'tp-hb-extra' ); ?></a>
                    </div>
                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="tp_extra_form_fields">
                <div class="name">
                    <h4><?php _e( 'Name', 'tp-hb-extra' ); ?></h4>
                    <input type="text" name="<?php echo esc_attr( $field_name ); ?>[0][name]" value=""/>
                </div>
                <div class="desc">
                    <h4><?php _e( 'Description', 'tp-hb-extra' ); ?></h4>
                    <textarea name="<?php echo esc_attr( $field_name ) ?>[0][desc]"></textarea>
                </div>
                <div class="price">
                    <h4><?php _e( 'Price', 'tp-hb-extra' ); ?></h4>
                    <input type="number" step="any" name="<?php echo esc_attr( $field_name ); ?>[0][price]" value=""/>
                    <span>/</span>
                    <input type="text" name="<?php echo esc_attr( $field_name ); ?>[0][respondent_name]" value="" placeholder="<?php esc_attr_e( 'Package', 'tp-hb-extra' ) ?>"/>
                </div>
                <div class="type">
                    <h4><?php _e( 'Price Type', 'tp-hb-extra' ); ?></h4>
                    <?php tp_hb_extra_select( $field_name . '[0][respondent]', array( 'options' => $respondent ), '' ) ; ?>
                </div>
                <div class="remove">
                    <a data-id="" class="button remove_button"><?php esc_attr_e( 'Remove', 'tp-hb-extra' ); ?></a>
                </div>
            </div>

        <?php endif; ?>
        <div class="tp_extra_form_foot">
            <button type="submit" class="button button-primary"><?php _e( 'Save Extra', 'tp-hb-extra' ); ?></button>
            <a class="button tp_extra_add_item"><?php _e( 'Add another item', 'tp-hb-extra' ); ?></a>
        </div>
    </div>
</form>

<script type="text/html" id="tmpl-tp-hb-extra-room">
    <div class="tp_extra_form_fields">
        <div class="name">
            <h4><?php _e( 'Name', 'tp-hb-extra' ); ?></h4>
            <input type="text" name="<?php echo esc_attr( $field_name ); ?>[{{ data.id }}][name]" value="" placeholder="<?php echo esc_attr( 'Package name' ) ?>"/>
        </div>
        <div class="desc">
            <h4><?php _e( 'Description', 'tp-hb-extra' ); ?></h4>
            <textarea name="<?php echo esc_attr( $field_name ) ?>[{{ data.id }}][desc]" placeholder="<?php esc_attr_e( 'Enter description here', 'tp-hb-extra' ) ?>"></textarea>
        </div>
        <div class="price">
            <h4><?php _e( 'Price', 'tp-hb-extra' ); ?></h4>
            <input type="number" step="any" name="<?php echo esc_attr( $field_name ); ?>[{{ data.id }}][price]" value="-" placeholder="<?php echo esc_attr( '10.5' ) ?>"/>
            <span>/</span>
            <input type="text" name="<?php echo esc_attr( $field_name ); ?>[{{ data.id }}][respondent_name]" value="" placeholder="<?php esc_attr_e( 'Package', 'tp-hb-extra' ) ?>"/>
        </div>
        <div class="type">
            <h4><?php _e( 'Price Type', 'tp-hb-extra' ); ?></h4>
            <?php tp_hb_extra_select( $field_name . '[{{ data.id }}][respondent]', array( 'options' => $respondent ), '' ) ; ?>
        </div>
        <div class="remove">
            <a data-id="{{ data.id }}" class="button remove_button"><?php esc_attr_e( 'Remove', 'tp-hb-extra' ); ?></a>
        </div>
    </div>
</script>