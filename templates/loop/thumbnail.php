<?php
/**
 * The template for displaying loop room thumbnail in archive room page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/loop/thumbnail.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6.1
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

global $hb_room;
$has_featured = get_the_post_thumbnail( $hb_room->ID ) ? true : false;
/**
 * @var $hb_room WPHB_Room
 */
?>

<?php if ( $has_featured ) : ?>
	<div class="media">
		<a href="<?php the_permalink(); ?>"><?php $hb_room->getImage( 'catalog' ); ?></a>
	</div>
<?php endif; ?>