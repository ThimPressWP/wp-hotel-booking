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
        add_shortcode( 'hotel_booking_best_reviews', array( __CLASS__, 'hotel_booking_best_reviews' ) );
        add_shortcode( 'hotel_booking_lastest_reviews', array( __CLASS__, 'hotel_booking_lastest_reviews' ) );
        add_shortcode( 'hotel_booking_cart', array( __CLASS__, 'hotel_booking_cart' ) );
        add_shortcode( 'hotel_booking_checkout', array( __CLASS__, 'hotel_booking_checkout' ) );
        add_shortcode( 'hotel_booking_mini_cart', array( __CLASS__, 'hotel_booking_mini_cart' ) );
        add_action( 'wp_footer', array( __CLASS__, 'mini_cart' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'utils' ) );
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

        $template = 'search-room.php';
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
            if( $post && ( $post_id = get_the_ID() ) && is_page( $post_id ) ){
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
                if( ! isset( $atts['page'] ) || $atts['page'] !== 'results' )
                    break;

                $template = 'results.php';
                $template_args['results']   = hb_search_rooms(
                    array(
                        'check_in_date'     => $start_date,
                        'check_out_date'    => $end_date,
                        'adults'            => $adults,
                        'max_child'         => $max_child
                    )
                );
                break;
            case 'cart':
                if( ! isset( $atts['page'] ) || $atts['page'] !== 'cart' )
                    break;
                $template = 'cart.php';
                break;
            case 'checkout':
                if( ! isset( $atts['page'] ) || $atts['page'] !== 'checkout' )
                    break;
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
                $template = 'checkout.php';
                break;
            case 'confirm':
                if( ! isset( $atts['page'] ) || $atts['page'] !== 'confirm' )
                    break;
                $template = 'confirm.php';
                break;
            case 'complete':
                if( ! isset( $atts['page'] ) || $atts['page'] !== 'complete' )
                    break;
                $template = 'message.php';
                break;
            default:
                $template = 'search-room.php';
                break;
        }

        ob_start();
        do_action( 'hb_wrapper_start' );
        hb_get_template( 'shortcodes/'.$template, $template_args );
        do_action( 'hb_wrapper_end' );
        $output = ob_get_clean();
        return $output;
    }

    static function hotel_booking_slider($atts, $content = null)
    {
        $number_rooms = isset($atts['rooms']) ? (int)$atts['rooms'] : 10;
        // $posts = get_terms( 'hb_room_type', array('hide_empty' => 0)); gallery of room_type taxonmy change to gallery of room post_type

        $args = array(
                'post_type'         => 'hb_room',
                'posts_per_page'    => $number_rooms,
                'orderby'           => 'date',
                'order'             => 'DESC',
                // 'meta_key'          => '_hb_gallery'
            );
        $query = new WP_Query( $args );

        if( $query->have_posts() ):
            hb_get_template( 'shortcodes/carousel.php', array( 'atts' => $atts, 'query' => $query ) );
        endif;
    }

    static function hotel_booking_best_reviews( $atts )
    {
        $number = isset( $atts['number'] ) ? $atts['number'] : 5;
        $args = array(
                'post_type'     => 'hb_room',
                'meta_key'      => 'arveger_rating',
                'limit'         => $number,
                'order'         => 'DESC',
                'orderby'       => array( 'meta_value_num' => 'DESC' )
            );
        $query = new WP_Query( $args );

        if( $query->have_posts() ):
            hb_get_template( 'shortcodes/best_reviews.php', array( 'atts' => $atts, 'query' => $query ));
        endif;
    }

    static function hotel_booking_lastest_reviews( $atts )
    {
        $number = isset( $atts['number'] ) ? $atts['number'] : 5;
        $args = array(
                'post_type'     => 'hb_room',
                'meta_key'      => 'arveger_rating_last_modify',
                'limit'         => $number,
                'order'         => 'DESC',
                'orderby'       => array( 'meta_value_num' => 'DESC' )
            );
        $query = new WP_Query( $args );

        if( $query->have_posts() ):
            hb_get_template( 'shortcodes/lastest_reviews.php', array( 'atts' => $atts, 'query' => $query ));
        endif;
    }

    static function hotel_booking_mini_cart( $atts )
    { ?>
        <div id="hotel_booking_mini_cart_<?php echo uniqid() ?>" class="hotel_booking_mini_cart">
            <?php if( isset($atts['title']) && $atts['title'] ): ?>

                <h3><?php echo $atts['title'] ?></h3>

            <?php endif; ?>

            <?php if ( isset( $_SESSION['hb_cart'], $_SESSION['hb_cart']['products'] ) || empty( $_SESSION['hb_cart']['products'] ) ): ?>

                <?php hb_get_template( 'shortcodes/mini_cart.php' ); ?>

            <?php else: ?>

                <p class="hotel_booking_mini_cart_description"><?php _e( 'You cart is empty.', 'tp-hotel-booking' ) ?></p>

            <?php endif; ?>
        </div>
    <?php
    }

    static function mini_cart()
    { ?>
        <script type="text/html" id="tmpl-hb-minicart-item">
            <div class="hb_mini_cart_item active" data-search-key="{{ data.search_key }}" data-id="{{ data.id }}">

                <div class="hb_mini_cart_top">

                    <h4 class="hb_title">{{ data.name }}</h4>
                    <span class="hb_mini_cart_remove"><i class="fa fa-times"></i></span>

                </div>

                <div class="hb_mini_cart_number">

                    <label><?php _e( 'Quantity: ', 'tp-hotel-booking' ); ?></label>
                    <span>{{ data.quantity }}</span>

                </div>

                <div class="hb_mini_cart_price">

                    <label><?php _e( 'Price: ', 'tp-hotel-booking' ); ?></label>
                    <span>{{{ data.total }}}</span>

                </div>
            </div>
        </script>
        <script type="text/html" id="tmpl-hb-minicart-footer">
            <div class="hb_mini_cart_footer">

                <a href="<?php echo hb_get_url(array( 'hotel-booking' => 'checkout')) ?>" class="hb_button hb_checkout"><?php _e( 'Check Out', 'tp-hotel-booking' );?></a>
                <a href="<?php echo hb_get_url( array('hotel-booking' => 'cart') ); ?>" class="hb_button hb_view_cart"><?php _e( 'View Cart', 'tp-hotel-booking' );?></a>

            </div>
        </script>
        <script type="text/html" id="tmpl-hb-minicart-empty">
            <p class="hb_mini_cart_empty"><?php _e( 'Your cart is empty!', 'tp-hotel-booking' ); ?></p>
        </script>
    <?php
    }

    static function utils()
    {
        wp_enqueue_script( 'wp-util' );
    }

    static function hotel_booking_cart( $atts )
    {
        ob_start();
        do_action( 'hb_wrapper_start' );
        hb_get_template( 'shortcodes/cart.php', $atts );
        do_action( 'hb_wrapper_end' );
        $output = ob_get_clean();
        return $output;
    }

    static function hotel_booking_checkout( $atts )
    {
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

        ob_start();
        do_action( 'hb_wrapper_start' );
        hb_get_template( 'checkout.php', $template_args );
        do_action( 'hb_wrapper_end' );
        $output = ob_get_clean();
        return $output;
    }
}

// Init
HB_Shortcodes::init();