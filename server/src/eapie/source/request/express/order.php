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




namespace eapie\source\request\express;

use eapie\main;
use eapie\error;

class order extends \eapie\source\request\express
{
    /**
     * 用户下单
     * Undocumented function
     *
     * api: EXPRESSORDERSELFADD
     * 
     * {"class":"express/order","method":"api_self_add"}
     * req:{
     * }
     * @param array $input
     * @return void
     */
    public function api_self_add($input = [])
    {
        //检测登录
        object(parent::REQUEST_USER)->check();
        // return $input;
        //实名认证
        $user_identity = object(parent::TABLE_USER_IDENTITY)->find($_SESSION['user_id']);
        if (
            empty($user_identity) ||
            $user_identity['user_identity_state'] != 1 ||
            !object(parent::TABLE_USER_IDENTITY)->check_state($_SESSION['user_id'], $user_identity['user_identity_update_time'])
        )
            throw new error('未实名认证，请在个人中心完成实名认证');

        //验证未支付订单数量
        $where = array(
            array("user_id =[+]", $_SESSION['user_id']),
            array("express_order_pay_state=0"),
            array("express_order_delete_state=0"),
            array("express_order_trash=0"),
            array("express_order_state<>0")
        );
        $count_data = object(parent::TABLE_EXPRESS_ORDER)->get_count($where);

        //获取订单配置
        $config_order_info = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("express_order"), true);
        $config_order_num = isset($config_order_info['no_pay_num']) && (int) $config_order_info['pay_num'] >= 0 ? $config_order_info['pay_num'] : 3;  //允许最大未支付订单数量


        if (!empty($count_data) && $count_data['count'] >= $config_order_num) {
            throw new error('您有多个未支付订单，请处理');
        }

        //验证输入
        object(parent::ERROR)->check($input, 'id', parent::TABLE_EXPRESS_ORDER, array('args'), 'user_address_id');
        object(parent::ERROR)->check($input, 'type', parent::TABLE_EXPRESS_ORDER, array('args'), 'express_order_type');
        object(parent::ERROR)->check($input, 'shipping', parent::TABLE_EXPRESS_ORDER, array('args'), 'get_shipping');
        object(parent::ERROR)->check($input, 'province', parent::TABLE_EXPRESS_ORDER, array('args'), 'get_province');
        object(parent::ERROR)->check($input, 'city', parent::TABLE_EXPRESS_ORDER, array('args'), 'get_city');
        object(parent::ERROR)->check($input, 'district', parent::TABLE_EXPRESS_ORDER, array('args'), 'get_district');
        object(parent::ERROR)->check($input, 'name', parent::TABLE_EXPRESS_ORDER, array('args'), 'get_name');
        object(parent::ERROR)->check($input, 'phone', parent::TABLE_EXPRESS_ORDER, array('args'), 'get_phone');
        object(parent::ERROR)->check($input, 'details', parent::TABLE_EXPRESS_ORDER, array('args'), 'get_details');
        object(parent::ERROR)->check($input, 'pay_type', parent::TABLE_EXPRESS_ORDER, array('args'), 'pay_type');

        $address_info = object(parent::TABLE_USER_ADDRESS)->find($input['id']);

        // return $address_info;


        $shipping = object(parent::TABLE_SHIPPING)->find($input['shipping']);
        $sending_type = object(parent::TABLE_TYPE)->find($input['type']);
        if (empty($address_info) || empty($shipping) || empty($sending_type))
            throw new error('地址参数错误');

        $time = time();
        $agent_id = '';

        $agent = object(parent::TABLE_USER)->find_join_parent($_SESSION['user_id']);
        if ($agent && isset($agent['user_parent_id']) && $agent['user_parent_id']) {
            $agent_id = $agent['user_parent_id'];
        }
        $express_order = array(
            'express_order_id' => object(parent::TABLE_EXPRESS_ORDER)->get_unique_id(),
            'user_id' => $_SESSION['user_id'],
            'user_parent_id' => $agent_id,
            'express_order_type' => $sending_type['type_id'], //寄件类型
            'express_order_json' => cmd(array(
                'send_user_info' => array(
                    'name' => $address_info['user_address_consignee'],
                    'phone' => $address_info['user_address_phone'],
                    'province' => $address_info['user_address_province'],
                    'city' => $address_info['user_address_city'],
                    'district' => $address_info['user_address_district'],
                    'details' => $address_info['user_address_details'],
                    'send_type' => $sending_type['type_name']
                ),
                'rider_info' => [],
                'get_user_info' => array(
                    'name' => $input['name'],
                    'phone' => $input['phone'],
                    'province' => $input['province'],
                    'city' => $input['city'],
                    'district' => $input['district'],
                    'details' => $input['details']
                )
            ), 'json decode'),
            'shipping_id' => $shipping['shipping_id'],
            'shipping_sign' => $shipping['shipping_sign'],
            'shipping_name' => $shipping['shipping_name'],
            'user_address_id' => $address_info['user_address_id'],
            'user_address_name' => $address_info['user_address_consignee'],
            'user_address_phone' => $address_info['user_address_phone'],
            'user_address_province' => $address_info['user_address_province'],
            'user_address_city' => $address_info['user_address_city'],
            'user_address_district' => $address_info['user_address_district'],
            'user_address_details' => $address_info['user_address_details'],
            'express_order_get_name' => $input['name'],
            'express_order_get_phone' => $input['phone'],
            'express_order_get_province' => $input['province'],
            'express_order_get_city' => $input['city'],
            'express_order_get_district' => $input['district'],
            'express_order_get_address' => $input['details'],
            'express_order_insert_time' => $time,
            'express_order_update_time' => $time,
            'express_order_insured_state' => isset($input['insured']) && $input['insured'] == 1 ? 1 : 0,
            'express_order_receive_start' => isset($input['start']) && $input['start'] ? $input['start'] : 0,
            'express_order_receive_end' => isset($input['end']) && $input['end'] ? $input['end'] : 0,
            'express_order_pay_type' => isset($input['pay_type']) && in_array($input['pay_type'], [0, 1]) ? $input['pay_type'] : 0
        );


