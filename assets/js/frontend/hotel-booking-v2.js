/** search api */
const urlCurrent = document.location.href;
const urlPageSearch = hotel_settings?.url_page_search;
const urlPageRooms = hotel_settings?.url_page_rooms;
let filterRooms = JSON.parse(window.localStorage.getItem('wphb_filter_rooms')) || {};
let firstLoad = true;
const hotelBookingSearchNode = document.querySelector('.hotel-booking-search');

const wphbAddQueryArgs = (endpoint, args) => {
    const url = new URL(endpoint);

    Object.keys(args).forEach((arg) => {
        url.searchParams.set(arg, args[arg]);
    });

    return url;
};

const removeFilterArgs = (url, args) => {
    const filters = ['min_price', 'max_price', 'rating', 'room_type'];

    [...filters].map(filter => {
        if (!args.hasOwnProperty(filter)) {
            url.searchParams.delete(filter);
        }
    });

    return url;
}

const searchRoomsPages = () => {

    const forms = document.querySelector('.wp-hotel-booking-search-rooms .hotel-booking-search form#hb-form-search-page');

    if (forms === null) {
        return;
    }

    requestSearchRoom(forms, filterRooms);
}

const requestSearchRoom = (forms, args, btn = false) => {
    const skeleton = document.querySelector('.wp-hotel-booking-search-rooms .hotel-booking-search ul.wphb-skeleton-animation');
    const wrapperResult = document.querySelector('.wp-hotel-booking-search-rooms .hotel-booking-search .detail__booking-rooms');
    const showNumber = document.querySelector('.wp-hotel-booking-search-rooms .sort-by-wrapper .show-number');
    const wpRestUrl = hotel_settings.wphb_rest_url;

    if (!wpRestUrl) {
        return;
    }

    if (Object.keys(args).length === 0) {
        const monthName = new Intl.DateTimeFormat("en-US", {month: "long"}).format;
        const today = new Date();
        const stringToday = (monthName(today) + ' ' + ('0' + today.getDate()).slice(-2) + ', ' + today.getFullYear());
        const tomorrow = new Date(new Date().getTime() + 24 * 60 * 60 * 1000);
        const stringTomorrow = (monthName(tomorrow) + ' ' + ('0' + tomorrow.getDate()).slice(-2) + ', ' + tomorrow.getFullYear());

        args.check_in_date = stringToday;
        args.check_out_date = stringTomorrow;
        args.adults = null;
        args.max_child = null;
        args.paged = 1;

        //Filter - Price, rating, type
        const searchFilter = document.querySelector('#hotel-booking-search-filter');

        if (searchFilter) {
            const priceField = searchFilter.querySelector('.hb-price-field');
            const ratingField = searchFilter.querySelector('.hb-rating-field');
            const typeField = searchFilter.querySelector('.hb-type-field');

            if (priceField) {
                args.min_price = '';
                args.max_price = '';
            }

            if (ratingField) {
                args.rating = '';
            }

            if (typeField) {
                args.room_type = '';
            }
        }
    }

    const urlWphbSearch = wphbAddQueryArgs(wpRestUrl + 'wphb/v1/rooms/search-rooms', {...args});

    wp.apiFetch({
        path: 'wphb/v1/rooms/search-rooms' + urlWphbSearch.search,
        method: 'GET',
    }).then((response) => {

        if (btn) {
            btn.classList.remove('wphb_loading');
        }

        const {status, data, message} = response;

        if (firstLoad) {
            formSearchRooms(forms, skeleton, wrapperResult);
        }

        const paginationEle = document.querySelector('.rooms-pagination');
        if (paginationEle) {
            paginationEle.remove();
        }

        if (status === 'error') {
            throw new Error(message || 'Error');
        }
        wrapperResult.style.display = 'block';
        wrapperResult.innerHTML = data.content;
        if (showNumber) {
            showNumber.innerHTML = data.show_number;
        }
        const pagination = data.pagination;

        if (typeof pagination !== 'undefined') {
            const paginationHTML = new DOMParser().parseFromString(pagination, 'text/html');
            const paginationNewNode = paginationHTML.querySelector('.rooms-pagination');

            if (paginationNewNode) {
                wrapperResult.after(paginationNewNode);
                wphbPaginationRoom(forms, skeleton, wrapperResult);
            }
        }

    }).catch((error) => {
        wrapperResult.innerHTML = '';
        const errorNode = document.querySelector('.wphb-message.error');

        if (errorNode) {
            errorNode.innerHTML = error.message || 'Error: Query wphb/v1/rooms/search-room';
        } else {
            wrapperResult.insertAdjacentHTML('beforeend', `<p class="wphb-message error" style="display:block">${error.message || 'Error: Query wphb/v1/rooms/search-room'}</p>`);
        }

        if (showNumber) {
            showNumber.innerHTML = '';
        }
    }).finally(() => {
        skeleton.style.display = 'none';
        // Save filter courses to Storage
        window.localStorage.setItem('wphb_filter_rooms', JSON.stringify(args));

        let urlPush = wphbAddQueryArgs(document.location, args);
        urlPush = removeFilterArgs(urlPush, args);

        //check is room extra not push url
        const url_string = urlPush.href;
        const url = new URL(url_string);
        const isRoomExtra = url.searchParams.get("is_page_room_extra");

        if (isRoomExtra != 'select-room-extra' && !firstLoad) {
            window.history.pushState('', '', urlPush);
            //update value checkin checkout to form search room when reload page
            const checkInDate = document.querySelector('.wp-hotel-booking-search-rooms .hotel-booking-search form#hb-form-search-page input[name="check_in_date"]');
            const checkOutDate = document.querySelector('.wp-hotel-booking-search-rooms .hotel-booking-search form#hb-form-search-page input[name="check_out_date"]');
            checkInDate.value = args.check_in_date;
            checkOutDate.value = args.check_out_date;
        }

        //form booking
        bookingRoomsPages(forms);

        firstLoad = false;
        const contentPageSearch = document.querySelector('.wp-hotel-booking-search-rooms .hotel-booking-search');
        if (contentPageSearch != null) {
            contentPageSearch.scrollIntoView({behavior: "smooth"});
        }
        //auto set quantity extra option when change select quantity before book room in page search
        toggleExtravalue();
    });
}

