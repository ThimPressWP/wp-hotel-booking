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

if ( empty( $faqs ) ) {
	return;
}
?>
<div class="_hb_room_faqs">
	<?php if ( ! empty( $faqs ) ) : ?>
		<?php foreach ( $faqs as $rule ) : ?>
			<div class="_hb_room_faqs__detail">
				<p class="_hb_room_rule_title"><?php echo $rule[0]; ?></p>
				<div class="_hb_room_rule_content"><?php echo $rule[1]; ?></div>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
