<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Thim_Ekit_Widget_List_Results_Room extends Widget_Base {

	use GroupControlTrait;
	protected $current_permalink;

    public function get_name() {
		return 'list-results-room';
	}

    public function get_title() {
		return esc_html__( 'List Results Room', 'wp-hotel-booking' );
	}

	public function get_icon() {
		return 'thim-eicon eicon-search-results';
	}

	public function get_categories() {
		return array( \WPHB\Elementor::CATEGORY_ARCHIVE_ROOM );
	}

    public function get_keywords() {
		return array( 'room', 'search', 'results' );
	}

	public function get_help_url() {
		return '';
	}

	protected function register_controls()
    {
		$this->start_controls_section(
			'section_options',
			array(
				'label' => esc_html__( 'Options', 'wp-hotel-booking' ),
			)
		);

		$this->add_control(
			'template_id',
			array(
				'label'   => esc_html__( 'Choose a template', 'wp-hotel-booking' ),
				'type'    => Controls_Manager::SELECT2,
				'default' => '0',
				'options' => array( '0' => esc_html__( 'None', 'wp-hotel-booking' ) ) + \Thim_EL_Kit\Functions::instance()->get_pages_loop_item( 'hb_room' ),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'              => esc_html__( 'Columns', 'wp-hotel-booking' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '3',
				'tablet_default'     => '2',
				'mobile_default'     => '1',
				'options'            => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'selectors'          => array(
					'{{WRAPPER}}' => '--hb-room-archive-columns: repeat({{VALUE}}, 1fr)',
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
		$this->register_style_layout();

		$this->register_navigation_archive();
		$this->register_style_pagination_archive('.hb-room-archive__pagination');
	}

	protected function register_style_layout() {
		$this->start_controls_section(
			'section_design_layout',
			array(
				'label' => esc_html__( 'Layout', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'column_gap',
			array(
				'label'     => esc_html__( 'Columns Gap', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 30,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--hb-room-archive-column-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'row_gap',
			array(
				'label'              => esc_html__( 'Rows Gap', 'wp-hotel-booking' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => array(
					'size' => 30,
				),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'frontend_available' => true,
				'selectors'          => array(
					'{{WRAPPER}}' => '--hb-room-archive-row-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render()
    {
		$settings        = $this->get_settings_for_display();
		$params = $_GET;
		$response         = new \WPHB_REST_RESPONSE();
		$response->status = 'success';

		$datetime 		 = new \DateTime('NOW');
		$tomorrow 		 = new \DateTime('tomorrow');
		$format 		 = get_option('date_format');

		$check_in_date   = isset($params['check_in_date']) ? $params['check_in_date'] : $datetime->format($format);
		$check_out_date  = isset($params['check_out_date']) ? $params['check_out_date'] : $tomorrow->format($format);
		$adults_capacity = isset($params['adults']) ? $params['adults'] : hb_get_request( 'adults', 1 );
		$max_child       = isset($params['max_child']) ? $params['max_child'] : hb_get_request( 'max_child', 0 );


		$paged           = isset( $params['paged'] ) ?? 1;

		if ( hb_get_request( 'is_page_room_extra' ) == 'select-room-extra' ) {

			hb_get_template( 'search/v2/select-extra-v2.php' );

			return;
		}
		$date_format = get_option( 'date_format' );

		if ( strpos( $check_in_date, '/' ) !== false ) {
			//$check_in_date = \DateTime::createFromFormat( $date_format, $check_in_date )->format( 'F j, Y' );
		}

		if ( strpos( $check_out_date, '/' ) !== false ) {
			//$check_out_date = \DateTime::createFromFormat( $date_format, $check_out_date )->format( 'F j, Y' );
		}

		$atts = array(
			'check_in_date'  => $check_in_date,
			'check_out_date' => $check_out_date,
			'adults'         => $adults_capacity,
			'max_child'      => $max_child,
			'search_page'    => null,
			'widget_search'  => false,
			'hb_page'        => $paged,
			'min_price'      => $params['min_price'] ?? '',
			'max_price'      => $params['max_price'] ?? '',
			'rating'         => $params['rating'] ?? '',
			'room_type'      => $params['room_type'] ?? '',
			'sort_by'        => $params['sort_by'] ?? '',
		);

		$results = hb_search_rooms( $atts );

		if ( empty( $results ) || empty( $results['data'] ) ) {
			echo '<p class="message message-error">' . esc_html__( 'Error: No rooms available!.', 'wp-hotel-booking' ) . '</p>';
			return;
		}
		$custom_process = get_option( 'tp_hotel_booking_custom_process' );
		$rooms = $results['data'];
		$class_item  = 'hb-room-archive__article'; ?>

		<div class="hb-room-archive">
			<div class="hb-room-archive__inner hb-search-results detail__booking-rooms">
			<?php
				foreach ($rooms as $room) {
					$post_object = get_post($room->ID);
					setup_postdata($GLOBALS['post'] = &$post_object);

					$this->current_permalink = get_permalink(); ?>
					<div class="hb-room clearfix">
						<form name="hb-search-results" class="hb-search-room-results <?php echo $class_item ?> <?php echo ! empty( $custom_process ) ? ' custom-process' : ' extra-option-loop'; ?>" >
						<?php do_action( 'hotel_booking_loop_before_item', $room->ID ); ?>
							<?php
								\Thim_EL_Kit\Utilities\Elementor::instance()->render_loop_item_content( $settings['template_id'] );
							?>
							<?php wp_nonce_field( 'hb_booking_nonce_action', 'nonce' ); ?>
							<input type="hidden" name="check_in_date"
									value="<?php echo hb_get_request( 'check_in_date' ); ?>"/>
							<input type="hidden" name="check_out_date"
									value="<?php echo hb_get_request( 'check_out_date' ); ?>">
							<input type="hidden" name="room-id" value="<?php echo esc_attr( $room->ID ); ?>">
							<input type="hidden" name="hotel-booking" value="cart">
							<input type="hidden" name="action" value="hotel_booking_ajax_add_to_cart"/>

							<?php do_action( 'hotel_booking_loop_after_item', $room->ID ); ?>
						</form>
					</div>
				<?php } ?>
			</div>
			<?php $this->render_loop_footer( $results, $settings ); ?>
		</div>
		<?php
	}

	protected function render_loop_footer( $query, $settings ) {
		$ajax_pagination = in_array( $settings['pagination_type'], array( 'load_more_on_click', 'load_more_infinite_scroll' ), true );

		if ( '' === $settings['pagination_type'] ) {
			return;
		}

		$page_limit = hb_settings()->get( 'posts_per_page', 8 );

		if ( 2 > $page_limit ) {
			return;
		}

		$has_numbers   = in_array( $settings['pagination_type'], array( 'numbers', 'numbers_and_prev_next' ) );

		$only_prev_next = in_array( $settings['pagination_type'], array( 'prev_next') );

		$load_more_type = $settings['pagination_type'];

		if ( $settings['pagination_type'] === '' ) {
			$paged = 1;
		} else {
			$paged = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
		}

		$current_page = $this->get_current_page();
		$next_page = intval( $current_page ) + 1;

		if ( $ajax_pagination ) {
			$this->render_load_more_pagination( $settings, $load_more_type, $paged, $page_limit, $next_page );
			return;
		}

		$links = array();
		$show_prev_next = false;
		if($settings['pagination_type'] == 'numbers_and_prev_next'){
			$show_prev_next = true;
		}
		if ( $has_numbers ) {
			$paginate_args = array(
				'type'               => 'array',
				'current'            => $paged,
				'total'              => $page_limit,
				'prev_next'          => $show_prev_next,
				'prev_text'          => $settings['pagination_prev_label'],
				'next_text'          => $settings['pagination_next_label'],
				'show_all'           => 'yes' !== $settings['pagination_numbers_shorten'],
				'before_page_number' => '<span class="elementor-screen-only">' . esc_html__( 'Page', 'thim-elementor-kit' ) . '</span>',
			);

			if ( is_singular() && ! is_front_page() ) {
				global $wp_rewrite;

				if ( $wp_rewrite->using_permalinks() ) {
					$paginate_args['base']   = trailingslashit( get_permalink() ) . '%_%';
					$paginate_args['format'] = user_trailingslashit( '%#%', 'single_paged' );
				} else {
					$paginate_args['format'] = '?page=%#%';
				}
			}

			$links = paginate_links( $paginate_args );
		}

		if ( $only_prev_next ) {
			$prev_next = $this->get_posts_nav_link( $query, $paged, $page_limit, $settings );
			array_unshift( $links, $prev_next['prev'] );
			$links[] = $prev_next['next'];
		}
		?>
		<nav class="hb-room-archive__pagination" aria-label="<?php esc_attr_e( 'Pagination', 'thim-elementor-kit' ); ?>">
			<?php echo wp_kses_post( implode( PHP_EOL, $links ) ); ?>
		</nav>
		<?php
	}

	public function get_posts_nav_link( $query, $paged, $page_limit = null, $settings = array() ) {
		if ( ! $page_limit ) {
			$page_limit = $query->max_num_pages;
		}

		$return = array();

		$link_template     = '<a class="page-numbers %s" href="%s">%s</a>';
		$disabled_template = '<span class="page-numbers %s">%s</span>';

		if ( $paged > 1 ) {
			$next_page = intval( $paged ) - 1;

			if ( $next_page < 1 ) {
				$next_page = 1;
			}

			$return['prev'] = sprintf( $link_template, 'prev', $this->get_wp_link_page( $next_page ), $settings['pagination_prev_label'] );
		} else {
			$return['prev'] = sprintf( $disabled_template, 'prev', $settings['pagination_prev_label'] );
		}

		$next_page = intval( $paged ) + 1;

		if ( $next_page <= $page_limit ) {
			$return['next'] = sprintf( $link_template, 'next', $this->get_wp_link_page( $next_page ), $settings['pagination_next_label'] );
		} else {
			$return['next'] = sprintf( $disabled_template, 'next', $settings['pagination_next_label'] );
		}

		return $return;
	}

	public function get_current_page() {
		if ( '' === $this->get_settings_for_display( 'pagination_type' ) ) {
			return 1;
		}

		return max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
	}

	private function get_wp_link_page( $i ) {
		if ( ! is_singular() || is_front_page() ) {
			return get_pagenum_link( $i );
		}

		global $wp_rewrite;
		$post       = get_post();
		$query_args = array();
		$url        = get_permalink();

		if ( $i > 1 ) {
			if ( '' === get_option( 'permalink_structure' ) || in_array( $post->post_status, array( 'draft', 'pending' ) ) ) {
				$url = add_query_arg( 'page', $i, $url );
			} elseif ( get_option( 'show_on_front' ) === 'page' && (int) get_option( 'page_on_front' ) === $post->ID ) {
				$url = trailingslashit( $url ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . $i, 'single_paged' );
			} else {
				$url = trailingslashit( $url ) . user_trailingslashit( $i, 'single_paged' );
			}
		}

		if ( is_preview() ) {
			if ( ( 'draft' !== $post->post_status ) && isset( $_GET['preview_id'], $_GET['preview_nonce'] ) ) {
				$query_args['preview_id']    = absint( wp_unslash( $_GET['preview_id'] ) );
				$query_args['preview_nonce'] = sanitize_text_field( wp_unslash( $_GET['preview_nonce'] ) );
			}

			$url = get_preview_post_link( $post, $query_args, $url );
		}

		return $url;

	}

}
