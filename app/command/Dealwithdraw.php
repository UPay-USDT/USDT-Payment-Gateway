<?php
declare (strict_types=1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Dealwithdraw extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('Dealwithdraw')
            ->setDescription('the Dealwithdraw command');
    }

    protected function execute(Input $input, Output $output)
    {
        $withdrawmodel = new \app\common\model\Withdraw();
        $addressmodel = new \app\common\model\Address();
        $addresstransfermodel = new \app\common\model\AddressTransfer();
        $addressmoneychangemodel = new \app\common\model\AddressMoneychange();
        $time = time();
        $min_trx = sysconfig('riskconfig', 'min_trx');
        $min_eth = sysconfig('riskconfig', 'min_eth');
        //去获取所有未处理的代付 
        $where = [];
        $where[] = ['status', '=', 0];
        $withdrawlist = $withdrawmodel->where($where)->select()->toArray();
        if ($withdrawlist) {
            foreach ($withdrawlist as $value) {
                // 开启事务
                $withdrawmodel->startTrans();
                $withdrawInfo = $withdrawmodel->where("id", $value['id'])->lock(true)->find();
                if (empty($withdrawInfo) || $withdrawInfo['status'] != 0) {
                    $withdrawmodel->rollback();
                    $output->writeln('代付ID：' . $withdrawInfo['id'] . ",已经处理了");
                    continue;
                }
                $start_time = time();
                //没有处理过的
                if ($withdrawInfo['deal_flag'] == 0) {
                    //变动金额为提款金额
                    $change_money = $withdrawInfo['money'];
                    //这里进行转账USDT   找出能量 和usdt都够的地址
                    $where = [];
                    $where[] = ['usdt_balance', '>=', $change_money];
                    $where[] = ['type', '=', $withdrawInfo['chain_type']];
                    $where[] = ['address', '<>', $withdrawInfo['receive_address']];
                    $addressInfo = $addressmodel->where($where)->order("usdt_balance desc")->lock(true)->find();
                    //如果没有满足条件的地址
                    if (empty($addressInfo)) {
                        $withdrawmodel->rollback();
                        $output->writeln('所有地址USDT余额不足');
                        continue;
                    }
                    $output->writeln('测试代付地址：' . $addressInfo['address'] . ' - ' . $withdrawInfo['receive_address']);
                    $result = transfer_usdt($addressInfo['address'], $withdrawInfo['receive_address'], $change_money, "3", $withdrawInfo['id'], $withdrawInfo['merchant_withdraw_sn']);
                    if ($result['code'] != 1) {
                        $withdrawmodel->rollback();
                        $output->writeln($result['msg']);
                        continue;
                    } else {
                        //修改标志  已经执行过了 等待查询是否成功
                        $save = $withdrawmodel->where("id", $withdrawInfo['id'])->save(["deal_flag" => 1, "submit_time" => time()]);
                        if (!$save) {
                            $withdrawmodel->rollback();
                            $output->writeln('修改标志失败');
                            continue;
                        }
                        $deal_time = time() - $start_time;
                        $withdrawmodel->commit();
                        $output->writeln('代付ID：' . $withdrawInfo['id'] . ",提交交易成功,耗时" . $deal_time . "秒");
                        continue;
                    }
                }
                if ($withdrawInfo['deal_flag'] == 1) {
                    //去查询代付是否成功  是否支付成功
                    $map = [];
                    $map[] = ['change_order_id', '=', $withdrawInfo['id']];
                    $map[] = ['type', '=', 3];
                    $transaction = $addresstransfermodel->where($map)->lock(true)->find();
                    if (empty($transaction['transaction_id'])) {
                        $withdrawmodel->rollback();
                        $output->writeln('代付ID：' . $withdrawInfo['id'] . ",交易不存在");
                        continue;
                    }
                    $transfer_result = find_transfer($transaction['transaction_id'], $transaction['chain_type']);
                    $deal_time = time() - $start_time;

                    $output->writeln('交易查询结果：' . $transfer_result['msg']);
                    //交易成功
                    if ($transfer_result['code'] == 1) {
                        //修改该笔交易记录的状态为已确认
                        $res1 = $addresstransfermodel->where("id", $transaction['id'])->save(["is_confirm" => 1, "transfer_time" => $transfer_result['data']['time']]);
                        //记录入账
                        $res2 = $addressmoneychangemodel->recordAddressMoneyChange($transaction['to_address'], $transaction['money'], $transaction, 1);
                        //记录出账
                        $res3 = $addressmoneychangemodel->recordAddressMoneyChange($transaction['from_address'], $transaction['money'], $transaction, 2);
                        if (!$res1 || !$res2 || !$res3) {
                            $withdrawmodel->rollback();
                            $output->writeln('代付ID：' . $withdrawInfo['id'] . ",修改状态和记录交易错误" . $deal_time . "秒");
                            continue;
                        }
                        //修改代付状态
                        $save = $withdrawmodel->where("id", $withdrawInfo['id'])->save(["status" => 1, "give_time" => $transfer_result['data']['time']]);
                        if (!$save) {
                            // 事务回滚
                            $withdrawmodel->rollback();
                            $output->writeln('代付ID：' . $withdrawInfo['id'] . ",修改状态失败，耗时" . $deal_time . "秒");
                            continue;
                        }
                        $withdrawmodel->commit();
                        $output->writeln('代付ID：' . $withdrawInfo['id'] . ",代付成功,耗时" . $deal_time . "秒");
                        continue;
                    } else {
                        $withdrawmodel->rollback();
                        $output->writeln('代付ID：' . $withdrawInfo['id'] . ",代付未成功,耗时" . $deal_time . "秒");
                        continue;
                    }

                }
            }
        } else {
            $output->writeln('没有未支付的代付');
        }
        // 指令输出
        $output->writeln('执行完毕');
    }
}
