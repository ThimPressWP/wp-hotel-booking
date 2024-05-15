(function () {
    const priceFields = document.querySelectorAll('.hb-price-field');
    const ratingFields = document.querySelectorAll('.hb-rating-field');
    const roomTypeFields = document.querySelectorAll('.hb-type-field');


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


                const url = new URL(window.location.href);

                url.searchParams.set('min_price', parseInt(minPrice));
                url.searchParams.set('max_price', parseInt(maxPrice));

                url.searchParams.set('paged', 1);
                window.location.href = url;
            });
        }
    }

    const hbRating = () => {
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

                    url.searchParams.set('paged', 1);
                    // window.location.href = url;
                });
            }
        }
    }

    const hbRoomType = () => {
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
                    } else {
                        url.searchParams.delete('room_type', value);
                    }

                    url.searchParams.set('paged', 1);

                    // window.location.href = url;
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

                url.searchParams.set('paged', 1);
                window.location.href = url;
            });
        }
    }

    const hbFilterSelection = () => {
        const selectionWrapper = document.querySelector('.hb-selection-field');
        if (!selectionWrapper) {
            return;
        }


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

            let param = [];
            [...roomTypeNodes].map(checkedInput => {
                param.push(checkedInput.value);
            });

            const url = new URL(window.location.href);
            if (param.length) {
                url.searchParams.set('rating', param);
            } else {
                url.searchParams.delete('rating');
            }

            url.searchParams.set('paged', 1);
            window.location.href = url;
        });
    }

    const resetRating = (value = 'all') => {
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

            let param = [];
            [...ratingNodes].map(checkedInput => {
                param.push(checkedInput.value);
            });

            const url = new URL(window.location.href);
            if (param.length) {
                url.searchParams.set('rating', param);
            } else {
                url.searchParams.delete('rating');
            }

            url.searchParams.set('paged', 1);
            window.location.href = url;
        });
    }

    const resetPrice = () => {
        if (priceFields.length) {
            for (let i = 0; i < priceFields.length; i++) {
                const priceField = priceFields[i];

                const priceSliderNode = priceField.querySelector('.hb-price-range');

                priceSliderNode.noUiSlider.updateOptions({
                    start: [parseInt(priceField.getAttribute('data-min')), parseInt(priceField.getAttribute('data-max'))],
                });
            }
        }

        const url = new URL(window.location.href);

        const filterArgs = ['min_price', 'max_price'];

        [...filterArgs].map(filterArg => {
            if (url.searchParams.get(filterArg)) {
                url.searchParams.delete(filterArg);
            }
        });

        url.searchParams.set('paged', 1);
        window.location.href = url;
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

    const hbFilter = () => {
        const roomFilterBtn = document.querySelector('.hb-room-filter-btn');

        if (roomFilterBtn) {
            roomFilterBtn.addEventListener('click', function (event) {
                event.preventDefault();

                const priceFields = document.querySelectorAll('.hb-price-field');
                const ratingFields = document.querySelectorAll('.hb-rating-field');
                const roomTypeFields = document.querySelectorAll('.hb-type-field');


                const url = new URL(window.location.href);
                url.searchParams.set('paged', 1);

                //Price
                for (let i = 0; i < priceFields.length; i++) {
                    const priceField = priceFields[i];
                    const minPrice = priceField.getAttribute('data-min');
                    const maxPrice = priceField.getAttribute('data-max');

                    url.searchParams.set('min_price', parseInt(minPrice));
                    url.searchParams.set('max_price', parseInt(maxPrice));
                }

                //Rating

                for (let i = 0; i < ratingFields.length; i++) {
                    const ratingField = ratingFields[i];

                    const allCheckedInput = ratingField.querySelectorAll('input[type="checkbox"]:checked');

                    let value = [];
                    [...allCheckedInput].map(checkedInput => {
                        value.push(checkedInput.value);
                    });


                    if (value.length) {
                        url.searchParams.set('rating', value);
                    }else if(url.searchParams.has('rating')){
                        url.searchParams.delete('rating');
                    }
                }

                //Room types

                for (let i = 0; i < roomTypeFields.length; i++) {
                    const roomTypeField = roomTypeFields[i];

                    const allCheckedInput = roomTypeField.querySelectorAll('input[type="checkbox"]:checked');

                    let value = [];
                    [...allCheckedInput].map(checkedInput => {
                        value.push(checkedInput.value);
                    });


                    if (value.length) {
                        url.searchParams.set('room_type', value);
                    }else if(url.searchParams.has('room_type')){
                        url.searchParams.delete('room_type');
                    }
                }

                // console.log(query);
                // console.log(url);
                window.location.href = url;
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (hotel_settings && hotel_settings.is_page_search) {
            return;
        }

        hbPriceSlider();
        hbRating();
        hbRoomType();
        hbFilterSelection();
        clearFieldFilter();
        removeSelection();
        hbFilter();
    });
})();
