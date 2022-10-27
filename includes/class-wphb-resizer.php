<?php
/**
 * WP Hotel Booking resizer.
 *
 * @version       1.9.6
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Classes
 * @category      Classes
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Reizer' ) ) {
	/**
	 * Class WPHB_Reizer
	 */
	class WPHB_Reizer {

		/**
		 * Base path upload folder. Ex: wp-content/uploads
		 *
		 * @var null
		 */
		protected $_upload_base_dir = null;

		/**
		 * Base path upload folder. Ex: localhost/wp/wp-content/uploads
		 *
		 * @var null
		 */
		protected $_upload_base_url = null;

		/**
		 * @var null
		 */
		static $_instance = null;

		/**
		 * List attachment image in the room. gallery, thumbnail
		 *
		 * @var null
		 */
		public static $_attachments = null;

		/**
		 * @var null
		 */
		public static $args = null;

		/**
		 * WPHB_Reizer constructor.
		 *
		 * @param null $args
		 */
		public function __construct( $args = null ) {
			self::$args = $args;

			if ( ! self::$_attachments ) {
				self::getAttachments();
			}
		}

		/**
		 * @param null $args
		 *
		 * @return null|WPHB_Reizer
		 */
		public static function getInstance( $args = null ) {
			if ( ! self::$_instance ) {
				self::$_instance = new self( $args );
			}

			return self::$_instance;
		}

		/**
		 * @return array|null
		 */
		public static function getAttachments() {
			global $wpdb;
			$posts = $wpdb->get_results( "SELECT * FROM `{$wpdb->posts}` WHERE `post_type` = 'attachment' AND `guid` != ''" );

			foreach ( $posts as $key => $post ) {
				self::$_attachments[] = $post->guid;
			}

			return self::$_attachments;
		}

		/**
		 * @param null  $attachmentID
		 * @param array $size
		 * @param bool  $single
		 * @param bool  $upscale
		 *
		 * @return array|bool|string
		 */
		public static function process( $attachmentID = null, $size = array(), $single = false, $upscale = true ) {
			$aq_resize = Aq_Resize::getInstance();
			global $hb_settings;
			if ( ! $attachmentID ) {
				return false;
			}

			if ( ! isset( $size['width'] ) || ! isset( $size['height'] ) ) {
				return false;
			}

			// generator image file with size setting in frontend
			$attachment = wp_get_attachment_url( $attachmentID );

			return $aq_resize->process( $attachment, (int) $size['width'], (int) $size['height'], true, $single, $upscale );
		}
	}
}
