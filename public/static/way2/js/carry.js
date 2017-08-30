/**
 * Created by Administrator on 17-8-25.
 */


$(function(){
    //屏幕高度
    $(".baiping").height($(window).height());
    get_wh();
});


//获取屏幕尺寸
function get_wh(){
    var bodyW = $(document.body).width();
    var fontSize = bodyW/10;
    $("html").css("font-size",fontSize);
//    $(window).resize(function(){
//        setTimeout(function(){
//            get_wh()
//        }, 0);
//    });
}