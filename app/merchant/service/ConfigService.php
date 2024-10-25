<?php

namespace app\merchant\service;

use think\facade\Cache;

class ConfigService
{

    public static function getVersion()
    {
        return Cache('version');
    }
}