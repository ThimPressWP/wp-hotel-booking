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
        'payments'      => __( 'Payments', 'tp-hotel-booking' ),
        'lightbox'      => __( 'Lightbox', 'tp-hotel-booking' )
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

/**
 * Callback handler for Hotel Information tab content
 */
function hb_admin_settings_tab_lightbox(){
    TP_Hotel_Booking::instance()->_include( 'includes/admin/views/settings/lightbox.php' );
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
        'confirm_remove_pricing_table'  => __( 'Are you sure you want to remove this pricing table?', 'tp-hotel-booking' ),
        'empty_pricing_plan_start_date' => __( 'Select start date for plan', 'tp-hotel-booking'),
        'empty_pricing_plan_start_end' => __( 'Select end date for plan', 'tp-hotel-booking'),
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
        ),
        array(
            'name'      => 'room_addition_information',
            'label'     => __( 'Addition Information', 'tp-hotel-booking' ),
            'type'      => 'textarea',
            'std'       => '',
            'editor'    => true
        )
    );
}
add_action( 'init', 'hb_add_meta_boxes', 50 );

function hb_update_meta_box_room_settings( $post_id ){
    wp_set_object_terms( $post_id, intval( $_POST['_hb_room_type'] ), 'hb_room_type' );
    //wp_set_object_terms( $post_id, intval( $_POST['room_capacity'] ), 'hb_room_capacity' );
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
            'type'  => 'label',
            'std'   => '',
            'attr'  => 'readonly="readonly"'
        ),        
        array(
            'name'  => 'check_in_date',
            'label' => __('Check-in date', 'tp-hotel-booking'),
            'type'  => 'label',
            'std'   => '',
            'attr'  => 'readonly="readonly"',
            'filter' => 'hb_meta_box_field_datetime'
        ),
        array(
            'name'  => 'check_out_date',
            'label' => __('Check-out date', 'tp-hotel-booking'),
            'type'  => 'label',
            'std'   => '',
            'attr'  => 'readonly="readonly"',
            'filter' => 'hb_meta_box_field_datetime'
        ),
        array(
            'name'  => 'total_nights',
            'label' => __('Total Nights', 'tp-hotel-booking'),
            'type'  => 'label',
            'std'   => '1',
            'attr'  => 'readonly="readonly"'
        ),
        array(
            'name'  => 'tax',
            'label' => __('Tax', 'tp-hotel-booking'),
            'type'  => 'label',
            'std'   => '0',
            'attr'  => 'readonly="readonly"',
            'filter' => 'hb_meta_box_field_tax'
        ),        
        array(
            'name'  => 'price_including_tax',
            'label' => __('Price Including Tax', 'tp-hotel-booking'),
            'type'  => 'label',
            'std'   => '0',
            'attr'  => 'readonly="readonly"',
            'filter' => 'hb_meta_box_field_price_including_tax'
        ),

        array(
            'name'  => 'sub_total',
            'label' => __('Sub Total', 'tp-hotel-booking'),
            'type'  => 'label',
            'std'   => '',
            'attr'  => 'readonly="readonly"',
            'filter' => 'hb_meta_box_field_sub_total'
        ),
        array(
            'name'  => 'total',
            'label' => __('Total', 'tp-hotel-booking'),
            'type'  => 'label',
            'std'   => '0',
            'attr'  => 'readonly="readonly"',
            'filter' => 'hb_meta_box_field_total'
        ),
        /*array(
            'name'  => 'room_id',
            'label' => 'Room ID',
            'type'  => 'multiple',
            'std'   => '1',                    
        ),*/
        array(
            'name'  => 'booking_status',
            'label' => 'Status',
            'std'   => 'Actives',
            'type'  => 'select',
            'options' => array(
                array(
                    'value' => 'Pending',
                    'text'  => __( 'Pending', 'tp-hotel-booking' )
                ),
                array(
                    'value' => 'Complete',
                    'text'  => __( 'Complete', 'tp-hotel-booking' )
                )
            )
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
            'type'  => 'country',
        ),
        array(
            'name'  => 'phone',
            'label' => __('Phone', 'tp-hotel-booking'),
            'type'  => 'text'                    
        ),
        array(
            'name'  => 'email',
            'label' => __('Email', 'tp-hotel-booking'),
            'type'  => 'text',
            'attr'  => 'readonly="readonly"'
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
    $default['customer_name']   = __( 'Customer Name', 'tp-hotel-booking' );

    //$default['check_in_date']   = __( 'Check-in Date', 'tp-hotel-booking' );
    //$default['check_out_date']  = __( 'Check-out Date', 'tp-hotel-booking' );
    $default['from_to']         = __( 'Check In/Out', 'tp-hotel-booking' );
    $default['booking_date']    = __( 'Booking Date', 'tp-hotel-booking' );
    $default['total']           = __( 'Total', 'tp-hotel-booking' );
    $default['title']           = __( 'ID', 'tp-hotel-booking' );
    $default['details']         = __( 'View Details', 'tp-hotel-booking' );
    return $default;
}
add_filter('manage_hb_booking_posts_columns', 'hb_booking_table_head');


