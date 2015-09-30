<?php

/**
 * Class HB_Settings
 */
class HB_Reizer{

    /**
    * $_upload_base_dir is base path upload folder. Ex: wp-content/uploads
    * @return string
    */
    protected $_upload_base_dir = null;

    /**
    * $_upload_base_url is base path upload folder. Ex: localhost/wp/wp-content/uploads
    * @return string
    */
    protected $_upload_base_url = null;

    /**
    * $_instance variable
    * @return self
    */
    static $_instance = null;

    /**
    * list attachment image in the room. gallery, thumbnail
    * @return array
    */
    public static $_attachments = null;

    public static $args = null;

    function __construct( $args = null )
    {
        if( ! $args )
            return;

        self::$args = $args;

        if( ! self::$_attachments )
            self::getAttachments();

    }

    static function getInstance( $args = null )
    {
        if( ! self::$_instance )
            self::$_instance = new self( $args );

        return self::$_instance;
    }

    public static function getAttachments()
    {
        global $wpdb;
        $posts = $wpdb->get_results( "SELECT * FROM `{$wpdb->posts}` WHERE `post_type` = 'attachment' AND `guid` != ''" );

        foreach ($posts as $key => $post) {
            self::$_attachments[] = $post->guid;
        }

        return self::$_attachments;
    }

    public static function process( $post_id = null, $size = array() )
    {
        if( ! self::$args )
            return;

        $aq_resize = Aq_Resize::getInstance();
        if( $post_id && $size )
        {
            $attachment = wp_get_attachment_url( get_post_thumbnail_id($post_id) );
            $return = $aq_resize->process( $attachment, $size['width'], $size['height'], true, false );
            return $return;
        }
        else
        {
            foreach ( self::$_attachments as $key => $attachment ) {
                foreach (self::$args as $key => $arg) {
                    $aq_resize->process( $attachment, $arg['width'], $arg['height'] );
                }
            }
            return true;
        }
        return true;
    }

}
