<?php
/**
 * Product loop thumbnail
 *
 * @author  ThimPress
 * @package wp-hotel-booking/templates
 * @version 1.1.4
 */

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

global $hb_settings;
global $hb_room;
$rating = $hb_room->average_rating();
?>
<?php if ( comments_open( $hb_room->ID ) ): ?>
    <div class="rating">
		<?php if ( $rating ) : ?>

			<?php if ( $rating ): ?>

                <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo sprintf( __( 'Rated %d out of 5', 'wp-hotel-booking' ), $rating ) ?>">
                    <span style="width:<?php echo ( $rating / 5 ) * 100; ?>%"></span>
                </div>

			<?php endif; ?>

		<?php endif; ?>
    </div>
<?php endif; ?>