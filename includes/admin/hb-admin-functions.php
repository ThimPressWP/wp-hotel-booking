<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

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
        'emails'        => __( 'Emails', 'tp-hotel-booking' ),
        'lightbox'      => __( 'Lightbox', 'tp-hotel-booking' ),
        'room'          => __( 'Room', 'tp-hotel-booking' )
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

/**
 * Callback handler for Emails tab content
 */
function hb_admin_settings_tab_emails(){
    TP_Hotel_Booking::instance()->_include( 'includes/admin/views/settings/emails.php' );
}

/**
 * @param $selected
 */
function hb_admin_settings_tab_content( $selected ){
    if( is_callable( "hb_admin_settings_tab_{$selected}" ) ) {
        call_user_func_array( "hb_admin_settings_tab_{$selected}", array() );
    }
}
add_action( 'hb_admin_settings_tab_before', 'hb_admin_settings_tab_content' );

function hb_admin_settings_tab_email_general(){
    TP_Hotel_Booking::instance()->_include( 'includes/admin/views/settings/email-general.php' );
}
add_action( 'hb_email_general_settings', 'hb_admin_settings_tab_email_general' );

function hb_admin_settings_tab_email_new_booking(){
    TP_Hotel_Booking::instance()->_include( 'includes/admin/views/settings/email-new-booking.php' );
}
add_action( 'hb_email_new_booking_settings', 'hb_admin_settings_tab_email_new_booking' );

function hb_admin_settings_tab_room(){
    TP_Hotel_Booking::instance()->_include( 'includes/admin/views/settings/room.php' );
}
add_action( 'hb_email_new_booking_settings', 'hb_admin_settings_tab_email_new_booking' );

/**
 * Admin translation text
 * @return mixed
 */
function hb_admin_l18n(){
    $l18n = array(
        'confirm_remove_pricing_table'  => __( 'Are you sure you want to remove this pricing table?', 'tp-hotel-booking' ),
        'empty_pricing_plan_start_date' => __( 'Select start date for plan', 'tp-hotel-booking'),
        'empty_pricing_plan_start_end'  => __( 'Select end date for plan', 'tp-hotel-booking'),
        'filter_error'                  => __( 'Please select date range and filter type', 'tp-hotel-booking' ),
        'date_time_format'              => hb_date_time_format_js(),
        'monthNames'                    => hb_month_name_js(),
        'monthNamesShort'               => hb_month_name_short_js()
    );
    return apply_filters( 'hb_admin_l18n', $l18n );
}

