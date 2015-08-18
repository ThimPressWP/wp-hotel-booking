<?php
if( ! get_option( 'tp_hotel_booking_ready' ) ){
    $options = array(
        'tp_hotel_booking_advance_payment' => '50',
        'tp_hotel_booking_advanced_payment' => '25',
        'tp_hotel_booking_currency' => 'USD',
        'tp_hotel_booking_hotel_address' => 'Ha Noi',
        'tp_hotel_booking_hotel_city' => 'Ha Noi',
        'tp_hotel_booking_hotel_country' => 'Viet Nam',
        'tp_hotel_booking_hotel_email_address' => '',
        'tp_hotel_booking_hotel_fax_number' => '',
        'tp_hotel_booking_hotel_name' => 'Hanoi Daewoo Hotel',
        'tp_hotel_booking_hotel_phone_number' => '',
        'tp_hotel_booking_hotel_state' => 'Ha Noi',
        'tp_hotel_booking_hotel_zip_code' => '1000',
        'tp_hotel_booking_lightbox' => 'a:1:{s:8:"lightbox";s:0:"";}',
        'tp_hotel_booking_offline-payment' => 'a:3:{s:6:"enable";s:2:"on";s:13:"email_subject";s:23:"Confirm Booking Details";s:13:"email_content";s:126:"Hi, {{customer_name}},

We send you the details of your booking on {{site_name}}

{{booking_details}}

Thank for booking";}',
        'tp_hotel_booking_offline-payment_email_content' => 'Hi, {{customer_name}},

We send you the details of your booking on {{site_name}}

{{booking_details}}

Thank for booking',
        'tp_hotel_booking_paypal' => 'a:4:{s:6:"enable";s:2:"on";s:5:"email";s:20:"your-email@gmail.com";s:7:"sandbox";s:2:"on";s:13:"sandbox_email";s:28:"your-email-sandbox@gmail.com";}',
        'tp_hotel_booking_price_currency_position' => 'left',
        'tp_hotel_booking_price_decimals_separator' => '.',
        'tp_hotel_booking_price_including_tax' => '0',
        'tp_hotel_booking_price_number_of_decimal' => '2',
        'tp_hotel_booking_price_thousands_separator' => ',',
        'tp_hotel_booking_require_advance_payment' => 'off',
        'tp_hotel_booking_search_page_id' => '0',
        'tp_hotel_booking_stripe' => 'a:1:{s:6:"enable";s:2:"on";}',
        'tp_hotel_booking_tax' => '10',
        'tp_hotel_booking_terms_page_id' => '0'
    );
    /*global $wpdb;
    $query = "
        SELECT option_name, option_value
        FROM {$wpdb->options}
        WHERE option_name LIKE 'tp_hotel_booking%'
    ";
    $rows = $wpdb->get_results($query);
    ob_start();
    echo '$options = array(';
    foreach( $rows as $row ){
        echo "\n\t'" . $row->option_name . "' => '" . $row->option_value . "',";
    }
    echo ');';
    file_put_contents( TP_Hotel_Booking::instance()->plugin_path( 'install.txt'), ob_get_clean());*/
    foreach( $options as $k => $v ){
        update_option( $k, $v );
    }
    update_option( 'tp_hotel_booking_ready', '1' );
}