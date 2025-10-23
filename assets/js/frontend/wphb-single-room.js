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
const dataSend = {};
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
		    onReady: function ( selectedDates, dateStr, instance ) {
				let month = instance.currentMonth + 1,
					year = instance.currentYear;
				fetchAndSetCalendarDatePrice( instance, roomId, month, year );
			},
		    onChange: function(selectedDates, dateStr, instance) {
		        if (selectedDates.length === 2) {
		        	elDateCheckIn.value = toYmdLocal( selectedDates[0] );
		        	elDateCheckOut.value = toYmdLocal(selectedDates[1]);
		        	instance._input.value = toYmdLocal( selectedDates[0] ) + ' - ' + toYmdLocal(selectedDates[1]);
		        	// if ( undefined !== roomCalendarPricing ) {
		        	// 	roomCalendarPricing.setDate(selectedDates, true);
		        	// }
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
		    	} else {
		    		fetchAndSetCalendarDatePrice( instance, roomId, month, year );
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
				if ( ! elDateCheckIn.value || ! elDateCheckOut.value ) {
					elForm.querySelector( '.hb-total-price-value' ).innerHTML = utils.wphbRenderPrice( 0 );
					return;
				}
				calculateBookingPrice( elForm, target );
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
				if ( selectedDates.length === 2 && elForm ) {
					elForm.querySelector(
						'input[name="check_in_date"]'
					).value = toYmdLocal( selectedDates[ 0 ] );
					elForm.querySelector(
						'input[name="check_out_date"]'
					).value = toYmdLocal( selectedDates[ 1 ] );
					if ( undefined !== roomDateRangeSelector ) {
						roomDateRangeSelector.setDate(selectedDates, true);
					}
				} else if ( selectedDates.length === 0 ) {
					elForm.querySelector(
						'input[name="check_in_date"]'
					).value = '';
					elForm.querySelector(
						'input[name="check_out_date"]'
					).value = '';
					if ( undefined !== roomDateRangeSelector ) {
						roomDateRangeSelector.setDate(selectedDates, true);
					}
				}
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
	if ( ! calendar.querySelector( '.calendar-loading-overlay' ) ) {
		let overlay = document.createElement( 'div' );
		overlay.className = 'calendar-loading-overlay';
		overlay.innerHTML = '<div class="calendar-loading-spinner"></div>';
		calendar.appendChild( overlay );
	}
};
const hideCalendarOverlay = ( calendarInstance ) => {
	let calendar = calendarInstance.calendarContainer;
	let overlay = calendar.querySelector( '.calendar-loading-overlay' );
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
	const data = new FormData( formCheckDate );
	for ( const pair of data.entries() ) {
		const key = pair[ 0 ]; // Get the field name
		if ( key === 'wpbh-dates-block' ) continue;
		const value = pair[ 1 ]; // Get the field value

		dataSend[ key ] = value;
	}

	const dateSendFrom = new FormData();
	Object.entries( dataSend ).forEach( ( [ key, value ] ) => {
		dateSendFrom.append( key, value );
	} );
	// For case theme override file, not have nonce
	dateSendFrom.append( 'nonce', hotel_settings.nonce );

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
	const option = { method: 'POST', headers: {}, body: dateSendFrom };
	if ( 0 !== parseInt( hotel_settings.user_id ) ) {
		option.headers[ 'X-WP-Nonce' ] = hotel_settings.nonce;
	}

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
	const elLoading = formAddToCart.querySelector( '.wphb-icon' );
	const data = new FormData( formAddToCart );
	for ( const pair of data.entries() ) {
		const key = pair[ 0 ]; // Get the field name
		// remove block dates date when add to cart
		if ( key === 'wpbh-dates-block' ) continue;
		const value = pair[ 1 ]; // Get the field value

		dataSend[ key ] = value;
	}

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

	const dateSendFrom = new FormData();
	Object.entries( dataSend ).forEach( ( [ key, value ] ) => {
		dateSendFrom.append( key, value );
	} );

	// Send to sever
	const option = { method: 'POST', headers: {}, body: dateSendFrom };
	if ( 0 !== parseInt( hotel_settings.user_id ) ) {
		option.headers[ 'X-WP-Nonce' ] = hotel_settings.nonce;
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
//calculate room price without tax
const calculateBookingPrice = ( elForm, target ) => {
	const formData = new FormData( elForm ),
		checkInDate = formData.get( 'check_in_date' ),
		checkOutDate = formData.get( 'check_out_date' ),
		checkInDateObject = new Date(checkInDate),
		checkOutDateObject = new Date(checkOutDate);

	let roomBookedNight = 0, roomBookedPrice = 0;

	for (var i = 0; i < roomPricing.length; i++) {
		let date = roomPricing[i];
		if ( date.date >= checkInDateObject.toISOString() && date.date <= checkOutDateObject.toISOString() ) {
			roomBookedNight += 1;
			roomBookedPrice += date.price;
		}
	}

	const extraData = [];
    const extraOptions = elForm.querySelectorAll('input.hb_optional_quantity_selected');
    const roomQtyEle = elForm.querySelector( 'input[name="hb-num-of-rooms"]' );
    const roomQty = roomQtyEle ? parseInt( roomQtyEle.value ) : 1;
    let extraOptionsPrice = 0;
    extraOptions && extraOptions.forEach((ele) => {
        if (ele.checked) {
            const extraID = ele?.dataset.id || null;
            const respondent = ele.dataset.respondent;
            const extraPrice = parseFloat( ele.closest( '[data-price]' ).dataset.price );

            const qty = parseInt(ele.parentElement?.nextElementSibling?.querySelector(`input[name="hb_optional_quantity[${extraID}]"]`)?.value) || 1;
            if ( respondent === 'trip' ) {
            	extraOptionsPrice += extraPrice;
            } else {
            	extraOptionsPrice += extraPrice * roomBookedNight * qty;
            }
        }
    });
    let roomTotalPrice = roomBookedPrice * roomQty + extraOptionsPrice;
    if ( undefined !== hotel_settings.include_tax && hotel_settings.include_tax > 0 ) {
    	roomTotalPrice = roomTotalPrice + roomTotalPrice * hotel_settings.include_tax / 100;
    }
    elForm.querySelector( '.hb-total-price-value' ).innerHTML = utils.wphbRenderPrice( roomTotalPrice );
}

const getRoomBookingPriceDetails = ( button ) => {
	let form = button.closest( 'form' );

	const data = new FormData( form );
	for ( const pair of data.entries() ) {
		const key = pair[ 0 ];
		if ( key === 'wpbh-dates-block' ) continue;
		const value = pair[ 1 ]; // Get the field value

		dataSend[ key ] = value;
	}
	const dateSendFrom = new FormData();
	Object.entries( dataSend ).forEach( ( [ key, value ] ) => {
		dateSendFrom.append( key, value );
	} );

	// Send to sever
	const option = { method: 'POST', headers: {}, body: dateSendFrom };
	if ( 0 !== parseInt( hotel_settings.user_id ) ) {
		option.headers[ 'X-WP-Nonce' ] = hotel_settings.wphb_rest_nonce;
	}
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
		.catch( ( error ) => {} ) .finally( () => {} );
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
			}
		}
	} else if ( target.classList.contains( 'hb-single-room-price-details' ) ) {
		if ( target.closest( '.hb_view_price.hb-room-content' ).querySelector( '.hb-booking-room-details' ) ) {
			target.closest( '.hb_view_price.hb-room-content' ).querySelector( '.hb-booking-room-details' ).remove();
		}
		getRoomBookingPriceDetails( target );
	}

	// faq toggle
	const targetFAQ = target.closest( '._hb_room_faqs__detail' );
	if ( targetFAQ ) {
		targetFAQ.classList.toggle( 'toggled' );
	}
} );

let modalPreview;
document.addEventListener( 'DOMContentLoaded', function ( e ) {
	wphbRoomInitDatePicker();

	// Check view calendar pricing will load calendar pricing
	const elRoomCalendarPricing = document.querySelector( '.wphb-room-calendar-pricing' );
	utils.listenElementViewed( elRoomCalendarPricing, () => {
		if ( elRoomCalendarPricing.classList.contains( 'loaded' ) ) {
			return;
		}

		elRoomCalendarPricing.classList.add( 'loaded' );
		calendarPricing();
	} )
} );
