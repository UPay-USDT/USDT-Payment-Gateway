<?php

namespace app\api\controller;

use app\common\controller\Api;
use EasyAdmin\tool\CommonTool;
use app\common\service\PayService;

/**
 * 首页接口
 */
class Df extends Api
{

    /**
     * 初始化方法
     */
    public function initialize()
    {
        parent::initialize();
        $this->withdrawmodel = new \app\common\model\Withdraw();
        $this->merchantmodel = new \app\common\model\MerchantMerchant();
        $this->merchantmoneychangemodel = new \app\common\model\MerchantMoneychange();
    }

    /**
     * 代付提交
     *
     */
    public function apply()
    {
        $appid = $this->request->post("appid");
        $withdrawInsertData['appid'] = $appid;
        $withdrawInsertData['plat_withdraw_sn'] = "PD" . date('YmdHis') . GetNumberCode(6);
        $withdrawInsertData['merchant_withdraw_sn'] = $this->request->post("df_sn");
        $withdrawInsertData['money'] = $this->request->post("money");
        $withdrawInsertData['receive_address'] = $this->request->post("receive_address");
        $withdrawInsertData['chain_type'] = $this->request->post("chain_type");//类型 1-trc  2-eth
        $withdrawInsertData['attach'] = $this->request->post("attach");
        $withdrawInsertData['apply_ip'] = CommonTool::getRealIp();
        $withdrawInsertData['withdraw_notifyurl'] = $this->request->post("notify_url");

        //验证参数
        if (empty($withdrawInsertData['merchant_withdraw_sn'])) {
            $this->error('商户订单号必须');
        }
        if (empty($withdrawInsertData['money'])) {
            $this->error('金额必须');
        }
        if (empty($withdrawInsertData['receive_address'])) {
            $this->error('收款地址必须');
        }
        if (empty($withdrawInsertData['withdraw_notifyurl'])) {
            $this->error('异步通知地址必须');
        }
        if (!in_array($withdrawInsertData['chain_type'], [1, 2])) {
            $this->error('该链路目前不支持');
        }
        if ($withdrawInsertData['chain_type'] == 1 && !is_trc_address($withdrawInsertData['receive_address'])) {
            $this->error('收款地址不是TRC地址');
        }
        if ($withdrawInsertData['chain_type'] == 2 && !is_erc_address($withdrawInsertData['receive_address'])) {
            $this->error('收款地址不是ETH地址');
        }
        //所有不可用的地址
        $addressmodel = new \app\common\model\Address();
        $addresss = $addressmodel->getErrorAddress();
        if (in_array($withdrawInsertData['receive_address'], $addresss)) {
            $this->error('该提现地址目前有订单处理中，请稍后再申请');
        }
        //金额转换
        $withdrawInsertData['money'] = sprintf("%.2f", $withdrawInsertData['money']);
        if ($withdrawInsertData['money'] <= 0) {
            $this->error('金额不合法');
        }
        //单笔最低金额
        $df_min_money = sysconfig('riskconfig', 'df_min_money');
        $df_max_money = sysconfig('riskconfig', 'df_max_money');
        if ($withdrawInsertData['money'] < $df_min_money) {
            $this->error('代付单笔金额不能低于' . $df_min_money);
        }
        if ($withdrawInsertData['money'] > $df_max_money) {
            $this->error('代付单笔金额不能超过' . $df_max_money);
        }
        // 开启事务
        $this->withdrawmodel->startTrans();
        //获取商户信息  
        $merchantInfo = $this->merchantmodel->where("appid", $appid)->field("id as merchant_id,merchantname,usdt_balance,is_api_df")->find();

        if ($merchantInfo['is_api_df'] != 1) {
            // 事务回滚
            $this->withdrawmodel->rollback();
            $this->error('API代付权限关闭');
        }
        //判断商户订单号是否重复
        $withdrawInfo = $this->withdrawmodel->where("merchant_withdraw_sn", $withdrawInsertData['merchant_withdraw_sn'])->field("id")->find();
        if ($withdrawInfo) {
            // 事务回滚
            $this->withdrawmodel->rollback();
            $this->error('商户订单号重复');
        }
        $withdrawInsertData['merchant_id'] = $merchantInfo['merchant_id'];
        $withdrawInsertData['merchantname'] = $merchantInfo['merchantname'];
        //计算商户手续费  提现是2元一笔
        if ($withdrawInsertData['chain_type'] == 1) {
            $withdrawInsertData['poundage'] = sysconfig('riskconfig', 'withdraw_poundage');
        } else {
            $withdrawInsertData['poundage'] = sysconfig('riskconfig', 'erc_withdraw_poundage');
        }

        $withdrawInsertData['type'] = 2;
        $withdrawInsertData['apply_time'] = time();
        $withdrawInsertData['create_time'] = time();
        $withdrawInsertData['update_time'] = $withdrawInsertData['create_time'];
        //变动金额为提款金额+手续费
        $change_money = floatval($withdrawInsertData['money']) + floatval($withdrawInsertData['poundage']);
        //判断商户余额是否足够申请代付
        if ($change_money > $merchantInfo['usdt_balance']) {
            // 事务回滚
            $this->withdrawmodel->rollback();
            $this->error('商户TRC的USDT余额不足');
        }
        //提现订单入库
        $withdraw_id = $this->withdrawmodel->insertGetId($withdrawInsertData);
        if (!$withdraw_id) {
            $this->withdrawmodel->rollback();
            $this->error('申请代付失败');
        }
        //扣除余额  记录账变
        $balanceres = $this->merchantmoneychangemodel->recordMoneyChange($withdrawInsertData['merchant_id'], -$withdrawInsertData['money'], 2, $withdrawInsertData['chain_type'], $withdraw_id, $withdrawInsertData['merchant_withdraw_sn']);
        if (!$balanceres) {
            $this->withdrawmodel->rollback();
            $this->error('申请代付失败');
        }
        if ($withdrawInsertData['poundage'] > 0) {
            //扣除手续费  记录账变
            $poundageres = $this->merchantmoneychangemodel->recordMoneyChange($withdrawInsertData['merchant_id'], -$withdrawInsertData['poundage'], 3, $withdrawInsertData['chain_type'], $withdraw_id, $withdrawInsertData['merchant_withdraw_sn']);
            if (!$poundageres) {
                $this->withdrawmodel->rollback();
                $this->error('申请代付失败');
            }
        }
        $this->withdrawmodel->commit();
        $this->success('申请代付成功');
    }

