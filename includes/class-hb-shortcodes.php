<?php
class HB_Shortcodes{
    static function init(){
        add_shortcode( 'hotel_booking', array( __CLASS__, 'hotel_booking' ) );
    }

    static function hotel_booking( $atts ){
        return hb_get_template_content( 'search.php', $atts );
    }
}

HB_Shortcodes::init();