<?php
declare (strict_types = 1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\common\service\PayService;

class Addressnotify extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('Addressnotify')
            ->setDescription('the Addressnotify command');
    }

    protected function execute(Input $input, Output $output)
    {
        $payService = new PayService();
        $listionaddresstransfermodel = new \app\common\model\ListionAddressTransfer();
        //去获取所有  小于5次的  地址监控记录
        $where[] = ['notice_flag', '=', 0];
        $where[] = ['notice_num', '<=', 5];
        $where[] = ['listion_url','<>', '']; //不为空的
        $list = $listionaddresstransfermodel->where($where)->select()->toArray();
        if ($list) {
            foreach ($list as $value){
                $start_time=time();
                $result=$payService->address_notify($value);
                $time=time()-$start_time;
                if ($result['code']==-1) {
                    $output->writeln('无需推送，交易订单号ID：'.$value['txid'].",耗时".$time."秒");
                }elseif ($result['code']==1) {
                    $output->writeln('推送完毕，交易订单号ID：'.$value['txid'].",商户已收到,耗时".$time."秒");
                }else{
                    $output->writeln('推送完毕，交易订单号ID：'.$value['txid'].",商户未返回,耗时".$time."秒");
                }
                
            }
        }
        // 指令输出
        $output->writeln('推送完毕');
    }
}
