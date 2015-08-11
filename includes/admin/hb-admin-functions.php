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
            'name'  => 'customer_id',
            'label' => __('Customer ID', 'tp-hotel-booking'),
            'type'  => 'text',
            'std'   => ''            
        ),        
        array(
            'name'  => 'check_in_date',
            'label' => __('Check-in date', 'tp-hotel-booking'),
            'type'  => 'datetime',
            'std'   => ''            
        ),
        array(
            'name'  => 'check_out_date',
            'label' => __('Check-out date', 'tp-hotel-booking'),
            'type'  => 'datetime',
            'std'   => ''
        ),
        array(
            'name'  => 'total_nights',
            'label' => __('Total Nights', 'tp-hotel-booking'),
            'type'  => 'number',
            'std'   => '1'
        ),
        array(
            'name'  => 'tax',
            'label' => __('Tax', 'tp-hotel-booking'),
            'type'  => 'number',
            'std'   => '0'
        ),        
        array(
            'name'  => 'price_including_tax',
            'label' => __('Price Including Tax', 'tp-hotel-booking'),
            'type'  => 'number',
            'std'   => '0'
        ),
        array(
            'name'  => 'total',
            'label' => __('Total', 'tp-hotel-booking'),
            'type'  => 'number',
            'std'   => '0'
        ),
        array(
            'name'  => 'sub_total',
            'label' => __('Sub Total', 'tp-hotel-booking'),
            'type'  => 'number',
            'std'   => ''
        ),
        array(
            'name'  => 'room_id',
            'label' => 'Room ID',
            'type'  => 'multiple',
            'std'   => '1',                    
        ),
        array(
            'name'  => 'booking_status',
            'label' => 'Status',
            'std'   => '',
            'type'  => 'text'
        )
    );    
}
add_action( 'init', 'hb_bookings_meta_boxes', 50 );

function hb_customer_meta_box() {
    HB_Meta_Box::instance(
        'customer_info',
        array(
            'title'             => __('Customer Info','tp-hotel-booking'),
            'post_type'         => 'hb_customer',
            'meta_key_prefix'   => '_hb_'
        ),
        array()
    )->add_field(        
        array(
            'name'      => 'title',
            'label'     => __('Title', 'tp-hotel-booking'),
            'type'      => 'select',
            'options'   => apply_filters( 'hb_customer_titles', array(
                            array(
                                'value' => 'mr',
                                'text'    => __( 'Mr.', 'tp-hotel-booking' )
                            ),
                            array(
                                'value' => 'ms',
                                'text'    => __( 'Ms.', 'tp-hotel-booking' )
                            ),
                            array(
                                'value' => 'mrs',
                                'text'    => __( 'Mrs.', 'tp-hotel-booking' )
                            ),
                            array(
                                'value' => 'miss',
                                'text'    => __( 'Miss.', 'tp-hotel-booking' )
                            ),
                            array(
                                'value' => 'dr',
                                'text'    => __( 'Dr.', 'tp-hotel-booking' )
                            ),
                            array(
                                'value' => 'prof',
                                'text'    => __( 'Prof.', 'tp-hotel-booking' )
                            )                            
                        )
                    )
        ),
        array(
            'name'  => 'first_name',
            'label' => __('First Name', 'tp-hotel-booking'),
            'type'  => 'text'                    
        ),
        array(
            'name'  => 'last_name',
            'label' => __('Last Name', 'tp-hotel-booking'),
            'type'  => 'text'                    
        ),
        array(
            'name'  => 'address',
            'label' => __('Address', 'tp-hotel-booking'),
            'type'  => 'text'                    
        ),
        array(
            'name'  => 'city',
            'label' => __('City', 'tp-hotel-booking'),
            'type'  => 'text'                    
        ),
        array(
            'name'  => 'state',
            'label' => __('State', 'tp-hotel-booking'),
            'type'  => 'text'                    
        ),
        array(
            'name'  => 'postal_code',
            'label' => __('Postal Code', 'tp-hotel-booking'),
            'type'  => 'text'                    
        ),
        array(
            'name'  => 'country',
            'label' => __('Country', 'tp-hotel-booking'),
            'type'  => 'text'                    
        ),
        array(
            'name'  => 'phone',
            'label' => __('Phone', 'tp-hotel-booking'),
            'type'  => 'text'                    
        ),
        array(
            'name'  => 'email',
            'label' => __('Email', 'tp-hotel-booking'),
            'type'  => 'text'                    
        ),
        array(
            'name'  => 'fax',
            'label' => __('Fax', 'tp-hotel-booking'),
            'type'  => 'text'                    
        )
    );
}
add_action( 'init', 'hb_customer_meta_box', 50 );

