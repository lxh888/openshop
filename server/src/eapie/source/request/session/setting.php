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
class setting extends \eapie\source\request\session {
	
	
	
	/**
	 * 自毁、删除 session 
	 * 
	 * @return	bool
	 */
	public function api_kill($http_referer = ''){
		//检查是否已初始化
		object(parent::REQUEST_SESSION)->check();
		
		$bool = object(parent::TABLE_SESSION)->delete_destruct();
		return $bool;
	}
	
	
		
	/**
	 * 设置上一步页面的地址
	 * 
	 * @param	string	$http_referer
	 * @return	bool
	 */
	public function api_http_referer($http_referer = ''){
		//检查是否已初始化
		object(parent::REQUEST_SESSION)->check();
		
		$_SESSION['session_public']['http_referer'] = $http_referer;
		
		return true;
	}
	
	
	
	
	
	
	
	
	
	
	
}
?>