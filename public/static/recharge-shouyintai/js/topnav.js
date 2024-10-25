layui.use(['jquery', 'layer'], function() {
	var $ = layui.$;

	$(".links-drawer-box").click(function(){
           if(!$(this).children(".drawer-line-top").hasClass('open-top')){
            $(this).children(".drawer-line-top").addClass("open-top");
			$(this).children(".drawer-line-bottom").addClass("open-bottom")
			$(".header-drawer-container").addClass("container-open")
			$('body').css({"height":"100vh","overflow":"hidden"})
           }else{
            $(this).children(".drawer-line-top").removeClass("open-top");
			$(this).children(".drawer-line-bottom").removeClass("open-bottom")
			$(".header-drawer-container").removeClass("container-open")
            $('body').css({"height":"auto","overflow":"initial"})
           }
    });

	$(".header-drawer").hover(function () {
		var son = $(this).children(".drawer-off"),
			linksHeight = $(this).find(".header-resources-drawer-box").height();
		son.stop().animate({
			height: linksHeight + 10 + "px",
			paddingTop: "10px",
			top: "80px",
		}, 250);
	}, function () {
		var son = $(this).children(".drawer-off");
		son.stop().animate({
			height: "0",
			padding: "0",
			top: "108px",
		}, 150);
	});

	var pagePath = window.location.pathname,
	pathCenter = pagePath.slice(11, -5),
	document = "document",
	blog = "blog",
	community = "community";

	if(pathCenter == document){
		$(".document-title").css({
			display: "block",
		});
	}else if(pathCenter == blog){
		$(".blog-title").css({
			display: "block",
		});
	}else if(pathCenter == community){
		$(".community-title").css({
			display: "block",
		});
	};
})