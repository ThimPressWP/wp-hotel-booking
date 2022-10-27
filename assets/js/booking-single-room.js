
'use strict';
(function ($) {

    function isInteger(a) {
        return Number(a) || (a % 1 === 0);
    }

    function getId(url) {
        var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        var match = url.match(regExp);
    
        if (match && match[2].length == 11) {
            return match[2];
        } else {
            return 'error';
        }
    }

    var Hotel_Booking_Room_Addon = {
        init: function () {
            // load add to cart form
            var _self = this,
                _doc = $(document);

            // check option external link
            if ( Hotel_Booking_Blocked_Days.external_link == '' ) {
                _doc.on('click', '#hb_room_load_booking_form', _self.load_room_add_to_cart_form);
            }
            // trigger lightbox open
            _doc.on('hotel_room_load_add_to_cart_form_open', _self.lightbox_init);

            // form submit
            _doc.on('submit', '.hotel-booking-single-room-action', _self.form_submit)

            // previous step
            _doc.on('click', '.hb_previous_step', _self.preStep);

            /* Room Preview */
            _doc.on('click', '#hb_room_images .room-preview', _self.room_preview);
        },
        
        room_preview: function (e) {
            e.preventDefault();
            var _self = $(this);
            let src = _self.attr('data-preview');
            if ( src.includes('iframe') ){
                const reg = new RegExp('(?<=src=").*?(?=[\?"])');
                src = reg.exec(src)[0];
                if ( getId(src) != 'error' ) {
                    src = 'https://www.youtube.com/watch?v=' + getId(src);
                }
            }
            $.magnificPopup.open({
                items: {
                    src: src,
                    type: 'iframe'
                },
            });
        },
        is_int: function (a) {
            return Number(a) && a % 1 === 0;
        },
        lightbox_init: function (e, button, lightbox, taget) {
            e.preventDefault();
            // search form
            if (taget === 'hb-room-load-form') {
                Hotel_Booking_Room_Addon.datepicker_init()
            }
        },
        form_submit: function (e) {
            e.preventDefault();
            var _self = $(this),
                _form_name = _self.attr('name'),
                _data = Hotel_Booking_Room_Addon.form_data();

            if (_form_name === 'hb-search-single-room') {
                Hotel_Booking_Room_Addon.check_avibility(_self, _data, _self.find('button[type="submit"]'));
            }
        },
        datepicker_init: function () {
            var checkin = $('.hb-search-results-form-container input[name="check_in_date"]'),
                checkout = $('.hb-search-results-form-container input[name="check_out_date"]'),
                today = new Date(),
                tomorrow = new Date();

            var date_range = $(document).triggerHandler('hotel_booking_min_check_in_date');

            var checkin_range_checkout = hotel_settings.min_booking_date;
            if (!isInteger(checkin_range_checkout)) {
                checkin_range_checkout = 1;
            }

            if (!Hotel_Booking_Room_Addon.is_int(date_range)) {
                date_range = 1;
            }

            tomorrow.setDate(today.getDate() + date_range);
            var unavailableDates = Hotel_Booking_Blocked_Days.blocked_days;
            function unavailable(date) {
                var offset = date.getTimezoneOffset();
                var timestamp = Date.parse(date) - offset * 60 * 1000;
                var newdate_nonutc = new Date(timestamp);
                var dmy = newdate_nonutc.toISOString().split('T')[0];
                if ($.inArray(dmy, unavailableDates) < 0) {
                    return [true, "", "Book Now"];
                } else {
                    return [false, "", "Booked Out"];
                }
            }
            checkin.datepicker({
                dateFormat: hotel_booking_i18n.date_time_format,
                monthNames: hotel_booking_i18n.monthNames,
                monthNamesShort: hotel_booking_i18n.monthNamesShort,
                dayNames: hotel_booking_i18n.dayNames,
                dayNamesShort: hotel_booking_i18n.dayNamesShort,
                dayNamesMin: hotel_booking_i18n.dayNamesMin,
                minDate: today,
                maxDate: '+365D',
                beforeShowDay: unavailable,
                onSelect: function (selected) {
                    var checkout_date = checkin.datepicker('getDate'),
                        time = new Date(checkout_date);

                    checkout_date.setDate(checkout_date.getDate() + checkin_range_checkout);
                    checkout.datepicker('option', 'minDate', checkout_date);
                },
                onClose: function () {
                    checkout.datepicker('show');
                }
            });

            checkout.datepicker({
                dateFormat: hotel_booking_i18n.date_time_format,
                monthNames: hotel_booking_i18n.monthNames,
                monthNamesShort: hotel_booking_i18n.monthNamesShort,
                dayNames: hotel_booking_i18n.dayNames,
                dayNamesShort: hotel_booking_i18n.dayNamesShort,
                dayNamesMin: hotel_booking_i18n.dayNamesMin,
                minDate: tomorrow,
                maxDate: '+365D',
                beforeShowDay: unavailable,
                onSelect: function (selected) {
                    var checkin_date = checkout.datepicker('getDate'),
                        time = new Date(checkin_date);
                    checkin_date.setDate(checkin_date.getDate() - checkin_range_checkout);
                    checkin.datepicker('option', 'maxDate', checkin_date);
                }
            });

            $(document).triggerHandler('hotel_booking_room_form_datepicker_init', checkin, checkout);
        },
        beforeAjax: function (_taget) {
            _taget.attr('disabled', 'disabled');
            _taget.html('<span class="lds-ring"><span></span><span></span><span></span><span></span></span>' + _taget.html());
            $(document).triggerHandler('hotel_booking_room_form_before_ajax');
        },
        afterAjax: function (_taget) {
            _taget.find('span.lds-ring').remove();
            _taget.removeAttr('disabled');
            $(document).triggerHandler('hotel_booking_room_form_after_ajax');
        },
        load_room_add_to_cart_form: function (e) {
            e.preventDefault();
            var _self = $(this),
                _room_id = _self.attr('data-id'),
                _room_name = _self.attr('data-name'),
                _doc = $(document),
                _taget = 'hb-room-load-form',
                _lightbox = '#hotel_booking_room_hidden';

            $(_lightbox).html(wp.template(_taget)({_room_id: _room_id, _room_name: _room_name}));
            $.magnificPopup.open({
                type: 'inline',
                items: {
                    src: '#hotel_booking_room_hidden'
                },
                callbacks: {
                    open: function () {
                        _doc.triggerHandler('hotel_room_load_add_to_cart_form_open', [_self, _lightbox, _taget]);
                    }
                }
            });
            return false;
        },
        check_avibility: function (form, _data, _taget) {
            var sanitize = Hotel_Booking_Room_Addon.sanitize();

            if (sanitize === false) {
                return;
            }

            $.ajax({
                url: hotel_settings.ajax,
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                type: 'POST',
                data: _data,
                dataType: 'json',
                beforeSend: function () {
                    const currentDate = new Date();
                    const time = (Date.parse(currentDate.toLocaleDateString())) / 1000;
                    
                    if(_data.check_in_date_timestamp >= _data.check_out_date_timestamp){
                        alert('Check out date must be greater than or equal to today');
                        return false;
                    }
                    if(_data.check_in_date_timestamp < time || _data.check_out_date_timestamp < time ){
                        alert('You can\'t choose a date in the past');
                        return false;
                    }
                    Hotel_Booking_Room_Addon.beforeAjax(_taget);
                }
            }).done(function (res) {
                Hotel_Booking_Room_Addon.afterAjax(_taget);
                if (typeof res.status === 'undefined') {
                    return;
                }

                if (res.status === false && typeof res.messages !== 'undefined') {
                    Hotel_Booking_Room_Addon.append_messages(form, res.messages);
                } else if (typeof res.qty !== 'undefined') {
                    if (typeof res.qty.errors !== 'undefined') {
                        alert(res.qty.errors.zero);
                    } else {
                        form.replaceWith(wp.template('hb-room-load-form-cart')(res));
                    }
                }
            }).fail(function () {
                Hotel_Booking_Room_Addon.afterAjax(_taget);
            });
        },
        preStep: function (e) {
            var _self = $(this),
                _tmpl = _self.attr('data-template'),
                _form = _self.parents('form:first-child'),
                _data = Hotel_Booking_Room_Addon.form_data();

            _self.addClass('hb_loading');
            _form.replaceWith(wp.template(_tmpl)(_data));
            _self.removeClass('hb_loading');

            Hotel_Booking_Room_Addon.datepicker_init();

            return false;
        },
        sanitize: function () {
            var _form = $('form[name="hb-search-single-room"]'),
                checkin = _form.find('input[name="check_in_date"]'),
                check_out = _form.find('input[name="check_out_date"]'),
                errors = [];

            if (checkin.datepicker('getDate') === null) {
                checkin.addClass('error');
                errors.push('<p>' + hotel_booking_i18n.empty_check_in_date + '</p>');
            }

            if (check_out.datepicker('getDate') === null) {
                check_out.addClass('error');
                errors.push('<p>' + hotel_booking_i18n.empty_check_out_date + '</p>');
            }

            if (errors.length > 0) {
                Hotel_Booking_Room_Addon.append_messages(_form, errors);
                return false;
            } else {
                Hotel_Booking_Room_Addon.append_messages(_form);
            }

            return true;
        },
        form_data: function () {
            var data = {},
                _form = $('.hotel-booking-single-room-action'),
                _data = _form.serializeArray();

            var data_length = Object.keys(_data).length;
            for (var i = 0; i < data_length; i++) {
                var input = _data[i];
                if (input.name === 'check_in_date' || input.name === 'check_out_date') {
                    var timestamp = _form.find('input[name="' + input.name + '"]');
                    timestamp = $(timestamp).datepicker('getDate');
                    timestamp = new Date(timestamp);
                    timestamp = timestamp.getTime() / 1000 - (timestamp.getTimezoneOffset() * 60);

                    data[input.name + '_timestamp'] = timestamp;
                }
                data[input.name] = input.value;
            }

            return data;
        },
        append_messages: function (form, errors) {
            if (typeof form !== 'undefined') {
                form.find('.hotel_booking_room_errors').slideUp(300, function () {
                    $(this).remove();
                });
                form.find('.error').removeClass('error');
            }

            if (typeof form === 'undefined' || typeof errors === 'undefined' || Object.keys(errors).length === 0) {
                return;
            }

            var mesg = [];

            for (var i = 0; i < Object.keys(errors).length; i++) {
                mesg[i] = '<p>' + errors[i] + '</p>';
            }
            form.find('.hb-booking-room-form-head').append('<div class="hotel_booking_room_errors">' + errors.join('') + '</div>');
        }

    };

    $(document).ready(function () {
        Hotel_Booking_Room_Addon.init();
    });

})(jQuery);
