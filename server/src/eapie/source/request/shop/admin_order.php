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
class admin_order extends \eapie\source\request\shop {
	
	
	
	
	/**
	 * 获取订单详情
	 * 
	 * SHOPADMINORDERDETAILS
	 * {"class":"shop/admin_order","method":"api_details"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_details($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ORDER_DETAILS_READ);
		object(parent::ERROR)->check($data, 'shop_order_id', parent::TABLE_SHOP_ORDER, array('args'));
		
		return object(parent::TABLE_SHOP_ORDER)->find_details($data["shop_order_id"]);
	}
	
	
	
	
	
		
	/**
	 * 获取数据列表
	 * 
	 * SHOPADMINORDERLIST
	 * {"class":"shop/admin_order","method":"api_list"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ORDER_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'user_id_desc' => array('user_id', true),
			'user_id_asc' => array('user_id', false),
			
			'close_time_desc' => array('shop_order_close_time', true),
			'close_time_asc' => array('shop_order_close_time', false),
			
			'update_time_desc' => array('shop_order_update_time', true),
			'update_time_asc' => array('shop_order_update_time', false),
			
			'insert_time_desc' => array('shop_order_insert_time', true),
			'insert_time_asc' => array('shop_order_insert_time', false),
			
			'order_id_desc' => array('shop_order_id', true),
			'order_id_asc' => array('shop_order_id', false),
			
			'consignee_desc' => array('user_address_consignee', true),
			'consignee_asc' => array('user_address_consignee', false),
			
			'money_desc' => array('shop_order_money', true),
			'money_asc' => array('shop_order_money', false),
			
			'credit_desc' => array('shop_order_credit', true),
			'credit_asc' => array('shop_order_credit', false),
			
			'state_desc' => array('shop_order_state', true),
			'state_asc' => array('shop_order_state', false),
			
			'pay_state_desc' => array('shop_order_pay_state', true),
			'pay_state_asc' => array('shop_order_pay_state', false),
			
			'shipping_state_desc' => array('shop_order_shipping_state', true),
			'shipping_state_asc' => array('shop_order_shipping_state', false),
			
		));
		
		//避免排序重复
		$config["orderby"][] = array('shop_order_id', false);
		
		
		$config["where"][] = array('[and] so.shop_order_trash=0');
		if(!empty($data['search'])){
			if( isset($data['search']['shop_order_id']) && is_string($data['search']['shop_order_id']) ){
				$config["where"][] = array('[and] so.shop_order_id=[+]', $data['search']['shop_order_id']);
			}
			
			
			if (isset($data['search']['user_id']) && is_string($data['search']['user_id'])) {
                $config['where'][] = array('[and] u.user_id=[+]', $data['search']['user_id']);
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
                $config['where'][] = array('[and] u.user_id=[+]', $user_id);
            }
			
			if (isset($data['search']['user_address_consignee']) && is_string($data['search']['user_address_consignee'])) {
                $config['where'][] = array('[and] so.user_address_consignee LIKE "%[-]%"', $data['search']['user_address_consignee']);
            }
			
			
			//订单状态
			if( isset($data['search']['state']) && 
			(is_string($data['search']['state']) || is_numeric($data['search']['state'])) &&
			in_array($data['search']['state'], array("0", "1")) ){
				$config["where"][] = array('[and] so.shop_order_state=[+]', $data['search']['state']);
				}
			
			//支付状态
			if( isset($data['search']['pay_state']) && 
			(is_string($data['search']['pay_state']) || is_numeric($data['search']['pay_state'])) &&
			in_array($data['search']['pay_state'], array("0", "1", "2")) ){
				$config["where"][] = array('[and] so.shop_order_pay_state=[+]', $data['search']['pay_state']);
				}
			
			//物流状态
			if( isset($data['search']['shipping_state']) && 
			(is_string($data['search']['shipping_state']) || is_numeric($data['search']['shipping_state'])) &&
			in_array($data['search']['shipping_state'], array("0", "1", "2")) ){
				$config["where"][] = array('[and] so.shop_order_shipping_state=[+]', $data['search']['shipping_state']);
				}
			
			//交易状态
			if( isset($data['search']['transaction_state']) && 
			(is_string($data['search']['transaction_state']) || is_numeric($data['search']['transaction_state'])) &&
			in_array($data['search']['transaction_state'], array("0", "1")) ){
				
				if( $data['search']['transaction_state'] == 1 ){
					$config["where"][] = array('[and] so.shop_order_state=1');
					$config["where"][] = array('[and] so.shop_order_pay_state=1');
					$config["where"][] = array('[and] so.shop_order_shipping_state=1');
				}else{
					$config["where"][] = array('[and] (so.shop_order_shipping_state=0 OR so.shop_order_state=0 OR so.shop_order_pay_state=0)');
				}
				
			}
			
		}
		
		return object(parent::TABLE_SHOP_ORDER)->select_page($config);
	}
	
	
	
	
	
	
		
	/**
	 * 逻辑回收订单
	 * 
	 * SHOPADMINORDERTRASH
	 * {"class":"shop/admin_order","method":"api_trash"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_trash($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ORDER_TRASH);
		object(parent::ERROR)->check($data, 'shop_order_id', parent::TABLE_SHOP_ORDER, array('args'));
		
		//获取旧数据
		$old_data = object(parent::TABLE_SHOP_ORDER)->find($data['shop_order_id']);
		if( empty($old_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		if( !empty($old_data["shop_order_trash"]) ){
			throw new error("该订单已经在回收站");
		}
		
		//更新回收状态
		$update_data["shop_order_trash"] = 1;
		$update_data['shop_order_trash_time'] = time();
		if( object(parent::TABLE_SHOP_ORDER)->update( array(array('shop_order_id=[+]', (string)$data['shop_order_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['shop_order_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	

	
	
		
	/**
	 * 获取回收订单的数据列表
	 * 
	 * SHOPADMINORDERTRASHLIST
	 * {"class":"shop/admin_order","method":"api_trash_list"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_trash_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ORDER_TRASH_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'user_id_desc' => array('user_id', true),
			'user_id_asc' => array('user_id', false),
			
			'trash_time_desc' => array('shop_order_trash_time', true),
			'trash_time_asc' => array('shop_order_trash_time', false),
			
			'update_time_desc' => array('shop_order_update_time', true),
			'update_time_asc' => array('shop_order_update_time', false),
			
			'insert_time_desc' => array('shop_order_insert_time', true),
			'insert_time_asc' => array('shop_order_insert_time', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('shop_order_id', false);
		
		$config["where"][] = array('[and] so.shop_order_trash=1');
		if(!empty($data['search'])){
			if( isset($data['search']['shop_order_id']) && is_string($data['search']['shop_order_id']) ){
				$config["where"][] = array('[and] so.shop_order_id=[+]', $data['search']['shop_order_id']);
				}
		}
		
		return object(parent::TABLE_SHOP_ORDER)->select_page($config);
	}
	
	
	
	
	
			
	/**
	 * 还原回收订单
	 * 
	 * SHOPADMINORDERTRASHRESTORE
	 * {"class":"shop/admin_order","method":"api_trash_restore"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_trash_restore($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ORDER_TRASH_RESTORE);
		object(parent::ERROR)->check($data, 'shop_order_id', parent::TABLE_SHOP_ORDER, array('args'));
		
		//获取旧数据
		$old_data = object(parent::TABLE_SHOP_ORDER)->find($data['shop_order_id']);
		if( empty($old_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		if( empty($old_data["shop_order_trash"]) ){
			throw new error("该商品不在回收站");
		}
		
		//更新回收状态
		$update_data["shop_order_trash"] = 0;
		$update_data["shop_order_update_time"] = time();
		if( object(parent::TABLE_SHOP_ORDER)->update( array(array('shop_order_id=[+]', (string)$data['shop_order_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['shop_order_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	/**
	 * 确认发货/确认收货
	 * 
	 * SHOPADMINORDERSHIPPING
	 * {"class":"shop/admin_order","method":"api_shipping"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_shipping($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ORDER_SHIPPING);
		object(parent::ERROR)->check($data, 'shop_order_id', parent::TABLE_SHOP_ORDER, array('args'));
		object(parent::ERROR)->check($data, 'shop_order_shipping_state', parent::TABLE_SHOP_ORDER, array('args'));
		
		//获取旧数据
		$old_data = object(parent::TABLE_SHOP_ORDER)->find($data['shop_order_id']);
		if( empty($old_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		if( empty($old_data['shop_order_state']) ){
			throw new error("该订单已取消");
		}
		
		if( empty($old_data['shop_order_pay_state']) ){
			throw new error("该订单未支付");
		}
		
		$update_data = array();
		$update_data['shop_order_shipping_state'] = $data['shop_order_shipping_state'];
		
		//状态。0未发货，等待发货；1确认收货; 2已发货，运送中, 3已到货
		if( $data['shop_order_shipping_state'] == 2 ){
			if( $old_data['shop_order_shipping_state'] != 0 ){
				throw new error("该订单已发货，请勿重复操作");
			}
			object(parent::ERROR)->check($data, 'shop_order_shipping_no', parent::TABLE_SHOP_ORDER, array('args'));
            object(parent::ERROR)->check($data, 'shop_order_shipping_id', parent::TABLE_SHOP_ORDER, array('args'));
			
			$update_data['shop_order_shipping_no'] = $data['shop_order_shipping_no'];
			$update_data['shop_order_shipping_send_time'] = time();
			
			$shinpping = object(parent::TABLE_SHIPPING)->find($data['shop_order_shipping_id']);
            if( empty($shinpping) || empty($shinpping['shipping_sign']) ){
                throw new error('物流信息异常');
            }
			
			//管理员物流推送 物流ID
            $update_data['shop_order_shipping_id'] = $data['shop_order_shipping_id'];
            //管理员物流推送 物流标识
            $update_data['shop_order_shipping_sign'] = $shinpping['shipping_sign'];
			
		}else
		if( $data['shop_order_shipping_state'] == 1 ){
			if( $old_data['shop_order_shipping_state'] == 1 ){
				throw new error("该订单已收货，请勿重复操作");
			}
			if( $old_data['shop_order_shipping_state'] != 2 ){
				throw new error("该订单未发货");
			}
			
			$update_data['shop_order_shipping_take_time'] = time();
		}else{
			// throw new error("该订单配送状态异常");
		}
		
		
		$update_data["shop_order_update_time"] = time();
		if( object(parent::TABLE_SHOP_ORDER)->update( array(array('shop_order_id=[+]', (string)$data['shop_order_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			if($data['shop_order_shipping_state'] == 1){
				//触发收货事件
				object(parent::REQUEST_SHOP)->event_order_shipping_take($data['shop_order_id']);
			}else
			if($data['shop_order_shipping_state'] == 2){
				//触发发货事件
				object(parent::REQUEST_SHOP)->event_order_shipping_send($data['shop_order_id']);
			}
			return $data['shop_order_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
	/**
	 * 取消/确认订单
	 * 
	 * SHOPADMINORDERSTATE
	 * {"class":"shop/admin_order","method":"api_state"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_state($data = array()){
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ORDER_STATE);
		object(parent::ERROR)->check($data, 'shop_order_id', parent::TABLE_SHOP_ORDER, array('args'));
		object(parent::ERROR)->check($data, 'shop_order_state', parent::TABLE_SHOP_ORDER, array('args'));
		
		//获取旧数据
		$old_data = object(parent::TABLE_SHOP_ORDER)->find($data['shop_order_id']);
		if( empty($old_data) ){
			throw new error("ID有误，数据不存在");
		}
		
		$update_data = array();
		$update_data['shop_order_state'] = $data['shop_order_state'];
		
		if( $data['shop_order_state'] == 1 ){
			if( $old_data['shop_order_state'] == 1 ){
				throw new error("该订单已确认，请勿重复操作");
			}
		}else
		if( $data['shop_order_state'] == 0 ){
			if( $old_data['shop_order_state'] == 0 ){
				throw new error("该订单已取消，请勿重复操作");
			}
			
			if( !empty($old_data['shop_order_pay_state']) ){
				throw new error("该订单已支付，无法取消");
			}
			
			$update_data['shop_order_close_time'] = time();
		}else{
			throw new error("该订单状态异常");
		}
		
		$update_data["shop_order_update_time"] = time();
		if( object(parent::TABLE_SHOP_ORDER)->update( array(array('shop_order_id=[+]', (string)$data['shop_order_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['shop_order_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
}
?>