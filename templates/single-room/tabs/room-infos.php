<?php
/**
 * The template for displaying single room faqs.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/single-room/tabs/room-faqs.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( empty( $infos ) ) {
	return;
}
?>
<div class="_hb_room_infos">
	<?php
		$infos = apply_filters( 'the_content', $infos );
		echo str_replace( ']]>', ']]&gt;', $infos );
	?>
</div>
