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



namespace eapie\source\request\application;
use eapie\main;
use eapie\error;
class admin_type extends \eapie\source\request\application {
	
	
	
	
	/**
	 * 获取分类模块选项列表
	 * 
	 * APPLICATIONADMINTYPEMODULEOPTION
	 * {"class":"application/admin_type","method":"api_module_option"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_module_option($data = array()){
		object(parent::REQUEST_ADMIN)->check();
		return object(parent::TABLE_TYPE)->get_module();
	}
	
	
	
	
			
	/**
	 * 获取分类选项列表
	 *  $data = array(
	 * 	"type" => "son" 只获取子级，为空获取父级
	 *  "module" => "module" 模块名称
	 * 	'sort' => ["sort_asc", "name_desc"] 排序
	 * );
	 * 
	 * APPLICATIONADMINTYPEOPTION
	 * {"class":"application/admin_type","method":"api_option"}
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
			'name_desc' => array('type_name', true),
			'name_asc' => array('type_name', false),
			
			'module_desc' => array('type_module', true),
			'module_asc' => array('type_module', false),
			
			'label_desc' => array('type_label', true),
			'label_asc' => array('type_label', false),
			
			'insert_time_desc' => array('type_insert_time', true),
			'insert_time_asc' => array('type_insert_time', false),
			'update_time_desc' => array('type_update_time', true),
			'update_time_asc' => array('type_update_time', false),
			'sort_desc' => array('type_sort', true),
			'sort_asc' => array('type_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('type_id', false);
		
		$parent_config = $config;
		$son_config = $config;
		
		if( !empty($data["type"]) && $data["type"] == "son" ){
			$parent_config["where"][] = array('[and] type_parent_id<>""');
		}else{
			$parent_config["where"][] = array("[and] type_parent_id=\"\"");
		}
		
		if( !empty($data["module"]) && (is_string($data["module"]) || is_numeric($data["module"]))){
			$parent_config["where"][] = array('[and] type_module=[+]', $data["module"]);
			$son_config["where"][] = array('[and] type_module=[+]', $data["module"]);
		}
		
		//前台的这里应该是需要判断状态的，第三个参数为true，表示不拿缓存的
		return object(parent::TABLE_TYPE)->select_parent_son_all($parent_config, $son_config);
	}
	
	
	
	/**
	 * 获取某一个分类数据
	 * $data = arrray(
	 * 	type_id 分类ID
	 * )
	 * 
	 * APPLICATIONADMINTYPEGET
	 * {"class":"application/admin_type","method":"api_get"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_get($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_TYPE_READ);
		object(parent::ERROR)->check($data, 'type_id', parent::TABLE_TYPE, array('args'));
		$get_data = object(parent::TABLE_TYPE)->find($data['type_id']);
		if( empty($get_data) ) throw new error('数据不存在');
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
	 * APPLICATIONADMINTYPELIST
	 * {"class":"application/admin_type","method":"api_list"}
	 * 
	 * @param	array	$data
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
			'name_desc' => array('type_name', true),
			'name_asc' => array('type_name', false),
			'state_desc' => array('type_state', true),
			'state_asc' =>  array('type_state', false),
			'insert_time_desc' => array('type_insert_time', true),
			'insert_time_asc' => array('type_insert_time', false),
			'update_time_desc' => array('type_update_time', true),
			'update_time_asc' => array('type_update_time', false),
			'parent_desc' => array('type_parent_id', true),
			'parent_asc' => array('type_parent_id', false),
			'sort_desc' => array('type_sort', true),
			'sort_asc' => array('type_sort', false),
			
			'label_desc' => array('type_label', true),
			'label_asc' => array('type_label', false),
			'module_desc' => array('type_module', true),
			'module_asc' => array('type_module', false),
			
			'son_desc' => array('type_son_count', true),
			'son_asc' => array('type_son_count', false)
		));
		
		//避免排序重复
		$config["orderby"][] = array('type_id', false);
		
		if(!empty($data['search'])){
			if( isset($data['search']['type_id']) && is_string($data['search']['type_id']) ){
				$config["where"][] = array('[and] t.type_id=[+]', $data['search']['type_id']);
			}
			if (isset($data['search']['type_name']) && is_string($data['search']['type_name'])) {
                $config['where'][] = array('[and] t.type_name LIKE "%[-]%"', $data['search']['type_name']);
            }
			if (isset($data['search']['type_label']) && is_string($data['search']['type_label'])) {
                $config['where'][] = array('[and] t.type_label LIKE "%[-]%"', $data['search']['type_label']);
            }
			if( isset($data['search']['type_module']) && is_string($data['search']['type_module']) ){
				$config["where"][] = array('[and] t.type_module=[+]', $data['search']['type_module']);
			}
		}
		
		if( isset($data['search']['type_parent_id']) && is_string($data['search']['type_parent_id']) ){
			$config["where"][] = array('[and] t.type_parent_id=[+]', $data['search']['type_parent_id']);
		}else{
			$config["where"][] = array('[and] t.type_parent_id=""');
		}
		
		return object(parent::TABLE_TYPE)->select_page($config);
	}
	
	
	
	
	
			
	
	/**
	 * 添加分类
	 * 
	 * APPLICATIONADMINTYPEADD
	 * {"class":"application/admin_type","method":"api_add"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_TYPE_ADD);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'type_name', parent::TABLE_TYPE, array('args', 'length'));
		object(parent::ERROR)->check($data, 'type_parent_id', parent::TABLE_TYPE, array('args'));
		object(parent::ERROR)->check($data, 'type_info', parent::TABLE_TYPE, array('args'));
		
		object(parent::ERROR)->check($data, 'type_module', parent::TABLE_TYPE, array('args'));
		object(parent::ERROR)->check($data, 'type_label', parent::TABLE_TYPE, array('args'));
		object(parent::ERROR)->check($data, 'type_comment', parent::TABLE_TYPE, array('args'));
		
		object(parent::ERROR)->check($data, 'type_sort', parent::TABLE_TYPE, array('args'));
		object(parent::ERROR)->check($data, 'type_state', parent::TABLE_TYPE, array('args'));
		object(parent::ERROR)->check($data, 'type_json', parent::TABLE_TYPE, array('args'));
		
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'type_parent_id', 
			'type_name', 
			'type_info',
			
			'type_module',
			'type_label',
			'type_comment',
			'type_json',
			
			'type_sort',
			'type_state',
			);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		
		//判断该分类是否存在、是否为顶级分类
		if( !empty($insert_data["type_parent_id"]) ){
			$parent_data = object(parent::TABLE_TYPE)->find($insert_data['type_parent_id']);
			if( empty($parent_data) ){
				throw new error("父级ID有误，数据不存在");
			}
			if( !empty($parent_data['type_parent_id']) ){
				throw new error("所编辑的父级并不是顶级分类");
			}
		}
		
		if( !empty($_FILES) ){
			$qiniu_image = object(parent::REQUEST_APPLICATION)->qiniu_image_upload();
			$insert_data['type_logo_image_id'] = $qiniu_image['image_id'];
		}
		
		//获取id号
		$insert_data['type_id'] = object(parent::TABLE_TYPE)->get_unique_id();
		//用户数据
		$insert_data['user_id'] = $_SESSION['user_id'];
		//创建时间
		$insert_data['type_insert_time'] = time();
		//更新时间
		$insert_data['type_update_time'] = time();
		
		if( object(parent::TABLE_TYPE)->insert($insert_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $insert_data);
			return $insert_data['type_id'];
		}else{
			object(parent::REQUEST_APPLICATION)->qiniu_image_remove($qiniu_image);
			throw new error("操作失败");
		}
		
	}
	
	
	
	
		
	/**
	 * 检查编辑的权限
	 * 
	 * APPLICATIONADMINTYPEEDITCHECK
	 * {"class":"application/admin_type","method":"api_edit_check"}
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
	 * 
	 * APPLICATIONADMINTYPEEDIT
	 * {"class":"application/admin_type","method":"api_edit"}
	 * 
	 * @param	array		$data
	 * @return	string
	 */
	public function api_edit($data = array()){
		//检查权限
		$admin = object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_TYPE_EDIT, true);
		if (!$admin) {
			$merchant_id = object(parent::TABLE_MERCHANT_USER)->check_identity();
		}

