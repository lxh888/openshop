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
class administrator extends main {
	
	
	const AUTHORITY_APPLICATION_ADD = "administrator_application_add";//添加权限
	const AUTHORITY_APPLICATION_READ = "administrator_application_read";//读取权限
	const AUTHORITY_APPLICATION_EDIT = "administrator_application_edit";//编辑权限
	const AUTHORITY_APPLICATION_REMOVE = "administrator_application_remove";//删除权限
	
	
	const AUTHORITY_MODULE_ADD = "administrator_module_add";//添加权限
	const AUTHORITY_MODULE_READ = "administrator_module_read";//读取权限
	const AUTHORITY_MODULE_EDIT = "administrator_module_edit";//编辑权限
	const AUTHORITY_MODULE_REMOVE = "administrator_module_remove";//删除权限
	
	/*管理员权限*/
	const AUTHORITY_AUTHORITY_ADD = "administrator_authority_add";//添加权限
	const AUTHORITY_AUTHORITY_READ = "administrator_authority_read";//读取权限
	const AUTHORITY_AUTHORITY_EDIT = "administrator_authority_edit";//编辑权限
	const AUTHORITY_AUTHORITY_REMOVE = "administrator_authority_remove";//删除权限
	
	
	const AUTHORITY_API_ADD = "administrator_api_add";//添加权限
	const AUTHORITY_API_READ = "administrator_api_read";//读取权限
	const AUTHORITY_API_EDIT = "administrator_api_edit";//编辑权限
	const AUTHORITY_API_REMOVE = "administrator_api_remove";//删除权限
	
	
	const AUTHORITY_MANAGEMENT_ADD = "administrator_management_add";//添加权限
	const AUTHORITY_MANAGEMENT_READ = "administrator_management_read";//读取权限
	const AUTHORITY_MANAGEMENT_EDIT = "administrator_management_edit";//编辑权限
	const AUTHORITY_MANAGEMENT_REMOVE = "administrator_management_remove";//删除权限
	
	
	const AUTHORITY_PROGRAM_ERROR_READ 		= "administrator_program_error_read";
	const AUTHORITY_PROGRAM_ERROR_REMOVE 	= "administrator_program_error_remove";
	
	
	const AUTHORITY_CACHE_READ			= 	"administrator_cache_read";//缓存信息读取
	const AUTHORITY_CACHE_CLEAR			= 	"administrator_cache_clear";//清理缓存权限
	
	
	const AUTHORITY_MARKDOWN_READ = "administrator_markdown_read";//开发文档查看权限
	
	
	
	
	
}
?>