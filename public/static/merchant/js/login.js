define(["easy-admin"], function (ea) {

    var Controller = {
        index: function () {
                let loginData = {}
            if (top.location !== self.location) {
                    top.location = self.location.hre

            }
            $('.bind-password').on('click', function () {
                if ($(this).hasClass('icon-5')) {
                    $(this).removeClass('icon-5');
                    $("input[name='password']").attr('type', 'password');
                } else {
                    $(this).addClass('icon-5');
                    $("input[name='password']").attr('type', 'text');
                }
            });

            $('.icon-nocheck').on('click', function () {
                if ($(this).hasClass('icon-check')) {
                    $(this).removeClass('icon-check');
                } else {
                    $(this).addClass('icon-check');
                }
            });

            $('.login-tip').on('click', function () {
                $('.icon-nocheck').click();
            });







            ea.listen(function (data) {
                data['keep_login'] = $('.icon-nocheck').hasClass('icon-check') ? 1 : 0;
                localStorage.setItem('data',JSON.stringify(data))
                loginData = data

                return data;
            }, function (res) {
                ea.msg.success(res.msg, function () {

                    window.location = ea.url('index');
                })

            }, function (res) {
            });

        },
        register: function () {
            ea.listen(function (data) {
                return data;
            },function (res) {
                ea.msg.success(res.msg, function () {
                    if (res.code==1) {
                        window.location = ea.url('login');
                    }

                })
            },function(err){

            })
        },
    };
    return Controller;
});
