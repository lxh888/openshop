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



namespace eapie\source\request\session;
use eapie\main;
use eapie\error;
class websocket extends \eapie\source\request\session {
	
	
	/**
	 * 链接ID 保存的最大值
	 * 
	 * @var	int
	 */
	private $_client_id_maximum = 50;
	

	
    /**
     * 获取websocket_token
     * 
	 * SESSIONWEBSOCKETTOKEN
	 * {"class":"session/websocket","method":"api_token"}
	 * 
	 * @param	void
	 * @return	string
     */
    public function api_token (){
        //检查是否已初始化
        object(parent::REQUEST_SESSION)->check();
        // 判断是否存在
        if( empty($_SESSION['session_websocket_token']) ){
            // 获取长连接会话
            $_SESSION['session_websocket_token'] = 'websocket'.object(parent::TABLE_SESSION)->get_token_id($_SESSION['session_id']);
        }
        // printexit($_SESSION['session_websocket_token']);
        return $_SESSION['session_websocket_token'];
    }

	
	
	
	/**
	 * 登录用户设置自己的 websocket 连接信息
	 * $data = array(
	 * 	'client_id' => '用户的链接ID'，
	 *  'server_time' => 'websocket 服务器时间'
	 * )
	 * 
	 * SESSIONWEBSOCKETSELFCLIENT
	 * {"class":"session/websocket","method":"api_self_client"}
	 * 
	 */
	public function api_self_client( $data = array() ){
		//检查是否已初始化
        object(parent::REQUEST_SESSION)->check();
		object(parent::ERROR)->check($data, 'client_id', parent::TABLE_SESSION, array('args'), 'websocket_client_id');
		object(parent::ERROR)->check($data, 'server_time', parent::TABLE_SESSION, array('args'), 'websocket_server_time');
		
		if( empty($_SESSION['session_private']['websocket']) || !is_array($_SESSION['session_private']['websocket']) ){
			$_SESSION['session_private']['websocket'] = array();
		}
		
		//循环获取 并 清理
		if( !empty($_SESSION['session_private']['websocket']) ){
			$temp_websocket = array();
			foreach($_SESSION['session_private']['websocket'] as $websocket){
				//时间存在，并且与服务器时间相等，并且唯一
				if( !empty($websocket['server_time']) && 
				$websocket['server_time'] == $data['server_time'] &&
				!in_array($websocket['client_id'], $temp_websocket) && 
				$websocket['client_id'] != $data['client_id'] ){
					$temp_websocket[] = $websocket;
				}
			}
			
			$_SESSION['session_private']['websocket'] = $temp_websocket;
		}
		
		
		$_SESSION['session_private']['websocket'][] = array(
			'client_id' => $data['client_id'],
			'server_time' => $data['server_time'],
		);
		
		//最多只能保存 限制
		$websocket_count = count($_SESSION['session_private']['websocket']);
		if( $websocket_count > $this->_client_id_maximum ){
			$shift_count = $websocket_count - $this->_client_id_maximum;
			while( $shift_count > 0 ){
				array_shift($_SESSION['session_private']['websocket']);
				$shift_count --;
			}
		}
		
		/*$_SESSION['session_private']['websocket']['client_id'] = $data['client_id'];
		$_SESSION['session_private']['websocket']['server_time'] = $data['server_time'];*/
		return true;
	}
	


	/**
	 * 初始化 websocket 必须数据
	 * 
	 * $data = array(
	 * 	'client_id' => websocket_client_id
	 *  'token' => session_websocket_token
	 * );
	 * 
	 * SESSIONWEBSOCKETINIT
	 * {"class":"session/websocket","method":"api_init"}
	 * 
	 * @return  array	$data
	 */
/*	public function api_init( $data = array() ){
		$session = $this->_check($data);
		$application_id = $session['application_id'];
		
		if( !empty($session['session_private']) ){
			$session['session_private'] = unserialize($session['session_private']);
		}
		
		if( empty($session['session_private']['websocket']) || !is_array($session['session_private']['websocket']) ){
			$session['session_private']['websocket'] = array();
		}
		
		if( isset($session['session_private']['websocket']['client_id']) &&
		$session['session_private']['websocket']['client_id'] == $data['client_id'] ){
			$bool = true;
		}else{
			$session['session_private']['websocket']['client_id'] = $data['client_id'];
			$bool = object(parent::TABLE_SESSION)->update(
				array(
					array('session_id=[+]', $session['session_id'])
				), 
				array(
					'session_private' => serialize($session['session_private'])
			));
		}
		
		if( empty($bool) ){
			throw new error('初始化失败！');
		}else{
			return true;
		}
				
	}*/




