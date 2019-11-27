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
class admin_user extends \eapie\source\request\admin {
	
	
	
	
		
		
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
	 * ADMINUSERLIST
	 * {"class":"admin/admin_user","method":"api_list"}
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ADMIN_USER_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'user_id_desc' => array('user_id', true),
            'user_id_asc' => array('user_id', false),
			'user_parent_id_desc' => array('user_parent_id', true),
            'user_parent_id_asc' => array('user_parent_id', false),
            'user_nickname_desc' => array('user_nickname', true),
            'user_nickname_asc' => array('user_nickname', false),
			'user_phone_verify_list_desc' => array('user_phone_verify_list', true),
            'user_phone_verify_list_asc' => array('user_phone_verify_list', false),
            
			'admin_id_desc' => array('admin_id', true),
            'admin_id_asc' => array('admin_id', false),
            
			'state_desc' => array('admin_user_state', true),
			'state_asc' =>  array('admin_user_state', false),
			
			'insert_time_desc' => array('admin_user_insert_time', true),
			'insert_time_asc' => array('admin_user_insert_time', false),
			'update_time_desc' => array('admin_user_update_time', true),
			'update_time_asc' => array('admin_user_update_time', false),
			
			'sort_desc' => array('admin_user_sort', true),
			'sort_asc' => array('admin_user_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('admin_id', false);
		
		if(!empty($data['search'])){
			if (isset($data['search']['user_id']) && is_string($data['search']['user_id'])) {
                $config['where'][] = array('[and] u.user_id=[+]', $data['search']['user_id']);
            }
			if (isset($data['search']['user_nickname']) && is_string($data['search']['user_nickname'])) {
                $config['where'][] = array('[and] u.user_nickname LIKE "%[-]%"', $data['search']['user_nickname']);
            }

			if (isset($data['search']['user_phone']) && is_string($data['search']['user_phone'])) {
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['user_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $config['where'][] = array('[and] u.user_id=[+]', $user_id);
            }
			
            if (isset($data['search']['user_parent_id']) && is_string($data['search']['user_parent_id'])) {
                $config['where'][] = array('[and] u.user_parent_id=[+]', $data['search']['user_parent_id']);
            }
			if (isset($data['search']['user_parent_phone']) && is_string($data['search']['user_parent_phone'])) {
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['user_parent_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "-";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $config['where'][] = array('[and] u.user_parent_id=[+]', $user_id);
            }
			
			if (isset($data['search']['admin_id']) && is_string($data['search']['admin_id'])) {
                $config['where'][] = array('[and] au.admin_id=[+]', $data['search']['admin_id']);
            }
			if (isset($data['search']['admin_name']) && is_string($data['search']['admin_name'])) {
                $config['where'][] = array('[and] a.admin_name LIKE "%[-]%"', $data['search']['admin_name']);
            }
		}
		
		return object(parent::TABLE_ADMIN_USER)->select_page($config);
	}
	
	
	
	
	
		
	/**
	 * 添加管理人员
	 * 
	 * ADMINUSERADD
	 * {"class":"admin/admin_user","method":"api_add"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ADMIN_USER_ADD);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'user', parent::TABLE_ADMIN_USER, array('args'));
		object(parent::ERROR)->check($data, 'admin_id', parent::TABLE_ADMIN, array('args', 'exists_id'));
		object(parent::ERROR)->check($data, 'admin_user_info', parent::TABLE_ADMIN_USER, array('args'));
		object(parent::ERROR)->check($data, 'admin_user_sort', parent::TABLE_ADMIN_USER, array('args'));
		object(parent::ERROR)->check($data, 'admin_user_state', parent::TABLE_ADMIN_USER, array('args'));
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'admin_id',
			'admin_user_info', 
			'admin_user_sort',
			'admin_user_state',
			);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		//判断推荐人的ID或者是登录手机号
        $user_data = object(parent::TABLE_USER)->find_id_or_phone($data['user']);
        object(parent::ERROR)->check($user_data, 'user_id', parent::TABLE_ADMIN_USER, array('args', 'exists'));
		
		//获取id号
		$insert_data['user_id'] = $user_data['user_id'];
		//时间
		$insert_data['admin_user_insert_time'] = time();
		$insert_data['admin_user_update_time'] = time();
		
		if( object(parent::TABLE_ADMIN_USER)->insert($insert_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $insert_data);
			return $insert_data['user_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
		
	/**
	 * 检查编辑的权限
	 * 
	 * ADMINUSEREDITCHECK
	 * {"class":"admin/admin_user","method":"api_edit_check"}
	 *  
	 * @param	void
	 * @return  bool
	 */
	public function api_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ADMIN_USER_EDIT);
		return true;
	}
	
	
	
	
	
		
	/**
	 * 编辑管理员
	 * 
	 * ADMINUSEREDIT
	 * {"class":"admin/admin_user","method":"api_edit"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ADMIN_USER_EDIT);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'user_id', parent::TABLE_USER, array('args'));
		if( isset($data['admin_id']) )
		object(parent::ERROR)->check($data, 'admin_id', parent::TABLE_ADMIN, array('args', 'exists_id'));
		if( isset($data['admin_user_info']) )
		object(parent::ERROR)->check($data, 'admin_user_info', parent::TABLE_ADMIN_USER, array('args'));
		if( isset($data['admin_user_sort']) )
		object(parent::ERROR)->check($data, 'admin_user_sort', parent::TABLE_ADMIN_USER, array('args'));
		if( isset($data['admin_user_state']) )
		object(parent::ERROR)->check($data, 'admin_user_state', parent::TABLE_ADMIN_USER, array('args'));
		
		//获取旧数据
		$admin_user_data = object(parent::TABLE_ADMIN_USER)->find($data['user_id']);
		if( empty($admin_user_data) ){
			throw new error("ID有误，数据不存在");
		}
		$admin_user_data['admin_user_json'] = cmd(array($admin_user_data['admin_user_json']), 'json decode');
		if (empty($admin_user_data['admin_user_json'])) {
			$admin_user_data['admin_user_json'] = array();
		}

		//白名单 私密数据不能获取
		$whitelist = array(
			'admin_id', 
			'admin_user_info', 
			'admin_user_sort',
			'admin_user_state',
			'admin_user_json',
		);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($update_data as $key => $value){
			if( isset($admin_user_data[$key]) ){
				if($admin_user_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		//不能更改当前自己管理角色
		if( isset($update_data['admin_id']) && 
		$data["user_id"] == $_SESSION["user_id"] && 
		$update_data["admin_id"] != $_SESSION["admin"]["admin_id"]){
			throw new error("不能更改当前登录用户的管理角色");
		}
		
		//不能封禁当前自己管理角色
		if( isset($update_data['admin_user_state']) && 
		$update_data['admin_user_state'] == 0 ){
			if( $data["user_id"] == $_SESSION["user_id"] ){
				throw new error("不能封禁当前登录的管理员");
			}
		}

		// json设置
		if (isset($update_data['admin_user_json'])) {
			$update_data['admin_user_json'] = $admin_user_data['admin_user_json'];
			if (isset($data['admin_user_json']['show_region_order'])) {
				$update_data['admin_user_json']['show_region_order'] = intval($data['admin_user_json']['show_region_order']);
			}
			$update_data['admin_user_json'] = cmd(array($update_data['admin_user_json']), 'json encode');
		}

		if( empty($update_data)){
			throw new error("没有需要更新的数据");
		}
		
		//更新时间
		$update_data['admin_user_update_time'] = time();
		if( object(parent::TABLE_ADMIN_USER)->update( array(array('user_id=[+]', (string)$data['user_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['user_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
			
	/**
	 * 获取一条数据
	 * $data = arrray(
	 * 	user_id 用户ID
	 * )
	 * 
	 * ADMINUSERGET
	 * {"class":"admin/admin_user","method":"api_get"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_get( $data = array() ){
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ADMIN_READ);
		object(parent::ERROR)->check($data, 'user_id', parent::TABLE_USER, array('args'));
		$get_data = object(parent::TABLE_ADMIN_USER)->find($data['user_id']);
		if( empty($get_data) ){
			throw new error("管理员不存在");
		}
		$get_data['admin_user_json'] = cmd(array($get_data['admin_user_json']), 'json decode');
		if (empty($get_data['admin_user_json'])) {
			$get_data['admin_user_json'] = array();
		}
		return $get_data;
	}
	
	
	
	
			
	/**
	 * 删除管理员
	 * 
	 * ADMINUSERREMOVE
	 * {"class":"admin/admin_user","method":"api_remove"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_remove( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ADMIN_USER_REMOVE);
		//数据检测 
		object(parent::ERROR)->check($data, 'user_id', parent::TABLE_USER, array('args'));
		
		//不能删除当前自己管理角色
		if( $data["user_id"] == $_SESSION["user_id"] ){
			throw new error("不能删除当前登录的管理员");
			}
		
		if( object(parent::TABLE_ADMIN_USER)->remove($data['user_id']) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $data);
			return $data['user_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	/**
	 * 管理员添加创始人库存
	 * Undocumented function
	 * api: ADMINUSERADDSCOKET
	 * {"class":"admin/admin_user","method":"api_add_scoket"}
	 * @param array $input
	 * @return void
	 */
	public function api_add_scoket($input = array()){

		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ADMIN_USER_ADD_SOCKET);

		if(empty($input['user_id']) || empty($input['scoket']) || !is_numeric($input['scoket'])){
			throw new error('参数错误');
		}

		$where = array(
			array('user_id=[+]',$input['user_id']),
		);
		$update_data = array(
			'admin_user_scoket'=>(int)$input['scoket'],
		);

		if(object(parent::TABLE_ADMIN_USER)->update($where,$update_data)){
			return $input['user_id'];
		}
		throw new error('更新库存失败');
	}
	
	
	
}
?>