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



namespace eapie\source\table\house;

use eapie\main;

//商家楼盘项目表
class house_product extends main
{

    /**
     * 缓存的键列表
     * @var string
     */
    const CACHE_KEY = array(__CLASS__, "house_product_image");


    /**
     * 数据检测
     * @var array
     */
    public $check = array(
        'house_product_id' => array(
            'args' => array(
                'exist'=> array('缺少楼盘项目ID参数'),
                'echo' => array('楼盘项目ID数据类型不合法'),
                '!null'=> array('楼盘项目ID不能为空'),
            ),
            'exist' => array(
                'method'=>array(array(parent::TABLE_HOUSE_PRODUCT, 'find'), '楼盘项目ID有误，数据不存在') 
            )
        ),
        'house_product_developer' => array(
            'args' => array(
                'exist'=> array('缺少开发商参数'),
                'echo' => array('开发商数据类型不合法'),
                '!null'=> array('开发商不能为空'),
            ),
        ),
        'house_product_manage_company' => array(
            'args' => array(
                'exist'=> array('缺少物业公司参数'),
                'echo' => array('物业公司数据类型不合法'),
                '!null'=> array('物业公司不能为空'),
            ),
        ),
        'house_product_manage_money' => array(
            'args' => array(
                'exist'=> array('缺少物业费参数'),
                'match' => array('/^\d{1,20}$/', '物业费不合法'),
            ),
        ),
        'house_product_recommend_level' => array(
            'args' => array(
                'exist'=> array('缺少推荐指数参数'),
                'match' => array('/^[1-5]$/', '推荐指数不合法'),
            )
        ),
        'house_product_state' => array(
            'args' => array(
                'exist'=> array('缺少楼盘项目状态参数'),
                'match' => array('/^[0-4]$/', '楼盘项目状态不合法'),
            ),
        ),
        'house_product_json' => array(
            'args' => array(
                'exist'=> array('缺少项目分析，项目动态参数'),
                'method'=> array(array(parent::TABLE_HOUSE_PRODUCT, 'check_json'), '项目分析，项目动态不合法'),
            ),
        ),
        'house_product_remark' => array(
            'args' => array(
                'exist'=> array('缺少备注参数'),
                'echo' => array('备注不合法'),
            ),
        ),
        //——楼盘——
        'house_product_name' => array(
            'args' => array(
                'exist'=> array('缺少项目名称参数'),
                'echo' => array('项目名称数据类型不合法'),
                '!null'=> array('项目名称不能为空'),
            ),
        ),
        'house_product_type' => array(
            'args' => array(
                'exist'=> array('缺少楼盘类型参数'),
                'echo' => array('楼盘类型不合法'),
                '!null'=> array('楼盘类型不能为空'),
            ),
        ),
        'house_product_total_floor' => array(
            'args' => array(
                'exist'=> array('缺少总层高参数'),
                '!null'=> array('总层高不能为空'),
                'match'=> array('/^(\d{1,20})$/', '总层高不合法'),
            ),
        ),
        'house_product_total_floor_area' => array(
            'args' => array(
                'exist'=> array('缺少总占地面积参数'),
                '!null'=> array('总占地面积不能为空'),
                'match'=> array('/^(\d{1,17})(\.[0-9]{1,3})?$/', '总占地面积不合法'),
            ),
        ),
        'house_product_total_room' => array(
            'args' => array(
                'exist'=> array('缺少总体量参数'),
                '!null'=> array('总体量不能为空'),
                'match'=> array('/^(\d{1,20})$/', '总体量不合法'),
            ),
        ),
        'house_product_plot_ratio' => array(
            'args' => array(
                'exist'=> array('缺少容积率参数'),
                '!null'=> array('容积率不能为空'),
                'match'=> array('/^(\d{1,2})(\.[0-9]{1,2})?$/', '容积率不合法'),
            ),
        ),
        'house_product_greening_rate' => array(
            'args' => array(
                'exist'=> array('缺少绿化率参数'),
                '!null'=> array('绿化率不能为空'),
                'match'=> array('/^(\d{1,2})(\.[0-9]{1,2})?$/', '绿化率不合法'),
            ),
        ),
        //——房子——
        'house_product_room_floor' => array(
            'args' => array(
                'exist'=> array('缺少层高参数'),
                '!null'=> array('层高不能为空'),
                'match'=> array('/^(\d{1,20})$/', '层高不合法'),
            ),
        ),
        'house_product_room_height' => array(
            'args' => array(
                'exist'=> array('缺少层高参数'),
                '!null'=> array('层高不能为空'),
                'match'=> array('/^(\d{1,20})(\.\d+)?$/', '层高不合法'),
            ),
        ),
        'house_product_room_rate' => array(
            'args' => array(
                'exist'=> array('缺少得房率参数'),
                '!null'=> array('得房率不能为空'),
                'match'=> array('/^(\d{1,2})(\.[0-9]{1,2})?$/', '得房率不合法'),
            ),
        ),
        'house_product_room_price' => array(
            'args' => array(
                'exist'=> array('缺少房屋价格参数'),
                '!null'=> array('房屋价格不能为空'),
            ),
        ),
        'house_product_room_state' => array(
            'args' => array(
                'exist'=> array('缺少销售状态参数'),
                'match' => array('/^(0|1)$/', '销售状态不合法'),
            )
        ),
        'house_product_property_right' => array(
            'args' => array(
                'exist'=> array('缺少产权年限参数'),
                'match' => array('/^\d{1,20}$/', '产权年限不合法'),
            )
        ),
        'house_product_ladder_ratio' => array(
            'args' => array(
                'exist'=> array('缺少梯户比参数'),
                'echo' => array('梯户比数据类型不合法'),
                '!null'=> array('梯户比不能为空'),
            ),
        ),
        //——经纪人——
        'house_product_agent_name' => array(
            'args' => array(
                'exist'=> array('缺少对接人姓名参数'),
                'echo' => array('对接人姓名数据类型不合法'),
                '!null'=> array('对接人姓名不能为空'),
            ),
        ),
        'house_product_agent_company' => array(
            'args' => array(
                'exist'=> array('缺少销售公司参数'),
                'echo' => array('销售公司数据类型不合法'),
                '!null'=> array('销售公司不能为空'),
            ),
        ),
        'house_product_agent_phone' => array(
            'args' => array(
                'exist'=> array('缺少渠道公司电话参数'),
                'match'=> array('/^\d{1,20}$/', '渠道公司电话数据类型不合法'),
            ),
        ),
        'house_product_agent_commision' => array(
            'args' => array(
                'exist'=> array('缺少参考佣金参数'),
                'echo' => array('参考佣金数据类型不合法'),
                '!null'=> array('参考佣金不能为空'),
            ),
        ),
        //——地址——
        'house_product_country' => array(
            'args' => array(
                'exist'=> array('缺少国家参数'),
                'echo' => array('国家数据类型不合法'),
                '!null'=> array('国家不能为空'),
            ),
        ),
        'house_product_province' => array(
            'args' => array(
                'exist'=> array('缺少省份参数'),
                'echo' => array('省份数据类型不合法'),
                '!null'=> array('省份不能为空'),
            ),
        ),
        'house_product_city' => array(
            'args' => array(
                'exist'=> array('缺少城市参数'),
                'echo' => array('城市数据类型不合法'),
                '!null'=> array('城市不能为空'),
            ),
        ),
        'house_product_district' => array(
            'args' => array(
                'exist'=> array('缺少地区参数'),
                'echo' => array('地区数据类型不合法'),
                '!null'=> array('地区不能为空'),
            ),
        ),
        'house_product_address' => array(
            'args' => array(
                'exist'=> array('缺少详细地址参数'),
                'echo' => array('详细地址数据类型不合法'),
                '!null'=> array('详细地址不能为空'),
            ),
        ),
        'house_product_address_sale' => array(
            'args' => array(
                'exist'=> array('缺少售楼处地址参数'),
                'echo' => array('售楼处地址数据类型不合法'),
                '!null'=> array('售楼处地址不能为空'),
            ),
        ),
        'house_product_longitude' => array(
            'args' => array(
                'exist'=> array('缺少坐标纬度参数'),
                'method' => array(array(parent::TABLE_HOUSE_PRODUCT, 'check_longitude'), '坐标纬度不合法'),
            ),
        ),
        'house_product_latitude' => array(
            'args' => array(
                'exist'=> array('缺少坐标经度参数'),
                'method' => array(array(parent::TABLE_HOUSE_PRODUCT, 'check_latitude'), '坐标经度不合法'),
            ),
        ),
        //——时间——
        'house_product_time_land' => array(
            'args' => array(
                'exist'=> array('缺少拿地时间参数'),
                'echo' => array('拿地时间数据类型不合法'),
                '!null'=> array('拿地时间不能为空'),
            ),
        ),
        'house_product_time_sale' => array(
            'args' => array(
                'exist'=> array('缺少开盘时间参数'),
                'echo' => array('开盘时间数据类型不合法'),
                '!null'=> array('开盘时间不能为空'),
            ),
        ),
        'house_product_time_delivery' => array(
            'args' => array(
                'exist'=> array('缺少交房时间参数'),
                'echo' => array('交房时间数据类型不合法'),
                '!null'=> array('交房时间不能为空'),
            ),
        ),
        'wechat_group_id' => array(
            'args' => array(
                'exist'=> array('缺少微信群ID参数'),
                'echo' => array('微信群ID的数据类型不合法'),
                //'!null'=> array('微信群ID不能为空'),
            ),
        ),
    );


