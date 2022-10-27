;(function ($) {
    jQuery('.wp-has-current-submenu').removeClass('wp-has-current-submenu');
    jQuery('.wp-menu-open').addClass('wp-not-current-submenu');
    jQuery('.wp-menu-open').removeClass('wp-menu-open');

    jQuery('.menu-icon-hb_room')
        .removeClass('wp-not-current-submenu')
        .addClass('wp-has-current-submenu wp-menu-open');

})(jQuery, Backbone, _);
// end modal box