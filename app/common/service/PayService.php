<?php

namespace app\common\service;

use Cassandra\Map;
use EasyAdmin\tool\CommonTool;
use app\admin\model\SystemConfig;
use think\facade\Db;
use think\cache\driver\Redis;
use think\cache\Driver;
use think\facade\Cache;
use think\facade\Log;

/**
 * 权限验证服务
 * Class PayService
 * @package app\common\service
 */
class PayService
{

    /**
     * 订单金额
     * @var null
     */
    public $pay_money = 0;

    /**
     * 初始化方法
     */
    public function __construct()
    {
        $this->ordermodel = new \app\common\model\Order();
        $this->rechargeordermodel = new \app\common\model\RechargeOrder();
        $this->withdrawmodel = new \app\common\model\Withdraw();
        $this->addressmodel = new \app\common\model\Address();
        $this->merchantmodel = new \app\common\model\MerchantMerchant();
        $this->addresstransfermodel = new \app\common\model\AddressTransfer();
        $this->ownaddressmodel = new \app\common\model\OwnAddress();
        $this->merchantmoneychangemodel = new \app\common\model\MerchantMoneychange();

    }

    /**
     * 分配地址  轮询获取地址  1.状态正常的 能收款的    2.最近没收过款的
     * @return array
     */
    public function allocationAddress($order): array
    {
        //找出商户的收款地址
        $whereaa = [];
        $whereaa[] = ['status', '=', 1];
        $whereaa[] = ['chain_type', '=', $order['chain_type']];
        $whereaa[] = ['merchant_id', '=', $order['merchant_id']];
        $address = $this->ownaddressmodel->where($whereaa)->order("allocation_time asc")->lock(true)->find();
        if (empty($address)) {
            return ['code' => '-1', 'msg' => '请先配置收款地址'];
        }
        //去查询这个地址的未处理订单的全部金额
        $whereaa = [];
        $whereaa[] = ['order_status', 'in', [0, 4, 5]];
        $whereaa[] = ['address', '=', $address['address']];
        $orderlist = $this->ordermodel->where($whereaa)->field("pay_usdt")->order("pay_usdt desc")->select()->toArray();
        //修改最新分配时间
        $allocationuserres = $this->ownaddressmodel->where("id", $address['id'])->update(["allocation_time" => time()]);

        if (empty($orderlist)) {//没有说明可以直接用下单金额
            return ['code' => '1', 'msg' => '下单成功', 'data' => $address];
        } else {
            $payUsdt = $order['pay_usdt'];
            $hashMap = array();
            foreach ($orderlist as $key => $value) {
                $hashMap[] = sprintf("%.2f", $value['pay_usdt']);
            }
            foreach ($orderlist as $key => $value) {
                Log::error('订单:' . $order['merchant_order_sn'] . ', ' . '判断金额冲突：' . $payUsdt . ' 与 ' . json_encode($hashMap));

                if (in_array(sprintf("%.2f", $payUsdt), $hashMap)) {
                    $payUsdt = $payUsdt + 0.01;
                    Log::error('订单:' . $order['merchant_order_sn'] . ', ' . '修改金额为：' . $payUsdt);
                } else {
                    break;
                }
            }

            $address['pay_usdt'] = $payUsdt;
            return ['code' => '1', 'msg' => '下单成功', 'data' => $address];
        }
    }

    public
    function allocationRechargeAddress($order)
    {
        //去查询这个地址是否有同金额未处理的充值单
        $whereaa = [];
        $whereaa[] = ['address', '=', $order['receive_address']];
        $address = $this->addressmodel->where($whereaa)->lock(true)->find();
        if (empty($address)) {
            return ['code' => '-1', 'msg' => '未找到地址信息'];
        }

        $whereaa = [];
        $whereaa[] = ['order_status', '=', 0];
        $whereaa[] = ['receive_address', '=', $order['receive_address']];
        $whereaa[] = ['pay_usdt', '=', $order['pay_usdt']];
        $rechargeorders = $this->rechargeordermodel->where($whereaa)->lock(true)->select()->toArray();
        if (empty($rechargeorders)) {
            return ['code' => '1', 'msg' => '下单成功', 'data' => $address];
        } else {
            $pay_usdt = array_column($rechargeorders, 'pay_usdt');
            while (in_array($order['pay_usdt'], $pay_usdt)) {
                // 给同金额的充值订单增加随机金额
                $order['pay_usdt'] = $order['pay_usdt'] + floatval(randomFloat());
            }
            $address['pay_usdt'] = $order['pay_usdt'];
            return ['code' => '1', 'msg' => '下单成功', 'data' => $address];
        }
    }

