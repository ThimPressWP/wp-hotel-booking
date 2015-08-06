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

/**
 * Admin translation text
 * @return mixed
 */
function hb_admin_l18n(){
    $l18n = array(
        'confirm_remove_pricing_table'  => __( 'Are you sure you want to remove this pricing table?', 'tp-hotel-booking' )
    );
    return apply_filters( 'hb_admin_l18n', $l18n );
}

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
    //echo '<pre>';print_r($_POST);echo '</pre>';die();

    $adults = get_option( 'hb_taxonomy_capacity_' . $_POST['_hb_room_capacity'] );
    update_post_meta( $post_id, '_hb_max_adults_per_room', intval( $adults ) );
}
add_action( 'hb_update_meta_box_room_settings', 'hb_update_meta_box_room_settings' );

function hb_bookings_meta_boxes() {
    HB_Meta_Box::instance(
        'booking_info',
        array(
            'title'             => __('Booking Info','tp-hotel-booking'),
            'post_type'         => 'hb_booking',
            'meta_key_prefix'   => '_hb_'
        ),
        array()
    )->add_field(        
        array(
            'name'  => 'check_in_date',
            'label' => __('Check-in date', 'tp-hotel-booking'),
            'type'  => 'text',
            'std'   => ''            
        ),
        array(
            'name'  => 'checkout_out_date',
            'label' => __('Check-out date', 'tp-hotel-booking'),
            'type'  => 'text',
            'std'   => ''
        ),
        array(
            'name'  => 'aldult_per_room',
            'label' => __('Adult per room'),
            'type'  => 'number',
            'std'   => '1',
            'min'   => '1',
            'max'   => '6'
        ),
        array(
            'name'  => 'child_per_room',
            'label' => 'Child Per Room',
            'type'  => 'number',
            'std'   => '0',
            'min'   => '0',
            'max'   => '2'            
        ),
        array(
            'name'  => 'numer_ber_room',
            'label' => 'Number of room',
            'type'  => 'number',
            'std'   => '1',
            'min'   => '1',            
        ),
        array(
            'name'  => 'room_type',
            'label' => 'Room Type',
            'type'  => 'text',
            'std'   => '1',                    
        )
    );
    HB_Meta_Box::instance(
        'customer_info',
        array(
            'title'             => __('Customer Information','tp-hotel-booking'),
            'post_type'         => 'hb_booking',
            'meta_key_prefix'   => '_hb_'
        ),
        array()
    )->add_field(        
        array(
            'name'  => 'email',
            'label' => __('email', 'tp-hotel-booking'),
            'type'  => 'text',
            'std'   => ''            
        ),        
        array(
            'name'  => 'email',
            'label' => __('Email', 'tp-hotel-booking'),
            'type'  => 'text',
            'std'   => ''            
        ),
        array(
            'name'  => 'first_name',
            'label' => __('First Name', 'tp-hotel-booking'),
            'type'  => 'text',
            'std'   => ''
        ),
        array(
            'name'  => 'last_name',
            'label' => __('Last Name', 'tp-hotel-booking'),
            'type'  => 'text',
            'std'   => ''
        )
    );
}
add_action( 'init', 'hb_bookings_meta_boxes', 50 );

function hb_booking_table_head( $default ) {
    unset($default['author']);
    unset($default['date']);
    unset($default['title']);
    $default['booking_id']   = __('Booking ID', 'tp-hotel-booking');
    $default['customer_name']   = __('Customer Name', 'tp-hotel-booking');
    $default['check_in_date']   = __('Check-in Date', 'tp-hotel-booking');
    $default['check_out_date']  = __('Check-out Date', 'tp-hotel-booking');
    $default['room_type_room']  = __('Room Type/Number of Room', 'tp-hotel-booking');
    $default['booking_date']  = __('Booking Date', 'tp-hotel-booking');
    return $default;
}
add_filter('manage_hb_booking_posts_columns', 'hb_booking_table_head');


