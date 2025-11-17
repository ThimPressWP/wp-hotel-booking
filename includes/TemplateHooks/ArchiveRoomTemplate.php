<?php
/**
 * Template archive rooms
 *
 * @since 2.1.8
 * @version 1.0.0
 */

namespace WPHB\TemplateHooks;

use Exception;
use WPHB\Helpers\Singleton;
use WPHB\Helpers\Template;
use WPHB_Settings;

class ArchiveRoomTemplate {
	use Singleton;

	public function init() {
		add_action( 'wphb/list-rooms/layout', array( $this, 'layout_rooms' ), 10, 1 );
	}

	public function layout_rooms( $atts = array() ) {
		try {
			$rooms_html_wrapper = apply_filters(
				'wphb/list-rooms/layout/wrapper',
				array(
					'<div class="container room-container">' => '</div>',
				)
			);

			$rooms_content = static::render_rooms();
			echo Template::instance()->nest_elements( $rooms_html_wrapper, $rooms_content );
		} catch ( Exception $e ) {
			echo 'Error: ' . $e->getMessage();
		}
	}

	/**
	 * Render template list rooms with settings param.
	 * 
	 *
	 * @return string
	 */
	public static function render_rooms() {
		global $wp_query;
		if ( $wp_query->is_tax( 'hb_room_type' ) ) {
			$room_type = $wp_query->queried_object_id;
		} else {
			$room_type = hb_get_request( 'room_type', '' );
		}
		$paged = get_query_var( 'paged' ) ?: hb_get_request( 'paged', 1, 'int' );
		$atts  = array(
			'check_in_date'  => hb_get_request( 'check_in_date', date( 'Y/m/d' ) ),
			'check_out_date' => hb_get_request( 'check_out_date', date( 'Y/m/d', strtotime( '+1 day' ) ) ),
			'adults'         => hb_get_request( 'adults', 1 ),
			'max_child'      => hb_get_request( 'max_child', 0 ),
			'room_qty'       => hb_get_request( 'room_qty', 1 ),
			'widget_search'  => false,
			'hb_page'        => $paged,
			'min_price'      => hb_get_request( 'min_price', 0 ),
			'max_price'      => hb_get_request( 'max_price', '' ),
			'rating'         => hb_get_request( 'rating', '' ),
			'room_type'      => $room_type,
			'sort_by'        => hb_get_request( 'sort_by', '' ),
		);

		$results = hb_search_rooms( $atts );
		$max_num_pages = 0;
		if ( empty( $results ) || empty( $results['data'] ) ) {
			$rooms = array();
			$total = 0;
			$paged = 1;

			$posts_per_page = (int) apply_filters( 'hb_number_search_rooms_per_page', WPHB_Settings::instance()->get( 'posts_per_page', 8 ) );
		} else {
			$rooms = $results['data'];
			$total = $results['total'];
			$paged = $results['page'];

			$posts_per_page = $results['posts_per_page'];
			$max_num_pages  = $results['max_num_pages'];
		}

		// HTML section rooms.
		$html_rooms = '';

		ob_start();
		if ( empty( $rooms ) ) {
			_e( 'No room found', 'wp-hotel-booking' );
		} else {
			hotel_booking_room_loop_start();
			foreach ($rooms as $room) {
				global $post;
				$post = get_post($room->ID);
				setup_postdata($post);
				hb_get_template_part( 'content', 'room' );
			}
			hotel_booking_room_loop_end();
			wp_reset_postdata();
		}

		$html_rooms = ob_get_clean();
		// end HTML section rooms

		// HTML Sort By
		$sort_by = hb_get_request( 'sort_by' );

		$data = array(
			'sort_by' => $sort_by,
		);

		if ( $total ) {
			$data['show_number'] = hb_get_show_room_text(
				array(
					'paged'         => $paged,
					'total'         => $total,
					'item_per_page' => $posts_per_page,
				)
			);
		}

		$sort_by = hb_get_template_content( 'search/v2/sort-by.php', compact( 'data' ) );

		// html pagination
		$data_pagination = array(
			'total_pages' => $max_num_pages,
			'paged'       => $paged,
		);
		$html_pagination = static::instance()->html_pagination( $data_pagination );

		// section_rooms
		$section_rooms = apply_filters(
			'wbhb/layout/list-rooms/section/rooms',
			array(
				'wrapper'     => '<div class="room-content">',
				'sort_by'     => $sort_by,
				'rooms'       => $html_rooms,
				'pagination'  => $html_pagination,
				'wrapper_end' => '</div>',
			),
			$results,
			$atts
		);

		// check show filter
		if ( get_option( 'tp_hotel_booking_filter_price_enable', 1 ) ) {
			$filter = hb_get_template_content( 'search/v2/search-filter-v2.php', array( 'atts' => array() ) );
		} else {
			$filter = '';
		}
		$check_room_availability = static::instance()->check_room_availability( $atts );
		// section ( filter + section_rooms )
		$section = apply_filters(
			'wbhb/layout/list-rooms/section',
			array(
				'check_availability'  => $check_room_availability,
				'archive_content'     => '<div>',
				'filter'              => $filter,
				'rooms'               => Template::combine_components( $section_rooms ),
				'archive_content_end' => '</div>',
			),
			$rooms,
			$atts
		);

		$content = Template::combine_components( $section );

		return $content;
	}

