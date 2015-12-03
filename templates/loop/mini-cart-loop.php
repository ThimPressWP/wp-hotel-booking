<?php
/**
 * Mini Cart loop
 *
 * @author 		ThimPress
 * @package 	Tp-hotel-booking/Templates
 * @version     0.9
 */
?>
<div class="hb_mini_cart_item" data-search-key="<?php echo $room->in_to_out; ?>" data-id="<?php echo $room->ID ?>">

	<?php do_action( 'hotel_booking_before_mini_cart_loop', $room ); ?>

	<div class="hb_mini_cart_top">

		<h4 class="hb_title"><a href="<?php echo get_permalink($room->ID); ?>"><?php printf( '%s %s', $room->name, $room->capacity_title ? '('.$room->capacity_title.')' : '' ) ?></a></h4>
		<span class="hb_mini_cart_remove"><i class="fa fa-times"></i></span>
	</div>

	<div class="hb_mini_cart_number">

		<label><?php _e( 'Quantity: ', 'tp-hotel-booking' ); ?></label>
		<span><?php printf( '%s', $room->quantity );  ?></span>

	</div>

	<?php do_action( 'hotel_booking_before_mini_cart_loop', $room ); ?>

	<div class="hb_mini_cart_price">

		<label><?php _e( 'Price: ', 'tp-hotel-booking' ); ?></label>
		<span><?php printf( '%s', hb_format_price( $room->total_price ) ) ?></span>

	</div>

	<?php do_action( 'hotel_booking_after_mini_cart_loop', $room ); ?>
</div>