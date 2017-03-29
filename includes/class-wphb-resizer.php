<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class WPHB_Settings
 */
class WPHB_Reizer {

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

    function __construct( $args = null ) {
        self::$args = $args;

        if ( !self::$_attachments )
            self::getAttachments();
    }

    static function getInstance( $args = null ) {
        if ( !self::$_instance )
            self::$_instance = new self( $args );

        return self::$_instance;
    }

    public static function getAttachments() {
        global $wpdb;
        $posts = $wpdb->get_results( "SELECT * FROM `{$wpdb->posts}` WHERE `post_type` = 'attachment' AND `guid` != ''" );

        foreach ( $posts as $key => $post ) {
            self::$_attachments[] = $post->guid;
        }

        return self::$_attachments;
    }

    public static function process( $attachmentID = null, $size = array(), $single = false, $upscale = true ) {
        $aq_resize = Aq_Resize::getInstance();
        global $hb_settings;
        if ( !$attachmentID )
            return false;

        if ( !isset( $size['width'] ) || !isset( $size['height'] ) )
            return false;

        // generator image file with size setting in frontend
        $attachment = wp_get_attachment_url( $attachmentID );
        return $aq_resize->process( $attachment, (int) $size['width'], (int) $size['height'], true, $single, $upscale );
    }

}
