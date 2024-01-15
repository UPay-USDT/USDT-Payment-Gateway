<?php

namespace app\payment\controller;

use app\BaseController;
use EasyAdmin\tool\CommonTool;
use app\common\service\PayService;
use think\facade\Log;

class Index extends BaseController
{
    use \app\common\traits\JumpTrait;

    /**
     * 解析和获取模板内容 用于输出
     * @param string $template
     * @param array $vars
     * @return mixed
     */
    public function fetch($template = '', $vars = [])
    {
        return $this->app->view->fetch($template, $vars);
    }

    /**
     * 模板变量赋值
     * @param string|array $name 模板变量
     * @param mixed $value 变量值
     * @return mixed
     */
    public function assign($name, $value = null)
    {
        return $this->app->view->assign($name, $value);
    }

    /**
     * 初始化方法
     */
    public function initialize()
    {
        parent::initialize();
        $this->ordermodel = new \app\common\model\Order();
        $this->merchantmodel = new \app\common\model\MerchantMerchant();
    }

    public function index()
    {
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
        if (empty(input("id"))) {
            $this->assign('merchant_id', "ciofh5fe");
        } else {
            $Info = $this->merchantmodel->where("appid", input("id"))->find();
            if (empty($Info)) {
                $this->error('错误');
            } else {
                $this->assign('merchant_id', input("id"));
            }
        }
        $this->assign('usdt_cny', $usdt_cny);
        $this->assign('usdt_inr', $usdt_inr);
        $this->assign('usdt_jpy', $usdt_jpy);
        $this->assign('usdt_krw', $usdt_krw);
        $this->assign('usdt_php', $usdt_php);
        $this->assign('usdt_eur', $usdt_eur);
        $this->assign('usdt_gbp', $usdt_gbp);
        $this->assign('usdt_chf', $usdt_chf);
        $this->assign('usdt_twd', $usdt_twd);
        $this->assign('usdt_hkd', $usdt_hkd);
        $this->assign('usdt_mop', $usdt_mop);
        $this->assign('usdt_sgd', $usdt_sgd);
        $this->assign('usdt_nzd', $usdt_nzd);
        $this->assign('usdt_thb', $usdt_thb);
        $this->assign('usdt_cad', $usdt_cad);
        return $this->fetch();
    }

    public function timeout()
    {
        $this->assign('data', "订单已超时");
        return $this->fetch();
    }

    public function en_timeout()
    {
        $this->assign('data', "Order timed out");
        return $this->fetch();
    }

    public function check()
    {

        //进行下单 

    }

    public function paydo()
    {
        Log::info('页面请求下单：' . json_encode($this->request->post()));

        //进行下单 
        $appid = $this->request->post("merchant_id");
        $orderInsertData['appid'] = $appid;
        $orderInsertData['plat_order_sn'] = "PO" . date('YmdHis') . GetNumberCode(6);
        $orderInsertData['merchant_order_sn'] = $this->request->post("order_sn") ? $this->request->post("order_sn") : $orderInsertData['plat_order_sn'];
        $orderInsertData['pay_money'] = $this->request->post("pay_money");
        $orderInsertData['product_name'] = $this->request->post("product_name") ? $this->request->post("product_name") : "";
        $orderInsertData['pay_username'] = $this->request->post("pay_username") ? $this->request->post("pay_username") : "";
        $orderInsertData['product_desc'] = $this->request->post("product_desc");
        $orderInsertData['product_num'] = intval($this->request->post("product_num"));
        $orderInsertData['attach'] = $this->request->post("attach");
        $orderInsertData['pay_notifyurl'] = $this->request->post("notify_url") ? $this->request->post("notify_url") : "";
        $orderInsertData['pay_callbackurl'] = $this->request->post("callback_url") ? $this->request->post("callback_url") : "";
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
            $this->error('地址类型错误');
        }

