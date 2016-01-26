<?php

/**
 * Class HB_Shortcodes
 */
abstract class HB_Shortcodes{

    // shortcode name
    protected $shortcode = null;

    function __construct()
    {
        add_shortcode( $this->shortcode, array( $this, 'add_shortcode' ) );
    }

    function add_shortcode( $atts, $content = null ){}

}
