<?php

namespace app\merchant\controller\merchant;

use app\common\controller\MerchantController;
use EasyAdmin\annotation\ControllerAnnotation;
use EasyAdmin\annotation\NodeAnotation;
use think\App;
use EasyAdmin\tool\CommonTool;
use jianyan\excel\Excel;
use think\facade\Db;
use think\facade\Cache;

/**
 * @ControllerAnnotation(title="账变")
 */
class Merchantmoneychange extends MerchantController
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

        $this->model = new \app\common\model\MerchantMoneychange();
        $this->merchantmodel = new \app\common\model\MerchantMerchant();
         
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
            list($page, $limit, $where,$excludes) = $this->buildTableParames();
            Cache::set('cwhere', $where, 120);
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
            $getStatusList=$this->model->getStatusList();
            foreach ($list as $key => &$value) {
                $value['type']=$getStatusList[$value['type']];
            }
            $data = [
                'code'  => 0,
                'msg'   => '',
                'count' => $count,
                'data'  => $list,
            ];
            return json($data);
        }
        return $this->fetch();
    }
    
}