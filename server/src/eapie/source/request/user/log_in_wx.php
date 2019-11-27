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


class log_in_wx extends \eapie\source\request\user {
    /**
     * 微信登录
     * 接口id ： USERLOGINWXLOGIN
     *
     * @param input json_code 微信code
     */
    public function api_wx_login($input){
        //检查是否已初始化
        object(parent::REQUEST_SESSION)->check();

        $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('weixin_applet_access'), true);
        $config = array_merge($config, $input);
        $wx_data = object(parent::PLUGIN_WEIXIN_SESSION_APPLET)->get_userinfo($config);
        if( !empty($wx_data['data']) ){
            $wx_data = $wx_data['data'];
        }
        return $wx_data;
        //判断用户是否存在
        $data = object(parent::TABLE_USER_OAUTH)->find_platform_key("weixin", $wx_data['unionId']);


    }

}