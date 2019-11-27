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
class log extends main {
	
	/*系统操作日记表*/
	
	
	
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
		
		//获取应用ID
		$application = object(parent::MAIN)->api_application();
		if( empty($application['application_id']) ){
			return false;
		}
		
		$data = array(
			'log_id' => $this->get_unique_id(),
			'application_id' => $application['application_id'],
			'user_id' => $_SESSION['user_id'],
			'api_id' => object(parent::MAIN)->api['api_id'],
			'log_time' => time(),
			'session_id' => $_SESSION['session_id'],
			'log_ip' => HTTP_IP,
			'log_session' => cmd(array($_SESSION), 'json encode')
		);
		
		if( !empty($real_args) ){
			$data['log_real_args'] = cmd(array($real_args), 'json encode');
		}
		if( !empty($clear_args) ){
			$data['log_clear_args'] = cmd(array($clear_args), 'json encode');
		}
		
		return (bool)db(parent::DB_SYSTEM_ID)
		->table('log')
		->insert($data);
	}
	
		
	
	
	
}
?>