<?php

function tp_hb_extra_template_path(){
    return apply_filters( 'hb_extra_template_path', 'wp-hb-extra' );
}
/**
 * get template part
 *
 * @param   string $slug
 * @param   string $name
 *
 * @return  string
 */
function tp_hb_extra_get_template_part( $slug, $name = '' ) {
    $template = '';

    // Look in yourtheme/slug-name.php and yourtheme/courses-manage/slug-name.php
    if ( $name ) {
        $template = locate_template( array(
            "{$slug}-{$name}.php",
            tp_hb_extra_template_path() . "/{$slug}-{$name}.php",
            hb_template_path() . '/' . tp_hb_extra_template_path() . "/{$slug}-{$name}.php",
        ));
    }

    // Get default slug-name.php
    if ( ! $template && $name && file_exists( TP_HB_EXTRA . "/templates/{$slug}-{$name}.php" ) ) {
        $template = TP_HB_EXTRA . "/templates/{$slug}-{$name}.php";
    }

    // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/courses-manage/slug.php
    if ( ! $template ) {
        $template = locate_template( array(
            "{$slug}.php",
            tp_hb_extra_template_path() . "{$slug}.php",
            hb_template_path() . '/' . tp_hb_extra_template_path() . "{$slug}.php",
        ) );
    }

    // Allow 3rd party plugin filter template file from their plugin
    if ( $template ) {
        $template = apply_filters( 'hb_extra_get_template_part', $template, $slug, $name );
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
function tp_hb_extra_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
    if ( $args && is_array( $args ) ) {
        extract( $args );
    }

    $located = tp_hb_extra_locate_template( $template_name, $template_path, $default_path );

    if ( !file_exists( $located ) ) {
        _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
        return;
    }
    // Allow 3rd party plugin filter template file from their plugin
    $located = apply_filters( 'hb_extra_get_template', $located, $template_name, $args, $template_path, $default_path );

    do_action( 'hb_extra_before_template_part', $template_name, $template_path, $located, $args );

    include( $located );

    do_action( 'hb_extra_after_template_part', $template_name, $template_path, $located, $args );
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
function tp_hb_extra_locate_template( $template_name, $template_path = '', $default_path = '' ) {

    if ( ! $template_path ) {
        $template_path = tp_hb_extra_template_path();
    }

    if ( ! $default_path ) {
        $default_path = TP_HB_EXTRA . '/templates/';
    }

    $template = null;
    // Look within passed path within the theme - this is priority
    $template = locate_template(
        array(
            trailingslashit( $template_path ) . $template_name,
            trailingslashit( hb_template_path() . '/' . $template_path ) . $template_name,
            $template_name
        )
    );
    // Get default template
    if ( ! $template ) {
        $template = $default_path . $template_name;
    }

    // Return what we found
    return apply_filters( 'hb_extra_locate_template', $template, $template_name, $template_path );
}

function tp_hb_extra_get_template_content( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
    ob_start();
    tp_hb_extra_get_template( $template_name, $args, $template_path, $default_path );
    return ob_get_clean();
}

if( ! function_exists( 'tp_hb_extra_type' ) )
{

	function tp_hb_extra_type()
	{
		return apply_filters( 'hb_extra_type', array(
				'trip' 		=> __( 'Trip', 'wp-hotel-booking' ),
				'number'	=> __( 'Number', 'wp-hotel-booking' )
			)
		);
	}

}

if( ! function_exists( 'tp_hb_extra_select' ) )
{
	/**
	 * generate select field html
	 * @param  array $options
	 * @param  array $selected
	 * @return html
	 */
	function tp_hb_extra_select( $name = '', $options = array(), $selected = array(), $multitye = false )
	{
		?>
			<select name="<?php echo esc_attr( $name ); ?>"<?php echo sprintf( '%s', $multitye ? ' multiple' : '' ) ?>>
				<?php if( $options['options'] ): ?>
					<?php foreach ( $options['options'] as $key => $option ): ?>
						<?php if ( is_array( $option['value'] ) ): ?>
							<optgroup label="">
								<?php foreach ( $option['value'] as $key => $value ): ?>
									<option value="<?php printf( '%s', $value['value'] ) ?>" <?php selected( $selected, $value['value'], 1 ); ?>>
										<?php printf( '%s', $value['text'] ) ?>
									</option>
								<?php endforeach; ?>
							</optgroup>
						<?php else: ?>
							<option value="<?php printf( '%s', $option['value'] ) ?>" <?php selected( $selected, $option['value'], 1 ); ?>>
								<?php printf( '%s', $option['text'] ) ?>
							</option>
						<?php endif ?>
					<?php endforeach ?>
				<?php endif; ?>
			</select>
		<?php
	}

}

if( ! function_exists( 'is_hb_checkout' ) )
{

    function is_hb_checkout()
    {
        return ( is_page( hb_get_page_id( 'checkout' ) ) || hb_get_request( 'hotel-booking' ) === 'checkout' );
    }
}


if( ! function_exists( 'is_hb_cart' ) )
{

    function is_hb_cart()
    {
        return ( is_page( hb_get_page_id( 'cart' ) ) || hb_get_request( 'hotel-booking' ) === 'cart' );
    }
}