        // return $express_order;
        if (object(parent::TABLE_EXPRESS_ORDER)->insert($express_order)) {
            
            //获取寄件地区所有骑手信息
            $rider_info_config['where'] = array(
                array('express_rider_province =[+]', $address_info['user_address_province']),
                array('[and] express_rider_city =[+]', $address_info['user_address_city']),
                array('[and] express_rider_district =[+]', $address_info['user_address_district']),
                array('[and] express_rider_on_off=1'),
                array('[and] express_rider_state=1')
            );
            $rider_info = object(parent::TABLE_EXPRESS_RIDER)->select($rider_info_config);
            // return $rider_info;
            //判断是否存在骑手，发送下单消息
            if (!empty($rider_info)) {
                $pick_up_time = date('Y-m-d', time());
                if (!empty($input['start']) && !empty($input['end'])) {
                    $times = date('Y-m-d', time());
                    $pick_up_time = $times . 'T' . $input['start'] . '--' . $input['end'];
                }
                foreach ($rider_info as $k) {
                    // return $k;
                    //获取骑手授权信息
                    $rider_user_oauth = object(parent::TABLE_USER_OAUTH)->find($k['user_id']);
                    if (!empty($rider_user_oauth['user_oauth_wx_key'])) {
                        // return $rider_user_oauth;
                        //发送新订单消息给骑手
                        $data_message = array(
                            "touser" => $rider_user_oauth['user_oauth_wx_key'], //微信登录--用户openID
                            "template_id" => "OKxTqBDyPdTL_COXfvSeuXD0-sFVC6wemsGsVkkGwMY", //模板ID
                            "url" => "http://mp.jiangyoukuaidi.eonfox.com", //点击跳转链接地址  
                            "topcolor" => "#FF0000",
                            "data" => array(
                                "first" => array(
                                    "value" => "取件员，有新的寄件单，请尽快处理！",
                                    "color" => "#173177"
                                ),
                                "keyword1" => array(
                                    "value" => $sending_type['type_name'],
                                    "color" => "#173177"
                                ),
                                "keyword2" => array(
                                    "value" => $address_info['user_address_province'] . '-' . $address_info['user_address_city'] . '-' . $address_info['user_address_district'] . '-' . $address_info['user_address_details'],
                                    "color" => "#173177"
                                ),
                                "keyword3" => array(
                                    "value" => $pick_up_time,
                                    "color" => "#173177"
                                ),
                                "keyword4" => array(
                                    "value" => $address_info['user_address_consignee'],
                                    "color" => "#173177"
                                ),
                                "keyword5" => array(
                                    "value" => $address_info['user_address_phone'],
                                    "color" => "#173177"
                                ),
                                "remark" => array(
                                    "value" => "请尽快取件",
                                    "color" => "#173177"
                                )
                            )
                        );
                        $data_message = cmd(array($data_message), 'json encode');
                        $time_out = 30;
                        $res = object(parent::PLUGIN_WEIXIN_MESSAGE_SEND)->template_message($data_message, $time_out);
                        return $res;
                    }
                }
            }

            return $express_order['express_order_id'];
        }
        throw new error('下单失败');
    }


    /**
     * 骑手接单
     * Undocumented function
     *
     * api: EXPRESSORDERSELFRECEIPT
     * 
     * {"class":"express/order","method":"api_self_receipt"}
     * 
     * @return void
     */
    public function api_self_receipt($input)
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //实名认证
        $user_identity = object(parent::TABLE_USER_IDENTITY)->find($_SESSION['user_id']);
        if (
            empty($user_identity) ||
            $user_identity['user_identity_state'] != 1 ||
            !object(parent::TABLE_USER_IDENTITY)->check_state($_SESSION['user_id'], $user_identity['user_identity_update_time'])
        )
            throw new error('未实名认证，请在个人中心完成实名认证');

        //数据检测
        object(parent::ERROR)->check($input, 'id', parent::TABLE_EXPRESS_ORDER, array('args'), 'express_order_id');

        //验证是否是骑手或骑手接单状态
        $express_rider = object(parent::TABLE_EXPRESS_RIDER)->find($_SESSION['user_id']);
        if (empty($express_rider)) {
            throw new error('抱歉，你没有此操作权限');
        }
        if ($express_rider['express_rider_on_off'] != 1) {
            throw new error('请在个人中心，开启接单状态');
        }

        $where = array(
            array('express_order_id =[+]', $input['id']),
            array('express_order_delete_state = 0'),
            array('express_order_trash = 0'),
            array('express_order_pay_state = 0'),
            array('express_order_state = 2')
        );
        $express_order = object(parent::TABLE_EXPRESS_ORDER)->find_where($where);
        if (empty($express_order))
            throw new error('未找到相关支付订单，或订单信息异常');

        $update_data = array(
            'express_order_state' => 3, //已接单
            'express_rider_user_id' => $express_rider['user_id'],
            'express_order_access_time' => time()
        );

        if (object(parent::TABLE_EXPRESS_ORDER)->update_one($express_order['express_order_id'], $update_data)) {
            return ['order_id' => $express_order['express_order_id']];
        } else {
            throw new error('接单失败');
        }
    }

    /**
     * 骑手取件  并设置取件价格
     * 
     * api: EXPRESSORDERSELFACCESS
     * {"class":"express/order","method":"api_self_access"}
     * 
     * @param array $input
     * @return void
     */
    public function api_self_access($input = [])
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //验证是否是骑手或骑手接单状态
        $express_rider = object(parent::TABLE_EXPRESS_RIDER)->find($_SESSION['user_id']);
        if (empty($express_rider)) {
            throw new error('抱歉，你没有此操作权限');
        }
        if ($express_rider['express_rider_on_off'] != 1) {
            throw new error('请在个人中心，开启接单状态');
        }

        object(parent::ERROR)->check($input, 'id', parent::TABLE_EXPRESS_ORDER, array('args'), 'express_order_id');
        object(parent::ERROR)->check($input, 'type', parent::TABLE_EXPRESS_ORDER, array('args'), 'express_send_type');
        object(parent::ERROR)->check($input, 'money', parent::TABLE_EXPRESS_ORDER, array('args'), 'express_order_money');

        if (isset($input['coupon_forbidden'])) {
            object(parent::ERROR)->check($input, 'coupon_forbidden', parent::TABLE_EXPRESS_ORDER, array('args'), 'express_order_coupon_forbidden_state');
        }

        if ($input['money'] < 1 || !is_int((int) $input['money']))
            throw new error('订单金额不合法');
        $where = array(
            array('express_order_id =[+]', $input['id']),
            array('express_order_pay_state =[+]', 0),
            array('express_order_delete_state =[+]', 0),
            array('express_order_trash =[+]', 0)
        );
        $express_order = object(parent::TABLE_EXPRESS_ORDER)->find_where($where);
        if (empty($express_order))
            throw new error('未找到相关订单信息');
        if ($express_order['express_rider_user_id'] != $_SESSION['user_id'] && $express_order['express_order_state'] == 4) {
            throw new error('当前订单已被骑手取件');
        }

        $data = array();
        $data['express_order_json'] = cmd(array($express_order['express_order_json']), 'json decode');
        if (isset($input['type'])) {
            $sending_type = object(parent::TABLE_TYPE)->find($input['type']);
            if (empty($sending_type))
                throw new error('寄件类型错误');
            $data['express_order_type'] = $sending_type['type_id'];
            if (empty($data['express_order_json'])) {
                $data['express_order_json'] = array();
            }
            if (empty($data['express_order_json']['send_user_info'])) {
                $data['express_order_json']['send_user_info'] = array();
            }
            $data['express_order_json']['send_user_info']['send_type'] = $sending_type['type_name'];
        }
        if (isset($input['coupon_forbidden']) && is_numeric($input['coupon_forbidden']) && $input['coupon_forbidden'] == 1) {
            $data['express_order_coupon_forbidden_state'] = 1;
        }
        $time = time();
        $data['express_order_json']['rider_user_info'] = $express_rider;
        $data['express_rider_user_id'] = $express_rider['user_id'];
        $data['express_rider_phone'] = $express_rider['express_rider_phone'];
        $data['express_order_money'] = $input['money'];
        $data['express_order_update_time'] = $time;
        $data['express_order_state'] = 4;
        $data['express_order_access_time'] = $time;
        $data['express_order_json'] = cmd(array($data['express_order_json']), 'json encode');
        $data['express_order_pick_time'] = $time;

        if (object(parent::TABLE_EXPRESS_ORDER)->update_one($express_order['express_order_id'], $data)) {
            return ['order_id' => $express_order['express_order_id']];
        }
        throw new error('骑手取件失败');
    }

    /**
     * 获取会员折扣
     * Undocumented function
     *
     * api: EXPRESSORDERSELFSCALE
     * {"class":"express/order","method":"api_self_scale"}
     * 
     * @return void
     */
    public function api_self_scale()
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        $time = time();

        //  会员折扣配置
        $user_send_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("express_order"), true);


        //  代理折扣配置
        $agent_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("agent_user"), true);


        $agent_scale_limit = isset($agent_config['agent_scale_limit']) && is_numeric((int) $agent_config['agent_scale_limit']) ? (int) $agent_config['agent_scale_limit'] : 20;
        $agent_user_scale = isset($agent_config['agent_user_scale']) && (float) $agent_config['agent_user_scale'] <= 1 ? (float) $agent_config['agent_user_scale'] : 0.85;
        $user_son_count = object(parent::TABLE_USER)->find_son_count($_SESSION['user_id']);

        if (empty($user_son_count['count']) || $user_son_count['count'] < $agent_scale_limit) {
            //  邀请人数小于代理折扣限制人数
            $vip_date = isset($user_send_config['vip_date']) && $user_send_config['vip_date'] ? $user_send_config['vip_date'] : '08,18,28';
            $vip_scale = isset($user_send_config['vip_scale']) && (float) $user_send_config['vip_scale'] ? (float) $user_send_config['vip_scale'] : 0.9;
            $vip_date_scale = isset($user_send_config['vip_date_scale']) && (float) $user_send_config['vip_date_scale'] ? (float) $user_send_config['vip_date_scale'] : 0.8;

            $date = date('d', $time);
            $dates = explode(',', $vip_date);
            if (in_array($date, $dates)) {
                $vip_scale = $vip_date_scale;
            }
        } else {

            $vip_scale = $agent_user_scale;
        }

        $pay_type = isset($user_send_config['pay_type']) && in_array($user_send_config['pay_type'], [0, 1]) ? $user_send_config['pay_type'] : 0;

        return ['vip_scale' => $vip_scale, 'pay_type' => $pay_type];
    }


    /**
     * 删除订单
     * Undocumented function
     *  
     * api: EXPRESSORDERSELFREVOKE
     * {"class":"express/order","method":"api_self_revoke"}
     * 
     * @param array $input
     * @return void
     */
    public function api_self_revoke($input = array())
    {

        //检测登录
        object(parent::REQUEST_USER)->check();

        //验证参数
        object(parent::ERROR)->check($input, 'id', parent::TABLE_EXPRESS_ORDER, array('args'), 'express_order_id');

        $where = array(
            array('express_order_id =[+]', $input['id']),
            array('user_id =[+]', $_SESSION['user_id']),
            array('express_order_delete_state =[+]', 0),
            array('express_order_trash =[+]', 0),
            array('express_order_pay_state =[+]', 0),
        );
        $express_order = object(parent::TABLE_EXPRESS_ORDER)->find_where($where);
        // printexit($express_order);
        if (empty($express_order))
            throw new error('未找到相关订单，或订单不允许撤销');

        $data = array();

        // $data['express_order_delete_time'] = time();
        $data['express_order_update_time'] = time();
        $data['express_order_state'] = 0;   //被撤销
        if (object(parent::TABLE_EXPRESS_ORDER)->update_one($express_order['express_order_id'], $data)) {
            return ['order_id' => $express_order['express_order_id']];
        }
        throw new error('撤销失败');
    }


    /**
     * 删除订单
     * Undocumented function
     *  
     * api: EXPRESSORDERSELFREMOVE
     * {"class":"express/order","method":"api_self_remove"}
     * 
     * @param array $input
     * @return void
     */
    public function api_self_remove($input = array())
    {

        //检测登录
        object(parent::REQUEST_USER)->check();

        //验证参数
        object(parent::ERROR)->check($input, 'id', parent::TABLE_EXPRESS_ORDER, array('args'), 'express_order_id');

        $where = array(
            array('express_order_id =[+]', $input['id']),
            array('user_id =[+]', $_SESSION['user_id']),
            array('express_order_delete_state =[+]', 0),
            array('express_order_trash =[+]', 0),
        );
        $express_order = object(parent::TABLE_EXPRESS_ORDER)->find_where($where);
        if (empty($express_order))
            throw new error('未找到相关订单，或订单不允许删除');

        $data = array();
        if ($express_order['express_order_pay_state'] == 0) {
            if (isset($input['msg'])) {
                $data['express_order_delete_msg'] = $input['msg'];
            }
        } else {
            if ($express_order['express_order_shipping_state'] != 1)
                throw new error('当前订单状态不允许删除');
        }

        $data['express_order_delete_time'] = time();
        $data['express_order_delete_state'] = 1;
        if (object(parent::TABLE_EXPRESS_ORDER)->update_one($express_order['express_order_id'], $data)) {
            return ['order_id' => $express_order['express_order_id']];
        }
        throw new error('删除失败');
    }

    /**
     * 获取订单列表
     * Undocumented function
     * api: EXPRESSORDERSELFLIST
     * {"class":"express/order","method":"api_self_list"}
     * @param array $input
     * @return void
     */
    public function api_self_list($input = [])
    {

        //检测登录
        object(parent::REQUEST_USER)->check();

        //查询配置
        $config = [
            'orderby' => array(
                array('insert_time', false),
            ),
            'where' => array(
                array("express_order_trash = 0"),
                array("express_order_delete_state = 0"),
            ),
            'limit' => object(parent::REQUEST)->limit($input, parent::REQUEST_USER),
            'select' => array()
        ];
        if (!isset($input['states'])) {
            throw new error('参数错误');
        }
        // return $input;
        $config['select'] = array(
            "eo.express_order_id AS id",
            "eo.user_id",
            "eo.user_coupon_id AS coupon",
            "eo.express_order_type AS send_type",
            "eo.express_order_rebate_money AS rebate_money",
            "eo.express_order_money AS money",
            "eo.shipping_sign AS express_sign",
            "eo.express_order_json AS express_info",
            "eo.shipping_id",
            "eo.express_order_shipping_no AS shipping_no",
            "eo.express_order_shipping_state AS shipping_state",
            "eo.express_order_shipping_send_time AS send_time",
            //"eo.express_order_shipping_take_time AS take_time",
            "FROM_UNIXTIME(eo.express_order_shipping_take_time,'%Y-%m-%d %T') AS take_time",
            "eo.shipping_name",
            "eo.express_rider_user_id AS rider_id",
            "eo.express_rider_phone AS rider_phone",
            "eo.user_address_id AS address_id",
            "eo.user_address_name AS send_name",
            "eo.user_address_phone AS send_phone",
            "eo.user_address_province AS send_province",
            "eo.user_address_city AS send_city",
            "eo.user_address_district AS send_district",
            "eo.user_address_details AS send_details",
            "eo.express_order_get_name AS get_name",
            "eo.express_order_get_phone AS get_phone",
            "eo.express_order_get_province AS get_province",
            "eo.express_order_get_city AS gey_city",
            "eo.express_order_get_district AS get_district",
            "eo.express_order_get_address AS get_details",
            "eo.express_order_pay_method AS pay_method",
            "eo.express_order_pay_money AS pay_money",
            "eo.express_order_pay_state AS pay_state",
            "eo.express_order_pay_time AS pay_time",
            "eo.express_order_agent_royalty AS royalty",
            "eo.express_order_insert_time AS insert_time",
            "FROM_UNIXTIME(eo.express_order_insert_time,'%Y-%m-%d %T') AS create_time",
            "eo.express_order_insured_state AS insured_state",
            "eo.express_order_receive_start AS receive_start",
            "eo.express_order_receive_end AS receive_end",
            "eo.express_order_state AS state",
            "FROM_UNIXTIME(eo.express_order_access_time,'%Y-%m-%d %T') AS access_time",
            "eo.express_order_access_time",
            "eo.express_order_coupon_forbidden_state AS coupon_forbidden_state"
        );
        switch ($input['states']) {
            case 1: //个人订单
                array_push(
                    $config['where'],
                    array("eo.user_id =[]", $_SESSION['user_id'])
                );
                if (isset($input['state'])) {
                    switch ($input['state']) {
                        case 1: //待支付
                            array_push(
                                $config['where'],
                                array("express_order_pay_state =[]", 0),
                                array('express_order_state <>[]', 0)
                            );
                            break;
                        case 2: //在路上
                            array_push(
                                $config['where'],
                                array('(express_order_pay_state = 1 AND express_order_shipping_state <> 1 AND express_order_state <> 0)')
                            );
                            break;
                        case 3: //已完成
                            array_push(
                                $config['where'],
                                array('(express_order_pay_state = 1 AND express_order_shipping_state = 1)')
                            );
                            break;
                        default:   //待支付
                            array_push(
                                $config['where'],
                                array("express_order_pay_state =[]", 0),
                                array('express_order_state <>[]', 0)
                            );
                    }
                } else {
                    array_push(
                        $config['where'],
                        array("express_order_pay_state =[]", 0),
                        array('express_order_state <>[]', 0)
                    );
                }
                break;
            case 2: //历史接单
                $express_rider = object(parent::TABLE_EXPRESS_RIDER)->find($_SESSION['user_id']);
                if (empty($express_rider)) {
                    throw new error('抱歉，你没有此操作权限');
                }

                array_push(
                    $config['where'],
                    array('express_rider_user_id =[]', $_SESSION['user_id']),
                    array('express_order_pay_state =1')
                );
                break;
            case 3: //骑手接单
                $express_rider = object(parent::TABLE_EXPRESS_RIDER)->find($_SESSION['user_id']);
                if (empty($express_rider)) {
                    throw new error('抱歉，你没有此操作权限');
                }

                array_push(
                    $config['where'],
                    array('express_order_pay_state = 0'),
                    array('((user_address_province ="' . $express_rider['express_rider_province'] . '" AND user_address_city ="' . $express_rider['express_rider_city'] . '" AND user_address_district ="' . $express_rider['express_rider_district'] . '" AND express_order_state=2 ) OR (express_rider_user_id ="' . $_SESSION['user_id'] . '" AND express_order_state in (3,4) AND express_order_shipping_state = 0 )) ')
                );
                break;
            default:
                throw new error('参数错误');
        }

        $data = object(parent::TABLE_EXPRESS_ORDER)->select_page($config);

        return $data;
    }


    /**
     * 订单详情
     * Undocumented function
     * 
     * api: EXPRESSORDERSELFONE
     * {"class":"express/order","method":"api_self_one"}
     * 
     * @param array $input
     * @return void
     */
    public function api_self_one($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //验证参数
        object(parent::ERROR)->check($input, 'id', parent::TABLE_EXPRESS_ORDER, array('args'), 'express_order_id');



        $select = array(
            "express_order_id AS id",
            "user_id AS uid",
            "user_coupon_id AS coupon",
            "express_order_type AS send_type",
            "express_order_rebate_money AS rebate_money",
            "express_order_money AS money",
            "shipping_sign AS express_sign",
            "express_order_json AS express_info",
            "shipping_id",
            "express_order_shipping_no AS shipping_no",
            "express_order_shipping_state AS shipping_state",
            "express_order_shipping_send_time AS send_time",
            "express_order_shipping_take_time AS take_time",
            "shipping_name",
            "express_rider_user_id AS rider_id",
            "express_rider_phone AS rider_phone",
            "user_address_id AS address_id",
            "user_address_name AS send_name",
            "user_address_phone AS send_phone",
            "user_address_province AS send_province",
            "user_address_city AS send_city",
            "user_address_district AS send_district",
            "user_address_details AS send_details",
            "express_order_get_name AS get_name",
            "express_order_get_phone AS get_phone",
            "express_order_get_province AS get_province",
            "express_order_get_city AS gey_city",
            "express_order_get_district AS get_district",
            "express_order_get_address AS get_details",
            "express_order_pay_method AS pay_method",
            "express_order_pay_money AS pay_money",
            "express_order_pay_state AS pay_state",
            "express_order_pay_time AS pay_time",
            "express_order_agent_royalty AS royalty",
            "express_order_insert_time AS insert_time",
            "FROM_UNIXTIME(express_order_insert_time,'%Y-%m-%d %T') AS create_time",
            "express_order_insured_state AS insured_state",
            "express_order_receive_start AS receive_start",
            "express_order_receive_end AS receive_end",
            "express_order_state AS state",
            "FROM_UNIXTIME(express_order_access_time,'%Y-%m-%d %T') AS access_time",
            "express_order_access_time AS ac_time",
            "express_order_coupon_forbidden_state AS coupon_forbidden_state"
        );

        //订单删除、回收的是不能查询出来的。
        $where = array();
        $where[] = array('express_order_id =[+]', $input['id']);
        //是否已回收。0正常；1已回收
        $where[] = array('[and] express_order_trash=0');
        //用户删除状态。0未删，1已删
        $where[] = array('[and] express_order_delete_state=0');
        //用户ID
        // $where[] = array('user_id = [+]', $_SESSION['user_id']);

        $data = object(parent::TABLE_EXPRESS_ORDER)->find_where($where, $select);
        if (!$data) {
            throw new error('订单缺失');
        }



        // 判断订单状态，不是未发货则查询快递100
        if ($data['state'] != 0 && $data['shipping_state'] != 0 && !empty($data['shipping_no'])) {
            $kd100_msg = object(parent::TABLE_EXPRESS_ORDER)->get_kuaidi100_messgae($data);
            if ($kd100_msg['errno'] == 0) {
                $res = cmd(array($kd100_msg['data']), 'json decode');
            }
        }

        // 如果快递100返回的状态值是签收（3）或者退回（6）
        if (isset($res['state']) && in_array((int) $res['state'], array(3, 6))) {
            // 更新订单物流状态为“确认收货（1）”
            $express_order_shipping_take_time = time();

            $res_data = $res['data'][0];
            if (!empty($res_data['time'])) {
                $express_order_shipping_take_time = cmd(array($res_data['time']), "time mktime");
            }

            object(parent::TABLE_EXPRESS_ORDER)->update_one(
                $data['id'],
                array('express_order_shipping_state' => 1, 'express_order_shipping_take_time' => $express_order_shipping_take_time, 'express_order_state' => 1)
            );
            // 修改订单详情物流状态
            $data['shipping_state'] = 1;
        }



        if ($data) {
            $data['express_info'] = cmd(array($data['express_info']), 'json decode');
            if ($data['pay_state'] == 0) {
                $shippings = object(parent::TABLE_SHIPPING)->find($data['shipping_id']);
                if (!empty($shippings) && $shippings['shipping_default'] == 1) {
                    $data['defaults'] = 1;
                } else {
                    $data['defaults'] = 0;
                }
            }
            return $data;
        }
        throw new error('订单缺失');
    }

    /**
     * 获取下单时间段
     * Undocumented function
     *
     * api: EXPRESSORDERGETTIME
     * {"class":"express/order","method":"api_get_time"}
     * @return void
     */
    public function api_get_time()
    {
        $data = array(
            array('start' => '08:00', 'end' => '10:00'),
            array('start' => '10:00', 'end' => '12:00'),
            array('start' => '12:00', 'end' => '14:00'),
            array('start' => '14:00', 'end' => '16:00'),
            array('start' => '16:00', 'end' => '18:00'),
            array('start' => '18:00', 'end' => '20:00'),
            array('start' => '06:00', 'end' => '22:00')
        );
        return $data;
    }


    /**
     * 代理获取提成信息
     * Undocumented function
     *
     * api: EXPRESSORDERSELFAGENTROYAL
     * {"class":"express/order","method":"api_self_agent_royal"}
     * 
     * @return void
     */
    public function api_self_agent_royal($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        $select = array(
            "user_id AS uid",
            "user_coupon_id AS coupon",
            "express_order_money AS money",
            "user_address_name AS send_name",
            "user_address_phone AS send_phone",
            "express_order_agent_royalty AS royalty",
            "express_order_insert_time AS insert_time",
            "FROM_UNIXTIME(express_order_insert_time,'%Y-%m-%d %T') AS create_time",
        );

        //查询配置
        $config = [
            'orderby' => array(
                array('insert_time', false),
            ),
            'where' => array(
                array('eo.user_parent_id =[+]', $_SESSION['user_id']),
                array('express_order_pay_state=1'),
                array('express_order_agent_royalty>0'),
                array('express_order_trash=0')
            ),
            'limit' => object(parent::REQUEST)->limit($input, parent::REQUEST_USER),
            'select' => array()
        ];

        $config['select'] = $select;

        $data = object(parent::TABLE_EXPRESS_ORDER)->select_page($config);

        return $data;
    }

    /**
     * 获取代理无效提成
     * Undocumented function
     * 
     * api: EXPRESSORDERSELFINVALIDROYAL
     * 
     * {"class":"express/order","method":"api_self_invalid_royal"}
     * 
     * @return void
     */
    public function api_self_invalid_royal($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        $where = array(
            array('user_parent_id =[+]', $_SESSION['user_id']),
            array('user_coupon_id =[+]', ''),
            array('express_order_pay_state=1'),
            array('express_order_agent_royalty>0'),
            array('express_order_trash=0')
        );

        $field = 'express_order_agent_royalty';
        $data = object(parent::TABLE_EXPRESS_ORDER)->find_count($where, $field);
        return $data;
    }










    /**
     * 用户支付--统一下单
     *
     * api: EXPRESSORDERSELFCREATE
     * {"class":"express/order","method":"api_self_create"}
     * 
     * req:{
     *     "id":"订单id",
     *      "discount_id":"使用优惠券时，优惠券ID",
     *      "pay_method":"weixinpay/alipay",
     *      "weixin_login_code":"微信支付是需要，用于换取用户OpenID",
     *      "weixin_trade_type":"微信的交易类型，必须是大写，如JSAPI|APP|MWEB|NATIVE"
     * }
     * 
     * @param array $input
     * @return void
     */
    public function api_self_create($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        object(parent::ERROR)->check($input, 'id', parent::TABLE_EXPRESS_ORDER, array('args'), 'express_order_id');
        object(parent::ERROR)->check($input, 'discount_id', parent::TABLE_EXPRESS_ORDER, array('args'), 'discount_id');
        object(parent::ERROR)->check($input, 'pay_method', parent::TABLE_EXPRESS_ORDER, array('args'), 'pay_method');
        object(parent::ERROR)->check($input, 'weixin_login_code', parent::TABLE_EXPRESS_ORDER, array('args'), 'weixin_login_code');
        object(parent::ERROR)->check($input, 'weixin_trade_type', parent::TABLE_EXPRESS_ORDER, array('args'), 'weixin_trade_type');

        $param = array(
            "id" => $input['id'],
            'discount_id' => $input['discount_id']
        );

        $data = $this->_compute_order($param);

        // 资金订单表要新增的数据
        $order_insert_data = $data["order_insert_data"];

        // 资金订单表的支付方式
        $order_insert_data['order_minus_method'] = $input["pay_method"];

        // 业务订单表要更新的数据
        $express_order_update_data = $data["express_order_update_data"];

        // 实际需要支付的money
        $pay_money = $express_order_update_data['express_order_pay_money'];

        //事务处理，开始锁用户的快递订单数据  (定义常量)
        $order_lock_id = object(parent::TABLE_LOCK)->start("express_order_id", $input["id"], parent::LOCK_PAY_STATE);

        if (empty($order_lock_id)) {
            throw new error('支付失败');
        }

        $lock_ids[] = $order_lock_id;


        if (!is_array($order_insert_data["order_json"])) {
            $order_insert_data["order_json"] = array();
        }


        //获取支付配置
        $return_api = object(parent::REQUEST_APPLICATION)->get_pay_config($input, $order_insert_data["order_json"], array(
            "money_fen" => $pay_money,
            "subject" => "用户寄件-下单支付",
            "body" => "商家寄件支付",
            "order_id" => $order_insert_data['order_id']
        ));

        $return_api['order_id'] = $order_insert_data['order_id'];


        if ($pay_money > 0 && $input['pay_method'] != parent::PAY_METHOD_ALIPAY && $input['pay_method'] != parent::PAY_METHOD_WEIXINPAY) {
            throw new error('支付方式错误');
        }

        // 转成字符串，才能更新到数据库
        if (!empty($order_insert_data["order_json"]) && is_array($order_insert_data["order_json"])) {
            $order_insert_data["order_json"] = cmd(array($order_insert_data["order_json"]), "json encode");
        }

        $bool = object(parent::TABLE_EXPRESS_ORDER)->create_order($order_insert_data, $input["id"], $express_order_update_data);
        object(parent::TABLE_LOCK)->close($lock_ids); //关闭锁

        if (!$bool) {
            throw new error('支付失败');
        } else {
            //支付金额--判断
            if ($pay_money > 0) {
                $return_api['state'] = 1;
            } else {
                $return_api['state'] = 0;
            }
            return $return_api;
        }
    }


    /**
     * 计算订单金额
     * 
     * api:EXPRESSORDERSELFMONEY
     * 
     * {"class":"express/order","method":"api_self_money"}
     * 
     * req:{
     *  "id":"订单ID",
     *  "discount_id":"使用优惠券时，优惠券ID"
     * }
     * 
     * @param   array   [业务订单ID，优惠券ID]
     * @return  
     */
    public function api_self_money($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        object(parent::ERROR)->check($input, 'id', parent::TABLE_EXPRESS_ORDER, array('args'), 'express_order_id');
        object(parent::ERROR)->check($input, 'discount_id', parent::TABLE_EXPRESS_ORDER, array('args'), 'discount_id');

        $param = array(
            "id" => $input['id'],
            'discount_id' => $input['discount_id']
        );

        $data = $this->_compute_order($param);

        // 业务订单表要更新的数据
        $express_order_update_data = $data["express_order_update_data"];

        // 实际需要支付的money
        $pay_money = $express_order_update_data['express_order_pay_money'];

        if ($pay_money < 0) {
            throw new error('订单数据不合法');
        }
        return $pay_money;
    }


    /**
     * 计算资金订单，业务订单
     *
     * @param   array   [业务订单ID，优惠券ID]
     * @return  array   [业务订单更新数据，资金订单新增数据]
     */
    private function _compute_order($input = array())
    {
        $express_order_where = array(
            array('express_order_id =[+]', $input['id']),
            array('express_order_delete_state = 0'),
            array('express_order_trash = 0'),
            array('express_order_pay_state = 0'),
            array('express_order_state = 4'),
        );
        $express_order = object(parent::TABLE_EXPRESS_ORDER)->find_where($express_order_where);
        if (empty($express_order)) {
            throw new error('未找到相关支付订单，或订单信息异常');
        }


        // 业务订单（express_order）更新数据
        $express_order_update_data = [];
        $express_order_update_data['express_order_json'] = cmd(array($express_order['express_order_json']), 'json decode');

        //获取业务订单配置信息
        $user_send_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("express_order"), true);

        //代理提成基数
        $royal_scale = isset($user_send_config['royal_scale']) && (float) $user_send_config['royal_scale'] >= 0 && (float) $user_send_config['royal_scale'] <= 1 ? (int) $user_send_config['royal_scale'] : 0.1;

        //算法
        $pay_algorithm = isset($user_send_config['algorithm']) && ($user_send_config['algorithm'] == 'ceil' || $user_send_config['algorithm'] == 'floor' || $user_send_config['algorithm'] == 'round') ? $user_send_config['algorithm'] : 'round';

        // 配送物流信息
        $shippings = object(parent::TABLE_SHIPPING)->find($express_order['shipping_id']);

        if (!empty($shippings) && $shippings['shipping_default'] == 1) {
            // 使用优惠劵
            if (isset($input['discount_id']) && $input['discount_id']) {
                $user_coupon_where = array(
                    array('user_coupon_id =[+]', $input['discount_id']),
                    array('user_id =[+]', $_SESSION['user_id']),
                    array('user_coupon_state =[+]', 1),
                    array('user_coupon_use_time =[+]', 0),
                    array('(user_coupon_expire_time >=' . time() . ' OR user_coupon_expire_time = 0 )'),
                );
                $select = array(
                    "user_coupon_id AS id",
                    "user_id AS uid",
                    "coupon_id",
                    "user_coupon_json AS coupon_info",
                    "user_coupon_state AS state",
                    "user_coupon_use_time AS use_time",
                    "user_coupon_expire_time AS expire_time",
                );

                // 用户优惠券
                $user_coupon = object(parent::TABLE_USER_COUPON)->find_where($user_coupon_where, $select);

                if (empty($user_coupon)) throw new error('优惠券信息异常');

                // 原始优惠券
                $coupon = object(parent::TABLE_COUPON)->find($user_coupon['coupon_id']);

                if (isset($coupon['coupon_type']) && $coupon['coupon_type'] == 2) {
                    if ($express_order['express_order_money'] > $coupon['coupon_discount']) {
                        $rebate_money = $coupon['coupon_discount'];
                        $pay_money = $express_order['express_order_money'] - $rebate_money;
                        $agent_royalty = $pay_money * $royal_scale;
                    } else {
                        $rebate_money = $express_order['express_order_money'];
                        $pay_money = 0;
                        $agent_royalty = 0;
                    }
                    $express_order_update_data['user_coupon_id'] = $user_coupon['id'];

                    if (!is_array($express_order_update_data['express_order_json'])) {
                        $express_order_update_data['express_order_json'] = array();
                    }

                    $express_order_update_data['express_order_json']['coupon_info'] = $coupon;
                } else 
                if (isset($coupon['coupon_type']) &&  $coupon['coupon_type'] == 4) {
                    $pay_money = $express_order['express_order_money'] * $coupon['coupon_discount'] / 100;
                    $rebate_money = $express_order['express_order_money'] - $pay_money;
                    $agent_royalty = $pay_money * $royal_scale;
                    $express_order_update_data['user_coupon_id'] = $user_coupon['id'];
                    $express_order_update_data['express_order_json']['coupon_info'] = $coupon;
                } else {
                    throw new error('优惠券信息异常');
                }
            } else {
                $vip_date = isset($user_send_config['vip_date']) && $user_send_config['vip_date'] ? $user_send_config['vip_date'] : '08,18,28'; //会员日
                $vip_scale = isset($user_send_config['vip_scale']) && (float) $user_send_config['vip_scale'] >= 0 && (float) $user_send_config['vip_scale'] <= 1 ? (float) $user_send_config['vip_scale'] : 0.9; //会员折扣
                $vip_date_scale = isset($user_send_config['vip_date_scale']) && (float) $user_send_config['vip_date_scale'] >= 0 && (float) $user_send_config['vip_date_scale'] <= 1 ? (float) $user_send_config['vip_date_scale'] : 0.8; //会员日折扣

                $time = time();
                $date = date('d', $time);
                $dates = explode(',', $vip_date);
                if (in_array($date, $dates)) {
                    $vip_scale = $vip_date_scale;
                }

                //代理邀请配置
                $agent_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("agent_user"), true);
                //代理限制人数
                $agent_scale_limit = isset($agent_config['agent_scale_limit']) && is_numeric((int) $agent_config['agent_scale_limit']) ? (int) $agent_config['agent_scale_limit'] : 20;
                //代理折扣
                $agent_user_scale = isset($agent_config['agent_user_scale']) && (float) $agent_config['agent_user_scale'] <= 1 ? (float) $agent_config['agent_user_scale'] : 0.85;
                //获取用户下级
                $user_son_count = object(parent::TABLE_USER)->find_son_count($_SESSION['user_id']);

                if (!empty($user_son_count['count']) && $user_son_count['count'] > $agent_scale_limit) {
                    $vip_scale = $agent_user_scale;
                }
                $pay_money = $express_order['express_order_money'] * $vip_scale;
                $rebate_money = $express_order['express_order_money'] - $pay_money;
                $agent_royalty = $pay_money * $royal_scale;
                $express_order_update_data['express_order_json']['vip_scale'] = [
                    'vip_scale' => $vip_scale
                ];
            }
        } else {
            $pay_money = $express_order['express_order_money'];
            $rebate_money = 0;
            $agent_royalty = $pay_money * $royal_scale;
        }

        switch ($pay_algorithm) {
            case 'ceil':
                $rebate_money = ceil($rebate_money);
                $pay_money = ceil($pay_money);
                $agent_royalty = ceil($agent_royalty);
                break;
            case 'floor':
                $rebate_money = floor($rebate_money);
                $pay_money = floor($pay_money);
                $agent_royalty = floor($agent_royalty);
                break;
            default:
                $rebate_money = round($rebate_money);
                $pay_money = round($pay_money);
                $agent_royalty = round($agent_royalty);
        }

        $time = time();
        $express_order_update_data['express_order_rebate_money'] = $rebate_money;
        $express_order_update_data['express_order_pay_money'] = $pay_money;
        $express_order_update_data['express_order_agent_royalty'] = $agent_royalty;
        $express_order_update_data['express_order_json']['agent_info'] = [
            'royal_scale' => $royal_scale
        ];

        $express_order_update_data['express_order_json'] = cmd(array($express_order_update_data['express_order_json']), 'json encode');
        $express_order_update_data['express_order_update_time'] = $time;

        //资金订单表（order）新增数据
        $order_insert_data = array(
            "order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
            "order_type" => parent::TRANSACTION_TYPE_EXPRESS_ORDER, //快递下单支付--需要定义常量
            "order_comment" => "用户寄件下单支付",
            //"order_plus_method" => "",//收款方--收款方式--需要定义
            //"order_plus_account_id" => $data['merchant_id'],//收款账户ID--商户号
            //"order_plus_value" => $pay_money,//收款值
            "order_plus_update_time" => $time,
            "order_sign" => $express_order['express_order_id'],
            "order_action_user_id" => $_SESSION['user_id'],
            // "order_minus_method" => $input["pay_method"],
            "order_minus_account_id" => $_SESSION['user_id'],
            "order_minus_update_time" => $time,
            "order_state" => 1, //确定订单
            "order_pay_state" => 0, //未支付
            "order_insert_time" => $time,
            "order_json" => array(
                "express_user_send_config" => $user_send_config
            )
        );

        $express_order_update_data['order_id'] = $order_insert_data['order_id'];


        return array(
            // 资金订单表要新增的数据
            "order_insert_data" => $order_insert_data,
            // 业务订单表要更新的数据
            "express_order_update_data" => $express_order_update_data
        );
    }

    /**
     * 再来一单
     * api:EXPRESSORDERSELFREORDER
     * 
     * {"class":"express/order","method":"api_self_reorder"}
     * 
     * @param array $input
     * {"id":"订单ID"}
     * @return  
     */
    public function api_self_reorder($input = array())
    {
        // 检测登录
        object(parent::REQUEST_USER)->check();

        object(parent::ERROR)->check($input, 'id', parent::TABLE_EXPRESS_ORDER, array('args'), 'express_order_id');

        // 订单详情
        $express_order = object(parent::TABLE_EXPRESS_ORDER)->find($input['id']);

        // 新订单数据
        $new_express_order = array(
            'express_order_id' => object(parent::TABLE_EXPRESS_ORDER)->get_unique_id(),
            'user_id' => $express_order['user_id'],
            'user_parent_id' => $express_order['user_parent_id'],
            'express_order_type' => $express_order['express_order_type'],
            'express_order_json' => $express_order['express_order_json'],

            'user_address_id' => $express_order['user_address_id'],
            'user_address_name' => $express_order['user_address_name'],
            'user_address_phone' => $express_order['user_address_phone'],
            'user_address_province' => $express_order['user_address_province'],
            'user_address_city' => $express_order['user_address_city'],
            'user_address_district' => $express_order['user_address_district'],
            'user_address_details' => $express_order['user_address_details'],
            'express_order_get_name' => $express_order['express_order_get_name'],
            'express_order_get_phone' => $express_order['express_order_get_phone'],
            'express_order_get_province' => $express_order['express_order_get_province'],
            'express_order_get_city' => $express_order['express_order_get_city'],
            'express_order_get_district' => $express_order['express_order_get_district'],
            'express_order_get_address' => $express_order['express_order_get_address'],
            'express_order_insert_time' => time(),
            'express_order_update_time' => time()
        );

        //寄件类型
        $sending_type = object(parent::TABLE_TYPE)->find($express_order['express_order_type']);
        //寄件地址
        $address_info = object(parent::TABLE_USER_ADDRESS)->find($express_order['user_address_id']);

        // 插入成功，返回业务订单ID
        if (object(parent::TABLE_EXPRESS_ORDER)->insert($new_express_order)) {
            //获取寄件地区所有骑手信息
            $rider_info_config['where'] = array(
                array('express_rider_province =[+]', $express_order['user_address_province']),
                array('[and] express_rider_city =[+]', $express_order['user_address_city']),
                array('[and] express_rider_district =[+]', $express_order['user_address_district']),
                array('[and] express_rider_on_off=1'),
                array('[and] express_rider_state=1')
            );
            $rider_info = object(parent::TABLE_EXPRESS_RIDER)->select($rider_info_config);
            //判断是否存在骑手，发送下单消息
            if (!empty($rider_info)) {
                $pick_up_time = date('Y-m-d', time());
                if (!empty($input['start']) && !empty($input['end'])) {
                    $times = date('Y-m-d', time());
                    $pick_up_time = $times . 'T' . $input['start'] . '--' . $input['end'];
                }
                foreach ($rider_info as $k) {
                    // return $k;
                    //获取骑手授权信息
                    $rider_user_oauth = object(parent::TABLE_USER_OAUTH)->find($k['user_id']);
                    if (!empty($rider_user_oauth['user_oauth_wx_key'])) {
                        // return $rider_user_oauth;
                        //发送新订单消息给骑手
                        $data_message = array(
                            "touser" => $rider_user_oauth['user_oauth_wx_key'], //微信登录--用户openID
                            "template_id" => "OKxTqBDyPdTL_COXfvSeuXD0-sFVC6wemsGsVkkGwMY", //模板ID
                            "url" => "http://mp.jiangyoukuaidi.eonfox.com", //点击跳转链接地址  
                            "topcolor" => "#FF0000",
                            "data" => array(
                                "first" => array(
                                    "value" => "取件员，有新的寄件单，请尽快处理！",
                                    "color" => "#173177"
                                ),
                                "keyword1" => array(
                                    "value" => !empty($sending_type['type_name']) ? $sending_type['type_name'] : '未知',
                                    "color" => "#173177"
                                ),
                                "keyword2" => array(
                                    "value" => $input['province'] . '-' . $input['city'] . '-' . $input['district'] . '-' . $input['details'],
                                    "color" => "#173177"
                                ),
                                "keyword3" => array(
                                    "value" => $pick_up_time,
                                    "color" => "#173177"
                                ),
                                "keyword4" => array(
                                    "value" => !empty($address_info['user_address_consignee']) ? $address_info['user_address_consignee'] : '未知',
                                    "color" => "#173177"
                                ),
                                "keyword5" => array(
                                    "value" => $express_order['user_address_phone'],
                                    "color" => "#173177"
                                ),
                                "remark" => array(
                                    "value" => "请尽快取件",
                                    "color" => "#173177"
                                )
                            )
                        );
                        $data_message = cmd(array($data_message), 'json encode');
                        $time_out = 30;
                        object(parent::PLUGIN_WEIXIN_MESSAGE_SEND)->template_message($data_message, $time_out);
                    }
                }
            }
            return ['id' => $new_express_order['express_order_id']];
        } else {
            throw new error('订单插入失败，请重试');
        }
    }
}