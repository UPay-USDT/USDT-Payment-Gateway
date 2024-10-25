<?php

namespace app\merchant\controller;



use app\common\controller\MerchantController;
use think\App;
use think\facade\Env;
use app\common\model\MerchantMerchant;
use app\common\model\Order;

class Index extends MerchantController
{

    /**
     * 商户主页
     * @return string
     * @throws \Exception
     */
    public function index()
    {
        $this->assign('liaotian', 1);

        return $this->fetch('', [
            'merchant' => session('merchant'),
        ]);
    }
     public function contact()
    {
        $this->assign('liaotian', 1);
        return $this->fetch('', [
            'merchant' => session('merchant'),
        ]);
    }

    /**
     * 后台欢迎页
     * @return string
     * @throws \Exception
     */
    public function welcome()
    {
        $id = session('merchant.id');
        $row = MerchantMerchant::withoutField('password')->find($id);
        $row['merchant_rate']=($row['merchant_rate']*100)."%";
        //统计成功订单
        $map[] = ['merchant_id', '=', $id];
        $map[] = ['order_status', '=', 1];
        $sum['total_money'] = Order::where($map)->sum("actual_usdt");
        $sum['total_number'] = Order::where($map)->count("id");
        $map[] = ['pay_time', 'between', [strtotime(date('Y-m-d')),strtotime(date('Y-m-d',strtotime('+1 day')))]];
        $sum['today_pay_usdt_sum'] = Order::where($map)->sum("actual_usdt");
        $sum['today_number'] = Order::where($map)->count();
        $this->assign('row', $row);
        $this->assign('sum', $sum);
        //获取前十天每天的订单金额
        for ($i=-20; $i < 1; $i++) {
            $j=$i+1;
            $start_time=strtotime(date('Y-m-d',strtotime("$i day")));
            $end_time=strtotime(date('Y-m-d',strtotime("$j day")));
            $chart_time[]=date('d',strtotime("$i day"))+0;
            $map=[];
            $map[]=['order_status','=',1];
            $map[] = ['merchant_id', '=', $id];
            $map[] = ['pay_time', 'between', [$start_time,$end_time]];
            $chart_order[] = Order::where($map)->sum("actual_usdt");
        }
        $this->assign('chart_time', json_encode($chart_time) );
        $this->assign('chart_order', json_encode($chart_order) );
        $this->assign('url', $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']."/payment?id=".$row['appid'] );

        $merchantmodel = new \app\common\model\MerchantMerchant();
        $merchantmap[]=['id','=',session('merchant.id')];
        $merchantmap[]=['status','=',1];
        $merchantInfo = $merchantmodel->where($merchantmap)->find();
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

        $this->assign('bindAddress',!empty($ownaddressInfo)?1:0);
        $this->assign('hadRecharged',!empty($rechargeInfo)?1:0);
        $this->assign('appid', sprintf("%s",$merchantInfo['appid']));

        return $this->fetch();
    }

    /**
     * 修改商户信息
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function editAdmin()
    {
        $id = session('merchant.id');
        $row = MerchantMerchant::withoutField('password')->find($id);
        empty($row) && $this->error('用户信息不存在');
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            $this->isDemo && $this->error('演示环境下不允许修改');
            $rule = [];
            $this->validate($post, $rule);

            try {
                $save = $row
                    ->allowField(['mobile', 'qq','update_time','autorefresh'])
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
     * 提现设置
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function config()
    {
        $id = session('merchant.id');
        $row = MerchantMerchant::withoutField('password')->find($id);
        empty($row) && $this->error('用户信息不存在');
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            $rule = [];
            $this->validate($post, $rule);
            if (isset($post['is_auto_tixian']) && $post['is_auto_tixian']==1) {
                if (empty($post['trc_address'])) {
                    $this->error('请填写自动提现地址');
                }
                if (!is_trc_address($post['trc_address'])) {
                    $this->error('收款地址不是TRC20地址');
                }
            }else{
                $post['is_auto_tixian']=0;
            }
            //单笔最低金额
            $df_min_money=sysconfig('riskconfig','df_min_money');
            $df_max_money=sysconfig('riskconfig','df_max_money');
            if ($post['tixian_usdt']<$df_min_money) {
                $this->error('提现金额不能低于'.$df_min_money);
            }
            if ($post['tixian_usdt']>$df_max_money) {
                $this->error('提现金额不能超过'.$df_max_money);
            }
            try {
                $save = $row
                    ->allowField(['mobile', 'qq','update_time','is_auto_tixian','trc_address','tixian_usdt'])
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
        $id = session('merchant.id');
        $row = MerchantMerchant::withoutField('password')->find($id);
        if (!$row) {
            $this->error('用户信息不存在');
        }
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            $this->isDemo && $this->error('演示环境下不允许修改');
            $rule = [
                'password|登录密码'       => 'require',
                'password_again|确认密码' => 'require',
            ];
            $this->validate($post, $rule);
            if ($post['password'] != $post['password_again']) {
                $this->error('两次密码输入不一致');
            }

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
