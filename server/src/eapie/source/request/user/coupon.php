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

class coupon extends \eapie\source\request\user
{

    /**
     * 获取用户优惠券
     *
     * api:  USERCOUPONSELFLIST
     * {"class":"user/coupon","method":"api_self_list"}
     * 
     * @param array $input
     * @return void
     */
    public function api_self_list($input = [])
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        $config = array(
            'where' => array(),
            'orderby' => array()
        );
        $config['orderby'][] = array('insert_time', false);
        $config['where'][] = array("user_id =[+]", $_SESSION['user_id']);
        $config['limit'] = object(parent::REQUEST)->limit($input, parent::REQUEST_USER);
        $config['select'] = array(
            "uc.user_coupon_id AS id",
            // "uc.coupon_id",
            "uc.user_coupon_json AS coupon_info",
            "uc.user_coupon_expire_time AS expire_time",
            "uc.user_coupon_insert_time AS insert_time",
            "uc.user_coupon_state AS state",
            // "uc.user_coupon_use_state AS use_state",
            "uc.user_coupon_number AS number",
            "uc.user_coupon_use_number AS use_number",
            "c.coupon_id",
            "c.coupon_name AS name",
            "c.coupon_info AS info",
            "c.coupon_type AS type",
            "c.coupon_discount AS discount",
            "c.coupon_start_time AS start_time",
            "FROM_UNIXTIME(c.coupon_start_time,'%Y-%m-%d') AS start_times",
            "c.coupon_end_time AS end_time",
            "FROM_UNIXTIME(c.coupon_end_time,'%Y-%m-%d') AS end_times",
            "c.coupon_state AS c_state",
            "c.coupon_limit_min AS min",
            "c.coupon_limit_max AS max",
            "c.coupon_property AS property",
        );

        $sql_effective_id = object(parent::TABLE_USER_COUPON)->sql_effective_id($config['where']);

        $state = array(
            // 1 => array('user_coupon_state = 1'),
            // 2 => array('user_coupon_state = 0')
            1 => array('user_coupon_state = 1 AND ( user_coupon_expire_time >=' . time() . ' OR user_coupon_expire_time=0 ) AND c.coupon_state = 1'),
            // 1 => array('user_coupon_state = 1 AND ( user_coupon_expire_time >=' . time() . ' OR user_coupon_expire_time=0 )'),
            2 => array('(user_coupon_state = 0 OR (user_coupon_id NOT IN ('.$sql_effective_id.')) OR  (user_coupon_expire_time <=' . time() . ' AND user_coupon_expire_time<>0))')
        );


        if (!isset($input['state'])) {
            $config['where'][] = $state[1];
        } elseif (!is_numeric($input['state']) || !in_array($input['state'], array(1, 2))) {
            throw new error('参数错误');
        } else {
            $config['where'][] = $state[$input['state']];
        }

        //查询数据
        $data = object(parent::TABLE_USER_COUPON)->select_page($config);

        // 转json
        foreach ($data['data'] as $k => $v) {
            $data['data'][$k]['coupon_info'] = cmd(array($data['data'][$k]['coupon_info']), 'json decode');
            if (empty($data['data'][$k]['coupon_id'])) {
                $data['data'][$k]['coupon_id'] = $data['data'][$k]['coupon_info']['coupon_id'];
                $data['data'][$k]['discount'] = $data['data'][$k]['coupon_info']['coupon_discount'];
                $data['data'][$k]['end_time'] = $data['data'][$k]['coupon_info']['coupon_end_time'];
                $data['data'][$k]['max'] = $data['data'][$k]['coupon_info']['coupon_limit_max'];
                $data['data'][$k]['min'] = $data['data'][$k]['coupon_info']['coupon_limit_min'];
                $data['data'][$k]['name'] = $data['data'][$k]['coupon_info']['coupon_name'];
                $data['data'][$k]['property'] = $data['data'][$k]['coupon_info']['coupon_property'];
                $data['data'][$k]['start_time'] = $data['data'][$k]['coupon_info']['coupon_start_time'];
                $data['data'][$k]['type'] = $data['data'][$k]['coupon_info']['coupon_type'];
                // 如果优惠券原始数据不存在，则失效
                $data['data'][$k]['c_state'] = 0;
            }
            unset($data['data'][$k]['coupon_info']);
        }




