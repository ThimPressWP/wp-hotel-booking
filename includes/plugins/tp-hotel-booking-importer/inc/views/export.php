<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-04-25 13:36:54
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-04-26 15:48:36
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>

<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=tp-hotel-tools&tab=export' ) ) ?>" enctype="multipart/form-data" name="hb-tool-export">

	<fieldset>
		<p>
			<input type="radio" name="export" value="all" id="all" checked/>
			<label for="all"><?php _e( 'All', 'tp-hotel-booking-importer' ) ?></label>
		</p>
		<p class="description"><?php _e( 'This will contain all of your rooms, bookings, coupons, users, pricing plan, block special date, additonal packages.', 'tp-hotel-booking-importer' ); ?></p>
	</fieldset>

	<p>
		<?php wp_nonce_field( 'hbtool_export', 'hbtool_export' ); ?>
		<button type="submit" class="button button-primary"><?php _e( 'Download Export File', 'tp-hotel-booking-importer' ); ?></button>
	</p>

</form>
