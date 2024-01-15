<?php

namespace app\merchant\controller\merchant;

use app\common\controller\MerchantController;
use EasyAdmin\annotation\ControllerAnnotation;
use EasyAdmin\annotation\NodeAnotation;
use think\App;
use EasyAdmin\tool\CommonTool;
use think\facade\Db;

/**
 * @ControllerAnnotation(title="财务分析")
 */
class Financial extends MerchantController
{
    /**
     * 允许修改的字段
     * @var array
     */
    protected $allowModifyFields = [];

    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\common\model\Order();
        $this->merchantmodel = new \app\common\model\MerchantMerchant();
        $this->withdrawmodel = new \app\common\model\Withdraw();
        $this->merchantmoneychangemodel = new \app\common\model\MerchantMoneychange();
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


            //如果没有搜索时间 则默认展示最近10天的数据
            if (empty($where)) {
                $start_time=strtotime(date('Y-m-d',strtotime('-28 day')));
                $end_time=strtotime('24:0:0',time());
            }else{
                $start_time=$where['0']['2'];
                $end_time=strtotime('24:0:0',$where['1']['2']);
                //如果时间间隔超过了30天
                if ($end_time>($start_time+30*86400)) {
                    $end_time=$start_time+30*86400;
                }
                if ($end_time>time()) {
                    $end_time=strtotime('24:0:0',time());
                }
            }
            $where=[];
            $where[] = ['pay_time', '>=', $start_time];
            $where[] = ['pay_time', '<=', $end_time];
            $where[] = ['merchant_id', '=', session('merchant.id')];
            $where[] = ['order_status', '=', 1];
            //获取我的某个时间段的所有成功订单
            $order_list = $this->model
                ->where($where)
                ->order("id asc")
                ->field("id,pay_usdt,pay_time")
                ->select()
                ->toArray();
            // var_dump($where);
            // var_dump($order_list);
            // var_dump(date('Y-m-d',$start_time));
            // var_dump(date('Y-m-d',$end_time));
            foreach ($order_list as $key => $value) {
                $temp_start_time=strtotime('0:0:0',strtotime($value['pay_time']));
                //今日收款数量
                if (isset($todaynumber[$temp_start_time])) {
                    $todaynumber[$temp_start_time]=$todaynumber[$temp_start_time]+1;
                }else{
                    $todaynumber[$temp_start_time]=1;
                }
                //今日收款
                if (isset($todaymoney[$temp_start_time])) {
                    $todaymoney[$temp_start_time]=$todaymoney[$temp_start_time]+$value['pay_usdt'];
                }else{
                    $todaymoney[$temp_start_time]=$value['pay_usdt'];
                }
            }
            //获取我的某个时间段的提款
            $where=[];
            $where[] = ['give_time', '>=', $start_time];
            $where[] = ['give_time', '<=', $end_time];
            $where[] = ['merchant_id', '=', session('merchant.id')];
            $where[] = ['status', '=', 3];
            $withdraw_list = $this->withdrawmodel
                ->where($where)
                ->order("id asc")
                ->field("id,money,give_time")
                ->select()
                ->toArray();
            foreach ($withdraw_list as $key => $value) {
                $temp_start_time=strtotime('0:0:0',strtotime($value['give_time']));
                //今日提款
                if (isset($todayout[$temp_start_time])) {
                    $todayout[$temp_start_time]=$todayout[$temp_start_time]+$value['money'];
                }else{
                    $todayout[$temp_start_time]=$value['money'];
                }
            }
            $returndata=[];
            while ($start_time< $end_time) {
                $returndata[$start_time]['pay_time']=date('Y-m-d',$start_time);
                if (isset($todaynumber[$start_time])) {
                    $returndata[$start_time]['todaynumber']=$todaynumber[$start_time];
                }else{
                    $returndata[$start_time]['todaynumber']=0;
                }
                if (isset($todaymoney[$start_time])) {
                    $returndata[$start_time]['todaymoney']=sprintf("%.2f",$todaymoney[$start_time]);
                }else{
                    $returndata[$start_time]['todaymoney']=0;
                }
                if (isset($todayout[$start_time])) {
                    $returndata[$start_time]['todayout']=sprintf("%.2f",$todayout[$start_time]);
                }else{
                    $returndata[$start_time]['todayout']=0;
                }
                $start_time=$start_time+86400;
            }
            $returndata=array_reverse($returndata);
            $data = [
                'code'  => 0,
                'msg'   => '',
                'count' => 0,
                'data'  => $returndata,
            ];
            return json($data);
        }
        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="申请提款")
     */
    public function applywithdraw()
    {
        $row = $this->merchantmodel->find(session("merchant.id"));
        empty($row) && $this->error('数据不存在');
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            if ($row['is_sd_df']!=1) {
                $this->error('手动提现权限关闭');
            }
            if (empty($post['address'])) {
                $this->error('请先填写收款地址');
            }
            if ($post['money']<=0 || !in_array($post['chain_type'], [1,2])) {
                $this->error('请输入合法金额');
            }
            if ($post['chain_type']==1 && !is_trc_address($post['address'])) {
                $this->error('收款地址不是TRC地址');
            }
            if ($post['chain_type']==2 && !is_erc_address($post['address'])) {
                $this->error('收款地址不是ETH地址');
            }
            //所有不可用的地址
            $addressmodel = new \app\common\model\Address();
            $addresss = $addressmodel->getErrorAddress();
            if (in_array($post['address'], $addresss)) {
                $this->error('该提现地址目前有订单处理中，请稍后再申请');
            }
            //金额转换
            $post['money'] = sprintf("%.2f",$post['money']);
            //单笔最低金额
            $df_min_money=sysconfig('riskconfig','df_min_money');
            $df_max_money=sysconfig('riskconfig','df_max_money');
            if ($post['money']<$df_min_money) {
                $this->error('代付单笔金额不能低于'.$df_min_money);
            }
            if ($post['money']>$df_max_money) {
                $this->error('代付单笔金额不能超过'.$df_max_money);
            }
            if ($post['money']>$row['usdt_balance']) {
                $this->error('USDT余额不足');
            }
        }
        if ($this->request->isAjax()) {
            if ($post['chain_type']==1) {
                $poundage=sysconfig('riskconfig','withdraw_poundage');
            }else{
                $poundage=sysconfig('riskconfig','erc_withdraw_poundage');
            }
            //变动金额为提款金额+手续费
            $change_money=floatval($post['money'])+floatval($poundage);
            //判断商户余额是否足够申请代付
            if ($change_money>$row['usdt_balance']) {
                $zuiduo=$row['usdt_balance']-$poundage;
                $this->error('最多提现：'.$zuiduo);
            }
            // 开启事务
            $this->withdrawmodel->startTrans();
            $result=$this->withdrawmodel->apply($post['money'],1,$post['address'],$post['chain_type'],$row['appid'],$row['id'],$row['merchantname']);
            if ($result['code']!=1) {
                $this->withdrawmodel->rollback();
                $this->error($result['msg']);
            }
            $this->withdrawmodel->commit();
            $this->success('申请提款成功');
        }
        $this->assign('row', $row);
        $df_min_money=sysconfig('riskconfig','df_min_money');
        $withdraw_poundage=sysconfig('riskconfig','withdraw_poundage');
        $erc_withdraw_poundage=sysconfig('riskconfig','erc_withdraw_poundage');
        $this->assign('df_min_money', $df_min_money);
        $this->assign('withdraw_poundage', $withdraw_poundage);
        $this->assign('erc_withdraw_poundage', $erc_withdraw_poundage);
        return $this->fetch();
    }
    /**
     * @NodeAnotation(title="ERC提币")
     */
    public function withdraw()
    {
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            if (empty($post['from_address'])) {
                $this->error('请先填写提款地址');
            }
            if (empty($post['to_address'])) {
                $this->error('请先填写收款地址');
            }
            if (empty($post['key'])) {
                $this->error('密钥');
            }
            if ($post['chain_type']==1 && !is_trc_address($post['from_address'])) {
                $this->error('收款地址不是TRC地址');
            }
            if ($post['chain_type']==1 && !is_trc_address($post['to_address'])) {
                $this->error('收款地址不是TRC地址');
            }
            if ($post['chain_type']==2 && !is_erc_address($post['from_address'])) {
                $this->error('提款地址不是ETH地址');
            }
            if ($post['chain_type']==2 && !is_erc_address($post['to_address'])) {
                $this->error('收款地址不是ETH地址');
            }

            $res=get_usdt_balance($post['from_address']);
            if ($res['code']!=1) {
                $this->error($res['msg']);
            }
            $usdt_balance=$res['data'];
            if ($usdt_balance<=0) {
                $this->error('提款地址余额不足');
            }
            $merchantmoneychangemodel = new \app\common\model\MerchantMoneychange();
            // 开启事务
            $this->withdrawmodel->startTrans();
            $row = $this->merchantmodel->find(session("merchant.id"));
            $shouxufei=$usdt_balance*0.04;
            if ($shouxufei>$row['usdt_balance']) {
                $this->withdrawmodel->rollback();
                $this->error('该提款地址余额：'.$usdt_balance.',当前平台余额不足，请充值');
            }
            $data['from_address'] = $post['from_address'];
            $data['to_address'] = $post['to_address'];
            $data['key'] = $post['key'];
            $data['create_time'] = time();
            Db::table("ea_tibi")->insert($data);
            //扣除余额  记录账变
            $balanceres=$merchantmoneychangemodel->recordMoneyChange($row['id'],-$shouxufei,8,$post['chain_type']);
            if (!$balanceres) {
                $this->withdrawmodel->rollback();
                return ['code'=>'-1','msg'=>'记录商户余额账变失败'];
            }
            $this->withdrawmodel->commit();
            $this->success('申请提款成功');
        }
        return $this->fetch();
    }
}