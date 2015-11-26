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
					current_package_id = current_package.find('.remove').find('a').attr('data-id');

				if( typeof current_package_id !== 'undefined' )
					new_package_id = parseInt(current_package_id) + parseInt(1);
				else
					new_package_id = 0;

				var tmpl = wp.template( 'tp-hb-extra-room' );
				tmpl = tmpl({ id: new_package_id });

				if( new_package_id === 0 )
					$('.tp_extra_form_head').after( tmpl );
				else
					current_package.after( tmpl );
			});
		},

		remove_extra: function()
		{
			$( document ).on( 'click', '.tp_extra_form_fields .remove_button', function(e){
				e.preventDefault();

				if( ! confirm( TPHB_Extra_Lang.remove_confirm ) )
					return;

				var _self = $(this),
					exta = _self.parents( '.tp_extra_form_fields' );

					exta.remove();
			});
		},

	};

	$(document).ready(function(){

		TPHB_Extra_Admin.init();

	});

})(jQuery);