const formSearchRooms = (forms, skeleton, wrapperResult) => {

    if (forms === null) return;

    forms.addEventListener('submit', (event) => {
        event.preventDefault();
        const checkinDate = forms.querySelector('input[name="check_in_date"]').value;
        const checkoutDate = forms.querySelector('input[name="check_out_date"]').value;
        const countAdults = forms.querySelector('select[name="adults_capacity"]') ? forms.querySelector('select[name="adults_capacity"]').value : 0;
        const maxChild = forms.querySelector('select[name="max_child"]') ? forms.querySelector('select[name="max_child"]').value : 0;
        const paged = forms.querySelector('input[name="paged"]') ? forms.querySelector('input[name="paged"]').value : 1;
        const btn = forms.querySelector('button.wphb-button');
        btn && btn.classList.add('wphb_loading');

        if (checkinDate === '' || checkoutDate === '') {
            alert(' Please select check in and check out date and search again! ');
            btn && btn.classList.remove('wphb_loading');
            return;
        }

        wrapperResult.innerHTML = '';
        skeleton.style.display = 'block';

        filterRooms = {
            ...filterRooms,
            check_in_date: checkinDate,
            check_out_date: checkoutDate,
            adults: countAdults,
            max_child: maxChild,
            paged: paged
        };

        window.localStorage.setItem('wphb_filter_rooms', JSON.stringify(filterRooms));

        requestSearchRoom(forms, filterRooms, btn);
    });
}
const wphbPaginationRoom = (forms, skeleton, wrapperResult) => {
    const paginationEle = document.querySelectorAll('.wp-hotel-booking-search-rooms .rooms-pagination .page-numbers');

    paginationEle.length > 0 && paginationEle.forEach((ele) => ele.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();

        wrapperResult.style.display = 'none';
        skeleton.style.display = 'block';

        let filterRooms = JSON.parse(window.localStorage.getItem('wphb_filter_rooms')) || {};

        const urlString = event.currentTarget.getAttribute('href');

        if (urlString) {
            const current = [...paginationEle].filter((el) => el.classList.contains('current'));
            const paged = parseInt(event.currentTarget.textContent) || (ele.classList.contains('next') && parseInt(current[0].textContent) + 1) || (ele.classList.contains('prev') && parseInt(current[0].textContent) - 1);
            filterRooms.paged = paged;

            requestSearchRoom(forms, {...filterRooms});
        }
    }));
};
/** end search api */

