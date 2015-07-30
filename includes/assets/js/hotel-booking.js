;(function($){

    $(document).ready(function(){
        $.datepicker.setDefaults({ dateFormat: 'mm/dd/yy'});
        $("#check_in_date").datepicker({
            minDate: 0,
            maxDate: "+365D",
            numberOfMonths: 2,
            onSelect: function(selected) {
                var date = jQuery(this).datepicker('getDate');
                if(date){
                    date.setDate(date.getDate() + 2);
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
    })

})((jQuery));