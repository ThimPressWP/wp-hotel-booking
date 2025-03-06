import flatpickr from 'flatpickr';
import tingle from 'tingle.js';

let elHotelBookingRoom, elTmplDateAvailable, elAddToCart, elForm;
const dataSend = {};

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
	const elDateCheckOut = elForm.querySelector(
		'input[name="check_out_date"]'
	);
	let datePickerCheckIn;
	let datePickerCheckOut;
	let dateMinCheckInCanBook;
	let dateMinCheckOutCanBook;
	const datesBlock = [];
	const dateNow = new Date();
	const dateTomorrow = new Date( dateNow.setDate( dateNow.getDate() + 1 ) );

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

	// Check in date
	const optionCheckIn = {
		dateFormat: 'Y/m/d',
		minDate: 'today',
		disable: datesBlock,
		//defaultDate: dateMinCheckInCanBook,
		disableMobile: true,
		locale: {
			firstDayOfWeek: 1,
		},
		onChange( selectedDates, dateStr, instance ) {
			if ( datePickerCheckOut ) {
				// calculate next day available
				const dateSelected = selectedDates[ 0 ];
				datePickerCheckOut.clear();
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

	// Check out date
	const optionCheckout = {
		dateFormat: 'Y/m/d',
		minDate: 'today',
		disable: datesBlock,
		//defaultDate: dateMinCheckOutCanBook,
		disableMobile: true,
		locale: {
			firstDayOfWeek: 1,
		},
		onChange( selectedDates, dateStr, instance ) {},
	};
	datePickerCheckOut = flatpickr( elDateCheckOut, optionCheckout );
};
const wphbRoomCheckDates = ( formCheckDate ) => {
	const elBtnCheck = formCheckDate.querySelector( 'button[type=submit]' );
	let elLoading = formCheckDate.querySelector( '.wphb-icon' );
	const data = new FormData( formCheckDate );
	for ( const pair of data.entries() ) {
		const key = pair[ 0 ]; // Get the field name
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

// Events
document.addEventListener( 'submit', function ( e ) {
	const target = e.target;

	if ( target.name === 'hb-search-single-room' ) {
		e.preventDefault();
		wphbRoomCheckDates( target );
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
		modalCheckDates = new tingle.modal( {
			onOpen() {
				elHotelBookingRoom.style.display = 'block';
				wphbRoomInitDatePicker();
			},
			onClose() {
				elHotelBookingRoom.style.display = 'none';
			},
		} );

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
	}

	// faq toggle
	const targetFAQ = target.closest('._hb_room_faqs__detail');
    if ( targetFAQ ) {
        targetFAQ.classList.toggle('toggled');
    }
} );

let modalCheckDates;
let modalPreview;
document.addEventListener( 'DOMContentLoaded', function ( e ) {
	const elRoomLoadBookingForm = document.querySelector(
		'#hb_room_load_booking_form'
	);
	if ( ! elRoomLoadBookingForm ) {
		wphbRoomInitDatePicker();
	}
} );