        if (empty($orderInsertData['pay_money'])) {
            $this->error('金额必须');
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
            $this->error('金额不能低于' . $pay_min_money);
        }
        if ($orderInsertData['pay_usdt'] > $pay_max_money) {
            $this->error('金额不能超过' . $pay_max_money);
        }
        // 开启事务
        $this->ordermodel->startTrans();
        //获取商户信息  计算费率和金额
        $merchantInfo = $this->merchantmodel->where("appid", $appid)->field("id as merchant_id,merchant_rate,merchantname,is_xiadan,usdt_balance")->find();
        if ($merchantInfo['is_xiadan'] != 1) {
            // 事务回滚
            $this->ordermodel->rollback();
            $this->error('下单权限关闭');
        }
        if ($merchantInfo['usdt_balance'] < $orderInsertData['pay_usdt'] * 0.01) {

            // 事务回滚
            $this->ordermodel->rollback();
            $this->error('余额不足，不能收款');
        }
        //判断商户订单号是否重复
        $orderInfo = $this->ordermodel->where("merchant_order_sn", $orderInsertData['merchant_order_sn'])->field("id")->find();
        if ($orderInfo) {
            // 事务回滚
            $this->ordermodel->rollback();
            $this->error('商户订单号重复');
        }
        $orderInsertData['merchant_rate'] = $merchantInfo['merchant_rate'];
        $orderInsertData['merchant_id'] = $merchantInfo['merchant_id'];
        $orderInsertData['merchantname'] = $merchantInfo['merchantname'];
        $orderInsertData['create_time'] = time();
        $orderInsertData['update_time'] = $orderInsertData['create_time'];
        //分配地址  轮询获取地址  1.状态正常的 能收款的   2.最近没收过款的
        $payService = new PayService();
        $result = $payService->allocationAddress($orderInsertData);
        if ($result['code'] != 1) {
            // 事务回滚
            $this->ordermodel->rollback();
            $this->error($result['msg'], $result['code']);
        }
        if (!empty($result['data']['pay_usdt'])) {
            $orderInsertData['pay_usdt'] = $result['data']['pay_usdt'];
        }
        $orderInsertData['address_id'] = $result['data']['id'];

