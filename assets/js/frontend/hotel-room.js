const hotelRoom = () => {
    const sortByWrapper = document.querySelector('.sort-by-wrapper');

    if (!sortByWrapper) {
        return;
    }

    const formSearch = document.querySelector('#hb-form-search-page');

    if (formSearch) {
        return;
    }

    const list = sortByWrapper.querySelector('ul');
    const listOptions = list.querySelectorAll('li');

    [...listOptions].map(element => {
        element.addEventListener('click', function (event) {
            const url = new URL(window.location.href);

            url.searchParams.set('sort_by', element.getAttribute('data-value'));

            window.location.href = url;
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    hotelRoom();
});
