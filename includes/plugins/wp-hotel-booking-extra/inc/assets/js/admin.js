(function($){

	TPHB_Extra_Admin = {

		init: function()
		{
			this.add_extra();
			this.remove_extra();
		},

		add_extra: function()
		{
			$( document ).on( 'click', '.tp_extra_add_item', function(e){
				e.preventDefault();
				var current_package = $('.tp_extra_form_fields:last'),
					new_package_id = new Date().getTime(),
					tmpl = wp.template( 'tp-hb-extra-room' );
				tmpl = tmpl({ id: new_package_id });

				if( current_package.length === 0 )
					$('.tp_extra_form_head').after( tmpl );
				else
					current_package.after( tmpl );
			});
		},

		remove_extra: function()
		{
			$( document ).on( 'click', '.tp_extra_form_fields .remove_button', function(e){
				e.preventDefault();

				if( ! confirm( hotel_booking_i18n.remove_confirm ) )
					return;

				var _self = $(this),
					package_id = _self.attr('data-id'),
					exta = _self.parents( '.tp_extra_form_fields' );

				$.ajax({
					url: hotel_settings.ajax,
					type: 'POST',
					data: {
						package_id: package_id,
						action: 'tp_extra_package_remove'
					}
				}).done( function( res ){
					if( typeof res.status !== 'undefined' && res.status === 'success' ){
						exta.remove();
					}
				});
			});
		},

	};

	$(document).ready(function(){

		TPHB_Extra_Admin.init();

	});

})(jQuery);