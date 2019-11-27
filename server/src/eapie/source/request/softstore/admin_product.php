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
class admin_product extends \eapie\source\request\softstore {
	
	
	
	/**
	 * 检查编辑的权限
	 * 
	 * @param	void
	 * @return  bool
	 */
	public function api_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_EDIT);
		return true;
	}
	
	
	
	/**
	 * 编辑产品
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_EDIT);
		
		//数据检测
		object(parent::ERROR)->check($data, 'ss_product_id', parent::TABLE_SOFTSTORE_PRODUCT, array('args'));
		if( isset($data['ss_product_name']) )
		object(parent::ERROR)->check($data, 'ss_product_name', parent::TABLE_SOFTSTORE_PRODUCT, array('args', 'length'));
		if( isset($data['ss_product_info']) )
		object(parent::ERROR)->check($data, 'ss_product_info', parent::TABLE_SOFTSTORE_PRODUCT, array('args'));
		if( isset($data['ss_product_source']) )
		object(parent::ERROR)->check($data, 'ss_product_source', parent::TABLE_SOFTSTORE_PRODUCT, array('args'));
		if( isset($data['ss_product_details']) )
		object(parent::ERROR)->check($data, 'ss_product_details', parent::TABLE_SOFTSTORE_PRODUCT, array('args'));
		if( isset($data['ss_product_sort']) )
		object(parent::ERROR)->check($data, 'ss_product_sort', parent::TABLE_SOFTSTORE_PRODUCT, array('args'));
		if( isset($data['ss_product_state']) )
		object(parent::ERROR)->check($data, 'ss_product_state', parent::TABLE_SOFTSTORE_PRODUCT, array('args'));
		
		//获取旧数据
		$ss_product_data = object(parent::TABLE_SOFTSTORE_PRODUCT)->find($data['ss_product_id']);
		if( empty($ss_product_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'ss_product_name', 
			'ss_product_info', 
			'ss_product_source',
			'ss_product_details', 
			'ss_product_sort',
			'ss_product_state',
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($update_data as $key => $value){
			if( isset($ss_product_data[$key]) ){
				if($ss_product_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		//更新时间
		$update_data['ss_product_update_time'] = time();
		if( object(parent::TABLE_SOFTSTORE_PRODUCT)->update( array(array('ss_product_id=[+]', (string)$data['ss_product_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['ss_product_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	/**
	 * 逻辑回收产品
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_trash($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_TRASH);
		object(parent::ERROR)->check($data, 'ss_product_id', parent::TABLE_SOFTSTORE_PRODUCT, array('args'));
		
		//获取旧数据
		$ss_product_data = object(parent::TABLE_SOFTSTORE_PRODUCT)->find($data['ss_product_id']);
		if( empty($ss_product_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		if( !empty($ss_product_data["ss_product_trash"]) ){
			throw new error("该产品已经在回收站");
		}
		
		//更新回收状态
		$update_data["ss_product_trash"] = 1;
		$update_data['ss_product_trash_time'] = time();
		if( object(parent::TABLE_SOFTSTORE_PRODUCT)->update( array(array('ss_product_id=[+]', (string)$data['ss_product_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['ss_product_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	/**
	 * 添加产品
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_ADD);
		
		//数据检测
		object(parent::ERROR)->check($data, 'ss_product_name', parent::TABLE_SOFTSTORE_PRODUCT, array('args', 'length'));
		object(parent::ERROR)->check($data, 'ss_product_info', parent::TABLE_SOFTSTORE_PRODUCT, array('args'));
		object(parent::ERROR)->check($data, 'ss_product_source', parent::TABLE_SOFTSTORE_PRODUCT, array('args'));
		object(parent::ERROR)->check($data, 'ss_product_sort', parent::TABLE_SOFTSTORE_PRODUCT, array('args'));
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'user_id', 
			'ss_product_name', 
			'ss_product_info',
			'ss_product_source',
			'ss_product_sort',
			);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		//获取id号
		$insert_data['ss_product_id'] = object(parent::TABLE_SOFTSTORE_PRODUCT)->get_unique_id();
		//创建时间
		$insert_data['ss_product_insert_time'] = time();
		//更新时间
		$insert_data['ss_product_update_time'] = time();
		//用户id
		$insert_data['user_id'] = $_SESSION['user_id'];
		
		if( object(parent::TABLE_SOFTSTORE_PRODUCT)->insert($insert_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $insert_data);
			return $insert_data['ss_product_id'];
		}else{
			throw new error("操作失败");
		}
		
		
	}
	
	
	
	
	/**
	 * 获取一条数据
	 * $data = arrray(
	 * 	ss_product_id 产品ID
	 * )
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_get($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_READ);
		object(parent::ERROR)->check($data, 'ss_product_id', parent::TABLE_SOFTSTORE_PRODUCT, array('args'));
		$get_data = object(parent::TABLE_SOFTSTORE_PRODUCT)->find($data['ss_product_id']);
		if( !empty($get_data) ){
			$data = array($get_data);
			$data = object(parent::TABLE_SOFTSTORE_PRODUCT)->get_additional_data($data);
			$get_data = $data[0];
		}
		return $get_data;
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
	 * @param	array	$data
	 * @return	array
	 */
	public function api_trash_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_TRASH_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'name_desc' => array('ss_product_name', true),
			'name_asc' => array('ss_product_name', false),
			'state_desc' => array('ss_product_state', true),
			'state_asc' => array('ss_product_state', false),
			'trash_time_desc' => array('ss_product_trash_time', true),
			'trash_time_asc' => array('ss_product_trash_time', false),
			'update_time_desc' => array('ss_product_update_time', true),
			'update_time_asc' => array('ss_product_update_time', false),
			'sort_desc' => array('ss_product_sort', true),
			'sort_asc' => array('ss_product_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('ss_product_id', false);
		
		$config["where"][] = array('[and] sp.ss_product_trash=1');
		if(!empty($data['search'])){
			if( isset($data['search']['id']) && is_string($data['search']['id']) ){
				$config["where"][] = array('[and] sp.ss_product_id=[+]', $data['search']['id']);
				}
		}
		
		return object(parent::TABLE_SOFTSTORE_PRODUCT)->select_page($config);
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
	 * @param	array	$data
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'name_desc' => array('ss_product_name', true),
			'name_asc' => array('ss_product_name', false),
			'state_desc' => array('ss_product_state', true),
			'state_asc' => array('ss_product_state', false),
			'insert_time_desc' => array('ss_product_insert_time', true),
			'insert_time_asc' => array('ss_product_insert_time', false),
			'update_time_desc' => array('ss_product_update_time', true),
			'update_time_asc' => array('ss_product_update_time', false),
			'sort_desc' => array('ss_product_sort', true),
			'sort_asc' => array('ss_product_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('ss_product_id', false);
		
		
		$config["where"][] = array('[and] sp.ss_product_trash=0');
		if(!empty($data['search'])){
			if( isset($data['search']['id']) && is_string($data['search']['id']) ){
				$config["where"][] = array('[and] sp.ss_product_id=[+]', $data['search']['id']);
				}
			if( isset($data['search']['state']) && 
			(is_string($data['search']['state']) || is_numeric($data['search']['state'])) &&
			in_array($data['search']['state'], array("0", "1", "2", "3")) ){
				$config["where"][] = array('[and] sp.ss_product_state=[+]', $data['search']['state']);
				}
			//ss_type_id 分类ID 从 ss_product_type 查询 获得 ss_product 产品ID 然后in()
			
		}
		
		$data = object(parent::TABLE_SOFTSTORE_PRODUCT)->select_page($config);
		if( !empty($data["data"]) ){
			$data["data"] = object(parent::TABLE_SOFTSTORE_PRODUCT)->get_additional_data($data["data"]);
		}
		
		return $data;
	}
	
	
	
	
	
	
	
	/**
	 * 获取产品规格属性的所有父级与子级关系列表
	 *  $data = array(
	 * 	"type" => "son" 只获取子级，为空获取父级
	 * 	'sort' => ["sort_asc", "name_desc"] 排序
	 * );
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_attr_option($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => array(),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'name_desc' => array('ss_product_attr_name', true),
			'name_asc' => array('ss_product_attr_name', false),
			'insert_time_desc' => array('ss_product_attr_insert_time', true),
			'insert_time_asc' => array('ss_product_attr_insert_time', false),
			'update_time_desc' => array('ss_product_attr_update_time', true),
			'update_time_asc' => array('ss_product_attr_update_time', false),
			'sort_desc' => array('ss_product_attr_sort', true),
			'sort_asc' => array('ss_product_attr_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('ss_product_attr_id', false);
		
		
		if(!empty($data['search'])){
			if( isset($data['search']['id']) && is_string($data['search']['id']) ){
				$config["where"][] = array('[and] ss_product_attr_id=[+]', $data['search']['id']);
				}
			
			if( isset($data['search']['parent_id']) && is_string($data['search']['parent_id']) ){
				$config["where"][] = array('[and] ss_product_attr_parent_id=[+]', $data['search']['parent_id']);
				}
			
			if( isset($data['search']['product_id']) && is_string($data['search']['product_id']) ){
				$config["where"][] = array('[and] ss_product_id=[+]', $data['search']['product_id']);
				}

		}
		
		$parent_config = $config;
		$son_config = $config;
		
		if( !empty($data["type"]) && $data["type"] == "son" ){
			$parent_config["where"][] = array('[and] ss_product_attr_parent_id<>""');
		}else{
			$parent_config["where"][] = array("[and] ss_product_attr_parent_id=\"\"");
		}
		
		//前台的这里应该是需要判断状态的，第三个参数为true，表示不拿缓存的
		return object(parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE)->select_parent_son_all($parent_config, $son_config);
		
	}
	
	
	
	
	/**
	 * 删除规格属性
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_attr_remove($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_ATTRIBUTE_REMOVE);
		//数据检测 
		object(parent::ERROR)->check($data, 'ss_product_attr_id', parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE, array('args'));
		
		//获取旧数据
		$ss_product_attr_data = object(parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE)->find($data['ss_product_attr_id']);
		if( empty($ss_product_attr_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//存在下级则无法删除
		if( object(parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE)->find_exists_son_id($data['ss_product_attr_id']) ){
			throw new error("该产品规格属性下存在子级，请先清理子级才能删除该产品规格属性");
			}
		
		if( object(parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE)->remove($data['ss_product_attr_id']) ){
			
			//更新产品修改时间
			object(parent::TABLE_SOFTSTORE_PRODUCT)->update( 
				array( array('ss_product_id=[+]', $ss_product_attr_data['ss_product_id']) ), 
				array('ss_product_update_time' => time() ) 
			);
			
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $ss_product_attr_data);
			return $data['ss_product_attr_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	/**
	 * 编辑规格属性的权限检测
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_attr_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_ATTRIBUTE_EDIT);
		return true;
	}
	
	
	/**
	 * 编辑规格属性
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_attr_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_ATTRIBUTE_EDIT);
		//数据检测 
		object(parent::ERROR)->check($data, 'ss_product_attr_id', parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE, array('args'));
		if( isset($data['ss_product_id']) )
		object(parent::ERROR)->check($data, 'ss_product_id', parent::TABLE_SOFTSTORE_PRODUCT, array('args', 'exists_id'));
		if( isset($data['ss_product_attr_name']) )
		object(parent::ERROR)->check($data, 'ss_product_attr_name', parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE, array('args', 'length'));
		if( isset($data['ss_product_attr_parent_id']) )
		object(parent::ERROR)->check($data, 'ss_product_attr_parent_id', parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE, array('args'));
		if( isset($data['ss_product_attr_info']) )
		object(parent::ERROR)->check($data, 'ss_product_attr_info', parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE, array('args'));
		if( isset($data['ss_product_attr_sort']) )
		object(parent::ERROR)->check($data, 'ss_product_attr_sort', parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE, array('args'));
		if( isset($data['ss_product_attr_required']) )
		object(parent::ERROR)->check($data, 'ss_product_attr_required', parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE, array('args'));
		if( isset($data['ss_product_attr_stock']) )
		object(parent::ERROR)->check($data, 'ss_product_attr_stock', parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE, array('args'));
		if( isset($data['ss_product_attr_money']) )
		object(parent::ERROR)->check($data, 'ss_product_attr_money', parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE, array('args'));
		
		//获取旧数据
		$ss_product_attr_data = object(parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE)->find($data['ss_product_attr_id']);
		if( empty($ss_product_attr_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'ss_product_id', 
			'ss_product_attr_parent_id', 
			'ss_product_attr_name', 
			'ss_product_attr_info',
			'ss_product_attr_sort',
			'ss_product_attr_required',
			'ss_product_attr_stock',
			'ss_product_attr_money'
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($update_data as $key => $value){
			if( isset($ss_product_attr_data[$key]) ){
				if($ss_product_attr_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		
		//父级不能是自己
		if( !empty($update_data["ss_product_attr_parent_id"]) ){
			if( $update_data["ss_product_attr_parent_id"] == $data['ss_product_attr_id'] ){
				throw new error("不能将自己设为父级");
			}
			
			//判断该分类是否存在、是否为顶级分类
			$parent_data = object(parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE)->find($update_data['ss_product_attr_parent_id']);
			if( empty($parent_data) ){
				throw new error("父级ID有误，数据不存在");
			}
			
			if( !empty($parent_data['ss_product_attr_parent_id']) ){
				throw new error("所编辑的父级并不是顶级的产品规格属性");
			}
			
		}
		
		
		//如果父级 编辑成 子级，必须要去掉子级
		if( empty($ss_product_attr_data["ss_product_attr_parent_id"]) &&
		isset($update_data["ss_product_attr_parent_id"]) ){
			if( object(parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE)->find_exists_son_id($data['ss_product_attr_id']) ){
				throw new error("该产品规格属性下存在子级，请先清理子级才能变更该产品规格属性的父级");
			}
		}
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		
		//更新时间
		$update_data['ss_product_attr_update_time'] = time();
		if( object(parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE)->update( array(array('ss_product_attr_id=[+]', (string)$data['ss_product_attr_id'])), $update_data) ){
			
			//更新产品修改时间
			object(parent::TABLE_SOFTSTORE_PRODUCT)->update( 
				array( array('ss_product_id=[+]', $ss_product_attr_data['ss_product_id']) ), 
				array('ss_product_update_time' => time() ) 
			);	
				
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['ss_product_attr_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
	/**
	 * 产品属性的添加
	 * $data = array(
	 * 	'ss_product_id' => string 产品ID
	 * 	
	 * )
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_attr_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_ATTRIBUTE_ADD);
		//数据检测
		object(parent::ERROR)->check($data, 'ss_product_id', parent::TABLE_SOFTSTORE_PRODUCT, array('args', 'exists_id'));
		object(parent::ERROR)->check($data, 'ss_product_attr_name', parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE, array('args', 'length'));
		object(parent::ERROR)->check($data, 'ss_product_attr_parent_id', parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE, array('args'));
		object(parent::ERROR)->check($data, 'ss_product_attr_info', parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE, array('args'));
		object(parent::ERROR)->check($data, 'ss_product_attr_sort', parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE, array('args'));
		object(parent::ERROR)->check($data, 'ss_product_attr_required', parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE, array('args'));
		object(parent::ERROR)->check($data, 'ss_product_attr_stock', parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE, array('args'));
		object(parent::ERROR)->check($data, 'ss_product_attr_money', parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE, array('args'));
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'ss_product_id', 
			'ss_product_attr_parent_id', 
			'ss_product_attr_name', 
			'ss_product_attr_info',
			'ss_product_attr_sort',
			'ss_product_attr_required',
			'ss_product_attr_stock',
			'ss_product_attr_money'
			);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		//判断该分类是否存在、是否为顶级分类
		if( !empty($insert_data["ss_product_attr_parent_id"]) ){
			$parent_data = object(parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE)->find($insert_data['ss_product_attr_parent_id']);
			if( empty($parent_data) ){
				throw new error("父级ID有误，数据不存在");
			}
			if( !empty($parent_data['ss_product_attr_parent_id']) ){
				throw new error("所编辑的父级并不是顶级分类");
			}
		}
		
		
		//获取id号
		$insert_data['ss_product_attr_id'] = object(parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE)->get_unique_id();
		//用户数据
		$insert_data['user_id'] = $_SESSION['user_id'];
		//创建时间
		$insert_data['ss_product_attr_insert_time'] = time();
		//更新时间
		$insert_data['ss_product_attr_update_time'] = time();
		
		if( object(parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE)->insert($insert_data) ){
			
			//更新产品修改时间
			object(parent::TABLE_SOFTSTORE_PRODUCT)->update( 
				array( array('ss_product_id=[+]', $data['ss_product_id']) ), 
				array('ss_product_update_time' => time() ) 
			);	
			
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $insert_data);
			return $insert_data['ss_product_attr_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
	
	
	
	
	
	/**
	 * 产品分类的编辑
	 * $data = array(
	 * 	'ss_product_id' => string 产品ID
	 * 	'ss_type_id' => array 索引数组
	 * )
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_type_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_TYPE_EDIT);
		//检查参数
		object(parent::ERROR)->check($data, 'ss_product_id', parent::TABLE_SOFTSTORE_PRODUCT, array('args', 'exists_id'));
		
		if( !empty($data['ss_type_id']) && is_array($data['ss_type_id'])){
			//清理数据
			$ss_type_id = array();
			foreach($data['ss_type_id'] as $value){
				if(is_string($value) || is_numeric($value)){
					$ss_type_id[] = cmd(array($value), 'str addslashes');
				}
			}
		}
		
		if( !empty($ss_type_id) ){
			//获取分类数据
			$in_string = "\"".implode("\",\"", $ss_type_id)."\"";
			$type_where = array();
			$type_where[] = array("ss_type_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
			//获取产品分类数据
			$type_data = object(parent::TABLE_SOFTSTORE_TYPE)->select(array("where"=>$type_where));
		}
		
		//获取产品的旧分类数据
		$product_type_data = object(parent::TABLE_SOFTSTORE_PRODUCT_TYPE)->select(array(
			"where" => array( array("ss_product_id=[+]", (string)$data['ss_product_id']) )
		));
		
		//获得清理数据。1）需要删除的产品分类ID   2）需要增加的分类ID
		$clear_data = $data;
		$clear_data["ss_type_id"] = array();
		$clear_data["delete"] = array();
		$clear_data["insert"] = array();
		
		//先收集 旧数据，假设都需要被删除
		if( !empty($product_type_data) ){
			foreach($product_type_data as $value){
				$clear_data["delete"][$value["ss_type_id"]] = $value["ss_product_type_id"];
			}
		}
		
		//进行筛选
		if( !empty($type_data) ){
			foreach($type_data as $type_value){
				$clear_data["ss_type_id"][] = $type_value["ss_type_id"];
				if( isset($clear_data["delete"][$type_value["ss_type_id"]]) ){
					unset($clear_data["delete"][$type_value["ss_type_id"]]);
				}else{
					//这里就是需要增加的产品分类
					//$type_value["ss_type_id"];
					$insert_data = array(
						"ss_product_type_id" => object(parent::TABLE_SOFTSTORE_PRODUCT_TYPE)->get_unique_id(),
						"ss_product_id" => $data['ss_product_id'],
						"ss_type_id" => $type_value["ss_type_id"],
						"user_id" => $_SESSION["user_id"],
						"ss_product_type_time" => time()
					);
					$clear_data["insert"][] = $insert_data;
					object(parent::TABLE_SOFTSTORE_PRODUCT_TYPE)->insert($insert_data);
				}
			}
		}
		
		//再删除
		if( !empty($clear_data["delete"]) ){
			$in_string = "\"".implode("\",\"", $clear_data["delete"])."\"";
			//是不加单引号并且强制不过滤
			object(parent::TABLE_SOFTSTORE_PRODUCT_TYPE)->delete(array( array("ss_product_type_id IN([-])", $in_string, true) ));
		}
		
		if( empty($clear_data["insert"]) && empty($clear_data["delete"]) ){
			throw new error("没有需要更新的数据");
		}
		
		//更新产品修改时间
		object(parent::TABLE_SOFTSTORE_PRODUCT)->update( 
			array( array('ss_product_id=[+]', $data['ss_product_id']) ), 
			array('ss_product_update_time' => time() ) 
		);	
		
		//插入操作日志
		object(parent::TABLE_ADMIN_LOG)->insert($data, $clear_data);
		
		return true;
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
	public function api_image_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'sort_desc' => array('ss_product_image_sort', true),
			'sort_asc' => array('ss_product_image_sort', false),
			'name_desc' => array('ss_product_image_name', true),
			'name_asc' => array('ss_product_image_name', false),
			'type_desc' => array('ss_product_image_type', true),
			'type_asc' => array('ss_product_image_type', false),
			'size_desc' => array('ss_product_image_size', true),
			'size_asc' => array('ss_product_image_size', false),
			'main_desc' => array('ss_product_image_main', true),
			'main_asc' => array('ss_product_image_main', false),
			'state_desc' => array('ss_product_image_state', true),
			'state_asc' => array('ss_product_image_state', false),
			'insert_time_desc' => array('ss_product_image_insert_time', true),
			'insert_time_asc' => array('ss_product_image_insert_time', false),
			'update_time_desc' => array('ss_product_image_update_time', true),
			'update_time_asc' => array('ss_product_image_update_time', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('ss_product_image_id', false);
		
		if(!empty($data['search'])){
			if( isset($data['search']['ss_product_id']) && is_string($data['search']['ss_product_id']) ){
				$config["where"][] = array('[and] spi.ss_product_id=[+]', $data['search']['ss_product_id']);
				}
		}
		
		
		return object(parent::TABLE_SOFTSTORE_PRODUCT_IMAGE)->select_page($config);
	}
	
	
	
	/**
	 * 检查编辑图片的权限
	 * 
	 * @param	void
	 * @return  bool
	 */
	public function api_image_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_IMAGE_EDIT);
		return true;
	}
	
	
	
	/**
	 * 编辑图片信息
	 * 
	 * @param	array	$data
	 * @return  bool
	 */
	public function api_image_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_IMAGE_EDIT);
		//检查数据
		object(parent::ERROR)->check($data, 'ss_product_image_id', parent::TABLE_SOFTSTORE_PRODUCT_IMAGE, array('args'));
		if( isset($data['ss_product_image_name']) )
		object(parent::ERROR)->check($data, 'ss_product_image_name', parent::TABLE_SOFTSTORE_PRODUCT_IMAGE, array('args', 'length'));
		if( isset($data['ss_product_image_sort']) )
		object(parent::ERROR)->check($data, 'ss_product_image_sort', parent::TABLE_SOFTSTORE_PRODUCT_IMAGE, array('args'));
		if( isset($data['ss_product_image_main']) )
		object(parent::ERROR)->check($data, 'ss_product_image_main', parent::TABLE_SOFTSTORE_PRODUCT_IMAGE, array('args'));
		
		//获取旧数据
		$ss_product_image_data = object(parent::TABLE_SOFTSTORE_PRODUCT_IMAGE)->find($data['ss_product_image_id']);
		if( empty($ss_product_image_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'ss_product_image_name', 
			'ss_product_image_sort', 
			'ss_product_image_main',
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($update_data as $key => $value){
			if( isset($ss_product_image_data[$key]) ){
				if($ss_product_image_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		$update_data['ss_product_image_update_time'] = time();
		//更新
		if( object(parent::TABLE_SOFTSTORE_PRODUCT_IMAGE)->update( array(array('ss_product_image_id=[+]', (string)$data['ss_product_image_id'])), $update_data) ){
				
			//更新产品修改时间
			object(parent::TABLE_SOFTSTORE_PRODUCT)->update( 
				array( array('ss_product_id=[+]', $ss_product_image_data['ss_product_id']) ), 
				array('ss_product_update_time' => time() ) 
			);	
				
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['ss_product_image_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	/**
	 * 设为主图
	 * 
	 * @param	array	$data
	 * @return bool
	 */
	public function api_image_edit_main($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_IMAGE_EDIT);
		//检查数据
		object(parent::ERROR)->check($data, 'ss_product_image_id', parent::TABLE_SOFTSTORE_PRODUCT_IMAGE, array('args'));
		
		//获取旧数据
		$ss_product_image_data = object(parent::TABLE_SOFTSTORE_PRODUCT_IMAGE)->find($data['ss_product_image_id']);
		if( empty($ss_product_image_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		if( !empty($ss_product_image_data["ss_product_image_main"]) ){
			throw new error("该产品图片已经是主图了");
		}
		
		$update_data = array();
		$update_data["ss_product_image_main"] = 1;
		$update_data['ss_product_image_update_time'] = time();
		
		//更新
		if( object(parent::TABLE_SOFTSTORE_PRODUCT_IMAGE)->update( array(array('ss_product_image_id=[+]', (string)$data['ss_product_image_id'])), $update_data) ){
			//将其他图片设为 非主图
			object(parent::TABLE_SOFTSTORE_PRODUCT_IMAGE)->update( 
				array(
					array('ss_product_image_id<>[+]', (string)$data['ss_product_image_id']),
					array('[and] ss_product_id=[+]', (string)$ss_product_image_data['ss_product_id']),
					array('[and] ss_product_image_main<>0'),
				), 
				array("ss_product_image_main" => 0, 'ss_product_image_update_time' => time() )
			);
			
			//更新产品修改时间
			object(parent::TABLE_SOFTSTORE_PRODUCT)->update( 
				array( array('ss_product_id=[+]', $ss_product_image_data['ss_product_id']) ), 
				array('ss_product_update_time' => time() ) 
			);
			
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['ss_product_image_id'];
		}else{
			throw new error("操作失败");
		}
		
		
	}
	
	
	/**
	 * 获取 产品图片 上传的token
	 * $data = array(
	 * 	"ss_product_id" 产品ID
	 * 	"file_name"	文件的原名字
	 *  "file_size"	文件的大小
	 *  "file_type"  文件的类型
	 * )
	 * 
	 * 返回值：
	 * array(
	 * 	"qiniu_uptoken" 七牛的上传token
	 * 	"key" 			图片ID
	 * )
	 * 
	 * @param	array	$data
	 * @return array
	 */
	public function api_image_qiniu_uptoken($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_IMAGE_UPLOAD);
		object(parent::ERROR)->check($data, 'ss_product_id', parent::TABLE_SOFTSTORE_PRODUCT, array('args', 'exists_id'));
		
		if( empty($data["file_name"]) || (!is_string($data["file_name"]) && !is_numeric($data["file_name"])) ){
			$data["file_name"] = "";
		}
		
		if( empty($data["file_type"]) ){
			throw new error("产品图片的类型不能为空");
		}
		//判断文件类型是否合法
		$file_type = array('image/jpeg','image/pjpeg','image/png','image/x-png','image/gif', 'image/bmp');
		if( !is_string($data["file_type"]) || !in_array($data["file_type"], $file_type)){
			throw new error("产品图片的类型不合法");
		}
		
		if( empty($data["file_size"]) || !is_numeric($data["file_size"]) ){
			throw new error("产品图片的大小不合法");
		}
		
		//获取配置
		$qiniu_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("qiniu_access"), true);
		if( empty($qiniu_config["bucket"]) ){
			throw new error("配置异常");
		}
		
		$insert_data = array(
			"ss_product_image_storage" => "qiniu",
			"ss_product_image_id" => object(parent::TABLE_SOFTSTORE_PRODUCT_IMAGE)->get_unique_id(),
			"user_id" => $_SESSION['user_id'],
			"ss_product_id" => $data["ss_product_id"],
			"ss_product_image_path" => $qiniu_config["bucket"],//储存的空间
			"ss_product_image_name" => $data["file_name"],
			"ss_product_image_type" => $data["file_type"],
			"ss_product_image_size" => $data["file_size"],
			"ss_product_image_state" => 0,
			"ss_product_image_insert_time" => time(),
			"ss_product_image_update_time" => time(),
		);
		
		//生成一个图片表数据
		if( !object(parent::TABLE_SOFTSTORE_PRODUCT_IMAGE)->insert($insert_data) ){
			throw new error("产品图片登记失败");
		}
		
		//根据文件大小，设置有效时间
		$qiniu_config["expires"] = 3600; //一个小时
		$qiniu_config["policy"] = array(
				'returnBody' => '{"key":"$(key)","hash":"$(etag)","type":$(mimeType),"size":$(fsize),"name":$(fname),"bucket":"$(bucket)","width":"$(imageInfo.width)","height":"$(imageInfo.height)","format":"$(imageInfo.format)"}',
				//限定用户上传的文件类型。
				//'mimeLimit' =>'image/*'
			);
		$qiniu_uptoken = object(parent::PLUGIN_QINIU)->uptoken($qiniu_config);
		if( !empty($qiniu_uptoken["errno"]) ){
			//删除文件
			object(parent::TABLE_SOFTSTORE_PRODUCT_IMAGE)->remove($insert_data["ss_product_image_id"]);
			
			throw new error($qiniu_uptoken["error"]);
		}
		
		//更新产品修改时间
		object(parent::TABLE_SOFTSTORE_PRODUCT)->update( 
			array( array('ss_product_id=[+]', $data['ss_product_id']) ), 
			array('ss_product_update_time' => time() ) 
		);	
		
		//插入操作日志
		object(parent::TABLE_ADMIN_LOG)->insert($data, $insert_data);
		
		return array("qiniu_uptoken" => $qiniu_uptoken["data"], "key" => $insert_data["ss_product_image_id"]);
	}
	
	
	
	/**
	 * 更新上传 产品图片 的状态
	 * $data = array(
	 * 	"ss_product_image_id" 图片ID
	 * 	"ss_product_image_format"	文件的后缀格式
	 *  "ss_product_image_width"	文件的宽
	 *  "ss_product_image_height"   文件的高
	 * )
	 * 
	 * @param	array	$data
	 * @return bool
	 */
	public function api_image_qiniu_state($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_IMAGE_UPLOAD);
		object(parent::ERROR)->check($data, 'ss_product_image_id', parent::TABLE_SOFTSTORE_PRODUCT_IMAGE, array('args'));
		object(parent::ERROR)->check($data, 'ss_product_image_format', parent::TABLE_SOFTSTORE_PRODUCT_IMAGE, array('args'));
		object(parent::ERROR)->check($data, 'ss_product_image_width', parent::TABLE_SOFTSTORE_PRODUCT_IMAGE, array('args'));
		object(parent::ERROR)->check($data, 'ss_product_image_height', parent::TABLE_SOFTSTORE_PRODUCT_IMAGE, array('args'));
		object(parent::ERROR)->check($data, 'ss_product_image_hash', parent::TABLE_SOFTSTORE_PRODUCT_IMAGE, array('args'));
		
		//获取旧数据
		$ss_product_image_data = object(parent::TABLE_SOFTSTORE_PRODUCT_IMAGE)->find($data['ss_product_image_id']);
		if( empty($ss_product_image_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'ss_product_image_format', 
			'ss_product_image_width', 
			'ss_product_image_height',
			'ss_product_image_hash',
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		//上传情况
		$update_data['ss_product_image_state'] = 1;
		$update_data['ss_product_image_update_time'] = time();
		if( object(parent::TABLE_SOFTSTORE_PRODUCT_IMAGE)->update( array(array('ss_product_image_id=[+]', (string)$data['ss_product_image_id'])), $update_data) ){
				
			//更新产品修改时间
			object(parent::TABLE_SOFTSTORE_PRODUCT)->update( 
				array( array('ss_product_id=[+]', $ss_product_image_data['ss_product_id']) ), 
				array('ss_product_update_time' => time() ) 
			);
				
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['ss_product_image_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	

	/**
	 * 删除产品图片
	 * 
	 * @param	array	$data
	 * @return bool
	 */
	public function api_image_qiniu_remove($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_IMAGE_REMOVE);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'ss_product_image_id', parent::TABLE_SOFTSTORE_PRODUCT_IMAGE, array('args'));
		
		//获取旧数据
		$ss_product_image_data = object(parent::TABLE_SOFTSTORE_PRODUCT_IMAGE)->find($data['ss_product_image_id']);
		if( empty($ss_product_image_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//获取配置
		$qiniu_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("qiniu_access"), true);
		if( empty($qiniu_config) ){
			throw new error("配置异常");
		}
		$qiniu_config["key"] = $data["ss_product_image_id"];
		$qiniu_uptoken = object(parent::PLUGIN_QINIU)->delete($qiniu_config);
		if( !empty($qiniu_uptoken["errno"]) ){
			throw new error($qiniu_uptoken["error"]);
		}
		
		if( object(parent::TABLE_SOFTSTORE_PRODUCT_IMAGE)->remove($data['ss_product_image_id']) ){
			
			//更新产品修改时间
			object(parent::TABLE_SOFTSTORE_PRODUCT)->update( 
				array( array('ss_product_id=[+]', $ss_product_image_data['ss_product_id']) ), 
				array('ss_product_update_time' => time() ) 
			);
			
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $ss_product_image_data);
			return $data['ss_product_image_id'];
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
	 * @param	array	$config
	 * @return	array
	 */
	public function api_file_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'sort_desc' => array('ss_product_file_sort', true),
			'sort_asc' => array('ss_product_file_sort', false),
			'name_desc' => array('ss_product_file_name', true),
			'name_asc' => array('ss_product_file_name', false),
			'type_desc' => array('ss_product_file_type', true),
			'type_asc' => array('ss_product_file_type', false),
			'size_desc' => array('ss_product_file_size', true),
			'size_asc' => array('ss_product_file_size', false),
			'state_desc' => array('ss_product_file_state', true),
			'state_asc' => array('ss_product_file_state', false),
			'insert_time_desc' => array('ss_product_file_insert_time', true),
			'insert_time_asc' => array('ss_product_file_insert_time', false),
			'update_time_desc' => array('ss_product_file_update_time', true),
			'update_time_asc' => array('ss_product_file_update_time', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('ss_product_file_id', false);
		
		if(!empty($data['search'])){
			if( isset($data['search']['ss_product_id']) && is_string($data['search']['ss_product_id']) ){
				$config["where"][] = array('[and] spf.ss_product_id=[+]', $data['search']['ss_product_id']);
				}
		}
		
		return object(parent::TABLE_SOFTSTORE_PRODUCT_FILE)->select_page($config);
	}
	




	/**
	 * 检查编辑产品文件信息的权限
	 * 
	 * @param	void
	 * @return  bool
	 */
	public function api_file_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_FILE_EDIT);
		return true;
	}



	/**
	 * 编辑产品文件信息
	 * 
	 * @param	array	$data
	 * @return  bool
	 */
	public function api_file_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_FILE_EDIT);
		//检查数据
		object(parent::ERROR)->check($data, 'ss_product_file_id', parent::TABLE_SOFTSTORE_PRODUCT_FILE, array('args'));
		if( isset($data['ss_product_file_name']) )
		object(parent::ERROR)->check($data, 'ss_product_file_name', parent::TABLE_SOFTSTORE_PRODUCT_FILE, array('args', 'length'));
		if( isset($data['ss_product_file_sort']) )
		object(parent::ERROR)->check($data, 'ss_product_file_sort', parent::TABLE_SOFTSTORE_PRODUCT_FILE, array('args'));
		
		//获取旧数据
		$ss_product_file_data = object(parent::TABLE_SOFTSTORE_PRODUCT_FILE)->find($data['ss_product_file_id']);
		if( empty($ss_product_file_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'ss_product_file_name', 
			'ss_product_file_sort', 
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($update_data as $key => $value){
			if( isset($ss_product_file_data[$key]) ){
				if($ss_product_file_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		$update_data['ss_product_file_update_time'] = time();
		//更新
		if( object(parent::TABLE_SOFTSTORE_PRODUCT_FILE)->update( array(array('ss_product_file_id=[+]', (string)$data['ss_product_file_id'])), $update_data) ){
				
			//更新产品修改时间
			object(parent::TABLE_SOFTSTORE_PRODUCT)->update( 
				array( array('ss_product_id=[+]', $ss_product_file_data['ss_product_id']) ), 
				array('ss_product_update_time' => time() ) 
			);	
				
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['ss_product_file_id'];
		}else{
			throw new error("操作失败");
		}
		
		
	}



	/**
	 * 获取 产品图片 上传的token
	 * $data = array(
	 * 	"ss_product_id" 产品ID
	 * 	"file_format" 文件的后缀
	 * 	"file_name"	文件的原名字
	 *  "file_size"	文件的大小
	 *  "file_type"  文件的类型
	 * )
	 * 
	 * 返回值：
	 * array(
	 * 	"qiniu_uptoken" 七牛的上传token
	 * 	"key" 			图片ID
	 * )
	 * 
	 * @param	array	$data
	 * @return array
	 */
	public function api_file_qiniu_uptoken($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_FILE_UPLOAD);
		object(parent::ERROR)->check($data, 'ss_product_id', parent::TABLE_SOFTSTORE_PRODUCT, array('args', 'exists_id'));
		
		if( empty($data["file_format"]) || (!is_string($data["file_format"]) && !is_numeric($data["file_format"])) ){
			$data["file_format"] = "";
		}
		if( empty($data["file_name"]) || (!is_string($data["file_name"]) && !is_numeric($data["file_name"])) ){
			$data["file_name"] = "";
		}
		if( empty($data["file_type"]) || (!is_string($data["file_type"]) && !is_numeric($data["file_type"])) ){
			//throw new error("产品文件的类型不合法");
			$data["file_type"] = "";
		}
		
		if( empty($data["file_size"]) || !is_numeric($data["file_size"]) ){
			throw new error("产品文件的大小不合法");
		}
		
		//获取配置
		$qiniu_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("qiniu_access"), true);
		if( empty($qiniu_config["bucket"]) ){
			throw new error("配置异常");
		}
		
		
		$insert_data = array(
			"ss_product_file_storage" => "qiniu",
			"ss_product_file_id" => object(parent::TABLE_SOFTSTORE_PRODUCT_FILE)->get_unique_id(),
			"user_id" => $_SESSION['user_id'],
			"ss_product_id" => $data["ss_product_id"],
			"ss_product_file_path" => $qiniu_config["bucket"],//储存的空间
			"ss_product_file_format" => $data["file_format"],
			"ss_product_file_name" => $data["file_name"],
			"ss_product_file_type" => $data["file_type"],
			"ss_product_file_size" => $data["file_size"],
			"ss_product_file_state" => 0,
			"ss_product_file_insert_time" => time(),
			"ss_product_file_update_time" => time(),
		);
		
		//生成一个图片表数据
		if( !object(parent::TABLE_SOFTSTORE_PRODUCT_FILE)->insert($insert_data) ){
			throw new error("产品文件登记失败");
		}
		
		
		//根据文件大小，设置有效时间
		$qiniu_config["expires"] = 7200;//两个小时
		$qiniu_config["policy"] = array(
				'returnBody' => '{"key":"$(key)","hash":"$(etag)","type":$(mimeType),"size":$(fsize),"name":$(fname),"bucket":"$(bucket)"}',
				//限定用户上传的文件类型。
				//'mimeLimit' =>'image/*'
			);
		$qiniu_uptoken = object(parent::PLUGIN_QINIU)->uptoken($qiniu_config);
		if( !empty($qiniu_uptoken["errno"]) ){
			//删除文件
			object(parent::TABLE_SOFTSTORE_PRODUCT_FILE)->remove($insert_data["ss_product_file_id"]);	
			
			throw new error($qiniu_uptoken["error"]);
		}
		
		//更新产品修改时间
		object(parent::TABLE_SOFTSTORE_PRODUCT)->update( 
			array( array('ss_product_id=[+]', $data['ss_product_id']) ), 
			array('ss_product_update_time' => time() ) 
		);	
		
		//插入操作日志
		object(parent::TABLE_ADMIN_LOG)->insert($data, $insert_data);
		
		
		return array("qiniu_uptoken" => $qiniu_uptoken["data"], "key" => $insert_data["ss_product_file_id"]);
		
	}

	
	/**
	 * 更新上传 产品文件 的状态
	 * $data = array(
	 * 	"ss_product_file_id" 文件ID
	 * )
	 * 
	 * @param	array	$data
	 * @return bool
	 */
	public function api_file_qiniu_state($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_FILE_UPLOAD);
		object(parent::ERROR)->check($data, 'ss_product_file_id', parent::TABLE_SOFTSTORE_PRODUCT_FILE, array('args'));
		if( isset($data['ss_product_file_hash']) )
		object(parent::ERROR)->check($data, 'ss_product_file_hash', parent::TABLE_SOFTSTORE_PRODUCT_FILE, array('args'));
		if( isset($data['ss_product_file_size']) )
		object(parent::ERROR)->check($data, 'ss_product_file_size', parent::TABLE_SOFTSTORE_PRODUCT_FILE, array('args'));
		if( isset($data['ss_product_file_type']) )
		object(parent::ERROR)->check($data, 'ss_product_file_type', parent::TABLE_SOFTSTORE_PRODUCT_FILE, array('args'));
		
		//获取旧数据
		$ss_product_file_data = object(parent::TABLE_SOFTSTORE_PRODUCT_FILE)->find($data['ss_product_file_id']);
		if( empty($ss_product_file_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'ss_product_file_hash',
			'ss_product_file_size',
			'ss_product_file_type'
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		//上传情况
		$update_data['ss_product_file_state'] = 1;
		$update_data['ss_product_file_update_time'] = time();
		if( object(parent::TABLE_SOFTSTORE_PRODUCT_FILE)->update( array(array('ss_product_file_id=[+]', (string)$data['ss_product_file_id'])), $update_data) ){
				
			//更新产品修改时间
			object(parent::TABLE_SOFTSTORE_PRODUCT)->update( 
				array( array('ss_product_id=[+]', $ss_product_file_data['ss_product_id']) ), 
				array('ss_product_update_time' => time() ) 
			);	
				
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['ss_product_file_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	/**
	 * 删除产品文件
	 * 
	 * @param	array	$data
	 * @return bool
	 */
	public function api_file_qiniu_remove($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PRODUCT_FILE_REMOVE);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'ss_product_file_id', parent::TABLE_SOFTSTORE_PRODUCT_FILE, array('args'));
		
		//获取旧数据
		$ss_product_file_data = object(parent::TABLE_SOFTSTORE_PRODUCT_FILE)->find($data['ss_product_file_id']);
		if( empty($ss_product_file_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		
		//获取配置
		$qiniu_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("qiniu_access"), true);
		if( empty($qiniu_config) ){
			throw new error("配置异常");
		}
		$qiniu_config["key"] = $data["ss_product_file_id"];
		$qiniu_uptoken = object(parent::PLUGIN_QINIU)->delete($qiniu_config);
		if( !empty($qiniu_uptoken["errno"]) ){
			throw new error($qiniu_uptoken["error"]);
		}
		
		
		if( object(parent::TABLE_SOFTSTORE_PRODUCT_FILE)->remove($data['ss_product_file_id']) ){
			
			//更新产品修改时间
			object(parent::TABLE_SOFTSTORE_PRODUCT)->update( 
				array( array('ss_product_id=[+]', $ss_product_file_data['ss_product_id']) ), 
				array('ss_product_update_time' => time() ) 
			);
			
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $ss_product_file_data);
			return $data['ss_product_file_id'];
		}else{
			throw new error("操作失败");
		}
		
		
		
	}
	
	
	
	
	
	
}
?>