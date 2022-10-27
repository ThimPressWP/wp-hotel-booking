<?php
/**
 * WP Hotel Booking sessions.
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

if ( ! session_id() ) {
	@session_start();
}

if ( ! class_exists( 'WPHB_Sessions' ) ) {
	/**
	 * Class WPHB_Sessions
	 */
	class WPHB_Sessions {
		/**
		 * @var null
		 */
		static $_instance = null;

		/**
		 * @var array|mixed|null
		 */
		public $session = null;

		/**
		 * @var float|int|null
		 */
		private $live_item = null;

		/**
		 * @var bool
		 */
		private $remember = false;

		/**
		 * @var null|string
		 */
		public $prefix = null;

		/**
		 * WPHB_Sessions constructor.
		 *
		 * @param string $prefix
		 * @param bool   $remember
		 */
		public function __construct( $prefix = '', $remember = true ) {
			if ( ! $prefix ) {
				return;
			}

			$this->prefix   = $prefix;
			$this->remember = $remember;

			$this->live_item = 12 * HOUR_IN_SECONDS;

			// get all
			$this->session = $this->load();
		}

		/**
		 * @return array|mixed
		 */
		public function load() {
			if ( isset( $_SESSION[ $this->prefix ] ) ) {
				return $_SESSION[ $this->prefix ];
			} elseif ( $this->remember && isset( $_COOKIE[ $this->prefix ] ) ) {
				return $_SESSION[ $this->prefix ] = json_decode( WPHB_Helpers::sanitize_params_submitted( $_COOKIE[ $this->prefix ] ) );
			}

			return array();
		}

		/**
		 * @return null
		 */
		public function remove() {
			if ( isset( $_SESSION[ $this->prefix ] ) ) {
				unset( $_SESSION[ $this->prefix ] );
			}

			if ( $this->remember && isset( $_COOKIE[ $this->prefix ] ) ) {
				unset( $_COOKIE[ $this->prefix ] );
				setcookie( $this->prefix, '', time() - $this->live_item, COOKIEPATH, COOKIE_DOMAIN );
			}

			return $this->session = null;
		}

		/**
		 * @param null $name
		 * @param null $value
		 */
		public function set( $name = null, $value = null ) {
			if ( ! $name ) {
				return;
			}
			$time = time();
			if ( ! $value ) {
				if ( is_array( $this->session ) ) {
					unset( $this->session[ $name ] );
				} else {
					unset( $this->session->{$name} );
				}
				$time = $time - $this->live_item;
			} else {
				if ( is_array( $this->session ) ) {
					$this->session[ $name ] = $value;
				} else {
					$this->session->{$name} = $value;
				}

				$time = $time + $this->live_item;
			}

			// save session
			$_SESSION[ $this->prefix ] = WPHB_Helpers::sanitize_params_submitted( $this->session );

			// save cookie
			if ( $this->remember ) {
				@setcookie( $this->prefix, wp_json_encode( $this->session ), $time, COOKIEPATH, COOKIE_DOMAIN );
			}
		}

		/**
		 * @param null $name
		 * @param null $default
		 *
		 * @return mixed|null
		 */
		public function get( $name = null, $default = null ) {
			if ( ! $name ) {
				return $default;
			}

			if ( isset( $this->session[ $name ] ) ) {
				return $this->session[ $name ];
			}

			return $default;
		}

		/**
		 * @param string $prefix
		 *
		 * @return WPHB_Sessions
		 */
		public static function instance( $prefix = '' ) {
			if ( ! empty( self::$_instance[ $prefix ] ) ) {
				return self::$_instance[ $prefix ];
			}

			return self::$_instance[ $prefix ] = new self( $prefix );
		}
	}
}
