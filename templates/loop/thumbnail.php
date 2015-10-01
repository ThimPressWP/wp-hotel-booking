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
?>
<div class="media">
	<a href="<?php the_permalink(); ?>">
		<?php $hb_room->getImage( 'catalog' ); ?>
	</a>
</div>