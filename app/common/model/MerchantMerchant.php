<?php

namespace app\common\model;

use app\common\model\TimeModel;

class MerchantMerchant extends TimeModel
{

    protected $name = "merchant_merchant";

    protected $deleteTime = "delete_time";



    public function getStatusList()
    {
        return ['0'=>'未激活','1'=>'激活',];
    }

    public function getmerchantList()
    {
        $list = $this->field('id,merchantname')
                     ->select()
                     ->toArray();
        $res=array_column($list,'merchantname','id');
        return $res;
    }

    //根据条件查询商户列表
    public function getmerchantListByWhere($where,$field='*')
    {
        $list = $this->field($field)
                     ->where($where)
                     ->select()
                     ->toArray();
        return $list;
    }

    //根据商户id集合 查询商户
    public function getmerchantListByIds($ids,$field='*')
    {
        $list = $this->field($field)
                     ->whereIn('id',$ids)
                     ->select()
                     ->toArray();
        return $list;
    }

}