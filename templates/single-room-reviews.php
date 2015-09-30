<?php
/**
 * Display single product reviews (comments)
 *
 * @author 		WooThemes
 * @package 	hb/Templates
 * @version     2.3.2
 */
$room = HB_Room::instance( get_the_ID() );
$settings = HB_Settings::instance();

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! comments_open() ) {
    return;
}
?>
<div id="reviews">
    <div id="comments">
        <h2><?php
            if ( $settings->get( 'enable_review_rating' ) && ( $count = $room->get_review_count() ) )
                printf( _n( '%s review for %s', '%s reviews for %s', $count, 'tp-hotel-booking' ), $count, get_the_title() );
            else
                _e( 'Reviews', 'tp-hotel-booking' );

            echo "[", have_comments(), "]";
            ?></h2>

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

            <p class="hb-noreviews"><?php _e( 'There are no reviews yet.', 'tp-hotel-booking' ); ?></p>

        <?php endif; ?>
    </div>

    <?php if ( hb_customer_booked_room( '', $room->id ) ) : ?>

        <div id="review_form_wrapper">
            <div id="review_form">
                <?php
                $commenter = wp_get_current_commenter();

                $comment_form = array(
                    'title_reply'          => have_comments() ? __( 'Add a review', 'tp-hotel-booking' ) : __( 'Be the first to review', 'tp-hotel-booking' ) . ' &ldquo;' . get_the_title() . '&rdquo;',
                    'title_reply_to'       => __( 'Leave a Reply to %s', 'tp-hotel-booking' ),
                    'comment_notes_before' => '',
                    'comment_notes_after'  => '',
                    'fields'               => array(
                        'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'tp-hotel-booking' ) . ' <span class="required">*</span></label> ' .
                            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" /></p>',
                        'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'tp-hotel-booking' ) . ' <span class="required">*</span></label> ' .
                            '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-required="true" /></p>',
                    ),
                    'label_submit'  => __( 'Submit', 'tp-hotel-booking' ),
                    'logged_in_as'  => '',
                    'comment_field' => ''
                );

                //if ( $account_page_url = wc_get_page_permalink( 'myaccount' ) ) {
                 //   $comment_form['must_log_in'] = '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a review.', 'tp-hotel-booking' ), esc_url( $account_page_url ) ) . '</p>';
                //}

                if ( $settings->get( 'enable_review_rating' ) ) {
                    $comment_form['comment_field'] = '<p class="comment-form-rating"><label for="rating">' . __( 'Your Rating', 'tp-hotel-booking' ) .'</label>
                    </p><div class="hb-rating-input"></div>';
                }

                $comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . __( 'Your Review', 'tp-hotel-booking' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
                //$comment_form['comment_field'] .= '<p class="comment-form-email"><label for="email">' . __( 'Your Email', 'tp-hotel-booking' ) . '</label><input type="email" id="email" name="email" cols="45" rows="8" aria-required="true" placeholder="'.__('Your email', 'tp-hotel-booking').'" /></p>';

                comment_form( apply_filters( 'hb_product_review_comment_form_args', $comment_form ) );
                ?>
            </div>
        </div>

    <?php else : ?>

        <p class="hb-verification-required"><?php _e( 'Only logged in customers who have purchased this product may leave a review.', 'tp-hotel-booking' ); ?></p>

    <?php endif; ?>

    <div class="clear"></div>
</div>
