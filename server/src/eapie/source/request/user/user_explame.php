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
class user_explame extends \eapie\source\request\user {
	
	

	
	/**
	 * 测试
	 * 
	 * $data = array(
	 * 	'xxx' => 这是测试
	 *  'xxx2' => 这是测试2
	 * )
	 * 
	 * [模块名称][类名称][找方法名称，要去掉api_]
	 * USEREXAMPLESELFTEST
	 * 
	 * {"class":"user/user_example","method":"api_self_test"}
	 * 
	 * @param	array 	$data	
	 * @return array
	 */
	public function api_self_test( $data = array() ){
		 //检测登录
        object(parent::REQUEST_SESSION)->check();
		
		throw new error('这是测试错误');
		//return array('erron'=> 1);
		
		$data['test'] = "这是测试返回值";
		
		
		
		
		return $data;
		
		
	}
	
	
	
	
	
	
	
	
}
?>