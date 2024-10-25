<?php

namespace app\common\model;

use app\common\model\TimeModel;
use think\facade\Db;

class Tibi extends TimeModel
{

    protected $name = "tibi";

    protected $deleteTime = "delete_time";


}