<?php

namespace app\common\model;

use app\common\model\TimeModel;

class MerchantProfit extends TimeModel
{

    protected $name = "merchant_profit";

    protected $deleteTime = "delete_time";

    public function getStatusList()
    {
        return ['0'=>'未激活','1'=>'激活',];
    }

}