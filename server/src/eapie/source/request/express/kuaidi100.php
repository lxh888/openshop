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

use eapie\error;

class kuaidi100 extends \eapie\source\request\express
{
    /**
     * 快递物流信息跟踪
     * 
     * api:EXPRESSKUAIDI100SELFTRACKING
     * 
     * {"class":"express/kuaidi100","method":"api_self_tracking"}
     * 
     * req:{
     *  "id":"订单ID",
     * }
     * 
     * @param   array   [id:订单ID]
     * @return  array
     * 
     */
    public function api_self_tracking($data = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();
        if (!isset($data['id'])) {
            throw new error('缺少订单ID');
        }
        // 订单ID字段检测
        object(parent::ERROR)->check($data, 'id', parent::TABLE_EXPRESS_ORDER, array('args'), 'express_order_id');


        $express_order_where = array(
            array('express_order_id =[+]', $data['id']),
            array('express_order_delete_state = 0'),
            array('express_order_trash = 0'),
            array('express_order_state != 0'),
            array('user_id = [+]', $_SESSION['user_id']),
        );
        $select = array(
            "express_order_shipping_no AS shipping_no",
            "shipping_sign AS express_sign",
            "user_address_phone AS send_phone",
            "user_address_province AS send_province",
            "user_address_city AS send_city",
            "user_address_district AS send_district",
            "express_order_get_province AS get_province",
            "express_order_get_city AS gey_city",
            "express_order_get_district AS get_district",
            "express_order_shipping_state AS shipping_state",

        );
        // $express_order = object(parent::TABLE_EXPRESS_ORDER)->find_where($express_order_where);
        $express_order = object(parent::TABLE_EXPRESS_ORDER)->find_where($express_order_where,$select);
        if (empty($express_order)) {
            throw new error('未找到相关订单，或订单信息异常');
        }

        if ($express_order['shipping_state'] == 0) {
            return array(
                'errno' => 3,
                'error' => '尚未发货!'
            );
        }
        if (empty($express_order['shipping_no'])) {
            return array(
                'errno' => 4,
                'error' => '运单号为空!'
            );
        }



        $kd100_msg = object(parent::TABLE_EXPRESS_ORDER)->get_kuaidi100_messgae($express_order);

        if ($kd100_msg['errno'] != 0) {
            return $kd100_msg;
        }

        $res = cmd(array($kd100_msg['data']), 'json decode');

        if (isset($res['state']) && in_array((int) $res['state'], array(3, 6))) {
        	
			$express_order_shipping_take_time = time();
			if( !empty($res['data']) && is_array($res['data']) ){
				//$res_data_length = count($res['data']);
				//$res_data = $res['data'][$res_data_length - 1];
				$res_data = $res['data'][0];
				if( !empty($res_data['time']) ){
					$express_order_shipping_take_time = cmd(array($res_data['time']), "time mktime");
				}
			}
			
            object(parent::TABLE_EXPRESS_ORDER)->update_one($data['id'], array('express_order_shipping_state' => 1, 'express_order_shipping_take_time'=> $express_order_shipping_take_time, 'express_order_state' => 1));
        }

        $return = array();
        $return['errno'] = 0;

        $return['message'] = $res['message'];
        if ($res['message'] == 'ok' && isset($res['data'])) {
            $return['data'] = cmd(array($res['data']), 'json decode');
        }

        return $return;


        // // 调用快递100接口要用的参数
        // $req = array();

        // // 快递运单号
        // $number = $express_order['express_order_shipping_no'];
        // if (!$number) {
        //     throw new error('快递运单号为空');
        // }
        // $req['number'] = $number;

        // // 快递名称英文
        // $req['conpany'] = $express_order['shipping_sign'];

        // // 寄件人电话
        // $req['phone'] = $express_order['user_address_phone'];

        // // 寄件人省
        // $from_province = $express_order['user_address_province'];
        // // 寄件人市
        // $from_city = $express_order['user_address_city'];
        // // 寄件人区
        // $from_district = $express_order['user_address_district'];

        // // 出发地城市
        // $req['from'] = $from_province . $from_city . $from_district;

        // // 收件人省
        // $toprovince = $express_order['express_order_get_province'];
        // // 收件人市
        // $tocity = $express_order['express_order_get_city'];
        // // 收件人区
        // $todistrict = $express_order['express_order_get_district'];

        // // 目的地城市
        // $req['to'] = $toprovince . $tocity . $todistrict;


        // $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("kuaidi100_access"), true);

        // if (empty($config)) {
        //     throw new error('快递100配置信息异常');
        // }

        // // 快递100分配的公司编号
        // $req['customer'] = $config['customer'];

        // // 授权码
        // $req['key'] = $config['key'];
        // // printexit($req);

        // // 添加此字段表示开通行政区域解析功能
        // $req['resultv2'] = 1;

        // $response = object(parent::PLUGIN_KUAIDI100)->real_time_query($req);


        // if ($response['errno'] != 0) {
        //     return $response;
        // }

        // $res = cmd(array($response['data']), 'json decode');

        // $return = array();
        // $return['errno'] = 0;

        // $return['message'] = $res['message'];
        // if ($res['message'] == 'ok' && isset($res['data'])) {
        //     $return['data'] = cmd(array($res['data']), 'json decode');
        // }

        // return $return;

        
    }
}