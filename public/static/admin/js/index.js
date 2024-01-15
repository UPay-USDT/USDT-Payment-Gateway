define(["jquery", "easy-admin", "echarts", "echarts-theme", "miniAdmin", "miniTab"], function ($, ea, echarts, undefined, miniAdmin, miniTab) {

    var Controller = {
        index: function () {
            var options = {
                iniUrl: ea.url('ajax/initAdmin'),    // 初始化接口
                clearUrl: ea.url("ajax/clearCache"), // 缓存清理接口
                urlHashLocation: true,      // 是否打开hash定位
                bgColorDefault: false,      // 主题默认配置
                multiModule: true,          // 是否开启多模块
                menuChildOpen: false,       // 是否默认展开菜单
                loadingTime: 0,             // 初始化加载时间
                pageAnim: true,             // iframe窗口动画
                maxTabNum: 20,              // 最大的tab打开数量
            };
            miniAdmin.render(options);

            $('.login-out').on("click", function () {
                ea.request.get({
                    url: 'login/out',
                    prefix: true,
                }, function (res) {
                    ea.msg.success(res.msg, function () {
                        window.location = ea.url('login/index');
                    })
                });
            });
        },
        welcome: function () {
            miniTab.listen();

            /**
             * 查看公告信息
             **/
            $('body').on('click', '.layuimini-notice', function () {
                var title = $(this).children('.layuimini-notice-title').text(),
                    noticeTime = $(this).children('.layuimini-notice-extra').text(),
                    content = $(this).children('.layuimini-notice-content').html();
                var html = '<div style="padding:15px 20px; text-align:justify; line-height: 22px;border-bottom:1px solid #e2e2e2;background-color: #2f4056;color: #ffffff">\n' +
                    '<div style="text-align: center;margin-bottom: 20px;font-weight: bold;border-bottom:1px solid #718fb5;padding-bottom: 5px"><h4 class="text-danger">' + title + '</h4></div>\n' +
                    '<div style="font-size: 12px">' + content + '</div>\n' +
                    '</div>\n';
                layer.open({
                    type: 1,
                    title: '系统公告' + '<span style="float: right;right: 1px;font-size: 12px;color: #b1b3b9;margin-top: 1px">' + noticeTime + '</span>',
                    area: '300px;',
                    shade: 0.8,
                    id: 'layuimini-notice',
                    btn: ['查看', '取消'],
                    btnAlign: 'c',
                    moveType: 1,
                    content: html,
                    success: function (layero) {
                        var btn = layero.find('.layui-layer-btn');
                        btn.find('.layui-layer-btn0').attr({
                            href: 'https://gitee.com/zhongshaofa/layuimini',
                            target: '_blank'
                        });
                    }
                });
            });

            /**
             * 报表功能
             */
            var echartsRecords = echarts.init(document.getElementById('echarts-records'), 'walden');
            var optionRecords = {
                title: {
                    text: '订单统计'
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: [ '订单']
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                toolbox: {
                    feature: {
                        saveAsImage: {}
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: chart_time
                },
                yAxis: {
                    type: 'value'
                },
                series: [

                    {
                        name: '订单',
                        type: 'line',
                        stack: '总量',
                        data: chart_order
                    }
                ]
            };
            echartsRecords.setOption(optionRecords);
            window.addEventListener("resize", function () {
                echartsRecords.resize();
            });
        },
        editAdmin: function () {
            ea.listen();
        },
        editPassword: function () {
            ea.listen();
        },
    };
    return Controller;
});
