<?php
/**
 * The template for displaying search room list.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/search/list.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
if ( empty( $results ) || empty( $atts ) ) {
	return;
}
?>

<ul class="hb-search-results">
	<?php
	foreach ( $results as $room ) {
		hb_get_template(
			'search/v2/loop-v2.php',
			array(
				'room' => $room,
				'atts' => $atts,
			)
		);
	}
	?>
</ul>
