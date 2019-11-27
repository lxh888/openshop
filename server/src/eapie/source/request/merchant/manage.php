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



namespace eapie\source\request\merchant;
use eapie\main;
use eapie\error;
class manage extends \eapie\source\request\merchant {
	
	
	/*商家后台管理*/
	
	
	
	/**
	 * 权限检测
	 * 
	 * @param	array	$data
	 */
	private function _authority($data){
		//首先验证 商家ID
		object(parent::ERROR)->check($data, 'merchant_id', parent::TABLE_MERCHANT, array('args'));
		//检测这个 商家ID 并且 检测登录状态  获得 商家数据、以及商家用户数据
		object(parent::REQUEST_MERCHANT)->check($data['merchant_id']);//一定要上传商家ID进去
		//只要没有报错  那么就能获取 数据 $_SESSION['merchant']
	}
	
	
	
	
	
	
	/**
	 * 获取商家后台分类模块选项列表
	 * 
	 * MERCHANTMANAGETYPEMODULEOPTION
	 * {"class":"merchant/manage","method":"api_type_module_option"}
	 * 
	 * 注意版本号的命名规范：merchant manage [接口去掉"version_"的名称]
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_type_module_option($data = array()){
		return object(parent::TABLE_TYPE)->get_module();
	}
	
	
	
					
	/**
	 * 获取商家后台分类选项列表
	 *  $data = array(
	 * 	"type" => "son" 只获取子级，为空获取父级
	 *  "module" => "module" 模块名称
	 * 	'sort' => ["sort_asc", "name_desc"] 排序
	 *  'merchant_id' => 商家ID
	 * 	'is_platform' => 是否是平台的分类，如果存在，那么 商家ID为空
	 * );
	 * 
	 * MERCHANTMANAGESELFTYPEOPTION
	 * {"class":"merchant/manage","method":"api_self_type_option"}
	 * 
	 * 注意版本号的命名规范：merchant manage [接口去掉"version_"的名称]
	 * 
	 * [{"type":"只获取子级:son，为空获取父级","module":"模块名称","sort":["sort_asc", "name_desc"],"merchant_id":"商家ID"}]
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_self_type_option($data = array()){
		//权限检测
		$this->_authority($data);
		
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
		
		if( empty($data['is_platform']) ){
			$config["where"][] = array('[and] merchant_id =[+]', $data['merchant_id']);
		}else{
			$config["where"][] = array('[and] merchant_id =""');
		}
		
		
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
	 * 商家后台-获取某一个分类数据
	 * $data = arrray(
	 * 	type_id 分类ID
	 * )
	 * 
	 * MERCHANTMANAGESELFTYPEGET
	 * {"class":"merchant/manage","method":"api_self_type_get"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_self_type_get($data = array()){
		//权限检测
		$this->_authority($data);
		object(parent::ERROR)->check($data, 'type_id', parent::TABLE_TYPE, array('args'));
		$get_data = object(parent::TABLE_TYPE)->find($data['type_id']);
		if( empty($get_data) || $get_data['merchant_id'] != $data['merchant_id'] ) throw new error('数据不存在');
        return $get_data;
	}
	
					
	/**
	 * 获取商家后台分类数据列表
	 * 
	 * MERCHANTMANAGESELFTYPELIST
	 * {"class":"merchant/manage","method":"api_self_type_list"}
	 * 
	 * 注意版本号的命名规范：merchant manage [接口去掉"version_"的名称]
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_self_type_list($data = array()){
		//权限检测
		$this->_authority($data);
		
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
		$config["orderby"][] = array('t.type_id', false);
		$config['where'][] = array('[and] t.merchant_id =[+]',$data['merchant_id']);
		
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
	 * 商家后台添加分类
	 * 
	 * MERCHANTMANAGESELFTYPEADD
	 * {"class":"merchant/manage","method":"api_self_type_add"}
	 * 
	 * 注意版本号的命名规范：merchant manage [接口去掉"version_"的名称]
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_self_type_add($data = array()){
		//权限检测
		$this->_authority($data);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'type_name', parent::TABLE_TYPE, array('args', 'length'));
		object(parent::ERROR)->check($data, 'type_parent_id', parent::TABLE_TYPE, array('args'));
		object(parent::ERROR)->check($data, 'type_info', parent::TABLE_TYPE, array('args'));
		
		object(parent::ERROR)->check($data, 'type_module', parent::TABLE_TYPE, array('args'));
		object(parent::ERROR)->check($data, 'type_label', parent::TABLE_TYPE, array('args'));
		object(parent::ERROR)->check($data, 'type_comment', parent::TABLE_TYPE, array('args'));
		
		object(parent::ERROR)->check($data, 'type_sort', parent::TABLE_TYPE, array('args'));
		object(parent::ERROR)->check($data, 'type_state', parent::TABLE_TYPE, array('args'));
		// object(parent::ERROR)->check($data, 'type_json', parent::TABLE_TYPE, array('args'));
		
		
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
		
		$merchant_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("merchant_data"), true);
        if(!empty($merchant_config['state']) && $merchant_config['state'] == 1){
            $type_num = !empty($merchant_config['type_num'])?$merchant_config['type_num']:20;
            $types = object(parent::TABLE_TYPE)->find_count(array(array('merchant_id =[+]',$data['merchant_id'])));
            if($types['count'] > $type_num){ throw new error('商家商品分类已达上限'); }
        }
		$insert_data['merchant_id'] = $data['merchant_id'];
		
		
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
	 * 商家后台-删除分类数据
	 * 
	 * MERCHANTMANAGESELFTYPEREMOVE
	 * {"class":"merchant/manage","method":"api_self_type_remove"}
	 * 
	 * @param	array	$data
	 * @return	string
	 */
	public function api_self_type_remove( $data = array() ){
		//权限检测
		$this->_authority($data);
		
		//校验数据
        object(parent::ERROR)->check($data, 'type_id', parent::TABLE_TYPE, array('args'));
        //查询旧数据
        $original = object(parent::TABLE_TYPE)->find($data['type_id']);
        if( empty($original) || $original['merchant_id'] != $data['merchant_id'] ) throw new error('数据不存在');
		
		//存在下级则无法删除
		if( object(parent::TABLE_TYPE)->find_merchant_exists_son_id($data['type_id'], $data['merchant_id']) ){
			throw new error("该分类下存在子级，请先清理子级才能删除该分类");
			}
        //删除数据，记录日志
        if( object(parent::TABLE_TYPE)->remove($original['type_id']) ){
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
	
	
	
	/**
	 * 商家后台管理-商品分类的编辑
	 * $data = array(
	 * 	'shop_goods_id' => string 商品ID
	 * 	'type_id' => array 索引数组
	 * )
	 * 
	 * MERCHANTMANAGESELFSHOPGOODSTYPE
	 * {"class":"merchant/manage","method":"api_self_shop_goods_type"}
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_self_shop_goods_type($data = array()){
		//权限检测
		$this->_authority($data);
		
		//检查参数
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		//获取旧数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find_shop($data['shop_goods_id'], $data['merchant_id']);
		if( empty($shop_goods_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		if( !empty($data['type_id']) && is_array($data['type_id'])){
			//清理数据
			$type_id = array();
			foreach($data['type_id'] as $value){
				if(is_string($value) || is_numeric($value)){
					$type_id[] = cmd(array($value), 'str addslashes');
				}
			}
		}
		
		$type_data = array();
		if( !empty($type_id) ){
			//获取分类数据
			$in_string = "\"".implode("\",\"", $type_id)."\"";
			$type_where = array();
			$type_where[] = array("type_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
			$type_where[] = array("[and] merchant_id=[+]", $data['merchant_id']);
			$type_where[] = array("[and] type_module=[+]", parent::MODULE_SHOP_GOODS_TYPE);
			//获取商品分类数据
			$type_data = object(parent::TABLE_TYPE)->select(array("where"=>$type_where));
		}
		
		//获取商品的旧分类数据
		$goods_type_data = object(parent::TABLE_SHOP_GOODS_TYPE)->select(array(
			"where" => array( array("shop_goods_id=[+]", $data['shop_goods_id']) )
		));
		
		//获取  总平台的分类，防止被覆盖
		$total_platform = object(parent::TABLE_SHOP_GOODS_TYPE)->select_join(array(
			"where" => array( 
				array("sgt.shop_goods_id=[+]", $data['shop_goods_id']),
				array("t.merchant_id=''") 
			),
			"select" => array('t.*')
		));
		
		//如果存在 总平台的 那么合并
		if( !empty($total_platform) ){
			$type_data = array_merge($type_data, $total_platform);
		}
		
		//printexit($type_data);
		
		//获得清理数据。1）需要删除的商品分类ID   2）需要增加的分类ID
		$clear_data = array();
		$clear_data["type_id"] = array();
		$clear_data["delete"] = array();
		$clear_data["insert"] = array();
		
		//先收集 旧数据，假设都需要被删除
		if( !empty($goods_type_data) ){
			foreach($goods_type_data as $value){
				$clear_data["delete"][$value["type_id"]] = $value["shop_goods_type_id"];
			}
		}
		
		//进行筛选
		if( !empty($type_data) ){
			foreach($type_data as $type_value){
				$clear_data["type_id"][] = $type_value["type_id"];
				if( isset($clear_data["delete"][$type_value["type_id"]]) ){
					unset($clear_data["delete"][$type_value["type_id"]]);
				}else{
					//这里就是需要增加的商品分类
					//$type_value["type_id"];
					$insert_data = array(
						"shop_goods_type_id" => object(parent::TABLE_SHOP_GOODS_TYPE)->get_unique_id(),
						"shop_goods_id" => $data['shop_goods_id'],
						"type_id" => $type_value["type_id"],
						"user_id" => $_SESSION["user_id"],
						"shop_goods_type_time" => time()
					);
					$clear_data["insert"][] = $insert_data;
				}
			}
		}
		
		if( !empty($clear_data["insert"]) ){
			object(parent::TABLE_SHOP_GOODS_TYPE)->insert_batch($clear_data["insert"]);
		}
		
		//再删除
		if( !empty($clear_data["delete"]) ){
			$in_string = "\"".implode("\",\"", $clear_data["delete"])."\"";
			//是不加单引号并且强制不过滤
			object(parent::TABLE_SHOP_GOODS_TYPE)->delete(array( array("shop_goods_type_id IN([-])", $in_string, true) ));
		}
		
		if( empty($clear_data["insert"]) && empty($clear_data["delete"]) ){
			throw new error("没有需要更新的数据");
		}
		
		//更新商品修改时间
		object(parent::TABLE_SHOP_GOODS)->update( 
			array( array('shop_goods_id=[+]', $data['shop_goods_id']) ), 
			array('shop_goods_update_time' => time() ) 
		);	
		
		//插入操作日志
		object(parent::TABLE_ADMIN_LOG)->insert($data, $clear_data);
		
		return true;
	}
	
	
	
	
		
	
	/**
	 * 商家后台管理-添加商品
     * MERCHANTMANAGESELFSHOPGOODSADD
     * {"class":"merchant/manage","method":"api_self_shop_goods_add"}
	 * 
	 * 注意版本号的命名规范：merchant manage [接口去掉"version_"的名称]
	 * 
	 * [{"merchant_id":"商家ID[必须]"}]
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_self_shop_goods_add($data = array()){
		//权限检测
		$this->_authority($data);
		
		//数据检测
		object(parent::ERROR)->check($data, 'shop_goods_parent_id', parent::TABLE_SHOP_GOODS, array('args', 'exists_id'));
		object(parent::ERROR)->check($data, 'shop_goods_name', parent::TABLE_SHOP_GOODS, array('args', 'length'));
		object(parent::ERROR)->check($data, 'shop_goods_sn', parent::TABLE_SHOP_GOODS, array('args', 'length'));
		object(parent::ERROR)->check($data, 'shop_goods_property', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_index', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_info', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_warning', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_details', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_stock_warning', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_stock_mode', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_keywords', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_description', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_sort', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_seller_note', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_admin_note', parent::TABLE_SHOP_GOODS, array('args'));
		
		//白名单
		$whitelist = array(
			'shop_goods_parent_id', 
			'shop_goods_name', 
			'shop_goods_sn', 
			'shop_goods_property',
			'shop_goods_index',
			'shop_goods_info',
			'shop_goods_warning',
			'shop_goods_details',
			'shop_goods_stock_warning',
			'shop_goods_stock_mode',
			'shop_goods_keywords',
			'shop_goods_description',
			'shop_goods_sort',
			'shop_goods_seller_note',
			'shop_goods_admin_note',
		);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		//获取id号
		$insert_data['shop_goods_id'] = object(parent::TABLE_SHOP_GOODS)->get_unique_id();
		//创建时间
		$insert_data['shop_goods_insert_time'] = time();
		//更新时间
		$insert_data['shop_goods_update_time'] = time();
		//用户id
		$insert_data['user_id'] = $_SESSION['user_id'];
		//商家id
		$insert_data['shop_id'] = $_SESSION['merchant']['merchant_id'];
		
		//状态。0未通过审核；1已审核并发布；2待审核；3编辑中
		$insert_data['shop_goods_state'] = 3;
		
		if( object(parent::TABLE_SHOP_GOODS)->insert($insert_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $insert_data);
			return $insert_data['shop_goods_id'];
		}else{
			throw new error("操作失败");
		}
		
		
	}
	
	
	
	/**
	 * 商家后台管理-编辑商品
     * MERCHANTMANAGESELFSHOPGOODSEDIT
     * {"class":"merchant/manage","method":"api_self_shop_goods_edit"}
	 * 
	 * 注意版本号的命名规范：merchant manage [接口去掉"version_"的名称]
	 * 
	 * [{"merchant_id":"商家ID[必须]"}]
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_self_shop_goods_edit($data = array()){
		//权限检测
		$this->_authority($data);
		
		//数据检测
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		if( isset($data['shop_goods_sn']) )
		object(parent::ERROR)->check($data, 'shop_goods_sn', parent::TABLE_SHOP_GOODS, array('args', 'length'));
		if( isset($data['shop_goods_name']) )
		object(parent::ERROR)->check($data, 'shop_goods_name', parent::TABLE_SHOP_GOODS, array('args', 'length'));
		if( isset($data['shop_goods_property']) )
		object(parent::ERROR)->check($data, 'shop_goods_property', parent::TABLE_SHOP_GOODS, array('args'));
		if( isset($data['shop_goods_index']) )
		object(parent::ERROR)->check($data, 'shop_goods_index', parent::TABLE_SHOP_GOODS, array('args'));
		if( isset($data['shop_goods_info']) )
		object(parent::ERROR)->check($data, 'shop_goods_info', parent::TABLE_SHOP_GOODS, array('args'));
		if( isset($data['shop_goods_warning']) )
		object(parent::ERROR)->check($data, 'shop_goods_warning', parent::TABLE_SHOP_GOODS, array('args'));
		if( isset($data['shop_goods_stock_warning']) )
		object(parent::ERROR)->check($data, 'shop_goods_stock_warning', parent::TABLE_SHOP_GOODS, array('args'));
		if( isset($data['shop_goods_stock_mode']) )
		object(parent::ERROR)->check($data, 'shop_goods_stock_mode', parent::TABLE_SHOP_GOODS, array('args'));
		if( isset($data['shop_goods_details']) )
		object(parent::ERROR)->check($data, 'shop_goods_details', parent::TABLE_SHOP_GOODS, array('args'));
		if( isset($data['shop_goods_keywords']) )
		object(parent::ERROR)->check($data, 'shop_goods_keywords', parent::TABLE_SHOP_GOODS, array('args'));
		if( isset($data['shop_goods_description']) )
		object(parent::ERROR)->check($data, 'shop_goods_description', parent::TABLE_SHOP_GOODS, array('args'));
		if( isset($data['shop_goods_sort']) )
		object(parent::ERROR)->check($data, 'shop_goods_sort', parent::TABLE_SHOP_GOODS, array('args'));
		if( isset($data['shop_goods_state']) )
		object(parent::ERROR)->check($data, 'shop_goods_state', parent::TABLE_SHOP_GOODS, array('args'));
		if( isset($data['shop_goods_admin_note']) )
		object(parent::ERROR)->check($data, 'shop_goods_admin_note', parent::TABLE_SHOP_GOODS, array('args'));
		
		//获取旧数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find_shop($data['shop_goods_id'], $data['merchant_id']);
		if( empty($shop_goods_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'shop_goods_parent_id', 
			'shop_goods_name', 
			'shop_goods_sn',
			'shop_goods_property',
			'shop_goods_index',
			'shop_goods_info',
			'shop_goods_warning',
			'shop_goods_details',
			'shop_goods_stock_warning',
			'shop_goods_stock_mode',
			'shop_goods_keywords',
			'shop_goods_description',
			'shop_goods_sort',
			'shop_goods_seller_note',
			'shop_goods_state',
			'shop_goods_admin_note',
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($update_data as $key => $value){
			if( isset($shop_goods_data[$key]) ){
				if($shop_goods_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		//判断父级
		if( !empty($update_data["shop_goods_parent_id"]) ){
			object(parent::ERROR)->check($data, 'shop_goods_parent_id', parent::TABLE_SHOP_GOODS, array('args', 'exists_id'));
			if($update_data["shop_goods_parent_id"] == $data["shop_goods_id"]){
				throw new error("父级关联不能设为自己");
			}
		}
		
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		//更新时间
		$update_data['shop_goods_update_time'] = time();
		$update_where = array(
			array('shop_goods_id=[+]', $data['shop_goods_id']),
			array('[and] shop_id=[+]', $data['merchant_id'])
		);
		if( object(parent::TABLE_SHOP_GOODS)->update( $update_where, $update_data ) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['shop_goods_id'];
		}else{
			throw new error("操作失败");
		}
		
		
	}
	
	
	
	/**
	 * 商家后台管理-商品列表
	 * 
     * MERCHANTMANAGESELFSHOPGOODSLIST
     * {"class":"merchant/manage","method":"api_self_shop_goods_list"}
	 * 
	 * 注意版本号的命名规范：merchant manage [接口去掉"version_"的名称]
	 * [{"merchant_id":"商家ID[必须]"}]
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_self_shop_goods_list($data = array()){
		//权限检测
		$this->_authority($data);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'name_desc' => array('shop_goods_name', true),
			'name_asc' => array('shop_goods_name', false),
			'state_desc' => array('shop_goods_state', true),
			'state_asc' => array('shop_goods_state', false),
			'insert_time_desc' => array('shop_goods_insert_time', true),
			'insert_time_asc' => array('shop_goods_insert_time', false),
			'update_time_desc' => array('shop_goods_update_time', true),
			'update_time_asc' => array('shop_goods_update_time', false),
			'sort_desc' => array('shop_goods_sort', true),
			'sort_asc' => array('shop_goods_sort', false),
			
			'stock_sum_desc' => array('shop_goods_stock_sum', true),
			'stock_sum_asc' => array('shop_goods_stock_sum', false),
			
			'min_price_desc' => array('shop_goods_min_price', true),
			'min_price_asc' => array('shop_goods_min_price', false),
			
			'max_price_desc' => array('shop_goods_max_price', true),
			'max_price_asc' => array('shop_goods_max_price', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('shop_goods_id', false);
		
		
		$config["where"][] = array('[and] sg.shop_goods_trash = 0');
		$config["where"][] = array('[and] sg.shop_id=[+]', $data['merchant_id']);
		
		
		if(!empty($data['search'])){
			
			if( isset($data['search']['shop_goods_id']) && is_string($data['search']['shop_goods_id']) ){
				$config["where"][] = array('[and] sg.shop_goods_id=[+]', $data['search']['shop_goods_id']);
			}

			if (isset($data['search']['shop_goods_name']) && is_string($data['search']['shop_goods_name'])) {
                $config['where'][] = array('[and] sg.shop_goods_name LIKE "%[-]%"', $data['search']['shop_goods_name']);
            }

			if( isset($data['search']['shop_goods_sn']) && is_string($data['search']['shop_goods_sn']) ){
				$config["where"][] = array('[and] sg.shop_goods_sn=[+]', $data['search']['shop_goods_sn']);
			}
			
			if( isset($data['search']['state']) && 
			(is_string($data['search']['state']) || is_numeric($data['search']['state'])) &&
			in_array($data['search']['state'], array("0", "1", "2", "3")) ){
				$config["where"][] = array('[and] sg.shop_goods_state=[+]', $data['search']['state']);
				}
			
			if( isset($data['search']['property']) && 
			(is_string($data['search']['property']) || is_numeric($data['search']['property'])) &&
			in_array($data['search']['property'], array("0", "1")) ){
				$config["where"][] = array('[and] sg.shop_goods_property=[+]', $data['search']['property']);
				}
			
			if( isset($data['search']['when']) && 
			(is_string($data['search']['when']) || is_numeric($data['search']['when'])) &&
			in_array($data['search']['when'], array("0", "1")) ){
				$sql_join_shop_goods_when = object(parent::TABLE_SHOP_GOODS_WHEN)->sql_join_goods_id("sg");
				if( $data['search']['when'] == 0){
					$config["where"][] = array('[and] ('.$sql_join_shop_goods_when.') IS NULL', NULL, TRUE);
				}else{
					$config["where"][] = array('[and] ('.$sql_join_shop_goods_when.') IS NOT NULL', NULL, TRUE);
				}
			}
		}

		$data = object(parent::TABLE_SHOP_GOODS)->select_page($config);
		if( !empty($data["data"]) ){
			$data["data"] = object(parent::TABLE_SHOP_GOODS)->get_additional_data($data["data"]);
		}
		
		return $data;
	}
	
	
		
	
	
	
			
	/**
	 * 商家后台，获取一条属于该商家的商品数据
	 * $data = arrray(
	 * 	merchant_id		商家ID
	 * 	shop_goods_id 	商品ID
	 * )
	 * 
	 * MERCHANTMANAGESELFSHOPGOODSGET
	 * {"class":"merchant/manage","method":"api_self_shop_goods_get"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_self_shop_goods_get($data = array()){
		//权限检测
		$this->_authority($data);
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		$get_data = object(parent::TABLE_SHOP_GOODS)->find_shop($data['shop_goods_id'], $data['merchant_id']);
		
		if( !empty($get_data) ){
			$data = array($get_data);
			$data = object(parent::TABLE_SHOP_GOODS)->get_additional_data($data);
			$get_data = $data[0];
		}
		
		return $get_data;
	}
	
	
	
	
	/**
	 * 商家后台管理-商品SKU列表
	 * 
     * MERCHANTMANAGESELFSHOPGOODSSKULIST
     * {"class":"merchant/manage","method":"api_self_shop_goods_sku_list"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_self_shop_goods_sku_list($data = array()){
		//权限检测
		$this->_authority($data);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'price_desc' => array('shop_goods_sku_price', true),
			'price_asc' => array('shop_goods_sku_price', false),
			'market_price_desc' => array('shop_goods_sku_market_price', true),
			'market_price_asc' => array('shop_goods_sku_market_price', false),
			
			'cost_price_desc' => array('shop_goods_sku_cost_price', true),
			'cost_price_asc' => array('shop_goods_sku_cost_price', false),
			
			'insert_time_desc' => array('shop_goods_sku_insert_time', true),
			'insert_time_asc' => array('shop_goods_sku_insert_time', false),
			'update_time_desc' => array('shop_goods_sku_update_time', true),
			'update_time_asc' => array('shop_goods_sku_update_time', false),
			'stock_desc' => array('shop_goods_sku_stock', true),
			'stock_asc' => array('shop_goods_sku_stock', false),
			'sort_desc' => array('shop_goods_spu_sort', true),
			'sort_asc' => array('shop_goods_spu_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('shop_goods_sku_id', false);
		
		$config["where"][] = array('[and] sg.shop_id=[+]', $data['merchant_id']);
		if(!empty($data['search'])){
			if( isset($data['search']['id']) && is_string($data['search']['id']) ){
				$config["where"][] = array('[and] sgs.shop_goods_sku_id=[+]', $data['search']['id']);
				}
			if( isset($data['search']['shop_goods_id']) && is_string($data['search']['shop_goods_id']) ){
				$config["where"][] = array('[and] sgs.shop_goods_id=[+]', $data['search']['shop_goods_id']);
				}
		}
		
		
		return object(parent::TABLE_SHOP_GOODS_SKU)->select_page($config);
				
	}
	
	
	
	/**
	 * 商家后台管理-删除规格
	 * 
	 * MERCHANTMANAGESELFSHOPGOODSSKUREMOVE
	 * {"class":"merchant/manage","method":"api_self_shop_goods_sku_remove"}
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_self_shop_goods_sku_remove($data = array()){
		//权限检测
		$this->_authority($data);
		//数据检测
		object(parent::ERROR)->check($data, 'shop_goods_sku_id', parent::TABLE_SHOP_GOODS_SKU, array('args'));
		
		//获取旧数据
		$shop_goods_sku_data = object(parent::TABLE_SHOP_GOODS_SKU)->find($data['shop_goods_sku_id']);
		if( empty($shop_goods_sku_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//查询商品数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find_shop($shop_goods_sku_data['shop_goods_id'], $data['merchant_id']);
		if( empty($shop_goods_data) ){
			throw new error("商品ID有误，数据不存在");
		}
		
		if( object(parent::TABLE_SHOP_GOODS_SKU)->remove($data['shop_goods_sku_id']) ){
			
			//更新商品修改时间
			object(parent::TABLE_SHOP_GOODS)->update( 
				array( array('shop_goods_id=[+]', $shop_goods_sku_data['shop_goods_id']) ), 
				array('shop_goods_update_time' => time() ) 
			);
			
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $shop_goods_sku_data);
			return $data['shop_goods_sku_id'];
		}else{
			throw new error("操作失败");
		}
		
		
	}
	
	
	
	/**
	 * 商家后台管理-商品规格的添加
	 * $data = array(
	 * 	'shop_goods_id' => string 	商品ID
	 * 	'merchant_id'	=> string	商家ID
	 * )
	 * 
	 * MERCHANTMANAGESELFSHOPGOODSSKUADD
	 * {"class":"merchant/manage","method":"api_self_shop_goods_sku_add"}
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_self_shop_goods_sku_add($data = array()){
		//权限检测
		$this->_authority($data);
		
		//数据检测
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'image_id', parent::TABLE_IMAGE, array('format', 'legal_id'));
		
		object(parent::ERROR)->check($data, 'shop_goods_sku_stock', parent::TABLE_SHOP_GOODS_SKU, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_sku_info', parent::TABLE_SHOP_GOODS_SKU, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_sku_cost_price', parent::TABLE_SHOP_GOODS_SKU, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_sku_price', parent::TABLE_SHOP_GOODS_SKU, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_sku_market_price', parent::TABLE_SHOP_GOODS_SKU, array('args'));
		
		//查询商品数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find_shop($data['shop_goods_id'], $data['merchant_id']);
		if( empty($shop_goods_data) ){
			throw new error("商品ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'shop_goods_id', 
			'image_id', 
			'shop_goods_sku_stock', 
			'shop_goods_sku_info', 
			'shop_goods_sku_price',
			'shop_goods_sku_cost_price',
			'shop_goods_sku_market_price',
			);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		//收集 属性ID
		$shop_goods_spu_id = array();
		if( !empty($data['shop_goods_spu_id']) && is_array($data['shop_goods_spu_id'])){
			//清理数据
			foreach($data['shop_goods_spu_id'] as $value){
				if(is_string($value) || is_numeric($value)){
					//要将数据过滤
					$shop_goods_spu_id[] = cmd(array($value), 'str addslashes');
				}
			}
		}
		
		//获取 属性数据
		if( !empty($shop_goods_spu_id) ){
			$in_string = "\"".implode("\",\"", $shop_goods_spu_id)."\"";
			$spu_where = array();
			$spu_where[] = array("shop_goods_id=[+]", $data["shop_goods_id"]);
			$spu_where[] = array("[and] shop_goods_spu_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
			//获取属性数据
			$goods_spu_data = object(parent::TABLE_SHOP_GOODS_SPU)->select(array("where"=>$spu_where));
		}
		
		//判断是否存在 属性值
		if( empty($goods_spu_data) ){
			throw new error("商品属性不存在");
		}
		
		
		$check_where1 = array();
		$check_where1[] = array("[and] shop_goods_id=[+]", $data["shop_goods_id"]);
		$check_where2 = array();
		
		$insert_data["shop_goods_spu_id"] = array();
		foreach($goods_spu_data as $value){
			$insert_data["shop_goods_spu_id"][] = $value["shop_goods_spu_id"];
			if( empty($check_where2) ){
				$check_where2[] = array('[and] shop_goods_spu_id like "%,[-],%"', $value["shop_goods_spu_id"], true);
			}else{
				$check_where2[] = array('[or] shop_goods_spu_id like "%,[-],%"', $value["shop_goods_spu_id"], true);
			}
		}
		
		//判断属性是否已经存在 库存售价
		$goods_sku_check_data = object(parent::TABLE_SHOP_GOODS_SKU)->select_two_where($check_where1, $check_where2);
		if( !empty($goods_sku_check_data) ){
			$pattern = "/(".implode("|", $insert_data["shop_goods_spu_id"]).")/i";
			
			foreach($goods_sku_check_data as $sku_key => $sku_value){
				$sku_count = count(explode(",", trim($sku_value["shop_goods_spu_id"], ",")));
				preg_match_all($pattern, $sku_value["shop_goods_spu_id"], $matches);
				
				//判断匹配到的个数，如果相等则代表已经存在，禁止重复添加库存
				if( !empty($matches[0]) ){
					//匹配到的个数与本身的属性个数相等，并且跟添加的属性个数相等，则表示已经存在
					if(count($matches[0]) == $sku_count && count($matches[0]) == count($insert_data["shop_goods_spu_id"])){
						throw new error("操作失败！该属性列表的规格已经存在");
					}
				}
			}
		}
		
		//将属性id格式化
		$insert_data["shop_goods_spu_id"] = ",".implode(",", $insert_data["shop_goods_spu_id"]).",";
		//获取id号
		$insert_data['shop_goods_sku_id'] = object(parent::TABLE_SHOP_GOODS_SKU)->get_unique_id();
		//用户数据
		$insert_data['user_id'] = $_SESSION['user_id'];
		//创建时间
		$insert_data['shop_goods_sku_insert_time'] = time();
		//更新时间
		$insert_data['shop_goods_sku_update_time'] = time();
		
		if( object(parent::TABLE_SHOP_GOODS_SKU)->insert($insert_data) ){
			
			//更新商品修改时间
			object(parent::TABLE_SHOP_GOODS)->update( 
				array( array('shop_goods_id=[+]', $data['shop_goods_id']) ), 
				array('shop_goods_update_time' => time() ) 
			);
			
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $insert_data);
			return $insert_data['shop_goods_sku_id'];
		}else{
			throw new error("操作失败");
		}
		
		
	}
	
	
	
	
	/**
	 * 商家后台管理-编辑规格
	 * 
	 * MERCHANTMANAGESELFSHOPGOODSSKUEDIT
	 * {"class":"merchant/manage","method":"api_self_shop_goods_sku_edit"}
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_self_shop_goods_sku_edit($data = array()){
		//权限检测
		$this->_authority($data);
		
		//数据检测
		object(parent::ERROR)->check($data, 'shop_goods_sku_id', parent::TABLE_SHOP_GOODS_SKU, array('args'));
		
		if( isset($data['shop_goods_id']) )
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		if( isset($data['image_id']) )
		object(parent::ERROR)->check($data, 'image_id', parent::TABLE_IMAGE, array('format'));
		
		if( isset($data['shop_goods_sku_stock']) )
		object(parent::ERROR)->check($data, 'shop_goods_sku_stock', parent::TABLE_SHOP_GOODS_SKU, array('args'));
		if( isset($data['shop_goods_sku_info']) )
		object(parent::ERROR)->check($data, 'shop_goods_sku_info', parent::TABLE_SHOP_GOODS_SKU, array('args'));
		if( isset($data['shop_goods_sku_price']) )
		object(parent::ERROR)->check($data, 'shop_goods_sku_price', parent::TABLE_SHOP_GOODS_SKU, array('args'));
		if( isset($data['shop_goods_sku_cost_price']) )
		object(parent::ERROR)->check($data, 'shop_goods_sku_cost_price', parent::TABLE_SHOP_GOODS_SKU, array('args'));
		if( isset($data['shop_goods_sku_market_price']) )
		object(parent::ERROR)->check($data, 'shop_goods_sku_market_price', parent::TABLE_SHOP_GOODS_SKU, array('args'));
		
		//获取旧数据
		$shop_goods_sku_data = object(parent::TABLE_SHOP_GOODS_SKU)->find($data['shop_goods_sku_id']);
		if( empty($shop_goods_sku_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//查询商品数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find_shop($shop_goods_sku_data['shop_goods_id'], $data['merchant_id']);
		if( empty($shop_goods_data) ){
			throw new error("商品ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'shop_goods_id', 
			'image_id', 
			'shop_goods_sku_stock', 
			'shop_goods_sku_info', 
			'shop_goods_sku_price',
			'shop_goods_sku_cost_price',
			'shop_goods_sku_market_price',
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($shop_goods_sku_data as $key => $value){
			if(isset($shop_goods_sku_data[$key]) && isset($update_data[$key]) ){
				if($update_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		//修改商品ID， 不能为空
		if( isset($update_data["shop_goods_id"]) ){
			object(parent::ERROR)->check($update_data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('exists_id'));
		}
		
		//修改图片ID
		if( !empty($update_data["image_id"]) ){
			object(parent::ERROR)->check($update_data, 'image_id', parent::TABLE_SHOP_GOODS, array('legal_id'));
		}
		
		
		//收集 属性ID
		$shop_goods_spu_id = array();
		if( !empty($data['shop_goods_spu_id']) && is_array($data['shop_goods_spu_id'])){
			//清理数据
			foreach($data['shop_goods_spu_id'] as $value){
				if(is_string($value) || is_numeric($value)){
					//要将数据过滤
					$shop_goods_spu_id[] = cmd(array($value), 'str addslashes');
				}
			}
		}
		
		//获取 属性数据
		if( !empty($shop_goods_spu_id) ){
			$in_string = "\"".implode("\",\"", $shop_goods_spu_id)."\"";
			$spu_where = array();
			$spu_where[] = array("shop_goods_id=[+]", $data["shop_goods_id"]);
			$spu_where[] = array("[and] shop_goods_spu_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
			//获取属性数据
			$goods_spu_data = object(parent::TABLE_SHOP_GOODS_SPU)->select(array("where"=>$spu_where));
		}
		
		//判断是否存在 属性值
		if( empty($goods_spu_data) ){
			throw new error("商品规格属性不存在");
		}
		
		
		$check_where1 = array();
		$check_where1[] = array("[and] shop_goods_sku_id<>[+]", $data["shop_goods_sku_id"]);
		$check_where1[] = array("[and] shop_goods_id=[+]", $data["shop_goods_id"]);
		$check_where2 = array();
		
		$update_data["shop_goods_spu_id"] = array();
		foreach($goods_spu_data as $value){
			$update_data["shop_goods_spu_id"][] = $value["shop_goods_spu_id"];
			if( empty($check_where2) ){
				$check_where2[] = array('[and] shop_goods_spu_id like "%,[-],%"', $value["shop_goods_spu_id"], true);
			}else{
				$check_where2[] = array('[or] shop_goods_spu_id like "%,[-],%"', $value["shop_goods_spu_id"], true);
			}
		}
		
		
		//判断属性是否已经存在 库存售价
		$goods_sku_check_data = object(parent::TABLE_SHOP_GOODS_SKU)->select_two_where($check_where1, $check_where2);
		if( !empty($goods_sku_check_data) ){
			$pattern = "/(".implode("|", $update_data["shop_goods_spu_id"]).")/i";
			
			foreach($goods_sku_check_data as $sku_key => $sku_value){
				$sku_count = count(explode(",", trim($sku_value["shop_goods_spu_id"], ",")));
				preg_match_all($pattern, $sku_value["shop_goods_spu_id"], $matches);
				
				//判断匹配到的个数，如果相等则代表已经存在，禁止重复添加库存
				if( !empty($matches[0]) ){
					//匹配到的个数与本身的属性个数相等，并且跟添加的属性个数相等，则表示已经存在
					if(count($matches[0]) == $sku_count && count($matches[0]) == count($update_data["shop_goods_spu_id"])){
						throw new error("操作失败！该属性列表的规格已经存在");
					}
				}
				
			}

		}
		
		//将属性id格式化
		$update_data["shop_goods_spu_id"] = ",".implode(",", $update_data["shop_goods_spu_id"]).",";
		if($update_data["shop_goods_spu_id"] == $shop_goods_sku_data["shop_goods_spu_id"]){
			unset($update_data["shop_goods_spu_id"]);
		}
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		//更新时间
		$update_data['shop_goods_sku_update_time'] = time();
		if( object(parent::TABLE_SHOP_GOODS_SKU)->update( array(array('shop_goods_sku_id=[+]', (string)$data['shop_goods_sku_id'])), $update_data) ){
				
			if( isset($update_data["shop_goods_id"]) ){
				//更新商品修改时间
				object(parent::TABLE_SHOP_GOODS)->update( 
					array( array('shop_goods_id=[+]', $update_data['shop_goods_id']) ), 
					array('shop_goods_update_time' => time() ) 
				);	
			}
			
			//更新商品修改时间
			object(parent::TABLE_SHOP_GOODS)->update( 
				array( array('shop_goods_id=[+]', $shop_goods_sku_data['shop_goods_id']) ), 
				array('shop_goods_update_time' => time() ) 
			);	
				
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['shop_goods_sku_id'];
		}else{
			throw new error("操作失败");
		}
		
		
		
	}
	
	
	
	
		
	/**
	 * 商家后台管理-获取商品属性的所有父级与子级关系列表
	 * 
	 * MERCHANTMANAGESELFSHOPGOODSSPUOPTION
	 * {"class":"merchant/manage","method":"api_self_shop_goods_spu_option"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_self_shop_goods_spu_option($data = array()){
		//权限检测
		$this->_authority($data);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => array(),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'name_desc' => array('shop_goods_spu_name', true),
			'name_asc' => array('shop_goods_spu_name', false),
			'insert_time_desc' => array('shop_goods_spu_insert_time', true),
			'insert_time_asc' => array('shop_goods_spu_insert_time', false),
			'update_time_desc' => array('shop_goods_spu_update_time', true),
			'update_time_asc' => array('shop_goods_spu_update_time', false),
			'sort_desc' => array('shop_goods_spu_sort', true),
			'sort_asc' => array('shop_goods_spu_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('shop_goods_spu_id', false);
		
		if(!empty($data['search'])){
			if( isset($data['search']['id']) && is_string($data['search']['id']) ){
				$config["where"][] = array('[and] shop_goods_spu_id=[+]', $data['search']['id']);
				}
			
			if( isset($data['search']['parent_id']) && is_string($data['search']['parent_id']) ){
				$config["where"][] = array('[and] shop_goods_spu_parent_id=[+]', $data['search']['parent_id']);
				}
			
			if( isset($data['search']['shop_goods_id']) && is_string($data['search']['shop_goods_id']) ){
				$config["where"][] = array('[and] shop_goods_id=[+]', $data['search']['shop_goods_id']);
				}

		}
		
		$parent_config = $config;
		$son_config = $config;
		
		if( !empty($data["type"]) && $data["type"] == "son" ){
			$parent_config["where"][] = array('[and] shop_goods_spu_parent_id<>""');
		}else{
			$parent_config["where"][] = array("[and] shop_goods_spu_parent_id=\"\"");
		}
		
		return object(parent::TABLE_SHOP_GOODS_SPU)->select_parent_son_all($parent_config, $son_config);
		
	}
	
	
	/**
	 * 商家后台管理-商品属性的添加
	 * $data = array(
	 * 	'shop_goods_id' => string 商品ID
	 * 	'merchant_id'	=> string 商家ID
	 * )
	 * 
	 * MERCHANTMANAGESELFSHOPGOODSSPUADD
	 * {"class":"merchant/manage","method":"api_self_shop_goods_spu_add"}
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_self_shop_goods_spu_add($data = array()){
		//权限检测
		$this->_authority($data);
		
		//数据检测
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'image_id', parent::TABLE_IMAGE, array('format', 'legal_id'));
		object(parent::ERROR)->check($data, 'shop_goods_spu_name', parent::TABLE_SHOP_GOODS_SPU, array('args', 'length'));
		object(parent::ERROR)->check($data, 'shop_goods_spu_parent_id', parent::TABLE_SHOP_GOODS_SPU, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_spu_info', parent::TABLE_SHOP_GOODS_SPU, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_spu_sort', parent::TABLE_SHOP_GOODS_SPU, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_spu_required', parent::TABLE_SHOP_GOODS_SPU, array('args'));
		
		//查询商品数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find_shop($data['shop_goods_id'], $data['merchant_id']);
		if( empty($shop_goods_data) ){
			throw new error("商品ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'shop_goods_id', 
			'image_id', 
			'shop_goods_spu_parent_id', 
			'shop_goods_spu_name', 
			'shop_goods_spu_info',
			'shop_goods_spu_sort',
			'shop_goods_spu_required'
			);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		//判断该分类是否存在、是否为顶级分类
		if( !empty($insert_data["shop_goods_spu_parent_id"]) ){
			$parent_data = object(parent::TABLE_SHOP_GOODS_SPU)->find($insert_data['shop_goods_spu_parent_id']);
			if( empty($parent_data) ){
				throw new error("父级ID有误，数据不存在");
			}
			if( !empty($parent_data['shop_goods_spu_parent_id']) ){
				throw new error("所编辑的父级并不是顶级分类");
			}
		}
		
		//获取id号
		$insert_data['shop_goods_spu_id'] = object(parent::TABLE_SHOP_GOODS_SPU)->get_unique_id();
		//用户数据
		$insert_data['user_id'] = $_SESSION['user_id'];
		//创建时间
		$insert_data['shop_goods_spu_insert_time'] = time();
		//更新时间
		$insert_data['shop_goods_spu_update_time'] = time();
		
		if( object(parent::TABLE_SHOP_GOODS_SPU)->insert($insert_data) ){
			
			//更新商品修改时间
			object(parent::TABLE_SHOP_GOODS)->update( 
				array( array('shop_goods_id=[+]', $data['shop_goods_id']) ), 
				array('shop_goods_update_time' => time() ) 
			);
			
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $insert_data);
			return $insert_data['shop_goods_spu_id'];
		}else{
			throw new error("操作失败");
		}
		
		
	}
	
	
	
	
	
	/**
	 * 商家后台管理-编辑属性
	 * 
	 * MERCHANTMANAGESELFSHOPGOODSSPUEDIT
	 * {"class":"merchant/manage","method":"api_self_shop_goods_spu_edit"}
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_self_shop_goods_spu_edit($data = array()){
		//权限检测
		$this->_authority($data);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'shop_goods_spu_id', parent::TABLE_SHOP_GOODS_SPU, array('args'));
		
		if( isset($data['shop_goods_id']) )
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		if( isset($data['image_id']) )
		object(parent::ERROR)->check($data, 'image_id', parent::TABLE_IMAGE, array('format'));
		if( isset($data['shop_goods_spu_name']) )
		object(parent::ERROR)->check($data, 'shop_goods_spu_name', parent::TABLE_SHOP_GOODS_SPU, array('args', 'length'));
		if( isset($data['shop_goods_spu_parent_id']) )
		object(parent::ERROR)->check($data, 'shop_goods_spu_parent_id', parent::TABLE_SHOP_GOODS_SPU, array('args'));
		if( isset($data['shop_goods_spu_info']) )
		object(parent::ERROR)->check($data, 'shop_goods_spu_info', parent::TABLE_SHOP_GOODS_SPU, array('args'));
		if( isset($data['shop_goods_spu_sort']) )
		object(parent::ERROR)->check($data, 'shop_goods_spu_sort', parent::TABLE_SHOP_GOODS_SPU, array('args'));
		if( isset($data['shop_goods_spu_required']) )
		object(parent::ERROR)->check($data, 'shop_goods_spu_required', parent::TABLE_SHOP_GOODS_SPU, array('args'));
		
		//获取旧数据
		$shop_goods_spu_data = object(parent::TABLE_SHOP_GOODS_SPU)->find($data['shop_goods_spu_id']);
		if( empty($shop_goods_spu_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//查询商品数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find_shop($shop_goods_spu_data['shop_goods_id'], $data['merchant_id']);
		if( empty($shop_goods_data) ){
			throw new error("商品ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'shop_goods_id', 
			'image_id', 
			'shop_goods_spu_parent_id', 
			'shop_goods_spu_name', 
			'shop_goods_spu_info',
			'shop_goods_spu_sort',
			'shop_goods_spu_required'
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		foreach($shop_goods_spu_data as $key => $value){
			if(isset($shop_goods_spu_data[$key]) && isset($update_data[$key]) ){
				if($update_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		//修改商品ID， 不能为空
		if( isset($update_data["shop_goods_id"]) ){
			object(parent::ERROR)->check($update_data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('exists_id'));
		}
		
		//修改图片ID
		if( !empty($update_data["image_id"]) ){
			object(parent::ERROR)->check($update_data, 'image_id', parent::TABLE_SHOP_GOODS, array('legal_id'));
		}
		
		//父级不能是自己
		if( !empty($update_data["shop_goods_spu_parent_id"]) ){
			if( $update_data["shop_goods_spu_parent_id"] == $data['shop_goods_spu_id'] ){
				throw new error("不能将自己设为父级");
			}
			
			//判断该分类是否存在、是否为顶级分类
			$parent_data = object(parent::TABLE_SHOP_GOODS_SPU)->find($update_data['shop_goods_spu_parent_id']);
			if( empty($parent_data) ){
				throw new error("父级ID有误，数据不存在");
			}
			
			if( !empty($parent_data['shop_goods_spu_parent_id']) ){
				throw new error("所编辑的父级并不是顶级的产品规格属性");
			}
			
		}
		
		
		//如果父级 编辑成 子级，必须要去掉子级
		if( empty($shop_goods_spu_data["shop_goods_spu_parent_id"]) &&
		isset($update_data["shop_goods_spu_parent_id"]) ){
			if( object(parent::TABLE_SHOP_GOODS_SPU)->find_exists_son_id($data['shop_goods_spu_id']) ){
				throw new error("该数据下存在子级，请先清理子级才能变更该数据的父级");
			}
		}
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		
		//更新时间
		$update_data['shop_goods_spu_update_time'] = time();
		if( object(parent::TABLE_SHOP_GOODS_SPU)->update( array(array('shop_goods_spu_id=[+]', (string)$data['shop_goods_spu_id'])), $update_data) ){
			
			if( isset($update_data["shop_goods_id"]) ){
				//更新商品修改时间
				object(parent::TABLE_SHOP_GOODS)->update( 
					array( array('shop_goods_id=[+]', $update_data['shop_goods_id']) ), 
					array('shop_goods_update_time' => time() ) 
				);	
			}
			
			//更新商品修改时间
			object(parent::TABLE_SHOP_GOODS)->update( 
				array( array('shop_goods_id=[+]', $shop_goods_spu_data['shop_goods_id']) ), 
				array('shop_goods_update_time' => time() ) 
			);	
				
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['shop_goods_spu_id'];
		}else{
			throw new error("操作失败");
		}
		
		
	}
	
	
	
	
	
		
	/**
	 * 商家后台管理-获取商品的图片数据列表
	 * 
	 * MERCHANTMANAGESELFSHOPGOODSIMAGELIST
	 * {"class":"merchant/manage","method":"api_self_shop_goods_image_list"}
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function api_self_shop_goods_image_list($data = array()){
		//权限检测
		$this->_authority($data);
		//筛选
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'sort_desc' => array('image_sort', true),
			'sort_asc' => array('image_sort', false),
			'name_desc' => array('image_name', true),
			'name_asc' => array('image_name', false),
			'type_desc' => array('image_type', true),
			'type_asc' => array('image_type', false),
			'size_desc' => array('image_size', true),
			'size_asc' => array('image_size', false),
			'main_desc' => array('shop_goods_image_main', true),
			'main_asc' => array('shop_goods_image_main', false),
			'state_desc' => array('image_state', true),
			'state_asc' => array('image_state', false),
			'insert_time_desc' => array('image_insert_time', true),
			'insert_time_asc' => array('image_insert_time', false),
			'update_time_desc' => array('image_update_time', true),
			'update_time_asc' => array('image_update_time', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('shop_goods_image_id', false);
		
		$config["where"][] = array('[and] sgi.shop_goods_id=[+]', $data['shop_goods_id']);
		//查询商品数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find_shop($data['shop_goods_id'], $data['merchant_id']);
		if( empty($shop_goods_data) ){
			throw new error("商品ID有误，数据不存在");
		}
		
		return object(parent::TABLE_SHOP_GOODS_IMAGE)->select_page($config);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>