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



namespace eapie\engine;
class init extends \eapie\source\initialize\transaction_type {
	
	/*初始化参数*/
	
	//用户数据库连接标识
	const	DB_SYSTEM_ID			=	'db_system_connect_id';//中央数据库连接标识
	const	DB_APPLICATION_ID		=	'db_application_connect_id';//远程应用数据库连接标识
	
	
	const	MAIN					=	'eapie\main';
	const	ERROR					=	'eapie\error';
	const	REQUEST					= 	'eapie\engine\request';
	const	REDIS					=	'eapie\engine\redis';
	const	CACHE					=	'eapie\engine\cache';
	const	SERVER					=	'eapie\engine\server';
	
	//数据表的常量命名：TABLE_表名称
	const	TABLE_API				=	'eapie\engine\table\api';
	const	TABLE_API_VERSION		=	'eapie\engine\table\api_version';
	const 	TABLE_MODULE			=	'eapie\engine\table\module';
	const 	TABLE_APPLICATION		=	'eapie\engine\table\application';
	const 	TABLE_MANAGEMENT		=	'eapie\engine\table\management';
	const 	TABLE_AUTHORITY			=	'eapie\engine\table\authority';
	const	TABLE_ERROR				=	'eapie\engine\table\error';
	const	TABLE_SESSION			=	'eapie\engine\table\session';
	const	TABLE_LOG				=	'eapie\engine\table\log';
	const	TABLE_APPLICATION_API	=	'eapie\engine\table\application_api';
	
	
	
}
?>