<?php
/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

global $hb_room;
/**
 * @var $hb_room WPHB_Room
 */
?>

<?php
if($hb_room ->is_featured()){
    ?>
    <div class="feature">
		<?php esc_html_e( 'Featured', 'wp-hotel-booking' ); ?>
    </div>
    <?php
}
?>
