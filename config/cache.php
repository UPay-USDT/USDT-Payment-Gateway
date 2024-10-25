<?php
use think\facade\Env;

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 默认缓存驱动
    'default' => Env::get('cache.driver', 'file'),

    // 缓存连接方式配置
    'stores'  => [
        'file' => [
            // 驱动方式
            'type'       => 'File',
            // 缓存保存目录
            'path'       => '',
            // 缓存前缀
            'prefix'     => '',
            // 缓存有效期 0表示永久缓存
            'expire'     => 0,
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
            // 序列化机制 例如 ['serialize', 'unserialize']
            'serialize'  => [],
        ],
	'redis' => [
            'type' => 'redis',
            'host' => Env::get('redis.host', '127.0.0.1'),
            'port' => Env::get('redis.port', '6379'),
            'password' => Env::get('redis.password', ''),
            'select' => '0',
            // 全局缓存有效期（0为永久有效）
            'expire' => 0,
            // 缓存前缀
            'prefix' => '',
            //默认缓存周期
            'timeout' => 3600,
        ],
    ],
];
