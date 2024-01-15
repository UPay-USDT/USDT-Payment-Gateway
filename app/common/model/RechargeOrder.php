<?php

namespace app\common\model;

use app\common\model\TimeModel;

class RechargeOrder extends TimeModel
{

    protected $name = "recharge_order";

    protected $deleteTime = "delete_time";

    public function getStatusList()
    {
        return ['0'=>'未支付','1'=>'已支付','2'=>'超时订单','3'=>'失败订单'];
    }

    //修改订单状态 
    public function updateOrderStatus($order_id,$order_status)
    {
        $where[] = ['id', '=', $order_id];
        $list = $this->where($where)
                     ->update(["order_status" => $order_status]);
        return $list;
    }




}