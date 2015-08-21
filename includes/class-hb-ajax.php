<?php

/**
 * Class HB_Ajax
 */
class HB_Ajax{

    /**
     * @var bool
     */
    protected static $_loaded = false;

    /**
     * Constructor
     */
    function __construct(){
        if( self::$_loaded ) return;

        $ajax_actions = array(
            'fetch_customer_info'       => true,
            'place_order'               => true,
            'load_room_type_galley'     => false,
            'parse_search_params'       => true,
            'parse_booking_params'      => true,
            'apply_coupon'              => true,
            'remove_coupon'             => true
        );

        foreach( $ajax_actions as $action => $priv ){
            add_action( "wp_ajax_hotel_booking_{$action}", array( __CLASS__, $action ) );
            if( $priv ){
                add_action( "wp_ajax_nopriv_hotel_booking_{$action}", array( __CLASS__, $action ) );
            }
        }

        self::$_loaded = true;
    }

    /**
     * Fetch customer information with user email
     */
    static function fetch_customer_info(){
        $email = hb_get_request( 'email' );
        $query_args = array(
            'post_type'     => 'hb_customer',
            'meta_query' => array(
                array(
                    'key' => '_hb_email',
                    'value' => $email,
                    'compare' => 'EQUALS'
                ),
            )
        );
        if( $posts = get_posts( $query_args ) ){
            $customer = $posts[0];
            $customer->data = array();
            $data = get_post_meta( $customer->ID );
            foreach( $data as $k => $v ) {
                $customer->data[$k] = $v[0];
            }
        }else{
            $customer = null;
        }
        hb_send_json( $customer );
        die();
    }

    /**
     * Process the order with customer information posted via form
     *
     * @throws Exception
     */
    static function place_order(){
        hb_customer_place_order();
    }

    /**
     * Get all images for a room type
     */
    static function load_room_type_galley(){
        $term_id = hb_get_request( 'term_id' );
        $attachment_ids = get_option( 'hb_taxonomy_thumbnail_' . $term_id );
        $attachments = array();
        if( $attachment_ids ) foreach( $attachment_ids as $id ){
            $attachment = wp_get_attachment_image_src( $id, 'thumbnail' );
            $attachments[] = array(
                'id'    => $id,
                'src'   => $attachment[0]
            );
        }
        hb_send_json( $attachments );
    }

    /**
     * Catch variables via post method and build a request param
     */
    static function parse_search_params(){
        /*if ( ! hb_get_request( 'nonce', $_POST ) || ! wp_verify_nonce( hb_get_request( 'nonce', $_POST ), 'hb_search_nonce_action' ) ) {
            hb_send_json( array( 'success' => 0, 'message' => __( 'Invalid request', 'tp-hotel-booking' ) ) );
        }*/
        check_ajax_referer( 'hb_search_nonce_action', 'nonce' );

        $check_in   = hb_get_request( 'check_in_date' );
        $check_out  = hb_get_request( 'check_out_date' );
        $adults_capacity     = hb_get_request( 'adults_capacity' );
        $max_child  = hb_get_request( 'max_child' );

        $params = array(
            'hotel-booking'     => hb_get_request( 'hotel-booking' ),
            'check_in_date'     => $check_in,
            'check_out_date'    => $check_out,
            'adults'            => $adults_capacity,
            'max_child'         => $max_child
        );
        hb_send_json(
            array(
                'success'   => 1,
                'sig'       => base64_encode( serialize( $params ) )
            )
        );
    }

    static function apply_coupon(){
        ! session_id() && session_start();
        $code = hb_get_request( 'code' );
        ob_start();
        $today = strtotime( date('m/d/Y') );
        $coupon = hb_get_coupons_active( $today, $code );
        $output = ob_get_clean();
        $response = array();
        if( $coupon ){
            $coupon = HB_Coupon::instance( $coupon );
            $response = $coupon->validate();
            if( $response['is_valid'] ) {
                $response['result'] = 'success';
                $response['type'] = get_post_meta($coupon->ID, '_hb_coupon_discount_type', true);
                $response['value'] = get_post_meta($coupon->ID, '_hb_coupon_discount_value', true);
                if (!session_id()) {
                    session_start();
                }
                set_transient('hb_user_coupon_' . session_id(), $coupon, HOUR_IN_SECONDS);
                hb_add_message( __( 'Coupon code applied', 'tp-hotel-booking' ) );
            }
        }else{
            $response['message'] = __( 'Coupon does not exist!', 'tp-hotel-booking' );
        }
        hb_send_json(
            $response
        );
    }

    static function remove_coupon(){
        ! session_id() && session_start();
        delete_transient( 'hb_user_coupon_' . session_id() );
        hb_add_message( __( 'Coupon code removed', 'tp-hotel-booking' ) );
        hb_send_json(
            array(
                'result' => 'success'
            )
        );

    }

    static function parse_booking_params(){
        /*if ( ! hb_get_request( 'nonce', $_POST ) || ! wp_verify_nonce( hb_get_request( 'nonce', $_POST ), 'hb_booking_nonce_action' ) ) {
            hb_send_json( array( 'success' => 0, 'message' => __( 'Invalid request', 'tp-hotel-booking' ) ) );
        }*/

        check_ajax_referer( 'hb_booking_nonce_action', 'nonce' );

        $check_in       = hb_get_request( 'check_in_date' );
        $check_out      = hb_get_request( 'check_out_date' );
        $num_of_rooms   = hb_get_request( 'hb-num-of-rooms' );

        $params = array(
            'hotel-booking'     => hb_get_request( 'hotel-booking' ),
            'check_in_date'     => $check_in,
            'check_out_date'    => $check_out,
            'hb-num-of-rooms'   => $num_of_rooms
        );

        //print_r($params);
        hb_send_json(
            array(
                'success'   => 1,
                'sig'       => base64_encode( serialize( $params ) )
            )
        );
    }

}

new HB_Ajax();