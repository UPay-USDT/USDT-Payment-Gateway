function show_reward_rule(){
	showBg();					
	$(".dialog_rule").show();					
	showDialog();	
	}	

function showBg() {
	var wh = $(window).height();	
	var bh=$("body").innerHeight()
	var bw = $(window).width();
	$("#fullbg").css({
		height:wh>bh?wh:bh,
		width:bw,
		display:"block",
		backgroundColor:"#2f323b",
		opacity:0.95
	});
}  
function showDialog() {
	var wh = $(window).height();
	var bh=$("body").innerHeight()
	var temph = wh>bh?wh:bh;
	var bw = $(window).width();
	var dialogh = $(".dialog").innerHeight();
	$(".dialog").css({
		top:(temph-dialogh)/2-15,
		left:bw*0.1/2-15,
		display:"block",
	});
}

function quit_dialog(){									
	$(".dialog").hide();
	$(".dialog_rule").hide();							
	$("#fullbg").hide();   				
}



// 弹窗控制
function show_tip_dialog(){
	showBg();	
	showTip();
}

function showTip(){
	var wh = $(window).height();
	var bh=$("body").innerHeight()
	var temph = wh>bh?wh:bh;
	var bw = $(window).width();
	var dialogh = $(".tip_dialog").innerHeight();
	$(".tip_dialog").css({
		top:(temph-dialogh)/2-15,
		left:bw*0.1/2-15,
		display:"block",
	});
}

function quit_tip_dialog(){
	$(".tip_dialog").hide();
	$("#fullbg").hide();  
}