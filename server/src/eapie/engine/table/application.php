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
class application extends main {
	
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__);
	
	
	
	
	
			
	/**
	 * 数据检测
	 * 
     * @var array
     */
    public $check = array(
		'primary_key' => array(
            'args'=>array(
                'exist' => array('缺少应用主键参数'),
                'echo'  => array('应用主键类型不合法'),
                '!null' => array('应用主键不能为空')
            ),
            'exists_id' => array(
                'method'=> array(array(parent::TABLE_APPLICATION, 'find_exists_id'), '应用主键有误，应用不存在') 
            ),
        ),
        
        'application_id' => array(
            'args'=>array(
                'exist' => array('缺少应用ID参数'),
                'echo'  => array('应用ID类型不合法'),
                '!null' => array('应用ID不能为空')
            ),
            'exists_id' => array(
                'method'=> array(array(parent::TABLE_APPLICATION, 'find_exists_id'), '应用ID有误，应用不存在') 
            ),
			//检查编号是否存在，则报错		
			'register_id'=>array(
				'!method'=>array(array(parent::TABLE_APPLICATION, 'find_exists_id'), "应用ID已经存在") 
			),
        ),
        'application_name' => array(
            'args'=>array(
                'echo'  => array('应用名称的数据类型不合法'),
            ),
            'length' => array('<length'=>array(200, '应用名称的字符长度太多'))    
        ),
        
		'application_info' => array(
            'args'=>array(
                'echo'  => array('应用简介的数据类型不合法'),
            ),
        ),
        'application_json' => array(
            'args'=>array(
                'method'  => array(array(parent::TABLE_APPLICATION, 'check_json'), '应用配置信息的格式输入有误') ,
            ),
        ),
		"application_sort" => array(
			//参数检测
			'args'=>array(
				'echo' => array("应用排序的数据类型不合法"),
				'match' => array('/^[0-9]{0,}$/i', "应用排序必须是整数"),
				),
		),
		
        'application_state' => array(
            'args'=>array(
                'match'=>array('/^[01]$/', '应用状态值必须是0或1'),
            )
        ),
        'administrator' => array(
            'args'=>array(
                'match'=>array('/^[01]$/', '是否为系统引擎状态值必须是0或1'),
            )
        ),
		'application_on_off' => array(
            'args'=>array(
                'match'=>array('/^[01]$/', '应用开关状态值必须是0或1'),
            )
        ),
        
		'application_warning' => array(
            'args'=>array(
                'echo'  => array('应用封禁信息的数据类型不合法'),
            ),
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
    public function find_exists_id($application_id = ''){
    	if( empty($application_id) ){
    		return false;
    	}
        return object(parent::CACHE)->data_system(__CLASS__, __METHOD__, array($application_id), function ($application_id) {
                return (bool)db(parent::DB_SYSTEM_ID)
                ->table('application')
                ->where(array('application_id=[+]', $application_id))
                ->find('application_id');
        });
    }
		
	
	
	
	
	/**
	 * 根据请求的接口ID，获取一个数据
	 * 
	 * @param	string	$application_id
	 * @return	array
	 */
	public function find_request($application_id = ''){
		if( empty($application_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data_system(__CLASS__, __METHOD__, array($application_id), function($application_id){
			return db(parent::DB_SYSTEM_ID)
			->table('application')
			->where(array('application_id=[+]', (string)$application_id))
			->find();
		});
		
	}
	
	
	
	
	/**
	 * 获取当前应用所有的模型表的类名称
	 * 
	 * @param	void
	 * @return	array
	 */
	public function get_table_list(){
		$pathinfo = pathinfo(__DIR__);
		$table_class_name = array();
		//引擎模型表
		$engine_files = cmd(array(ROOT_PATH.'/eapie/engine/table', true), "disk file_path");
		if( !empty($engine_files) ){
			foreach($engine_files as $file_name){
				$table_class_name[] = preg_replace("/\.php$/i", "", $file_name);
			}
		}
		
		//项目模型表
		$source_files = cmd(array(ROOT_PATH.'/eapie/source/table', true), "disk file_path");
		if( !empty($source_files) ){
			foreach($source_files as $file_name){
				$table_class_name[] = preg_replace("/\.php$/i", "", $file_name);
			}
		}
		
		return $table_class_name;
	}
	
	
	
	
			
	/**
	 * 获取一条数据
	 * 
	 * @param	array	$application_id
	 * @return	array
	 */
	public function find( $application_id = '' ){
		if( empty($application_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data_system(__CLASS__, __METHOD__, array($application_id), function($application_id) {
			return db(parent::DB_SYSTEM_ID)
			->table('application')
			->where(array('application_id=[+]', (string)$application_id))
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
		->table('application')
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
		->table('application')
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
		->table('application')
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
	 * @param	array	$application_id
	 * @return	array
	 */
	public function remove($application_id = ''){
		if( empty($application_id) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_SYSTEM_ID)
		->table('application')
		->where(array('application_id=[+]', (string)$application_id))
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
			->table('application')
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
			->table('application a')
			->call('where', $call_where)
			->find('count(distinct a.application_id) as count');
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
					'a.*',
				);
			}
			
			$data['data'] =  db(parent::DB_SYSTEM_ID)
			->table('application a')
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
			->select($select);				
				
			return $data;
		});
	}
	
	
	

	
	
	
	
	
	
	
	
	
	
}
?>