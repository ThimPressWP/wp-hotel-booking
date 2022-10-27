<?php
/**
 * Template for displaying pagination of room within the loop.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/search/v2/pagination-v2.php.
 *
 * @author   ThimPress
 * @package  wp-hotel-booking/Templates
 * @version  1.10.7
 */

defined( 'ABSPATH' ) || exit();

global $wp_query;

if ( empty( $total ) || empty( $paged ) ) {
	return;
}

if ( $total <= 1 ) {
	return;
}

?>

<nav class="rooms-pagination">
	<?php
	global $wp;
	$base = esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) );
	// $base = str_replace( home_url( $wp->request ) . '/', hb_get_page_permalink( 'search' ), $base );
	
	echo paginate_links(
		apply_filters(
			'hb_pagination_args',
			array(
				'base'      => $base,
				'format'    => '',
				'prev_text' => __( 'Previous', 'wp-hotel-booking' ),
				'next_text' => __( 'Next', 'wp-hotel-booking' ),
				'total'     => $total,
				'current'   => max( 1, $paged ),
				'type'      => 'list',
				'end_size'  => 3,
				'mid_size'  => 3,
			)
		)
	);
	?>
</nav>