    public
    function order_notify($orderInfo)
    {
        //通知 5次通知 1-立即推送  2-20秒  3-30秒 4-1分钟 5-5分钟
        if ($orderInfo['notice_num'] == '1' && ($orderInfo['last_notify_time'] + 20) > time()) {//第二次推送了  如果距离上次不足20秒 则不推送了
            return ['code' => '-1', 'msg' => '无需推送'];
        }
        if ($orderInfo['notice_num'] == '2' && ($orderInfo['last_notify_time'] + 30) > time()) {//第三次推送了  如果距离上次不足30秒 则不推送了
            return ['code' => '-1', 'msg' => '无需推送'];
        }
        if ($orderInfo['notice_num'] == '3' && ($orderInfo['last_notify_time'] + 60) > time()) {//第四次推送了  如果距离上次不足60秒 则不推送了
            return ['code' => '-1', 'msg' => '无需推送'];
        }
        if ($orderInfo['notice_num'] == '4' && ($orderInfo['last_notify_time'] + 300) > time()) {//第五次推送了  如果距离上次不足300秒 则不推送了
            return ['code' => '-1', 'msg' => '无需推送'];
        }
        $url = $orderInfo['pay_notifyurl'];
        $data = [];
        $update = [];
        $data['appid'] = $orderInfo['appid'];
        $data['order_sn'] = $orderInfo['merchant_order_sn'];
        $data['pay_money'] = sprintf("%.2f", $orderInfo['usdt_cny'] * $orderInfo['actual_usdt']);
        $data['pay_usdt'] = $orderInfo['actual_usdt'];
        $data['status'] = 1;
        $data['success_time'] = strtotime($orderInfo['pay_time']);
        $data['attach'] = $orderInfo['attach'];
        //取随机10位字符串

        //获取商户密钥
        $merchantInfo = $this->merchantmodel->where("appid", $orderInfo['appid'])->field("appsecret")->find();
        $data['signature'] = get_signature($data, $merchantInfo['appsecret']);
        $res = GApiCurlExecute($url, $data);
        if ($res == "OK") {//表示收到了 直接不再推送
            $update['notice_flag'] = 1;
            $update['notice_num'] = $orderInfo['notice_num'] + 1;
            $update['last_notify_time'] = time();
            $this->ordermodel->where("id", $orderInfo['id'])->save($update);
            return ['code' => '1', 'msg' => '推送成功'];
        } else {
            $update['notice_num'] = $orderInfo['notice_num'] + 1;
            $update['last_notify_time'] = time();
            $this->ordermodel->where("id", $orderInfo['id'])->save($update);
            return ['code' => '-2', 'msg' => '推送了，但是没有收到OK'];
        }
    }

    public
    function withdraw_notify($withdrawInfo)
    {
        //通知 5次通知 1-立即推送  2-20秒  3-30秒 4-1分钟 5-5分钟
        if ($withdrawInfo['notice_num'] == '1' && ($withdrawInfo['last_notify_time'] + 20) > time()) {//第二次推送了  如果距离上次不足20秒 则不推送了
            return ['code' => '-1', 'msg' => '无需推送'];
        }
        if ($withdrawInfo['notice_num'] == '2' && ($withdrawInfo['last_notify_time'] + 30) > time()) {//第三次推送了  如果距离上次不足30秒 则不推送了
            return ['code' => '-1', 'msg' => '无需推送'];
        }
        if ($withdrawInfo['notice_num'] == '3' && ($withdrawInfo['last_notify_time'] + 60) > time()) {//第四次推送了  如果距离上次不足60秒 则不推送了
            return ['code' => '-1', 'msg' => '无需推送'];
        }
        if ($withdrawInfo['notice_num'] == '4' && ($withdrawInfo['last_notify_time'] + 300) > time()) {//第五次推送了  如果距离上次不足300秒 则不推送了
            return ['code' => '-1', 'msg' => '无需推送'];
        }
        $url = $withdrawInfo['withdraw_notifyurl'];
        $data = [];
        $update = [];
        $data['appid'] = $withdrawInfo['appid'];
        $data['df_sn'] = $withdrawInfo['merchant_withdraw_sn'];
        $data['money'] = $withdrawInfo['money'];
        $data['status'] = 1;
        $data['success_time'] = strtotime($withdrawInfo['give_time']);
        //取随机10位字符串

        //获取商户密钥
        $merchantInfo = $this->merchantmodel->where("appid", $withdrawInfo['appid'])->field("appsecret")->find();
        $data['signature'] = get_signature($data, $merchantInfo['appsecret']);
        $res = GApiCurlExecute($url, $data);
        if ($res == "OK") {//表示收到了 直接不再推送
            $update['notice_flag'] = 1;
            $update['notice_num'] = $withdrawInfo['notice_num'] + 1;
            $update['last_notify_time'] = time();
            $this->withdrawmodel->where("id", $withdrawInfo['id'])->save($update);
            return ['code' => '1', 'msg' => '推送成功'];
        } else {
            $update['notice_num'] = $withdrawInfo['notice_num'] + 1;
            $update['last_notify_time'] = time();
            $this->withdrawmodel->where("id", $withdrawInfo['id'])->save($update);
            return ['code' => '-2', 'msg' => '推送了，但是没有收到OK'];
        }
    }

