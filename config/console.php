<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'order_notify' => 'app\command\Ordernotify',//php think  order_notify 订单异步通知商户  一分钟一次
        'withdraw_notify' => 'app\command\Withdrawnotify',//php think  withdraw_notify 代付异步通知商户  一分钟一次
        'statistics' => 'app\command\Statistics',//php think  statistics 按天统计资金  每天凌晨2点执行
        'deal_order' => 'app\command\Dealorder',//php think  deal_order 处理订单
        'deal_withdraw' => 'app\command\Dealwithdraw',//php think  deal_withdraw 处理代付
        'auto_withdraw' => 'app\command\Autowithdraw',//php think  auto_withdraw 自动提现
        'address_notify' => 'app\command\Addressnotify',//php think  address_notify 推送余额变化记录
        'deal_recharge' => 'app\command\Dealrecharge',//php think  deal_recharge 处理充值单
    ],
];
