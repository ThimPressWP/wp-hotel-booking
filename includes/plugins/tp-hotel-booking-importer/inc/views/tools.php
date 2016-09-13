<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-25 13:18:04
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-26 08:31:29
 */
if ( !defined( 'ABSPATH' ) ) {
    exit();
}

$tooltabs = apply_filters( 'hotelbooking_importer_tabs', array() );
$selected_tab = isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : current( array_keys( $tooltabs ) );
?>

<div class="wrap">
    <h2 class="nav-tab-wrapper">
<?php if ( $tooltabs ) :
    foreach ( $tooltabs as $slug => $title ) {
        ?>
                <a class="nav-tab<?php echo sprintf( '%s', $selected_tab == $slug ? ' nav-tab-active' : ''  ); ?>" href="?page=tp-hotel-tools&tab=<?php echo esc_attr( $slug ); ?>">
                    <?php echo esc_html( $title ); ?>
                </a>
            <?php } endif; ?>
    </h2>
    <?php do_action( 'hb_tooladmin_settings_sections_' . $selected_tab ); ?>
</div>
