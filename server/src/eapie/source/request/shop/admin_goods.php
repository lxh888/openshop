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



namespace eapie\source\request\shop;
use eapie\main;
use eapie\error;
class admin_goods extends \eapie\source\request\shop {
	
	
		
	
	/**
	 * 添加商品
	 * 
	 * SHOPADMINGOODSADD
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADD);
		
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
	 * 获取一条数据
	 * $data = arrray(
	 * 	shop_goods_id 商品ID
	 * )
	 * 
	 * SHOPADMINPGOODSGET
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_get($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_READ);
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		$get_data = object(parent::TABLE_SHOP_GOODS)->find($data['shop_goods_id']);
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $get_data['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
		}
		
		if( !empty($get_data) ){
			$data = array($get_data);
			$data = object(parent::TABLE_SHOP_GOODS)->get_additional_data($data);
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
	 * SHOPADMINGOODSLIST
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_READ);
		
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
		
		
		$config["where"][] = array('[and] sg.shop_goods_trash=0');
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			$config["where"][] = array('[and] sg.user_id=[+]', $_SESSION['user_id']);
		}
		
		if(!empty($data['search'])){
			
			if( isset($data['search']['shop_goods_id']) && is_string($data['search']['shop_goods_id']) ){
				$config["where"][] = array('[and] sg.shop_goods_id=[+]', $data['search']['shop_goods_id']);
			}

			if( isset($data['search']['merchant_id']) && is_string($data['search']['merchant_id']) ){
				$config["where"][] = array('[and] sg.merchant_id=[+]', $data['search']['merchant_id']);
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
	 * 检查编辑的权限
	 * 
	 * SHOPADMINGOODSEDITCHECK
	 * 
	 * @param	void
	 * @return  bool
	 */
	public function api_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_EDIT);
		return true;
	}
	
	
	
	/**
	 * 编辑商品
	 * 
	 * SHOPADMINGOODSEDIT
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_EDIT);
		
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
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find($data['shop_goods_id']);
		if( empty($shop_goods_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $shop_goods_data['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
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
		if( object(parent::TABLE_SHOP_GOODS)->update( array(array('shop_goods_id=[+]', (string)$data['shop_goods_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['shop_goods_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	/**
	 * 逻辑回收商品
	 * 
	 * SHOPADMINGOODSTRASH
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_trash($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_TRASH);
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		
		//获取旧数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find($data['shop_goods_id']);
		if( empty($shop_goods_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		if( !empty($shop_goods_data["shop_goods_trash"]) ){
			throw new error("该商品已经在回收站");
		}
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $shop_goods_data['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
		}
		
		//更新回收状态
		$update_data["shop_goods_trash"] = 1;
		$update_data['shop_goods_trash_time'] = time();
		if( object(parent::TABLE_SHOP_GOODS)->update( array(array('shop_goods_id=[+]', (string)$data['shop_goods_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['shop_goods_id'];
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
	 * SHOPADMINGOODSTRASHLIST
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_trash_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_TRASH_READ);
		
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
			'trash_time_desc' => array('shop_goods_trash_time', true),
			'trash_time_asc' => array('shop_goods_trash_time', false),
			'update_time_desc' => array('shop_goods_update_time', true),
			'update_time_asc' => array('shop_goods_update_time', false),
			'sort_desc' => array('shop_goods_sort', true),
			'sort_asc' => array('shop_goods_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('shop_goods_id', false);
		
		$config["where"][] = array('[and] sg.shop_goods_trash=1');
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			$config["where"][] = array('[and] sg.user_id=[+]', $_SESSION['user_id']);
		}
		
		if(!empty($data['search'])){
			if( isset($data['search']['id']) && is_string($data['search']['id']) ){
				$config["where"][] = array('[and] sg.shop_goods_id=[+]', $data['search']['id']);
				}
		}
		
		return object(parent::TABLE_SHOP_GOODS)->select_page($config);
	}
	
	
	


		
	/**
	 * 恢复回收文章
	 * 
	 * SHOPADMINGOODSTRASHRESTORE
	 * {"class":"shop/admin_goods","method":"api_trash_restore"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_trash_restore($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_TRASH_RESTORE);
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		
		//获取旧数据
		$old_data = object(parent::TABLE_SHOP_GOODS)->find($data['shop_goods_id']);
		if( empty($old_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		if( empty($old_data["shop_goods_trash"]) ){
			throw new error("该商品不在回收站");
		}
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $old_data['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
		}
		
		//更新回收状态
		$update_data["shop_goods_trash"] = 0;
		$update_data["shop_goods_update_time"] = time();
		if( object(parent::TABLE_SHOP_GOODS)->update( array(array('shop_goods_id=[+]', (string)$data['shop_goods_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['shop_goods_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
	/**
	 * 获取商品的一些需要的数据
	 * 并且返回规格
	 * 
	 * $data = arrray(
	 * 	shop_goods_id 商品ID
	 * )
	 * 
	 * SHOPADMINGOODSQUERY
	 * {"class":"shop/admin_goods","method":"api_query"}
	 * 
	 * [{"shop_goods_id":"商品ID"}]
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_query( $data = array() ){
		//检测权限
		object(parent::REQUEST_ADMIN)->check();
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		$get_data = object(parent::TABLE_SHOP_GOODS)->find($data['shop_goods_id']);
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $get_data['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
		}
		
		if( empty($get_data) ){
			throw new error('商品不存在');
		}else{
			$data = array($get_data);
			$data = object(parent::TABLE_SHOP_GOODS)->get_additional_data($data);
			$get_data = $data[0];
			
			return array(
				'shop_goods_id' => $get_data['shop_goods_id'],
				'shop_goods_name' => $get_data['shop_goods_name'],
				'shop_goods_property' => $get_data['shop_goods_property'],
				'shop_goods_sn' => $get_data['shop_goods_sn'],
				'shop_goods_state' => $get_data['shop_goods_state'],
				'shop_goods_sku' => $get_data['shop_goods_sku'],
			);
		}
	}
	
	
	
	
	
	
	
	
}
?>