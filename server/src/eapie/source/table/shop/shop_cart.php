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

class shop_cart extends main
{


    /**
     * 缓存的键列表
     * 
     * @var string
     */
    const CACHE_KEY = array(__CLASS__);


    /**
     * 数据检测
     * 
     * @var array
     */
    public $check = array(
        'shop_cart_id' => array(
            'args'=>array(
                'exist' => array('缺少购物车ID参数'),
                'echo'  => array('购物车ID数据类型不合法'),
                '!null' => array('购物车ID不能为空'),
            ),    
            'exist'=>array(
                'method' => array(array(parent::TABLE_SHOP_CART, 'find'), '购物车ID有误，数据不存在',) 
            ),
        ),
        'shop_cart_number' => array(
            'args'=>array(
                'exist' => array('缺少商品数量参数'),
                'echo'  => array('商品数量数据类型不合法'),
                '!null' => array('商品数量不能为空'),
                'match' => array('/^[1-9]\d*$/', '商品数量不合法'),
            ),   
        ),
        'shop_cart_json' => array(
            'args'=>array(
                'exist'  => array('缺少商品json参数'),
                'method' => array(array(parent::TABLE_SHOP_CART, 'check_json'), '商品json数据不合法'),
            ), 
        ),
        'recommend_user_id' => array(
            //参数检测
            'format'=>array(
                    'echo'=>array("商品推荐人ID的数据类型不合法"),
                    '!null' => array('商品推荐人ID不能为空'),
                    ),
            //检查编号是否存在      
            'exists'=>array(
                    'method'=>array(array(parent::TABLE_USER, 'find_exists_id'), "商品推荐人ID有误，该推荐人数据不存在")       
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
     * 插入新数据
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
            ->table('shop_cart')
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
            ->table('shop_cart')
            ->call('where', $call_where)
            ->call('data', $call_data)
            ->update($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    /**
     * 删除数据，根据条件
     * 
     * @param   array $call_where 条件
     * @return  bool
     */
    public function delete($call_where = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_cart')
            ->call('where', $call_where)
            ->delete();

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    /**
     * 删除数据，根据主键
     * 
     * @param   string $id 主键ID
     * @return  bool
     */
    public function remove($id = '')
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_cart')
            ->where(array('shop_cart_id=[+]', $id))
            ->delete();

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }



	/**
     * 清除购物车
     *
     * @param  string $user_id [用户ID]
     * @param  array  $shop_goods_sku_ids [商品规格ID，索引数组]
     * @return bool
     */
    public function clear($user_id, $shop_cart_ids){
        $shop_cart_ids_str = '"'.implode('","', $shop_cart_ids).'"';

        $bool = db(parent::DB_APPLICATION_ID)
        ->table('shop_cart')
        ->where(array('user_id=[+]', $user_id))
        ->where(array('shop_cart_id in ([-])', $shop_cart_ids_str, true))
        ->delete();
		
        //清理缓存
        if( $bool ){
        	object(parent::CACHE)->clear(self::CACHE_KEY);
        }
		
        return $bool;
    }





    /**
     * 清除购物车
     *
     * @param  string $user_id [用户ID]
     * @param  array  $shop_goods_sku_ids [商品规格ID，索引数组]
     * @return bool
     */
    public function clear_cart($user_id, $shop_goods_sku_ids){
        $shop_goods_sku_ids_str = '"'.implode('","', $shop_goods_sku_ids).'"';

        $bool = db(parent::DB_APPLICATION_ID)
        ->table('shop_cart')
        ->where(array('user_id=[+]', $user_id))
        ->where(array('shop_goods_sku_id in ([-])', $shop_goods_sku_ids_str, true))
        ->delete();

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    //===========================================
    // 查询数据
    //===========================================


    /**
     * 获取所有的分页数据
     * 
     * $config = array(
     *  'where' => array(), //条件
     *  'orderby' => array(), //排序
     *  'limit' => array(0, page_size), //取出条数，默认获取10条
     * );
     * 
     * limit的分页算法是：当前页数-1 * page_size
     * 序号的算法：key键+1，+每页显示的条数。等于分页后的序号。{key + 1 + page_size}
     * 
     * 返回的数据：
     * $data = array(
     *  'row_count' => //数据总条数
     *  'limit_count' => //已取出条数
     *  'page_size' => //每页的条数
     *  'page_count' => //总页数
     *  'page_now' => //当前页数
     *  'data' => //数据
     * );
     * 
     * @param   array   $config     配置信息
     * @return  array
     */
    public function select_page($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config) {
            $call_where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $call_limit   = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select       = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();
            
            $limit = array(
                (isset($call_limit[0])? $call_limit[0] : 0),
                (isset($call_limit[1])? $call_limit[1] : 0)
            );

            //设置返回的数据
            $data = array(
                'row_count' => 0,
                'limit_count' => $limit[0] + $limit[1],
                'page_size' => $limit[1],
                'page_count' => 0,
                'page_now' => 0,
                'data' => array()
            );

            //获取总条数
            $total_count = db(parent::DB_APPLICATION_ID)
                ->table('shop_cart sc')
                ->call('where', $call_where)
                ->find('count(*) as count');

            //是否有数据
            if (empty($total_count['count'])) {
                return $data;
            } else {
                $data['row_count'] = $total_count['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count'] / $data['page_size']);
                    $data['page_now'] = ceil($limit[0] / $data['page_size']) + 1;
                }
            }

            //左连店铺表
            $join_shop = array(
                'table' => 'shop s',
                'type' => 'left',
                'on' => 's.shop_id = sc.shop_id'
            );

            //左连商品表
            $join_goods = array(
                'table' => 'shop_goods sg',
                'type' => 'left',
                'on' => 'sg.shop_goods_id = sc.shop_goods_id'
            );

            //左连商品规格表
            $join_sku = array(
                'table' => 'shop_goods_sku sku',
                'type' => 'left',
                'on' => 'sku.shop_goods_sku_id = sc.shop_goods_sku_id'
            );

            //左连商品属性表
            $join_spu = array(
                'table' => 'shop_goods_spu spu',
                'type' => 'left',
                'on' => 'find_in_set(spu.shop_goods_spu_id, sku.shop_goods_spu_id)'
            );

            //字段
            if (empty($select)) {
                $sql_join_cart_main_id = object(parent::TABLE_SHOP_GOODS_IMAGE)->sql_join_cart_main_id('sc');
                $select = array(
                    's.shop_name',
                    'sc.shop_id',
                    'sc.shop_cart_id AS cart_id',
                    'sc.shop_goods_id AS goods_id',
                    'sc.shop_goods_sku_id AS goods_sku_id',
                    'sc.shop_cart_json AS json',
                    'sc.shop_cart_number AS number',
                    'sg.shop_goods_name AS goods_name',
                    'sg.shop_goods_state',
                    'sg.shop_goods_trash',
                    'sku.shop_goods_sku_name AS sku_name',
                    'sku.image_id AS sku_image_id',
                    'sku.shop_goods_sku_stock AS sku_stock',
                    'sku.shop_goods_sku_price AS sku_price',
                    'GROUP_CONCAT(spu.shop_goods_spu_name) as spu_name',
                    '('.$sql_join_cart_main_id.') as goods_image_id',
                );
            }

            //查询数据
            $data['data'] =  db(parent::DB_APPLICATION_ID)
                ->table('shop_cart sc')
                ->joinon($join_shop)
                ->joinon($join_goods)
                ->joinon($join_sku)
                ->joinon($join_spu)
                ->call('where', $call_where)
                ->groupby('sc.shop_goods_sku_id')
                ->call('orderby', $call_orderby)
                ->call('limit', $call_limit)
                ->select($select);

            return $data;
        });
    }



    /**
     * 获取多个数据
     *  $config = array(
     *  'where' => array(), //条件
     *  'orderby' => array(), //排序
     *  'limit' => array(0, page_size), //取出条数，默认不限制
     * );
     * 
     * @param   array   $config
     * @return  array
     */
    public function select($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            return db(parent::DB_APPLICATION_ID)
                ->table('shop_cart')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select);
        });
    }


    /**
     * 查询全部
     * @author green
     * @param  array $config [查询配置]
     * @return array
     */
    public function fetch_all($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config) {
            // 查询配置
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();
            $call_join = isset($config['join']) && is_array($config['join']) ? $config['join'] : array();
            $call_where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();

            // 查询数据
            $data = db(parent::DB_APPLICATION_ID)
                ->table('shop_cart sc')
                ->call('joinon', $call_join)
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->select($select);

            return $data;
        });
    }


    /**
     * 查一条记录，根据主键
     * @param  string $id 商家用户表ID
     * @return array
     */
    public function find($id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($id), function ($id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('shop_cart')
                ->where(array('shop_cart_id=[+]', $id))
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
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where), function ($call_where) {
            return db(parent::DB_APPLICATION_ID)
                ->table('shop_cart')
                ->call('where', $call_where)
                ->find();
        });
    }


	
	/**
	 * 根据 用户id、sku ID、商品 ID 获取购物车数据
	 * 
	 * @param	string		$user_id
	 * @param	string		$shop_goods_sku_id
	 * @param	string		$shop_goods_id
	 * @return array
	 */
	public function find_user_sku_goods($user_id, $shop_goods_sku_id, $shop_goods_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id, $shop_goods_sku_id, $shop_goods_id), function ($user_id, $shop_goods_sku_id, $shop_goods_id) {
            return db(parent::DB_APPLICATION_ID)
            ->table('shop_cart')
            ->where( 
            	array('user_id=[+]', $user_id),
            	array('[and] shop_goods_sku_id=[+]', $shop_goods_sku_id), 
            	array('[and] shop_goods_id=[+]', $shop_goods_id) 
				)
            ->find();
        });
	}





    //===========================================
    // 检测数据
    //===========================================


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
	 * 获取购物车的商品、规格
	 * 
	 * @param	array		$cart_data
	 * @return	array
	 */
	public function get_data( $cart_data = array() ){
		$result = array(
			'shop_list' => array(),
			'shop_goods_list' => array(),
			'shop_goods_sku_list' => array(),
		);
		
		if( empty($cart_data) ){
			return $result;
		}
		
		$shop_ids = array();
		$shop_goods_ids = array();
		$shop_goods_sku_ids = array();
		foreach($cart_data as $key => $value){
			if( $value['shop_id'] != '' && !in_array($value['shop_id'], $shop_ids) ){
				$shop_ids[] = $value['shop_id'];
			}
			if( $value['shop_goods_id'] != '' && !in_array($value['shop_goods_id'], $shop_goods_ids) ){
				$shop_goods_ids[] = $value['shop_goods_id'];
			}
			if( $value['shop_goods_sku_id'] != '' && !in_array($value['shop_goods_sku_id'], $shop_goods_sku_ids) ){
				$shop_goods_sku_ids[] = $value['shop_goods_sku_id'];
			}
		}
		
		//获取店铺列表数据
		if( !empty($shop_ids) ){
			$shop_in_string = "\"" . implode("\",\"", $shop_ids) . "\"";
        	$result['shop_list'] = object(parent::TABLE_SHOP)->select(array(
				'where' => array(
					array('shop_id IN ([-])', $shop_in_string, true),
					array('[and] shop_state=1')
				),
				'select' => array(
					'shop_id',
					'shop_name'
				)
			));
		}else{
			$result['shop_list'] = array(
				array(
					'shop_id' => '',
					'shop_name' => '',
				)
			);
		}
		
		//更新限时购商品状态
        object(parent::TABLE_SHOP_GOODS_WHEN)->update_state_clear();
		//获取商品列表数据
		if( !empty($shop_goods_ids) ){
			$shop_goods_in_string = "\"" . implode("\",\"", $shop_goods_ids) . "\"";
			$sql_join_main_id = object(parent::TABLE_SHOP_GOODS_IMAGE)->sql_join_main_id('sg');
        	$result['shop_goods_list'] = object(parent::TABLE_SHOP_GOODS)->select_join(array(
				'where' => array(
					array('sg.shop_goods_id IN ([-])', $shop_goods_in_string, true),
				),
				'select' => array(
					'sg.*',
					'sgw.shop_goods_id as when_shop_goods_id',
					'sgw.shop_goods_when_state',
					'('.$sql_join_main_id.') as image_id'
				)
			));
		}
		
		//获取商品规格列表数据
		if( !empty($shop_goods_sku_ids) ){
			$shop_goods_sku_in_string = "\"" . implode("\",\"", $shop_goods_sku_ids) . "\"";
        	$result['shop_goods_sku_list'] = object(parent::TABLE_SHOP_GOODS_SKU)->select(array(
				'where' => array(
					array('shop_goods_sku_id IN ([-])', $shop_goods_sku_in_string, true),
				)
			));
			
			$config = array(
				'orderby' => array(
					array('shop_goods_spu_sort'),
					array('shop_goods_spu_parent_id'),
					array('shop_goods_spu_update_time'),
					array('shop_goods_spu_id'),
				),
	            'select' => array(
	                'shop_goods_spu_id',
	                'shop_goods_spu_parent_id',
	                'shop_goods_spu_name',
	                'image_id',
	            )
	        );
	        //商品属性(shop_goods_spu)数据
	        $result['shop_goods_sku_list'] = object(parent::TABLE_SHOP_GOODS)->get_spu_data($result['shop_goods_sku_list'], $config, $config);
		}
		
		
		return $result;
	}



	/**
	 * 获取购物车的商品、规格
	 * 
	 * @param	array		$cart_data
	 * @return	array
	 */
	public function get_datas( $cart_data = array() ){

		$result = array(
			'shop_list' => array(),
			'shop_goods_list' => array(),
			'shop_goods_sku_list' => array(),
		);
		
		if( empty($cart_data) ){
			return $result;
		}
		
		$shop_goods_ids = array();
		$shop_goods_sku_ids = array();
		foreach($cart_data as $key => $value){
			//获取店铺列表数据
			if( !empty($value['shop_id']) ){
				$merchant = object(parent::TABLE_MERCHANT)->find($value['shop_id']);
				$result['shop_list'][] = array(
					'merchant_id'=>!empty($merchant['merchant_id'])?$merchant['merchant_id']:'',
					'merchant_name'=>!empty($merchant['merchant_name'])?$merchant['merchant_name']:''
				); 
			}else{
				$result['shop_list'][] = array(
					'merchant_id' => '',
					'merchant_name' => '',
				);
			}
			if( $value['shop_goods_id'] != '' && !in_array($value['shop_goods_id'], $shop_goods_ids) ){
				$shop_goods_ids[] = $value['shop_goods_id'];
			}
			if( $value['shop_goods_sku_id'] != '' && !in_array($value['shop_goods_sku_id'], $shop_goods_sku_ids) ){
				$shop_goods_sku_ids[] = $value['shop_goods_sku_id'];
			}
		}
		
		
		
		//更新限时购商品状态
        object(parent::TABLE_SHOP_GOODS_WHEN)->update_state_clear();
		//获取商品列表数据
		if( !empty($shop_goods_ids) ){
			$shop_goods_in_string = "\"" . implode("\",\"", $shop_goods_ids) . "\"";
			$sql_join_main_id = object(parent::TABLE_SHOP_GOODS_IMAGE)->sql_join_main_id('sg');
        	$result['shop_goods_list'] = object(parent::TABLE_SHOP_GOODS)->select_join(array(
				'where' => array(
					array('sg.shop_goods_id IN ([-])', $shop_goods_in_string, true),
				),
				'select' => array(
					'sg.*',
					'sgw.shop_goods_id as when_shop_goods_id',
					'sgw.shop_goods_when_state',
					'('.$sql_join_main_id.') as image_id'
				)
			));
		}
		
		//获取商品规格列表数据
		if( !empty($shop_goods_sku_ids) ){
			$shop_goods_sku_in_string = "\"" . implode("\",\"", $shop_goods_sku_ids) . "\"";
        	$result['shop_goods_sku_list'] = object(parent::TABLE_SHOP_GOODS_SKU)->select(array(
				'where' => array(
					array('shop_goods_sku_id IN ([-])', $shop_goods_sku_in_string, true),
				)
			));
			
			$config = array(
				'orderby' => array(
					array('shop_goods_spu_sort'),
					array('shop_goods_spu_parent_id'),
					array('shop_goods_spu_update_time'),
					array('shop_goods_spu_id'),
				),
	            'select' => array(
	                'shop_goods_spu_id',
	                'shop_goods_spu_parent_id',
	                'shop_goods_spu_name',
	                'image_id',
	            )
	        );
	        //商品属性(shop_goods_spu)数据
	        $result['shop_goods_sku_list'] = object(parent::TABLE_SHOP_GOODS)->get_spu_data($result['shop_goods_sku_list'], $config, $config);
		}
		
		
		return $result;
	}




	/**
	 * 获取购物车商品列表
	 * 
	 * @param	string	$shop_goods_id
	 * @param	array	$shop_goods_list_data
	 * @param	array	$shop_goods_sku_list_data
	 * @return	array
	 */
	public function get_goods($cart_data, $shop_goods_list_data, $shop_goods_sku_list_data){
		$result = array(
			'state' => 'OK',//状态码，“OK”合法商品、“SKUNOTEXIST”规格不存在、“SKUNOTSTOCK”库存不足、“GOODSNOTEXIST”商品不存在、“GOODSUNSHELVE”商品已下架、“GOODSWHENEND”限时商品已结束、“GOODSWHENNOTSTART”限时商品未开始
			'id' => '',//商品id
			'sn' => '',//商品货号
			'name' => '',//商品名称
			'logo' => '',//先判断规格图片是否存在，不存在则保存商品主图
			'image_id' => '',//商品主图
			'when' => 0,//是否是限时商品，0不是，1是
			'property' => 0,//商品类型
			'index' => 0,//商品索引标记
			'sku_id' => '',//规格ID
			'sku_image_id' => '',//规格主图
			'stock' => 0,//库存数量
			'stock_mode' => 0,//减库存方式
            'price' => 0,//商品价格
			'spu_array' => array(),
			'spu_string' => '',//spu拼接字符串
			'cart_id' => $cart_data['shop_cart_id'],//购物车ID
			'number' => $cart_data['shop_cart_number'],//购买数量
			'timestamp' => $cart_data['shop_cart_update_time'],//最后的更新时间
			'format_time' => cmd(array($cart_data['shop_cart_update_time']), 'time format'),//格式化时间戳 口语化时间
			'time' => cmd(array($cart_data['shop_cart_update_time'], 'Y/m/d H:i'), 'time date'),
			'recommend' => array(),//推荐人ID
			//下面是收集数据
			'shop_goods' => array(),
            'shop_goods_sku' => array(),
            'shipping_price' => 0,
		);
		
		//获取旧数据
		if( !empty($cart_data['shop_cart_json']) ){
			$cart_data['shop_cart_json'] = cmd(array($cart_data['shop_cart_json']), 'json decode');
		}
		if( !is_array($cart_data['shop_cart_json']) ){
			$cart_data['shop_cart_json'] = array();
		}
		
		if( isset($cart_data['shop_cart_json']['recommend_user_id']) ){
			$result['recommend']['user_id'] = $cart_data['shop_cart_json']['recommend_user_id'];
		}
		
		if( !empty($cart_data['shop_cart_json']['goods']['when_shop_goods_id']) ){
			$result['when'] = 1;
		}
		if( isset($cart_data['shop_cart_json']['goods']['image_id']) ){
			$result['image_id'] = $cart_data['shop_cart_json']['goods']['image_id'];
		}
		if( isset($cart_data['shop_cart_json']['goods']['shop_goods_id']) ){
			$result['id'] = $cart_data['shop_cart_json']['goods']['shop_goods_id'];
		}
		if( isset($cart_data['shop_cart_json']['goods']['shop_goods_name']) ){
			$result['name'] = $cart_data['shop_cart_json']['goods']['shop_goods_name'];
		}
		if( isset($cart_data['shop_cart_json']['goods']['shop_goods_sn']) ){
			$result['sn'] = $cart_data['shop_cart_json']['goods']['shop_goods_sn'];
		}
		if( isset($cart_data['shop_cart_json']['goods']['shop_goods_index']) ){
			$result['index'] = $cart_data['shop_cart_json']['goods']['shop_goods_index'];
		}
		if( isset($cart_data['shop_cart_json']['goods']['shop_goods_property']) ){
			$result['property'] = $cart_data['shop_cart_json']['goods']['shop_goods_property'];
		}
		if( isset($cart_data['shop_cart_json']['goods']['shop_goods_stock_mode']) ){
			$result['stock_mode'] = $cart_data['shop_cart_json']['goods']['shop_goods_stock_mode'];
        }
        if( isset($cart_data['shop_cart_json']['goods']['shop_goods_shipping_price']) ){
			$result['shipping_price'] = $cart_data['shop_cart_json']['goods']['shop_goods_shipping_price'];
		}

		if( isset($cart_data['shop_cart_json']['goods_sku']['shop_goods_sku_id']) ){
			$result['sku_id'] = $cart_data['shop_cart_json']['goods_sku']['shop_goods_sku_id'];
		}
		if( isset($cart_data['shop_cart_json']['goods_sku']['image_id']) ){
			$result['sku_image_id'] = $cart_data['shop_cart_json']['goods_sku']['image_id'];
		}
		if( isset($cart_data['shop_cart_json']['goods_sku']['shop_goods_sku_stock']) ){
			$result['stock'] = $cart_data['shop_cart_json']['goods_sku']['shop_goods_sku_stock'];
		}
		if( isset($cart_data['shop_cart_json']['goods_sku']['shop_goods_sku_price']) ){
			$result['price'] = $cart_data['shop_cart_json']['goods_sku']['shop_goods_sku_price'];
		}
		if( !empty($cart_data['shop_cart_json']['goods_sku']['shop_goods_spu']) ){
			foreach($cart_data['shop_cart_json']['goods_sku']['shop_goods_spu'] as $json_spu_k => $json_spu_v){
				$parent_spu_name = empty($json_spu_v['parent']['shop_goods_spu_name'])? '':$json_spu_v['parent']['shop_goods_spu_name'];
				
				//获取拼凑的数据
				if( $result['spu_string'] != '' ){
					$result['spu_string'] .= ';';
				}
				$result['spu_string'] .= $parent_spu_name.':'.$json_spu_v['shop_goods_spu_name'];
				
				$result['spu_array'][] = array(
					'id' => $json_spu_v['shop_goods_spu_id'],
					'name' => $json_spu_v['shop_goods_spu_name'],
					'image_id' => $json_spu_v['image_id'],
					'parent' => array(
						'id' => empty($json_spu_v['parent']['shop_goods_spu_id'])? '':$json_spu_v['parent']['shop_goods_spu_id'],
						'name' => $parent_spu_name,
						'image_id' => empty($json_spu_v['parent']['image_id'])? '':$json_spu_v['parent']['image_id'],
						)
				);
			}
		}

		//显示图片
		$result['logo'] = !empty($result['sku_image_id'])? $result['sku_image_id']:$result['image_id'];
		
		
		
		//匹配商品
		foreach($shop_goods_list_data as $sg_key => $sg_value){
			if($cart_data['shop_goods_id'] == $sg_value['shop_goods_id']){
				$result['shop_goods'] = $sg_value;
				break;
			}
		}
		
		//新数据没有，那么获取旧数据
		if( empty($result['shop_goods']) ){
			$result['state'] = 'GOODSNOTEXIST';//商品不存在
			return $result;
		}
		
		
		$result['id'] = $result['shop_goods']['shop_goods_id'];
		$result['name'] = $result['shop_goods']['shop_goods_name'];
		$result['sn'] = $result['shop_goods']['shop_goods_sn'];
		$result['property'] = $result['shop_goods']['shop_goods_property'];
		$result['index'] = $result['shop_goods']['shop_goods_index'];
		$result['stock_mode'] = $result['shop_goods']['shop_goods_stock_mode'];
		$result['image_id'] = $result['shop_goods']['image_id'];
		$result['logo'] = $result['shop_goods']['image_id'];
		$result['sku_id'] = $cart_data['shop_goods_sku_id'];
		
		
		//限时商品
		if( !empty($result['shop_goods']['when_shop_goods_id']) ){
			if( empty($result['shop_goods']['shop_goods_when_state']) || $result['shop_goods']['shop_goods_when_state'] == 0 ){
				$result['state'] = 'GOODSWHENEND';//限时商品已结束
				return $result;
			}
			if( !empty($result['shop_goods']['shop_goods_when_state']) && $result['shop_goods']['shop_goods_when_state'] == 2 ){
				$result['state'] = 'GOODSWHENNOTSTART';//限时商品未开始
				return $result;
			}
		}
		
		
		//判断商品状态
		if( $result['shop_goods']['shop_goods_state'] != 1 ){
			$result['state'] = 'GOODSUNSHELVE';//商品已下架
			return $result;
		}
		
		//匹配规格
		foreach($shop_goods_sku_list_data as $sgs_key => $sgs_value){
			if($cart_data['shop_goods_sku_id'] == $sgs_value['shop_goods_sku_id']){
				$result['shop_goods_sku'] = $sgs_value;
				break;
			}
		}
		//判断规格数据
		if( empty($result['shop_goods_sku']) ){
			$result['state'] = 'SKUNOTEXIST';//规格不存在
			return $result;
		}
		
		$result['sku_image_id'] = $result['shop_goods_sku']['image_id'];
		$result['stock'] = $result['shop_goods_sku']['shop_goods_sku_stock'];
		$result['price'] = $result['shop_goods_sku']['shop_goods_sku_price'];
		$result['logo'] = !empty($result['sku_image_id'])? $result['sku_image_id']:$result['shop_goods']['image_id'];//显示图片
		
		$result['spu_string'] = '';
		$result['spu_array'] = array();
		if( !empty($result['shop_goods_sku']['shop_goods_spu']) ){
			foreach($result['shop_goods_sku']['shop_goods_spu'] as $spu_key => $spu_value){
				$parent_spu_name = empty($spu_value['parent']['shop_goods_spu_name'])? '':$spu_value['parent']['shop_goods_spu_name'];
				
				//获取拼凑的数据
				if( $result['spu_string'] != '' ){
					$result['spu_string'] .= ';';
				}
				$result['spu_string'] .= $parent_spu_name.':'.$spu_value['shop_goods_spu_name'];
				
				$result['spu_array'][] = array(
					'id' => $spu_value['shop_goods_spu_id'],
					'name' => $spu_value['shop_goods_spu_name'],
					'image_id' => $spu_value['image_id'],
					'parent' => array(
						'id' => empty($spu_value['parent']['shop_goods_spu_id'])? '':$spu_value['parent']['shop_goods_spu_id'],
						'name' => $parent_spu_name,
						'image_id' => empty($spu_value['parent']['image_id'])? '':$spu_value['parent']['image_id'],
						)
				);
			}
		}
		
		//判断库存
		if( empty($result['shop_goods_sku']['shop_goods_sku_stock']) || 
		$cart_data['shop_cart_number'] > $result['shop_goods_sku']['shop_goods_sku_stock'] ){
			$result['state'] = 'SKUNOTSTOCK';//库存不足
		}
		
		return $result;
	}






    
	/**
     * ------Mr.Zhao------2019.07.22------
     * 
	 * emshop获取购物车商品列表
	 * 
	 * @param	string	$shop_goods_id
	 * @param	array	$shop_goods_list_data
	 * @param	array	$shop_goods_sku_list_data
	 * @return	array
	 */
	public function emshop_get_goods($cart_data, $shop_goods_list_data, $shop_goods_sku_list_data){
		$result = array(
			'state' => 'OK',//状态码，“OK”合法商品、“SKUNOTEXIST”规格不存在、“SKUNOTSTOCK”库存不足、“GOODSNOTEXIST”商品不存在、“GOODSUNSHELVE”商品已下架、“GOODSWHENEND”限时商品已结束、“GOODSWHENNOTSTART”限时商品未开始
			'id' => '',//商品id
			'sn' => '',//商品货号
			'name' => '',//商品名称
			'logo' => '',//先判断规格图片是否存在，不存在则保存商品主图
			'image_id' => '',//商品主图
			'when' => 0,//是否是限时商品，0不是，1是
			'property' => 0,//商品类型
			'index' => 0,//商品索引标记
			'sku_id' => '',//规格ID
			'sku_image_id' => '',//规格主图
			'stock' => 0,//库存数量
			'stock_mode' => 0,//减库存方式
            'price' => 0,//商品价格
            'additional_money' => 0,//商品附加人民币
            'additional_credit' => 0,//商品附加积分
			'spu_array' => array(),
			'spu_string' => '',//spu拼接字符串
			'cart_id' => $cart_data['shop_cart_id'],//购物车ID
			'number' => $cart_data['shop_cart_number'],//购买数量
			'timestamp' => $cart_data['shop_cart_update_time'],//最后的更新时间
			'format_time' => cmd(array($cart_data['shop_cart_update_time']), 'time format'),//格式化时间戳 口语化时间
			'time' => cmd(array($cart_data['shop_cart_update_time'], 'Y/m/d H:i'), 'time date'),
			'recommend' => array(),//推荐人ID
			//下面是收集数据
			'shop_goods' => array(),
			'shop_goods_sku' => array(),
		);
		
		//获取旧数据
		if( !empty($cart_data['shop_cart_json']) ){
			$cart_data['shop_cart_json'] = cmd(array($cart_data['shop_cart_json']), 'json decode');
		}
		if( !is_array($cart_data['shop_cart_json']) ){
			$cart_data['shop_cart_json'] = array();
		}
		
		if( isset($cart_data['shop_cart_json']['recommend_user_id']) ){
			$result['recommend']['user_id'] = $cart_data['shop_cart_json']['recommend_user_id'];
		}
		
		if( !empty($cart_data['shop_cart_json']['goods']['when_shop_goods_id']) ){
			$result['when'] = 1;
		}
		if( isset($cart_data['shop_cart_json']['goods']['image_id']) ){
			$result['image_id'] = $cart_data['shop_cart_json']['goods']['image_id'];
		}
		if( isset($cart_data['shop_cart_json']['goods']['shop_goods_id']) ){
			$result['id'] = $cart_data['shop_cart_json']['goods']['shop_goods_id'];
		}
		if( isset($cart_data['shop_cart_json']['goods']['shop_goods_name']) ){
			$result['name'] = $cart_data['shop_cart_json']['goods']['shop_goods_name'];
		}
		if( isset($cart_data['shop_cart_json']['goods']['shop_goods_sn']) ){
			$result['sn'] = $cart_data['shop_cart_json']['goods']['shop_goods_sn'];
		}
		if( isset($cart_data['shop_cart_json']['goods']['shop_goods_index']) ){
			$result['index'] = $cart_data['shop_cart_json']['goods']['shop_goods_index'];
		}
		if( isset($cart_data['shop_cart_json']['goods']['shop_goods_property']) ){
			$result['property'] = $cart_data['shop_cart_json']['goods']['shop_goods_property'];
		}
		if( isset($cart_data['shop_cart_json']['goods']['shop_goods_stock_mode']) ){
			$result['stock_mode'] = $cart_data['shop_cart_json']['goods']['shop_goods_stock_mode'];
		}

		if( isset($cart_data['shop_cart_json']['goods_sku']['shop_goods_sku_id']) ){
			$result['sku_id'] = $cart_data['shop_cart_json']['goods_sku']['shop_goods_sku_id'];
		}
		if( isset($cart_data['shop_cart_json']['goods_sku']['image_id']) ){
			$result['sku_image_id'] = $cart_data['shop_cart_json']['goods_sku']['image_id'];
		}
		if( isset($cart_data['shop_cart_json']['goods_sku']['shop_goods_sku_stock']) ){
			$result['stock'] = $cart_data['shop_cart_json']['goods_sku']['shop_goods_sku_stock'];
		}
		if( isset($cart_data['shop_cart_json']['goods_sku']['shop_goods_sku_price']) ){
			$result['price'] = $cart_data['shop_cart_json']['goods_sku']['shop_goods_sku_price'];
		}
		if( isset($cart_data['shop_cart_json']['goods_sku']['shop_goods_sku_additional_money']) ){
			$result['additional_money'] = $cart_data['shop_cart_json']['goods_sku']['shop_goods_sku_additional_money'];
		}
		if( isset($cart_data['shop_cart_json']['goods_sku']['shop_goods_sku_additional_credit']) ){
			$result['additional_credit'] = $cart_data['shop_cart_json']['goods_sku']['shop_goods_sku_additional_credit'];
		}
		if( !empty($cart_data['shop_cart_json']['goods_sku']['shop_goods_spu']) ){
			foreach($cart_data['shop_cart_json']['goods_sku']['shop_goods_spu'] as $json_spu_k => $json_spu_v){
				$parent_spu_name = empty($json_spu_v['parent']['shop_goods_spu_name'])? '':$json_spu_v['parent']['shop_goods_spu_name'];
				
				//获取拼凑的数据
				if( $result['spu_string'] != '' ){
					$result['spu_string'] .= ';';
				}
				$result['spu_string'] .= $parent_spu_name.':'.$json_spu_v['shop_goods_spu_name'];
				
				$result['spu_array'][] = array(
					'id' => $json_spu_v['shop_goods_spu_id'],
					'name' => $json_spu_v['shop_goods_spu_name'],
					'image_id' => $json_spu_v['image_id'],
					'parent' => array(
						'id' => empty($json_spu_v['parent']['shop_goods_spu_id'])? '':$json_spu_v['parent']['shop_goods_spu_id'],
						'name' => $parent_spu_name,
						'image_id' => empty($json_spu_v['parent']['image_id'])? '':$json_spu_v['parent']['image_id'],
						)
				);
			}
		}

		//显示图片
		$result['logo'] = !empty($result['sku_image_id'])? $result['sku_image_id']:$result['image_id'];
		
		
		
		//匹配商品
		foreach($shop_goods_list_data as $sg_key => $sg_value){
			if($cart_data['shop_goods_id'] == $sg_value['shop_goods_id']){
				$result['shop_goods'] = $sg_value;
				break;
			}
		}
		
		//新数据没有，那么获取旧数据
		if( empty($result['shop_goods']) ){
			$result['state'] = 'GOODSNOTEXIST';//商品不存在
			return $result;
		}
		
		
		$result['id'] = $result['shop_goods']['shop_goods_id'];
		$result['name'] = $result['shop_goods']['shop_goods_name'];
		$result['sn'] = $result['shop_goods']['shop_goods_sn'];
		$result['property'] = $result['shop_goods']['shop_goods_property'];
		$result['index'] = $result['shop_goods']['shop_goods_index'];
		$result['stock_mode'] = $result['shop_goods']['shop_goods_stock_mode'];
		$result['image_id'] = $result['shop_goods']['image_id'];
		$result['logo'] = $result['shop_goods']['image_id'];
		$result['sku_id'] = $cart_data['shop_goods_sku_id'];
		
		
		//限时商品
		if( !empty($result['shop_goods']['when_shop_goods_id']) ){
			if( empty($result['shop_goods']['shop_goods_when_state']) || $result['shop_goods']['shop_goods_when_state'] == 0 ){
				$result['state'] = 'GOODSWHENEND';//限时商品已结束
				return $result;
			}
			if( !empty($result['shop_goods']['shop_goods_when_state']) && $result['shop_goods']['shop_goods_when_state'] == 2 ){
				$result['state'] = 'GOODSWHENNOTSTART';//限时商品未开始
				return $result;
			}
		}
		
		
		//判断商品状态
		if( $result['shop_goods']['shop_goods_state'] != 1 ){
			$result['state'] = 'GOODSUNSHELVE';//商品已下架
			return $result;
		}
		
		//匹配规格
		foreach($shop_goods_sku_list_data as $sgs_key => $sgs_value){
			if($cart_data['shop_goods_sku_id'] == $sgs_value['shop_goods_sku_id']){
				$result['shop_goods_sku'] = $sgs_value;
				break;
			}
		}
		//判断规格数据
		if( empty($result['shop_goods_sku']) ){
			$result['state'] = 'SKUNOTEXIST';//规格不存在
			return $result;
		}
		
		$result['sku_image_id'] = $result['shop_goods_sku']['image_id'];
		$result['stock'] = $result['shop_goods_sku']['shop_goods_sku_stock'];
		$result['price'] = $result['shop_goods_sku']['shop_goods_sku_price'];
		
		if( isset($result['shop_goods_sku']['shop_goods_sku_additional_money']) )
		$result['additional_money'] = $result['shop_goods_sku']['shop_goods_sku_additional_money'];
		if( isset($result['shop_goods_sku']['shop_goods_sku_additional_credit']) )
		$result['additional_credit'] = $result['shop_goods_sku']['shop_goods_sku_additional_credit'];
		$result['logo'] = !empty($result['sku_image_id'])? $result['sku_image_id']:$result['shop_goods']['image_id'];//显示图片
		
		$result['spu_string'] = '';
		$result['spu_array'] = array();
		if( !empty($result['shop_goods_sku']['shop_goods_spu']) ){
			foreach($result['shop_goods_sku']['shop_goods_spu'] as $spu_key => $spu_value){
				$parent_spu_name = empty($spu_value['parent']['shop_goods_spu_name'])? '':$spu_value['parent']['shop_goods_spu_name'];
				
				//获取拼凑的数据
				if( $result['spu_string'] != '' ){
					$result['spu_string'] .= ';';
				}
				$result['spu_string'] .= $parent_spu_name.':'.$spu_value['shop_goods_spu_name'];
				
				$result['spu_array'][] = array(
					'id' => $spu_value['shop_goods_spu_id'],
					'name' => $spu_value['shop_goods_spu_name'],
					'image_id' => $spu_value['image_id'],
					'parent' => array(
						'id' => empty($spu_value['parent']['shop_goods_spu_id'])? '':$spu_value['parent']['shop_goods_spu_id'],
						'name' => $parent_spu_name,
						'image_id' => empty($spu_value['parent']['image_id'])? '':$spu_value['parent']['image_id'],
						)
				);
			}
		}
		
		//判断库存
		if( empty($result['shop_goods_sku']['shop_goods_sku_stock']) || 
		$cart_data['shop_cart_number'] > $result['shop_goods_sku']['shop_goods_sku_stock'] ){
			$result['state'] = 'SKUNOTSTOCK';//库存不足
		}
		
		return $result;
	}







}