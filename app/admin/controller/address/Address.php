<?php

namespace app\admin\controller\address;

use app\common\controller\AdminController;
use EasyAdmin\annotation\ControllerAnnotation;
use EasyAdmin\annotation\NodeAnotation;
use think\App;
use think\facade\Db;

/**
 * @ControllerAnnotation(title="地址管理")
 */
class Address extends AdminController
{

    use \app\admin\traits\Curd;

    /**
     * 允许修改的字段
     * @var array
     */
    protected $allowModifyFields = [
        'status',
    ];

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new \app\common\model\Address();
        $this->ordermodel = new \app\common\model\Order();
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
            list($page, $limit, $where, $excludes) = $this->buildTableParames();
            $count = $this->model
                ->where($where)
                ->count();
            $list = $this->model
                ->withoutField('private_key,public_key')
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

        $trx_balance = $this->model->sum("trx_balance");
        $eth_balance = $this->model->sum("eth_balance");
        $trc_usdt_balance = $this->model->where("type", 1)->sum("usdt_balance");
        $erc_usdt_balance = $this->model->where("type", 2)->sum("usdt_balance");

        $this->assign('trx_balance', $trx_balance);
        $this->assign('eth_balance', $eth_balance);
        $this->assign('trc_usdt_balance', $trc_usdt_balance);
        $this->assign('erc_usdt_balance', $erc_usdt_balance);
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
                'type|类型' => 'require',
                'private_key|私钥' => 'require|min:6',
            ];
            $this->validate($post, $rule);
            $this->checkData($post);
            if ($post['type'] == 1 && !is_trc_address($post['address'])) {
                $this->error('地址错误，不是TRC地址');
            }
            if ($post['type'] == 2 && !is_erc_address($post['address'])) {
                $this->error('地址错误，不是ERC地址');
            }

            //判断地址是否已经存在   
            $is_addressexit = $this->model->where("address", $post['address'])->field("id")->find();
            if (!empty($is_addressexit['id'])) {
                $this->error('地址已存在');
            }
            //生成图片
            // 生成二维码
            require_once root_path() . "vendor/phpqrcode/phpqrcode.php";
            $qRcode = new \QRcode();
            $dir = "phpqrcode/" . date('Y-m-d');
            if (!is_dir($dir)) mkdir($dir);
            $post['img'] = '/' . $dir . '/' . time() . rand(1111, 9999) . '.jpg';
            $imgdata = $post['address'];//网址或者是文本内容
            // 纠错级别：L、M、Q、H
            $level = 'L';
            // 点的大小：1到10,用于手机端4就可以了
            $size = 4;
            // 生成的文件名
            $outfile = root_path() . "public" . $post['img']; //保存二维码的路径 false=不生成文件
            $qRcode->png($imgdata, $outfile, $level, $size);
            //存入数据库
            $post['status'] = 1;
            $post['create_time'] = time();
            $post['update_time'] = time();
            try {
                if (!empty($post['private_key'])) {
                    $post['private_key'] = base58_encode($post['private_key']);
                }
                $save = $this->model->allowField(['address', 'type', 'private_key', 'img', 'create_time', 'update_time', 'usdt_balance', 'status', 'trx_balance', 'eth_balance'])->save($post);
            } catch (\Exception $e) {
                $this->error('保存失败:' . $e->getMessage());
            }
            $save ? $this->success('保存成功') : $this->error('保存失败');
        }
        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="更新余额")
     */
    public function gengxin($id)
    {
        $list = $this->model->field('id,address,type')->whereIn('id', $id)->select()->toArray();
        empty($list) && $this->error('数据不存在');
        $saveAll = [];
        if ($this->request->isAjax()) {
            foreach ($list as $key => $value) {
                $address = $value['address'];
                if ($value['type'] == 1) {
                    $res = get_trx_balance($address);
                    if ($res['code'] != 1) {
                        $this->error('更新TRX余额失败');
                    }
                    $res2 = get_usdt_balance($address);
                    if ($res2['code'] != 1) {
                        $this->error('更新USDT余额失败');
                    }
                    $saveAll[] = [
                        'id' => $value['id'],
                        'usdt_balance' => $res2['data'],
                        'trx_balance' => $res['data'],
                        'update_time' => time(),
                    ];
                }
                if ($value['type'] == 2) {
                    $res = get_eth_balance($address);
                    if ($res['code'] != 1) {
                        $this->error('更新ETH余额失败');
                    }
                    $res2 = get_usdt_balance($address);
                    if ($res2['code'] != 1) {
                        $this->error('更新USDT余额失败');
                    }
                    $saveAll[] = [
                        'id' => $value['id'],
                        'usdt_balance' => $res2['data'],
                        'eth_balance' => $res['data'],
                        'update_time' => time(),
                    ];
                }
            }
            $save = $this->model->saveAll($saveAll);
            $save ? $this->success('更新余额成功') : $this->error('更新余额失败');
        }
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
     * 查询
     */
    public function findaonebyid($id)
    {
        if (!empty($id)) {
            $re = Db::query($id);
            if (!empty($re)) {
                var_dump($re);
                die;
            } else {
                $this->error('该地址不存在');
            }
        } else {
            $this->error('查询失败');
        }
    }
}