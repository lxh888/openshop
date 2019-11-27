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
class admin_management extends \eapie\source\request\administrator {
	
	
	/*管理栏目*/
	
	
				
	/**
	 * 获取管理栏目选项列表
	 * 
	 * $data = array(
	 * 	"level" => "son" 只获取子级，为空获取父级
	 * 	'sort' => ["sort_asc", "name_desc"] 排序
	 * );
	 * 
	 * ADMINISTRATORADMINMANAGEMENTOPTION
	 * {"class":"administrator/admin_management","method":"api_option"}
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
		
		$parent_config = $config;
		$son_config = $config;
		
		if( !empty($data["level"]) && $data["level"] == "son" ){
			$parent_config["where"][] = array('[and] management_parent_id<>""');
		}else{
			$parent_config["where"][] = array("[and] management_parent_id=\"\"");
		}
		
		return object(parent::TABLE_MANAGEMENT)->select_parent_son_all($parent_config, $son_config);
	}
	
	
	
	
	
		
	/**
	 * 获取管理菜单的数据列表
	 * 
	 * ADMINISTRATORADMINMANAGEMENTLIST
	 * {"class":"administrator/admin_management","method":"api_list"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MANAGEMENT_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'name_desc' => array('management_name', true),
			'name_asc' => array('management_name', false),
			'page_desc' => array('management_page', true),
			'page_asc' => array('management_page', false),
			'state_desc' => array('management_state', true),
			'state_asc' =>  array('management_state', false),
			'hide_desc' => array('management_hide', true),
			'hide_asc' =>  array('management_hide', false),
			'insert_time_desc' => array('management_insert_time', true),
			'insert_time_asc' => array('management_insert_time', false),
			'update_time_desc' => array('management_update_time', true),
			'update_time_asc' => array('management_update_time', false),
			'parent_desc' => array('management_parent_id', true),
			'parent_asc' => array('management_parent_id', false),
			'sort_desc' => array('management_sort', true),
			'sort_asc' => array('management_sort', false),
			'son_desc' => array('management_son_count', true),
			'son_asc' => array('management_son_count', false)
		));
		
		//避免排序重复
		$config["orderby"][] = array('management_id', false);
		
		
		if(!empty($data['search'])){
			if( isset($data['search']['management_id']) && is_string($data['search']['management_id']) ){
				$config["where"][] = array('[and] m.management_id=[+]', $data['search']['management_id']);
			}
			if( isset($data['search']['management_name']) && is_string($data['search']['management_name']) ){
                $config['where'][] = array('[and] m.management_name LIKE "%[-]%"', $data['search']['management_name']);
            }
			if( isset($data['search']['management_page']) && is_string($data['search']['management_page']) ){
                $config['where'][] = array('[and] m.management_page LIKE "%[-]%"', $data['search']['management_page']);
            }
			if( isset($data['search']['authority_id']) && is_string($data['search']['authority_id']) ){
				$config["where"][] = array('[and] m.authority_id=[+]', $data['search']['authority_id']);
			}
		}
		
		if( isset($data['search']['management_parent_id']) && is_string($data['search']['management_parent_id']) ){
			$config["where"][] = array('[and] m.management_parent_id=[+]', $data['search']['management_parent_id']);
		}else{
			$config["where"][] = array('[and] m.management_parent_id=""');
		}
		
		return object(parent::TABLE_MANAGEMENT)->select_page($config);
		
	}
	
	
	
				
	/**
	 * 获取一条数据
	 * $data = arrray(
	 * 	authority_id 权限ID
	 * )
	 * 
	 * ADMINISTRATORADMINMANAGEMENTGET
	 * {"class":"administrator/admin_management","method":"api_get"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_get( $data = array() ){
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MANAGEMENT_READ);
		object(parent::ERROR)->check($data, 'management_id', parent::TABLE_MANAGEMENT, array('args'));
		
		$get_data = object(parent::TABLE_MANAGEMENT)->find($data['management_id']);
		if( empty($get_data) ){
			throw new error("数据不存在");
		}
		
		return $get_data;
	}
	
	
	
	
	
	/**
	 * 删除管理菜单
	 * 
	 * ADMINISTRATORADMINMANAGEMENTREMOVE
	 * {"class":"administrator/admin_management","method":"api_remove"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_remove( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MANAGEMENT_REMOVE);
		//数据检测 
		object(parent::ERROR)->check($data, 'management_id', parent::TABLE_MANAGEMENT, array('args'));
		
		//查询旧数据
        $original = object(parent::TABLE_MANAGEMENT)->find($data['management_id']);
        if( empty($original) ) throw new error('数据不存在');
		
		//存在下级则无法删除
		if( object(parent::TABLE_MANAGEMENT)->find_exists_son_id($data['management_id']) ){
			throw new error("该数据下存在子级，请先清理子级才能删除该数据");
			}
		
		if( object(parent::TABLE_MANAGEMENT)->remove($data['management_id']) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $original);
			return $data['management_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
			
	/**
	 * 添加菜单
	 * 
	 * ADMINISTRATORADMINMANAGEMENTADD
	 * {"class":"administrator/admin_management","method":"api_add"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MANAGEMENT_ADD);
		
		//数据检测 
		if( isset($data['management_parent_id']) && $data['management_parent_id'] != '' )
		object(parent::ERROR)->check($data, 'management_parent_id', parent::TABLE_MANAGEMENT, array('args', 'exists_id'));
		if( isset($data['authority_id']) && $data['authority_id'] != '' )
		object(parent::ERROR)->check($data, 'authority_id', parent::TABLE_AUTHORITY, array('args', 'exists_id'));
		object(parent::ERROR)->check($data, 'management_name', parent::TABLE_MANAGEMENT, array('args', 'length'));
		object(parent::ERROR)->check($data, 'management_info', parent::TABLE_MANAGEMENT, array('args'));
		object(parent::ERROR)->check($data, 'management_page', parent::TABLE_MANAGEMENT, array('args'));
		object(parent::ERROR)->check($data, 'management_sort', parent::TABLE_MANAGEMENT, array('args'));
		object(parent::ERROR)->check($data, 'management_state', parent::TABLE_MANAGEMENT, array('args'));
		object(parent::ERROR)->check($data, 'management_hide', parent::TABLE_MANAGEMENT, array('args'));
		object(parent::ERROR)->check($data, 'management_label_before', parent::TABLE_MANAGEMENT, array('args'));
		object(parent::ERROR)->check($data, 'management_label_after', parent::TABLE_MANAGEMENT, array('args'));
		object(parent::ERROR)->check($data, 'management_href', parent::TABLE_MANAGEMENT, array('args'));
		object(parent::ERROR)->check($data, 'management_target', parent::TABLE_MANAGEMENT, array('args'));
		if( isset($data['management_query']) && $data['management_query'] != '' )
		object(parent::ERROR)->check($data, 'management_query', parent::TABLE_MANAGEMENT, array('args'));
		if( isset($data['management_path']) && $data['management_path'] != '' )
		object(parent::ERROR)->check($data, 'management_path', parent::TABLE_MANAGEMENT, array('args'));
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'authority_id', 
			'management_name',
			'management_page',
			'management_parent_id',
			'management_info', 
			'management_sort',
			'management_state',
			'management_hide',
			'management_label_before',
			'management_label_after',
			'management_href',
			'management_target',
			'management_query',
			'management_path',
			);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		$insert_data['management_id'] = object(parent::TABLE_MANAGEMENT)->get_unique_id();
		//创建时间
		$insert_data['management_insert_time'] = time();
		//更新时间
		$insert_data['management_update_time'] = time();
		
		if( object(parent::TABLE_MANAGEMENT)->insert($insert_data) ){
			//插入操作日志
			object(parent::TABLE_LOG)->insert($data, $insert_data);
			return $insert_data['management_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
			
	/**
	 * 检查编辑管理菜单的权限
	 * 
	 * ADMINISTRATORADMINMANAGEMENTEDITCHECK
	 * {"class":"administrator/admin_management","method":"api_edit_check"}
	 * 
	 * @param	void
	 * @return  bool
	 */
	public function api_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MANAGEMENT_EDIT);
		return true;
	}
	
	
	
	
	
	/**
	 * 编辑管理菜单
	 * 
	 * ADMINISTRATORADMINMANAGEMENTEDIT
	 * {"class":"administrator/admin_management","method":"api_edit"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MANAGEMENT_EDIT);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'management_id', parent::TABLE_MANAGEMENT, array('args'));
		
		//数据检测 
		if( isset($data['management_parent_id']) && $data['management_parent_id'] != '' )
		object(parent::ERROR)->check($data, 'management_parent_id', parent::TABLE_MANAGEMENT, array('args', 'exists_id'));
		if( isset($data['authority_id']) && $data['authority_id'] != '' )
		object(parent::ERROR)->check($data, 'authority_id', parent::TABLE_AUTHORITY, array('args', 'exists_id'));
		if( isset($data['management_name']) )
		object(parent::ERROR)->check($data, 'management_name', parent::TABLE_MANAGEMENT, array('args', 'length'));
		if( isset($data['management_info']) )
		object(parent::ERROR)->check($data, 'management_info', parent::TABLE_MANAGEMENT, array('args'));
		if( isset($data['management_page']) )
		object(parent::ERROR)->check($data, 'management_page', parent::TABLE_MANAGEMENT, array('args'));
		if( isset($data['management_sort']) )
		object(parent::ERROR)->check($data, 'management_sort', parent::TABLE_MANAGEMENT, array('args'));
		if( isset($data['management_state']) )
		object(parent::ERROR)->check($data, 'management_state', parent::TABLE_MANAGEMENT, array('args'));
		if( isset($data['management_hide']) )
		object(parent::ERROR)->check($data, 'management_hide', parent::TABLE_MANAGEMENT, array('args'));
		if( isset($data['management_label_before']) )
		object(parent::ERROR)->check($data, 'management_label_before', parent::TABLE_MANAGEMENT, array('args'));
		if( isset($data['management_label_after']) )
		object(parent::ERROR)->check($data, 'management_label_after', parent::TABLE_MANAGEMENT, array('args'));
		if( isset($data['management_href']) )
		object(parent::ERROR)->check($data, 'management_href', parent::TABLE_MANAGEMENT, array('args'));
		if( isset($data['management_target']) )
		object(parent::ERROR)->check($data, 'management_target', parent::TABLE_MANAGEMENT, array('args'));
		if( isset($data['management_query']) && $data['management_query'] != '' )
		object(parent::ERROR)->check($data, 'management_query', parent::TABLE_MANAGEMENT, array('args'));
		if( isset($data['management_path']) && $data['management_path'] != '' )
		object(parent::ERROR)->check($data, 'management_path', parent::TABLE_MANAGEMENT, array('args'));
		
		
		//获取旧数据
		$module_data = object(parent::TABLE_MANAGEMENT)->find($data['management_id']);
		if( empty($module_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'authority_id', 
			'management_name',
			'management_page',
			'management_parent_id',
			'management_info', 
			'management_sort',
			'management_state',
			'management_hide',
			'management_label_before',
			'management_label_after',
			'management_href',
			'management_target',
			'management_query',
			'management_path',
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($update_data as $key => $value){
			if( isset($module_data[$key]) ){
				if($module_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		//更新时间
		$update_data['management_update_time'] = time();
		if( object(parent::TABLE_MANAGEMENT)->update( array(array('management_id=[+]', (string)$data['management_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_LOG)->insert($data, $update_data);
			return $data['management_id'];
		}else{
			throw new error("操作失败");
		}
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>