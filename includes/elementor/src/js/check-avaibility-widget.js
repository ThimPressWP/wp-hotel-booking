(function() {
    'use strict';

    class HotelBookingSearchHandler extends elementorModules.frontend.handlers.Base {
        getDefaultSettings() {
            return {
                selectors: {
                    toggleButton: '.hb-toggle-button',
                    searchForm: '.hb-search-form',
                    container: '.hotel-booking-search'
                }
            };
        }

        getDefaultElements() {
            const selectors = this.getSettings('selectors');
            const element = this.$element[0];
            
            return {
                toggleButton: element.querySelector(selectors.toggleButton),
                searchForm: element.querySelector(selectors.searchForm),
                container: element.querySelector(selectors.container)
            };
        }

        bindEvents() {
            const { toggleButton } = this.elements;
            
            if (toggleButton) {
                toggleButton.addEventListener('click', this.onToggleClick.bind(this));
            }
        }

        onToggleClick(event) {
            event.preventDefault();
            
            const { toggleButton, searchForm } = this.elements;
            const toggleText = toggleButton.dataset.toggleText;
            const closeText = toggleButton.dataset.closeText;
            const isActive = toggleButton.classList.contains('active');

            if (isActive) {
                searchForm.classList.remove('show');
                toggleButton.textContent = toggleText;
                toggleButton.classList.remove('active');
            } else {
                searchForm.classList.add('show');
                toggleButton.textContent = closeText;
                toggleButton.classList.add('active');
                if ( searchForm.style.display=='none' ) {
                    searchForm.style.display = 'block';
                }
            }
        }
    }

    // Register handler
    window.addEventListener('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction(
            'frontend/element_ready/wphb-room-check-avaibility.default',
            ($scope) => {
                new HotelBookingSearchHandler({ $element: $scope });
            }
        );
    });

})();
