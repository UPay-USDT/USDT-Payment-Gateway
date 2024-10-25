<?php

namespace app\api\controller;

use app\common\controller\Api;
use EasyAdmin\tool\CommonTool;
use app\common\service\PayService;

//use think\cache\driver\Redis;
use think\facade\Cache;
use think\facade\Log;

/**
 * 首页接口
 */
class Pay extends Api
{

    /**
     * 初始化方法
     */
    public function initialize()
    {
        parent::initialize();
        $this->ordermodel = new \app\common\model\Order();
        $this->merchantmodel = new \app\common\model\MerchantMerchant();
    }

    /**
     * 统一下单
     *
     */
    public function unifiedorder()
    {
        Log::info('接口请求下单：' . json_encode($this->request->post()));

        $appid = $this->request->post("appid");
        $orderInsertData['appid'] = $appid;
        $orderInsertData['plat_order_sn'] = "PO" . date('YmdHis') . GetNumberCode(6);
        $orderInsertData['merchant_order_sn'] = $this->request->post("order_sn");
        $orderInsertData['pay_money'] = $this->request->post("pay_money");
        $orderInsertData['product_name'] = $this->request->post("product_name");
        $orderInsertData['pay_username'] = $this->request->post("pay_username");
        $orderInsertData['product_desc'] = $this->request->post("product_desc");
        $orderInsertData['product_num'] = intval($this->request->post("product_num"));
        $orderInsertData['attach'] = $this->request->post("attach");
        $orderInsertData['pay_notifyurl'] = $this->request->post("notify_url");
        $orderInsertData['pay_callbackurl'] = $this->request->post("callback_url");
        $orderInsertData['chain_type'] = $this->request->post("chain_type");//类型 1-trc  2-eth
        $money_type = $this->request->post("money_type") ? $this->request->post("money_type") : 1;//类型 1-USDT  2-CNY 3-INR 4-JPY 5-KRW 6-PHP
        $orderInsertData['apply_ip'] = CommonTool::getRealIp();
        //验证参数
        if (empty($orderInsertData['merchant_order_sn'])) {
            $this->error('商户订单号必须');
        }
        if (empty($orderInsertData['chain_type'])) {
            $this->error('地址类型必须');
        }
        if (!in_array($orderInsertData['chain_type'], [1, 2])) {
            $this->error('该链路目前不支持');
        }
        if (empty($orderInsertData['pay_money'])) {
            $this->error('金额必须');
        }
        if (empty($orderInsertData['pay_notifyurl'])) {
            $this->error('异步通知地址必须');
        }
        //金额转换
        $orderInsertData['pay_money'] = sprintf("%.2f", $orderInsertData['pay_money']);
        //转换成usdt
        $usdt_huilv = get_usdt_cny($money_type);
        if (empty($usdt_huilv)) {
            $this->error('获取usdt价格失败');
        }
        $usdt_cny = get_usdt_cny();
        $usdt_inr = get_usdt_cny(3);
        $usdt_jpy = get_usdt_cny(4);
        $usdt_krw = get_usdt_cny(5);
        $usdt_php = get_usdt_cny(6);
        $usdt_eur = get_usdt_cny(7);
        $usdt_gbp = get_usdt_cny(8);
        $usdt_chf = get_usdt_cny(9);
        $usdt_twd = get_usdt_cny(10);
        $usdt_hkd = get_usdt_cny(11);
        $usdt_mop = get_usdt_cny(12);
        $usdt_sgd = get_usdt_cny(13);
        $usdt_nzd = get_usdt_cny(14);
        $usdt_thb = get_usdt_cny(15);
        $usdt_cad = get_usdt_cny(16);
        //应该支付的usdt
        $orderInsertData['usdt_cny'] = $usdt_cny;
        $orderInsertData['usdt_inr'] = $usdt_inr;
        $orderInsertData['usdt_jpy'] = $usdt_jpy;
        $orderInsertData['usdt_krw'] = $usdt_krw;
        $orderInsertData['usdt_php'] = $usdt_php;
        $orderInsertData['usdt_eur'] = $usdt_eur;
        $orderInsertData['usdt_gbp'] = $usdt_gbp;
        $orderInsertData['usdt_chf'] = $usdt_chf;
        $orderInsertData['usdt_twd'] = $usdt_twd;
        $orderInsertData['usdt_hkd'] = $usdt_hkd;
        $orderInsertData['usdt_mop'] = $usdt_mop;
        $orderInsertData['usdt_sgd'] = $usdt_sgd;
        $orderInsertData['usdt_nzd'] = $usdt_nzd;
        $orderInsertData['usdt_thb'] = $usdt_thb;
        $orderInsertData['usdt_cad'] = $usdt_cad;
        if ($money_type == 1) {
            $orderInsertData['pay_usdt'] = $orderInsertData['pay_money'];
            $orderInsertData['pay_money'] = sprintf("%.2f", $orderInsertData['pay_money'] * $usdt_cny);
        } else {
            $orderInsertData['pay_usdt'] = sprintf("%.2f", $orderInsertData['pay_money'] / $usdt_huilv);
            $orderInsertData['pay_money'] = sprintf("%.2f", $orderInsertData['pay_usdt'] * $usdt_cny);
        }
        //单笔最低金额
        $pay_min_money = sysconfig('riskconfig', 'pay_min_money');
        $pay_max_money = sysconfig('riskconfig', 'pay_max_money');
        if ($orderInsertData['pay_usdt'] <= 0) {
            $this->error('金额不能低于0');
        }
        if ($orderInsertData['pay_usdt'] < $pay_min_money) {
            $this->error('单笔金额不能低于' . $pay_min_money);
        }
        if ($orderInsertData['pay_usdt'] > $pay_max_money) {
            $this->error('单笔金额不能超过' . $pay_max_money);
        }
        //获取商户信息  计算费率和金额
        $merchantInfo = $this->merchantmodel->where("appid", $appid)->field("id as merchant_id,appsecret,merchant_rate,merchantname,is_xiadan,usdt_balance")->find();

        if ($merchantInfo['is_xiadan'] != 1) {
            // 事务回滚
            $this->error('下单权限关闭');
        }
        if ($merchantInfo['usdt_balance'] < $orderInsertData['pay_usdt'] * 0.01) {
            // 事务回滚
            $this->error('余额不足，不能收款');
        }
        //判断商户订单号是否重复
        $orderInfo = $this->ordermodel->where("merchant_order_sn", $orderInsertData['merchant_order_sn'])->field("id")->find();
        if ($orderInfo) {
            // 事务回滚
            $this->error('商户订单号重复');
        }
        $orderInsertData['merchant_rate'] = $merchantInfo['merchant_rate'];
        $orderInsertData['merchant_id'] = $merchantInfo['merchant_id'];
        $orderInsertData['merchantname'] = $merchantInfo['merchantname'];
        $orderInsertData['create_time'] = time();
        $orderInsertData['update_time'] = $orderInsertData['create_time'];
        //分配地址  轮询获取地址  1.状态正常的 能收款的   2.最近没收过款的
        // TODO: 自旋
        $lockKey = "lock_address_" . $orderInsertData['appid'] . '_' . $orderInsertData['pay_usdt'];
        $redis = new \Redis();
        $redis->connect(env('redis.host'), intval(env('redis.port')), 10);

        if (!empty(env('redis.password', ''))) {
            $redis->auth(env('redis.password', ''));
        }

        $isOk = self::spinRedis($redis, $lockKey, '1', 20, 1);
        if (!$isOk) {
            Log::error('下单加锁失败：' . $lockKey);
            $this->error('系统繁忙，请稍后');
        }
        $payService = new PayService();
        $result = $payService->allocationAddress($orderInsertData);
        if ($result['code'] != 1) {
            // 事务回滚
            $this->error($result['msg'], $result['code']);
        }
        if (!empty($result['data']['pay_usdt'])) {
            $orderInsertData['pay_usdt'] = $result['data']['pay_usdt'];

            $redis->set($orderInsertData['plat_order_sn'], $orderInsertData['pay_usdt']);
        }
        $orderInsertData['address_id'] = $result['data']['id'];

        $orderInsertData['address'] = $result['data']['address'];
        $orderInsertData['img'] = $result['data']['img'];
        //计算商户手续费

        $poundageMoney = $merchantInfo['merchant_rate'] * $orderInsertData['pay_money'] < (0.01 * $usdt_cny) ? (0.01 * $usdt_cny) : $merchantInfo['merchant_rate'] * $orderInsertData['pay_money'];
        $poundageUsdt = $merchantInfo['merchant_rate'] * $orderInsertData['pay_usdt'] < 0.01 ? 0.01 : $merchantInfo['merchant_rate'] * $orderInsertData['pay_usdt'];
        $orderInsertData['poundage_money'] = sprintf("%.2f", $poundageMoney);//手续费金额
        $orderInsertData['poundage_usdt'] = sprintf("%.2f", $poundageUsdt);//手续费usdt
        $orderInsertData['merchant_money'] = $orderInsertData['pay_money'] - $orderInsertData['poundage_money'];//商户实得金额
        $orderInsertData['merchant_usdt'] = $orderInsertData['pay_usdt'] - $orderInsertData['poundage_usdt'];//商户实得usdt

        //订单入库
        $order_id = $this->ordermodel->insertGetId($orderInsertData);
        if (!$order_id) {
            $this->error('下单失败，订单入库失败', 10003);
        }

        $isOk = self::redisUnlock($redis, $lockKey);
        if (!$isOk) {
            Log::error('下单解锁失败：' . $lockKey);
        }

        //返回订单信息
        $returnData['appid'] = $appid;
        $returnData['order_sn'] = $orderInsertData['merchant_order_sn'];
        $returnData['pay_usdt'] = floatval(sprintf("%.2f", $orderInsertData['pay_usdt']));
        $returnData['address'] = $orderInsertData['address'];
        $returnData['img'] = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $orderInsertData['img'];
        $returnData['chain_type'] = $orderInsertData['chain_type'];
        $returnData['pay_url'] = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/payment/index/order/id/" . $orderInsertData['merchant_order_sn'];
        $returnData['en_pay_url'] = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/payment/index/en_order/id/" . $orderInsertData['merchant_order_sn'];
        $returnData['exchange_rate'] = $usdt_huilv;
        //订单超时时间
        $time_out = sysconfig('riskconfig', 'time_out');
        $returnData['time_out'] = strtotime($orderInsertData['create_time']) + $time_out * 60;
        //签名
        $returnData['signature'] = get_signature($returnData, $merchantInfo['appsecret']);

        Log::info('接口下单返回：' . json_encode($returnData));

        $this->success($returnData);

        $this->success('下单成功');
    }

