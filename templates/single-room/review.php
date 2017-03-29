<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$rating   = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
$settings = WPHB_Settings::instance();
?>
<li itemprop="review" itemscope itemtype="http://schema.org/Review" <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

    <div id="comment-<?php comment_ID(); ?>" class="comment_container">

		<?php echo get_avatar( $comment, apply_filters( 'hb_review_gravatar_size', '60' ), '' ); ?>

        <div class="comment-text">

			<?php if ( $rating && $settings->get( 'enable_review_rating' ) ) : ?>

                <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo sprintf( __( 'Rated %d out of 5', 'wp-hotel-booking' ), $rating ) ?>">
                    <span style="width:<?php echo ( $rating / 5 ) * 100; ?>%"></span>
                </div>

			<?php endif; ?>

			<?php if ( $comment->comment_approved == '0' ) : ?>

                <p class="meta"><em><?php _e( 'Your comment is awaiting approval', 'wp-hotel-booking' ); ?></em></p>

			<?php else : ?>

                <p class="meta">
                    <strong itemprop="author"><?php comment_author(); ?></strong> <?php

					?>&ndash;
                    <time itemprop="datePublished" datetime="<?php echo get_comment_date( 'c' ); ?>"><?php echo get_comment_date( hb_date_format() ); ?></time>
                    :
                </p>

			<?php endif; ?>

            <div itemprop="description" class="description"><?php comment_text(); ?></div>
        </div>
    </div>
</li>