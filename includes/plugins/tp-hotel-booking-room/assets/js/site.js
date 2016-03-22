/*
* @Author: ducnvtt
* @Date:   2016-03-21 08:50:48
* @Last Modified by:   ducnvtt
* @Last Modified time: 2016-03-22 15:38:53
*/

'use strict';
(function($){

	var Hotel_Booking_Room_Addon = {

		init: function() {
			// datepicker init
			this.datepicker_init();

			// step
			this.book_steps.init();
			// check avibility
			this.check_avibility();
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
				dateFormat 		: hotel_booking_l18n.date_time_format,
				monthNames 	  	: hotel_booking_l18n.monthNames,
				monthNamesShort	: hotel_booking_l18n.monthNamesShort,
				dayNames 		: hotel_booking_l18n.dayNames,
				dayNamesShort 	: hotel_booking_l18n.dayNamesShort,
				dayNamesMin		: hotel_booking_l18n.dayNamesMin,
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
				dateFormat 		: hotel_booking_l18n.date_time_format,
				monthNames 	  	: hotel_booking_l18n.monthNames,
				monthNamesShort	: hotel_booking_l18n.monthNamesShort,
				dayNames 		: hotel_booking_l18n.dayNames,
				dayNamesShort 	: hotel_booking_l18n.dayNamesShort,
				dayNamesMin		: hotel_booking_l18n.dayNamesMin,
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

		book_steps: {
			steps: false,
			current_step_active: false,
			current_step: 0,
			current_step_content: 0,

			init: function () {
				this.steps = $('#hbr-nav a'),
				this.current_step_active = $('#hbr-nav a.active'),
				this.current_step = this.current_step_active.index();
				this.current_step_content = $( $(this.current_step).attr( 'href' ) );

				if( this.current_step_active.length != 1 ) {
					this.steps.removeClass( 'active' );
					this.current_step_active = this.steps.first().addClass( 'active' );

					this.current_step = 0;

					this.current_step_content = $( this.current_step_active.attr( 'href' ) ).addClass('active');
				}

				this.steps.on( 'click', function(e) {
					e.preventDefault();
					var _self = $(this),
						_self_step = _self.index();

					if ( Hotel_Booking_Room_Addon.book_steps.current_step === _self_step || Hotel_Booking_Room_Addon.book_steps.current_step <= _self_step  ) {
						return false;
					}

					Hotel_Booking_Room_Addon.book_steps.switch_step( _self );
					return false;
				});
			},

			switch_step: function ( st ) {

				// reset
				Hotel_Booking_Room_Addon.book_steps.reset_step();

				this.current_step_active = st.addClass('active' ),
				this.current_step = this.current_step_active.index();
				this.current_step_content = $( $(this.current_step_active).attr( 'href' ) ).addClass( 'active' );
			},

			next_step: function () {
				Hotel_Booking_Room_Addon.book_steps.reset_step();
				// new
				this.current_step_active = this.current_step_active.next();
				this.current_step = this.current_step_active.index();
				this.current_step_active.addClass( 'active' );

				this.current_step_content = $( $(this.current_step_active).attr( 'href' ) ).addClass( 'active' );
			},

			reset_step: function(){
				// reset
				this.steps.removeClass( 'active' );
				this.current_step_content.removeClass( 'active' );
			},
		},

		beforeAjax: function() {
			$('.hotel_booking_room_overflow').addClass('active');
		},

		afterAjax: function(){
			$('.hotel_booking_room_overflow').removeClass('active');
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
				errors.push( '<p>' + hotel_booking_l18n.empty_check_in_date + '</p>' );
			}

			if ( check_out === null ) {
				errors.push( '<p>' + hotel_booking_l18n.empty_check_out_date + '</p>' );
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