    /**
     * 订单查询
     *
     */
    public function search()
    {
        $appid = $this->request->post("appid");
        $merchant_order_sn = $this->request->post("order_sn");
        //验证参数
        if (empty($merchant_order_sn)) {
            $this->error('商户订单号必须');
        }
        //获取商户信息   
        $map[] = ['appid', '=', $appid];
        $map[] = ['status', '=', 1];
        $merchantInfo = $this->merchantmodel->where($map)->field("id as merchant_id,appid,appsecret")->find();
        if (empty($merchantInfo)) {
            $this->error('商户不存在', 10005);
        }
        //查询订单
        $where[] = ['merchant_id', '=', $merchantInfo['merchant_id']];
        $where[] = ['merchant_order_sn', '=', $merchant_order_sn];
        $orderInfo = $this->ordermodel->where($where)->find();
        if (empty($orderInfo)) {
            $this->error('订单不存在', 10006);
        }
        $returnData['appid'] = $appid;
        $returnData['pay_usdt'] = floatval($orderInfo['actual_usdt']);
        $returnData['pay_money'] = sprintf("%.2f", $orderInfo['usdt_cny'] * $orderInfo['actual_usdt']);
        $returnData['order_sn'] = $orderInfo['merchant_order_sn'];
        $returnData['attach'] = $orderInfo['attach'];
        $returnData['status'] = $orderInfo['order_status'];
        if ($returnData['status'] == 1) {
            $returnData['success_time'] = strtotime($orderInfo['pay_time']);
        }
        $returnData['signature'] = get_signature($returnData, $merchantInfo['appsecret']);
        $this->success($returnData);
    }

