<?php

namespace app\merchant\controller;


use app\common\model\MerchantMerchant;
use app\common\controller\MerchantController;
use think\captcha\facade\Captcha;
use think\facade\Env;
use EasyAdmin\tool\CommonTool;
use think\facade\Log;

/**
 * Class Login
 * @package app\merchant\controller
 */
class Login extends MerchantController
{

    /**
     * 初始化方法
     */
    public function initialize()
    {
        parent::initialize();
        $action = $this->request->action();
        if (!empty(session('merchant')) && !in_array($action, ['out'])) {
            $this->redirect('/merchant/index/index');
        }
    }

    /**
     * 用户登录
     * @return string
     * @throws \Exception
     */
    public function index()
    {
        $captcha = true;
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $rule = [
                'merchantname|用户名'      => 'require',
                'password|密码'       => 'require',
                'keep_login|是否保持登录' => 'require',
            ];
            // if (isset($post['newpassword']) && strlen($post['newpassword']) > 0) {
            //     $captcha = 0;
            // }
            $captcha == 1 && $rule['captcha|验证码'] = 'require|captcha';
            $this->validate($post, $rule);
            $merchant = MerchantMerchant::where(['merchantname' => $post['merchantname']])->find();
            if (empty($merchant)) {
                $this->error('商户不存在');
            }
            //判断是白名单登录
            $ip = CommonTool::getRealIp();
            if (trim($merchant['login_ip'])) {
                $ipItem = explode("\n", $merchant['login_ip']);
                if (!in_array($ip, $ipItem)) {
                   $this->error('登录IP错误');
                }
            }
            if (password($post['password']) != $merchant->password) {
                $this->error('密码输入有误');
            }
            if ($merchant->status == 0) {
                $this->error('账号已被禁用');
            }

            $merchant->login_num += 1;
            $merchant->last_login_time = time();
            $merchant->save();
            $merchant = $merchant->toArray();
            unset($merchant['password']);
            $merchant['expire_time'] = $post['keep_login'] == 1 ? true : time() + 86400;
            session('merchant', $merchant);
            $this->success('登录成功');
        }
        $this->assign('captcha', $captcha);
        $this->assign('demo', $this->isDemo);
        $this->assign('liaotian', 1);
        return $this->fetch();
    }

    /**
     * 用户退出
     * @return mixed
     */
    public function out()
    {
        session('merchant', null);
        $this->success('退出登录成功');
    }

    /**
     * 验证码
     * @return \think\Response
     */
    public function captcha()
    {
        return Captcha::create();
    }

    /**
     * 注册
     * @return \think\Response
     */
    public function register()
    {
        // TODO: 商户注册开放验证码
//        $captcha = Env::get('easyadmin.captcha', 1);
        $captcha = true;
        if ($this->request->isPost()) {
            $model = new \app\common\model\MerchantMerchant();
            $post = $this->request->post();
            $rule = [
                'merchantname|用户名'      => 'require',
                'password|密码'       => 'require',
                'password2|重复密码' => 'require',
            ];
            $captcha == 1 && $rule['captcha|验证码'] = 'require|captcha';
            $this->validate($post, $rule);
            if ($post['password'] != $post['password2']) {
                $this->error('两次密码输入不一致');
            }
            $merchant = $model->where(['merchantname' => $post['merchantname']])->find();
            if (!empty($merchant)) {
                $this->error('商户名称已存在');
            }
            //判断appid是否已经存在  存在则稍后再试
            $appid=GetNumberStr(8);
            $appsecret=GetNumberStr(16);
            $is_exit = $model->where("appid",$appid)->field("id,merchant_rate")->find();
            if (!empty($is_exit) && $is_exit['id']) {
                $this->error('请3秒后再试');
            }
            try {
                $post['password']=password($post['password']);
                $post['appid']=$appid;
                $post['appsecret']=$appsecret;
                $post['merchant_rate']=sysconfig('riskconfig','merchant_rate');
                $save = $model->allowField(['merchantname', 'password','merchant_rate', 'appid','appsecret'])->save($post);
            } catch (\Exception $e) {
                $this->error('注册失败:'.$e->getMessage());
            }
            $save ? $this->success('注册成功，去登录') : $this->error('注册失败');
        }
        $this->assign('captcha', $captcha);
        $this->assign('liaotian', 1);
        return $this->fetch();
    }
}
