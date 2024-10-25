<?php

namespace app\common\model;

use app\common\model\TimeModel;
use think\facade\Db;

class MerchantMoneychange extends TimeModel
{

    protected $name = "merchant_moneychange";

    protected $deleteTime = "delete_time";

    public function getStatusList()
    {
        return ['1'=>'订单收款','2'=>'提款','3'=>'提款手续费','4'=>'余额变动','5'=>'代付失败返回金额','6'=>'代付失败返回手续费','7'=>'收款扣除手续费','8'=>'ERC20提币手续费','9'=>'商户补单','10'=>'充值'];
    }

    //记录账变
    public function recordMoneyChange($merchant_id,$money,$type,$chain_type,$change_order_id='',$change_order_sn='',$remark='',$operate_id=0,$operate_name='',$order_merchant_id=0)
    {
        $merchantmodel = new \app\common\model\MerchantMerchant();
        $profitmodel = new \app\common\model\MerchantProfit();
        //加上锁机制
        //$merchant = $merchantmodel->where('id',$merchant_id)->find();
        $merchant = $merchantmodel->where('id',$merchant_id)->lock(true)->find();
        $merchant['balance']=$merchant['usdt_balance'];
        if ($type==1) {
            //商户只加实际金额 扣除手续费
            $poundage=sprintf("%.2f",$money*$merchant['merchant_rate']);
            $changemoney=$money-$poundage;
            $data['money']=$changemoney;  
            $data['before_money']=$merchant['balance'];  
            $data['after_money']=$merchant['balance']+$changemoney;  
            $updateData['usdt_balance']=$data['after_money'];
        }elseif ($type==2) {
            $data['money']=$money;  
            $data['before_money']=$merchant['balance'];  
            $data['after_money']=$merchant['balance']+$money;  
            $updateData['usdt_balance']=$data['after_money'];
        }elseif ($type==3) {
            $data['money']=$money;  
            $data['before_money']=$merchant['balance'];  
            $data['after_money']=$merchant['balance']+$money;  
            $updateData['usdt_balance']=$data['after_money'];
        }elseif ($type==4) {
            $data['money']=$money;
            $data['before_money']=$merchant['balance'];  
            $data['after_money']=$merchant['balance']+$money;  
            $updateData['usdt_balance']=$data['after_money'];
        }elseif ($type==5) {
            $data['money']=$money;  
            $data['before_money']=$merchant['balance'];  
            $data['after_money']=$merchant['balance']+$money;  
            $updateData['usdt_balance']=$data['after_money'];
        }elseif ($type==6) {
            $data['money']=$money;  
            $data['before_money']=$merchant['balance'];  
            $data['after_money']=$merchant['balance']+$money;  
            $updateData['usdt_balance']=$data['after_money'];
        }elseif ($type==7) {
            $data['money']=$money;  
            $data['before_money']=$merchant['balance'];  
            $data['after_money']=$merchant['balance']+$money;  
            $updateData['usdt_balance']=$data['after_money'];
        }elseif ($type==8) {
            $data['money']=$money;  
            $data['before_money']=$merchant['balance'];  
            $data['after_money']=$merchant['balance']+$money;  
            $updateData['usdt_balance']=$data['after_money'];
        }elseif ($type==9) {
            $data['money']=$money;  
            $data['before_money']=$merchant['balance'];  
            $data['after_money']=$merchant['balance']+$money;  
            if ($chain_type==1) {
                $updateData['trc_balance']=$data['after_money'];
            }else{
                $updateData['erc_balance']=$data['after_money'];
            }
        }elseif ($type==10) {
            $data['money']=$money;
            $data['before_money']=$merchant['balance'];
            $data['after_money']=$merchant['balance']+$money;
            $updateData['usdt_balance']=$data['after_money'];
        }
        $data['merchant_id']=$merchant_id;
        $data['merchantname']=$merchant['merchantname'];
        $data['type']=$type;  
        $data['chain_type']=$chain_type;  
        $data['change_order_id']=$change_order_id;  
        $data['change_order_sn']=$change_order_sn;  
        $data['remark']=$remark;  
        $data['operate_id']=$operate_id;  
        $data['operate_name']=$operate_name;  
        $data['create_time']=time();
        $data['update_time']=time();
        $res=$this->insert($data);
        if (!$res) {
            return false;
        }
        $updateData['update_time']=time();
        $result=$merchantmodel->where("id",$merchant_id)->update($updateData);
        if (!$result) {
            return false;
        }
        return true;
    }

     




}