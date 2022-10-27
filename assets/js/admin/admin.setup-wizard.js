( function( $ ) {
    'use strict';
	let $main, $setupForm;

    const blockContent = function blockContent( block ) {
		$main.toggleClass( 'loading', block === undefined ? true : block );
	};

    const getFormData = function getFormData( more ) {
		$setupForm = $('#wphb-setup-form');
        if( $setupForm.length === 0 ) return;
		const data = $setupForm.serializeJSON();
		return $.extend( data, more || {} );
	};
    const getDatabyStep = function getDatabyStep() {
		$.post( {
			url:'',
			dataType: 'html',
			data: getFormData (),
		} );
	};

    const createPages = function createPages( e ) {
		e.preventDefault();
		blockContent();

		$.post( {
			url: hotel_settings.ajax,
			dataType: 'html',
			data: getFormData( {
				action: 'hotel_booking_setup_create_pages',
			} ),
			success( res ) {
                res = JSON.parse( res);
                $('.wphb-setup-detail').replaceWith(res.data);
                $('td.hb-form-field-select_page').DropdownPages();
				blockContent( false );
			},
		} );
	};

    $( function() {
        $main = $( '#main' );
		$setupForm = $( '#wphb-setup-form' );

        $('#wphb-setup-form select').select2({width:'230px', allowClear: true,});
        $( document ).
			on( 'change', 'input, select', getDatabyStep )
		    .on( 'click', '#create-pages', createPages );
	} );
   
}( jQuery ));