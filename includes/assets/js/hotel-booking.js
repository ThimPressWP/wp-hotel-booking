;(function($){
    var $doc = $(document);

    if( Date.prototype.compareWith == undefined ) {
        Date.prototype.compareWith = function( d ){
            if( typeof d == 'string' ){
                d = new Date( d );
            }
            var thisTime = parseInt( this.getTime() / 1000 ),
                compareTime = parseInt( d.getTime() / 1000 );
            if( thisTime > compareTime ){
                return 1;
            }else if( thisTime < compareTime ){
                return -1;
            }
            return 0;
        }
    }
    function isEmail( email ){
        return new RegExp( '^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$' ).test(email);
    }

    function isDate( date ){
        date = new Date( date );
        return !isNaN(date.getTime());
    }

    function parseJSON(data){
        if( ! $.isPlainObject(data) ){
            var m = data.match(/<!-- HB_AJAX_START -->(.*)<!-- HB_AJAX_END -->/);
            try {
                if (m) {
                    data = $.parseJSON(m[1]);
                } else {
                    data = $.parseJSON(data);
                }
            }catch(e){
                console.log(e);
                data = {};
            }
        }
        return data;
    }
    function fetchCustomerInfo(){
        var $button = $(this),
            $email = $('input[name="existing-customer-email"]');
        if( ! isEmail( $email.val() ) ){
            alert(hotel_booking_l18n.invalid_email);
            $email.focus();
            return;
        }
        $button.attr('disabled', true);
        $email.attr('disabled', true);
        $.ajax({
            url: hotel_settings.ajax,
            dataType: 'html',
            type: 'post',
            data: {
                action: 'hotel_booking_fetch_customer_info',
                email: $email.val()
            },
            success: function(response){
                response = parseJSON(response);
                if( response && response.ID ){
                    var $container = $('#hb-order-new-customer');
                    for( var key in response.data ){
                        var inputName = key.replace(/^_hb_/, '');
                        var $field = $container.find('input[name="'+inputName+'"], select[name="'+inputName+'"], textarea[name="'+inputName+'"]');
                        $field.val(response.data[key]);
                    }
                    $container.find('input[name="existing-customer-id"]').val(response.ID);
                    $('.hb-order-existing-customer').fadeOut(function(){
                        //$(this).remove();
                    });
                }else{
                    alert( 'Customer email not found!' );
                }
                $button.removeAttr('disabled');
                $email.removeAttr('disabled');

            },
            error: function(){
                alert(hotel_booking_l18n.ajax_error);
                $button.removeAttr('disabled');
                $email.removeAttr('disabled');
            }
        });
    }

    function validateOrder( $form ){

        var $title = $('select[name="title"]', $form);
        if( -1 == $title.val() ){
            alert( hotel_booking_l18n.empty_customer_title );
            $title.focus();
            return false;
        }

        var $firstName = $('input[name="first_name"]', $form);
        if( ! $firstName.val() ){
            alert( hotel_booking_l18n.empty_customer_first_name );
            $firstName.focus();
            return false;
        }

        var $lastName = $('input[name="last_name"]', $form);
        if( ! $lastName.val() ){
            alert( hotel_booking_l18n.empty_customer_last_name );
            $lastName.focus();
            return false;
        }

        var $address = $('input[name="address"]', $form);
        if( ! $address.val() ){
            alert( hotel_booking_l18n.empty_customer_address );
            $address.focus();
            return false;
        }

        var $city = $('input[name="city"]', $form);
        if( ! $city.val() ){
            alert( hotel_booking_l18n.empty_customer_city );
            $city.focus();
            return false;
        }

        var $state = $('input[name="state"]', $form);
        if( ! $state.val() ){
            alert( hotel_booking_l18n.empty_customer_state );
            $state.focus();
            return false;
        }

        var $postalCode = $('input[name="postal_code"]', $form);
        if( ! $postalCode.val() ){
            alert( hotel_booking_l18n.empty_customer_postal_code );
            $postalCode.focus();
            return false;
        }

        var $country = $('select[name="country"]', $form);
        if( ! $country.val() ){
            alert( hotel_booking_l18n.empty_customer_country );
            $country.focus();
            return false;
        }

        var $phone = $('input[name="phone"]', $form);
        if( ! $phone.val() ){
            alert( hotel_booking_l18n.empty_customer_phone );
            $phone.focus();
            return false;
        }

        var $email = $('input[name="email"]', $form);
        if( ! isEmail( $email.val() ) ){
            alert( hotel_booking_l18n.customer_email_invalid );
            $email.focus();
            return false;
        }

        var $payment_method = $('input[name="hb-payment-method"]:checked');
        if( $payment_method.length == 0 ){
            alert( hotel_booking_l18n.no_payment_method_selected );
            return false;
        }

        var $tos = $('input[name="tos"]');
        if( $tos.length && ! $tos.is(':checked') ){
            alert( hotel_booking_l18n.confirm_tos );
            return false;
        }
        if( $('input[name="existing-customer-id"]', $form).val() ) {
            if ($email.val() != $('input[name="existing-customer-email"]', $form).val() ) {
                if( ! confirm(hotel_booking_l18n.customer_email_not_match) ){
                    return false;
                }
            }
        }
        return true;
    }

    function orderSubmit(e){
        var $form = $(this),
            action = window.location.href.replace(/\?.*/, '');
        try {
            if ($form.triggerHandler('hb_order_submit') === false) {
                return false;
            }

            if( ! validateOrder( $form ) ){
                return false;
            }

            $form.attr('action', action);

            $.ajax({
                type: 'POST',
                url: hotel_settings.ajax,
                data: $form.serialize(),
                dataType: 'text',
                success: function (code) {
                    try {
                        var response = parseJSON(code);
                        if( response.result == 'success' ){
                            if( response.redirect != undefined ){
                                window.location.href = response.redirect;
                            }
                        }
                    }catch(e){
                        alert(e)
                    }
                },
                error: function(){
                    alert('eror')
                }

            });

        }catch(e){
            alert(e)
        }
        return false;
    }

    function applyCoupon(){
        var $coupon = $('input[name="hb-coupon-code"]');
        if( ! $coupon.val() ){
            alert('xxx')
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
            success: function (code) {
                try {
                    var response = parseJSON(code);
                    if (response.result == 'success') {
                        window.location.href = window.location.href;
                    }
                } catch (e) {
                    alert(e)
                }
            },
            error: function () {
                alert('error')
            }
        });
    }
    $(document).ready(function(){
        $.datepicker.setDefaults({ dateFormat: 'mm/dd/yy'});
        $("#check_in_date").datepicker({
            minDate: 0,
            maxDate: "+365D",
            numberOfMonths: 2,
            onSelect: function(selected) {
                var date = jQuery(this).datepicker('getDate');
                if(date){
                    date.setDate(date.getDate() + 1);
                }
                $("#check_out_date").datepicker("option","minDate", date)
            }
        });

        $("#check_out_date").datepicker({
            minDate: 0,
            maxDate:"+365D",
            numberOfMonths: 2,
            onSelect: function(selected) {
                $("#check_in_date").datepicker("option","maxDate", selected)
            }
        });
        $("#datepickerImage").click(function() {
            $("#txtFromDate").datepicker("show");
        });
        $("#datepickerImage1").click(function() {
            $("#txtToDate").datepicker("show");
        });

        $('form[name="hb-search-form"]').submit(function() {
            var $check_in = $('#check_in_date', this);
            if( ! isDate( $check_in.val() ) ){
                alert( hotel_booking_l18n.empty_check_in_date );
                $check_in.focus();
                return false;
            }

            var $check_out = $('#check_out_date', this);
            if( ! isDate( $check_out.val() ) ){
                alert( hotel_booking_l18n.empty_check_out_date );
                $check_out.focus();
                return false;
            }

            var check_in = new Date( $check_in.val() ),
                check_out = new Date( $check_out.val()),
                current = new Date();
            /*if( check_in.compareWith( current ) == -1 ){
                alert( hotel_booking_l18n.check_in_date_must_be_greater );
                $check_in.focus();
                return false;
            }*/
            if( check_in.compareWith( check_out ) >= 0 ){
                alert( hotel_booking_l18n.check_out_date_must_be_greater );
                $check_out.focus();
                return false;
            }

            $.ajax({
                url: hotel_settings.ajax,
                type: 'post',
                dataType: 'html',
                data: $(this).serialize(),
                success: function (response) {
                    response = parseJSON(response)
                    if(response.success && response.sig){
                        window.location.href = window.location.href.replace(/\?.*/, '') + '?hotel-booking-params='+response.sig
                    }
                }
            });
            return false;
        });
        $('form[name="hb-search-results"]').submit(function(){
            var total_rooms = 0;

            $('select[name^="hb-num-of-rooms"]').each(function(){
                if( this.value ) {
                    total_rooms += parseInt(this.value);
                }
            });
            if( total_rooms == 0 ) {
                alert( hotel_booking_l18n.no_rooms_selected );
                return false;
            }

            $.ajax({
                url: hotel_settings.ajax,
                type: 'post',
                dataType: 'html',
                data: $(this).serialize(),
                success: function (response) {
                    response = parseJSON(response)
                    if(response.success && response.sig){
                        window.location.href = window.location.href.replace(/\?(.*)/, '?hotel-booking-params='+response.sig )
                    }
                }
            });

            return false;
        });

        $('form#hb-payment-form').submit(orderSubmit);

        $('#fetch-customer-info').click(fetchCustomerInfo);

        $doc.on('click', '.hb-view-booking-room-details', function(e){
            e.preventDefault();
            $(this).closest('.hb-room-content').find('.hb-booking-room-details').fadeToggle();
        }).on('click', 'input[name="hb-payment-method"]', function(){
            if( this.checked ){
                $('.hb-payment-method-form:not(.'+this.value+')').slideUp();
                $('.hb-payment-method-form.'+this.value+'').slideDown();
            }
        }).on('click', '#hb-apply-coupon', function(){
            applyCoupon();
        }).on('click', '#hb-remove-coupon', function(evt){
            evt.preventDefault();
            $.ajax({
                url: hotel_settings.ajax,
                type: 'post',
                dataType: 'html',
                data: {
                    action: 'hotel_booking_remove_coupon'
                },
                success: function (response) {
                    response = parseJSON(response)
                    if(response.result == 'success'){
                        window.location.href = window.location.href
                    }
                }
            });
        });
    })

})((jQuery));