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



namespace eapie\engine\table;
use eapie\main;
class module extends main {
	
	
	/*模块*/
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, "management", "application", "api", "authority");
	
	
	
	/**
	 * 数据检测
	 * 
	 * @var	array
	 */
	public $check = array(
		'module_id' => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少模块ID参数"),
					'echo'=>array("模块ID数据类型不合法"),
					'!null'=>array("模块ID不能为空"),
					),
			//检查编号是否存在		
			'exists_id'=>array(
					'method'=>array(array(parent::TABLE_MODULE, 'find_exists_id'), "模块ID有误，数据不存在",) 
			),
		),
		
		"module_parent_id" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("模块的父ID数据类型不合法"),
					),
					
		),
		
		"module_name" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少模块名称参数"),
					'echo'=>array("模块名称数据类型不合法"),
					'!null'=>array("模块名称不能为空"),
					),
			//字符长度检测
			'length' => array(
					'<length'=>array(200, "模块名称的字符长度太多")
					),		
		),
		"module_info" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("模块简介的数据类型不合法"),
					),
		),
		"module_sort" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("模块排序的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/', "模块排序必须是整数"),
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
	 * @param	string 		$module_id
	 */
	public function find_exists_id($module_id){
		return object(parent::CACHE)->data_system(__CLASS__, __METHOD__, array($module_id), function($module_id){
			return (bool)db(parent::DB_SYSTEM_ID)
			->table('module')
			->where(array('module_id=[+]', $module_id))
			->find('module_id');
		});
	}
	
	
		
		
	/**
	 * 获取一条数据
	 * 
	 * @param	array	$module_id
	 * @return	array
	 */
	public function find($module_id = ''){
		if( empty($module_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data_system(__CLASS__, __METHOD__, array($module_id), function($module_id) {
			return db(parent::DB_SYSTEM_ID)
			->table('module')
			->where(array('module_id=[+]', (string)$module_id))
			->find();
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
		$bool = (bool)db(parent::DB_SYSTEM_ID)
		->table('module')
		->call('where', $where)
		->call('data', $call_data)
		->update($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear_system(self::CACHE_KEY);
		}
		
		return $bool;
	}	
		
		
		
						
	/**
	 * 根据唯一标识，删除数据
	 * 
	 * @param	array	$module_id
	 * @return	array
	 */
	public function remove($module_id = ''){
		if( empty($module_id) ){
			return false;
		}
		$bool = (bool)db(parent::DB_SYSTEM_ID)
		->table('module')
		->where(array('module_id=[+]', (string)$module_id))
		->delete();
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear_system(self::CACHE_KEY);
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
		
		$bool = (bool)db(parent::DB_SYSTEM_ID)
		->table('module')
		->call('data', $call_data)
		->insert($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear_system(self::CACHE_KEY);
		}
		
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
		return object(parent::CACHE)->data_system(__CLASS__, __METHOD__, array($config), function($config){
			$where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
			$orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
			$limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
			$select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
				
			return db(parent::DB_SYSTEM_ID)
			->table('module')
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
		return object(parent::CACHE)->data_system(__CLASS__, __METHOD__, array($config), function($config){
			$where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
			$orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
			$limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
			$select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
			
			//权限表
            $authority = array(
                'table' => 'authority as a',
                'type' => 'INNER',
                'on' => 'a.module_id = m.module_id'
            );
				
			return db(parent::DB_SYSTEM_ID)
			->table('module as m')
			->joinon($authority)
			->groupby('m.module_id')
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
		return object(parent::CACHE)->data_system(__CLASS__, __METHOD__, array($config), function($config){
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
		
			
			//先获取总条数
			$find_data = db(parent::DB_SYSTEM_ID)
			->table('module m')
			->call('where', $call_where)
			->find('count(distinct m.module_id) as count');
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
				$select = array(
					'm.*',
				);
			}
			
			$data['data'] =  db(parent::DB_SYSTEM_ID)
			->table('module m')
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
			->select($select);				
				
			return $data;
		});
	}
	
	
	

	
	
	
	
	
}
?>