$(document).ready(function() {
    //console.log('xx', AOS);
    window.AOS && window.AOS.init();

    // menu-control
    var rMenu = $('#jy_r_menu'),
        headerMenuControl = $('.header .menu-control');
    $('.menu-control').click(function () {
        rMenu.toggleClass('active');
        headerMenuControl.toggleClass('ign');
    });

    $('#right-helper div:last-child').on('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // prioduct.slider
    $('#slider-main').on('slide.bs.carousel', function (e) {
        var node = e.target;
        var index = $(node).find('.item.active').index();

        setTimeout(function() {
            $(node).prev().children().eq(+index).removeClass('active');
        }, 100);
    });

    $('#slider-main').on('slid.bs.carousel', function (e) {
        var node = e.target;
        var index = $(node).find('.item.active').index();

        $(node).prev().children().removeClass('active');
        $(node).prev().children().eq(+index).addClass('active');
    });
    $('#slider-main').prev().children().on('click', function() {
        let index = $(this).index();
        $('#slider-main').carousel(+index);
    });

    // h5 swipe
    $('.carousel').on('touchstart', function(event){
        var xClick = event.originalEvent.touches[0].pageX;
        $(this).one('touchmove', function(event){
            var xMove = event.originalEvent.touches[0].pageX;
            if( Math.floor(xClick - xMove) > 5 ){
                $(this).carousel('next');
            }
            else if( Math.floor(xClick - xMove) < -5 ){
                $(this).carousel('prev');
            }
        });
        $('.carousel').on('touchend', function(){
            $(this).off('touchmove');
        });
    });
    //加载底部
	$("#footerBox").load("footer.html", function() {
	});
});
//设置cookie
function setCookie(name, value, liveMinutes) {  
	if (liveMinutes == undefined || liveMinutes == null) {
		liveMinutes = 60 * 2;
	}
	if (typeof (liveMinutes) != 'number') {
		liveMinutes = 60 * 2;//默认120分钟
	}
	var minutes = liveMinutes * 60 * 1000;
	var exp = new Date();
	exp.setTime(exp.getTime() + minutes + 8 * 3600 * 1000);
	//path=/表示全站有效，而不是当前页
	document.cookie = name + "=" + value + ";path=/;expires=" + exp.toUTCString();
}
//获取cookie
function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ')
			c = c.substring(1);
		if (c.indexOf(name) != -1)
			return c.substring(name.length, c.length);
	}
	return "";
};
//删除cookies 
function delCookie(name) 
{ 
	 setCookie(name, 1, -1);
}
//判断是不是手机端
function ismobile() {
    //判断访问终端
    var browser = {
        versions: function() {
            var u = navigator.userAgent,
            app = navigator.appVersion;
            return {
                trident: u.indexOf('Trident') > -1,
                //IE内核
                presto: u.indexOf('Presto') > -1,
                //opera内核
                webKit: u.indexOf('AppleWebKit') > -1,
                //苹果、谷歌内核
                gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1,
                //火狐内核
                mobile: ( !! u.match(/AppleWebKit.*Mobile/) || !!u.match(/Windows Phone/) || !!u.match(/Android/) || !!u.match(/MQQBrowser/)) && !u.match(/iPad/),
                //是否为移动终端
                ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/),
                //ios终端                  
                android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1,
                //android终端                             
                iPhone: u.indexOf('iPhone') > -1 || u.indexOf('Mac') > -1 || u.indexOf('UCBrowser') > -1,
                //iPhone终端    
                iPad: u.indexOf('iPad') > -1,
                //是否iPad
                webApp: u.indexOf('Safari') == -1,
                //是否web应该程序，没有头部与底部
                weixin: u.indexOf('MicroMessenger') > -1,
                //是否微信
                qq: u.match(/\sQQ/i) == " qq" //是否QQ
            };
        } (),
        language: (navigator.browserLanguage || navigator.language).toLowerCase()
    }
    //判断是否移动端
    if (browser.versions.mobile || browser.versions.iPhone || browser.versions.android || browser.versions.weixin || browser.versions.qq) {
        return true;
    } else {
        return false;
    }
}