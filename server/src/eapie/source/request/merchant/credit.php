<?php
/** ---- eapie ----
 * 优狐积木框架，让开发就像组装积木一样简单！
 * 解放千千万万程序员！这只是1.0版本，后续版本势如破竹！
 * 
 * QQ群：523668865
 * 开源地址 https://gitee.com/lxh888/openshop
 * 官网 http://eonfox.com/
 * 后端框架文档 http://cao.php.eonfox.com
 * 
 * 作者：绵阳市优狐网络科技有限公司
 * 电话/微信：18981181942
 * QQ：294520544
 */



namespace eapie\source\request\merchant;

use eapie\main;
use eapie\error;

// 商家积分
class credit extends \eapie\source\request\merchant
{

    /**
     * 获取当前登录用户的指定商家的剩余积分 
     * 
     * api: MERCHANTCREDITSELF
     * req: {
     *  merchant_id  [str] [可选] [商家ID，默认该用户所属的商家第一个ID]
     * }
     * 
     * @return  [int] [商家积分]
     */
    public function api_self($input = array())
    {
        //检测用户角色
        $merchant_id = $this->check_role($input);
        $data = object(parent::TABLE_MERCHANT_CREDIT)->find_now_data($merchant_id);
        return empty($data['merchant_credit_value']) ? 0 : $data['merchant_credit_value'];
    }

    /**
     * 获取当前登录用户的指定商家的消费积分
     *
     * api MERCHANTCREDITSELFMINUS
     * req: {
     *  merchant_id  [str] [必填] [商家ID]
     * }
     *
     * @return [int] [商家消费积分]
     */
    public function api_self_minus($input = array())
    {
    	object(parent::REQUEST_USER)->check();
        //校验数据
        object(parent::ERROR)->check($input, 'merchant_id', parent::TABLE_MERCHANT, array('args'));

        //判断权限
        if (!object(parent::TABLE_MERCHANT_USER)->check_exist($_SESSION['user_id'], $input['merchant_id'], true))
            throw new error('权限不足');

        return object(parent::TABLE_MERCHANT_CREDIT)->get_sum_minus($input['merchant_id']);
    }

}