const addtocartElementor = () => {
    const formBookingel = document.querySelector('.hotel-booking-search-el form#hb-form-search-page');

    if ( !formBookingel ) {
		return;
	}

    bookingRoomsPages(formBookingel);
}
/** Booking room search page */

const bookingRoomsPages = (formsCheck) => {

    const formBooking = document.querySelectorAll('.wp-hotel-booking-search-rooms form.hb-page-search-room-results');

    if (formBooking.length == 0) return;

    const checkinDate = formsCheck.querySelector('input[name="check_in_date"]')?.value;
    const checkoutDate = formsCheck.querySelector('input[name="check_out_date"]')?.value;

    const formSearchPage = document.querySelector( '#hb-form-search-page' );
    let adults = 1, maxChild = 0;
    if ( formSearchPage ) {
        adults = formSearchPage.querySelector('select[name="adults_capacity"]') ? formSearchPage.querySelector('select[name="adults_capacity"]').value : 1;
        maxChild = formSearchPage.querySelector('select[name="max_child"]') ? formSearchPage.querySelector('select[name="max_child"]').value : 0;
    }
    const submit = async (form, btn = false, numRoom, roomID) => {
        const extraData = [];
        const hotelOption = form.querySelectorAll('input.hb_optional_quantity_selected');

        hotelOption && hotelOption.forEach((ele) => {
            if (ele.checked) {
                // const eleName = ele.getAttribute('name');
                // const extraID = eleName?.match(/(?<=\[).+?(?=\])/)?.[0] || null;
                const extraID = ele.dataset.id;
                const qty = parseInt(ele.parentElement?.nextElementSibling?.querySelector('input[class="hb_optional_quantity"]')?.value) || 1;
                if (extraID) {
                    extraData.push({extraID, qty});
                }
            }
        });

        try {
            const response = await wp.apiFetch({
                path: 'wphb/v1/rooms/book-rooms',
                method: 'POST',
                data: { roomID, checkinDate, checkoutDate, numRoom, extraData, adults, maxChild },
            });

            const { status, data } = response;
            const redirect = data?.results?.redirect || '';
            const message = data?.results?.message || '';
            const hasExtra = data?.results?.has_extra || false;

            if (btn) {
                btn.classList.remove('wphb_loading');
            }
            if ( 'error' === status ) {
                throw new Error( message );
            }
            if (!hasExtra) {
                window.location.href = redirect;
            } else {
                const htmlExtraOptions = data?.results?.extra_html || '';
                const addtocartWrap = form.querySelector( '.hb_search_add_to_cart' );
                if ( addtocartWrap ) {
                    btn.style.display = 'none';
                    addtocartWrap.insertAdjacentHTML('beforeend', htmlExtraOptions);
                } else {
                    form.insertAdjacentHTML('beforeend', htmlExtraOptions);
                }
            }
        } catch (error) {
            alert(error);
        }
    };

    formBooking.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const numRoom = form.querySelector('select[name="hb-num-of-rooms"]')?.value;
            const roomID = form.querySelector('input[name="room-id"]')?.value;
            const quantityBook = form.querySelector('select[name="hb-num-of-rooms"]')?.value;

            if (quantityBook == 0) {
                alert('Please select quantity room!');
                return;
            }
            if (checkinDate === '' || checkoutDate === '') {
                alert(' Please select check in and check out date and search again! ');
                return;
            }
            const btn = form.querySelector('button.hb_add_to_cart');
            btn && btn.classList.add('wphb_loading');
            submit(form, btn, numRoom, roomID);
        });
        form.addEventListener('click', (e) => {
            let target = e.target;
            if ( target.classList.contains( 'add-extra-to-cart' ) ) {
                target.classList.add('wphb_loading');
                const cartID = target.dataset.cartid;
                addExtraToCartNew( form, cartID, target );
            }
        });
    });
    const addExtraToCartNew = async ( form, cartID, btn ) => {
        const extraData = [];
        const hotelOption = form.querySelectorAll('input.hb_optional_quantity_selected');

        hotelOption && hotelOption.forEach((ele) => {
            if (ele.checked) {
                const extraID = ele?.dataset.id || null;
                const qty = parseInt(ele.parentElement?.nextElementSibling?.querySelector(`input[name="hb_optional_quantity[${extraID}]"]`)?.value) || 1;

                if (extraID) {
                    extraData.push({extraID, qty});
                }
            }
        });

        try {
            const response = await wp.apiFetch({
                path: 'wphb/v1/rooms/add-extra-cart',
                method: 'POST',
                data: {cartID, extraData},
            });

            const {status, data, message} = response;
            btn.classList.remove( 'wphb_loading' );
            if ( 'error' === status ) {
                throw new Error( message )
            }
            const redirect = data?.redirect || '';
            if ('success' === status && redirect) {
                window.location.href = redirect;
            }
        } catch (error) {
            alert(error);
        }
    }
}

