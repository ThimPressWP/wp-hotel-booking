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
	// Skip session for REST API requests - major performance improvement
	$skip_session = defined( 'REST_REQUEST' ) && REST_REQUEST;

	// Skip for CLI/WP-CLI requests
	$skip_session = $skip_session || php_sapi_name() === 'cli' || defined( 'WP_CLI' );

	// Skip for cron requests
	$skip_session = $skip_session || ( defined( 'DOING_CRON' ) && DOING_CRON );

	if ( ! $skip_session ) {
		@session_start( array( 'read_and_close' => true ) );
	}
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
				return $_SESSION[ $this->prefix ] = json_decode( WPHB_Helpers::sanitize_params_submitted( $_COOKIE[ $this->prefix ] ), true );
			} else {
				// Only try transient if the session ID exists (sessions may be skipped for REST/CLI)
				$session_id = session_id();
				if ( ! empty( $session_id ) ) {
					$transient_prefix = $this->prefix . '_' . $session_id;
					$transient        = get_transient( $transient_prefix );
					if ( ! empty( $transient ) && is_array( $transient ) ) {
						return $transient;
					}
				}
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
				setcookie( $this->prefix, '', time() - $this->live_item, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
			}

			// Only delete transient if session ID exists
			$session_id = session_id();
			if ( ! empty( $session_id ) ) {
				$transient_prefix = $this->prefix . '_' . $session_id;
				if ( get_transient( $transient_prefix ) ) {
					delete_transient( $transient_prefix );
				}
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
			if ( ! $value ) {
				if ( is_array( $this->session ) ) {
					unset( $this->session[ $name ] );
				} else {
					unset( $this->session->{$name} );
				}
			} elseif ( is_array( $this->session ) ) {
					$this->session[ $name ] = $value;
			} else {
				$this->session->{$name} = $value;
			}
			$time = empty( $this->session ) ? time() - $this->live_item : time() + $this->live_item;

			// save session
			$_SESSION[ $this->prefix ] = WPHB_Helpers::sanitize_params_submitted( $this->session );

			// save cookie
			if ( $this->remember ) {
				// set transient for special case when cookie and session was removed after adding
				// Only set transient if session ID exists (sessions may be skipped for REST/CLI)
				$session_id = session_id();
				if ( ! empty( $session_id ) ) {
					$transient_prefix = $this->prefix . '_' . $session_id;
					set_transient( $transient_prefix, $this->session, $this->live_item );
				}
				@setcookie( $this->prefix, wp_json_encode( $this->session ), $time, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
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
