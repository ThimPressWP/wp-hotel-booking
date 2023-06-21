/** search api */
const urlCurrent = document.location.href;
const urlPageSearch = hotel_settings?.url_page_search;
let filterRooms = JSON.parse(window.localStorage.getItem('wphb_filter_rooms')) || {};
let firstLoad = true;


const wphbAddQueryArgs = (endpoint, args) => {
    const url = new URL(endpoint);

    Object.keys(args).forEach((arg) => {
        url.searchParams.set(arg, args[arg]);
    });

    return url;
};

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

        const urlPush = wphbAddQueryArgs(document.location, args);
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


/** Booking room search page */

const bookingRoomsPages = (formsCheck) => {

    const formBooking = document.querySelectorAll('.wp-hotel-booking-search-rooms form.hb-page-search-room-results');

    if (formBooking.length == 0) return;

    const checkinDate = formsCheck.querySelector('input[name="check_in_date"]')?.value;
    const checkoutDate = formsCheck.querySelector('input[name="check_out_date"]')?.value;

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
                data: {roomID, checkinDate, checkoutDate, numRoom, extraData},
            });

            const {status, data} = response;
            const redirect = data?.results?.redirect || '';
            const message = data?.results?.message || '';

            if (btn) {
                btn.classList.remove('wphb_loading');
            }

            if ('success' === status && redirect) {
                // if ( message != '' ) {
                //     alert( message );
                // }
                window.location.href = redirect;
            }

        } catch (error) {
            alert(error.message && error.message);
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
        })
    })
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
            ;

        });

        try {
            const response = await wp.apiFetch({
                path: 'wphb/v1/rooms/add-extra-cart',
                method: 'POST',
                data: {cartID, extraData},
            });

            const {status, redirect} = response;
            console.log(response);
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

const checkAvaliableRooms = () => {

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
            window.location.href = urlPageSearch + urlString;
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
            minPriceNode.value = parseInt(values[0]);
            maxPriceNode.value = parseInt(values[1]);
            priceField.querySelector('.min').innerHTML = values[0];
            priceField.querySelector('.max').innerHTML = values[1];
        });

        const applyBtn = priceField.querySelector('button.apply');

        //apply btn click event
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

    for(let i = 0 ; i < roomTypeFields.length; i++){
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
    checkAvaliableRooms(); // use multi form search will redirect to page search room with data valid :
    processCheckout();
    priceSlider();
    rating();
    roomType();
    sortBy();
});


