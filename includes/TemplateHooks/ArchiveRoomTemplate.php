<?php
/**
 * Template archive rooms
 * @since 2.1.8-beta.1
 * @version 1.0.0
 */

namespace WPHB\TemplateHooks;

use WPHB\Helpers\Singleton;
use WPHB\Helpers\Template;

class ArchiveRoomTemplate {
	use Singleton;

	public function init() { 
		add_action( 'wphb/list-rooms/layout', [ $this, 'layout_rooms' ], 10, 1 );
	}

	public function layout_rooms( $atts = [] ) {
        try {
            $html_wrapper = [
                '<div class="container room-container">' => '</div>',
            ];
    
            $args    = hb_get_room_query_args( $atts );
            $content = static::render_rooms( $args );
    
            echo Template::instance()->nest_elements( $html_wrapper, $content );
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
	}

    /**
	 * Render template list rooms with settings param.
	 *
	 * @param array $args
	 *
	 * @return { string_html }

	 */
	public static function render_rooms( array $settings = [] ) {
        $rooms = new \WP_Query( $settings );

		// HTML section rooms.
		$html_rooms = '';

        ob_start();
        if ( $rooms->have_posts() ) :
            hotel_booking_room_loop_start();

            while ( $rooms->have_posts() ) : $rooms->the_post();
                hb_get_template_part( 'content', 'room' );
            endwhile; // end of the loop.

            hotel_booking_room_loop_end();
        else :
            _e( 'No room found', 'wp-hotel-booking' );
        endif; wp_reset_postdata();

        $html_rooms = ob_get_clean();
        // end HTML section rooms

		// html pagination
		$data_pagination = [
			'total_pages' => $rooms->max_num_pages,
			'paged'       => isset($settings['paged']) ? $settings['paged'] : 1,
		];
		$html_pagination = static::instance()->html_pagination( $data_pagination );

        // section_rooms 
        $section_rooms = [
			'wrapper'     => '<div class="room-content">',
			'rooms'       => $html_rooms,
            'pagination'  => $html_pagination,
			'wrapper_end' => '</div>',
		];

        // check show filter
        if (  get_option( 'tp_hotel_booking_filter_price_enable', 1 ) ) {
            $filter = hb_get_template_content( 'search/v2/search-filter-v2.php', array( 'atts' => array() ) );
        } else {
            $filter = '';
        }
       
        // section ( filter + section_rooms )
		$section = apply_filters(
			'wbhb/layout/list-rooms/section',
			[
				'filter'     => $filter,
				'rooms'      => Template::combine_components( $section_rooms ),
			],
			$rooms,
			$settings
		);

		$content = Template::combine_components( $section );

		return $content;
	}
    
    /**
     * Pagination
     * support pagination number
     * any support other type pagination add here
     * @param array $data
     *
     * @return string
     */
    public function html_pagination( array $data = [] ): string {
        if ( empty( $data['total_pages'] ) || $data['total_pages'] <= 1 ) {
            return '';
        }

        $html_wrapper = [
            ' <nav class="rooms-pagination">' => '</nav>',
        ];

        $pagination = paginate_links(
            apply_filters(
                'hb_pagination_args',
                array(
                    'base'      => esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) ),
                    'format'    => '',
                    'add_args'  => '',
                    'current'   => max( 1, $data['paged'] ?? 1 ),
                    'total'     => $data[ 'total_pages' ?? 1 ],
                    'prev_text' => __( 'Previous', 'wp-hotel-booking' ),
                    'next_text' => __( 'Next', 'wp-hotel-booking' ),
                    'type'      => 'list',
                    'end_size'  => 3,
                    'mid_size'  => 3,
                )
            )
        );

        return Template::instance()->nest_elements( $html_wrapper, $pagination );
    }
}