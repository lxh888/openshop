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



namespace eapie\source\request\admin;
use eapie\main;
use eapie\error;
class admin_log extends \eapie\source\request\admin {
	
	
	
			
		
	/**
	 * 获取数据列表
	 * 
	 * ADMINLOGLIST
	 * {"class":"admin/admin_log","method":"api_list"}
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_LOG_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'user_id_desc' => array('user_id', true),
            'user_id_asc' => array('user_id', false),
            'user_nickname_desc' => array('user_nickname', true),
            'user_nickname_asc' => array('user_nickname', false),
			'user_phone_verify_list_desc' => array('user_phone_verify_list', true),
            'user_phone_verify_list_asc' => array('user_phone_verify_list', false),
            
			'api_id_desc' => array('api_id', true),
            'api_id_asc' => array('api_id', false),
            
            'ip_desc' => array('admin_log_ip', true),
            'ip_asc' => array('admin_log_ip', false),
			
			'time_desc' => array('admin_log_time', true),
			'time_asc' => array('admin_log_time', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('admin_log_id', false);
		
		if(!empty($data['search'])){
			if (isset($data['search']['user_id']) && is_string($data['search']['user_id'])) {
                $config['where'][] = array('[and] u.user_id=[+]', $data['search']['user_id']);
            }
			if (isset($data['search']['user_nickname']) && is_string($data['search']['user_nickname'])) {
                $config['where'][] = array('[and] u.user_nickname LIKE "%[-]%"', $data['search']['user_nickname']);
            }

			if (isset($data['search']['user_phone']) && is_string($data['search']['user_phone'])) {
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['user_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $config['where'][] = array('[and] u.user_id=[+]', $user_id);
            }
			
			if( isset($data['search']['admin_log_id']) && is_string($data['search']['admin_log_id']) ){
                $config['where'][] = array('[and] al.admin_log_id=[+]', $data['search']['admin_log_id']);
            }
			if( isset($data['search']['api_id']) && is_string($data['search']['api_id']) ){
                $config['where'][] = array('[and] al.api_id=[+]', $data['search']['api_id']);
            }
			if( isset($data['search']['admin_log_ip']) && is_string($data['search']['admin_log_ip']) ){
                $config['where'][] = array('[and] al.admin_log_ip=[+]', $data['search']['admin_log_ip']);
            }
			
		}
		
		return object(parent::TABLE_ADMIN_LOG)->select_page($config);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>