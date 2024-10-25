<?php
declare (strict_types = 1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Statistics extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('Statistics')
            ->setDescription('the Statistics command');
    }

    protected function execute(Input $input, Output $output)
    {
        $merchantmodel = new \app\common\model\MerchantMerchant();
        $ordermodel = new \app\common\model\Order();
        $withdrawmodel = new \app\common\model\Withdraw();
        $statisticsmodel = new \app\common\model\Statistics();
        $merchant_list = $merchantmodel->field("id,merchantname")->select()->toArray();
        //去查询第一笔订单
        $firsr_order_time = $ordermodel->order("id asc")->value("create_time");
        $now=strtotime('24:0:0',time());//当前的24点
        if (empty($firsr_order_time)) {
            $firsr_order_time=$now;
        }
        $start_time=strtotime('0:0:0',$firsr_order_time);//第一笔订单时间
        //自定义从几号开始
        $my_time=strtotime('0:0:0',strtotime(date('2022-3-31')));
        //如果没有自定义 则从昨天开始算
        if (!isset($my_time) && empty($my_time)) {
            $my_time=strtotime(date('Y-m-d',strtotime('-1 day')));
        }
        if ($my_time>$start_time) {//如果自定义的时间大于订单的第一笔时间 则从自定义的时间开始算
            $start_time=$my_time;
        }
        $end_time=$start_time+86400;//第一笔订单时间的第二天
        //清除从第一笔订单开始
        $del=$statisticsmodel->where('statistics_time','>=',$start_time)->select();
        $save = $del->delete();
        while ($end_time<=$now) {
            //统计每天的总收款和总手续费
            $where=[];
            $where[] = ['pay_time', '>=', $start_time];
            $where[] = ['pay_time', '<', $end_time];
            $where[] = ['order_status', '=', 1];
            $where[] = ['plat_money', '<', 50];
            //获取所有商户在某天的所有成功订单
            $result = $ordermodel
                ->where($where)
                ->group("merchant_id")
                ->field("sum(pay_usdt)-sum(keys_money2) as m_in_money,sum((pay_usdt-keys_money2)*merchant_rate) as m_in_poundage,merchant_id")
                ->select()
                ->toArray();
            $m_in_money=array_column($result,'m_in_money','merchant_id');
            $m_in_poundage=array_column($result,'m_in_poundage','merchant_id');

            $map=[];
            $map[] = ['give_time', '>=', $start_time];
            $map[] = ['give_time', '<', $end_time];
            $map[] = ['status', '=', 1];
            $map[] = ['flag', '<', 100];
            //获取所有商户在某天的所有成功代付
            $res = $withdrawmodel
                ->where($map)
                ->group("merchant_id")
                ->field("sum(money) as m_out_money,sum(poundage) as m_out_poundage,merchant_id")
                ->select()
                ->toArray();
            $m_out_money=array_column($res,'m_out_money','merchant_id');
            $m_out_poundage=array_column($res,'m_out_poundage','merchant_id');

            $m_insertData=[];
            foreach ($merchant_list as $key => $value) {
                if (!isset($m_in_money[$value['id']])) {
                    $m_in_money[$value['id']]=0;
                }
                if (!isset($m_in_poundage[$value['id']])) {
                    $m_in_poundage[$value['id']]=0;
                }
                if (!isset($m_out_money[$value['id']])) {
                    $m_out_money[$value['id']]=0;
                }
                if (!isset($m_out_poundage[$value['id']])) {
                    $m_out_poundage[$value['id']]=0;
                }
                $m_insertData[]=[
                    'statistics_time'=>$start_time,
                    'merchant_id'=>$value['id'],
                    'merchantname'=>$value['merchantname'],
                    'in_money'=>$m_in_money[$value['id']],
                    'in_poundage'=>$m_in_poundage[$value['id']],
                    'out_money'=>$m_out_money[$value['id']],
                    'out_poundage'=>$m_out_poundage[$value['id']],
                ];
            }
            $re=$statisticsmodel->saveAll($m_insertData);

            $start_time=$start_time+86400;
            $end_time=$end_time+86400;
        }

        // 指令输出
        $output->writeln('执行完毕');
    }
}