        return $data;
    }

    /**
     * ------Mr.Zhao------2019.07.08------
     * 获取当前订单可用优惠券
     * 
     * api：    USERCOUPONSELFAVAILABLELIST
     * 
     * {"class":"user/coupon","method":"api_self_available_list"}
     * 
     * req：{
     *  'money':100,
     *  'credit':100
     * }
     * 
     */
    public function api_self_available_list($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        $req = array(
            'user_id' =>$_SESSION['user_id'],
            'orderby' => array()
        );

        $req['orderby'][] = array('uc.user_coupon_insert_time', false);
        if(isset($input['money']) && is_numeric($input['money'])){
            $req['money'] = (int)$input['money'];
        }
        if(isset($input['credit']) && is_numeric($input['credit'])){
            $req['credit'] = (int)$input['credit'];
        }
        
        $req['select'] = array(
            "uc.user_coupon_id AS id",
            "uc.coupon_id",
            "uc.user_coupon_json AS coupon_info",
            "uc.user_coupon_expire_time AS expire_time",
            "uc.user_coupon_insert_time AS insert_time",
            "uc.user_coupon_state AS state",
            "uc.user_coupon_number AS number",
            "uc.user_coupon_use_number AS use_number",
            "c.coupon_name AS name",
            "c.coupon_info AS info",
            "c.coupon_type AS type",
            "c.coupon_discount AS discount",
            "c.coupon_start_time AS start_time",
            "c.coupon_end_time AS end_time",
            "c.coupon_state AS c_state",
            "c.coupon_limit_min AS min",
            "c.coupon_limit_max AS max",
            "c.coupon_property AS property",
        );

        //查询数据
        $res_data = object(parent::TABLE_USER_COUPON)->select_available_join_coupon($req);

        // 获取积分单位进制和精度配置
        $credit_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("credit"), true);
        // 单位进制
        $scale = $credit_config['scale'] ? $credit_config['scale'] : 1;
        // 精度
        $precision = $credit_config['precision'] ? $credit_config['precision'] : 2;

        // 转json
        foreach ($res_data as $k => $v) {
            // $res_data[$k]['min'] /= 100;
            $res_data[$k]['min'] = number_format($res_data[$k]['min'] / $scale, $precision);
            // $res_data[$k]['max'] /= 100;
            $res_data[$k]['max'] = number_format($res_data[$k]['max'] / $scale, $precision);
            // $res_data[$k]['discount'] /= 100;
            $res_data[$k]['discount'] = number_format($res_data[$k]['discount'] / $scale, $precision);
            $res_data[$k]['coupon_info'] = cmd(array($res_data[$k]['coupon_info']), 'json decode');
            // $res_data[$k]['coupon_info']['coupon_limit_min'] /= 100;
            $res_data[$k]['coupon_info']['coupon_limit_min'] = number_format($res_data[$k]['coupon_info']['coupon_limit_min'] / $scale, $precision);
            // $res_data[$k]['coupon_info']['coupon_limit_max'] /= 100;
            $res_data[$k]['coupon_info']['coupon_limit_max'] = number_format($res_data[$k]['coupon_info']['coupon_limit_max'] / $scale, $precision);
            // $res_data[$k]['coupon_info']['coupon_discount'] /= 100;
            $res_data[$k]['coupon_info']['coupon_discount'] = number_format($res_data[$k]['coupon_info']['coupon_discount'] / $scale, $precision);
        }

        return $res_data;

    }
}