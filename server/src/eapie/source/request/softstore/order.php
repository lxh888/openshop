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
class order extends \eapie\source\request\softstore {
	
	
	
	
	/**
	 * 前台的当前登录用户的订单列表
	 * 并且可以获取下级的订单列表
	 * $request["type"] = "son" 是获取下级订单列表 否则获取 自己的订单列表
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
	public function api_self_list( $data = array() ){
		//检查是否已初始化
		object(parent::REQUEST_USER)->check();
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_USER),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'id_desc' => array('ss_order_id', true),
			'id_asc' => array('ss_order_id', false),
			'name_desc' => array('ss_order_name', true),
			'name_asc' => array('ss_order_name', false),
			'phone_desc' => array('ss_order_phone', true),
			'phone_asc' => array('ss_order_phone', false),
			'qq_desc' => array('ss_order_qq', true),
			'qq_asc' => array('ss_order_qq', false),
			'wechat_desc' => array('ss_order_wechat', true),
			'wechat_asc' => array('ss_order_wechat', false),
			'contact_state_desc' => array('ss_order_contact_state', true),
			'contact_state_asc' => array('ss_order_contact_state', false),
			'contact_time_desc' => array('ss_order_contact_time', true),
			'contact_time_asc' => array('ss_order_contact_time', false),
			'time_desc' => array('ss_order_time', true),
			'time_asc' => array('ss_order_time', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('ss_order_id', false);
		
		
		$config["where"][] = array("[and] so.ss_order_trash=0");//没有被删除的
		
		//必须是当前登录用户的下级的 订单
		if( !empty($data['type']) && $data['type'] == "son" ){
			$sql_user_son_id = object(parent::TABLE_USER)->sql_select_son_id($_SESSION['user_id']);
			$config["where"][] = array("[and] so.user_id IN([-])", $sql_user_son_id, true);
		}else{
			$config["where"][] = array("[and] so.user_id=[+]", $_SESSION['user_id']);
		}
		
		if( !empty($data['search']) ){
			if( isset($data['search']['id']) && 
			(is_string($data['search']['id']) || is_numeric($data['search']['id'])) ){
				$config["where"][] = array('[and] so.ss_order_id=[+]', $data['search']['id']);
				}

			if( isset($data['search']['name']) && 
			(is_string($data['search']['name']) || is_numeric($data['search']['name'])) ){
				$config["where"][] = array('[and] so.ss_order_name=[+]', $data['search']['name']);
				}

			if( isset($data['search']['phone']) && 
			(is_string($data['search']['phone']) || is_numeric($data['search']['phone'])) ){
				$config["where"][] = array('[and] so.ss_order_phone=[+]', $data['search']['phone']);
				}
			
			if( isset($data['search']['contact_state']) && 
			(is_string($data['search']['contact_state']) || is_numeric($data['search']['contact_state'])) &&
			in_array($data['search']['contact_state'], array("0", "1")) ){
				$config["where"][] = array('[and] so.ss_order_contact_state=[+]', $data['search']['contact_state']);
				}
		}
		
		object(parent::TABLE_SOFTSTORE_ORDER)->select_page($config);
	}
	
	
	
	/**
	 * 获取一条下级的订单数据
	 * $data = arrray(
	 * 	ss_order_id 订单ID
	 * )
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_self_get( $data = array() ){
		//检查是否已初始化
		object(parent::REQUEST_USER)->check();
		object(parent::ERROR)->check($data, 'ss_order_id', parent::TABLE_SOFTSTORE_ORDER, array('args'));
		
		//必须是 登录 用户自己和该用户下级的 订单
		$sql_user_son_id = object(parent::TABLE_USER)->sql_select_son_id($_SESSION['user_id']);
		$config["where"][] = array("[and] so.user_id IN([-])", $sql_user_son_id, true);
		$config["where"][] = array("[and] so.ss_order_id=[+]", $data['ss_order_id']);
		$config["where"][] = array("[and] so.ss_order_trash=0");//没有被删除的
		//or 自动会把左右的查询条件分开
		$config["where"][] = array("[or] so.user_id=[+]", $_SESSION['user_id']);
		$config["where"][] = array("[and] so.ss_order_id=[+]", $data['ss_order_id']);
		$config["where"][] = array("[and] so.ss_order_trash=0");//没有被删除的
		
		return object(parent::TABLE_SOFTSTORE_ORDER)->find_detail($data["ss_order_id"]);
	}
	
	
	
	
	
	/**
	 * 前台的当前登录用户添加一个订单
	 * {
	 * "ss_order_name":"姓名",
	 * "ss_order_phone":"手机号",
	 * "ss_order_company":"公司名称",
	 * "ss_order_region":"地区信息",
	 * "ss_order_wechat":"微信号",
	 * "ss_order_product 订单产品":[{ss_product_id:"产品ID","ss_product_attr_id":["产品属性ID","产品属性ID"], number:"购买的数量。可选，默认为1"}]
	 * }
	 * 
	 * 
	 * @param	array	$data
	 * @return 订单ID
	 */
	public function api_add( $data = array() ){
		//检查是否已初始化
		object(parent::REQUEST_USER)->check();
		object(parent::ERROR)->check($data, 'ss_order_name', parent::TABLE_SOFTSTORE_ORDER, array('args', 'length'));
		object(parent::ERROR)->check($data, 'ss_order_phone', parent::TABLE_SOFTSTORE_ORDER, array('args'));
		object(parent::ERROR)->check($data, 'ss_order_company', parent::TABLE_SOFTSTORE_ORDER, array('args', 'length'));
		object(parent::ERROR)->check($data, 'ss_order_region', parent::TABLE_SOFTSTORE_ORDER, array('args', 'length'));
		object(parent::ERROR)->check($data, 'ss_order_wechat', parent::TABLE_SOFTSTORE_ORDER, array('args', 'length'));
		object(parent::ERROR)->check($data, 'ss_order_qq', parent::TABLE_SOFTSTORE_ORDER, array('args', 'length'));
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'ss_order_name', 
			'ss_order_phone', 
			'ss_order_company', 
			'ss_order_region',
			'ss_order_wechat',
			'ss_order_qq',
			);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		//判断产品数据
		if( empty($data['ss_order_product']) )	throw new error("订单产品不能为空");
		if( !is_array($data['ss_order_product']) )	throw new error("订单产品不能为空");
		
		
		//获取所有的产品id，并获取所有的产品属性ID
		$product_ids = array();
		foreach($data['ss_order_product'] as $key => $value){
			//判断产品id是否合法
			$err = object(parent::ERROR)->check($value, 'ss_product_id', parent::TABLE_SOFTSTORE_PRODUCT, array('args'), NULL, true);
			if( !empty($err) && is_array($err)){
				continue;//存在错误
			}
			$product_ids[] = cmd(array($value['ss_product_id']), 'str addslashes');
		}
		if( empty($product_ids) ){
			throw new error("订单产品没有合法的产品ID");
		}
		
