<?php

namespace app\merchant\middleware;

use think\Request;

/**
 * 检测用户登录 
 * Class CheckMerchant
 * @package app\merchant\middleware
 */
class CheckMerchant
{

    use \app\common\traits\JumpTrait;

    public function handle(Request $request, \Closure $next)
    {
        $merchantConfig = config('merchant');
        $merchantId = session('merchant.id');
        $expireTime = session('merchant.expire_time');
        $currentController = parse_name($request->controller());
        $currentNode = parse_name($request->controller()) . '/' . parse_name($request->action());
        // 验证登录
        if (!in_array($currentController, $merchantConfig['no_login_controller']) &&
            !in_array($currentNode, $merchantConfig['no_login_node'])) {
            
            empty($merchantId) && $this->redirect('/merchant/login/index');;

            // 判断是否登录过期
            if ($expireTime !== true && time() > $expireTime) {
                session('merchant', null);
                $this->redirect('/merchant/login/index');
                //$this->error('登录已过期，请重新登录', [], __url('merchant/login/index'));
            }
        }

        return $next($request);
    }

}