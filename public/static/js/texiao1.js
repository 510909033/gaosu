/**
 * Created by user on 16-10-11.
 */



jQuery(document).ready(function($) {
    var pull = $('#pull');
    menu = $('.navm');
    $(pull).on('click',
        function(e) {
            e.preventDefault();
            menu.slideToggle();
            $(pull).toggleClass('on')
        });
    $(window).resize(function() {
        var w = $(this).width();
        if (w > 640 && menu.is(':hidden')) {
            menu.removeAttr('style')
        }
    });
    $('.navm li').on('click',
        function(e) {
            var w = $(window).width();
            if (w < 640) {
                menu.slideToggle()
            }
        })
    get_wh()
    $(window).resize(function(){
        setTimeout(function(){
            get_wh()
        }, 0);
    });
    function get_wh(){
        var bodyW = $(document.body).width();
        var fontSize = bodyW/10;
        $("html").css("font-size",fontSize);
    }
});

