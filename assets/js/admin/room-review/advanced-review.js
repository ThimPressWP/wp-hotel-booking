const hbAdvancedReview = () => {
    const {__} = wp.i18n;

    const advancedReviewNode = document.querySelector('input[name="tp_hotel_booking_enable_advanced_review"]');

    if (!advancedReviewNode) {
        return;
    }

    const maxImageNode = document.querySelector('input[name="tp_hotel_booking_max_review_image_number"]');
    const maxFileSizeNode = document.querySelector('input[name="tp_hotel_booking_max_review_image_file_size"]');

    maxImageNode.required = true;
    maxFileSizeNode.required = true;
}

export default hbAdvancedReview;