const addExtraToCart = () => {
    const formExtra = document.querySelector('form.hb-select-extra-results');

    if (formExtra === null) return;

    const submit = async () => {

        const extraData = [];
        const cartID = formExtra.querySelector('input[name="cart_id"]').value;
        const hotelOption = formExtra.querySelectorAll('input.hb_optional_quantity_selected');

        hotelOption && hotelOption.forEach((ele) => {
            if (ele.checked) {
                // const eleName = ele.getAttribute('name');
                // const extraID = eleName?.match(/(?<=\[).+?(?=\])/)?.[0] || null;
                const extraID = ele?.dataset.id || null;
                const qty = parseInt(ele.parentElement?.nextElementSibling?.querySelector('input[class="hb_optional_quantity"]')?.value) || 1;

                if (extraID) {
                    extraData.push({extraID, qty});
                }
            }
        });

        try {
            const response = await wp.apiFetch({
                path: 'wphb/v1/rooms/add-extra-cart',
                method: 'POST',
                data: {cartID, extraData},
            });

            const {status, redirect} = response;
            if ('success' === status && redirect) {
                window.location.href = redirect;
            }

        } catch (error) {
            alert(error.message && error.message);
        }
    }

    formExtra.addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = formExtra.querySelector('button[type="submit"]');
        btn && btn.classList.add('wphb_loading');
        submit();
    });

}
/** End Booking search page */


/** search form */

const checkAvailableRooms = () => {

    //remove sidebar search in page search room_select
    if (hotel_settings?.is_page_search) {
        const sideBar = document.querySelector('.thim-widget-search-room');
        if (sideBar != null) {
            const FormSidebar = sideBar.querySelector('form#hb-form-search-page');
            if (FormSidebar != null) {
                FormSidebar.removeAttribute('id');
            }
            const searchResult = sideBar.querySelector('#hotel-booking-results');
            if (searchResult != null) {
                searchResult.remove();
            }
        }
    }

    const forms = document.querySelectorAll('form[class^="hb-search-form"]:not(#hb-form-search-page)');

    forms.length > 0 && forms.forEach((form) => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const checkinDate = form.querySelector('input[name="check_in_date"]').value;
            const checkoutDate = form.querySelector('input[name="check_out_date"]').value;
            const countAdults = form.querySelector('select[name="adults_capacity"]') ? form.querySelector('select[name="adults_capacity"]').value : 0;
            const maxChild = form.querySelector('select[name="max_child"]') ? form.querySelector('select[name="max_child"]').value : 0;
            const paged = form.querySelector('input[name="paged"]') ? form.querySelector('input[name="paged"]').value : 1;

            if (checkinDate === '' || checkoutDate === '') {
                alert(' Please select check in and check out date and search again! ');
                return;
            }

            const data = {
                check_in_date: checkinDate,
                check_out_date: checkoutDate,
                adults: countAdults,
                max_child: maxChild,
                paged: paged,
            }
            window.localStorage.setItem('wphb_filter_rooms', JSON.stringify(data));
            const urlPush = wphbAddQueryArgs(document.location, data);
            const urlString = urlPush.search;
            window.location.href = urlPageRooms + urlString;
        })

    });
}
/** End search form */

const processCheckout = () => {
    const form = document.getElementById('hb-cart-form');
    if (form === null) return;
    const btn = form.querySelector('a.hb_checkout');
    if (btn === null) return;
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        if (hotel_settings?.checkout_page_url) {
            window.location.href = hotel_settings.checkout_page_url;
        } else {
            alert('Please set checkout page url in settings');
        }
    });
}

