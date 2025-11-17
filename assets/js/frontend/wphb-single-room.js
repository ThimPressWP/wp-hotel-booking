import flatpickr from 'flatpickr';
import tingle from 'tingle.js';
// import 'flatpickr/dist/flatpickr.min.css';
import * as utils from '../utils.js';

let modalCheckDates;
const className = {
	'elBtnsCalendarPricing': '.wphb-room-calendar-pricing-buttons',
}
let elHotelBookingRoom,
	elTmplDateAvailable,
	elAddToCart,
	elForm,
	roomCalendarPricing,
	roomPricing,
	elBtnsCalendarPricing,
	roomDateRangeSelector;
const toYmdLocal = ( date ) => {
	const z = ( n ) => ( '0' + n ).slice( -2 );
	return (
		date.getFullYear() +
		'/' +
		z( date.getMonth() + 1 ) +
		'/' +
		z( date.getDate() )
	);
};
const wphbRoomInitDatePicker = () => {
	elHotelBookingRoom = document.querySelector( '#hotel_booking_room_hidden' );
	if ( ! elHotelBookingRoom ) {
		return;
	}

	elTmplDateAvailable = elHotelBookingRoom.querySelector(
		'.wphb-room-tmpl-dates-available'
	);
	elAddToCart = elHotelBookingRoom.querySelector(
		'.wpdb-room-tmpl-add-to-cart'
	);
	elForm = elHotelBookingRoom.querySelector(
		'form[name=hb-search-single-room]'
	);
	const elDateCheckIn = elForm.querySelector( 'input[name="check_in_date"]' );
	const elDateCheckOut = elForm.querySelector( 'input[name="check_out_date"]' );
	const elDateRange = elForm.querySelector( 'input[name="select-date-range"]' );
	let datePickerCheckIn;
	let datePickerCheckOut;
	let dateMinCheckInCanBook;
	let dateMinCheckOutCanBook;
	const datesBlock = [];
	const dateNow = new Date();
	const dateTomorrow = new Date( dateNow.setDate( dateNow.getDate() + 1 + hotel_settings.min_booking_date ) );
	const minBookingDateNumber =
		hotel_settings.min_booking_date > 0
			? parseInt( hotel_settings.min_booking_date )
			: 1;

	if ( hotel_settings.block_dates ) {
		const dateTimeStampsBlock = hotel_settings.block_dates;

		if ( dateTimeStampsBlock ) {
			dateTimeStampsBlock.forEach( ( timeStamp ) => {
				const date = new Date( timeStamp * 1000 );
				const dateBlock = new Date(
					date.getFullYear(),
					date.getMonth(),
					date.getDate()
				);
				datesBlock.push( dateBlock );
			} );
		}

		// Get date min check in can book
		/*let dateMinCheckInCanBook;

		const getDateMinCheckInCanBook = ( dateCompare, datesBlock ) => {
			datesBlock.some( ( date ) => {
				console.log()
				if ( date.getTime() > dateCompare.getTime() ) {
					dateMinCheckInCanBook = dateCompare;
					return true;
				}

				dateCompare = new Date( dateCompare.setDate( dateCompare.getDate() + 1 ) );
				//dateDisableNear = getDateMinCheckInCanBook( dateCompare, datesBlock );
			} );
		};

		getDateMinCheckInCanBook( dateNow, datesBlock );*/
	} else {
		dateMinCheckInCanBook = dateNow;
		dateMinCheckOutCanBook = dateTomorrow;
	}

	const calculateDatesCheckOutDisable = ( dateSelected, dateCalendar ) => {
		if ( datesBlock.length === 0 ) {
			return dateCalendar <= dateSelected;
		}

		let dateDisableNear;
		datesBlock.some( ( date ) => {
			if ( date > dateSelected ) {
				dateDisableNear = date;
				return true;
			}
		} );

		if ( dateDisableNear ) {
			return (
				dateCalendar > dateDisableNear || dateCalendar <= dateSelected
			);
		}

		return dateCalendar <= dateSelected;
	};
	if ( elDateRange ) {
		let positionEle = parseInt( elDateRange.getAttribute( 'data-hidden' ) ) === 1 ? elDateCheckIn : null,
			roomId = elForm.querySelector('input[name="room-id"]').value;
			let minEndDate;
		roomDateRangeSelector = flatpickr( elDateRange, {
		    mode: "range",
		    dateFormat: 'Y/m/d',
		    minDate: 'today',
		    disable: datesBlock,
		    showMonths: 2,
		    positionElement: positionEle,
		    locale: {
		    	firstDayOfWeek: 1,
		    },
		    defaultDate: [elDateCheckIn.value, elDateCheckOut.value],
		    onDayCreate: function(dObj, dStr, fpInstance, dayElem) {
		        if (!minEndDate) return;
		        const date = dayElem.dateObj;
		        // disable dates which smaller than minEndDate
		        if (date < minEndDate) {
		        	dayElem.classList.add('flatpickr-disabled');
		        	dayElem.setAttribute('aria-disabled', 'true');
		        }
		    },
		    onChange: function(selectedDates, dateStr, instance) {
		        if (selectedDates.length === 2) {
		        	elDateCheckIn.value = toYmdLocal( selectedDates[0] );
		        	elDateCheckOut.value = toYmdLocal(selectedDates[1]);
		        	instance._input.value = toYmdLocal( selectedDates[0] ) + ' - ' + toYmdLocal(selectedDates[1]);
		        	// reset minEndDate
		        	minEndDate = new Date();
		        	instance.redraw();
		        	// Calculate booking price after selecting dates
		        	calculateBookingPrice( elForm );
		        } else if (selectedDates.length === 1) {
	                const start = selectedDates[0];
	                minEndDate = new Date(start);
	                minEndDate.setDate(minEndDate.getDate() + hotel_settings.min_booking_date);
	                // redraw to trigger onDayCreate
	                instance.redraw();
		        } else if ( selectedDates.length === 0 ) {
		        	// reset minEndDate
		        	minEndDate = new Date();
		        	instance.redraw();
		        }
		    },
		    onMonthChange: function ( selectedDates, dateStr, instance ) {
		    	let month = instance.currentMonth + 1,
		    		year = instance.currentYear;
		    	if ( undefined!==roomCalendarPricing ) {
		    		roomCalendarPricing.jumpToDate(year + "/" + month + "/01");
		    		roomCalendarPricing.config.onMonthChange.forEach(fn => fn(
			    			roomCalendarPricing.selectedDates,
			    			roomCalendarPricing.input.value,
			    			roomCalendarPricing
		    			)
		    		);
		    	}
		    }
		});
		elForm.addEventListener( 'click', (e) => {
			let target = e.target;
			if ( target === elDateCheckIn || target === elDateCheckOut ) {
				roomDateRangeSelector.open();
			}
		} );
		elForm.addEventListener( 'change', (e) => {
			let target = e.target;
			if ( target.closest( '.hb-booking-room-form-field' ) ) {
				if ( target === elDateRange ) {
					return;
				}
				if ( ! elDateCheckIn.value || ! elDateCheckOut.value ) {
					// elForm.querySelector( '.hb-total-price-value' ).innerHTML = utils.wphbRenderPrice( 0 );
					return;
				}
				calculateBookingPrice( elForm );
			}
		} );
	} else {
		let defaultCheckInDate = elDateCheckIn.value ? elDateCheckIn.value : dateMinCheckInCanBook;
		// Check in date
		const optionCheckIn = {
			dateFormat: 'Y/m/d',
			minDate: 'today',
			disable: datesBlock,
			defaultDate: defaultCheckInDate,
			disableMobile: true,
			locale: {
				firstDayOfWeek: 1,
			},
			onChange( selectedDates, dateStr, instance ) {
				if ( datePickerCheckOut ) {
					// calculate next day available
					const dateSelected = selectedDates[ 0 ];
					datePickerCheckOut.clear();
					const dateNext = new Date( dateSelected.setDate( dateSelected.getDate() + minBookingDateNumber - 1 ) );
					datePickerCheckOut.set( 'minDate', dateNext );
					datePickerCheckOut.open();
					datePickerCheckOut.set( 'disable', [
						( dateCalendar ) => {
							return calculateDatesCheckOutDisable(
								dateSelected,
								dateCalendar
							);
						},
					] );
				}
			},
		};
		datePickerCheckIn = flatpickr( elDateCheckIn, optionCheckIn );
		// console.log( elDateCheckIn.value );
		let defaultCheckOutDate = elDateCheckOut.value ? elDateCheckOut.value : dateMinCheckOutCanBook;
		// Check out date
		const optionCheckout = {
			dateFormat: 'Y/m/d',
			minDate: hotel_settings.min_booking_date > 0 ?  new Date().fp_incr( hotel_settings.min_booking_date ) : 'today',
			disable: datesBlock,
			defaultDate: defaultCheckOutDate,
			disableMobile: true,
			locale: {
				firstDayOfWeek: 1,
			},
			onChange( selectedDates, dateStr, instance ) {},
		};
		datePickerCheckOut = flatpickr( elDateCheckOut, optionCheckout );
	}
};
/**
 * Handle show calendar pricing
 * Show price by date in calendar when hover
 * Select date range in calendar
 */
