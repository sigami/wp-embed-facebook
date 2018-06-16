(function ($) {
    $(".wef-measure").each(function () {
        $(this).next().attr("data-width", $(this).outerWidth() + "px")
    })
})(jQuery);