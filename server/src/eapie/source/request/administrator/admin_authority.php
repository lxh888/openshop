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
class admin_authority extends \eapie\source\request\administrator {
	
	
	
		
	/**
	 * 删除权限
	 * 
	 * ADMINISTRATORADMINAUTHORITYREMOVE
	 * {"class":"administrator/admin_authority","method":"api_remove"}
	 * 
	 */
	public function api_remove( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_AUTHORITY_REMOVE);
		//数据检测 
		object(parent::ERROR)->check($data, 'authority_id', parent::TABLE_AUTHORITY, array('args'));
		//查询旧数据
        $original = object(parent::TABLE_AUTHORITY)->find($data['authority_id']);
        if( empty($original) ) throw new error('数据不存在');
		
		if( object(parent::TABLE_AUTHORITY)->remove($data['authority_id']) ){
			//插入操作日志
			object(parent::TABLE_LOG)->insert($data, $original);
			return $data['authority_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	/**
	 * 检查编辑的权限
	 * 
	 * ADMINISTRATORADMINAUTHORITYEDITCHECK
	 * {"class":"administrator/admin_authority","method":"api_edit_check"}
	 * 
	 * @param	void
	 * @return  bool
	 */
	public function api_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_AUTHORITY_EDIT);
		return true;
	}
	
	
	/**
	 * 编辑权限
	 * 
	 * ADMINISTRATORADMINAUTHORITYEDIT
	 * {"class":"administrator/admin_authority","method":"api_edit"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_AUTHORITY_EDIT);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'authority_id', parent::TABLE_AUTHORITY, array('args'));
		
		if( isset($data['authority_name']) )
		object(parent::ERROR)->check($data, 'authority_name', parent::TABLE_AUTHORITY, array('args', 'length'));
		if( isset($data['authority_info']) )
		object(parent::ERROR)->check($data, 'authority_info', parent::TABLE_AUTHORITY, array('args'));
		if( isset($data['authority_sort']) )
		object(parent::ERROR)->check($data, 'authority_sort', parent::TABLE_AUTHORITY, array('args'));
		if( isset($data['module_id']) && $data['module_id'] != "" )
		object(parent::ERROR)->check($data, 'module_id', parent::TABLE_MODULE, array('args', 'exists_id'));
		
		//获取旧数据
		$authority_data = object(parent::TABLE_AUTHORITY)->find($data['authority_id']);
		if( empty($authority_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'authority_name', 
			'authority_info',
			'authority_sort',
			'module_id'
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($update_data as $key => $value){
			if( isset($authority_data[$key]) ){
				if($authority_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		//更新时间
		$update_data['authority_update_time'] = time();
		if( object(parent::TABLE_AUTHORITY)->update( array(array('authority_id=[+]', (string)$data['authority_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_LOG)->insert($data, $update_data);
			return $data['authority_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
		
	/**
	 * 添加权限
	 * 
	 * ADMINISTRATORADMINAUTHORITYADD
	 * {"class":"administrator/admin_authority","method":"api_add"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_AUTHORITY_ADD);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'authority_id', parent::TABLE_AUTHORITY, array('args', 'register_id'));
		object(parent::ERROR)->check($data, 'authority_name', parent::TABLE_AUTHORITY, array('args', 'length'));
		object(parent::ERROR)->check($data, 'authority_info', parent::TABLE_AUTHORITY, array('args'));
		object(parent::ERROR)->check($data, 'authority_sort', parent::TABLE_AUTHORITY, array('args'));
		if( isset($data['module_id']) && $data['module_id'] != "" )
		object(parent::ERROR)->check($data, 'module_id', parent::TABLE_MODULE, array('args', 'exists_id'));
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'authority_id', 
			'authority_name', 
			'authority_info', 
			'authority_sort',
			'module_id'
			);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		//创建时间
		$insert_data['authority_insert_time'] = time();
		//更新时间
		$insert_data['authority_update_time'] = time();
		
		if( object(parent::TABLE_AUTHORITY)->insert($insert_data) ){
			//插入操作日志
			object(parent::TABLE_LOG)->insert($data, $insert_data);
			return $insert_data['authority_id'];
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
	 * ADMINISTRATORADMINAUTHORITYLIST
	 * {"class":"administrator/admin_authority","method":"api_list"}
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function api_list( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_AUTHORITY_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'id_desc' => array('authority_id', true),
			'id_asc' => array('authority_id', false),
			'name_desc' => array('authority_name', true),
			'name_asc' => array('authority_name', false),
			'insert_time_desc' => array('authority_insert_time', true),
			'insert_time_asc' => array('authority_insert_time', false),
			'update_time_desc' => array('authority_update_time', true),
			'update_time_asc' => array('authority_update_time', false),
			
			'module_id_desc' => array('a.module_id', true),
			'module_id_asc' => array('a.module_id', false),
			
			'sort_desc' => array('authority_sort', true),
			'sort_asc' => array('authority_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('authority_id', false);
		
		if(!empty($data['search'])){
			if( isset($data['search']['authority_id']) && is_string($data['search']['authority_id']) ){
				$config["where"][] = array('[and] a.authority_id=[+]', $data['search']['authority_id']);
			}
			
			if (isset($data['search']['authority_name']) && is_string($data['search']['authority_name'])) {
                $config['where'][] = array('[and] a.authority_name LIKE "%[-]%"', $data['search']['authority_name']);
            }

			if( isset($data['search']['module_id']) && is_string($data['search']['module_id']) ){
				$config["where"][] = array('[and] a.module_id=[+]', $data['search']['module_id']);
			}
			
		}
		
		return object(parent::TABLE_AUTHORITY)->select_page($config);
	}
	
	
	
	
			
	/**
	 * 获取一条数据
	 * $data = arrray(
	 * 	authority_id 权限ID
	 * )
	 * 
	 * ADMINISTRATORADMINAUTHORITYGET
	 * {"class":"administrator/admin_authority","method":"api_get"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_get( $data = array() ){
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_AUTHORITY_READ);
		object(parent::ERROR)->check($data, 'authority_id', parent::TABLE_AUTHORITY, array('args'));
		
		$get_data = object(parent::TABLE_AUTHORITY)->find($data['authority_id']);
		if( empty($get_data) ){
			throw new error("权限不存在");
		}
		
		return $get_data;
	}
	
	
	
	
	
		
	
	/**
	 * 根据模块获取权限选项列表
	 *  $data = array(
	 * 	"module_id" => "" 模块ID如果为空，则获取所有的模块权限
	 * 	'sort' => ["sort_asc", "update_time_desc"] 排序
	 * );
	 * 
	 * 
	 * ADMINISTRATORADMINAUTHORITYMODULEOPTION
	 * {"class":"administrator/admin_authority","method":"api_module_option"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_module_option($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check();
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => array(),
		);
		
		$module_config = $config;
		if( isset($data['module_id']) ){
			object(parent::ERROR)->check($data, 'module_id', parent::TABLE_MODULE, array('args'));
			$module_config['where'][] = array('module_id=[+]', $data['module_id']);
		}else{
			$module_config["orderby"] = object(parent::REQUEST)->orderby($data, array(
				'insert_time_desc' => array('module_insert_time', true),
				'insert_time_asc' => array('module_insert_time', false),
				'update_time_desc' => array('module_update_time', true),
				'update_time_asc' => array('module_update_time', false),
				'sort_desc' => array('module_sort', true),
				'sort_asc' => array('module_sort', false),
			));
			
			//避免排序重复
			$module_config["orderby"][] = array('module_id', false);
		}
		
		$module_data = object(parent::TABLE_MODULE)->select($module_config);
		if( empty($module_data) ){
			return array();
		}
		
		$module_ids = array();
		foreach($module_data as $key => $value){
			$module_ids[] = $value['module_id'];
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
		
		if( !empty($module_ids) ){
			$module_in_string = "\"".implode("\",\"", $module_ids)."\",\"\"";//还要加上空格
			$authority_config['where'][] = array('[and] module_id IN([-])', $module_in_string, true);
		}
		
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
		}
		
		
		/*foreach($module_data as $key => $value){
			$temp_authority_config = array();
			$module_data[$key]['authority'] = array();
			if( empty($value['module_id']) ){
				continue;
			}
			$temp_authority_config = $authority_config;
			$temp_authority_config['where'][] = array('module_id=[+]', $value['module_id']);
			$module_data[$key]['authority'] = object(parent::TABLE_AUTHORITY)->select($temp_authority_config);
		}*/
		
		return $module_data;
	}
	
	
	
	
	
	
	
	
	
	
	
	
}
?>