//auto set quantity extra option when change select quantity before book room in page search
const toggleExtravalue = () => {
    const select = document.querySelector('.hb-page-search-room-results select.number_room_select');
    if (select == null) return;

    select.addEventListener('change', function (e) {
        e.preventDefault();
        const optionExtras = document.querySelectorAll('.hb-page-search-room-results .hb_optional_quantity');
        if (optionExtras == null) return;
        optionExtras.forEach(function (extra) {
            extra.value = select.value;
        });
    })
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

const priceSlider = () => {
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

        const start = filterRooms.min_price || minPrice;
        const end = filterRooms.max_price || maxPrice;
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
            let minValue = values[0], maxValue = values[1];

            if (isNaN(minValue)) {
                minValue = 0;
            }
            if (isNaN(maxValue)) {
                maxValue = 0;
            }

            minPriceNode.value = parseInt(minValue);
            maxPriceNode.value = parseInt(maxValue);
            priceField.querySelector('.min').innerHTML = renderPrice(minValue);
            priceField.querySelector('.max').innerHTML = renderPrice(maxValue);
        });

        const applyBtn = priceField.querySelector('button.apply');

        //apply btn click event
        if(applyBtn){
            applyBtn.addEventListener('click', function (event) {
                event.preventDefault();

                const minPrice = minPriceNode.value;
                const maxPrice = maxPriceNode.value;

                filterRooms = {
                    ...filterRooms,
                    min_price: parseInt(minPrice),
                    max_price: parseInt(maxPrice)
                };

                window.localStorage.setItem('wphb_filter_rooms', JSON.stringify(filterRooms));

                searchRoomsPages();
            });
        }
    }
}

const rating = () => {
    const ratingFields = document.querySelectorAll('.hb-rating-field');
    if (!ratingFields) {
        return;
    }

    for (let i = 0; i < ratingFields.length; i++) {
        const ratingField = ratingFields[i];

        const allInputs = ratingField.querySelectorAll('input[type="checkbox"]');
        const rating = filterRooms.rating || [];

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

                filterRooms = {
                    ...filterRooms,
                    rating: value
                };

                window.localStorage.setItem('wphb_filter_rooms', JSON.stringify(filterRooms));

                searchRoomsPages();
            });
        }
    }
}

const roomType = () => {
    const roomTypeFields = document.querySelectorAll('.hb-type-field');
    if (!roomTypeFields) {
        return;
    }

    for (let i = 0; i < roomTypeFields.length; i++) {
        const roomTypeField = roomTypeFields[i];

        const allInputs = roomTypeField.querySelectorAll('input[type="checkbox"]');

        const roomTypesValue = filterRooms.room_type || [];
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

                filterRooms = {
                    ...filterRooms,
                    room_type: value
                };

                window.localStorage.setItem('wphb_filter_rooms', JSON.stringify(filterRooms));

                searchRoomsPages();
            });
        }
    }
}

const clearFilter = () => {
    const filterForms = document.querySelectorAll('.search-filter-form');
    if (!filterForms) {
        return;
    }

    for (let i = 0; i < filterForms.length; i++) {
        const filterForm = filterForms[i];
        const clearFilter = filterForm.querySelector('.clear-filter button');

        clearFilter.addEventListener('click', function (event) {
            const priceField = document.querySelector('.hb-price-field');
            if (priceField) {
                const minPrice = priceField.getAttribute('data-min');
                const maxPrice = priceField.getAttribute('data-max');
                const priceSliderNode = priceField.querySelector('.hb-price-range');
                const start = minPrice;
                const end = maxPrice;

                priceSliderNode.noUiSlider.updateOptions({
                    start: [parseInt(start), parseInt(end)]
                });
            }

            const ratingFields = filterForm.querySelectorAll('.hb-rating-field input');

            [...ratingFields].map(ratingField => {
                ratingField.checked = false;
            });

            const roomTypeFields = filterForm.querySelectorAll('.hb-type-field input');

            [...roomTypeFields].map(roomTypeField => {
                roomTypeField.checked = false;
            });

            if (filterRooms.hasOwnProperty('min_price')) {
                delete filterRooms['min_price'];
            }

            if (filterRooms.hasOwnProperty('max_price')) {
                delete filterRooms['max_price'];
            }

            if (filterRooms.hasOwnProperty('rating')) {
                delete filterRooms['rating'];
            }

            if (filterRooms.hasOwnProperty('room_type')) {
                delete filterRooms['room_type'];
            }

            const listItemNodes = document.querySelectorAll('.hb-selection-field .list-item');
            [...listItemNodes].map(listItemNode => {
                listItemNode.remove();
            });
            window.localStorage.setItem('wphb_filter_rooms', JSON.stringify(filterRooms));

            searchRoomsPages();
        });
    }
}

