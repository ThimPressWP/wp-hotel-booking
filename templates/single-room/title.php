<?php
/**
 * The template for displaying loop room title in single room page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/single-room/title.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @since 1.8.1
 * @version 1.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
?>

<div class="title">
	<h1>
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</h1>
</div>
