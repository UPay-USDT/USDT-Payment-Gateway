<?php

namespace app\merchant\controller\order;

use app\common\controller\MerchantController;
use EasyAdmin\annotation\ControllerAnnotation;
use EasyAdmin\annotation\NodeAnotation;
use think\App;
use EasyAdmin\tool\CommonTool;
use think\facade\Db;
use think\facade\Cache;
use app\common\service\PayService;

/**
 * @ControllerAnnotation(title="订单管理")
 */
class Order extends MerchantController
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

        $this->model = new \app\common\model\Order();
        $this->merchantmodel = new \app\common\model\MerchantMerchant();
        $this->addressmodel = new \app\common\model\Address();
        $this->ownaddressmodel = new \app\common\model\OwnAddress();
        $this->addresstransfermodel = new \app\common\model\AddressTransfer();

        $this->assign('getStatusList', $this->model->getStatusList());

    }

    /**
     * @NodeAnotation(title="列表")
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            if (input('selectFields')) {
                return $this->selectList();
            }
            list($page, $limit, $where, $excludes) = $this->buildTableParames();
            Cache::set('orderwhere', $where, 120);
            $where[] = ['merchant_id', '=', session("merchant.id")];
            $count = $this->model
                ->where($where)
                ->count();
            $list = $this->model
                ->where($where)
                ->page($page, $limit)
                ->order($this->sort)
                ->select()
                ->toArray();

            $orderIds = array_column($list, 'id');
            $addresstransfermodel = new \app\common\model\AddressTransfer();
            $addresstransfers = $addresstransfermodel->where('change_order_id', 'in', $orderIds)->select()->toArray();

            $addresses = array_column($addresstransfers, 'from_address');

            $retList = $list;
            $msg = "resp";
            $data = [
                'code' => 0,
                'msg' => $msg,
                'count' => $count,
                'data' => $retList,
                'risk_score' => json_encode([]),
            ];
            return json($data);
        }
        return $this->fetch();
    }

    public function budan()
    {
        if ($this->request->isAjax()) {
            $merchantmoneychangemodel = new \app\common\model\MerchantMoneychange();
            $post = $this->request->post();
            $rule = [
                'txid|交易哈希' => 'require',
                'merchant_order_sn|商户订单号' => 'require',
                'chain_type|链路类型' => 'require',
                'onecode|谷歌验证码' => 'require',
            ];
            $this->validate($post, $rule);
            $this->checkData($post);
            $merchant = $this->merchantmodel->where("id", session("merchant.id"))->find();
            // 开启事务
            $this->model->startTrans();
            //判断哈希是否存在于系统中
            $where = [];
            $where[] = ['transaction_id', '=', $post['txid']];
            $address = $this->addresstransfermodel->where($where)->find();
            if (!empty($address)) {
                $this->model->rollback();
                $this->error('交易哈希已存在');
            }
            //去查找交易是否成功
            $transfer_result = find_transfer($post['txid'], $post['chain_type']);
            if ($transfer_result['code'] == 1) {

                //判断地址是否存在在系统中  收款不存在则是非法地址
                $where = [];
                $where[] = ['address', '=', $transfer_result['data']['to_address']];
                $address = $this->ownaddressmodel->where($where)->find();
                if (empty($address)) {
                    $this->model->rollback();
                    $this->error('地址不存在');
                }

                //付款地址是系统的也非法
                $where = [];
                $where[] = ['address', '=', $transfer_result['data']['from_address']];
                $address = $this->ownaddressmodel->where($where)->find();
                if (!empty($address)) {
                    $this->model->rollback();
                    $this->error('地址错误');
                }

                //记录地址交易 和  地址账变
                //去查询这笔交易是否已经存入转账记录表  如果没有则存入
                $recordData = [];
                $recordData = $transfer_result['data'];
                $recordData['money'] = $transfer_result['data']['amount'];
                $recordData['transaction_id'] = $post['txid'];
                $recordData['change_order_sn'] = $post['txid'];
                $is_exits = $this->addresstransfermodel->recordTransfer($recordData, '5');
                if (!$is_exits) {
                    $this->model->rollback();
                    $this->error('记录地址交易失败');
                }
                $where = [];
                $where[] = ["merchant_id", "=", session("merchant.id")];
                $where[] = ["merchant_order_sn", "=", $post['merchant_order_sn']];
                $orderInfo = $this->model->where($where)->find();
                if (empty($orderInfo)) {
                    $this->model->rollback();
                    $this->error('订单不存在: ' . $post['merchant_order_sn']);
                }

                //给商户增加余额  记录商户账变
                $merchantres = $merchantmoneychangemodel->recordMoneyChange(session("merchant.id"), $recordData['money'], 9, $post['chain_type'], $orderInfo['id'], $post['txid']);
                if (!$merchantres) {
                    $this->model->rollback();
                    $this->error('增加商户余额失败');
                }
                //存入补单列表
                $data = [];
                $data['merchant_id'] = session("merchant.id");
                $data['merchantname'] = $merchant['merchantname'];
                $data['transaction_id'] = $post['txid'];
                $data['from_address'] = $transfer_result['data']['from_address'];
                $data['to_address'] = $transfer_result['data']['to_address'];
                $data['money'] = $transfer_result['data']['amount'];
                $data['transfer_time'] = $transfer_result['data']['time'];
                $data['chain_type'] = $post['chain_type'];
                $res = Db::table('ea_budan')->insert($data);
                if (!$res) {
                    $this->model->rollback();
                    $this->error('存入补单列表失败');
                }
                //TODO：根据商户订单号修改订单状态等相关字段
                $orderupdate['order_status'] = 1;
                $orderupdate['actual_usdt'] = $transfer_result['data']['amount'];
                $orderupdate['pay_time'] = $transfer_result['data']['time'];

                $orderRecord = $this->model->where($where)->update($orderupdate);
                if (!$orderRecord) {
                    $this->model->rollback();
                    $this->error('修改订单状态失败');
                }

                $this->model->commit();
                $this->success('补单成功');
            }
            $this->model->rollback();
            $this->error('交易失败或不存在');
        }
        return $this->fetch();
    }
}