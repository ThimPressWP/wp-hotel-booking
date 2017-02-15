<?php
/**
 * Display single room reviews (comments)
 *
 * Override this template by copying it to yourtheme/tp-hotel-booking/single-room-reviews.php
 *
 * @author        ThimPress
 * @package       wp-hotel-booking/templates
 * @version       1.6
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


global $hb_room;
global $hb_settings;

if ( !comments_open() ) {
	return;
}
?>
<div id="reviews">
    <div id="comments">
        <h2>
			<?php
			if ( $hb_settings->get( 'enable_review_rating' ) && ( $count = $hb_room->get_review_count() ) )
				printf( _n( '%s review for %s', '%s reviews for %s', $count, 'wp-hotel-booking' ), $count, get_the_title() );
			else
				_e( 'Reviews', 'wp-hotel-booking' );
			?>
        </h2>

		<?php if ( have_comments() ) : ?>

            <ol class="commentlist">
				<?php wp_list_comments( apply_filters( 'hb_room_review_list_args', array( 'callback' => 'hb_comments' ) ) ); ?>
            </ol>

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
				echo '<nav class="hb-pagination">';
				paginate_comments_links( apply_filters( 'hb_comment_pagination_args', array(
					'prev_text' => '&larr;',
					'next_text' => '&rarr;',
					'type'      => 'list',
				) ) );
				echo '</nav>';
			endif; ?>

		<?php else : ?>

            <p class="hb-noreviews"><?php _e( 'There are no reviews yet.', 'wp-hotel-booking' ); ?></p>

		<?php endif; ?>
    </div>

	<?php if ( hb_customer_booked_room( $hb_room->id ) ) : ?>

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
					'comment_field'        => ''
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

	<?php else : ?>

        <p class="hb-verification-required"><?php _e( 'Only logged in customers who have purchased this product may leave a review.', 'wp-hotel-booking' ); ?></p>

	<?php endif; ?>

    <div class="clear"></div>
</div>
