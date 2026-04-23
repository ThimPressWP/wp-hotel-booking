import flatpickr from 'flatpickr';
import flatpickrLocales from 'flatpickr/dist/l10n/index.js';
import {
	FLATPICKR_RANGE_SEPARATOR,
	applyFlatpickrLocaleConfig,
	createBookingDateHelpers,
	createFlatpickrConfig,
	createFlatpickrLocaleConfig,
	formatDateRangeValue,
	syncBookingDateFieldsForUi,
	validateBookingDateRange,
} from './flatpickr-locale-utils.js';

( function( $ ) {
	const $doc = $( document );

	if ( Date.prototype.compareWith == undefined ) {
		Date.prototype.compareWith = function( d ) {
			if ( typeof d == 'string' ) {
				d = new Date( d );
			}

			const thisTime = parseInt( this.getTime() / 1000 ),
				compareTime = parseInt( d.getTime() / 1000 );
			if ( thisTime > compareTime ) {
				return 1;
			} else if ( thisTime < compareTime ) {
				return -1;
			}
			return 0;
		};
	}

	function isInteger( a ) {
		return Number( a ) || ( a % 1 === 0 );
	}

	function isEmail( email ) {
		return new RegExp( '^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$' ).test( email );
	}

	function parseJSON( data ) {
		if ( ! $.isPlainObject( data ) ) {
			const m = data.match( /<!-- HB_AJAX_START -->(.*)<!-- HB_AJAX_END -->/ );
			try {
				if ( m ) {
					data = $.parseJSON( m[ 1 ] );
				} else {
					data = $.parseJSON( data );
				}
			} catch ( e ) {
				data = {};
			}
		}
		return data;
	}

	function hotel_checkout_fetch_error( msgs ) {
		if ( msgs.length === 0 ) {
			return;
		}
		$( '.hotel_checkout_errors' ).slideUp().remove();
		const html = [];

		html.push( '<div class="hotel_checkout_errors">' );
		for ( let i = 0; i < msgs.length; i++ ) {
			html.push( '<p>' + msgs[ i ] + '</p>' );
		}
		html.push( '</div>' );
		$( '#hb-payment-form h3:first-child' ).after( html.join( '' ) );
	}

	function validateOrder( $form ) {
		const $title = $form.find( 'select[name="title"]' ),
			mesgs = [];
		if ( $title.length === 1 && -1 === $title.val() ) {

			mesgs.push( hotel_booking_i18n.empty_customer_title );
			$title.parents( 'div:first' ).addClass( 'error' );
		}

		const $firstName = $form.find( 'input[name="first_name"]' );
		if ( $firstName.length === 1 && ! $firstName.val() ) {

			mesgs.push( hotel_booking_i18n.empty_customer_first_name );
			$firstName.parents( 'div:first' ).addClass( 'error' );
		}

		const $lastName = $form.find( 'input[name="last_name"]' );
		if ( $lastName.length === 1 && ! $lastName.val() ) {

			mesgs.push( hotel_booking_i18n.empty_customer_last_name );
			$lastName.parents( 'div:first' ).addClass( 'error' );
		}

		const $address = $form.find( 'input[name="address"]' );
		if ( $address.length === 1 && ! $address.val() ) {

			mesgs.push( hotel_booking_i18n.empty_customer_address );
			$address.parents( 'div:first' ).addClass( 'error' );
		}

		const $city = $form.find( 'input[name="city"]' );
		if ( $city.length === 1 && ! $city.val() ) {

			mesgs.push( hotel_booking_i18n.empty_customer_city );
			$city.parents( 'div:first' ).addClass( 'error' );
		}

		const $state = $form.find( 'input[name="state"]' );
		if ( $state.length === 1 && ! $state.val() ) {

			mesgs.push( hotel_booking_i18n.empty_customer_state );
			$state.parents( 'div:first' ).addClass( 'error' );
		}

		const $postalCode = $form.find( 'input[name="postal_code"]' );
		if ( $postalCode.length === 1 && ! $postalCode.val() ) {

			mesgs.push( hotel_booking_i18n.empty_customer_postal_code );
			$postalCode.parents( 'div:first' ).addClass( 'error' );
		}

		const $country = $form.find( 'select[name="country"]' );
		if ( $country.length === 1 && ! $country.val() ) {

			mesgs.push( hotel_booking_i18n.empty_customer_country );
			$country.parents( 'div:first' ).addClass( 'error' );
		}

		const $phone = $form.find( 'input[name="phone"]' );
		if ( $phone.length === 1 && ! $phone.val() ) {

			mesgs.push( hotel_booking_i18n.empty_customer_phone );
			$phone.parents( 'div:first' ).addClass( 'error' );
		}

		const $email = $form.find( 'input[name="email"]' );
		if ( $email.length === 1 && ! isEmail( $email.val() ) ) {

			mesgs.push( hotel_booking_i18n.customer_email_invalid );
			$email.parents( 'div:first' ).addClass( 'error' );
		}

		const $payment_method = $form.find( 'input[name="hb-payment-method"]:checked' );
		if ( $payment_method.length === 0 ) {

			mesgs.push( hotel_booking_i18n.no_payment_method_selected );
			$payment_method.parents( 'div:first' ).addClass( 'error' );
		}

		const $tos = $form.find( 'input[name="tos"]' );
		if ( $tos.length && ! $tos.is( ':checked' ) ) {
			alert( hotel_booking_i18n.confirm_tos );
			mesgs.push( hotel_booking_i18n.confirm_tos );
			$tos.addClass( 'error' );
		}
		if ( $( 'input[name="existing-customer-id"]' ).val() ) {
			if ( $email.val() != $( 'input[name="existing-customer-email"]', $form ).val() ) {
				mesgs.push( hotel_booking_i18n.customer_email_not_match );
			}
			$email.parents( 'div:first' ).addClass( 'error' );
			$form.find( 'input[name="existing-customer-id"]' ).parents( 'div:first' ).addClass( 'error' );
		}

		if ( mesgs.length > 0 ) {
			hotel_checkout_fetch_error( mesgs );
			return false;
		}
		return true;
	}

	function orderSubmit( form ) {
		const action = window.location.href.replace( /\?.*/, '' );
		form.attr( 'action', action );
		const button = form.find( 'button[type="submit"]' );
		const old_text = button.html();
		if ( form.triggerHandler( 'hotel_booking_place_order' ) !== false ) {
			$.ajax( {
				type: 'POST',
				url: hotel_settings.ajax,
				data: form.serialize(),
				dataType: 'text',
				beforeSend() {
					button.attr( 'disabled', 'disabled' );
					button.html( '<span class="lds-ring"><span></span><span></span><span></span><span></span></span>' + button.html() );
				},
				success( code ) {
					button.html( old_text );
					try {
						const response = parseJSON( code );
						if ( response.result === 'success' ) {
							if ( response.redirect !== undefined ) {
								window.location.href = response.redirect;
							}
						} else if ( typeof response.message !== 'undefined' ) {
							alert( response.message );
						}
					} catch ( e ) {
						alert( e );
					}
				},
				error() {
					button.html( old_text );
					hotel_checkout_fetch_error( [ hotel_booking_i18n.waring.try_again ] );
				},

			} );
		}
		return false;
	}

	function applyCoupon() {
		const $coupon = $( 'input[name="hb-coupon-code"]' );
		const table = $coupon.parents( 'table' );
		if ( ! $coupon.val() ) {
			alert( hotel_booking_i18n.enter_coupon_code );
			$coupon.focus();
			return false;
		}
		$.ajax( {
			type: 'POST',
			url: hotel_settings.ajax,
			data: {
				action: 'hotel_booking_apply_coupon',
				code: $coupon.val(),
				nonce: hotel_settings.nonce,
			},
			dataType: 'text',
			beforeSend() {
				table.hb_overlay_ajax_start();
			},
			success( code ) {
				table.hb_overlay_ajax_stop();
				try {
					const response = parseJSON( code );
					if ( response.result == 'success' ) {
						window.location.href = window.location.href;
					} else {
						alert( response.message );
					}
				} catch ( e ) {
					alert( e );
				}
			},
			error() {
				table.hb_overlay_ajax_stop();
				alert( 'error' );
			},
		} );
	}

	/**
	 * HB_Booking_Cart object class
	 * @type {Object}
	 */
	const HB_Booking_Cart = {
		init() {
			this.remove_cart();
		},
		hb_add_to_cart_callback( data, callback ) {
			const mini_cart = $( '.hotel_booking_mini_cart' );
			const length = mini_cart.length;
			let template = wp.template( 'hb-minicart-item' );
			template = template( data );

			if ( length > 0 ) {
				for ( let i = 0; i < length; i++ ) {
					let cart = $( mini_cart[ i ] ),
						cart_item = $( mini_cart[ i ] ).find( '.hb_mini_cart_item' ),
						insert = false,
						empty = cart.find( '.hb_mini_cart_empty' ),
						footer_ele = cart.find( '.hb_mini_cart_footer' ),
						items_length = cart_item.length;

					if ( items_length === 0 ) {
						const footer = wp.template( 'hb-minicart-footer' );
						const ele = footer_ele;
						if ( empty.length === 1 ) {
							empty.after( footer( {} ) );
							empty.before( template );
						} else {
							footer_ele.before( template );
						}
						insert = true;
						break;
					} else {
						for ( let y = 0; y < items_length; y++ ) {
							const item = $( cart_item[ y ] ),
								cart_id = item.attr( 'data-cart-id' );

							if ( data.cart_id === cart_id ) {
								item.replaceWith( template );
								insert = true;
								break;
							}
						}

						if ( insert === false ) {
							footer_ele.before( template );
						}
					}
				}
			}

			$( '.hb_mini_cart_empty' ).remove();
			var timeout = setTimeout( function() {
				$( '.hb_mini_cart_item' ).removeClass( 'active' );
				clearTimeout( timeout );
			}, 3500 );

			if ( typeof callback !== 'undefined' ) {
				callback();
			}
		},
		hb_remove_cart_item_callback( cart_id, res ) {
			const minicart = $( '.hotel_booking_mini_cart' );
			for ( var i = 0; i < minicart.length; i++ ) {
				const cart = $( minicart[ i ] );
				let items = cart.find( '.hb_mini_cart_item' );

				for ( var y = 0; y < items.length; y++ ) {
					var _item = $( items[ y ] ),
						cart_item_id = _item.attr( 'data-cart-id' );
					if ( cart_id === cart_item_id ) {
						_item.remove();
						break;
					}
				}

				// append message empty cart
				items = cart.find( '.hb_mini_cart_item' );
				if ( items.length === 0 ) {
					const empty = wp.template( 'hb-minicart-empty' );
					cart.find( '.hb_mini_cart_footer' ).remove();
					cart.append( empty( {} ) );
					break;
				}
			}

			const cart_table = $( '#hotel-booking-payment, #hotel-booking-cart' );

			if ( cart_table.length > 0 ) {
				$(`tr[data-cart-id="${cart_id}"]`).remove();
				$(`tr[data-parent-id="${cart_id}"]`).remove();

				if ( typeof res.sub_total !== 'undefined' ) {
					cart_table.find( 'span.hb_sub_total_value' ).html( res.sub_total );
				}

				if ( typeof res.grand_total !== 'undefined' ) {
					cart_table.find( 'span.hb_grand_total_value' ).html( res.grand_total );
				}

				if ( typeof res.advance_payment !== 'undefined' ) {
					cart_table.find( 'span.hb_advance_payment_value' ).html( res.advance_payment );
				}
				// if cart is empty. reload page
				if ( $( 'tr.hb_checkout_item' ).length === 0 ) {
					window.location.href = window.location.href;
				}
			}
		},
		remove_cart() {
			// var updateOrderButton
			$( document ).on( 'click', '.hb_remove_cart_item', function( e ) {
				e.preventDefault();

				const tr = $( this ).parents( 'tr' ),
					cart_item = $( this ).attr( 'data-cart-id' );
				$.ajax( {
					url: hotel_settings.ajax,
					type: 'POST',
					data: {
						cart_id: cart_item,
						nonce: hotel_settings.nonce,
						action: 'hotel_booking_ajax_remove_item_cart',
					},
					dataType: 'html',
					beforeSend() {
						tr.hb_overlay_ajax_start();
					},
				} ).done( function( res ) {
					res = parseJSON( res );
					if ( typeof res.status === 'undefined' || res.status !== 'success' ) {
						alert( hotel_booking_i18n.waring.try_again );
					}

					// update woo cart when remove room from cart
					$( 'body' ).trigger( 'hb_removed_item_to_cart' );

					if ( typeof res.sub_total !== 'undefined' ) {
						$( 'span.hb_sub_total_value' ).html( res.sub_total );
					}

					if ( typeof res.grand_total !== 'undefined' ) {
						$( 'span.hb_grand_total_value' ).html( res.grand_total );
					}

					if ( typeof res.advance_payment !== 'undefined' ) {
						$( 'span.hb_advance_payment_value' ).html( res.advance_payment );
					}
					tr.hb_overlay_ajax_stop();
					tr.remove();
					HB_Booking_Cart.hb_remove_cart_item_callback( cart_item, res );
				} );
			} );

			//remove minicart item
			$( '.hotel_booking_mini_cart' ).on( 'click', '.hb_mini_cart_remove', function( event ) {
				event.preventDefault();
				const minicart = $( '.hotel_booking_mini_cart' );
				const item = $( this ).parents( '.hb_mini_cart_item' );
				const cart_id = item.attr( 'data-cart-id' );

				$.ajax( {
					url: hotel_settings.ajax,
					type: 'POST',
					data: {
						cart_id,
						nonce: hotel_settings.nonce,
						action: 'hotel_booking_ajax_remove_item_cart',
					},
					dataType: 'html',
					beforeSend() {
						item.addClass( 'before_remove' );
						item.hb_overlay_ajax_start();
					},
				} ).done( function( res ) {
					res = parseJSON( res );
					if ( typeof res.status === 'undefined' || res.status !== 'success' ) {
						alert( hotel_booking_i18n.waring.try_again );
						return;
					}

					HB_Booking_Cart.hb_remove_cart_item_callback( cart_id, res );
					item.hb_overlay_ajax_stop();
				} );
			} );
		},
	};

	$( document ).ready( function() {

		HB_Booking_Cart.init();

		$( 'form#hb-payment-form' ).submit( function( e ) {
			e.preventDefault();
			const _self = $( this );

			const action = window.location.href.replace( /\?.*/, '' );
			_self.find( '.hotel_checkout_errors' ).slideUp().remove();
			_self.find( 'input, select' ).parents( 'div:first-child' ).removeClass( 'error' );
			try {
				if ( _self.triggerHandler( 'hb_order_submit' ) === false ) {
					return false;
				}

				_self.attr( 'action', action );

				if ( ! validateOrder( _self ) ) {
					return false;
				}
				orderSubmit( _self );
			} catch ( e ) {
				alert( e );
			}
		} );

		$doc.on( 'click', '.hb-view-booking-room-details, .hb_search_room_item_detail_price_close', function( e ) {
			e.preventDefault();
			const _self = $( this );
			const _details = _self.parents( '.hb-room-content' ).find( '.hb-booking-room-details' );

			_details.toggleClass( 'active' );


		} ).on( 'click', 'input[name="hb-payment-method"]', function() {
			if ( this.checked ) {
				$( '.hb-payment-method-form:not(.' + this.value + ')' ).slideUp();
				$( '.hb-payment-method-form.' + this.value + '' ).slideDown();
			}
		} ).on( 'click', '#hb-apply-coupon', function() {
			applyCoupon();
		} ).on( 'click', '#hb-remove-coupon', function( evt ) {
			evt.preventDefault();
			const table = $( this ).parents( 'table' );
			$.ajax( {
				url: hotel_settings.ajax,
				type: 'post',
				dataType: 'html',
				data: {
					action: 'hotel_booking_remove_coupon',
                    nonce: hotel_settings.nonce,
				},
				beforeSend() {
					table.hb_overlay_ajax_start();
				},
				success( response ) {
					table.hb_overlay_ajax_stop();
					response = parseJSON( response );
					if ( response.result == 'success' ) {
						window.location.href = window.location.href;
					}
				},
			} );
		} );

		// single room tabs video & gallery
		$(".hb_single_room .images_video_tabs a").click(function(e) {
			e.preventDefault();

			var parentTab = $(this).closest(".images_video_tabs");
			var target = $(this).attr("href");

			parentTab.find("a").removeClass("active");
			$(this).addClass("active");

			parentTab.nextAll(".room_media_content").hide();
			$(target).fadeIn();
		});

		$(".images_video_tabs a.active").trigger("click");


		// single room detail tabs
		const hb_single_details = $( '.hb_single_room_details' );
		const hb_single_details_tab = hb_single_details.find( '.hb_single_room_tabs' );
		const hb_single_details_content = hb_single_details.find( '.hb_single_room_tabs_content' );
		const hb_single_tab_details = $( '.hb_single_room_tab_details' );
		const hb_current_uri = window.location.href;

		const commentID = hb_current_uri.match( /\#comment-[0-9]+/gi );
		const params = new URL( document.location.toString() ).searchParams;
		const tab = params.get( 'tab' );
		const isReviewTab = tab === 'review';

		if ( ( commentID && typeof commentID[ 0 ] !== 'undefined' ) || isReviewTab ) {
			hb_single_details_tab.find( 'a' ).removeClass( 'active' );
			hb_single_details_tab.find( 'a[href="#hb_room_reviews"]' ).addClass( 'active' );
		} else {
			hb_single_details_tab.find( 'a:first' ).addClass( 'active' );
			$( '.hb_single_room_tabs_content .hb_single_room_tab_details:not(:first)' ).hide();
		}

		hb_single_tab_details.hide();
		const tabActive = hb_single_details_tab.find( 'a.active' ).attr( 'href' );
		hb_single_details_content.find( tabActive ).fadeIn();

		hb_single_details_tab.find( 'a' ).on( 'click', function( event ) {
			event.preventDefault();
			hb_single_details_tab.find( 'a' ).removeClass( 'active' );
			$( this ).addClass( 'active' );
			const tab_id = $( this ).attr( 'href' );
			hb_single_tab_details.hide();
			hb_single_details_content.find( tab_id ).fadeIn();

		} );

		$( '.hb-rating-input' ).rating();

		$( '#commentform' ).submit( function() {
			const rate = $( '#rating' ),
				comment = $( '#comment' );
			const author = $( '#author' );
			const email = $( '#email' );
			const val = rate.val();

			if ( email.length === 1 && author.val() === '' ) {
				window.alert( hotel_booking_i18n.review_author_required );
				return false;
			}

			if ( email.length === 1 && ( email.val() === '' || isEmail( email.val() ) === false ) ) {
				window.alert( hotel_booking_i18n.review_email_required );
				return false;
			}
			if ( rate.length === 1 && typeof val !== 'undefined' && val === '' ) {
				window.alert( hotel_booking_i18n.review_rating_required );
				return false;
			}
			if ( comment.val() === '' ) {
				window.alert( hotel_booking_i18n.review_content_required );
				return false;
			}

			$( this ).submit();
		} );
	} );

	// rating single room
	$.fn.rating = function() {
		const ratings = this,
			legnth = this.length;

		for ( let i = 0; i < legnth; i++ ) {
			var rating = $( ratings[ i ] ),
				html = [];

			html.push( '<span class="rating-input" data-rating="1"></span>' );
			html.push( '<span class="rating-input" data-rating="2"></span>' );
			html.push( '<span class="rating-input" data-rating="3"></span>' );
			html.push( '<span class="rating-input" data-rating="4"></span>' );
			html.push( '<span class="rating-input" data-rating="5"></span>' );
			html.push( '<input name="rating" id="rating" type="hidden" value="" />' );
			rating.html( html.join( '' ) );

			rating.mousemove( function( e ) {
				e.preventDefault();
				const parentOffset = ratings.offset(),
					relX = e.pageX - parentOffset.left,
					star = $( this ).find( '.rating-input' ),
					star_width = star.width(),
					rate = Math.ceil( relX / star_width );

				for ( let y = 0; y < star.length; y++ ) {
					const st = $( star[ y ] ),
						_data_star = parseInt( st.attr( 'data-rating' ) );
					if ( _data_star <= rate ) {
						st.addClass( 'high-light' );
					}
				}
			} ).mouseout( function( e ) {
				const parentOffset = ratings.offset(),
					relX = e.pageX - parentOffset.left,
					star = $( this ).find( '.rating-input' ),
					star_width = star.width(),
					rate = $( this ).find( '.rating-input.selected' );

				if ( rate.length === 0 ) {
					star.removeClass( 'high-light' );
				} else {
					for ( let y = 0; y < star.length; y++ ) {
						const st = $( star[ y ] ),
							_data_star = parseInt( st.attr( 'data-rating' ) );

						if ( _data_star <= parseInt( rate.attr( 'data-rating' ) ) ) {
							st.addClass( 'high-light' );
						} else {
							st.removeClass( 'high-light' );
						}
					}
				}
			} ).mousedown( function( e ) {
				const parentOffset = ratings.offset(),
					relX = e.pageX - parentOffset.left,
					star = $( this ).find( '.rating-input' ),
					star_width = star.width(),
					rate = Math.ceil( relX / star_width );
				star.removeClass( 'selected' ).removeClass( 'high-light' );
				for ( let y = 0; y < star.length; y++ ) {
					const st = $( star[ y ] ),
						_data_star = parseInt( st.attr( 'data-rating' ) );
					if ( _data_star === rate ) {
						st.addClass( 'selected' ).addClass( 'high-light' );
						break;
					} else {
						st.addClass( 'high-light' );
					}
				}
				rating.find( 'input[name="rating"]' ).val( rate );
			} );
		}
	};

	// overlay before ajax
	$.fn.hb_overlay_ajax_start = function() {
		const _self = this;
		_self.css( {
			position: 'relative',
			overflow: 'hidden',
		} );
		let overlay = '<div class="hb_overlay_ajax">';
		overlay += '</div>';

		_self.append( overlay );
	};

	$.fn.hb_overlay_ajax_stop = function() {
		const _self = this;
		const overlay = _self.find( '.hb_overlay_ajax' );

		overlay.addClass( 'hide' );
		var timeOut = setTimeout( function() {
			overlay.remove();
			clearTimeout( timeOut );
		}, 400 );
	};
}( ( jQuery ) ) );

const INTERNAL_DATE_FORMAT = hotel_settings.internal_date_format || 'Y/m/d';
const FRONTEND_DATE_FORMAT =
	hotel_settings.flatpickr_date_format || INTERNAL_DATE_FORMAT;
// Global locale used by all frontend pickers: browser locale + admin-configured week start.
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
const BOOKING_CHECK_IN_SELECTOR =
	'input[name="check_in_date"], input[data-wphb-original-name="check_in_date"]';
const BOOKING_CHECK_OUT_SELECTOR =
	'input[name="check_out_date"], input[data-wphb-original-name="check_out_date"]';

// Validate and convert date values into INTERNAL_DATE_FORMAT before form submit.
const validateAndNormalizeBookingDates = ( form ) => {
	const validationResult = validateBookingDateRange( form, {
		parseBookingDateValue,
		formatBookingDateForSubmit,
		checkInSelector: BOOKING_CHECK_IN_SELECTOR,
		checkOutSelector: BOOKING_CHECK_OUT_SELECTOR,
		emptyCheckInMessage:
			window?.hotel_booking_i18n?.empty_check_in_date ||
			'Please select check in date.',
		emptyCheckOutMessage:
			window?.hotel_booking_i18n?.empty_check_out_date ||
			'Please select check out date.',
		invalidRangeMessage:
			window?.hotel_booking_i18n?.check_out_date_must_be_greater ||
			'Check out date must be greater than the check in.',
		toggleErrorClass: true,
	} );

	if ( validationResult.ok ) {
		return validationResult;
	}

	if ( validationResult.error ) {
		alert( validationResult.error );
	}

	return validationResult;
};

const setHiddenBookingSubmitField = ( form, fieldName, fieldValue ) => {
	let hiddenField = form.querySelector(
		`input[type="hidden"][data-wphb-submit-proxy="${ fieldName }"]`
	);

	if ( ! hiddenField ) {
		hiddenField = document.createElement( 'input' );
		hiddenField.type = 'hidden';
		hiddenField.dataset.wphbSubmitProxy = fieldName;
		form.appendChild( hiddenField );
	}

	hiddenField.name = fieldName;
	hiddenField.value = fieldValue;
};

const preserveBookingUiFieldsOnSubmit = ( form, validationResult ) => {
	if ( ! validationResult?.ok ) {
		return;
	}

	const bookingFieldConfigs = [
		{
			fieldName: 'check_in_date',
			selector: BOOKING_CHECK_IN_SELECTOR,
			value: validationResult.checkInDate,
		},
		{
			fieldName: 'check_out_date',
			selector: BOOKING_CHECK_OUT_SELECTOR,
			value: validationResult.checkOutDate,
		},
	];

	bookingFieldConfigs.forEach( ( { fieldName, selector, value } ) => {
		const visibleField = form.querySelector( selector );

		if ( visibleField && visibleField.type !== 'hidden' ) {
			visibleField.dataset.wphbOriginalName = fieldName;
			visibleField.removeAttribute( 'name' );
		}

		setHiddenBookingSubmitField( form, fieldName, value );
	} );
};

const wphbDatePicker = () => {
	const elFormTables = document.querySelectorAll( '.hb-form-table' );
	if ( ! elFormTables.length ) {
		return;
	}
	elFormTables.forEach( ( elFormTable ) => {
		const elDateCheckIn = elFormTable.querySelector( 'input[name="check_in_date"]' );
		const elDateCheckOut = elFormTable.querySelector( 'input[name="check_out_date"]' );
		const elDateRange = elFormTable.querySelector( 'input[name="check_in_out_range"]' );
		const elDateCheckInOut = elFormTable.querySelector( '.hb-form-check-in-check-out' );
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
		const minBookingDateNumber = hotel_settings.min_booking_date > 0 ? parseInt( hotel_settings.min_booking_date ) : 1;

		if ( elDateCheckIn && elDateCheckOut && ! elDateCheckIn.closest( '.hb-form-check-in-check-out' ) ) {
			let datePickerCheckOut;
			// Two-input mode: check-in and check-out are separate flatpickr instances.
			const optionCheckIn = createFlatpickrConfig( {
				dateFormat: FRONTEND_DATE_FORMAT,
				parseDate: parseFlatpickrDate,
				locale: FLATPICKR_LOCALE,
			}, {
				minDate: 'today',
				onChange( selectedDates, dateStr, instance ) {
					if ( datePickerCheckOut ) {
						// calculate next day available
						const dateSelected = selectedDates[ 0 ];
						datePickerCheckOut.clear();
						const dateNext = new Date( dateSelected.setDate( dateSelected.getDate() + minBookingDateNumber ) );
						elDateCheckOut.focus();
						datePickerCheckOut.set( 'minDate', dateNext );
						datePickerCheckOut.open();
					}
				},
			} );

			flatpickr( elDateCheckIn, optionCheckIn );

			// Check-out picker shares parser/locale and updates based on check-in selection.
			const optionCheckout = createFlatpickrConfig( {
				dateFormat: FRONTEND_DATE_FORMAT,
				parseDate: parseFlatpickrDate,
				locale: FLATPICKR_LOCALE,
			}, {
				minDate: 'today',
			} );

			datePickerCheckOut = flatpickr( elDateCheckOut, optionCheckout );
		}

		if ( elDateRange && elDateRange.closest( '.hb-form-check-in-check-out' ) ) {
			// Range mode: one flatpickr instance controls both dates.
			const optionRange = createFlatpickrConfig( {
				dateFormat: FRONTEND_DATE_FORMAT,
				parseDate: parseFlatpickrDate,
				locale: FLATPICKR_LOCALE,
			}, {
				minDate: 'today',
				mode: 'range',
				showMonths: 2,
				defaultDate: [ elDateCheckIn.value, elDateCheckOut.value ],
				onClose( selectedDates, dateStr, instance ) {
					const dateCheckInSelected = selectedDates[ 0 ];
					const dateCheckOutSelected = selectedDates[ 1 ];
					if ( ! dateCheckInSelected || ! dateCheckOutSelected ) {
						return;
					}

					const dateCheckInStr = formatBookingDateForUi( dateCheckInSelected );
					const dateCheckOutStr = formatBookingDateForUi( dateCheckOutSelected );
					elDateCheckIn.value = dateCheckInStr;
					elDateCheckOut.value = dateCheckOutStr;
				},
			} );
			const datePickerRange = flatpickr( elDateRange, optionRange );

			if ( elDateCheckInOut ) {
				elDateCheckInOut.addEventListener( 'click', ( e ) => {
					e.preventDefault();
					datePickerRange.open();
				} );
			}
		}
	} );
};

document.addEventListener( 'DOMContentLoaded', function( e ) {
	wphbDatePicker();
	// Final submit guard for all booking/search forms with check-in/check-out fields.
	document.addEventListener( 'submit', ( event ) => {
		if ( event.defaultPrevented ) {
			return;
		}

		const target = event.target;
		if ( ! target || target.tagName !== 'FORM' ) {
			return;
		}

		const hasCheckInDate = target.querySelector( BOOKING_CHECK_IN_SELECTOR );
		const hasCheckOutDate = target.querySelector( BOOKING_CHECK_OUT_SELECTOR );
		if ( ! hasCheckInDate || ! hasCheckOutDate ) {
			return;
		}

		const validationResult = validateAndNormalizeBookingDates( target );
		if ( ! validationResult?.ok ) {
			event.preventDefault();
			return;
		}

		preserveBookingUiFieldsOnSubmit( target, validationResult );
	} );
} );
