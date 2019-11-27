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
class agent extends main {

    //权限码
    const AUTHORITY_AGENT_READ = "agent_read";//读取权限
    const AUTHORITY_AGENT_EDIT = "agent_edit";//编辑权限
    const AUTHORITY_AGENT_REMOVE = "agent_user_remove";//删除权限
    const AUTHORITY_AGENT_STATE = "agent_state";//审核
	
	
	//代理用户
	const AUTHORITY_USER_READ = "agent_user_read";//读取权限
	const AUTHORITY_USER_ADD = "agent_user_add";//代理用户添加
	const AUTHORITY_USER_REMOVE = "agent_user_remove";//代理用户删除
	const AUTHORITY_USER_EDIT = "agent_user_edit";//代理用户编辑、审核
	const AUTHORITY_ADMIN_USER_ADD_SOCKET = "admin_user_add_socket";//更新创始人库存
	
	//代理地区
	const AUTHORITY_REGION_READ = "agent_region_read";//读取权限
	const AUTHORITY_REGION_ADD = "agent_region_add";//代理地区添加
	const AUTHORITY_REGION_REMOVE = "agent_region_remove";//代理地区删除
	const AUTHORITY_REGION_EDIT = "agent_region_edit";//代理地区编辑、审核


	
	
     
}