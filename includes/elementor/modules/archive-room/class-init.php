<?php

namespace WPHB\Modules;

use Thim_EL_Kit\Modules\Modules;
use Thim_EL_Kit\SingletonTrait;

class Inits extends Modules {
	use SingletonTrait;

	public function __construct() {
		$this->tab      = 'archive-room';
		$this->tab_name = esc_html__( 'Archive Room', 'realpress' );

		parent::__construct();

		add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), 10 );
	}

	public function template_include( $template ) {
		$this->template_include = is_post_type_archive( 'hb_room' );

		return parent::template_include( $template );
	}

    /**
     * 
     *
     * @param $query \WP_Query
     */
	public function pre_get_posts( \WP_Query $query ): \WP_Query {
		$is_archive_room = is_post_type_archive( 'hb_room' );

		if ( $is_archive_room && ! $this->is_editor_preview() && ! is_admin() && isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] === 'hb_room' ) {
			$room_id = $this->get_layout_id( $this->tab );

            if ( ! empty( $room_id ) ) {
                global $hb_settings;
                $query->set( 'posts_per_page', $hb_settings->get( 'posts_per_page', 8 ) );
			}

		}

		return $query;
	}

	public function priority( $type ) {
		$priority = 100;

		switch ( $type ) {
			case 'all':
				$priority = 10;
				break;
			case 'hb_room_type':
				$priority = 20;
				break;
		}

		return apply_filters( 'thim_ekit_pro/condition/priority', $priority, $type );
	}

	public function is( $condition ) {

		switch ( $condition['type'] ) {
			case 'all':
				return is_post_type_archive( 'hb_room' );
			case 'hb_room_type':
				$object      = get_queried_object();
				$taxonomy_id = is_object( $object ) && property_exists( $object, 'term_id' ) ? $object->term_id : false;
				return (int) $taxonomy_id === (int) $condition['query'] && ! is_search();
		}
	}

	public function get_conditions() {
		return array(
			array(
				'label'    => esc_html__( 'All Room', 'realpress' ),
				'value'    => 'all',
				'is_query' => false,
			),
			array(
				'label'    => esc_html__( 'Select Room Type', 'realpress' ),
				'value'    => 'hb_room_type',
				'is_query' => true,
			),
		);
	}
}

Inits::instance();
