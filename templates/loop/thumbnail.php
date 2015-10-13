<?php
/**
 * Product loop thumbnail
 *
 * @author  ThimPress
 * @package Tp-hotel-booking/Templates
 * @version 0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $hb_room;
global $hb_settings;
$gallery = $hb_room->gallery;
$featured = $gallery ? array_shift( $gallery ) : false;
?>
<div class="media">
	<a href="<?php echo $hb_settings->get('enable_gallery_lightbox') ? $featured['src'] : get_the_permalink(); ?>"
	class="media-image<?php echo $hb_settings->get('enable_gallery_lightbox') ? ' hb-room-gallery' : '' ?>"
	title="<?php the_title() ?>" rel="hb-room-gallery-<?php the_ID();?>"
	data-lightbox="hb-room-gallery[<?php the_ID(); ?>]"
	>
		<?php $hb_room->getImage( 'catalog' ); ?>
	</a>
</div>