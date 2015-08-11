;(function($){
    var $doc = $(document);
    function isEmail( email ){
        return new RegExp( '^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$' ).test(email);
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
                action: 'hotel_booking_fetch_custom_info',
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
                        $(this).remove();
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
                            if( response.redirect ){
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
                $("#check_out_date").datepicker("option","maxDate", selected)
            }
        });
        $("#datepickerImage").click(function() {
            $("#txtFromDate").datepicker("show");
        });
        $("#datepickerImage1").click(function() {
            $("#txtToDate").datepicker("show");
        });

        $('#hb-search-form').submit(function() {
            if($('#check_in_date').val() == ''){
                alert('Please Enter Check-In Date');
                return false;
            }else if(jQuery('#check_out_date').val() == ''){
                alert('Please Enter Check-Out Date');
                return false;
            }
            return true;
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
        });

        $('form#hb-payment-form').submit(orderSubmit);

        $('#fetch-customer-info').click(fetchCustomerInfo);

        $doc.on('click', '.hb-view-booking-room-details', function(e){
            e.preventDefault();
            $(this).closest('.hb-room-content').find('.hb-booking-room-details').fadeToggle();
        })
    })

})((jQuery));