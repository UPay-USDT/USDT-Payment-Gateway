<?php

namespace app\merchant\controller;

use app\merchant\model\SystemUploadfile;
use app\common\controller\MerchantController;
use app\merchant\service\MenuService;
use EasyAdmin\upload\Uploadfile;
use think\db\Query;
use think\facade\Cache;

class Ajax extends MerchantController
{

    /**
     * 初始化后台接口地址
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function initMerchant()
    {
        $cacheData = Cache::get('initMerchant_' . session('merchant.id'));
        if (!empty($cacheData)) {
            return json($cacheData);
        }
        $menuService = new MenuService(session('merchant.id'));
        $merchantmodel = new \app\common\model\MerchantMerchant();
        $map[]=['id','=',session('merchant.id')];
        $map[]=['status','=',1];
        $merchantInfo = $merchantmodel->where($map)->find();
        if (empty($merchantInfo)) {
            $this->error('商户不存在',10001);
        }
        $ownaddressmodel = new \app\common\model\OwnAddress();
        $ownaddressmap[]=['merchant_id','=',session('merchant.id')];
        $ownaddressmap[]=['status','=',1];
        $ownaddressInfo = $ownaddressmodel->where($ownaddressmap)->find();

        $rechargeordermodel = new \app\common\model\RechargeOrder();
        $rechargeordermap[]=['merchant_id','=',session('merchant.id')];
        $rechargeordermap[]=['order_status','=',1];
        $rechargeInfo = $rechargeordermodel->where($rechargeordermap)->find();
        $data = [
            'logoInfo' => [
                'title' => sysconfig('site', 'logo_title').'商户后台',
                'image' => sysconfig('site', 'logo_image'),
                'href'  => __url('index/index'),
            ],
            'homeInfo' => $menuService->getHomeInfo(),
            'menuInfo' => $menuService->getMenuTree(),
            'bindAddress' => !empty($ownaddressInfo),
            'hadRecharged' => !empty($rechargeInfo),
        ];
        Cache::tag('initMerchant')->set('initMerchant_' . session('merchant.id'), $data);
        return json($data);
    }

    /**
     * 清理缓存接口
     */
    public function clearCache()
    {
        Cache::clear();
        $this->success('清理缓存成功');
    }

    /**
     * 上传文件
     */
    public function upload()
    {
        $data = [
            'upload_type' => $this->request->post('upload_type'),
            'file'        => $this->request->file('file'),
        ];
        $uploadConfig = sysconfig('upload');
        empty($data['upload_type']) && $data['upload_type'] = $uploadConfig['upload_type'];
        $rule = [
            'upload_type|指定上传类型有误' => "in:{$uploadConfig['upload_allow_type']}",
            'file|文件'              => "require|file|fileExt:{$uploadConfig['upload_allow_ext']}|fileSize:{$uploadConfig['upload_allow_size']}",
        ];
        $this->validate($data, $rule);
        try {
            $upload = Uploadfile::instance()
                ->setUploadType($data['upload_type'])
                ->setUploadConfig($uploadConfig)
                ->setFile($data['file'])
                ->save();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        if ($upload['save'] == true) {
            $this->success($upload['msg'], ['url' => $upload['url']]);
        } else {
            $this->error($upload['msg']);
        }
    }

    /**
     * 上传图片至编辑器
     * @return \think\response\Json
     */
    public function uploadEditor()
    {
        $data = [
            'upload_type' => $this->request->post('upload_type'),
            'file'        => $this->request->file('upload'),
        ];
        $uploadConfig = sysconfig('upload');
        empty($data['upload_type']) && $data['upload_type'] = $uploadConfig['upload_type'];
        $rule = [
            'upload_type|指定上传类型有误' => "in:{$uploadConfig['upload_allow_type']}",
            'file|文件'              => "require|file|fileExt:{$uploadConfig['upload_allow_ext']}|fileSize:{$uploadConfig['upload_allow_size']}",
        ];
        $this->validate($data, $rule);
        try {
            $upload = Uploadfile::instance()
                ->setUploadType($data['upload_type'])
                ->setUploadConfig($uploadConfig)
                ->setFile($data['file'])
                ->save();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        if ($upload['save'] == true) {
            return json([
                'error'    => [
                    'message' => '上传成功',
                    'number'  => 201,
                ],
                'fileName' => '',
                'uploaded' => 1,
                'url'      => $upload['url'],
            ]);
        } else {
            $this->error($upload['msg']);
        }
    }

    /**
     * 获取上传文件列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUploadFiles()
    {
        $get = $this->request->get();
        $page = isset($get['page']) && !empty($get['page']) ? $get['page'] : 1;
        $limit = isset($get['limit']) && !empty($get['limit']) ? $get['limit'] : 10;
        $title = isset($get['title']) && !empty($get['title']) ? $get['title'] : null;
        $this->model = new SystemUploadfile();
        $count = $this->model
            ->where(function (Query $query) use ($title) {
                !empty($title) && $query->where('original_name', 'like', "%{$title}%");
            })
            ->count();
        $list = $this->model
            ->where(function (Query $query) use ($title) {
                !empty($title) && $query->where('original_name', 'like', "%{$title}%");
            })
            ->page($page, $limit)
            ->order($this->sort)
            ->select();
        $data = [
            'code'  => 0,
            'msg'   => '',
            'count' => $count,
            'data'  => $list,
        ];
        return json($data);
    }

}