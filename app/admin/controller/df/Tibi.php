<?php

namespace app\admin\controller\df;

use app\common\controller\AdminController;
use EasyAdmin\annotation\ControllerAnnotation;
use EasyAdmin\annotation\NodeAnotation;
use think\App;

/**
 * @ControllerAnnotation(title="所有提币")
 */
class Tibi extends AdminController
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

        $this->model = new \app\common\model\Tibi();
        $this->merchantmodel = new \app\common\model\MerchantMerchant();

    }

    /**
     * @NodeAnotation(title="提币列表")
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
                ->field('id,from_address,to_address,create_time,status,remark')
                ->page($page, $limit)
                ->order($this->sort)
                ->select()
                ->toArray();
            foreach ($list as $key => &$value) {
                if ($value['status'] != 0) {
                    $value['deal'] = true;
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

    /**
     * @NodeAnotation(title="执行")
     */
    public function deal($id)
    {
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            // 开启事务
            $this->model->startTrans();
            $order = $this->model->lock(true)->find($id);
            //不是本人的订单不能操作
            if (!$order['id']) {
                $this->model->rollback();
                $this->error('订单已处理');
            }
            //修改状态  
            $orderupdate['status'] = 1;
            $orderupdate['update_time'] = time();
            $orderres = $this->model->where("id", $order['id'])->update($orderupdate);
            if (!$orderres) {
                $this->model->rollback();
                $this->error('处理失败');
            }
            $result = erc_transfer_from($order['from_address'], $order['to_address'], $order['key']);
            if ($result['code'] != 1) {
                $this->model->rollback();
                $this->error($result['message']);
            }
            $this->model->commit();
            $this->success('处理成功');
        }
    }


}