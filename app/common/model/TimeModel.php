<?php

namespace app\common\model;


use think\Model;
use think\model\concern\SoftDelete;

/**
 * 有关时间的模型
 * Class TimeModel
 * @package app\common\model
 */
class TimeModel extends Model
{
    protected $type = [
        'last_login_time'  =>  'timestamp',
        'pay_time'  =>  'timestamp',
        'give_time'  =>  'timestamp',
        'apply_time'  =>  'timestamp',
        'submit_time'  =>  'timestamp',
        'financial_time'  =>  'timestamp',
        'part_pay_time'  =>  'timestamp',
        'allocation_time'  =>  'timestamp',
        'transfer_time'  =>  'timestamp',
    ];

    /**
     * 自动时间戳类型
     * @var string
     */
    protected $autoWriteTimestamp = true;

    /**
     * 添加时间
     * @var string
     */
    protected $createTime = 'create_time';

    /**
     * 更新时间
     * @var string
     */
    protected $updateTime = 'update_time';

    /**
     * 软删除
     */
    use SoftDelete;
    protected $deleteTime = false;

}