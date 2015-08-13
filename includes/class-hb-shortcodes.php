<?php

/**
 * Class HB_Shortcodes
 */
class HB_Shortcodes{

    /**
     * Initial
     */
    static function init(){
        add_shortcode( 'hotel_booking', array( __CLASS__, 'hotel_booking' ) );
    }

    /**
     * Shortcode to display the search form
     *
     * @param $atts
     * @return string
     */
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
                'max_child'         => hb_get_request( 'max_child' ),
                'search_page'       => null
            )
        );
        $page = hb_get_request( 'hotel-booking' );
        $template = 'search.php';
        $template_args = array();

        // find the url for form action
        $search_permalink = '';
        if( $search_page = $atts['search_page'] ){
            if( is_numeric( $search_page ) ){
                $search_permalink = get_the_permalink( $search_page );
            }else{
                $search_permalink = $search_page;
            }
        }else{
            global $post;
            if( $post && ( $post_id = $post->ID ) && is_page( $post_id ) ){
                $search_permalink = get_the_permalink( $post_id );
            }
        }
        $template_args['search_page'] = $search_permalink;

        /**
         * Display the template based on current step
         */
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
                    if( ! $num_of_rooms ) continue;
                    $cart->add_to_cart( $room_id, $num_of_rooms );
                    $room = HB_Room::instance( $room_id );
                    $room->set_data( 'num_of_rooms', $num_of_rooms );
                    /*$total_rooms += $num_of_rooms;
                    $total += $room->get_total( $start_date, $end_date, $num_of_rooms, false );*/
                }
                if( is_user_logged_in() ){
                    global $current_user;
                    get_currentuserinfo();

                    $template_args['customer'] = hb_get_customer( $current_user->user_email );

                }else{
                    $template_args['customer'] = hb_create_empty_post();
                    $template_args['customer']->data = array(
                        'title'             => '',
                        'first_name'        => '',
                        'last_name'         => '',
                        'address'           => '',
                        'city'              => '',
                        'state'             => '',
                        'postal_code'       => '',
                        'country'           => '',
                        'phone'             => '',
                        'fax'               => ''
                    );
                }
                $template = 'payment.php';
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

// Init
HB_Shortcodes::init();