        $orderInsertData['address'] = $result['data']['address'];
        $orderInsertData['img'] = $result['data']['img'];
        //计算商户手续费
        //这里判断是哪种模式扣费
        $poundageMoney = $merchantInfo['merchant_rate'] * $orderInsertData['pay_money'] < (0.01 * $usdt_cny) ? (0.01 * $usdt_cny) : $merchantInfo['merchant_rate'] * $orderInsertData['pay_money'];
        $poundageUsdt = $merchantInfo['merchant_rate'] * $orderInsertData['pay_usdt'] < 0.01 ? 0.01 : $merchantInfo['merchant_rate'] * $orderInsertData['pay_usdt'];
        $orderInsertData['poundage_money'] = sprintf("%.2f", $poundageMoney);//手续费金额
        $orderInsertData['poundage_usdt'] = sprintf("%.2f", $poundageUsdt);//手续费usdt
        $orderInsertData['merchant_money'] = $orderInsertData['pay_money'] - $orderInsertData['poundage_money'];//商户实得金额
        $orderInsertData['merchant_usdt'] = $orderInsertData['pay_usdt'] - $orderInsertData['poundage_usdt'];//商户实得usdt
        //订单入库
        $order_id = $this->ordermodel->insertGetId($orderInsertData);
        if (!$order_id) {
            $this->ordermodel->rollback();
            $this->error('下单失败，订单入库失败', 10003);
        }
        $this->ordermodel->commit();
        $data = [
            'code' => 1,
            'msg' => '',
            'data' => "http://" . $_SERVER['HTTP_HOST'] . "/payment/index/order/id/" . $orderInsertData['merchant_order_sn'],
        ];
        return json($data);
    }

    /**
     * 支付页面
     *
     */
    public function order()
    {
        $merchant_order_sn = input("id");
        //验证参数
        if (empty($merchant_order_sn)) {
            $this->assign('data', "商户订单号必须");
            return $this->fetch("timeout");
        }
        //查询订单
        $where[] = ['merchant_order_sn', '=', $merchant_order_sn];
        $orderInfo = $this->ordermodel->where($where)->find();
        if (!empty($orderInfo['pay_callbackurl'])) {
            $this->assign('callback_url', $orderInfo['pay_callbackurl']);
        }
        if (empty($orderInfo)) {
            $this->assign('data', "订单不存在");
            return $this->fetch("timeout");
        }
        if ($orderInfo['order_status'] == 1) {
            $this->assign('data', "订单已支付");
            return $this->fetch("timeout");
        }
        //订单超时时间
        $time_out = sysconfig('riskconfig', 'time_out');
        if (time() >= (strtotime($orderInfo['create_time']) + $time_out * 60)) {
            $this->assign('data', "订单已超时");
            return $this->fetch("timeout");
        }
        $orderInfo['time_out'] = date("Y/m/d H:i:s", strtotime($orderInfo['create_time']) + $time_out * 60);
        $this->assign('data', $orderInfo);
        return $this->fetch();
    }

    /**
     * 英文支付页面
     *
     */
    public function en_order()
    {
        $merchant_order_sn = input("id");
        //验证参数
        if (empty($merchant_order_sn)) {
            $this->assign('data', "Merchant order number must");
            return $this->fetch("en_timeout");
        }
        //查询订单
        $where[] = ['merchant_order_sn', '=', $merchant_order_sn];
        $orderInfo = $this->ordermodel->where($where)->find();
        if (!empty($orderInfo['pay_callbackurl'])) {
            $this->assign('callback_url', $orderInfo['pay_callbackurl']);
        }
        if (empty($orderInfo)) {
            $this->assign('data', "Order does not exist");
            return $this->fetch("en_timeout");
        }
        if ($orderInfo['order_status'] == 1) {
            $this->assign('data', "Order paid");
            return $this->fetch("en_timeout");
        }
        //订单超时时间
        $time_out = sysconfig('riskconfig', 'time_out');
        if (time() >= (strtotime($orderInfo['create_time']) + $time_out * 60)) {
            $this->assign('data', "Order timed out");
            return $this->fetch("en_timeout");
        }
        $orderInfo['time_out'] = date("Y/m/d H:i:s", strtotime($orderInfo['create_time']) + $time_out * 60);
        $this->assign('data', $orderInfo);
        return $this->fetch();
    }

    /**
     * 订单查询
     *
     */
    public function payresult()
    {
        $merchant_order_sn = input("token");
        //验证参数
        if (empty($merchant_order_sn)) {
            $this->error('商户订单号必须');
        }
        //查询订单
        $where[] = ['merchant_order_sn', '=', $merchant_order_sn];
        $orderInfo = $this->ordermodel->where($where)->find();
        if (empty($orderInfo)) {
            $this->error('订单不存在', 10006);
        }

        $returnData['pay_usdt'] = floatval($orderInfo['actual_usdt']);
        $returnData['order_sn'] = $orderInfo['merchant_order_sn'];
        $returnData['plat_sn'] = $orderInfo['plat_order_sn'];
        $returnData['attach'] = $orderInfo['attach'];
        $returnData['status'] = "$orderInfo[order_status]";
        if ($orderInfo['order_status'] == 1) {
            $returnData['success_time'] = strtotime($orderInfo['pay_time']);
        }
        $data = [
            'code' => 1,
            'msg' => '',
            'data' => $returnData,
        ];
        return json($data);
    }

    /**
     * 订单列表
     *
     */
    public function list()
    {
        $where[] = ['appid', '=', 'ciofh5fe'];
        $page = !empty(input('page')) ? input('page') : 1;
        $limit = !empty(input('limit')) ? input('limit') : 10;
        $count = $this->ordermodel
            ->where($where)
            ->count();
        $list = $this->ordermodel
            ->where($where)
            ->page($page, $limit)
            ->field("merchant_order_sn,pay_usdt,order_status,create_time,chain_type,apply_ip,address")
            ->order("id desc")
            ->select()
            ->toArray();
        foreach ($list as $key => &$value) {
            $value['apply_ip'] = preg_replace('/(\d+)\.(\d+)\.(\d+)\.(\d+)/is', "$1.$2.$3.*", $value['apply_ip']);
        }
        $data = [
            'code' => 0,
            'msg' => '',
            'count' => $count,
            'data' => $list,
        ];
        return json($data);
    }

}
