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
        add_shortcode( 'hotel_booking_slider', array( __CLASS__, 'hotel_booking_slider' ) );
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
        *  Add argument use in shortcode display
        */
        $template_args['atts']         = $atts;

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
        ob_start();
        do_action( 'hb_wrapper_start' );
        hb_get_template( $template, $template_args );
        do_action( 'hb_wrapper_end' );
        $output = ob_get_clean();
        return $output;
    }

    static function hotel_booking_slider($atts, $content = null)
    {
        $number_rooms = isset($atts['rooms']) ? (int)$atts['rooms'] : 10;
        $size = isset($atts['image_size']) ? $atts['image_size'] : 'thumbnail';
        $items = isset($atts['number']) ? (int)$atts['number'] : 4;
        // $posts = get_terms( 'hb_room_type', array('hide_empty' => 0)); gallery of room_type taxonmy change to gallery of room post_type
        $posts = get_posts( array(
                'post_type'         => 'hb_room',
                'posts_per_page'    => $number_rooms,
                'orderby'           => 'date',
                'order'             => 'DESC',
                // 'meta_key'          => '_hb_gallery'
            ) );
        $currentcy = hb_get_currency_symbol();
        $sliderId = 'hotel_booking_slider_'.uniqid();
        $upload_dir = wp_upload_dir();
        $upload_base_dir = $upload_dir['basedir'];
        $upload_base_url = $upload_dir['baseurl'];
        if( $posts ):
    ?>
            <div id="<?php echo $sliderId ?>" class="hb_room_carousel_container">
                <!--navigation-->
                <?php if( !isset($atts['navigation']) || $atts['navigation'] ): ?>
                    <div class="navigation">
                        <div class="prev"><i class="fa fa-angle-left"></i></div>
                        <div class="next"><i class="fa fa-angle-right"></i></div>
                    </div>
                <?php endif; ?>
                <!--pagination-->
                <?php if( !isset($atts['pagination']) || $atts['pagination'] ): ?>
                    <div class="pagination"></div>
                <?php endif; ?>
                <!--text_link-->
                <?php if( isset($atts['text_link']) && $atts['text_link'] !== '' ): ?>
                    <div class="text_link"><a href="<?php echo esc_url(get_post_type_archive_link('hb_room')) ?>"><?php echo $atts['text_link']; ?></a></div>
                <?php endif; ?>
                <div class="hb_room_carousel">
                    <?php foreach ($posts as $key => $post): ?>
                        <?php $galleries = get_post_meta( $post->ID, '_hb_gallery', true ); ?>
                        <?php
                            $prices = array();
                            if( function_exists('hb_get_price_plan_room') )
                            {
                                $prices = hb_get_price_plan_room($post->ID);
                                sort($prices);
                            }
                            $currency = get_option( 'tp_hotel_booking_currency' );
                            $title = $post->post_title;
                        ?>
                            <div class="item">
                                <div class="media">
                                    <a href="<?php echo esc_url(get_the_permalink($post->ID)); ?>" class="media-image" title="<?php echo esc_attr($title); ?>">
                                    <?php echo get_the_post_thumbnail( $post->ID, $size); ?>
                                    </a>
                                </div>
                                <div class="title">
                                    <h4>
                                        <a href="<?php echo esc_url(get_the_permalink($post->ID)); ?>" class="media-image"><?php echo $title; ?></a>
                                    </h4>
                                </div>
                                <?php if( (!isset($atts['price']) || $atts['price'] !== '*') && $prices ): ?>
                                    <div class="price">
                                        <span>
                                            <?php
                                                $current = current($prices);
                                                $end = end($prices);
                                                if( $current !== $end && $atts['price'] === 'min_to_max' )
                                                {
                                                    echo $currentcy.$current . ' - ' . $end;
                                                }
                                                else
                                                {
                                                    echo $currentcy.$current;
                                                }
                                            ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                    <?php endforeach;?>
                </div>
            </div>
            <script type="text/javascript">
                (function($){
                    "use strict";
                    $(document).ready(function(){
                        $('#<?php echo $sliderId ?> .hb_room_carousel').carouFredSel({
                            responsive: true,
                            items: {
                                height: 'auto',
                                visible: {
                                    min: <?php echo $items ?>,
                                    max: <?php echo $items ?>
                                }
                            },
                            width: 'auto',
                            prev: {
                                button: '#<?php echo $sliderId; ?> .navigation .prev'
                            },
                            next: {
                                button: '#<?php echo $sliderId; ?> .navigation .next'
                            },
                            pagination: '#<?php echo $sliderId; ?> > .pagination',
                            mousewheel: true,
                            pauseOnHover: true,
                            onCreate: function()
                            {

                            },
                            swipe: {
                                onTouch: true,
                                onMouse: true
                            },
                            scroll : {
                                items           : 1,
                                easing          : "swing",
                                duration        : 700,
                                pauseOnHover    : true
                            }
                        });
                    });
                })(jQuery);
            </script>
    <?php
        endif;
    }
}

// Init
HB_Shortcodes::init();