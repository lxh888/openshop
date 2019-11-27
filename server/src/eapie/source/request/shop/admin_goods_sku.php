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
class admin_goods_sku extends \eapie\source\request\shop {
	
	
	
	
		
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
	 * SHOPADMINGOODSSKULIST
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
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			$config["where"][] = array('[and] sg.user_id=[+]', $_SESSION['user_id']);
		}
		
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
	 * 商品规格的添加
	 * $data = array(
	 * 	'shop_goods_id' => string 商品ID
	 * )
	 * 
	 * SHOPADMINGOODSSKUADD
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_SKU_ADD);
		//数据检测
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'image_id', parent::TABLE_IMAGE, array('format', 'legal_id'));
		
		object(parent::ERROR)->check($data, 'shop_goods_sku_stock', parent::TABLE_SHOP_GOODS_SKU, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_sku_info', parent::TABLE_SHOP_GOODS_SKU, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_sku_cost_price', parent::TABLE_SHOP_GOODS_SKU, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_sku_price', parent::TABLE_SHOP_GOODS_SKU, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_sku_market_price', parent::TABLE_SHOP_GOODS_SKU, array('args'));
		
		//获取旧数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find($data['shop_goods_id']);
		if( empty($shop_goods_data) ){
			throw new error("商品ID有误，数据不存在");
		}
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $shop_goods_data['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
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
	 * 编辑规格的权限检测
	 * 
	 * SHOPADMINGOODSSKUEDITCHECK
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_edit_check(){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_SKU_EDIT);
		return true;
	}
	
	
	
		
	/**
	 * 编辑规格
	 * 
	 * SHOPADMINGOODSSKUEDIT
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_SKU_EDIT);
		
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
		
		//获取旧数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find($shop_goods_sku_data['shop_goods_id']);
		if( empty($shop_goods_data) ){
			throw new error("商品ID有误，数据不存在");
		}
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $shop_goods_data['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
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
	 * 删除规格
	 * 
	 * SHOPADMINGOODSSKUREMOVE
	 * 
	 * @param	array		$data
	 * @return	bool
	 */
	public function api_remove($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_SKU_REMOVE);
		//数据检测
		object(parent::ERROR)->check($data, 'shop_goods_sku_id', parent::TABLE_SHOP_GOODS_SKU, array('args'));
		
		//获取旧数据
		$shop_goods_sku_data = object(parent::TABLE_SHOP_GOODS_SKU)->find($data['shop_goods_sku_id']);
		if( empty($shop_goods_sku_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		//获取旧数据
		$shop_goods_data = object(parent::TABLE_SHOP_GOODS)->find($shop_goods_sku_data['shop_goods_id']);
		if( empty($shop_goods_data) ){
			throw new error("商品ID有误，数据不存在");
		}
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $shop_goods_data['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
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
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>