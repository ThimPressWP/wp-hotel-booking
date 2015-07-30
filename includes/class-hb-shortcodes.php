<?php
class HB_Shortcodes{
    static function init(){
        add_shortcode( 'hotel_booking', array( __CLASS__, 'hotel_booking' ) );
    }

    static function hotel_booking( $atts ){
        if( ! class_exists( 'HB_Room' ) ){
            TP_Hotel_Booking::instance()->_include( 'includes/class-hb-room.php' );
        }
        $atts = wp_parse_args(
            $atts,
            array(
                'check_in_date'     => hb_get_request( 'check_in_date' ),
                'check_out_date'    => hb_get_request( 'check_out_date' ),
                'adults'            => hb_get_request( 'adults' ),
                'max_child'         => hb_get_request( 'max_child' )
            )
        );
        $page = hb_get_request( 'hotel-booking' );
        $template = 'search.php';
        switch( $page ){
            case 'results':
                $template = 'results.php';
                $atts['results'] = hb_search_rooms(
                    array(
                        'check_in_date'     => $atts['check_in_date'],
                        'check_out_date'    => $atts['check_out_date'],
                        'adults'            => $atts['adults'],
                        'max_child'         => $atts['max_child']
                    )
                );
                break;
            case 'payment':
                $template = 'payment.php';
                break;
            case 'confirm':
                $template = 'confirm.php';
            case 'complete':
                $template = 'message.php';
        }
        return hb_get_template_content( $template, $atts );
    }
}

HB_Shortcodes::init();