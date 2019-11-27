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
class management extends main {
	
	
	/*管理栏目*/
	
	
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
		'management_id' => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少菜单ID参数"),
					'echo'=>array("菜单ID数据类型不合法"),
					'!null'=>array("菜单ID不能为空"),
					),
			//检查编号是否存在
			'exists_id'=>array(
					'method'=>array(array(parent::TABLE_MANAGEMENT, 'find_exists_id'), "菜单ID有误，数据不存在",) 
			),
		),
		
		"management_parent_id" => array(
			'args'=>array(
					'echo'=>array("菜单的父ID数据类型不合法"),
					),
			//检查编号是否存在
			'exists_id'=>array(
					'method'=>array(array(parent::TABLE_MANAGEMENT, 'find_exists_id'), "菜单的父ID有误，数据不存在",) 
			),
		),
		
		"management_name" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少菜单名称参数"),
					'echo'=>array("菜单名称数据类型不合法"),
					'!null'=>array("菜单名称不能为空"),
					),
			//字符长度检测
			'length' => array(
					'<length'=>array(200, "菜单名称的字符长度太多")
					),		
		),
		"management_page" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("菜单页面标识的数据类型不合法"),
					),
		),
		"management_info" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("菜单简介的数据类型不合法"),
					),
		),
		"management_label_before" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("菜单前标签的数据类型不合法"),
					),
		),
		"management_label_after" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("菜单后标签的数据类型不合法"),
					),
		),
		"management_href" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("菜单跳转链接的数据类型不合法"),
					),
		),
		"management_target" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("菜单跳转目标的数据类型不合法"),
					),
		),
		"management_sort" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("菜单排序的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/i', "菜单排序必须是整数"),
					),
		),
		"management_query" => array(
			'args'=>array(
					'echo'=>array("菜单query参数的数据类型不合法"),
					'method'  => array(array(parent::TABLE_MANAGEMENT, 'check_json'), '菜单query参数的JSON参数格式输入有误') ,
					),
		),
		"management_path" => array(
			'args'=>array(
					'echo'=>array("菜单path参数的数据类型不合法"),
					'method'  => array(array(parent::TABLE_MANAGEMENT, 'check_json'), '菜单path参数的JSON参数格式输入有误') ,
					),
		),
		"management_hide" => array(
			'args'=>array(
					'echo'=>array("菜单隐藏状态值的数据类型不合法"),
					'match'=>array('/^[01]{1}$/', "菜单隐藏状态值必须是0或1"),
					),
		),
		"management_state" => array(
			'args'=>array(
					'echo'=>array("菜单状态值的数据类型不合法"),
					'match'=>array('/^[01]{1}$/', "菜单状态值必须是0或1"),
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
     * 判断JSON数据
	 * 
     * @param  string	 $slideshow_id		轮播图ID
     * @return bool
     */
	public function check_json($json){
		if( !is_string($json) ){
			return false;
		}
		$json_array = cmd(array($json), "json decode");
		return is_array($json_array)? true : false;
	}
	
	
	
		
				
	/**
	 * 根据ID，判断是否存在
	 * 
	 * @param	string 		$management_id
	 */
	public function find_exists_id($management_id){
		return object(parent::CACHE)->data_system(__CLASS__, __METHOD__, array($management_id), function($management_id){
			return (bool)db(parent::DB_SYSTEM_ID)
			->table('management')
			->where(array('management_id=[+]', $management_id))
			->find('management_id');
		});
	}
	
	
		
		
	/**
	 * 根据ID，判断是否存在子级
	 * 
	 * @param	string 		$management_id
	 */
	public function find_exists_son_id($management_id){
		return object(parent::CACHE)->data_system(__CLASS__, __METHOD__, array($management_id), function($management_id){
			return (bool)db(parent::DB_SYSTEM_ID)
			->table('management')
			->where(array('management_parent_id=[+]', $management_id))
			->find('management_id');
		});
	}
	
	
	
	
	
		
		
	/**
	 * 获取一条数据
	 * 
	 * @param	array	$management_id
	 * @return	array
	 */
	public function find($management_id = ''){
		if( empty($management_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data_system(__CLASS__, __METHOD__, array($management_id), function($management_id) {
			return db(parent::DB_SYSTEM_ID)
			->table('management')
			->where(array('management_id=[+]', (string)$management_id))
			->find();
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
		
		$bool = (bool)db(parent::DB_SYSTEM_ID)
		->table('management')
		->call('data', $call_data)
		->insert($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear_system(self::CACHE_KEY);
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
		$bool = (bool)db(parent::DB_SYSTEM_ID)
		->table('management')
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
	 * @param	array	$management_id
	 * @return	array
	 */
	public function remove($management_id = ''){
		if( empty($management_id) ){
			return false;
		}
		$bool = (bool)db(parent::DB_SYSTEM_ID)
		->table('management')
		->where(array('management_id=[+]', (string)$management_id))
		->delete();
		
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
			->table('management')
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
		return object(parent::CACHE)->data_system(__CLASS__, __METHOD__, array($parent_config, $son_config), function($parent_config, $son_config){
			$parent_where = isset($parent_config['where']) && is_array($parent_config['where'])? $parent_config['where'] : array();
			$parent_orderby = isset($parent_config['orderby']) && is_array($parent_config['orderby'])? $parent_config['orderby'] : array();
			$parent_limit = isset($parent_config['limit']) && is_array($parent_config['limit'])? $parent_config['limit'] : array();
			$parent_select = isset($parent_config['select']) && is_array($parent_config['select'])? $parent_config['select'] : array();
			
			$son_where = isset($son_config['where']) && is_array($son_config['where'])? $son_config['where'] : array();
			$son_orderby = isset($son_config['orderby']) && is_array($son_config['orderby'])? $son_config['orderby'] : array();
			$son_limit = isset($son_config['limit']) && is_array($son_config['limit'])? $son_config['limit'] : array();
			$son_select = isset($son_config['select']) && is_array($son_config['select'])? $son_config['select'] : array();
			
			$data = db(parent::DB_SYSTEM_ID)
			->table('management')
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
			$management_parent_ids = array();
			foreach($data as $key => $value){
				if( !isset($value['management_id']) || !empty($value['management_parent_id']) ){
					//分类id不存在，或者不是顶级，则直接返回
					break;
				}
				$data[$key]["son"] = array();//初始化键值
				$management_parent_ids[] = $value['management_id'];
			}
			
			//没有父类
			if( empty($management_parent_ids) ){
				return $data;
			}
			
			$son_in_string = "\"".implode("\",\"", $management_parent_ids)."\"";
			$son_where[] = array("[and] management_parent_id IN([-])", $son_in_string, true);//是不加单引号并且强制不过滤
			$son_data = db(parent::DB_SYSTEM_ID)
			->table('management')
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
					if($son_value['management_parent_id'] == $parent_value['management_id']){
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
		
			//父级数据
			$management_parent = array(
				'table' => 'management parent_m',
				'type' => 'left',
				'on' => 'parent_m.management_id = m.management_parent_id'
			);
			
			//父级数据
			$authority = array(
				'table' => 'authority a',
				'type' => 'left',
				'on' => 'a.authority_id = m.authority_id'
			);
			
			
			//先获取总条数
			$find_data = db(parent::DB_SYSTEM_ID)
			->table('management m')
			->joinon($management_parent, $authority)
			->call('where', $call_where)
			->find('count(distinct m.management_id) as count');
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
					'parent_m.management_name as management_parent_name',
					'a.authority_name',
					'('.$this->sql_son_count("m").') as management_son_count',//获取 子级的 总数
				);
			}
			
			$data['data'] =  db(parent::DB_SYSTEM_ID)
			->table('management m')
			->joinon($management_parent, $authority)
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
		
		return db(parent::DB_SYSTEM_ID)
		->table('management son')
		->where(array('son.management_parent_id = '.$alias.'management_id'))
		->find(array('count(distinct son.management_id) as count'), function($q){
			return $q['query']['find'];
		});
	}
	
	
	
	
	
	
	
	
	
	
}
?>