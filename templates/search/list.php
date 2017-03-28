<?php

if ( !defined( 'ABSPATH' ) ) {
	exit();
}

?>
<ul class="hb-search-results">
	<?php
	foreach ( $results as $room ) {
		wphb_get_template( 'search/loop.php', array( 'room' => $room, 'atts' => $atts ) );
	}
	?>
</ul>