<?php

namespace app\admin\controller;


use app\admin\model\SystemAdmin;
use app\common\controller\AdminController;
use think\App;
use think\facade\Env;
use app\common\model\Order;


class Index extends AdminController
{

    /**
     * 后台主页
     * @return string
     * @throws \Exception
     */
    public function index()
    {
        return $this->fetch('', [
            'admin' => session('admin'),
        ]);
    }

    /**
     * 后台欢迎页
     * @return string
     * @throws \Exception
     */
    public function welcome()
    {
        $yesterday = strtotime(date('Y-m-d', strtotime('-1 day')));
        $today = strtotime(date('Y-m-d'));
        $tomorryday = strtotime(date('Y-m-d', strtotime('+1 day')));
        //总交易金额
        $where = [];
        $where[] = ['order_status', '=', 1];
        $sum['pay_usdt'] = Order::where($where)->sum("actual_usdt");
        //今日交易金额
        $where[] = ['pay_time', 'between', [$today, $tomorryday]];
        $sum['today_pay_usdt'] = Order::where($where)->sum("actual_usdt");
        //昨日交易金额
        $where = [];
        $where[] = ['order_status', '=', 1];
        $where[] = ['pay_time', 'between', [$yesterday, $today]];
        $sum['yesterday_pay_usdt'] = Order::where($where)->sum("actual_usdt");

        $where = [];
        //总商户手续费
        $where[] = ['order_status', '=', 1];
        $sum['poundage_usdt'] = Order::where($where)->sum("poundage_usdt");
        //今日商户手续费
        $where[] = ['pay_time', 'between', [$today, $tomorryday]];
        $sum['today_poundage_usdt'] = Order::where($where)->sum("poundage_usdt");
        //昨日商户手续费
        $where = [];
        $where[] = ['order_status', '=', 1];
        $where[] = ['pay_time', 'between', [$yesterday, $today]];
        $sum['yesterday_poundage_usdt'] = Order::where($where)->sum("poundage_usdt");

        //平台总收入  商户手续费
        $sum['plat_money'] = sprintf("%.2f", ($sum['poundage_usdt']));
        //今日平台收入
        $sum['today_plat_money'] = sprintf("%.2f", ($sum['today_poundage_usdt']));
        //昨日平台收入
        $sum['yesterday_plat_money'] = sprintf("%.2f", ($sum['yesterday_poundage_usdt']));
        $this->assign('sum', $sum);
        //获取前十天每天的订单金额
        for ($i = -20; $i < 1; $i++) {
            $j = $i + 1;
            $start_time = strtotime(date('Y-m-d', strtotime("$i day")));
            $end_time = strtotime(date('Y-m-d', strtotime("$j day")));
            $chart_time[] = date('d', strtotime("$i day")) + 0;
            $map = [];
            $map[] = ['order_status', '=', 1];
            $map[] = ['pay_time', 'between', [$start_time, $end_time]];
            $chart_order[] = Order::where($map)->sum("actual_usdt");
        }
        $this->assign('chart_time', json_encode($chart_time));
        $this->assign('chart_order', json_encode($chart_order));
        return $this->fetch();
    }

    /**
     * 修改管理员信息
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function editAdmin()
    {
        $id = session('admin.id');
        $row = (new SystemAdmin())
            ->withoutField('password')
            ->find($id);
        empty($row) && $this->error('用户信息不存在');
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            $this->isDemo && $this->error('演示环境下不允许修改');
            $rule = [];
            $this->validate($post, $rule);
            try {
                $save = $row
                    ->allowField(['head_img', 'phone', 'remark', 'update_time'])
                    ->save($post);
            } catch (\Exception $e) {
                $this->error('保存失败');
            }
            $save ? $this->success('保存成功') : $this->error('保存失败');
        }
        $this->assign('row', $row);
        return $this->fetch();
    }

    /**
     * 修改密码
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function editPassword()
    {
        $id = session('admin.id');
        $row = (new SystemAdmin())
            ->withoutField('password')
            ->find($id);
        if (!$row) {
            $this->error('用户信息不存在');
        }
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            $this->isDemo && $this->error('演示环境下不允许修改');
            $rule = [
                'password|登录密码' => 'require',
                'password_again|确认密码' => 'require',
            ];
            $this->validate($post, $rule);
            if ($post['password'] != $post['password_again']) {
                $this->error('两次密码输入不一致');
            }

            // 判断是否为演示站点
            $example = Env::get('easyadmin.example', 0);
            $example == 1 && $this->error('演示站点不允许修改密码');

            try {
                $save = $row->save([
                    'password' => password($post['password']),
                ]);
            } catch (\Exception $e) {
                $this->error('保存失败');
            }
            if ($save) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
        $this->assign('row', $row);
        return $this->fetch();
    }
}
