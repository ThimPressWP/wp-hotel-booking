<?php

/**
 * Class HB_Comments
 *
 * Handle actions for comments and reviews
 */
class HB_Comments{

    /**
     * Constructor
     */
    function __construct(){
        add_action( 'comment_post', array( __CLASS__, 'add_comment_rating' ), 1 );
        add_action( 'hotel_booking_single_room_before_tabs_content_hb_room_reviews', 'comments_template' );
        add_filter( 'comments_template', array( __CLASS__, 'load_comments_template' ) );
    }

    /**
     * Load template for room reviews if it is enable
     */
    function comments_template(){
        if( comments_open() ){
            comments_template();
        }
    }

    /**
     * Load template for reviews if we found a file in theme/plugin directory
     *
     * @param string $template
     * @return string
     */
    static function load_comments_template( $template ){
        if ( get_post_type() !== 'hb_room' ) {
            return $template;
        }

        $check_dirs = array(
            trailingslashit( get_stylesheet_directory() ) . 'tp-hotel-booking',
            trailingslashit( get_template_directory() ) . 'tp-hotel-booking',
            trailingslashit( get_stylesheet_directory() ),
            trailingslashit( get_template_directory() ),
            trailingslashit( TP_Hotel_Booking::instance()->plugin_path( 'templates/' ) )
        );

        foreach ( $check_dirs as $dir ) {
            if ( file_exists( trailingslashit( $dir ) . 'single-room-reviews.php' ) ) {
                return trailingslashit( $dir ) . 'single-room-reviews.php';
            }
        }
    }

    /**
     * Add comment rating
     *
     * @param int $comment_id
     */
    public static function add_comment_rating( $comment_id ) {
        if ( isset( $_POST['rating'] ) && 'hb_room' === get_post_type( $_POST['comment_post_ID'] ) ) {
            if ( ! $_POST['rating'] || $_POST['rating'] > 5 || $_POST['rating'] < 0 ) {
                return;
            }
            add_comment_meta( $comment_id, 'rating', (int) esc_attr( $_POST['rating'] ), true );
        }
    }
}

new HB_Comments();