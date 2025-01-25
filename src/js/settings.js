(function ($) {
    $(function () {
        if ($('body').hasClass('toplevel_page_wpde')) {
            $('html').css('padding-top', '0');
            $("#wpcontent").prepend($("#wpde-navbar"));
            $('#post-body').removeClass('columns-2').addClass('columns-1');
        }
    });
})(jQuery);
