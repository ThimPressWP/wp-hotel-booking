import flatpickr from 'flatpickr';
'use strict';

let elTmplDateAvailable, elAddToCart;
const dataSend = {};
const wphbDatePicker = () => {
	const elHotelBookingRoom = document.querySelector( '#hotel_booking_room_hidden' );
	elTmplDateAvailable = elHotelBookingRoom.querySelector( '.wphb-room-tmpl-dates-available' );
	elAddToCart = elHotelBookingRoom.querySelector( '.wpdb-room-tmpl-add-to-cart' );
	const elForm = document.querySelector( 'form[name=hb-search-single-room]' );
	const elDateCheckIn = elForm.querySelector( 'input[name="check_in_date"]' );
	const elDateCheckOut = elForm.querySelector( 'input[name="check_out_date"]' );
	const elDatesBlock = elForm.querySelector( 'input[name="wpbh-dates-block"]' );
	let datePickerCheckIn, datePickerCheckOut;

	const dateTimeStampsBlock = JSON.parse( elDatesBlock.value );
	const datesBlock = [];
	dateTimeStampsBlock.forEach( ( timeStamp ) => {
		const date = new Date( timeStamp * 1000 );
		const dateBlock = new Date( date.getFullYear(), date.getMonth(), date.getDate() );
		datesBlock.push( dateBlock );
	} );

	const calculateLastDayCanBook = ( dateSelected, datesCalendar ) => {
		if ( datesBlock.length === 0 ) {
			return datesCalendar <= dateSelected;
		}
		let dateDisableNear;
		datesBlock.some( ( date ) => {
			if ( date > dateSelected ) {
				dateDisableNear = date;
				return true;
			}
		} );

		if ( dateDisableNear ) {
			return datesCalendar >= dateDisableNear || datesCalendar <= dateSelected;
		}
		return datesCalendar <= dateSelected;
	};

	// Check in date
	const optionCheckIn = {
		dateFormat: 'Y/m/d',
		minDate: 'today',
		disable: datesBlock,
		onChange( selectedDates, dateStr, instance ) {
			if ( datePickerCheckOut ) {
				// calculate next day available
				const dateSelected = selectedDates[ 0 ];
				datePickerCheckOut.clear();
				datePickerCheckOut.open();
				datePickerCheckOut.set( 'disable', [ ( datesCalendar ) => {
					return calculateLastDayCanBook( dateSelected, datesCalendar );
				} ] );
			}
		},
	};

	datePickerCheckIn = flatpickr( elDateCheckIn, optionCheckIn );

	// Check out date
	const optionCheckout = {
		dateFormat: 'Y/m/d',
		minDate: 'today',
		disable: datesBlock,
		onChange( selectedDates, dateStr, instance ) {
		},
	};

	datePickerCheckOut = flatpickr( elDateCheckOut, optionCheckout );
};

