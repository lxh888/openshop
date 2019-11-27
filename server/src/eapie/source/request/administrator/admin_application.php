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
class admin_application extends \eapie\source\request\administrator {
	
	
	/*应用管理*/
	
	
	
	/**
	 * 获取应用选项列表
	 *  $data = array(
	 * 	'sort' => ["sort_asc", "name_desc"] 排序
	 * );
	 * 
	 * ADMINISTRATORADMINAPPLICATIONOPTION
	 * {"class":"administrator/admin_application","method":"api_option"}
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
			'select' => array(
				'application_id',
				'application_name',
				'application_state',
				'application_insert_time',
				'application_update_time'
				),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'id_desc' => array('application_id', true),
			'id_asc' => array('application_id', false),
			'name_desc' => array('application_name', true),
			'name_asc' => array('application_name', false),
			
			'insert_time_desc' => array('application_insert_time', true),
			'insert_time_asc' => array('application_insert_time', false),
			'update_time_desc' => array('application_update_time', true),
			'update_time_asc' => array('application_update_time', false),
			
			'sort_desc' => array('application_sort', true),
			'sort_asc' => array('application_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('application_id', false);
		
		//前台的这里应该是需要判断状态的，第三个参数为true，表示不拿缓存的
		return object(parent::TABLE_APPLICATION)->select($config);
	}
	
	
	
	
	
	
		
		
	/**
	 * 删除应用
	 * 
	 * ADMINISTRATORADMINAPPLICATIONREMOVE
	 * {"class":"administrator/admin_application","method":"api_remove"}
	 * 
	 */
	public function api_remove( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_APPLICATION_REMOVE);
		//数据检测 
		object(parent::ERROR)->check($data, 'application_id', parent::TABLE_APPLICATION, array('args'));
		//查询旧数据
        $original = object(parent::TABLE_APPLICATION)->find($data['application_id']);
        if( empty($original) ) throw new error('数据不存在');
		
