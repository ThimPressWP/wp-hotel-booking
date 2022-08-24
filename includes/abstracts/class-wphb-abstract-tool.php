<?php
/**
 * Abstract WP Hotel Booking admin tool class.
 *
 * @class       WPHB_Abstract_Tool
 * @version     1.9.7.4
 * @package     WP_Hotel_Booking/Classes
 * @category    Abstract Class
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'WPHB_Abstract_Tool' ) ) {

	/**
	 * Class WPHB_Abstract_Tool.
	 *
	 * @since 2.0
	 */
	abstract class WPHB_Abstract_Tool {

		/**
		 * Setting tab id.
		 *
		 * @var null
		 */
		protected $id = null;

		/**
		 * Setting tab title.
		 *
		 * @var null
		 */
		protected $title = null;

		/**
		 * WPHB_Abstract_Tool constructor.
		 */
		public function __construct() {
			add_filter( 'wphb/admin/tool-tabs', array( $this, 'tool_tabs' ) );
			add_action( 'wphb/admin/tools-tab-' . $this->id, array( $this, 'output' ) );
		}

		/**
		 * @param $tabs
		 *
		 * @return array
		 */
		public function tool_tabs( $tabs ) {
			$tabs[ $this->id ] = $this->title;

			return $tabs;
		}

		/**
		 * Out tool tab.
		 */
		public function output() {
			return;
		}
	}
}
