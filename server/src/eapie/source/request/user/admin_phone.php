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
class admin_phone extends \eapie\source\request\user {
	
	
	
	
			
	/**
	 * 获取一条用户的认证手机列表数据
	 * $data = arrray(
	 * 	user_id 用户ID
	 * )
	 * 
	 * USERADMINPHONELIST
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_list($data = array()){
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_READ);
		object(parent::ERROR)->check($data, 'user_id', parent::TABLE_USER, array('args'));
		
		$config = array(
			"where" => array(),
			"orderby" => array(),
			"select" => array(),
		);
		
		$config["where"][] = array('user_id=[+]', (string)$data['user_id']);
		$config["where"][] = array('[and] user_phone_state=1');
		$config["orderby"][] = array("user_phone_sort", false);
		$config["orderby"][] = array("user_phone_update_time", false);
		$config["orderby"][] = array("user_phone_insert_time", false);
		$config["orderby"][] = array("user_phone_id", false);
		
		
		$config["select"] = array(
			"user_phone_id",
			"user_phone_type",
			"user_phone_state",
			"user_phone_sort",
			"user_phone_update_time",
			"user_phone_insert_time"
		);
		$user_phone_data = object(parent::TABLE_USER_PHONE)->select($config);
		
		return $user_phone_data;
	}
	
	
	
	
	
	/**
	 * 添加一个用户的认证手机号
	 * $data = array(
	 *	user_id 用户ID
	 *  user_phone_id 用户手机号
	 * )
	 * 
	 * 添加的时候，要判断手机号是否已经 认证存在。
	 * 但是已认证用户不存在的将会被删除。
	 * 
	 * USERADMINPHONEADD
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_add($data = array()){
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_PHONE_ADD);
		object(parent::ERROR)->check($data, 'user_id', parent::TABLE_USER, array('args', 'exists_id'));
		object(parent::ERROR)->check($data, 'user_phone_id', parent::TABLE_USER_PHONE, array('args', 'length'));
		object(parent::ERROR)->check($data, 'user_phone_type', parent::TABLE_USER_PHONE, array('args'));
		object(parent::ERROR)->check($data, 'user_phone_sort', parent::TABLE_USER_PHONE, array('args'));
		
		//获取旧数据
		$user_phone_data = object(parent::TABLE_USER_PHONE)->find($data['user_phone_id']);
		
		//判断手机是否已经存在用户并且已经认证
		if( !empty($user_phone_data["user_id"]) && !empty($user_phone_data["user_phone_state"]) ){
			throw new error("手机号已经被认证登记");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'user_phone_type', 
			'user_phone_sort', 
			);
		$filter_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		//判断数据是否存在，不存在则添加，存在则更新
		if( empty($user_phone_data) ){
			$filter_data['user_phone_id'] = $data['user_phone_id'];
			$filter_data['user_id'] = $data['user_id'];
			$filter_data['user_phone_state'] = 1;//管理员操作，自动认证
			$filter_data['user_phone_insert_time'] = time();
			$filter_data['user_phone_update_time'] = time();
			$bool = object(parent::TABLE_USER_PHONE)->insert($filter_data);
		}else{
			$update_where = array();
			$update_where[] = array("user_phone_id=[+]", $data["user_phone_id"]);
			$update_where[] = array('[and] user_phone_state=0');
			
			$filter_data['user_id'] = $data['user_id'];
			$filter_data['user_phone_state'] = 1;//管理员操作，自动认证
			$filter_data['user_phone_update_time'] = time();
			$bool = object(parent::TABLE_USER_PHONE)->update($update_where, $filter_data);
		}
		
		if( !empty($bool) ){
			//更新用户编辑时间
			object(parent::TABLE_USER)->update( array(array('user_id=[+]', (string)$data['user_id'])), array('user_update_time'=>time()));
			
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $filter_data);
			return $data['user_phone_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	/**
	 * 编辑一个用户的认证手机号
	 * $data = array(
	 *	user_id 用户ID
	 *  user_phone_id 用户手机号
	 * )
	 * 
	 * 添加的时候，要判断手机号是否已经 认证存在。
	 * 但是已认证用户不存在的将会被删除。
	 * 
	 * USERADMINPHONEEDIT
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_edit($data = array()){
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_PHONE_EDIT);
		object(parent::ERROR)->check($data, 'user_id', parent::TABLE_USER, array('args'));//编辑的时候就不需要判断用户ID是否存在了，因为有旧数据匹配
		object(parent::ERROR)->check($data, 'user_phone_id', parent::TABLE_USER_PHONE, array('args', 'length'));
		
		if( isset($data['user_phone_type']) )
		object(parent::ERROR)->check($data, 'user_phone_type', parent::TABLE_USER_PHONE, array('args'));
		if( isset($data['user_phone_sort']) )
		object(parent::ERROR)->check($data, 'user_phone_sort', parent::TABLE_USER_PHONE, array('args'));
		
		
		//获取旧数据
		$user_phone_data = object(parent::TABLE_USER_PHONE)->find($data['user_phone_id']);
		if( empty($user_phone_data) ){
			throw new error("手机号码有误，数据不存在");
		}
		if( $user_phone_data["user_id"] != $data["user_id"] ){
			throw new error("手机号码与用户ID不匹配");
		}
		if( empty($user_phone_data['user_phone_state']) ){
			throw new error("手机号码没有认证");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'user_phone_type', 
			'user_phone_sort',
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($update_data as $key => $value){
			if( isset($user_phone_data[$key]) ){
				if($user_phone_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		//更新时间
		$update_data['user_phone_update_time'] = time();
		$update_where = array();
		$update_where[] = array('user_phone_id=[+]', (string)$data['user_phone_id']);
		$update_where[] = array('[and] user_id=[+]', $data['user_id']);
		$update_where[] = array('[and] user_phone_state=1');//必须是已认证的手机号
		if( object(parent::TABLE_USER_PHONE)->update( $update_where, $update_data) ){
			//更新用户编辑时间
			object(parent::TABLE_USER)->update( array(array('user_id=[+]', (string)$data['user_id'])), array('user_update_time'=>time()));
			
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['user_phone_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	/**
	 * 删除一个用户的登录手机号 无论是登录手机还是联系手机，只要是绑定了该用户
	 * $data = arrray(
	 * 	user_id 用户ID
	 *  user_phone 用户手机号
	 * )
	 * 
	 * USERADMINPHONEREMOVE
	 * 
	 * @param	array	$data
	 * @return	string	返回被删除手机号
	 */
	public function api_remove($data = array()){
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_PHONE_REMOVE);
		object(parent::ERROR)->check($data, 'user_id', parent::TABLE_USER, array('args'));//删除的时候就不需要判断用户ID是否存在了，因为有旧数据匹配
		object(parent::ERROR)->check($data, 'user_phone_id', parent::TABLE_USER_PHONE, array('args', 'length'));
		
