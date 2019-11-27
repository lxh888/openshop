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


// 用户资金
class money extends \eapie\source\request\user
{

    /**
     * 预付款
     *
     * api: [旧] USERSELFMONEYTOTAL
     * api: USERMONEYSELFTOTAL
     *
     * @return int
     */
    public function api_self_total()
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        $money = object(parent::TABLE_USER_MONEY)->find_now_data($_SESSION['user_id']);
        $money = empty($money['user_money_value']) ? 0 : $money['user_money_value'];
        return $money;
    }

    /**
     * 养老金
     *
     * api: [旧] USERSELFMONEYANNUITY
     * api: USERMONEYSELFANNUITY
     * req: null
     *
     * @return decimal
     */
    public function api_self_annuity()
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        $money = object(parent::TABLE_USER_MONEY_ANNUITY)->find_now_data($_SESSION['user_id']);
        $money = empty($money['user_money_annuity_value']) ? 0 : $money['user_money_annuity_value'];
        return $money;
    }

    /**
     * 赠送收益
     *
     * api: [旧] USERSELFMONEYEARNING
     * api: USERMONEYSELFEARNING
     * req: null
     *
     * @return decimal
     */
    public function api_self_earning()
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        $money =object(parent::TABLE_USER_MONEY_EARNING)->find_now_data($_SESSION['user_id']);
        $money = empty($money['user_money_earning_value']) ? 0 : $money['user_money_earning_value'];
        return $money;
    }

    /**
     * 扶贫资金
     *
     * api: [旧] USERSELFMONEYHELP
     * api: USERMONEYSELFHELP
     * req: null
     *
     * @return decimal
     */
    public function api_self_help()
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        $money = object(parent::TABLE_USER_MONEY_HELP)->find_now_data($_SESSION['user_id']);
        $money = empty($money['user_money_help_value']) ? 0 : $money['user_money_help_value'];
        return $money;
    }

    /**
     * 服务费
     *
     * api: [旧] USERSELFMONEYSERVICE
     * api: USERMONEYSELFSERVICE
     * req: null
     *
     * @return decimal
     */
    public function api_self_service()
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        $money = object(parent::TABLE_USER_MONEY_SERVICE)->find_now_data($_SESSION['user_id']);
        $money = empty($money['user_money_service_value']) ? 0 : $money['user_money_service_value'];
        return $money;
    }

    /**
     * 共享金
     *
     * api: [旧] USERSELFMONEYSHARE
     * api: USERMONEYSELFSHARE
     * req: null
     *
     * @return decimal
     */
    public function api_self_share()
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        $money = object(parent::TABLE_USER_MONEY_SHARE)->find_now_data($_SESSION['user_id']);
        $money = empty($money['user_money_share_value']) ? 0 : $money['user_money_share_value'];
        return $money;
    }

    /**
     * 累计收入金额
     * api: USERMONEYSELFPLUS
     * req: {
     *  type [str] [可选] [交易类型]
     * }
     * @return int
     */
    public function api_self_plus($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        $where = array();
        $where[] = array('user_id = [+]', $_SESSION['user_id']);
        if (!empty($input['type']) && is_string($input['type'])) {
            $where[] = array('[and] user_money_type = [+]', $input['type']);
        }

        return object(parent::TABLE_USER_MONEY)->get_sum_plus($where);
    }

    /**
     * E麦冻结资金(RMB)
     * api: USERMONEYSELFUSERMONEYFREEZE
     * 
     * {"class":"user/money","method":"api_self_user_money_freeze"}
     * @return array result []
     */
    public function api_self_user_money_freeze(){
        $user_id = $_SESSION['user_id'];

        // 查找订单记录
        $config = array();

        $config['where'] = array(
            array('so.shop_order_pay_state=1'),
            array('sog.recommend_user_id=[+]',$user_id),
            array('sog.recommend_money_order_id=[+]',''),
        );

        $config['select'] = array(
            'sog.recommend_money',
            'sog.shop_goods_property as goods_property',
            'sog.shop_goods_index as goods_index',
            'sog.shop_order_goods_name as goods_name',
            'sog.shop_order_goods_number as goods_number',
            'sog.shop_order_goods_price as goods_price',
            'so.shop_order_pay_time as pay_time',
            'so.shop_order_pay_money as pay_money',
        );
        $result = object(parent::TABLE_SHOP_ORDER_GOODS)->select_join_shop_order($config);

        return $result;
    }
}