const hbFilterSelection = () => {
    const selectionWrapper = document.querySelector('.hb-selection-field');
    if (!selectionWrapper) {
        return;
    }

    const priceFields = document.querySelectorAll('.hb-price-field');

    if (priceFields.length) {
        for (let i = 0; i < priceFields.length; i++) {
            const priceField = priceFields[i];

            const priceSliderNode = priceField.querySelector('.hb-price-range');

            priceSliderNode.noUiSlider.on('update', function (values, handle, unencoded) {
                const minPrice = parseInt(values[0]);
                const maxPrice = parseInt(values[1]);

                changeSelectedField('price', minPrice + '-' + maxPrice, renderPrice(minPrice) + '-' + renderPrice(maxPrice));
            });
        }
    }

    const ratingFields = document.querySelectorAll('.hb-rating-field');

    if (ratingFields.length) {
        [...ratingFields].map(ratingField => {
            const allInputs = ratingField.querySelectorAll('input[type="checkbox"]');
            [...allInputs].map(ratingNode => {
                if (ratingNode.checked) {
                    const value = ratingNode.value;
                    const label = ratingNode.closest('label').querySelector('span').innerHTML.replace('-', ' ');
                    changeSelectedField('rating', value, label);
                }

                ratingNode.addEventListener('change', function () {
                    const value = this.value;
                    const label = ratingNode.closest('label').querySelector('span').innerHTML.replace('-', ' ');
                    changeSelectedField('rating', value, label);
                });
            })
        });
    }

    const roomTypeFields = document.querySelectorAll('.hb-type-field');
    if (roomTypeFields.length) {
        for (let i = 0; i < roomTypeFields.length; i++) {
            const roomTypeField = roomTypeFields[i];

            const allInputs = roomTypeField.querySelectorAll('input[type="checkbox"]');

            [...allInputs].map(roomTypeNode => {
                if (roomTypeNode.checked) {
                    const value = roomTypeNode.value;
                    const label = roomTypeNode.closest('label').querySelector('span').innerHTML.replace('-', ' ');
                    changeSelectedField('room-type', value, label);
                }

                roomTypeNode.addEventListener('change', function () {
                    const value = this.value;
                    const label = roomTypeNode.closest('label').querySelector('span').innerHTML.replace('-', ' ');
                    changeSelectedField('room-type', value, label);
                });
            })
        }
    }
}

const removeSelection = () => {
    document.addEventListener('click', function (event) {
        const target = event.target;

        if (!target.classList.contains('remove')) {
            return;
        }

        const selectionWrapper = target.closest('.hb-selection-field');

        if (!selectionWrapper) {
            return;
        }

        const listItem = target.closest('.list-item');

        const field = listItem.getAttribute('data-field');
        switch (field) {
            case 'room-type':
                resetRoomType(listItem.getAttribute('data-value'));
                break;
            case 'rating':
                resetRating(listItem.getAttribute('data-value'));
                break;
            case 'price':
                resetPrice();
                break;
            default:
                break;
        }

        if (listItem) {
            listItem.remove();
        }
    });
}

const resetRoomType = (value = 'all') => {
    const roomTypeFields = document.querySelectorAll('.hb-type-field');

    [...roomTypeFields].map(roomTypeField => {
        const roomTypeNodes = roomTypeField.querySelectorAll('input[type="checkbox"]');
        if (value === 'all') {
            [...roomTypeNodes].map(roomTypeNode => {
                roomTypeNode.checked = false;
            })
        } else {
            const input = roomTypeField.querySelector(`.room-type-list input[value="${value}"]`);
            input.checked = false;
        }

        let roomTypeVal = [];

        const allCheckedInput = roomTypeField.querySelectorAll('input[type="checkbox"]:checked');

        [...allCheckedInput].map(checkedInput => {
            roomTypeVal.push(checkedInput.value);
        });

        filterRooms = {
            ...filterRooms,
            room_type: roomTypeVal
        };

        window.localStorage.setItem('wphb_filter_rooms', JSON.stringify(filterRooms));

        searchRoomsPages();
    });
}

