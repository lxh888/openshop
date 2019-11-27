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
class product extends \eapie\source\request\softstore {
	
	
	/*前台调用的产品*/
	
	
	
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
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_USER),
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
			'click_desc' => array('ss_product_click', true),
			'click_asc' => array('ss_product_click', false),
			'sort_desc' => array('ss_product_sort', true),
			'sort_asc' => array('ss_product_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('ss_product_id', false);

		
		$config["where"][] = array("sp.ss_product_state=1");//已审核并已经发布的
		$config["where"][] = array("[and] sp.ss_product_trash=0");//没有被删除的
		if(!empty($data['search'])){
			if( isset($data['search']['id']) && is_string($data['search']['id']) ){
				$config["where"][] = array('[and] sp.ss_product_id=[+]', $data['search']['id']);
				}
			
			//ss_type_id 分类ID 从 ss_product_type 查询 获得 ss_product 产品ID 然后in()
			$where_in_product_type = array();
			$where_in_product_type[] = array("[and] st.ss_type_state=1");
			if( !empty($data['search']['type_id']) && is_array($data['search']['type_id']) ){
				//清理数据
				foreach($data['search']['type_id'] as $key => $value){
					if(is_string($value) || is_numeric($value)){
						//要将数据过滤
						$data['search']['type_id'][$key] = cmd(array($value), 'str addslashes');
					}else{
						unset($data['search']['type_id'][$key]);
					}
				}
				//是不加单引号并且强制不过滤
				$where_in_product_type[] = array("[and] st.ss_type_id IN([-])", "\"".implode("\",\"", $data['search']['type_id'])."\"", true);
			}
			
			//根据父id获取全部子级
			if( !empty($data['search']['type_parent_id']) && is_array($data['search']['type_parent_id']) ){
				//清理数据
				foreach($data['search']['type_parent_id'] as $key => $value){
					if(is_string($value) || is_numeric($value)){
						//要将数据过滤
						$data['search']['type_parent_id'][$key] = cmd(array($value), 'str addslashes');
					}else{
						unset($data['search']['type_parent_id'][$key]);
					}
				}
				$sql_in_type_parent_id = object(parent::TABLE_SOFTSTORE_TYPE)->sql_search_type(array(
					array("[and] ss_type_parent_id IN([-])", "\"".implode("\",\"", $data['search']['type_parent_id'])."\"", true),
					array("[and] ss_type_state=1")
				));
				
				if( empty($data['search']['type_id']) ){
					$where_in_product_type[] = array("[and] st.ss_type_id IN([-])", $sql_in_type_parent_id, true);
				}else{
					$where_in_product_type[] = array("[or] st.ss_type_id IN([-])", $sql_in_type_parent_id, true);
				}
			}
			
			if(!empty($data['search']['type_id']) || !empty($data['search']['type_parent_id'])){
				$sql_in_product_id = object(parent::TABLE_SOFTSTORE_PRODUCT_TYPE)->sql_search_type($where_in_product_type);
				$config["where"][] = array('[and] sp.ss_product_id IN([-])', $sql_in_product_id, true);
			}
		}

		// 搜索
		if (!empty($data['keyword']) && is_string($data['keyword'])) {
			$keyword = cmd(array($data['keyword']), 'str addslashes');
    		$keyword = "%{$keyword}%";
    		$config['where'][] = array('[and] sp.ss_product_name like [+]', $keyword);
		}
		
		$config["select"] = array(
			"u.user_nickname",
			"u.user_compellation",
			"sp.ss_product_id",
			"sp.user_id",
			"sp.ss_product_name",
			"sp.ss_product_info",
			"sp.ss_product_click",
			"sp.ss_product_sort",
			"sp.ss_product_update_time",
			"sp.ss_product_insert_time",
		);
		
			
		$data = object(parent::TABLE_SOFTSTORE_PRODUCT)->select_page($config);
		if( !empty($data["data"]) ){
			$data["data"] = object(parent::TABLE_SOFTSTORE_PRODUCT)->get_additional_data($data["data"], array(
				'ss_product_type' => array(
					"where" => array(
							array("[and] st.ss_type_state=1")
						),
					),
				));
		}
		
		return $data;
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
		object(parent::ERROR)->check($data, 'ss_product_id', parent::TABLE_SOFTSTORE_PRODUCT, array('args'));
		$get_data = object(parent::TABLE_SOFTSTORE_PRODUCT)->find($data['ss_product_id']);
		if( empty($get_data) || (string)$get_data["ss_product_state"] != "1" ){
			throw new error("产品不存在，或者没有发布");
		}
		
		$data = array($get_data);
		$data = object(parent::TABLE_SOFTSTORE_PRODUCT)->get_additional_data($data, array(
			'ss_product_type' => array(
				"where" => array(
						array("[and] st.ss_type_state=1")
					),
				),
			));
		$get_data = $data[0];
		
		return $get_data;
	}
	
	
	
	/**
	 * 获取产品的图片数据
	 * $data = arrray(
	 * 	ss_product_id 产品ID
	 * )
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_image_data($data = array()){
		object(parent::ERROR)->check($data, 'ss_product_id', parent::TABLE_SOFTSTORE_PRODUCT, array('args', 'legal_id'));
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
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
		$config["where"] = array(
				array("[and] spi.ss_product_id=[+]", $data['ss_product_id']),
				array("[and] spi.ss_product_image_state=1")
				);
		
		/*$config = array(
			"where" => array(
				array("[and] spi.ss_product_id=[+]", $data['ss_product_id']),
				array("[and] spi.ss_product_image_state=1")
			),
			'orderby' => array(
				array("ss_product_image_main", true),
				array("ss_product_image_sort", true),
				array("ss_product_image_update_time", true),
				array("ss_product_image_insert_time", true),
			),
		);*/
		return object(parent::TABLE_SOFTSTORE_PRODUCT_IMAGE)->select_join($config);
	}
	
	
	
	/**
	 * 获取产品的视频数据
	 * $data = arrray(
	 * 	ss_product_id 产品ID
	 * )
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_video_data($data = array()){
		object(parent::ERROR)->check($data, 'ss_product_id', parent::TABLE_SOFTSTORE_PRODUCT, array('args', 'legal_id'));
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
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
		
		$config["where"] = array(
				array("[and] spf.ss_product_id=[+]", $data['ss_product_id']),
				array('[and] spf.ss_product_file_type LIKE "video/%"'),
				array("[and] spf.ss_product_file_state=1")
				);
		
		/*$config = array(
			"where" => array(
				array("[and] spf.ss_product_id=[+]", $data['ss_product_id']),
				array('[and] spf.ss_product_file_type LIKE "video/%"'),
				array("[and] spf.ss_product_file_state=1")
			),
			'orderby' => array(
				array("ss_product_file_sort"),
				array("ss_product_file_update_time"),
				array("ss_product_file_insert_time"),
			),
		);*/
		return object(parent::TABLE_SOFTSTORE_PRODUCT_FILE)->select_join($config);
	}
	
	
	
	
	/**
	 * 获取产品的非视频的文档数据
	 * $data = arrray(
	 * 	ss_product_id 产品ID
	 * )
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_file_data($data = array()){
		object(parent::ERROR)->check($data, 'ss_product_id', parent::TABLE_SOFTSTORE_PRODUCT, array('args', 'legal_id'));
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
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
		
		$config["where"] = array(
				array("[and] spf.ss_product_id=[+]", $data['ss_product_id']),
				array('[and] spf.ss_product_file_type NOT LIKE "video/%"'),
				array("[and] spf.ss_product_file_state=1")
				);
				
		/*$config = array(
			"where" => array(
				array("[and] spf.ss_product_id=[+]", $data['ss_product_id']),
				array('[and] spf.ss_product_file_type NOT LIKE "video/%"'),
				array("[and] spf.ss_product_file_state=1")
			),
			'orderby' => array(
				array("ss_product_file_sort"),
				array("ss_product_file_update_time"),
				array("ss_product_file_insert_time"),
			),
		);*/
		return object(parent::TABLE_SOFTSTORE_PRODUCT_FILE)->select_join($config);
	}
		
	
	
	/**
	 * 获取产品的规格属性
	 * $data = arrray(
	 * 	ss_product_id 产品ID
	 * )
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_attr_option($data = array()){
		object(parent::ERROR)->check($data, 'ss_product_id', parent::TABLE_SOFTSTORE_PRODUCT, array('args', 'legal_id'));
		$config = array(
			"where" => array(
				array("[and] ss_product_id=[+]", $data['ss_product_id']),
			),
			'orderby' => array(
				array("ss_product_attr_sort"),
				array("ss_product_attr_insert_time"),
			),
		);
		
		$parent_config = $config;
		$son_config = $config;
		
		if( !empty($data["type"]) && $data["type"] == "son" ){
			$parent_config["where"][] = array('[and] ss_product_attr_parent_id<>""');
		}else{
			$parent_config["where"][] = array("[and] ss_product_attr_parent_id=\"\"");
		}
		
		return object(parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE)->select_parent_son_all($parent_config, $son_config);
	}
	
	
	
	
	
}
?>