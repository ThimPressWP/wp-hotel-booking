const roomSortBy = () => {
    const sortByWrapper = document.querySelector('.sort-by-wrapper');

    if (!sortByWrapper) {
        return;
    }

    const toggle = sortByWrapper.querySelector('.toggle');
    const list = sortByWrapper.querySelector('ul');
    const listOptions = list.querySelectorAll('li');

    [...listOptions].map(element => {
        element.addEventListener('click', function (event) {
            [...listOptions].map(element2 => {
                element2.classList.remove('active');
            });

            element.classList.add('active');
            toggle.innerHTML = element.innerHTML;
        });
    });
}

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
    roomSortBy();
    hotelRoom();
});
