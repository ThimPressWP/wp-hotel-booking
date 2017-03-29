;
(function ($) {
	var $doc = $(document);

	if (Date.prototype.compareWith == undefined) {
		Date.prototype.compareWith = function (d) {
			if (typeof d == 'string') {
				d = new Date(d);
			}
			var thisTime = parseInt(this.getTime() / 1000),
				compareTime = parseInt(d.getTime() / 1000);
			if (thisTime > compareTime) {
				return 1;
			} else if (thisTime < compareTime) {
				return -1;
			}
			return 0;
		}
	}
	function isEmail(email) {
		return new RegExp('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$').test(email);
	}

	function isDate(date) {
		date = new Date(date);
		return !isNaN(date.getTime());
	}

	function create_pricing_plan(data) {
		var $plan = $(wp.template('hb-pricing-table')(data));
		return $plan;
	}

	function init_pricing_plan(plan) {
		$(plan).find('.datepicker').datepicker({
			dateFormat     : hotel_booking_i18n.date_time_format,
			monthNames     : hotel_booking_i18n.monthNames,
			monthNamesShort: hotel_booking_i18n.monthNamesShort,
			dayNames       : hotel_booking_i18n.dayNames,
			dayNamesShort  : hotel_booking_i18n.dayNamesShort,
			dayNamesMin    : hotel_booking_i18n.dayNamesMin,
			onSelect       : function (date) {
				var _self = $(this),
					_date = _self.datepicker('getDate'),
					_timestamp = new Date(_date).getTime() / 1000 - ( new Date(_date).getTimezoneOffset() * 60 ),
					name = _self.attr('name');
				var hidden_name = false;
				if (name.indexOf('date-start') === 0) {
					hidden_name = name.replace('date-start', 'date-start-timestamp');
				} else if (name.indexOf('date-end') === 0) {
					hidden_name = name.replace('date-end', 'date-end-timestamp');
				}
				if (hidden_name) {
					$(plan).find('input[name="' + hidden_name + '"]').val(_timestamp);
				}
			}
		});
		// $(plan).find('.datepicker').datepicker('disable');
	}

	function _ready() {
		$doc.on('click', '.hb-pricing-controls a', function (e) {
			var $button = $(this),
				$table = $button.closest('.hb-pricing-table'),
				action = $button.data('action');
			e.preventDefault();

			switch (action) {
				case 'clone':
					var clone_allow = false;
					if ($('.hb-pricing-table').length < 1) {
						clone_allow = true;
					}
					var $cloned = $(wp.template('hb-pricing-table')({clone: clone_allow})),
						$inputs = $cloned.find('.hb-pricing-price');
					$cloned.hide().css("background-color", "#00A0D2").css("transition", "background-color 0.5s");
					init_pricing_plan($cloned);
					$table.find('.hb-pricing-price').each(function (i) {
						$inputs.eq(i).val(this.value);
					});
					if ($table.hasClass('regular-price')) {
						$cloned.removeClass('regular-price')
						$('.hb-pricing-table-title > span', $cloned).html('Date Range');
						$('#hb-pricing-plan-list').append($cloned);
					} else {
						$cloned.insertAfter($table);
					}
					$cloned.fadeTo(350, 0.8).delay(1000).fadeTo(250, 1, function () {
						$(this).css("background-color", "");
						$('.dashicons-edit', this).trigger('click');
					});
					$('#hb-no-plan-message').hide();
					break;
				case 'edit':
					if ($button.hasClass('dashicons-edit')) {
						$('input', $table).removeAttr('readonly');
						$('input', $table).datepicker("enable");
						$button.removeClass('dashicons-edit').addClass('dashicons-yes');
						$('.hb-pricing-table .dashicons-yes').not($button).trigger('click')
					} else {
						$('input', $table).attr('readonly', 'readonly');
						$('input', $table).datepicker("disable");
						$button.removeClass('dashicons-yes').addClass('dashicons-edit');
					}
					break;
				case 'remove':
					if (confirm(hotel_booking_i18n.confirm_remove_pricing_table)) {
						if ($table.siblings('.hb-pricing-table').length == 0) {
							$('#hb-no-plan-message').show();
						}
						$table.remove();

					}
					break;
			}
			return false;
		});

		$('#tp_hotel_booking_pricing #hb-room-select').change(function () {
			var location = window.location.href;
			location = location.replace(/[&]?hb-room=[0-9]+/, '');
			if (this.value != 0) location += '&hb-room=' + this.value;
			window.location.href = location;
		});

		$('form[name="pricing-table-form"]').submit(function () {
			var can_submit = true;
			$('.hb-pricing-table').each(function (i) {
				var $table = $(this),
					$start = $table.find('input[name^="date-start"]'),
					$end = $table.find('input[name^="date-end"]');
				if (!$table.hasClass('regular-price')) {
					if (!isDate($start.datepicker('getDate'))) {
						alert(hotel_booking_i18n.empty_pricing_plan_start_date);
						$start.focus();
						can_submit = false;
					} else if (!isDate($end.datepicker('getDate'))) {
						alert(hotel_booking_i18n.empty_pricing_plan_start_date);
						$end.focus();
						can_submit = false;
					}

					if (!can_submit) return false;
				}
				$table.find('input[type="text"], input[type="number"], input[type="hidden"]').each(function () {
					var $input = $(this),
						name = $input.attr('name');
					name = name.replace(/__INDEX__/, i - 1000);
					$input.attr('name', name);
				});
			});
			return can_submit;
		});

		$('.hb-pricing-table').each(function () {
			init_pricing_plan(this);
		});

		/* full calendar */
		function fullcalendar_init() {
			var hb_fullcalendar = $('.hotel-booking-fullcalendar');

			for (var i = 0; i < hb_fullcalendar.length; i++) {
				var _fullcalendar = $(hb_fullcalendar[i]),
					_data_events = _fullcalendar.attr('data-events');

				if (typeof _data_events === 'undefined') {
					_data_events = [];
				}

				_fullcalendar.fullCalendar({
					header            : {
						left : '',
						right: '',
					},
					ignoreTimezone    : false,
					handleWindowResize: true,
					editable          : false,
					defaultView       : 'singleRowMonth',
					events            : function (start, end, timezone, callback) {
						callback(JSON.parse(_data_events));
					}
				});
			}
		}

		var date = new Date();
		var fullcalendar_initdates = [];
		fullcalendar_initdates.push(date.getYear() + '-' + date.getMonth());
		fullcalendar_init(); // init fullcalendar
		$(document).on('click', '.hotel-booking-fullcalendar-toolbar .fc-button', function (event) {
			event.preventDefault();
			var _self = $(this),
				_calendar = $('.hotel-booking-fullcalendar'),
				_room_id = _self.attr('data-room'),
				_date = _self.attr('data-month');

			$.ajax({
				url       : ajaxurl,
				type      : 'POST',
				data      : {
					action : 'hotel_booking_load_other_full_calendar',
					nonce  : hotel_settings.nonce,
					room_id: _room_id,
					date   : _date
				},
				beforeSend: function () {
					_self.append('<i class="fa fa-spinner fa-spin"></i>');
				}
			}).done(function (res) {
				_self.find('.fa').remove();
				if (res.status === true) {
					// $( '.hotel-booking-fullcalendar' ).fullCalendar( 'removeEvents' );
					var events = JSON.parse(res.events);

					try {
						var date = new Date(events[0].start),
							month_string = date.getYear() + '-' + date.getMonth();
						if (fullcalendar_initdates.indexOf(month_string) == -1) {

							fullcalendar_initdates.push(month_string);
							for (var i = 0; i < events.length; i++) {
								var event = events[i];
								_calendar.fullCalendar('renderEvent', event, true);
							}
						}
					} catch (error) {
						console.debug(error);
					}
					$('.hotel-booking-fullcalendar').fullCalendar('refetchEvents');

					if (_self.hasClass('fc-next-button')) {
						_calendar.fullCalendar('next');
					} else {
						_calendar.fullCalendar('prev');
					}

					$('.hotel-booking-fullcalendar-month').text(res.month_name);
					$('.hotel-booking-fullcalendar-toolbar .fc-next-button').attr('data-month', res.next);
					$('.hotel-booking-fullcalendar-toolbar .fc-prev-button').attr('data-month', res.prev);
				}

			}).fail(function () {
				_self.find('.fa').remove();
			});

			return false;
		});

		/* end fullcalendar */

		// var $tabClicked = $('.hb-admin-sub-tab li a').click(function(e){
		//     e.preventDefault();
		//     var id = $(this).attr('href'),
		//         $div = $(id),
		//         $parent = $(this).parent();
		//     $parent.addClass('current').siblings().removeClass('current');

		//     $div.show().css('opacity', 1).siblings('.hb-sub-tab-content').hide();

		//     history.pushState({}, '', window.location.href.replace(/#?.*/, '') + id);

		//     return false;
		// }).filter('[href*="'+window.location.hash+'"]').trigger('click');

		// $.datepicker.setDefaults({ dateFormat: hotel_booking_i18n.date_time_format });
		// $.datepicker.setDefaults({ dateFormat: 'mm/dd/yy' });

		$(".datetime-picker-metabox").datepicker({
			dateFormat     : hotel_booking_i18n.date_time_format,
			monthNames     : hotel_booking_i18n.monthNames,
			monthNamesShort: hotel_booking_i18n.monthNamesShort,
			dayNames       : hotel_booking_i18n.dayNames,
			dayNamesShort  : hotel_booking_i18n.dayNamesShort,
			dayNamesMin    : hotel_booking_i18n.dayNamesMin,
			minDate        : 0,
			maxDate        : '+365D',
			numberOfMonths : 2,
			onSelect       : function (selected) {
				var _self = $(this),
					name = _self.attr('name'),
					date = jQuery(this).datepicker('getDate'),
					timestamp = new Date(date).getTime() / 1000 - ( new Date().getTimezoneOffset() * 60 );
				if (date) {
					date.setDate(date.getDate() + 1);
				}
				$('input[name="' + name + '_timestamp"]').val(timestamp);
				// $("#check_out_date").datepicker("option","minDate", date)
			}
		});
		$("#datepickerImage").click(function () {
			$("#txtFromDate").datepicker("show");
		});

		$('#hb-booking-date-from').datepicker({
			dateFormat     : hotel_booking_i18n.date_time_format,
			monthNames     : hotel_booking_i18n.monthNames,
			monthNamesShort: hotel_booking_i18n.monthNamesShort,
			dayNames       : hotel_booking_i18n.dayNames,
			dayNamesShort  : hotel_booking_i18n.dayNamesShort,
			dayNamesMin    : hotel_booking_i18n.dayNamesMin,
			onSelect       : function () {
				var _self = $(this),
					date = _self.datepicker('getDate'),
					timestamp = new Date(date).getTime() / 1000 - ( new Date().getTimezoneOffset() * 60 );
				_self.parent().find('input[name="date-from-timestamp"]').val(timestamp);
				$('#hb-booking-date-to').datepicker('option', 'minDate', date)
			}
		});
		$('#hb-booking-date-to').datepicker({
			dateFormat     : hotel_booking_i18n.date_time_format,
			monthNames     : hotel_booking_i18n.monthNames,
			monthNamesShort: hotel_booking_i18n.monthNamesShort,
			dayNames       : hotel_booking_i18n.dayNames,
			dayNamesShort  : hotel_booking_i18n.dayNamesShort,
			dayNamesMin    : hotel_booking_i18n.dayNamesMin,
			onSelect       : function () {
				var _self = $(this),
					date = _self.datepicker('getDate'),
					timestamp = new Date(date).getTime() / 1000 - ( new Date().getTimezoneOffset() * 60 );
				_self.parent().find('input[name="date-to-timestamp"]').val(timestamp);
				$('#hb-booking-date-from').datepicker('option', 'maxDate', date)
			}
		});

		$('form#posts-filter').submit(function () {
			var counter = 0;
			$('#hb-booking-date-from, #hb-booking-date-to, select[name="filter-type"]').each(function () {
				if ($(this).val()) counter++;
			});
			if (counter > 0 && counter < 3) {
				alert(hotel_booking_i18n.filter_error);
				return false;
			}
		});

		$('#gallery_settings').on('click', '.attachment.add-new', function (event) {
			event.preventDefault();
			var fileFrame = wp.media.frames.file_frame = wp.media({
				multiple: true
			});
			var self = $(this);
			fileFrame.on('select', function () {
				var attachments = fileFrame.state().get('selection').toJSON();
				var html = '';

				for (var i = 0; i < attachments.length; i++) {
					var attachment = attachments[i];
					var url = attachment.url.replace(hotel_settings.upload_base_url, '');
					html += '<li class="attachment">';
					html += '<div class="attachment-preview">';
					html += '<div class="thumbnail">';
					html += '<div class="centered">'
					html += '<img src="' + attachment.url + '"/>';
					html += '<input type="hidden" name="_hb_gallery[]" value="' + attachment.id + '" />'
					html += '</div>';
					html += '</div>';
					html += '</div>';
					html += '<a class="dashicons dashicons-trash" title="Remove this image"></a>';
					html += '</li>';
				}
				self.before(html);
			});
			fileFrame.open();
		})
			.on('click', '.attachment .dashicons-trash', function (event) {
				event.preventDefault();
				$(this).parent().remove();
			});

		$('form[name="hb-admin-settings-form"] select').select2();

		$('#hb-booking-details select').select2();

		$('.hb-form-field .hb-form-field-input select').select2();

		$('input[name="tp_hotel_booking_email_new_booking_enable"]').on('change _change', function () {
			var $siblings = $(this).closest('tr').siblings('.' + $(this).attr('name'));
			if (this.checked) {
				$siblings.show();
			} else {
				$siblings.hide();
			}
		}).trigger('change');
		$('#gallery_settings ul').sortable();

		/**
		 * other settings tabs
		 */
		$('.tp_hotel_booking_tabs_settings li a:first').addClass('active');
		$('.tp_hotel_booking_setting_fields:first').addClass('active');
		$(document).on('click', '.tp_hotel_booking_tabs_settings li a', function (e) {
			e.preventDefault();
			var self = $(this),
				tab = self.attr('href'),
				tab_content = $(tab);

			if (typeof tab === 'undefined')
				return;

			$('.tp_hotel_booking_setting_fields').removeClass('active');
			$('.tp_hotel_booking_tabs_settings li a').removeClass('active');
			self.addClass('active');

			if (tab_content.length === 1)
				tab_content.addClass('active');

		});

		$(document).on('click', '.hb-dismiss-notice button', function (event) {
			var parent = $(this).closest('.hb-dismiss-notice');
			if (parent.length) {
				event.preventDefault();
				$.ajax({
					url : ajaxurl,
					type: 'POST',
					data: {
						action: 'hotel_booking_dismiss_notice'
					}
				})
			}
		});
	}

	$doc.ready(_ready);

	// process booking items, post type 'hb_booking'
	Hotel_Booking_Order = {
		init           : function () {
			var _doc = $(document),
				_self = this;

			_self.select2();

			_doc.on('click', '.section h4 .edit', _self.edit_customer)

			_doc.on('change', '#booking-item-checkall', _self.toggle_checkbox)
			// add room
			_doc.on('click', '#add_room_item', _self.add_room_item)
			// add coupon
				.on('click', '#add_coupon', _self.add_coupon)
				//remove coupon
				.on('click', '#remove_coupon', _self.remove_coupon)
				// sync
				.on('click', '#action_sync', _self.action_sync)
				// edit
				.on('click', '#booking_items .actions .edit', _self.edit_room)
				// remove room item
				.on('click', '#booking_items .actions .remove', _self.remove_room)

				// on open trigger
				.on('hb_modal_open', this.openCallback)

				// room available
				.on('hb_check_available_action', this.check_available)

				// on save action
				.on('hb_before_update_action', this.save_action);
		},
		edit_customer  : function (e, target, data) {
			e.preventDefault();
			var _self = $(this),
				_section = _self.parents('.section:first'),
				_details = _section.find('.details'),
				_edit_input = _section.find('.edit_details');

			if (!_edit_input.hasClass('active')) {
				_self.hide();
				_details.hide();
				_edit_input.addClass('active');
			}
		},
		toggle_checkbox: function (e) {
			e.preventDefault();
			var _self = $(this),
				_checkox = $('#booking_items input[name*="book_item"]');

			if (_self.is(':checked')) {
				_checkox.attr('checked', true);
			} else {
				_checkox.attr('checked', false);
			}
		},
		edit_customer  : function (e) {
			e.preventDefault();
			var _self = $(this),
				_section = _self.parents('.section:first'),
				_details = _section.find('.details'),
				_edit_input = _section.find('.edit_details');

			if (!_edit_input.hasClass('active')) {
				_self.hide();
				_details.hide();
				_edit_input.addClass('active');
			}
		},
		toggle_checkbox: function (e) {
			e.preventDefault();
			var _self = $(this),
				_checkox = $('#booking_items input[name*="book_item"]');

			if (_self.is(':checked')) {
				_checkox.attr('checked', true);
			} else {
				_checkox.attr('checked', false);
			}
		},
		select2        : function () {
			$('#booking_details_section #_hb_user_id').select2({
				placeholder       : hotel_booking_i18n.select_user,
				minimumInputLength: 3,
				ajax              : {
					url           : ajaxurl,
					dataType      : 'json',
					type          : 'POST',
					quietMillis   : 50,
					data          : function (user_name) {
						return {
							user_name: user_name.term,
							action   : 'hotel_booking_load_order_user',
							nonce    : hotel_settings.nonce
						};
					},
					processResults: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.user_login + '(#' + item.ID + ' ' + item.user_email + ')',
									id  : item.ID
								}
							})
						};
					},
					cache         : true
				}
			});
		},
		add_room_item  : function (e) {
			e.preventDefault();
			var _self = $(this),
				_order_id = _self.attr('data-order-id');
			$(this).hb_modal_box({
				tmpl    : 'hb-add-room',
				settings: {
					'order_id': _order_id
				}
			});
			return false;
		},
		add_coupon     : function (e, target, data) {
			e.preventDefault();
			var _self = $(this),
				_order_id = _self.attr('data-order-id');
			$(this).hb_modal_box({
				tmpl    : 'hb-coupons',
				settings: {
					order_id: _order_id
				}
			});

			return false;
		},
		remove_coupon  : function (e, target, data) {
			e.preventDefault();
			var _self = $(this),
				_order_id = _self.attr('data-order-id'),
				_coupon_id = _self.attr('data-coupon-id')
			$(this).hb_modal_box({
				tmpl    : 'hb-confirm',
				settings: {
					order_id : _order_id,
					coupon_id: _coupon_id,
					action   : 'hotel_booking_remove_coupon_on_order'
				}
			});

			return false;
		},
		action_sync    : function (e) {
			e.preventDefault();

			var _self = $(this),
				_form_element = $('#hb-booking-items'),
				_form_overlay = _form_element.find('.hb_overlay'),
				_checked = $('input[name*="book_item"]:checked'),
				_data = {};
			_do_process = false;

			if (_checked.length > 0) {
				_do_process = true;

				for (var i = 0; i < _checked.length; i++) {
					_data[i] = $(_checked[i]).val();
				}
			}

			if (_do_process) {
				_self.hb_modal_box({
					tmpl    : 'hb-confirm',
					settings: {
						order_id     : _self.attr('data-order-id'),
						order_item_id: _data,
						action       : 'hotel_booking_admin_remove_order_items'
					}
				});
			}

			return false;
		},
		edit_room      : function (e) {
			e.preventDefault();
			e.stopPropagation();

			var _self = $(this),
				_order_id = _self.attr('data-order-id'),
				_order_item_id = _self.attr('data-order-item-id'),
				_order_item_type = _self.attr('data-order-item-type'),
				_order_item_parent = _self.attr('data-order-item-parent'),
				_icon = _self.find('.fa');

			$.ajax({
				url       : ajaxurl,
				type      : 'POST',
				data      : {
					order_id       : _order_id,
					order_item_id  : _order_item_id,
					order_item_type: _order_item_type,
					action         : 'hotel_booking_load_order_item',
					nonce          : hotel_settings.nonce
				},
				beforeSend: function () {
					_icon.addClass('fa-spin');
				}
			}).done(function (res) {
				_icon.removeClass('fa-spin');
				_self.hb_modal_box({
					tmpl    : 'hb-add-room',
					settings: res
				});
			});

			return false;
		},
		remove_room    : function (e) {
			e.preventDefault();

			var _self = $(this);
			_self.hb_modal_box({
				tmpl    : 'hb-confirm',
				settings: {
					order_id     : _self.attr('data-order-id'),
					order_item_id: _self.attr('data-order-item-id'),
					action       : 'hotel_booking_admin_remove_order_item'
				}
			});

			return false;
		},
		openCallback   : function (e, target, form) {
			e.preventDefault();
			if (target === 'hb-add-room') {
				var _check_in = form.find('.check_in_date'),
					_check_out = form.find('.check_out_date'),
					_select = form.find('.booking_search_room_items');

				// select2
				_select.select2({
					placeholder       : hotel_booking_i18n.select_room,
					minimumInputLength: 3,
					// z-index: 10000,
					ajax              : {
						url           : ajaxurl,
						dataType      : 'json',
						type          : 'POST',
						quietMillis   : 50,
						data          : function (room) {
							return {
								room  : room.term,
								action: 'hotel_booking_load_room_ajax',
								nonce : hotel_settings.nonce
							};
						},
						processResults: function (data) {
							return {
								results: $.map(data, function (item) {
									return {
										text: item.post_title,
										id  : item.ID
									}
								})
							};
						},
						cache         : true
					}
				});

				// date picker
				_check_in.datepicker({
					dateFormat     : hotel_booking_i18n.date_time_format,
					monthNames     : hotel_booking_i18n.monthNames,
					monthNamesShort: hotel_booking_i18n.monthNamesShort,
					dayNames       : hotel_booking_i18n.dayNames,
					dayNamesShort  : hotel_booking_i18n.dayNamesShort,
					dayNamesMin    : hotel_booking_i18n.dayNamesMin,
					onSelect       : function () {
						var _self = $(this),
							date = _self.datepicker('getDate'),
							timestamp = new Date(date).getTime() / 1000 - ( new Date().getTimezoneOffset() * 60 );
						_self.parent().find('input[name="check_in_date_timestamp"]').val(timestamp);

						_check_out.datepicker('option', 'minDate', date);
					}
				});
				_check_out.datepicker({
					dateFormat     : hotel_booking_i18n.date_time_format,
					monthNames     : hotel_booking_i18n.monthNames,
					monthNamesShort: hotel_booking_i18n.monthNamesShort,
					dayNames       : hotel_booking_i18n.dayNames,
					dayNamesShort  : hotel_booking_i18n.dayNamesShort,
					dayNamesMin    : hotel_booking_i18n.dayNamesMin,
					onSelect       : function () {
						var _self = $(this),
							date = _self.datepicker('getDate'),
							timestamp = new Date(date).getTime() / 1000 - ( new Date().getTimezoneOffset() * 60 );
						_self.parent().find('input[name="check_out_date_timestamp"]').val(timestamp);

						_check_in.datepicker('option', 'maxDate', date);
					}
				});

			} else if (target === 'hb-coupons') {
				var _select = form.find('.booking_coupon_code');
				// select2
				_select.select2({
					placeholder       : hotel_booking_i18n.select_coupon,
					minimumInputLength: 3,
					ajax              : {
						url           : ajaxurl,
						dataType      : 'json',
						type          : 'POST',
						quietMillis   : 50,
						data          : function (coupon) {
							return {
								coupon: coupon.term,
								action: 'hotel_booking_load_coupon_ajax',
								nonce : hotel_settings.nonce
							};
						},
						processResults: function (data) {
							return {
								results: $.map(data, function (item) {
									return {
										text: item.post_title,
										id  : item.ID
									}
								})
							};
						},
						cache         : true
					}
				});

			}
		},
		check_available: function (e, target, form) {
			e.preventDefault();
			e.stopPropagation();

			var _self = $(this),
				_button = $('.form_footer .check_available');
			form.push({
				name : 'action',
				value: 'hotel_booking_check_room_available'
			});

			$.ajax({
				url       : ajaxurl,
				type      : 'POST',
				data      : form,
				beforeSend: function () {
					_button.append('<i class="fa fa-spinner fa-spin"></i>');
					$('select[name="qty"]').remove();
				}
			}).done(function (res) {
				_button.find('.fa').remove();
				if (typeof res.status === 'undefined') {
					return;
				}
				if (res.status === false && typeof res.message !== 'undefined') {
					alert(res.message);
					return;
				}
				$('#hb_modal_dialog .section:last-child').append(wp.template('hb-qty')(res));
			});
		},
		save_action    : function (e, target, form) {
			var _form_element = $('#hb-booking-items'),
				_form_overlay = _form_element.find('.hb_overlay');
			$.ajax({
				url       : ajaxurl,
				type      : 'POST',
				data      : form,
				beforeSend: function () {
					_form_overlay.addClass('active');
				}
			}).done(function (res) {
				_form_overlay.removeClass('active');
				if (typeof res.status !== 'undefined') {
					if (res.status === true) {
						$('#hb-booking-items').html(res.html);
					} else if (typeof res.message !== 'undefined') {
						alert(res.message);
					}
				}
			});
		},
	};

	$(document).ready(function () {
		// admin order initialize
		Hotel_Booking_Order.init();
	});
	// end process booking items, post type 'hb_booking'

})(jQuery);

