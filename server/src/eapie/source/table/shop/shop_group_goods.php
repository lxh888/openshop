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



namespace eapie\source\table\shop;
use eapie\main;
class shop_group_goods extends main
{


    /**
     * 缓存的键列表
     * @var string
     */
    const CACHE_KEY = array(__CLASS__);


    /**
     * 数据检测
     * @var array
     */
    public $check = array(
        'group_id' => array(
            'args' => array(
                'exist'=> array('缺少拼团ID参数'),
                'echo' => array('拼团ID数据类型不合法'),
                '!null'=> array('拼团ID不能为空'),
			),
			//检查编号是否存在		
			'exists_id'=>array(
				'method'=>array(array(parent::TABLE_SHOP_GROUP_GOODS, 'find'), "拼团ID有误，数据不存在",)
			),
		),
        'shop_goods_sku_id' => array(
            'args' => array(
                'exist'=> array('缺少拼团商品SKU参数'),
                'echo' => array('拼团商品SKU数据类型不合法'),
                '!null'=> array('拼团商品SKU不能为空'),
			),
			//检查编号是否存在      
            'exists_id'=>array(
				'method'=>array(array(parent::TABLE_SHOP_GOODS_SKU, 'find_exists_id'), "商品规格ID有误，数据不存在",) 
			),
		),
		'shop_goods_id' => array(
            'args' => array(
                'exist'=> array('缺少商品ID参数'),
                'echo' => array('商品ID数据类型不合法'),
                '!null'=> array('商品ID不能为空'),
			),
			//检查编号是否存在		
			'exists_id'=>array(
				'method'=>array(array(parent::TABLE_SHOP_GOODS, 'find_exists_id'), "商品ID有误，数据不存在",)
			),
			//检查编号是否合法	
			'legal_id'=>array(
				'method'=>array(array(parent::TABLE_SHOP_GOODS, 'find_legal_id'), "商品ID有误，商品未发布或已发布在审核") 
			),
		),
		'shop_group_goods_start_time' => array(
			'args' => array(
                'exist'=> array('缺少拼团开始时间参数'),
                'echo' => array('拼团开始时间数据类型不合法'),
                //'!null'=> array('拼团开始时间不能为空'),
                'match'=> array('/^[0-9]{4,}\-[0-9]{1,2}\-[0-9]{1,2}\s{1,}[0-9]{1,2}\:[0-9]{1,2}\:[0-9]{1,2}$/', "拼团开始时间格式有误"),
			)
		),
		'shop_group_goods_end_time' => array(
			'args' => array(
                'exist'=> array('缺少拼团结束时间参数'),
                'echo' => array('拼团结束时间数据类型不合法'),
                //'!null'=> array('拼团结束时间不能为空'),
                'match'=>array('/^[0-9]{4,}\-[0-9]{1,2}\-[0-9]{1,2}\s{1,}[0-9]{1,2}\:[0-9]{1,2}\:[0-9]{1,2}$/', "拼团结束时间格式有误"),
			)
		),
		'shop_group_goods_num' => array(
			'args' => array(
                'exist'=> array('缺少拼团总人数参数'),
                'echo' => array('拼团总人数数据类型不合法'),
                'match'=>array('/^[0-9]{0,}$/', "拼团总人数必须是整数"),
			)
		),
		'shop_group_goods_price' => array(
			'args' => array(
                'exist'=> array('缺少拼团价格参数'),
                'echo' => array('拼团价格数据类型不合法'),
                'match'=>array('/^[0-9]{0,}$/', "拼团价格必须是整数"),
			)
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

    /**
     * 分页数据
     * @param  config array
     * @return data   array
     */
    public function select_page($config = array()) {
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
			
			//左连拼团表
            $join_goods = array(
                'table' => 'shop_goods sg',
                'type' => 'left',
                'on' => 'sgg.shop_goods_id = sg.shop_goods_id'
			);			
			
			//左连商品图片表，获取主图
            $join_image = array(
                'table' => 'shop_goods_image sgi',
                'type' => 'left',
                'on' => 'sgg.shop_goods_id = sgi.shop_goods_id AND sgi.shop_goods_image_main = 1'
			);
			
			//左连规格表
			$join_sku = array(
				'table' => 'shop_goods_sku sku',
				'type' 	=> 'left',
				'on' => 'sgg.shop_goods_sku_id = sku.shop_goods_sku_id'
			);
				
			//用户数据
            $join_user = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = sgg.user_id'
            );	
				
	        //获取总条数
	        $total_count = db(parent::DB_APPLICATION_ID)
            ->table('shop_group_goods sgg')
			->joinon($join_user, $join_goods, $join_sku)
            ->call('where', $call_where)
            ->find('count(distinct sgg.shop_group_goods_id) as count');
	        if (empty($total_count['count'])) {
	            return $data;
	        } else {
	            $data['row_count'] = $total_count['count'];
	            if (!empty($data['page_size'])) {
	                $data['page_count'] = ceil($data['row_count']/$data['page_size']);
	                $data['page_now'] = ceil($limit[0]/$data['page_size']) + 1;//当前页数
	            }
			}
			
			
			if( empty($select) ){
				$sql_join_stock_max_price = object(parent::TABLE_SHOP_GOODS_SKU)->sql_join_stock_max_price("sgg");
       			$sql_join_stock_min_price = object(parent::TABLE_SHOP_GOODS_SKU)->sql_join_stock_min_price("sgg");
				$user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
				$select = array(
					'u.user_parent_id',
                    'u.user_nickname',
                    'u.user_logo_image_id',
                    '('.$user_phone_verify_list_sql.') as user_phone_verify_list',
					'sgg.*',
					'sg.shop_goods_name',
					'sg.shop_goods_property',
					'sku.shop_goods_spu_id',
					'sku.shop_goods_sku_stock',
					'sku.shop_goods_sku_price',
					'sku.shop_goods_sku_market_price',
					'(' . $sql_join_stock_max_price . ') as original_price_max',
            		'(' . $sql_join_stock_min_price . ') as original_price_min',
				);
			}
			
			
	        //查询数据
	        $data['data'] = db(parent::DB_APPLICATION_ID)
			->table('shop_group_goods sgg')
			->groupby('sgg.shop_group_goods_id')
			->joinon($join_image, $join_user, $join_goods, $join_sku)
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);
			
			
	        return $data;
		});
	}

	/**
	 * 验证拼团商品，是否拼团结束
	 * @param string $group_id 拼团ID
	 * @return int $result
	 *  0 => 时间未到，人数未满,不结束，不成功
	 *  1 => 时间到，人数未满，拼团结束，未拼团成功
	 *  2 => 时间到，人数已满，拼团结束，拼团成功
	 *  3 => 时间未到，拼团人数已满，拼团结束，拼团成功
	 *  false => 拼团ID不存在
	 */
	public function group_is_end($group_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($group_id), function($group_id){
			$data = db(parent::DB_APPLICATION_ID)
				->table('shop_group_goods')
				->where(array('shop_group_goods_id=[+]', $group_id))
				->find();

			$group_is_success = (int)$data['shop_group_goods_is_success'];
			$group_is_end = (int)$data['shop_group_goods_is_end'];
			// 拼团时间是否结束
			if($group_is_end === 0 && $group_is_success === 0){
				// 暂未结束
				if($data['shop_group_goods_end_time'] <= time()){
					//时间到，对比人数
					if($data['shop_group_goods_now_num'] < $data['shop_group_goods_num']){
						//时间到，人数未满，拼团结束，未拼团成功

						//修改订单状态（拼团结束）
						$update_where = array(
							array('shop_group_goods_id=[+]', $group_id)
						);

						$update_data = array(
							'shop_group_goods_is_end' => 1,
							'shop_group_goods_update_time' => time(),
						);
						if(object(parent::TABLE_SHOP_GROUP_GOODS)->update($update_where, $update_data)){
							return 1;
						}else {
							throw new error('操作失败');
						}
					} else {
						//时间到，人数已满，拼团结束，拼团成功

						//订单数据 拼团成功订单处理
						object(parent::TABLE_SHOP_GROUP_ORDER)->add_success_group_order($data['shop_group_goods_id']);

						//修改拼团状态（拼团成功）
						$update_where = array(
							array('shop_group_goods_id=[+]', $group_id)
						);

						$update_data = array(
							'shop_group_goods_is_end' => 1,
							'shop_group_goods_is_success' => 1,
							'shop_group_goods_update_time' => time(),
						);
						if(object(parent::TABLE_SHOP_GROUP_GOODS)->update($update_where, $update_data)){
							return 2;
						}else {
							throw new error('操作失败');
						}
					}
				} else {
					//时间未到
					if($data['shop_group_goods_now_num'] < $data['shop_group_goods_num']){
						//时间未到，人数未满,不结束，不成功
						return 0;
					} else {
						//时间未到，拼团人数已满，拼团结束，拼团成功

						//订单数据 拼团成功订单处理
						object(parent::TABLE_SHOP_GROUP_ORDER)->add_success_group_order($data['shop_group_goods_id']);
						
						//修改拼团状态（拼团成功，拼团结束）
						$update_where = array(
							array('shop_group_goods_id=[+]', $group_id)
						);

						$update_data = array(
							'shop_group_goods_is_end' => 1,
							'shop_group_goods_is_success' => 1,
							'shop_group_goods_update_time' => time(),
						);

						if(object(parent::TABLE_SHOP_GROUP_GOODS)->update($update_where, $update_data)){
							return 3;
						}else {
							throw new error('操作失败');
						}
					}
				}
			} else {
				// 已经结束
				return 2;
			}
			
		});
	}

	/**
	 * 拼团人数+1
	 * @param string $group_id
	 * @return int $group_now_num
	 */
	public function group_now_num_add($group_id){
		$group_data = db(parent::DB_APPLICATION_ID)
			->table('shop_group_goods')
			->where(array('shop_group_goods_id=[+]', $group_id))
			->find();
		$group_old_num = $group_data['shop_group_goods_now_num'];
		$group_now_num = $group_old_num + 1;

		$where = array(
			array('shop_group_goods_id=[+]', $group_id)
		);
		$data = array(
			'shop_group_goods_now_num' => $group_now_num,
			'shop_group_goods_update_time' => time(),
		);
		if ($this->update($where,$data)) {
			return $group_now_num;
		}
	}

	/**
	 * 查询单个拼团商品的所有数据
	 * @param string $group_id 拼团ID
	 * @return array $data  单个拼团商品数据
	 */
	public function find($group_id = null){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($group_id), function($group_id){
			return db(parent::DB_APPLICATION_ID)
			->table('shop_group_goods')
			->where(array('shop_group_goods_id=[+]', $group_id))
			->find();
		});
	}



	/**
	 * 查询单个拼团商品的所有数据，包括商品、SKU
	 * 
	 * @param string $shop_group_goods_id 	拼团ID
	 * @return array $data  				单个拼团商品数据
	 */
	public function find_join($shop_group_goods_id = '', $find = array()){
		if( empty($shop_group_goods_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_group_goods_id, $find), function($shop_group_goods_id, $find){
			if( empty($find) ){
				$find = array(
					'sgg.*',
					'sg.shop_goods_name',
					'sg.shop_goods_property',
					'sku.shop_goods_spu_id',
					'sku.shop_goods_sku_stock',
					'sku.shop_goods_sku_price',
					'sku.shop_goods_sku_market_price',
				);
			}	
				
			//左连拼团表
            $join_goods = array(
                'table' => 'shop_goods sg',
                'type' => 'left',
                'on' => 'sgg.shop_goods_id = sg.shop_goods_id'
			);			
			
			//左连规格表
			$join_sku = array(
				'table' => 'shop_goods_sku sku',
				'type' 	=> 'left',
				'on' => 'sgg.shop_goods_sku_id = sku.shop_goods_sku_id'
			);
			
			return db(parent::DB_APPLICATION_ID)
			->table('shop_group_goods as sgg')
			->joinon($join_goods, $join_sku)
			->where(array('shop_group_goods_id=[+]', $shop_group_goods_id))
			->find($find);
		});
	}



	/**
	 * 检查开始结束时间
	 * @param int start_time 开始时间
	 * @param int end_time 结束时间
	 * @return boolean res 结果 true 结束时间晚于开始时间 false 结束时间早于开始时间
	 */
	public function _end_time_is_early($start_time = 0, $end_time = 0){
		if(is_string($start_time) || is_string($end_time)){
			$start_time = (int)$start_time;
			$end_time = (int)$end_time;
		}

		if($end_time <= $start_time){
			return false;
		} else {
			return true;
		}
	}


	/**
	 * 查询拼团是否存在
	 * @param string $gourp_id 拼团ID
	 * @return boolean $result
	 *   false =>  不存在
	 *   true  =>  存在
	 */
	public function has_group($group_id){
		$bool = db(parent::DB_APPLICATION_ID)
		->table('shop_group_goods')
		->where(array('shop_group_goods_id=[+]', $group_id))
		->find(array('shop_group_goods_id'));
		
		if(empty($bool)){
			return false;
		}
		return true;
	}


	/**
	 * 插入拼团商品数据
	 * @param $data array
	 * @return $result bool
	 */
	public function insert($data = array(), $call_data = array()){
		if (empty($data) && empty($call_data))
			return false;

		$bool = db(parent::DB_APPLICATION_ID)
			->table('shop_group_goods')
			->call('data', $call_data)
			->insert($data);

		//清理缓存
		if ($bool)
			object(parent::CACHE)->clear(self::CACHE_KEY);

		return $bool;
	}


	/**
	 * 更新拼团商品数据
	 * @param $data array 
	 * $data['group_id] 为修改的拼团商品ID
	 * @return $result bool
	 */
	public function update($call_where = array(), $data = array(), $call_data = array()){
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_group_goods')
            ->call('where', $call_where)
            ->call('data', $call_data)
            ->update($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
	}

		/**
	 * 根据唯一标识，删除数据
	 * 
	 * @param	string	$group_id 拼团ID
	 * @return	boolean $bool 
	 */
	public function remove($group_id = ''){
		if( empty($group_id) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('shop_group_goods')
		->where(array('shop_group_goods_id=[+]', (string)$group_id))
		->delete();
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}	
	
	
	

	
	
	
}