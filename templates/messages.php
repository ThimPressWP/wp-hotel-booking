<?php
/**
 * Message - Show plugin messages
 *
 * Override this template by copying it to yourtheme/tp-hotel-booking/messages.php
 *
 * @author        ThimPress
 * @package       wp-hotel-booking/templates
 * @version       1.6
 */

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

if ( $messages = get_transient( 'hb_message_' . session_id() ) ) {
	foreach ( $messages as $message ) {
		?>
        <div class="hb-message <?php echo esc_attr( $message['type'] ); ?>">
            <div class="hb-message-content">
				<?php echo esc_html( $message['message'] ); ?>
            </div>
        </div>
		<?php
	}
}
delete_transient( 'hb_message_' . session_id() );