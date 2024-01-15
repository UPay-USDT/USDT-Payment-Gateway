<?php

namespace app\merchant\service;

use app\common\constants\MenuConstant;
use think\facade\Db;

class MenuService
{

    /**
     * 管理员ID
     * @var integer
     */
    protected $merchantId;

    public function __construct($merchantId)
    {
        $this->merchantId = $merchantId;
        return $this;
    }

    /**
     * 获取首页信息
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getHomeInfo()
    {
        $data = Db::name('merchant_menu')
            ->field('title,icon,href')
            ->where("delete_time is null")
            ->where('pid', MenuConstant::HOME_PID)
            ->order([
                'sort' => 'desc',
            ])
            ->find();
        !empty($data) && $data['href'] = __url($data['href']);
        return $data;
    }

    /**
     * 获取后台菜单树信息
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getMenuTree()
    {
        /** @var AuthService $authService */
        return $this->buildMenuChild(0, $this->getMenuData());
    }

    private function buildMenuChild($pid, $menuList)
    {
        $treeList = [];
        foreach ($menuList as &$v) {
            $check = true;
            !empty($v['href']) && $v['href'] = __url($v['href']);
            if ($pid == $v['pid'] && $check) {
                $node = $v;
                $child = $this->buildMenuChild($v['id'], $menuList);
                if (!empty($child)) {
                    $node['child'] = $child;
                }
                if (!empty($v['href']) || !empty($child)) {
                    $treeList[] = $node;
                }
            }
        }
        return $treeList;
    }

    /**
     * 获取所有菜单数据
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function getMenuData()
    {
        $menuData = Db::name('merchant_menu')
            ->field('id,pid,title,icon,href,target')
            ->where("delete_time is null")
            ->where([
                ['status', '=', '1'],
                ['pid', '<>', MenuConstant::HOME_PID],
            ])
            ->order([
                'sort' => 'desc',
//                'id'   => 'asc',
            ])
            ->select();
        return $menuData;
    }

}