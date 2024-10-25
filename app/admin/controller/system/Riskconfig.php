<?php

namespace app\admin\controller\system;

use app\common\controller\AdminController;
use EasyAdmin\annotation\ControllerAnnotation;
use EasyAdmin\annotation\NodeAnotation;
use think\App;
use think\facade\Db;

/**
 * @ControllerAnnotation(title="风控设置")
 */
class Riskconfig extends AdminController
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

        $this->ordermodel = new \app\common\model\Order();
        $this->merchantmodel = new \app\common\model\MerchantMerchant();

    }

    /**
     * @NodeAnotation(title="风控设置")
     */
    public function index()
    {
        return $this->fetch();
    }

}