const calendarPricing = () => {
	const elRoomCalendarPricing = document.querySelector(
		'.wphb-room-calendar-pricing'
	);
	if ( elRoomCalendarPricing ) {
		elBtnsCalendarPricing = document.querySelector( className.elBtnsCalendarPricing );
		const roomId = parseInt( elRoomCalendarPricing.dataset.roomId ) ?? 0;
		let blockDates = [];
		if ( hotel_settings.block_dates ) {
			const dateTimeStampsBlock = hotel_settings.block_dates;

			if ( dateTimeStampsBlock ) {
				dateTimeStampsBlock.forEach( ( timeStamp ) => {
					const date = new Date( timeStamp * 1000 );
					const dateBlock = new Date(
						date.getFullYear(),
						date.getMonth(),
						date.getDate()
					);
					blockDates.push( dateBlock );
				} );
			}
		}
		let calendarDefaultDate = [];
		if ( elForm && elForm.querySelector( 'input[name="check_in_date"]' ).value && elForm.querySelector( 'input[name="check_out_date"]' ).value ) {
			calendarDefaultDate = [elForm.querySelector( 'input[name="check_in_date"]' ).value, elForm.querySelector( 'input[name="check_out_date"]' ).value];
		}

		roomCalendarPricing = flatpickr( elRoomCalendarPricing, {
			dateFormat: 'Y/m/d',
			mode: 'range',
			minDate: 'today',
			inline: true,
			disable: blockDates,
			defaultDate: calendarDefaultDate,
			showMonths: 2,
			locale: {
				firstDayOfWeek: 1,
			},
			onReady: function ( selectedDates, dateStr, instance ) {
				let month = instance.currentMonth + 1,
					year = instance.currentYear;
				if ( undefined !== roomPricing ) {
					setCalendarDatePrice( instance, roomPricing );
				} else {
					fetchAndSetCalendarDatePrice( instance, roomId, month, year );
				}
			},
			onChange: function ( selectedDates, dateStr, instance ) {
				setCalendarDatePrice( instance, roomPricing );
			},
			onMonthChange: function ( selectedDates, dateStr, instance ) {
				let month = instance.currentMonth + 1,
					year = instance.currentYear;
				fetchAndSetCalendarDatePrice( instance, roomId, month, year );
			},
		} );
	}
};
const fetchAndSetCalendarDatePrice = (
	calendarInstance,
	roomId,
	month,
	year
) => {
	let restUrl = `${ hotel_settings.wphb_rest_url }wphb/v1/rooms/room-pricing?roomId=${ roomId }&month=${ month }&year=${ year }`;
	if ( undefined !== roomCalendarPricing ) {
		showCalendarOverlay( calendarInstance );
	}
	fetch( restUrl, {
		method: 'GET',
		headers: {
			'X-WP-Nonce': hotel_settings.wphb_rest_nonce,
		},
	} ) // wrapped
		.then( ( res ) => res.json() )
		.then( ( res ) => {
			if ( res.status === 'error' ) {
				throw new Error( res.message );
			}
			const data = res.data;
			roomPricing = data.pricing;
			setCalendarDatePrice( calendarInstance, roomPricing );
			if ( undefined !== roomCalendarPricing ) {
				elBtnsCalendarPricing.style.display = 'block';
			}
		} )
		.catch( ( err ) => console.log( err ) )
		.finally( () => {
			if ( undefined !== roomCalendarPricing ) {
				hideCalendarOverlay( calendarInstance );
			}
		} );
};
const showCalendarOverlay = ( calendarInstance ) => {
	let calendar = calendarInstance.calendarContainer;
	if ( ! calendar.querySelector( '.wphb-single-room-loading-overlay' ) ) {
		let overlay = document.createElement( 'div' );
		overlay.className = 'wphb-single-room-loading-overlay';
		overlay.innerHTML = '<div class="wphb-single-room-loading-spinner"></div>';
		calendar.appendChild( overlay );
	}
};
const hideCalendarOverlay = ( calendarInstance ) => {
	let calendar = calendarInstance.calendarContainer;
	let overlay = calendar.querySelector( '.wphb-single-room-loading-overlay' );
	if ( overlay ) overlay.remove();
};
const setCalendarDatePrice = ( calendarInstance, pricing ) => {
	const dayCells = calendarInstance.calendarContainer.querySelectorAll(
		'.flatpickr-day:not(.hidden)'
	);
	dayCells.forEach( ( dayElem, idx ) => {
		const dateObj = dayElem.dateObj;
		// Skip if dateObj is missing
		if ( ! dateObj || undefined === pricing[ idx ] ) {
			return;
		}
		dayElem.setAttribute(
			'data-title',
			decodeHtmlEntity( pricing[ idx ].price_html )
		);
	} );
};
const decodeHtmlEntity = ( str ) => {
	return new DOMParser().parseFromString( str, 'text/html' ).body.textContent;
};
const wphbRoomCheckDates = ( formCheckDate ) => {
	const elBtnCheck = formCheckDate.querySelector( 'button[type=submit]' );
	let elLoading = formCheckDate.querySelector( '.wphb-icon' );

	if ( ! elLoading ) {
		elBtnCheck.insertAdjacentHTML(
			'afterbegin',
			'<span class="dashicons dashicons-update hide wphb-icon"></span>'
		);
		elLoading = elBtnCheck.querySelector( '.wphb-icon' );
	}

	elLoading.classList.remove( 'hide' );
	elLoading.classList.toggle( 'loading' );
	elBtnCheck.setAttribute( 'disabled', 'disabled' );

	const showErrors = ( message ) => {
		const elMesErrors = formCheckDate.querySelectorAll(
			'.hotel_booking_room_errors'
		);
		if ( elMesErrors ) {
			elMesErrors.forEach( ( el ) => {
				el.remove();
			} );
		}

		formCheckDate
			.querySelector( '.hb-booking-room-form-head' )
			.insertAdjacentHTML(
				'beforeend',
				`<div class="hotel_booking_room_errors">${ message }</div>`
			);

		setTimeout( () => {
			formCheckDate
				.querySelector( '.hotel_booking_room_errors' )
				.remove();
		}, 2500 );
	};

	// Send to sever
	const option = handleBookingFormData( formCheckDate );

	fetch( hotel_settings.ajax, option )
		.then( ( response ) => response.json() )
		.then( ( res ) => {
			const { status, message, data } = res;

			if ( status === 'error' ) {
				showErrors( message );
				return;
			}

			if ( ! elAddToCart ) {
				elHotelBookingRoom.insertAdjacentHTML(
					'beforeend',
					data.html_extra
				);
			}

			elAddToCart = elHotelBookingRoom.querySelector(
				'.wpdb-room-tmpl-add-to-cart'
			);
			if ( ! elAddToCart ) {
				return;
			}

			// set list qty for room
			const elNumRoom = elAddToCart.querySelector(
				'input[name=hb-num-of-rooms]'
			);
			if ( elNumRoom ) {
				const elQtyMax = elAddToCart.querySelector( '.qty-max' );
				if ( elQtyMax ) {
					elQtyMax.textContent = data.qty;
				}
				// for ( let i = 1; i <= parseInt( data.qty ); i++ ) {
				// 	elNumRoom.insertAdjacentHTML( 'beforeend', '<option value="' + i + '">' + i + '</option>' );
				// }
				elNumRoom.setAttribute( 'max', data.qty );
			}

			// Set dates checked
			const elDatesChecked = elAddToCart.querySelector(
				'.wphb-room-dates-checked'
			);
			if ( elDatesChecked ) {
				elDatesChecked.innerHTML = data.dates_booked;
			} else {
				formCheckDate.insertAdjacentHTML(
					'beforebegin',
					data.dates_booked
				);
			}

			if ( elTmplDateAvailable ) {
				elTmplDateAvailable.style.display = 'none';
			} else {
				formCheckDate.style.display = 'none';
			}
			elAddToCart.style.display = 'block';
		} )
		.catch( ( error ) => {
			showErrors( error );
		} )
		.finally( () => {
			elBtnCheck.removeAttribute( 'disabled' );
			if ( elLoading ) {
				elLoading.classList.add( 'hide' );
				elLoading.classList.toggle( 'loading' );
			}
		} );
};
const wphbRoomAddToCart = ( formAddToCart ) => {
	const elBtnSubmit = formAddToCart.querySelector( 'button[type=submit]' );
	const elLoading = elBtnSubmit.querySelector( '.wphb-icon' );

	const showErrors = ( message ) => {
		const elMesErrors = formAddToCart.querySelectorAll(
			'.hotel_booking_room_errors'
		);
		if ( elMesErrors ) {
			elMesErrors.forEach( ( el ) => {
				el.remove();
			} );
		}

		formAddToCart
			.querySelector( '.hb-booking-room-form-head' )
			.insertAdjacentHTML(
				'beforeend',
				`<div class="hotel_booking_room_errors">${ message }</div>`
			);

		setTimeout( () => {
			formAddToCart
				.querySelector( '.hotel_booking_room_errors' )
				.remove();
		}, 2500 );
	};

	// Send to sever
	const option = handleBookingFormData( formAddToCart );

	elBtnSubmit.setAttribute( 'disabled', 'disabled' );
	elLoading.classList.remove( 'hide' );
	elLoading.classList.toggle( 'loading' );

	fetch( hotel_settings.ajax, option )
		.then( ( response ) => response.json() )
		.then( ( res ) => {
			const { status, message, data } = res;
			if ( status === 'error' ) {
				showErrors( message );
				elBtnSubmit.removeAttribute( 'disabled' );
				return;
			}

			window.location.href = data.redirect;
		} )
		.catch( ( error ) => {
			showErrors( error );
		} )
		.finally( () => {
			elLoading.classList.add( 'hide' );
			elLoading.classList.toggle( 'loading' );
		} );
};

