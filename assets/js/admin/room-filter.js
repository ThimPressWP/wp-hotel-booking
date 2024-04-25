(function ($) {
    $(document).ready(function () {
        const minPrice = document.querySelector('input[name="tp_hotel_booking_filter_price_min"]');
        const maxPrice = document.querySelector('input[name="tp_hotel_booking_filter_price_max"]');
        const stepPrice = document.querySelector('input[name="tp_hotel_booking_filter_price_step"]');

        if (!minPrice || !maxPrice || !stepPrice) {
            return;
        }

        minPrice.required = true;
        maxPrice.required = true;
        stepPrice.required = true;

        const setMaxPriceField = () => {
            const minPriceValue = parseInt(minPrice.value);
            if (!isNaN(minPriceValue)) {
                maxPrice.setAttribute('min', minPriceValue);
            }
        }

        const setMinPriceField = () => {
            const maxPriceValue = parseInt(maxPrice.value);
            if (!isNaN(maxPriceValue)) {
                minPrice.setAttribute('max', maxPriceValue);
            }
        }

        minPrice.addEventListener('input', function () {
            setMaxPriceField();
        });
        maxPrice.addEventListener('input', function () {
            setMinPriceField();
        });
    });
}(jQuery));

