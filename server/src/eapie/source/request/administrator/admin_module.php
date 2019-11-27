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
class admin_module extends \eapie\source\request\administrator {
	
	
	
		
		
	/**
	 * 删除模块
	 * 
	 * ADMINISTRATORADMINMODULEREMOVE
	 * {"class":"administrator/admin_module","method":"api_remove"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_remove( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MODULE_REMOVE);
		//数据检测 
		object(parent::ERROR)->check($data, 'module_id', parent::TABLE_MODULE, array('args'));
		
		//查询旧数据
        $original = object(parent::TABLE_MODULE)->find($data['module_id']);
        if( empty($original) ) throw new error('数据不存在');
		
		if( object(parent::TABLE_MODULE)->remove($data['module_id']) ){
			//插入操作日志
			object(parent::TABLE_LOG)->insert($data, $original);
			return $data['module_id'];
		}else{
			throw new error("操作失败");
		}
		
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
	 * ADMINISTRATORADMINMODULELIST
	 * {"class":"administrator/admin_module","method":"api_list"}
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function api_list( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MODULE_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'id_desc' => array('module_id', true),
			'id_asc' => array('module_id', false),
			
			'parent_id_desc' => array('module_parent_id', true),
			'parent_id_asc' => array('module_parent_id', false),
			'name_desc' => array('module_name', true),
			'name_asc' => array('module_name', false),
			'insert_time_desc' => array('module_insert_time', true),
			'insert_time_asc' => array('module_insert_time', false),
			'update_time_desc' => array('module_update_time', true),
			'update_time_asc' => array('module_update_time', false),
			
			'sort_desc' => array('module_sort', true),
			'sort_asc' => array('module_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('module_id', false);
		
		if(!empty($data['search'])){
			if( isset($data['search']['module_id']) && is_string($data['search']['module_id']) ){
				$config["where"][] = array('[and] m.module_id=[+]', $data['search']['module_id']);
				}
				
			if (isset($data['search']['module_name']) && is_string($data['search']['module_name'])) {
                $config['where'][] = array('[and] m.module_name LIKE "%[-]%"', $data['search']['module_name']);
            }	
		}
		
		return object(parent::TABLE_MODULE)->select_page($config);
	}
	
	
	
	
			
	/**
	 * 添加模块
	 * 
	 * ADMINISTRATORADMINMODULEADD
	 * {"class":"administrator/admin_module","method":"api_add"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MODULE_ADD);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'module_name', parent::TABLE_MODULE, array('args', 'length'));
		object(parent::ERROR)->check($data, 'module_info', parent::TABLE_MODULE, array('args'));
		object(parent::ERROR)->check($data, 'module_sort', parent::TABLE_MODULE, array('args'));
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'module_id', 
			'module_name', 
			'module_info', 
			'module_sort',
			);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		$insert_data['module_id'] = object(parent::TABLE_MODULE)->get_unique_id();//获取模块ID
		//创建时间
		$insert_data['module_insert_time'] = time();
		//更新时间
		$insert_data['module_update_time'] = time();
		
		if( object(parent::TABLE_MODULE)->insert($insert_data) ){
			//插入操作日志
			object(parent::TABLE_LOG)->insert($data, $insert_data);
			return $insert_data['module_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
		
	/**
	 * 检查编辑模块的权限
	 * 
	 * ADMINISTRATORADMINMODULEEDITCHECK
	 * {"class":"administrator/admin_module","method":"api_edit_check"}
	 * 
	 * @param	void
	 * @return  bool
	 */
	public function api_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MODULE_EDIT);
		return true;
	}
	
	
	/**
	 * 编辑模块
	 * 
	 * ADMINISTRATORADMINMODULEEDIT
	 * {"class":"administrator/admin_module","method":"api_edit"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MODULE_EDIT);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'module_id', parent::TABLE_MODULE, array('args'));
		
		if( isset($data['module_name']) )
		object(parent::ERROR)->check($data, 'module_name', parent::TABLE_MODULE, array('args', 'length'));
		if( isset($data['module_info']) )
		object(parent::ERROR)->check($data, 'module_info', parent::TABLE_MODULE, array('args'));
		if( isset($data['module_sort']) )
		object(parent::ERROR)->check($data, 'module_sort', parent::TABLE_MODULE, array('args'));
		
		
		//获取旧数据
		$module_data = object(parent::TABLE_MODULE)->find($data['module_id']);
		if( empty($module_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'module_name', 
			'module_info',
			'module_sort',
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
		$update_data['module_update_time'] = time();
		if( object(parent::TABLE_MODULE)->update( array(array('module_id=[+]', (string)$data['module_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_LOG)->insert($data, $update_data);
			return $data['module_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>