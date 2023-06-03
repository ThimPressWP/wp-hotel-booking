<?php
/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

$room_id = $post->ID;
if ( empty( $room_id ) ) {
	return;
}

$facilities = get_post_meta( $room_id, '_wphb_room_facilities', true );
?>
<div class="_hb_room_fac_meta_box form-field">
    <label><?php esc_html_e( 'Room facilities', 'wp-hotel-booking' ); ?></label>
    <div class="_hb_room_fac_panel_inner _hb_room_faq_meta_box__content">
        <?php
        foreach($facilities as $fac_key => $facility){
            ?>
            <div class="_hb_room_fac_panel">
                <div class="_hb_room_fac_panel_title">
                    <i class="dashicons dashicons-move"></i>
                    <span class="_hb_room_fac_panel_title_text"></span>
                    <span class="_hb_room_fac_panel_remove">
                        <i class="dashicons dashicons-no-alt"></i>
                    </span>
                    <span class="_hb_room_fac_panel_toggle">
                        <i class="dashicons dashicons-arrow-down-alt2"></i>
                    </span>
                </div>
                <div class="_hb_room_fac_panel_content">
                    <div class="_hb_room_fac_label form-field">
                        <label><?php esc_attr_e( 'Label', 'wp-hotel-booking' ); ?></label>
                        <div class="hb-form-field-input">
                            <input type="text" name="_hb_room_fac_label[]" value="<?php echo esc_attr($facility['label']); ?>">
                        </div>
                    </div>
                    <div class="_hb_fac_attr form-field">
                        <label><?php esc_html_e( 'Attribute', 'wp-hotel-booking' ); ?></label>
                       <div class="_hb_fac_attr_inner">
                           <?php
                           $attrs = $facility['attr'];

                           foreach($attrs as $attr){
                               ?>
                               <div class="_hb_fac_attr_panel">
                                   <div class="_hb_fac_attr_panel_title">
                                       <i class="dashicons dashicons-move"></i>
                                       <span class="_hb_fac_attr_panel_title_text"></span>
                                       <span class="_hb_fac_panel_remove">
                                        <i class="dashicons dashicons-no-alt"></i>
                                    </span>
                                       <span class="_hb_fac_panel_toggle">
                                        <i class="dashicons dashicons-arrow-down-alt2"></i>
                                    </span>
                                   </div>
                                   <div class="_hb_fac_attr_panel_content">
                                       <div class="_hb_fac_attr_label form-field">
                                           <label><?php esc_attr_e( 'Label', 'wp-hotel-booking' ); ?></label>
                                           <div class="hb-form-field-input">
                                               <?php
                                               $fac_attr_label_name = '_hb_room_fac_attr_label['.$fac_key.'][]';
                                               $fac_attr_label_value = $attr['label'] ?? '';
                                               ?>
                                               <input type="text" name="<?php echo esc_attr($fac_attr_label_name);?>" value="<?php echo esc_attr($fac_attr_label_value);?>">
                                           </div>
                                       </div>
			                           <?php
			                           $fac_attr_label_name = '_hb_room_fac_attr_image['.$fac_key.'][]';
			                           $fac_attr_label_value = $attr['image'] ?? '';
			                           $field = array(
				                           'class'       => '_hb_room_fac_attr_image',
				                           'title'       => esc_html__( 'Image', 'wp-hotel-booking' ),
				                           'id'          => '_hb_room_fac_attr_image',
				                           'name'        => $fac_attr_label_name,
				                           'description' => '',
				                           'value'       => $fac_attr_label_value
			                           );
			                           require WP_Hotel_Booking::instance()->locate( "includes/admin/metaboxes/views/fields/image.php" );
			                           ?>
                                   </div>
                               </div>
                                <?php
                           }
                           ?>
                           <a href="#" class="button button-primary _hb_fac_attr_meta_box__add">
                               <?php esc_html_e( '+ Add more', 'wp-hotel-booking' ); ?>
                           </a>
                       </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
        <a href="#" class="button button-primary _hb_room_fac_meta_box__add">
		    <?php esc_html_e( '+ Add more', 'wp-hotel-booking' ); ?>
        </a>
    </div>


    <div class="fac_append" style="display:none">
        <div class="_hb_room_fac_panel">
            <div class="_hb_room_fac_panel_title">
                <i class="dashicons dashicons-move"></i>
                <span class="_hb_room_fac_panel_title_text"></span>
                <span class="_hb_room_fac_panel_remove">
                    <i class="dashicons dashicons-no-alt"></i>
                </span>
                <span class="_hb_room_fac_panel_toggle">
                    <i class="dashicons dashicons-arrow-down-alt2"></i>
                </span>
            </div>
            <div class="_hb_room_fac_panel_content">
                <div class="_hb_room_fac_label form-field">
                    <label><?php esc_attr_e( 'Label', 'wp-hotel-booking' ); ?></label>
                    <div class="hb-form-field-input">
                        <input type="text" name="_hb_room_fac_label[]" value="">
                    </div>
                </div>
                <div class="_hb_fac_attr form-field">
                    <label><?php esc_html_e( 'Attribute', 'wp-hotel-booking' ); ?></label>
                    <div class="_hb_fac_attr_inner">
<!--                        <div class="_hb_fac_attr_panel">-->
<!--                            <div class="_hb_fac_attr_panel_title">-->
<!--                                <i class="dashicons dashicons-move"></i>-->
<!--                                <span class="_hb_fac_attr_panel_title_text"></span>-->
<!--                                <span class="_hb_fac_panel_remove">-->
<!--                                    <i class="dashicons dashicons-no-alt"></i>-->
<!--                                </span>-->
<!--                                <span class="_hb_fac_panel_toggle">-->
<!--                                    <i class="dashicons dashicons-arrow-down-alt2"></i>-->
<!--                                </span>-->
<!--                            </div>-->
<!--                            <div class="_hb_fac_attr_panel_content">-->
<!--                                <div class="_hb_fac_attr_label form-field">-->
<!--                                    <label>--><?php //esc_attr_e( 'Label', 'wp-hotel-booking' ); ?><!--</label>-->
<!--                                    <div class="hb-form-field-input">-->
<!--                                        <input type="text" name="_hb_room_fac_attr_label[]" value="">-->
<!--                                    </div>-->
<!--                                </div>-->
<!--							    --><?php
//							    $field = array(
//								    'class'       => '_hb_room_fac_attr_image',
//								    'title'       => esc_html__( 'Image', 'wp-hotel-booking' ),
//								    'id'          => '_hb_room_fac_attr_image',
//								    'name'        => '_hb_room_fac_attr_image',
//								    'description' => '',
//								    'value'       => ''
//							    );
//							    require WP_Hotel_Booking::instance()->locate( "includes/admin/metaboxes/views/fields/image.php" );
//							    ?>
<!--                            </div>-->
<!--                        </div>-->
                        <a href="#" class="button button-primary _hb_fac_attr_meta_box__add">
						    <?php esc_html_e( '+ Add more', 'wp-hotel-booking' ); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="fac_attr_append" style="display:none">
        <div class="_hb_fac_attr_panel">
            <div class="_hb_fac_attr_panel_title">
                <i class="dashicons dashicons-move"></i>
                <span class="_hb_fac_attr_panel_title_text"></span>
                <span class="_hb_fac_panel_remove">
                                    <i class="dashicons dashicons-no-alt"></i>
                                </span>
                <span class="_hb_fac_panel_toggle">
                                    <i class="dashicons dashicons-arrow-down-alt2"></i>
                                </span>
            </div>
            <div class="_hb_fac_attr_panel_content">
                <div class="_hb_fac_attr_label form-field">
                    <label><?php esc_attr_e( 'Label', 'wp-hotel-booking' ); ?></label>
                    <div class="hb-form-field-input">
                        <input type="text" name="_hb_room_fac_attr_label[0][]" value="">
                    </div>
                </div>
			    <?php
			    $field = array(
				    'class'       => '_hb_room_fac_attr_image',
				    'title'       => esc_html__( 'Image', 'wp-hotel-booking' ),
				    'id'          => '_hb_room_fac_attr_image',
				    'name'        => '_hb_room_fac_attr_image[0][]',
				    'description' => '',
				    'value'       => ''
			    );
			    require WP_Hotel_Booking::instance()->locate( "includes/admin/metaboxes/views/fields/image.php" );
			    ?>
            </div>
        </div>
    </div>
</div>
