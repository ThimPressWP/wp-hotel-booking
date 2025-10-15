<?php
/**
 * The template for displaying loop room title in archive room page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/loop/title.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
$check_in_date  = hb_get_request( 'check_in_date', date( 'Y-m-d' ) );
$check_out_date = hb_get_request( 'check_out_date', date( 'Y-m-d', strtotime( '+1 day' ) ) );
$adults         = hb_get_request( 'adults', 1 );
$children       = hb_get_request( 'children', 0 );
$room_qty       = hb_get_request( 'room_qty', 1 );

$room_link = add_query_arg( 
	array(
	    'check_in_date'  => urlencode( $check_in_date ),
	    'check_out_date' => urlencode( $check_out_date ),
	    'adults'         => $adults,
	    'children'       => $children,
	    'room_qty'       => $room_qty,
	),
	get_the_permalink()
);
?>

<div class="title">
	<h4>
		<a href="<?php echo esc_url( $room_link ); ?>"><?php the_title(); ?></a>
	</h4>
</div>
