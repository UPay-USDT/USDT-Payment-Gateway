define(["jquery", "easy-admin", "vue"], function ($, ea, Vue) {

    var form = layui.form;

    var Controller = {
        index: function () {
            ea.listen(function (data) {
                return data;
            }, function (res) {
                ea.msg.success(res.msg, function () {
                    window.location = ea.url('/merchant.config/index');
                })
            }, function (res) {
                if (res.url=="/show") {
                    layer.open({
                        title: '接口密钥',
                        type: 1,
                        content: '<div class="layui-card-body layui-text"><table class="layui-table"><colgroup><col width="100"><col></colgroup><tbody><tr><td>APPID</td><td>'+res.data.appid+'</td></tr><tr><td>APPSECRET</td><td>'+res.data.appsecret+'</td></tr></tbody></table></div>',
                        shade: 0 ,
                        area: ['350px', '350px'],
                        closeBtn: 1,
                        id: 'auth',
                        maxmin: false ,
                        extend: 'data-full="false"',
                      });
                }else{
                    ea.msg.error(res.msg, function () {
                });
                }
                
            });
        },
    };
    return Controller;
});