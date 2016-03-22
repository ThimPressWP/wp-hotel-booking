<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class HB_Autoloader {

    /**
     * Path to the includes directory
     * @var string
     */
    private $include_path = '';

    /**
     * The Constructor
     */
    public function __construct() {
        if ( function_exists( "__autoload" ) ) {
            spl_autoload_register( "__autoload" );
        }

        spl_autoload_register( array( $this, 'autoload' ) );

        $this->include_path = untrailingslashit( HB_PLUGIN_PATH ) . '/includes/';
    }

    /**
     * Take a class name and turn it into a file name
     * @param  string $class
     * @return string
     */
    private function get_file_name_from_class( $class ) {
        return 'class-' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
    }

    /**
     * Include a class file
     * @param  string $path
     * @return bool successful or not
     */
    private function load_file( $path ) {
        if ( $path && is_readable( $path ) ) {
            include_once( $path );
            return true;
        }
        return false;
    }

    /**
     * Auto-load WC classes on demand to reduce memory consumption.
     *
     * @param string $class
     */
    public function autoload( $class ) {
        $class = strtolower( $class );
        $file  = $this->get_file_name_from_class( $class );
        $path  = $path = $this->include_path;

        // payment gateways
        if ( strpos( $class, 'hb_payment_gateway_' ) === 0 ) {
            $path = $this->include_path . 'payment-gateways/';// . substr( str_replace( '_', '-', $class ), 26 );
        }
        if ( strpos( $class, 'hb_admin_metabox_' ) === 0 ) {
            $path = $this->include_path . 'admin/metaboxes/';// . substr( str_replace( '_', '-', $class ), 26 );
        }
                //if ( empty( $path ) || ( ! $this->load_file( $path . $file ) && strpos( $class, 'lpr_' ) === 0 ) ) {
        $this->load_file( $path . $file );
        //}
    }
}

new HB_Autoloader();