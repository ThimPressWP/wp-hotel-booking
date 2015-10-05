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
            alert(hotel_booking_l18n.enter_coupon_code)
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
                    }else{
                        alert(response.message);
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
            numberOfMonths: 1,
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
            numberOfMonths: 1,
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
            if( check_in.compareWith( current ) == -1 ){
                alert( hotel_booking_l18n.check_in_date_must_be_greater );
                $check_in.focus();
                return false;
            }
            if( check_in.compareWith( check_out ) >= 0 ){
                alert( hotel_booking_l18n.check_out_date_must_be_greater );
                $check_out.focus();
                return false;
            }

            var action = $(this).attr('action') || window.location.href;
            $.ajax({
                url: hotel_settings.ajax,
                type: 'post',
                dataType: 'html',
                data: $(this).serialize(),
                success: function (response) {
                    response = parseJSON(response)
                    if(response.success && response.sig){

                        window.location.href = action.replace(/\?.*/, '') + '?hotel-booking-params='+response.sig;
                    }
                }
            });
            return false;
        });
        // $('form[name="hb-search-results"]').submit(function(){
        //     var total_rooms = 0;

        //     if( typeof hotel_settings_cart === 'undefined' || hotel_settings_cart === false )
        //     {
        //         alert( hotel_booking_l18n.no_rooms_selected );
        //         return false;
        //     }

        //     $.ajax({
        //         url: hotel_settings.ajax,
        //         type: 'post',
        //         dataType: 'html',
        //         data: $(this).serialize(),
        //         success: function (response) {
        //             response = parseJSON(response)
        //             if(response.success && response.sig){
        //                 window.location.href = window.location.href.replace(/\?(.*)/, '?hotel-booking-params='+response.sig )
        //             }
        //         }
        //     });

        //     return false;
        // });

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

        // single room detail tabs
        var hb_single_details = $('.hb_single_room_details');
        var hb_single_details_tab = hb_single_details.find('.hb_single_room_tabs')
        var hb_single_details_content = hb_single_details.find('.hb_single_room_tabs_content');
        var hb_single_tab_details = $('.hb_single_room_tab_details');
        var hb_current_uri = window.location.href;

        var commentID = hb_current_uri.match( /\#comment-[0-9]+/gi );

        if( commentID && typeof commentID[0] !== 'undefined' )
        {
            hb_single_details_tab.find('a').removeClass('active');
            hb_single_details_tab.find('a[href="#hb_room_reviews"]').addClass('active');
        }
        else
        {
            hb_single_details_tab.find('a:first').addClass('active');
            $('.hb_single_room_tabs_content .hb_single_room_tab_details:not(:first)').hide();
        }

        hb_single_tab_details.hide();
        var tabActive = hb_single_details_tab.find('a.active').attr('href');
        hb_single_details_content.find(tabActive).show();

        hb_single_details_tab.find('a').on('click', function(event){
            event.preventDefault();
            hb_single_details_tab.find('a').removeClass('active');
            $(this).addClass('active');
            var tab_id = $(this).attr('href');
            hb_single_tab_details.hide();
            hb_single_details_content.find(tab_id).show();
            return false;
        });

        $('.hb-rating-input').rating();

        $('#commentform').submit( function() {
            /*var $email = $( this ).closest( '#respond' ).find( '#email' );
            if( ! $email.val() ){
                $email.focus();
                alert('enter your email');
                return false;
            }*/
        });
    });

    // rating single room
    $.fn.rating = function(){
        return $.each(this, function(){
            var $el = $(this);
            var starWidth = 15;
            $el.html( '<div class="rating-input"><span><input name="rating" id="rating" type="hidden" value="" /></span></div>');
            $('.rating-input', $el).mousemove(function(e){
                var parentOffset = $(this).parent().offset(),
                    relX = e.pageX - parentOffset.left,
                    w = relX - ( relX % starWidth ) + starWidth,
                    rating = w / starWidth;
                $(this).find('span').width( w ).attr('rating', rating);
            }).mouseout(function(){
                var rating = $('input', this).val();
                $(this).find('span').width( rating * starWidth );
            }).mousedown(function(){
                $('input', $(this)).attr('value', $('>span', this).attr('rating'))
                $(this).addClass('mousedown');
            }).mouseup(function(){
                $(this).removeClass('mousedown');
            });
            $(document.body).on( 'click', '#respond #submit', function() {
                var $rating = $( this ).closest( '#respond' ).find( '#rating' ),
                    rating  = $rating.val();
                if ( $rating.size() > 0 && ! rating && hotel_settings.settings.review_rating_required === '1' ) {
                    window.alert( hotel_booking_l18n.review_rating_required );
                    return false;
                }
            });
        })
    }

    // overlay before ajax
    $.fn.hb_overlay_ajax_start = function()
    {
        var _self = this;
        _self.css({
            'position' : 'relative'
        });
        var overlay = '<div class="hb_overlay_ajax">';
        overlay += '</div>';

        _self.append(overlay);
    }

    $.fn.hb_overlay_ajax_stop = function()
    {
        var _self = this;
        var overlay = _self.find('.hb_overlay_ajax');

        overlay.addClass('hide');
        var timeOut = setTimeout(function(){
            overlay.remove();
            clearTimeout(timeOut);
        }, 400);
    }

    $(document).ready(function(){
        var searchResult = $('form.hb-search-room-results');

        searchResult.each(function(){
            $(this).submit(function(event){
                event.preventDefault();
                var number_room_select = $(this).find('.number_room_select').val();
                if( typeof number_room_select === 'undefined' || number_room_select === '' )
                {
                    alert( hotel_settings_language.waring.room_select );
                    return false;
                }
                var data = $(this).serializeArray();
                var room_title = $(this).find('.hb-room-name');
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: data,
                    dataType: 'html',
                    beforeSend: function()
                    {
                        searchResult.hb_overlay_ajax_start();
                    },
                    success: function(code)
                    {
                        searchResult.hb_overlay_ajax_stop();
                        code = parseJSON(code);
                        if( typeof code.message !== 'undefined' )
                            room_title.append( code.message );

                        if( typeof code.status !== 'undefined' && code.status === 'success' )
                        {
                            // add message successfully
                            hotel_settings_cart = true;
                        }
                        else
                        {
                            alert(code.message);
                        }
                    },
                    error: function()
                    {
                        searchResult.hb_overlay_ajax_stop();
                        alert( hotel_settings_language.waring.try_again );
                    }
                });
                return false;
            });
        });

        // var updateOrderButton
    });
})((jQuery));