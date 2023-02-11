
'use strict';
(function ($) {

    function isInteger(a) {
        return Number(a) || (a % 1 === 0);
    }
    window.mobileCheck = function() {
        let check = false;
        (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
        return check;
    };

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
                    let time = (Date.parse(currentDate.toLocaleDateString())) / 1000;
                    if(window.mobileCheck()){
                        time = (Date.parse(currentDate)) / 1000;
                    }
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
