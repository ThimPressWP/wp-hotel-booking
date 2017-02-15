<?php
/**
 * @Author: ducnvtt
 * @Date:   2016-03-25 16:12:28
 * @Last Modified by:   ducnvtt
 * @Last Modified time: 2016-03-25 16:28:24
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

global $post;

?>

<div class="submitbox">
	<div id="delete-action">
		<?php if ( current_user_can( 'delete_post', $post->ID ) ) : ?>
			<a class="submitdelete deletion" href="<?php echo esc_attr( get_delete_post_link( $post->ID ) ) ?>"><?php _e( 'Move to Trash', 'wp-hotel-booking' ); ?></a>
		<?php endif; ?>
	</div>
	<div id="publishing-action">
		<button name="save" type="submit" class="button button-primary" id="publish">
			<?php printf( '%s', $post->post_status !== 'auto-draft' ? __( 'Update', 'wp-hotel-booking' ) : __( 'Save Book', 'wp-hotel-booking' ) ) ?>
		</button>
	</div>
</div>
