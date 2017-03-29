<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class WPHB_Comments
 *
 * Handle actions for comments and reviews
 */
class WPHB_Comments {

    /**
     * Constructor
     */
    function __construct() {
        add_action( 'comment_post', array( __CLASS__, 'add_comment_rating' ), 10, 2 );
        add_action( 'hotel_booking_single_room_before_tabs_content_hb_room_reviews', 'comments_template' );
        add_filter( 'comments_template', array( __CLASS__, 'load_comments_template' ) );
        // details title tab
        add_action( 'hotel_booking_single_room_after_tabs_hb_room_reviews', array( __CLASS__, 'comments_count' ) );
        add_filter( 'hotel_booking_single_room_infomation_tabs', array( __CLASS__, 'addTabReviews' ) );

        add_filter( 'manage_edit-comments_columns', array( $this, 'comments_column' ), 10, 2 );
        add_filter( 'manage_comments_custom_column', array( $this, 'comments_custom_column' ), 10, 2 );
    }

    /**
     * Load template for room reviews if it is enable
     */
    function comments_template() {
        if ( comments_open() ) {
            comments_template();
        }
    }

    /**
     * Load template for reviews if we found a file in theme/plugin directory
     *
     * @param string $template
     * @return string
     */
    static function load_comments_template( $template ) {
        if ( get_post_type() !== 'hb_room' ) {
            return $template;
        }

        $check_dirs = array(
            trailingslashit( get_stylesheet_directory() ) . 'wp-hotel-booking',
            trailingslashit( get_template_directory() ) . 'wp-hotel-booking',
            trailingslashit( get_stylesheet_directory() ),
            trailingslashit( get_template_directory() ),
            trailingslashit( WP_Hotel_Booking::instance()->plugin_path( 'templates/' ) )
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
    public static function add_comment_rating( $comment_id, $approved ) {
        if ( isset( $_POST['rating'] ) && 'hb_room' === get_post_type( $_POST['comment_post_ID'] ) ) {
            $rating = absint( sanitize_text_field( $_POST['rating'] ) );
            if ( $rating && $rating <= 5 && $rating > 0 ) {
                // save comment rating
                add_comment_meta( $comment_id, 'rating', $rating, true );

                if ( $approved === 1 ) {
                    // save post meta arveger_rating
                    $comment = get_comment( $comment_id );

                    $postID = absint( $comment->comment_post_ID );

                    $room = WPHB_Room::instance( $postID );
                    $averger_rating = $room->average_rating();

                    $old_rating = get_post_meta( $postID, 'arveger_rating', true );
                    $old_modify = get_post_meta( $postID, 'arveger_rating_last_modify', true );
                    if ( $old_rating ) {
                        update_post_meta( $postID, 'arveger_rating', $averger_rating );
                        update_post_meta( $postID, 'arveger_rating_last_modify', time() );
                    } else {
                        add_post_meta( $postID, 'arveger_rating', $averger_rating );
                        add_post_meta( $postID, 'arveger_rating_last_modify', time() );
                    }
                }
            }
        }
    }

    static function comments_count() {
        global $hb_room;
        echo '<span class="comment-count">(' . $hb_room->get_review_count() . ')</span>';
    }

    static function addTabReviews( $tabsInfo ) {
        if ( !comments_open() )
            return $tabsInfo;

        $tabsInfo[] = array(
            'id' => 'hb_room_reviews',
            'title' => __( 'Reviews', 'wp-hotel-booking' ),
            'content' => ''
        );

        return $tabsInfo;
    }

    function comments_column( $columns ) {
        $columns['hb_rating'] = __( 'Rating Room', 'wp-hotel-booking' );
        return $columns;
    }

    function comments_custom_column( $column, $comment_id ) {
        switch ( $column ) {
            case 'hb_rating':
                if ( $rating = get_comment_meta( $comment_id, 'rating', true ) ) {
                    $html = array();
                    $html[] = '<div class="rating">';
                    if ( $rating ):
                        $html[] = '<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="' . ( sprintf( __( 'Rated %d out of 5', 'wp-hotel-booking' ), $rating ) ) . '">';
                        $html[] = '<span style="width:' . ( ( $rating / 5 ) * 100 ) . '%"></span>';
                        $html[] = '</div>';
                    endif;
                    $html[] = '</div>';
                    $html = implode( '', $html );
                }
                else {
                    $html = __( 'No rating', 'wp-hotel-booking' );
                }
                echo sprintf( '%s', $html );
                break;
        }
    }

}

new WPHB_Comments();