		//获取旧数据
		$user_phone_data = object(parent::TABLE_USER_PHONE)->find($data['user_phone_id']);
		if( empty($user_phone_data) ){
			throw new error("手机号码有误，数据不存在");
		}
		if( $user_phone_data["user_id"] != $data["user_id"] ){
			throw new error("手机号码与用户ID不匹配");
		}
		
		if( object(parent::TABLE_USER_PHONE)->remove($data['user_phone_id']) ){
			
			//更新用户编辑时间
			object(parent::TABLE_USER)->update( array(array('user_id=[+]', (string)$user_phone_data['user_id'])), array('user_update_time'=>time()));
			
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $data);
			return $data['user_phone_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	/**
	 * 检索手机验证码信息
	 * 
	 * $data = array(
	 * 		'user_phone_id'
	 * )
	 * 
	 * USERADMINPHONESEARCHVERIFYCODE
	 * {"class":"user/admin_phone","method":"api_search_verify_code"}
	 * 
	 * @param	array	$data
	 * @return	string	返回被删除手机号
	 */
	public function api_search_verify_code( $data = array() ){
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_PHONE_VERIFY_CODE);
		object(parent::ERROR)->check($data, 'user_phone_id', parent::TABLE_USER_PHONE, array('args', 'length'));
		//获取手机数据
		$user_phone_data = object(parent::TABLE_USER_PHONE)->find($data['user_phone_id']);
		if( empty($user_phone_data) ){
			throw new error("该手机号没有记录，检查手机号码是否输入正确");
		}
		
		//获得配置信息
		if( !empty($user_phone_data["user_phone_json"]) ){
			$user_phone_data["user_phone_json"] = cmd(array($user_phone_data["user_phone_json"]), "json decode");
		}else{
			$user_phone_data["user_phone_json"] = array();
		}
		
		if( empty($user_phone_data["user_phone_json"]["verify_code"]) || 
		!is_array($user_phone_data["user_phone_json"]["verify_code"]) ){
			$user_phone_data["user_phone_json"]["verify_code"] = array();
		}
		
		return $user_phone_data["user_phone_json"]["verify_code"];
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>