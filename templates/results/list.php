<ul class="hb-search-results">
	<?php
		foreach( $results as $room ){
		    hb_get_template( 'results/loop.php', array( 'room' => $room, 'atts' => $atts ) );
		}
	?>
</ul>