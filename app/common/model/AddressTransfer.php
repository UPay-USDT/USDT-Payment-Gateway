<?php

namespace app\common\model;

use app\common\model\TimeModel;
use think\facade\Db;

class AddressTransfer extends TimeModel
{

    protected $name = "address_transfer";

    protected $deleteTime = "delete_time";

    public function getStatusList()
    {
        return ['1'=>'订单','3'=>'代付','5'=>'补单'];
    }

    //记录交易
    public function recordTransfer($transaction,$type)
    {
        if (is_trc_address($transaction['from_address'])) {
            $chain_type=1;
        }
        if (is_erc_address($transaction['from_address'])) {
            $chain_type=2;
        }
        $addressmoneychangemodel = new \app\common\model\AddressMoneychange();
        //先去查找是否已经存入了  没有的话 则写入
        $is_exits=$this->where("transaction_id",$transaction['transaction_id'])->find();
        if (!empty($is_exits)) {
            return true;
        }
        if ($type==1) {//收款的话  表示已经确认交易了
            $data['is_confirm']=1;  
            //记录入账
            $res2=$addressmoneychangemodel->recordAddressMoneyChange($transaction['to_address'],$transaction['money'],$transaction,1);
        }
        $data['transaction_id']=$transaction['transaction_id'];
        $data['from_address']=$transaction['from_address'];
        $data['to_address']=$transaction['to_address'];
        $data['money']=$transaction['money'];
        $data['type']=$type;  
        $data['chain_type']=$chain_type;  
        if (!empty($transaction['change_order_id'])) {
            $data['change_order_id']=$transaction['change_order_id'];
        }  
        if (!empty($transaction['change_order_sn'])) {
            $data['change_order_sn']=$transaction['change_order_sn'];
        }  
        if (!empty($transaction['time'])) {
            $data['transfer_time']=$transaction['time'];
        }
        $data['create_time']=time();
        $data['update_time']=time();
        $res=$this->insert($data);
        return true;
    }

}