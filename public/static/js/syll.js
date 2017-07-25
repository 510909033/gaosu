 (function(doc, win) {
     var docEl = doc.documentElement,
         resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
         recalc = function() {
             var clientWidth = docEl.clientWidth;
             if (!clientWidth) return;
             docEl.style.fontSize = 20 * (clientWidth / 375) + 'px';
         };
     if (!doc.addEventListener) return;
     win.addEventListener(resizeEvt, recalc, false);
     doc.addEventListener('DOMContentLoaded', recalc, false);
 })(document, window);



$(function() {
        $(".service_nav .col").on("click", function() {
            $(".service_nav .col").removeClass("active");
            $(this).addClass("active");
            var index = $(this).index();
            $(".syzpdrop").hide();
            $(".syzpdrop").eq(index).show();
        });

        $(".service_nav1 .col").on("click", function() {
            $(".service_nav1 .col").removeClass("active");
            $(this).addClass("active");
            var index = $(this).index();
            $(".service_main1").children(".ser_lists").hide();
            $(".service_main1").children(".ser_lists").eq(index).show();
        });

        // $('.jq-myda').on('click',function(){
        //     $(this).parents('.jq-dzhe').remove();
        // })

        $('.jq-scbx').on('click',function(){
            $(this).parents('.apfwlistsbx').remove();
        })

        $('.appendsl').on('click',function(){
            $(this).toggleClass('active');
        })


        //无地址的时候
        $('.batbtn').on('click',function(){
             $(this).parents('.delbox').remove();
             $('.nonefix').remove();
        })

        //加减(加)
        $('.appendsr .jq-sja').on('click',function(){
            var njs = $(this).siblings('.aprin').val();
            var num = parseInt(njs)+1;
            if (num==0) {
                return;
            }
            $(this).siblings('.aprin').val(num);
        })
        // 加减(减)
        $('.appendsr .jq-sjj').on('click',function(){
            var njs = $(this).siblings('.aprin').val();
            var num=parseInt(njs)-1;
            if(num==0){
                return;
            }
            $(this).siblings('.aprin').val(num);
        })
    })


