const getParam = (param) => {
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);

    return urlParams.get(param);
}

const renderPrice = (price) => {
    const currencySymbol = hotel_settings.currency_symbol || '';
    const currencyPosition = hotel_settings.currency_position || 'left';

    price = renderPriceNumber(price);

    switch (currencyPosition) {
        case 'left':
            price = currencySymbol + price;
            break;
        case 'right':
            price = price + currencySymbol;
            break;
        case 'left_with_space':
            price = currencySymbol + ' ' + price;
            break;
        case 'right_with_space':
            price = price + ' ' + currencySymbol;
            break;
        default:
            break;
    }

    return price;
};

const renderPriceNumber = (price) => {
    const numberDecimals = hotel_settings.number_decimal || 0;
    const thousandsSeparator = hotel_settings.thousands_separator || '';

    price = (price / 1).toFixed(numberDecimals);
    price = price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator);

    return price;
};

const hbPriceSlider = () => {
    const priceFields = document.querySelectorAll('.hb-price-field');
    if (!priceFields) {
        return;
    }

    for (let i = 0; i < priceFields.length; i++) {
        const priceField = priceFields[i];
        const minPrice = priceField.getAttribute('data-min');
        const maxPrice = priceField.getAttribute('data-max');
        let step = priceField.getAttribute('data-step');

        if (minPrice === '' || maxPrice === '' || step === '') {
            continue;
        }

        const minPriceNode = priceField.querySelector('.hb-min-price');
        const maxPriceNode = priceField.querySelector('.hb-max-price');


        const priceSliderNode = priceField.querySelector('.hb-price-range');


        const start = getParam('min_price') || minPrice;
        const end = getParam('max_price') || maxPrice;

        step = parseInt(step);

        noUiSlider.create(priceSliderNode, {
            start: [parseInt(start), parseInt(end)],
            connect: true,
            step,
            tooltips: false,
            range: {
                min: parseInt(minPrice), max: parseInt(maxPrice),
            },
            // direction: 'lt',
        });

        priceSliderNode.noUiSlider.on('update', function (values, handle, unencoded) {
            minPriceNode.value = parseInt(values[0]);
            maxPriceNode.value = parseInt(values[1]);
            priceField.querySelector('.min').innerHTML = renderPrice(values[0]);
            priceField.querySelector('.max').innerHTML = renderPrice(values[1]);
        });

        const applyBtn = priceField.querySelector('button.apply');

        //apply btn click event
        applyBtn.addEventListener('click', function (event) {
            event.preventDefault();

            const minPrice = minPriceNode.value;
            const maxPrice = maxPriceNode.value;

            const url = new URL(window.location.href);
            url.searchParams.set('min_price', parseInt(minPrice));
            url.searchParams.set('max_price', parseInt(maxPrice));

            url.searchParams.set('paged', 1);
            window.location.href = url;
        });
    }
}

const hbRating = () => {
    const ratingFields = document.querySelectorAll('.hb-rating-field');
    if (!ratingFields) {
        return;
    }

    for (let i = 0; i < ratingFields.length; i++) {
        const ratingField = ratingFields[i];

        const allInputs = ratingField.querySelectorAll('input[type="checkbox"]');

        let rating = [];
        if (getParam('rating')) {
            rating = getParam('rating').split(',');
        }

        [...rating].map(value => {
            ratingField.querySelector(`input[name ="rating"][value ="${value}"]`).checked = true;
        });

        for (let i = 0; i < allInputs.length; i++) {
            const input = allInputs[i];

            input.addEventListener('change', function (event) {
                const allCheckedInput = ratingField.querySelectorAll('input[type="checkbox"]:checked');

                let value = [];
                [...allCheckedInput].map(checkedInput => {
                    value.push(checkedInput.value);
                });

                const url = new URL(window.location.href);
                if (value.length) {
                    url.searchParams.set('rating', value);
                } else {
                    url.searchParams.delete('rating');
                }

                window.location.href = url;
            });
        }
    }
}

const hbRoomType = () => {
    const roomTypeFields = document.querySelectorAll('.hb-type-field');
    if (!roomTypeFields) {
        return;
    }

    for (let i = 0; i < roomTypeFields.length; i++) {
        const roomTypeField = roomTypeFields[i];

        const allInputs = roomTypeField.querySelectorAll('input[type="checkbox"]');

        let roomTypesValue = [];
        if (getParam('room_type')) {
            roomTypesValue = getParam('room_type').split(',');
        }

        [...roomTypesValue].map(value => {
            roomTypeField.querySelector(`input[name ="room_type"][value ="${value}"]`).checked = true;
        });

        for (let i = 0; i < allInputs.length; i++) {
            const input = allInputs[i];

            input.addEventListener('change', function (event) {
                const allCheckedInput = roomTypeField.querySelectorAll('input[type="checkbox"]:checked');

                let value = [];
                [...allCheckedInput].map(checkedInput => {
                    value.push(checkedInput.value);
                });

                const url = new URL(window.location.href);

                if (value.length) {
                    url.searchParams.set('room_type', value);
                } else if ( value.length == 0 ) {
                    url.searchParams.delete('room_type')
                } else {
                    url.searchParams.delete('room_type', value);
                }

                window.location.href = url;
            });
        }
    }
}

const clearFieldFilter = () => {
    const filterForms = document.querySelectorAll('.search-filter-form');
    if (!filterForms) {
        return;
    }

    for (let i = 0; i < filterForms.length; i++) {
        const filterForm = filterForms[i];
        const clearFilterBtn = filterForm.querySelector('.clear-filter button');

        if (!clearFilterBtn) {
            return;
        }

        clearFilterBtn.addEventListener('click', function () {
            const url = new URL(window.location.href);

            const filterArgs = ['min_price', 'max_price', 'rating', 'room_type'];

            [...filterArgs].map(filterArg => {
                if (url.searchParams.get(filterArg)) {
                    url.searchParams.delete(filterArg);
                }
            });

            window.location.href = url;
        });
    }
}

// Click element
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

document.addEventListener('DOMContentLoaded', () => {
    if (hotel_settings && hotel_settings.is_page_search) {
        return;
    }

    hbPriceSlider();
    hbRating();
    hbRoomType();
    clearFieldFilter();
});
