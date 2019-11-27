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
class admin_coupon extends \eapie\source\request\application {
	
	
	
	
		
	/**
	 * 获取模块选项列表
	 * 
	 * APPLICATIONADMINCOUPONMODULEOPTION
	 * {"class":"application/admin_coupon","method":"api_module_option"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_module_option($data = array()){
		object(parent::REQUEST_ADMIN)->check();
		return object(parent::TABLE_COUPON)->get_module();
	}
	
	
	/**
	 * 获取类型选项列表
	 * 
	 * APPLICATIONADMINCOUPONTYPEOPTION
	 * {"class":"application/admin_coupon","method":"api_type_option"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_type_option($data = array()){
		object(parent::REQUEST_ADMIN)->check();
		return object(parent::TABLE_COUPON)->get_type();
	}
	
	
	
	
	
    /**
	 * 获取一条优惠券数据
	 * 
	 * {"coupon_id":"优惠券ID"}
	 * 
	 * @param string $data['coupon_id'] 优惠券ID
	 * 
	 * APPLICATIONADMINCOUPONGET
	 * {"class":"application/admin_coupon","method":"api_get"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_get( $data = array() ){
		// 检测权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_COUPON_READ);
		// 检测数据
		object(parent::ERROR)->check($data, 'coupon_id', parent::TABLE_COUPON, array('args'));
		
		$get_data = object(parent::TABLE_COUPON)->find($data['coupon_id']);
		if( empty($get_data) ){
			throw new error("数据不存在");
		}
		
		return $get_data;
    }
    
    /**
	 * 获取优惠券数据列表
	 * 
	 * [{"sort 排序":["number_desc","number_asc","use_number_desc","use_number_asc","state_desc","state_asc","use_time_desc","expire_time_asc","id_desc","id_asc","insert_time_desc","insert_time_asc","update_time_desc","update_time_asc"],"search":{"coupon_id":"优惠券ID","coupon_name":"优惠券名称","coupon_module":"优惠券所属模块","coupon_label":"优惠券标签","property":"优惠券资金类型","state":"0或1","coupon_type":"优惠券类型"}}]
	 * 
	 * APPLICATIONADMINCOUPONLIST
	 * {"class":"application/admin_coupon","method":"api_list"}
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function api_list($data = array()){
		// 检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_COUPON_READ);
		// 初始化配置
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'name_desc' => array('coupon_name', true),
			'name_asc' => array('coupon_name', false),
			'type_desc' => array('coupon_type', true),
			'type_asc' => array('coupon_type', false),
			'state_desc' => array('coupon_state', true),
			'state_asc' => array('coupon_state', false),
			'start_time_desc' => array('coupon_start_time', true),
			'start_time_asc' => array('coupon_start_time', false),
			'end_time_desc' => array('coupon_end_time', true),
			'end_time_asc' => array('coupon_end_time', false),
			'id_desc' => array('coupon_id', true),
			'id_asc' =>  array('coupon_id', false),
			'insert_time_desc' => array('coupon_insert_time', true),
			'insert_time_asc' => array('coupon_insert_time', false),
			'update_time_desc' => array('coupon_update_time', true),
            'update_time_asc' => array('coupon_update_time', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('coupon_id', false);
		
		if(!empty($data['search'])){
			if( isset($data['search']['coupon_id']) && is_string($data['search']['coupon_id']) ){
				$config["where"][] = array('[and] c.coupon_id=[+]', $data['search']['coupon_id']);
			}
			if( isset($data['search']['coupon_name']) && is_string($data['search']['coupon_name']) ){
                $config['where'][] = array('[and] c.coupon_name LIKE "%[-]%"', $data['search']['coupon_name']);
            }
			if( isset($data['search']['coupon_label']) && is_string($data['search']['coupon_label']) ){
                $config['where'][] = array('[and] c.coupon_label LIKE "%[-]%"', $data['search']['coupon_label']);
            }
			if (isset($data['search']['coupon_type']) && is_string($data['search']['coupon_type']) ){
                $config['where'][] = array('[and] c.coupon_type=[+]', $data['search']['coupon_type']);
            }
			if (isset($data['search']['coupon_module']) && is_string($data['search']['coupon_module']) ){
                $config['where'][] = array('[and] c.coupon_module=[+]', $data['search']['coupon_module']);
            }
			
			if( isset($data['search']['state']) && 
			(is_string($data['search']['state']) || is_numeric($data['search']['state'])) &&
			in_array($data['search']['state'], array("0", "1")) ){
				$config["where"][] = array('[and] c.coupon_state=[+]', $data['search']['state']);
				}
				
			if( isset($data['search']['property']) && 
			(is_string($data['search']['property']) || is_numeric($data['search']['property'])) &&
			in_array($data['search']['property'], array("0", "1")) ){
				$config["where"][] = array('[and] c.coupon_property=[+]', $data['search']['property']);
				}

		}

		$data_list = object(parent::TABLE_COUPON)->select_page($config);
		if( !empty($data_list["data"]) && !empty($type_list) ){
			$type_list = object(parent::TABLE_COUPON)->get_type();
			$method_list = object(parent::TABLE_COUPON)->get_method();
			foreach($data_list["data"] as $key => $value){
				if( isset($type_list[$value["coupon_type"]]) ){
					$data_list["data"][$key]["coupon_type_name"] = $type_list[$value["coupon_type_type"]];
				}
				if( isset($method_list[$value["coupon_module"]]) ){
					$data_list["data"][$key]["coupon_module_name"] = $method_list[$value["coupon_module"]];
				}
			}
		}
		
        return $data_list;
	}



	/**
	 * 添加优惠券
	 * 
	 * APPLICATIONADMINCOUPONADD
	 * {"class":"application/admin_coupon","method":"api_add"}
	 * 
	 * [{"coupon_name":"优惠券名称，必填","coupon_info":"描述","coupon_label":"优惠券标签","coupon_comment":"优惠券注释信息","coupon_type":"优惠类型,必填","coupon_module":"优惠所属模块","coupon_key":"优惠模块主键","coupon_property":"所属资金类型，0人民币优惠券，1积分优惠券","coupon_limit_min":"最小限制，0表示不限制。人民币单位分，积分单位数量。1）满减，判断 coupon_limit_min ，coupon_limit_min 如果为0则代表最小不限制。2）代金，判断 coupon_limit_min ，coupon_limit_min 如果为0则代表最小不限制。3）折扣，判断 coupon_limit_min ，coupon_limit_min 如果为0则代表最小不限制。4）抵扣，判断 coupon_limit_min ，coupon_limit_min 如果为0则代表最小不限制。","coupon_limit_max":"最大限制，人民币单位分，积分单位数量。为空，默认不限制","coupon_discount":"整数，当是整数，单位百分。当是人民币，单位分    当是折扣券时,表示多少折（百分）；当是代金券是,表示券价值多少分（单位分）","coupon_start_time":"有效期—开始，0则领取后计时","coupon_end_time":"有效期—结束，0则永久有效","coupon_state":"状态。0失效，1正常"}]
	 * 
	 * @param string $input
	 * 	['coupon_name'] [优惠券名称] [必填]
	 *  ['coupon_info'] [信息] [选填]
	 *  ['coupon_label'] [优惠券标签] [选填]
	 * 	['coupon_comment'] [优惠券注释信息] [选填]
	 * 	['coupon_type'] [优惠类型] [必填] 
	 *  ['coupon_module'] [优惠所属模块] [选填]
	 *  ['coupon_key'] [优惠模块主键] [当填写优惠券所属模块，则必填]
	 * 	['coupon_property'] [所属商品类型] [必填]
	 * 	['coupon_limit_min'] [最小优惠金额，为空，默认不限制] [选填]
	 * 	['coupon_limit_max'] [最大优惠金额，为空，默认不限制] [选填]
	 *  ['coupon_discount'] [折扣] [选填] [不填默认为零]
	 *  ['coupon_start_time'] [开始时间] [必填]
	 *  ['coupon_end_time'] [结束时间] [必填]
	 *  
	 * @return string coupon_id 优惠券ID
	 */
	public function api_add($input = array()){

		//检测权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_COUPON_ADD);
		// 数据检测
		object(parent::ERROR)->check($input, 'coupon_name', parent::TABLE_COUPON, array('args'));
		if( isset($input['coupon_info']) && $input['coupon_info'] != '' )
		object(parent::ERROR)->check($input, 'coupon_info', parent::TABLE_COUPON, array('args'));
		object(parent::ERROR)->check($input, 'coupon_property', parent::TABLE_COUPON, array('args'));
		object(parent::ERROR)->check($input, 'coupon_type', parent::TABLE_COUPON, array('args'));
		if( isset($input['coupon_module']) && $input['coupon_module'] != '' )
		object(parent::ERROR)->check($input, 'coupon_module', parent::TABLE_COUPON, array('args'));
		if( isset($input['coupon_key']) && $input['coupon_key'] != '' )
		object(parent::ERROR)->check($input, 'coupon_key', parent::TABLE_COUPON, array('args'));
		object(parent::ERROR)->check($input, 'coupon_state', parent::TABLE_COUPON, array('args'));
		object(parent::ERROR)->check($input, 'coupon_comment', parent::TABLE_COUPON, array('args'));
		
		//白名单
		$whitelist = array(
			'coupon_name', 
			'coupon_info', 
			'coupon_module', 
			'coupon_key',
			'coupon_label',
			'coupon_comment',
			'coupon_type',
			'coupon_property',
			'coupon_state',
		);
		$insert_data = cmd(array($input, $whitelist), 'arr whitelist');

		if( isset($input['coupon_start_time']) && $input['coupon_start_time'] != '' ){
			object(parent::ERROR)->check($input, 'coupon_start_time', parent::TABLE_COUPON, array('args'));
			$insert_data['coupon_start_time'] = cmd(array($input['coupon_start_time']), "time mktime");
		}else{
			$insert_data['coupon_start_time'] = 0;
		}
		
		if( isset($input['coupon_end_time']) && $input['coupon_end_time'] != '' ){
			object(parent::ERROR)->check($input, 'coupon_end_time', parent::TABLE_COUPON, array('args'));
			$insert_data['coupon_end_time'] = cmd(array($input['coupon_end_time']), "time mktime");
		}else{
			$insert_data['coupon_end_time'] = 0;
		}
		
		if( $insert_data['coupon_start_time'] > $insert_data['coupon_end_time'] ){
			throw new error("结束时间早于开始时间");
		}
		
		if( isset($input['coupon_limit_min']) && $input['coupon_limit_min'] != '' ){
			object(parent::ERROR)->check($input, 'coupon_limit_min', parent::TABLE_COUPON, array('args'));
			$insert_data['coupon_limit_min'] = $input['coupon_limit_min'];
		}else{
			$insert_data['coupon_limit_min'] = 0;
		}
		if( isset($input['coupon_limit_max']) && $input['coupon_limit_max'] != '' ){
			object(parent::ERROR)->check($input, 'coupon_limit_max', parent::TABLE_COUPON, array('args'));
			$insert_data['coupon_limit_max'] = $input['coupon_limit_max'];
		}else{
			$insert_data['coupon_limit_max'] = 0;
		}
		if( isset($input['coupon_discount']) && $input['coupon_discount'] != '' ){
			object(parent::ERROR)->check($input, 'coupon_discount', parent::TABLE_COUPON, array('args'));
			$insert_data['coupon_discount'] = $input['coupon_discount'];
		}else{
			$insert_data['coupon_discount'] = 0;
		}
		
		// 初始化数据
		$insert_data['coupon_id'] = object(parent::TABLE_COUPON)->get_unique_id();

		// 最大优惠金额不能低于最小优惠金额
		if( !empty($insert_data['coupon_limit_min']) && 
		!empty($insert_data['coupon_limit_max']) && 
		$insert_data['coupon_limit_max'] <=  $input['coupon_limit_min']){
			throw new error("优惠券最大优惠限制不能低于最小优惠限制");
		}
		
		$insert_data['coupon_insert_time'] = time();
		$insert_data['coupon_update_time'] = time();
		if( object(parent::TABLE_COUPON)->insert($insert_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($input, $insert_data);
			return $insert_data['coupon_id'];
		}else{
			throw new error("操作失败");
		}
		
	}


	/**
	 * 编辑优惠券
	 * APPLICATIONADMINCOUPONEDIT
	 * {"class":"application/admin_coupon","method":"api_edit"}
	 * 
	 * [{"coupon_id":"优惠券ID，必填。该参数作为修改时查询的主键","coupon_name":"优惠券名称","coupon_info":"描述","coupon_label":"优惠券标签","coupon_comment":"优惠券注释信息","coupon_type":"优惠类型,必填","coupon_module":"优惠所属模块","coupon_key":"优惠模块主键","coupon_property":"所属资金类型，0人民币优惠券，1积分优惠券","coupon_limit_min":"最小限制，0表示不限制。人民币单位分，积分单位数量。1）满减，判断 coupon_limit_min ，coupon_limit_min 如果为0则代表最小不限制。2）代金，判断 coupon_limit_min ，coupon_limit_min 如果为0则代表最小不限制。3）折扣，判断 coupon_limit_min ，coupon_limit_min 如果为0则代表最小不限制。4）抵扣，判断 coupon_limit_min ，coupon_limit_min 如果为0则代表最小不限制。","coupon_limit_max":"最大限制，人民币单位分，积分单位数量。为空，默认不限制","coupon_discount":"整数，当是整数，单位百分。当是人民币，单位分    当是折扣券时,表示多少折（百分）；当是代金券是,表示券价值多少分（单位分）","coupon_start_time":"有效期—开始，0则领取后计时","coupon_end_time":"有效期—结束，0则永久有效","coupon_state":"状态。0失效，1正常"}]
	 * 
	 * @param array $input
	 *  ['coupon_id'] [优惠券ID] [必填]
	 */

	 public function api_edit($input){
		// 检测权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_COUPON_EDIT);
		//数据检测 
		object(parent::ERROR)->check($input, 'coupon_id', parent::TABLE_COUPON, array('args'));
		// 数据检测
		if( isset($input['coupon_name']) )
		object(parent::ERROR)->check($input, 'coupon_name', parent::TABLE_COUPON, array('args'));
		if( isset($input['coupon_info']))
		object(parent::ERROR)->check($input, 'coupon_info', parent::TABLE_COUPON, array('args'));
		if( isset($input['coupon_property']) && $input['coupon_property'] != '' )
		object(parent::ERROR)->check($input, 'coupon_property', parent::TABLE_COUPON, array('args'));
		if( isset($input['coupon_type']) && $input['coupon_type'] != '' )
		object(parent::ERROR)->check($input, 'coupon_type', parent::TABLE_COUPON, array('args'));
		if( isset($input['coupon_module']) && $input['coupon_module'] != '' )
		object(parent::ERROR)->check($input, 'coupon_module', parent::TABLE_COUPON, array('args'));
		if( isset($input['coupon_key']) && $input['coupon_key'] != '' )
		object(parent::ERROR)->check($input, 'coupon_key', parent::TABLE_COUPON, array('args'));
		if( isset($input['coupon_state']) && $input['coupon_state'] != '' )
		object(parent::ERROR)->check($input, 'coupon_state', parent::TABLE_COUPON, array('args'));
		if( isset($input['coupon_comment']) )
		object(parent::ERROR)->check($input, 'coupon_comment', parent::TABLE_COUPON, array('args'));
		if( isset($input['coupon_limit_min']) && $input['coupon_limit_min'] != '' )
		object(parent::ERROR)->check($input, 'coupon_limit_min', parent::TABLE_COUPON, array('args'));
		if( isset($input['coupon_limit_max']) && $input['coupon_limit_max'] != '' )
		object(parent::ERROR)->check($input, 'coupon_limit_max', parent::TABLE_COUPON, array('args'));
		if( isset($input['coupon_discount']) && $input['coupon_discount'] != '' )
		object(parent::ERROR)->check($input, 'coupon_discount', parent::TABLE_COUPON, array('args'));
		
		
		if( isset($input['coupon_start_time']) && $input['coupon_start_time'] != '' ){
			object(parent::ERROR)->check($input, 'coupon_start_time', parent::TABLE_COUPON, array('args'));
			$input['coupon_start_time'] = cmd(array($input['coupon_start_time']), "time mktime");
		}
		
		if( isset($input['coupon_end_time']) && $input['coupon_end_time'] != '' ){
			object(parent::ERROR)->check($input, 'coupon_end_time', parent::TABLE_COUPON, array('args'));
			$input['coupon_end_time'] = cmd(array($input['coupon_end_time']), "time mktime");
		}
		
		
		// 获取旧数据
		$old_coupon_data = object(parent::TABLE_COUPON)->find($input['coupon_id']);
		if( empty($old_coupon_data) ){
			throw new error("ID有误，数据不存在");
		}

		//白名单 私密数据不能获取
		$whitelist = array(
			'coupon_name', 
			'coupon_info', 
			'coupon_module', 
			'coupon_key',
			'coupon_label',
			'coupon_comment',
			'coupon_type',
			'coupon_property',
			'coupon_limit_min',
			'coupon_limit_max',
			'coupon_discount',
			'coupon_start_time',
			'coupon_end_time',
			'coupon_state',
		);
		$update_data = cmd(array($input, $whitelist), 'arr whitelist');
		foreach($update_data as $key => $value){
			if( isset($old_coupon_data[$key]) ){
				if($old_coupon_data[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}

		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}

		if( isset($update_data['coupon_end_time']) && isset($update_data['coupon_start_time']) && $update_data['coupon_end_time'] <= $update_data['coupon_start_time']){
			throw new error("结束时间早于开始时间");
		}

		if(isset($update_data['coupon_end_time']) && $old_coupon_data['coupon_start_time'] !== '0' && $update_data['coupon_end_time'] <= (int)$old_coupon_data['coupon_start_time']){	
			throw new error("结束时间早于旧的开始时间");
		}

		if(isset($update_data['coupon_start_time']) && $old_coupon_data['coupon_end_time'] !== '0' && $update_data['coupon_start_time'] >= (int)$old_coupon_data['coupon_end_time']){
			throw new error("开始时间晚于旧的结束时间");
		}
		
		
		$update_data['coupon_update_time']  = time();
		if( object(parent::TABLE_COUPON)->update( array(array('coupon_id=[+]', (string)$input['coupon_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($input, $update_data);
			return $input['coupon_id'];
		}else{
			throw new error("操作失败");
		}
	}

	/**
	 * 删除优惠券
	 * 
	 * APPLICATIONADMINCOUPONREMOVE
	 * {"class":"application/admin_coupon","method":"api_remove"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_remove( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_COUPON_REMOVE);
		
		//数据检测 
		object(parent::ERROR)->check($data, 'coupon_id', parent::TABLE_COUPON, array('args'));
		
		//查询旧数据
        $original = object(parent::TABLE_COUPON)->find($data['coupon_id']);
        if( empty($original) ) throw new error('数据不存在');
		
		if( object(parent::TABLE_COUPON)->remove($data['coupon_id']) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $original);
			return $data['coupon_id'];
		}else{
			throw new error("操作失败");
		}
	}
}