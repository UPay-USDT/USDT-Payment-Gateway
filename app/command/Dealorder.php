<?php
declare (strict_types=1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\common\service\PayService;

class Dealorder extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('Dealorder')
            ->setDescription('the Dealorder command');
    }

    protected function execute(Input $input, Output $output)
    {
        $ordermodel = new \app\common\model\Order();
        $payService = new PayService();
        $time_out = sysconfig('riskconfig', 'time_out');
        $time = time();

        //去获取所有未支付的订单
        $where = [];
        $where[] = ['order_status', 'in', [0]];
        $orderlist = $ordermodel->where($where)->select()->toArray();
        if ($orderlist) {
            foreach ($orderlist as $value) {
                // 开启事务
                $ordermodel->startTrans();
                $tenorderInfo = $ordermodel->where("id", $value['id'])->lock(true)->find();
                if ($tenorderInfo['order_status'] != 0 && $tenorderInfo['order_status'] != 4) {
                    $ordermodel->rollback();
                    $output->writeln('订单ID：' . $tenorderInfo['id'] . ",已经匹配了");
                    continue;
                }
                $start_time = time();
                //如果时间到了 并且是未支付的订单 则修改订单状态为超时   
                $timeout = strtotime($tenorderInfo['create_time']) + $time_out * 60;
                if ($timeout < $time) {
                    $output->writeln('订单号ID：' . $tenorderInfo['id'] . '变更为超时订单-开始,超时时间为' . $time_out . '分钟,超时时间为' . date('Y-m-d H:i:s', $timeout) . ',当前时间为' . date('Y-m-d H:i:s', $time) . ',订单创建时间为' . $tenorderInfo['create_time'] . '');
                    if ($tenorderInfo['order_status'] == 0) {
                        //修改订单状态为超时
                        $update['order_status'] = 2;
                        $ress = $ordermodel->where("id", $tenorderInfo['id'])->save($update);
                        $deal_time = time() - $start_time;
                        if (!$ress) {
                            $ordermodel->rollback();
                            $output->writeln('订单号ID：' . $tenorderInfo['id'] . '变更为超时订单-失败,耗时' . $deal_time . "秒");
                            continue;
                        } else {
                            $ordermodel->commit();
                            $output->writeln('订单号ID：' . $tenorderInfo['id'] . '变更为超时订单-成功,耗时' . $deal_time . "秒");
                            continue;
                        }
                    } else {
                        $ordermodel->rollback();
                        continue;
                    }
                }

                $output->writeln('订单ID：' . $tenorderInfo['id'] . ",开始匹配");
                //进行订单匹配  是否支付成功
                $result = $payService->match_order($tenorderInfo);
                if ($result['code'] == -1) {
                    $output->writeln('订单ID：' . $tenorderInfo['id'] . ",匹配失败,原因：" . $result['msg']);
                }

                $deal_time = time() - $start_time;
                if ($result['code'] == 1) {//如果匹配成功
                    $ordermodel->commit();
                    $output->writeln('订单ID：' . $tenorderInfo['id'] . ",支付成功,耗时" . $deal_time . "秒");

                    continue;
                }
                if ($result['code'] == -1000) {
                    $ordermodel->rollback();
                    $output->writeln('订单ID：' . $tenorderInfo['id'] . ",支付成功，但修改失败,耗时" . $deal_time . "秒，原因：" . $result['msg']);
                    continue;
                }

                $ordermodel->rollback();
                $deal_time = time() - $start_time;
                $output->writeln('订单号ID：' . $tenorderInfo['id'] . '未支付、未超时,耗时' . $deal_time . "秒");
            }
        } else {
            $output->writeln('没有未支付的订单');
        }
        // 指令输出
        $output->writeln('执行完毕');
    }
}
