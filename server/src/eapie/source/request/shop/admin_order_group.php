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
class admin_order_group extends \eapie\source\request\shop
{

    /**
     * 列表
     * api: SHOPADMINORDERGROUPLIST
     * @author green
     *
     * @return array
     */
    public function api_list($input = array())
    {
        // 连表
        $joinon = array();
        $joinon[] = array(
            'table' => 'user u',
            'type' => 'left',
            'on' => 'u.user_id = sog.user_id',
        );
        $joinon[] = array(
            'table' => 'user_phone up',
            'type' => 'left',
            'on' => 'up.user_id = sog.user_id',
        );

        // 字段
        $select = array(
            'sog.user_id',
            'sog.shop_order_group_id',
            'sog.shop_order_id',
            'sog.shop_goods_group_id',
            'sog.shop_order_group_price',
            'sog.shop_order_group_pay_method',
            'sog.shop_order_group_pay',
            'sog.shop_order_group_pay_time',
            'sog.user_address_consignee',
            'sog.user_address_phone',
            'sog.shop_order_group_state',
            'sog.shop_order_group_insert_time',
            
            'sog.shop_order_group_refund_state',
            'sog.shop_order_group_refund_time',
            'sog.shop_order_group_refund_order',
            
            'u.user_nickname as nickname',
            'up.user_phone_id as phone',
        );

        // 条件
        $where = array();

        // 排序
        $orderby = array(
            array('sog.shop_order_group_pay_time', true),
            array('sog.shop_order_group_insert_time', true),
        );

        // 查询数据
        $config = array(
            'select' => $select,
            'joinon' => $joinon,
            'where' => $where,
            'orderby' => $orderby,
            'limit' => object(parent::REQUEST)->limit($input, parent::REQUEST_ADMIN),
        );
        $data = object(parent::TABLE_SHOP_ORDER_GROUP)->select_paginate($config);
        return $data;
    }

}