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
class ss_product_type extends main {
	
	
		
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, "ss_product", "ss_type");
	
		
	
	
	/**
	 * 数据检测
	 * 
	 * @var	array
	 */
	public $check = array(
		'ss_product_type_id' => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少产品分类ID参数"),
					'echo'=>array("产品分类ID数据类型不合法"),
					'!null'=>array("产品分类ID不能为空"),
					),
			//检查编号是否存在		
			'exists_id'=>array(
					'method'=>array(array(parent::TABLE_SOFTSTORE_PRODUCT_TYPE, 'find_exists_id'), "产品分类ID有误，数据不存在",) 
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
	 * @param	string 		$ss_product_type_id
	 */
	public function find_exists_id($ss_product_type_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($ss_product_type_id), function($ss_product_type_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('ss_product_type')
			->where(array('ss_product_type_id=[+]', (string)$ss_product_type_id))
			->find('ss_product_type_id');
		});
		
	}
		
	
	
			
	/**
	 * 获取一个数据
	 * 
	 * @param	string	$ss_product_type_id
	 * @return	array
	 */
	public function find($ss_product_type_id = ''){
		if( empty($ss_product_type_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($ss_product_type_id), function($ss_product_type_id){
			return db(parent::DB_APPLICATION_ID)
			->table('ss_product_type')
			->where(array('ss_product_type_id=[+]', (string)$ss_product_type_id))
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
			->table('ss_product_type')
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
			
			//类型
			$ss_type = array(
				'table' => 'ss_type st',
				'type' => 'INNER',
				'on' => 'st.ss_type_id = spt.ss_type_id'
			);
			
			if( empty($select) ){
				$select = array(
					"spt.ss_product_type_id",
					"spt.ss_product_type_time",
					"spt.ss_product_id",
					"spt.ss_type_id",
					"st.ss_type_parent_id",
					"st.ss_type_name",
					"st.ss_type_info",
					"st.ss_type_sort",
					"st.ss_type_state",
					"st.ss_type_update_time",
					"st.ss_type_insert_time",
				);
			}
			
			return db(parent::DB_APPLICATION_ID)
			->table('ss_product_type spt')
			->joinon($ss_type)
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
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
		->table('ss_product_type')
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
	 * 删除数据
	 * 
	 * @param	array	$where
	 * @return	array
	 */
	public function delete($where = array()){
		if( empty($where) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('ss_product_type')
		->call('where', $where)
		->delete();
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}
		
		
	
	
		
					
	/**
	 * 根据唯一标识，删除数据
	 * 
	 * @param	array	$ss_product_type_id
	 * @return	array
	 */
	public function remove($ss_product_type_id = ''){
		if( empty($ss_product_type_id) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('ss_product_type')
		->where(array('ss_product_type_id=[+]', (string)$ss_product_type_id))
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
		->table('ss_product_type')
		->call('data', $call_data)
		->insert($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}	
	
	
	
	
	/**
	 * 根据 分类ID 获取产品ID
	 * 
	 * @param	array		$where
	 * @return	string
	 */
	public function sql_search_type($where = array()){
		
		//类型
		$ss_type = array(
			'table' => 'ss_type st',
			'type' => 'INNER',
			'on' => 'st.ss_type_id = spt.ss_type_id'
		);
		
		return db(parent::DB_APPLICATION_ID)
		->table('ss_product_type spt')
		->joinon($ss_type)
		->call('where', $where)
		->select(array('distinct spt.ss_product_id'), function($q){
			return $q['query']['select'];
		});
	}
	
	
	
	
	
	
	
		
	
	
	
	
}
?>