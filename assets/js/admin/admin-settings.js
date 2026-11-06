/**
 * WP Hotel Booking - Modern Admin Settings UI Controller
 *
 * Features:
 * - Instant Tab & Sub-section switching without page reload (0ms)
 * - URL state synchronization via History API (replaceState / popstate)
 * - Sticky Header live breadcrumb & Unsaved Changes (dirty state) tracking
 * - AJAX Save Settings with TinyMCE + External Links sync & Toast Notification
 * - Quick Search / Instant Filter across all tabs with per-tab match badges
 * - Live Payment Gateway status indicator updates
 */
( function () {
	'use strict';

	const initAdminSettings = () => {
		const form = document.getElementById( 'wphb-admin-settings-form' );
		if ( ! form ) {
			return;
		}
		form.noValidate = true;

		const navTabs = Array.from( form.querySelectorAll( '.wphb-nav-tab[data-tab]' ) );
		const tabPanels = Array.from( form.querySelectorAll( '.wphb-settings-tab-panel[data-tab-panel]' ) );
		const subTabLinks = Array.from( form.querySelectorAll( '.wphb-sub-tab-link[data-tab][data-section]' ) );
		const currentTabTitleEl = document.getElementById( 'wphb-current-tab-title' );
		const unsavedBadge = document.getElementById( 'wphb-unsaved-badge' );
		const saveButtons = Array.from( form.querySelectorAll( '.wphb-btn-save' ) );
		const searchInput = document.getElementById( 'wphb-settings-search-input' );
		const searchClearBtn = document.getElementById( 'wphb-settings-search-clear' );
		const searchBanner = document.getElementById( 'wphb-search-status-banner' );
		const searchStatusText = document.getElementById( 'wphb-search-status-text' );
		const searchResetBtn = document.getElementById( 'wphb-search-reset-btn' );
		const toast = document.getElementById( 'wphb-settings-toast' );
		const toastMessage = document.getElementById( 'wphb-settings-toast-message' );

		let isDirty = false;
		let isSaving = false;
		let toastTimer = null;

		/**
		 * Get active tab slug.
		 */
		const getActiveTabSlug = () => {
			const activeTab = form.querySelector( '.wphb-nav-tab.is-active' );
			return activeTab ? activeTab.dataset.tab : ( navTabs[ 0 ] ? navTabs[ 0 ].dataset.tab : 'general' );
		};

		/**
		 * Get active sub-section slug for a given tab.
		 */
		const getActiveSectionSlug = ( tabSlug ) => {
			const panel = form.querySelector( `.wphb-settings-tab-panel[data-tab-panel="${ tabSlug }"]` );
			if ( ! panel ) {
				return '';
			}
			const activeSubTab = panel.querySelector( '.wphb-sub-tab-link.is-active' );
			return activeSubTab ? activeSubTab.dataset.section : '';
		};

		/**
		 * Update sticky header breadcrumb title.
		 */
		const updateBreadcrumb = ( tabSlug ) => {
			if ( ! currentTabTitleEl ) {
				return;
			}
			const activeNav = form.querySelector( `.wphb-nav-tab[data-tab="${ tabSlug }"]` );
			const tabTitle = activeNav ? activeNav.dataset.tabTitle : tabSlug;
			const panel = form.querySelector( `.wphb-settings-tab-panel[data-tab-panel="${ tabSlug }"]` );
			const activeSub = panel ? panel.querySelector( '.wphb-sub-tab-link.is-active' ) : null;

			if ( activeSub ) {
				const subTitle = activeSub.textContent.trim();
				currentTabTitleEl.textContent = `${ tabTitle } › ${ subTitle }`;
			} else {
				currentTabTitleEl.textContent = tabTitle;
			}
		};

		/**
		 * Sync browser URL with current tab & section without reloading.
		 */
		const syncUrl = ( tabSlug, sectionSlug = '', push = false ) => {
			try {
				const url = new URL( window.location.href );
				url.searchParams.set( 'page', 'tp_hotel_booking_settings' );
				url.searchParams.set( 'tab', tabSlug );
				if ( sectionSlug ) {
					url.searchParams.set( 'section', sectionSlug );
				} else {
					url.searchParams.delete( 'section' );
				}
				if ( push ) {
					window.history.pushState( { tab: tabSlug, section: sectionSlug }, '', url.toString() );
				} else {
					window.history.replaceState( { tab: tabSlug, section: sectionSlug }, '', url.toString() );
				}
			} catch ( err ) {
				// Ignore URL errors in older environments
			}
		};

		/**
		 * Switch active main tab.
		 */
		const switchTab = ( tabSlug, updateHistory = true ) => {
			navTabs.forEach( ( tab ) => {
				const match = tab.dataset.tab === tabSlug;
				tab.classList.toggle( 'nav-tab-active', match );
				tab.classList.toggle( 'is-active', match );
			} );

			tabPanels.forEach( ( panel ) => {
				const match = panel.dataset.tabPanel === tabSlug;
				panel.classList.toggle( 'is-active', match );
			} );

			const sectionSlug = getActiveSectionSlug( tabSlug );
			updateBreadcrumb( tabSlug );

			if ( updateHistory ) {
				syncUrl( tabSlug, sectionSlug, false );
			}
		};

		/**
		 * Switch active sub-section inside a tab.
		 */
		const switchSection = ( tabSlug, sectionSlug, updateHistory = true ) => {
			const panel = form.querySelector( `.wphb-settings-tab-panel[data-tab-panel="${ tabSlug }"]` );
			if ( ! panel ) {
				return;
			}

			const subLinks = Array.from( panel.querySelectorAll( '.wphb-sub-tab-link[data-section]' ) );
			const sectionPanels = Array.from( panel.querySelectorAll( '.wphb-settings-section-panel[data-section]' ) );

			subLinks.forEach( ( link ) => {
				const match = link.dataset.section === sectionSlug;
				link.classList.toggle( 'current', match );
				link.classList.toggle( 'is-active', match );
			} );

			sectionPanels.forEach( ( secPanel ) => {
				const match = secPanel.dataset.section === sectionSlug;
				secPanel.classList.toggle( 'is-active', match );
			} );

			updateBreadcrumb( tabSlug );

			if ( updateHistory ) {
				syncUrl( tabSlug, sectionSlug, false );
			}
		};

		// Bind main tab clicks
		navTabs.forEach( ( tab ) => {
			tab.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				const targetTab = tab.dataset.tab;
				if ( targetTab ) {
					switchTab( targetTab, true );
				}
			} );
		} );

		// Bind sub-tab clicks
		subTabLinks.forEach( ( link ) => {
			link.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				const targetTab = link.dataset.tab;
				const targetSection = link.dataset.section;
				if ( targetTab && targetSection ) {
					if ( getActiveTabSlug() !== targetTab ) {
						switchTab( targetTab, false );
					}
					switchSection( targetTab, targetSection, true );
				}
			} );
		} );

		// Handle browser back/forward navigation
		window.addEventListener( 'popstate', () => {
			const params = new URLSearchParams( window.location.search );
			const tabParam = params.get( 'tab' ) || ( navTabs[ 0 ] ? navTabs[ 0 ].dataset.tab : 'general' );
			const sectionParam = params.get( 'section' ) || '';

			switchTab( tabParam, false );
			if ( sectionParam ) {
				switchSection( tabParam, sectionParam, false );
			}
		} );

		// Initialize breadcrumb on load
		updateBreadcrumb( getActiveTabSlug() );

		/**
		 * Dirty State (Unsaved Changes) Management
		 */
		const markDirty = () => {
			if ( isSaving ) {
				return;
			}
			isDirty = true;
			if ( unsavedBadge ) {
				unsavedBadge.hidden = false;
			}
			saveButtons.forEach( ( btn ) => btn.classList.add( 'is-dirty' ) );
		};

		const clearDirty = () => {
			isDirty = false;
			if ( unsavedBadge ) {
				unsavedBadge.hidden = true;
			}
			saveButtons.forEach( ( btn ) => btn.classList.remove( 'is-dirty' ) );
		};

		const isIgnoredTarget = ( target ) => {
			if ( ! target ) {
				return false;
			}
			return target === searchInput || Boolean( target.closest && target.closest( '.wphb-live-preview-wrapper, #wphb-test-email-modal-overlay, [data-wphb-ignore-dirty]' ) );
		};

		form.addEventListener( 'input', ( e ) => {
			if ( isIgnoredTarget( e.target ) ) {
				return;
			}
			markDirty();
		} );

		form.addEventListener( 'change', ( e ) => {
			if ( isIgnoredTarget( e.target ) ) {
				return;
			}
			markDirty();
		} );

		if ( typeof window.jQuery !== 'undefined' ) {
			window.jQuery( form ).on( 'change select2:select select2:unselect', 'select', ( e ) => {
				if ( isIgnoredTarget( e.target ) ) {
					return;
				}
				markDirty();
			} );
		}

		window.addEventListener( 'beforeunload', ( e ) => {
			if ( isDirty && ! isSaving ) {
				e.preventDefault();
				e.returnValue = '';
			}
		} );

		/**
		 * Live Payment Gateway Status Badge Toggle
		 */
		const gatewayToggles = Array.from( form.querySelectorAll( '.wphb-gateway-enable-toggle[data-gateway]' ) );
		gatewayToggles.forEach( ( toggle ) => {
			toggle.addEventListener( 'change', () => {
				const gw = toggle.dataset.gateway;
				const enabled = toggle.checked;

				const dot = form.querySelector( `.wphb-gateway-status-dot[data-gateway="${ gw }"]` );
				if ( dot ) {
					dot.classList.toggle( 'is-enabled', enabled );
					dot.classList.toggle( 'is-disabled', ! enabled );
					dot.title = enabled ? 'Enabled' : 'Disabled';
				}

				const badge = form.querySelector( `.wphb-gateway-badge[data-gateway-badge="${ gw }"]` );
				if ( badge ) {
					badge.classList.toggle( 'is-enabled', enabled );
					badge.classList.toggle( 'is-disabled', ! enabled );
					badge.textContent = enabled ? 'Active' : 'Inactive';
				}
			} );
		} );

		/**
		 * Quick Search / Instant Filter across all tabs
		 */
		const applySearchFilter = ( rawQuery ) => {
			const query = ( rawQuery || '' ).trim().toLowerCase();
			const isSearching = query.length > 0;

			if ( searchClearBtn ) {
				searchClearBtn.hidden = ! isSearching;
			}

			form.classList.toggle( 'is-searching', isSearching );

			let totalMatches = 0;
			let firstMatchedTab = null;

			tabPanels.forEach( ( panel ) => {
				const tabSlug = panel.dataset.tabPanel;
				const rows = Array.from( panel.querySelectorAll( 'tr.wphb-setting-row, .wphb-settings-table tr' ) );
				let tabMatches = 0;

				rows.forEach( ( row ) => {
					if ( row.classList.contains( 'header-row' ) || row.classList.contains( 'wphb-sample-row' ) || row.classList.contains( 'wphb-single-external-link' ) ) {
						return;
					}
					if ( ! isSearching ) {
						row.classList.remove( 'is-search-hidden', 'is-search-match' );
						return;
					}
					const haystack = ( ( row.dataset.searchText || '' ) + ' ' + row.textContent ).toLowerCase();
					const matched = haystack.includes( query );
					row.classList.toggle( 'is-search-hidden', ! matched );
					row.classList.toggle( 'is-search-match', matched );
					if ( matched ) {
						tabMatches++;
						totalMatches++;
					}
				} );

				// Hide cards that have no visible setting rows during search
				const cards = Array.from( panel.querySelectorAll( '.wphb-settings-card' ) );
				cards.forEach( ( card ) => {
					if ( ! isSearching ) {
						card.classList.remove( 'is-search-hidden' );
						return;
					}
					const visibleRows = card.querySelectorAll( 'tr:not(.is-search-hidden):not(.header-row):not(.wphb-sample-row)' );
					card.classList.toggle( 'is-search-hidden', visibleRows.length === 0 );
				} );

				// Update sidebar tab match badge
				const badge = form.querySelector( `.wphb-nav-tab__badge[data-tab-match-count="${ tabSlug }"]` );
				if ( badge ) {
					if ( isSearching && tabMatches > 0 ) {
						badge.textContent = String( tabMatches );
						badge.hidden = false;
						if ( ! firstMatchedTab ) {
							firstMatchedTab = tabSlug;
						}
					} else {
						badge.hidden = true;
					}
				}
			} );

			if ( searchBanner && searchStatusText ) {
				if ( isSearching ) {
					searchBanner.hidden = false;
					searchStatusText.textContent = totalMatches > 0
						? `Found ${ totalMatches } matching setting(s) for "${ rawQuery.trim() }"`
						: `No settings found matching "${ rawQuery.trim() }"`;
				} else {
					searchBanner.hidden = true;
				}
			}
		};

		if ( searchInput ) {
			searchInput.addEventListener( 'input', () => {
				applySearchFilter( searchInput.value );
			} );

			searchInput.addEventListener( 'keydown', ( e ) => {
				if ( e.key === 'Escape' ) {
					searchInput.value = '';
					applySearchFilter( '' );
					searchInput.blur();
				} else if ( e.key === 'Enter' ) {
					e.preventDefault();
				}
			} );
		}

		if ( searchClearBtn ) {
			searchClearBtn.addEventListener( 'click', () => {
				if ( searchInput ) {
					searchInput.value = '';
					applySearchFilter( '' );
					searchInput.focus();
				}
			} );
		}

		if ( searchResetBtn ) {
			searchResetBtn.addEventListener( 'click', () => {
				if ( searchInput ) {
					searchInput.value = '';
					applySearchFilter( '' );
				}
			} );
		}

		// Keyboard shortcut Ctrl+K / Cmd+K to focus quick search
		document.addEventListener( 'keydown', ( e ) => {
			if ( ( e.ctrlKey || e.metaKey ) && e.key.toLowerCase() === 'k' ) {
				if ( searchInput ) {
					e.preventDefault();
					searchInput.focus();
					searchInput.select();
				}
			}
		} );

		/**
		 * Show Floating Toast Notification
		 */
		const showToast = ( message, isError = false ) => {
			if ( ! toast || ! toastMessage ) {
				return;
			}
			if ( toastTimer ) {
				clearTimeout( toastTimer );
			}
			toastMessage.textContent = message;
			toast.classList.toggle( 'is-error', isError );
			toast.hidden = false;
			requestAnimationFrame( () => {
				toast.classList.add( 'is-visible' );
			} );

			toastTimer = setTimeout( () => {
				toast.classList.remove( 'is-visible' );
				setTimeout( () => {
					toast.hidden = true;
				}, 250 );
			}, 3500 );
		};

		/**
		 * Reveal tab/section if an invalid input is inside a hidden panel before validity reporting.
		 */
		const ensureValidOrFocusInvalid = () => {
			const invalidEl = form.querySelector( ':invalid' );
			if ( ! invalidEl ) {
				return true;
			}
			const parentTabPanel = invalidEl.closest( '.wphb-settings-tab-panel[data-tab-panel]' );
			if ( parentTabPanel ) {
				const tabSlug = parentTabPanel.dataset.tabPanel;
				switchTab( tabSlug, true );
				const parentSectionPanel = invalidEl.closest( '.wphb-settings-section-panel[data-section]' );
				if ( parentSectionPanel ) {
					switchSection( tabSlug, parentSectionPanel.dataset.section, true );
				}
			}
			if ( typeof invalidEl.reportValidity === 'function' ) {
				invalidEl.reportValidity();
			}
			invalidEl.focus();
			return false;
		};

		/**
		 * Handle Form Submit via AJAX (with fallback to normal POST)
		 */
		form.addEventListener( 'submit', ( e ) => {
			// Sync external link table & TinyMCE editors first
			if ( typeof window.wphbSyncExternalLinkSettings === 'function' ) {
				window.wphbSyncExternalLinkSettings();
			}
			if ( typeof window.tinyMCE !== 'undefined' && typeof window.tinyMCE.triggerSave === 'function' ) {
				window.tinyMCE.triggerSave();
			}

			if ( ! ensureValidOrFocusInvalid() ) {
				e.preventDefault();
				return;
			}

			const ajaxEndpoint = ( typeof window.ajaxurl !== 'undefined' && window.ajaxurl )
				? window.ajaxurl
				: ( typeof window.hotel_settings !== 'undefined' && window.hotel_settings.ajax ? window.hotel_settings.ajax : '' );

			if ( ! ajaxEndpoint || typeof window.fetch !== 'function' ) {
				// Fallback to standard POST submit
				isSaving = true;
				return;
			}

			e.preventDefault();
			if ( isSaving ) {
				return;
			}

			isSaving = true;
			saveButtons.forEach( ( btn ) => {
				btn.classList.add( 'is-saving' );
				btn.disabled = true;
				const labelEl = btn.querySelector( '.wphb-btn-save__label' );
				if ( labelEl && labelEl.dataset.savingLabel ) {
					labelEl.textContent = labelEl.dataset.savingLabel;
				}
			} );

			const formData = new FormData( form );
			formData.set( 'action', 'wphb_save_admin_settings' );
			formData.set( 'wphb_save_all_tabs', '1' );

			window.fetch( ajaxEndpoint, {
				method: 'POST',
				credentials: 'same-origin',
				body: formData,
			} )
				.then( ( res ) => res.json() )
				.then( ( response ) => {
					isSaving = false;
					if ( response && response.success ) {
						clearDirty();
						saveButtons.forEach( ( btn ) => {
							btn.classList.remove( 'is-saving' );
							btn.classList.add( 'is-saved' );
							btn.disabled = false;
							const labelEl = btn.querySelector( '.wphb-btn-save__label' );
							if ( labelEl && labelEl.dataset.savedLabel ) {
								labelEl.textContent = labelEl.dataset.savedLabel;
							}
						} );

						showToast( ( response.data && response.data.message ) ? response.data.message : 'Settings saved successfully.', false );

						setTimeout( () => {
							saveButtons.forEach( ( btn ) => {
								btn.classList.remove( 'is-saved' );
								const labelEl = btn.querySelector( '.wphb-btn-save__label' );
								if ( labelEl && labelEl.dataset.defaultLabel ) {
									labelEl.textContent = labelEl.dataset.defaultLabel;
								}
							} );
						}, 1800 );
					} else {
						saveButtons.forEach( ( btn ) => {
							btn.classList.remove( 'is-saving' );
							btn.disabled = false;
							const labelEl = btn.querySelector( '.wphb-btn-save__label' );
							if ( labelEl && labelEl.dataset.defaultLabel ) {
								labelEl.textContent = labelEl.dataset.defaultLabel;
							}
						} );
						const errMsg = ( response && response.data && response.data.message )
							? response.data.message
							: 'Failed to save settings. Please try again.';
						showToast( errMsg, true );
					}
				} )
				.catch( () => {
					// Fallback to native form submission if AJAX fails
					isSaving = true;
					form.submit();
				} );
		} );
	};

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initAdminSettings );
	} else {
		initAdminSettings();
	}
} )();

