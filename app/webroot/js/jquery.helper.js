
//* simple modal plugin

jQuery.fn.showModal = function(options)
{
    return this.each(function()
    {
        var modalDiv = $('#modal');
        if (modalDiv.length == 0)
            modalDiv = $('<div id="modal" class="modalDiv"></div>').appendTo(document.body);

        //* you can remove this if you don't have to support IE6
        if ($.browser.msie && $.browser.version == "6.0")
        {
            $('select').hide();
            modalDiv.css({ 'position': 'absolute', 'height': $(document).height() - 5, 'width': $(window).width() }).show();
        }
        else
            modalDiv.css({ 'position': 'fixed' }).show();

        var x = $(window).width() / 2 - $(this).outerWidth() / 2;
        var y = $(window).height() / 2 - $(this).outerHeight() / 2;

        $(this).css({ 'position': 'absolute', 'left': x + $(window).scrollLeft(), 'top': y + $(window).scrollTop(), 'z-index': '10001' }).focus().slideDown();
    });
};


jQuery.fn.hideModal = function (options)
{
    return this.each(function ()
    {
        //* you can remove this if you don't have to support IE6
        if ($.browser.msie && $.browser.version == '6.0')
            $('select').show();

        $(this).slideUp(function() { $('#modal').hide(); });
    });
};


