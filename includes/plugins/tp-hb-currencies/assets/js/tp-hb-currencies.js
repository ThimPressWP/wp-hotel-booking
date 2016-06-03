;(function($){

	$(document).ready(function(){
		// try {
	 //        /**
	 //         * select2
	 //         */
	 //        $('.hb_form_currencies_switcher_select').select2();
		// }
		// catch(err) {
		//     console.log( 'select2 does not include' );
		// }

		$(document).on( 'change', '.hb_form_currencies_switcher_select', function(e){
			e.preventDefault();
			var currency = $(this).val();
			var href = window.location.href;

			href = href.replace( /[\&]?currency\=[a-z]{1,3}/gi, '' );

			if( href.slice( -1 ) === '/' )
				href += '?currency=' + currency;
			else
				href += '&currency=' + currency;

			// add query to set storage
			window.location = href;
		});
	});

})(jQuery);