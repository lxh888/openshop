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
class session extends main {
	
	
	/**
     * 数据检测
     * @var array
     */
    public $check = array(
    	'websocket_client_id' => array(
            'args' => array(
                'exist'=> array('缺少websocket连接ID参数'),
                'echo' => array('websocket连接ID的数据类型不合法'),
                '!null'=> array('websocket连接ID不能为空'),
            ),
        ),
        'session_websocket_token' => array(
            'args' => array(
                'exist'=> array('缺少websocket会话令牌参数'),
                'echo' => array('websocket会话令牌的数据类型不合法'),
                '!null'=> array('websocket会话令牌不能为空'),
            ),
        ),
        'websocket_server_time' => array(
            'args' => array(
                'exist'=> array('缺少websocket服务器时间参数'),
                'echo' => array('websocket服务器时间的数据类型不合法'),
                'match'=>array('/^[\d]{1,}$/', "websocket服务器时间必须是整数"),
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
		//保持会话id为72位
		return cmd(array(58), 'random autoincrement');
	}	
	
	
	
	/**
	 * 获取失效时间
	 * 
	 * @param	void
	 * @return	string
	 */
	public function get_expire_time(){
		if( empty($_SESSION['user_id']) ){
	    	//当前时间+ 1个小时的秒数
	    	return time() + 3600;
	    }else{
	    	//登录状态，当前时间+ 30天的秒数
	    	return time() + 2592000;
	        }
	}	
	
	
	
	
	
	/**
	 * 获取一个令牌号
	 * 
	 * @param	string 		$session_id
	 * @return	string
	 */
	public function get_token_id($session_id = ''){
		if( empty($session_id) && !empty($_SESSION['session_id']) ){
			$session_id = $_SESSION['session_id'];
		}
		
		$time = time();
		return md5($session_id.$time).cmd(array(18), 'random autoincrement').md5($time.$session_id);
	}	
	
	
	
				
	/**
	 * 根据ID，判断数据是否存在
	 * 
	 * @param	string 		$session_id
	 */
	public function find_exists_id($session_id = ''){
		if( empty($session_id) ){
			return false;
		}
		return (bool)db(parent::DB_SYSTEM_ID)
		->table('session')
		->where(array('session_id=[+]', (string)$session_id))
		->find('session_id');
	}
	
	
	
	
	/**
	 * 根据令牌 获取会话编号
	 * 
	 * @param	string	$token
	 * @return	array
	 */
	public function find_token_get_id($token = ''){
		if( empty($token) ){
			return false;
		}
		return db(parent::DB_SYSTEM_ID)
		->table('session')
		->where(array('session_right_token=[+]', (string)$token), array('[or] session_left_token=[+]', (string)$token))
		->find('session_id', 'session_right_token', 'session_left_token', 'session_lock');
	}		
	
	
	
	/**
	 * 根据websocket令牌 获取会话数据
	 * 
	 * @param	string	$session_websocket_token
	 * @return	array
	 */
	public function find_websocket_token($session_websocket_token = ''){
		if( empty($session_websocket_token) ){
			return false;
		}
		return db(parent::DB_SYSTEM_ID)
		->table('session')
		->where( array('session_websocket_token=[+]', (string)$session_websocket_token) )
		->find();
	}
	
	
	
		
	/**
	 * 获取一个数据
	 * 
	 * @param	string	$session_id
	 * @return	array
	 */
	public function find($session_id = ''){
		if( empty($session_id) ){
			return false;
		}
		return db(parent::DB_SYSTEM_ID)
		->table('session')
		->where(array('session_id=[+]', (string)$session_id))
		->find();
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
		if( empty($where) || empty($data) ){
			return false;
		}
		return (bool)db(parent::DB_SYSTEM_ID)
		->table('session')
		->call('where', $where)
		->call('data', $call_data)
		->update($data);
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
		return (bool)db(parent::DB_SYSTEM_ID)
		->table('session')
		->call('where', $where)
		->delete();
	}
		
		
	
	
	
				
	/**
	 * 根据唯一标识，删除数据
	 * 
	 * @param	array	$session_id
	 * @return	array
	 */
	public function remove($session_id = ''){
		if( empty($session_id) ){
			return false;
		}
		return (bool)db(parent::DB_SYSTEM_ID)
		->table('session')
		->where(array('session_id=[+]', (string)$session_id))
		->delete();
	}		
		
		
	
	
		
	/**
	 * 获取多个用户数据
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
		$where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
		$orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
		$limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
		$select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
		
		return db(parent::DB_SYSTEM_ID)
		->table('session')
		->call('where', $where)
		->call('orderby', $orderby)
		->call('limit', $limit)
		->select($select);
	}		
	
	
	
	/**
	 * 根据用户ID和应用ID 获取会话数据
	 * 
	 * @param	string	$user_id
	 * @return	array
	 */
	public function select_application_user($application_id = '', $user_id = ''){
		if( empty($user_id) ){
			return false;
		}
		
		if( is_array($user_id) ){
			$in_string = "\"".implode("\",\"", $user_id)."\"";
			$where_user_id = array('user_id IN([-])', $in_string, true);
		}else{
			$where_user_id = array('user_id=[+]', (string)$user_id);
		}
		
		return db(parent::DB_SYSTEM_ID)
		->table('session')
		->where( $where_user_id, array('[and] application_id=[+]', (string)$application_id) )
		->select();
	}
	
	
	
	
		
	/**
	 * 初始化会话
	 * 
	 * @param	string			$session_id				会话id
	 * @param	bool | NULL		$exists					开启会话时，会话数据是否存在
	 * @return	bool
	 */
	public function insert_init($session_id = '', $exists = NULL){
		$session_id = is_string($session_id) || is_numeric($session_id)? (string)$session_id : '';
		
		//获取数据库配置
		$info = db(parent::DB_SYSTEM_ID)->info();
		$db_config = $info['config'];
		$db_system_id = parent::DB_SYSTEM_ID;
		$_SESSION = db(parent::DB_SYSTEM_ID, true, $db_config)->session($session_id, $exists);
		if( empty($_SESSION['session_id']) ){
			return false;
		}
		
		//sleep ( 30 );//先延迟0.5秒
		
		//析构，覆盖更新会话
		destruct('db.session.update:'.$_SESSION['session_id'], true, array($db_config, $db_system_id), function($db_config, $db_system_id){
			//再会话更新之前，锁设为0
			$_SESSION['session_lock'] = 0;
			db($db_system_id, true, $db_config)->session($_SESSION);
		});
		
		return true;
	}
	
	
	
	
	
		
	/**
	 * 删除数据
	 * 
	 * @param	array	$where
	 * @return	array
	 */
	public function delete_destruct($where = array()){
		if( !isset($_SESSION['session_id']) ){
			return true;
		}
		
		//登陆的用户 更新 user_log_out_time
		if( !empty($_SESSION['user_id']) ){
			object(parent::TABLE_USER_LOG)->update_log_out();
		}
		
		//删除当前会话
		$bool = $this->remove($_SESSION['session_id']);
		
		return $bool;
	}
	
	
	
	
	
	
	/**
	 * 获取当前接口合法会话数据
	 * 
	 * @param	void
	 * @return	array
	 */
	public function get_api_legal_data(){
		if( empty($_SESSION) || empty($_SESSION['session_id']) ){
			return false;
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'session_found_time', 
			'session_now_time', 
			'session_expire_time', 
			'session_websocket_token',
			'session_right_token',
			'session_left_token'
			);
		$session_data = cmd(array($_SESSION, $whitelist), 'arr whitelist');
		
		return $session_data;
	}
	
	
	
	
		
	/**
	 * 获取当前合法会话数据
	 * 
	 * @param	void
	 * @return	array
	 */
	public function get_legal_data(){
		if( empty($_SESSION) || empty($_SESSION['session_id']) ){
			return false;
		}
		
		///白名单 私密数据不能获取
		$whitelist = array(
			'session_id',
			'user_id',
			'session_ip',
			'session_browser',
			'session_public',
			'session_found_time', 
			'session_now_time', 
			'session_expire_time', 
			'session_websocket_token',
			'session_right_token',
			'session_left_token'
			);
		$session_data = cmd(array($_SESSION, $whitelist), 'arr whitelist');
		
		return $session_data;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>