const handleBookingFormData = ( form ) => {
	let dataSend = {};
	const data = new FormData( form );
	for ( const pair of data.entries() ) {
		const key = pair[ 0 ];
		if ( key === 'wpbh-dates-block' ) continue;
		const value = pair[ 1 ]; // Get the field value

		dataSend[ key ] = value;
	}
	const dataSendFrom = new FormData();
	Object.entries( dataSend ).forEach( ( [ key, value ] ) => {
		dataSendFrom.append( key, value );
	} );
	// For case theme override file, not have nonce
	dataSendFrom.append( 'nonce', hotel_settings.nonce );

	// Send to sever
	const option = { method: 'POST', headers: {}, body: dataSendFrom };
	if ( 0 !== parseInt( hotel_settings.user_id ) ) {
		option.headers[ 'X-WP-Nonce' ] = hotel_settings.wphb_rest_nonce;
	}
	return option;
}
//calculate room price without tax
const calculateBookingPrice = ( elForm ) => {
	const option = handleBookingFormData( elForm );
    elForm.querySelector( '.wphb-single-room-loading-overlay' ).classList.remove( 'hidden' );
    fetch( `${hotel_settings.wphb_rest_url}wphb/v1/rooms/calculate-booking-price`, option )
    	.then( ( response ) => response.json() )
    	.then( ( res ) => {
    		const { status, message, data } = res;
    		
    		if ( status === 'error' ) {
    			alert( message );
    			return;
    		}
    		elForm.querySelector( '[name="hb-num-of-rooms"]' ).setAttribute( 'max', data.available_qty );
    		elForm.querySelector( '.wphb-max-qty .qty-max' ).innerHTML = data.available_qty;
    		elForm.querySelector( '.hb-total-price-value' ).innerHTML = data.amount_html;
    	} )
    	.catch( ( error ) => {} ) .finally( () => { elForm.querySelector( '.wphb-single-room-loading-overlay' ).classList.add( 'hidden' ); } );
}

