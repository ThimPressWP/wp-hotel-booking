document.addEventListener( 'click', function( e ) {
    const target = e.target;
    const sectionBtns = document.querySelector('.hb-button-popup');

    if ( target.classList.contains( 'icon-toggle-filter' )) {
		e.preventDefault();
		const toggleContent = target.closest( '.toggle-content' );
        const form = document.querySelector( '.search-filter-form-el' );
        const toggleOn = target.closest( '.toggle-on' );

		if ( ! toggleContent ) {
			return;
		}

        const contentdropdown = form.querySelectorAll( '.dropdown' );
        if (contentdropdown.length > 0){
            for (let i = 0; i < contentdropdown.length; i++) {
                if(contentdropdown[i].classList.contains( 'toggle-on' )) {
                    contentdropdown[i].classList.remove( 'toggle-on' );
                }
            }
        }
		
		if (  ! toggleOn ) {
			toggleContent.classList.add("toggle-on");
		}else {
			toggleContent.classList.remove("toggle-on");
		}
	}

    if ( sectionBtns && sectionBtns.contains( e.target ) ) {
        e.preventDefault();
		const elhbFilter = target.closest( '.hotel-booking-search-filter' );
		if ( ! elhbFilter ) {
			return;
		}
		elhbFilter.classList.toggle("filter-popup-show");
    }

    if ( target.classList.contains( 'filter-bg' ) ) {
		const elLpCourseFilter = target.closest( '.hotel-booking-search-filter' );
		if ( ! elLpCourseFilter ) {
			return;
		}
		elLpCourseFilter.classList.remove("filter-popup-show");
	}

    if ( target.classList.contains( 'icon-remove-selected' ) ) {
		e.preventDefault();
		window.hbRoomFilterEl.resetSelected( target );
	}

    if ( target.classList.contains( 'clear-selected-list' ) ) {
		e.preventDefault();
		window.hbRoomFilterEl.resetList( target );
	}
});

const classCourseFilter = 'search-filter-form-el';
window.hbRoomFilterEl = {
    resetList: ( target ) => {
		const form = document.querySelector( `.${ classCourseFilter }` );
		const selectedList   = document.querySelector( '.selected-list' );
		const elSelectedList = selectedList.querySelectorAll( '.selected-item' );

		if ( ! elSelectedList ) {
			return; 
		}

		for ( let i = 0; i < elSelectedList.length; i++ ) {
			elSelectedList[i].remove();
		}

		for ( let i = 0; i < form.elements.length; i++ ) {
			form.elements[ i ].removeAttribute( 'checked' );
		}

		target.remove();
        const url = new URL(window.location.href);
        const filterArgs = ['min_price', 'max_price', 'rating', 'room_type'];

        [...filterArgs].map(filterArg => {
            if (url.searchParams.get(filterArg)) {
                url.searchParams.delete(filterArg);
            }
        });

        window.location.href = url;
	},
    resetSelected: ( target ) => {
		const form = document.querySelector( `.${ classCourseFilter }` );
		const lpSelected = target.closest( '.selected-item' );
		const lpSelectedName = lpSelected.getAttribute( 'data-name' ); 
		const lpSelectedID = lpSelected.getAttribute( 'data-value' );
        const url = new URL(window.location.href);

		if ( ! lpSelected ) {
			return;
		}

		for ( let i = 0; i < form.elements.length; i++ ) {
			if(form.elements[ i ].getAttribute('name') ==  lpSelectedName && form.elements[ i ].getAttribute('value') == lpSelectedID){
                form.elements[ i ].click();
			}
		}

        if ( lpSelectedName == 'price' ) {
            const filterArgs = ['min_price', 'max_price'];

            [...filterArgs].map(filterArg => {
                if (url.searchParams.get(filterArg)) {
                    url.searchParams.delete(filterArg);
                }
            });
            window.location.href = url;
        }

		if ( lpSelected ) {
			lpSelected.remove();
		}
	},
}