<?php
	if ( ! defined( 'ABSPATH' ) ) {
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