/**
 * Retrieve information for listing in booking list
 * 
 * @param  string
 * @param  int
 * @return mixed
 */
function hb_manage_booking_column( $column_name, $post_id ) {    
    switch ( $column_name ){
        case 'booking_id':
            $booking_id = $post_id;
            echo hb_format_order_number( $booking_id );
            break;
        case 'customer_name':
            $customer_id = get_post_meta( $post_id, '_hb_customer_id', true );
            $title = hb_get_title_by_slug( get_post_meta( $customer_id, '_hb_title', true ) );
            $first_name = get_post_meta( $customer_id, '_hb_first_name', true );
            $last_name = get_post_meta( $customer_id, '_hb_last_name', true );
            printf( '%s %s %s', $title ? $title : 'Cus.', $first_name, $last_name );
            break;
        case 'from_to':
            $check_in_date = get_post_meta( $post_id, '_hb_check_in_date', true );
            $check_in_date = date( _x( 'm/d/Y', 'Check-in date format', 'tp-hotel-booking' ), $check_in_date );

            $check_out_date = get_post_meta( $post_id, '_hb_check_out_date', true );
            $check_out_date = date( _x( 'm/d/Y', 'Check-out date format', 'tp-hotel-booking' ), $check_out_date );
            printf( __( 'From <strong>%s</strong><br />To <strong>%s</strong>', 'tp-hotel-booking' ), $check_in_date, $check_out_date );
            break;
        case 'total':
            $total      = get_post_meta( $post_id, '_hb_total', true );
            $currency   = get_post_meta( $post_id, '_hb_currency', true );
            $total_with_currency = hb_format_price( $total, hb_get_currency_symbol( $currency ) );
            echo $total_with_currency;
            do_action( 'hb_manage_booing_column_total', $post_id, $total, $total_with_currency );
            break;
        case 'booking_date':
            echo date( 'F d, Y', strtotime( get_post_field( 'post_date', $post_id ) ) );
            break;
        case 'details':
            $status = get_post_meta( $post_id, '_hb_booking_status', true );
            echo '<a href="'. admin_url('admin.php?page=hb_booking_details&id='. $post_id) . '">' . sprintf( __( 'View (%s)', 'tp-hotel-booking' ), $status ) . '</a>';
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
        $from           = hb_get_request( 'date-from' );
        $to             = hb_get_request( 'date-to' );
        $filter_type    = hb_get_request( 'filter-type' );

        $filter_types = apply_filters(
            'hb_booking_filter_types',
            array(
                'booking-date'      => __( 'Booking date', 'tp-hotel-booking' ),
                'check-in-date'     => __( 'Check-in date', 'tp-hotel-booking' ),
                'check-out-date'    => __( 'Check-out date', 'tp-hotel-booking' )
            )
        );
        ?>
        <input type="text" id="hb-booking-date-from" class="hb-date-field" value="<?php echo $from;?>" name="date-from" readonly placeholder="<?php _e( 'From', 'tp-hotel-booking' );?>" />
        <input type="text" id="hb-booking-date-to" class="hb-date-field" value="<?php echo $to;?>" name="date-to" readonly placeholder="<?php _e( 'To', 'tp-hotel-booking' );?>" />
        <select name="filter-type">
            <option value=""><?php _e( '---Filter By---', 'tp-hotel-booking' );?></option>
            <?php foreach( $filter_types as $slug => $text ){?>
            <option value="<?php echo $slug;?>" <?php selected( $slug == $filter_type );?>><?php echo $text;?></option>
            <?php }?>
        </select>
        <?php
        return;
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
        //$query->query_vars['meta_key'] = '_hb_check_out_date';
        //$query->query_vars['meta_value'] = $_GET['filter_by_checkout_date'];
    }
}


function hb_customer_posts_columns( $default ) {
    unset($default['author']);
    unset($default['date']);
    $default['customer_address']    = __( 'Address', 'tp-hotel-booking' );
    $default['phone_number']        = __( 'Phone Number', 'tp-hotel-booking' );
    $default['email']               = __( 'Email', 'tp-hotel-booking' );
    $default['bookings']            = __( 'Bookings', 'tp-hotel-booking' );
    $default['title']               = __( 'Cus. Name', 'tp-hotel-booking' );
    return $default;
}
add_filter('manage_hb_customer_posts_columns', 'hb_customer_posts_columns');

