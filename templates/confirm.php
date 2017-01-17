<?php
/**
 * Confirm plugin actions
 *
 * Override this template by copying it to yourtheme/tp-hotel-booking/confirm.php
 *
 * @author        ThimPress
 * @package       tp-hotel-booking/templates
 * @version       1.6
 */

if ( !defined( 'ABSPATH' ) ) {
	exit();
}
?>
<div id="hotel-booking-confirm">
    Confirm
    <form name="hb-search-form">
        <input type="hidden" name="hotel-booking" value="complete">
        <p>
            <button type="submit"><?php _e( 'Finish', 'tp-hotel-booking' ); ?></button>
        </p>
    </form>
</div>