/**
 * Custom booking list in admin
 *
 * 
 * @param  [type] $default 
 * @return [type]          
 */
function hb_booking_table_head( $default ) {
    unset($default['author']);
    unset($default['date']);
    // unset($default['title']);
    $default['booking_id']   = __('Booking ID', 'tp-hotel-booking');
    $default['customer_name']   = __('Customer Name', 'tp-hotel-booking');
    $default['check_in_date']   = __('Check-in Date', 'tp-hotel-booking');
    $default['check_out_date']  = __('Check-out Date', 'tp-hotel-booking');
    $default['room_type_room']  = __('Room Type/Number of Room', 'tp-hotel-booking');
    $default['date']  = __('Booking Date', 'tp-hotel-booking');
    $default['total']  = __('Total', 'tp-hotel-booking');    
    return $default;
}
add_filter('manage_hb_booking_posts_columns', 'hb_booking_table_head');


/**
 * Retrieve information for listing in booking list
 * 
 * @param  [type] $column_name [description]
 * @param  [type] $post_id     [description]
 * @return [type]              [description]
 */
function hb_manage_booking_column( $column_name, $post_id ) {    
    if ($column_name == 'booking_id') {
        $booking_id = $post_id;
        echo $booking_id;        
    }
    if ($column_name == 'customer_name') {
        $customer_id = get_post_meta( $post_id, '_hb_customer_id', true );
        $customer_name = get_post_meta( $customer_id, '_hb_title', true ) . get_post_meta( $customer_id, '_hb_first_name', true ) . ' ' . get_post_meta( $customer_id, '_hb_last_name', true );      
        echo $customer_name;
    }
    if ($column_name == 'check_in_date') {
        $check_in_date = get_post_meta( $post_id, '_hb_check_in_date', true );
        echo date( _x( 'F d, Y', 'Check-in date format', 'tp-hotel-booking' ), strtotime( $check_in_date ) );
    }
    if ($column_name == 'check_out_date') {
        $check_out_date = get_post_meta( $post_id, '_hb_check_out_date', true );
        echo  date( _x( 'F d, Y', 'Check-out date format', 'tp-hotel-booking' ), strtotime( $check_out_date ) );
    }    
    if ($column_name == 'total') {
        $total = get_post_meta( $post_id, '_hb_total', true );
        echo  $total;
    }
}   
add_action('manage_hb_booking_posts_custom_column', 'hb_manage_booking_column', 10, 2);


add_action( 'restrict_manage_posts', 'hb_booking_restrict_manage_posts' );
/**
 * First create the dropdown
 * 
 * @return void
 */
function hb_booking_restrict_manage_posts(){
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    //only add filter to post type you want
    if ('hb_booking' == $type){
        //change this to the list of values you want to show
        //in 'label' => 'value' format
        $values = array(
            'Today check-in' => date('m/d/Y'),            
        );
        ?>
        <select name="filter_by_checkin_date">
        <option value=""><?php _e('All Check-in Date ', 'tp-hotel-booking'); ?></option>
        <?php
            $current_v = isset($_GET['filter_by_checkin_date'])? $_GET['filter_by_checkin_date']:'';
            foreach ($values as $label => $value) {
                printf
                    (
                        '<option value="%s"%s>%s</option>',
                        $value,
                        $value == $current_v? ' selected="selected"':'',
                        $label
                    );
                }
        ?>
        </select>        
        <?php
        $values = array(
            'Today check-out' => date('m/d/Y'),            
        );
        ?>
        <select name="filter_by_checkout_date">
        <option value=""><?php _e('All Check-out Date ', 'tp-hotel-booking'); ?></option>
        <?php
            $current_v = isset($_GET['filter_by_checkout_date'])? $_GET['filter_by_checkout_date']:'';
            foreach ($values as $label => $value) {
                printf
                    (
                        '<option value="%s"%s>%s</option>',
                        $value,
                        $value == $current_v? ' selected="selected"':'',
                        $label
                    );
                }
        ?>
        </select>
        <?php
    }
}


