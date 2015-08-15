<?php
if( $messages = get_transient( 'hb_message' ) ){
    foreach( $messages as $message ){
        ?>
        <div class="hb-message <?php echo $message['type'];?>">
            <div class="hb-message-content">
            <?php echo $message['message'];?>
            </div>
        </div>
        <?php
    }
}
delete_transient( 'hb_message' );