import flatpickr from 'flatpickr';
import flatpickrLocales from 'flatpickr/dist/l10n/index.js';
import tingle from 'tingle.js';

import * as utils from '../utils.js';
import {
	FLATPICKR_RANGE_SEPARATOR,
	applyFlatpickrLocaleConfig,
	createBookingDateHelpers,
	createFlatpickrConfig,
	createFlatpickrLocaleConfig,
	createMinEndDateDayCreateHandler,
	formatDateRangeValue,
	getMinBookingDays,
	getMinEndDateFromStartDate,
	mapBookingTimestampsToDates,
	syncBookingDateFieldsForUi,
	validateBookingDateRange,
} from './flatpickr-locale-utils.js';

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
	roomDateRangeSelector,
	minEndDatePlan;
const INTERNAL_DATE_FORMAT = hotel_settings.internal_date_format || 'Y/m/d';
const FRONTEND_DATE_FORMAT =
	hotel_settings.flatpickr_date_format || INTERNAL_DATE_FORMAT;
// Build one locale config for all pickers: browser locale + admin first day of week.
const FLATPICKR_LOCALE = createFlatpickrLocaleConfig(
	flatpickrLocales,
	hotel_settings.first_day_of_week
);
applyFlatpickrLocaleConfig( flatpickr, FLATPICKR_LOCALE );
const {
	parseBookingDateValue,
	parseFlatpickrDate,
	formatBookingDateForUi,
	formatBookingDateForSubmit,
} = createBookingDateHelpers(
	flatpickr,
	FRONTEND_DATE_FORMAT,
	INTERNAL_DATE_FORMAT
);

