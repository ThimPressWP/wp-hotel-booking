<?php
/**
 * Pagination - Show numbered pagination for catalog pages.
 *
 * @author 		ThimPress
 * @package 	Tp-hotel-booking/Templates
 * @version     0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wp_query;

if ( $wp_query->max_num_pages <= 1 ) {
	return;
}

?>
<nav class="rooms-pagination">
	<?php
		echo paginate_links( apply_filters( 'hb_pagination_args', array(
			'base'         => esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) ),
			'format'       => '',
			'add_args'     => '',
			'current'      => max( 1, get_query_var( 'paged' ) ),
			'total'        => $wp_query->max_num_pages,
			'prev_text'    => __('Previous', 'Tp-hotel-booking'),
			'next_text'    => __('Next', 'Tp-hotel-booking'),
			'type'         => 'list',
			'end_size'     => 3,
			'mid_size'     => 3
		) ) );
	?>
</nav>
