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

class ArchiveRoomTemplate {
	use Singleton;

	public function init() {
		add_action( 'wphb/list-rooms/layout', array( $this, 'layout_rooms' ), 10, 1 );
	}

	public function layout_rooms( $atts = array() ) {
		try {
			// $search = hb_get_template_content( 'search/v2/search-form-v2.php', array( 'atts' => array() ) );
			$rooms_html_wrapper = array(
				'<div class="container room-container">' => '</div>',
			);

			$args          = hb_get_room_query_args( $atts );
			$rooms_content = static::render_rooms( $args );
			echo $this->check_room_availability();
			echo Template::instance()->nest_elements( $rooms_html_wrapper, $rooms_content );
		} catch ( Exception $e ) {
			echo 'Error: ' . $e->getMessage();
		}
	}

	/**
	 * Render template list rooms with settings param.
	 *
	 * @param array $settings
	 *
	 * @return string
	 */
	public static function render_rooms( array $settings = array() ) {

		$atts = array(
			'check_in_date'  => hb_get_request( 'check_in_date', date( 'Y/m/d' ) ),
			'check_out_date' => hb_get_request( 'check_out_date', date( 'Y/m/d', strtotime( '+1 day' ) ) ),
			'adults'         => hb_get_request( 'adults', 1 ),
			'max_child'      => hb_get_request( 'max_child', 0 ),
			'room_qty'       => hb_get_request( 'room_qty', 1 ),
			'widget_search'  => false,
			'hb_page'        => $settings['paged'] ?? hb_get_request( 'paged', 1, 'int' ),
			'min_price'      => hb_get_request( 'min_price', '' ),
			'max_price'      => hb_get_request( 'max_price', '' ),
			'rating'         => hb_get_request( 'rating', '' ),
			'room_type'      => hb_get_request( 'room_type', '' ),
			'sort_by'        => hb_get_request( 'sort_by', '' ),
		);

		$results = hb_search_rooms( $atts );
		$max_num_pages = 0;
		if ( empty( $results ) || empty( $results['data'] ) ) {
			$rooms = array();
			$total = 0;
		} else {
			$rooms = $results['data'];
			$total = $results['total'];
			$max_num_pages = $results['max_num_pages'];
		}
		// $rooms = new \WP_Query( $settings );

		// $total          = $rooms->post_count;
		$posts_per_page = $settings['posts_per_page'];
		$paged          = $settings['paged'] ?? 1;

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
		$section_rooms = array(
			'wrapper'     => '<div class="room-content">',
			'sort_by'     => $sort_by,
			'rooms'       => $html_rooms,
			'pagination'  => $html_pagination,
			'wrapper_end' => '</div>',
		);

		// check show filter
		if ( get_option( 'tp_hotel_booking_filter_price_enable', 1 ) ) {
			$filter = hb_get_template_content( 'search/v2/search-filter-v2.php', array( 'atts' => array() ) );
		} else {
			$filter = '';
		}

		// section ( filter + section_rooms )
		$section = apply_filters(
			'wbhb/layout/list-rooms/section',
			array(
				'filter' => $filter,
				'rooms'  => Template::combine_components( $section_rooms ),
			),
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

	public function check_room_availability() {
		$title          = sprintf( '<h3>%s</h3>', __( 'Check avaibility', 'wp-hotel-booking' ) );
		$check_in_date  = hb_get_request( 'check_in_date', date( 'Y/m/d' ) );
		$check_out_date = hb_get_request( 'check_out_date', date( 'Y/m/d', strtotime( '+1 day' ) ) );
		$adults         = hb_get_request( 'adults', 1 );
		$max_child      = hb_get_request( 'max_child', 0 );
		$room_qty       = hb_get_request( 'room_qty', 1 );

		$check_in_date_html  = $this->date_field( __( 'Arrival Date', 'wp-hotel-booking' ), 'check_in_date', $check_in_date );
		$check_out_date_html = $this->date_field( __( 'Departure Date', 'wp-hotel-booking' ), 'check_out_date', $check_out_date );
		$adults_html         = $this->dropdown_selector(
			__( 'Adults', 'wp-hotel-booking' ),
			'adults',
			$adults,
			1,
			hb_get_max_capacity_of_rooms(),
			hb_get_capacity_of_rooms()
		);
		$child_html          = $this->dropdown_selector(
			__( 'Childs', 'wp-hotel-booking' ),
			'max_child',
			$max_child,
			0,
		);
		$quantity_html       = $this->dropdown_selector(
			__( 'Quantity', 'wp-hotel-booking' ),
			'room_qty',
			$room_qty,
		);
		$button_html         = sprintf( '<div class="hb-form-field-input button"><button type="submit" class="rooms-check-avaibility">%s</button></div>', __( 'Check avaibility', 'wp-hotel-booking' ) );
		$sections            = array(
			'wrapper'         => '<div class="hotel-booking-rooms-search">',
			'title'           => $title,
			'form_start'      => '<form name="hb-search-form" class="hb-form-table" style="display: flex;">',
			'check_in_date'   => $check_in_date_html,
			'check_out_date'  => $check_out_date_html,
			'adults_capacity' => $adults_html,
			'child_capacity'  => $child_html,
			'quantity'        => $quantity_html,
			'button_search'   => $button_html,
			'form_end'        => '</form>',
			'wrapper_end'     => '</div>',
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

	public function dropdown_selector( $label = '', $name = '', $selected = 1, $min = 1, $max = 10, $options = array() ) {
		ob_start();
		$selector_dropdown      = hb_dropdown_numbers(
			array(
				'name'              => $name,
				'min'               => $min,
				'max'               => $max,
				'show_option_none'  => $label,
				'selected'          => $selected,
				'option_none_value' => '',
				'options'           => $options,
			)
		);
		$selector_dropdown_html = ob_get_clean();
		$label                  = sprintf( '<label>%s</label>', $label );
		// $selector = sprintf('<div class="hb-form-field-input">%s<?/div>', $selector_dropdown);
		$sections = array(
			'wrapper'     => '<div class="hb-form-field-input">',
			'label'       => $label,
			'input'       => $selector_dropdown_html,
			'wrapper_end' => '</div>',
		);
		return Template::combine_components( $sections );
	}
}
