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

class test extends \eapie\source\request\user
{

    const TABLE_USER_ORDER = 'eapie\source\table\user\user_order';
    /**
     * Undocumented function
     *
     * api：USERTESTLIST
     * @return void
     */
    public function api_list( $input = array() )
    {
        // return $input;
        //throw new error('hashadas测试');
        //检测登录
        // object(parent::REQUEST_USER)->check();
        // printexit(test::TABLE_USER_ORDER);
        // die;
        //检测输入
        object(parent::ERROR)->check($input, 'id', test::TABLE_USER_ORDER, array('args'), 'user_order_id');
        // return $input;
        $config = [
            'orderby'=>[
                ['order_insert_time',false],
            ],
            'where'=>[
                ['order_id > [+]',0]
            ],
            'limit'   => object(parent::REQUEST)->limit($input, parent::REQUEST_ADMIN),
        ];

        array_push($config['where'],['order_pay_time >[+]',0]);
        // return $config;
        //查询数据
        $data = object(test::TABLE_USER_ORDER)->select_page($config);
        
        return $data;
    }

        /**
     * 查询收货地址详情
     *
     * api: USERTESTSELFGET
     * req: {
     *  id  [str] [可选] [地址ID。不填则默认地址]
     * }
     * 
     * @return array
     */
    public function api_config_option($input = array())
    {
        //检测登录
        // object(parent::REQUEST_USER)->check();
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){});
    }
    public function api_testabc ($abc)
    {

        //object(parent::REQUEST_USER)->check();
        if( empty($_SESSION['session_websocket_token']) ){
            $_SESSION['session_websocket_token'] = 'websocket'.object(parent::TABLE_SESSION)->get_token_id($_SESSION['session_id']);
        }

        return $_SESSION['session_websocket_token'];

        // $config=[];
        // throw new error('hashadas测试');
        // $data = object(test::TABLE_USER_ORDER)->select();
        // $find = array("order_type","order_comment","house_top_option_info");
        // $data = object(test::TABLE_USER_ORDER)->find(1);
        // $data = object(test::TABLE_USER_ORDER)->select();
        // return $data;
        //$ab = 'websocket'.object(parent::TABLE_SESSION)->get_token_id($_SESSION['session_id']);
        // $session_websocket_token = 'websocket'.object(parent::TABLE_SESSION)->get_token_id($_SESSION['session_id']);
        // $ab = object(parent::TABLE_SESSION)->find_websocket_token($session_websocket_token);
        //printexit($ab);
        //return $ab;
    }


    /**
     * Undocumented function
     * api: 
     *
     * @return void
     */
    public function api_goods_list()
    {

    }
}