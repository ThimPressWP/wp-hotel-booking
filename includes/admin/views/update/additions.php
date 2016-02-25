<?php
/**
 * Template Customer
 * @since  1.1
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<?php if( $addition_information = get_post_field( 'post_content', $booking->id ) ) : ?>
	<table class="hb-booking-table hb-table-width70">
	    <thead>
		    <tr>
		        <th colspan="2">
		            <h3><?php _e( 'Addition Information', 'tp-hotel-booking') ?></h3>
		        </th>
		    </tr>
	    </thead>
	    <tbody>
	    <tr>
	        <td colspan="2">
	            <?php echo sprintf( '%s', $addition_information ); ?>
	        </td>
	    </tr>
	    </tbody>
	</table>
<?php endif; ?>
