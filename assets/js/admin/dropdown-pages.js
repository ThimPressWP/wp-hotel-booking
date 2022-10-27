( function( $ ) {
	function DropdownPages( el, options ) {
		this.options = $.extend( {
			ID: '',
			name: 'Add new page',
		}, options || {} );
		const $element = $( el ),
			$select = $element.find( 'select' ),
			$listWrap = $element.find( '.list-pages-wrapper' ),
			$actions = $element.find( '.quick-add-page-actions' ),
			$form = $element.find( '.quick-add-page-inline' );

		function addNewPageToList( args ) {
			const $new_option = $( '<option value="' + args.ID + '">' + args.name + '</option>' );
			const position = $.inArray( args.ID + '', args.positions );

			$( '.hb-form-field-select_page select' ).each( function() {
				const $sel = $( this ),
					$option = $new_option.clone();
                $sel.append( $option );
			} );
		}

		$select.on( 'change', function() {
			$actions.addClass( 'hide-if-js' );
			if ( this.value !== 'add_new_page' ) {
				if ( parseInt( this.value ) ) {
					$actions.find( 'a.edit-page' ).attr( 'href', 'post.php?post=' + this.value + '&action=edit' );
					$actions.find( 'a.view-page' ).attr( 'href', hotel_settings.site_url + '?page_id=' + this.value );
					$actions.removeClass( 'hide-if-js' );
					$select.attr( 'data-selected', this.value );
				}
				return;
			}           
			$listWrap.addClass( 'hide-if-js' );
			$form.removeClass( 'hide-if-js' ).find( 'input' ).trigger( 'focus' ).val( '' );
		} );

		// Select 2
		$select
			.css( 'width', $select.width() + 50 )
			.find( 'option' ).each( function() {
				$( this ).html( $( this ).html().replace( /&nbsp;&nbsp;&nbsp;/g, '' ) );
			} );

		$select.select2( {
            width: '250px',
			allowClear: true,
		} );

		$select.on( 'select2:select', function( e ) {
			const data = e.params.data;
		} );

		$element.on( 'click', '.quick-add-page-inline button', function() {
			const $button = $( this ),
				$input = $form.find( 'input' ),
				page_name = $input.val();
			if ( ! page_name ) {
				alert( 'Please enter the name of page' );
				$input.trigger( 'focus' );
				return;
			}
			$button.prop( 'disabled', true );
			$.ajax( {
				url: hotel_settings.ajax,
				data:{
					action:'hotel_booking_create_pages',
					page_name,
				},
				type: 'post',
				dataType: 'html',
				success( response ) {
					response = JSON.parse( response );
                    console.log(response);
					if ( response.page ) {
						addNewPageToList( {
							ID: response.page.ID,
							name: response.page.post_title,
							positions: 0,
						} );
						$select.val( response.page.ID ).trigger( 'focus' );
						$select.val( response.page.ID ).trigger( 'change' );
						$form.addClass( 'hide-if-js' );
					} else if ( response.error ) {
						alert( response.error );
					}
					$button.prop( 'disabled', false );
					$listWrap.removeClass( 'hide-if-js' );
				},
			} );
		} ).on( 'click', '.quick-add-page-inline a', function( e ) {
			e.preventDefault();
			$form.addClass( 'hide-if-js' );
			$select.val( $select.attr( 'data-selected' ) + '' ).removeAttr( 'disabled' ).trigger( 'change' );
			$listWrap.removeClass( 'hide-if-js' );
		} ).on( 'click', '.button-quick-add-page', function( e ) {
			$select.val( 'add_new_page' ).trigger( 'change' );
		} ).on( 'keypress keydown', '.quick-add-page-inline input[type="text"]', function( e ) {
			if ( e.key == 'Enter' && e.type == 'keypress' ) {
				e.preventDefault();
				$( this ).siblings( 'button' ).trigger( 'click' );
			} else if ( e.key == 'Escape' && e.type == 'keydown' ) {
				$( this ).siblings( 'a' ).trigger( 'click' );
			}
		} );
	}

    $.fn.DropdownPages = function() {
        return $.each( this, function() {
			let $instance = $( this ).data( 'DropdownPages' );
			if ( ! $instance ) {
				$instance = new DropdownPages( this, {} );
				$( this ).data( 'DropdownPages', $instance );
			}
			return $instance;
		} );
    }

    $( function() {
        $('td.hb-form-field-select_page').DropdownPages();
	} );

}( jQuery ) );

