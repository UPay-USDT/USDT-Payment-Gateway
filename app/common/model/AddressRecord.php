<?php

namespace app\common\model;

use app\common\model\TimeModel;
use think\facade\Db;

class AddressRecord extends TimeModel
{

    protected $name = "address_record";

    protected $deleteTime = "delete_time";

    public function getStatusList()
    {
        return ['1'=>'入账','2'=>'出账'];
    }

    //记录账变
    public function record($address,$merchant_id,$merchantname,$money,$type='1',$change_order_id='',$change_order_sn='',$remark='')
    {
        $address_balance_data=get_usdt_balance($address);//地址余额
        if ($address_balance_data['code']!=1) {
            return false;
        }
        $address_balance=$address_balance_data['data'];
        if ($type==1) {
            $data['money']=$money;  
            $data['before_money']=$address_balance;  
            $data['after_money']=$address_balance+$money;  
        }elseif ($type==2) {
            $data['money']=$money;  
            $data['before_money']=$address_balance;  
            $data['after_money']=$address_balance+$money;  
        }
        $data['address']=$address;
        $data['merchant_id']=$merchant_id;
        $data['merchantname']=$merchantname;
        $data['type']=$type;  
        $data['change_order_id']=$change_order_id;  
        $data['change_order_sn']=$change_order_sn;  
        $data['create_time']=time();
        $data['update_time']=time();
        $res=$this->insert($data);
        if (!$res) {
            return false;
        }
        return true;
    }
}