    public
    function address_notify($addressInfo)
    {
        $listionaddresstransfermodel = new \app\common\model\ListionAddressTransfer();
        //通知 5次通知 1-立即推送  2-20秒  3-30秒 4-1分钟 5-5分钟
        if ($addressInfo['notice_num'] == '1' && ($addressInfo['last_notify_time'] + 20) > time()) {//第二次推送了  如果距离上次不足20秒 则不推送了
            return ['code' => '-1', 'msg' => '无需推送'];
        }
        if ($addressInfo['notice_num'] == '2' && ($addressInfo['last_notify_time'] + 30) > time()) {//第三次推送了  如果距离上次不足30秒 则不推送了
            return ['code' => '-1', 'msg' => '无需推送'];
        }
        if ($addressInfo['notice_num'] == '3' && ($addressInfo['last_notify_time'] + 60) > time()) {//第四次推送了  如果距离上次不足60秒 则不推送了
            return ['code' => '-1', 'msg' => '无需推送'];
        }
        if ($addressInfo['notice_num'] == '4' && ($addressInfo['last_notify_time'] + 300) > time()) {//第五次推送了  如果距离上次不足300秒 则不推送了
            return ['code' => '-1', 'msg' => '无需推送'];
        }
        $url = $addressInfo['listion_url'];
        $data = [];
        $update = [];
        $data['appid'] = $addressInfo['appid'];
        $data['address'] = $addressInfo['address'];
        $data['value'] = $addressInfo['value'];
        $data['transfer_time'] = strtotime($addressInfo['transfer_time']);
        if (is_trc_address($addressInfo['address'])) {
            $data['chain_type'] = 1;
        }
        if (is_erc_address($addressInfo['address'])) {
            $data['chain_type'] = 2;
        }
        //取随机10位字符串

        //获取商户密钥
        $merchantInfo = $this->merchantmodel->where("appid", $addressInfo['appid'])->field("appsecret")->find();
        $data['signature'] = get_signature($data, $merchantInfo['appsecret']);
        $res = GApiCurlExecute($url, $data);
        if ($res == "OK") {//表示收到了 直接不再推送
            $update['notice_flag'] = 1;
            $update['notice_num'] = $addressInfo['notice_num'] + 1;
            $update['last_notify_time'] = time();
            $listionaddresstransfermodel->where("id", $addressInfo['id'])->save($update);
            return ['code' => '1', 'msg' => '推送成功'];
        } else {
            $update['notice_num'] = $addressInfo['notice_num'] + 1;
            $update['last_notify_time'] = time();
            $listionaddresstransfermodel->where("id", $addressInfo['id'])->save($update);
            return ['code' => '-2', 'msg' => '推送了，但是没有收到OK'];
        }
    }

