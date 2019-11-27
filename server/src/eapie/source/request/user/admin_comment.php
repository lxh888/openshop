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



namespace eapie\source\request\user;
use eapie\main;
use eapie\error;
class admin_comment extends \eapie\source\request\user {
	
	
	
	/*用户评论管理*/
	
	
	
	/**
	 * 获取模块选项列表
	 * 
	 * USERADMINCOMMENTMODULEOPTION
	 * {"class":"user/admin_comment","method":"api_module_option"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_module_option($data = array()){
		object(parent::REQUEST_ADMIN)->check();
		return object(parent::TABLE_USER_COMMENT)->get_module();
	}
	
	
	
	
			
	/**
	 * 获取数据列表
	 * 
	 * USERADMINCOMMENTLIST
	 * {"class":"user/admin_comment","method":"api_list"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_COMMENT_READ);
		
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
            
			'id_desc' => array('user_comment_id', true),
			'id_asc' =>  array('user_comment_id', false),
			'root_id_desc' => array('user_comment_root_id', true),
			'root_id_asc' =>  array('user_comment_root_id', false),
			'parent_id_desc' => array('user_comment_parent_id', true),
			'parent_id_asc' =>  array('user_comment_parent_id', false),
			
			'state_desc' => array('user_comment_state', true),
			'state_asc' =>  array('user_comment_state', false),
			'insert_time_desc' => array('user_comment_insert_time', true),
			'insert_time_asc' => array('user_comment_insert_time', false),
			'update_time_desc' => array('user_comment_update_time', true),
			'update_time_asc' => array('user_comment_update_time', false),
			
			'ip_desc' => array('user_comment_ip', true),
			'ip_asc' => array('user_comment_ip', false),
			
			'key_desc' => array('user_comment_key', true),
			'key_asc' => array('user_comment_key', false),
			
			'module_desc' => array('user_comment_module', true),
			'module_asc' => array('user_comment_module', false),
		));
		
		
		//避免排序重复
		$config["orderby"][] = array('user_comment_id', false);
		
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
			
			if( isset($data['search']['user_comment_id']) && is_string($data['search']['user_comment_id']) ){
				$config["where"][] = array('[and] uc.user_comment_id=[+]', $data['search']['user_comment_id']);
			}
			if (isset($data['search']['user_comment_value']) && is_string($data['search']['user_comment_value'])) {
                $config['where'][] = array('[and] uc.user_comment_value LIKE "%[-]%"', $data['search']['user_comment_value']);
            }
			if (isset($data['search']['user_comment_key']) && is_string($data['search']['user_comment_key'])) {
                $config['where'][] = array('[and] uc.user_comment_key=[+]', $data['search']['user_comment_key']);
            }
			if( isset($data['search']['type_module']) && is_string($data['search']['type_module']) ){
				$config["where"][] = array('[and] uc.type_module=[+]', $data['search']['type_module']);
			}
			
			if( isset($data['search']['user_comment_root_id']) && is_string($data['search']['user_comment_root_id']) ){
				$config["where"][] = array('[and] uc.user_comment_root_id=[+]', $data['search']['user_comment_root_id']);
			}
			if( isset($data['search']['user_comment_parent_id']) && is_string($data['search']['user_comment_parent_id']) ){
				$config["where"][] = array('[and] uc.user_comment_parent_id=[+]', $data['search']['user_comment_parent_id']);
			}
			
			
			//订单状态
			if( isset($data['search']['state']) && 
			(is_string($data['search']['state']) || is_numeric($data['search']['state'])) &&
			in_array($data['search']['state'], array("0", "1", "2")) ){
				$config["where"][] = array('[and] uc.user_comment_state=[+]', $data['search']['state']);
				}
			
		}
		
		
		return object(parent::TABLE_USER_COMMENT)->select_page($config);
	}
	
	
	
	
			
	/**
	 * 编辑
	 * 
	 * USERADMINCOMMENTEDIT
	 * {"class":"user/admin_comment","method":"api_edit"}
	 * 
	 * @param	array		$data
	 * @return	string
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_COMMENT_EDIT);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'user_comment_id', parent::TABLE_USER_COMMENT, array('args'));
		object(parent::ERROR)->check($data, 'user_comment_state', parent::TABLE_USER_COMMENT, array('args'));
		
		//获取旧数据
		$type_data = object(parent::TABLE_USER_COMMENT)->find($data['user_comment_id']);
		if( empty($type_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'user_comment_state', 
		);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($update_data as $key => $value){
			if( isset($type_data[$key]) ){
				if($type_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		
		//更新时间
		$update_data['user_comment_update_time'] = time();
		if( object(parent::TABLE_USER_COMMENT)->update( array(array('user_comment_id=[+]', (string)$data['user_comment_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['user_comment_id'];
		}else{
			throw new error("操作失败");
		}
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>