<?php

if( ! function_exists( 'hb_sw_get_template' ) )
{

	/**
	 * Get other templates passing attributes and including the file.
	 *
	 * @param string $template_name
	 * @param array  $args          (default: array())
	 * @param string $template_path (default: '')
	 * @param string $default_path  (default: '')
	 *
	 * @return void
	 */
	function hb_sw_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	    if ( $args && is_array( $args ) ) {
	        extract( $args );
	    }

	    $located = hb_sw_locate_template( $template_name, $template_path, $default_path );

	    if ( !file_exists( $located ) ) {
	        _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
	        return;
	    }
	    // Allow 3rd party plugin filter template file from their plugin
	    $located = apply_filters( 'hb_sw_get_template', $located, $template_name, $args, $template_path, $default_path );

	    do_action( 'hb_sw_before_template_part', $template_name, $template_path, $located, $args );

	    include( $located );

	    do_action( 'hb_sw_after_template_part', $template_name, $template_path, $located, $args );
	}

	function hb_sw_template_path(){
	    return apply_filters( 'hb_sw_template_path', 'tp-hb-currencies' );
	}
	/**
	 * get template part
	 *
	 * @param   string $slug
	 * @param   string $name
	 *
	 * @return  string
	 */
	function hb_sw_get_template_part( $slug, $name = '' ) {
	    $template = '';

	    // Look in yourtheme/slug-name.php and yourtheme/courses-manage/slug-name.php
	    if ( $name ) {
	        $template = locate_template( array( "{$slug}-{$name}.php", hb_sw_template_path() . "/{$slug}-{$name}.php" ) );
	    }

	    // Get default slug-name.php
	    if ( !$template && $name && file_exists( TP_HB_CURRENCY . "/templates/{$slug}-{$name}.php" ) ) {
	        $template = TP_HB_CURRENCY . "/templates/{$slug}-{$name}.php";
	    }

	    // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/courses-manage/slug.php
	    if ( !$template ) {
	        $template = locate_template( array( "{$slug}.php", hb_sw_template_path() . "{$slug}.php" ) );
	    }

	    // Allow 3rd party plugin filter template file from their plugin
	    if ( $template ) {
	        $template = apply_filters( 'hb_sw_get_template_part', $template, $slug, $name );
	    }
	    if ( $template && file_exists( $template ) ) {
	        load_template( $template, false );
	    }

	    return $template;
	}

	/**
	 * Locate a template and return the path for inclusion.
	 *
	 * This is the load order:
	 *
	 *        yourtheme        /    $template_path    /    $template_name
	 *        yourtheme        /    $template_name
	 *        $default_path    /    $template_name
	 *
	 * @access public
	 *
	 * @param string $template_name
	 * @param string $template_path (default: '')
	 * @param string $default_path  (default: '')
	 *
	 * @return string
	 */
	function hb_sw_locate_template( $template_name, $template_path = '', $default_path = '' ) {

	    if ( !$template_path ) {
	        $template_path = hb_sw_template_path();
	    }

	    if ( !$default_path ) {
	        $default_path = TP_HB_CURRENCY . '/templates/';
	    }

	    $template = null;
	    // Look within passed path within the theme - this is priority
	    $template = locate_template(
            array(
                trailingslashit($template_path) . $template_name,
                $template_name
            )
        );
	    // Get default template
	    if ( !$template ) {
	        $template = $default_path . $template_name;
	    }

	    // Return what we found
	    return apply_filters( 'hb_sw_locate_template', $template, $template_name, $template_path );
	}
}