const getRoomBookingPriceDetails = ( button ) => {
	let form = button.closest( 'form' );
	const option = handleBookingFormData( form );
	const iconLoading = button.querySelector('.dashicons.dashicons-update.hide.wphb-icon');
	iconLoading.classList.toggle('hide');
	iconLoading.classList.toggle('loading');

	fetch( `${hotel_settings.wphb_rest_url}wphb/v1/rooms/single-room-price-details`, option )
		.then( ( response ) => response.json() )
		.then( ( res ) => {
			const { status, message, data } = res;
			if ( status === 'error' ) {
				console.log( message );
				return;
			}
			button.closest('.hb_view_price.hb-room-content').insertAdjacentHTML( 'beforeend', data.price_html );
		} )
		.catch( ( error ) => {} ) .finally( () => {
			iconLoading.classList.toggle('hide');
			iconLoading.classList.toggle('loading');
		} );
};

// Events
document.addEventListener( 'submit', function ( e ) {
	const target = e.target;

	if ( target.name === 'hb-search-single-room' ) {
		e.preventDefault();
		wphbRoomAddToCart( target );
		// wphbRoomCheckDates( target );
	}

	if ( target.name === 'hb-search-results' ) {
		e.preventDefault();
		wphbRoomAddToCart( target );
	}
} );
document.addEventListener( 'click', function ( e ) {
	const target = e.target;

	if ( target.classList.contains( 'hb_previous_step' ) ) {
		e.preventDefault();
		if ( elTmplDateAvailable ) {
			elTmplDateAvailable.style.display = 'block';
		} else {
			elForm.style.display = 'block';
		}
		elAddToCart.style.display = 'none';
	} else if ( target.id === 'hb_room_load_booking_form' ) {
		const href = target.href;
		const checkHref = /.*#/;
		if ( ! checkHref.test( href ) ) {
			return;
		}
		e.preventDefault();

		elHotelBookingRoom = document.querySelector(
			'#hotel_booking_room_hidden'
		);
		if ( ! elHotelBookingRoom ) {
			return;
		}

		// Init new modal
		if ( ! modalCheckDates ) {
			modalCheckDates = new tingle.modal( {
				onOpen() {
					elHotelBookingRoom.style.display = 'block';
				},
				onClose() {
					elHotelBookingRoom.style.display = 'none';
				},
			} );
		}

		// set content
		modalCheckDates.setContent( elHotelBookingRoom );
		modalCheckDates.open();
	} else if ( target.closest( '.room-preview' ) ) {
		e.preventDefault();
		const elRoomPreview = target.closest( '.room-preview' );
		let iframe = elRoomPreview.dataset.preview;
		const regexSearchInIframe = /.*<iframe/;
		const windowHeight = window.innerHeight / 2;

		// Check link youtube
		const regexYoutube = /.*youtube.com/;
		if ( regexYoutube.test( iframe ) ) {
			iframe = iframe.replace( 'watch?v=', 'embed/' );
		}

		if ( ! regexSearchInIframe.test( iframe ) ) {
			iframe = `<iframe class="wphb-iframe-preview" src="${ iframe }" 
			width="100%" height="${ windowHeight }px" frameborder="0" allowfullscreen></iframe>`;
		}

		modalPreview = new tingle.modal();
		modalPreview.setContent( iframe );
		modalPreview.open();
	} else if ( target.classList.contains( 'hb-btn-cancel' ) ) {
		if ( undefined !== roomCalendarPricing ) {
			roomCalendarPricing.clear();
		}
	} else if ( target.classList.contains( 'hb-btn-apply' ) ) {
		if ( undefined !== roomCalendarPricing ) {
			if (
				roomCalendarPricing.selectedDates.length === 2 &&
				undefined !== elForm
			) {
				if ( document.querySelector( '#hb_room_load_booking_form' ) ) {
					document
						.querySelector( '#hb_room_load_booking_form' )
						.click();
					if ( elTmplDateAvailable ) {
						elTmplDateAvailable.style.display = 'block';
					}
					if ( elAddToCart ) {
						elAddToCart.style.display = 'none';
					}
				} else {
					elHotelBookingRoom.scrollIntoView( { behavior: 'smooth' } );
					elForm.style.display = 'block';
					if ( elAddToCart ) {
						elAddToCart.style.display = 'none';
					}
				}
				if ( undefined !== roomDateRangeSelector ) {
					roomDateRangeSelector.setDate(roomCalendarPricing.selectedDates, true);
				}
			}
		}
	} else if ( target.classList.contains( 'hb-single-room-price-details' ) ) {
		if ( target.closest( '.hb_view_price.hb-room-content' ).querySelector( '.hb-booking-room-details' ) ) {
			target.closest( '.hb_view_price.hb-room-content' ).querySelector( '.hb-booking-room-details' ).remove();
		}
		getRoomBookingPriceDetails( target );
	} if ( target.closest( 'li[data-tab-id="hb_room_pricing_plans"]' )
        || target.closest( 'a[href="#hb_room_pricing_plans"]' ) ) {
        calendarPricing();
    }

	// faq toggle
	const targetFAQ = target.closest( '._hb_room_faqs__detail' );
	if ( targetFAQ ) {
		targetFAQ.classList.toggle( 'toggled' );
	}
	let bookingPriceDetails = document.querySelector( '.hb-booking-room-details' );
	if ( bookingPriceDetails && ! bookingPriceDetails.contains( target ) && target !== bookingPriceDetails ) {
		bookingPriceDetails.classList.remove('active');
	}
} );

let modalPreview;
document.addEventListener( 'DOMContentLoaded', function ( e ) {
	wphbRoomInitDatePicker();

	// Check view calendar pricing will load calendar pricing
	const elRoomCalendarPricing = document.querySelector( '.wphb-room-calendar-pricing' );
	if ( elRoomCalendarPricing ) {
        // If calendar pricing in tab content, only load when click to tab
        const elTabContent = elRoomCalendarPricing.closest( '.hb_single_room_tabs_content' );
        if ( elTabContent ) {
            return;
        }

		utils.listenElementViewed( elRoomCalendarPricing, () => {
			if ( elRoomCalendarPricing.classList.contains( 'loaded' ) ) {
				return;
			}

			elRoomCalendarPricing.classList.add( 'loaded' );
			calendarPricing();
		} )
	}
} );
