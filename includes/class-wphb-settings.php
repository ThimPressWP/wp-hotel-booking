<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

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
	protected $_resizeImage = array();

	/**
	 * Construction
	 *
	 * @param string
	 * @param array
	 */
	function __construct( $new_prefix = null, $default = array() ) {
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
	function get( $name, $default = false ) {
		if ( strpos( $name, 'tp_hotel_booking_' ) === 0 ) {
			$name = str_replace( 'tp_hotel_booking_', '', $name );
		}
		if ( !empty( $this->_options[$name] ) ) {
			return $this->_options[$name];
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
	function set( $name, $value ) {
		// update option
		update_option( $this->_option_prefix . $name, $value );
		$this->_options[$name] = $value;

		// allow hook
		do_action( 'hb_update_settings_' . $name, $name, $value );
		return $this->_options;
	}

	/**
	 * Remove an option
	 *
	 * @param string
	 *
	 * @return array
	 */
	function remove( $name ) {
		if ( array_key_exists( $name, $this->_options ) ) {
			unset( $this->_options[$name] );
		}
		return $this->_options;
	}

	/**
	 * Update all options into database
	 */
	function update() {
		if ( $this->_options )
			foreach ( $this->_options as $k => $v ) {
				update_option( $this->_option_prefix . $k, $v );
			}
	}

	/**
	 * Get the name of field
	 *
	 * @param string
	 *
	 * @return string
	 */
	function get_field_name( $name ) {
		return $this->_option_prefix . $name;
	}

	/**
	 * Get the id of field
	 *
	 * @param string
	 *
	 * @return string
	 */
	function get_field_id( $name ) {
		return sanitize_title( $this->get_field_name( $name ) );
	}

	/**
	 * Update settings
	 */
	function update_settings() {
		if ( strtolower( $_SERVER['REQUEST_METHOD'] ) != 'post' )
			return;
		foreach ( $_POST as $k => $v ) {
			if ( preg_match( '!^' . $this->_option_prefix . '!', $k ) ) {
				$option_key = preg_replace( '!^' . $this->_option_prefix . '!', '', $k );
				if ( !$option_key )
					continue;
				if ( is_string( $v ) ) {
					$_POST[$k] = sanitize_text_field( $v );
				}
				$this->set( $option_key, $_POST[$k] );
			}
		}
		$this->update();
	}

	/**
	 * Load all options
	 * @return array
	 */
	private function _load_options() {
		global $wpdb;
		$query = $wpdb->prepare( "
                SELECT option_name, option_value
                FROM {$wpdb->options}
                WHERE option_name LIKE %s
            ", $this->_option_prefix . '%'
		);
		if ( $options = $wpdb->get_results( $query ) ) {
			foreach ( $options as $option ) {
				$name                  = str_replace( $this->_option_prefix, '', $option->option_name );
				$this->_options[$name] = maybe_unserialize( $option->option_value );
			}
		}
		return $this->_options;
	}

	/**
	 * Magic function to convert object to string with json format
	 *
	 * @return string
	 */
	function __toString() {
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
	function toJson( $fields = array() ) {
		if ( $fields ) {
			$options = array();
			foreach ( $fields as $k => $v ) {
				$options[$v] = $this->get( $v );
			}
			$return = json_encode( $options );
		} else {
			$return = json_encode( $this->_options );
		}
		return $return;
	}

	function get_prefix() {
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
	static function instance( $prefix = null, $default = array() ) {
		if ( !$prefix || !is_string( $prefix ) ) {
			$prefix = 'tp_hotel_booking_';
		}
		if ( empty( self::$_instances[$prefix] ) ) {
			self::$_instances[$prefix] = new self( $prefix, $default );
		}
		return self::$_instances[$prefix];
	}

}

$GLOBALS['hb_settings'] = WPHB_Settings::instance();

if ( !function_exists( 'hb_settings' ) ) {
	function hb_settings() {
		return WPHB_Settings::instance();
	}
}