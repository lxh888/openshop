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
class authority extends \eapie\source\request\administrator {
	
	
	
	
	/**
	 * 根据模块获取权限选项列表
	 *  $data = array(
	 * 	"module_id" => "" 模块ID如果为空，则获取所有的模块权限
	 * 	'sort' => ["sort_asc", "name_desc"] 排序
	 * );
	 * 
	 * 根据当前应用的模块来查询
	 * 
	 * ADMINISTRATORAUTHORITYMODULEOPTION
	 * {"class":"administrator/authority","method":"api_module_option"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_module_option($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check();
		
		//根据当前应用的模块来查询
		/*$application = object(parent::MAIN)->api_application();
		if( !empty($application['module_id']) ){
			$application_module_id = explode(',', trim($application['module_id'], ','));
			if( !empty($application_module_id) ){
				$application_module_in_string = "\"".implode("\",\"", $application_module_id)."\",\"\"";//还要加上空格
			}
		}else{
			return array();//没有模块，返回空
		}*/
		
		//根据当前应用的模块来查询
		$application = object(parent::MAIN)->api_application();
		if( !empty($application['authority_id']) ){
			$application_authority_id = explode(',', trim($application['authority_id'], ','));
			if( !empty($application_authority_id) ){
				$application_authority_in_string = "\"".implode("\",\"", $application_authority_id)."\",\"\"";//还要加上空格
			}
		}
		
		if( empty($application_authority_in_string) ){
			return array();//没有模块，返回空
		}
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => array(),
		);
		
		$module_config = $config;
		$module_config['select'] = array('m.*');
		
		if( isset($data['module_id']) ){
			object(parent::ERROR)->check($data, 'module_id', parent::TABLE_MODULE, array('args'));
			$module_config['where'][] = array('m.module_id=[+]', $data['module_id']);
		}else{
			$module_config["orderby"] = object(parent::REQUEST)->orderby($data, array(
				'insert_time_desc' => array('m.module_insert_time', true),
				'insert_time_asc' => array('m.module_insert_time', false),
				'update_time_desc' => array('m.module_update_time', true),
				'update_time_asc' => array('m.module_update_time', false),
				'sort_desc' => array('m.module_sort', true),
				'sort_asc' => array('m.module_sort', false),
			));
			
			//避免排序重复
			$module_config["orderby"][] = array('m.module_id', false);
		}

		$module_config['where'][] = array('[and] a.authority_id IN([-])', $application_authority_in_string, true);
		
		$module_data = object(parent::TABLE_MODULE)->select_join($module_config);
		if( empty($module_data) ){
			return array();
		}
		
		$authority_config = $config;
		$authority_config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'insert_time_desc' => array('authority_insert_time', true),
			'insert_time_asc' => array('authority_insert_time', false),
			'update_time_desc' => array('authority_update_time', true),
			'update_time_asc' => array('authority_update_time', false),
			'sort_desc' => array('authority_sort', true),
			'sort_asc' => array('authority_sort', false),
		));
		
		//避免排序重复
		$authority_config["orderby"][] = array('authority_id', false);
		
		$authority_config['where'][] = array('[and] authority_id IN([-])', $application_authority_in_string, true);
		$authority_data = object(parent::TABLE_AUTHORITY)->select($authority_config);
		if( empty($authority_data) ){
			return array();
		}
		
		
		foreach($module_data as $key => $value){
			if( empty($authority_data) ){
				break;
			}
			foreach($authority_data as $k => $v){
				if( $v['module_id'] == $value['module_id'] ){
					if( empty($module_data[$key]['authority']) ){
						$module_data[$key]['authority'] = array();
					}
					$module_data[$key]['authority'][] = $v;
					unset($authority_data[$k]);
				}
			}
			
			/*$temp_authority_config = array();
			$module_data[$key]['authority'] = array();
			if( empty($value['module_id']) ){
				continue;
			}
			$temp_authority_config = $authority_config;
			$temp_authority_config['where'][] = array('module_id=[+]', $value['module_id']);
			$module_data[$key]['authority'] = object(parent::TABLE_AUTHORITY)->select($temp_authority_config);*/
			
		}
		
		return $module_data;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>