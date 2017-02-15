<?php
/**
 * Room Loop Start
 *
 * @author        ThimPress
 * @package       wp-hotel-booking/templates
 * @version       1.1.4
 */

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

global $hb_settings;

?>

<ul class="rooms tp-hotel-booking hb-catalog-column-<?php echo esc_attr( $hb_settings->get( 'catalog_number_column', 4 ) ) ?>">