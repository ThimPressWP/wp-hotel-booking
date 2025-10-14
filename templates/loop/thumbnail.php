<?php
/**
 * The template for displaying loop room thumbnail in archive room page.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/loop/thumbnail.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6.1
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

global $hb_room;
$has_featured = get_the_post_thumbnail( $hb_room->ID ) ? true : false;

$check_in_date  = hb_get_request( 'check_in_date', date( 'Y-m-d' ) );
$check_out_date = hb_get_request( 'check_out_date', date( 'Y-m-d', strtotime( '+1 day' ) ) );
$adults         = hb_get_request( 'adults', 1 );
$max_child      = hb_get_request( 'max_child', 0 );
$room_qty       = hb_get_request( 'room_qty', 1 );

$room_link = add_query_arg( 
	array(
	    'check_in_date'  => urlencode( $check_in_date ),
	    'check_out_date' => urlencode( $check_out_date ),
	    'adults'         => $adults,
	    'max_child'      => $max_child,
	    'room_qty'       => $room_qty,
	),
	get_the_permalink()
);
/**
 * @var $hb_room WPHB_Room
 */
?>

<?php if ( $has_featured ) : ?>
	<div class="media">
		<a href="<?php echo esc_url( $room_link ) ?>"><?php $hb_room->getImage( 'catalog' ); ?></a>
	</div>
<?php endif; ?>