function hb_manage_booking_column( $column_name, $post_id ) {
    if ($column_name == 'booking_id') {
        $booking_id = $post_id;
        echo $booking_id;
    }
    if ($column_name == 'customer_name') {
        $customer_name = get_post_meta( $post_id, '_hb_first_name', true );
        echo $customer_name;
    }
    if ($column_name == 'check_in_date') {
        $check_in_date = get_post_meta( $post_id, '_hb_check_in_date', true );
        echo date( _x( 'F d, Y', 'Check-in date format', 'tp-hotel-booking' ), strtotime( $check_in_date ) );
    }
    if ($column_name == 'check_out_date') {
    $check_in_date = get_post_meta( $post_id, '_hb_check_out_date', true );
      echo  date( _x( 'F d, Y', 'Check-out date format', 'tp-hotel-booking' ), strtotime( $check_out_date ) );
    }
    if ($column_name == 'booking_date') {
    $booking_date = get_post_meta( $post_id, '_hb_booking_date', true );
      echo  date( _x( 'F d, Y', 'Booking date format', 'tp-hotel-booking' ), strtotime( $booking_date ) );
    }
}   
add_action('manage_hb_booking_posts_custom_columns', 'hb_manage_booking_column', 10, 2);

function hb_delete_pricing_plan( $ids ){
    global $wpdb;
    if( $ids ) {
        $query = $wpdb->prepare("
            DELETE
            FROM {$wpdb->posts}
            WHERE post_type = %s
            AND ID IN(" . ( is_array( $ids ) ? join(",", $ids) : $ids ) . ")
        ", 'hb_pricing_plan');
        $wpdb->query( $query );

        $delete_query = $wpdb->prepare("
            DELETE
            FROM {$wpdb->postmeta}
            WHERE post_id IN(%s)
        ", ( is_array( $ids ) ? join(",", $ids) : $ids ) );
        $wpdb->query( $delete_query );
    }
}
function hb_update_pricing_plan( ){
    if ( ! isset( $_POST['hb-update-pricing-plan-field'] ) || ! wp_verify_nonce( $_POST['hb-update-pricing-plan-field'], 'hb-update-pricing-plan' ) ){
        return;
    }
    if( ! empty( $_POST['price'] ) ){
        $loop = 0;
        $post_ids = array();
        foreach( $_POST['price'] as $t => $v ){
            $start  = $_POST['date-start'][ $t ];
            $end    = $_POST['date-end'][ $t ];
            $prices = $_POST['price'][ $t ];
            if( $t > 0 ){
                $post_id = intval( $t );
            }else{
                $post_id = wp_insert_post(
                    array(
                        'post_title'    => $loop == 0 ? 'Regular Price' : "Date Range[{$start} to {$end}]",
                        'post_type'     => 'hb_pricing_plan',
                        'post_status'   => 'publish'
                    )
                );
            }
            if( $post_id ){
                update_post_meta( $post_id, '_hb_pricing_plan_start', $start );
                update_post_meta( $post_id, '_hb_pricing_plan_end', $end );
                update_post_meta( $post_id, '_hb_pricing_plan_prices', $prices );
                update_post_meta( $post_id, '_hb_pricing_plan_room', $_POST['hb-room-types'] );
            }
            $post_ids[] = $post_id;
            $loop++;
        }
        $existing_ids = get_posts(
            array(
                'post_type'         => 'hb_pricing_plan',
                'posts_per_page'    => 9999,
                'fields'            => 'ids',
                'meta_query' => array(
                    array(
                        'key'     => '_hb_pricing_plan_room',
                        'value'   => $_POST['hb-room-types']
                    )
                )
            )
        );
        $delete_ids = array_diff( $existing_ids, $post_ids );
        hb_delete_pricing_plan($delete_ids);
    }
    //echo '<pre>';print_r($_POST);echo '</pre>';die();
}
add_action( 'init', 'hb_update_pricing_plan' );
