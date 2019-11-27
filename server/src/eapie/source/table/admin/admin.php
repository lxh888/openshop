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
class admin extends main {
	
	
	
	/*管理角色表*/
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, "admin_user");
	

	
	
	
	/**
	 * 数据检测
	 * 
	 * @var	array
	 */
	public $check = array(
		'admin_id' => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少管理角色ID参数"),
					'echo'=>array("管理角色ID数据类型不合法"),
					'!null'=>array("管理角色ID不能为空"),
					),
			//检查编号是否存在		
			'exists_id'=>array(
					'method'=>array(array(parent::TABLE_ADMIN, 'find_exists_id'), "管理角色ID有误，数据不存在",) 
			),
			
		),
		
		"admin_name" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少管理角色名称参数"),
					'echo'=>array("管理角色名称数据类型不合法"),
					'!null'=>array("管理角色名称不能为空"),
					),
			//字符长度检测
			'length' => array(
					'<length'=>array(200, "分类名称的字符长度太多")
					),				
		),
		
		
		
		"admin_info" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("管理角色简介的数据类型不合法"),
					),
		),
		
		
		"admin_sort" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("管理角色排序的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/i', "管理角色排序必须是整数"),
					),
		),
		
		
		"admin_state" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("管理角色状态的数据类型不合法"),
					'match'=>array('/^[01]{1}$/i', "管理角色状态必须是0或1"),
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
	 * 根据ID，判断数据是否存在
	 * 
	 * @param	string 		$admin_id
	 */
	public function find_exists_id($admin_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($admin_id), function($admin_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('admin')
			->where(array('admin_id=[+]', $admin_id))
			->find('admin_id');
		});
	}
	
		
	
	
		
	/**
	 * 获取当前管理人员、管理角色的数据
	 * 并且要联表 admin_manager 
	 * 
	 * @param	void
	 * @return	array
	 */
	public function find_info($user_id = ''){
		if($user_id == '' && !empty($_SESSION['user_id'])){
			$user_id = $_SESSION['user_id'];
		}
		if( empty($user_id) ){
			return array();
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id), function($user_id){
			//管理用户表
			$admin_user = array(
			    'table' => 'admin_user as au',
			    'type' => 'INNER',
			    'on' => 'au.admin_id = a.admin_id'
			);
			
			
			//用户表
			$user = array(
			    'table' => 'user as u',
			    'type' => 'INNER',
			    'on' => 'u.user_id = au.user_id'
			);
			
			
			return db(parent::DB_APPLICATION_ID)
			->table('admin a')//管理角色表
			->joinon($admin_user, $user)
			->where(array('au.user_id=[]', (string)$user_id), array('u.user_id=[]', (string)$user_id) )
			->find( array('u.user_state', 'au.authority_id as self_authority_id', 'au.*', 'a.*') );
			
		});
		
	}
	
	
	
		
	/**
	 * 获取一个数据
	 * 
	 * @param	string	$admin_id
	 * @return	array
	 */
	public function find($admin_id = ''){
		if( empty($admin_id) ){
			return false;
			}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($admin_id), function($admin_id){
			return db(parent::DB_APPLICATION_ID)
			->table('admin')
			->where(array('admin_id=[+]', (string)$admin_id))
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
		->table('admin')
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
	 * @param	array	$admin_id
	 * @return	array
	 */
	public function remove($admin_id = ''){
		if( empty($admin_id) ){
			return false;
		}
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('admin')
		->where(array('admin_id=[+]', (string)$admin_id))
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
		->table('admin')
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
			->table('admin')
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
		});
	}		
	
	
	
	/**
	 * 获取所有的数据
	 * 
	 * @param	array	$config		配置信息
	 * @return	array
	 */
	public function select_join($config = array()){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
			$call_where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
			$call_orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
			$call_limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
			$select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
			
			if( empty($select) ){
				$sql_join_admin_count = object(parent::TABLE_ADMIN_USER)->sql_join_admin_count("a");
				$select = array(
					"a.*",
					"(".$sql_join_admin_count.") as admin_user_count"
				);
			}
			
			return db(parent::DB_APPLICATION_ID)
			->table('admin a')
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
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
		
			//先获取总条数
			$find_data = db(parent::DB_APPLICATION_ID)
			->table('admin a')
			->call('where', $call_where)
			->find('count(distinct a.admin_id) as count');
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
				$sql_join_admin_count = object(parent::TABLE_ADMIN_USER)->sql_join_admin_count("a");
				$select = array(
					"a.*",
					"(".$sql_join_admin_count.") as admin_user_count"
				);
			}
						
			
			$data['data'] =  db(parent::DB_APPLICATION_ID)
			->table('admin a')
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
			->select($select);				
				
			return $data;
		});
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>