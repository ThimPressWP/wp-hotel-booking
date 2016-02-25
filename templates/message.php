<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

?>
<pre><?php print_r($_REQUEST); ?></pre>
<?php _e( 'Thank for your booking!', 'tp-hotel-booking' ); ?>
<a href="<?php echo get_the_permalink(33); ?>"><?php _e( 'New Booking', 'tp-hotel-booking' ); ?></a>