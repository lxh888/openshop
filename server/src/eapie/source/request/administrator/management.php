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
class management extends \eapie\source\request\administrator {
	
	
	/*管理栏目*/
	
	
					
	/**
	 * 获取管理栏目选项列表
	 * 根据当前登录用户的权限ID、或者管理栏目的权限的ID为空
	 * 
	 * $data = array(
	 * 	"level" => "son" 只获取子级，为空获取父级
	 * 	'sort' => ["sort_asc", "name_desc"] 排序
	 * );
	 * 
	 * ADMINISTRATORMANAGEMENTSELF
	 * {"class":"administrator/management","method":"api_self"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_self($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check();
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => array(),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'name_desc' => array('management_name', true),
			'name_asc' => array('management_name', false),
			
			'page_desc' => array('management_page', true),
			'page_asc' => array('management_page', false),
			
			'insert_time_desc' => array('management_insert_time', true),
			'insert_time_asc' => array('management_insert_time', false),
			'update_time_desc' => array('management_update_time', true),
			'update_time_asc' => array('management_update_time', false),
			
			'state_desc' => array('management_state', true),
			'state_asc' => array('management_state', false),
			
			'hide_desc' => array('management_hide', true),
			'hide_asc' => array('management_hide', false),
			
			'sort_desc' => array('management_sort', true),
			'sort_asc' => array('management_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('management_id', false);
		
		//根据当前用户的权限ID、或者管理栏目的权限的ID为空
		if( !empty($_SESSION['admin']['authority_id']) ){
			$in_string = "\"".implode("\",\"", $_SESSION['admin']['authority_id'])."\",\"\"";//还要加上空格
		}else{
			$in_string = "\"\"";//可以为空
		}
		
		$config["where"][] = array('[and] authority_id IN([-])', $in_string, true);
		$config["where"][] = array('[and] management_state=1');
		
		$parent_config = $config;
		$son_config = $config;
		
		if( !empty($data["level"]) && $data["level"] == "son" ){
			$parent_config["where"][] = array('[and] management_parent_id<>""');
		}else{
			$parent_config["where"][] = array("[and] management_parent_id=\"\"");
		}
		
		return object(parent::TABLE_MANAGEMENT)->select_parent_son_all($parent_config, $son_config);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>