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




namespace eapie\source\request\shop;

use eapie\main;
use eapie\error;

/**
 * 拼团商品订单
 */
class order_group extends \eapie\source\request\shop
{


    //===========================================
    // 操作
    //===========================================


    /**
     * 下单
     * @author green
     *
     * api: SHOPORDERGROUPSELFFOUND
     * req: {
     *  shop_goods_group_id   [str] [必填] [拼团商品ID]
     *  user_address_id     [str] [必填] [收货地址ID]
     * }
     *
     * @return array
     */
    public function api_self_found($input = array())
    {
        // 检测登录
        object(parent::REQUEST_USER)->check();
        $user_id = $_SESSION['user_id'];

        // 检测请求参数
        object(parent::ERROR)->check($input, 'shop_goods_group_id', parent::TABLE_SHOP_GOODS_GROUP, array('args'));
        object(parent::ERROR)->check($input, 'user_address_id', parent::TABLE_USER_ADDRESS, array('args'));

        // 查询拼团商品
        $shop_goods_group = object(parent::TABLE_SHOP_GOODS_GROUP)->find($input['shop_goods_group_id']);
        if (empty($shop_goods_group)) {
            throw new error('该拼团商品不存在');
        }

        // 查询商品信息
        $shop_goods = object(parent::TABLE_SHOP_GOODS)->find($shop_goods_group['shop_goods_id']);

        // 查询商品规格
        $shop_goods_sku = object(parent::TABLE_SHOP_GOODS_SKU)->find($shop_goods_group['shop_goods_sku_id']);
        $shop_goods['sku'] = $shop_goods_sku;
        $shop_goods['image_id'] = object(parent::TABLE_SHOP_GOODS_IMAGE)->get_main_img_id($shop_goods['shop_goods_id']);

        // 查询收货地址
        $user_address = object(parent::TABLE_USER_ADDRESS)->find($input['user_address_id']);
        if (!$user_address) {
            throw new error('收货地址不存在');
        }

        // 查询拼团订单
        $shop_order_group = object(parent::TABLE_SHOP_ORDER_GROUP)->find_where(array(
            array('user_id = [+]', $user_id),
            array('shop_goods_group_id = [+]', $input['shop_goods_group_id']),
        ));
        if ($shop_order_group) {
            if ($shop_order_group['shop_order_group_pay'] === '1') {
                throw new error('已参加拼团');
            } else {
                return array(
                    'shop_order_group_id' => $shop_order_group['shop_order_group_id'],
                    'order_money' => $shop_goods['shop_goods_property'] ? 0 : $shop_order_group['shop_order_group_price'],
                    'order_credit' => $shop_goods['shop_goods_property'] ? $shop_order_group['shop_order_group_price'] : 0,
                );
            }
        }

        // 拼团订单数据
        $data_shopordergroup = array(
            'shop_order_group_id'           => object(parent::TABLE_SHOP_ORDER_GROUP)->get_unique_id(),
            'user_id'                       => $user_id,
            'shop_id'                       => $shop_goods['shop_id'],
            'shop_goods_group_id'           => $input['shop_goods_group_id'],
            'shop_order_group_price'        => $shop_goods_group['shop_goods_group_price'],
            'shop_goods'                    => cmd(array($shop_goods), 'json encode'),
            // 收货信息
            'user_address_consignee'        => $user_address['user_address_consignee'],
            'user_address_phone'            => $user_address['user_address_phone'],
            'user_address_province'         => $user_address['user_address_province'],
            'user_address_city'             => $user_address['user_address_city'],
            'user_address_district'         => $user_address['user_address_district'],
            'user_address_details'          => $user_address['user_address_details'],
            'shop_order_group_insert_time'  => time(),
            'shop_order_group_update_time'  => time(),
        );

        // 插入数据
        $res = object(parent::TABLE_SHOP_ORDER_GROUP)->insert($data_shopordergroup);
        if ($res) {
            return array(
                'shop_order_group_id' => $data_shopordergroup['shop_order_group_id'],
                'order_money' => $shop_goods['shop_goods_property'] ? 0 : $data_shopordergroup['shop_order_group_price'],
                'order_credit' => $shop_goods['shop_goods_property'] ? $data_shopordergroup['shop_order_group_price'] : 0,
            );
        } else {
            throw new error('操作失败');
        }
    }

