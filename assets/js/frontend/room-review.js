(function () {
    const reviewForm = document.querySelector('#review_form');
    let reviewGallery;
    let postId;

    let imageData = [];

    const init = () => {
        if (!reviewForm) {
            return;
        }

        reviewGallery = reviewForm.querySelector('.review-gallery');
        if (!reviewGallery) {
            return;
        }

        postId = reviewForm.querySelector('#comment_post_ID').getAttribute('value');

        uploadImages();
    };


    const uploadImages = () => {
        if (!HB_ROOM_REVIEW_GALLERY) {
            return;
        }

        const uploadImage = reviewGallery.querySelector('#hb-review-image');
        const preview = reviewGallery.querySelector('.gallery-preview');
        const reviewNotice = reviewGallery.querySelector('.review-notice');

        const maxImages = HB_ROOM_REVIEW_GALLERY.max_images || 0;

        uploadImage.addEventListener('change', function () {
            removeNotice();

            const count = uploadImage.files.length;
            let uploadedCount = 0;
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];

            //Validate image number
            if (count + uploadedCount > maxImages) {
                handleUploadError();
                displayNotice(HB_ROOM_REVIEW_GALLERY.max_image_error, 'error');
                return;
            }

            for (let i = 0; i < count; i++) {
                if (!allowedTypes.includes(uploadImage.files[i].type)) {
                    handleUploadError();
                    displayNotice(HB_ROOM_REVIEW_GALLERY.file_type_error, 'error');
                    return;
                } else if (uploadImage.files[i].size && uploadImage.files[i].size > HB_ROOM_REVIEW_GALLERY.max_file_size) {
                    handleUploadError();
                    displayNotice(HB_ROOM_REVIEW_GALLERY.max_file_size_error, 'error');
                    return;
                }
            }

            let previewItemHtml = '';
            [...uploadImage.files].map(file => {
                const previewImage = window.URL.createObjectURL(file);
                previewItemHtml += `<div class="preview-item"><img src="${previewImage}" alt="#preview"></div>`
            });

            if (previewItemHtml) {
                preview.innerHTML = previewItemHtml;
            }

            imageData = uploadImage.files;
        });

        const handleUploadError = () => {
            const notUploadedPreview = reviewGallery.querySelectorAll('.cr-upload-images-preview .tour-upload-images-containers:not(.tour-upload-ok)');
            [...notUploadedPreview].map(el => {
                el.remove();
            });

            uploadImage.value = '';
        }

        const removeNotice = () => {
            reviewNotice.innerHTML = '';
        }

        const displayNotice = (message, status = 'success') => {
            if (status === 'success') {
                reviewNotice.classList.remove('error');
                reviewNotice.classList.add('success');
            } else {
                reviewNotice.classList.remove('success');
                reviewNotice.classList.add('error');
            }

            reviewNotice.innerHTML = message;
        }
    }

    // const submitReview = () => {
    //     const submitBtnNode = reviewFormPopup.querySelector('footer button');
    //     submitBtnNode.addEventListener('click', async function () {
    //         submitBtnNode.disabled = true;
    //         const spinnerNode = reviewFormPopup.querySelector('.tour-spinner');
    //         spinnerNode.classList.add('active');
    //         const noticeNode = reviewFormPopup.querySelector('p.notice');
    //         const ratingNode = reviewFormPopup.querySelector('input[name="review-rating"]');
    //         const contentNode = reviewFormPopup.querySelector('#review-content');
    //         const titleNode = reviewFormPopup.querySelector('#review-title');
    //
    //         const rating = ratingNode.value;
    //         const content = contentNode.value;
    //         const title = titleNode.value;
    //
    //         const base64Images = await handleBase64();
    //
    //         for (let i = 0; i < base64Images.length; i++) {
    //             base64Images[i].name = imageData[i].name;
    //             base64Images[i].type = imageData[i].type;
    //         }
    //
    //         const productId = reviewForm.getAttribute('data-product-id');
    //
    //         let data = {
    //             "product_id": productId
    //         }
    //
    //         if (rating) {
    //             data = {...data, rating};
    //         }
    //
    //         if (title) {
    //             data = {...data, title};
    //         }
    //
    //         if (content) {
    //             data = {...data, content};
    //         }
    //
    //         if (base64Images) {
    //             data = {...data, "base64_images": base64Images};
    //         }
    //
    //         wp.apiFetch({
    //             path: '/travel-tour/v1/update-review', method: 'POST', data,
    //         }).then((res) => {
    //             if (res.status === 'success') {
    //                 noticeNode.classList.remove('failed');
    //                 noticeNode.classList.add('success');
    //
    //                 if (res.msg) {
    //                     noticeNode.innerHTML = res.msg;
    //                 }
    //
    //                 if (res.data.redirect_url) {
    //                     window.location.href = res.data.redirect_url;
    //                     location.reload();
    //                     return false;
    //                 }
    //             } else {
    //                 noticeNode.classList.remove('success');
    //                 noticeNode.classList.add('failed');
    //
    //                 if (res.msg) {
    //                     noticeNode.innerHTML = res.msg;
    //                 }
    //             }
    //         }).catch((err) => {
    //             console.log(err);
    //         }).finally(() => {
    //             submitBtnNode.disabled = false;
    //             spinnerNode.classList.remove('active');
    //         });
    //     });
    // }
    //
    // const toBase64 = (file) => {
    //     return new Promise((resolve, reject) => {
    //         const reader = new FileReader();
    //         reader.readAsDataURL(file);
    //         reader.onload = () => resolve(reader.result);
    //         reader.onerror = error => reject(error);
    //     });
    // };
    //
    // const handleBase64 = async () => {
    //     const filePathsPromises = [];
    //     [...imageData].map(file => {
    //         filePathsPromises.push(toBase64(file));
    //     });
    //     const filePaths = await Promise.all(filePathsPromises);
    //     return filePaths.map((base64File) => ({"base64": base64File}));
    // }

    init();
})();

