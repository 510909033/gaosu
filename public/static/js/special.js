/**
 * Created by Administrator on 17-8-25.
 */



$(function(){
    next();
});

//能否下一步
function next(){
    var cb=document.getElementById("yes");
    var btn = document.getElementById("zhuce");
    btn.style.background = "#e0e0e0";
    btn.style.color = "#000000";
    cb.onclick=function(){
        if(cb.checked==true){
            document.getElementById("zhuce").disabled=false;
            btn.style.background = "#40c087";
            btn.style.color = "#ffffff";
        }
        else{
            document.getElementById("zhuce").disabled=true;
            btn.style.background = "#e0e0e0";
            btn.style.color = "#000000";
        }
    };
}

var countdown=60;
function settime(val) {
    if (countdown == 0) {
        val.removeAttribute("disabled");
        val.value="获取验证码";
        countdown = 60;
        return false;
    } else {
        val.setAttribute("disabled", true);
        val.value="重新发送(" + countdown + ")";
        countdown--;
    }
    setTimeout(function() {
        settime(val);
    },1000);
}


