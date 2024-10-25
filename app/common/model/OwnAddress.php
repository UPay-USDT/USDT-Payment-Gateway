<?php

namespace app\common\model;

use app\common\model\TimeModel;

class OwnAddress extends TimeModel
{

    protected $name = "own_address";

    protected $deleteTime = "delete_time";

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