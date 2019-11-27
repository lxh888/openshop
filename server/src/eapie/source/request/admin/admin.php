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



namespace eapie\source\request\admin;
use eapie\main;
use eapie\error;
class admin extends \eapie\source\request\admin {
	
	
	
		
	/**
	 * 删除管理员角色
	 * 
	 * ADMINREMOVE
	 * 
	 */
	public function api_remove( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ADMIN_REMOVE);
		//数据检测 
		object(parent::ERROR)->check($data, 'admin_id', parent::TABLE_ADMIN, array('args'));
		
		//不能删除当前自己管理角色
		if($data["admin_id"] == $_SESSION["admin"]["admin_id"]){
			throw new error("不能删除当前登录用户的管理角色");
		}
		
		//该角色下存在管理人员则无法删除
		if( object(parent::TABLE_ADMIN_USER)->find_exists_administrator($data['admin_id']) ){
			throw new error("该角色下存在管理人员，请先清理管理人员才能删除该角色");
			}
		
		if( object(parent::TABLE_ADMIN)->remove($data['admin_id']) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $data);
			return $data['admin_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
		
	/**
	 * 添加管理员角色
	 * 
	 * ADMINADD
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ADMIN_ADD);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'admin_name', parent::TABLE_ADMIN, array('args', 'length'));
		object(parent::ERROR)->check($data, 'admin_info', parent::TABLE_ADMIN, array('args'));
		object(parent::ERROR)->check($data, 'admin_sort', parent::TABLE_ADMIN, array('args'));
		object(parent::ERROR)->check($data, 'admin_state', parent::TABLE_ADMIN, array('args'));
		
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'admin_name', 
			'admin_info', 
			'admin_sort',
			'admin_state',
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
		
		//获取id号
		$insert_data['admin_id'] = object(parent::TABLE_ADMIN)->get_unique_id();
		//时间
		$insert_data['admin_insert_time'] = time();
		$insert_data['admin_update_time'] = time();
		
		if( object(parent::TABLE_ADMIN)->insert($insert_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $insert_data);
			return $insert_data['admin_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
		
	/**
	 * 获取管理角色选项列表
	 *  $data = array(
	 * 	'sort' => ["sort_asc", "name_desc"] 排序
	 * );
	 * 
	 * ADMINOPTION
	 * {"class":"admin/admin","method":"api_option"}
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
			'name_desc' => array('admin_name', true),
			'name_asc' => array('admin_name', false),
			
			'state_desc' => array('admin_state', true),
			'state_asc' =>  array('admin_state', false),
			
			'insert_time_desc' => array('admin_insert_time', true),
			'insert_time_asc' => array('admin_insert_time', false),
			'update_time_desc' => array('admin_update_time', true),
			'update_time_asc' => array('admin_update_time', false),
			
			'sort_desc' => array('admin_sort', true),
			'sort_asc' => array('admin_sort', false),
			'user_count_desc' => array('admin_user_count', true),
			'user_count_asc' => array('admin_user_count', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('admin_id', false);
		
		return object(parent::TABLE_ADMIN)->select_join($config);
	}
	
	
	
	
	
	
		
	/**
	 * 获取数据列表
	 * 需要判断浏览权限
	 * 
	 * $request = array(
	 * 	'search' => array(),//搜索、筛选
	 * 	'sort' => array(),//排序
	 *  'size' => 0,//每页的条数
	 * 	'page' => 0, //当前页数，如果是等于 all 那么则查询所有
	 *  'start' => 0, //开始的位置，如果存在，则page无效
	 * );
	 * limit的分页算法是：当前页数-1 * page_size
	 * 序号的算法：key键+1，+每页显示的条数。等于分页后的序号。{key + 1 + page_size}
	 * 
	 * 返回的数据：
	 * $data = array(
	 * 	'row_count' => //数据总条数
	 * 	'limit_count' => //已取出条数
	 * 	'page_size' => //每页的条数
	 *  'page_count' => //总页数
	 * 	'data' => //数据
	 * );
	 * 
	 * ADMINLIST
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ADMIN_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'name_desc' => array('admin_name', true),
			'name_asc' => array('admin_name', false),
			
			'state_desc' => array('admin_state', true),
			'state_asc' =>  array('admin_state', false),
			
			'insert_time_desc' => array('admin_insert_time', true),
			'insert_time_asc' => array('admin_insert_time', false),
			'update_time_desc' => array('admin_update_time', true),
			'update_time_asc' => array('admin_update_time', false),
			
			'sort_desc' => array('admin_sort', true),
			'sort_asc' => array('admin_sort', false),
			'user_count_desc' => array('admin_user_count', true),
			'user_count_asc' => array('admin_user_count', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('admin_id', false);
		
		if(!empty($data['search'])){
			if( isset($data['search']['id']) && is_string($data['search']['id']) ){
				$config["where"][] = array('[and] a.admin_id=[+]', $data['search']['id']);
			}
		}
		
		return object(parent::TABLE_ADMIN)->select_page($config);
		
	}
	
	
	
	/**
	 * 检查编辑的权限
	 * 
	 * ADMINEDITCHECK
	 * 
	 * @param	void
	 * @return  bool
	 */
	public function api_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ADMIN_EDIT);
		return true;
	}
	
	
	
	
	/**
	 * 编辑管理角色
	 * 
	 * ADMINEDIT
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ADMIN_EDIT);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'admin_id', parent::TABLE_ADMIN, array('args'));
		
		if( isset($data['admin_name']) )
		object(parent::ERROR)->check($data, 'admin_name', parent::TABLE_ADMIN, array('args', 'length'));
		if( isset($data['admin_info']) )
		object(parent::ERROR)->check($data, 'admin_info', parent::TABLE_ADMIN, array('args'));
		if( isset($data['admin_sort']) )
		object(parent::ERROR)->check($data, 'admin_sort', parent::TABLE_ADMIN, array('args'));
		if( isset($data['admin_state']) )
		object(parent::ERROR)->check($data, 'admin_state', parent::TABLE_ADMIN, array('args'));
		
		//获取旧数据
		$admin_data = object(parent::TABLE_ADMIN)->find($data['admin_id']);
		if( empty($admin_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'admin_name', 
			'admin_info', 
			'admin_sort',
			'admin_state',
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($update_data as $key => $value){
			if( isset($admin_data[$key]) ){
				if($admin_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		
		//不能封禁当前自己管理角色
		if( isset($update_data['admin_state']) && $update_data['admin_state'] == 0 ){
			if( $data["admin_id"] == $_SESSION["admin"]["admin_id"] ){
				throw new error("不能停用当前登录用户的管理角色");
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
		$update_data['admin_update_time'] = time();
		if( object(parent::TABLE_ADMIN)->update( array(array('admin_id=[+]', (string)$data['admin_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['admin_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
		
		
	/**
	 * 获取一条数据
	 * $data = arrray(
	 * 	admin_id 管理角色ID
	 * )
	 * 
	 * ADMINGET
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_get( $data = array() ){
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ADMIN_READ);
		object(parent::ERROR)->check($data, 'admin_id', parent::TABLE_ADMIN, array('args'));
		
		
		$get_data = object(parent::TABLE_ADMIN)->find($data['admin_id']);
		if( empty($get_data) ){
			throw new error("角色不存在");
		}
		
		return $get_data;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>