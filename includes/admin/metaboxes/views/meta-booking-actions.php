<?php
/**
 * Admin View: Meta booking actions.
 *
 * @version     1.9.7
 * @package     WP_Hotel_Booking/Views
 * @category    Views
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

global $post;

?>

<div class="submitbox">
	<div id="delete-action">
		<?php if ( current_user_can( 'delete_post', $post->ID ) ) : ?>
			<a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link( $post->ID ) ); ?>"><?php _e( 'Move to Trash', 'wp-hotel-booking' ); ?></a>
		<?php endif; ?>
	</div>
	<div id="publishing-action">
		<button name="save" type="submit" class="button button-primary" id="publish">
			<?php echo esc_html( sprintf( '%s', $post->post_status !== 'auto-draft' ? __( 'Update', 'wp-hotel-booking' ) : __( 'Save Book', 'wp-hotel-booking' ) ) ); ?>
		</button>
	</div>
</div>
