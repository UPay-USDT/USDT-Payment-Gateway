<?php

namespace app\admin\controller\merchant;

use app\common\controller\AdminController;
use EasyAdmin\annotation\ControllerAnnotation;
use EasyAdmin\annotation\NodeAnotation;
use think\App;

/**
 * @ControllerAnnotation(title="商户管理")
 */
class Merchant extends AdminController
{

    use \app\admin\traits\Curd;

    /**
     * 允许修改的字段
     * @var array
     */
    protected $allowModifyFields = [
        'status',
        'is_xiadan',
        'is_api_df',
        'is_sd_df',
    ];

    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\common\model\MerchantMerchant();
        $this->adminmodel = new \app\admin\model\SystemAdmin();
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
            list($page, $limit, $where, $excludes) = $this->buildTableParames(['username']);

            $count = $this->model
                ->where($where)
                ->count();
            $list = $this->model
                ->withoutField('password')
                ->where($where)
                ->page($page, $limit)
                ->order($this->sort)
                ->select()
                ->toArray();

            $data = [
                'code' => 0,
                'msg' => '',
                'count' => $count,
                'data' => $list,
            ];
            return json($data);
        }
        $usdt_balance = $this->model->sum("usdt_balance");
        $this->assign('usdt_balance', $usdt_balance);
        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="编辑")
     */
    public function edit($id)
    {
        $row = $this->model->withoutField('password')->find($id);
        empty($row) && $this->error('数据不存在');
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            $rule = [];
            $this->validate($post, $rule);

            if (!empty($post['password']) && mb_strlen($post['password']) < 6) {
                $this->error('密码长度不能小于6位');
            }
            $this->checkData($post);

            try {
                if (!empty($post['password'])) {
                    $post['password'] = password($post['password']);
                } else {
                    unset($post['password']);
                }
                $save = $row->allowField(['password', 'merchant_rate'])->save($post);
            } catch (\Exception $e) {
                $this->error('保存失败');
            }
            $save ? $this->success('保存成功') : $this->error('保存失败');
        }
        $this->assign('row', $row);
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
                'merchantname|用户名' => 'require',
                'merchant_rate|费率' => 'require',
                'password|密码' => 'require|min:6',
            ];
            $this->validate($post, $rule);
            $this->checkData($post);

            //判断用户名是否已经存在   
            $is_merchantnameexit = $this->model->where("merchantname", $post['merchantname'])->field("id")->find();
            if (!empty($is_merchantnameexit) && $is_merchantnameexit['id']) {
                $this->error('用户名已存在');
            }
            //判断appid是否已经存在  存在则稍后再试
            $appid = GetNumberStr(8);
            $appsecret = GetNumberStr(16);
            $is_exit = $this->model->where("appid", $appid)->field("id,merchant_rate")->find();
            if (!empty($is_exit) && $is_exit['id']) {
                $this->error('请3秒后再试');
            }
            try {
                $post['password'] = password($post['password']);
                $post['appid'] = $appid;
                $post['appsecret'] = $appsecret;

                $save = $this->model->allowField(['merchantname', 'password', 'merchant_rate', 'appid', 'appsecret'])->save($post);
            } catch (\Exception $e) {
                $this->error('保存失败:' . $e->getMessage());
            }
            $save ? $this->success('保存成功') : $this->error('保存失败');
        }
        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="属性修改")
     */
    public function modify()
    {
        $post = $this->request->post();
        $rule = [
            'id|ID' => 'require',
            'field|字段' => 'require',
            'value|值' => 'require',
        ];
        $this->validate($post, $rule);
        $row = $this->model->find($post['id']);
        if (!$row) {
            $this->error('数据不存在');
        }
        if (!in_array($post['field'], $this->allowModifyFields)) {
            $this->error('该字段不允许修改：' . $post['field']);
        }

        try {
            $row->save([
                $post['field'] => $post['value'],
            ]);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->success('保存成功');
    }

    /**
     * @NodeAnotation(title="修改金额")
     */
    public function editmoney($id)
    {
        $row = $this->model->withoutField('password')->find($id);
        empty($row) && $this->error('数据不存在');
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            $this->checkData($post);
        }
        if ($this->request->isAjax()) {
            $merchantmoneychangemodel = new \app\common\model\MerchantMoneychange();
            // 开启事务
            $this->model->startTrans();
            //变动的金额
            if ($post['modify_type'] == 1) {//加上
                $money = $post['money'];
            } else {//减去
                $money = -$post['money'];
            }
            $res = $merchantmoneychangemodel->recordMoneyChange($id, $money, 4, 1, 0, '', $post['remark'], session("admin.id"), session("admin.username"));
            if (!$res) {
                $this->model->rollback();
                $this->error('修改失败');
            }
            $this->model->commit();
            $this->success('保存成功');
        }
        $this->assign('row', $row);
        return $this->fetch();
    }
}