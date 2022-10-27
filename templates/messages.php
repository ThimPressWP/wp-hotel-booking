<?php
/**
 * The template for displaying message for user.
 *
 * This template can be overridden by copying it to yourtheme/wp-hotel-booking/message.php.
 *
 * @author  ThimPress, leehld
 * @package WP-Hotel-Booking/Templates
 * @version 1.6
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( $messages = get_transient( 'hb_message_' . session_id() ) ) {
	foreach ( $messages as $message ) { ?>
		<div class="hb-message <?php echo esc_attr( $message['type'] ); ?>">
			<div class="hb-message-content">
				<?php echo esc_html( $message['message'] ); ?>
			</div>
		</div>
		<?php
	}

	delete_transient( 'hb_message_' . session_id() );
}