add_filter( 'parse_query', 'hb_booking_filter' );
/**
 * if submitted filter by post meta
 *  
 * @param  (wp_query object) $query
 * 
 * @return Void
 */
function hb_booking_filter( $query ){
    global $pagenow;
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }
    if ( 'hb_booking' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['filter_by_checkin_date']) && $_GET['filter_by_checkin_date'] != '') {
        $query->query_vars['meta_key'] = '_hb_check_in_date';
        $query->query_vars['meta_value'] = $_GET['filter_by_checkin_date'];
    }
    if ( 'hb_booking' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['filter_by_checkout_date']) && $_GET['filter_by_checkout_date'] != '') {
        $query->query_vars['meta_key'] = '_hb_check_out_date';
        $query->query_vars['meta_value'] = $_GET['filter_by_checkout_date'];
    }
}


function hb_customer_posts_columns( $default ) {
    unset($default['author']);
    unset($default['date']);
    // unset($default['title']);    
    $default['customer_name']   = __('Customer Name', 'tp-hotel-booking');
    $default['customer_address']   = __('Custormer Address', 'tp-hotel-booking');
    $default['phone_number']  = __('Phone Number', 'tp-hotel-booking');
    $default['email']  = __('Email', 'tp-hotel-booking');    
    $default['booking']  = __('Booking', 'tp-hotel-booking');
    $default['booking_date']  = __('Booking Date', 'tp-hotel-booking');
    return $default;
}
add_filter('manage_hb_customer_posts_columns', 'hb_customer_posts_columns');


function hb_manage_customer_column( $column_name, $post_id ) {        
    if ($column_name == 'customer_name') {
        $customer_name = get_post_meta( $post_id, '_hb_title', true ) . get_post_meta( $post_id, '_hb_first_name', true ) . ' ' . get_post_meta( $post_id, '_hb_last_name', true );
        echo $customer_name;
    }
    if ($column_name == 'customer_address') {
        $customer_address = get_post_meta( $post_id, '_hb_address', true );
        echo $customer_address;
    }
    if ($column_name == 'phone_number') {
        $phone = get_post_meta( $post_id, '_hb_phone', true );
        echo $phone;
    }
    if ($column_name == 'email') {
        $email = get_post_meta( $post_id, '_hb_email', true );
        echo $email;
    }
    if ($column_name == 'booking') {
        // global $wpdb;
        // $query = $wpdb->prepare("
        //         SELECT ID
        //         FROM {$wpdb->posts} as p1
        //         LEFT JOIN {$wpdb->postmeta} as p2
        //         ON p1.ID = p2._hb_customer_id
        //         WHERE p2._hb_customer_id = %s
        //     ", $post_id);
        // $booking = $wpdb->query( $query );
        echo  '<a href=' . get_admin_url() . 'edit.php?post_type=hb_booking&customer_id='. $post_id . '>View Booking</a>';
    }
}   
add_action('manage_hb_customer_posts_custom_column', 'hb_manage_customer_column', 10, 2);


add_filter( 'parse_query', 'hb_booking_custormer_filter' );
/**
 * if submitted filter by post meta
 *  
 * @param  (wp_query object) $query
 * 
 * @return Void
 */
function hb_booking_custormer_filter( $query ){
    global $pagenow;    
    if ( isset( $_GET['post_type']) && 'hb_booking' == $_GET['post_type'] && is_admin() && $pagenow=='edit.php' && isset($_GET['customer_id']) && $_GET['customer_id'] != '') {
        $query->query_vars['meta_key'] = '_hb_customer_id';
        $query->query_vars['meta_value'] = $_GET['customer_id'];
    }    
}

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
