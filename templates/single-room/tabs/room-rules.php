<?php
/**
 * The template for displaying single room rules.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/single-room/tabs/room-rules.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 2.3.7
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( empty( $rules ) ) {
	return;
}
?>
<div class="_hb_room_rules">
	<div class="_hb_room_rules__detail">
	<?php
		$rules = apply_filters( 'the_content', hb_kses_post_with_iframe( $rules ) );
		echo str_replace( ']]>', ']]&gt;', $rules );
	?>
	</div>
</div>