function hb_edit_post_change_title_in_list() {
    add_filter( 'the_title', 'hb_edit_post_new_title_in_list', 100, 2 );
}
add_action( 'admin_head-edit.php', 'hb_edit_post_change_title_in_list' );

function hb_edit_post_new_title_in_list( $title, $post_id ){
    global $post_type;
    if( $post_type == 'hb_customer' ) {
        $title = hb_get_title_by_slug(get_post_meta($post_id, '_hb_title', true));
        $first_name = get_post_meta($post_id, '_hb_first_name', true);
        $last_name = get_post_meta($post_id, '_hb_last_name', true);
        $customer_name = sprintf('%s %s %s', $title ? $title : 'Cus.', $first_name, $last_name);
        $title = $customer_name;
    }elseif( $post_type == 'hb_booking' ) {
        $title = hb_format_order_number( $post_id );
    }
    return $title;
}

function hb_manage_customer_column( $column_name, $post_id ) {        
    switch ( $column_name ){
        case 'customer_name':
            $title = hb_get_title_by_slug ( get_post_meta( $post_id, '_hb_title', true ) );
            $first_name =  get_post_meta( $post_id, '_hb_first_name', true );
            $last_name = get_post_meta( $post_id, '_hb_last_name', true );
            $customer_name = sprintf( '%s %s %s', $title ? $title : 'Cus.', $first_name, $last_name );
            echo $customer_name;
            break;
        case 'customer_address':
            $customer_address = get_post_meta( $post_id, '_hb_address', true );
            echo $customer_address;
            break;
        case 'phone_number':
            $phone = get_post_meta( $post_id, '_hb_phone', true );
            echo $phone;
            break;
        case 'email':
            $email = get_post_meta( $post_id, '_hb_email', true );
            echo $email;
            break;
        case 'bookings':
            printf(
                '<a href="%sedit.php?post_type=hb_booking&customer_id=%d">%s</a>',
                get_admin_url(),
                $post_id,
                __( 'View Booking', 'tp-hotel-booking' )
            );
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

function hb_admin_js_template(){
?>
<script type="text/html" id="tmpl-room-type-gallery">
<tr id="room-gallery-{{data.id}}" class="room-gallery">
    <td colspan="{{data.colspan}}">
        <div class="hb-room-gallery">
            <ul>
                <# jQuery.each(data.gallery, function(){ var attachment = this;#>
                    <li class="attachment">
                        <div class="attachment-preview">
                            <div class="thumbnail">
                                <div class="centered">
                                    <img src="{{attachment.src}}" alt="">
                                    <input type="hidden" name="hb-gallery[{{data.id}}][gallery][]" value="{{attachment.id}}" />
                                </div>
                            </div>
                        </div>
                        <a class="dashicons dashicons-trash" title="<?php _e( 'Remove this image', 'tp-hotel-booking' );?>"></a>
                    </li>
                <# }); #>
                <li class="attachment add-new">
                    <div class="attachment-preview">
                        <div class="thumbnail">
                            <div class="dashicons-plus dashicons">
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <input type="hidden" name="hb-gallery[{{data.id}}][id]" value="{{data.id}}" />
    </td>
</tr>
</script>
<script type="text/html" id="tmpl-room-type-attachment">
    <li class="attachment">
        <div class="attachment-preview">
            <div class="thumbnail">
                <div class="centered">
                    <img src="{{data.src}}" alt="">
                    <input type="hidden" name="hb-gallery[{{data.gallery_id}}][gallery][]" value="{{data.id}}" />
                </div>
            </div>
        </div>
        <a class="dashicons dashicons-trash" title="<?php _e( 'Remove this image', 'tp-hotel-booking' );?>"></a>
    </li>
</script>
<?php
}
add_action( 'admin_print_scripts', 'hb_admin_js_template');


function hb_booking_detail_page_register() {

}
add_action( 'admin_menu', 'hb_booking_detail_page_register' );

function hb_booking_detail_page() {
    if( is_admin() && hb_get_request( 'page' ) == 'hb_booking_details' ) {
        TP_Hotel_Booking::instance()->_include( 'includes/admin/views/booking-details.php' );
    }
}

function hb_meta_box_field_datetime( $value ){
    return date( 'l, m/d/Y', $value );
}
function hb_meta_box_field_tax( $value ){

    return ( $value * 100 ) . '%';
}
function hb_meta_box_field_sub_total( $value ){
    return hb_format_price( $value );
}
function hb_meta_box_field_total( $value ){
    return hb_format_price( $value );
}
function hb_meta_box_field_price_including_tax( $value ){
    return $value == 'yes' ? __( 'Yes', 'tp-hotel-booking' ) : __( 'No', 'tp-hotel-booking' ) ;
}