	/**
	 * Pagination
	 * support pagination number
	 * any support other type pagination add here
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	public function html_pagination( array $data = array() ): string {
		if ( empty( $data['total_pages'] ) || $data['total_pages'] <= 1 ) {
			return '';
		}

		$html_wrapper = array(
			' <nav class="rooms-pagination">' => '</nav>',
		);

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

	public function check_room_availability( $atts ) {
		$title          = sprintf( '<h3>%s</h3>', __( 'Check avaibility', 'wp-hotel-booking' ) );
		$check_in_date  = hb_get_request( 'check_in_date', date( 'Y/m/d' ) );
		$check_out_date = hb_get_request( 'check_out_date', date( 'Y/m/d', strtotime( '+1 day' ) ) );
		$adults         = hb_get_request( 'adults', 1 );
		$max_child      = hb_get_request( 'max_child', 0 );
		$room_qty       = hb_get_request( 'room_qty', 1 );

		$check_in_date_html  = $this->date_field( __( 'Check-in Date', 'wp-hotel-booking' ), 'check_in_date', $atts['check_in_date'] );
		$check_out_date_html = $this->date_field( __( 'Check-out Date', 'wp-hotel-booking' ), 'check_out_date', $atts['check_out_date'] );
		$adults_html         = $this->dropdown_selector(
			__( 'Adults', 'wp-hotel-booking' ),
			'adults_capacity',
			$atts['adults']
		);
		$child_html          = $this->dropdown_selector(
			__( 'Children', 'wp-hotel-booking' ),
			'max_child',
			$atts['max_child'],
			0
		);
		$quantity_html       = $this->dropdown_selector(
			__( 'Rooms', 'wp-hotel-booking' ),
			'room_qty',
			$atts['room_qty'],
		);
		$button_html         = sprintf( '<div class="hb-form-field-input"><button type="submit" class="rooms-check-avaibility">%s</button></div>', __( 'Check avaibility', 'wp-hotel-booking' ) );

		$sections            = apply_filters(
			'wbhb/layout/list-rooms/section/check-availability-form',
			array(
				'wrapper'         => '<div class="hotel-booking-rooms-search">',
				'title'           => $title,
				'form_start'      => '<form name="hb-search-form" class="hb-search-form hb-form-table" >',
				'check_in_date'   => $check_in_date_html,
				'check_out_date'  => $check_out_date_html,
				'adults_capacity' => $adults_html,
				'child_capacity'  => $child_html,
				'quantity'        => $quantity_html,
				'button_search'   => $button_html,
				'form_end'        => '</form>',
				'wrapper_end'     => '</div>',
			),
			$atts
		);
		return Template::combine_components( $sections );
	}

	public function date_field( $label = '', $name = '', $value = '' ) {
		$label_html = sprintf( '<label>%s</label>', $label );
		$input      = sprintf(
			'<input type="text" name="%1$s" class="hb_input_date_check" value="%2$s" placeholder="%3$s" autocomplete="off"/>',
			$name,
			$value,
			$label
		);
		$sections   = array(
			'wrapper'     => '<div class="hb-form-field-input">',
			'label'       => $label_html,
			'input'       => $input,
			'wrapper_end' => '</div>',
		);
		return Template::combine_components( $sections );
	}

	public function dropdown_selector( $label = '', $name = '', $value = 1, $min = 1 ) {

		$label          = sprintf( '<label>%s</label>', $label );
		$input_html     = sprintf(
			'<div class="hb-form-field-input hb-input-field-number">
		        <input type="text" min="%1$d" name="%2$s" value="%3$s" />
		    </div>',
		    $min, $name, $value
		);
		$nav_number_html = sprintf(
			'<div class="hb-form-field-list nav-number-input-field">
		        <span class="label">%s</span>
		        <div class="number-box">
		            <span class="number-icons hb-goDown"><i class="fa fa-minus"></i></span>
		            <span class="hb-number-field-value">
		            </span>
		            <span class="number-icons hb-goUp"><i class="fa fa-plus"></i></span>
		        </div>
		    </div>',
		    $label
		);

		$sections = array(
			// 'wrapper'     => '<div class="hb-form-field hb-form-number hb-form-number-input">',  //thêm class để theme hiển thị dạng +/- 
			'wrapper'     => '<div class="hb-form-field hb-form-number">',
			'label'       => $label,
			'input'       => $input_html,
			'nav_number'  => $nav_number_html,
			'wrapper_end' => '</div>',
		);

		return Template::combine_components( $sections );
	}
}