		//获取所有的订单产品的数据
		$product_config = array(
			"where" => array(
				array("ss_product_id IN([-])", "\"".implode("\",\"", $product_ids)."\"", true),
				array("[and] ss_product_state=1")
			),
			"select" => array(
				"ss_product_id",
				"ss_product_name"
			)
		);
		$product_data = object(parent::TABLE_SOFTSTORE_PRODUCT)->select($product_config);
		if( empty($product_data) ){
			throw new error("订单产品没有合法的产品数据");
		}
		
		//搜集产品数据
		foreach($product_data as $product_value){
			foreach($data['ss_order_product'] as $key => $value){
				if($product_value['ss_product_id'] == $value['ss_product_id']){
					$data['ss_order_product'][$key]['ss_product'] = $product_value;
					break;//可能存在多个相同的产品ID 只保留最前面的一个
				}
			}
		}
		
		
		//获取产品的属性
		$product_attr_ids = array();
		$product_ids = array();//重新获取id
		foreach($data['ss_order_product'] as $key => $value){
			//如果产品数据不存在
			if( empty($value['ss_product']) ){
				continue;
			}
			
			$product_ids[] = $value['ss_product']['ss_product_id'];//数据库查询出来的，不需要过滤
			//搜集合法的属性ID
			if( !empty($value['ss_product_attr_id']) && is_array($value['ss_product_attr_id']) ){
				foreach($value['ss_product_attr_id'] as $attr_k => $attr_id){
					if(is_string($attr_id) || is_numeric($attr_id)){
						//要将数据过滤
						$product_attr_ids[] = cmd(array($attr_id), 'str addslashes');
					}else{
						unset($data['ss_order_product'][$key]['ss_product_attr_id'][$attr_k]);	
						}
				}
			}
		}
		
		
		if( empty($product_attr_ids) ){
			throw new error("订单产品没有存在合法的产品属性ID");
		}
		
		//获取产品的所选属性。注意，即使没有选择必选项也会被要求必选
		$product_attr_config = array(
			"where" => array(
				array("spa.ss_product_id IN([-])", "\"".implode("\",\"", $product_ids)."\"", true),
				array("[and] spa.ss_product_attr_id IN([-])", "\"".implode("\",\"", $product_attr_ids)."\"", true),
				//or 自动会把左右的查询条件分开
				array('[or] spa.ss_product_attr_required=1'),//必须项
				array("spa.ss_product_id IN([-])", "\"".implode("\",\"", $product_ids)."\"", true),
			),
			"orderby" => array(
				array("ss_product_attr_parent_sort"),
				array("ss_product_attr_parent_insert_time"),
				array("ss_product_attr_sort"),
				array("ss_product_attr_insert_time"),
			)
		);
		$product_attr_data = object(parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE)->select_join_son($product_attr_config);
		if( empty($product_attr_data) ){
			throw new error("订单产品没有存在合法的产品属性数据");
		}
		