		if( object(parent::TABLE_APPLICATION)->remove($data['application_id']) ){
			//插入操作日志
			object(parent::TABLE_LOG)->insert($data, $original);
			return $data['application_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
		
				
	/**
	 * 获取一条数据
	 * $data = array(
	 * 	application_id ID
	 * )
	 * 
	 * ADMINISTRATORADMINAPPLICATIONGET
	 * {"class":"administrator/admin_application","method":"api_get"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_get( $data = array() ){
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_APPLICATION_EDIT);
		object(parent::ERROR)->check($data, 'application_id', parent::TABLE_APPLICATION, array('args'));
		
		$get_data = object(parent::TABLE_APPLICATION)->find($data['application_id']);
		if( empty($get_data) ){
			throw new error("数据不存在");
		}
		
		return $get_data;
	}
	
	
	
	
	
	
			
	/**
	 * 获取数据列表
	 * 
	 * ADMINISTRATORADMINAPPLICATIONLIST
	 * {"class":"administrator/admin_application","method":"api_list"}
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function api_list( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_APPLICATION_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'id_desc' => array('application_id', true),
			'id_asc' => array('application_id', false),
			'name_desc' => array('application_name', true),
			'name_asc' => array('application_name', false),
			
			'administrator_desc' => array('administrator', true),
			'administrator_asc' => array('administrator', false),
			
			'on_off_desc' => array('application_on_off', true),
			'on_off_asc' => array('application_on_off', false),
			
			'state_desc' => array('application_state', true),
			'state_asc' => array('application_state', false),
			
			'insert_time_desc' => array('application_insert_time', true),
			'insert_time_asc' => array('application_insert_time', false),
			'update_time_desc' => array('application_update_time', true),
			'update_time_asc' => array('application_update_time', false),
			
			'sort_desc' => array('application_sort', true),
			'sort_asc' => array('application_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('application_id', false);
		
		if( !empty($data['search']) ){
			if( isset($data['search']['application_id']) && is_string($data['search']['application_id']) ){
				$config["where"][] = array('[and] a.application_id=[+]', $data['search']['application_id']);
			}
				
			if( isset($data['search']['application_name']) && is_string($data['search']['application_name']) ){
                $config['where'][] = array('[and] a.application_name LIKE "%[-]%"', $data['search']['application_name']);
            }	
			
			if( isset($data['search']['state']) && 
			(is_string($data['search']['state']) || is_numeric($data['search']['state'])) &&
			in_array($data['search']['state'], array("0", "1")) ){
				$config["where"][] = array('[and] a.application_state=[+]', $data['search']['state']);
				}
			if( isset($data['search']['on_off']) && 
			(is_string($data['search']['on_off']) || is_numeric($data['search']['on_off'])) &&
			in_array($data['search']['on_off'], array("0", "1")) ){
				$config["where"][] = array('[and] a.application_on_off=[+]', $data['search']['on_off']);
				}
			if( isset($data['search']['administrator']) && 
			(is_string($data['search']['administrator']) || is_numeric($data['search']['administrator'])) &&
			in_array($data['search']['administrator'], array("0", "1")) ){
				$config["where"][] = array('[and] a.administrator=[+]', $data['search']['administrator']);
				}
			
		}
		
		return object(parent::TABLE_APPLICATION)->select_page($config);
	}
	
	
	
	
	
			
	/**
	 * 添加应用
	 * 
	 * ADMINISTRATORADMINAPPLICATIONADD
	 * {"class":"administrator/admin_application","method":"api_add"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_APPLICATION_ADD);
		
		//数据检测
		object(parent::ERROR)->check($data, 'application_id', parent::TABLE_APPLICATION, array('args', 'register_id'));
		object(parent::ERROR)->check($data, 'application_name', parent::TABLE_APPLICATION, array('args', 'length'));
		object(parent::ERROR)->check($data, 'application_info', parent::TABLE_APPLICATION, array('args'));
		if( isset($data['application_json']) && $data['application_json'] != "" )
		object(parent::ERROR)->check($data, 'application_json', parent::TABLE_APPLICATION, array('args'));
		object(parent::ERROR)->check($data, 'application_sort', parent::TABLE_APPLICATION, array('args'));
		object(parent::ERROR)->check($data, 'application_state', parent::TABLE_APPLICATION, array('args'));
		object(parent::ERROR)->check($data, 'administrator', parent::TABLE_APPLICATION, array('args'));
		object(parent::ERROR)->check($data, 'application_on_off', parent::TABLE_APPLICATION, array('args'));
		object(parent::ERROR)->check($data, 'application_warning', parent::TABLE_APPLICATION, array('args'));
		
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'application_id', 
			'application_name', 
			'application_info', 
			'application_json',
			'application_sort',
			'application_state',
			'administrator',
			'application_on_off',
			'application_warning',
			);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		if( !empty($data['authority_id']) && is_array($data['authority_id'])){
			//清理数据
			$authority_ids = array();
			foreach($data['authority_id'] as $value){
				if(is_string($value) || is_numeric($value)){
					$authority_ids[] = cmd(array($value), 'str addslashes');
				}
			}
		}
		
		if( !empty($authority_ids) ){
			//获取权限数据
			$in_string = "\"".implode("\",\"", $authority_ids)."\"";
			$admin_authority_where = array();
			$admin_authority_where[] = array("authority_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
			$admin_authority_data = object(parent::TABLE_AUTHORITY)->select(array("where"=>$admin_authority_where));
		}
		
		if( !empty($admin_authority_data) ){
			$insert_data['authority_id'] = array();
			foreach($admin_authority_data as $value){
				$insert_data['authority_id'][] = $value['authority_id'];
			}
			
			if( !empty($insert_data['authority_id']) ){
				$insert_data['authority_id'] = ",".implode(",", $insert_data['authority_id']).",";
			}else{
				$insert_data['authority_id'] = "";
				}
		}
		
		
		//创建时间
		$insert_data['application_insert_time'] = time();
		//更新时间
		$insert_data['application_update_time'] = time();
		if( object(parent::TABLE_APPLICATION)->insert($insert_data) ){
			//插入操作日志
			object(parent::TABLE_LOG)->insert($data, $insert_data);
			return $insert_data['application_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
		
	/**
	 * 编辑应用
	 * 
	 * ADMINISTRATORADMINAPPLICATIONEDIT
	 * {"class":"administrator/admin_application","method":"api_edit"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_API_EDIT);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'primary_key', parent::TABLE_APPLICATION, array('args'));
		if( isset($data['application_id']) && $data['application_id'] != $data['primary_key'] )
		object(parent::ERROR)->check($data, 'application_id', parent::TABLE_APPLICATION, array('args', 'register_id'));
		if( isset($data['application_name']) )
		object(parent::ERROR)->check($data, 'application_name', parent::TABLE_APPLICATION, array('args', 'length'));
		if( isset($data['application_info']) )
		object(parent::ERROR)->check($data, 'application_info', parent::TABLE_APPLICATION, array('args'));
		if( isset($data['application_json']) && $data['application_json'] != "" )
		object(parent::ERROR)->check($data, 'application_json', parent::TABLE_APPLICATION, array('args'));
		if( isset($data['application_sort']) )
		object(parent::ERROR)->check($data, 'application_sort', parent::TABLE_APPLICATION, array('args'));
		if( isset($data['application_state']) )
		object(parent::ERROR)->check($data, 'application_state', parent::TABLE_APPLICATION, array('args'));
		if( isset($data['administrator']) )
		object(parent::ERROR)->check($data, 'administrator', parent::TABLE_APPLICATION, array('args'));
		if( isset($data['application_on_off']) )
		object(parent::ERROR)->check($data, 'application_on_off', parent::TABLE_APPLICATION, array('args'));
		if( isset($data['application_warning']) )
		object(parent::ERROR)->check($data, 'application_warning', parent::TABLE_APPLICATION, array('args'));
		
		//获取旧数据
		$old_data = object(parent::TABLE_APPLICATION)->find($data['primary_key']);
		if( empty($old_data) ){
			throw new error("应用主键有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'application_id', 
			'application_name', 
			'application_info', 
			'application_json',
			'application_sort',
			'application_state',
			'administrator',
			'application_on_off',
			'application_warning',
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($update_data as $key => $value){
			if( isset($old_data[$key]) ){
				if($old_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		
		//如果权限修改
		if( isset($data['authority_id']) ){
			//清理数据
			$authority_ids = array();
			if( !empty($data['authority_id']) ){
				foreach($data['authority_id'] as $value){
					if(is_string($value) || is_numeric($value)){
						$authority_ids[] = cmd(array($value), 'str addslashes');
					}
				}
			}
			
			
			//旧数据不为空，那么本次为空，需要更新
			if( empty($authority_ids) ){
				
				if( empty($admin_data['authority_id']) ){
					unset($update_data['authority_id']);
				}else{
					$update_data['authority_id'] = ""; 
				}
				
			}else{
				//获取权限数据
				$in_string = "\"".implode("\",\"", $authority_ids)."\"";
				$admin_authority_where = array();
				$admin_authority_where[] = array("authority_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
				$admin_authority_data = object(parent::TABLE_AUTHORITY)->select(array("where"=>$admin_authority_where));
				$update_data['authority_id'] = array();
				if(!empty($admin_authority_data)){
					foreach($admin_authority_data as $value){
						$update_data['authority_id'][] = $value['authority_id'];
					}
				}
				
				//与旧数据对比
				if( empty($update_data['authority_id']) ){
					$update_data['authority_id'] = ""; 
				}else{
					
					if( !empty($admin_data['authority_id']) ){
						$admin_data['authority_id'] = explode(',', trim($admin_data['authority_id'], ','));
						$not_exist = false;//是否有不存在项
						foreach($admin_data['authority_id'] as $value){
							if( !in_array($value, $update_data['authority_id']) ){
								$not_exist = true;
								break;
							}
						}
					}
					
					if( !empty($not_exist) || 
					empty($admin_data['authority_id']) || 
					count($admin_data['authority_id']) != count($update_data['authority_id']) ){
						$update_data['authority_id'] = ",".implode(",", $update_data['authority_id']).",";
					}else{
						unset($update_data['authority_id']);//删除
					}
					
				}
			}
		
		}
		
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		//更新时间
		$update_data['application_update_time'] = time();
		if( object(parent::TABLE_APPLICATION)->update( array(array('application_id=[+]', (string)$data['primary_key'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_LOG)->insert($data, $update_data);
			return $data['application_id'];
		}else{
			throw new error("操作失败");
		}
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>