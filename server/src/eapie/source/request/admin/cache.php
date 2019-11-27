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
class cache extends \eapie\source\request\admin {
	
	
	
	
	
	/**
	 * 清理缓存
	 * 如果是清理多个项目，则前台请求该接口 填写多次
	 * 如果全部清理，则参数为空
	 * 
	 * @param	string	$project_name 项目名称
	 * @return 	bool
	 */
	public function api_clear($project_name = ""){
		//检查权限等
		//object(parent::REQUEST_ADMIN)->check(self::AUTHORITY_CLEAR);
		
		$redis_keys = array();
		if( !empty($project_name) ){
			//获得项目。替换并清理符号，将\斜杆，替换成/ 并且清理前后的斜杠
			$namespace_project_name = parent::REQUEST.'\\'.trim(str_replace('/', '\\', $project_name), '\\');
			if( !class_exists($namespace_project_name) ){
				throw new error("清理失败，项目名称不合法");
				}
			$redis_keys = object(parent::REDIS)->get_redis_key($namespace_project_name);
			if( empty($redis_keys) ){
				throw new error("该项目没有缓存");
			}
			
		}
		
		//清理 redis 缓存
		object(parent::REDIS)->delete($redis_keys);
		
		return true;
	}
	
	
	
	
	
}
?>