( function( $ ) {
	function isInteger( a ) {
		return Number( a ) || ( a % 1 === 0 );
	}
	window.mobileCheck = function() {
		let check = false;
		( function( a ) {
			if ( /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test( a ) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test( a.substr( 0, 4 ) ) ) {
				check = true;
			}
		}( navigator.userAgent || navigator.vendor || window.opera ) );
		return check;
	};

	function getId( url ) {
		const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
		const match = url.match( regExp );

		if ( match && match[ 2 ].length == 11 ) {
			return match[ 2 ];
		}
		return 'error';
	}

	var Hotel_Booking_Room_Addon = {
		init() {
			// load add to cart form
			const _self = this,
				_doc = $( document );

			// check option external link
			if ( Hotel_Booking_Blocked_Days.external_link == '' ) {
				_doc.on( 'click', '#hb_room_load_booking_form', _self.load_room_add_to_cart_form );
			}
			// trigger lightbox open
			_doc.on( 'hotel_room_load_add_to_cart_form_open', _self.lightbox_init );

			// form submit
			//_doc.on( 'submit', '.hotel-booking-single-room-action', _self.form_submit );

			// previous step
			_doc.on( 'click', '.hb_previous_step', _self.preStep );

			/* Room Preview */
			_doc.on( 'click', '#hb_room_images .room-preview', _self.room_preview );
		},

		room_preview( e ) {
			e.preventDefault();
			const _self = $( this );
			let src = _self.attr( 'data-preview' );
			if ( src.includes( 'iframe' ) ) {
				const reg = new RegExp( '(?<=src=").*?(?=[\?"])' );
				src = reg.exec( src )[ 0 ];
				if ( getId( src ) != 'error' ) {
					src = 'https://www.youtube.com/watch?v=' + getId( src );
				}
			}
			$.magnificPopup.open( {
				items: {
					src,
					type: 'iframe',
				},
			} );
		},
		is_int( a ) {
			return Number( a ) && a % 1 === 0;
		},
		lightbox_init( e, button, lightbox, taget ) {
			e.preventDefault();
			// search form
			if ( taget === 'hb-room-load-form' ) {
				//Hotel_Booking_Room_Addon.datepicker_init()
				wphbDatePicker();
			}
		},
		form_submit( e ) {
			e.preventDefault();
			const _self = $( this ),
				_form_name = _self.attr( 'name' ),
				_data = Hotel_Booking_Room_Addon.form_data();

			if ( _form_name === 'hb-search-single-room' ) {
				Hotel_Booking_Room_Addon.check_avibility( _self, _data, _self.find( 'button[type="submit"]' ) );
			}
		},
		datepicker_init() {
			const checkin = $( '.hb-search-results-form-container input[name="check_in_date"]' ),
				checkout = $( '.hb-search-results-form-container input[name="check_out_date"]' ),
				today = new Date(),
				tomorrow = new Date();

			let date_range = $( document ).triggerHandler( 'hotel_booking_min_check_in_date' );

			let checkin_range_checkout = hotel_settings.min_booking_date;
			if ( ! isInteger( checkin_range_checkout ) ) {
				checkin_range_checkout = 1;
			}

			if ( ! Hotel_Booking_Room_Addon.is_int( date_range ) ) {
				date_range = 1;
			}

			tomorrow.setDate( today.getDate() + date_range );
			const unavailableDates = Hotel_Booking_Blocked_Days.blocked_days;
			function unavailable( date ) {
				const offset = date.getTimezoneOffset();
				const timestamp = Date.parse( date ) - offset * 60 * 1000;
				const newdate_nonutc = new Date( timestamp );
				const dmy = newdate_nonutc.toISOString().split( 'T' )[ 0 ];
				if ( $.inArray( dmy, unavailableDates ) < 0 ) {
					return [ true, '', 'Book Now' ];
				}
				return [ false, '', 'Booked Out' ];
			}

			function unavailableCheckIn( date ) {
				return [ true, '', 'Book Now' ];
			}

			checkin.datepicker( {
				dateFormat: hotel_booking_i18n.date_time_format,
				monthNames: hotel_booking_i18n.monthNames,
				monthNamesShort: hotel_booking_i18n.monthNamesShort,
				dayNames: hotel_booking_i18n.dayNames,
				dayNamesShort: hotel_booking_i18n.dayNamesShort,
				dayNamesMin: hotel_booking_i18n.dayNamesMin,
				minDate: today,
				maxDate: '+365D',
				beforeShowDay: unavailable,
				onSelect( selected ) {
					const checkout_date = checkin.datepicker( 'getDate' ),
						time = new Date( checkout_date );

					checkout_date.setDate( checkout_date.getDate() + checkin_range_checkout );
					checkout.datepicker( 'option', 'minDate', checkout_date );
				},
				onClose() {
					checkout.datepicker( 'show' );
				},
			} );

			checkout.datepicker( {
				dateFormat: hotel_booking_i18n.date_time_format,
				monthNames: hotel_booking_i18n.monthNames,
				monthNamesShort: hotel_booking_i18n.monthNamesShort,
				dayNames: hotel_booking_i18n.dayNames,
				dayNamesShort: hotel_booking_i18n.dayNamesShort,
				dayNamesMin: hotel_booking_i18n.dayNamesMin,
				minDate: tomorrow,
				maxDate: '+365D',
				beforeShowDay: unavailableCheckIn,
				onSelect( selected ) {
					const checkin_date = checkout.datepicker( 'getDate' ),
						time = new Date( checkin_date );
					checkin_date.setDate( checkin_date.getDate() - checkin_range_checkout );
					checkin.datepicker( 'option', 'maxDate', checkin_date );
				},
			} );

			$( document ).triggerHandler( 'hotel_booking_room_form_datepicker_init', checkin, checkout );
		},
		beforeAjax( _taget ) {
			_taget.attr( 'disabled', 'disabled' );
			_taget.html( '<span class="lds-ring"><span></span><span></span><span></span><span></span></span>' + _taget.html() );
			$( document ).triggerHandler( 'hotel_booking_room_form_before_ajax' );
		},
		afterAjax( _taget ) {
			_taget.find( 'span.lds-ring' ).remove();
			_taget.removeAttr( 'disabled' );
			$( document ).triggerHandler( 'hotel_booking_room_form_after_ajax' );
		},
		load_room_add_to_cart_form( e ) {
			e.preventDefault();
			const _self = $( this ),
				_room_id = _self.attr( 'data-id' ),
				_room_name = _self.attr( 'data-name' ),
				_doc = $( document ),
				_taget = 'hb-room-load-form',
				_lightbox = '#hotel_booking_room_hidden';

			$( _lightbox ).html( wp.template( _taget )( { _room_id, _room_name } ) );
			$.magnificPopup.open( {
				type: 'inline',
				items: {
					src: '#hotel_booking_room_hidden',
				},
				callbacks: {
					open() {
						_doc.triggerHandler( 'hotel_room_load_add_to_cart_form_open', [ _self, _lightbox, _taget ] );
					},
				},
			} );
			return false;
		},
		check_avibility( form, _data, _taget ) {
			$.ajax( {
				url: hotel_settings.ajax,
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
				type: 'POST',
				data: _data,
				dataType: 'json',
				beforeSend() {
					Hotel_Booking_Room_Addon.beforeAjax( _taget );
				},
			} ).done( function( res ) {
				Hotel_Booking_Room_Addon.afterAjax( _taget );
				if ( typeof res.status === 'undefined' ) {
					return;
				}

				const { status, message, data } = res;
				if ( status === 'error' ) {
					Hotel_Booking_Room_Addon.append_messages( form, message );
					return;
				}

				const qty = parseInt( data.qty );

				// set list qty for room
				const elNumRoom = elAddToCart.querySelector( 'select[name=hb-num-of-rooms]' );
				if ( elNumRoom ) {
					if ( qty > 1 ) {
						for ( let i = 2; i <= qty; i++ ) {
							elNumRoom.insertAdjacentHTML( 'beforeend', '<option value="' + i + '">' + i + '</option>' );
						}
					}
				}

				//form.replaceWith(wp.template('hb-room-load-form-cart')(data));
				elTmplDateAvailable.style.display = 'none';
				elAddToCart.style.display = 'block';
			} ).fail( function() {
				Hotel_Booking_Room_Addon.afterAjax( _taget );
			} );
		},
		preStep( e ) {
			e.preventDefault();
			elTmplDateAvailable.style.display = 'block';
			elAddToCart.style.display = 'none';
		},
		sanitize() {
			const _form = $( 'form[name="hb-search-single-room"]' ),
				checkin = _form.find( 'input[name="check_in_date"]' ),
				check_out = _form.find( 'input[name="check_out_date"]' ),
				errors = [];

			if ( checkin.datepicker( 'getDate' ) === null ) {
				checkin.addClass( 'error' );
				errors.push( '<p>' + hotel_booking_i18n.empty_check_in_date + '</p>' );
			}

			if ( check_out.datepicker( 'getDate' ) === null ) {
				check_out.addClass( 'error' );
				errors.push( '<p>' + hotel_booking_i18n.empty_check_out_date + '</p>' );
			}

			if ( errors.length > 0 ) {
				Hotel_Booking_Room_Addon.append_messages( _form, errors );
				return false;
			}
			Hotel_Booking_Room_Addon.append_messages( _form );

			return true;
		},
		form_data() {
			const form = document.querySelector( '.hotel-booking-single-room-action' );
			const formData = new FormData( form );

			// Iterate over each field in the form
			for ( const pair of formData.entries() ) {
				const key = pair[ 0 ]; // Get the field name
				const value = pair[ 1 ]; // Get the field value

				if ( ! dataSend.hasOwnProperty( key ) ) {
					dataSend[ key ] = value;
				}
			}

			dataSend.timezone_brwoser = Intl.DateTimeFormat().resolvedOptions().timeZone;

			return dataSend;
		},
		append_messages( form, error ) {
			form.find( '.hotel_booking_room_errors' ).remove();
			form.find( '.hb-booking-room-form-head' ).append( '<div class="hotel_booking_room_errors">' + error + '</div>' );
			setTimeout( () => {
				form.find( '.hotel_booking_room_errors' ).remove();
			}, 2000 );
		},

	};

	$( document ).ready( function() {
		Hotel_Booking_Room_Addon.init();
	} );
}( jQuery ) );

