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



namespace eapie\source\request;
use eapie\main;
use eapie\error;
class session extends main {
	
	
	
	
	/**
	 * 子方法的命名空间
	 * 
	 * @var	string
	 */
	private $_action_namespace = 'eapie\source\request\session\\';
	
	
	
			
	/**
	 * 检查是否已初始化
	 * 
	 * @param	bool	$return_bool	是否返回布尔值
	 * @return	mixed
	 */
	public function check($return_bool = false){
		if(!isset($_SESSION['session_id'])){
			if( empty($return_bool) ){
				throw new error("会话未初始化");
			}else{
				return false;
			}
		}
		
		return true;
	}
	
	
	
	
	/**
	 * 检测图片验证码
	 * 
	 * @return	mixed
	 */
	public function image_verify_code_check(){
		return call_user_func_array(array(object($this->_action_namespace.'image_verify_code'), '_check_'), func_get_args());
	}	
	
	
	
	/**
	 * 检测手机验证码
	 * 
	 * SESSIONPHONEVERIFYCODECHECK
	 * [{"phone":"必须|检测手机验证码","phone_verify_key":"必须|手机验证码键名称，如注册sign_up、重置密码reset_password","phone_verify_code":"必须|手机验证码"}]
	 * 
	 * @return	mixed
	 */
	public function phone_verify_code_check(){
		return call_user_func_array(array(object($this->_action_namespace.'phone_verify_code'), '_check_'), func_get_args());
	}	
	
	
	
	
	
	
	
	
	
	
	
}
?>