    public
    function match_order($order)
    {
        //去查询该订单地址的交易情况
        $min_time = strtotime($order['create_time']);
        //订单超时时间
        $time_out = sysconfig('riskconfig', 'time_out');
        $max_time = strtotime($order['create_time']) + $time_out * 60;//超时时间之内的交易
        $transderData = select_transfer($order['address'], $min_time, $max_time, $order['chain_type']);
        if ($transderData['code'] == 1 && !empty($transderData['data'])) {
            foreach ($transderData['data'] as $key => $value) {
                //支付金额和订单金额必须相同 并且支付时间大于订单创建时间
                if (strtolower($value['to_address']) == strtolower($order['address']) && $value['time'] > $min_time && $value['time'] < $max_time && $value['money'] == $order['pay_usdt']) {
                    //去查询这笔交易是否已经存入转账记录表  如果没有则存入
                    $recordData = [];
                    $recordData = $value;
                    $recordData['change_order_id'] = $order['id'];
                    $recordData['change_order_sn'] = $order['merchant_order_sn'];
                    $is_exits = $this->addresstransfermodel->recordTransfer($recordData, '1');
                    $order['old_transfer_time'] = $value['time'];
                    $order['actual_usdt'] = $value['money'];
                    $notifyres = $this->success_notify($order);
                    if ($notifyres['code'] == 1) {
                        return ['code' => '1', 'msg' => '订单支付成功'];
                    } else {
                        return ['code' => '-1000', 'msg' => $notifyres['msg']];
                    }
                }
            }
        }
        return ['code' => '-1', 'msg' => '订单未支付'];

    }

    public
    function success_notify($order, $type = 1)
    {
        $merchantmoneychangemodel = new \app\common\model\MerchantMoneychange();
        //修改订单状态
        $orderupdate['order_status'] = 1;

        $orderupdate['actual_usdt'] = $order['actual_usdt'];
        $orderupdate['pay_time'] = $order['old_transfer_time'];
        $orderupdate['type'] = $type;
        $orderres = $this->ordermodel->where("id", $order['id'])->update($orderupdate);
        if (!$orderres) {
            return ['code' => '-1', 'msg' => '修改订单状态失败'];
        }
        //扣除使用费
        $shiyongfei = $order['poundage_usdt'];
        //给商户减少余额  记录商户账变
        $merchantres = $merchantmoneychangemodel->recordMoneyChange($order['merchant_id'], -$shiyongfei, 7, $order['chain_type'], $order['id'], $order['merchant_order_sn'], '', 0, '', $order['merchant_id']);
        if (!$merchantres) {
            return ['code' => '-1', 'msg' => '减少商户余额失败'];
        }
        return ['code' => '1', 'msg' => '订单支付成功'];
    }

    public
    function match_recharge_order($order)
    {
        $rechargeordermodel = new \app\common\model\RechargeOrder();
        $min_time = strtotime($order['create_time']);
        //订单超时时间
        $time_out = sysconfig('riskconfig', 'time_out');
        //去查询该订单地址的交易情况
        $max_time = strtotime($order['create_time']) + $time_out * 60;//超时时间之内的交易
        $transderData = select_transfer($order['receive_address'], $min_time, $max_time, $order['chain_type']);
        if ($transderData['code'] == 1 && !empty($transderData['data'])) {
            foreach ($transderData['data'] as $key => $value) {
                //支付金额和订单金额必须相同 并且支付时间大于订单创建时间
                if (strtolower($value['to_address']) == strtolower($order['receive_address']) && $value['time'] > $min_time && $value['time'] < $max_time && $value['money'] == $order['pay_usdt']) {
                    //去查询这笔交易是否已经存入转账记录表  如果没有则存入
                    $recordData = [];
                    $recordData = $value;
                    $recordData['change_order_id'] = $order['id'];
                    $recordData['change_order_sn'] = $order['recharge_order_sn'];
                    // 记录地址账变
                    $is_exits = $this->addresstransfermodel->recordTransfer($recordData, '1');
                    if (!$is_exits) {
                        return ['code' => '-1', 'msg' => '存入地址交易记录失败'];
                    }
                    $order['pay_time'] = $value['time'];
                    $order['actual_usdt'] = $value['money'];
                    // 给商户增加余额  记录商户账变
                    $merchantres = $this->merchantmoneychangemodel->recordMoneyChange($order['merchant_id'], $order['actual_usdt'], 10, $order['chain_type'], $order['id'], $order['recharge_order_sn'], '充值', 0, '', $order['merchant_id']);
                    if (!$merchantres) {
                        return ['code' => '-1', 'msg' => '增加商户充值金额失败'];
                    }
                    // 修改订单状态和付款地址
                    $order['from_address'] = $value['from_address'];
                    $order['create_time'] = strtotime($order['create_time']);
                    $order['update_time'] = strtotime($order['update_time']);
                    $order['order_status'] = 1;
                    $orderres = $rechargeordermodel->where("id", $order['id'])->update($order);
                    if (!$orderres) {
                        return ['code' => '-1', 'msg' => '修改充值订单状态失败'];
                    }
                    return ['code' => '1', 'msg' => '充值订单支付成功'];
                }
            }
        }
        return ['code' => '-1', 'msg' => '充值订单未支付'];
    }
}