    /**
     * 支付
     * @author green
     * 
     * api: SHOPORDERGROUPSELFPAYMENT
     * req: {
     *  shop_order_group_id [str] [必填] [拼团订单ID]
     *  pay_method          [str] [必填] [支付方式]
     * }
     *
     * @return array
     */
    public function api_self_payment($input = array())
    {
        // 检测登录
        object(parent::REQUEST_USER)->check();
        $user_id = $_SESSION['user_id'];

        // 检测输入
        object(parent::ERROR)->check($input, 'shop_order_group_id', parent::TABLE_SHOP_ORDER_GROUP, array('args'));
        object(parent::ERROR)->check($input, 'pay_method', parent::TABLE_SHOP_ORDER, array('args'));

        // 查询拼团订单信息
        $shop_order_group = object(parent::TABLE_SHOP_ORDER_GROUP)->find($input['shop_order_group_id']);
        if (empty($shop_order_group) || $shop_order_group['user_id'] !== $user_id) {
            throw new error('订单数据不存在');
        }
        if( $shop_order_group['shop_order_group_delete'] === '1' ){
            throw new error('订单已删除');
        }
        if( $shop_order_group['shop_order_group_trash'] === '1' ){
            throw new error('订单已回收');
        }
        if ($shop_order_group['shop_order_group_pay'] === '1') {
            throw new error('订单已支付');
        }

        // 查询拼团商品信息
        $shop_goods_group = object(parent::TABLE_SHOP_GOODS_GROUP)->find($shop_order_group['shop_goods_group_id']);
        if (empty($shop_goods_group)) {
            throw new error('该拼团商品不存在');
        }
        if ($shop_goods_group['shop_goods_group_state'] === '0') {
            throw new error('拼团已结束');
        }

        // 查询商品信息
        $shop_goods = object(parent::TABLE_SHOP_GOODS)->find($shop_goods_group['shop_goods_id']);

        // 资金订单信息
        $order = array(
            'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
            'order_type' => parent::TRANSACTION_TYPE_SHOP_ORDER_GROUP,
            'order_comment' => '商城拼团',
            'order_action_user_id' => $user_id,
            'order_plus_method' => '',
            'order_plus_account_id' => '',
            'order_plus_value' => $shop_order_group['shop_order_group_price'],
            'order_minus_method' => $input['pay_method'],
            'order_minus_account_id' => $user_id,
            'order_minus_value' => $shop_order_group['shop_order_group_price'],
            'order_state' => 1,
            'order_pay_state' => 0,
            'order_sign' => $shop_order_group['shop_order_group_id'],
            'order_json' => array(),
            'order_insert_time' => time(),
        );

        // 第三方支付
        if ($input['pay_method'] === parent::PAY_METHOD_WEIXINPAY || $input['pay_method'] === parent::PAY_METHOD_ALIPAY) {
            $output = object(parent::REQUEST_APPLICATION)->get_pay_config($input, $order['order_json'], array(
                'money_fen' => $shop_order_group['shop_order_group_price'],
                'subject' => $shop_goods['shop_goods_name'],
                'body' => $shop_goods['shop_goods_name'],
                'order_id' => $order['order_id']
            ));

            // 插入订单数据
            if (object(parent::TABLE_ORDER)->insert($order)) {
                $output['order_id'] = $order['order_id'];
                return $output;
            } else {
                throw new error ('操作失败');
            }
        }

        // 钱包支付
        object(parent::ERROR)->check($input, 'pay_password', parent::TABLE_USER, array('args'));

        // 检测支付密码
        $res = object(parent::TABLE_USER)->check_pay_password($input['pay_password']);
        if ($res !== true) {
            throw new error($res);
        }

        // 检测余额
        $res = object(parent::TABLE_USER_MONEY)->check_balance($user_id, $shop_goods_group['shop_goods_group_price']);
        if ($res !== true) {
            throw new error($res);
        }

        // 插入订单
        if (!object(parent::TABLE_ORDER)->insert($order)) {
            throw new error('资金订单登记失败');
        }
        // 支付
        if (!object(parent::TABLE_SHOP_ORDER_GROUP)->payment($order)) {
            throw new error('支付失败');
        }

        return 1;
    }


    //===========================================
    // 查询
    //===========================================


