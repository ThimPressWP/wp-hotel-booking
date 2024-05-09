
jQuery( window ).on( 'elementor/frontend/init', () => {
	const addHandler = ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( window.ThimEkits.ThimSlider, { 
			$element,
		} );
	};
	elementorFrontend.hooks.addAction( 'frontend/element_ready/list-room.default', addHandler );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/room-related.default', addHandler );
	elementorFrontend.hooks.addAction(
		'frontend/element_ready/list-results-room.default',
		( $element ) => {
			thimEkitLoadMoreArchive( '.hb-room-archive', '.hb-room-archive__inner' );
		},
	);
	elementorFrontend.hooks.addAction(
		'frontend/element_ready/wphb-archive-room.default',
		( $element ) => {
			thimEkitLoadMoreArchive( '.hb-room-archive', '.hb-room-archive__inner' );
		},
	);
} )

function thimEkitLoadMoreArchive( parent = '.hb-room-archive', inner = '.hb-room-archive__inner' ) {
	const archive = document.querySelector( parent );

	if (! archive) {
		return;
	}

	const innerHtml = archive.querySelector( inner );
	const loadMoreButton = archive.querySelector( '.thim-ekits-archive__loadmore-button' );
	const elSpinner = archive.querySelector( '.thim-ekits-archive__loadmore-spinner' );
	const loadMoreBtn = archive.querySelector( '.thim-ekits-archive__loadmore-btn' );
	const loadMoreData = archive.querySelector( '.thim-ekits-archive__loadmore-data' );

	if (! loadMoreData) {
		return;
	}

	let isLoading = false;

	let currentPage = loadMoreData.dataset.page ? parseInt( loadMoreData.dataset.page ) : 1;
	const maxPage = loadMoreData.dataset.maxPage ? parseInt( loadMoreData.dataset.maxPage ) : 1;

	const isInfinityScroll = loadMoreData.dataset.infinityScroll ? parseInt( loadMoreData.dataset.infinityScroll ) : false;

	const beforeLoading = () => {
		isLoading = true;

		elSpinner.classList.remove( 'hide' );

		if (loadMoreBtn) {
			loadMoreBtn.disabled = true;
		}
	};

	const afterLoading = () => {
		isLoading = false;

		elSpinner.classList.add( 'hide' );

		if (loadMoreBtn) {
			loadMoreBtn.disabled = false;
		}
	};

	const handleInfiniteScroll = () => {
		// Use observer to check if the element is visible in the viewport
		const observer = new IntersectionObserver( ( entries ) => {
			entries.forEach( ( entry ) => {
				if (isLoading) {
					return;
				}

				if (entry.isIntersecting) {
					handlePostsQuery();
				}
			} );
		} );

		observer.observe( loadMoreData );
	};

	const handlePostsQuery = () => {
		const nextPageUrl = loadMoreData.dataset.nextPage;

		if (currentPage >= maxPage) {
			return;
		}

		beforeLoading();

		currentPage++;

		return fetch( nextPageUrl )
			.then( ( response ) => response.text() )
			.then( ( html ) => {
				const parser = new DOMParser();
				const doc = parser.parseFromString( html, 'text/html' );
				const nextData = doc.querySelector( '.thim-ekits-archive__loadmore-data' );
				const nextPosts = doc.querySelector( inner );

				loadMoreData.dataset.page = nextData.dataset.page;
				loadMoreData.dataset.nextPage = nextData.dataset.nextPage;

				innerHtml.insertAdjacentHTML( 'beforeend', nextPosts.innerHTML );

				if (! nextData.dataset.nextPage || currentPage >= maxPage) {
					loadMoreButton && loadMoreButton.remove();
				}

				afterLoading();
			} );
	};

	loadMoreBtn && loadMoreBtn.addEventListener( 'click', ( e ) => {
		e.preventDefault();

		if (isLoading) {
			return;
		}

		if (currentPage >= maxPage && loadMoreButton) {
			loadMoreButton.remove();
			return;
		}

		handlePostsQuery();
	} );

	if (isInfinityScroll) {
		handleInfiniteScroll();
	}
}