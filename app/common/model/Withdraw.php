<?php

namespace app\common\model;

use app\common\model\TimeModel;
use EasyAdmin\tool\CommonTool;

class Withdraw extends TimeModel
{

    protected $name = "withdraw";

    protected $deleteTime = "delete_time";

    public function getStatusList()
    {
        return ['0'=>'未处理','1'=>'打款成功','2'=>'打款失败'];
    }

    //根据条件查询订单列表
    public function getuserListByWhere($where,$field='*')
    {
        $list = $this->field($field)
                     ->where($where)
                     ->select()
                     ->toArray();
        return $list;
    }

    //抢单
    public function qiangdan($withdraw_id)
    {
        $where[] = ['id', '=', $withdraw_id];
        $res = $this->where($where)->update(["status" => 1,"own_id" => session("user.id"),"own_name" => session("user.username"),"apply_time" => time()]);
        return $res;
    }

    //重新分配
    public function fenpei($withdraw_id)
    {
        $where[] = ['id', '=', $withdraw_id];
        $res = $this->where($where)->update(["status" => 0,"own_id" => '',"own_name" => '',"apply_time" => '']);
        return $res;
    }

    //申请提现
    public function apply($money,$type,$receive_address,$chain_type,$appid,$merchant_id='',$merchantname='')
    {
        $withdrawmodel = new \app\common\model\Withdraw();
        $merchantmoneychangemodel = new \app\common\model\MerchantMoneychange();
        
        if ($chain_type==1) {
                $poundage=sysconfig('riskconfig','withdraw_poundage');
            }else{
                $poundage=sysconfig('riskconfig','erc_withdraw_poundage');
            }

        $insertdata=[];
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $insertdata['apply_ip']=CommonTool::getRealIp();
        }
        $insertdata['plat_withdraw_sn']="PW".date('YmdHis').GetNumberCode(6);
        $insertdata['merchant_withdraw_sn']="Me".date('YmdHis').GetNumberCode(6);
        if (empty($merchant_id)) {
            return ['code'=>'-1','msg'=>'缺少商户信息'];
        }
        $insertdata['appid']=$appid;
        $insertdata['merchant_id']=$merchant_id;
        $insertdata['merchantname']=$merchantname;
        $insertdata['type']=$type;
        $insertdata['chain_type']=$chain_type;
        $insertdata['money']=$money;
        $insertdata['poundage']=$poundage;
        $insertdata['receive_address']=$receive_address;
        $insertdata['apply_time']=time();
        $insertdata['create_time']=time();
        $insertdata['update_time']=time();
        //提现订单入库
        $withdraw_id=$withdrawmodel->insertGetId($insertdata);
        if (!$withdraw_id) {
            return ['code'=>'-1','msg'=>'代付订单入库失败'];
        }
        //商户提现
        if (!empty($insertdata['merchant_id'])) {
            //扣除余额  记录账变
            $balanceres=$merchantmoneychangemodel->recordMoneyChange($insertdata['merchant_id'],-$insertdata['money'],2,$insertdata['chain_type'],$withdraw_id,$insertdata['merchant_withdraw_sn']);
            if (!$balanceres) {
                return ['code'=>'-1','msg'=>'记录商户余额账变失败'];
            }
            if ($insertdata['poundage']>0 && $type!=3) {
                //扣除手续费  记录账变
                $poundageres=$merchantmoneychangemodel->recordMoneyChange($insertdata['merchant_id'],-$insertdata['poundage'],3,$insertdata['chain_type'],$withdraw_id,$insertdata['merchant_withdraw_sn']);
                if (!$poundageres) {
                    return ['code'=>'-1','msg'=>'记录商户手续费账变失败'];
                }
            }
        }
        return ['code'=>'1','msg'=>'申请提现成功'];
        
    }
}