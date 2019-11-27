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
class admin_log extends main {
	
	/*管理员操作日记表*/
	
	
	
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
	 * 插入日志
	 * 
	 * @param	array		$real_args		实际传入的参数
	 * @param	array		$clear_args		清理过后的参数
	 * @return	bool
	 */
	public function insert($real_args = array(), $clear_args = array()){
		if( empty($_SESSION['user_id']) || empty(object(parent::MAIN)->api['api_id']) ){
			return false;
		}
		
		
		$data = array(
			'admin_log_id' => $this->get_unique_id(),
			'user_id' => $_SESSION['user_id'],
			'api_id' => object(parent::MAIN)->api['api_id'],
			'admin_log_time' => time(),
			'session_id' => $_SESSION['session_id'],
			'admin_log_ip' => HTTP_IP,
			'admin_log_session' => cmd(array($_SESSION), 'json encode')
		);
		
		if( !empty($real_args) ){
			$data['admin_log_real_args'] = cmd(array($real_args), 'json encode');
		}
		if( !empty($clear_args) ){
			$data['admin_log_clear_args'] = cmd(array($clear_args), 'json encode');
		}
		
		return (bool)db(parent::DB_APPLICATION_ID)
		->table('admin_log')
		->insert($data);
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
                'on' => 'u.user_id = al.user_id'
            );
			
			
			//先获取总条数
			$find_data = db(parent::DB_APPLICATION_ID)
			->table('admin_log al')
			->joinon($user)
			->call('where', $call_where)
			->find('count(distinct al.admin_log_id) as count');
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
					"al.*",
					'u.user_logo_image_id',
                    'u.user_nickname',
                    'u.user_compellation',
                    'u.user_state',
                    '('.$user_phone_verify_list_sql.') as user_phone_verify_list',
				);
			}
						
			
			$data['data'] =  db(parent::DB_APPLICATION_ID)
			->table('admin_log al')
			->joinon($user)
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
			->select($select);				
			
			if( empty($data['data']) ){
				return $data;
			}
			
			$api_ids = array();
			foreach($data['data'] as $value){
				if( !isset($api_ids[$value['api_id']]) ){
					$api_ids[$value['api_id']] = $value['api_id'];
				}
			}
			
			$api_id_in_string = "\"".implode("\",\"", $api_ids)."\",\"\"";//还要加上空格
			$api_config = array(
				'where' => array(
					array('[and] api_id IN([-])', $api_id_in_string, true)
				),
				'select' => array(
					'api_id',
					'api_name',
				)
			);
			$api_data = object(parent::TABLE_API)->select($api_config);
			if( empty($api_data) ){
				return $data;
			}
			
			foreach($data['data'] as $key => $value){
				foreach($api_data as $k => $v){
					if( $value['api_id'] == $v['api_id']){
						$data['data'][$key]['api'] = $v;
						break;
					}
				}
				
				if( empty($data['data'][$key]['api']) ){
					$data['data'][$key]['api'] = array();
				}
			}
				
			return $data;
		});
	}
	
	
	
	
	
	
	
	
	
	
}
?>