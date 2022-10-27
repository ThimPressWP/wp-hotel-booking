<?php
/**
 * WP Hotel Booking Extra Settings.
 *
 * @version       1.9.6
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Extra/Classes
 * @category      Classes
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'HB_Extra_Settings' ) ) {

	class HB_Extra_Settings {

		/**
		 * @var null
		 */
		protected $_options = null;

		/**
		 * @var null
		 */
		protected $_type = null;

		/**
		 * @var null
		 */
		static $_self = null;

		/**
		 * HB_Extra_Settings constructor.
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'admin_init', array( $this, 'admin_init' ) );
		}

		/**
		 * Load extra room post type
		 */
		function init() {
			if ( ! $this->_options ) {
				$this->_options = $this->get_extra();
			}
		}

		/**
		 * Admin init.
		 */
		public function admin_init() {
			if ( ! isset( $_POST ) || empty( $_POST ) ) {
				return;
			}

			if ( ! isset( $_POST[ WPHB_EXTRA_OPTION_NAME ] ) || empty( $_POST[ WPHB_EXTRA_OPTION_NAME ] ) ) {
				return;
			}

			$post_type             = HB_Extra_Post_Type::instance();
			$wphbExtraOpionNameArr = (array) $_POST[ WPHB_EXTRA_OPTION_NAME ];

			foreach ( $wphbExtraOpionNameArr as $post_id => $post ) {
				$post_id = absint( $post_id );
				$post    = WPHB_Helpers::sanitize_params_submitted( $post );
				$post_type->add_extra( $post_id, $post );
			}
		}

		/**
		 * @return array|null|object
		 */
		public function get_extra() {
			global $wpdb;
			$query = $wpdb->prepare(
				"
				SELECT * FROM $wpdb->posts WHERE `post_type` = %s
			",
				'hb_extra_room'
			);

			return $wpdb->get_results( $query, OBJECT );
		}

		/**
		 * Get instance instead of new ClassName();
		 *
		 * @return HB_Extra_Settings|null
		 */
		static function instance() {
			if ( ! self::$_self ) {
				return new self();
			}

			return self::$_self;
		}
	}
}

// set global variable hb_extra_settings
$GLOBALS['hb_extra_settings'] = HB_Extra_Settings::instance();
