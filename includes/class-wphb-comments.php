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
		//Review gallery
		add_action( 'comment_form_after', array( $this, 'add_form_enctype_end' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_comment_metaboxes' ), 10, 2 );
		add_action( 'edit_comment', array( $this, 'save_comment_metaboxes' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_action( 'wp_footer', array( $this, 'add_submit_review_form_popup' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_action( 'pre_get_comments', array( $this, 'filter_comment_query' ) );
	}

	/**
	 * @param $query
	 *
	 * @return void
	 */
	public function filter_comment_query( $query ) {
		if ( isset( $_GET['photos_only'] ) && $_GET['photos_only'] === 'yes' ) {
			$query->query_vars['meta_query'] = array(
				'relation' => 'AND',
				array(
					'key'     => 'hb_room_review_images',
					'compare' => 'EXISTS'
				),
			);
		}


		if ( isset( $_GET['review_sort_by'] ) && ! empty( $_GET['review_sort_by'] ) ) {
			if ( $_GET['review_sort_by'] === 'newest' ) {
				$query->query_vars['orderby'] = 'comment_date_gmt';
				$query->query_vars['order']   = 'DESC';
			}

			if ( $_GET['review_sort_by'] === 'top-review' ) {
				$query->query_vars['meta_key'] = 'rating';
				$query->query_vars['orderby']  = 'meta_value_num';
				$query->query_vars['order']    = 'DESC';
			}
		}
	}

	public function register_rest_routes() {
		register_rest_route(
			'hb-room/v1',
			'/update-review',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'update_review' ),
				'args'                => array(),
				'permission_callback' => '__return_true',
			),
		);
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return false|string|WP_REST_Response
	 */
	public function update_review( \WP_REST_Request $request ) {
		if ( ! is_user_logged_in() ) {
			return $this->error( esc_html__( 'You must log in to submit a review.', 'wp-hotel-booking' ), 401 );
		}

		$params = $request->get_params();

		$enable_review_rating = WPHB_Settings::instance()->get( 'enable_review_rating' );

		if ( ! isset( $params['rating'] ) && $enable_review_rating ) {
			return $this->error( esc_html__( 'The rating is required.', 'wp-hotel-booking' ), 400 );
		}

		if ( ! isset( $params['content'] ) ) {
			return $this->error( esc_html__( 'The review content is required.', 'wp-hotel-booking' ), 400 );
		}

		if ( ! isset( $params['title'] ) ) {
			return $this->error( esc_html__( 'The review title is required.', 'wp-hotel-booking' ), 400 );
		}

		if ( ! isset( $params['room_id'] ) ) {
			return $this->error( esc_html__( 'The room id is required.', 'wp-hotel-booking' ), 400 );
		}

		$user_id = get_current_user_id();

        // Check user was comment in room
        $user_comments = get_comments( array(
            'user_id' => $user_id,
            'post_id' => $params['room_id'],
            'type'    => 'comment',
        ) );

        if ( ! empty( $user_comments ) ) {
            return $this->error( esc_html__( 'You have already reviewed this room.', 'wp-hotel-booking' ), 400 );
        }

		$user       = get_userdata( $user_id );
		$comment_id = wp_insert_comment( array(
			'comment_post_ID'      => $params['room_id'],
			'comment_author'       => $user->display_name,
			'comment_author_email' => $user->user_email,
			'comment_author_url'   => '',
			'comment_content'      => sanitize_textarea_field( $params['content'] ),
			'comment_type'         => 'comment',
			'comment_parent'       => 0,
			'user_id'              => $user_id,
			'comment_author_IP'    => '',
			'comment_agent'        => '',
			'comment_date'         => date( 'Y-m-d H:i:s' ),
			'comment_approved'     => 1,
		) );

		if ( ! $comment_id ) {
			return $this->error( esc_html__( 'Could not create review.', 'wp-hotel-booking' ), 400 );
		}

		//Update comment meta
		update_comment_meta( $comment_id, 'hb_room_review_title', sanitize_text_field( $params['title'] ) );
		if ( ! empty( $params['rating'] ) ) {
			update_comment_meta( $comment_id, 'rating', sanitize_text_field( $params['rating'] ) );
		}

		$images = $params['base64_images'] ?? '';

		if ( ! empty( $images ) ) {
			$upload_dir  = wp_upload_dir();
			$upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;

			$attachment_ids = array();

			foreach ( $images as $image ) {
				$img             = preg_replace( '/^data:image\/[a-z]+;base64,/', '', $image['base64'] );
				$img             = str_replace( ' ', '+', $img );
				$img             = WPHB_Helpers::sanitize_params_submitted( $img );
				$decoded         = base64_decode( $img );
				$filename        = sanitize_file_name( $image['name'] );
				$file_type       = sanitize_mime_type( $image['type'] );

				// Only allow image type
				$image_types_allow = [ 'image/jpeg', 'image/png', 'image/gif', 'image/webp' ];

				$validate = wp_check_filetype( $filename );
				if ( ! $validate['type'] ) {
					continue;
				} elseif ( ! in_array( $validate['type'], $image_types_allow ) ) {
                    continue;
                }

				if ( ! in_array( $file_type, $image_types_allow ) ) {
                    continue;
                }

				$hashed_filename = md5( $filename . microtime() ) . '_' . $filename;
				$upload_file = file_put_contents( $upload_path . $hashed_filename, $decoded );

				if ( $upload_file ) {
					$attachment = array(
						'post_mime_type' => $file_type,
						'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $hashed_filename ) ),
						'post_content'   => '',
						'post_status'    => 'inherit',
						'guid'           => $upload_dir['url'] . '/' . basename( $hashed_filename )
					);

					$attachment_id = wp_insert_attachment( $attachment, $upload_dir['path'] . '/' . $hashed_filename );

					if ( ! is_wp_error( $attachment_id ) && $attachment_id ) {
						$attachment_ids [] = $attachment_id;
					}
				}
			}

			if ( count( $attachment_ids ) ) {
				update_comment_meta( $comment_id, 'hb_room_review_images', $attachment_ids );
			}
		}
		wp_update_comment_count( $params['room_id'] );

		return $this->success( esc_html__( 'Submit review successfully.', 'wp-hotel-booking' ), array(
			'comment_id'   => $comment_id,
			'redirect_url' => '#comment-' . $comment_id
		) );
	}

	/**
	 * @param string $msg
	 * @param $status_code
	 *
	 * @return WP_REST_Response
	 */
	public function error( string $msg = '', $status_code = 404 ) {
		return new WP_REST_Response(
			array(
				'status'      => 'error',
				'msg'         => $msg,
				'status_code' => $status_code,
			),
		//            $status_code
		);
	}


	/**
	 * @param string $msg
	 * @param array $data
	 *
	 * @return WP_REST_Response
	 */
	public function success( string $msg = '', array $data = array() ) {
		return new WP_REST_Response(
			array(
				'status' => 'success',
				'msg'    => $msg,
				'data'   => $data,
			),
			200
		);
	}

	/**
	 * @return false|void
	 */
	public function add_submit_review_form_popup() {
		global $post;

		if ( empty( $post ) && ! is_singular( 'hb_room' ) ) {
			return false;
		}

		if ( intval(hb_settings()->get( 'enable_advanced_review' )) !== 1 ) {
			return false;
		}

		$max_images = hb_settings()->get( 'max_review_image_number' );

		if ( empty( $max_images ) ) {
			$max_images = 10;
		}

//		$max_file_size    = hb_settings()->get( 'max_review_image_number' );
//
//		if(empty($max_file_size)){
//			$max_file_size =  1000000;
//		}
		?>

        <div id="hb-room-review-form-popup">
            <div class="bg-overlay"></div>
            <form id="hb-room-submit-review-form" data-room-id="<?php echo esc_attr( $post->ID ); ?>">
                <header>
                    <h3><?php esc_html_e( 'Write a review', 'wp-hotel-booking' ); ?></h3>
                    <div class="close-form-btn">
                        <span class="dashicons dashicons-no"></span>
                    </div>
                </header>
                <main>
					<?php
					if ( WPHB_Settings::instance()->get( 'enable_review_rating' ) ) {
						?>
                        <div class="review-rating field">
                            <label for="review-rating"><?php esc_html_e( 'Rate your experience *', 'wp-hotel-booking' ); ?></label>
                            <input type="hidden" name="review-rating" value="">
                            <div class="rating-star">
								<?php
								for ( $i = 1; $i <= 5; $i ++ ) {
									?>
                                    <a class="rating-star-item" href="#"
                                       data-star-rating="<?php echo esc_attr( $i ); ?>">
                                    </a>
									<?php
								}
								?>
                            </div>
                        </div>
						<?php
					}
					?>

                    <div class="review-content field">
                        <label for="review-content"><?php esc_html_e( 'Leave a review *', 'wp-hotel-booking' ); ?></label>
                        <textarea name="review-content" id="review-content" cols="30" rows="5"></textarea>
                    </div>

                    <div class="review-title field">
                        <label for="review-title"><?php esc_html_e( 'Give your review a title *', 'wp-hotel-booking' ); ?></label>
                        <input type="text" name="review-title" id="review-title">
                    </div>


                    <div class="hb-gallery-review" data-room-id="<?php echo esc_attr( $post->ID ); ?>">
                        <div class="select-images">
                            <label for="tour_review-image">
								<?php
								printf( esc_html( _n( 'Uploads up to %s image', 'Upload up to %s images', $max_images, 'wp-hotel-booking' ) ), $max_images );
								?>
                            </label>
                            <div class="review-notice">
                            </div>
                            <div class="gallery-preview">
                            </div>
                            <label class="upload-images">
                                <span><?php esc_html_e( 'Upload', 'wp-hotel-booking' ); ?></span>
                                <input type="file" accept="image/*" multiple="multiple" name="review-image[]"
                                       id="hb-room-review-image">
                            </label>
                        </div>
                    </div>
                </main>
                <footer>
                    <p class="notice"></p>
                    <div class="submit">
                        <button type="button"><?php esc_html_e( 'Send', 'wp-hotel-booking' ); ?></button>
                        <span class="hb-room-spinner"></span>
                    </div>
                </footer>
            </form>
        </div>
		<?php
	}

	/**
	 * @param $comment_id
	 * @param $data
	 *
	 * @return void
	 */
	public function save_comment_metaboxes( $comment_id, $data ) {
		if ( isset( $_POST['hb_room_review_title'] ) ) {
			update_comment_meta( $comment_id, 'hb_room_review_title', sanitize_text_field( $_POST['hb_room_review_title'] ) );
		}

		$value = array();
		if ( isset( $_POST['hb_review_images'] ) && ! empty( $_POST['hb_review_images'] ) ) {
			$value = $_POST['hb_review_images'];
			if ( is_string( $value ) ) {
				$value = explode( ',', $value );
			}
		}

		update_comment_meta( $comment_id, 'hb_room_review_images', $value );
	}

	/**
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		//date time
		wp_register_script( 'hb-room-review', WPHB_PLUGIN_URL . '/assets/dist/js/admin/room-review.min.js',
			array(),
			uniqid(),
			true
		);
		wp_enqueue_script( 'hb-room-review' );
	}

	/**
	 * @param $type
	 * @param $comment
	 *
	 * @return void
	 */
	public function add_comment_metaboxes( $type, $comment ) {
		if ( $type !== 'comment' ) {
			return;
		}

		if ( $comment->comment_type !== 'comment' ) {
			return;
		}

		$room_id = $comment->comment_post_ID;

		if ( empty( $room_id ) ) {
			return;
		}

		if ( get_post_type( $room_id ) !== 'hb_room' ) {
			return;
		}

		if ( intval(hb_settings()->get( 'enable_advanced_review' )) !== 1 ) {
			return;
		}

		add_meta_box(
			'hb_review_title',
			esc_html__( 'Title', 'travel-booking' ),
			array( $this, 'render_review_title' ),
			array( 'comment' ),
			'normal',
			'low',
		);

		add_meta_box(
			'hb-review-image',
			esc_html__( 'Images', 'wp-hotel-booking' ),
			array( $this, 'render_review_images' ),
			array( 'comment' ),
			'normal',
			'low',
		);
	}

	/**
	 * @param $comment
	 *
	 * @return void
	 */
	public function render_review_title( $comment ) {
		$comment_id = $comment->comment_ID;
		$value      = get_comment_meta( $comment_id, 'hb_room_review_title', true );

		if ( empty( $value ) ) {
			$value = '';
		}
		?>

        <input type="text" name="hb_room_review_title" value="<?php echo esc_attr( $value ); ?>">
		<?php
	}

	/**
	 * @param $comment
	 *
	 * @return void
	 */
	public function render_review_images( $comment ) {
		$comment_id = $comment->comment_ID;
		$image_ids  = get_comment_meta( $comment_id, 'hb_room_review_images', true );
		?>
        <div class="hb-review-images">
			<?php
			$max_images    = intval( hb_settings()->get( 'max_review_image_number', 0 ) );
			$max_file_size = intval( hb_settings()->get( 'max_review_image_file_size', 0 ) );

			if ( empty( $image_ids ) ) {
				$image_ids = array();
			} else {
				if ( count( $image_ids ) > $max_images ) {

					$image_ids = array_slice( $image_ids, 0, $max_images );
				}
			}

			$value_data = implode( ',', $image_ids );
			?>
            <div class="hb-image-info"
                 data-max-file-size="<?php echo esc_attr( $max_file_size ); ?>">
                <div class="hb-gallery-inner">
                    <input type="hidden" name="hb_review_images"
                           data-number="<?php echo esc_attr( $max_images ); ?>"
                           value="<?php echo esc_attr( $value_data ); ?>" readonly/>
					<?php
					$count = count( $image_ids );
					for ( $i = 0; $i < $count; $i ++ ) {
						$data_id = empty( $image_ids[ $i ] ) ? '' : $image_ids[ $i ];
						$img_src = '';
						if ( ! empty( wp_get_attachment_image_url( $data_id, 'thumbnail' ) ) ) {
							$img_src = wp_get_attachment_image_url( $data_id, 'thumbnail' );
						}
						$alt_text = '#';
						?>
                        <div class="hb-gallery-preview" data-id="<?php echo esc_attr( $data_id ); ?>">
                            <div class="hb-gallery-centered">
                                <img src="<?php echo esc_url_raw( $img_src ); ?>"
                                     alt="<?php echo esc_attr( $alt_text ); ?>">
                            </div>
                            <span class="hb-gallery-remove dashicons dashicons dashicons-no-alt"></span>
                        </div>
						<?php
					}
					?>
                    <button type="button"
                            class="button hb-gallery-add"><?php esc_html_e( 'Add Images', 'wp-hotel-booking' ); ?></button>
                </div>
            </div>
            <p class="image-description"><?php printf(
					esc_html__(
						'You can upload maximum %1$s images. The maximum file size is %2$s KB.',
						'wp-hotel-booking'
					),
					$max_images,
					$max_file_size,
				); ?></p>
        </div>

		<?php
		if ( ! did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}
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
		$postID  = absint( $comment->comment_post_ID );

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

	/**
	 * @param $rating
	 * @param $post_id
	 *
	 * @return string|null
	 */
	public static function get_review_count_by_rating( $rating, $post_id ) {
		global $wpdb;
		$comment_tbl      = $wpdb->comments;
		$comment_meta_tbl = $wpdb->commentmeta;

		return $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(*) meta_value FROM $comment_meta_tbl LEFT JOIN $comment_tbl ON 
                $comment_meta_tbl.comment_id = $comment_tbl.comment_ID WHERE meta_key = 'rating' AND comment_post_ID = %s 
              AND comment_approved = '1' AND meta_value = %s",
				$post_id,
				$rating
			)
		);
	}
}

new WPHB_Comments();
