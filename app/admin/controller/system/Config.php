<?php

namespace app\admin\controller\system;


use app\admin\model\SystemConfig;
use app\admin\service\TriggerService;
use app\common\controller\AdminController;
use EasyAdmin\annotation\ControllerAnnotation;
use EasyAdmin\annotation\NodeAnotation;
use think\App;

/**
 * Class Config
 * @package app\admin\controller\system
 * @ControllerAnnotation(title="系统配置管理")
 */
class Config extends AdminController
{

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new SystemConfig();
        $this->adminmodel = new \app\admin\model\SystemAdmin();
    }

    /**
     * @NodeAnotation(title="保存")
     */
    public function save()
    {
        $post = $this->request->post();
        if (isset($post['single_max_money']) && $post['single_max_money'] <= 0) {
            $this->error('风控设置的参数不能小于0');
        }
        if (isset($post['pay_min_money']) && $post['pay_min_money'] <= 0) {
            $this->error('风控设置的参数不能小于0');
        }
        if (isset($post['pay_max_money']) && $post['pay_max_money'] <= 0) {
            $this->error('风控设置的参数不能小于0');
        }
        if (isset($post['df_min_money']) && $post['df_min_money'] <= 0) {
            $this->error('风控设置的参数不能小于0');
        }
        if (isset($post['df_max_money']) && $post['df_max_money'] <= 0) {
            $this->error('风控设置的参数不能小于0');
        }
        if (isset($post['day_max_money']) && $post['day_max_money'] <= 0) {
            $this->error('风控设置的参数不能小于0');
        }
        if (isset($post['time_out']) && $post['time_out'] <= 0) {
            $this->error('风控设置的参数不能小于0');
        }

        foreach ($post as $key => $val) {
            $this->model
                ->where('name', $key)
                ->update([
                    'value' => $val,
                ]);
        }
        TriggerService::updateMenu();
        TriggerService::updateSysconfig();
        $this->model->commit();
        $this->success('保存成功');
    }

}