    /**
     * 创建地址
     *
     */
    public function address()
    {
        $appid = $this->request->post("appid");
        $chain_type = $this->request->post("chain_type");
        //验证参数
        if (empty($chain_type)) {
            $this->error('地址类型必须');
        }
        //获取商户信息   
        $map[] = ['appid', '=', $appid];
        $map[] = ['status', '=', 1];
        $merchantInfo = $this->merchantmodel->where($map)->field("id as merchant_id,appid,merchantname")->find();
        if (empty($merchantInfo)) {
            $this->error('商户不存在', 10005);
        }
        //创建地址
        $address = create_merchant_address($chain_type, $merchantInfo['merchant_id'], $merchantInfo['merchantname']);
        if ($address['code'] == 1) {
            $new_address = $address['data']['address'];
            $this->success($new_address);
        } else {
            $this->error($address['msg']);
        }
    }

    /**
     * 变更监控地址
     *
     */
    public function listion_address()
    {
        $appid = $this->request->post("appid");
        $address = $this->request->post("address");
        $type = $this->request->post("type");
        //验证参数
        if (empty($address)) {
            $this->error('地址必须');
        }
        if (empty($type)) {
            $this->error('type必须');
        }
        //获取商户信息   
        $map[] = ['appid', '=', $appid];
        $map[] = ['status', '=', 1];
        $merchantInfo = $this->merchantmodel->where($map)->field("id as merchant_id,appid,merchantname")->find();
        if (empty($merchantInfo)) {
            $this->error('商户不存在', 10005);
        }
        if ($type == 1) {//增加监控地址
            # code...
        }
        $address = create_merchant_address($chain_type, $merchantInfo['merchant_id'], $merchantInfo['merchantname']);
        if ($address['code'] == 1) {
            $new_address = $address['data']['address'];
            $this->success($new_address);
        } else {
            $this->error($address['msg']);
        }
    }

    /**
     * 创建地址
     *
     */
    public function exchange_rate()
    {
        $huilv = get_usdt_cny();
        if (!empty($huilv)) {
            $this->success($huilv);
        } else {
            $this->error("查询失败，请稍后再试");
        }
    }

    public function spinRedis($redis, $key, $value, int $spinTimes = 20, $spinSeconds = 1)
    {
        while ($spinTimes > 0) {
            $lock = self::redisLock($redis, $key, $value);

            if (!$lock) {
                $spinTimes = $spinTimes - 1;
                sleep($spinSeconds);
            } else {
                return $lock;
            }
        }
        return false;
    }

    public function redisLock($redis, $key, $value)
    {
        return $redis->set($key, $value, array('nx', 'ex' => 2));
    }

    public function redisUnlock($redis, $key)
    {
        return $redis->del($key);
    }

}
