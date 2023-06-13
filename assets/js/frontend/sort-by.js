const roomSortBy = () => {
    const sortByWrapper = document.querySelector('.sort-by-wrapper');

    if (!sortByWrapper) {
        return;
    }

    const showNumber = sortByWrapper.querySelector('.show-number');
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

document.addEventListener('DOMContentLoaded', () => {
    roomSortBy();
});
