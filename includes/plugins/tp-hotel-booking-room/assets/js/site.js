/*
* @Author: ducnvtt
* @Date:   2016-03-21 08:50:48
* @Last Modified by:   ducnvtt
* @Last Modified time: 2016-04-20 16:37:58
*/

'use strict';
(function($){

	var Hotel_Booking_Room_Addon = {

		init: function() {
			// load add to cart form
			var _self = this,
				_doc = $( document );
			_doc.on( 'click', '#hb_room_load_booking_form', _self.load_room_add_to_cart_form );
			// check avibility
			_self.check_avibility();
		},

		datepicker_init: function() {
			var checkin = $('.hotel_booking_room_check_in'),
				checkin_timestamp = $('.hotel_booking_room_check_in_timestamp'),
				checkout = $('.hotel_booking_room_check_out'),
				checkout_timestamp = $('.hotel_booking_room_check_out_timestamp'),
				today = new Date(),
				tomorrow = new Date();

			tomorrow.setDate( today.getDate() + 1 );

			checkin.datepicker({
				dateFormat 		: hotel_booking_i18n.date_time_format,
				monthNames 	  	: hotel_booking_i18n.monthNames,
				monthNamesShort	: hotel_booking_i18n.monthNamesShort,
				dayNames 		: hotel_booking_i18n.dayNames,
				dayNamesShort 	: hotel_booking_i18n.dayNamesShort,
				dayNamesMin		: hotel_booking_i18n.dayNamesMin,
				minDate       	: tomorrow,
				maxDate       	: '+365D',
				onSelect: function( selected ){
					var checkout_date = checkin.datepicker('getDate');
					var time = new Date( checkout_date );
					checkout_date.setDate( checkout_date.getDate() + 1 );
					checkout.datepicker( 'option', 'minDate', checkout_date );

					checkin_timestamp.val( time.getTime() / 1000 - ( time.getTimezoneOffset() * 60 ) );
				}
			});

			checkout.datepicker({
				dateFormat 		: hotel_booking_i18n.date_time_format,
				monthNames 	  	: hotel_booking_i18n.monthNames,
				monthNamesShort	: hotel_booking_i18n.monthNamesShort,
				dayNames 		: hotel_booking_i18n.dayNames,
				dayNamesShort 	: hotel_booking_i18n.dayNamesShort,
				dayNamesMin		: hotel_booking_i18n.dayNamesMin,
				minDate       	: tomorrow,
				maxDate       	: '+365D',
				onSelect: function( selected ){
					var checkin_date = checkout.datepicker('getDate');
					var time = new Date( checkin_date );
					checkin_date.setDate( checkin_date.getDate() );
					checkin.datepicker( 'option', 'maxDate', checkin_date );

					checkout_timestamp.val( time.getTime() / 1000 - ( time.getTimezoneOffset() * 60 ) );
				}
			});
		},

		beforeAjax: function() {
			$('.hotel_booking_room_overflow').addClass('active');
		},

		afterAjax: function(){
			$('.hotel_booking_room_overflow').removeClass('active');
		},

		load_room_add_to_cart_form: function( e ){
			e.preventDefault();
			var _self = $( this ),
				_room_id = _self.attr( 'data-id' );


			return false;
		},

		check_avibility: function() {
			$( document ).on( 'submit', '.hotel_booking_room_check_available', function(e) {
				e.preventDefault();

				var _form = $(this),
					sanitize = Hotel_Booking_Room_Addon.sanitize();

				if ( sanitize === false ) {
					return;
				}

				$.ajax({
					type: 'POST',
					url: hotel_settings.ajax,
					data: _form.serializeArray(),
					beforeSend: function(){
						Hotel_Booking_Room_Addon.beforeAjax();
					}
				}).done( function( res ){
					Hotel_Booking_Room_Addon.afterAjax();
					if ( typeof res.status === 'undefined' ) {
						return;
					}
					if ( res.status === false && typeof res.message !== 'undefined' ) {
						_form.find('#hbr-nav').after( '<div class="hotel_booking_room_errors">' + res.message + '</div>' );
					} else if( typeof res.qty !== 'undefined' ) {
						var template = wp.template( 'hotel-booking-select-qty' );
						template = template( res );
						$('#hbr-quantity ul').prepend( template );

						if( typeof window.Dropkick !== 'undefined' ) {
							$('select[name="hotel_booking_room_qty"]').dropkick();
						}
						Hotel_Booking_Room_Addon.book_steps.next_step();
					}
				}).fail( function() {
					Hotel_Booking_Room_Addon.afterAjax();
				});
			});
		},

		sanitize: function() {

			$('.hotel_booking_room_errors').slideUp( 300, function() {
				$(this).remove();
			});
			var _form = $('.hotel_booking_room_check_available'),
				checkin = $( 'input[name="hotel_booking_room_check_in"]' ).datepicker( 'getDate' ),
				check_out = $( 'input[name="hotel_booking_room_check_out"]' ).datepicker( 'getDate' ),
				errors = [];

			if ( checkin === null ) {
				errors.push( '<p>' + hotel_booking_i18n.empty_check_in_date + '</p>' );
			}

			if ( check_out === null ) {
				errors.push( '<p>' + hotel_booking_i18n.empty_check_out_date + '</p>' );
			}

			if ( errors.length > 0 ) {
				_form.find('#hbr-nav').after( '<div class="hotel_booking_room_errors">' + errors.join( '' ) + '</div>' );
				return false;
			}

			return true;
		},

	};

	$(document).ready(function(){
		Hotel_Booking_Room_Addon.init();
	});

})(jQuery);
