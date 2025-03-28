<?php

namespace WPHB\Helpers;

/**
 * Class Template
 *
 * @package WPHB\Helpers
 * @since 2.1.8-beta.1
 * @version 1.0.0
 */

class Template {
	/**
	 * @var bool
	 */
	protected $include;

	protected function __construct() {
	}

	/**
	 * Set 1 for include file, 0 for not
	 * Set 1 for separate template is block, 0 for not | use "wp_is_block_theme" function
	 *
	 * @param bool $has_include
	 *
	 * @return self
	 */
	public static function instance( bool $has_include = true ): Template {
		$self          = new self();
		$self->include = $has_include;

		return $self;
	}

	/**
	 * Nest elements by tags
	 *
	 * @param array $els [ 'html_tag_open' => 'html_tag_close' ]
	 * @param string $main_content
	 *
	 * @return string
	 */
	public function nest_elements( array $els = [], string $main_content = '' ): string {
		$html = '';
		foreach ( $els as $tag_open => $tag_close ) {
			$html .= $tag_open;
		}

		$html .= $main_content;

		foreach ( array_reverse( $els, true ) as $tag_close ) {
			$html .= $tag_close;
		}

		return $html;
	}

	/**
	 * Combine html elements
	 *
	 * @param array $elms
	 *
	 * @return string
	 */
	public static function combine_components( array $elms = [] ): string {
		$html = '';
		foreach ( $elms as $tag => $val ) {
			$html .= $val;
		}

		return $html;
	}
}