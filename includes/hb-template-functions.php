<?php
function hb_template_path(){
    return apply_filters( 'hb_template_path', 'tp-hotel-booking' );
}
/**
 * get template part
 *
 * @param   string $slug
 * @param   string $name
 *
 * @return  string
 */
function hb_get_template_part( $slug, $name = '' ) {
    $template = '';

    // Look in yourtheme/slug-name.php and yourtheme/courses-manage/slug-name.php
    if ( $name ) {
        $template = locate_template( array( "{$slug}-{$name}.php", hb_template_path() . "/{$slug}-{$name}.php" ) );
    }

    // Get default slug-name.php
    if ( !$template && $name && file_exists( HB_PLUGIN_PATH . "/templates/{$slug}-{$name}.php" ) ) {
        $template = HB_PLUGIN_PATH . "/templates/{$slug}-{$name}.php";
    }

    // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/courses-manage/slug.php
    if ( !$template ) {
        $template = locate_template( array( "{$slug}.php", hb_template_path() . "{$slug}.php" ) );
    }

    // Allow 3rd party plugin filter template file from their plugin
    if ( $template ) {
        $template = apply_filters( 'hb_get_template_part', $template, $slug, $name );
    }
    if ( $template && file_exists( $template ) ) {
        load_template( $template, false );
    }

    return $template;
}

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
function hb_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
    if ( $args && is_array( $args ) ) {
        extract( $args );
    }

    $located = hb_locate_template( $template_name, $template_path, $default_path );

    if ( !file_exists( $located ) ) {
        _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
        return;
    }
    // Allow 3rd party plugin filter template file from their plugin
    $located = apply_filters( 'hb_get_template', $located, $template_name, $args, $template_path, $default_path );

    do_action( 'hb_before_template_part', $template_name, $template_path, $located, $args );

    include( $located );

    do_action( 'hb_after_template_part', $template_name, $template_path, $located, $args );
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
function hb_locate_template( $template_name, $template_path = '', $default_path = '' ) {

    if ( !$template_path ) {
        $template_path = hb_template_path();
    }

    if ( !$default_path ) {
        $default_path = HB_PLUGIN_PATH . '/templates/';
    }

    $template = null;
    // Look within passed path within the theme - this is priority
    if( hb_enable_overwrite_template() ) {
        $template = locate_template(
            array(
                trailingslashit($template_path) . $template_name,
                $template_name
            )
        );
    }
    // Get default template
    if ( !$template ) {
        $template = $default_path . $template_name;
    }

    // Return what we found
    return apply_filters( 'hb_locate_template', $template, $template_name, $template_path );
}

function hb_get_template_content( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
    ob_start();
    hb_get_template( $template_name, $args, $template_path, $default_path );
    return ob_get_clean();
}

function hb_enqueue_lightbox_assets(){
    $settings = HB_Settings::instance();
    $lightbox_settings = $settings->get('lightbox');
    if( ! $lightbox_settings ) return;
    if( empty( $lightbox_settings['lightbox'] ) ) return;
    do_action( 'hb_lightbox_assets_' . $lightbox_settings['lightbox'] );
}

function hb_lightbox_assets_lightbox2(){
    wp_enqueue_script( 'lightbox2', TP_Hotel_Booking::instance()->plugin_url( 'includes/lightbox/lightbox2/src/js/lightbox.js' ) );
    wp_enqueue_style( 'lightbox2', TP_Hotel_Booking::instance()->plugin_url( 'includes/lightbox/lightbox2/src/css/lightbox.css' ) );
    ?>
    <script type="text/javascript">
    jQuery(function(){

    });
    </script>
    <?php
}

function hb_lightbox_assets_fancyBox(){
    wp_enqueue_script( 'fancyBox', TP_Hotel_Booking::instance()->plugin_url( 'includes/lightbox/fancyBox/source/jquery.fancybox.js' ) );
    wp_enqueue_style( 'fancyBox', TP_Hotel_Booking::instance()->plugin_url( 'includes/lightbox/fancyBox/source/jquery.fancybox.css' ) );
    ?>
    <script type="text/javascript">
        jQuery(function($){
            $(".hb-room-gallery").fancybox();
        });
    </script>
<?php
}

if( ! function_exists( 'hb_display_message' ) ){
    function hb_display_message(){
        hb_get_template( 'global/message.php' );
    }
}

/*=====================================================
=            single-room.php template hooks            =
=====================================================*/
if( ! function_exists( 'hotel_booking_before_main_content' ) )
{
    function hotel_booking_before_main_content()
    {

    }
}

if( ! function_exists( 'hotel_booking_after_main_content' ) )
{
    // others room block
    function hotel_booking_after_main_content()
    {

    }
}

if( ! function_exists( 'hotel_booking_sidebar' ) )
{
    function hotel_booking_sidebar()
    {

    }
}

/*=====  End of single-room.php template hooks  ======*/
