<?php

/**
 * @Author: ducnvtt
 * @Date:   2016-04-25 11:24:59
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-25 14:16:05
 */
if ( !class_exists( 'ABSPATH' ) ) {
    
}

class HBIP_Admin_Menu {
    /* init */

    public function __construct() {

        add_filter( 'hotel_booking_menu_items', array( $this, 'add_menu' ) );

        /* tool page tabs */
        add_filter( 'hotelbooking_importer_tabs', array( $this, 'tabs' ) );

        /* add tab content */
        add_action( 'hb_tooladmin_settings_sections_export', array( $this, 'export' ) );
        add_action( 'hb_tooladmin_settings_sections_import', array( $this, 'import' ) );
    }

    /* menu */

    public function add_menu( $menus ) {

        $menus['tools'] = array(
            'tp_hotel_booking',
            __( 'Tools', 'tp-hotel-booking-block' ),
            __( 'Tools', 'tp-hotel-booking-block' ),
            'manage_options',
            'tp-hotel-tools',
            array( $this, 'page_settings' )
        );

        return $menus;
    }

    /* render page */

    public function page_settings() {
        require_once HOTEL_BOOKING_IMPORTER_PATH . 'inc/views/tools.php';
    }

    /* tabs */

    public function tabs( $tabs ) {
        $tabs['export'] = __( 'Export', 'tp-hotel-booking-importer' );
        $tabs['import'] = __( 'Import', 'tp-hotel-booking-importer' );
        return $tabs;
    }

    /* export content tab */

    public function export() {
        require_once HOTEL_BOOKING_IMPORTER_PATH . 'inc/views/export.php';
    }

    /* import content tab */

    public function import() {
        require_once HOTEL_BOOKING_IMPORTER_PATH . 'inc/views/import.php';
    }

}

new HBIP_Admin_Menu();
