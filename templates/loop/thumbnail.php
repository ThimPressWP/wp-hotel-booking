<?php
/**
 * Product loop thumbnail
 *
 * @author  ThimPress
 * @package Tp-hotel-booking/Templates
 * @version 1.1.4
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $hb_room;
global $hb_settings;
$gallery  = $hb_room->gallery;
$featured = $gallery ? array_shift( $gallery ) : false;
?>
<div class="media">
    <a href="<?php the_permalink(); ?>">
		<?php $hb_room->getImage( 'catalog' ); ?>
    </a>
</div>