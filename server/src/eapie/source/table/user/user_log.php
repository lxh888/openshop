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



namespace eapie\source\table\user;
use eapie\main;
class user_log extends main {
	
	
	/*用户记录表。用户的登陆、退出日志 */
	
	
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
	 * 更新用户退出时间
	 * 
	 * @param	string		$user_log_id
	 * @return	bool
	 */
	public function destruct_out_time(){
		//获取数据库配置
		$info = db(parent::DB_APPLICATION_ID)->info();
		$db_config = $info['config'];
		$db_application_id = parent::DB_APPLICATION_ID;
		$table_session_class = parent::TABLE_SESSION;
		destruct(__METHOD__.':'.$_SESSION['session_id'], true, array($db_config, $db_application_id, $table_session_class), function($db_config, $db_application_id, $table_session_class){
			
			//登陆的用户 更新 user_log_out_time
			if( !empty($_SESSION['session_private']['user_log_id']) ){
				$session_expire_time = object($table_session_class)->get_expire_time();
				db($db_application_id, true, $db_config)
				->table('user_log')
				->where(array('user_log_id=[+]', $_SESSION['session_private']['user_log_id']))
				->update(array('user_log_out_time' => $session_expire_time));
			}
			
		});
	}
	
	
	
	
	
	
	
	/**
	 * 根据用户id插入日志
	 * 
	 * '数据' = array(
	 * 	'data' => array(//登陆时的账号数据
	 * 		'用户电邮表' => array('账号'=>'','xx'=>'xx',...),
	 * 		或者
	 * 		'用户授权表' => array('编号'=>'','xx'=>'xx',...),
	 * 		)
	 * );
	 * 
	 * $data = array(
	 * 		'user_id' 		=> 用户编号
	 * 		'method'		=> 登录方式
	 * 		'table_data'	=> 表数据
	 * 		'session'		=> 会话数据
	 * 		'logout_time'	=> 注销时间
	 * );
	 * 
	 * 
	 * @param	array		$data	参数
	 * @return	bool|string			成功返回记录编号，否则返回false	
	 */
	public function insert($data = array()){
		//获取用户编号
		if( empty($data['user_id']) && !empty($_SESSION['user_id']) ) $data['user_id'] = $_SESSION['user_id'];
		//获取登录方式
		if( empty($data['method']) ) $data['method'] = '';
		//账号数据
		if( empty($data['table_data']) ) $data['table_data'] = array();
		//会话数据
		if( empty($data['session']) ) $data['session'] = object(parent::TABLE_SESSION)->get_legal_data();//获取合法的会话数据
		//注销时间
		if( empty($data['logout_time']) && !empty($_SESSION['session_expire_time']) ) $data['logout_time'] = $_SESSION['session_expire_time'];
		
		//判断合法性
		if( empty($data['user_id']) || empty($data['session']['session_id']) ){
			return false;
		}
		
		//可能存在相同的会话没有退出，那么将其修改退出时间
		$this->update_log_out(time(), $data['user_id'], $data['session']['session_id']);
		
		//将会话数据添加上
		$data['table_data']['session'] = $data['session'];
		
		//开始插入数据
		$insert_data = array(
			'user_log_id' 			=> $this->get_unique_id(),
			'user_id' 				=> $data['user_id'],
			'session_id' 			=> $data['session']['session_id'],
			'user_log_ip' 			=> HTTP_IP,
			'user_log_method'		=> $data['method'],
			//数据里面不包括私密数据
			'user_log_json' 		=> cmd(array( $data['table_data'] ), 'json encode'),
			'user_log_in_time' 		=> time(),
			'user_log_out_time' 	=> $data['logout_time']
		);
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('user_log')
		->insert($insert_data);
		
		if( empty($bool) ){
			return false;
		}else{
			return $insert_data['user_log_id'];
		}
		
	}
	
	
	
	
	/**
	 * 根据唯一标识，删除数据
	 * 
	 * @param	string	$user_log_id
	 * @return	array
	 */
	public function remove($user_log_id = ''){
		if( empty($user_log_id) ){
			return false;
		}
		return (bool)db(parent::DB_APPLICATION_ID)
		->table('user_log')
		->where(array('user_log_id=[+]', (string)$user_log_id))
		->delete();
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
		return (bool)db(parent::DB_APPLICATION_ID)
		->table('user_log')
		->call('where', $where)
		->call('data', $call_data)
		->update($data);
	}		
	
	
	
	
		
