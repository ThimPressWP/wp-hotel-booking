<?php

/**
 * AbstractBlockTemplate class.
 *
 * View woocommerce/packages/woocommerce-blocks/src/BlockTypes/AbstractBlock.php
 */
abstract class AbstractBlockTemplate extends \WP_Block_Template {
	public $theme = 'wp-hotel-booking/wp-hotel-booking';
	public $type  = 'wp_template';
	/**
	 * @var string name of the block
	 */
	public $name                          = '';
	public $origin                        = 'plugin';
	public $source                        = 'plugin'; // plugin|custom|theme, if custom save on db will be use 'custom'.
	public $content                       = ''; // Set content will be show on edit block and the frontend.
	public $has_theme_file                = true;
	public $is_custom                     = false;
	public $path_html_block_template_file = '';
	/**
	 * @var bool|string path of the file block.json metadata.
	 */
	public $inner_block = false;

	public function __construct() {
		if ( ! wp_is_block_theme() ) {
			return;
		}
		$this->id      = $this->theme . '//' . $this->slug;
		$template_file = '';

		if ( ! empty( $this->path_html_block_template_file ) ) {
			$template_file = hb_locate_template( $this->path_html_block_template_file, '', WPHB_PLUGIN_PATH . '/block-templates/' );
		}
		// Set content from theme file.
		if ( file_exists( $template_file ) ) {
			$content       = file_get_contents( $template_file );
			$this->content = _inject_theme_attribute_in_block_template_content( $content );
		}
	}

	/**
	 * Render content of block tag
	 *
	 * @param array $attributes | Attributes of block tag.
	 *
	 * @return false|string
	 */
	public function render_content_block_template( array $attributes ) {
		ob_start();

		if ( isset( $attributes['template'] ) ) {
			hb_get_template( $attributes['template'] );
		}

		return ob_get_clean();
	}
}
