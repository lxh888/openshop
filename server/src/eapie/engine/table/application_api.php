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
class application_api extends main {
	
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, 'api_version', 'application_api');
	
	
	
		
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
			//接口表
            $api = array(
                'table' => 'api as a',
                'type' => 'INNER',
                'on' => 'a.api_id = aa.api_id'
            );
			
			//接口表
            $api_version = array(
                'table' => 'api_version as av',
                'type' => 'INNER',
                'on' => 'av.api_version_id = aa.api_version_id'
            );
			
			return db(parent::DB_SYSTEM_ID)
			->table('application_api aa')
			->joinon($api, $api_version)
			->where(array('aa.application_id=[+]', (string)$application_id), array('[and] aa.api_id=[+]', (string)$api_id))
			->find(array(
				'a.api_id',
				'a.api_program',
				'a.api_state',
				'a.administrator',
				'a.module_id',
				'a.api_admin',
				'a.api_insert_time',
				'a.api_update_time',
				'aa.api_version_id',
				'av.api_version_program',
				'av.api_version_state',
			));
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
			->table('application_api')
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
		});
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
		->table('application_api')
		->call('where', $where)
		->delete();
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear_system(self::CACHE_KEY);
		}
		
		return $bool;
	}
		
		
	
		
	
    /**
     * 批量插入数据
     * 
     * @param   array $data 数据，索引数组
     * @return  bool
     */
    public function insert_batch($data){
        if (empty($data))
            return false;

        $bool = db(parent::DB_SYSTEM_ID)
        ->table('application_api')
        ->batch()
        ->insert($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear_system(self::CACHE_KEY);

        return $bool;
    }
	
	
	
	
	/**
     * 返回应用列表
     * 
     * @param   string      $alias	别名称
     * @return  string
     */
    public function sql_join_list($alias = ""){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        
        return db(parent::DB_SYSTEM_ID)
        ->table('application_api aa_temp')
        ->where(
        	array('aa_temp.api_version_id = '.$alias.'api_version_id'), 
        	array('[and] aa_temp.api_id = '.$alias.'api_id')
		)
        ->select(array("GROUP_CONCAT(aa_temp.application_id SEPARATOR ',') as list"), function($q){
            return $q['query']['select'];
        });
        
    }
	
	
	
	
	
}
?>