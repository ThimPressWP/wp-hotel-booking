<?php
/**
 * WP Hotel Booking list rooms shortcode.
 *
 * @version       1.9.6
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Classes/Shortcode
 * @category      Classes
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

class WPHB_Shortcode_Hotel_Booking_Rooms extends WPHB_Shortcodes {

	public $shortcode = 'hotel_booking_rooms';

	public function __construct() {
		parent::__construct();
	}

	function add_shortcode( $atts, $content = null ) {
		$args = hb_get_room_query_args( $atts );

		$current_theme = wp_get_theme()->get('Name');

		/* remove action */
		remove_action( 'pre_get_posts', 'hotel_booking_num_room_archive', 999 );
		
		$query = new WP_Query( $args );
		ob_start();
	?>

		<div class="room-container">

			<?php if( $current_theme != 'Hotel WP' ) { // Now show if theme is Hotel WP: Luxstay
				hb_get_template( 'search/v2/search-filter-v2.php', array( 'atts' => array() ) ); 
			} ?>

			<!-- Room Content -->
			<div class="room-content">
				<?php // show number + sort by
					$data = array(
						'sort_by' => hb_get_request( 'sort_by' ),
					);
					if ( $query->post_count ) {
						$data['show_number'] = hb_get_show_room_text(
							array(
								'total'         => $query->post_count,
								'paged'         => 1,
								'item_per_page' => $query->post_count,
							)
						);
					}

					hb_get_template( 'search/v2/sort-by.php', compact( 'data' ) );
				?>

				<?php if ( $query->have_posts() ) :
					hotel_booking_room_loop_start();

					while ( $query->have_posts() ) : $query->the_post();
						hb_get_template_part( 'content', 'room' );
					endwhile; // end of the loop.

					hotel_booking_room_loop_end();
				else :
					_e( 'No room found', 'wp-hotel-booking' );
				endif;
				wp_reset_postdata();
				?>
			</div>

			<?php 
				add_action( 'pre_get_posts', 'hotel_booking_num_room_archive', 999 ); 
			?>
		</div>

	<?php return ob_get_clean(); }
}

new WPHB_Shortcode_Hotel_Booking_Rooms();