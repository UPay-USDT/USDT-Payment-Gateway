<?php

namespace app\common\model;

use app\common\model\TimeModel;
use think\facade\Db;

class AddressMoneychange extends TimeModel
{

    protected $name = "address_moneychange";

    protected $deleteTime = "delete_time";

    public function getStatusList()
    {
        return ['1'=>'入账','2'=>'出账'];
    }

    //记录账变
    public function recordAddressMoneyChange($address,$money,$transaction,$type)
    {
        $addressmodel = new \app\common\model\Address();
        $address_balance_data=get_usdt_balance($address);//地址USDT余额
        if ($address_balance_data['code']!=1) {
            return false;
        }
        $address_balance=$address_balance_data['data'];
        if (is_trc_address($address)) {
            //更新地址TRX余额  USDT余额
            $address_balance_trx=get_trx_balance($address);
            if ($address_balance_trx['code']==1) {
                $res1 = $addressmodel->where("address",$address)->update(["usdt_balance"=>$address_balance,"trx_balance"=>$address_balance_trx['data']]);
            }
        }
        if (is_erc_address($address)) {
            //更新地址ETH余额  USDT余额
            $address_balance_eth=get_eth_balance($address);
            if ($address_balance_eth['code']==1) {
                $res2 = $addressmodel->where("address",$address)->update(["usdt_balance"=>$address_balance,"eth_balance"=>$address_balance_eth['data']]);
            }
        }
        if ($type==1) {
            $data['money']=$money;  
            $data['before_money']=$address_balance-$money;  
            $data['after_money']=$address_balance;  
        }elseif ($type==2) {
            $data['money']=$money;  
            $data['before_money']=$address_balance-$money;  
            $data['after_money']=$address_balance;  
        }
        $data['address']=$address;
        $data['type']=$type;  
        if (!empty($transaction['change_order_id'])) {
            $data['change_order_id']=$transaction['change_order_id'];  
        } 
        if (!empty($transaction['change_order_sn'])) {
            $data['change_order_sn']=$transaction['change_order_sn'];  
        }   
        $data['create_time']=time();
        $data['update_time']=time();
        $res=$this->insert($data);
        if (!$res) {
            return false;
        }
        return true;
    }
}