	/**
     * 获取 用户的 client_id
     * 可能为多个
	 * 
	 * SESSIONWEBSOCKETUSERCLIENTID
	 * {"class":"session/websocket","method":"api_user_client_id"}
	 * 
	 * @param	void
	 * @return	string
     */
    public function api_user_client_id( $data = array() ){
    	object(parent::ERROR)->check($data, 'client_id', parent::TABLE_SESSION, 	array('args'), 'websocket_client_id');
		object(parent::ERROR)->check($data, 'token', parent::TABLE_SESSION, array('args'), 'session_websocket_token');
		object(parent::ERROR)->check($data, 'server_time', parent::TABLE_SESSION, array('args'), 'websocket_server_time');
		
		//根据websocket_token获取会话数据
        $session = object(parent::TABLE_SESSION)->find_websocket_token($data['token']);
        if( empty($session['session_id']) ) 								throw new error('websocket_token异常，会话信息不存在！');
		
		//判断应用ID是否合法
		$application = object(parent::MAIN)->api_application();
		if( empty($application['application_id']) )  						throw new error('当前应用ID异常！');
		if( $session['application_id'] != $application['application_id']) 	throw new error('会话与当前应用ID不匹配！');
		
		
		//会话合法，那么拿到用户ID，进行查询
		object(parent::ERROR)->check($data, 'user_id', parent::TABLE_USER, array('args', 'exists_id'));
		//在会话中搜索 用户连接ID
		$select_session_data = object(parent::TABLE_SESSION)->select_application_user($session['application_id'], $data['user_id']);
		$client_id = array();
		if( !empty($select_session_data) ){
			foreach($select_session_data as $session){
				if( !empty($session['session_private']) ){
					$session['session_private'] = unserialize($session['session_private']);
				}
				
				if( !empty($session['session_private']['websocket']) && is_array($session['session_private']['websocket']) ){
					foreach($session['session_private']['websocket'] as $websocket){
						//判断服务器时间，失效的则不算  | 并且唯一存在连接中
						if( !empty($websocket['server_time']) && 
						$websocket['server_time'] == $data['server_time'] && 
						!in_array($websocket['client_id'], $client_id) ){
							$client_id[] = $websocket['client_id'];
						}
					}
					
				}
			}
		}
		
		return $client_id;
    }



	
	/**
     * 获取 管理员的 client_id
     * 可能为多个
	 * 
	 * SESSIONWEBSOCKETADMINCLIENTID
	 * {"class":"session/websocket","method":"api_admin_client_id"}
	 * 
	 * @param	void
	 * @return	string
     */
    public function api_admin_client_id( $data = array() ){
    	object(parent::ERROR)->check($data, 'client_id', parent::TABLE_SESSION, 	array('args'), 'websocket_client_id');
		object(parent::ERROR)->check($data, 'token', parent::TABLE_SESSION, array('args'), 'session_websocket_token');
		object(parent::ERROR)->check($data, 'server_time', parent::TABLE_SESSION, array('args'), 'websocket_server_time');
		
		//根据websocket_token获取会话数据
        $session = object(parent::TABLE_SESSION)->find_websocket_token($data['token']);
        if( empty($session['session_id']) ) 								throw new error('websocket_token异常，会话信息不存在！');
		
		//判断应用ID是否合法
		$application = object(parent::MAIN)->api_application();
		if( empty($application['application_id']) )  						throw new error('当前应用ID异常！');
		if( $session['application_id'] != $application['application_id']) 	throw new error('会话与当前应用ID不匹配！');
		
		
		//会话合法，那么拿到用户ID，进行查询
		$admin_user_data = object(parent::TABLE_ADMIN_USER)->select_user_id();
		$client_id = array();
		$user_ids = array();
		if( empty($admin_user_data) ){
			return $client_id;
		}
		
		foreach($admin_user_data as $user){
			$user_ids[] = $user['user_id'];
		}
		
		//在会话中搜索 用户连接ID
		$select_session_data = object(parent::TABLE_SESSION)->select_application_user($session['application_id'], $user_ids);
		if( empty($select_session_data) ){
			return $client_id;
		}
		
		foreach($select_session_data as $session){
			if( !empty($session['session_private']) ){
				$session['session_private'] = unserialize($session['session_private']);
			}
			
			if( !empty($session['session_private']['websocket']) && is_array($session['session_private']['websocket']) ){
				foreach($session['session_private']['websocket'] as $websocket){
					//判断服务器时间，失效的则不算  | 并且唯一存在连接中
					if( !empty($websocket['server_time']) && 
					$websocket['server_time'] == $data['server_time'] && 
					!in_array($websocket['client_id'], $client_id) ){
						$client_id[] = $websocket['client_id'];
					}
				}
				
			}
		}
		return $client_id;
    }

	
	



    
	
}
?>