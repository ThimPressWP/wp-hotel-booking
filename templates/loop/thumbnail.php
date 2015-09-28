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

?>
<div class="media">
	<?php $thumbnail = apply_filters( 'hotel_booking_loop_room_thumbnail_size', 'thumbnail' ); ?>
	<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail($thumbnail); ?></a>
</div>