<?php
declare (strict_types = 1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class Autowithdraw extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('Autowithdraw')
            ->setDescription('the Autowithdraw command');
    }

    protected function execute(Input $input, Output $output)
    {
        $withdrawmodel = new \app\common\model\Withdraw();
        $merchantmodel = new \app\common\model\MerchantMerchant();
        $addressmodel = new \app\common\model\Address();
        $poundage=sysconfig('riskconfig','withdraw_poundage');
        //单笔最低金额
        $auto_df_min_money=sysconfig('riskconfig','auto_df_min_money');
        $df_max_money=sysconfig('riskconfig','df_max_money');
        //去获取所有商户 状态正常  有提现权限  配置了自动提现的  并且满足条件
        $where=[];
        $where[]=['status','=',1];
        $where[]=['is_api_df','=',1];
        $where[]=['is_sd_df','=',1];
        $where[]=['is_auto_tixian','=',1];
        $merchant_list = $merchantmodel->field("id,appid,merchantname,usdt_balance,trc_address,erc_address,tixian_usdt")->where($where)->whereColumn('usdt_balance','>=','tixian_usdt')->select()->toArray();
        if (!empty($merchant_list)) {
            foreach ($merchant_list as $key => $value) {
                // 开启事务
                $withdrawmodel->startTrans();
                $merchant_info = $merchantmodel->where("id",$value['id'])->lock(true)->find();
                if(empty($merchant_info)){
                    $withdrawmodel->rollback();
                    $output->writeln('商户：'.$value['merchantname'].",不存在");
                    continue;
                }
                //如果是trc的大于 则提现trc的
                if ($value['usdt_balance']>=$value['tixian_usdt']) {
                    $money=$value['usdt_balance']-$poundage;//能提现的金额 为余额减去手续费
                    $receive_address=$value['trc_address'];
                    $chain_type=1;
                }
                //如果能提现的金额小于等于0 则表示是余额不够 
                if ($money<=0) {
                    $withdrawmodel->rollback();
                    $output->writeln('商户：'.$value['merchantname'].",余额不足");
                    continue;
                }
                if (empty($receive_address)) {
                    $withdrawmodel->rollback();
                    $output->writeln('商户：'.$value['merchantname'].",提现地址为空");
                    continue;
                }
                //所有不可用的地址
                $addresss = $addressmodel->getErrorAddress();
                if (in_array($receive_address, $addresss)) {
                    $withdrawmodel->rollback();
                    $output->writeln('商户：'.$value['merchantname'].",提现地址有未完成的订单或代付");
                    continue;
                }
                if ($money<$auto_df_min_money) {
                    $withdrawmodel->rollback();
                    $output->writeln('商户：'.$value['merchantname'].",代付单笔金额不能低于".$auto_df_min_money);
                    continue;
                }
                if ($money>$df_max_money) {
                    $withdrawmodel->rollback();
                    $output->writeln('商户：'.$value['merchantname'].",代付单笔金额不能超过".$df_max_money);
                    continue;
                }
                $result=$withdrawmodel->apply($money,3,$receive_address,$chain_type,$value['appid'],$value['id'],$value['merchantname']);
                if ($result['code']!=1) {
                    $withdrawmodel->rollback();
                    $output->writeln('商户：'.$value['merchantname'].",自动提现失败，原因：".$result['msg']);
                    continue;
                }
                $withdrawmodel->commit();
                $output->writeln('商户：'.$value['merchantname'].",自动提现成功");
                continue;
            }
        }
        // 指令输出
        $output->writeln('没有要执行的自动提现');
    }
}
