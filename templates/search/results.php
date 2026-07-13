<?php
/**
 * The template for displaying search room results.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/search/results.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

do_action( 'hb_before_search_result' ); ?>

<?php global $hb_search_rooms; ?>

<div id="hotel-booking-results">
	<?php if ( $results && ! empty( $hb_search_rooms['data'] ) ) { ?>
		<h3><?php esc_html_e( 'Search results', 'wp-hotel-booking' ); ?></h3>

		<?php
		hb_get_template(
			'search/list.php',
			array(
				'results' => $hb_search_rooms['data'],
				'atts'    => $atts,
			)
		);
		?>

		<nav class="rooms-pagination">
			<?php
			echo paginate_links(
				apply_filters(
					'hb_pagination_args',
					array(
						'base'      => add_query_arg( 'hb_page', '%#%' ),
						'format'    => '',
						'prev_text' => esc_html__( 'Previous', 'wp-hotel-booking' ),
						'next_text' => esc_html__( 'Next', 'wp-hotel-booking' ),
						'total'     => $hb_search_rooms['max_num_pages'],
						'current'   => $hb_search_rooms['page'],
						'type'      => 'list',
						'end_size'  => 3,
						'mid_size'  => 3,
					)
				)
			);
			?>
		</nav>
	<?php } else { ?>
		<p><?php esc_html_e( 'No room found.', 'wp-hotel-booking' ); ?></p>
		<p>
			<a href="<?php echo esc_url( hb_get_url() ); ?>"><?php esc_html_e( 'Search again!', 'wp-hotel-booking' ); ?></a>
		</p>
	<?php } ?>
</div>
