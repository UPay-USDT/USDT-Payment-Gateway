<?php

namespace app\common\model;

use app\common\model\TimeModel;

class MerchantAddress extends TimeModel
{

    protected $name = "merchant_address";

    protected $deleteTime = "delete_time";

    

    public function getStatusList()
    {
        return ['0'=>'禁用','1'=>'正常',];
    }
    
 
}