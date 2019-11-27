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



namespace eapie\source\request\user;

use eapie\main;
use eapie\error;

//支付密码
class pay_password extends \eapie\source\request\user
{


    /**
     * 重置支付密码
     *
     * api: USERSELFPAYPASSWORD
     * req: {
     *  phone               [int] [必填] [手机号]
     *  phone_verify_key    [str] [必填] [手机验证码键名称(reset_pay_password)]
     *  phone_verify_code   [int] [必填] [手机验证码]
     *  password            [int] [必填] [支付密码]
     * }
     * $user_data['user_json'] = array(
     *      "pay_password":{
     *          "left"
     *          "right"
     *          "error_time" = []
     *      }
     * )
     * 
     */
    public function api_self($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();
        $user_id = $_SESSION['user_id'];

        //检测验证码，这里面会检测 $data["phone"] 的合法性
        object(parent::REQUEST_SESSION)->phone_verify_code_check($input);

        //检测输入
        object(parent::ERROR)->check($input, 'password', parent::TABLE_USER, array('args'), 'pay_password');

        //查询数据
        $data = object(parent::TABLE_USER)->find($user_id);
        if (empty($data))
            throw new error('用户数据不存在');

        //用户json数据
        $user_json = cmd(array($data['user_json']), 'json decode');
        if (empty($user_json))
            $user_json = array();

        //支付密码
        $pay_password = array(
            'left' => md5($input['password'].$user_id),
            'right' => md5($user_id.$input['password']),
            'error_time' => array()
        );

        //格式化数据
        $user_json['pay_password'] = $pay_password;
        $update_data = array(
            'user_json' => cmd(array($user_json), 'json encode'),
            'user_update_time' => time()
        );

        //更新数据
        if (object(parent::TABLE_USER)->update(array(array('user_id=[+]', $user_id)), $update_data)) {
            return $user_id;
        } else {
            throw new error ('操作失败');
        }
    }


}