    /**
     * 列表
     * @author green
     *
     * api: SHOPORDERGROUPSELFLIST
     *
     * @param  array $input [请求参数]
     * @return array
     */
    public function api_self_list($input = array())
    {
        // 检测登录
        object(parent::REQUEST_USER)->check();
        $user_id = $_SESSION['user_id'];

        // 连表
        $joinon = array();
        // 左连拼团商品
        $joinon[] = array(
            'table' => 'shop_goods_group sgg',
            'type' => 'left',
            'on' => 'sog.shop_goods_group_id = sgg.shop_goods_group_id',
        );

        // 字段
        $select = array(
            'sog.shop_order_group_id',
            'sog.shop_goods',
            'sog.shop_order_group_number AS number',
            'sog.shop_order_group_price AS price',
            'sog.shop_order_group_pay AS pay',
            'sog.shop_order_group_state AS state',
            'sgg.shop_goods_group_people AS people',
            'sgg.shop_goods_group_people_now AS people_now',
        );

        // 筛选
        $where = array();
        $where[] = array('sog.user_id = [+]', $user_id);
        $where[] = array('sog.shop_order_group_delete = 0');
        $where[] = array('sog.shop_order_group_trash = 0');
        if (isset($input['search'])) {
            $search = $input['search'];

            // 状态
            if (isset($search['state']) && is_numeric($search['state'])) {
                $where[] = array('sog.shop_order_group_state = [-]', $search['state']);
            }
        }

        // 查询数据
        $config = array(
            'select' => $select,
            'joinon' => $joinon,
            'where' => $where,
            'orderby' => array(array('shop_order_group_insert_time', false)),
            'limit' => object(parent::REQUEST)->limit($input, parent::REQUEST_USER),
        );
        $data = object(parent::TABLE_SHOP_ORDER_GROUP)->select_paginate($config);

        // 格式化数据
        $list_data = array();
        foreach ($data['data'] as $i) {
            $i['shop_goods'] = cmd(array($i['shop_goods']), 'json decode_array');
            $i['pay'] = intval($i['pay']);
            $i['state'] = intval($i['state']);
            $i['people'] = intval($i['people']);
            $i['people_now'] = intval($i['people_now']);
            $list_data[] = $i;
        }

        $data['data'] = $list_data;
        return $data;
    }

    /**
     * 单个
     * @author green
     *
     * api: SHOPORDERGROUPSELFGET
     * req: {
     *  id [str] [必填] [拼团订单ID]
     * }
     *
     * @param  array $input [请求参数]
     * @return array
     */
    public function api_self_get($input = array())
    {
        // 检测登录
        object(parent::REQUEST_USER)->check();
        $user_id = $_SESSION['user_id'];

        // 检测请求参数
        object(parent::ERROR)->check($input, 'id', parent::TABLE_SHOP_ORDER_GROUP, array('args'), 'shop_order_group_id');

        // 查询拼团订单
        $shop_order_group = object(parent::TABLE_SHOP_ORDER_GROUP)->find($input['id']);

        $group_id = $group_id['shop_group_goods_id'];
        // 查询拼团是否结束、是否成功
        $is_end = object(parent::TABLE_SHOP_GROUP_GOODS)->group_is_end($group_id);

        // 查询配置
        $config = array(
            'select' => array(),
            'where' => array(),
        );

        // 查询字段
        $config['find'] = array(
            'gorder.shop_order_id AS order_id',
            'gorder.shop_group_goods_id AS group_id',
            'gorder.shop_group_order_price AS order_price',
            'gorder.shop_group_order_property AS property',
            'gorder.shop_group_order_state AS order_state',
            'gorder.shop_group_order_price AS pay_price',
            'gorder.shop_order_pay_method AS pay_method',
            'gorder.shop_group_order_pay_state AS pay_state',
            'gorder.shop_group_order_pay_time AS pay_time',
            'gorder.user_address_consignee AS consignee',
            'gorder.user_address_tel AS tel',
            'gorder.user_address_phone AS phone',
            'gorder.user_address_country AS country',
            'gorder.user_address_province AS province',
            'gorder.user_address_city AS city',
            'gorder.user_address_district AS district',
            'gorder.user_address_details AS details',
            'gg.shop_goods_id AS goods_id',
            'gg.shop_group_goods_start_time AS start_time',
            'gg.shop_group_goods_end_time as end_time',
            'gg.shop_group_goods_num AS num',
            'gg.shop_group_goods_now_num AS now_num',
        );

        $config['where'] = array(
            array("gorder.user_id=[+]",$user_id),
            array("[and] gorder.shop_group_order_id=[+]",$input['group_order_id']),
        );

        $result = object(parent::TABLE_SHOP_ORDER_GROUP)->find_join_group_goods($config);

        // 回收config
        unset($config);
        if(empty($result['goods_id'])){
            return $result;
        } else {
            $config['where'] = array(
                array('shop_goods_id=[+]',$result['goods_id']),
                array('[and] shop_goods_image_main=[+]',1)
            );
            $config['select'] = array('image_id');
            $img_data = object(parent::TABLE_SHOP_GOODS_IMAGE)->select($config);

            if(isset($img_data[0]['image_id']) && $img_data[0]['image_id'] !== null && $img_data[0]['image_id'] !== ''){
                $result['main_img'] = $img_data[0]['image_id'];
            }
            return $result;
        }
    }


}