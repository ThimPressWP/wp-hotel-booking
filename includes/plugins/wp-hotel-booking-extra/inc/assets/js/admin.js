(function ($) {

    TPHB_Extra_Admin = {

        init: function () {
            this.add_extra();
            this.remove_extra();
            this.toggle_extra();
        },

        add_extra: function () {
            $(document).on('click', '.tp_extra_add_item', function (e) {
                e.preventDefault();
                var current_package = $('.tp_extra_form_fields:last'),
                    new_package_id = new Date().getTime(),
                    tmpl = wp.template('tp-hb-extra-room');
                tmpl = tmpl({id: new_package_id});

                if (current_package.length === 0)
                    $('.tp_extra_form_head').after(tmpl);
                else
                    current_package.after(tmpl);
            });
        },

        remove_extra: function () {
            $(document).on('click', '.tp_extra_form_fields .remove_button', function (e) {
                e.preventDefault();

                if (!confirm(hotel_booking_i18n.remove_confirm))
                    return;

                var _self = $(this),
                    package_id = _self.attr('data-id'),
                    exta = _self.parents('.tp_extra_form_fields');

                $.ajax({
                    url: hotel_settings.ajax,
                    type: 'POST',
                    data: {
                        package_id: package_id,
                        action: 'tp_extra_package_remove'
                    }
                }).done(function (res) {
                    if (typeof res.status !== 'undefined' && res.status === 'success') {
                        exta.remove();
                    }
                });
            });
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
        }

    };

    $(document).ready(function () {

        TPHB_Extra_Admin.init();

    });

})(jQuery);