document.addEventListener( 'submit', function( e ) {
	const target = e.target;

	if ( target.name === 'hb-search-single-room' ) {
		e.preventDefault();
		const elBtnCheck = target.querySelector( 'button[type=submit]' );
		const elLoading = target.querySelector( '.wphb-icon' );
		const data = new FormData( target );
		for ( const pair of data.entries() ) {
			const key = pair[ 0 ]; // Get the field name
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
			option.headers[ 'X-WP-Nonce' ] = hotel_settings.nonce;
		}

		elLoading.classList.remove( 'hide' );
		elLoading.classList.toggle( 'loading' );
		elBtnCheck.setAttribute( 'disabled', 'disabled' );

		fetch(
			hotel_settings.ajax,
			option
		).then( ( response ) => response.json() )
			.then( ( res ) => {
				const { status, message, data } = res;

				if ( status === 'error' ) {
					const elMesErros = target.querySelectorAll( '.hotel_booking_room_errors' );
					if ( elMesErros ) {
						elMesErros.forEach( ( el ) => {
							el.remove();
						} );
					}
					target.querySelector( '.hb-booking-room-form-head' ).insertAdjacentHTML(
						'beforeend',
						`<div class="hotel_booking_room_errors">${ message }</div>`
					);
					setTimeout( () => {
						target.querySelector( '.hotel_booking_room_errors' ).remove();
					}, 2000 );
					return;
				}

				// set list qty for room
				const elNumRoom = elAddToCart.querySelector( 'select[name=hb-num-of-rooms]' );
				console.log( elNumRoom );
				if ( elNumRoom ) {
					for ( let i = 1; i <= parseInt( data.qty ); i++ ) {
						elNumRoom.insertAdjacentHTML( 'beforeend', '<option value="' + i + '">' + i + '</option>' );
					}
				}

				//form.replaceWith(wp.template('hb-room-load-form-cart')(data));
				elTmplDateAvailable.style.display = 'none';
				elAddToCart.style.display = 'block';
			} )
			.catch( ( error ) => {
				console.error( 'Error:', error );
			} )
			.finally( () => {
				elBtnCheck.removeAttribute( 'disabled' );
				elLoading.classList.add( 'hide' );
				elLoading.classList.toggle( 'loading' );
			} );
	}

	if ( target.name === 'hb-search-results' ) {
		e.preventDefault();
		const elBtnSubmit = target.querySelector( 'button[type=submit]' );
		const elLoading = target.querySelector( '.wphb-icon' );
		const data = new FormData( target );
		for ( const pair of data.entries() ) {
			const key = pair[ 0 ]; // Get the field name
			const value = pair[ 1 ]; // Get the field value

			dataSend[ key ] = value;
		}

		//console.log( dataSend );

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

		fetch(
			hotel_settings.ajax,
			option
		).then( ( response ) => response.json() )
			.then( ( res ) => {
				const { status, message, data } = res;
				if ( message === 'error' ) {
					alert( data.message );
					elBtnSubmit.removeAttribute( 'disabled' );
				} else {
					window.location.href = data.redirect;
				}
			} )
			.catch( ( error ) => {
				elBtnSubmit.removeAttribute( 'disabled' );
			} )
			.finally( () => {
				elLoading.classList.add( 'hide' );
				elLoading.classList.toggle( 'loading' );
			} );
	}
} );