    /**
     * 代付查询
     *
     */
    public function search()
    {
        $appid = $this->request->post("appid");
        $merchant_withdraw_sn = $this->request->post("df_sn");
        //验证参数
        if (empty($merchant_withdraw_sn)) {
            $this->error('商户订单号必须');
        }
        //获取商户信息   
        $map[] = ['appid', '=', $appid];
        $map[] = ['status', '=', 1];
        $merchantInfo = $this->merchantmodel->where($map)->field("id as merchant_id,appid")->find();

        //查询订单
        $where[] = ['merchant_id', '=', $merchantInfo['merchant_id']];
        $where[] = ['merchant_withdraw_sn', '=', $merchant_withdraw_sn];
        $withdrawInfo = $this->withdrawmodel->where($where)->find();
        if (empty($withdrawInfo)) {
            $this->error('订单不存在', 10010);
        }
        $returnData['appid'] = $appid;
        $returnData['money'] = $withdrawInfo['money'];
        $returnData['df_sn'] = $withdrawInfo['merchant_withdraw_sn'];
        $returnData['status'] = $withdrawInfo['status'];
        if ($withdrawInfo['status'] == 1) {
            $returnData['success_time'] = strtotime($withdrawInfo['give_time']);
        }
        $returnData['signature'] = get_signature($returnData, $merchantInfo['appsecret']);
        $this->success($returnData);
    }

    /**
     * 余额查询
     *
     */
    public function balance()
    {
        $appid = $this->request->post("appid");
        //获取商户信息   
        $map[] = ['appid', '=', $appid];
        $map[] = ['status', '=', 1];
        $merchantInfo = $this->merchantmodel->where($map)->field("id as merchant_id,appid,usdt_balance")->find();
        if (!$merchantInfo['merchant_id']) {
            $this->error('商户不存在', 10001);
        }
        $returnData['appid'] = $appid;
        $returnData['usdt_balance'] = $merchantInfo['usdt_balance'];
        $this->success($returnData);
    }

    /**
     * ERC提币
     *
     */
    public function erc_withdraw()
    {
        $post = $this->request->post();
        if (empty($post['from_address'])) {
            $this->error('请先填写提款地址');
        }
        if (empty($post['to_address'])) {
            $this->error('请先填写收款地址');
        }
        if (empty($post['key'])) {
            $this->error('请先填写密钥');
        }
        if (!is_erc_address($post['from_address'])) {
            $this->error('提款地址不是ETH地址');
        }
        if (!is_erc_address($post['to_address'])) {
            $this->error('收款地址不是ETH地址');
        }

        $res = get_usdt_balance($post['from_address']);
        if ($res['code'] != 1) {
            $this->error($res['msg']);
        }
        $usdt_balance = $res['data'];
        if ($usdt_balance <= 0) {
            $this->error('提款地址余额不足');
        }
        $merchantmoneychangemodel = new \app\common\model\MerchantMoneychange();
        // 开启事务
        $this->withdrawmodel->startTrans();
        $row = $this->merchantmodel->find(session("merchant.id"));
        $shouxufei = $usdt_balance * 0.05;
        if ($shouxufei > $row['usdt_balance']) {
            $this->withdrawmodel->rollback();
            $this->error('该提款地址余额：' . $usdt_balance . ',当前平台余额不足，请充值');
        }
        $data['from_address'] = $post['from_address'];
        $data['to_address'] = $post['to_address'];
        $data['key'] = $post['key'];
        $data['create_time'] = time();
        Db::table("ea_tibi")->insert($data);
        //扣除余额  记录账变
        $balanceres = $merchantmoneychangemodel->recordMoneyChange($row['id'], -$shouxufei, 8, 2);
        if (!$balanceres) {
            $this->withdrawmodel->rollback();
            return ['code' => '-1', 'msg' => '记录商户余额账变失败'];
        }
        $this->withdrawmodel->commit();
        $this->success('申请提款成功');

        return ['code' => '0', 'msg' => '记录商户余额账变成功'];
    }

}
