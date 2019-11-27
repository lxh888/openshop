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



namespace eapie\source\request\house;

use eapie\main;
use eapie\error;

//置顶订单
class top_order extends \eapie\source\request\house
{


    /**
     * 创建订单
     *
     * api: HOUSETOPORDERSELFADD
     * req: {
     *  house_top_id        [str] [必填] [楼盘置顶ID]
     *  house_product_id    [str] [必填] [楼盘项目ID]
     *  pay_method          [str] [必填] [支付方式]
     * }
     * 
     * @param  array  $input [description]
     * @return array
     */
    public function api_self_add($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();
        $user_id = $_SESSION['user_id'];

        //检测输入
        object(parent::ERROR)->check($input, 'house_top_id', parent::TABLE_HOUSE_TOP_OPTION, array('args'));
        object(parent::ERROR)->check($input, 'house_product_id', parent::TABLE_HOUSE_PRODUCT, array('args'));
        object(parent::ERROR)->check($input, 'pay_method', parent::TABLE_SHOP_ORDER, array('args'));

        //查询置顶楼盘的数量
        $order_count = object(parent::TABLE_HOUSE_PRODUCT_TOP)->get_count();
        if ($order_count >= 12)
            throw new Exception('置顶楼盘名额已满');

        //查询置顶信息
        $data_housetoption = object(parent::TABLE_HOUSE_TOP_OPTION)->find($input['house_top_id']);
        if (empty($data_housetoption))
            throw new error('楼盘置顶信息不存在');

        //查询楼盘项目信息
        $data_houseproduct = object(parent::TABLE_HOUSE_PRODUCT)->find($input['house_product_id']);
        if (empty($data_houseproduct))
            throw new error('楼盘信息不存在');

        $order = array(
            'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
            'order_type' => parent::TRANSACTION_TYPE_HOUSE_TOP_ORDER,
            'order_comment' => '置顶',
            'order_action_user_id' => $user_id,
            'order_plus_method' => '',
            'order_plus_account_id' => '',
            'order_plus_value' => $data_housetoption['house_top_option_money'],
            'order_minus_method' => $input['pay_method'],
            'order_minus_account_id' => $user_id,
            'order_minus_value' => $data_housetoption['house_top_option_money'],
            'order_state' => 1,
            'order_pay_state' => 0,
            'order_insert_time' => time(),
            'order_json' => array(
                'house_top_option' => $data_housetoption,
            ),
        );

        $house_top_order = array(
            'house_top_order_id' => object(parent::TABLE_HOUSE_TOP_ORDER)->get_unique_id(),
            'user_id' => $user_id,
            'order_id' => $order['order_id'],
            'house_product_id' => $input['house_product_id'],
            'house_top_option_id' => $data_housetoption['house_top_option_id'],
            'house_top_option_name' => $data_housetoption['house_top_option_name'],
            'house_top_option_month' => $data_housetoption['house_top_option_month'],
            'house_top_option_money' => $data_housetoption['house_top_option_money'],
            'house_top_order_state' => 1,
            'house_top_order_pay_state' => 0,
            'house_top_order_insert_time' => time(),
            'house_top_order_json' => array(
                'house_top_option' => $data_housetoption,
            ),
        );

        //输出数据
        $output = array();

        //判断支付方式
        switch ($input['pay_method']) {
            case parent::PAY_METHOD_WEIXINPAY://微信
            case parent::PAY_METHOD_ALIPAY://支付宝
                $output = object(parent::REQUEST_APPLICATION)->get_pay_config($input, $order['order_json'], array(
                    'money_fen' => $data_housetoption['house_top_option_money'],
                    'subject' => $data_housetoption['house_top_option_name'],
                    'body' => $data_housetoption['house_top_option_name'],
                    'order_id' => $order['order_id']
                ));
                break;
            case parent::PAY_METHOD_USER_MONEY://用户钱包
                $output['order_id'] = $order['order_id'];
                $output['house_top_order_id'] = $house_top_order['house_top_order_id'];
                $output['order_money'] = $data_housetoption['house_top_option_money'];
                break;
        }

        $order['order_json'] = cmd(array($order['order_json']), 'json encode');
        $house_top_order['house_top_order_json'] = cmd(array($house_top_order['house_top_order_json']), 'json encode');

        //创建订单
        if (object(parent::TABLE_HOUSE_TOP_ORDER)->create_order($order, $house_top_order)) {
            return $output;
        } else {
            throw new error('操作失败');
        }
    }


    /**
     * 支付
     *
     * api: HOUSETOPORDERSELFPAY
     * req: {
     *  id              [int] [必填] [订单ID]
     *  pay_method      [str] [必填] [支付方式]
     *  pay_password    [int] [可选] [支付密码。支付方式为用户钱包时，必填]
     * }
     * 
     * @return string 订单ID
     */
    public function api_self_pay($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();
        $user_id = $_SESSION['user_id'];

        //检测输入
        object(parent::ERROR)->check($input, 'id', parent::TABLE_SHOP_ORDER, array('args'), 'shop_order_id');
        object(parent::ERROR)->check($input, 'pay_method', parent::TABLE_SHOP_ORDER, array('args'));

        //查询置顶楼盘的数量
        $order_count = object(parent::TABLE_HOUSE_PRODUCT_TOP)->get_count();
        if ($order_count >= 12)
            throw new Exception('置顶楼盘名额已满');

        //查询置顶订单信息
        $house_top_order = object(parent::TABLE_HOUSE_TOP_ORDER)->find($input['id']);
        if (empty($house_top_order))
            throw new error('订单数据不存在');

        if ($house_top_order['house_top_order_state'] == 0)
            throw new error('订单已取消');

        if ($house_top_order['house_top_order_trash'] == 1)
            throw new error('订单已回收');

        if ($house_top_order['house_top_order_delete_state'] == 1)
            throw new error('订单已删除');

        if ($house_top_order['house_top_order_pay_state'] == 1)
            throw new error('订单已支付');

        //判断支付方式
        switch ($input['pay_method']) {
            case parent::PAY_METHOD_WEIXINPAY://微信
            case parent::PAY_METHOD_ALIPAY://支付宝
                //获取支付参数
                $output = object(parent::REQUEST_APPLICATION)->get_pay_config($input, array(), array(
                    'money_fen' => $house_top_order['house_top_order_money'],
                    'subject' => $house_top_order['house_top_order_name'],
                    'body' => $house_top_order['house_top_order_name'],
                    'order_id' => $house_top_order['order_id']
                ));
                $output['order_id'] = $house_top_order['order_id'];

                return $output;
                break;
            case parent::PAY_METHOD_USER_MONEY://用户钱包
                //检测输入
                object(parent::ERROR)->check($input, 'pay_password', parent::TABLE_USER, array('args'));

                //检测支付密码
                $res = object(parent::TABLE_USER)->check_pay_password($input['password']);
                if ($res !== true)
                    throw new error($res);

                //检测余额
                $user_money = object(parent::TABLE_USER_MONEY)->find_now_data($user_id);
                if (empty($user_money['user_money_value']) || $user_money['user_money_value'] < $house_top_order['house_top_option_price'])
                    throw new error('余额不足');

                return object(parent::TABLE_SHOP_ORDER)->pay_by_user_money($house_top_order['order_id']);
                break;
        }
    }


}