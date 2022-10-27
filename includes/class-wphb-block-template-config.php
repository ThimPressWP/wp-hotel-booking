<?php

defined( 'ABSPATH' ) || exit;

/**
 * Class Block_Template_Config
 *
 * Handle register, render block template
 */
class Block_Template_Config {

	private static $instance;

	protected $block_template = array();

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		$this->block_templates = array(
			'BlockTemplateArchiveProperty',
		);

		add_action( 'wp_loaded', array( $this, 'add_includes' ) );
	}

	public function add_includes() {
		require_once WPHB_PLUGIN_PATH . '/includes/abstracts/class-wphb-abstract-block-template.php';
		require_once WPHB_PLUGIN_PATH . '/includes/templates/class-wphb-block-template-archive.php';

		add_filter( 'get_block_templates', array( $this, 'add_block_templates' ), 10, 3 );
		add_action( 'init', array( $this, 'register_tag_block' ) );

	}

	public function add_block_templates( array $query_result, array $query, $template_type ): array {
		if ( $template_type === 'wp_template_part' ) { // Template not Template part
			return $query_result;
		}

		foreach ( $this->block_templates as $block_template ) {
			$new = new $block_template();

			// Get block template if custom - save on table posts.
			$block_custom = $this->is_custom_block_template( $template_type, $new->slug );

			if ( $block_custom ) {
				$new->is_custom = true;
				$new->source    = 'custom';
				$new->content   = _inject_theme_attribute_in_block_template_content( $block_custom->post_content );
			}

			if ( empty( $query ) ) { // For Admin and rest api call to this function, so $query is empty
				$query_result[] = $new;
			} else {
				$slugs = $query['slug__in'] ?? array();

				if ( in_array( $new->slug, $slugs ) ) {
					$query_result[] = $new;
				}
			}
		}

		return $query_result;
	}

	/**
	 * Check is custom block template
	 *
	 * @param $template_type
	 * @param $post_name
	 *
	 * @return WP_Post|null
	 */
	public function is_custom_block_template( $template_type, $post_name ) {
		$post_block_theme = null;

		$check_query_args = array(
			'post_type'      => $template_type,
			'posts_per_page' => 1,
			'no_found_rows'  => true,
			'post_name__in'  => array( $post_name ),
		);

		$check = new \WP_Query( $check_query_args );

		if ( count( $check->get_posts() ) > 0 ) {
			$post_block_theme = $check->get_posts()[0];
		}

		return $post_block_theme;
	}


	public function register_tag_block() {

		foreach ( $this->block_templates as $block_template ) {
			$block_template = new $block_template();
			if ( $block_template->inner_block ) {
				register_block_type_from_metadata(
					$block_template->inner_block,
					array(
						'render_callback' => array( $block_template, 'render_content_inner_block_template' ),
					)
				);
				continue;
			}
			// Render content block template parent
			register_block_type(
				$block_template->name,
				array(
					'render_callback' => array( $block_template, 'render_content_block_template' ),
				)
			);
		}
	}
}

Block_Template_Config::instance();


