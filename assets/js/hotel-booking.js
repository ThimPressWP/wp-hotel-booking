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

    function isInteger(a) {
        return Number(a) || ( a % 1 === 0 );
    }

    function isEmail(email) {
        return new RegExp('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$').test(email);
    }

    function isDate(date) {
        date = new Date(date);
        return !isNaN(date.getTime());
    }

    function parseJSON(data) {
        if (!$.isPlainObject(data)) {
            var m = data.match(/<!-- HB_AJAX_START -->(.*)<!-- HB_AJAX_END -->/);
            try {
                if (m) {
                    data = $.parseJSON(m[1]);
                } else {
                    data = $.parseJSON(data);
                }
            } catch (e) {
                data = {};
            }
        }
        return data;
    }

    function fetchCustomerInfo() {
        var $button = $(this),
            $email = $('input[name="existing-customer-email"]');
        if (!isEmail($email.val())) {
            // alert( hotel_booking_i18n.invalid_email );
            $email.addClass('error');
            $email.focus();
            return;
        }
        $button.attr('disabled', true);
        $email.attr('disabled', true);
        var customer_table = $('.hb-col-padding.hb-col-border');
        $.ajax({
            url: hotel_settings.ajax,
            dataType: 'html',
            type: 'post',
            data: {
                action: 'hotel_booking_fetch_customer_info',
                email: $email.val()
            },
            beforeSend: function () {
                customer_table.hb_overlay_ajax_start();
            },
            success: function (response) {
                customer_table.hb_overlay_ajax_stop();
                response = parseJSON(response);
                if (response && response.ID) {
                    var $container = $('#hb-order-new-customer');
                    for (var key in response.data) {
                        var inputName = key.replace(/^_hb_customer_/, '');
                        var $field = $container.find('input[name="' + inputName + '"], select[name="' + inputName + '"], textarea[name="' + inputName + '"]');
                        $field.val(response.data[key]);
                    }
                    $container.find('input[name="existing-customer-id"]').val(response.ID);
                    $('.hb-order-existing-customer').fadeOut(function () {
                        //$(this).remove();
                    });
                } else {
                    hotel_checkout_fetch_error([hotel_booking_i18n.invalid_email]);
                }
                $button.removeAttr('disabled');
                $email.removeAttr('disabled');

            },
            error: function () {
                customer_table.hb_overlay_ajax_stop();
                hotel_checkout_fetch_error([hotel_booking_i18n.ajax_error]);
                $button.removeAttr('disabled');
                $email.removeAttr('disabled');
            }
        });
    }

    function hotel_checkout_fetch_error(msgs) {
        if (msgs.length === 0) {
            return;
        }
        $('.hotel_checkout_errors').slideUp().remove();
        var html = [];

        html.push('<div class="hotel_checkout_errors">');
        for (var i = 0; i < msgs.length; i++) {
            html.push('<p>' + msgs[i] + '</p>');
        }
        html.push('</div>');
        $('#hb-payment-form h3:first-child').after(html.join(''));
    }

    function validateOrder($form) {

        var $title = $form.find('select[name="title"]'),
            mesgs = [];
        if ($title.length === 1 && -1 === $title.val()) {
            // alert( hotel_booking_i18n.empty_customer_title );
            mesgs.push(hotel_booking_i18n.empty_customer_title);
            $title.parents('div:first').addClass('error');
        }

        var $firstName = $form.find('input[name="first_name"]');
        if ($firstName.length === 1 && !$firstName.val()) {
            // alert(hotel_booking_i18n.empty_customer_first_name);
            mesgs.push(hotel_booking_i18n.empty_customer_first_name);
            $firstName.parents('div:first').addClass('error');
        }

        var $lastName = $form.find('input[name="last_name"]');
        if ($lastName.length === 1 && !$lastName.val()) {
            // alert( hotel_booking_i18n.empty_customer_last_name );
            mesgs.push(hotel_booking_i18n.empty_customer_last_name);
            $lastName.parents('div:first').addClass('error');
        }

        var $address = $form.find('input[name="address"]');
        if ($address.length === 1 && !$address.val()) {
            // alert( hotel_booking_i18n.empty_customer_address );
            mesgs.push(hotel_booking_i18n.empty_customer_address);
            $address.parents('div:first').addClass('error');
        }

        var $city = $form.find('input[name="city"]');
        if ($city.length === 1 && !$city.val()) {
            // alert(hotel_booking_i18n.empty_customer_city);
            mesgs.push(hotel_booking_i18n.empty_customer_city);
            $city.parents('div:first').addClass('error');
        }

        var $state = $form.find('input[name="state"]');
        if ($state.length === 1 && !$state.val()) {
            // alert( hotel_booking_i18n.empty_customer_state );
            mesgs.push(hotel_booking_i18n.empty_customer_state);
            $state.parents('div:first').addClass('error');
        }

        var $postalCode = $form.find('input[name="postal_code"]');
        if ($postalCode.length === 1 && !$postalCode.val()) {
            // alert( hotel_booking_i18n.empty_customer_postal_code );
            mesgs.push(hotel_booking_i18n.empty_customer_postal_code);
            $postalCode.parents('div:first').addClass('error');
        }

        var $country = $form.find('select[name="country"]');
        if ($country.length === 1 && !$country.val()) {
            // alert( hotel_booking_i18n.empty_customer_country );
            mesgs.push(hotel_booking_i18n.empty_customer_country);
            $country.parents('div:first').addClass('error');
        }

        var $phone = $form.find('input[name="phone"]');
        if ($phone.length === 1 && !$phone.val()) {
            // alert( hotel_booking_i18n.empty_customer_phone );
            mesgs.push(hotel_booking_i18n.empty_customer_phone);
            $phone.parents('div:first').addClass('error');
        }

        var $email = $form.find('input[name="email"]');
        if ($email.length === 1 && !isEmail($email.val())) {
            // alert( hotel_booking_i18n.customer_email_invalid );
            mesgs.push(hotel_booking_i18n.customer_email_invalid);
            $email.parents('div:first').addClass('error');
        }

        var $payment_method = $form.find('input[name="hb-payment-method"]:checked');
        if ($payment_method.length === 1 && $payment_method.length === 0) {
            // alert( hotel_booking_i18n.no_payment_method_selected );
            mesgs.push(hotel_booking_i18n.no_payment_method_selected);
            $payment_method.parents('div:first').addClass('error');
        }

        var $tos = $form.find('input[name="tos"]');
        if ($tos.length && !$tos.is(':checked')) {
            // alert(hotel_booking_i18n.confirm_tos);
            mesgs.push(hotel_booking_i18n.confirm_tos);
            $tos.addClass('error');
        }
        if ($('input[name="existing-customer-id"]').val()) {
            if ($email.val() != $('input[name="existing-customer-email"]', $form).val()) {
                mesgs.push(hotel_booking_i18n.customer_email_not_match);
            }
            $email.parents('div:first').addClass('error');
            $form.find('input[name="existing-customer-id"]').parents('div:first').addClass('error');
        }

        if (mesgs.length > 0) {
            hotel_checkout_fetch_error(mesgs);
            return false;
        }
        return true;
    }

    function stripeSubmit(form) {
        var pl_key = 'pk_test_HHukcwWCsD7qDFWKKpKdJeOT';
        if (typeof TPBooking_Payment_Stripe !== 'undefined') {
            pl_key = TPBooking_Payment_Stripe.stripe_publish;
        }

        var handler = StripeCheckout.configure({
            key: pl_key,
            image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
            locale: 'auto',
            token: function (token) {
                // Use the token to create the charge with a server-side script.
                // You can access the token ID with `token.id`
                stripe_payment_process(form, token);
            }
        });

        var first_name = form.find('input[name="first_name"]').val().trim();
        var last_name = form.find('input[name="last_name"]').val().trim();
        var email = form.find('input[name="email"]').val().trim();
        var currency = form.find('input[name="currency"]').val().trim();
        var price = 0;
        if (form.find('input[name="pay_all"]').is(':checked')) {
            price = form.find('input[name="total_price"]').val();
        } else {
            price = form.find('input[name="total_advance"]').val();
        }

        // Open Checkout with further options
        handler.open({
            name: first_name + ' ' + last_name,
            description: email,
            currency: currency,
            amount: price * 100
        });
    }

    function stripe_payment_process(form, token) {
        var data = {};
        var payment_data = form.serializeArray();
        var button = form.find('button[type="submit"]');

        $.each(payment_data, function (index, obj) {
            data[obj.name] = obj.value;
        });

        $.extend(token, data);

        $.ajax({
            url: hotel_settings.ajax,
            data: token,
            type: 'POST',
            dataType: 'html',
            beforeSend: function () {
                button.addClass('hb_loading');
            }
        }).done(function (res) {
            button.removeClass('hb_loading');
            res = parseJSON(res);

            if (typeof res.result !== 'undefined' && res.result == 'success') {
                if (typeof res.redirect !== 'undefined')
                    window.location.href = res.redirect;
            } else if (typeof res.message !== 'undefined') {
                alert(res.message);
            }
        }).fail(function () {
            button.removeClass('hb_loading');
        });
    }

    function orderSubmit(form) {
        var action = window.location.href.replace(/\?.*/, '');
        form.attr('action', action);
        var button = form.find('button[type="submit"]');
        if (form.triggerHandler('hotel_booking_place_order') !== false) {
            $.ajax({
                type: 'POST',
                url: hotel_settings.ajax,
                data: form.serialize(),
                dataType: 'text',
                beforeSend: function () {
                    button.addClass('hb_loading');
                },
                success: function (code) {
                    button.removeClass('hb_loading');
                    try {
                        var response = parseJSON(code);
                        if (response.result === 'success') {
                            if (response.redirect !== undefined) {
                                window.location.href = response.redirect;
                            }
                        } else if (typeof response.message !== 'undefined') {
                            alert(response.message);
                        }
                    } catch (e) {
                        alert(e)
                    }
                },
                error: function () {
                    button.removeClass('hb_loading');
                    hotel_checkout_fetch_error([hotel_booking_i18n.waring.try_again]);
                }

            });
        }
        return false;
    }

    function applyCoupon() {
        var $coupon = $('input[name="hb-coupon-code"]');
        var table = $coupon.parents('table');
        if (!$coupon.val()) {
            alert(hotel_booking_i18n.enter_coupon_code)
            $coupon.focus();
            return false;
        }
        $.ajax({
            type: 'POST',
            url: hotel_settings.ajax,
            data: {
                action: 'hotel_booking_apply_coupon',
                code: $coupon.val()
            },
            dataType: 'text',
            beforeSend: function () {
                table.hb_overlay_ajax_start();
            },
            success: function (code) {
                table.hb_overlay_ajax_stop();
                try {
                    var response = parseJSON(code);
                    if (response.result == 'success') {
                        window.location.href = window.location.href;
                    } else {
                        alert(response.message);
                    }
                } catch (e) {
                    alert(e)
                }
            },
            error: function () {
                table.hb_overlay_ajax_stop();
                alert('error')
            }
        });
    }

    /**
     * HB_Booking_Cart object class
     * @type {Object}
     */
    HB_Booking_Cart = {
        init: function () {
            this.add_to_cart();
            this.remove_cart();
        },
        hb_add_to_cart_callback: function (data, callback) {
            var mini_cart = $('.hotel_booking_mini_cart');
            var length = mini_cart.length;
            var template = wp.template('hb-minicart-item');
            template = template(data);

            if (length > 0) {
                for (var i = 0; i < length; i++) {
                    var cart = $(mini_cart[i]),
                        cart_item = $(mini_cart[i]).find('.hb_mini_cart_item'),
                        insert = false,
                        empty = cart.find('.hb_mini_cart_empty'),
                        footer_ele = cart.find('.hb_mini_cart_footer'),
                        items_length = cart_item.length;

                    if (items_length === 0) {
                        var footer = wp.template('hb-minicart-footer');
                        var ele = footer_ele;
                        if (empty.length === 1) {
                            empty.after(footer({}));
                            empty.before(template);
                        } else {
                            footer_ele.before(template);
                        }
                        insert = true;
                        break;
                    } else {
                        for (var y = 0; y < items_length; y++) {
                            var item = $(cart_item[y]),
                                cart_id = item.attr('data-cart-id');

                            if (data.cart_id === cart_id) {
                                item.replaceWith(template);
                                insert = true;
                                break;
                            }
                        }

                        if (insert === false) {
                            footer_ele.before(template);
                        }
                    }
                }
            }

            $('.hb_mini_cart_empty').remove();
            var timeout = setTimeout(function () {
                $('.hb_mini_cart_item').removeClass('active');
                clearTimeout(timeout);
            }, 3500);

            if (typeof callback !== 'undefined') {
                callback();
            }
        },
        hb_remove_cart_item_callback: function (cart_id, res) {
            var minicart = $('.hotel_booking_mini_cart');
            for (var i = 0; i < minicart.length; i++) {
                var cart = $(minicart[i]);
                var items = cart.find('.hb_mini_cart_item');

                for (var y = 0; y < items.length; y++) {
                    var _item = $(items[y]),
                        cart_item_id = _item.attr('data-cart-id');
                    if (cart_id === cart_item_id) {
                        _item.remove();
                        break;
                    }
                }

                // append message empty cart
                items = cart.find('.hb_mini_cart_item');
                if (items.length === 0) {
                    var empty = wp.template('hb-minicart-empty');
                    cart.find('.hb_mini_cart_footer').remove();
                    cart.append(empty({}));
                    break;
                }
            }

            var cart_table = $('#hotel-booking-payment, #hotel-booking-cart');

            for (var i = 0; i < cart_table.length; i++) {
                var _table = $(cart_table[i]);
                var tr = _table.find('table').find('.hb_checkout_item, .hb_addition_services_title');
                for (var y = 0; y < tr.length; y++) {
                    var _tr = $(tr[y]);
                    cart_item_id = _tr.attr('data-cart-id'),
                        parent_item_id = _tr.attr('data-parent-id');
                    if (cart_id === cart_item_id || cart_id === parent_item_id) {
                        _tr.remove();
                        continue;
                    }
                }

                if (typeof res.sub_total !== 'undefined')
                    _table.find('span.hb_sub_total_value').html(res.sub_total);

                if (typeof res.grand_total !== 'undefined')
                    _table.find('span.hb_grand_total_value').html(res.grand_total);

                if (typeof res.advance_payment !== 'undefined')
                    _table.find('span.hb_advance_payment_value').html(res.advance_payment);

            }
        },
        add_to_cart: function () {
            var searchResult = $('form.hb-search-room-results');

            // searchResult.each(function () {
            $(document).on('submit', 'form.hb-search-room-results', function (event) {
                event.preventDefault();
                var _form = $(this),
                    button = _form.find('.hb_add_to_cart'),
                    select = _form.find('.number_room_select'),
                    number_room_select = _form.find('.number_room_select option:selected').val(),
                    room_title = _form.find('.hb-room-name');

                $('.number_room_select').removeClass('hotel_booking_invalid_quantity');
                if (typeof number_room_select === 'undefined' || number_room_select === '') {
                    select.addClass('hotel_booking_invalid_quantity');
                    //message
                    room_title.find('.hb_success_message').remove();
                    room_title.append('<label class="hb_success_message">' + hotel_booking_i18n.waring.room_select + '</label>');
                    var timeOut = setTimeout(function () {
                        room_title.find('.hb_success_message').remove();
                    }, 2000);
                    return false;
                }
                var data = $(this).serializeArray();
                $.ajax({
                    url: hotel_settings.ajax,
                    type: 'POST',
                    data: data,
                    dataType: 'html',
                    beforeSend: function () {
                        // _form.hb_overlay_ajax_start();
                        button.addClass('hb_loading');
                    },
                    success: function (code) {
                        _form.hb_overlay_ajax_stop();
                        code = parseJSON(code);
                        if (typeof code.message !== 'undefined') {

                            //message
                            room_title.find('.hb_success_message').remove();
                            room_title.append('<div class="hb_success_message">' + code.message + '</div>');
                            var timeOut = setTimeout(function () {
                                room_title.find('.hb_success_message').remove();
                            }, 3000);
                        }

                        if (typeof code.status !== 'undefined' && code.status === 'success') {

                            // update woo cart when add room to cart
                            $('body').trigger('hb_added_item_to_cart');
                            // add message successfully
                            if (typeof code.redirect !== 'undefined') {
                                window.location.href = code.redirect;
                            }
                        } else {
                            alert(code.message);
                        }

                        if (typeof code.id !== 'undefined') {
                            HB_Booking_Cart.hb_add_to_cart_callback(code);
                        }
                        button.removeClass('hb_loading');
                        if (_form.find('.hb_search_add_to_cart').length) {
                            if (!$('.hb_search_add_to_cart').find('.hb_view_cart').length) {
                                button.after('<a href="' + hotel_booking_i18n.cart_url + '" class="hb_button hb_view_cart">' + hotel_booking_i18n.view_cart + '</a>');
                            }
                        }
                    },
                    error: function () {
                        // searchResult.hb_overlay_ajax_stop();
                        button.removeClass('hb_loading');
                        alert(hotel_booking_i18n.waring.try_again);
                    }
                });
                return false;
            });
            // });
        },
        remove_cart: function () {
            // var updateOrderButton
            $(document).on('click', '.hb_remove_cart_item', function (e) {
                e.preventDefault();

                var tr = $(this).parents('tr'),
                    cart_item = $(this).attr('data-cart-id');
                $.ajax({
                    url: hotel_settings.ajax,
                    type: 'POST',
                    data: {
                        cart_id: cart_item,
                        nonce: hotel_settings.nonce,
                        action: 'hotel_booking_ajax_remove_item_cart'
                    },
                    dataType: 'html',
                    beforeSend: function () {
                        tr.hb_overlay_ajax_start();
                    }
                }).done(function (res) {
                    res = parseJSON(res);
                    if (typeof res.status === 'undefined' || res.status !== 'success')
                        alert(hotel_booking_i18n.waring.try_again);

                    // update woo cart when remove room from cart
                    $('body').trigger('hb_removed_item_to_cart');

                    if (typeof res.sub_total !== 'undefined')
                        $('span.hb_sub_total_value').html(res.sub_total);

                    if (typeof res.grand_total !== 'undefined')
                        $('span.hb_grand_total_value').html(res.grand_total);

                    if (typeof res.advance_payment !== 'undefined')
                        $('span.hb_advance_payment_value').html(res.advance_payment);
                    tr.hb_overlay_ajax_stop();
                    tr.remove();
                    HB_Booking_Cart.hb_remove_cart_item_callback(cart_item, res);
                });
            });

            //remove minicart item
            $('.hotel_booking_mini_cart').on('click', '.hb_mini_cart_remove', function (event) {
                event.preventDefault();
                var minicart = $('.hotel_booking_mini_cart');
                var item = $(this).parents('.hb_mini_cart_item');
                var cart_id = item.attr('data-cart-id');

                $.ajax({
                    url: hotel_settings.ajax,
                    type: 'POST',
                    data: {
                        cart_id: cart_id,
                        nonce: hotel_settings.nonce,
                        action: 'hotel_booking_ajax_remove_item_cart'
                    },
                    dataType: 'html',
                    beforeSend: function () {
                        item.addClass('before_remove');
                        item.hb_overlay_ajax_start();
                    }
                }).done(function (res) {
                    res = parseJSON(res);
                    if (typeof res.status === 'undefined' || res.status !== 'success') {
                        alert(hotel_booking_i18n.waring.try_again);
                        return;
                    }

                    HB_Booking_Cart.hb_remove_cart_item_callback(cart_id, res);
                    item.hb_overlay_ajax_stop();
                });
            });
        },
    };

    $(document).ready(function () {
        HB_Booking_Cart.init();
        $.datepicker.setDefaults({dateFormat: hotel_booking_i18n.date_time_format});
        // $.datepicker.setDefaults({dateFormat: 'mm/dd/yy'});
        var today = new Date();
        var tomorrow = new Date();

        var start_plus = $(document).triggerHandler('hotel_booking_min_check_in_date', [1, today, tomorrow]);
        start_plus = parseInt(start_plus);
        if (!isInteger(start_plus)) {
            start_plus = 1;
        }

        tomorrow.setDate(today.getDate() + start_plus);

        $('input[id^="check_in_date"]').datepicker({
            dateFormat: hotel_booking_i18n.date_time_format,
            firstDay: hotel_booking_i18n.date_start,
            monthNames: hotel_booking_i18n.monthNames,
            monthNamesShort: hotel_booking_i18n.monthNamesShort,
            dayNames: hotel_booking_i18n.dayNames,
            dayNamesShort: hotel_booking_i18n.dayNamesShort,
            dayNamesMin: hotel_booking_i18n.dayNamesMin,
            minDate: today,
            maxDate: '+365D',
            numberOfMonths: 1,
            onSelect: function () {
                var unique = $(this).attr('id');
                unique = unique.replace('check_in_date_', '');
                var date = $(this).datepicker('getDate');

                var check_in_range_check_out = hotel_settings.min_booking_date;
                if (!isInteger(check_in_range_check_out)) {
                    check_in_range_check_out = 1;
                }

                if (date) {
                    date.setDate(date.getDate() + check_in_range_check_out);
                }

                var checkout = $('#check_out_date_' + unique);
                checkout.datepicker('option', 'minDate', date);
            }
        }).on('click', function () {
            $(this).datepicker('show');
        });

        $('input[id^="check_out_date"]').datepicker({
            dateFormat: hotel_booking_i18n.date_time_format,
            monthNames: hotel_booking_i18n.monthNames,
            monthNamesShort: hotel_booking_i18n.monthNamesShort,
            dayNames: hotel_booking_i18n.dayNames,
            dayNamesShort: hotel_booking_i18n.dayNamesShort,
            dayNamesMin: hotel_booking_i18n.dayNamesMin,
            minDate: tomorrow,
            maxDate: '+365D',
            numberOfMonths: 1,
            onSelect: function () {
                var unique = $(this).attr('id');
                unique = unique.replace('check_out_date_', '');
                var check_in = $('#check_in_date_' + unique),
                    selected = $(this).datepicker('getDate');

                var check_in_range_check_out = hotel_settings.min_booking_date;
                if (!isInteger(check_in_range_check_out)) {
                    check_in_range_check_out = 1;
                }

                selected.setDate(selected.getDate() - check_in_range_check_out);

                check_in.datepicker('option', 'maxDate', selected);
            }
        }).on('click', function () {
            $(this).datepicker('show');
        });

        $('#datepickerImage').click(function () {
            $('#txtFromDate').datepicker('show');
        });

        $('#datepickerImage1').click(function () {
            $('#txtToDate').datepicker('show');
        });

        $('form[class^="hb-search-form"]').submit(function (e) {
            e.preventDefault();
            var _self = $(this),
                unique = _self.attr('class'),
                button = _self.find('button[type="submit"]');

            unique = unique.replace('hb-search-form-', '');

            _self.find('input, select').removeClass('error');
            var $check_in = $('#check_in_date_' + unique);
            if ($check_in.val() === '' || !isDate($check_in.datepicker('getDate'))) {
                $check_in.addClass('error');
                return false;
            }

            var $check_out = $('#check_out_date_' + unique);
            if ($check_out.val() === '' || !isDate($check_out.datepicker('getDate'))) {
                $check_out.addClass('error');
                return false;
            }

            if ($check_in.datepicker('getDate') === null) {
                $check_in.addClass('error');
                return false;
            }

            if ($check_out.datepicker('getDate') === null) {
                $check_out.addClass('error');
                return false;
            }

            var check_in = new Date($check_in.datepicker('getDate')),
                check_out = new Date($check_out.datepicker('getDate')),
                current = new Date();
            // if (check_in.compareWith(current) == -1) {
            // 	$check_in.addClass('error');
            // 	return false;
            // }

            if (check_in.compareWith(check_out) >= 0) {
                $check_in.addClass('error');
                error = true;
                return false;
            }

            var action = $(this).attr('action') || window.location.href;
            var data = $(this).serializeArray();
            for (var i = 0; i < data.length; i++) {
                var input = data[i];
                if (input.name === 'check_in_date' || input.name === 'check_out_date') {
                    var time = $(this).find('input[name="' + input.name + '"]').datepicker('getDate');
                    time = new Date(time);
                    data.push({
                        name: 'hb_' + input.name,
                        value: time.getTime() / 1000 - ( time.getTimezoneOffset() * 60 )
                    })
                }
            }

            $.ajax({
                url: hotel_settings.ajax,
                type: 'post',
                dataType: 'html',
                data: data,
                beforeSend: function () {
                    button.addClass('hb_loading');
                },
                success: function (response) {
                    response = parseJSON(response);
                    if (typeof response.success === 'undefined' || !response.success) {
                        return;
                    }

                    // redirect if url is ! undefined
                    if (typeof response.url !== 'undefined') {
                        window.location.href = response.url;
                    } else if (response.sig) {
                        if (action.indexOf('?') === -1) {
                            action += '?hotel-booking-params=' + response.sig;
                        } else {
                            action += '&hotel-booking-params=' + response.sig;
                        }
                        window.location.href = action;
                    }
                    // button.removeClass('hb_loading');
                }
            });
            return false;
        });

        $('form#hb-payment-form').submit(function (e) {
            e.preventDefault();
            var _self = $(this);
            var _method = _self.find('input[name="hb-payment-method"]:checked').val();

            var action = window.location.href.replace(/\?.*/, '');
            _self.find('.hotel_checkout_errors').slideUp().remove();
            _self.find('input, select').parents('div:first-child').removeClass('error');
            try {

                if (_self.triggerHandler('hb_order_submit') === false) {
                    return false;
                }

                _self.attr('action', action);

                if (!validateOrder(_self)) {
                    return false;
                }

                if (_method === 'stripe') {
                    stripeSubmit(_self);
                } else {
                    orderSubmit(_self);
                }

            } catch (e) {
                alert(e);
            }
        });

        $('#fetch-customer-info').click(fetchCustomerInfo);

        $doc.on('click', '.hb-view-booking-room-details, .hb_search_room_item_detail_price_close', function (e) {
            e.preventDefault();
            var _self = $(this);
            var _details = _self.parents('.hb-room-content').find('.hb-booking-room-details');

            _details.toggleClass('active');

            // $(this).closest('.hb-room-content').find('.hb-booking-room-details').fadeToggle();
        }).on('click', 'input[name="hb-payment-method"]', function () {
            if (this.checked) {
                $('.hb-payment-method-form:not(.' + this.value + ')').slideUp();
                $('.hb-payment-method-form.' + this.value + '').slideDown();
            }
        }).on('click', '#hb-apply-coupon', function () {
            applyCoupon();
        }).on('click', '#hb-remove-coupon', function (evt) {
            evt.preventDefault();
            var table = $(this).parents('table');
            $.ajax({
                url: hotel_settings.ajax,
                type: 'post',
                dataType: 'html',
                data: {
                    action: 'hotel_booking_remove_coupon'
                },
                beforeSend: function () {
                    table.hb_overlay_ajax_start();
                },
                success: function (response) {
                    table.hb_overlay_ajax_stop();
                    response = parseJSON(response)
                    if (response.result == 'success') {
                        window.location.href = window.location.href
                    }
                }
            });
        });

        // single room detail tabs
        var hb_single_details = $('.hb_single_room_details');
        var hb_single_details_tab = hb_single_details.find('.hb_single_room_tabs')
        var hb_single_details_content = hb_single_details.find('.hb_single_room_tabs_content');
        var hb_single_tab_details = $('.hb_single_room_tab_details');
        var hb_current_uri = window.location.href;

        var commentID = hb_current_uri.match(/\#comment-[0-9]+/gi);

        if (commentID && typeof commentID[0] !== 'undefined') {
            hb_single_details_tab.find('a').removeClass('active');
            hb_single_details_tab.find('a[href="#hb_room_reviews"]').addClass('active');
        } else {
            hb_single_details_tab.find('a:first').addClass('active');
            $('.hb_single_room_tabs_content .hb_single_room_tab_details:not(:first)').hide();
        }

        hb_single_tab_details.hide();
        var tabActive = hb_single_details_tab.find('a.active').attr('href');
        hb_single_details_content.find(tabActive).fadeIn();

        hb_single_details_tab.find('a').on('click', function (event) {
            event.preventDefault();
            hb_single_details_tab.find('a').removeClass('active');
            $(this).addClass('active');
            var tab_id = $(this).attr('href');
            hb_single_tab_details.hide();
            hb_single_details_content.find(tab_id).fadeIn();
            return false;
        });

        $('.hb-rating-input').rating();

        $('#commentform').submit(function () {
            var rate = $('#rating'),
                val = rate.val();
            if (rate.length === 1 && typeof val !== 'undefined' && val === '') {
                window.alert(hotel_booking_i18n.review_rating_required);
                return false;
            }
            $(this).submit();
        });
    });

    // rating single room
    $.fn.rating = function () {
        var ratings = this,
            legnth = this.length;

        for (var i = 0; i < legnth; i++) {
            var rating = $(ratings[i]),
                html = [];

            html.push('<span class="rating-input" data-rating="1"></span>');
            html.push('<span class="rating-input" data-rating="2"></span>');
            html.push('<span class="rating-input" data-rating="3"></span>');
            html.push('<span class="rating-input" data-rating="4"></span>');
            html.push('<span class="rating-input" data-rating="5"></span>');
            html.push('<input name="rating" id="rating" type="hidden" value="" />');
            rating.html(html.join(''));

            rating.mousemove(function (e) {
                e.preventDefault();
                var parentOffset = ratings.offset(),
                    relX = e.pageX - parentOffset.left,
                    star = $(this).find('.rating-input'),
                    star_width = star.width(),
                    rate = Math.ceil(relX / star_width);

                for (var y = 0; y < star.length; y++) {
                    var st = $(star[y]),
                        _data_star = parseInt(st.attr('data-rating'));
                    if (_data_star <= rate) {
                        st.addClass('high-light');
                    }
                }
            }).mouseout(function (e) {
                var parentOffset = ratings.offset(),
                    relX = e.pageX - parentOffset.left,
                    star = $(this).find('.rating-input'),
                    star_width = star.width(),
                    rate = $(this).find('.rating-input.selected');

                if (rate.length === 0) {
                    star.removeClass('high-light');
                } else {
                    for (var y = 0; y < star.length; y++) {
                        var st = $(star[y]),
                            _data_star = parseInt(st.attr('data-rating'));

                        if (_data_star <= parseInt(rate.attr('data-rating'))) {
                            st.addClass('high-light');
                        } else {
                            st.removeClass('high-light');
                        }
                    }
                }
            }).mousedown(function (e) {
                var parentOffset = ratings.offset(),
                    relX = e.pageX - parentOffset.left,
                    star = $(this).find('.rating-input'),
                    star_width = star.width(),
                    rate = Math.ceil(relX / star_width);
                star.removeClass('selected').removeClass('high-light');
                for (var y = 0; y < star.length; y++) {
                    var st = $(star[y]),
                        _data_star = parseInt(st.attr('data-rating'));
                    if (_data_star === rate) {
                        st.addClass('selected').addClass('high-light');
                        break;
                    } else {
                        st.addClass('high-light');
                    }
                }
                rating.find('input[name="rating"]').val(rate);
            });

        }

    }

    // overlay before ajax
    $.fn.hb_overlay_ajax_start = function () {
        var _self = this;
        _self.css({
            'position': 'relative',
            'overflow': 'hidden'
        });
        var overlay = '<div class="hb_overlay_ajax">';
        overlay += '</div>';

        _self.append(overlay);
    }

    $.fn.hb_overlay_ajax_stop = function () {
        var _self = this;
        var overlay = _self.find('.hb_overlay_ajax');

        overlay.addClass('hide');
        var timeOut = setTimeout(function () {
            overlay.remove();
            clearTimeout(timeOut);
        }, 400);
    }

})(( jQuery ));