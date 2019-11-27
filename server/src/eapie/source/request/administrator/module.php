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



namespace eapie\source\request\administrator;
use eapie\main;
use eapie\error;
class module extends \eapie\source\request\administrator {
	
	
	
	
		
	/**
	 * 获取模块选项列表
	 *  $data = array(
	 * 	'sort' => ["sort_asc", "name_desc"] 排序
	 * );
	 * 
	 * ADMINISTRATORMODULEOPTION
	 * {"class":"administrator/module","method":"api_option"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_option($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check();
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => array(),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'id_desc' => array('module_id', true),
			'id_asc' => array('module_id', false),
			'parent_id_desc' => array('module_parent_id', true),
			'parent_id_asc' => array('module_parent_id', false),
			
			'insert_time_desc' => array('module_insert_time', true),
			'insert_time_asc' => array('module_insert_time', false),
			'update_time_desc' => array('module_update_time', true),
			'update_time_asc' => array('module_update_time', false),
			
			'sort_desc' => array('module_sort', true),
			'sort_asc' => array('module_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('module_id', false);
		
		//前台的这里应该是需要判断状态的，第三个参数为true，表示不拿缓存的
		return object(parent::TABLE_MODULE)->select($config);
	}
	
	
	
	
	
				
	/**
	 * 获取一条数据
	 * $data = arrray(
	 * 	module_id 模块ID
	 * )
	 * 
	 * ADMINISTRATORMODULEGET
	 * {"class":"administrator/module","method":"api_get"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_get( $data = array() ){
		object(parent::REQUEST_ADMIN)->check();
		object(parent::ERROR)->check($data, 'module_id', parent::TABLE_MODULE, array('args'));
		
		$get_data = object(parent::TABLE_MODULE)->find($data['module_id']);
		if( empty($get_data) ){
			throw new error("模块不存在");
		}
		
		return $get_data;
	}
	
	
	
	
	
	
	
	
	
	
	
}
?>