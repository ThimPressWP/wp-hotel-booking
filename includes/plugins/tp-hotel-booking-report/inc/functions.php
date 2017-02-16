<?php

// report menu
add_filter( 'hotel_booking_menu_items', 'hotel_report_menu' );
if ( !function_exists( 'hotel_report_menu' ) ) {

    function hotel_report_menu( $menus ) {
        $menus['reports'] = array(
            'tp_hotel_booking',
            __( 'Reports', 'tp-hotel-booking-report' ),
            __( 'Reports', 'tp-hotel-booking-report' ),
            'manage_options',
            'tp_hotel_booking_report',
            'hotel_create_report_page'
        );
        return $menus;
    }

}

if ( !function_exists( 'hotel_create_report_page' ) ) {

    function hotel_create_report_page() {
        require_once TP_HB_REPORT_DIR . 'inc/views/report.php';
    }

}


add_action( 'hotel_booking_chart_sidebar', 'tp_hotel_core_report_sidebar', 10, 2 );

/**
 * @param $tab, $range
 * @return file if file exists
 */
function tp_hotel_core_report_sidebar( $tab = '', $range = '' ) {
    if ( !$tab || !$range )
        return;

    $file = apply_filters( "tp_hotel_booking_chart_sidebar_{$tab}_{$range}", '', $tab, $range );

    if ( !$file || !file_exists( $file ) )
        $file = apply_filters( "hotel_booking_chart_sidebar_layout", '', $tab, $range );

    if ( file_exists( $file ) )
        require $file;
}

add_action( 'hotel_booking_chart_canvas', 'hotel_report_canvas', 10, 2 );

/**
 * @param $tab, $range
 * @return html file canvas
 */
function hotel_report_canvas( $tab = '', $range = '' ) {
    if ( !$tab || !$range )
        return;

    $file = apply_filters( "tp_hotel_booking_chart_{$tab}_{$range}_canvas", '', $tab, $range );

    if ( !$file || !file_exists( $file ) )
        $file = apply_filters( "hotel_booking_chart_layout_canvas", '', $tab, $range );

    if ( file_exists( $file ) )
        require $file;
}

add_filter( 'hotel_booking_chart_sidebar_layout', 'hb_report_sidebar_layout', 10, 3 );

if ( !function_exists( 'hb_report_sidebar_layout' ) ) {

    function hb_report_sidebar_layout( $file, $tab, $range ) {
        $tab_range = TP_HB_REPORT_DIR . 'inc/views/sidebar-' . $tab . '-' . $range . '.php';
        $tab = TP_HB_REPORT_DIR . 'inc/views/sidebar-' . $tab . '.php';
        if ( file_exists( $tab_range ) ) {
            return $tab_range;
        } else if ( file_exists( $tab ) ) {
            return $tab;
        }

        return TP_HB_REPORT_DIR . 'inc/views/sidebar.php';
    }

}

add_filter( 'hotel_booking_chart_layout_canvas', 'hb_report_layout_canvas', 10, 3 );
if ( !function_exists( 'hb_report_layout_canvas' ) ) {

    function hb_report_layout_canvas( $file, $tab, $range ) {
        $file = TP_HB_REPORT_DIR . 'inc/views/canvas-' . strtolower( $tab ) . '.php';
        if ( file_exists( $file ) )
            return $file;
    }

}