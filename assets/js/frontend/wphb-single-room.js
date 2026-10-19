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
	const minBookingDays = getMinBookingDays( hotel_settings.min_booking_date );
	return validateBookingDateRange( form, {
		parseBookingDateValue,
		formatBookingDateForSubmit,
		minBookingDays,
		minBookingDaysMessage:
			window?.hotel_booking_i18n?.minimum_booking_nights
				? window.hotel_booking_i18n.minimum_booking_nights.replace( '%d', minBookingDays )
				: `Minimum booking is ${ minBookingDays } nights.`,
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
	let formBlockDates = hotel_settings.block_dates;
	const elBlockInput = elForm?.querySelector( 'input[name="wpbh-dates-block"]' );
	if ( elBlockInput && elBlockInput.value ) {
		try {
			formBlockDates = JSON.parse( elBlockInput.value );
		} catch ( e ) {}
	}
	const datesBlock = mapBookingTimestampsToDates( formBlockDates );
	const dateNow = new Date();
	const minBookingDateNumber = getMinBookingDays( hotel_settings.min_booking_date );
	const dateTomorrow = new Date( dateNow.getTime() );
	dateTomorrow.setDate( dateTomorrow.getDate() + minBookingDateNumber );

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
		if ( roomDateRangeSelector?.calendarContainer ) {
			roomDateRangeSelector.calendarContainer.addEventListener(
				'mouseover',
				( e ) => {
					e.stopImmediatePropagation();
				},
				true
			);
		}
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
					const dateNext = new Date( dateSelected.getTime() );
					dateNext.setDate( dateNext.getDate() + minBookingDateNumber );
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
			minDate: hotel_settings.min_booking_date > 0 ? new Date().fp_incr( hotel_settings.min_booking_date ) : new Date().fp_incr( 1 ),
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
	if ( ! elRoomCalendarPricing ) {
		return;
	}

	if ( elRoomCalendarPricing._flatpickr ) {
		elRoomCalendarPricing._flatpickr.redraw();
		return;
	}

	elBtnsCalendarPricing =
		elRoomCalendarPricing.parentNode?.querySelector?.( className.elBtnsCalendarPricing ) ||
		document.querySelector( className.elBtnsCalendarPricing );

	const roomId = parseInt( elRoomCalendarPricing.dataset.roomId, 10 ) || 0;
	let rawBlockDates = hotel_settings.block_dates;
	if ( elRoomCalendarPricing.dataset.blockDates ) {
		try {
			rawBlockDates = JSON.parse( elRoomCalendarPricing.dataset.blockDates );
		} catch ( e ) {}
	}
	const blockDates = mapBookingTimestampsToDates( rawBlockDates );

	let calendarDefaultDate = [];
	const checkInInput = elForm?.querySelector?.( 'input[name="check_in_date"]' );
	const checkOutInput = elForm?.querySelector?.( 'input[name="check_out_date"]' );
	if ( checkInInput?.value && checkOutInput?.value ) {
		calendarDefaultDate = [ checkInInput.value, checkOutInput.value ];
	}

	const showMonths = window.innerWidth >= 992 ? 2 : 1;
	const flatpickrOverrides = {
		mode: 'range',
		minDate: 'today',
		inline: true,
		disable: blockDates,
		showMonths: showMonths,
		onDayCreate: createMinEndDateDayCreateHandler(
			() => minEndDatePlan
		),
		onReady: function ( selectedDates, dateStr, instance ) {
			const month = instance.currentMonth + 1;
			const year = instance.currentYear;
			if ( undefined !== roomPricing ) {
				setCalendarDatePrice( instance, roomPricing );
				if ( elBtnsCalendarPricing ) {
					elBtnsCalendarPricing.style.display = 'block';
				}
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
			const month = instance.currentMonth + 1;
			const year = instance.currentYear;
			fetchAndSetCalendarDatePrice( instance, roomId, month, year );
		},
	};

	if ( calendarDefaultDate.length === 2 ) {
		flatpickrOverrides.defaultDate = calendarDefaultDate;
	}

	roomCalendarPricing = flatpickr(
		elRoomCalendarPricing,
		createFlatpickrConfig(
			{
				dateFormat: FRONTEND_DATE_FORMAT,
				parseDate: parseFlatpickrDate,
				locale: FLATPICKR_LOCALE,
			},
			flatpickrOverrides
		)
	);

	elRoomCalendarPricing.classList.add( 'loaded' );
};
const fetchAndSetCalendarDatePrice = (
	calendarInstance,
	roomId,
	month,
	year
) => {
	const restUrl = `${ hotel_settings.wphb_rest_url }wphb/v1/rooms/room-pricing?roomId=${ roomId }&month=${ month }&year=${ year }`;
	if ( calendarInstance ) {
		showCalendarOverlay( calendarInstance );
	}
	fetch( restUrl, {
		method: 'GET',
		headers: {
			'X-WP-Nonce': hotel_settings.wphb_rest_nonce,
		},
	} )
		.then( ( res ) => res.json() )
		.then( ( res ) => {
			if ( res.status === 'error' ) {
				throw new Error( res.message );
			}
			const data = res.data;
			if ( Array.isArray( data?.blocked_dates ) && calendarInstance ) {
				const updatedBlockDates = mapBookingTimestampsToDates( data.blocked_dates );
				calendarInstance.set( 'disable', updatedBlockDates );
			}
			roomPricing = indexRoomPricingByDate( data?.pricing );
			if ( calendarInstance ) {
				setCalendarDatePrice( calendarInstance, roomPricing );
			}
			if ( elBtnsCalendarPricing ) {
				elBtnsCalendarPricing.style.display = 'block';
			}
		} )
		.catch( ( err ) => console.error( err ) )
		.finally( () => {
			if ( calendarInstance ) {
				hideCalendarOverlay( calendarInstance );
			}
		} );
};
const showCalendarOverlay = ( calendarInstance ) => {
	const calendar = calendarInstance?.calendarContainer;
	if ( calendar && ! calendar.querySelector( '.wphb-single-room-loading-overlay' ) ) {
		const overlay = document.createElement( 'div' );
		overlay.className = 'wphb-single-room-loading-overlay';
		overlay.innerHTML = '<div class="wphb-single-room-loading-spinner"></div>';
		calendar.appendChild( overlay );
	}
};
const hideCalendarOverlay = ( calendarInstance ) => {
	const calendar = calendarInstance?.calendarContainer;
	const overlay = calendar?.querySelector( '.wphb-single-room-loading-overlay' );
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
		if ( dayElem.classList.contains( 'flatpickr-disabled' ) ) {
			dayElem.removeAttribute( 'data-title' );
			return;
		}
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
	const elLoading   = elBtnSubmit?.querySelector( '.wphb-icon' );

	const showErrors = ( message ) => {
		formAddToCart.querySelectorAll( '.hotel_booking_room_errors' ).forEach( ( el ) => el.remove() );
		formAddToCart.closest( '#hotel_booking_room_hidden, .wphb-room-tmpl-dates-available' )
			?.querySelectorAll( '.hotel_booking_room_errors' ).forEach( ( el ) => el.remove() );

		const errEl = document.createElement( 'div' );
		errEl.className = 'hotel_booking_room_errors';
		errEl.setAttribute( 'role', 'alert' );
		errEl.innerHTML = message;

		const formContainer = formAddToCart.querySelector( '.hb-search-results-form-container' );
		if ( formContainer ) {
			formContainer.prepend( errEl );
		} else {
			formAddToCart.prepend( errEl );
		}

		errEl.scrollIntoView?.( { behavior: 'smooth', block: 'nearest' } );

		setTimeout( () => {
			if ( errEl.parentNode ) {
				errEl.remove();
			}
		}, 6000 );
	};

	// Send to sever
	let option;
	try {
		option = handleBookingFormData( formAddToCart );
	} catch ( error ) {
		showErrors( error.message || String( error ) );
		return;
	}

	elBtnSubmit?.setAttribute( 'disabled', 'disabled' );
	elLoading?.classList.remove( 'hide' );
	elLoading?.classList.add( 'loading' );

	fetch( hotel_settings.ajax, option )
		.then( ( response ) => response.json() )
		.then( ( res ) => {
			const { status, message, data } = res;
			if ( status === 'error' ) {
				showErrors( message );
				elBtnSubmit?.removeAttribute( 'disabled' );
				return;
			}
			if ( data?.redirect ) {
				window.location.href = data.redirect;
				return;
			}
			if ( elAddToCart && data?.dates_booked ) {
				const elNumRoom = elAddToCart.querySelector( 'select[name=hb-num-of-rooms]' );
				if ( elNumRoom && data.qty ) {
					elNumRoom.innerHTML = '<option value="">' + ( window?.hotel_booking_i18n?.qty || '--- Quantity ---' ) + '</option>';
					for ( let i = 1; i <= parseInt( data.qty ); i++ ) {
						elNumRoom.insertAdjacentHTML( 'beforeend', '<option value="' + i + '">' + i + '</option>' );
					}
				}
				const elDatesChecked = elAddToCart.querySelector( '.wphb-room-dates-checked' );
				if ( elDatesChecked ) {
					elDatesChecked.innerHTML = data.dates_booked;
				}
				if ( elTmplDateAvailable ) {
					elTmplDateAvailable.style.display = 'none';
				}
				elAddToCart.style.display = 'block';
				return;
			}
			if ( message ) {
				showErrors( message );
			}
			elBtnSubmit?.removeAttribute( 'disabled' );
		} )
		.catch( ( error ) => {
			showErrors( error.message || error );
			elBtnSubmit?.removeAttribute( 'disabled' );
		} )
		.finally( () => {
			elLoading?.classList.add( 'hide' );
			elLoading?.classList.remove( 'loading' );
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
			const selectedDates = roomCalendarPricing.selectedDates;
			if ( selectedDates.length < 2 ) {
				alert(
					window?.hotel_booking_i18n?.empty_check_out_date ||
					'Please select check out date.'
				);
				return;
			}

			const minBookingDays = getMinBookingDays( hotel_settings.min_booking_date );
			const diffDays = Math.round( Math.abs( selectedDates[ 1 ].getTime() - selectedDates[ 0 ].getTime() ) / ( 1000 * 60 * 60 * 24 ) );
			if ( minBookingDays > 1 && diffDays < minBookingDays ) {
				alert(
					window?.hotel_booking_i18n?.minimum_booking_nights
						? window.hotel_booking_i18n.minimum_booking_nights.replace( '%d', minBookingDays )
						: `Minimum booking is ${ minBookingDays } nights.`
				);
				return;
			}

			const bookingRoom = document.querySelector( '#hotel_booking_room_hidden' ) || elHotelBookingRoom;
			const form = bookingRoom?.querySelector?.( 'form[name="hb-search-single-room"]' ) || elForm;

			const checkInField = form?.querySelector?.( 'input[name="check_in_date"]' );
			const checkOutField = form?.querySelector?.( 'input[name="check_out_date"]' );
			if ( checkInField ) {
				checkInField.value = formatBookingDateForUi( selectedDates[ 0 ] );
			}
			if ( checkOutField ) {
				checkOutField.value = formatBookingDateForUi( selectedDates[ 1 ] );
			}

			if ( undefined !== roomDateRangeSelector ) {
				roomDateRangeSelector.setDate( selectedDates, true );
			}

			if ( form ) {
				calculateBookingPrice( form );
			}

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
			} else if ( bookingRoom ) {
				if ( form ) {
					form.style.display = 'block';
				}
				if ( elAddToCart ) {
					elAddToCart.style.display = 'none';
				}
				const headerOffset = 100;
				const elementPosition = bookingRoom.getBoundingClientRect().top;
				const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
				window.scrollTo( {
					top: offsetPosition,
					behavior: 'smooth',
				} );
			}
		}
	} else if ( target.classList.contains( 'hb-single-room-price-details' ) ) {
		if ( target.closest( '.hb_view_price.hb-room-content' ).querySelector( '.hb-booking-room-details' ) ) {
			target.closest( '.hb_view_price.hb-room-content' ).querySelector( '.hb-booking-room-details' ).remove();
		}
		getRoomBookingPriceDetails( target );
	} else if (
		target.closest( 'li[data-tab-id="hb_room_pricing_plans"]' ) ||
		target.closest( 'a[href="#hb_room_pricing_plans"]' )
	) {
		setTimeout( () => {
			calendarPricing();
		}, 100 );
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
		const elTabContent = elRoomCalendarPricing.closest( '.hb_single_room_tabs_content' );
		const tabPane = elRoomCalendarPricing.closest( '.hb_single_room_tab_details' );
		const isHiddenTab = elTabContent && tabPane && (
			tabPane.style.display === 'none' ||
			( ! tabPane.classList.contains( 'active' ) && tabPane !== elTabContent.querySelector( '.hb_single_room_tab_details:first-child' ) && getComputedStyle( tabPane ).display === 'none' )
		);

		utils.listenElementViewed( elRoomCalendarPricing, () => {
			if ( elRoomCalendarPricing.classList.contains( 'loaded' ) ) {
				return;
			}
			calendarPricing();
		} );

		if ( ! isHiddenTab && elRoomCalendarPricing.offsetParent !== null ) {
			calendarPricing();
		}
	}
} );
