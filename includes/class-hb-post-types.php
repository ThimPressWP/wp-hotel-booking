<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class HB_Post_Types
 */
class HB_Post_Types{
    /**
     * @var array
     */
    protected static $_ordering = array();

    /**
     * Construction
     */
    function __construct(){
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'init', array( $this, 'register_post_statues' ) );
        add_action( 'admin_init', array( $this, 'update_taxonomy' ) );

        add_action( 'admin_menu' , array( $this, 'remove_meta_boxes' ) );
        add_action( 'admin_head-edit-tags.php', array( $this, 'fix_menu_parent_file' ) );

        // add_filter( 'manage_edit-hb_room_type_columns', array( $this, 'taxonomy_columns' ) );
        add_filter( 'manage_edit-hb_room_capacity_columns', array( $this, 'taxonomy_columns' ) );

        // add_filter( 'manage_hb_room_type_custom_column', array( $this, 'taxonomy_column_content' ), 10, 3 );
        add_filter( 'manage_hb_room_capacity_custom_column', array( $this, 'taxonomy_column_content' ), 10, 3 );

        add_action( 'delete_term_taxonomy', array( $this, 'delete_term_data' ) );

        add_filter( 'manage_hb_room_posts_columns', array( $this, 'custom_room_columns' ) );
        add_action( 'manage_hb_room_posts_custom_column', array( $this, 'custom_room_columns_filter' ) );

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        //add_filter( 'pre_get_posts', array( $this, 'filter_post_type' ) );

        add_filter( 'posts_fields', array( $this, 'posts_fields' ) );
        add_filter( 'posts_join_paged', array( $this, 'posts_join_paged' ) );
        add_filter( 'posts_where' , array( $this, 'posts_where_paged' ), 999 );

        add_filter( 'get_terms_orderby', array( $this, 'terms_orderby' ), 100, 3 );
        add_filter( 'get_terms_args', array( $this, 'terms_args' ), 100, 2 );

        add_filter( 'manage_hb_coupon_posts_columns' , array( $this, 'custom_coupon_columns' ) );
        add_action( 'manage_hb_coupon_posts_custom_column', array( $this, 'custom_coupon_columns_filter' ) );

