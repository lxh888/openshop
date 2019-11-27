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



namespace eapie\source\request\application;
use eapie\main;
use eapie\error;
class admin_cache extends \eapie\source\request\application {
	
	
	
	
	/**
	 * 缓存清理
	 * $data 可以是一个索引数组，是多个表名称。如果为空，那么清理全部缓存
	 * APPLICATIONADMINCACHECLEAR
	 * 
	 * {"class":"application/admin_cache","method":"api_clear"}
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_clear( $data = array() ){
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_CACHE_CLEAR);
		
		//获取当前应用的缓存列表
		if( empty($data) ){
			$table_list = object(parent::TABLE_APPLICATION)->get_table_list();
			$bool = object(parent::CACHE)->clear($table_list);
		}else
		if( is_array($data) || is_string($data) ){
			$bool = object(parent::CACHE)->clear($data);
		}else{
			return false;
		}
		
		//插入操作日志
		object(parent::TABLE_ADMIN_LOG)->insert($data, $data);
		return $bool;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>