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



namespace eapie\source\table\admin;
use eapie\main;
class admin_authority extends main {
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__);
	
	
	
	/**
	 * 数据检测
	 * 
	 * @var	array
	 */
	public $check = array(
		'admin_authority_id' => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少权限ID参数"),
					'echo'=>array("权限ID数据类型不合法"),
					'!null'=>array("权限ID不能为空"),
					),
			//检查编号是否存在		
			'exists_id'=>array(
					'method'=>array(array(parent::TABLE_ADMIN_AUTHORITY, 'find_exists_id'), "权限ID有误，数据不存在",) 
			),
			//检查编号是否存在，则报错		
			'register_id'=>array(
					'!method'=>array(array(parent::TABLE_ADMIN_AUTHORITY, 'find_exists_id'), "权限ID已经存在",) 
			),
			
		),
		
		"admin_authority_parent_id" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("权限的父ID数据类型不合法"),
					),
					
		),
		
		"admin_authority_name" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少权限名称参数"),
					'echo'=>array("权限名称数据类型不合法"),
					'!null'=>array("权限名称不能为空"),
					),
			//字符长度检测
			'length' => array(
					'<length'=>array(200, "权限名称的字符长度太多")
					),		
		),
		
		
		"admin_authority_info" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("权限简介的数据类型不合法"),
					),
		),
		
		
		"admin_authority_sort" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("权限排序的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/i', "权限排序必须是整数"),
					),
		),
		
	);
	
	
	
	
	
				
	/**
	 * 根据ID，判断是否存在
	 * 
	 * @param	string 		$admin_authority_id
	 */
	public function find_exists_id($admin_authority_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($admin_authority_id), function($admin_authority_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('admin_authority')
			->where(array('admin_authority_id=[+]', $admin_authority_id))
			->find('admin_authority_id');
		});
	}
	
	
		
		
	/**
	 * 根据ID，判断是否存在子级
	 * 
	 * @param	string 		$admin_authority_id
	 */
	public function find_exists_son_id($admin_authority_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($admin_authority_id), function($admin_authority_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('admin_authority')
			->where(array('admin_authority_parent_id=[+]', $admin_authority_id))
			->find('admin_authority_id');
		});
	}
	
	
	
	
		
	/**
	 * 获取一条数据
	 * 
	 * @param	array	$admin_authority_id
	 * @return	array
	 */
	public function find($admin_authority_id = ''){
		if( empty($admin_authority_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($admin_authority_id), function($admin_authority_id) {
			return db(parent::DB_APPLICATION_ID)
			->table('admin_authority')
			->where(array('admin_authority_id=[+]', (string)$admin_authority_id))
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
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('admin_authority')
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
	 * @param	array	$admin_authority_id
	 * @return	array
	 */
	public function remove($admin_authority_id = ''){
		if( empty($admin_authority_id) ){
			return false;
		}
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('admin_authority')
		->where(array('admin_authority_id=[+]', (string)$admin_authority_id))
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
		->table('admin_authority')
		->call('data', $call_data)
		->insert($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
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
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
			$where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
			$orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
			$limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
			$select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
				
			return db(parent::DB_APPLICATION_ID)
			->table('admin_authority')
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
		});
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
			->table('admin_authority')
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
			$admin_authority_parent_ids = array();
			foreach($data as $key => $value){
				if( !isset($value['admin_authority_id']) || !empty($value['admin_authority_parent_id']) ){
					//分类id不存在，或者不是顶级，则直接返回
					break;
				}
				$data[$key]["son"] = array();//初始化键值
				$admin_authority_parent_ids[] = $value['admin_authority_id'];
			}
			
			//没有父类
			if( empty($admin_authority_parent_ids) ){
				return $data;
			}
			
			$son_in_string = "\"".implode("\",\"", $admin_authority_parent_ids)."\"";
			$son_where[] = array("[and] admin_authority_parent_id IN([-])", $son_in_string, true);//是不加单引号并且强制不过滤
			$son_data = db(parent::DB_APPLICATION_ID)
			->table('admin_authority')
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
					if($son_value['admin_authority_parent_id'] == $parent_value['admin_authority_id']){
						$data[$parent_key]['son'][] = $son_value;
						unset($son_data[$son_key]);
					}
				}
				
			}
			
			
			return $data;
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
		
			//父级数据
			$admin_authority_parent = array(
				'table' => 'admin_authority aa_parent',
				'type' => 'left',
				'on' => 'aa_parent.admin_authority_id = aa.admin_authority_parent_id'
			);
			
			//先获取总条数
			$find_data = db(parent::DB_APPLICATION_ID)
			->table('admin_authority aa')
			->joinon($admin_authority_parent)
			->call('where', $call_where)
			->find('count(distinct aa.admin_authority_id) as count');
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
					'aa.*',
					'aa_parent.admin_authority_name as admin_authority_parent_name',
					'('.$this->sql_son_count("aa").') as admin_authority_son_count',//获取 子级的 总数
				);
			}
			
			$data['data'] =  db(parent::DB_APPLICATION_ID)
			->table('admin_authority aa')
			->joinon($admin_authority_parent)
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
			->select($select);				
				
			return $data;
		});
	}
	
	
	
		
	/**
	 * 获取子级的总数，SQL语句
	 * 
	 * @param	string		$alias
	 * @return	string
	 */
	public function sql_son_count($alias = ""){
		if( is_string($alias) && $alias != "" ){
			$alias .= ".";
		}
		
		return db(parent::DB_APPLICATION_ID)
		->table('admin_authority son')
		->where(array('son.admin_authority_parent_id = '.$alias.'admin_authority_id'))
		->find(array('count(distinct son.admin_authority_id) as count'), function($q){
			return $q['query']['find'];
		});
	}
	
	
	
	
	
	
	
	
}
?>