// Validate and normalize before submit to avoid ambiguous string date parsing.
const validateAndNormalizeBookingDateRange = ( form ) => {
	return validateBookingDateRange( form, {
		parseBookingDateValue,
		formatBookingDateForSubmit,
		emptyCheckInMessage:
			window?.hotel_booking_i18n?.empty_check_in_date ||
			'Please select check in date.',
		emptyCheckOutMessage:
			window?.hotel_booking_i18n?.empty_check_out_date ||
			'Please select check out date.',
		invalidRangeMessage:
			window?.hotel_booking_i18n?.check_out_date_must_be_greater ||
			'Check out date must be greater than the check in.',
	} );
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
	syncBookingDateFieldsForUi(
		{
			checkInField: elDateCheckIn,
			checkOutField: elDateCheckOut,
			rangeField: elDateRange,
			rangeSeparator: FLATPICKR_RANGE_SEPARATOR,
		},
		{
			parseBookingDateValue,
			formatBookingDateForUi,
		}
	);
	let datePickerCheckIn;
	let datePickerCheckOut;
	let dateMinCheckInCanBook;
	let dateMinCheckOutCanBook;
	const datesBlock = mapBookingTimestampsToDates( hotel_settings.block_dates );
	const dateNow = new Date();
	const dateTomorrow = new Date( dateNow.setDate( dateNow.getDate() + 1 + hotel_settings.min_booking_date ) );
	const minBookingDateNumber = getMinBookingDays( hotel_settings.min_booking_date );

	if ( datesBlock.length === 0 ) {
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
			// Single range picker mode: one input controls both check-in and check-out.
			let positionEle = parseInt( elDateRange.getAttribute( 'data-hidden' ) ) === 1 ? elDateCheckIn : null;
			let minEndDate = null;
			roomDateRangeSelector = flatpickr( elDateRange, createFlatpickrConfig(
				{
					dateFormat: FRONTEND_DATE_FORMAT,
					parseDate: parseFlatpickrDate,
					locale: FLATPICKR_LOCALE,
				},
				{
					mode: 'range',
					minDate: 'today',
					disable: datesBlock,
					showMonths: 2,
					positionElement: positionEle,
					defaultDate: [ elDateCheckIn.value, elDateCheckOut.value ],
					onDayCreate: createMinEndDateDayCreateHandler(
						() => minEndDate
					),
					onChange: function( selectedDates, dateStr, instance ) {
						if ( selectedDates.length === 2 ) {
							elDateCheckIn.value = formatBookingDateForUi( selectedDates[ 0 ] );
							elDateCheckOut.value = formatBookingDateForUi( selectedDates[ 1 ] );
							instance._input.value = formatDateRangeValue(
								formatBookingDateForUi( selectedDates[ 0 ] ),
								formatBookingDateForUi( selectedDates[ 1 ] ),
								FLATPICKR_RANGE_SEPARATOR
							);
							minEndDate = null;
							instance.redraw();
							calculateBookingPrice( elForm );
						} else if ( selectedDates.length === 1 ) {
							minEndDate = getMinEndDateFromStartDate(
								selectedDates[ 0 ],
								minBookingDateNumber
							);
							instance.redraw();
						} else {
							minEndDate = null;
							instance.redraw();
						}
					},
					onMonthChange: function( selectedDates, dateStr, instance ) {
						let month = instance.currentMonth + 1,
							year = instance.currentYear;
						if ( undefined !== roomCalendarPricing ) {
							roomCalendarPricing.jumpToDate(
								new Date( year, month - 1, 1 )
							);
							roomCalendarPricing.config.onMonthChange.forEach(
								( fn ) =>
									fn(
										roomCalendarPricing.selectedDates,
										roomCalendarPricing.input.value,
										roomCalendarPricing
									)
							);
						}
					},
				}
			) );
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

					return;
				}
				calculateBookingPrice( elForm );
			}
		} );
	} else {
		let defaultCheckInDate = elDateCheckIn.value ? elDateCheckIn.value : dateMinCheckInCanBook;
		// Two-input mode: check-in picker drives minDate/disabled logic of check-out picker.
		const optionCheckIn = createFlatpickrConfig( {
			dateFormat: FRONTEND_DATE_FORMAT,
			parseDate: parseFlatpickrDate,
			locale: FLATPICKR_LOCALE,
		}, {
			minDate: 'today',
			disable: datesBlock,
			defaultDate: defaultCheckInDate,
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
		} );
		datePickerCheckIn = flatpickr( elDateCheckIn, optionCheckIn );
		let defaultCheckOutDate = elDateCheckOut.value ? elDateCheckOut.value : dateMinCheckOutCanBook;
		// Check-out picker uses the same strict parser and locale as check-in picker.
		const optionCheckout = createFlatpickrConfig( {
			dateFormat: FRONTEND_DATE_FORMAT,
			parseDate: parseFlatpickrDate,
			locale: FLATPICKR_LOCALE,
		}, {
			minDate: hotel_settings.min_booking_date > 0 ?  new Date().fp_incr( hotel_settings.min_booking_date ) : 'today',
			disable: datesBlock,
			defaultDate: defaultCheckOutDate,
		} );
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
		let blockDates = mapBookingTimestampsToDates( hotel_settings.block_dates );
		let calendarDefaultDate = [];
		if ( elForm && elForm.querySelector( 'input[name="check_in_date"]' ).value && elForm.querySelector( 'input[name="check_out_date"]' ).value ) {
			calendarDefaultDate = [elForm.querySelector( 'input[name="check_in_date"]' ).value, elForm.querySelector( 'input[name="check_out_date"]' ).value];
		}
		let showMonths = window.innerWidth >= 992 ? 2 : 1;
		// Inline pricing calendar is also a flatpickr range picker sharing locale/format config.
		roomCalendarPricing = flatpickr( elRoomCalendarPricing, createFlatpickrConfig( {
			dateFormat: FRONTEND_DATE_FORMAT,
			parseDate: parseFlatpickrDate,
			locale: FLATPICKR_LOCALE,
		}, {
			mode: 'range',
			minDate: 'today',
			inline: true,
			disable: blockDates,
			defaultDate: calendarDefaultDate,
			showMonths: showMonths,
			onDayCreate: createMinEndDateDayCreateHandler(
				() => minEndDatePlan
			),
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
				if ( selectedDates.length === 1 ) {
					minEndDatePlan = getMinEndDateFromStartDate(
						selectedDates[ 0 ],
						getMinBookingDays( hotel_settings.min_booking_date )
					);
					instance.redraw();
				} else {
					minEndDatePlan = null;
					instance.redraw();
				}
				setCalendarDatePrice( instance, roomPricing );
			},
			onMonthChange: function ( selectedDates, dateStr, instance ) {
				let month = instance.currentMonth + 1,
					year = instance.currentYear;
				fetchAndSetCalendarDatePrice( instance, roomId, month, year );
			},
		} ) );
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
			roomPricing = indexRoomPricingByDate( data.pricing );
			setCalendarDatePrice( calendarInstance, roomPricing );
			if ( undefined !== roomCalendarPricing ) {
				elBtnsCalendarPricing.style.display = 'block';
			}
		} )
		.catch( ( err ) => console.error( err ) )
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
const htmlDecodeElement = document.createElement( 'textarea' );
const getPricingDateKey = ( dateValue ) => {
	if ( ! dateValue ) {
		return '';
	}

	if ( dateValue instanceof Date && ! Number.isNaN( dateValue.getTime() ) ) {
		const year = dateValue.getFullYear();
		const month = String( dateValue.getMonth() + 1 ).padStart( 2, '0' );
		const day = String( dateValue.getDate() ).padStart( 2, '0' );

		return `${ year }-${ month }-${ day }`;
	}

	return String( dateValue ).slice( 0, 10 );
};
const indexRoomPricingByDate = ( pricing = [] ) =>
	( Array.isArray( pricing ) ? pricing : [] ).reduce( ( acc, priceItem ) => {
		const pricingDateKey = getPricingDateKey( priceItem?.date );

		if ( pricingDateKey ) {
			acc[ pricingDateKey ] = priceItem;
		}

		return acc;
	}, {} );