		//搜集产品属性
		foreach($data['ss_order_product'] as $key => $value){
			if( empty($product_attr_data) ){
				break;	//已经没有属性数据了
				}
			
			if( empty($data['ss_order_product'][$key]['ss_product_attr']) ){
				$data['ss_order_product'][$key]['ss_product_attr'] = array();
			}
			//价格搜集
			if( empty($data['ss_order_product'][$key]['money']) ){
				$data['ss_order_product'][$key]['money'] = 0;
			}
			
			foreach($product_attr_data as $product_attr_key => $product_attr_value){
				if($product_attr_value['ss_product_id'] != $value['ss_product_id']){
					continue;
				}
				
				//等于当前产品，并且是必选项
				if( !empty($product_attr_value['ss_product_attr_required']) ){
					$data['ss_order_product'][$key]['ss_product_attr'][] = $product_attr_value;
					$data['ss_order_product'][$key]['money'] += (int)$product_attr_value['ss_product_attr_money'];
					unset($product_attr_data[$product_attr_key]);
				}else
				//否则必须是传递过来的对应产品的属性。A产品传递B产品的属性，同时B产品也传递过来了。而放在A产品中的B产品属性无效
				if( in_array($product_attr_value['ss_product_attr_id'], $value['ss_product_attr_id']) ){
					$data['ss_order_product'][$key]['ss_product_attr'][] = $product_attr_value;
					$data['ss_order_product'][$key]['money'] += (int)$product_attr_value['ss_product_attr_money'];
					unset($product_attr_data[$product_attr_key]);
				}
				
			}
		}
		
		
		//开始收集要插入的订单产品的数据
		$order_product_insert_array = array();
		foreach($data['ss_order_product'] as $key => $value){
			//如果产品数据不存在
			if( empty($value['ss_product']) || empty($value['ss_product_attr']) ){
				continue;
			}
			
			if( isset($value['number']) ){
				$err = object(parent::ERROR)->check($value, 'number', parent::TABLE_SOFTSTORE_ORDER_PRODUCT, array('args'), "ss_order_product_number", true);
			}
			if( !isset($value['number']) || (!empty($err) && is_array($err)) ){
				$value['number'] = 1;
				}
			
			$order_product_insert_array[] = array(
				"ss_product_id" => $value['ss_product_id'],
				"ss_product_attr" => $value['ss_product_attr'],
				"ss_order_product_number" => $value['number'],
				"ss_product_name" => $value['ss_product']["ss_product_name"],
				"ss_order_product_money" => $value['money'],
			);
		}
		
		
		if( empty($order_product_insert_array) ){
			throw new error("订单产品不能为空，你并没有提交产品信息，或者提交的产品并没有发布");
		}
		
		//开始获取订单需要的数据
		$insert_data['user_id'] = $_SESSION['user_id'];
		if( empty($data['ss_order_phone']) ){
			$user_phone_data = object(parent::TABLE_USER_PHONE)->find_user_login_data($_SESSION['user_id']);
			$insert_data['ss_order_phone'] = $user_phone_data['user_phone_id'];
		}
		$insert_data['ss_order_contact_state'] = 0;
		$insert_data['ss_order_time'] = time();
		
		//创建订单并返回ID
		$ss_order_id = object(parent::TABLE_SOFTSTORE_ORDER)->insert($insert_data);
		
		//创建订单产品
		$error_order_product_ids = array();//记录下订单产品ID 错误时删除
		foreach($order_product_insert_array as $value){
			$insert_order_product_data = array(
				"ss_order_product_id" => object(parent::TABLE_SOFTSTORE_ORDER_PRODUCT)->get_unique_id(),
				"ss_order_id" => $ss_order_id,
				"ss_product_id" => $value["ss_product_id"],
				"ss_product_name" => $value["ss_product_name"],
				"ss_product_attr" => cmd(array($value["ss_product_attr"]), "json encode"),
				"ss_order_product_number" => $value["ss_order_product_number"],
				"ss_order_product_money" => $value["ss_order_product_money"],
				"ss_order_product_time" => time()
			);
			
			//插入
			if( !object(parent::TABLE_SOFTSTORE_ORDER_PRODUCT)->insert($insert_order_product_data) ){
				object(parent::TABLE_SOFTSTORE_ORDER)->remove($ss_order_id);//先删除生成的订单
				
				$where = array();
				$where[] = array("ss_order_product_id IN([-])", "\"".implode("\",\"", $error_order_product_ids)."\"", true);
				object(parent::TABLE_SOFTSTORE_ORDER_PRODUCT)->delete($where);
				
				throw new error("订单创建失败");
			}else{
				$error_order_product_ids[] = $insert_order_product_data["ss_order_product_id"];
			}
		}
		
		return $ss_order_id;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>