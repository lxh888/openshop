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



namespace eapie\source\table\softstore;
use eapie\main;
class ss_product_attr extends main {
	
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, "ss_product");
	


	/**
	 * 数据检测
	 * 
	 * @var	array
	 */
	public $check = array(
		'ss_product_attr_id' => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少产品属性ID参数"),
					'echo'=>array("产品属性ID数据类型不合法"),
					'!null'=>array("产品属性ID不能为空"),
					),
			//检查编号是否存在		
			'exists_id'=>array(
					'method'=>array(array(parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE, 'find_exists_id'), "产品属性ID有误，数据不存在",) 
			),
			
		),
		
		"ss_product_attr_parent_id" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("产品属性的父ID数据类型不合法"),
					),
					
		),
		
		"ss_product_attr_name" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少产品属性名称参数"),
					'echo'=>array("产品属性名称数据类型不合法"),
					'!null'=>array("产品属性名称不能为空"),
					),
			//字符长度检测
			'length' => array(
					'<length'=>array(200, "产品属性名称的字符长度太多")
					),		
		),
		
		"ss_product_attr_info" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("产品属性描述的数据类型不合法"),
					),
		),
		
		
		"ss_product_attr_sort" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("产品属性排序的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/iu', "产品属性排序必须是整数"),
					),
		),
		
		
			
		"ss_product_attr_required" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("产品属性是否必选的数据类型不合法"),
					'match'=>array('/^[01]{1}$/iu', "产品属性是否必选的值必须是0或1"),
					),
		),
		
		"ss_product_attr_stock" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("产品属性的库存数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/iu', "产品属性的库存必须是整数"),
					),
		),
		
		
		"ss_product_attr_money" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("产品属性的单价数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/iu', "产品属性的单价必须是整数"),
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
	 * 根据ID，判断是否存在子级
	 * 
	 * @param	string 		$ss_product_attr_id
	 */
	public function find_exists_son_id($ss_product_attr_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($ss_product_attr_id), function($ss_product_attr_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('ss_product_attr')
			->where(array('ss_product_attr_parent_id=[+]', $ss_product_attr_id))
			->find('ss_product_attr_id');
		});
	}
	
	
	
	
	
				
	/**
	 * 获取一个数据
	 * 
	 * @param	string	$ss_product_attr_id
	 * @return	array
	 */
	public function find($ss_product_attr_id = ''){
		if( empty($ss_product_attr_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($ss_product_attr_id), function($ss_product_attr_id){
			return db(parent::DB_APPLICATION_ID)
			->table('ss_product_attr')
			->where(array('ss_product_attr_id=[+]', (string)$ss_product_attr_id))
			->find();
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
	public function select($config = array()){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
			$where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
			$orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
			$limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
			$select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
				
			return db(parent::DB_APPLICATION_ID)
			->table('ss_product_attr')
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
		});
	}		
	
	
	
	/**
	 * 获取多条子级数据。并且获得父级的信息。
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
	public function select_join_son($config = array()){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
			$where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
			$orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
			$limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
			$select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
				
			//父级数据	
			$parent_product_attr = array(
				'table' => 'ss_product_attr pspa',
				'type' => 'left',
				'on' => 'pspa.ss_product_attr_id = spa.ss_product_attr_parent_id'
			);	
			
			if( empty($select) ){
				$select = array(
					"spa.*",
					"pspa.ss_product_attr_name as ss_product_attr_parent_name",
					"pspa.ss_product_attr_info as ss_product_attr_parent_info",
					"pspa.ss_product_attr_sort as ss_product_attr_parent_sort",
					"pspa.ss_product_attr_required as ss_product_attr_parent_required",
					"pspa.ss_product_attr_insert_time as ss_product_attr_parent_insert_time",
					"pspa.ss_product_attr_update_time as ss_product_attr_parent_update_time",
				);
			}	
				
			return db(parent::DB_APPLICATION_ID)
			->table('ss_product_attr spa')
			->joinon($parent_product_attr)
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
				
			//产品数据
			$ss_product = array(
				'table' => 'ss_product sp',
				'type' => 'left',
				'on' => 'sp.ss_product_id = spa.ss_product_id'
			);
			
			//用户数据
			$user = array(
				'table' => 'user u',
				'type' => 'left',
				'on' => 'u.user_id = spa.user_id'
			);	
				
			if( empty($select) ){
				$select = array(
					"u.user_nickname",
					"sp.ss_product_name",
					"spa.*"
				);
			}	
				
			return db(parent::DB_APPLICATION_ID)
			->table('ss_product_attr spa')
			->joinon($ss_product,$user)
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
		});
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
		->table('ss_product_attr')
		->call('data', $call_data)
		->insert($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
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
		->table('ss_product_attr')
		->call('where', $where)
		->call('data', $call_data)
		->update($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}	
		
	
	
				
	/**
	 * 根据唯一标识，删除数据
	 * 
	 * @param	array	$ss_product_attr_id
	 * @return	array
	 */
	public function remove($ss_product_attr_id = ''){
		if( empty($ss_product_attr_id) ){
			return false;
		}
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('ss_product_attr')
		->where(array('ss_product_attr_id=[+]', (string)$ss_product_attr_id))
		->delete();
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}		
		
	
	
	
	
	
	
	
	
	
	
	
	
	
		
	/**
	 * 获取多条数据，包括子级数据
	 *  $config = array(
	 * 	'where' => array(), //条件
	 * 	'orderby' => array(), //排序
	 * 	'limit'	=> array(0, page_size), //取出条数，默认不限制
	 *  'select' => array(),//查询的字段，可以是数组和字符串
	 * );
	 * 
	 * @param	array	$parent_config	父级配置
	 * @param	array	$son_config	子级配置
	 * @return	array
	 */
	public function select_parent_son_all($parent_config = array(), $son_config = array()){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($parent_config, $son_config), function($parent_config, $son_config){
			$parent_where = isset($parent_config['where']) && is_array($parent_config['where'])? $parent_config['where'] : array();
			$parent_orderby = isset($parent_config['orderby']) && is_array($parent_config['orderby'])? $parent_config['orderby'] : array();
			$parent_limit = isset($parent_config['limit']) && is_array($parent_config['limit'])? $parent_config['limit'] : array();
			$parent_select = isset($parent_config['select']) && is_array($parent_config['select'])? $parent_config['select'] : array();
			
			$son_where = isset($son_config['where']) && is_array($son_config['where'])? $son_config['where'] : array();
			$son_orderby = isset($son_config['orderby']) && is_array($son_config['orderby'])? $son_config['orderby'] : array();
			$son_limit = isset($son_config['limit']) && is_array($son_config['limit'])? $son_config['limit'] : array();
			$son_select = isset($son_config['select']) && is_array($son_config['select'])? $son_config['select'] : array();
			
			$data = db(parent::DB_APPLICATION_ID)
			->table('ss_product_attr')
			->call('where', $parent_where)
			->call('orderby', $parent_orderby)
			->call('limit', $parent_limit)
			->select($parent_select);
			
			//没有父类
			if( empty($data) ){
				return $data;
			}
			
			//获取子分类
			if( !isset($son_where) || !is_array($son_where)){
				$son_where = array();
			}
			$type_parent_ids = array();
			foreach($data as $key => $value){
				if( !isset($value['ss_product_attr_id']) || !empty($value['ss_product_attr_parent_id']) ){
					//分类id不存在，或者不是顶级，则直接返回
					break;
				}
				$data[$key]["son"] = array();//初始化键值
				$type_parent_ids[] = $value['ss_product_attr_id'];
			}
			
			//没有父类
			if( empty($type_parent_ids) ){
				return $data;
			}
			
			$son_in_string = "\"".implode("\",\"", $type_parent_ids)."\"";
			$son_where[] = array("[and] ss_product_attr_parent_id IN([-])", $son_in_string, true);//是不加单引号并且强制不过滤
			$son_data = db(parent::DB_APPLICATION_ID)
			->table('ss_product_attr')
			->call('where', $son_where)
			->call('orderby', $son_orderby)
			->call('limit', $son_limit)
			->select($son_select);
			
			//没有子数据
			if( empty($son_data) ){
				return $data;
			}
			
			
			foreach($data as $parent_key => $parent_value){
				if( empty($son_data) ){
					break;
				}
				
				//循环子级
				foreach($son_data as $son_key => $son_value){
					if($son_value['ss_product_attr_parent_id'] == $parent_value['ss_product_attr_id']){
						$data[$parent_key]['son'][] = $son_value;
						unset($son_data[$son_key]);
					}
				}
				
			}
			
			
			return $data;
		});
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	






	
}
?>