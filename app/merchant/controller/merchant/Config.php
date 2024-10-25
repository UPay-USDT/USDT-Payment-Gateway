<?php

namespace app\merchant\controller\merchant;


 
use app\merchant\service\TriggerService;
use app\common\controller\MerchantController;
use EasyAdmin\annotation\ControllerAnnotation;
use EasyAdmin\annotation\NodeAnotation;
use think\App;
use app\common\model\MerchantMerchant;
/**
 * Class Config
 * @package app\merchant\controller\system
 * @ControllerAnnotation(title="设置")
 */
class Config extends MerchantController
{

    public function __construct(App $app)
    {
        parent::__construct($app);
         
    }

    /**
     * @NodeAnotation(title="列表")
     */
    public function index()
    {
        $id = session('merchant.id');
        $row = MerchantMerchant::withoutField('password')->find($id);
        $this->assign('row', $row);
        $auto_df_min_money=sysconfig('riskconfig','auto_df_min_money');
        $df_max_money=sysconfig('riskconfig','df_max_money');
        $this->assign('auto_df_min_money', $auto_df_min_money);
        return $this->fetch();
    }

    /**
     * 提现设置
     */
    public function tixian()
    {
        $id = session('merchant.id');
        $row = MerchantMerchant::withoutField('password')->find($id);
        empty($row) && $this->error('用户信息不存在');
         //单笔最低金额
        $auto_df_min_money=sysconfig('riskconfig','auto_df_min_money');
        $df_max_money=sysconfig('riskconfig','df_max_money');
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            $rule = [];
            $this->validate($post, $rule);
            if (isset($post['is_auto_tixian']) && $post['is_auto_tixian']==1) {
                if (empty($post['trc_address'])) {
                    $this->error('请填写TRC自动提现地址');
                }
            }else{
                $post['is_auto_tixian']=0;
            }
            if (!empty($post['trc_address']) && !is_trc_address($post['trc_address'])) {
                $this->error('TRC收款地址错误');
            }
            if ($post['tixian_usdt']<$auto_df_min_money) {
                $this->error('自动提现金额不能低于'.$auto_df_min_money);
            }
            if ($post['tixian_usdt']>$df_max_money) {
                $this->error('自动提现金额不能超过'.$df_max_money);
            }
            try {
                $save = $row
                    ->allowField(['mobile', 'qq','update_time','is_auto_tixian','trc_address','tixian_usdt'])
                    ->save($post);
            } catch (\Exception $e) {
                $this->error('保存失败');
            }
            $save ? $this->success('保存成功') : $this->error('保存失败');
        }
        $this->assign('row', $row);
        return $this->fetch();
    }

    /**
     * 密钥设置
     */
    public function miyue()
    {
        $type=input('type');
        $id = session('merchant.id');
        $row = MerchantMerchant::withoutField('password')->find($id);
        empty($row) && $this->error('用户信息不存在');
        if ($this->request->isAjax()) {
            $post = $this->request->post();
            $rule = [];
            $this->validate($post, $rule);

            if ($type==1) {
                $returndata['appid']=$row['appid'];
                $returndata['appsecret']=$row['appsecret'];
                $this->error('显示密钥',$returndata,'/show');
            }
            if ($type==2) {
                //判断appid是否已经存在  存在则稍后再试
                $appid=GetNumberStr(8);
                $appsecret=GetNumberStr(16);
                $is_exit = MerchantMerchant::where("appid",$appid)->field("id,merchant_rate")->find();
                if (!empty($is_exit) && $is_exit['id']) {
                    $this->error('请3秒后再试');
                }
                try {
                    $post['appid']=$appid;
                    $post['appsecret']=$appsecret;
                    $save = $row
                        ->allowField(['appid', 'appsecret'])
                        ->save($post);
                } catch (\Exception $e) {
                    $this->error('生成失败');
                }
                $save ? $this->success('生成成功') : $this->error('生成失败');
            }
        }
        $this->assign('row', $row);
        return $this->fetch();
    }

}