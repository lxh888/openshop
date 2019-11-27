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



namespace eapie\source\request\softstore;
use eapie\main;
use eapie\error;
class type extends \eapie\source\request\softstore {
	
	
	/*前台调用的分类*/
	
	
	
		
	/**
	 * 获取分类选项列表
	 * $data = array(
	 * 	"type" => "son" 只获取子级，为空获取父级
	 * 	'sort' => ["sort_asc", "name_desc"] 排序
	 * );
	 * 
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_option($data = array()){
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => array(),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'name_desc' => array('ss_type_name', true),
			'name_asc' => array('ss_type_name', false),
			'insert_time_desc' => array('ss_type_insert_time', true),
			'insert_time_asc' => array('ss_type_insert_time', false),
			'update_time_desc' => array('ss_type_update_time', true),
			'update_time_asc' => array('ss_type_update_time', false),
			'sort_desc' => array('ss_type_sort', true),
			'sort_asc' => array('ss_type_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('ss_type_id', false);
		
		
		$config["where"][] = array("[and] ss_type_state=1");
		
		$parent_config = $config;
		$son_config = $config;
		
		if( !empty($data["type"]) && $data["type"] == "son" ){
			$parent_config["where"][] = array('[and] ss_type_parent_id<>""');
		}else{
			$parent_config["where"][] = array("[and] ss_type_parent_id=\"\"");
		}
		
		//前台的这里应该是需要判断状态的，第三个参数为true，表示不拿缓存的
		return object(parent::TABLE_SOFTSTORE_TYPE)->select_parent_son_all($parent_config, $son_config);
		
	}
	
	
	
	
	
	
	
	
	
}
?>