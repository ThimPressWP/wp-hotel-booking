<?php
/**
 * The template for displaying loop room rating in archive room page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/loop/rating.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

global $hb_room;
/**
 * @var $hb_room WPHB_Room
 */
$rating = $hb_room->average_rating();
?>

<?php if ( comments_open( $hb_room->ID ) ) { ?>
	<div class="rating">
		<?php if ( $rating ) { ?>
			<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating"
				 title="<?php echo esc_html( sprintf( __( 'Rated %d out of 5', 'wp-hotel-booking' ), $rating ) ); ?>">
				<span style="width:<?php echo ( ( $rating / 5 ) * 100 ); ?>%"></span>
			</div>
		<?php } ?>
	</div>
<?php } ?>
