/**
 * date:2020/02/27
 * author:Mr.Chung
 * version:2.0
 * description:layuimini 主体框架扩展
 */


define(["jquery", "miniMenu", "miniTheme", "miniTab"], function ($, miniMenu, miniTheme, miniTab) {

    var $ = layui.$,
        layer = layui.layer,
        element = layui.element;

    if (!/http(s*):\/\//.test(location.href)) {
        var tips = "请先将项目部署至web容器（Apache/Tomcat/Nginx/IIS/等），否则部分数据将无法显示";
        return layer.alert(tips);
    }

    let obj = {
        cn:{
            'tip1':'商户主页',
            'tip2':'绑定谷歌验证器',
            'tip3':'申请提款',
            'tip4':'订单管理',
            'tip5':'提款记录',
            'tip6':'余额账变',
            'tip7':'财务分析',
            'tip8':'商户设置',
            'tip9':'收款钱包地址',
            'tip10':'使用帮助',
            'tip11':'暂无菜单信息',
            'tip12':'菜单接口有误',
            'tip13':'浏览器不支持全屏调用',
            'tip14':'清理缓存接口有误',
            'tip15':'清除缓存成功',
            'tip16':'刷新成功',
            'tip17':'商户后台',
        },
        en:{
            'tip1':'Overview',
            'tip2':'Security',
            'tip3':'Withdraw',
            'tip4':'Transaction History',
            'tip5':'Withdraw History',
            'tip6':'Balance History',
            'tip7':'Daily History',
            'tip8':'Settings',
            'tip9':'Receive Wallet Address',
            'tip10':'Contact us',
            'tip11':'No menu information',
            'tip12':'Menu port is wrong',
            'tip13':'The browser does not support full-screen transfer',
            'tip14':'Error clearing the cache interface',
            'tip15':'Clear cache successfully',
            'tip16':'Refresh successfully',
            'tip17':' Merchant',
        }
    }
    let lang = document.cookie.includes('en-us')?'en-us':'zh-cn'
    let findText = (text)=>{
                switch (text) {
                    case '商户主页':
                        text = lang === 'en-us'?obj.en['tip1']:obj.cn['tip1']
                        break
                    case '商户后台':
                        text = lang === 'en-us'?obj.en['tip17']:obj.cn['tip17']
                        break
                    case '申请提款':
                        text = lang === 'en-us'?obj.en['tip3']:obj.cn['tip3']
                        break
                    case '绑定谷歌验证器':
                        text = lang === 'en-us'?obj.en['tip2']:obj.cn['tip2']
                        break
                    case '订单管理':
                        text = lang === 'en-us'?obj.en['tip4']:obj.cn['tip4']
                        break
                    case '提款记录':
                        text = lang === 'en-us'?obj.en['tip5']:obj.cn['tip5']
                        break
                    case '余额账变':
                        text = lang === 'en-us'?obj.en['tip6']:obj.cn['tip6']
                        break
                    case '财务分析':
                        text = lang === 'en-us'?obj.en['tip7']:obj.cn['tip7']
                        break
                    case '商户设置':
                        text = lang === 'en-us'?obj.en['tip8']:obj.cn['tip8']
                        break
                    case '收款钱包地址':
                        text = lang === 'en-us'?obj.en['tip9']:obj.cn['tip9']
                        break
                    case '使用帮助':
                        text = lang === 'en-us'?obj.en['tip10']:obj.cn['tip10']
                        break
                    case '暂无菜单信息':
                        text = lang === 'en-us'?obj.en['tip11']:obj.cn['tip11']
                        break
                    case '菜单接口有误':
                        text = lang === 'en-us'?obj.en['tip12']:obj.cn['tip12']
                        break
                    case '浏览器不支持全屏调用':
                        text = lang === 'en-us'?obj.en['tip13']:obj.cn['tip13']
                        break
                    case '清理缓存接口有误':
                        text = lang === 'en-us'?obj.en['tip14']:obj.cn['tip14']
                        break
                    case '清除缓存成功':
                        text = lang === 'en-us'?obj.en['tip15']:obj.cn['tip15']
                        break
                    case '刷新成功':
                        text = lang === 'en-us'?obj.en['tip16']:obj.cn['tip16']
                        break
                }
                return text
            }


    var miniAdmin = {

        /**
         * 后台框架初始化
         * @param options.iniUrl   后台初始化接口地址
         * @param options.clearUrl   后台清理缓存接口
         * @param options.urlHashLocation URL地址hash定位
         * @param options.bgColorDefault 默认皮肤
         * @param options.multiModule 是否开启多模块
         * @param options.menuChildOpen 是否展开子菜单
         * @param options.loadingTime 初始化加载时间
         * @param options.pageAnim iframe窗口动画
         * @param options.maxTabNum 最大的tab打开数量
         */
        render: function (options) {

            options.iniUrl = options.iniUrl || null;
            options.clearUrl = options.clearUrl || null;
            options.urlHashLocation = options.urlHashLocation || false;
            options.bgColorDefault = options.bgColorDefault || 0;
            options.multiModule = options.multiModule || false;
            options.menuChildOpen = options.menuChildOpen || false;
            options.loadingTime = options.loadingTime || 1;
            options.pageAnim = options.pageAnim || false;
            options.maxTabNum = options.maxTabNum || 20;
            $.getJSON(options.iniUrl, function (data) {
                if (data == null) {
                    miniAdmin.error(findText('暂无菜单信息'))
                } else {
                    let menuList = data.menuInfo
                    //qqq remove menuItem erc
                    if(data.menuInfo.length === 1){
                        let newChild  = data.menuInfo[0]?.child.filter(item=>![260,257].includes(item.id))
                        let newO
                        if(data.logoInfo.href === '/merchant/index/index.html'){
                             newO = {
                                href: "/merchant/index/contact.html",
                                icon: "fa fa-phone-square",
                                id: 600,
                                pid: 228,
                                target: "_self",
                                title: findText("使用帮助"),
                            }
                            for(let v of newChild){
                                v.title = findText(v.title)
                            }
                        }else if( data.logoInfo.href === "/user/index/index.html"){
                             newO = {
                                href: "/user/index/contact.html",
                                icon: "fa fa-phone-square",
                                id: 600,
                                pid: 228,
                                target: "_self",
                                title: "使用帮助",
                            }
                        }


                        if(newO) newChild.push(newO)

                        menuList = [{...menuList,child:newChild}]
                        // add concat info    257 的 页面替换
                    }


                    // menuList = [{...menuList,child:newChild}]
                    miniAdmin.renderLogo(data.logoInfo);
                    miniAdmin.renderClear(options.clearUrl);
                    miniAdmin.renderHome(data.homeInfo);
                    miniAdmin.renderAnim(options.pageAnim);
                    miniAdmin.listen();
                    miniMenu.render({
                        menuList: menuList,
                        // menuList: data.menuInfo,
                        multiModule: options.multiModule,
                        menuChildOpen: options.menuChildOpen
                    });
                    miniTab.render({
                        filter: 'layuiminiTab',
                        urlHashLocation: options.urlHashLocation,
                        multiModule: options.multiModule,
                        menuChildOpen: options.menuChildOpen,
                        maxTabNum: options.maxTabNum,
                        menuList: menuList,
                        // menuList: data.menuInfo,
                        homeInfo: data.homeInfo,
                        listenSwichCallback: function () {
                            miniAdmin.renderDevice();
                        }
                    });
                    miniTheme.render({
                        bgColorDefault: options.bgColorDefault,
                        listen: true,
                    });
                    miniAdmin.deleteLoader(options.loadingTime);
                }
            }).fail(function () {
                miniAdmin.error(findText('菜单接口有误'));
            });
        },

        /**
         * 初始化logo
         * @param data
         */
        renderLogo: function (data) {


            let site =  localStorage.getItem('site_name')
            let html = `
                <a style="display:flex;justify-content:flex-start;align-items:center;padding: 0 15px;" href="${data.href}">
                    <div style="font-weight: 700;font-size: 26px;color: #263237;display: flex;justify-content: flex-start;align-items: center">
                        <span style="display:inline-block;max-width:119px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis">
                       ${site || data.title}
                      

                        
                        </span>
                        <span style="font-weight: 500;font-size: 14px;line-height: 12px;letter-spacing: 0.01em;color: #8995A1;margin-left:10px">后台</span>
                    </div>
                </a>
                `


            $('.layuimini-logo').html(html);
        },

        /**
         * 初始化首页
         * @param data
         */
        renderHome: function (data) {
            sessionStorage.setItem('layuiminiHomeHref', data.href);
            $('#layuiminiHomeTabId').html('<span class="layuimini-tab-active"></span><span class="disable-close">' + data.title + '</span><i class="layui-icon layui-unselect layui-tab-close">ဆ</i>');
            $('#layuiminiHomeTabId').attr('lay-id', data.href);
            $('#layuiminiHomeTabIframe').html('<iframe width="100%" height="100%" frameborder="no" border="0" marginwidth="0" marginheight="0"  src="' + data.href + '"></iframe>');
        },

        /**
         * 初始化缓存地址
         * @param clearUrl
         */
        renderClear: function (clearUrl) {
            $('.layuimini-clear').attr('data-href',clearUrl);
        },

        /**
         * 初始化iframe窗口动画
         * @param anim
         */
        renderAnim: function (anim) {
            if (anim) {
                $('#layuimini-bg-color').after('<style id="layuimini-page-anim">' +
                    '.layui-tab-item.layui-show {animation:moveTop 1s;-webkit-animation:moveTop 1s;animation-fill-mode:both;-webkit-animation-fill-mode:both;position:relative;height:100%;-webkit-overflow-scrolling:touch;}\n' +
                    '@keyframes moveTop {0% {opacity:0;-webkit-transform:translateY(30px);-ms-transform:translateY(30px);transform:translateY(30px);}\n' +
                    '    100% {opacity:1;-webkit-transform:translateY(0);-ms-transform:translateY(0);transform:translateY(0);}\n' +
                    '}\n' +
                    '@-o-keyframes moveTop {0% {opacity:0;-webkit-transform:translateY(30px);-ms-transform:translateY(30px);transform:translateY(30px);}\n' +
                    '    100% {opacity:1;-webkit-transform:translateY(0);-ms-transform:translateY(0);transform:translateY(0);}\n' +
                    '}\n' +
                    '@-moz-keyframes moveTop {0% {opacity:0;-webkit-transform:translateY(30px);-ms-transform:translateY(30px);transform:translateY(30px);}\n' +
                    '    100% {opacity:1;-webkit-transform:translateY(0);-ms-transform:translateY(0);transform:translateY(0);}\n' +
                    '}\n' +
                    '@-webkit-keyframes moveTop {0% {opacity:0;-webkit-transform:translateY(30px);-ms-transform:translateY(30px);transform:translateY(30px);}\n' +
                    '    100% {opacity:1;-webkit-transform:translateY(0);-ms-transform:translateY(0);transform:translateY(0);}\n' +
                    '}' +
                    '</style>');
            }
        },

        fullScreen: function () {
            var el = document.documentElement;
            var rfs = el.requestFullScreen || el.webkitRequestFullScreen;
            if (typeof rfs != "undefined" && rfs) {
                rfs.call(el);
            } else if (typeof window.ActiveXObject != "undefined") {
                var wscript = new ActiveXObject("WScript.Shell");
                if (wscript != null) {
                    wscript.SendKeys("{F11}");
                }
            } else if (el.msRequestFullscreen) {
                el.msRequestFullscreen();
            } else if (el.oRequestFullscreen) {
                el.oRequestFullscreen();
            } else if (el.webkitRequestFullscreen) {
                el.webkitRequestFullscreen();
            } else if (el.mozRequestFullScreen) {
                el.mozRequestFullScreen();
            } else {
                miniAdmin.error(findText('浏览器不支持全屏调用')+'！');
            }
        },

        /**
         * 退出全屏
         */
        exitFullScreen: function () {
            var el = document;
            var cfs = el.cancelFullScreen || el.webkitCancelFullScreen || el.exitFullScreen;
            if (typeof cfs != "undefined" && cfs) {
                cfs.call(el);
            } else if (typeof window.ActiveXObject != "undefined") {
                var wscript = new ActiveXObject("WScript.Shell");
                if (wscript != null) {
                    wscript.SendKeys("{F11}");
                }
            } else if (el.msExitFullscreen) {
                el.msExitFullscreen();
            } else if (el.oRequestFullscreen) {
                el.oCancelFullScreen();
            }else if (el.mozCancelFullScreen) {
                el.mozCancelFullScreen();
            } else if (el.webkitCancelFullScreen) {
                el.webkitCancelFullScreen();
            } else {
                miniAdmin.error(findText('浏览器不支持全屏调用')+'！');
            }
        },

        /**
         * 初始化设备端
         */
        renderDevice: function () {
            if (miniAdmin.checkMobile()) {
                $('.layuimini-tool i').attr('data-side-fold', 1);
                $('.layuimini-tool i').attr('class', 'fa fa-outdent');
                $('.layui-layout-body').removeClass('layuimini-mini');
                $('.layui-layout-body').addClass('layuimini-all');
            }
        },


        /**
         * 初始化加载时间
         * @param loadingTime
         */
        deleteLoader: function (loadingTime) {
            $('.layuimini-loader').fadeOut();
        },

        /**
         * 成功
         * @param title
         * @returns {*}
         */
        success: function (title) {
            return layer.msg(title, {icon: 1, shade: this.shade, scrollbar: false, time: 2000, shadeClose: true});
        },

        /**
         * 失败
         * @param title
         * @returns {*}
         */
        error: function (title) {
            return layer.msg(title, {icon: 2, shade: this.shade, scrollbar: false, time: 3000, shadeClose: true});
        },

        /**
         * 判断是否为手机
         * @returns {boolean}
         */
        checkMobile: function () {
            var ua = navigator.userAgent.toLocaleLowerCase();
            var pf = navigator.platform.toLocaleLowerCase();
            var isAndroid = (/android/i).test(ua) || ((/iPhone|iPod|iPad/i).test(ua) && (/linux/i).test(pf))
                || (/ucweb.*linux/i.test(ua));
            var isIOS = (/iPhone|iPod|iPad/i).test(ua) && !isAndroid;
            var isWinPhone = (/Windows Phone|ZuneWP7/i).test(ua);
            var clientWidth = document.documentElement.clientWidth;
            if (!isAndroid && !isIOS && !isWinPhone && clientWidth > 1024) {
                return false;
            } else {
                return true;
            }
        },

        /**
         * 监听
         */
        listen: function () {

            /**
             * 清理
             */
            $('body').on('click', '[data-clear]', function () {
                var loading = layer.load(0, {shade: false, time: 2 * 1000});
                sessionStorage.clear();

                // 判断是否清理服务端
                var clearUrl = $(this).attr('data-href');
                if (clearUrl != undefined && clearUrl != '' && clearUrl != null) {
                    $.getJSON(clearUrl, function (data, status) {
                        layer.close(loading);
                        if (data.code != 1) {
                            return miniAdmin.error(data.msg);
                        } else {
                            return miniAdmin.success(data.msg);
                        }
                    }).fail(function () {
                        layer.close(loading);
                        return miniAdmin.error(findText('清理缓存接口有误'));
                    });
                } else {
                    layer.close(loading);
                    return miniAdmin.success(findText('清除缓存成功'));
                }
            });

            /**
             * 刷新
             */
            $('body').on('click', '[data-refresh]', function () {
                $(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload();
                miniAdmin.success(findText('刷新成功'));
            });

            /**
             * 监听提示信息
             */
            $("body").on("mouseenter", ".layui-nav-tree .menu-li", function () {
                if (miniAdmin.checkMobile()) {
                    return false;
                }
                // var classInfo = $(this).attr('class'),
                //     tips = $(this).prop("innerHTML"),
                //     isShow = $('.layuimini-tool i').attr('data-side-fold');
                // if (isShow == 0 && tips) {
                //     tips = "<ul class='layuimini-menu-left-zoom layui-nav layui-nav-tree layui-this'><li class='layui-nav-item layui-nav-itemed'>"+tips+"</li></ul>" ;
                //     window.openTips = layer.tips(tips, $(this), {
                //         tips: [2, '#2f4056'],
                //         time: 3000,
                //         skin:"popup-tips",
                //         success:function (el) {
                //             var left = $(el).position().left - 10 ;
                //             $(el).css({ left:left });
                //             element.render();
                //         }
                //     });
                // }
            });

            $("body").on("mouseleave", ".popup-tips", function () {
                if (miniAdmin.checkMobile()) {
                    return false;
                }
                var isShow = $('.layuimini-tool i').attr('data-side-fold');
                if (isShow == 0) {
                    try {
                        layer.close(window.openTips);
                    } catch (e) {
                        console.log(e.message);
                    }
                }
            });


            /**
             * 全屏
             */
            $('body').on('click', '[data-check-screen]', function () {
                var check = $(this).attr('data-check-screen');
                if (check == 'full') {
                    miniAdmin.fullScreen();
                    $(this).attr('data-check-screen', 'exit');
                    $(this).html('<i class="icon-compress-arrows-alt-solid iconfont"></i>');
                } else {
                    miniAdmin.exitFullScreen();
                    $(this).attr('data-check-screen', 'full');
                    $(this).html('<i class="icon-arrowsalt iconfont"></i>');
                }
            });

            /**
             * 点击遮罩层
             */
            $('body').on('click', '.layuimini-make', function () {
                miniAdmin.renderDevice();
            });

        }
    };



    return miniAdmin;
});
