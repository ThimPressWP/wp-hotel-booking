<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

class WPHB_Autoloader {

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

        $this->include_path = untrailingslashit( WPHB_PLUGIN_PATH ) . '/includes/';
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
     * Auto-load HB classes on demand to reduce memory consumption.
     *
     * @param string $class
     */
    public function autoload( $class ) {
        $class = strtolower( $class );
        $file = $this->get_file_name_from_class( $class );
        $path = $this->include_path;

        // payment gateways
        if ( strpos( $class, 'wphb_payment_gateway_' ) === 0 ) {
            $path = $this->include_path . 'gateways/' . substr( str_replace( '_', '-', $class ), 21 ) . '/';
        }

        // widgets
        if ( stripos( $class, 'hb_widget_' ) === 0 ) {
            $path = $this->include_path . '/widgets/';
        }

        // admin metaboxs
        if ( strpos( $class, 'wphb_admin_metabox_' ) === 0 ) {
            $path = $this->include_path . 'admin/metaboxes/';
        }

        $this->load_file( $path . $file );
    }

}

new WPHB_Autoloader();