const setCalendarDatePrice = ( calendarInstance, pricing ) => {
	const dayCells = calendarInstance.calendarContainer.querySelectorAll(
		'.flatpickr-day:not(.hidden)'
	);
	dayCells.forEach( ( dayElem ) => {
		const dateObj = dayElem.dateObj;
		const pricingDateKey = getPricingDateKey( dateObj );
		const pricingItem = pricing?.[ pricingDateKey ];

		if ( ! dateObj || ! pricingItem ) {
			return;
		}

		dayElem.setAttribute(
			'data-title',
			decodeHtmlEntity( pricingItem.price_html )
		);
	} );
};
const decodeHtmlEntity = ( str ) => {
	htmlDecodeElement.innerHTML = str;
	return htmlDecodeElement.value;
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
	let option;
	try {
		option = handleBookingFormData( formAddToCart );
	} catch ( error ) {
		showErrors( error.message || String( error ) );
		return;
	}

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
	const normalizedDates = validateAndNormalizeBookingDateRange( form );
	if ( normalizedDates.error ) {
		throw new Error( normalizedDates.error );
	}

	let dataSend = {};
	const data = new FormData( form );
	for ( const pair of data.entries() ) {
		const key = pair[ 0 ];
		if ( key === 'wpbh-dates-block' ) continue;
		const value = pair[ 1 ]; // Get the field value

		dataSend[ key ] = value;
	}
	dataSend.check_in_date = normalizedDates.checkInDate;
	dataSend.check_out_date = normalizedDates.checkOutDate;
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
	let option;
	try {
		option = handleBookingFormData( elForm );
	} catch ( error ) {
		alert( error.message || String( error ) );
		return;
	}
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
    	.catch( ( error ) => {
			console.error( error );
		} )
		.finally( () => { elForm.querySelector( '.wphb-single-room-loading-overlay' ).classList.add( 'hidden' ); } );
}

const getRoomBookingPriceDetails = ( button ) => {
	let form = button.closest( 'form' );
	let option;
	try {
		option = handleBookingFormData( form );
	} catch ( error ) {
		alert( error.message || String( error ) );
		return;
	}
	const iconLoading = button.querySelector('.dashicons.dashicons-update.hide.wphb-icon');
	iconLoading.classList.toggle('hide');
	iconLoading.classList.toggle('loading');

	fetch( `${hotel_settings.wphb_rest_url}wphb/v1/rooms/single-room-price-details`, option )
		.then( ( response ) => response.json() )
		.then( ( res ) => {
			const { status, message, data } = res;
			if ( status === 'error' ) {
				console.error( message );
				return;
			}
			button.closest('.hb_view_price.hb-room-content').insertAdjacentHTML( 'beforeend', data.price_html );
		} )
		.catch( ( error ) => {
			console.error( error );
		} )
		.finally( () => {
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
			minEndDatePlan = null;
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
