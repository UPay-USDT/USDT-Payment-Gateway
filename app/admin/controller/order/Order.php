<?php

namespace app\admin\controller\order;

use app\common\controller\AdminController;
use EasyAdmin\annotation\ControllerAnnotation;
use EasyAdmin\annotation\NodeAnotation;
use think\App;

/**
 * @ControllerAnnotation(title="所有订单")
 */
class Order extends AdminController
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

        $this->model = new \app\common\model\Order();
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
            $money_model = sysconfig('riskconfig', 'money_model');
            foreach ($list as $key => &$value) {
                if ($money_model == 3) {
                    $value['pay_money'] = sprintf("%.2f", $value['pay_usdt'] * $value['usdt_inr']);
                } elseif ($money_model == 4) {
                    $value['pay_money'] = sprintf("%.2f", $value['pay_usdt'] * $value['usdt_jpy']);
                } elseif ($money_model == 5) {
                    $value['pay_money'] = sprintf("%.2f", $value['pay_usdt'] * $value['usdt_krw']);
                } else {
                    $value['pay_money'] = floatval($value['pay_money']);
                }

                $value['poundage_money'] = floatval($value['poundage_money']);
                $value['poundage_usdt'] = floatval($value['poundage_usdt']);
                $value['actual_usdt'] = floatval($value['actual_usdt']);
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