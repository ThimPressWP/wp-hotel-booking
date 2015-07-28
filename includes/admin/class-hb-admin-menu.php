<?php
class HB_Admin_Menu{
    function __construct(){
        add_action( 'admin_menu', array( $this, 'register' ) );
    }

    function register(){
        add_menu_page(
            __( 'TP Hotel Booking', 'tp-hotel-booking' ),
            __( 'TP Hotel Booking', 'tp-hotel-booking' ),
            'manage_options',
            'tp_hotel_booking',
            '',
            'dashicons-welcome-learn-more',
            '3.99'
        );

        $menu_items = array(
            'settings'   => array(
                'tp_hotel_booking',
                __( 'Settings', 'tp-hotel-booking' ),
                __( 'Settings', 'tp-hotel-booking' ),
                'manage_options',
                'tp_hotel_booking_settings',
                array( $this, 'settings_page' )
            )
        );

        // Third-party can be add more items
        $menu_items = apply_filters( 'tp_hotel_booking_menu_items', $menu_items );

        if ( $menu_items ) foreach ( $menu_items as $item ) {
            call_user_func_array( 'add_submenu_page', $item );
        }
    }

    function settings_page(){
        TP_Hotel_Booking::instance()->_include( 'includes/admin/views/settings.php' );
    }
}

new HB_Admin_Menu();