	/**
	 * 根据用户记录，更新退出时间戳  
	 * 
	 * @param	string		$user_log_id	用户记录id
	 * @return	bool
	 */
	public function update_log_id_out($user_log_id = '', $out_time = 0){
		if( empty($user_log_id) ){
			return false;
		}
		if( empty($out_time) ) $out_time = time();
		
		return (bool)db(parent::DB_APPLICATION_ID)
		->table('user_log')
		->where(array('user_log_id=[+]', (string)$user_log_id))
		->update(array('user_log_out_time' => $out_time));
	}
	
	
	
	
	
		
	/**
	 * 更新退出时间戳  
	 * 
	 * @param	int			$out_time			更新的时间戳
	 * @param	string		$user_id			用户id
	 * @param	string		$session_id			会话id
	 * @return	bool
	 */
	public function update_log_out($out_time = 0, $user_id = '', $session_id = ''){
		if( empty($user_id) && !empty($_SESSION['user_id']) ) $user_id = $_SESSION['user_id'];
		if( empty($session_id) && !empty($_SESSION['session_id']) ) $session_id = $_SESSION['session_id'];
		if( empty($out_time) ) $out_time = time();
		
		$user_id = (string)$user_id;
		$session_id = (string)$session_id;
		$out_time = (integer)$out_time;
		
		if( $user_id == '' || $session_id == '' || $out_time == 0 ){
			return false;
		}
		
		return (bool)db(parent::DB_APPLICATION_ID)
		->table('user_log')
		->where(
			array('user_id=[]', $user_id), 
			array('[and] session_id=[]', $session_id),
			//当前时间必须小于退出时间
			array('[and] user_log_out_time>[]', time())
		)
		//有可能是用户id和会话id相同的。因为在cookie中会话id保存了很久
		->orderby(array('user_log_in_time', true)) //所以desc排序，只更新最新的那一条日志数据
		->limit(1)
		->update(array('user_log_out_time' => $out_time));
	}
	
	
	
	
		
	/**
	 * 根据用户的id、会话的id 获取最新的日志数据
	 * 
	 * @param	string	$user_id			用户id
	 * @param	string	$session_id			会话id
	 * @return	array
	 */
	public function find_now_data($user_id = NULL, $session_id = NULL){
		if($user_id === NULL && !empty($_SESSION['user_id'])) $user_id = $_SESSION['user_id'];
		if($session_id === NULL && !empty($_SESSION['session_id'])) $session_id = $_SESSION['session_id'];
		
		$user_id = (string)$user_id;
		$session_id = (string)$session_id;
		if( $user_id == '' || $session_id == '' ){
			return false;
		}
		
		//用户表
		$user = array(
		    'table' => 'user as u',
		    'type' => 'LEFT',
		    'on' => 'u.user_id = ul.user_id'
		);
		
		return db(parent::DB_APPLICATION_ID)
		->table('user_log ul')
		->joinon($user)
		->where(
			array('ul.user_id=[]', $user_id), 
			array('[and] ul.session_id=[]', $session_id),
			//当前时间必须小于退出时间
			array('[and] ul.user_log_out_time>[]', time())
		)
		//有可能是用户id和会话id相同的。因为在cookie中会话id保存了很久
		->orderby(array('ul.user_log_in_time', true)) //所以desc排序，只更新最新的那一条日志数据
		->find(
		array("ul.user_log_id as user_log_id"), 
		array("ul.session_id as session_id"), 
		array("ul.user_log_method as user_log_method"), 
		array("ul.user_log_in_time as user_log_in_time"), 
		array("ul.user_log_out_time as user_log_out_time"),
		array("u.*")
		);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>