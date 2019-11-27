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



namespace eapie\source\request\merchant;

use eapie\main;
use eapie\error;

//商家
class merchant extends \eapie\source\request\merchant
{


    /**
     * 商家-前台-数据列表
     *
     * api: MERCHANTLIST
     * req: {
     *  lon     [des] [必填] [经度]
     *  lat     [des] [必填] [纬度]
     *  search  [arr] [可选] [搜索、筛选]
     *  sort    [arr] [可选] [排序]
     *  size    [int] [可选] [每页的条数]
     *  page    [int] [可选] [当前页数，如果是等于 all 那么则查询所有]
     *  start   [int] [可选] [开始的位置，如果存在，则page无效]
     * }
     * 
     * @param  [arr] $input [请求参数]
     * @return  {
     *  row_count   [int] [数据总条数]
     *  limit_count [int] [已取出条数]
     *  page_size   [int] [每页的条数]
     *  page_count  [int] [总页数]
     *  page_now    [int] [当前页数]
     *  data        [arr] [数据]
     * }
     */
    public function api_list($input = array())
    {
        //查询配置
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($input, parent::REQUEST_ADMIN),
        );

        //用户坐标
        $user_lon = empty($input['lon']) ? 0 : $input['lon'];
        $user_lat = empty($input['lat']) ? 0 : $input['lat'];

        //字段
        $subquery = object(parent::TABLE_MERCHANT_TYPE)->subquery_get_merchant_type_name();
        $sql_join_count = object(parent::TABLE_USER_COMMENT)->sql_join_count('m', 'merchant', 'merchant_id');
        $config['select'] = array(
            'm.merchant_id AS id',
            'merchant_name AS name',
            'merchant_info AS info',
            'merchant_address AS address',
            'merchant_longitude AS lon',
            'merchant_latitude AS lat',
            'merchant_logo_image_id AS logo_img_id',
            'merchant_json AS json',
            "ROUND(6378.137*2*ASIN(SQRT(POW(SIN(({$user_lat}*PI()/180-merchant_latitude*PI()/180)/2),2)+COS({$user_lat}*PI()/180)*COS(merchant_latitude*PI()/180)*POW(SIN(({$user_lon}*PI()/180-merchant_longitude*PI()/180)/2),2)))*1000) AS distance_num",
            "({$subquery}) AS type_name",
            "($sql_join_count) as comments",
        );

        //条件
        $config['where'][] = array('merchant_state=1');

        //排序
        $config['orderby'] = object(parent::REQUEST)->orderby($input, array(
            'id_desc' => array('m.merchant_id', true),
            'id_asc' => array('m.merchant_id', false),
            'name_desc' => array('convert(merchant_name using gbk)', true),
            'name_asc' => array('convert(merchant_name using gbk)', false),
            'insert_time_desc' => array('merchant_insert_time', true),
            'insert_time_asc' => array('merchant_insert_time', false),
            'update_time_desc' => array('merchant_update_time', true),
            'update_time_asc' => array('merchant_update_time', false),
        ));
        $config['orderby'][] = array('distance_num', false);

        //筛选——类别
        if (!empty($input['search']['type_id'])) {
            $type_id = $input['search']['type_id'];
            if (is_string($type_id)) {
                $subquery = object(parent::TABLE_MERCHANT_TYPE)->subquery_get_merchant_id($type_id);
                $config['where'][] = array('[and] m.merchant_id in ([-])', $subquery, true);
            }
        }

        //筛选——搜索
        if (!empty($input['search']['keywords'])) {
            $keywords = cmd(array($input['search']['keywords']), 'str addslashes');
            $keywords = "%{$keywords}%";
            $config['where'][] = array('[and] m.merchant_name like [+]', $keywords);
        }

        //查询数据
        $data = object(parent::TABLE_MERCHANT)->select_page($config);

        //格式化数据
        foreach ($data['data'] as &$i) {
            $distance = $this->_calc_distance($user_lon, $user_lat, $i['lon'], $i['lat']);
            $i['distance'] = $distance['str'];
            $i['distance_num'] = $distance['num'];
            $i['type_name'] = $i['type_name'] ?: '';
            $i['star'] = 5;
            $json = json_decode($i['json'], true);
            $i['capita_price'] = '人均' . rand(10, 100) . '元';
            $i['tag'] = '关键词';
            $i['comments'] = $i['comments'] ? "{$i['comments']}人评价" : '暂无评论';
            unset($i['json']);
        }

