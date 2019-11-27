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
class admin_group_order extends \eapie\source\request\shop {
	
	
	//团购订单
	
	
	
	
    /**
     * 拼团订单
     * api: SHOPADMINGROUPGOODSLISTORDER
     * {"class":"shop/admin_group_goods","method":"api_list_order"}
     * 
     * @param $request array
     * 
     * @return $data array
     */
    public function api_list_order($request = array()){

        // 查询配置
        $config = array(
           'orderby' => array(),
           'where' => array(),
           'limit' => object(parent::REQUEST)->limit($request, parent::REQUEST_ADMIN),
        );

        // 查询字段
        $config['select'] = array(
            'sgo.shop_group_order_id AS group_order_id',
            'sgg.shop_goods_id AS goods_id',
            'sgo.shop_order_id AS order_id',
            'sgo.shop_group_goods_id AS group_id',
            'sgo.shop_group_order_price AS order_price',
            'sgo.shop_group_order_property AS order_property',                // 订单类型。0普通商品订单，1是积分商品订单
            'sgo.shop_order_pay_price AS pay_price',
            'sgo.shop_order_pay_method AS pay_method',
            'sgo.shop_group_order_pay_state AS pay_state',                    // 状态。0未支付；1支付成功; 
            'sgo.shop_group_order_pay_time AS pay_time',
            'sgo.user_address_consignee AS consignee',
            'sgo.user_address_tel AS tel',
            'sgo.user_address_phone AS phone',
            'sgo.user_address_country AS country',
            'sgo.user_address_province AS province',
            'sgo.user_address_city AS city',
            'sgo.user_address_district AS district',
            'sgo.user_address_details AS details',
            'sgo.shop_group_order_status AS group_order_status',              // 拼团订单状态 0=>未关闭  1=>已关闭
            'sgo.shop_group_order_state as group_order_state',                // 拼团状态 0 => 时间未到，人数未满,不结束，不成功 1 => 时间到，人数未满，拼团结束，未拼团成功 2 => 时间到，人数已满，拼团结束，拼团成功 3 => 时间未到，拼团人数已满，拼团结束，拼团成功
            'sgo.user_id as user_id',
            'u.user_nickname as nickname',                                    // 用户昵称
            'up.user_phone_id as phone',                                      // 手机号
        );

        
        // 避免排序重复
        $config["orderby"][] = array('shop_order_id', false);

        // 查询数据
        $data = object(parent::TABLE_SHOP_GROUP_ORDER)->select_page($config);
        return $data;
     }

    /**
     * 单个拼团商品订单查询，需要登录
     *
     * api: SHOPADMINGROUPGOODSGETORDER
     * {"class":"shop/admin_group_goods","method":"api_get_order"}
     * 
     * @param array $data
     * 
     * @return array $result
     */
    public function api_get_order($data = array()){
        //检测登录
        object(parent::REQUEST_USER)->check();

        //检测数据
        object(parent::ERROR)->check($data, 'group_order_id', parent::TABLE_SHOP_GROUP_ORDER, array('args','exist'));

        //查询拼团ID
        $group_id = object(parent::TABLE_SHOP_GROUP_ORDER)->find_group($data['group_order_id']);

        $group_id = $group_id['shop_group_goods_id'];
        
        //查询拼团是否结束、是否成功
        $is_end = object(parent::TABLE_SHOP_GROUP_GOODS)->group_is_end($group_id);

        //查询配置
        $config = array(
            'select' => array(),
            'where' => array(),
        );

        //查询字段
        $config['find'] = array(
            'gorder.shop_group_order_id AS group_order_id',
            'gorder.shop_order_id AS order_id',
            'gorder.shop_group_goods_id AS group_id',
            'gorder.shop_group_order_price AS order_price',
            'gorder.shop_group_order_state AS order_state',
            'gorder.shop_order_pay_price AS pay_price',
            'gorder.shop_group_order_property AS order_property',
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
            "shop_group_order_id=[+]",$data['group_order_id'],
        );

        $result = object(parent::TABLE_SHOP_GROUP_ORDER)->find_join_group_goods($config);
        return $result;
     }

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>