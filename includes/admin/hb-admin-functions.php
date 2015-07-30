<?php
/**
 * Common function for admin side
 */
/**
 * Define default tabs for settings
 *
 * @return mixed
 */
function hb_admin_settings_tabs(){
    $tabs = array(
        'general'       => __( 'General', 'tp-hotel-booking' ),
        'hotel_info'    => __( 'Hotel Information', 'tp-hotel-booking' ),
        'payments'      => __( 'Payments', 'tp-hotel-booking' )
    );
    return apply_filters( 'hb_admin_settings_tabs', $tabs );
}

/**
 * Callback handler for Hotel Information tab content
 */
function hb_admin_settings_tab_hotel_info(){
    TP_Hotel_Booking::instance()->_include( 'includes/admin/views/settings/hotel-info.php' );
}

/**
 * Callback handler for Hotel Information tab content
 */
function hb_admin_settings_tab_payments(){
    TP_Hotel_Booking::instance()->_include( 'includes/admin/views/settings/payments.php' );
}

/**
 * Callback handler for Hotel Information tab content
 */
function hb_admin_settings_tab_general(){
    TP_Hotel_Booking::instance()->_include( 'includes/admin/views/settings/general.php' );
}

function hb_admin_settings_tab_content( $selected ){
    if( is_callable( "hb_admin_settings_tab_{$selected}" ) ) {
        call_user_func_array( "hb_admin_settings_tab_{$selected}", array() );
    }
}
add_action( 'hb_admin_settings_tab_before', 'hb_admin_settings_tab_content' );

function hb_add_meta_boxes(){
    HB_Meta_Box::instance(
        'room_settings',
        array(
            'title' => __( 'Room Settings', 'tp-hotel-booking' ),
            'post_type' => 'hb_room',
            'meta_key_prefix' => '_hb_'
        ),
        array()
    )->add_field(
        array(
            'name'      => 'num_of_rooms',
            'label'     => __( 'Number of rooms', 'tp-hotel-booking' ),
            'type'      => 'number',
            'std'       => '100',
            'desc'      => __( 'The number of rooms', 'tp-hotel-booking' ),
            'min'       => 1,
            'max'       => 100
        )
    )->add_field(
        array(
            'name'      => 'room_type',
            'label'     => __( 'Room type', 'tp-hotel-booking' ),
            'type'      => 'select',
            'options'   => hb_get_room_types(
                array(
                    'map_fields' => array(
                        'term_id'   => 'value',
                        'name' => 'text'
                    )
                )
            )
        ),
        array(
            'name'      => 'room_capacity',
            'label'     => __( 'Number of adults', 'tp-hotel-booking' ),
            'type'      => 'select',
            'options'   => hb_get_room_capacities(
                array(
                    'map_fields' => array(
                        'term_id'   => 'value',
                        'name' => 'text'
                    )
                )
            )
        ),
        array(
            'name'      => 'max_child_per_room',
            'label'     => __( 'Max child per room', 'tp-hotel-booking' ),
            'type'      => 'number',
            'std'       => 0,
            'min'       => 0,
            'max'       => 100
        )
    );
}
add_action( 'init', 'hb_add_meta_boxes', 50 );
function hb_update_meta_box_room_settings( $post_id ){
    wp_set_object_terms( $post_id, intval( $_POST['room_type'] ), 'hb_room_type' );
    wp_set_object_terms( $post_id, intval( $_POST['room_capacity'] ), 'hb_room_capacity' );
}

add_action( 'hb_update_meta_box_room_settings', 'hb_update_meta_box_room_settings' );