    /**
     * 获取一个id号
     * @return  string
     */
    public function get_unique_id()
    {
        return cmd(array(22), 'random autoincrement');
    }


    //===========================================
    // 操作数据
    //===========================================


    /**
     * 插入数据
     * 
     * @param   array $data      数据
     * @param   array $call_data 数据
     * @return  bool
     */
    public function insert($data = array(), $call_data = array())
    {
        if (empty($data) && empty($call_data))
            return false;

        $bool = db(parent::DB_APPLICATION_ID)
            ->table('house_product')
            ->call('data', $call_data)
            ->insert($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    /**
     * 更新数据
     * 
     * @param   array $call_where   更新条件
     * @param   array $data         更新数据
     * @param   array $call_data
     * @return  bool
     */
    public function update($call_where = array(), $data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('house_product')
            ->call('where', $call_where)
            ->call('data', $call_data)
            ->update($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }  


    //===========================================
    // 查询数据
    //===========================================


    /**
     * 查一条记录，根据主键
     * @param  string $id 商家用户表ID
     * @return array
     */
    public function find($id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($id), function($id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('house_product')
                ->where(array('house_product_id=[+]', $id))
                ->find();
        });
    }


    /**
     * 查一条记录，根据条件
     * @param  array  $call_where 查询条件
     * @return array
     */
    public function find_where($call_where = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where), function($call_where) {
            return db(parent::DB_APPLICATION_ID)
                ->table('house_product')
                ->call('where', $call_where)
                ->find();
        });
    }


