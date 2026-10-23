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
		 * Retrieve authentication salt safely, even if called before pluggable.php is loaded.
		 *
		 * @return string
		 */
		protected function get_salt() {
			if ( function_exists( 'wp_salt' ) ) {
				return wp_salt( 'auth' );
			}

			return ( defined( 'AUTH_KEY' ) ? AUTH_KEY : '' ) . ( defined( 'AUTH_SALT' ) ? AUTH_SALT : '' );
		}

		/**
		 * Generate cryptographic HMAC-SHA256 signature for session payload.
		 *
		 * @param string $payload JSON encoded payload string.
		 * @return string Hex-encoded HMAC signature.
		 */
		protected function generate_signature( $payload ) {
			return hash_hmac( 'sha256', $this->prefix . '|' . $payload, $this->get_salt() );
		}

		/**
		 * Verify HMAC signature of session payload using timing-safe comparison.
		 *
		 * @param string $payload
		 * @param string $signature
		 * @return bool
		 */
		protected function verify_signature( $payload, $signature ) {
			if ( empty( $payload ) || empty( $signature ) || ! is_string( $signature ) ) {
				return false;
			}

			return hash_equals( $this->generate_signature( $payload ), $signature );
		}

		/**
		 * @return array|mixed
		 */
		public function load() {
			if ( isset( $_SESSION[ $this->prefix ] ) && is_array( $_SESSION[ $this->prefix ] ) ) {
				return $_SESSION[ $this->prefix ];
			} elseif ( $this->remember && isset( $_COOKIE[ $this->prefix ] ) ) {
				$raw_cookie = wp_unslash( $_COOKIE[ $this->prefix ] );
				if ( is_string( $raw_cookie ) && '' !== $raw_cookie ) {
					// Check for signed cookie format: <base64_payload>.<hmac_signature>
					if ( strpos( $raw_cookie, '.' ) !== false ) {
						list( $encoded_payload, $signature ) = explode( '.', $raw_cookie, 2 );
						$payload = base64_decode( $encoded_payload, true );

						if ( false !== $payload && $this->verify_signature( $payload, $signature ) ) {
							$data = json_decode( $payload, true );
							if ( is_array( $data ) ) {
								return $_SESSION[ $this->prefix ] = $data;
							}
						}

						// Signature verification failed (cookie was tampered) - reject and purge
						$this->remove();
						return array();
					}

					// Backwards compatibility: gracefully load legacy unsigned JSON cookie
					$legacy_data = json_decode( WPHB_Helpers::sanitize_params_submitted( $raw_cookie ), true );
					if ( is_array( $legacy_data ) ) {
						// Upgrade legacy cookie to signed format immediately if headers permit
						if ( ! headers_sent() ) {
							$payload      = wp_json_encode( $legacy_data );
							$signature    = $this->generate_signature( $payload );
							$signed_value = base64_encode( $payload ) . '.' . $signature;
							@setcookie( $this->prefix, $signed_value, time() + $this->live_item, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
							$_COOKIE[ $this->prefix ] = $signed_value;
						}

						return $_SESSION[ $this->prefix ] = $legacy_data;
					}
				}
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
			if ( ! is_array( $this->session ) && ! is_object( $this->session ) ) {
				$this->session = array();
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
					if ( empty( $this->session ) ) {
						delete_transient( $transient_prefix );
					} else {
						set_transient( $transient_prefix, $this->session, $this->live_item );
					}
				}

				if ( empty( $this->session ) ) {
					unset( $_COOKIE[ $this->prefix ] );
					@setcookie( $this->prefix, '', $time, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
				} else {
					$payload      = wp_json_encode( $this->session );
					$signature    = $this->generate_signature( $payload );
					$signed_value = base64_encode( $payload ) . '.' . $signature;

					@setcookie( $this->prefix, $signed_value, $time, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
					$_COOKIE[ $this->prefix ] = $signed_value;
				}
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

			if ( is_array( $this->session ) && isset( $this->session[ $name ] ) ) {
				return $this->session[ $name ];
			}

			if ( is_object( $this->session ) && isset( $this->session->{$name} ) ) {
				return $this->session->{$name};
			}

			return $default;
		}

		/**
		 * Clear all WP Hotel Booking sessions on user logout to prevent session leaks across shared devices.
		 */
		public static function destroy_all_sessions() {
			// 1. Delegate to active Cart instance to wipe sessions, cookies, transients and runtime memory
			if ( function_exists( 'hb_get_cart' ) ) {
				$cart = hb_get_cart();
				if ( $cart && method_exists( $cart, 'reset_all_sessions' ) ) {
					$cart->reset_all_sessions();
				}
			}

			// 2. Wipe any cached session instances in memory
			if ( ! empty( self::$_instance ) && is_array( self::$_instance ) ) {
				foreach ( self::$_instance as $session ) {
					if ( $session instanceof self ) {
						$session->remove();
					}
				}
				self::$_instance = array();
			}
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

	add_action( 'wp_logout', array( 'WPHB_Sessions', 'destroy_all_sessions' ) );
}

