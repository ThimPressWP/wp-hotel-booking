<?php
/**
 * Product loop thumbnail
 *
 * @author  ThimPress
 * @package Tp-hotel-booking/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $hb_room;
global $hb_settings;
?>
<div class="media">
	<a href="<?php the_permalink(); ?>"
	class="media-image<?php echo $hb_settings->get('enable_gallery') ? ' hb-room-gallery' : '' ?>"
	title="<?php the_title() ?>" rel="hb-room-gallery-<?php the_ID();?>"
	data-lightbox="hb-room-gallery[<?php the_ID(); ?>]"
	>
		<?php $hb_room->getImage( 'catalog' ); ?>
	</a>
</div>