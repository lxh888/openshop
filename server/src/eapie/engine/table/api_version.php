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
class api_version extends main {
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, 'api', 'application_api');
	
	
	
	/**
	 * 数据检测
	 * 
     * @var array
     */
    public $check = array(
        'api_version_id' => array(
            'args'=>array(
                'exist' => array('缺少接口版本ID参数'),
                'echo'  => array('接口版本ID类型不合法'),
                '!null' => array('接口版本ID不能为空')
            )
        ),
        'api_version_program' => array(
            'args'=>array(
                'method'  => array(array(parent::TABLE_API, 'check_json'), '接口版本程序入口的格式输入有误') ,
            ),
        ),
        'api_version_state' => array(
            'args'=>array(
                'match'=>array('/^[01]$/', '接口版本状态值必须是0或1'),
            )
        ),
    );
	
	
	
	
	/**
	 * 根据接口ID，判断数据是否存在
	 * 
	 * @param	string 		$api_id
	 */
	public function find_exists_api($api_id){
		return object(parent::CACHE)->data_system(__CLASS__, __METHOD__, array($api_id), function($api_id){
			return (bool)db(parent::DB_SYSTEM_ID)
			->table('api_version')
			->where(array('api_id=[+]', $api_id))
			->find('api_version_id');
		});
	}
	
	
	
	
    /**
     * 批量插入数据
	 * 
     * @param   array $data 数据，索引数组
     * @return  bool
     */
    public function insert_batch($data = array()){
        if( empty($data) )
        	return false;

		$bool = db(parent::DB_SYSTEM_ID)
        ->table('api_version')
		->batch()
        ->insert($data);
		
        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear_system(self::CACHE_KEY);

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
			->table('api_version av')
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
		});		
	}		
	
	
	
	/**
	 * 根据请求的接口ID与版本ID 获取数据
	 * 
	 * @param	string	$api_id
	 * @param	string	$api_version_id
	 * @return	array
	 */
	public function find_unique($api_id = '', $api_version_id = ''){
		if( empty($api_id) || empty($api_version_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data_system(__CLASS__, __METHOD__, array($api_id, $api_version_id), function($api_id, $api_version_id){
			return db(parent::DB_SYSTEM_ID)
			->table('api_version')
			->where(array('api_id=[+]', (string)$api_id), array('[and] api_version_id=[+]', (string)$api_version_id))
			->find();
		});
		
	}
	
	
			
	/**
	 * 根据唯一标识，删除数据
	 * 
	 * @param	array	$api_id
	 * @return	array
	 */
	public function remove_unique($api_id = '', $api_version_id = ''){
		if( empty($api_id) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_SYSTEM_ID)
		->table('api_version')
		->where(array('api_id=[+]', (string)$api_id), array('[and] api_version_id=[+]', (string)$api_version_id))
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
		->table('api_version')
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
		->table('api_version')
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
     * 返回接口的版本个数
     * 
     * @param   string      $alias	别名称
     * @return  string
     */
    public function sql_join_count($alias = ""){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        
        return db(parent::DB_SYSTEM_ID)
        ->table('api_version temp_av')
        ->where(array('temp_av.api_id = '.$alias.'api_id'))
        ->select(array('count(distinct temp_av.api_version_id) as c'), function($q){
            return $q['query']['select'];
        });
    }
    
	
	
	
	
}
?>