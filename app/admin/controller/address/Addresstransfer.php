<?php

namespace app\admin\controller\address;

use app\common\controller\AdminController;
use EasyAdmin\annotation\ControllerAnnotation;
use EasyAdmin\annotation\NodeAnotation;
use think\App;

/**
 * @ControllerAnnotation(title="地址转账记录")
 */
class Addresstransfer extends AdminController
{

    //use \app\admin\traits\Curd;

    /**
     * 允许修改的字段
     * @var array
     */
    protected $allowModifyFields = [];

    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\common\model\AddressTransfer();
        $this->ordermodel = new \app\common\model\Order();

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
            $count = $this->model
                ->where($where)
                ->count();
            $list = $this->model
                ->where($where)
                ->page($page, $limit)
                ->order($this->sort)
                ->select()
                ->toArray();

            $getStatusList = $this->model->getStatusList();
            foreach ($list as $key => &$value) {
                if ($value['type'] == 1) {
                    $order_ids[] = $value['change_order_id'];
                }
                $value['old_type'] = $value['type'];
                $value['type'] = $getStatusList[$value['type']];
            }
            //去获取订单信息
            if (!empty($order_ids)) {
                $map[] = ['id', 'in', $order_ids];
                $orderlist = $this->ordermodel
                    ->where($map)
                    ->field("id,pay_money,keys_id,keys_money")
                    ->select()
                    ->toArray();
                foreach ($orderlist as $kk => $vv) {
                    if ($vv['keys_id'] == 0 && $vv['keys_money'] > 0) {
                        $pay_money[$vv['id']] = $vv['keys_money'];
                    } else {
                        $pay_money[$vv['id']] = $vv['pay_money'];
                    }

                }
            }
            foreach ($list as $k => &$v) {

                if ($v['old_type'] == 1 && isset($pay_money[$v['change_order_id']])) {
                    if (!isset($v['kk'])) {
                        $v['pay_money'] = $pay_money[$v['change_order_id']];
                    }
                }
            }
            $data = [
                'code' => 0,
                'msg' => '',
                'count' => $count,
                'data' => $list,
            ];
            return json($data);
        }
        return $this->fetch();
    }


}