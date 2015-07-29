<?php

/**
 * Class HB_Post_Types
 */
class HB_Post_Types{
    /**
     * Construction
     */
    function __construct(){
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'admin_menu' , array( $this, 'remove_meta_boxes' ) );

        add_action( 'admin_head-edit-tags.php', array( $this, 'fix_menu_parent_file' ) );
    }
    function fix_menu_parent_file() {
        if ( in_array( $_GET['taxonomy'], array( 'hb_room_type', 'hb_room_capacity' ) ) )
            $GLOBALS['parent_file'] = 'tp_hotel_booking';
    }
    /**
     * Remove default meta boxes
     */
    function remove_meta_boxes() {
        remove_meta_box( 'hb_room_typediv', 'hb_room', 'side' );
        remove_meta_box( 'hb_room_capacitydiv', 'hb_room', 'side' );
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
                'name'               => _x( 'Rooms', 'Post Type General Name', 'learn_press' ),
                'singular_name'      => _x( 'Room', 'Post Type Singular Name', 'learn_press' ),
                'menu_name'          => __( 'Rooms', 'learn_press' ),
                'parent_item_colon'  => __( 'Parent Item:', 'learn_press' ),
                'all_items'          => __( 'Rooms', 'learn_press' ),
                'view_item'          => __( 'View Room', 'learn_press' ),
                'add_new_item'       => __( 'Add New Room', 'learn_press' ),
                'add_new'            => __( 'Add New', 'learn_press' ),
                'edit_item'          => __( 'Edit Room', 'learn_press' ),
                'update_item'        => __( 'Update Room', 'learn_press' ),
                'search_items'       => __( 'Search Room', 'learn_press' ),
                'not_found'          => __( 'No room found', 'learn_press' ),
                'not_found_in_trash' => __( 'No room found in Trash', 'learn_press' ),
            ),
            'public'             => true,
            'query_var'          => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'has_archive'        => 'rooms',
            'capability_type'    => 'hb_room',
            'map_meta_cap'       => true,
            'show_in_menu'       => 'tp_hotel_booking',
            'show_in_admin_bar'  => true,
            'show_in_nav_menus'  => true,
            'taxonomies'         => array( 'room_category', 'room_tag' ),
            'supports'           => array( 'title', 'editor', 'thumbnail', 'revisions', 'comments', 'author' ),
            'hierarchical'       => false,
            'rewrite'            => array( 'slug' => 'rooms', 'hierarchical' => true, 'with_front' => false )
        );
        register_post_type( 'hb_room', $args );

        echo "123";

        /**
         * Register room type taxonomy
         */
        register_taxonomy( 'hb_room_type',
            array( 'hb_room' ),
            array(
                'hierarchical'          => true,
                'update_count_callback' => '_wc_term_recount',
                'label'                 => __( 'Room Type', 'tp-hotel-booking' ),
                'labels' => array(
                    'name'              => __( 'Room Types', 'tp-hotel-booking' ),
                    'singular_name'     => __( 'Room Type', 'tp-hotel-booking' ),
                    'menu_name'         => _x( 'Types', 'Admin menu name', 'tp-hotel-booking' ),
                    'search_items'      => __( 'Search Room Types', 'tp-hotel-booking' ),
                    'all_items'         => __( 'All Room Types', 'tp-hotel-booking' ),
                    'parent_item'       => __( 'Parent Room Type', 'tp-hotel-booking' ),
                    'parent_item_colon' => __( 'Parent Room Type:', 'tp-hotel-booking' ),
                    'edit_item'         => __( 'Edit Room Type', 'tp-hotel-booking' ),
                    'update_item'       => __( 'Update Room Type', 'tp-hotel-booking' ),
                    'add_new_item'      => __( 'Add New Room Type', 'tp-hotel-booking' ),
                    'new_item_name'     => __( 'New Room Type Name', 'tp-hotel-booking' )
                ),
                'show_ui'               => true,
                'query_var'             => true,
                'rewrite'               => array(
                    'slug'         => 'hb_room_type',
                    'with_front'   => false,
                    'hierarchical' => false,
                )
            )
        );

        /**
         * Register room capacity taxonomy
         */
        register_taxonomy( 'hb_room_capacity',
            array( 'hb_room' ),
            array(
                'hierarchical'          => true,
                'update_count_callback' => '_wc_term_recount',
                'label'                 => __( 'Room Capacity', 'tp-hotel-booking' ),
                'labels' => array(
                    'name'              => __( 'Room Capacities', 'tp-hotel-booking' ),
                    'singular_name'     => __( 'Room Capacity', 'tp-hotel-booking' ),
                    'menu_name'         => _x( 'Types', 'Admin menu name', 'tp-hotel-booking' ),
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
                    'slug'         => 'hb_room_capacity',
                    'with_front'   => false,
                    'hierarchical' => true,
                )
            )
        );

        /**
         * Register custom post type for booking
         */
        $args = array(
            'labels'             => array(
                'name'               => _x( 'Bookings', 'Post Type General Name', 'learn_press' ),
                'singular_name'      => _x( 'Booking', 'Post Type Singular Name', 'learn_press' ),
                'menu_name'          => __( 'Bookings', 'learn_press' ),
                'parent_item_colon'  => __( 'Parent Item:', 'learn_press' ),
                'all_items'          => __( 'Bookings', 'learn_press' ),
                'view_item'          => __( 'View Booking', 'learn_press' ),
                'add_new_item'       => __( 'Add New Booking', 'learn_press' ),
                'add_new'            => __( 'Add New', 'learn_press' ),
                'edit_item'          => __( 'Edit Booking', 'learn_press' ),
                'update_item'        => __( 'Update Booking', 'learn_press' ),
                'search_items'       => __( 'Search Booking', 'learn_press' ),
                'not_found'          => __( 'No booking found', 'learn_press' ),
                'not_found_in_trash' => __( 'No booking found in Trash', 'learn_press' ),
            ),
            'public'             => false,
            'query_var'          => true,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'has_archive'        => false,
            'capability_type'    => 'hb_booking',
            'map_meta_cap'       => true,
            'show_in_menu'       => 'tp_hotel_booking',
            'show_in_admin_bar'  => true,
            'show_in_nav_menus'  => true,
            'supports'           => array( 'title', 'author' ),
            'hierarchical'       => false
        );
        register_post_type( 'hb_booking', $args );
    }
}

new HB_Post_Types();