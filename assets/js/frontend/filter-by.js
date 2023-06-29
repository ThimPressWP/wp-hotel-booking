const getParam = (param) => {
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);

    return urlParams.get(param);
}

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
                if(value.length){
                    url.searchParams.set('rating', value);
                }else{
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

                if(value.length){
                    url.searchParams.set('room_type', value);
                }else{
                    url.searchParams.delete('room_type', value);
                }

                window.location.href = url;
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (hotel_settings && hotel_settings.is_page_search) {
        return;
    }

    hbPriceSlider();
    hbRating();
    hbRoomType();
});
