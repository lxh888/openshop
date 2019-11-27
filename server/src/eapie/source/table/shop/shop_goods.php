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
class shop_goods extends main {
	
	
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(
		__CLASS__, 
		"shop_goods_file", 
		"shop_goods_image",
		"shop_goods_sku",
		"shop_goods_spu",
		"shop_goods_type"
	);
	
	
	
	/**
	 * 数据检测
	 * 
	 * @var	array
	 */
	public $check = array(
		'shop_goods_id' => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少商品ID参数"),
					'echo'=>array("商品ID数据类型不合法"),
					'!null'=>array("商品ID不能为空"),
					),
			//检查编号是否存在		
			'exists_id'=>array(
					'method'=>array(array(parent::TABLE_SHOP_GOODS, 'find_exists_id'), "商品ID有误，数据不存在",) 
			),
			//检查编号是否合法	
			'legal_id'=>array(
					'method'=>array(array(parent::TABLE_SHOP_GOODS, 'find_legal_id'), "商品ID有误，未发布或者不存在") 
			),
		),
		
		"shop_goods_parent_id" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("商品的父ID数据类型不合法"),
					),
			'exists_id'=>array(
					'method'=>array(array(parent::TABLE_SHOP_GOODS, 'find_exists_id'), "商品的父ID有误，数据不存在",) 
			),		
		),
		
		
		"shop_goods_sn" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("商品货号的数据类型不合法"),
					),
			//字符长度检测
			'length' => array(
					'<length'=>array(200, "商品货号的字符长度太多")
					),			
		),
		
		
		"shop_goods_name" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少商品名称参数"),
					'echo'=>array("商品名称数据类型不合法"),
					'!null'=>array("商品名称不能为空"),
					),
			//字符长度检测
			'length' => array(
					'<length'=>array(200, "商品名称的字符长度太多")
					),		
		),
		
		"shop_goods_property" => array(
			//商品类型。0普通商品，1是积分商品
			'args'=>array(
					'exist'=>array("缺少商品类型参数"),
					'echo'=>array("商品类型的数据类型不合法"),
					'match'=>array('/^[01]{1}$/', "商品类型必须是0、1"),
					),
        ),
        

		"shop_goods_index" => array(
			//商品索引标记
			'args'=>array(
					'echo'=>array("商品索引标记的数据类型不合法"),
					'match'=>array('/^[\d]{1,}$/', "商品商品索引标记必须是整数"),
					),
		),
		
		
		"shop_goods_info" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("商品简介的数据类型不合法"),
					),
		),
		
		
		"shop_goods_state" => array(
			//状态。0审核失败；1售卖中；2待审核；3停售编辑中; 
			'args'=>array(
					'echo'=>array("商品状态的数据类型不合法"),
					'match'=>array('/^[0123]{1}$/', "商品状态值必须是0、1、2、3"),
					),
		),
		
		
		"shop_goods_warning" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("商品警告的数据类型不合法"),
					),
		),
		
		
		"shop_goods_details" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("商品详细的数据类型不合法"),
					),
		),
		
		
		
		"shop_goods_sort" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("商品排序的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/', "分类排序必须是整数"),
					),
		),
		
		
		"shop_goods_stock_warning" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("商品库存警告的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/', "商品库存警告必须是整数"),
					),
		),
		
		
		"shop_goods_stock_mode" => array(
			//减库存方式。0表示不减库存；1表示下单预扣，如果买家在保留时间内未付款，那预扣库存会被释放出来重新供大家购买。有效时间以全局配置默认。2表示付款减库存。退款则恢复库存。3表示发货减库存,退货则恢复库存。
			'args'=>array(
					'echo'=>array("商品减库存方式的数据类型不合法"),
					'match'=>array('/^[0123]{1}$/', "商品减库存方式必须是0、1、2、3"),
					),
		),
		
		
		
		"shop_goods_keywords" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("商品关键字的数据类型不合法"),
					),
		),
		
		"shop_goods_description" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("商品描述的数据类型不合法"),
					),
		),
		
		
		
		"shop_goods_seller_note" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("商品卖家备注的数据类型不合法"),
					),
		),
		
		
		"shop_goods_admin_note" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("商品管理员备注的数据类型不合法"),
					),
		),
		
		
		"shop_goods_trash" => array(
			//是否已回收。0正常；1已回收
			'args'=>array(
					'echo'=>array("商品回收的数据类型不合法"),
					'match'=>array('/^[01]{1}$/iu', "商品回收值必须是0或1"),
					),
        ),
        

        'shop_goods_recommend_money' => array(
            //参数检测
            'args'=>array(
                'exist'=>array("缺少商品规格推荐购买奖金参数"),
                'echo'=>array("商品规格推荐购买奖金数据类型不合法"),
                '!null'=>array("商品规格推荐购买奖金不能为空"),
            ),
        ),
		
		'shop_goods_ticket_address' => array(
            //参数检测
            'args'=>array(
                'exist'=>array("缺少商品兑票点详细地址参数"),
                'echo'=>array("商品兑票点详细地址不合法"),
                '!null'=>array("商品兑票点详细地址不能为空"),
            ),
        ),

        'shop_id' => array(
            //参数检测
            'args'=>array(
                'echo'=>array('店铺ID参数错误'),
                '!null'=>array('店铺ID不能为空')
            ),
        ),

        //商家ID
        'merchant_id' => array(
            //参数检测
            'args'=>array(
                'echo'=>array('商家ID参数错误'),
                '!null'=>array('商家ID不能为空')
            ),
        ),

        'goods_name' => array(
            //参数检测
            'args'=>array(
                'exist'=>array("缺少商品名称"),
                'echo'=>array('商品名称错误'),
                '!null'=>array('商品名称不能为空')
            ),
            //字符长度检测
			'length' => array(
                '<length'=>array(200, "商品名称的字符长度太多")
            ),
        ),

        'type_id' => array(
            //参数检测
            'args'=>array(
                'exist'=>array("缺少商品分类"),
                'echo'=>array('分类参数错误'),
                '!null'=>array('分类参数不能为空')
            ),
        ),

        'goods_stock' => array(
            //参数检测
            'args'=>array(
                'echo'=>array('库存参数错误'),
                '!null'=>array('库存参数不能为空')
            ),
        ),

        'money' => array(
            //参数检测
            'args'=>array(
                'exist'=>array("缺少商品价格"),
                'echo'=>array('商品价格参数错误'),
                '!null'=>array('商品价格参数不能为空')
            ),
        ),
        
		
	);
	
	
	
		
	/**
	 * 获取一个id号
	 * 
	 * @param	void
	 * @return	string
	 */
	public function get_unique_id(){
		return cmd(array(22), 'random autoincrement');
	}	
	
	
	
	
	
	
				
	/**
	 * 根据ID，判断是否存在
	 * 
	 * @param	string 		$shop_goods_id
	 */
	public function find_exists_id($shop_goods_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_goods_id), function($shop_goods_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('shop_goods')
			->where(array('shop_goods_id=[+]', (string)$shop_goods_id))
			->find('shop_goods_id');
		});
	}
	
		
		
		
	/**
	 * 获取一个数据
	 * 
	 * @param	string	$shop_goods_id
	 * @return	array
	 */
	public function find($shop_goods_id = ''){
		if( empty($shop_goods_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_goods_id), function($shop_goods_id){
			return db(parent::DB_APPLICATION_ID)
			->table('shop_goods')
			->where(array('shop_goods_id=[+]', (string)$shop_goods_id))
			->find();
		});
		
    }	
    
	
	
	
	/**
     * 查一条记录，根据主键\商家ID
	 * 
     * @param  string $shop_goods_id 	商品表ID
	 * @param  string $shop_id			商家ID或店铺ID
     * @return array
     */
    public function find_shop($shop_goods_id = '', $shop_id = ''){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_goods_id, $shop_id), function ($shop_goods_id, $shop_id) {
            return db(parent::DB_APPLICATION_ID)
            ->table('shop_goods')
            ->where(array('shop_goods_id=[+]', (string)$shop_goods_id), array('shop_id=[+]', (string)$shop_id))
            ->find();
        });
    }
	
	
	

    /**
	 * 获取一个数据
	 * 
	 * @param	string	$shop_goods_id
	 * @return	array
	 */
    public function find_where($call_where=array(),$call_data=array()){
        if( empty($call_where) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where,$call_data), function($call_where,$call_data){
			return db(parent::DB_APPLICATION_ID)
			->table('shop_goods')
            ->call('where',$call_where)
            ->call('data',$call_data)
			->find();
		});
    }
	
	
	
	/**
	 * 获取一个数据
	 * 
	 * @param	string	$shop_goods_id
	 * @return	array
	 */
	public function find_join($shop_goods_id = '', $find = array()){
		if( empty($shop_goods_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_goods_id, $find), function($shop_goods_id, $find){
			//限时商品表
			$join_shop_goods_when = array(
				'table' => 'shop_goods_when sgw',
				'type' 	=> 'left',
				'on' => 'sgw.shop_goods_id = sg.shop_goods_id'
			);
		
			if( empty($find) ){
				$find = array(
					'sg.*',
					'sgw.shop_goods_id as when_shop_goods_id',
					'sgw.shop_goods_when_state',
				);
			}
		
			return db(parent::DB_APPLICATION_ID)
			->table('shop_goods sg')
			->joinon($join_shop_goods_when)
			->where(array('sg.shop_goods_id=[+]', (string)$shop_goods_id))
			->find($find);
		});
		
	}	
	
	
	
	
	/**
	 * 根据ID，判断商品是否合法（1已审核并发布）
	 * shop_goods_state 状态。0未通过审核；1已审核并发布；2待审核；3编辑中
	 * 
	 * @param	string 		$shop_goods_id
	 */
	public function find_legal_id($shop_goods_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_goods_id), function($shop_goods_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('shop_goods')
			->where(array('shop_goods_id=[+]', (string)$shop_goods_id), array("shop_goods_state=1"))
			->find('shop_goods_id');
		});
	}
		
		
		
	/**
	 * 更新数据
	 * 
	 * @param	array		$where
	 * @param	array		$data
	 * @param	array		$call_data
	 * @return	bool
	 */
	public function update($where = array(), $data = array(), $call_data = array()){
		if( empty($where) || (empty($data) && empty($call_data)) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('shop_goods')
		->call('where', $where)
		->call('data', $call_data)
		->update($data);
		
		if( !empty($bool) ){
			$microtime = microtime(true);
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
			file_put_contents(CACHE_PATH."/缓存清理时间", (microtime(true)-$microtime));
		}
		
		return $bool;
	}	
			
		
		
				
	/**
	 * 根据唯一标识，删除数据
	 * 
	 * @param	array	$shop_goods_id
	 * @return	array
	 */
	public function remove($shop_goods_id = ''){
		if( empty($shop_goods_id) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('shop_goods')
		->where(array('shop_goods_id=[+]', (string)$shop_goods_id))
		->delete();
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}		
		
	
	
	
	
		
		
	/**
	 * 插入新数据
	 * 
	 * @param	array		$data			数据
	 * @param	array		$call_data		数据
	 * @return	bool
	 */
	public function insert($data = array(), $call_data = array()){
		if( empty($data) && empty($call_data) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('shop_goods')
		->call('data', $call_data)
		->insert($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}	
	
	/**
     * 批量插入数据
     * 
     * @param   array $data 数据，索引数组
     * @return  bool
     */
    public function insert_batch($data = array())
    {
        if (empty($data))
            return false;

        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_goods')
            ->batch()
            ->insert($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }
	
	
	
				
	/**
	 * 获取多条数据
	 *  $config = array(
	 * 	'where' => array(), //条件
	 * 	'orderby' => array(), //排序
	 * 	'limit'	=> array(0, page_size), //取出条数，默认不限制
	 *  'select' => array(),//查询的字段，可以是数组和字符串
	 * );
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function select($config = array()){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
			$where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
			$orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
			$limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
			$select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
			
			return db(parent::DB_APPLICATION_ID)
			->table('shop_goods')
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
		});
		
	}		
	
	
	
	/**
	 * 获取多条数据
	 *  $config = array(
	 * 	'where' => array(), //条件
	 * 	'orderby' => array(), //排序
	 * 	'limit'	=> array(0, page_size), //取出条数，默认不限制
	 *  'select' => array(),//查询的字段，可以是数组和字符串
	 * );
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function select_join($config = array()){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
			$where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
			$orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
			$limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
			$select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
			
			//限时商品表
			$join_shop_goods_when = array(
				'table' => 'shop_goods_when sgw',
				'type' 	=> 'left',
				'on' => 'sgw.shop_goods_id = sg.shop_goods_id'
			);
			
			return db(parent::DB_APPLICATION_ID)
			->table('shop_goods sg')
			->joinon($join_shop_goods_when)
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
		});
		
	}		
	
	
		
		
	/**
	 * 获取所有的分页数据
	 * 
	 * $config = array(
	 * 	'where' => array(), //条件
	 * 	'orderby' => array(), //排序
	 * 	'limit'	=> array(0, page_size), //取出条数，默认获取10条
	 * );
	 * 
	 * limit的分页算法是：当前页数-1 * page_size
	 * 序号的算法：key键+1，+每页显示的条数。等于分页后的序号。{key + 1 + page_size}
	 * 
	 * 返回的数据：
	 * $data = array(
	 * 	'row_count' => //数据总条数
	 * 	'limit_count' => //已取出条数
	 * 	'page_size' => //每页的条数
	 * 	'page_count' => //总页数
	 *  'page_now' => //当前页数
	 * 	'data' => //数据
	 * );
	 * 
	 * @param	array	$config		配置信息
	 * @return	array
	 */
	public function select_page($config = array()){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
			$call_where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
			$call_orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
			$call_limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
			$select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
			
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
			
			//用户数据
			$user = array(
				'table' => 'user u',
				'type' => 'left',
				'on' => 'u.user_id = sg.user_id'
			);

			//左连规格表
//			$join_sku = array(
//				'table' => 'shop_goods_sku sku',
//				'type' 	=> 'left',
//				'on' => 'sku.shop_goods_id = sg.shop_goods_id'
//			);
			
			
			//限时商品表
			$join_shop_goods_when = array(
				'table' => 'shop_goods_when sgw',
				'type' 	=> 'left',
				'on' => 'sgw.shop_goods_id = sg.shop_goods_id'
			);
			
			//先获取总条数
			$find_data = db(parent::DB_APPLICATION_ID)
			->table('shop_goods sg')
			->joinon($user, $join_shop_goods_when)
			->call('where', $call_where)
			->find('count(distinct sg.shop_goods_id) as count');
			if( empty($find_data['count']) ){
				return $data;
				}else{
					$data['row_count'] = $find_data['count'];
					if( !empty($data['page_size']) ){
						$data['page_count'] = ceil($data['row_count']/$data['page_size']);
						$data['page_now'] = ceil($limit[0]/$data['page_size']) + 1;//当前页数
					}
				}
			
			if( empty($select) ){
				$sql_join_stock_max_price = object(parent::TABLE_SHOP_GOODS_SKU)->sql_join_stock_max_price("sg");
				$sql_join_stock_min_price = object(parent::TABLE_SHOP_GOODS_SKU)->sql_join_stock_min_price("sg");
				$sql_join_stock_sum = object(parent::TABLE_SHOP_GOODS_SKU)->sql_join_stock_sum("sg");
				$select = array(
					"u.user_logo_image_id",
					"u.user_nickname",
					"u.user_compellation",
					"sg.*",
					'('.$sql_join_stock_max_price.') as shop_goods_max_price',
					'('.$sql_join_stock_min_price.') as shop_goods_min_price',
					'('.$sql_join_stock_sum.') as shop_goods_stock_sum',
					'sgw.shop_goods_id as shop_goods_when',
					'sgw.shop_goods_when_state'
				);
			}
			
			$data['data'] = db(parent::DB_APPLICATION_ID)
			->table('shop_goods sg')
			->joinon($user, $join_shop_goods_when)
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
			->select($select);
			
			return $data;
		});
		
		
	}

	/**
	 * 分页查询
	 * @author green
	 *
	 * @param  array  $config [查询配置]
	 * @return array
	 */
	public function select_paginate($config = array())
	{
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config) {
			// 查询配置
			$select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();
			$call_joinon = isset($config['joinon']) && is_array($config['joinon']) ? $config['joinon'] : array();
			$call_where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
			$call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
			$limit = array(
				(isset($config['limit'][0]) ? $config['limit'][0] : 0),
				(isset($config['limit'][1]) ? $config['limit'][1] : 0),
			);

			// 返回数据
			$data = array(
				'row_count' => 0,
				'limit_count' => $limit[0] + $limit[1],
				'page_size' => $limit[1],
				'page_count' => 0,
				'page_now' => 0,
				'data' => array()
			);

			// 查询数据条数
            $total_count = db(parent::DB_APPLICATION_ID)
                ->table('shop_goods sg')
                ->call('joinon', $call_joinon)
                ->call('where', $call_where)
                ->find('count(*) as count');

            // 计算分页信息
            if (empty($total_count['count'])) {
                return $data;
            } else {
                $data['row_count'] = $total_count['count'];
                if ($data['page_size']) {
                    $data['page_count'] = ceil($data['row_count'] / $data['page_size']);
                    $data['page_now'] = ceil($limit[0] / $data['page_size']) + 1;
                }
            }

            // 查询数据
            $data['data'] = db(parent::DB_APPLICATION_ID)
				->table('shop_goods sg')
				->call('joinon', $call_joinon)
				->call('where', $call_where)
				->call('orderby', $call_orderby)
				->call('limit', $limit)
				->select($select);

			return $data;
		});
	}

	
	public function find_join_goods_image($shop_goods_id = '')
	{
		if( empty($shop_goods_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_goods_id), function($shop_goods_id){
			$join_goods = array(
                'table' => 'shop_goods_image sgi',
                'on' => 'sg.shop_goods_id = sgi.shop_goods_id'
            );
			return db(parent::DB_APPLICATION_ID)
			->table('shop_goods sg')
			->joinon($join_goods)
			->where(array('sg.shop_goods_id=[+]', (string)$shop_goods_id))
			->find();
		});
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
			if( !isset($value['shop_goods_id']) ){
				//分类id不存在，则直接返回
				break;
			}
			$data[$key]["shop_goods_type"] = array();//初始化键值
			$data[$key]["shop_goods_image_main"] = array();
			$data[$key]["shop_goods_sku"] = array();
			$data[$key]["shop_goods_sku_min"] = array();
			$data[$key]["shop_goods_sku_max"] = array();
			$goods_ids[] = $value['shop_goods_id'];
		}	
		
		//没有可查询的数据
		if( empty($goods_ids) ){
			return $data;
		}
		
		//标识符的目的是，有些数据不一致
		$identifier = md5(cmd(array($goods_ids), "json encode").cmd(array($data), "json encode")).
		md5(cmd(array($data), "json encode").cmd(array($goods_ids), "json encode"));
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($goods_ids, $config, $identifier), function($goods_ids, $config, $identifier) use ($data){
		
		//获取分类数据
		$in_string = "\"".implode("\",\"", $goods_ids)."\"";
		
		
		//获取商品分类数据
		if(empty($config['shop_goods_type']['where']) || !is_array($config['shop_goods_type']['where']))
		$config['shop_goods_type']['where'] = array();
		if(empty($config['shop_goods_type']['orderby']) || !is_array($config['shop_goods_type']['orderby']))
		$config['shop_goods_type']['orderby'] = array();
		if(empty($config['shop_goods_type']['limit']) || !is_array($config['shop_goods_type']['limit']))
		$config['shop_goods_type']['limit'] = array();
		if(empty($config['shop_goods_type']['select']) || !is_array($config['shop_goods_type']['select']))
		$config['shop_goods_type']['select'] = array();
		$config['shop_goods_type']['where'][] = array("[and] sgt.shop_goods_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
		$goods_type_data = object(parent::TABLE_SHOP_GOODS_TYPE)->select_join($config['shop_goods_type']);
		
		
		//获取商品主图的数据
		if(empty($config['shop_goods_image']['where']) || !is_array($config['shop_goods_image']['where']))
		$config['shop_goods_image']['where'] = array();
		if(empty($config['shop_goods_image']['orderby']) || !is_array($config['shop_goods_image']['orderby']))
		$config['shop_goods_image']['orderby'] = array();
		if(empty($config['shop_goods_image']['limit']) || !is_array($config['shop_goods_image']['limit']))
		$config['shop_goods_image']['limit'] = array();
		if(empty($config['shop_goods_image']['select']) || !is_array($config['shop_goods_image']['select']))
		$config['shop_goods_image']['select'] = array();
		
		$config['shop_goods_image']['where'][] = array("[and] sgi.shop_goods_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
		$config['shop_goods_image']['where'][] = array("[and] i.image_state=1");
		$config['shop_goods_image']['where'][] = array("[and] sgi.shop_goods_image_main=1");
		if( empty($config['shop_goods_image']['orderby']) ){
			$config['shop_goods_image']['orderby'][] = array("image_sort");
			$config['shop_goods_image']['orderby'][] = array("shop_goods_image_id");
		}
		$goods_image_data = object(parent::TABLE_SHOP_GOODS_IMAGE)->select_join($config['shop_goods_image']);
		
		//获取当前产品的所有属性
		if(empty($config['shop_goods_spu']['where']) || !is_array($config['shop_goods_spu']['where']))
		$config['shop_goods_spu']['where'] = array();
		if(empty($config['shop_goods_spu']['orderby']) || !is_array($config['shop_goods_spu']['orderby']))
		$config['shop_goods_spu']['orderby'] = array();
		if(empty($config['shop_goods_spu']['limit']) || !is_array($config['shop_goods_spu']['limit']))
		$config['shop_goods_spu']['limit'] = array();
		if(empty($config['shop_goods_spu']['select']) || !is_array($config['shop_goods_spu']['select']))
		$config['shop_goods_spu']['select'] = array();
		
		$config['shop_goods_spu']['where'][] = array("[and] shop_goods_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
		if( empty($config['shop_goods_spu']['orderby']) ){
			$config['shop_goods_spu']['orderby'][] = array("shop_goods_spu_sort");
			$config['shop_goods_spu']['orderby'][] = array("shop_goods_spu_update_time");
			$config['shop_goods_spu']['orderby'][] = array("shop_goods_spu_id");
		}
		$goods_spu_data = object(parent::TABLE_SHOP_GOODS_SPU)->select($config['shop_goods_spu']);
		
		
		//获取当前产品的所有规格
		if(empty($config['shop_goods_sku']['where']) || !is_array($config['shop_goods_sku']['where']))
		$config['shop_goods_sku']['where'] = array();
		if(empty($config['shop_goods_sku']['orderby']) || !is_array($config['shop_goods_sku']['orderby']))
		$config['shop_goods_sku']['orderby'] = array();
		if(empty($config['shop_goods_sku']['limit']) || !is_array($config['shop_goods_sku']['limit']))
		$config['shop_goods_sku']['limit'] = array();
		if(empty($config['shop_goods_sku']['select']) || !is_array($config['shop_goods_sku']['select']))
		$config['shop_goods_sku']['select'] = array();
		
		$config['shop_goods_sku']['where'][] = array("[and] shop_goods_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
		//库存不能等于0
		$config['shop_goods_sku']['where'][] = array("[and] shop_goods_sku_stock>0");
		if( empty($config['shop_goods_sku']['orderby']) ){
			$config['shop_goods_sku']['orderby'][] = array("shop_goods_sku_id");
			$config['shop_goods_sku']['orderby'][] = array("shop_goods_sku_update_time");
		}
		$goods_sku_data = object(parent::TABLE_SHOP_GOODS_SKU)->select($config['shop_goods_sku']);
		
		
		
		foreach($data as $parent_key => $parent_value){
			//已经删完了则终止
			if( empty($goods_type_data) && 
			empty($goods_image_data) &&
			empty($goods_spu_data) &&
			empty($goods_sku_data) ){
				break;
			}
			
			//获得主图
			if( !empty($goods_image_data) ){
				foreach($goods_image_data as $image_key => $image_value){
					if($image_value['shop_goods_id'] == $parent_value['shop_goods_id']){
						$data[$parent_key]['shop_goods_image_main'][] = $image_value;
						unset($goods_image_data[$image_key]);
					}
				}
			}
			
			//获得分类
			if( !empty($goods_type_data) ){
				foreach($goods_type_data as $type_key => $type_value){
					if($type_value['shop_goods_id'] == $parent_value['shop_goods_id']){
						$data[$parent_key]['shop_goods_type'][] = $type_value;
						unset($goods_type_data[$type_key]);
					}
				}
			}
			
			
			//获取当前商品属性
			if( !empty($goods_spu_data) ){
				foreach($goods_spu_data as $spu_key => $spu_value){
					if($spu_value['shop_goods_id'] == $parent_value['shop_goods_id']){
						$data[$parent_key]['shop_goods_spu'][] = $spu_value;
						unset($goods_spu_data[$spu_key]);
					}
				}
			}
			
			
			//获取父、子商品属性
			$temp_shop_goods_spu_son = array();
			if( !empty($data[$parent_key]['shop_goods_spu']) ){
				$temp_shop_goods_spu = $data[$parent_key]['shop_goods_spu'];
				$data[$parent_key]['shop_goods_spu'] = array();
				//先获取父级
				foreach($temp_shop_goods_spu as $spu_key => $spu_value){
					if( !empty($spu_value["shop_goods_spu_parent_id"])){
						continue;
					}
					
					$spu_value['son'] = array();
					$data[$parent_key]['shop_goods_spu'][] = $spu_value;
					unset($temp_shop_goods_spu[$spu_key]);
				}

				//获取子级
				if( !empty($data[$parent_key]['shop_goods_spu']) && 
				!empty($temp_shop_goods_spu) ){
					foreach($data[$parent_key]['shop_goods_spu'] as $spu_parent_key => $spu_parent_value){
						if( empty($temp_shop_goods_spu) ){
							break;
						}
						
						foreach($temp_shop_goods_spu as $spu_son_key => $spu_son_value){
							if($spu_son_value['shop_goods_spu_parent_id'] == $spu_parent_value['shop_goods_spu_id']){
								$data[$parent_key]['shop_goods_spu'][$spu_parent_key]['son'][] = $spu_son_value;
								
								//将父级值放入子级 spu  再存入 $temp_shop_goods_spu_son
								$spu_son_value['parent'] = $spu_parent_value;
								unset($spu_son_value['parent']['son']);
								$temp_shop_goods_spu_son[] = $spu_son_value;
								unset($temp_shop_goods_spu[$spu_son_key]);
							}
						}
						
					}
				}

			}
			
			
			//获取当前商品规格，并且获取最小价格和最大价格
			if( !empty($goods_sku_data) ){
				foreach($goods_sku_data as $sku_key => $sku_value){
					if($sku_value['shop_goods_id'] == $parent_value['shop_goods_id']){
						
						//获取属性
						if( empty($sku_value['shop_goods_spu']) ) 
						$sku_value['shop_goods_spu'] = array();
						
						
						/*if( !empty($data[$parent_key]['shop_goods_spu']) ){
							foreach($data[$parent_key]['shop_goods_spu'] as $spu_value){
								if( strpos($sku_value["shop_goods_spu_id"], ",".$spu_value["shop_goods_spu_id"].",") !== FALSE ){
									//存在这个库存则添加进去
									$sku_value['shop_goods_spu'][] = $spu_value;
								}
							}
						}*/
						
						if( !empty($temp_shop_goods_spu_son) ){
							foreach($temp_shop_goods_spu_son as $spu_value){
								if( strpos($sku_value["shop_goods_spu_id"], ",".$spu_value["shop_goods_spu_id"].",") !== FALSE ){
									//存在这个库存则添加进去
									$sku_value['shop_goods_spu'][] = $spu_value;
								}
							}
						}
						
						
						$data[$parent_key]['shop_goods_sku'][] = $sku_value;
						
						if( empty($data[$parent_key]['shop_goods_sku_min']) ){
							$data[$parent_key]['shop_goods_sku_min'] = $sku_value;
						}else
						if( $data[$parent_key]['shop_goods_sku_min']['shop_goods_sku_price'] > $sku_value['shop_goods_sku_price']){
							$data[$parent_key]['shop_goods_sku_min'] = $sku_value;
						}
						
						if( empty($data[$parent_key]['shop_goods_sku_max']) ){
							$data[$parent_key]['shop_goods_sku_max'] = $sku_value;
						}else
						if( $data[$parent_key]['shop_goods_sku_max']['shop_goods_sku_price'] < $sku_value['shop_goods_sku_price']){
							$data[$parent_key]['shop_goods_sku_max'] = $sku_value;
						}
						
						unset($goods_sku_data[$sku_key]);
					}
				}
			}
			
			
			
			
			
			
		}


		return $data;

		});
		
		
	}
	
	
	
	
		
		
	/**
	 * 获取附加数据
	 * 
	 * 注意  主键不能取别名称,如下：
	 * shop_goods_spu_id
	 * shop_goods_sku_id
	 * 
	 * $data = array(
	 * 		array('shop_goods_spu_id'=>'SPUID1'),
	 * 		array('shop_goods_spu_id'=>'SPUID2'),
	 * 		...
	 * )
	 * 
	 * @param	array		$data			数据
	 * @param	array		$parent_config	父配置
	 * @param	array		$son_config		子配置
	 * @return	array	
	 */
	public function get_spu_data($data = array(), $parent_config = array(), $son_config = array()){
		if( empty($data) ){
			return $data;
		}
		
		//获取sku信息
		$shop_goods_spu_ids = array();
		foreach($data as $key => $value){
			$data[$key]['shop_goods_spu'] = array();
			$data[$key]['shop_goods_spu_ids'] = array();
			if( empty($value['shop_goods_spu_id']) ){
				continue;
			}
			$data[$key]['shop_goods_spu_ids'] = explode(",", trim($value["shop_goods_spu_id"], ","));
			foreach($data[$key]['shop_goods_spu_ids'] as $spu_id){
				if( !in_array($spu_id, $shop_goods_spu_ids) ){
					$shop_goods_spu_ids[] = $spu_id;
				}
			}
		}
		
		if( empty($shop_goods_spu_ids) ){
			return $data;
		}
		
		$in_string = "\"".implode("\",\"", $shop_goods_spu_ids)."\"";
		
		if( empty($son_config['where']) || !is_array($son_config['where']) ){
			$son_config['where'] = array();
		}
		$son_config['where'][] = array("[and] shop_goods_spu_id IN([-])", $in_string, true);
		$spu_data = object(parent::TABLE_SHOP_GOODS_SPU)->select_son_parent_all($parent_config, $son_config);
		
		if( empty($spu_data) ){
			return $data;
		}
		
		//匹配 spu
		foreach($data as $key => $value){
			if( empty($value['shop_goods_spu_ids']) ){
				continue;
			}
			
			//用 $spu_data 来循环  是为了兼容排序
			foreach($spu_data as $key_spu => $value_spu){
				foreach($value['shop_goods_spu_ids'] as $spu_id){
					//匹配 并且 防止重复 添加
					if( $value_spu['shop_goods_spu_id'] == $spu_id &&
					!in_array($value_spu['shop_goods_spu_id'], $data[$key]['shop_goods_spu']) ){
						$data[$key]['shop_goods_spu'][] = $value_spu;
						break;
					}
				}
			}
			
		}
		
		
		return $data;
	}
	
	
	
	/**
     * 获取多个商品的数据
     * @param   array   
     * @return  array   
     */
    public function get_data($data = array())
    {
        $goods_ids = array();
        foreach ($data as $v) {
            if (!in_array($v['shop_goods_id'], $goods_ids) && $v['shop_goods_id']) {
                $goods_ids[] = $v['shop_goods_id'];
            }
        }

        if(empty($goods_ids)){
            return $data;
        }

        // printexit($shop_ids);

        //获取分类数据
        $in_string = "\"" . implode("\",\"", $goods_ids) . "\"";
        $config = array(
            'where' => array(),
            'select' => array(
                '*',
            )
        );

        $config['where'][] = array("[and] shop_goods_id IN([-])", $in_string, true); //是不加单引号并且强制不过滤

        $shop_goods_data = object(parent::TABLE_SHOP_GOODS)->select($config);

        foreach ($data as &$v) {
            if (empty($shop_goods_data)) {
                break;
            }
            foreach ($shop_goods_data as $v1) {
                if ($v1['shop_goods_id'] == $v['shop_goods_id']) {
                    $v['goods'] = $v1;
                    break;
                }
            }
        }
        return $data;
    }
	
	
	
	/**
	 * 获取附加数据
	 * 
	 * @param	array		$data
	 * @param	array		$config
	 * @return	array	
	 */
	public function get_addition_data($data = array(), $config = array(),$type=0)
	{

		
		if( empty($data) ){
			return $data;
		}
		
		$goods_ids = array();
		
		//$data <-> 商品id所对应的商品详细信息
		
		foreach($data as $key => $value){
			if( !isset($value['shop_goods_id']) ){
				//分类id不存在，则直接返回
				break;
			}
			$data[$key]["shop_goods_type"] = array();//初始化键值
			$data[$key]["shop_goods_image_main"] = array();
			$data[$key]["shop_goods_sku"] = array();
			$data[$key]["shop_goods_sku_min"] = array();
			$data[$key]["shop_goods_sku_max"] = array();
			$goods_ids[] = $value['shop_goods_id'];
		}	
		
		
		//没有可查询的数据
		if( empty($goods_ids) ){
			return $data;
		}
		
		//标识符的目的是，有些数据不一致
		$identifier = md5(cmd(array($goods_ids), "json encode").cmd(array($data), "json encode")).
		md5(cmd(array($data), "json encode").cmd(array($goods_ids), "json encode"));
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($goods_ids, $config, $identifier, $type), function($goods_ids, $config, $identifier, $type) use ($data){
		
		//获取分类数据
		$in_string = "\"".implode("\",\"", $goods_ids)."\"";
		
		
		//获取商品分类数据
		if(empty($config['shop_goods_type']['where']) || !is_array($config['shop_goods_type']['where']))
		$config['shop_goods_type']['where'] = array();
		if(empty($config['shop_goods_type']['orderby']) || !is_array($config['shop_goods_type']['orderby']))
		$config['shop_goods_type']['orderby'] = array();
		if(empty($config['shop_goods_type']['limit']) || !is_array($config['shop_goods_type']['limit']))
		$config['shop_goods_type']['limit'] = array();
		if(empty($config['shop_goods_type']['select']) || !is_array($config['shop_goods_type']['select']))
		$config['shop_goods_type']['select'] = array();
		$config['shop_goods_type']['where'][] = array("[and] sgt.shop_goods_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
		$goods_type_data = object(parent::TABLE_SHOP_GOODS_TYPE)->select_join($config['shop_goods_type']);
		
		
		//获取商品主图的数据
		if(empty($config['shop_goods_image']['where']) || !is_array($config['shop_goods_image']['where']))
		$config['shop_goods_image']['where'] = array();
		if(empty($config['shop_goods_image']['orderby']) || !is_array($config['shop_goods_image']['orderby']))
		$config['shop_goods_image']['orderby'] = array();
		if(empty($config['shop_goods_image']['limit']) || !is_array($config['shop_goods_image']['limit']))
		$config['shop_goods_image']['limit'] = array();
		if(empty($config['shop_goods_image']['select']) || !is_array($config['shop_goods_image']['select']))
		$config['shop_goods_image']['select'] = array();
		
		$config['shop_goods_image']['where'][] = array("[and] sgi.shop_goods_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
		$config['shop_goods_image']['where'][] = array("[and] i.image_state=1");
		$config['shop_goods_image']['where'][] = array("[and] sgi.shop_goods_image_main=1");
		if( empty($config['shop_goods_image']['orderby']) ){
			$config['shop_goods_image']['orderby'][] = array("image_sort");
			$config['shop_goods_image']['orderby'][] = array("shop_goods_image_id");
		}
		$goods_image_data = object(parent::TABLE_SHOP_GOODS_IMAGE)->select_join($config['shop_goods_image']);
		
		//获取当前产品的所有属性
		if(empty($config['shop_goods_spu']['where']) || !is_array($config['shop_goods_spu']['where']))
		$config['shop_goods_spu']['where'] = array();
		if(empty($config['shop_goods_spu']['orderby']) || !is_array($config['shop_goods_spu']['orderby']))
		$config['shop_goods_spu']['orderby'] = array();
		if(empty($config['shop_goods_spu']['limit']) || !is_array($config['shop_goods_spu']['limit']))
		$config['shop_goods_spu']['limit'] = array();
		if(empty($config['shop_goods_spu']['select']) || !is_array($config['shop_goods_spu']['select']))
		$config['shop_goods_spu']['select'] = array();
		
		$config['shop_goods_spu']['where'][] = array("[and] shop_goods_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
		if( empty($config['shop_goods_spu']['orderby']) ){
			$config['shop_goods_spu']['orderby'][] = array("shop_goods_spu_sort");
			$config['shop_goods_spu']['orderby'][] = array("shop_goods_spu_update_time");
			$config['shop_goods_spu']['orderby'][] = array("shop_goods_spu_id");
		}
		$goods_spu_data = object(parent::TABLE_SHOP_GOODS_SPU)->select($config['shop_goods_spu']);
		
		
		//获取当前产品的所有规格
		if(empty($config['shop_goods_sku']['where']) || !is_array($config['shop_goods_sku']['where']))
		$config['shop_goods_sku']['where'] = array();
		if(empty($config['shop_goods_sku']['orderby']) || !is_array($config['shop_goods_sku']['orderby']))
		$config['shop_goods_sku']['orderby'] = array();
		if(empty($config['shop_goods_sku']['limit']) || !is_array($config['shop_goods_sku']['limit']))
		$config['shop_goods_sku']['limit'] = array();
		if(empty($config['shop_goods_sku']['select']) || !is_array($config['shop_goods_sku']['select']))
		$config['shop_goods_sku']['select'] = array();
		
		$config['shop_goods_sku']['where'][] = array("[and] shop_goods_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
		//库存不能等于0
		$config['shop_goods_sku']['where'][] = array("[and] shop_goods_sku_stock>0");
		//查询会员价规格和当前角色所对应的规格
		
		// if($type != 1){
        if(!empty($config['admin_id'])){
            $admin_id = (string)$config['admin_id'];
            array_push($config['shop_goods_sku']['where'],
                // array('((shop_goods_sku_admin_id = "'.''.'") OR (shop_goods_sku_admin_id = "'.$admin_id.'" ))')
                array('shop_goods_sku_admin_id=[+]',$admin_id)
            );
        }else{
            array_push($config['shop_goods_sku']['where'],
                array("shop_goods_sku_admin_id = '' ")
            );
        }
		// }
		// printexit($config);
		// exit;
		if( empty($config['shop_goods_sku']['orderby']) ){
			$config['shop_goods_sku']['orderby'][] = array("shop_goods_sku_id");
			$config['shop_goods_sku']['orderby'][] = array("shop_goods_sku_update_time");
		}

		$goods_sku_data = object(parent::TABLE_SHOP_GOODS_SKU)->select($config['shop_goods_sku']);
		
		
		foreach($data as $parent_key => $parent_value){
			//已经删完了则终止
			if( empty($goods_type_data) && 
			empty($goods_image_data) &&
			empty($goods_spu_data) &&
			empty($goods_sku_data) ){
				break;
			}
			
			//获得主图
			if( !empty($goods_image_data) ){
				foreach($goods_image_data as $image_key => $image_value){
					if($image_value['shop_goods_id'] == $parent_value['shop_goods_id']){
						$data[$parent_key]['shop_goods_image_main'][] = $image_value;
						unset($goods_image_data[$image_key]);
					}
				}
			}
			
			//获得分类
			if( !empty($goods_type_data) ){
				foreach($goods_type_data as $type_key => $type_value){
					if($type_value['shop_goods_id'] == $parent_value['shop_goods_id']){
						$data[$parent_key]['shop_goods_type'][] = $type_value;
						unset($goods_type_data[$type_key]);
					}
				}
			}
			
			
			//获取当前商品属性
			if( !empty($goods_spu_data) ){
				foreach($goods_spu_data as $spu_key => $spu_value){
					if($spu_value['shop_goods_id'] == $parent_value['shop_goods_id']){
						$data[$parent_key]['shop_goods_spu'][] = $spu_value;
						unset($goods_spu_data[$spu_key]);
					}
				}
			}
			
			
			//获取父、子商品属性
			$temp_shop_goods_spu_son = array();
			if( !empty($data[$parent_key]['shop_goods_spu']) ){
				$temp_shop_goods_spu = $data[$parent_key]['shop_goods_spu'];
				$data[$parent_key]['shop_goods_spu'] = array();
				//先获取父级
				foreach($temp_shop_goods_spu as $spu_key => $spu_value){
					if( !empty($spu_value["shop_goods_spu_parent_id"])){
						continue;
					}
					
					$spu_value['son'] = array();
					$data[$parent_key]['shop_goods_spu'][] = $spu_value;
					unset($temp_shop_goods_spu[$spu_key]);
				}

				//获取子级
				if( !empty($data[$parent_key]['shop_goods_spu']) && 
				!empty($temp_shop_goods_spu) ){
					foreach($data[$parent_key]['shop_goods_spu'] as $spu_parent_key => $spu_parent_value){
						if( empty($temp_shop_goods_spu) ){
							break;
						}
						
						foreach($temp_shop_goods_spu as $spu_son_key => $spu_son_value){
							if($spu_son_value['shop_goods_spu_parent_id'] == $spu_parent_value['shop_goods_spu_id']){
								$data[$parent_key]['shop_goods_spu'][$spu_parent_key]['son'][] = $spu_son_value;
								
								//将父级值放入子级 spu  再存入 $temp_shop_goods_spu_son
								$spu_son_value['parent'] = $spu_parent_value;
								unset($spu_son_value['parent']['son']);
								$temp_shop_goods_spu_son[] = $spu_son_value;
								unset($temp_shop_goods_spu[$spu_son_key]);
							}
						}
						
					}
				}

			}
			
			
			//获取当前商品规格，并且获取最小价格和最大价格
			if( !empty($goods_sku_data) ){
				foreach($goods_sku_data as $sku_key => $sku_value){
					if($sku_value['shop_goods_id'] == $parent_value['shop_goods_id']){
						
						//获取属性
						if( empty($sku_value['shop_goods_spu']) ) 
						$sku_value['shop_goods_spu'] = array();
						
						if( !empty($temp_shop_goods_spu_son) ){
							foreach($temp_shop_goods_spu_son as $spu_value){
								if( strpos($sku_value["shop_goods_spu_id"], ",".$spu_value["shop_goods_spu_id"].",") !== FALSE ){
									//存在这个库存则添加进去
									$sku_value['shop_goods_spu'][] = $spu_value;
								}
							}
						}

						$data[$parent_key]['shop_goods_sku'][] = $sku_value;
						
						if( empty($data[$parent_key]['shop_goods_sku_min']) ){
							$data[$parent_key]['shop_goods_sku_min'] = $sku_value;
						}else
						if( $data[$parent_key]['shop_goods_sku_min']['shop_goods_sku_price'] > $sku_value['shop_goods_sku_price']){
							$data[$parent_key]['shop_goods_sku_min'] = $sku_value;
						}
						
						if( empty($data[$parent_key]['shop_goods_sku_max']) ){
							$data[$parent_key]['shop_goods_sku_max'] = $sku_value;
						}else
						if( $data[$parent_key]['shop_goods_sku_max']['shop_goods_sku_price'] < $sku_value['shop_goods_sku_price']){
							$data[$parent_key]['shop_goods_sku_max'] = $sku_value;
						}
						
						unset($goods_sku_data[$sku_key]);
					}
				}
			}	
		}		

		return $data;

		});
		
		
	}
	

	/**
	 * 获取附加数据
	 * 
	 * @param	array		$data
	 * @param	array		$config
	 * @return	array	
	 */
	public function get_additional_admin_data($data = array(), $config = array(), $admin_id=""){
		if( empty($data) ){
			return $data;
		}
		
		$goods_ids = array();
		foreach($data as $key => $value){
			if( !isset($value['shop_goods_id']) ){
				//分类id不存在，则直接返回
				break;
			}
			$data[$key]["shop_goods_type"] = array();//初始化键值
			$data[$key]["shop_goods_image_main"] = array();
			$data[$key]["shop_goods_sku"] = array();
			$data[$key]["shop_goods_sku_min"] = array();
			$data[$key]["shop_goods_sku_max"] = array();
			$goods_ids[] = $value['shop_goods_id'];
		}	
		
		//没有可查询的数据
		if( empty($goods_ids) ){
			return $data;
		}
		
		//标识符的目的是，有些数据不一致
		$identifier = md5(cmd(array($goods_ids), "json encode").cmd(array($data), "json encode")).
		md5(cmd(array($data), "json encode").cmd(array($goods_ids), "json encode"));
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($goods_ids, $config, $identifier,$admin_id), function($goods_ids, $config, $identifier,$admin_id) use ($data){
		
		//获取分类数据
		$in_string = "\"".implode("\",\"", $goods_ids)."\"";
		
		
		//获取商品分类数据
		if(empty($config['shop_goods_type']['where']) || !is_array($config['shop_goods_type']['where']))
		$config['shop_goods_type']['where'] = array();
		if(empty($config['shop_goods_type']['orderby']) || !is_array($config['shop_goods_type']['orderby']))
		$config['shop_goods_type']['orderby'] = array();
		if(empty($config['shop_goods_type']['limit']) || !is_array($config['shop_goods_type']['limit']))
		$config['shop_goods_type']['limit'] = array();
		if(empty($config['shop_goods_type']['select']) || !is_array($config['shop_goods_type']['select']))
		$config['shop_goods_type']['select'] = array();
		$config['shop_goods_type']['where'][] = array("[and] sgt.shop_goods_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
		$goods_type_data = object(parent::TABLE_SHOP_GOODS_TYPE)->select_join($config['shop_goods_type']);
		
		
		//获取商品主图的数据
		if(empty($config['shop_goods_image']['where']) || !is_array($config['shop_goods_image']['where']))
		$config['shop_goods_image']['where'] = array();
		if(empty($config['shop_goods_image']['orderby']) || !is_array($config['shop_goods_image']['orderby']))
		$config['shop_goods_image']['orderby'] = array();
		if(empty($config['shop_goods_image']['limit']) || !is_array($config['shop_goods_image']['limit']))
		$config['shop_goods_image']['limit'] = array();
		if(empty($config['shop_goods_image']['select']) || !is_array($config['shop_goods_image']['select']))
		$config['shop_goods_image']['select'] = array();
		
		$config['shop_goods_image']['where'][] = array("[and] sgi.shop_goods_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
		$config['shop_goods_image']['where'][] = array("[and] i.image_state=1");
		$config['shop_goods_image']['where'][] = array("[and] sgi.shop_goods_image_main=1");
		if( empty($config['shop_goods_image']['orderby']) ){
			$config['shop_goods_image']['orderby'][] = array("image_sort");
			$config['shop_goods_image']['orderby'][] = array("shop_goods_image_id");
		}
		$goods_image_data = object(parent::TABLE_SHOP_GOODS_IMAGE)->select_join($config['shop_goods_image']);
		
		//获取当前产品的所有属性
		if(empty($config['shop_goods_spu']['where']) || !is_array($config['shop_goods_spu']['where']))
		$config['shop_goods_spu']['where'] = array();
		if(empty($config['shop_goods_spu']['orderby']) || !is_array($config['shop_goods_spu']['orderby']))
		$config['shop_goods_spu']['orderby'] = array();
		if(empty($config['shop_goods_spu']['limit']) || !is_array($config['shop_goods_spu']['limit']))
		$config['shop_goods_spu']['limit'] = array();
		if(empty($config['shop_goods_spu']['select']) || !is_array($config['shop_goods_spu']['select']))
		$config['shop_goods_spu']['select'] = array();
		
		$config['shop_goods_spu']['where'][] = array("[and] shop_goods_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
		if( empty($config['shop_goods_spu']['orderby']) ){
			$config['shop_goods_spu']['orderby'][] = array("shop_goods_spu_sort");
			$config['shop_goods_spu']['orderby'][] = array("shop_goods_spu_update_time");
			$config['shop_goods_spu']['orderby'][] = array("shop_goods_spu_id");
		}
		$goods_spu_data = object(parent::TABLE_SHOP_GOODS_SPU)->select($config['shop_goods_spu']);
		
		
		//获取当前产品的所有规格
		if(empty($config['shop_goods_sku']['where']) || !is_array($config['shop_goods_sku']['where']))
		$config['shop_goods_sku']['where'] = array();
		if(empty($config['shop_goods_sku']['orderby']) || !is_array($config['shop_goods_sku']['orderby']))
		$config['shop_goods_sku']['orderby'] = array();
		if(empty($config['shop_goods_sku']['limit']) || !is_array($config['shop_goods_sku']['limit']))
		$config['shop_goods_sku']['limit'] = array();
		if(empty($config['shop_goods_sku']['select']) || !is_array($config['shop_goods_sku']['select']))
		$config['shop_goods_sku']['select'] = array();
		
		$config['shop_goods_sku']['where'][] = array("[and] shop_goods_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
		//库存不能等于0
		// $config['shop_goods_sku']['where'][] = array("[and] shop_goods_sku_stock>0");
		$config['shop_goods_sku']['where'][] = array("[and] shop_goods_sku_admin_id=[+]",$admin_id);
		if( empty($config['shop_goods_sku']['orderby']) ){
			$config['shop_goods_sku']['orderby'][] = array("shop_goods_sku_id");
			$config['shop_goods_sku']['orderby'][] = array("shop_goods_sku_update_time");
		}
		$goods_sku_data = object(parent::TABLE_SHOP_GOODS_SKU)->select($config['shop_goods_sku']);
		
		
		
		foreach($data as $parent_key => $parent_value){
			//已经删完了则终止
			if( empty($goods_type_data) && 
			empty($goods_image_data) &&
			empty($goods_spu_data) &&
			empty($goods_sku_data) ){
				break;
			}
			
			//获得主图
			if( !empty($goods_image_data) ){
				foreach($goods_image_data as $image_key => $image_value){
					if($image_value['shop_goods_id'] == $parent_value['shop_goods_id']){
						$data[$parent_key]['shop_goods_image_main'][] = $image_value;
						unset($goods_image_data[$image_key]);
					}
				}
			}
			
			//获得分类
			if( !empty($goods_type_data) ){
				foreach($goods_type_data as $type_key => $type_value){
					if($type_value['shop_goods_id'] == $parent_value['shop_goods_id']){
						$data[$parent_key]['shop_goods_type'][] = $type_value;
						unset($goods_type_data[$type_key]);
					}
				}
			}
			
			
			//获取当前商品属性
			if( !empty($goods_spu_data) ){
				foreach($goods_spu_data as $spu_key => $spu_value){
					if($spu_value['shop_goods_id'] == $parent_value['shop_goods_id']){
						$data[$parent_key]['shop_goods_spu'][] = $spu_value;
						unset($goods_spu_data[$spu_key]);
					}
				}
			}
			
			
			//获取父、子商品属性
			$temp_shop_goods_spu_son = array();
			if( !empty($data[$parent_key]['shop_goods_spu']) ){
				$temp_shop_goods_spu = $data[$parent_key]['shop_goods_spu'];
				$data[$parent_key]['shop_goods_spu'] = array();
				//先获取父级
				foreach($temp_shop_goods_spu as $spu_key => $spu_value){
					if( !empty($spu_value["shop_goods_spu_parent_id"])){
						continue;
					}
					
					$spu_value['son'] = array();
					$data[$parent_key]['shop_goods_spu'][] = $spu_value;
					unset($temp_shop_goods_spu[$spu_key]);
				}

				//获取子级
				if( !empty($data[$parent_key]['shop_goods_spu']) && 
				!empty($temp_shop_goods_spu) ){
					foreach($data[$parent_key]['shop_goods_spu'] as $spu_parent_key => $spu_parent_value){
						if( empty($temp_shop_goods_spu) ){
							break;
						}
						
						foreach($temp_shop_goods_spu as $spu_son_key => $spu_son_value){
							if($spu_son_value['shop_goods_spu_parent_id'] == $spu_parent_value['shop_goods_spu_id']){
								$data[$parent_key]['shop_goods_spu'][$spu_parent_key]['son'][] = $spu_son_value;
								
								//将父级值放入子级 spu  再存入 $temp_shop_goods_spu_son
								$spu_son_value['parent'] = $spu_parent_value;
								unset($spu_son_value['parent']['son']);
								$temp_shop_goods_spu_son[] = $spu_son_value;
								unset($temp_shop_goods_spu[$spu_son_key]);
							}
						}
						
					}
				}

			}
			
			
			//获取当前商品规格，并且获取最小价格和最大价格
			if( !empty($goods_sku_data) ){
				foreach($goods_sku_data as $sku_key => $sku_value){
					if($sku_value['shop_goods_id'] == $parent_value['shop_goods_id']){
						
						//获取属性
						if( empty($sku_value['shop_goods_spu']) ) 
						$sku_value['shop_goods_spu'] = array();
						
						
						/*if( !empty($data[$parent_key]['shop_goods_spu']) ){
							foreach($data[$parent_key]['shop_goods_spu'] as $spu_value){
								if( strpos($sku_value["shop_goods_spu_id"], ",".$spu_value["shop_goods_spu_id"].",") !== FALSE ){
									//存在这个库存则添加进去
									$sku_value['shop_goods_spu'][] = $spu_value;
								}
							}
						}*/
						
						if( !empty($temp_shop_goods_spu_son) ){
							foreach($temp_shop_goods_spu_son as $spu_value){
								if( strpos($sku_value["shop_goods_spu_id"], ",".$spu_value["shop_goods_spu_id"].",") !== FALSE ){
									//存在这个库存则添加进去
									$sku_value['shop_goods_spu'][] = $spu_value;
								}
							}
						}
						
						
						$data[$parent_key]['shop_goods_sku'][] = $sku_value;
						
						if( empty($data[$parent_key]['shop_goods_sku_min']) ){
							$data[$parent_key]['shop_goods_sku_min'] = $sku_value;
						}else
						if( $data[$parent_key]['shop_goods_sku_min']['shop_goods_sku_price'] > $sku_value['shop_goods_sku_price']){
							$data[$parent_key]['shop_goods_sku_min'] = $sku_value;
						}
						
						if( empty($data[$parent_key]['shop_goods_sku_max']) ){
							$data[$parent_key]['shop_goods_sku_max'] = $sku_value;
						}else
						if( $data[$parent_key]['shop_goods_sku_max']['shop_goods_sku_price'] < $sku_value['shop_goods_sku_price']){
							$data[$parent_key]['shop_goods_sku_max'] = $sku_value;
						}
						
						unset($goods_sku_data[$sku_key]);
					}
				}
			}
			
			
			
			
			
			
		}


		return $data;

		});
		
		
	}
	
	
	
}
?>