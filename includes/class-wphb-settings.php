<?php
/**
 * WP Hotel Booking settings.
 *
 * @version       1.9.7.4
 * @author        ThimPress
 * @package       WP_Hotel_Booking/Classes
 * @category      Classes
 * @author        Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPHB_Settings' ) ) {
	/**
	 * Class WPHB_Settings
	 */
	class WPHB_Settings {

		/**
		 * @var object
		 */
		protected static $_instances = array();

		/**
		 * The prefix of wp option name will be stored in database
		 *
		 * @var string
		 */
		protected $_option_prefix = 'tp_hotel_booking_';

		/**
		 * @var array
		 */
		protected $_options = array();

		/**
		 * @var array
		 */
		protected $_resizeImage = array();

		/**
		 * WPHB_Settings constructor.
		 *
		 * @param null  $new_prefix
		 * @param array $default
		 */
		public function __construct( $new_prefix = null, $default = array() ) {

			add_action( 'admin_init', array( $this, 'update_settings' ) );

			if ( $new_prefix ) {
				$this->_option_prefix = $new_prefix;
			}

			if ( is_object( $default ) ) {
				$default = (array) $default;
			}

			if ( is_array( $default ) ) {
				foreach ( $default as $k => $value ) {
					add_option( $this->_option_prefix . $k, $value );
				}
			}
			$this->_load_options();
		}

		/**
		 * Get an option
		 *
		 * @param string
		 *
		 * @return mixed
		 */
		public function get( $name, $default = false ) {
			if ( strpos( $name, 'tp_hotel_booking_' ) === 0 ) {
				$name = str_replace( 'tp_hotel_booking_', '', $name );
			}
			if ( ! empty( $this->_options[ $name ] ) ) {
				return $this->_options[ $name ];
			}

			return $default;
		}

		/**
		 * Update new value for an option
		 *
		 * @param string
		 * @param mixed
		 *
		 * @return array
		 */
		public function set( $name, $value ) {
			// update option
			update_option( $this->_option_prefix . $name, $value );
			$this->_options[ $name ] = $value;

			// allow hook
			do_action( 'hb_update_settings_' . $name, $name, $value );

			add_action( 'admin_notices', array( $this, 'notice_success' ) );

			return $this->_options;
		}

		/**
		 * Admin notice settings saved.
		 */
		public function notice_success() {
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php _e( 'Settings saved.', 'wp-hotel-booking' ); ?></p>
			</div>
			<?php
		}

		/**
		 * Remove an option
		 *
		 * @param string
		 *
		 * @return array
		 */
		public function remove( $name ) {
			if ( array_key_exists( $name, $this->_options ) ) {
				unset( $this->_options[ $name ] );
			}

			return $this->_options;
		}

		/**
		 * Update all options into database
		 */
		public function update() {
			if ( $this->_options ) {
				foreach ( $this->_options as $k => $v ) {
					update_option( $this->_option_prefix . $k, $v );
				}
			}
		}

		/**
		 * Get the name of field
		 *
		 * @param string
		 *
		 * @return string
		 */
		public function get_field_name( $name ) {
			return $this->_option_prefix . $name;
		}

		/**
		 * Get the id of field
		 *
		 * @param string
		 *
		 * @return string
		 */
		public function get_field_id( $name ) {
			return sanitize_title( $this->get_field_name( $name ) );
		}

		/**
		 * Update settings
		 */
		public function update_settings() {
		
			if ( empty( $_POST ) || ( empty( $_POST['wphb_meta_box_settings_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['wphb_meta_box_settings_nonce'] ), 'wphb_update_meta_box_settings' ) ) ) {
				return;
			}

			if ( ! current_user_can( 'administrator' ) ) {
				return;
			}

			foreach ( $_POST as $k => $v ) {
				if ( preg_match( '!^' . $this->_option_prefix . '!', $k ) ) {
					$option_key = preg_replace( '!^' . $this->_option_prefix . '!', '', $k );
					if ( ! $option_key ) {
						continue;
					}

					$v = WPHB_Helpers::sanitize_params_submitted( $v );

					$this->set( $option_key, $v );
				}
			}
			$this->update();
		}

		/**
		 * @return array
		 */
		private function _load_options() {
			global $wpdb;
			$query = $wpdb->prepare(
				"
                SELECT option_name, option_value
                FROM {$wpdb->options}
                WHERE option_name LIKE %s
            ",
				$this->_option_prefix . '%'
			);
			if ( $options = $wpdb->get_results( $query ) ) {
				foreach ( $options as $option ) {
					$name                    = str_replace( $this->_option_prefix, '', $option->option_name );
					$this->_options[ $name ] = maybe_unserialize( $option->option_value );
				}
			}

			return $this->_options;
		}

		/**
		 * Magic function to convert object to string with json format
		 *
		 * @return string
		 */
		public function __toString() {
			return json_encode( $this->_options );
		}

		/**
		 * Return settings to json format
		 * If $fields is empty, all fields will be converted
		 *
		 * @param array $fields
		 *
		 * @return string
		 */
		public function toJson( $fields = array() ) {
			if ( $fields ) {
				$options = array();
				foreach ( $fields as $k => $v ) {
					$options[ $v ] = $this->get( $v );
				}
				$return = json_encode( $options );
			} else {
				$return = json_encode( $this->_options );
			}

			return $return;
		}

		public function get_prefix() {
			return $this->_option_prefix;
		}

		/**
		 * Get unique instance of WPHB_Settings
		 * Create a new one if it is not created
		 *
		 * @param string
		 * @param array
		 *
		 * @return WPHB_Settings instance
		 */
		public static function instance( $prefix = null, $default = array() ) {
			if ( ! $prefix || ! is_string( $prefix ) ) {
				$prefix = 'tp_hotel_booking_';
			}
			if ( empty( self::$_instances[ $prefix ] ) ) {
				self::$_instances[ $prefix ] = new self( $prefix, $default );
			}

			return self::$_instances[ $prefix ];
		}

		/**
		 * Get page redirect option
		 *
		 * @return string
		 */
		public function getPageRedirect() {
			$redirectPage            = '';
			$idPageCart              = (int) get_option( 'tp_hotel_booking_cart_page_id', 0 );
			$idPageRedirectAfterBook = (int) get_option( 'tp_hotel_booking_go_page_after_booking', 0 );

			if ( $idPageRedirectAfterBook > 0 ) {
				$redirectPage = get_the_permalink( $idPageRedirectAfterBook );
			} elseif ( $idPageCart > 0 ) {
				$redirectPage = get_the_permalink( $idPageCart );
			}

			return $redirectPage;
		}
	}
}

$GLOBALS['hb_settings'] = WPHB_Settings::instance();

if ( ! function_exists( 'hb_settings' ) ) {
	/**
	 * @return WPHB_Settings
	 */
	function hb_settings() {
		return WPHB_Settings::instance();
	}
}
