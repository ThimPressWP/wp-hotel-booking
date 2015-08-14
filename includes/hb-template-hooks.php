<?php

add_action( 'hb_before_search_result', 'hb_enqueue_lightbox_assets' );
add_action( 'hb_lightbox_assets_lightbox2', 'hb_lightbox_assets_lightbox2' );
add_action( 'hb_lightbox_assets_fancyBox', 'hb_lightbox_assets_fancyBox' );