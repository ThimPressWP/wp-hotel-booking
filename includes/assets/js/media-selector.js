;(function($){
    window.mediaSelector = {
        __onSelect: null,
            __multiple: false,
            activeFrame: false,
            frame: function (multiple) {
            multiple = typeof multiple == 'undefined' ? 0 : ( multiple ? 1 : 0)
            if (!this._frame) {
                this._frame = [];
            }
            if (!this._frame[multiple]) {
                this._frame[multiple] = wp.media({
                    title: 'Select Media',
                    button: {
                        text: 'Insert'
                    },
                    multiple: multiple ? true : false
                });
                this._frame[multiple].state('library').on('select', this.select);
            }
            return this._frame[multiple];
        },
        select: function () {
            var app = window.mediaSelector;
            if ($.isFunction(app.__onSelect)) {
                var source = this.get('selection')
                if ( ! app.__multiple ) {
                    source = source.single().toJSON();
                } else {
                    source = source.toJSON();
                }
                app.__onSelect.call(app._frame, source);
                app.__onSelect = null;
            }
        },
        open: function (args) {
            args = $.extend({
                multiple: false,
                onSelect: function () {
                }
            }, args || {});
            if ($.isFunction(args.onSelect)) {
                this.__onSelect = args.onSelect;
                this.__multiple = args.multiple;
                var f = this.frame(args.multiple);
                f.open();
            }
        }
    }
})(jQuery);