        add_action( 'deleted_post', array( $this, 'delete_post_type' ) );
    }

    function custom_coupon_columns( $columns ){
        $columns['type']            = __( 'Type', 'tp-hotel-booking' );
        $columns['from']            = __( 'Validate From', 'tp-hotel-booking' );
        $columns['to']              = __( 'Validate To', 'tp-hotel-booking' );
        $columns['minimum_spend']   = __( 'Minimum spend', 'tp-hotel-booking' );
        $columns['maximum_spend']   = __( 'Maximum spend', 'tp-hotel-booking' );
        $columns['limit_per_coupon']     = __( 'Usage limit per coupon', 'tp-hotel-booking' );
        $columns['usage_count']                = __( 'Used', 'tp-hotel-booking' );
        unset( $columns['date'] );
        return $columns;
    }

    function custom_coupon_columns_filter( $column ){
        global $post;
        switch( $column ){
            case 'type':
                switch( get_post_meta( $post->ID, '_hb_coupon_discount_type', true ) ){
                    case 'fixed_cart': _e( 'Fixed cart', 'tp-hotel-booking' ); break;
                    case 'percent_cart': _e( 'Percent cart', 'tp-hotel-booking' ); break;
                }
                break;
            case 'from':
            case 'to':
                if( $from = get_post_meta( $post->ID, '_hb_coupon_date_' . $column, true ) ) {
                    echo date_i18n( hb_get_date_format(), $from );
                }else{
                    echo '-';
                }
                break;
            case 'minimum_spend':
            case 'maximum_spend':
                if( $value = get_post_meta( $post->ID, '_hb_' . $column, true ) ) {
                    if( get_post_meta( $post->ID, '_hb_coupon_discount_type', true ) == 'fixed_cart' ) {
                        echo hb_format_price( $value );
                    }else{
                        echo sprintf( '%s', $value . '%' );
                    }
                }else{
                    echo '-';
                }
                break;
            case 'limit_per_coupon':
            case 'usage_count':
                if( $value = get_post_meta( $post->ID, '_hb_' . $column, true ) ) {
                    echo sprintf( '%s', $value );
                }else{
                    echo '-';
                }
        }
    }

    function terms_orderby( $orderby, $args, $taxonomies ){
        if( in_array( hb_get_request( 'taxonomy' ), array( 'hb_room_type', 'hb_room_capacity' ) ) ){
            $orderby = 'term_group';

        }

        return $orderby;
    }

    function terms_args( $args, $taxonomies ){
        if( in_array( hb_get_request( 'taxonomy' ), array( 'hb_room_type', 'hb_room_capacity' ) ) ){
            $args['order'] = 'ASC';
        }
        return $args;
    }

    function posts_fields( $fields ){
        if( hb_get_request( 'post_type' ) == 'hb_booking' ) {
            $from       = hb_get_request('date-from-timestamp');
            $to         = hb_get_request('date-to-timestamp');
            $filter     = hb_get_request('filter-type');
            if( $from && $to && $filter == 'booking-date' ) {
                $fields .= ", DATE_FORMAT(`post_date`,'%Y%m%d') AS post_date_timestamp";
            }
        }
        return $fields;
    }

    /**
     * Join with postmeta to enable search by customer meta such as first name, last name, email, etc...
     *
     * @param $join
     * @return string
     */
    function posts_join_paged( $join ){
        global $wpdb;

        if( $this->is_search( 'customer' ) ){
            $join .= "
                INNER JOIN {$wpdb->postmeta} pm1 ON {$wpdb->posts}.ID = pm1.post_id and pm1.meta_key='_hb_first_name'
                INNER JOIN {$wpdb->postmeta} pm2 ON {$wpdb->posts}.ID = pm2.post_id and pm2.meta_key='_hb_last_name'
                INNER JOIN {$wpdb->postmeta} pm3 ON {$wpdb->posts}.ID = pm3.post_id and pm3.meta_key='_hb_email'
                INNER JOIN {$wpdb->postmeta} pm4 ON {$wpdb->posts}.ID = pm4.post_id and pm4.meta_key='_hb_phone'
                INNER JOIN {$wpdb->postmeta} pm5 ON {$wpdb->posts}.ID = pm5.post_id and pm5.meta_key='_hb_address'
            ";
        }elseif( $this->is_search( 'booking' ) ){

            $join .= "
                INNER JOIN {$wpdb->postmeta} pm ON {$wpdb->posts}.ID=pm.post_id and pm.meta_key='_hb_customer_id'
                INNER JOIN {$wpdb->postmeta} cus1 ON cus1.post_id = pm.meta_value and cus1.meta_key='_hb_first_name'
                INNER JOIN {$wpdb->postmeta} cus2 ON cus2.post_id = pm.meta_value and cus2.meta_key='_hb_last_name'
                INNER JOIN {$wpdb->postmeta} cus3 ON cus3.post_id = pm.meta_value and cus3.meta_key='_hb_email'
                INNER JOIN {$wpdb->postmeta} cus4 ON cus4.post_id = pm.meta_value and cus4.meta_key='_hb_phone'
                INNER JOIN {$wpdb->postmeta} cus5 ON cus5.post_id = pm.meta_value and cus5.meta_key='_hb_address'
            ";
        }

        if( hb_get_request( 'post_type' ) == 'hb_booking' ){
            $from   = hb_get_request( 'date-from-timestamp' );
            $to     = hb_get_request( 'date-to-timestamp' );
            $filter = hb_get_request( 'filter-type' );
            if( $from && $to & $filter ){
                switch( $filter ){
                    case 'booking-date':
                        break;
                    case 'check-in-date':
                        $join .= "
                            INNER JOIN {$wpdb->postmeta} pm_check_in ON {$wpdb->posts}.ID=pm_check_in.post_id and pm_check_in.meta_key='_hb_check_in_date'
                        ";
                        break;
                    case 'check-out-date':
                        $join .= "
                            INNER JOIN {$wpdb->postmeta} pm_check_out ON {$wpdb->posts}.ID=pm_check_out.post_id and pm_check_out.meta_key='_hb_check_out_date'
                        ";
                        break;
                }
            }
        }
        return $join;
    }

    /**
     * Conditions to filter customer by meta value such as first name, last name, email, etc...
     *
     * @param $where
     * @return string
     */
    function posts_where_paged( $where ){
        if( $s = $this->is_search( 'customer' ) ) {
            $where .= "
                OR (
                    pm1.meta_value LIKE '%{$s}%'
                    OR pm2.meta_value LIKE '%{$s}%'
                    OR pm3.meta_value LIKE '%{$s}%'
                    OR pm4.meta_value LIKE '%{$s}%'
                    OR pm5.meta_value LIKE '%{$s}%'
                )
            ";
        }elseif( $s = $this->is_search( 'booking' ) ) {
            $where .= "
                OR (
                    cus1.meta_value LIKE '%{$s}%'
                    OR cus2.meta_value LIKE '%{$s}%'
                    OR cus3.meta_value LIKE '%{$s}%'
                    OR cus4.meta_value LIKE '%{$s}%'
                    OR cus5.meta_value LIKE '%{$s}%'
                )
            ";
        }

        if( hb_get_request( 'post_type' ) == 'hb_booking' ){
            $from   = hb_get_request( 'date-from-timestamp' );
            $to     = hb_get_request( 'date-to-timestamp' );
            $filter = hb_get_request( 'filter-type' );
            if( $from && $to & $filter ){
                $from   = absint( $from );
                $to     = absint( $to );
                switch( $filter ){
                    case 'booking-date':
                        $from   = date( 'Ymd', $from );
                        $to     = date( 'Ymd', $to );
                        if( $from == $to ){
                            $where .= "
                                HAVING post_date_timestamp = {$from}
                            ";
                        }else {
                            $where .= "
                                HAVING post_date_timestamp >= {$from} AND post_date_timestamp <= {$to}
                            ";
                        }
                        break;
                    case 'check-in-date':
                        $where .= "
                            AND ( pm_check_in.meta_value >= {$from} AND pm_check_in.meta_value <= {$to} )
                        ";
                        break;
                    case 'check-out-date':
                        $where .= "
                            AND ( pm_check_out.meta_value >= {$from} AND pm_check_out.meta_value <= {$to} )
                        ";
                        break;
                }
            }
        }

        return $where;
    }

    function is_search( $type ){
        global $post_type;
        if( $post_type == "hb_{$type}" && $s = hb_get_request( 's' ) ){
            return $s;
        }
        return false;
    }


    /**
     * Enqueue scripts
     */
    function enqueue_scripts(){
        if( in_array( hb_get_request( 'taxonomy' ), array( 'hb_room_type', 'hb_room_capacity' ) ) ){
            wp_enqueue_media();
            wp_enqueue_script('hb-media-selector', TP_Hotel_Booking::instance()->plugin_url('includes/assets/js/media-selector.min.js'));
            wp_enqueue_style( 'hb-edit-tags', TP_Hotel_Booking::instance()->plugin_url( 'includes/assets/css/edit-tags.min.css' ) );
            wp_enqueue_script( 'hb-edit-tags', TP_Hotel_Booking::instance()->plugin_url( 'includes/assets/js/edit-tags.min.js' ), array( 'jquery', 'jquery-ui-sortable' ) );
        }
    }

    /**
     * Add more column to taxonomy manage
     *
     * @param $a
     * @return mixed
     */
    function custom_room_columns( $a ){
        $a['room_type'] = __( 'Room Type', 'tp-hotel-booking' );
        $a['room_capacity'] = __( 'Max Child', 'tp-hotel-booking' );
        $a['room_price_plan'] = __( 'Price', 'tp-hotel-booking' );
        $a['room_average_rating'] = __( 'Average Rating', 'tp-hotel-booking' );

        // move comments to the last of list
        if( isset( $a['comments'] ) ){
            $t = $a['comments'];
            unset( $a['comments'] );
            $a['comments'] = $t;
        }
        return $a;
    }

    /**
     * Display content for taxonomy custom field
     *
     * @param $column
     */
    function custom_room_columns_filter( $column ){
        global $post;
        switch( $column ){
            case 'room_type':
                // $type_id = get_post_meta( $post->ID, '_hb_room_type', true );
                // $type = get_term( $type_id, 'hb_room_type' );
                $terms = wp_get_post_terms( $post->ID, 'hb_room_type' );

                $cap_id = get_post_meta( $post->ID, '_hb_room_capacity', true );
                $cap = get_term( $cap_id, 'hb_room_capacity' );

                if( $cap && isset( $cap->name ) )
                {
                    // printf( '%s (%s)', $type->name, $cap->name );
                    $room_types = array();
                    foreach ($terms as $key => $term) {
                        $room_types[] = $term->name;
                    }
                    printf( '%s (%s)', implode(', ', $room_types), $cap->name );
                }
                break;
            case 'room_capacity':
                echo get_post_meta( $post->ID, '_hb_max_child_per_room', true );
                break;
            case 'room_price_plan':
                echo '<a href="'.admin_url( 'admin.php?page=tp_hotel_booking_pricing&hb-room='.$post->ID ).'">'.__('View Price', 'tp-hotel-booking').'</a>';
                break;
            case 'room_average_rating':
                $room = HB_Room::instance( $post->ID );
                $rating = $room->average_rating();
                $html = array();
                $html[] = '<div class="rating">';
                if( $rating ):
                    $html[] =   '<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="'.( sprintf( __( 'Rated %d out of 5', 'tp-hotel-booking' ), $rating ) ).'">';
                    $html[] =       '<span style="width:'.( ( $rating / 5 ) * 100 ) .'%"></span>';
                    $html[] =   '</div>';
                endif;
                $html[] =  '</div>';
                echo implode( '', $html);
                break;
        }
    }

    /**
     * Update custom fields for taxonomy
     */
    function update_taxonomy(){

        if( ! empty( $_REQUEST['action'] ) && in_array( hb_get_request( 'taxonomy'), array( 'hb_room_type', 'hb_room_capacity' ) ) ){
            $taxonomy = ! empty( $_REQUEST['taxonomy'] ) ? $_REQUEST['taxonomy'] : '';
            global $wpdb;
            if( ! empty( $_POST[ "{$taxonomy}_ordering" ] ) ){
                $when = array();
                $ids = array();
                foreach( $_POST[ "{$taxonomy}_ordering" ] as $term_id => $ordering ){
                    $when[] = "WHEN term_id = {$term_id} THEN {$ordering}";
                    $ids[] = absint( $term_id );
                }

                $query = sprintf("
                    UPDATE {$wpdb->terms}
                    SET term_group = CASE
                       %s
                    END
                    WHERE term_id IN(%s)
                ", join( "\n", $when ), join(', ', $ids ) );
                $wpdb->query( $query );
            }

            if( ! empty( $_POST[ "{$taxonomy}_capacity" ] ) ){
                foreach( (array)$_POST[ "{$taxonomy}_capacity" ] as $term_id => $capacity ) {
                    if( $capacity ) {
                        // update_option( 'hb_taxonomy_capacity_' . $term_id, $capacity );
                        update_term_meta( $term_id, 'hb_max_number_of_adults', absint( sanitize_text_field( $capacity ) ) );
                    } else {
                        // delete_option( 'hb_taxonomy_capacity_' . $term_id );
                        delete_term_meta( $term_id, 'hb_max_number_of_adults' );
                    }
                }
            }

        }

    }

    function delete_term_data( $term_id ) {
        delete_option( 'hb_taxonomy_thumbnail_' . $term_id );
    }

    function taxonomy_columns( $columns ){
        if( 'hb_room_type' == $_REQUEST['taxonomy'] ){
            $columns['thumbnail'] = __( 'Gallery', 'tp-hotel-booking' );
        }else{
            $columns['capacity'] = __( 'Capacity', 'tp-hotel-booking' );
        }
        $columns['ordering'] = __( 'Ordering', 'tp-hotel-booking' );
        if( isset( $columns['description'] ) ){
            unset( $columns['description'] );
        }
        if( isset( $columns['posts'] ) ) {
            unset($columns['posts']);
        }
        return $columns;
    }

    function taxonomy_column_content( $content, $column_name, $term_id ){
        $taxonomy = $_REQUEST['taxonomy'];
        $term = get_term( $term_id, $taxonomy );
        switch ($column_name) {
            case 'ordering':
                $content = sprintf( '<input class="hb-number-field" type="number" name="%s_ordering[%d]" value="%d" size="3" />', $taxonomy, $term_id, $term->term_group );
                break;
            case 'capacity':
                $capacity = get_term_meta( $term_id, 'hb_max_number_of_adults', true);
                if ( ! $capacity ) {
                    $capacity = get_option( 'hb_taxonomy_capacity_' . $term_id );
                }
                $content = '<input class="hb-number-field" type="number" name="' . $taxonomy . '_capacity[' . $term_id . ']" value="' . $capacity .'" size="2" />';
                break;
            default:
                break;
        }

        return $content;
    }

    /**
     * Fix menu parent for taxonomy menu item
     */
    function fix_menu_parent_file() {
        if ( in_array( hb_get_request( 'taxonomy' ), array( 'hb_room_type', 'hb_room_capacity' ) ) )
            $GLOBALS['parent_file'] = 'tp_hotel_booking';
    }
    /**
     * Remove default meta boxes
     */
    function remove_meta_boxes() {
        // remove_meta_box( 'hb_room_typediv', 'hb_room', 'side' );
        remove_meta_box( 'hb_room_capacitydiv', 'hb_room', 'side' );

        // remove_meta_box( 'tagsdiv-hb_room_type', 'hb_room', 'side' );
        remove_meta_box( 'tagsdiv-hb_room_capacity', 'hb_room', 'side' );
    }

    /**
     * Register custom post types for Hotel Booking
     */
    function register_post_types(){
        /**
         * Register custom post type for room
         */
        $args = array(
            'labels'             => array(
                'name'               => _x( 'Rooms', 'Rooms', 'tp-hotel-booking' ),
                'singular_name'      => _x( 'Room', 'Room', 'tp-hotel-booking' ),
                'menu_name'          => __( 'Rooms', 'tp-hotel-booking' ),
                'parent_item_colon'  => __( 'Parent Item:', 'tp-hotel-booking' ),
                'all_items'          => __( 'Rooms', 'tp-hotel-booking' ),
                'view_item'          => __( 'View Room', 'tp-hotel-booking' ),
                'add_new_item'       => __( 'Add New Room', 'tp-hotel-booking' ),
                'add_new'            => __( 'Add New', 'tp-hotel-booking' ),
                'edit_item'          => __( 'Edit Room', 'tp-hotel-booking' ),
                'update_item'        => __( 'Update Room', 'tp-hotel-booking' ),
                'search_items'       => __( 'Search Room', 'tp-hotel-booking' ),
                'not_found'          => __( 'No room found', 'tp-hotel-booking' ),
                'not_found_in_trash' => __( 'No room found in Trash', 'tp-hotel-booking' ),
            ),
            'public'             => true,
            'query_var'          => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'has_archive'        => true,
            //'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'show_in_menu'       => 'tp_hotel_booking',
            'show_in_admin_bar'  => true,
            'show_in_nav_menus'  => true,
            'taxonomies'         => array( 'room_category', 'room_tag' ),
            'supports'           => array( 'title', 'editor', 'thumbnail', 'revisions', 'comments', 'author' ),
            'hierarchical'       => false,
            'rewrite'            => array( 'slug' => _x( 'rooms', 'URL slug', 'tp-hotel-booking' ), 'with_front' => false, 'feeds' => true )
        );

        $args = apply_filters( 'hotel_booking_register_post_type_room_arg', $args );
        register_post_type( 'hb_room', $args );

        /**
         * Register room type taxonomy
         */
        $args = array(
                'hierarchical'          => true,
                'label'                 => __( 'Room Type', 'tp-hotel-booking' ),
                'labels' => array(
                    'name'              => __( 'Room Types', 'tp-hotel-booking' ),
                    'singular_name'     => __( 'Room Type', 'tp-hotel-booking' ),
                    'menu_name'         => _x( 'Types', 'Room Types', 'tp-hotel-booking' ),
                    'search_items'      => __( 'Search Room Types', 'tp-hotel-booking' ),
                    'all_items'         => __( 'All Room Types', 'tp-hotel-booking' ),
                    'parent_item'       => __( 'Parent Room Type', 'tp-hotel-booking' ),
                    'parent_item_colon' => __( 'Parent Room Type:', 'tp-hotel-booking' ),
                    'edit_item'         => __( 'Edit Room Type', 'tp-hotel-booking' ),
                    'update_item'       => __( 'Update Room Type', 'tp-hotel-booking' ),
                    'add_new_item'      => __( 'Add New Room Type', 'tp-hotel-booking' ),
                    'new_item_name'     => __( 'New Room Type Name', 'tp-hotel-booking' )
                ),
                'public'                => true,
                'show_ui'               => true,
                'query_var'             => true,
                'rewrite'               => array( 'slug' => _x( 'room-type', 'URL slug', 'tp-hotel-booking' ) )
            );
        $args = apply_filters( 'hotel_booking_register_tax_room_type_arg', $args );
        register_taxonomy( 'hb_room_type',
            array( 'hb_room' ),
            $args
        );

        /**
         * Register room capacity taxonomy
         */
        $args = array(
                'hierarchical'          => false,
                // 'update_count_callback' => '_wc_term_recount',
                'label'                 => __( 'Room Capacity', 'tp-hotel-booking' ),
                'labels' => array(
                    'name'              => __( 'Room Capacities', 'tp-hotel-booking' ),
                    'singular_name'     => __( 'Room Capacity', 'tp-hotel-booking' ),
                    'menu_name'         => _x( 'Types', 'Room Capacities', 'tp-hotel-booking' ),
                    'search_items'      => __( 'Search Room Capacities', 'tp-hotel-booking' ),
                    'all_items'         => __( 'All Room Capacity', 'tp-hotel-booking' ),
                    'parent_item'       => __( 'Parent Room Capacity', 'tp-hotel-booking' ),
                    'parent_item_colon' => __( 'Parent Room Capacity:', 'tp-hotel-booking' ),
                    'edit_item'         => __( 'Edit Room Capacity', 'tp-hotel-booking' ),
                    'update_item'       => __( 'Update Room Capacity', 'tp-hotel-booking' ),
                    'add_new_item'      => __( 'Add New Room Capacity', 'tp-hotel-booking' ),
                    'new_item_name'     => __( 'New Room Type Capacity', 'tp-hotel-booking' )
                ),
                'show_ui'               => true,
                'query_var'             => true,
                'rewrite'               => array(
                    'slug'         => _x( 'room-capacity', 'URL slug', 'tp-hotel-booking' ),
                    'with_front'   => false,
                    'hierarchical' => true,
                )
            );
        $args = apply_filters( 'hotel_booking_register_tax_capacity_arg', $args );
        register_taxonomy( 'hb_room_capacity',
            array( 'hb_room' ),
            $args
        );

        /**
         * Register custom post type for booking
         */
        $args = array(
            'labels'             => array(
                'name'               => _x( 'Bookings', 'Bookings', 'tp-hotel-booking' ),
                'singular_name'      => _x( 'Booking', 'Booking', 'tp-hotel-booking' ),
                'menu_name'          => __( 'Bookings', 'tp-hotel-booking' ),
                'parent_item_colon'  => __( 'Parent Item:', 'tp-hotel-booking' ),
                'all_items'          => __( 'Bookings', 'tp-hotel-booking' ),
                'view_item'          => __( 'View Booking', 'tp-hotel-booking' ),
                'add_new_item'       => __( 'Add New Booking', 'tp-hotel-booking' ),
                'add_new'            => __( 'Add New', 'tp-hotel-booking' ),
                'edit_item'          => __( 'Edit Booking', 'tp-hotel-booking' ),
                'update_item'        => __( 'Update Booking', 'tp-hotel-booking' ),
                'search_items'       => __( 'Search Booking', 'tp-hotel-booking' ),
                'not_found'          => __( 'No booking found', 'tp-hotel-booking' ),
                'not_found_in_trash' => __( 'No booking found in Trash', 'tp-hotel-booking' ),
            ),
            'public'             => false,
            'query_var'          => true,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'has_archive'        => false,
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'show_in_menu'       => 'tp_hotel_booking',
            'show_in_admin_bar'  => true,
            'show_in_nav_menus'  => true,
            'supports'           => array( 'title', 'author' ),
            'hierarchical'       => false,
            'capabilities'       => array(
                'create_posts'  => 'do_not_allow'
            )
        );
        $args = apply_filters( 'hotel_booking_register_post_type_booking_arg', $args );
        register_post_type( 'hb_booking', $args );

        /**
         * Register custom post type for customer
         */
        $args = array(
            'labels'             => array(
                'name'               => _x( 'Customers', 'Customers', 'tp-hotel-booking' ),
                'singular_name'      => _x( 'Customer', 'Customer', 'tp-hotel-booking' ),
                'menu_name'          => __( 'Customers', 'tp-hotel-booking' ),
                'parent_item_colon'  => __( 'Parent Item:', 'tp-hotel-booking' ),
                'all_items'          => __( 'Customers', 'tp-hotel-booking' ),
                'view_item'          => __( 'View Customer', 'tp-hotel-booking' ),
                'add_new_item'       => __( 'Add New Customer', 'tp-hotel-booking' ),
                'add_new'            => __( 'Add New', 'tp-hotel-booking' ),
                'edit_item'          => __( 'Edit Customer', 'tp-hotel-booking' ),
                'update_item'        => __( 'Update Customer', 'tp-hotel-booking' ),
                'search_items'       => __( 'Search Customer', 'tp-hotel-booking' ),
                'not_found'          => __( 'No customer found', 'tp-hotel-booking' ),
                'not_found_in_trash' => __( 'No customer found in Trash', 'tp-hotel-booking' ),
            ),
            'public'             => false,
            'query_var'          => true,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'has_archive'        => false,
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'show_in_menu'       => 'tp_hotel_booking',
            'show_in_admin_bar'  => true,
            'show_in_nav_menus'  => true,
            'supports'           => array( '' ),
            'hierarchical'       => false,
            'capabilities'       => array(
                'create_posts'  => 'do_not_allow'
            )
        );
        $args = apply_filters( 'hotel_booking_register_post_type_customer_arg', $args );
        register_post_type( 'hb_customer', $args );

        /**
         * Register custom post type for pricing plan
         */
        $args = array(
            'labels'             => array(
                /*'name'               => _x( 'Bookings', 'Post Type General Name', 'tp-hotel-booking' ),
                'singular_name'      => _x( 'Booking', 'Post Type Singular Name', 'tp-hotel-booking' ),
                'menu_name'          => __( 'Bookings', 'tp-hotel-booking' ),
                'parent_item_colon'  => __( 'Parent Item:', 'tp-hotel-booking' ),
                'all_items'          => __( 'Bookings', 'tp-hotel-booking' ),
                'view_item'          => __( 'View Booking', 'tp-hotel-booking' ),
                'add_new_item'       => __( 'Add New Booking', 'tp-hotel-booking' ),
                'add_new'            => __( 'Add New', 'tp-hotel-booking' ),
                'edit_item'          => __( 'Edit Booking', 'tp-hotel-booking' ),
                'update_item'        => __( 'Update Booking', 'tp-hotel-booking' ),
                'search_items'       => __( 'Search Booking', 'tp-hotel-booking' ),
                'not_found'          => __( 'No booking found', 'tp-hotel-booking' ),
                'not_found_in_trash' => __( 'No booking found in Trash', 'tp-hotel-booking' ),*/
            ),
            'public'             => false,
            'query_var'          => false,
            'publicly_queryable' => false,
            'show_ui'            => false,
            'has_archive'        => false,
            //'capability_type'    => 'hb_booking',
            'map_meta_cap'       => true,
            'show_in_menu'       => false,
            'show_in_admin_bar'  => false,
            'show_in_nav_menus'  => false,
            'supports'           => array( 'title', 'author' ),
            'hierarchical'       => false
        );
        $args = apply_filters( 'hotel_booking_register_post_type_pricing_arg', $args );
        register_post_type( 'hb_pricing_plan', $args );

        // coupon
        /**
         * Register custom post type for booking
         */
        $args = array(
            'labels'             => array(
                'name'               => _x( 'Coupons', 'Coupons', 'tp-hotel-coupon' ),
                'singular_name'      => _x( 'Coupon', 'Coupon', 'tp-hotel-coupon' ),
                'menu_name'          => __( 'Coupons', 'tp-hotel-coupon' ),
                'parent_item_colon'  => __( 'Parent Item:', 'tp-hotel-coupon' ),
                'all_items'          => __( 'Coupons', 'tp-hotel-coupon' ),
                'view_item'          => __( 'View Coupon', 'tp-hotel-coupon' ),
                'add_new_item'       => __( 'Add New Coupon', 'tp-hotel-coupon' ),
                'add_new'            => __( 'Add New', 'tp-hotel-coupon' ),
                'edit_item'          => __( 'Edit Coupon', 'tp-hotel-coupon' ),
                'update_item'        => __( 'Update Coupon', 'tp-hotel-coupon' ),
                'search_items'       => __( 'Search Coupon', 'tp-hotel-coupon' ),
                'not_found'          => __( 'No coupon found', 'tp-hotel-coupon' ),
                'not_found_in_trash' => __( 'No coupon found in Trash', 'tp-hotel-coupon' ),
            ),
            'public'             => false,
            'query_var'          => true,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'has_archive'        => false,
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'show_in_menu'       => 'tp_hotel_booking',
            'show_in_admin_bar'  => true,
            'show_in_nav_menus'  => true,
            'supports'           => array( 'title' ),
            'hierarchical'       => false
        );
        $args = apply_filters( 'hotel_booking_register_post_type_coupon_arg', $args );
        register_post_type( 'hb_coupon', $args );

        /**
         * Register custom post type for hb_booking_item
         */
        $args = array(
            'labels'             => array(),
            'public'             => false,
            'query_var'          => false,
            'publicly_queryable' => false,
            'show_ui'            => false,
            'has_archive'        => false,
            'map_meta_cap'       => true,
            'show_in_menu'       => false,
            'show_in_admin_bar'  => false,
            'show_in_nav_menus'  => false,
            'supports'           => array( 'title', 'author' ),
            'hierarchical'       => false
        );
        $args = apply_filters( 'hotel_booking_register_post_type_booking_item_arg', $args );
        register_post_type( 'hb_booking_item', $args );

        if( is_admin() ){
            TP_Hotel_Booking::instance()->_include( 'includes/walkers/class-hb-walker-room-type-dropdown.php' );
        }
    }

    /**
     * Registers custom post statues
     */
    function register_post_statues(){
        $args = array(
            'label'                     => _x( 'Pending Payment', 'Booking status', 'tp-hotel-booking' ),
            'public'                    => false,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'tp-hotel-booking' )
        );
        register_post_status( 'hb-pending', $args );

        $args = array(
            'label'                     => _x( 'Processing', 'Booking status', 'tp-hotel-booking' ),
            'public'                    => false,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Processing <span class="count">(%s)</span>', 'Processing <span class="count">(%s)</span>', 'tp-hotel-booking' )
        );
        register_post_status( 'hb-processing', $args );

        $args = array(
            'label'                     => _x( 'Completed', 'Booking status', 'tp-hotel-booking' ),
            'public'                    => false,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'tp-hotel-booking' )
        );
        register_post_status( 'hb-completed', $args );
    }

    /**
     *
     **/
    function delete_post_type( $postID )
    {
        $post_type = get_post_type( $postID );
        $hb_post_type_delete = array(
                'hb_room',
                'hb_booking'
            );

        if( ! in_array( $post_type, $hb_post_type_delete ) )
            return;

        if( $post_type === 'hb_booking' )
        {
            // new script
            global $wpdb;
            $query = $wpdb->prepare("
                    SELECT * FROM `$wpdb->postmeta`
                        WHERE meta_key = %s
                        AND meta_value = %s
                ", '_hb_booking_id', $postID );
            $metas = $wpdb->get_results( $query );
            if ( $metas ) {
                foreach ( $metas as $key => $meta ) {
                    wp_delete_post( $meta->post_id );
                    delete_post_meta( $meta->post_id, $meta->meta_key );
                }
            }

            delete_post_meta( $postID, '_hb_booking_cart_params' );

            // end new script
            delete_post_meta( $postID, '_hb_method_id' );
            delete_post_meta( $postID, '_hb_check_in_date' );
            delete_post_meta( $postID, '_hb_check_out_date' );
            delete_post_meta( $postID, '_hb_id' );
            delete_post_meta( $postID, '_hb_name' );
            delete_post_meta( $postID, '_hb_quantity' );
            delete_post_meta( $postID, '_hb_sub_total' );
            delete_post_meta( $postID, '_hb_customer_id' );
            delete_post_meta( $postID, '_hb_booking_params' );
            delete_post_meta( $postID, '_hb_currency' );
            delete_post_meta( $postID, '_hb_method' );
            delete_post_meta( $postID, '_hb_method_title' );
            delete_post_meta( $postID, '_hb_room_id' );
        }
        else if( $post_type === 'hb_room' )
        {
            delete_post_meta( $postID, '_hb_num_of_rooms' );
            delete_post_meta( $postID, '_hb_room_capacity' );
            delete_post_meta( $postID, '_hb_max_child_per_room' );
            delete_post_meta( $postID, '_hb_room_addition_information' );
            delete_post_meta( $postID, '_hb_gallery' );
            delete_post_meta( $postID, '_hb_room_extra' );
        }

    }
}

new HB_Post_Types();