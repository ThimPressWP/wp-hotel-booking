<?php

namespace WPHB\Modules\SingleRoom;

use Thim_EL_Kit\Modules\Modules;
use Thim_EL_Kit\SingletonTrait;

class Init extends Modules {
	use SingletonTrait;

	public function __construct() {
		$this->tab      = 'single-room';
		$this->tab_name = esc_html__( 'Single Room', 'wp-hotel-booking' );
		parent::__construct();

		add_action( 'WPHB/modules/single-room/before-preview-query', array( $this, 'before_preview_query' ) );
		add_action( 'WPHB/modules/single-room/after-preview-query', array( $this, 'after_preview_query' ) );
		add_filter( 'thim_ekit/elementor/documents/preview_item', array( $this, 'add_preview_type' ), 10, 2 );
	}

	public function template_include( $template ) {
		$this->template_include = is_singular( 'hb_room' );

		return parent::template_include( $template );
	}

	public function get_preview_id() {
		global $post;

		$output = false;

		if ( $post ) {
			$document = \Elementor\Plugin::$instance->documents->get( $post->ID );

			if ( $document ) {
				$preview_id = $document->get_settings( 'thim_ekits_preview_id' );

				$output = ! empty( $preview_id ) ? absint( $preview_id ) : false;
			}
		}

		return $output;
	}

	public function add_preview_type( $post_type, $type ) {
		if ( $type === 'single-room' ) {
			$post_type = 'hb_room';
		}

		return $post_type;
	}

	public function before_preview_query() {
		if ( $this->is_editor_preview() || $this->is_modules_view() ) {
			$this->after_preview_query();
			$preview_id = $this->get_preview_id();

			if ( $preview_id ) {
				$query = array(
					'p'         => absint( $preview_id ),
					'post_type' => 'hb_room',
				);
			} else {
				$query_vars = array(
					'post_type'      => 'hb_room',
					'posts_per_page' => 1,
				);

				$posts = get_posts( $query_vars );

				if ( ! empty( $posts ) ) {
					$query = array(
						'p'         => $posts[0]->ID,
						'post_type' => 'hb_room',
					);
				}
			}

			if ( ! empty( $query ) ) {
				\Elementor\Plugin::instance()->db->switch_to_query( $query, true );
			}
		}
	}

	public function after_preview_query() {
		if ( $this->is_editor_preview() || $this->is_modules_view() ) {
			\Elementor\Plugin::instance()->db->restore_current_query();
		}
	}

	public function priority( $type ) {
		$priority = 100;

		switch ( $type ) {
			case 'all':
				$priority = 10;
				break;
		}

		return apply_filters( 'thim_ekit_pro/condition/priority', $priority, $type );
	}

	public function is( $condition ) {

		switch ( $condition['type'] ) {
			case 'all':
				return is_room();
		}
	}

	public function get_conditions() {
		return array(
			array(
				'label'    => esc_html__( 'Single Room', 'wp-hotel-booking' ),
				'value'    => 'all',
				'is_query' => false,
			),
		);
	}
}

Init::instance();
