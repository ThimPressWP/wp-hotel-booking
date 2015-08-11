<?php
class HB_Ajax{
    protected static $_loaded = false;
    function __construct(){
        if( self::$_loaded ) return;

        $ajax_actions = array(
            'fetch_custom_info'         => true,
            'place_order'               => true,
            'load_room_type_galley'     => false
        );

        foreach( $ajax_actions as $action => $priv ){
            add_action( "wp_ajax_hotel_booking_{$action}", array( __CLASS__, $action ) );
            if( $priv ){
                add_action( "wp_ajax_nopriv_hotel_booking_{$action}", array( __CLASS__, $action ) );
            }
        }

        self::$_loaded = true;
    }

    static function fetch_custom_info(){
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

    static function place_order(){
        hb_customer_place_order();
    }

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
}

new HB_Ajax();