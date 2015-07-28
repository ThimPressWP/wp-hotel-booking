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
        'hotel_info'    => __( 'Hotel Information', 'tp-hotel-booking' ),
        'payments'      => __( 'Payments', 'tp-hotel-booking' ),
        'other'         => __( 'Other Settings', 'tp-hotel-booking' ),
    );
    return apply_filters( 'hb_admin_settings_tabs', $tabs );
}

/**
 * Callback handler for Hotel Information tab content
 */
function hb_admin_settings_tab_hotel_info(){
    echo 'Hotel Info';
}

/**
 * Callback handler for Hotel Information tab content
 */
function hb_admin_settings_tab_payments(){
    echo 'Payment Gateways';
}

/**
 * Callback handler for Hotel Information tab content
 */
function hb_admin_settings_tab_other(){
    echo 'Other settings here';
}

function hb_admin_settings_tab_content( $selected ){
    if( is_callable( "hb_admin_settings_tab_{$selected}" ) ) {
        call_user_func_array( "hb_admin_settings_tab_{$selected}", array() );
    }
}

add_action( 'hb_admin_settings_tab_before', 'hb_admin_settings_tab_content' );

HB_Meta_Box::instance(
    'room_settings',
    array(
        'title' => __( 'Room Settings', 'tp-hotel-booking' ),
        'post_type' => 'hb_room'
    ),
    array()
)->add_field(
    array(
        'name'      => 'num_of_rooms',
        'label'     => __( 'Number of rooms', 'tp-hotel-booking' ),
        'type'      => 'number',
        'std'       => '1000',
        'desc'      => __( 'The number of rooms', 'tp-hotel-booking' ),
        'min'       => 0,
        'max'       => 10
    )
)->add_field(
    array(
        'name'      => 'room_type',
        'label'     => __( 'Room type', 'tp-hotel-booking' ),
        'type'      => 'select'
    )
);

HB_Meta_Box::instance(
    'room_type',
    array(
        'title' => __( 'Room Type', 'tp-hotel-booking' ),
        'post_type' => 'hb_room'
    ),
    array()
)->add_field(
    array(
        'name'      => 'xxxxxxxxxxxxxxxxxxxx',
        'label'     => __( 'Number of rooms', 'tp-hotel-booking' ),
        'type'      => 'text'
    )
)->add_field(
    array(
        'name'      => 'yyyyyyyyyyyyyyyyyyyyyyy',
        'label'     => __( 'Room type', 'tp-hotel-booking' ),
        'type'      => 'select'
    )
);


HB_Meta_Box::instance(
    'room_typex',
    array(
        'title' => __( 'Room Type', 'tp-hotel-booking' ),
        'post_type' => 'post'
    ),
    array()
)->add_field(
    array(
        'name'      => 'xxxxxxxxxxxxxxxxxxxx',
        'label'     => __( 'Number of rooms', 'tp-hotel-booking' ),
        'type'      => 'text'
    )
)->add_field(
    array(
        'name'      => 'yyyyyyyyyyyyyyyyyyyyyyy',
        'label'     => __( 'Room type', 'tp-hotel-booking' ),
        'type'      => 'select'
    )
);