<?php
declare (strict_types = 1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\common\model\Order;
use app\common\service\PayService;

class Ordernotify extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('ordernotify')
            ->setDescription('the ordernotify command');        
    }

    protected function execute(Input $input, Output $output)
    {
        $payService = new PayService();
        //去获取所有支付后未返回的订单  小于5次的
        $where[] = ['order_status', '=', 1];
        $where[] = ['notice_flag', '=', 0];
        $where[] = ['notice_num', '<=', 5];
        $where[] = ['pay_notifyurl','<>', '']; //不为空的
        $list = Order::where($where)->select()->toArray();
        if ($list) {
            foreach ($list as $value){
                $start_time=time();
                $result=$payService->order_notify($value);
                $time=time()-$start_time;
                if ($result['code']==-1) {
                    $output->writeln('无需推送，订单号ID：'.$value['id'].",耗时".$time."秒");
                }elseif ($result['code']==1) {
                    $output->writeln('推送完毕，订单号ID：'.$value['id'].",商户已收到,耗时".$time."秒");
                }else{
                    $output->writeln('推送完毕，订单号ID：'.$value['id'].",商户未返回,耗时".$time."秒");
                }
            }
        }
        // 指令输出
        $output->writeln('推送完毕');
    }
}
