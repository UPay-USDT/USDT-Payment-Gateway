<?php

namespace app\merchant\middleware;

use app\merchant\service\MerchantLogService;
use EasyAdmin\tool\CommonTool;

/**
 * 系统操作日志中间件
 * Class SystemLog
 * @package app\merchant\middleware
 */
class MerchantLog
{

    /**
     * 敏感信息字段，日志记录时需要加密
     * @var array
     */
    protected $sensitiveParams = [
        'password',
        'password_again',
    ];

    public function handle($request, \Closure $next)
    {
        if ($request->isAjax()) {
            $method = strtolower($request->method());
            if (in_array($method, ['post', 'put', 'delete'])) {
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
                    'user_id'    => session('merchant.id'),
                    'url'         => $url,
                    'method'      => $method,
                    'ip'          => $ip,
                    'content'     => json_encode($params, JSON_UNESCAPED_UNICODE),
                    'useragent'   => $_SERVER['HTTP_USER_AGENT'],
                    'create_time' => time(),
                ];
                MerchantLogService::instance()->save($data);
            }
        }
        return $next($request);
    }

}