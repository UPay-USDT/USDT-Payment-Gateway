<?php

namespace app\common\model;

use app\common\model\TimeModel;

class Address extends TimeModel
{

    protected $name = "address";

    protected $deleteTime = "delete_time";


    public function getStatusList()
    {
        return ['0' => '禁用', '1' => '正常',];
    }

    //找出TRX足够的地址
    public function getEnoughAddress($money, $type)
    {
        if ($type == 1) {
            $address = $this->where('trx_balance', '>', $money)->order("trx_balance desc")->lock(true)->find();
            if (!empty($address)) {
                return $address['address'];
            }
        }
        if ($type == 2) {
            $address = $this->where('eth_balance', '>', $money)->order("eth_balance desc")->lock(true)->find();
            if (!empty($address)) {
                return $address['address'];
            }
        }
        return false;
    }

    public function getErrorAddress()
    {
        $ordermodel = new \app\common\model\Order();
        $withdrawmodel = new \app\common\model\Withdraw();
        $address = [];
        $address1 = [];
        $address2 = [];
        //搜索所有未支付
        $where = [];
        $where[] = ['order_status', 'in', ['0']];
        $orderlist = $ordermodel->where($where)->select()->toArray();
        $address1 = array_column($orderlist, 'address');
        //搜索所有未完成的代付
        $where = [];
        $where[] = ['status', '=', 0];
        $withdrawlist = $withdrawmodel->where($where)->select()->toArray();
        $address2 = array_column($withdrawlist, 'receive_address');
        $address = array_merge($address1, $address2);
        return $address;
    }


}