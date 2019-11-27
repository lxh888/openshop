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
class admin_group_goods extends \eapie\source\request\shop {
	/* 拼团组件后台管理接口 */

	/**
	 * 添加拼团商品，需要登录
	 *
	 * api: SHOPADMINGROUPGOODSADD
	 * {"class":"shop/admin_group_goods","method":"api_add"}
	 *
	 * @param data array
	 *  $data[shop_group_goods_start_time] [int] [开始时间]
	 *  $data[shop_goods_id] [str] [商品ID]
	 *  $data[shop_group_goods_end_time] [int] [结束时间]
	 *  $data[shop_group_goods_num] [int] [参团人数]
	 *  $data[shop_group_goods_price] [int] [参团价格]
	 * @return result array
	 *  $result['shop_group_goods_id'] [str] [拼团ID]
	 */
	public function api_add($data = array()) {
		//检测登录
		object(parent::REQUEST_USER) -> check(parent::AUTHORITY_GROUP_GOODS_ADD);

		//检测参数
		//object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GROUP_GOODS, array('args', 'exists_id', 'legal_id'));
		object(parent::ERROR)->check($data, 'shop_goods_id', parent::TABLE_SHOP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'shop_group_goods_start_time', parent::TABLE_SHOP_GROUP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'shop_group_goods_end_time', parent::TABLE_SHOP_GROUP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'shop_group_goods_num', parent::TABLE_SHOP_GROUP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'shop_group_goods_price', parent::TABLE_SHOP_GROUP_GOODS, array('args'));
		object(parent::ERROR)->check($data, 'shop_goods_sku_id', parent::TABLE_SHOP_GOODS_SKU, array('args'));
		// 查询该商品是否存在
		$find_goods_data = object(parent::TABLE_SHOP_GOODS_SKU)->find_goods($data['shop_goods_sku_id'], $data['shop_goods_id']);
		if( empty($find_goods_data) ){
			throw new error('规格id和商品id不匹配');
		}


		$data['shop_group_goods_start_time'] = cmd(array($data['shop_group_goods_start_time']), "time mktime");
		$data['shop_group_goods_end_time'] = cmd(array($data['shop_group_goods_end_time']), "time mktime");
		if (! object(parent::TABLE_SHOP_GROUP_GOODS) -> _end_time_is_early($data['shop_group_goods_start_time'], $data['shop_group_goods_end_time'])) {
			throw new error('结束时间早于开始时间');
		}


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
		

		//初始化数组
		$insert_data = array();
		
		//缓存数组
		$insert_data['shop_goods_id'] = $data['shop_goods_id'];
		$insert_data['shop_group_goods_price'] = $data['shop_group_goods_price'];
		$insert_data['shop_group_goods_start_time'] = $data['shop_group_goods_start_time'];
		$insert_data['shop_group_goods_end_time'] = $data['shop_group_goods_end_time'];
		$insert_data['shop_goods_sku_id'] = $data['shop_goods_sku_id'];
		$insert_data['shop_group_goods_num'] = $data['shop_group_goods_num'];
		//用户id
		$insert_data['user_id'] = $_SESSION['user_id'];
		//拼团ID
		$insert_data['shop_group_goods_id'] = object(parent::TABLE_SHOP_GROUP_GOODS) -> get_unique_id();
		//初始化现有人数
		$insert_data['shop_group_goods_now_num'] = 0;
		//初始化状态
		$insert_data['shop_group_goods_is_end'] = 0;
		//初始化状态
		$insert_data['shop_group_goods_is_success'] = 0;
		//创建时间
		$insert_data['shop_group_goods_insert_time'] = time();
		//更新时间
		$insert_data['shop_group_goods_update_time'] = time();

		//插入数据
		if ( object(parent::TABLE_SHOP_GROUP_GOODS) -> insert($insert_data)) {
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG) -> insert($data, $insert_data);
			return $insert_data['shop_group_goods_id'];
		} else {
			throw new error("操作失败");
		}
	}
	
	
	
	
	/**
	 * 编辑拼团商品，需要登录
	 *
	 * api: SHOPADMINGROUPGOODSEDIT
	 * {"class":"shop/admin_group_goods","method":"api_edit"}
	 *
	 * @param array $request
	 *
	 * @return string $result
	 */
	public function api_edit($data = array()) {
		//检测登录
		object(parent::REQUEST_USER) -> check(parent::AUTHORITY_GROUP_GOODS_EDIT);

		//校验数据
		object(parent::ERROR) -> check($data, 'shop_group_goods_id', parent::TABLE_SHOP_GROUP_GOODS, array('args'), 'group_id');
		/*if (isset($data['shop_group_goods_num']))
			object(parent::ERROR) -> check($data, 'shop_group_goods_num', parent::TABLE_SHOP_GROUP_GOODS, array('args'));
		if (isset($data['shop_group_goods_price']))
			object(parent::ERROR) -> check($data, 'shop_group_goods_price', parent::TABLE_SHOP_GROUP_GOODS, array('args'));*/
		
		//获取旧数据
		$old_data = object(parent::TABLE_SHOP_GROUP_GOODS) -> find($data['shop_group_goods_id']);
		if (empty($old_data)) {
			throw new error("ID有误，数据不存在");
		}

		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $old_data['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
		}

		$start_time = $old_data['shop_group_goods_start_time'];
		if( isset($data['shop_group_goods_start_time']) ){
			object(parent::ERROR) -> check($data, 'shop_group_goods_start_time', parent::TABLE_SHOP_GROUP_GOODS, array('args'));
			$data['shop_group_goods_start_time'] = cmd(array($data['shop_group_goods_start_time']), "time mktime");
			$start_time = $data['shop_group_goods_start_time'];
		}
		
		$end_time = $old_data['shop_group_goods_end_time'];	
		if( isset($data['shop_group_goods_end_time']) ){
			object(parent::ERROR) -> check($data, 'shop_group_goods_end_time', parent::TABLE_SHOP_GROUP_GOODS, array('args'));
			$data['shop_group_goods_end_time'] = cmd(array($data['shop_group_goods_end_time']), "time mktime");
			$end_time = $data['shop_group_goods_end_time'];
		}
			
		if (! object(parent::TABLE_SHOP_GROUP_GOODS) -> _end_time_is_early($start_time, $end_time)) {
			throw new error('结束时间早于开始时间');
		}
		
		//白名单
		$whitelist = array(
			'shop_group_goods_start_time', 
			'shop_group_goods_end_time', 
			//'shop_group_goods_num', 
			//'shop_group_goods_price', 
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
		if (!empty($update_data)) {
			foreach ($update_data as $key => $value) {
				if (isset($old_data[$key])) {
					if ($old_data[$key] == $value) {
						unset($update_data[$key]);
					}
				}
			}
		}

		if (empty($update_data)) {
			throw new error("没有需要更新的数据");
		}

		//更新时间
		$update_data['shop_group_goods_update_time'] = time();
		if ( object(parent::TABLE_SHOP_GROUP_GOODS) -> update(array( array('shop_group_goods_id=[+]', (string)$data['shop_group_goods_id'])), $update_data)) {
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG) -> insert($data, $update_data);
			return $data['shop_group_goods_id'];
		} else {
			throw new error("操作失败");
		}
	}

	/**
	 * 拼团商品删除，需要登录
	 *
	 * api: SHOPADMINGROUPGOODSREMOVE
	 * {"class":"shop/admin_group_goods","method":"api_remove"}
	 *
	 * @param array $data
	 *  $data['shop_group_goods_id']  拼团ID
	 * @return array $result
	 */
	public function api_remove($data = array()) {
		//检测登录
		object(parent::REQUEST_USER) -> check(parent::AUTHORITY_GROUP_GOODS_REMOVE);
		//校验数据
		object(parent::ERROR) -> check($data, 'shop_group_goods_id', parent::TABLE_SHOP_GROUP_GOODS, array('args'), 'group_id');
		//查询旧数据
		$original = object(parent::TABLE_SHOP_GROUP_GOODS)->find($data['shop_group_goods_id']);
		if (empty($original)) throw new error('数据不存在');

		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			if( $original['user_id'] != $_SESSION['user_id'] ){
				throw new error("权限不足，不能操作非自己添加的数据");
			}
		}

		//删除数据，记录日志
		if ( object(parent::TABLE_SHOP_GROUP_GOODS)->remove($data['shop_group_goods_id'])) {
			object(parent::TABLE_ADMIN_LOG)->insert($data, $original);
			return $data['shop_group_goods_id'];
		} else {
			throw new error('删除失败');
		}
	}

	/**
	 * 拼团商品列表，需要登录
	 *
	 * api: SHOPADMINGROUPGOODSLIST
	 * {"class":"shop/admin_group_goods","method":"api_list"}
	 *
	 * @param array $data
	 *
	 * @return $result array
	 */
	public function api_list($data = array()) {
		//检测登录
		object(parent::REQUEST_USER) -> check(parent::AUTHORITY_GROUP_GOODS_READ);

		//查询配置
		$config = array(
			'orderby' => array(), 
			'where' => array(), 
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN) 
			);
		
		//排序
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'group_id_desc' => array('shop_group_goods_id', true), 
			'group_id_asc' => array('shop_group_goods_id', false), 
			
			'price_desc' => array('shop_group_goods_price', true), 
			'price_asc' => array('shop_group_goods_price', false), 
			
			'num_desc' => array('shop_group_goods_num', true), 
			'num_asc' => array('shop_group_goods_num', false), 
			
			'now_num_desc' => array('shop_group_goods_now_num', true), 
			'now_num_asc' => array('shop_group_goods_now_num', false), 
			
			'start_time_desc' => array('shop_group_goods_start_time', true), 
			'start_time_asc' => array('shop_group_goods_start_time', false), 
			
			'end_time_desc' => array('shop_group_goods_end_time', true), 
			'end_time_asc' => array('shop_group_goods_end_time', false),
			
			'insert_time_desc' => array('shop_group_goods_insert_time', true),
			'insert_time_asc' => array('shop_group_goods_insert_time', false),
			
			'update_time_desc' => array('shop_group_goods_update_time', true),
			'update_time_asc' => array('shop_group_goods_update_time', false),
		));
		
		
		//避免排序重复
		$config["orderby"][] = array('shop_group_goods_id', false);
		
		//判断是不是 商品管理员资格权限，如果不是就只能管理自己上传的商品
		if( !object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_GOODS_ADMINISTRATOR, true) ){
			$config["where"][] = array('[and] sgg.user_id=[+]', $_SESSION['user_id']);
		}
		
		
		if(!empty($data['search'])){
			if( isset($data['search']['is_success']) && 
			(is_string($data['search']['is_success']) || is_numeric($data['search']['is_success'])) &&
			in_array($data['search']['is_success'], array("0", "1")) ){
				$config["where"][] = array('[and] sgg.shop_group_goods_is_success=[+]', $data['search']['is_success']);
				}
			
			if( isset($data['search']['is_end']) && 
			(is_string($data['search']['is_end']) || is_numeric($data['search']['is_end'])) &&
			in_array($data['search']['is_end'], array("0", "1")) ){
				$config["where"][] = array('[and] sgg.shop_group_goods_is_end=[+]', $data['search']['is_end']);
				}

			
			if( isset($data['search']['shop_goods_id']) && is_string($data['search']['shop_goods_id']) ){
				$config["where"][] = array('[and] sgg.shop_goods_id=[+]', $data['search']['shop_goods_id']);
			}
			
			if (isset($data['search']['shop_goods_name']) && is_string($data['search']['shop_goods_name'])) {
                $config['where'][] = array('[and] sg.shop_goods_name LIKE "%[-]%"', $data['search']['shop_goods_name']);
            }


			if (isset($data['search']['user_id']) && is_string($data['search']['user_id'])) {
                $config['where'][] = array('[and] sgg.user_id=[+]', $data['search']['user_id']);
            }
			if (isset($data['search']['user_nickname']) && is_string($data['search']['user_nickname'])) {
                $config['where'][] = array('[and] u.user_nickname LIKE "%[-]%"', $data['search']['user_nickname']);
            }
			
			if (isset($data['search']['user_phone']) && is_string($data['search']['user_phone'])) {
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['user_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $config['where'][] = array('[and] sgg.user_id=[+]', $user_id);
            }
			
		}
		
		//查询数据
		$select_data = object(parent::TABLE_SHOP_GROUP_GOODS)->select_page($config);
		if( empty($select_data) ){
			return $select_data;
		}
		
		//获取sku信息
		$select_data['data'] = object(parent::TABLE_SHOP_GOODS)->get_spu_data($select_data['data']);
		return $select_data;
	}
	
	
	/**
	 * 单个拼团商品，需要登录
	 * 
	 * api: SHOPADMINGROUPGOODSGET
	 * {"class":"shop/admin_group_goods","method":"api_get"}
	 * 
	 * $data['shop_group_goods_id'] 拼团ID
	 * 
	 * @param	array		$data
	 * @return	array		$result
	 */
	public function api_get( $data = array() ){
		//检测登录
		object(parent::REQUEST_USER) -> check(parent::AUTHORITY_GROUP_GOODS_READ);
		//检测参数
		object(parent::ERROR)->check($data, 'shop_group_goods_id', parent::TABLE_SHOP_GROUP_GOODS, array('args'), 'group_id');
		$goods_data = object(parent::TABLE_SHOP_GROUP_GOODS)->find_join($data['shop_group_goods_id']);
		if( empty($goods_data) ){
			throw new error('数据不存在');
		}else{
			//获取sku信息
			$get_spu = array($goods_data);
			$get_spu = object(parent::TABLE_SHOP_GOODS)->get_spu_data($get_spu);
			return $get_spu[0];
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
	 * SHOPADMINGROUPGOODS
	 * {"class":"shop/admin_group_goods","method":"api_goods"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	/*public function api_goods( $data = array() ){
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
				'shop_goods_sku' => $get_data['shop_goods_sku'],
			);
		}
	}
	
	
	

	*/
	
	
	
	
	
	
	
}