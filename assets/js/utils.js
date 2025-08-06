/**
 * Utils functions
 * Copy from learnpress
 *
 * @param url
 * @param data
 * @param functions
 * @since 1.0.0
 * @version 1.0.0
 */
const className = {
	hidden: 'wphb-hidden',
	loading: 'wphb-loading',
	targetAjax: 'wphb-target-ajax',
};
const fetchAPI = ( url, data = {}, functions = {} ) => {
	if ( 'function' === typeof functions.before ) {
		functions.before();
	}

	fetch( url, { method: 'GET', ...data } )
		.then( ( response ) => response.json() )
		.then( ( response ) => {
			if ( 'function' === typeof functions.success ) {
				functions.success( response );
			}
		} ).catch( ( err ) => {
			if ( 'function' === typeof functions.error ) {
				functions.error( err );
			}
		} )
		.finally( () => {
			if ( 'function' === typeof functions.completed ) {
				functions.completed();
			}
		} );
};

/**
 * Get current URL without params.
 *
 * @since 4.2.5.1
 */
const getCurrentURLNoParam = () => {
	let currentUrl = window.location.href;
	const hasParams = currentUrl.includes( '?' );
	if ( hasParams ) {
		currentUrl = currentUrl.split( '?' )[ 0 ];
	}

	return currentUrl;
};

const addQueryArgs = ( endpoint, args ) => {
	const url = new URL( endpoint );

	Object.keys( args ).forEach( ( arg ) => {
		url.searchParams.set( arg, args[ arg ] );
	} );

	return url;
};

/**
 * Listen element viewed.
 *
 * @param el
 * @param callback
 * @since 4.2.5.8
 */
const listenElementViewed = ( el, callback ) => {
	const observerSeeItem = new IntersectionObserver( function( entries ) {
		for ( const entry of entries ) {
			if ( entry.isIntersecting ) {
				callback( entry );
			}
		}
	} );

	observerSeeItem.observe( el );
};

/**
 * Listen element created.
 *
 * @param callback
 * @since 4.2.5.8
 */
const listenElementCreated = ( callback ) => {
	const observerCreateItem = new MutationObserver( function( mutations ) {
		mutations.forEach( function( mutation ) {
			if ( mutation.addedNodes ) {
				mutation.addedNodes.forEach( function( node ) {
					if ( node.nodeType === 1 ) {
						callback( node );
					}
				} );
			}
		} );
	} );

	observerCreateItem.observe( document, { childList: true, subtree: true } );
	// End.
};

/**
 * Listen element created.
 *
 * @param selector
 * @param callback
 * @since 4.2.7.1
 */
const onElementReady = ( selector, callback ) => {
	const element = document.querySelector( selector );
	if ( element ) {
		callback( element );
		return;
	}

	const observer = new MutationObserver( ( mutations, obs ) => {
		const element = document.querySelector( selector );
		if ( element ) {
			obs.disconnect();
			callback( element );
		}
	} );

	observer.observe( document.documentElement, {
		childList: true,
		subtree: true,
	} );
};

// status 0: hide, 1: show
const showHideEl = ( el, status = 0 ) => {
	if ( ! el ) {
		return;
	}

	if ( ! status ) {
		el.classList.add( className.hidden );
	} else {
		el.classList.remove( className.hidden );
	}
};

// status 0: hide, 1: show
const setLoadingEl = ( el, status ) => {
	if ( ! el ) {
		return;
	}

	if ( ! status ) {
		el.classList.remove( className.loading );
	} else {
		el.classList.add( className.loading );
	}
};

export {
	fetchAPI, addQueryArgs, getCurrentURLNoParam,
	listenElementViewed, listenElementCreated, onElementReady,
	showHideEl, setLoadingEl, className,
};
