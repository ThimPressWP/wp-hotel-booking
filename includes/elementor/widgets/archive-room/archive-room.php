<?php

namespace Elementor;

use Thim_EL_Kit\GroupControlTrait;
use WPHB_Post_Types;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Thim_Ekit_Widget_Archive_Room extends Widget_Base {

	use GroupControlTrait;

	protected $current_permalink;

    public function get_name() {
		return 'wphb-archive-room';
	}

    public function get_title() {
		return esc_html__( 'Archive Room', 'wp-hotel-booking' );
	}

	public function get_icon() {
		return 'thim-eicon eicon-archive-posts';
	}

	public function get_categories() {
		return array( \WPHB\Elementor::CATEGORY_ARCHIVE_ROOM );
	}

    public function get_keywords() {
		return array( 'room', 'archive' );
	}

	public function get_help_url() {
		return '';
	}

    protected function register_controls() {
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

		$repeater_header = new \Elementor\Repeater();

		$repeater_header->add_control(
			'header_key',
			array(
				'label'   => esc_html__( 'Type', 'wp-hotel-booking' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'result',
				'options' => array(
					'result'    => 'Result Count',
					'order' 	=> 'Order',
				),
			)
		);

		$this->add_control(
			'thim_header_repeater',
			array(
				'label'       => esc_html__( 'Top Bar', 'wp-hotel-booking' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater_header->get_controls(),
				'prevent_empty'=>false,
				'title_field' => '<span style="text-transform: capitalize;">{{{ header_key.replace("_", " ") }}}</span>',
			)
		);

		$this->end_controls_section();

        $this->register_style_topbar();
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

	protected function register_style_topbar() {
		$this->start_controls_section(
			'section_style_topbar',
			array(
				'label' => esc_html__( 'Top Bar', 'wp-hotel-booking' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'topbar_spacing',
			array(
				'label'     => esc_html__( 'Spacing', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .hb-room-archive__topbar' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'topbar_gap',
			array(
				'label'     => esc_html__( 'Gap', 'wp-hotel-booking' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .hb-room-archive__topbar' => '--hb-room-archive-topbar-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

    public function render(){

        global $wp_query;

		$query_vars = $wp_query->query_vars;

		if ( !empty($_GET['order_by']) ) {
			$orderby = sanitize_key( $_GET['order_by'] );
			$orderby = explode('-', $orderby );

			if (isset($orderby) && is_array($orderby)){		
				$query_vars['orderby'] = $orderby[0];
				$query_vars['order'] = $orderby[1];
			}
		}
		
		if ( null !== get_queried_object_id() && ! empty( get_queried_object_id() ) && get_post_type() == 'hb_room') {
			//Price
			$min_price = $_GET['min_price'] ?? null;
			$max_price = $_GET['max_price'] ?? null;

			if ( $min_price !== null && $max_price !== null ) {
				$query_vars['meta_query'][] = array(
					'key'     => 'hb_price',
					'value'   => array( $min_price, $max_price ),
					'type'    => 'DECIMAL',
					'compare' => 'BETWEEN',
				);
			}

			//Rating
			$rating = hb_get_request( 'rating' );

			if ( $rating ) {
				$rating = explode( ',', $rating );

				$unrated_index = array_search( 'unrated', $rating );

				if ( $unrated_index !== false ) {
					$rating[ $unrated_index ] = 0;
				}

				$rating_count = count( $rating );
				if ( ! empty( $rating_count ) ) {
					$rating_query = array();

					if ( $rating_count === 1 ) {
						$rating_query[] = array(
							'key'     => 'hb_average_rating',
							'value'   => $rating[0],
							'type'    => 'NUMERIC',
							'compare' => '>=',
						);

						$rating_query[] = array(
							'key'     => 'hb_average_rating',
							'value'   => $rating[0] + 1,
							'type'    => 'NUMERIC',
							'compare' => '<',
						);

						$rating_query ['relation'] = 'AND';
					} else {
						for ( $i = 0; $i < $rating_count; $i++ ) {
							$rating_query[ $i ][] = array(
								'key'     => 'hb_average_rating',
								'value'   => $rating[ $i ],
								'type'    => 'NUMERIC',
								'compare' => '>=',
							);
							$rating_query[ $i ][] = array(
								'key'     => 'hb_average_rating',
								'value'   => $rating[ $i ] + 1,
								'type'    => 'NUMERIC',
								'compare' => '<',
							);

							$rating_query[ $i ]['raltion'] = 'AND';
						}

						$rating_query ['relation'] = 'OR';
					}

					$query_vars['meta_query'][] = $rating_query;
				}
			}

			if ( isset( $query_vars['meta_query'] ) ) {
				$query_vars['meta_query']['relation'] = 'AND';
			}
		}

		$query_vars = apply_filters( 'WPHB/modules/archive_room/query_posts/query_vars', $query_vars );

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$query_vars  = array(
				'post_type'    => 'hb_room',
				'status'       => 'publish',
			);
		}

		if ( $query_vars !== $wp_query->query_vars ) {
			$rooms = new \WP_Query( $query_vars );
		} else {
			$rooms = $wp_query;
		}

		if ( ! $rooms->found_posts ) {
            echo '<p class="message message-error">' . esc_html__( 'No room found !', 'wp-hotel-booking' ) . '</p>';
			return;
		}

        $settings = $this->get_settings_for_display(); 
        $class_item  = 'hb-room-archive__article'; ?>

        <div class="hb-room-archive">
            <?php $this->render_topbar( $rooms, $settings ); ?>

            <div class="hb-room-archive__inner">
                <?php 
                while ( $rooms->have_posts() ) {
                    $rooms->the_post();
                    $this->current_permalink = get_permalink(); ?>

                    <div <?php post_class( array( $class_item ) ); ?>>
                        <?php  
                            \Thim_EL_Kit\Utilities\Elementor::instance()->render_loop_item_content( $settings['template_id'] ); 
                        ?>
                    </div>
                <?php } ?>
            </div>

            <?php $this->render_loop_footer( $rooms, $settings ); ?>       
        </div>

        <?php
    }

	protected function render_topbar( $query, $settings ) {
		if ( $settings['thim_header_repeater'] ) {
			?>
			<div class="hb-room-archive__topbar">
				<?php
				foreach ( $settings['thim_header_repeater'] as $item ) {
					switch ( $item['header_key'] ) {
 						case 'result':
							$this->render_result_count( $query );
							break;
						case 'order':
							$this->render_orderby( $item );
							break;
					}
				}
				?>
			</div>
			<?php
		}
	}

	protected function render_result_count( $query ) {
		global $hb_settings;
		$total = $query->found_posts;

		if ( $total == 1 ) {
			$index = esc_html__( 'Showing only one result', 'wp-hotel-booking' );
		} else {
			$post_per_page_get = $hb_settings->get( 'posts_per_page', 8 );
			$post_per_page     = is_numeric( $post_per_page_get ) ? $post_per_page_get : 9;
			$paged             = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;

			$from = 1 + ( $paged - 1 ) * $post_per_page;
			$to   = ( $paged * $post_per_page > $total ) ? $total : $paged * $post_per_page;

			if ( $from == $to ) {
				$index = sprintf(
					esc_html__( 'Showing last post of %s results', 'wp-hotel-booking' ),
					$total
				);
			} else {
				$index = sprintf(
					esc_html__( 'Showing %s - %s of %s results', 'wp-hotel-booking' ),
					$from,
					$to,
					$total
				);
			}
		}
		?>

		<span class="hb-room-archive__topbar__result">
			<?php echo( $index ); ?>
		</span>
		<?php
	}

	protected function render_orderby( $settings ) {
		$catalog_orderby_options = apply_filters(
			'hb_room_archive_orderby',
			array(
				'date-DESC' => esc_html__( 'Default (Newest)', 'wp-hotel-booking' ),
				'date-ASC'  => esc_html__( 'Oldest', 'wp-hotel-booking' ),
				'title-ASC' => esc_html__( 'A to Z', 'wp-hotel-booking' ),
				'title-DESC'=> esc_html__( 'Z to A', 'wp-hotel-booking' ),
			)
		);

		$orderby = isset( $_GET['order_by'] ) ? sanitize_text_field( wp_unslash( $_GET['order_by'] ) ) : 'post_date';

		if ( ! array_key_exists( $orderby, $catalog_orderby_options ) ) {
			$orderby = current( array_keys( $catalog_orderby_options ) );
		}
		?>
		<form class="hb-room-archive__topbar__orderby " method="get">
			<select name="order_by" class="orderby room-order-by" aria-label="<?php esc_attr_e( 'Room order', 'wp-hotel-booking' ); ?>" onchange="this.form.submit()">
				<?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
					<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
				<?php endforeach; ?>
			</select>
			<input type="hidden" name="paged" value="1">
		</form>
		<?php
	}

	protected function render_loop_footer( $query, $settings ) {
		$ajax_pagination = in_array( $settings['pagination_type'], array( 'load_more_on_click', 'load_more_infinite_scroll' ), true );

		if ( '' === $settings['pagination_type'] ) {
			return;
		}

		$page_limit = $query->max_num_pages;

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

		// Based on wp-includes/post-template.php:957 `_wp_link_page`.
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