		//数据检测 
		object(parent::ERROR)->check($data, 'type_id', parent::TABLE_TYPE, array('args'));
		if( isset($data['type_name']) )
		object(parent::ERROR)->check($data, 'type_name', parent::TABLE_TYPE, array('args', 'length'));
		if( isset($data['type_parent_id']) )
		object(parent::ERROR)->check($data, 'type_parent_id', parent::TABLE_TYPE, array('args'));
		if( isset($data['type_info']) )
		object(parent::ERROR)->check($data, 'type_info', parent::TABLE_TYPE, array('args'));
		if( isset($data['type_module']) )
		object(parent::ERROR)->check($data, 'type_module', parent::TABLE_TYPE, array('args'));
		if( isset($data['type_label']) )
		object(parent::ERROR)->check($data, 'type_label', parent::TABLE_TYPE, array('args'));
		if( isset($data['type_comment']) )
		object(parent::ERROR)->check($data, 'type_comment', parent::TABLE_TYPE, array('args'));
		if( isset($data['type_sort']) )
		object(parent::ERROR)->check($data, 'type_sort', parent::TABLE_TYPE, array('args'));
		if( isset($data['type_state']) )
		object(parent::ERROR)->check($data, 'type_state', parent::TABLE_TYPE, array('args'));
		// if( isset($data['type_json']) )
		// object(parent::ERROR)->check($data, 'type_json', parent::TABLE_TYPE, array('args'));
		
		
		//获取旧数据
		$type_data = object(parent::TABLE_TYPE)->find($data['type_id']);
		if( empty($type_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'type_parent_id', 
			'type_name', 
			'type_info',
			
			'type_module',
			'type_label',
			'type_comment',
			'type_json',
			
			'type_sort',
			'type_state',
		);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($update_data as $key => $value){
			if( isset($type_data[$key]) ){
				if($type_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		//父级不能是自己
		if( !empty($update_data["type_parent_id"]) ){
			if( $update_data["type_parent_id"] == $data['type_id'] ){
				throw new error("不能将自己设为父级");
			}
			
			//判断该分类是否存在、是否为顶级分类
			$parent_data = object(parent::TABLE_TYPE)->find($update_data['type_parent_id']);
			if( empty($parent_data) ){
				throw new error("父级ID有误，数据不存在");
			}
			
			if( !empty($parent_data['type_parent_id']) ){
				throw new error("所编辑的父级并不是顶级分类");
			}
			
		}
		
		//如果父级 编辑成 子级，必须要去掉子级
		if( empty($type_data["type_parent_id"]) &&
		isset($update_data["type_parent_id"]) ){
			if( object(parent::TABLE_TYPE)->find_exists_son_id($data['type_id']) ){
				throw new error("该分类下存在子级，请先清理子级才能变更该分类的父级");
			}
		}
		
		
		if( !empty($_FILES) ){
			$qiniu_image = object(parent::REQUEST_APPLICATION)->qiniu_image_upload();
			$update_data['type_logo_image_id'] = $qiniu_image['image_id'];
		}
		
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		
		//更新时间
		$update_data['type_update_time'] = time();

		// 更新条件
		$update_where = array(
			array('type_id=[+]', $data['type_id']),
		);
		if (!$admin) {
			$update_where[] = array('[and] merchant_id = [+]', $merchant_id);
		}
		
		if( object(parent::TABLE_TYPE)->update($update_where, $update_data) ){
			
			//如果有上传，删除旧图片
			if( !empty($type_data['type_logo_image_id']) && !empty($qiniu_image) ){
				$qiniu_image['image_id'] = $type_data['type_logo_image_id'];
				object(parent::REQUEST_APPLICATION)->qiniu_image_remove($qiniu_image);
			}
			 
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['type_id'];
		}else{
			object(parent::REQUEST_APPLICATION)->qiniu_image_remove($qiniu_image);
			throw new error("操作失败");
		}
		
		
	}
	
	

	
	
	/**
	 * 删除
	 * 
	 * APPLICATIONADMINTYPEREMOVE
	 * {"class":"application/admin_type","method":"api_remove"}
	 * 
	 * @param	array	$data
	 * @return	string
	 */
	public function api_remove( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_TYPE_REMOVE);
        //校验数据
        object(parent::ERROR)->check($data, 'type_id', parent::TABLE_TYPE, array('args'));
        //查询旧数据
        $original = object(parent::TABLE_TYPE)->find($data['type_id']);
        if (empty($original)) throw new error('数据不存在');
		
		//存在下级则无法删除
		if( object(parent::TABLE_TYPE)->find_exists_son_id($data['type_id']) ){
			throw new error("该分类下存在子级，请先清理子级才能删除该分类");
			}
		
        //删除数据，记录日志
        if ( object(parent::TABLE_TYPE)->remove($original['type_id']) ) {
            //logo存在，那么要删除旧图片
            if( !empty($original["type_logo_image_id"]) ){
            	object(parent::REQUEST_APPLICATION)->qiniu_image_remove(array( "image_id" => $original["type_logo_image_id"] ));
            }
            object(parent::TABLE_ADMIN_LOG)->insert($data, $original);
            return $data['type_id'];
        } else {
            throw new error('删除失败');
        }
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>