    /**
     * 分页数据
     * @param  array $config 配置参数
     * @return array
     */
    public function select_page($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            //查询配置
            $call_where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $call_limit   = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select       = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            $limit = array(
                (isset($call_limit[0]) ? $call_limit[0] : 0),
                (isset($call_limit[1]) ? $call_limit[1] : 0)
            );

            //设置返回的数据
            $data = array(
                'row_count'   => 0,
                'limit_count' => $limit[0] + $limit[1],
                'page_size'   => $limit[1],
                'page_count'  => 0,
                'page_now'    => 0,
                'data'        => array()
            );

            //左连楼盘项目置顶表
            $join_houseproducttop = array(
                'table' => 'house_product_top hpt',
                'type' => 'left',
                'on' => 'hpt.house_product_id = hp.house_product_id'
            );

			//用户数据
            $user = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = hp.user_id'
            );

            //获取总条数
            $total_count = db(parent::DB_APPLICATION_ID)
                ->table('house_product hp')
                ->joinon($user, $join_houseproducttop)
                ->call('where', $call_where)
                ->find('count(distinct hp.house_product_id) as count');

            //是否有数据
            if (empty($total_count['count'])) {
                return $data;
            } else {
                $data['row_count'] = $total_count['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count']/$data['page_size']);
                    $data['page_now'] = ceil($limit[0]/$data['page_size']) + 1;
                }
            }

			if( empty($select) ){
				$user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
				$select = array(
					'u.user_parent_id',
                    'u.user_nickname',
                    'u.user_logo_image_id',
                    '('.$user_phone_verify_list_sql.') as user_phone_verify_list',
					'hp.*',
					'hpt.house_product_id as house_product_top',
					'hpt.house_product_top_start_time',
					'hpt.house_product_top_end_time'
				);
			}

            //查询数据
            $data['data'] = db(parent::DB_APPLICATION_ID)
                ->table('house_product hp')
                ->joinon($user, $join_houseproducttop)
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $call_limit)
                ->select($select);

            return $data;
        });
    }


    /**
     * 查询楼盘信息
     * @return [type] [description]
     */
    public function get_house_data($house = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($house), function($house) {
            $house = cmd(array($house), 'str addslashes');
            return db(parent::DB_APPLICATION_ID)
                ->table('house_product')
                ->where(array('house_product_name like [+]', '%'.$house.'%'))
                ->find(array(
                    'house_product_developer AS developer',
                    'house_product_manage_company AS manage_company',
                    'house_product_manage_money AS manage_money',
                    'house_product_type AS type',
                    'house_product_name AS name',
                    'house_product_total_area AS total_area',
                    'house_product_total_floor AS total_floor',
                    'house_product_total_floor_area AS total_floor_area',
                    'house_product_plot_ratio AS plot_ratio',
                    'house_product_greening_rate AS greening_rate',
                ));
        });
    }


    //===========================================
    // 检测数据
    //===========================================


    /**
     * 检测——楼盘类型
     * @param  string $val 数据
     * @return Boolean
     */
    public function check_product_type($val = '')
    {
        $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('house_product_type'), true);
        return is_string($val) && in_array($val, $config);
    }


    /**
     * 检测——经度
     * @param  [dec] $v [经度]
     * @return bool
     */
    public function check_longitude($v)
    {
        return is_numeric($v) && ($v >= -180) && ($v <= 180);
    }

    /**
     * 检测——纬度
     * @param  [dec] $v [纬度]
     * @return bool
     */
    public function check_latitude($v)
    {
        return is_numeric($v) && ($v >= -90) && ($v <= 90);
    }


    /**
     * 检测——时间是否合法
     * @param  string $val 数据
     * @return Boolean
     */
    public function check_time($val = '')
    {
        return boolval(strtotime($val));
    }


    /**
     * 检测——是否json数据
     * @param  array  $val 数据
     * @return Boolean
     */
    public function check_json($val = array())
    {
        return is_array($val);
    }





	/**
	 * 获取附加数据
	 * 
	 * @param	array		$data
	 * @param	array		$config
	 * @return	array	
	 */
	public function get_additional_data($data = array(), $config = array()){
		if( empty($data) ){
			return $data;
		}
		
		$goods_ids = array();
		foreach($data as $key => $value){
			if( !isset($value['house_product_id']) ){
				break;
			}
			$data[$key]["house_product_image"] = array();
			$data[$key]["house_product_image_type"] = array();
			$product_ids[] = $value['house_product_id'];
		}	
		
		//没有可查询的数据
		if( empty($product_ids) ){
			return $data;
		}

		//标识符的目的是，有些数据不一致
		$identifier = md5(cmd(array($product_ids), "json encode").cmd(array($data), "json encode")).
		md5(cmd(array($data), "json encode").cmd(array($product_ids), "json encode"));
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($product_ids, $config, $identifier), function($product_ids, $config, $identifier) use ($data){
			
			$in_string = "\"".implode("\",\"", $product_ids)."\"";
			
			//获取图片
			if(empty($config['house_product_image']['where']) || !is_array($config['house_product_image']['where']))
			$config['house_product_image']['where'] = array();
			if(empty($config['house_product_image']['orderby']) || !is_array($config['house_product_image']['orderby']))
			$config['house_product_image']['orderby'] = array();
			if(empty($config['house_product_image']['limit']) || !is_array($config['house_product_image']['limit']))
			$config['house_product_image']['limit'] = array();
			if(empty($config['house_product_image']['select']) || !is_array($config['house_product_image']['select']))
			$config['house_product_image']['select'] = array();
			
			$config['house_product_image']['where'][] = array("[and] hpi.house_product_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
			$config['house_product_image']['where'][] = array("[and] i.image_state=1");
			if( empty($config['house_product_image']['orderby']) ){
				$config['house_product_image']['orderby'][] = array("image_sort");
				$config['house_product_image']['orderby'][] = array("house_product_image_id");
			}
			$house_product_image_data = object(parent::TABLE_HOUSE_PRODUCT_IMAGE)->select_join($config['house_product_image']);
			
			//获取类型
			$product_image_type_data = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('house_product_image_type'), true);
			if( empty($product_image_type_data) ){
				return $data;
			}
			
			foreach($data as $parent_key => $parent_value){
				foreach($product_image_type_data as $type_key => $type_value){
					$data[$parent_key]['house_product_image_type'][$type_key] = array(
						"name" => $type_value,
						"image" => array()
					);
				}
				
				//已经删完了
				if( empty($house_product_image_data) ){
					continue;
				}
				
				//先获取当前图片
				if( !empty($house_product_image_data) ){
					foreach($house_product_image_data as $image_key => $image_value){
						if($image_value['house_product_id'] == $parent_value['house_product_id']){
							if( isset($data[$parent_key]['house_product_image_type'][$image_value['house_product_image_type']]) ){
								$data[$parent_key]['house_product_image_type'][$image_value['house_product_image_type']]['image'][] = $image_value;
							}
							$data[$parent_key]['house_product_image'][] = $image_value;
							unset($house_product_image_data[$image_key]);
						}
					}
				}
			
			}
			
			return $data;
		});


	}


    /**
     * 子查询——查某用户的所有楼盘ID
     * @param  string $user_id [用户ID]
     * @return string 
     */
    public function sql_select_product_id($user_id)
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id), function($user_id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('house_product')
                ->where(array('user_id = [+]', $user_id))
                ->where(array('house_product_trash = 0'))
                ->where(array('house_product_delete_state = 0'))
                ->select(array('house_product_id'), function ($e) {
                    return $e['query']['select'];
                });
        });
    }

}