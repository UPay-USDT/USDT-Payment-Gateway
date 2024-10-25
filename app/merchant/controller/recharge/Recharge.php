<?php

namespace app\merchant\controller\recharge;

use app\common\controller\MerchantController;
use think\App;
use EasyAdmin\tool\CommonTool;
use app\common\service\PayService;

/**
 * @ControllerAnnotation(title="订单管理")
 */
class Recharge extends MerchantController
{

    //use \app\merchant\traits\Curd;

    /**
     * 允许修改的字段
     * @var array
     */
    protected $allowModifyFields = [];

    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\common\model\RechargeOrder();
        $this->merchantmodel = new \app\common\model\MerchantMerchant();
        $this->addressmodel = new \app\common\model\Address();
        $this->ownaddressmodel = new \app\common\model\OwnAddress();
        $this->addresstransfermodel = new \app\common\model\AddressTransfer();

        $this->assign('getStatusList', $this->model->getStatusList());

    }

    public function index()
    {
        $usdt_cny=get_usdt_cny();
        $usdt_inr=get_usdt_cny(3);
        $usdt_jpy=get_usdt_cny(4);
        $usdt_krw=get_usdt_cny(5);
        if (empty(input("id"))) {
            $this->error('缺少商户id');
//            $this->assign('merchant_id', "ciofh5fe");
        }else{
            $Info = $this->merchantmodel->where("appid",input("id"))->find();
            if (empty($Info)) {
                $this->error('错误');
            }else{
                $this->assign('merchant_id', input("id"));
            }
        }
        $this->assign('usdt_cny', $usdt_cny);
        $this->assign('usdt_inr', $usdt_inr);
        $this->assign('usdt_jpy', $usdt_jpy);
        $this->assign('usdt_krw', $usdt_krw);
        return $this->fetch();
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
            $this->assign('data', "充值订单号必须");
            return $this->fetch("timeout");
        }
        //查询订单
        $where[]=['recharge_order_sn','=',$merchant_order_sn];
        $orderInfo = $this->model->where($where)->find();
        /*if (!empty($orderInfo['pay_callbackurl'])) {
            $this->assign('callback_url', $orderInfo['pay_callbackurl']);
        }*/
        if (empty($orderInfo)) {
            $this->assign('data', "充值订单不存在");
            return $this->fetch("timeout");
        }
        if ($orderInfo['order_status']==1) {
            $this->assign('data', "充值订单已支付");
            return $this->fetch("timeout");
        }
        //订单超时时间
        $time_out=sysconfig('riskconfig','time_out');
        if (time()>=(strtotime($orderInfo['create_time']) +$time_out*60)) {
            $this->assign('data', "充值订单已超时");
            return $this->fetch("timeout");
        }
        $orderInfo['time_out']=date("Y/m/d H:i:s",strtotime($orderInfo['create_time']) +$time_out*60);
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
            $this->assign('data', "Recharge order number must");
            return $this->fetch("en_timeout");
        }
        //查询订单
        $where[]=['recharge_order_sn','=',$merchant_order_sn];
        $orderInfo = $this->model->where($where)->find();
        /*if (!empty($orderInfo['pay_callbackurl'])) {
            $this->assign('callback_url', $orderInfo['pay_callbackurl']);
        }*/
        if (empty($orderInfo)) {
            $this->assign('data', "Recharge order does not exist");
            return $this->fetch("en_timeout");
        }
        if ($orderInfo['order_status']==1) {
            $this->assign('data', "Recharge order paid");
            return $this->fetch("en_timeout");
        }
        //订单超时时间
        $time_out=sysconfig('riskconfig','time_out');
        if (time()>=(strtotime($orderInfo['create_time']) +$time_out*60)) {
            $this->assign('data', "Recharge order timed out");
            return $this->fetch("en_timeout");
        }
        $orderInfo['time_out']=date("Y/m/d H:i:s",strtotime($orderInfo['create_time']) +$time_out*60);
        $this->assign('data', $orderInfo);
        return $this->fetch();
    }

    /**
     * 订单查询
     *
     */
    public function payresult()
    {
        $recharge_order_sn = input("token");
        //验证参数
        if (empty($recharge_order_sn)) {
            $this->error('充值订单号必须');
        }
        //查询订单
        $where[]=['recharge_order_sn','=',$recharge_order_sn];
        $orderInfo = $this->model->where($where)->find();
        if (empty($orderInfo)) {
            $this->error('订单不存在',10006);
        }

        $returnData['pay_usdt']=floatval($orderInfo['actual_usdt']);
        $returnData['recharge_order_sn']=$orderInfo['recharge_order_sn'];
        $returnData['attach']=$orderInfo['remark'];
        $returnData['status']="$orderInfo[order_status]";
        $returnData['receive_address']=$orderInfo['receive_address'];
        if ($orderInfo['order_status']==1) {
            $returnData['success_time']=strtotime($orderInfo['pay_time']);
        }
        $data = [
            'code'  => 1,
            'msg'   => '',
            'data'  => $returnData,
        ];
        return json($data);
    }

    public function addrechargeorder()
    {
        //进行下单
        $appid = $this->request->post("merchant_id");
        $orderInsertData['appid'] = $appid;
        $orderInsertData['recharge_order_sn'] = $this->request->post("recharge_order_sn");
        $orderInsertData['pay_usdt'] = $this->request->post("pay_money");
        $orderInsertData['remark'] = $this->request->post("product_name");
        $orderInsertData['chain_type'] = $this->request->post("chain_type");//类型 1-trc  2-eth
        $orderInsertData['apply_ip']=CommonTool::getRealIp();
        //验证参数
        if (empty($orderInsertData['recharge_order_sn'])) {
            $this->error('充值订单号必须');
        }
        if (empty($orderInsertData['chain_type'])) {
            $this->error('地址类型必须');
        }
        if (!in_array($orderInsertData['chain_type'], [1,2])) {
            $this->error('地址类型错误');
        }
        if (empty($orderInsertData['pay_usdt'])) {
            $this->error('金额必须');
        }
        //金额精度转换
        $orderInsertData['pay_usdt'] = sprintf("%.2f",$orderInsertData['pay_usdt']);

        //单笔最低金额
        if ($orderInsertData['pay_usdt']<1.00) {
            $this->error('充值金额不能低于1.00');
        }
        // 开启事务
        $this->model->startTrans();
        //获取商户信息
        $merchantInfo = $this->merchantmodel->where("appid",$appid)->field("id as merchant_id,merchantname")->find();
        //判断充值订单号是否重复
        $orderInfo = $this->model->where("recharge_order_sn",$orderInsertData['recharge_order_sn'])->field("id")->find();
        if ($orderInfo) {
            // 事务回滚
            $this->model->rollback();
            $this->error('充值订单号重复');
        }
        // 收款地址和二维码先写死
        $orderInsertData['receive_address']=env('recharge.trc','');
        if ($orderInsertData['chain_type'] == 2) {
            $orderInsertData['receive_address']=env('recharge.erc','');
        }
        $orderInsertData['merchant_id']=$merchantInfo['merchant_id'];
        $orderInsertData['merchantname']=$merchantInfo['merchantname'];
        $orderInsertData['create_time']=time();
        $orderInsertData['update_time']=$orderInsertData['create_time'];
        $payService = new PayService();
        $result=$payService->allocationRechargeAddress($orderInsertData);
        if ($result['code']!=1) {
            // 事务回滚
            $this->model->rollback();
            $this->error($result['msg'],$result['code']);
        }
        if (!empty($result['data']['pay_usdt'])) {
            $orderInsertData['pay_usdt']=$result['data']['pay_usdt'];
        }
        $orderInsertData['address_id']=$result['data']['id'];
        $orderInsertData['img']=$result['data']['img'];

        //订单入库
        $order_id=$this->model->insertGetId($orderInsertData);
        if (!$order_id) {
            $this->model->rollback();
            $this->error('充值下单失败，订单入库失败',10003);
        }
        $this->model->commit();
        $data = [
            'code'  => 1,
            'msg'   => '',
            'data'  => "http://".$_SERVER['HTTP_HOST']."/merchant/recharge.recharge/order/id/".$orderInsertData['recharge_order_sn'],
        ];
        return json($data);
    }

    public function timeout()
    {
        $this->assign('data', "充值订单已超时");
        return $this->fetch();
    }

    public function en_timeout()
    {
        $this->assign('data', "Recharge order timed out");
        return $this->fetch();
    }

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

}