/**
 * Created by Administrator on 17-8-23.
 */

//左侧下拉
function zuoxiala(){
    $(".l1 p").click(function(){
        var zhuangtai = $(this).parent().find('.u2').css("display");
        if(zhuangtai=="block"){
            $(this).parent().find('.u2').hide();
        }else{
            $(this).parent().find('.u2').show();
        }
    });
    $(".l1 p").hover(function(){
        $(this).css("color","#d41b99");
    },function(){
        $(this).css("color","");
    });
    $(".l2").hover(function(){
        $(this).css("color","#d41b99");
    },function(){
        $(this).css("color","");
    });
}
//左右高度相等
function zyxiangdeng(){
    var lmain = $(".left_main").height();
    var rmain = $(".right_main").height();
    if(lmain>rmain){
        $(".right_main").css("height",lmain);
    }else{
        $(".left_main").css("height",rmain);
    }
}
//弹出显示
function tcxianshi(){
    $(".bt2").click(function(){
        $(".tanchukuang").fadeIn();
        $(".baiping").fadeIn();
    });
}
//弹出隐藏
function tcyincang(){
    $(".bt4").click(function(){
        $(".form-control").val("");
        $(".wenben").val("");
        $(".tanchukuang").fadeOut();
        $(".baiping").fadeOut();
    });
}
//右侧隐藏
function cela(){
    $(".hide_bt").click(function(){
        var width =  $(".left_main").css("width");

        if(width=='0px'){
            $(".left_main").animate({width:"20%"},250);
            $(".right_main").animate({width:"79%"},250);
            $(".u1").show();
        }else{
            $(".left_main").animate({width:"0px"},250);
            $(".right_main").animate({width:"99%"},250);
            $(".u1").hide();
        }
    });
}
//时间
function shijian(time){
    var date = new Date(+new Date()+8*3600*1000).toISOString().replace(/T/g,' ').replace(/\.[\d]{3}Z/,'');

    $(time).text(date);
}
//分页
function fenye(){

    $('.fancybox-thumbs').fancybox({
        prevEffect : 'none',
        nextEffect : 'none',
        closeBtn  : false,
        arrows    : false,
        nextClick : true,
        helpers : {
            thumbs : {
                width  : 50,
                height : 50
            }
        }
    });
}
//
function windwg(){

}
$(function(){
    zuoxiala();
    zyxiangdeng();
    tcxianshi();
    tcyincang();
    cela();
    shijian("#shijian");
    $(".baiping").height($(window).height());
    fenye();
    $('.ws_t').fancybox();
    $("body").mLoading("hide");
});

