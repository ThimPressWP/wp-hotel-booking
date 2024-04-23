(function () {
    const openFomBtn = document.querySelector('#hb-room-add-new-review');
    const closeFormBtn = document.querySelector('.close-form-btn');

    const reviewFormPopup = document.querySelector('#hb-room-review-form-popup');
    let background, reviewForm
    let imageData = [];

    const init = () => {
        if (!reviewFormPopup) {
            return;
        }

        background = reviewFormPopup.querySelector('.bg-overlay');
        reviewForm = reviewFormPopup.querySelector('#hb-room-submit-review-form');

        openForm();
        closeForm();
        ratingReview();
        uploadImages();
        submitReview();
        sortReview();
    };
    jQuery(window).on('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction('frontend/element_ready/tours-widget-comment.default', init);
    });

    const sortReview = () => {
        const sortByNode = document.querySelector('#hb-room-sort-by');

        if (!sortByNode) {
            return;
        }

        document.addEventListener('click', function (event) {
            const target = event.target;
            if (target.classList.contains('toggle') && target.closest('.hb-room-commentlist-sort-filter')) {
                sortByNode.classList.add('is-open')
            } else if (!target.classList.contains('hb-sort-by-option')) {
                sortByNode.classList.remove('is-open')
            }
        });
    }

    const openForm = () => {
        if (!openFomBtn) {
            return;
        }


        openFomBtn.addEventListener('click', function () {
            background.classList.add('active');
            reviewForm.classList.add('active');
        });
    }

    const closeForm = () => {
        closeFormBtn.addEventListener('click', function () {
            background.classList.remove('active');
            reviewForm.classList.remove('active');
        })

        document.addEventListener('click', function (event) {
            const target = event.target;

            if (target.getAttribute('id') === 'hb-room-add-new-review') {
                return;
            }

            const formNode = target.closest('#hb-room-submit-review-form');

            if (!formNode && target.getAttribute('id') !== 'hb-room-submit-review-form') {
                background.classList.remove('active');
                reviewForm.classList.remove('active');
            }
        });
    }

    const ratingReview = () => {
        const reviewRatingNode = reviewForm.querySelector('#hb-room-submit-review-form input[name="review-rating"]');

        if (!reviewRatingNode) {
            return;
        }

        const ratingNode = document.querySelector('#hb-room-submit-review-form .rating-star');
        const ratingStarItems = ratingNode.querySelectorAll('#hb-room-submit-review-form .rating-star-item');

        [...ratingStarItems].map(ratingStarItem => {
            ratingStarItem.addEventListener('mouseover', function (event) {
                const actived = this.closest('.rating-star.active');
                if (actived) {
                    return;
                }

                ratingNode.classList.add('selected');
                ratingStarItem.classList.add('selected');
            });

            ratingStarItem.addEventListener('mouseleave', function (event) {
                const actived = this.closest('.rating-star.active');
                if (actived) {
                    return;
                }

                ratingNode.classList.remove('selected');
                ratingStarItem.classList.remove('selected');
            });

            ratingStarItem.addEventListener('click', function (event) {
                event.preventDefault();
                reviewRatingNode.value = this.getAttribute('data-star-rating');

                const activeRatingStarNode = document.querySelector('#hb-room-submit-review-form .rating-star-item.active');

                if (activeRatingStarNode) {
                    activeRatingStarNode.classList.remove('active');
                }

                ratingNode.classList.remove('selected');
                ratingNode.classList.add('active');
                ratingStarItem.classList.remove('selected');
                ratingStarItem.classList.add('active');
            });
        });
    }

    const uploadImages = () => {
        const galleryReview = document.querySelector('.hb-gallery-review');
        if (!galleryReview) {
            return;
        }

        if (typeof HB_ROOM_REVIEW_GALLERY === 'undefined') {
            return;
        }

        const roomId = galleryReview.getAttribute('data-room-id');
        const uploadImage = galleryReview.querySelector('#hb-room-review-image');
        const preview = galleryReview.querySelector('.gallery-preview');
        const reviewNotice = galleryReview.querySelector('.review-notice');

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
                } else if (uploadImage.files[i].size && uploadImage.files[i].size > parseInt(HB_ROOM_REVIEW_GALLERY.max_file_size) * 1024) {
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
            const notUploadedPreview = galleryReview.querySelectorAll('.cr-upload-images-preview .hb-upload-images-containers:not(.hb-upload-ok)');
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

    const submitReview = () => {
        const submitBtnNode = reviewFormPopup.querySelector('footer button');
        submitBtnNode.addEventListener('click', async function () {
            submitBtnNode.disabled = true;
            const spinnerNode = reviewFormPopup.querySelector('.hb-room-spinner');
            spinnerNode.classList.add('active');
            const noticeNode = reviewFormPopup.querySelector('p.notice');
            noticeNode.innerHTML = '';
            const ratingNode = reviewFormPopup.querySelector('input[name="review-rating"]');
            const contentNode = reviewFormPopup.querySelector('#review-content');
            const titleNode = reviewFormPopup.querySelector('#review-title');

            let rating = '';
            if (ratingNode) {
                rating = ratingNode.value;
            }

            const content = contentNode.value;
            const title = titleNode.value;

            const base64Images = await handleBase64();

            for (let i = 0; i < base64Images.length; i++) {
                base64Images[i].name = imageData[i].name;
                base64Images[i].type = imageData[i].type;
            }

            const roomId = reviewForm.getAttribute('data-room-id');

            let data = {
                "room_id": roomId
            }

            if (rating) {
                data = {...data, rating};
            }

            if (title) {
                data = {...data, title};
            }

            if (content) {
                data = {...data, content};
            }

            if (base64Images) {
                data = {...data, "base64_images": base64Images};
            }

            wp.apiFetch({
                path: '/hb-room/v1/update-review', method: 'POST', data,
            }).then((res) => {
                if (res.status === 'success') {
                    noticeNode.classList.remove('failed');
                    noticeNode.classList.add('success');

                    if (res.msg) {
                        noticeNode.innerHTML = res.msg;
                    }

                    if (res.data.redirect_url) {
                        window.location.href = res.data.redirect_url;
                        location.reload();
                        return false;
                    }
                } else {
                    noticeNode.classList.remove('success');
                    noticeNode.classList.add('failed');

                    if (res.msg) {
                        noticeNode.innerHTML = res.msg;
                    }
                }
            }).catch((err) => {
                console.log(err);
            }).finally(() => {
                submitBtnNode.disabled = false;
                spinnerNode.classList.remove('active');
            });
        });
    }

    const toBase64 = (file) => {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => resolve(reader.result);
            reader.onerror = error => reject(error);
        });
    };

    const handleBase64 = async () => {
        const filePathsPromises = [];
        [...imageData].map(file => {
            filePathsPromises.push(toBase64(file));
        });
        const filePaths = await Promise.all(filePathsPromises);
        return filePaths.map((base64File) => ({"base64": base64File}));
    }

    init();
})();

