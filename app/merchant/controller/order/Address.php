<?php

namespace app\merchant\controller\order;

use app\common\controller\MerchantController;
use EasyAdmin\annotation\ControllerAnnotation;
use EasyAdmin\annotation\NodeAnotation;
use think\App;
use EasyAdmin\tool\CommonTool;
use jianyan\excel\Excel;
use think\facade\Db;
use think\facade\Cache;

/**
 * @ControllerAnnotation(title="监控地址")
 */
class Address extends MerchantController
{

    use \app\merchant\traits\Curd;

    /**
     * 允许修改的字段
     * @var array
     */
    protected $allowModifyFields = [];

    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\common\model\ListionAddress();
        $this->merchantmodel = new \app\common\model\MerchantMerchant();
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

    /**
     * @NodeAnotation(title="添加")
     */
    public function add()
    {
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            $rule = [
                'address|地址' => 'require',
            ];
            $this->validate($post, $rule);
            $this->checkData($post);
            if (!is_trc_address($post['address']) && !is_erc_address($post['address'])) {
                $this->error('地址格式错误');
            }
            //获取该管理员的谷歌key
            $merchant = $this->merchantmodel->where("id",session("merchant.id"))->find();

            //判断地址是否已经存在   
            $is_addressexit = $this->model->where("address",$post['address'])->field("id")->find();
            if (!empty($is_addressexit['id'])) {
                $this->error('地址已存在');
            }
            $post['merchant_id']=$merchant['id'];
            $post['merchantname']=$merchant['merchantname'];
            $post['create_time']=time();
            $post['update_time']=time();
            try {
                $save = $this->model->allowField(['address','create_time','update_time','merchant_id','merchantname'])->save($post);
            } catch (\Exception $e) {
                $this->error('保存失败:'.$e->getMessage());
            }
            $save ? $this->success('保存成功') : $this->error('保存失败');
        }
        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="修改")
     */
    public function edit($id)
    {
        $row = $this->model->find($id);
        empty($row) && $this->error('数据不存在');
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            $this->checkData($post);
            if (!is_trc_address($post['address']) && !is_erc_address($post['address'])) {
                $this->error('地址格式错误');
            }

            try {
                $save = $this->model->where("id",$id)->save(["address"=>$post['address'],"update_time"=>time()]);
            } catch (\Exception $e) {
                $this->error('修改失败:'.$e->getMessage());
            }
            $save ? $this->success('修改成功') : $this->error('修改失败');
        }
        $this->assign('row', $row);
        return $this->fetch();
    }

}