<?php
/**
 * WP Hotel Booking comment.
 *
 * @version       1.9.6
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Classes
 * @category      Classes
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

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
		add_action( 'comment_form_after', array( $this, 'add_form_enctype_end' ) );
	}

	/**
	 * @return void
	 */
	public function add_form_enctype_end() {
		if ( ! is_room() ) {
			return;
		}

		$content = ob_get_clean();
		$content = str_replace( '<form', '<form enctype="multipart/form-data"', $content );

		print( $content );
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
	 *
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
			trailingslashit( WP_Hotel_Booking::instance()->plugin_path( 'templates/' ) ),
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
		$comment = get_comment( $comment_id );
		$postID = absint( $comment->comment_post_ID );

		if ( isset( $_POST['rating'] ) && 'hb_room' === get_post_type( $_POST['comment_post_ID'] ) ) {
			$rating = absint( sanitize_text_field( $_POST['rating'] ) );
			if ( $rating && $rating <= 5 && $rating > 0 ) {
				// save comment rating
				add_comment_meta( $comment_id, 'rating', $rating, true );

				if ( $approved === 1 ) {
					// save post meta arveger_rating

					$room           = WPHB_Room::instance( $postID );
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

		//Upload images
		require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
		require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
		require_once( ABSPATH . "wp-admin" . '/includes/media.php' );

		$images = $_FILES['review-image'] ?? array();
		$attachment_ids = array();
		foreach ( $images['name'] as $key => $value ) {
			if ( ! empty( $images['name'][ $key ] ) ) {
				$file                   = array(
					'name'     => $images['name'][ $key ],
					'type'     => $images['type'][ $key ],
					'tmp_name' => $images['tmp_name'][ $key ],
					'error'    => $images['error'][ $key ],
					'size'     => $images['size'][ $key ]
				);
				$_FILES ["upload_file"] = $file;
				$attachment_id          = media_handle_upload( "upload_file", $postID );
				if ( is_wp_error( $attachment_id ) ) {
					wc_add_notice( sprintf( esc_html__( 'Error adding file: %s.', 'wp-hotel-booking' ), $attachment_id->get_error_message() ), 'error' );
					break;
				} else {
					$attachment_ids[] = $attachment_id;
				}
			}
		}

		if ( count( $attachment_ids ) ) {
			update_comment_meta( $comment_id, 'hb_review_images', $attachment_ids );
		}
	}

	static function comments_count() {
		global $hb_room;
		echo '<span class="comment-count">(' . $hb_room->get_review_count() . ')</span>';
	}

	static function addTabReviews( $tabsInfo ) {
		if ( ! comments_open() ) {
			return $tabsInfo;
		}

		$tabsInfo[] = array(
			'id'      => 'hb_room_reviews',
			'title'   => __( 'Reviews', 'wp-hotel-booking' ),
			'content' => '',
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
					$html   = array();
					$html[] = '<div class="rating">';
					if ( $rating ) :
						$html[] = '<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="' . ( sprintf( __( 'Rated %d out of 5', 'wp-hotel-booking' ), $rating ) ) . '">';
						$html[] = '<span style="width:' . ( ( $rating / 5 ) * 100 ) . '%"></span>';
						$html[] = '</div>';
					endif;
					$html[] = '</div>';
					$html   = implode( '', $html );
				} else {
					$html = __( 'No rating', 'wp-hotel-booking' );
				}
				WPHB_Helpers::print( sprintf( '%s', $html ) );
				break;
		}
	}
}

new WPHB_Comments();
