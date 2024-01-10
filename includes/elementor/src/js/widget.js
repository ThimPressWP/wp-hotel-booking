
jQuery( window ).on( 'elementor/frontend/init', () => {
	const addHandler = ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( window.ThimEkits.ThimSlider, { 
			$element,
		} );
	};
	elementorFrontend.hooks.addAction( 'frontend/element_ready/list-room.default', addHandler );
} )