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
class api extends main {
	
	
	/*表映射的四大件：find、insert、update、delete*/
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, 'api_version', 'application_api');
	
		
	/**
	 * 数据检测
	 * 
     * @var array
     */
    public $check = array(
		'primary_key' => array(
            'args'=>array(
                'exist' => array('缺少接口主键参数'),
                'echo'  => array('接口主键类型不合法'),
                '!null' => array('接口主键不能为空')
            ),
            'exists_id' => array(
                'method'=> array(array(parent::TABLE_API, 'find_exists_id'), '接口主键有误，接口不存在') 
            ),
        ),
        
        'api_id' => array(
            'args'=>array(
                'exist' => array('缺少接口ID参数'),
                'echo'  => array('接口ID类型不合法'),
                '!null' => array('接口ID不能为空')
            ),
            'exists_id' => array(
                'method'=> array(array(parent::TABLE_API, 'find_exists_id'), '接口ID有误，接口不存在') 
            ),
			//检查编号是否存在，则报错		
			'register_id'=>array(
				'!method'=>array(array(parent::TABLE_API, 'find_exists_id'), "接口ID已经存在") 
			),
        ),
        'api_name' => array(
            'args'=>array(
                'echo'  => array('接口名称的数据类型不合法'),
            ),
            'length' => array('<length'=>array(200, '接口名称的字符长度太多'))    
        ),
        
		'api_info' => array(
            'args'=>array(
                'echo'  => array('接口简介的数据类型不合法'),
            ),
        ),
        'api_explain' => array(
            'args'=>array(
                'echo'  => array('接口详细说明的数据类型不合法'),
            ),
        ),
        'api_program' => array(
            'args'=>array(
                'method'  => array(array(parent::TABLE_API, 'check_json'), '接口程序入口的格式输入有误') ,
            ),
        ),
        'api_request_args' => array(
            'args'=>array(
                'method'  => array(array(parent::TABLE_API, 'check_json'), '接口请求参数的格式输入有误') ,
            ),
        ),
        'api_response_args' => array(
            'args'=>array(
                'method'  => array(array(parent::TABLE_API, 'check_json'), '接口响应参数的格式输入有误') ,
            ),
        ),
		"api_sort" => array(
			//参数检测
			'args'=>array(
				'echo' => array("接口排序的数据类型不合法"),
				'match' => array('/^[0-9]{0,}$/i', "接口排序必须是整数"),
				),
		),
        'api_state' => array(
            'args'=>array(
                'match'=>array('/^[01]$/', '接口状态值必须是0或1'),
            )
        ),
        'administrator' => array(
            'args'=>array(
                'match'=>array('/^[01]$/', '是否为系统接口值必须是0或1'),
            )
        ),
		'api_admin' => array(
            'args'=>array(
                'match'=>array('/^[01]$/', '是否为后台接口值必须是0或1'),
            )
        ),
        
		
    );
	
	
	
		
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
     * 根据ID，判断数据是否存在
	 * 
     * @param  string	 $api_id		接口ID
     * @return bool
     */
    public function find_exists_id($api_id = ''){
    	if( empty($api_id) ){
    		return false;
    	}
        return object(parent::CACHE)->data_system(__CLASS__, __METHOD__, array($api_id), function ($api_id) {
                return (bool)db(parent::DB_SYSTEM_ID)
                ->table('api')
                ->where(array('api_id=[+]', $api_id))
                ->find('api_id');
        });
    }
		
	
	
	
	/**
	 * 根据请求的接口ID，获取一个数据
	 * 
	 * @param	string	$api_id
	 * @return	array
	 */
	public function find_request($api_id = ''){
		if( empty($api_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data_system(__CLASS__, __METHOD__, array($api_id), function($api_id){
			return db(parent::DB_SYSTEM_ID)
			->table('api')
			->where(array('api_id=[+]', (string)$api_id))
			->find("api_id, api_program, api_state, administrator, module_id, api_admin, api_insert_time, api_update_time");
		});
		
	}
	
	
	
	
	/**
	 * 根据接口ID 和 应用ID 获取接口信息
	 * 
	 * @param	string	$api_id				接口ID
	 * @param	string	$application_id		应用ID
	 *
	 */
	public function find_join($api_id = '', $application_id = ''){
		if( empty($application_id) || empty($api_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data_system(__CLASS__, __METHOD__, array($api_id, $application_id), function($api_id, $application_id){
				
			//应用接口表
			$application_api = array(
                'table' => 'application_api as aa',
                'type' => 'LEFT',
                'on' => 'a.api_id = aa.api_id AND aa.application_id="'.cmd(array((string)$application_id), 'str addslashes').'"'
            );
				
			//接口版本表
            $api_version = array(
                'table' => 'api_version as av',
                'type' => 'LEFT',
                'on' => 'aa.api_id = av.api_id AND av.api_version_id = aa.api_version_id'
            );
			
			return db(parent::DB_SYSTEM_ID)
			->table('api a')
			->joinon($application_api, $api_version)
			->where(array('a.api_id=[+]', (string)$api_id))
			->find(array(
				'a.api_id',
				'a.api_program',
				'a.api_state',
				'a.administrator',
				'a.module_id',
				'a.api_admin',
				'a.api_insert_time',
				'a.api_update_time',
				'av.api_version_id ',
				'av.api_version_program',
				'av.api_version_state',
			));
		});
	}
	
	
	
	
	
	
	
	
	
	/**
	 * 获取一个数据
	 * 
	 * @param	string	$api_id
	 * @return	array
	 */
	public function find($api_id = ''){
		if( empty($api_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data_system(__CLASS__, __METHOD__, array($api_id), function($api_id){
			return db(parent::DB_SYSTEM_ID)
			->table('api')
			->where(array('api_id=[+]', (string)$api_id))
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
		return object(parent::CACHE)->data_system(__CLASS__, __METHOD__, array($config), function($config){
			$where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
			$orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
			$limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
			$select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
			
			return db(parent::DB_SYSTEM_ID)
			->table('api')
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
		$bool = (bool)db(parent::DB_SYSTEM_ID)
		->table('api')
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
		->table('api')
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
	 * 删除数据
	 * 
	 * @param	array	$where
	 * @return	array
	 */
	public function delete($where = array()){
		if( empty($where) ){
			return false;
		}
		$bool = (bool)db(parent::DB_SYSTEM_ID)
		->table('api')
		->call('where', $where)
		->delete();
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear_system(self::CACHE_KEY);
		}
		
		return $bool;
	}
		
		
		
		
			
	/**
	 * 根据唯一标识，删除数据
	 * 
	 * @param	array	$api_id
	 * @return	array
	 */
	public function remove($api_id = ''){
		if( empty($api_id) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_SYSTEM_ID)
		->table('api')
		->where(array('api_id=[+]', (string)$api_id))
		->delete();
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear_system(self::CACHE_KEY);
		}
		
		return $bool;
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
			
			//模块数据
			$module = array(
				'table' => 'module m',
				'type' => 'left',
				'on' => 'm.module_id = a.module_id'
			);
			
			
			
			//先获取总条数
			$find_data = db(parent::DB_SYSTEM_ID)
			->table('api a')
			->joinon($module)
			->call('where', $call_where)
			->find('count(distinct a.api_id) as count');
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
				$sql_api_version_count = object(parent::TABLE_API_VERSION)->sql_join_count('a');
				$select = array(
					'a.*',
					'm.*',
					'('.$sql_api_version_count.') as api_version_count',
				);
			}
			
			$data['data'] =  db(parent::DB_SYSTEM_ID)
			->table('api a')
			->joinon($module)
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
			->select($select);				
				
			return $data;
		});
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>