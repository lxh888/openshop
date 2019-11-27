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
class admin_type extends \eapie\source\request\softstore {
	
	
	
	/**
	 * 删除分类
	 */
	public function api_remove( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_TYPE_REMOVE);
		//数据检测 
		object(parent::ERROR)->check($data, 'ss_type_id', parent::TABLE_SOFTSTORE_TYPE, array('args'));
		//存在下级则无法删除
		if( object(parent::TABLE_SOFTSTORE_TYPE)->find_exists_son_id($data['ss_type_id']) ){
			throw new error("该分类下存在子级，请先清理子级才能删除该分类");
			}
		
		if( object(parent::TABLE_SOFTSTORE_TYPE)->remove($data['ss_type_id']) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $data);
			return $data['ss_type_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	/**
	 * 检查编辑的权限
	 * 
	 * @param	void
	 * @return  bool
	 */
	public function api_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_TYPE_EDIT);
		return true;
	}
	
	
	/**
	 * 编辑分类
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_TYPE_EDIT);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'ss_type_id', parent::TABLE_SOFTSTORE_TYPE, array('args'));
		
		if( isset($data['ss_type_name']) )
		object(parent::ERROR)->check($data, 'ss_type_name', parent::TABLE_SOFTSTORE_TYPE, array('args', 'length'));
		if( isset($data['ss_type_parent_id']) )
		object(parent::ERROR)->check($data, 'ss_type_parent_id', parent::TABLE_SOFTSTORE_TYPE, array('args'));
		if( isset($data['ss_type_info']) )
		object(parent::ERROR)->check($data, 'ss_type_info', parent::TABLE_SOFTSTORE_TYPE, array('args'));
		if( isset($data['ss_type_sort']) )
		object(parent::ERROR)->check($data, 'ss_type_sort', parent::TABLE_SOFTSTORE_TYPE, array('args'));
		if( isset($data['ss_type_state']) )
		object(parent::ERROR)->check($data, 'ss_type_state', parent::TABLE_SOFTSTORE_TYPE, array('args'));
		
		//获取旧数据
		$ss_type_data = object(parent::TABLE_SOFTSTORE_TYPE)->find($data['ss_type_id']);
		if( empty($ss_type_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'ss_type_parent_id', 
			'ss_type_name', 
			'ss_type_info',
			'ss_type_sort',
			'ss_type_state',
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($update_data as $key => $value){
			if( isset($ss_type_data[$key]) ){
				if($ss_type_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		//父级不能是自己
		if( !empty($update_data["ss_type_parent_id"]) ){
			if( $update_data["ss_type_parent_id"] == $data['ss_type_id'] ){
				throw new error("不能将自己设为父级");
			}
			
			//判断该分类是否存在、是否为顶级分类
			$parent_data = object(parent::TABLE_SOFTSTORE_TYPE)->find($update_data['ss_type_parent_id']);
			if( empty($parent_data) ){
				throw new error("父级ID有误，数据不存在");
			}
			
			if( !empty($parent_data['ss_type_parent_id']) ){
				throw new error("所编辑的父级并不是顶级分类");
			}
			
		}
		
		//如果父级 编辑成 子级，必须要去掉子级
		if( empty($ss_type_data["ss_type_parent_id"]) &&
		isset($update_data["ss_type_parent_id"]) ){
			if( object(parent::TABLE_SOFTSTORE_TYPE)->find_exists_son_id($data['ss_type_id']) ){
				throw new error("该分类下存在子级，请先清理子级才能变更该分类的父级");
			}
		}
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		//更新时间
		$update_data['ss_type_update_time'] = time();
		if( object(parent::TABLE_SOFTSTORE_TYPE)->update( array(array('ss_type_id=[+]', (string)$data['ss_type_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['ss_type_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	/**
	 * 添加分类
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_TYPE_ADD);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'ss_type_name', parent::TABLE_SOFTSTORE_TYPE, array('args', 'length'));
		object(parent::ERROR)->check($data, 'ss_type_parent_id', parent::TABLE_SOFTSTORE_TYPE, array('args'));
		object(parent::ERROR)->check($data, 'ss_type_info', parent::TABLE_SOFTSTORE_TYPE, array('args'));
		object(parent::ERROR)->check($data, 'ss_type_sort', parent::TABLE_SOFTSTORE_TYPE, array('args'));
		object(parent::ERROR)->check($data, 'ss_type_state', parent::TABLE_SOFTSTORE_TYPE, array('args'));
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'ss_type_id', 
			'ss_type_parent_id', 
			'ss_type_name', 
			'ss_type_info',
			'ss_type_sort',
			'ss_type_state',
			'ss_type_insert_time',
			'ss_type_update_time'
			);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		//判断该分类是否存在、是否为顶级分类
		if( !empty($insert_data["ss_type_parent_id"]) ){
			$parent_data = object(parent::TABLE_SOFTSTORE_TYPE)->find($insert_data['ss_type_parent_id']);
			if( empty($parent_data) ){
				throw new error("父级ID有误，数据不存在");
			}
			if( !empty($parent_data['ss_type_parent_id']) ){
				throw new error("所编辑的父级并不是顶级分类");
			}
		}
		
		//获取id号
		$insert_data['ss_type_id'] = object(parent::TABLE_SOFTSTORE_TYPE)->get_unique_id();
		//用户数据
		$insert_data['user_id'] = $_SESSION['user_id'];
		//创建时间
		$insert_data['ss_type_insert_time'] = time();
		//更新时间
		$insert_data['ss_type_update_time'] = time();
		
		if( object(parent::TABLE_SOFTSTORE_TYPE)->insert($insert_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $insert_data);
			return $insert_data['ss_type_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	/**
	 * 获取分类选项列表
	 *  $data = array(
	 * 	"type" => "son" 只获取子级，为空获取父级
	 * 	'sort' => ["sort_asc", "name_desc"] 排序
	 * );
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
		
		$parent_config = $config;
		$son_config = $config;
		
		if( !empty($data["type"]) && $data["type"] == "son" ){
			$parent_config["where"][] = array('[and] ss_type_parent_id<>""');
		}else{
			$parent_config["where"][] = array("ss_type_parent_id=\"\"");
		}
		
		//前台的这里应该是需要判断状态的，第三个参数为true，表示不拿缓存的
		return object(parent::TABLE_SOFTSTORE_TYPE)->select_parent_son_all($parent_config, $son_config);
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
	 * @param	array	$config
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_TYPE_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'name_desc' => array('ss_type_name', true),
			'name_asc' => array('ss_type_name', false),
			'state_desc' => array('ss_type_state', true),
			'state_asc' =>  array('ss_type_state', false),
			'insert_time_desc' => array('ss_type_insert_time', true),
			'insert_time_asc' => array('ss_type_insert_time', false),
			'update_time_desc' => array('ss_type_update_time', true),
			'update_time_asc' => array('ss_type_update_time', false),
			'parent_desc' => array('ss_type_parent_id', true),
			'parent_asc' => array('ss_type_parent_id', false),
			'sort_desc' => array('ss_type_sort', true),
			'sort_asc' => array('ss_type_sort', false),
			'son_desc' => array('ss_type_son_count', true),
			'son_asc' => array('ss_type_son_count', false)
		));
		
		//避免排序重复
		$config["orderby"][] = array('ss_type_id', false);
		
		if(!empty($data['search'])){
			if( isset($data['search']['id']) && is_string($data['search']['id']) ){
					$config["where"][] = array('[and] st.ss_type_id=[+]', $data['search']['id']);
				}else
			if( isset($data['search']['parent_id']) && is_string($data['search']['parent_id']) ){
					$config["where"][] = array('[and] st.ss_type_parent_id=[+]', $data['search']['parent_id']);
				}else
			if( isset($data['search']['type']) && is_string($data['search']['type']) ){
				if($data['search']['type'] == "parent"){
					$config["where"][] = array('[and] st.ss_type_parent_id=""');
				}else if($data['search']['type'] == "son"){
					$config["where"][] = array('[and] st.ss_type_parent_id<>""');
					}
				}
		}
		
		
		return object(parent::TABLE_SOFTSTORE_TYPE)->select_page($config);
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
}
?>