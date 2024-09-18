<?php
/**
 * The template for displaying room reviews (comment).
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/single-room-reviews.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */

use WPHB\TemplateHooks\SingleRoomTemplate;

defined( 'ABSPATH' ) || exit();

global $hb_room, $hb_settings;

/**
 * @var $hb_room WPHB_Room
 * @var $hb_settings WPHB_Settings
 */

if ( ! comments_open() ) {
	return;
}

$room_id                = get_the_ID();
$room                   = WPHB_Room::instance( $room_id );
$average_rating         = round( $room->average_rating(), 2 );
$count                  = intval( $room->get_review_count() );
$enable_advanced_review = hb_settings()->get( 'enable_advanced_review' ) === '1';
// Check user was comment in room
$user_comments = get_comments( array(
	'user_id' => get_current_user_id(),
	'post_id' => $room_id,
	'type'    => 'comment',
) );
?>

<div id="reviews">
    <!--    Review top section-->
	<?php
	if ( $enable_advanced_review ) {
		?>
        <div class="review-top-section">
            <div class="header">
                <h2 class="title"><?php esc_html_e( 'Review', 'wp-hotel-booking' ); ?></h2>
				<?php
				if ( is_user_logged_in() && empty( $user_comments ) ) {
					?>
                    <button type="button" id="hb-room-add-new-review">
                        <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.3751 21.1746H4.47194C4.37679 21.1758 4.28236 21.158 4.1942 21.1222C4.10605 21.0863 4.02597 21.0332 3.95869 20.9659C3.8914 20.8987 3.83828 20.8186 3.80244 20.7304C3.76661 20.6423 3.7488 20.5478 3.75006 20.4527V12.5496C3.75006 10.2621 4.65877 8.06827 6.27627 6.45076C7.89377 4.83326 10.0876 3.92456 12.3751 3.92456V3.92456C13.5077 3.92456 14.6293 4.14765 15.6757 4.5811C16.7221 5.01455 17.673 5.64986 18.4739 6.45076C19.2748 7.25167 19.9101 8.20248 20.3435 9.24892C20.777 10.2953 21.0001 11.4169 21.0001 12.5496V12.5496C21.0001 13.6822 20.777 14.8038 20.3435 15.8502C19.9101 16.8966 19.2748 17.8475 18.4739 18.6484C17.673 19.4493 16.7221 20.0846 15.6757 20.518C14.6293 20.9515 13.5077 21.1746 12.3751 21.1746V21.1746Z"
                                  stroke="#01AA90" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12.75 12.9246C12.75 13.1317 12.5821 13.2996 12.375 13.2996C12.1679 13.2996 12 13.1317 12 12.9246C12 12.7175 12.1679 12.5496 12.375 12.5496C12.5821 12.5496 12.75 12.7175 12.75 12.9246Z"
                                  fill="#01AA90" stroke="#01AA90" stroke-width="1.5"/>
                            <path d="M7.875 14.0496C8.49632 14.0496 9 13.5459 9 12.9246C9 12.3032 8.49632 11.7996 7.875 11.7996C7.25368 11.7996 6.75 12.3032 6.75 12.9246C6.75 13.5459 7.25368 14.0496 7.875 14.0496Z"
                                  fill="#01AA90"/>
                            <path d="M16.875 14.0496C17.4963 14.0496 18 13.5459 18 12.9246C18 12.3032 17.4963 11.7996 16.875 11.7996C16.2537 11.7996 15.75 12.3032 15.75 12.9246C15.75 13.5459 16.2537 14.0496 16.875 14.0496Z"
                                  fill="#01AA90"/>
                        </svg>
						<?php esc_html_e( 'Write a Review', 'wp-hotel-booking' ); ?>
                    </button>
					<?php
				}
				?>
            </div>
            <div class="statistic">
                <div class="statistic-general">
					<?php
					if ( $hb_settings->get( 'enable_review_rating' ) ) {
						?>
                        <div class="review-average-rating">
                            <div class="average-rating">
								<?php
								printf( '%s/5', $average_rating );
								?>
                            </div>
                            <div class="rating">
								<?php
								//echo wc_get_rating_html( $average_rating );
								echo SingleRoomTemplate::instance()->html_rating_info( $average_rating );
								?>
                            </div>
                        </div>
						<?php
					}
					?>
                    <div class="review-count">
						<?php
						printf( esc_html( _n( '%s Review', '%s Reviews', $count, 'wp-hotel-booking' ) ), $count );
						?>
                    </div>
                </div>
				<?php
				if ( $hb_settings->get( 'enable_review_rating' ) ) {
					?>
                    <div class="statistic-detail">
						<?php
						for ( $i = 5; $i > 0; $i -- ) {
							$review_count = WPHB_Comments::get_review_count_by_rating( $i, $room_id );
							if ( $count === 0 ) {
								$percent = 0;
							} else {
								$percent = ( $review_count / $count ) * 100;
							}
							?>
                            <div class="statistic-detail-item" data-rating="<?php echo esc_attr( $i ); ?>">
                                <div class="rating-label">
									<?php
									printf( esc_html( _n( '%s star', '%s stars', $i, 'wp-hotel-booking' ) ), $i );
									?>
                                </div>
                                <div class="full-width">
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"
                                             style="width:<?php echo esc_attr( $percent ); ?>%"></div>
                                    </div>
                                </div>

                                <div class="rating-number">
									<?php echo esc_html( $review_count ); ?>
                                </div>
                            </div>
							<?php
						}
						?>
                    </div>
					<?php
				}
				?>
            </div>
        </div>
		<?php
	}
	?>
    <!--    End review top section-->
    <div id="comments">
		<?php
		if ( $enable_advanced_review ) {
			?>
            <div class="hb-room-commentlist-sort-filter">
				<?php
				global $wp_rewrite;
				$origin_link = get_the_permalink() . '/' . $wp_rewrite->comments_pagination_base . '-1';

				?>
                <div class="gallery-filter">
					<?php
					$link = $origin_link;
					if ( isset( $_GET['review_sort_by'] ) ) {
						$review_sort_by = $_GET['review_sort_by'];
						$link           = add_query_arg( 'review_sort_by', $review_sort_by, $origin_link );
					}
					$photos_only = $_GET['photos_only'] ?? '';
					?>
                    <a class="<?php echo empty( $photos_only ) || $photos_only === 'no' ? 'active' : ''; ?>"
                       href="<?php echo add_query_arg( array(
						   'photos_only' => 'no',
						   'tab'         => 'review'
					   ), $link ); ?>"><?php esc_html_e( 'All', 'wp-hotel-booking' ); ?></a>
                    <a class="<?php echo $photos_only === 'yes' ? 'active' : ''; ?>"
                       href="<?php echo add_query_arg( array(
						   'photos_only' => 'yes',
						   'tab'         => 'review'
					   ), $link ); ?>"><?php esc_html_e( 'With Photos Only', 'wp-hotel-booking' ); ?></a>
                </div>
                <div class="sort-by">
                    <div class="label"><?php esc_html_e( 'Sort by', 'wp-hotel-booking' ); ?></div>
                    <div class="option">
						<?php
						$review_sort_by = $_GET['review_sort_by'] ?? '';
						$toggle         = __( 'Oldest', 'wp-hotel-booking' );
						$icon_dropdown  = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
					<path d="M13 6.92456L8 11.9246L3 6.92456" stroke="#121212" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				  </svg>';
						if ( $review_sort_by === 'newest' ) {
							$toggle = __( 'Newest', 'wp-hotel-booking' );
						} else if ( $review_sort_by === 'top-review' ) {
							if ( $hb_settings->get( 'enable_review_rating' ) ) {
								$toggle = __( 'Top Review', 'wp-hotel-booking' );
							} else {
								$toggle = '';
							}
						}
						?>
                        <div class="toggle" data-value="top-oldest"><?php echo esc_html( $toggle );
							echo $icon_dropdown; ?></div>
                        <ul id="hb-room-sort-by">
							<?php
							$link = $origin_link;

							if ( isset( $_GET['photos_only'] ) ) {
								$photos_only = LP_Helper::sanitize_params_submitted( 'photos_only', 'key' );

								$link = esc_url_raw( add_query_arg( 'photos_only', $photos_only, $origin_link ) );
							}
							?>
                            <li class="<?php echo $review_sort_by === 'oldest' ? 'active' : ''; ?>">
                                <a class="hb-room-sort-by-option"
                                   href="<?php echo add_query_arg( array(
									   'review_sort_by' => 'oldest',
									   'tab'            => 'review'
								   ), $link ); ?>">
									<?php esc_html_e( 'Oldest', 'wp-hotel-booking' ); ?></a>
                            </li>
                            <li class="<?php echo $review_sort_by === 'newest' ? 'active' : ''; ?>">
                                <a class="hb-room-sort-by-option"
                                   href="<?php echo add_query_arg( array(
									   'review_sort_by' => 'newest',
									   'tab'            => 'review'
								   ), $link ); ?>">
									<?php esc_html_e( 'Newest', 'wp-hotel-booking' ); ?></a>
                            </li>
                            <?php
                            if ( $hb_settings->get( 'enable_review_rating' ) ) {
                                ?>
                                <li class="<?php echo $review_sort_by === 'top-review' ? 'active' : ''; ?>">
                                    <a class="hb-room-sort-by-option"
                                       href="<?php echo add_query_arg( array(
				                               'review_sort_by' => 'top-review',
				                               'tab'            => 'review'
			                               ), $link ) . '#tab-reviews'; ?>">
			                            <?php esc_html_e( 'Top Review', 'wp-hotel-booking' ); ?></a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
			<?php
		}
		?>
        <h2>
			<?php
			if ( $hb_settings->get( 'enable_review_rating' ) && ( $count = $hb_room->get_review_count() ) ) {
				printf( _n( '%1$s review for %2$s', '%1$s reviews for %2$s', $count, 'wp-hotel-booking' ), $count, get_the_title() );
			} else {
				_e( 'Reviews', 'wp-hotel-booking' );
			}
			?>
        </h2>

		<?php if ( have_comments() ) { ?>
            <ol class="commentlist">
				<?php wp_list_comments( apply_filters( 'hb_room_review_list_args', array( 'callback' => 'hb_comments' ) ) ); ?>
            </ol>

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) { ?>
                <nav class="hb-pagination">
					<?php
					paginate_comments_links(
						apply_filters(
							'hb_comment_pagination_args',
							array(
								'prev_text' => '&larr;',
								'next_text' => '&rarr;',
								'type'      => 'list',
							)
						)
					);
					?>
                </nav>
			<?php } ?>

		<?php } else { ?>
            <p class="hb-noreviews"><?php _e( 'There are no reviews yet.', 'wp-hotel-booking' ); ?></p>
		<?php } ?>
    </div>

	<?php if ( hb_customer_booked_room( $hb_room->id ) && ! $enable_advanced_review ) { ?>

        <div id="review_form_wrapper">
            <div id="review_form">
				<?php
				$commenter    = wp_get_current_commenter();
				$comment_form = array(
					'title_reply'          => have_comments() ? __( 'Add a review', 'wp-hotel-booking' ) : __( 'Be the first to review', 'wp-hotel-booking' ) . ' &ldquo;' . get_the_title() . '&rdquo;',
					'title_reply_to'       => __( 'Leave a Reply to %s', 'wp-hotel-booking' ),
					'comment_notes_before' => '',
					'comment_notes_after'  => '',
					'fields'               => array(
						'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'wp-hotel-booking' ) . ' <span class="required">*</span></label> ' .
						            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" /></p>',
						'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'wp-hotel-booking' ) . ' <span class="required">*</span></label> ' .
						            '<input id="email" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-required="true" /></p>',
					),
					'label_submit'         => __( 'Submit', 'wp-hotel-booking' ),
					'logged_in_as'         => '',
					'comment_field'        => '',
				);

				if ( $hb_settings->get( 'enable_review_rating' ) ) {
					$comment_form['comment_field'] = '<p class="comment-form-rating"><label for="rating">' . __( 'Your Rating', 'wp-hotel-booking' ) . '</label>
                        </p><div class="hb-rating-input"></div>';
				}

				$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . __( 'Your Review', 'wp-hotel-booking' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';

				comment_form( apply_filters( 'hb_product_review_comment_form_args', $comment_form ) );
				?>
            </div>
        </div>

	<?php } else { ?>
        <p class="hb-verification-required"><?php _e( 'Only logged in customers who have purchased this product may leave a review.', 'wp-hotel-booking' ); ?></p>
	<?php } ?>

    <div class="clear"></div>
</div>
