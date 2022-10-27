<?php
/**
 * Class BlockTemplate
 *
 * Handle register, render block template
 */
class BlockTemplateArchiveProperty extends AbstractBlockTemplate {
	public $slug                          = 'archive-room';
	public $name                          = 'wp-hotel-booking/archive-room';
	public $title                         = 'Archive Rooms';
	public $description                   = 'Archive Rooms Block Template';
	public $path_html_block_template_file = 'archive-room.html';

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Render content of block tag
	 *
	 * @param array $attributes | Attributes of block tag.
	 *
	 * @return false|string
	 */
	public function render_content_block_template( array $attributes ) {
		return parent::render_content_block_template( $attributes );
	}
}
new BlockTemplateArchiveProperty();
