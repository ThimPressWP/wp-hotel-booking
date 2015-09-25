<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class HB_TemplateLoader {

    /**
     * Path to the includes directory
     * @var string
     */
    private $include_path = '';

    /**
     * The Constructor
     */
    public function __construct() {
        add_filter('template_include', array($this, 'template_loader'));
    }

    public function template_loader($template)
    {
        $post_type = get_post_type();

        $tpl_name = '';
        if( $post_type !== 'hb_room' )
            return $template;

        if( is_post_type_archive( 'hb_room' ) )
        {
            $tpl_name = 'archive-room.php';
            $template = hb_template_path() . '/' . $tpl_name;
        }
        else if( is_single() )
        {
            $tpl_name = 'single-room.php';
            $template = hb_template_path() . '/' . $tpl_name;
        }

        $hb_template = untrailingslashit(HB_PLUGIN_PATH) . '/templates/' . $tpl_name;
        $template = locate_template( array( $tpl_name, $template ) );
        if( ! $template && file_exists( $hb_template ) )
        {
            $template = $hb_template;
        }

        if ( '' != $template ) {
            return $template ;
        }
        return $template;
    }
}

new HB_TemplateLoader();