// modal box
(function ($, Backbone, _) {

	$.fn.hb_modal_box = function (options) {

		var options = $.extend({}, {
			tmpl    : '',
			settings: {}
		}, options);

		if (options.tmpl) {
			new HotelModal.view(options.tmpl, options.settings);
		}
	};

	var HotelModal = {
		view: function (target, options) {
			var view = Backbone.View.extend({
				id             : 'hb_modal_dialog',
				options        : options,
				target         : target,
				// events handles
				events         : {
					'click .hb_modal_close'  : 'close',
					'click .hb_modal_overlay': 'close',
					'click .hb_form_submit'  : 'submit',
					'click .check_available' : 'check_available'
				},
				// initialize
				initialize     : function (data) {
					this.render();
				},
				// render
				render         : function () {
					var template = wp.template(this.target);

					template = template(this.options);

					$('body').append(this.$el.html(template));

					var _content = $('.hb_modal'),
						_content_width = _content.outerWidth(),
						_content_height = _content.outerHeight(),
						_window_width = $(window).width(),
						_window_height = $(window).height(),
						_adminbar_height = $('#wpadminbar').innerWidth();

					_content.css({
						'margin-top' : '-' + _content_height / 2 + 'px',
						'margin-left': '-' + _content_width / 2 + 'px'
					});

					$(document).trigger('hb_modal_open', [this.target, _content.find('form')]);
				},
				// submit
				submit         : function () {
					$(document).trigger('hb_before_update_action', [this.target, this.form_data()]);

					// close
					this.close();

					$(document).trigger('hb_before_updated_action', [this.target, this.form_data()]);

					return false;
				},
				// close
				close          : function () {

					$(document).trigger('hb_before_close_action', [this.target, this.form_data()]);

					this.$el.remove();

					return false;
				},
				// check available
				check_available: function (e) {
					$(document).trigger('hb_check_available_action', [this.target, this.form_data()]);
					return false;
				},
				// form data
				form_data      : function () {
					return _form = $(this.$el).find('form:first-child').serializeArray();
				}

			});

			return new view(options);
		},
	};

})(jQuery, Backbone, _);
// end modal box
