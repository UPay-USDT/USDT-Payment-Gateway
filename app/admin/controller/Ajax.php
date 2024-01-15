<?php

namespace app\admin\controller;

use app\common\controller\AdminController;
use app\common\service\MenuService;
use EasyAdmin\upload\Uploadfile;
use think\db\Query;
use think\facade\Cache;

class Ajax extends AdminController
{

    /**
     * 初始化后台接口地址
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function initAdmin()
    {
        $cacheData = Cache::get('initAdmin_' . session('admin.id'));
        if (!empty($cacheData)) {
            return json($cacheData);
        }
        $menuService = new MenuService(session('admin.id'));
        $data = [
            'logoInfo' => [
                'title' => sysconfig('site', 'logo_title'),
                'image' => sysconfig('site', 'logo_image'),
                'href' => __url('index/index'),
            ],
            'homeInfo' => $menuService->getHomeInfo(),
            'menuInfo' => $menuService->getMenuTree(),
        ];
        // $this->error($data['menuInfo']);
        Cache::tag('initAdmin')->set('initAdmin_' . session('admin.id'), $data);
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
            'file' => $this->request->file('file'),
        ];
        $uploadConfig = sysconfig('upload');
        empty($data['upload_type']) && $data['upload_type'] = $uploadConfig['upload_type'];
        $rule = [
            'upload_type|指定上传类型有误' => "in:{$uploadConfig['upload_allow_type']}",
            'file|文件' => "require|file|fileExt:{$uploadConfig['upload_allow_ext']}|fileSize:{$uploadConfig['upload_allow_size']}",
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
            'file' => $this->request->file('upload'),
        ];
        $uploadConfig = sysconfig('upload');
        empty($data['upload_type']) && $data['upload_type'] = $uploadConfig['upload_type'];
        $rule = [
            'upload_type|指定上传类型有误' => "in:{$uploadConfig['upload_allow_type']}",
            'file|文件' => "require|file|fileExt:{$uploadConfig['upload_allow_ext']}|fileSize:{$uploadConfig['upload_allow_size']}",
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
                'error' => [
                    'message' => '上传成功',
                    'number' => 201,
                ],
                'fileName' => '',
                'uploaded' => 1,
                'url' => $upload['url'],
            ]);
        } else {
            $this->error($upload['msg']);
        }
    }
}