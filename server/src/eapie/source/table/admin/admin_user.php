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
class admin_user extends main {
	
	
	
	/*管理用户表*/
	
		
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, "admin");
	

		
	/**
	 * 数据检测
	 * 
	 * @var	array
	 */
	public $check = array(
		'user' => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少用户数据的参数"),
					'echo'=>array("用户数据的类型不合法"),
					'!null'=>array("用户数据不能为空"),
					),
        ),
        'user_id' => array(
        	//参数检测
			'args'=>array(
					'exist'=>array("用户ID或用户手机号输入有误"),
					'echo'=>array("用户ID的数据类型不合法"),
					'!null'=>array("用户ID异常"),
					),
			//检查编号是否存在      
            'exists'=>array(
                    '!method'=>array(array(parent::TABLE_ADMIN_USER, 'find_exists_user'), "该用户已经是管理员") 
            ),		
        ),
		"admin_user_info" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("管理员简介的数据类型不合法"),
					),
		),
		"admin_user_sort" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("管理员排序的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/i', "管理员排序必须是整数"),
					),
		),
		"admin_user_state" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("管理员状态的数据类型不合法"),
					'match'=>array('/^[01]{1}$/i', "管理员状态必须是0或1"),
					),
		),
	);
	
	
			
		
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
		->table('admin_user')
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
		->table('admin_user')
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
	 * @param	array	$user_id
	 * @return	array
	 */
	public function remove($user_id = ''){
		if( empty($user_id) ){
			return false;
		}
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('admin_user')
		->where(array('user_id=[+]', (string)$user_id))
		->delete();
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}		
		
	
		
		
	
			
	/**
	 * 获取一个数据
	 * 
	 * @param	string	$user_id
	 * @return	array
	 */
	public function find($user_id = ''){
		if( empty($user_id) ){
			return false;
			}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id), function($user_id){
			return db(parent::DB_APPLICATION_ID)
			->table('admin_user')
			->where(array('user_id=[+]', (string)$user_id))
			->find();
		});
		
	}	
	
	
	
	
	/**
	 * 根据用户ID，判断数据是否存在
	 * 
	 * @param	string 		$user_id
	 */
	public function find_exists_user($user_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id), function($user_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('admin_user')
			->where(array('user_id=[+]', $user_id))
			->find('admin_id');
		});
	}
	
	
	/**
	 * 根据管理角色ID，判断数据是否存在
	 * 
	 * @param	string 		$admin_id
	 */
	public function find_exists_administrator($admin_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($admin_id), function($admin_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('admin_user')
			->where(array('admin_id=[+]', $admin_id))
			->find('user_id');
		});
	}
	
	
	
	
		
    /**
     * 返回管理员的用户id
     * 
     * @param   string      $alias	别名称
     * @return  string
     */
    public function select_user_id(){
		//管理员数据
        $join_admin = array(
            'table' => 'admin a',
            'type' => 'INNER',
            'on' => 'a.admin_id = au.admin_id AND a.admin_state=1' //管理员状态。0 封禁|1 正常
        );
		
        return db(parent::DB_APPLICATION_ID)
        ->table('admin_user au')
		->joinon($join_admin)
        ->where( array('au.admin_id = a.admin_id'), array('[and] au.admin_user_state = 1') ) //管理员状态(保留字段)。0 封禁|1 正常
        ->select(array('au.user_id'));
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
                'on' => 'u.user_id = au.user_id'
            );
			
			
			//管理员数据
            $admin = array(
                'table' => 'admin a',
                'type' => 'left',
                'on' => 'a.admin_id = au.admin_id'
            );
			
			
			//先获取总条数
			$find_data = db(parent::DB_APPLICATION_ID)
			->table('admin_user au')
			->joinon($user, $admin)
			->call('where', $call_where)
			->find('count(distinct au.user_id) as count');
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
				$user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
				$select = array(
					"au.*",
					"a.admin_name",
					"a.admin_state",
					'u.user_logo_image_id',
                    'u.user_nickname',
                    'u.user_compellation',
                    'u.user_state',
                    '('.$user_phone_verify_list_sql.') as user_phone_verify_list',
				);
			}
						
			
			$data['data'] =  db(parent::DB_APPLICATION_ID)
			->table('admin_user au')
			->joinon($user, $admin)
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
			->select($select);				
				
			return $data;
		});
	}
	
	
	
	
	
	
	
	
	
	
	
	
	        
    /**
     * 返回用户的 登录手机号个数
     * 
     * @param   string      $alias	别名称
     * @return  string
     */
    public function sql_join_admin_count($alias = ""){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        
        return db(parent::DB_APPLICATION_ID)
        ->table('admin_user au')
        ->where( array('au.admin_id = '.$alias.'admin_id') )
        ->select(array('count(distinct au.user_id) as admin_user_count'), function($q){
            return $q['query']['select'];
        });
    }
    
    
    /**
	 * 根据条件，获取多个管理员信息
	 */
	public function select($config=array()){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            $where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();


            return db(parent::DB_APPLICATION_ID)
            ->table('admin_user')
            ->call('where', $where)
            ->call('orderby', $orderby)
            ->call('limit', $limit)
            ->select($select);
        });
	}
	
	
	
}
?>