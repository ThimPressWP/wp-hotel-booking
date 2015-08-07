<?php
class HB_Shortcodes{
    static function init(){
        add_shortcode( 'hotel_booking', array( __CLASS__, 'hotel_booking' ) );
    }

    static function hotel_booking( $atts ){
        if( ! class_exists( 'HB_Room' ) ){
            TP_Hotel_Booking::instance()->_include( 'includes/class-hb-room.php' );
        }
        $start_date     = hb_get_request( 'check_in_date' );
        $end_date       = hb_get_request( 'check_out_date' );
        $adults         = hb_get_request( 'adults' );
        $max_child      = hb_get_request( 'max_child' );

        $atts = wp_parse_args(
            $atts,
            array(
                'check_in_date'     => $start_date,
                'check_out_date'    => $end_date,
                'adults'            => hb_get_request( 'adults' ),
                'max_child'         => hb_get_request( 'max_child' )
            )
        );
        $page = hb_get_request( 'hotel-booking' );
        $template = 'search.php';
        $template_args = array();
        switch( $page ){
            case 'results':
                $template = 'results.php';
                $template_args['results'] = hb_search_rooms(
                    array(
                        'check_in_date'     => $start_date,
                        'check_out_date'    => $end_date,
                        'adults'            => $adults,
                        'max_child'         => $max_child
                    )
                );
                break;
            case 'payment':
                $rooms          = hb_get_request( 'hb-num-of-rooms' );
                $total_rooms    = 0;
                $total          = 0;
                $cart           = HB_Cart::instance();
                $cart
                    ->empty_cart()
                    ->set_option(
                        array(
                            'check_in_date'     => $start_date,
                            'check_out_date'    => $end_date
                        )
                    );
                if( $rooms ) foreach( $rooms as $room_id => $num_of_rooms ) {
                    $cart->add_to_cart( $room_id, $num_of_rooms );
                    $total_rooms += $num_of_rooms;
                    $room = HB_Room::instance( $room_id );
                    $total += $room->get_total( $start_date, $end_date, $num_of_rooms, false );
                }
                $total_nights = hb_count_nights_two_dates( $end_date, $start_date );
                $tax = hb_get_tax_settings();
                if( $tax > 0 ) {
                    $grand_total = $total + $total * $tax;
                }else{
                    $grand_total = $total;
                }
                $sig = array(
                    'check_in_date'         => hb_get_request( 'check_in_date' ),
                    'check_out_date'        => hb_get_request( 'check_out_date' ),
                    'total_nights'          => $total_nights,
                    'num_of_rooms'          => array(),
                    'sub_total_of_rooms'    => array(),
                    'total'                 => $total,
                    'grand_total'           => $grand_total
                );
                $template = 'payment.php';
                $template_args = array(
                    //'total_nights'  => $total_nights,
                    //'total_rooms'   => $total_rooms,
                    //'rooms'         => $rooms,
                    'total'         => $total,
                    'tax'           => $tax,
                    'grand_total'   => $grand_total,
                    'tos_page_id'   => hb_get_page_id( 'terms' )
                );
                break;
            case 'confirm':
                $template = 'confirm.php';
                break;
            case 'complete':
                $template = 'message.php';
                break;
        }
        return hb_get_template_content( $template, $template_args );
    }
}

HB_Shortcodes::init();