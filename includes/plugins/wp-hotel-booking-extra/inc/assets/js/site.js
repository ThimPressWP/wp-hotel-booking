(function ($) {

    TPHB_Extra_Site = {

        init: function () {
            // toggle extra field optional
            this.toggle_extra();
            // toggle input number when checked checkbox and process price
            // this.toggleCheckbox();
            // remove package cart
            this.removePackage();

        },

        parseJSON: function (data) {
            if (!$.isPlainObject(data)) {
                var m = data.match(/<!-- HB_AJAX_START -->(.*)<!-- HB_AJAX_END -->/);
                try {
                    if (m) {
                        data = $.parseJSON(m[1]);
                    } else {
                        data = $.parseJSON(data);
                    }
                } catch (e) {
                    console.log(e);
                    data = {};
                }
            }
            return data;
        },

        toggle_extra: function () {

            $(document).on('change', '.number_room_select', function (e) {
                e.preventDefault();

                var _self = $(this),
                    _form = _self.parents('.hb-search-room-results'),
                    _exta_area = _form.find('.hb_addition_package_extra'),
                    _toggle = _exta_area.find('.hb_addition_packages'),
                    _val = _self.val();

                if (_val !== '') {
                    _form.parent().siblings().find('.hb_addition_packages').removeClass('active').slideUp();
                    _toggle.removeAttr('style').addClass('active');
                    _exta_area.removeAttr('style').slideDown();
                }
                else {
                    _exta_area.slideUp();
                    _val = 1;
                }

                _form.find('.hb_optional_quantity').val(_val);

            });

            $(document).on('click', '.hb_package_toggle', function (e) {
                e.preventDefault();

                var _self = $(this),
                    parent = _self.parents('.hb_addition_package_extra');
                toggle = parent.find('.hb_addition_packages');

                _self.toggleClass('active');
                toggle.toggleClass('active');

                TPHB_Extra_Site.optional_toggle(toggle);
            });
        },

        optional_toggle: function (toggle) {
            if (toggle.hasClass('active'))
                toggle.slideDown();
            else
                toggle.slideUp();
        },

        toggleCheckbox: function () {
            $(document).on('change', '.hb_optional_quantity_selected', function (e) {
                e.preventDefault();
                var _self = $(this),
                    parent = _self.parents('li:first'),
                    inputQuantity = parent.find('.hb_optional_quantity');

                if (_self.is(':checked')) {
                    inputQuantity.attr('readonly', true);
                }
                else {
                    if (!inputQuantity.hasClass('tp_hb_readonly'))
                        inputQuantity.removeAttr('readonly');
                }
            });
        },

        removePackage: function () {
            $(document).on('click', '.hb_package_remove', function (e) {
                e.preventDefault();
                var _self = $(this),
                    _cart_id = _self.attr('data-cart-id'),
                    _parents = _self.parents('.hb_mini_cart_item:first'),
                    _overlay = _self.parents('.hb_mini_cart_item:first, tr');

                if (typeof _parents === 'undefined' || _parents.length === 0) {
                    _parents = _self.parents('.hb_checkout_item.package:first');
                }

                $.ajax({
                    url: hotel_settings.ajax,
                    method: 'POST',
                    data: {
                        action: 'tp_hotel_booking_remove_package',
                        cart_id: _cart_id
                    },
                    dataType: 'html',
                    beforeSend: function () {
                        // ajax start effect
                        _overlay.hb_overlay_ajax_start();
                    }
                }).done(function (res) {
                    res = TPHB_Extra_Site.parseJSON(res);
                    if (typeof res.status !== 'undefined' && res.status == 'success') {
                        HB_Booking_Cart.hb_add_to_cart_callback(res, function () {
                            var cart_table = $('#hotel-booking-payment, #hotel-booking-cart');

                            for (var i = 0; i < cart_table.length; i++) {
                                var _table = $(cart_table[i]);
                                var tr = _table.find('table').find('.hb_checkout_item.package');
                                for (var y = 0; y < tr.length; y++) {
                                    var _tr = $(tr[y]),
                                        _cart_id = _tr.attr('data-cart-id'),
                                        _cart_parent_id = _tr.attr('data-parent-id');
                                    if (_cart_id === res.package_id && _cart_parent_id == res.cart_id) {
                                        var _packages = $('tr.hb_checkout_item.package[data-cart-id="' + _cart_id + '"][data-parent-id="' + _cart_parent_id + '"]'),
                                            _additon_package = $('tr.hb_addition_services_title[data-cart-id="' + _cart_parent_id + '"]'),
                                            _tr_room = $('.hb_checkout_item:not(.package)[data-cart-id="' + _cart_parent_id + '"]'),
                                            _packages_length = $('tr.hb_checkout_item.package[data-parent-id="' + _cart_parent_id + '"]').length;

                                        if (_packages_length === 1) {
                                            _tr.remove();
                                            _additon_package.remove();
                                            _tr_room.find('td:first').removeAttr('rowspan');
                                        }
                                        else {
                                            var _rowspan = _tr_room.find('td:first').attr('rowspan');
                                            _tr.remove();
                                            _tr_room.find('td:first').attr('rowspan', _rowspan - 1);
                                        }

                                        break;
                                    }
                                }

                                if (typeof res.sub_total !== 'undefined')
                                    _table.find('span.hb_sub_total_value').html(res.sub_total);

                                if (typeof res.grand_total !== 'undefined')
                                    _table.find('span.hb_grand_total_value').html(res.grand_total);

                                if (typeof res.advance_payment !== 'undefined')
                                    _table.find('span.hb_advance_payment_value').html(res.advance_payment);

                            }
                        });
                    }
                    // ajax stop effect
                    _overlay.hb_overlay_ajax_stop();
                });
            });
        },

    };

    $(document).ready(function () {
        TPHB_Extra_Site.init();
    });

})(jQuery);