const resetRating = (value = 'all') => {
    const ratingFields = document.querySelectorAll('.hb-rating-field');

    [...ratingFields].map(ratingField => {
        const ratingNodes = ratingField.querySelectorAll('input[type="checkbox"]');
        if (value === 'all') {
            [...ratingNodes].map(ratingNode => {
                ratingNode.checked = false;
            })
        } else {
            const input = ratingField.querySelector(`.rating-list input[value="${value}"]`);
            input.checked = false;
        }

        let ratingVal = [];
        const allCheckedInput = ratingField.querySelectorAll('input[type="checkbox"]:checked');

        [...allCheckedInput].map(checkedInput => {
            ratingVal.push(checkedInput.value);
        });

        filterRooms = {
            ...filterRooms,
            rating: ratingVal
        };

        window.localStorage.setItem('wphb_filter_rooms', JSON.stringify(filterRooms));

        searchRoomsPages();
    });
}

const resetPrice = () => {
    const priceFields = document.querySelectorAll('.hb-price-field');

    if (priceFields.length) {
        for (let i = 0; i < priceFields.length; i++) {
            const priceField = priceFields[i];

            const priceSliderNode = priceField.querySelector('.hb-price-range');

            priceSliderNode.noUiSlider.updateOptions({
                start: [parseInt(priceField.getAttribute('data-min')), parseInt(priceField.getAttribute('data-max'))],
            });
        }
    }

    if (filterRooms.hasOwnProperty('min_price')) {
        delete filterRooms['min_price'];
    }

    if (filterRooms.hasOwnProperty('max_price')) {
        delete filterRooms['max_price'];
    }

    window.localStorage.setItem('wphb_filter_rooms', JSON.stringify(filterRooms));

    searchRoomsPages();
}

const changeSelectedField = (field, value, text) => {
    const listNode = document.querySelector('.hb-selection-field .list');

    let fieldNode = listNode.querySelector(`li[data-field="${field}"]`);

    if (field === 'rating' || field === 'room-type') {
        fieldNode = listNode.querySelector(`li[data-field="${field}"][data-value="${value}"]`);
    }

    if (fieldNode) {
        if (field === 'rating' || field === 'room-type') {
            fieldNode.remove();
        } else {
            if (value) {
                fieldNode.setAttribute('data-value', value);
                fieldNode.querySelector('.title').innerHTML = text;
            } else {
                fieldNode.remove();
            }
        }
    } else {
        const item = `<li class="list-item" data-field = "${field}" data-value="${value}">
            <span class="title">${text}</span>
            <svg class="remove" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M12.5 3.5L3.5 12.5" stroke="#AAAFB6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12.5 12.5L3.5 3.5" stroke="#AAAFB6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
           </svg>
        </li>`;
        listNode.insertAdjacentHTML('beforeend', item);
    }
}

const sortBy = () => {
    const sortByWrapper = document.querySelector('.sort-by-wrapper');
    if (!sortByWrapper) {
        return;
    }

    const sortBy = filterRooms.sort_by || '';
    const listOptions = sortByWrapper.querySelectorAll('ul li');
    const toggle = sortByWrapper.querySelector('.toggle');

    [...listOptions].map(element => {
        if (element.getAttribute('data-value') === sortBy) {
            toggle.innerHTML = element.innerHTML;
            element.classList.add('active');
        } else {
            element.classList.remove('active');
        }

        element.addEventListener('click', function (event) {
            const value = element.getAttribute('data-value');

            filterRooms = {
                ...filterRooms,
                'sort_by': value
            };

            window.localStorage.setItem('wphb_filter_rooms', JSON.stringify(filterRooms));

            searchRoomsPages();
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    searchRoomsPages();//use in page search room
    addExtraToCart();
    addtocartElementor();
    checkAvailableRooms(); // use multi form search will redirect to page search room with data valid :
    processCheckout();

    if (hotelBookingSearchNode && hotel_settings && hotel_settings.is_page_search) {
        priceSlider();
        rating();
        roomType();
        hbFilterSelection();
        removeSelection();
        clearFilter();
        sortBy();
    }
});
