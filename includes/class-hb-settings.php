<?php

/**
 * Class HB_Settings
 */
class HB_Settings{
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
     * Construction
     *
     * @param string
     * @param array
     */
    function __construct( $new_prefix = null, $default = array() ){
        add_action( 'init', array( $this, 'update_settings' ) );
        if( $new_prefix ){
            $this->_option_prefix = $new_prefix;
        }

        if( is_object( $default ) ){
            $default = (array)$default;
        }

        if( is_array( $default ) ){
            foreach( $default as $k => $value ){
                add_option( $this->_option_prefix . $k, $value );
            }
        }

        $this->_load_options();
    }

    /**
     * Get an option
     *
     * @param string
     * @return mixed
     */
    function get( $name ){
        if( ! empty( $this->_options[ $name ] ) ){
            return $this->_options[ $name ];
        }
        return false;
    }

    /**
     * Update new value for an option
     *
     * @param string
     * @param mixed
     * @return array
     */
    function set( $name, $value ){
        $this->_options[ $name ] = $value;
        return $this->_options;
    }

    /**
     * Remove an option
     *
     * @param string
     * @return array
     */
    function remove( $name ){
        if( array_key_exists( $name, $this->_options ) ){
            unset( $this->_options[ $name ] );
        }
        return $this->_options;
    }

    /**
     * Update all options into database
     */
    function update(){
        if( $this->_options ) foreach( $this->_options as $k => $v ){
            update_option( $this->_option_prefix . $k, $v );
        }
    }

    /**
     * Get the name of field
     *
     * @param string
     * @return string
     */
    function get_field_name( $name ){
        return $this->_option_prefix . $name;
    }

    /**
     * Get the id of field
     *
     * @param string
     * @return string
     */
    function get_field_id( $name ){
        return sanitize_title( $this->get_field_name( $name ) );
    }

    function update_settings(){
        if( strtolower( $_SERVER['REQUEST_METHOD']) != 'post' ) return;
        foreach( $_POST as $k => $v ){
            if( preg_match( '!^' . $this->_option_prefix . '!', $k ) ) {
                $option_key = preg_replace( '!^' . $this->_option_prefix . '!', '', $k );
                if( ! $option_key ) continue;
                $this->set( $option_key, $_POST[ $k ]);
            }

        }
        $this->update();
    }

    /**
     * Load all options
     * @return array
     */
    private function _load_options(){
        global $wpdb;
        $query = $wpdb->prepare("
                SELECT option_name, option_value
                FROM {$wpdb->options}
                WHERE option_name LIKE %s
            ",
            $this->_option_prefix . '%'
        );
        if( $options = $wpdb->get_results( $query) ){
            foreach( $options as $option ){
                $name = str_replace( $this->_option_prefix, '', $option->option_name );
                $this->_options[ $name ] = maybe_unserialize( $option->option_value );
            }
        }
        return $this->_options;
    }

    /**
     * Get unique instance of HB_Settings
     * Create a new one if it is not created
     *
     * @param string
     * @param array
     * @return HB_Settings instance
     */
    static function instance( $prefix = null, $default = array() ){
        if( ! $prefix || ! is_string( $prefix ) ){
            $prefix = 'tp_hotel_booking_';
        }
        if( empty( self::$_instances[ $prefix ] ) ){
            self::$_instances[ $prefix ] = new self( $prefix, $default );
        }
        return self::$_instances[ $prefix ];
    }
}
$GLOBALS['hb_settings'] = HB_Settings::instance(
    '',
    array(
        'overwrite_templates'           => 'on',
        'currency'                      => 'USD',
        'price_currency_position'       => 'before',
        'price_thousands_separator'     => ',',
        'price_decimals_separator'      => '.',
        'price_number_of_decimal'       => '2',
        'price_display'         => 'min',
        'hotel_name'            => 'Hanoi Daewoo Hotel',
        'hotel_address'         => 'Số 360, Phố Kim Mã, Quận Ba Đình, Quận Ba Đình, Hà Nội, Việt Nam',
        'hotel_city'            => 'Hà Nội',
        'hotel_state'           => '',
        'hotel_country'         => 'Việt Nam',
        'hotel_zip_code'        => '1000',
        'hotel_phone_number'    => '0123.456.789',
        'hotel_fax_number'      => '',
        'hotel_email_address'   => 'daewoo_hotel@gmail.com',
        'tax'                   => 0,
        'price_including_tax'   => 0
    )
);

function hb_settings(){
    return HB_Settings::instance();
}
