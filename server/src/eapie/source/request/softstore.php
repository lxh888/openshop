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
class softstore extends main {
	
	
	/*软件商城*/
	
	
	
	/**
	 * 分类权限码
	 */
	const AUTHORITY_TYPE_READ = "softstore_type_read";//读取产品分类列表的权限
	const AUTHORITY_TYPE_ADD = "softstore_type_add";//产品分类添加权限
	const AUTHORITY_TYPE_REMOVE = "softstore_type_remove";//产品分类删除权限
	const AUTHORITY_TYPE_EDIT = "softstore_type_edit";//产品分类编辑权限
	
	
	
	
	/**
	 * 产品权限码
	 */
	const AUTHORITY_PRODUCT_READ 			= "softstore_product_read";
	const AUTHORITY_PRODUCT_ADD 			= "softstore_product_add";
	const AUTHORITY_PRODUCT_EDIT 			= "softstore_product_edit";
	const AUTHORITY_PRODUCT_TRASH 			= "softstore_product_trash";//逻辑删除，丢进回收站
	const AUTHORITY_PRODUCT_TRASH_READ 		= "softstore_product_trash_read";//回收站读取权限
	const AUTHORITY_PRODUCT_REMOVE 			= "softstore_product_remove";//物理删除
	
	
	const AUTHORITY_PRODUCT_ATTRIBUTE_ADD 		= "softstore_product_attr_add";//产品规格属性添加权限
	const AUTHORITY_PRODUCT_ATTRIBUTE_EDIT 		= "softstore_product_attr_edit";//产品规格属性编辑权限
	const AUTHORITY_PRODUCT_ATTRIBUTE_REMOVE 	= "softstore_product_attr_remove";//产品规格属性删除权限
	
	
	const AUTHORITY_PRODUCT_TYPE_EDIT 		= "softstore_product_type_edit";//产品分类编辑权限
	const AUTHORITY_PRODUCT_IMAGE_UPLOAD 	= "softstore_product_image_upload";//产品图片上传权限
	const AUTHORITY_PRODUCT_IMAGE_REMOVE 	= "softstore_product_image_remove";//产品图片删除权限
	const AUTHORITY_PRODUCT_IMAGE_EDIT 		= "softstore_product_image_edit";//产品图片编辑权限
	
	const AUTHORITY_PRODUCT_FILE_UPLOAD 	= "softstore_product_file_upload";//产品文件上传权限
	const AUTHORITY_PRODUCT_FILE_REMOVE 	= "softstore_product_file_remove";//产品文件删除权限
	const AUTHORITY_PRODUCT_FILE_EDIT 		= "softstore_product_file_edit";//产品文件编辑权限
	
	
	
	
	
		
	const AUTHORITY_ORDER_READ 				= "softstore_order_read";//读取订单
	const AUTHORITY_ORDER_DETAIL_READ 		= "softstore_order_detail_read";//读取订单详细
	const AUTHORITY_ORDER_TRASH_READ 		= "softstore_order_trash_read";//读取回收订单
	const AUTHORITY_ORDER_TRASH 			= "softstore_order_trash";//逻辑删除，丢进回收站
	const AUTHORITY_ORDER_CONTACT 			= "softstore_order_contact";//订单联系操作
	
	
	
	
	
	
	
	
	
	
	
}
?>