function hb_add_meta_boxes(){
    HB_Meta_Box::instance(
        'room_settings',
        array(
            'title'             => __( 'Room Settings', 'tp-hotel-booking' ),
            'post_type'         => 'hb_room',
            'meta_key_prefix'   => '_hb_',
            'priority'          => 'high'
        ),
        array()
    )->add_field(
        array(
            'name'      => 'num_of_rooms',
            'label'     => __( 'Quantity', 'tp-hotel-booking' ),
            'type'      => 'number',
            'std'       => '100',
            'desc'      => __( 'The number of rooms', 'tp-hotel-booking' ),
            'min'       => 1,
            'max'       => 100
        ),
        array(
            'name'      => 'room_capacity',
            'label'     => __( 'Number of adults', 'tp-hotel-booking' ),
            'type'      => 'select',
            'options'   => hb_get_room_capacities(
                array(
                    'map_fields' => array(
                        'term_id'   => 'value',
                        'name'      => 'text'
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

    // coupon meta box
    HB_Meta_Box::instance(
        'coupon_settings',
        array(
            'title'             => __( 'Coupon Settings', 'tp-hotel-booking' ),
            'post_type'         => 'hb_coupon',
            'meta_key_prefix'   => '_hb_',
            'context'           => 'normal',
            'priority'          => 'high'
        ),
        array()
    )->add_field(
        array(
            'name'      => 'coupon_description',
            'label'     => __( 'Description', 'tp-hotel-booking' ),
            'type'      => 'textarea',
            'std'       => ''
        ),
        array(
            'name'      => 'coupon_discount_type',
            'label'     => __( 'Discount type', 'tp-hotel-booking' ),
            'type'      => 'select',
            'std'       => '',
            'options'   => array(
                'fixed_cart' => __( 'Cart discount', 'tp-hotel-booking' ),
                'percent_cart' => __( 'Cart % discount', 'tp-hotel-booking' )
            )
        ),
        array(
            'name'      => 'coupon_discount_value',
            'label'     => __( 'Discount value', 'tp-hotel-booking' ),
            'type'      => 'number',
            'std'       => '',
            'min'       => 0,
            'step'      => 0.1
        ),
        array(
            'name'      => 'coupon_date_from',
            'label'     => __( 'Validate from', 'tp-hotel-booking' ),
            'type'      => 'datetime',
            'filter' => 'hb_meta_box_field_coupon_date'
        ),
        array(
            'name'      => 'coupon_date_from_timestamp',
            'label'     => '',
            'type'      => 'hidden'
        ),
        array(
            'name'      => 'coupon_date_to',
            'label'     => __( 'Validate until', 'tp-hotel-booking' ),
            'type'      => 'datetime',
            'filter' => 'hb_meta_box_field_coupon_date'
        ),
        array(
            'name'      => 'coupon_date_to_timestamp',
            'label'     => '',
            'type'      => 'hidden'
        ),
        array(
            'name'      => 'minimum_spend',
            'label'     => __( 'Minimum spend', 'tp-hotel-booking' ),
            'type'      => 'number',
            'desc'      => __( 'This field allows you to set the minimum subtotal needed to use the coupon.', 'tp-hotel-booking' ),
            'min'       => 0,
            'step'      => 0.1
        ),
        array(
            'name'      => 'maximum_spend',
            'label'     => __( 'Maximum spend', 'tp-hotel-booking' ),
            'type'      => 'number',
            'desc'      => __( 'This field allows you to set the maximum subtotal allowed when using the coupon.', 'tp-hotel-booking' ),
            'min'       => 0,
            'step'      => 0.1
        ),
        array(
            'name'      => 'limit_per_coupon',
            'label'     => __( 'Usage limit per coupon', 'tp-hotel-booking' ),
            'type'      => 'number',
            'desc'      => __( 'How many times this coupon can be used before it is void.', 'tp-hotel-booking' ),
            'min'       => 0
        ),
        array(
            'name'      => 'used',
            'label'     => __( 'Used', 'tp-hotel-booking' ),
            'type'      => 'label',
            'filter'    => 'hb_meta_box_field_coupon_used'
        )
    );

    HB_Meta_Box::instance(
        'gallery_settings',
        array(
            'title'             => __( 'Gallery Settings', 'tp-hotel-booking' ),
            'post_type'         => 'hb_room',
            'meta_key_prefix'   => '_hb_', // meta key prefix,
            'priority'          => 'high'
            // 'callback'  => 'hb_add_meta_boxes_gallery_setings' // callback arg render meta form
        ),
        array()
    )->add_field(
        array(
            'name'      => 'gallery',
            'type'      => 'gallery'
        )
    );
}
add_action( 'init', 'hb_add_meta_boxes', 50 );

function hb_meta_box_coupon_settings_update_meta_value( $meta_value, $field_name, $meta_box_name, $post_id  ){
    if( $field_name == 'booking_status' ){
        hb_update_booking_status( $post_id, $meta_value );
    }
    return $meta_value;
}
add_filter( 'hb_meta_box_update_meta_value', 'hb_meta_box_coupon_settings_update_meta_value', 10, 4 );

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
        array(
            'name'  => 'booking_status',
            'label' => 'Status',
            'std'   => 'hb-pending',
            'type'  => 'select',
            'filter' => 'hb_meta_box_field_booking_status',
            'options' => hb_get_booking_statuses()
        ),
        array(
            'name'  => 'advance_payment',
            'label' => 'Advance Payment',
            'std'   => '',
            'type'  => 'label',
            'filter' => 'hb_meta_box_field_sub_total'
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
    $default['booking_date']    = __( 'Booking Date', 'tp-hotel-booking' );
    $default['check_in_date']    = __( 'Check in', 'tp-hotel-booking' );
    $default['check_out_date']    = __( 'Check out', 'tp-hotel-booking' );
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
    $booking = HB_Booking::instance( $post_id );
    $echo = array();
    $status = get_post_status( $post_id );
    switch ( $column_name ){
        case 'booking_id':
            $echo[] = hb_format_order_number( $post_id );
            break;
        case 'customer_name':
            $customer_id = $booking->customer_id;
            $echo[] = hb_get_customer_fullname( $customer_id, true);
            break;
        case 'total':
            global $hb_settings;
            $total      = $booking->total;
            $currency   = $booking->payment_currency;
            if( ! $currency ) {
                $currency = $booking->currency;
            }
            $total_with_currency = hb_format_price( $total, hb_get_currency_symbol( $currency ) );

            $echo[] = $total_with_currency;
            if( $method = hb_get_user_payment_method( $booking->method ) ) {
                $echo[] = sprintf( __( '<br />(<small>%s</small>)', 'tp-hotel-booking' ), $method->description );
            }
            // display paid
            if( $status === 'hb-processing' )
            {
                $advance_payment =  $booking->advance_payment;
                $advance_settings = $booking->advance_payment_setting;
                if( ! $advance_settings ) {
                    $advance_settings = $hb_settings->get( 'advance_payment', 50 );
                }

                if ( floatval($total) !== floatval( $advance_payment ) ) {
                    $echo[] = sprintf(
                        __( '<br />(<small class="hb_advance_payment">Charged %s = %s</small>)', 'tp-hotel-booking' ),
                        $advance_settings . '%',
                        hb_format_price( $advance_payment, hb_get_currency_symbol( $currency ) )
                    );
                }
            }
            // end display paid
            do_action( 'hb_manage_booing_column_total', $post_id, $total, $total_with_currency );
            break;
        case 'booking_date':
            echo date( hb_get_date_format(), strtotime( get_post_field( 'post_date', $post_id ) ) );
            break;
        case 'check_in_date':
            if ( $booking->check_in_date ) {
                echo date( hb_get_date_format(), $booking->check_in_date );
            }
            break;
        case 'check_out_date':
            if( $booking->check_out_date ) {
                echo date( hb_get_date_format(), $booking->check_out_date );
            }
            break;
        case 'details':
            $echo[] = '<a href="'. admin_url('admin.php?page=hb_booking_details&id='. $post_id) . '">' . __( 'View', 'tp-hotel-booking' ) . '</a><br />';
            $echo[] = '<span class="hb-booking-status ' . $status . '">' . hb_get_booking_status_label( $post_id ) . '</span>';
    }
    echo apply_filters( 'hotel_booking_booking_total', sprintf( '%s', implode('', $echo) ), $column_name, $post_id );
}
add_action('manage_hb_booking_posts_custom_column', 'hb_manage_booking_column', 10, 2);

function hb_request_query( $vars = array() ){
    global $typenow, $wp_query, $wp_post_statuses;

    if ( 'hb_booking' === $typenow ) {
        // Status
        if ( ! isset( $vars['post_status'] ) ) {
            $post_statuses = hb_get_booking_statuses();

            foreach ( $post_statuses as $status => $value ) {
                if ( isset( $wp_post_statuses[ $status ] ) && false === $wp_post_statuses[ $status ]->show_in_admin_all_list ) {
                    unset( $post_statuses[ $status ] );
                }
            }

            $vars['post_status'] = array_keys( $post_statuses );
        }
    }
    return $vars;
}
add_filter( 'request', 'hb_request_query' );

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
        $from           = hb_get_request( 'date-from' );
        $from_timestamp = hb_get_request( 'date-from-timestamp' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
        $to             = hb_get_request( 'date-to' );
        $to_timestamp   = hb_get_request( 'date-to-timestamp' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
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
        <span><?php _e( 'Date Range', 'tp-hotel-booking' ); ?></span>
        <input type="text" id="hb-booking-date-from" class="hb-date-field" value="<?php echo esc_attr( $from ); ?>" name="date-from" readonly placeholder="<?php _e( 'From', 'tp-hotel-booking' ); ?>" />
        <input type="hidden" value="<?php echo esc_attr( $from_timestamp ); ?>" name="date-from-timestamp" />
        <input type="text" id="hb-booking-date-to" class="hb-date-field" value="<?php echo esc_attr( $to ); ?>" name="date-to" readonly placeholder="<?php _e( 'To', 'tp-hotel-booking' ); ?>" />
        <input type="hidden" value="<?php echo esc_attr( $to_timestamp ); ?>" name="date-to-timestamp" />
        <select name="filter-type">
            <option value=""><?php _e( '---Filter By---', 'tp-hotel-booking' ); ?></option>
            <?php foreach( $filter_types as $slug => $text ){?>
            <option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $slug == $filter_type ); ?>><?php echo esc_html( $text ); ?></option>
            <?php } ?>
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
        $type = sanitize_text_field( $_GET['post_type'] );
    }
    if ( 'hb_booking' == $type && is_admin() && $pagenow =='edit.php' && isset($_GET['filter_by_checkin_date']) && $_GET['filter_by_checkin_date'] != '') {
        $query->query_vars['meta_key'] = '_hb_check_in_date';
        $query->query_vars['meta_value'] = sanitize_text_field( $_GET['filter_by_checkin_date'] );
    }
    if ( 'hb_booking' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['filter_by_checkout_date']) && $_GET['filter_by_checkout_date'] != '') {
        //$query->query_vars['meta_key'] = '_hb_check_out_date';
        //$query->query_vars['meta_value'] = sanitize_text_field( $_GET['filter_by_checkout_date'] );
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
            echo esc_html( $customer_name );
            break;
        case 'customer_address':
            $customer_address = get_post_meta( $post_id, '_hb_address', true );
            echo esc_html( $customer_address );
            break;
        case 'phone_number':
            $phone = get_post_meta( $post_id, '_hb_phone', true );
            echo esc_html( $phone );
            break;
        case 'email':
            $email = get_post_meta( $post_id, '_hb_email', true );
            echo esc_html( $email );
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
        $query->query_vars['meta_value'] = absint( sanitize_text_field( $_GET['customer_id'] ) );
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

function hb_update_pricing_plan(){
    if ( ! isset( $_POST['hb-update-pricing-plan-field'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['hb-update-pricing-plan-field'] ), 'hb-update-pricing-plan' ) ){
        return;
    }
    if( ! empty( $_POST['price'] ) ){
        $loop = 0;
        $post_ids = array();
        foreach( (array)$_POST['price'] as $t => $v ){
            $start  = absint( sanitize_text_field( $_POST['date-start'][ $t ] ) );
            $end    = absint( sanitize_text_field( $_POST['date-end'][ $t ] ) );
            $prices = (array)$_POST['price'][ $t ];
            if( $t > 0 ) {
                $post_id = intval( $t );
            } else {
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
                update_post_meta( $post_id, '_hb_pricing_plan_room', $_POST['hb-room'] );

                if ( ! empty( $_POST['date-start-timestamp'] ) && isset( $_POST['date-start-timestamp'][$t] ) ) {
                    update_post_meta( $post_id, '_hb_pricing_plan_start_timestamp', absint( sanitize_text_field( $_POST['date-start-timestamp'][$t] ) ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
                }
                if ( ! empty( $_POST['date-end-timestamp'] ) && isset( $_POST['date-end-timestamp'][$t] ) ) {
                    update_post_meta( $post_id, '_hb_pricing_plan_end_timestamp', absint( sanitize_text_field( $_POST['date-end-timestamp'][$t] ) ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
                }
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
                        'value'   => absint( $_POST['hb-room'] )
                    )
                )
            )
        );
        $delete_ids = array_diff( $existing_ids, $post_ids );
        hb_delete_pricing_plan($delete_ids);
    }
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
                        <a class="dashicons dashicons-trash" title="<?php _e( 'Remove this image', 'tp-hotel-booking' ); ?>"></a>
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
        <a class="dashicons dashicons-trash" title="<?php _e( 'Remove this image', 'tp-hotel-booking' ); ?>"></a>
    </li>
</script>
<?php
}
add_action( 'admin_print_scripts', 'hb_admin_js_template');

function hb_booking_detail_page() {
    if( is_admin() && hb_get_request( 'page' ) == 'hb_booking_details' ) {
        $booking_id = hb_get_request( 'id' );
        $booking = HB_Booking::instance( $booking_id );

        // new version @version 1.1
        if( $booking->get_cart_params() ) {
            TP_Hotel_Booking::instance()->_include( 'includes/admin/views/update/booking-details.php', true, array( 'booking' => $booking ) );
        } else { // $booking->get_booking_rooms_params() // old ver
            TP_Hotel_Booking::instance()->_include( 'includes/admin/views/booking-details.php' );
        }
    }
}

function hb_meta_box_field_datetime( $value ){
    return date( 'l, m/d/Y', $value );
}
function hb_meta_box_field_tax( $value ){
    if( ! $value  )
        return;

    if( is_string($value) )
        $value = ( $value * 100 ) . '%';

    return apply_filters( 'hotel_booking_label_details', $value );
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

function hb_meta_box_coupon_date( $value, $field_name, $meta_box_name ){
    if( in_array( $field_name, array( 'coupon_date_from', 'coupon_date_to' ) ) && $meta_box_name == 'coupon_settings' ){
        $value = strtotime( $value );
    }
    return $value;
}
add_filter( 'hb_meta_box_update_meta_value', 'hb_meta_box_coupon_date', 10, 3 );

function hb_meta_box_field_coupon_date( $value ){
    if( intval( $value ) ) {
        return date( hb_get_date_format(), $value);
    }
    return $value;
}

function hb_meta_box_field_booking_status( $value ){
    global $post;
    return get_post_status( $post->ID );
    // return get_post_meta( $post->ID, '_hb_booking_status', true );
}

function hb_meta_box_field_coupon_used( $value ){
    global $post;
    return intval( get_post_meta( $post->ID, '_hb_usage_count', true ) );
}
function hb_create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
    global $wpdb;

    $option_value     = get_option( $option );

    if ( $option_value > 0 ) {
        $page_object = get_post( $option_value );

        if ( $page_object && 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ) ) ) {
            // Valid page is already in place
            return $page_object->ID;
        }
    }

    if ( strlen( $page_content ) > 0 ) {
        // Search for an existing page with the specified page content (typically a shortcode)
        $valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
    } else {
        // Search for an existing page with the specified page slug
        $valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
    }

    $valid_page_found = apply_filters( 'hotel_booking_create_page_id', $valid_page_found, $slug, $page_content );

    if ( $valid_page_found ) {
        if ( $option ) {
            update_option( $option, $valid_page_found );
        }
        return $valid_page_found;
    }

    // Search for a matching valid trashed page
    if ( strlen( $page_content ) > 0 ) {
        // Search for an existing page with the specified page content (typically a shortcode)
        $trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
    } else {
        // Search for an existing page with the specified page slug
        $trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
    }

    if ( $trashed_page_found ) {
        $page_id   = $trashed_page_found;
        $page_data = array(
            'ID'             => $page_id,
            'post_status'    => 'publish',
        );
        wp_update_post( $page_data );
    } else {
        $page_data = array(
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'post_author'    => 1,
            'post_name'      => $slug,
            'post_title'     => $page_title,
            'post_content'   => $page_content,
            'post_parent'    => $post_parent,
            'comment_status' => 'closed'
        );
        $page_id = wp_insert_post( $page_data );
    }

    if ( $option ) {
        update_option( $option, $page_id );
    }

    return $page_id;
}

if ( ! function_exists( 'hb_get_rooms' ) )
{
    /**
     * get all of post have post type hb_room
     */
    function hb_get_rooms()
    {
        $args = array(
                'post_type'         => 'hb_room',
                'posts_per_page'    => -1,
                'order'             => 'ASC',
                'orderby' => 'title'
            );

        return get_posts( $args );
    }
}
add_action( 'save_post', 'hb_update_meta_box_booking_status' );
if ( ! function_exists( 'hb_update_meta_box_booking_status' ) )
{
    /**
     * update status booking
     */
    function hb_update_meta_box_booking_status( $post )
    {
        if( get_post_type() !== 'hb_booking' )
            return;

        if( ! isset($_POST) )
            return;

        if( ! isset($_POST['_hb_booking_status']) || ! $_POST['_hb_booking_status'] )
            return;

        $status = sanitize_text_field( $_POST['_hb_booking_status'] );

        remove_action( 'save_post', 'hb_update_meta_box_booking_status' );

        $book = HB_Booking::instance( $post );
        $book->update_status( $status );

        add_action( 'save_post', 'hb_update_meta_box_booking_status' );
    }
}