        return $data;
    }




	/**
	 * 商家后台管理，获得一个商家用户及商家数据
	 * 可以指定，也可以默认
	 * 
	 * MERCHANTSELFADMIN
	 * {"class":"merchant/merchant","method":"api_self_admin"}
	 * 
	 * [{"merchant_id":"当前登录用户的所属商家ID，不填则默认"}]
	 * 
	 * @param  array $input [请求参数]
     * @return array
	 */
	public function api_self_admin( $input = array() ){
		//判断是否登录
        object(parent::REQUEST_USER)->check();
		if( isset($input['merchant_id']) ){
			object(parent::ERROR)->check($input, 'merchant_id', parent::TABLE_MERCHANT, array('args'));
		}
		
        $config = array(
            "where" => array()
        );
        $config["where"][] = array("mu.user_id=[+]", $_SESSION['user_id']);
		if( isset($input['merchant_id']) ){
			$config["where"][] = array("[and] m.merchant_id=[+]", $input['merchant_id']);
		}
		
        $config["where"][] = array("[and] m.merchant_state=1");
        $config["where"][] = array("[and] mu.merchant_user_state=1");
        $config["select"] = array(
            'mu.user_id',
            'mu.merchant_user_name',
            'mu.merchant_user_info',
            'mu.merchant_user_info',
            'm.merchant_id',
            'm.merchant_logo_image_id',
            'm.merchant_name',
            'm.merchant_info',
            'm.merchant_province',
            'm.merchant_city',
            'm.merchant_district',
            'm.merchant_address',
            'm.merchant_longitude',
            'm.merchant_latitude',
            'm.merchant_update_time',
            'm.merchant_insert_time',
        );
        $data = object(parent::TABLE_MERCHANT)->select_join($config);
        if( empty($data[0]) ){
            throw new error('不是商家');
        }
		
		return $data[0];
	}
	
	





    /**
     * 商家-前台-数据详情
     *
     * api: MERCHANTGET
     * req: {
     *  merchant_id     [str] [必填] [商户ID]
     *  lon             [dec] [必填] [用户坐标经度]
     *  lat             [dec] [必填] [用户坐标纬度]
     * }
     * 
     * @param  [arr] $input [请求参数]
     * @return array
     */
    public function api_get($input = array())
    {
        //校验数据
        object(parent::ERROR)->check($input, 'merchant_id', parent::TABLE_MERCHANT, array('args'));
        object(parent::ERROR)->check($input, 'lon', parent::TABLE_MERCHANT, array('args'), 'merchant_longitude');
        object(parent::ERROR)->check($input, 'lat', parent::TABLE_MERCHANT, array('args'), 'merchant_latitude');

        //查询数据
        $data = object(parent::TABLE_MERCHANT)->find($input['merchant_id']);

        if (empty($data))
            throw new error('商家不存在');
        if ($data['merchant_state'] != 1)
            throw new error('商家状态异常，暂停服务');

        //白名单过滤
        $whitelist = array(
            'merchant_id',
            'merchant_logo_image_id',
            'merchant_license_image_id',
            'merchant_name',
            'merchant_info',
            'merchant_province',
            'merchant_city',
            'merchant_district',
            'merchant_address',
            'merchant_longitude',
            'merchant_latitude',
            'merchant_star',
            'merchant_phone',
        );
        $data = cmd(array($data, $whitelist), 'arr whitelist');

        //取消前缀
        $output = array();
        foreach ($data as $key => $val) {
            $output[str_replace('merchant_', '', $key)] = $val;
        }

        //计算距离
        $calc_distance = $this->_calc_distance($input['lon'], $input['lat'], $output['longitude'], $output['latitude']);
        $output['distance'] = $calc_distance['str'];

        //查询商家图片
        $data_img = object(parent::TABLE_MERCHANT_IMAGE)->select(array(
            'select' => array('image_id'),
            'where' => array(array('merchant_id=[+]', $input['merchant_id'])),
            'orderby' => array(array('merchant_image_time', true))
        ));

        $output['merchant_img'] = array();
        foreach ($data_img as $val) {
            $output['merchant_img'][] = $val['image_id'];
        }

        return $output;
    }


    /**
     * 获取当前登录用户的商家数据列表
     * 
     * api: MERCHANTSELF
     * 1对多，所以返回的商家数据是  多条。未索引数组
     * 
     * @param   void    
     * @return  array
     */
    public function api_self($input = array())
    {
        //判断是否登录
        object(parent::REQUEST_USER)->check();

        $config = array(
            "where" => array()
        );
        $config["where"][] = array("mu.user_id=[+]", $_SESSION['user_id']);
        $config["where"][] = array("[and] m.merchant_state=1");
        $config["where"][] = array("[and] mu.merchant_user_state=1");
        $config["select"] = array(
            'mu.user_id',
            'mu.merchant_user_name as user_name',
            'mu.merchant_user_info as user_info',
            'mu.merchant_user_info as user_info',
            'm.merchant_id as id',
            'm.merchant_logo_image_id as logo_image_id',
            'm.merchant_name as name',
            'm.merchant_info as info',
            'merchant_country AS country',
            'merchant_province AS province',
            'merchant_city AS city',
            'merchant_district AS district',
            'm.merchant_address as address',
            'm.merchant_longitude as longitude',
            'm.merchant_latitude as latitude',
            'm.merchant_update_time as update_time',
            'm.merchant_insert_time as insert_time',
        );
        $data = object(parent::TABLE_MERCHANT)->select_join($config);
        if (empty($data)) {
            throw new error('不是商家');
        }

        //获取积分
        /*foreach($data as $key => $value){
            $data[$key]["credit_number"] = 0;
            $find_now_data = object(parent::TABLE_MERCHANT_CREDIT)->find_now_data($value['id']);
            if( !empty($find_now_data) ){
                $data[$key]["credit_number"] =$find_now_data['merchant_credit_value'];
            }
        }*/

        return $data;
    }


    /**
     * 二维码——当前用户的商家的收钱二维码
     *
     * api: MERCHANTSELFQRCODEMONEYPLUS
     * req: {
     *  merchant_id [str] [可选] [商家ID，默认该用户的第一个商家ID]
     *  level   [str] [可选] [级别,容错率,(L,M,Q,H)]
     *  size    [int] [可选] [二维码大小，默认3]
     *  padding [int] [可选] [二维码内边距,默认0]
     * }
     * 
     * @param  [arr]  $input [请求参数]
     * @return image
     */
    public function api_self_qrcode_money_plus($input = array())
    {
        //二维码参数
        $config = array();
        if (isset($input['level']))
            $config['level'] = $input['level'];
        if (isset($input['size']))
            $config['size'] = $input['size'];
        if (isset($input['padding']))
            $config['padding'] = $input['padding'];

        //二维码内容
        $data = array(
            'errno' => 0,
            'type' => 'merchant_money_plus',
            'data' => array()
        );

        //是否合法用户
        $user_id = null;
        if (object(parent::REQUEST_USER)->check(true)) {
            $user_id = $_SESSION['user_id'];
        } else {
            $data['errno'] = 1;
            $data['error'] = '非法用户';
        }

        //是否合法商家
        if ($user_id) {
            //是否指定商家ID
            $mch_id = null;
            if (!empty($input['merchant_id']) && is_string($input['merchant_id'])) {
                $mch_id = $input['merchant_id'];
            } else {
                $mch_ids = object(parent::TABLE_MERCHANT_USER)->get_mch_ids($user_id);
                if (empty($mch_ids)) {
                    $data['errno'] = 1;
                    $data['error'] = '非法商家';
                } else {
                    $mch_id = $mch_ids[0];
                }
            }

            //判断权限
            if ($mch_id && object(parent::TABLE_MERCHANT_USER)->check_exist($user_id, $mch_id, true)) {
                $data['data']['merchant_id'] = $mch_id;
            } else {
                $data['errno'] = 1;
                $data['error'] = '非法商家';
            }
        }

        //生成二维码
        $config['data'] = $data;
        object(parent::PLUGIN_PHPQRCODE)->output($config);
    }


    //===========================================
    // 私有方法
    //===========================================


    /**
     * 根据经纬度计算距离
     * 
     * @param  [dec] $lon [description]
     * @param  [dec] $lat [description]
     * @return [str] [距离]
     */
    private function _calc_distance($lon1 = 0, $lat1 = 0, $lon2 = 0, $lat2 = 0)
    {
        $lon1 = floatval($lon1);
        $lat1 = floatval($lat1);
        $lon2 = floatval($lon2);
        $lat2 = floatval($lat2);

        $d = 0;
        if ($lon1 && $lat1 && $lon2 && $lat2) {
            $dlat = deg2rad($lat2 - $lat1);
            $dlon = deg2rad($lon2 - $lon1);
            $a = pow(sin($dlat / 2), 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * pow(sin($dlon / 2), 2);
            $d = 6378137 * 2 * atan2(sqrt($a), sqrt(1 - $a));
            $d = round($d, 1);
        }

        $str = '未知';
        if ($d > 0 && $d < 100) {
            $str = '<100m';
        } elseif ($d > 100 && $d < 1000) {
            $str = $d . 'm';
        } elseif ($d > 1000) {
            $str = round($d / 1000, 1) . 'km';
        }

        return array(
            'num' => $d,
            'str' => $str
        );
    }

 
}