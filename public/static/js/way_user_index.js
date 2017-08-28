var cb = document.getElementById("yes");
var btn = document.getElementById("zhuce");
var nm1 = document.getElementById("y1");
var nm2 = document.getElementById("y2");
function hide_btn(){
	nm1.style.display = "none";
	nm2.style.display = "none";
}
function show(msg){
	$("#y2 .y2_1").html(msg);
	nm1.style.display = "block";
	nm2.style.display = "block";
	
}

function hide(){
	$("body").mLoading('hide');
	//nm1.style.display = "none";
	//nm2.style.display = "none";
}
function show_loading(){
	$("body").mLoading('show');
	//nm1.style.display = "block";
	//nm2.style.display = "block";
	//$("#y2 .y2_1").html("数据提交中。。。");
}



btn.style.background = "#e0e0e0";
cb.onclick = function() {
	if (cb.checked == true) {
		document.getElementById("zhuce").disabled = false;
		btn.style.background = "#2ba246";
	} else {
		document.getElementById("zhuce").disabled = true;
		btn.style.background = "#e0e0e0";
	}
}
nm2.onclick = function() {
	nm1.style.display = "none";
	nm2.style.display = "none";
}
