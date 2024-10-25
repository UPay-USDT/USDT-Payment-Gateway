
layui.cache.page = '';
layui.config({
    version: "3.0.0"
    ,base: 'js/'  //这里实际使用时，建议改成绝对路径
}).extend({
    fly: 'index'
}).use(['jquery', 'layer', 'fly'], function() {
    var $ = layui.$;

    $(".links-title").click(function(){
        var winWidth = $(window).width();
        if(winWidth <= 768){
            if(!$(this).next(".links-container").hasClass('open')){
                $(this).next(".links-container").addClass("open");
                $(this).children(".links-I").addClass("links-O")
            }else{
                $(this).next(".links-container").removeClass("open");
                $(this).children(".links-I").removeClass("links-O")
            }
        }
    });

})

//宽度改变则调用此函数
let wWidth = function() {
	let foot = document.getElementById('foot') //获得foot
    let foots = foot.querySelectorAll('div>p') //获得foot里面全部的div
	let uls = foot.getElementsByTagName('ul') //获得foot里面全部的div 中的全部的ul
	let windowWidth = window.innerWidth //获取屏幕宽度
    let footLis = foot.querySelectorAll('div ul li')
	//小于569px ，则认为是移动端。
	if (windowWidth <= 568) {
		//设置移动端 ul 的高度为0
		function ulsNone() {
			for (let j = 0; j < uls.length; j++) {
				uls[j].style.height = '0px'
			}
		}
		ulsNone() //调用一次
		for (let j = 0; j < uls.length; j++) {
			//获取每个ul下的li的数量，用来确定它的高度
			let liH = (uls[j].getElementsByTagName('li').length)
			foots[j].onclick = () => {
				if (parseInt(uls[j].style.height) === 0) { //判断高度是否为0
					ulsNone()
					uls[j].style.height = (liH * 48) + 'px'
				} else {
					ulsNone()
				}
			}
		}
		for(let n = 0;n<footLis.length;n++) {
		    footLis[n].onclick = function(e) {
                e.stopPropagation() //取消事件冒泡
            }
        }
	} else {
		//大于569px ，则认为是PC端。ul高度设置为 auto
		for (let j = 0; j < uls.length; j++) {
			uls[j].style.height = 'auto'
		}
	}
}
wWidth() //函数调用一次
//在这里监听浏览器宽度，动态获取浏览器宽度
window.onresize = function() {
	wWidth()
}


//回到顶部功能
let toTop = document.getElementById('to-top') //获取回到顶部按钮


function toTopChange() {
	if (document.documentElement.scrollTop > 500) {
		toTop.style.display = "block"

	} else {
		toTop.style.display = "none"
	}
	toTop.onclick = function() {
		// document.body.scrollTop = 0;
		// document.documentElement.scrollTop = 0;
		timer = setInterval(function() {
			var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
			var ispeed = Math.floor(-scrollTop / 6);

			if (scrollTop == 0) {
				clearInterval(timer);
			}
			document.documentElement.scrollTop = document.body.scrollTop = scrollTop + ispeed;
		}, 5)

	}
}
toTopChange()

//监听页面滚动
window.onscroll = function() {
    //回到顶部板块
    toTopChange()
    //fixed 板块
    //首先判断 元素是否会超出视口
    if (typeof sh == 'number') {
        if (sh > 0) {
            //需要防抖或者节流
            allHeight()
        } else {
            leftSid.style.position = 'absolute'
            leftSid.style.top = leftFixedTop + 'px'
        }
	}
}

//移除 body 的 height:100%
document.body.style.height = 'auto'