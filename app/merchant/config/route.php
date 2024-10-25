<?php
// +----------------------------------------------------------------------
// | 路由设置
// +----------------------------------------------------------------------

return [

    // 路由中间件
    'middleware' => [

        // 后台视图初始化
        \app\merchant\middleware\ViewInit::class,

        // 检测用户是否登录
        \app\merchant\middleware\CheckMerchant::class,


    ],
];
