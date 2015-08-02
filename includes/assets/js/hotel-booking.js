;(function($){
    function isEmail( email ){
        return new RegExp( '^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$' ).test(email);
    }
    function parseJSON(data){
        if(!$.isPlainObject(data)){
            var m = data.match(/<!-- HB_AJAX_START -->(.*)<!-- HB_AJAX_START -->/);
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
                action: 'hotel_booking_fetch_custom_info'
            },
            success: function(response){
                response = parseJSON(response)
            },
            error: function(){
                alert(hotel_booking_l18n.ajax_error)
            }
        });
    }

    function orderSubmit(e){
        var $form = $(this),
            action = window.location.href.replace(/\?.*/, '');
        try {
            if ($form.triggerHandler('hb_order_submit') === false) {
                return false;
            }
            $form.attr('action', action);
        }catch(e){
            alert(e)
        }
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

        $('form#hb-payment-form').submit(orderSubmit);

        $('#fetch-customer-info').click(fetchCustomerInfo);
    })

})((jQuery));