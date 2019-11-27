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
class event extends \eapie\source\request\shop {
	
	
	//商城事件
	
	
	
	/**
	 * 当订单支付完成 
	 * 支付状态。0未支付；1支付成功; 2支付中(积分支付或者人民币支付任一个完成支付)
	 * shop_order_pay_state == 1
	 * 
	 * SHOPEVENTORDERPAYMENTCOMPLETE
	 * {"class":"shop/event","method":"api_order_payment_complete"}
	 * 
	 * @param	string	$order_id [商城订单ID]
	 * @return	void
	 */
	public function api_order_payment_complete( $order_id = '' ){
		//object(parent::TABLE_LOCK)->start('api_order_payment_complete', $order_id, '事件已出发', 30, true);
		//file_put_contents(CACHE_PATH."/api_order_payment_complete", cmd(array($order_id), "json encode"));
		
		// 易淘
		if(object(parent::TABLE_USER_RECOMMEND)->verification_yitao_distribution()){
			object(parent::TABLE_USER_RECOMMEND)->no_condition_goods_user_recommend($order_id);        		// 确认支付，新增会员，生成分销关系链
		}
		// E麦
		if(object(parent::TABLE_USER_RECOMMEND)->verification_distribution()){
			object(parent::TABLE_USER_RECOMMEND)->goods_user_recommend($order_id);        					// 确认支付，新增会员，生成分销关系链,并发放分销奖励
		}
		
		
		$shop_order = object(parent::TABLE_SHOP_ORDER)->find_unbuffered($order_id);
		if( empty($shop_order) ){
			throw new error('订单数据不存在');
		}
        
		//订单状态。0取消订单，1确认订单
		if( $shop_order['shop_order_state'] == 0 ){
			throw new error('订单已取消');
		}
        //是否已回收。0正常；1已回收
		if( $shop_order['shop_order_trash'] == 1 ){
			throw new error('订单已回收');
		}
		//用户删除状态。0未删，1已删
		if( $shop_order['shop_order_delete_state'] == 1 ){
			throw new error('订单已删除');
		}
		//支付状态。0未支付；1支付成功; 2支付中(积分支付或者人民币支付任一个完成支付)
        if( $shop_order['shop_order_pay_state'] != 1 ){
        	throw new error('订单没有支付完成');
        }
		
		//判断事件是否已经执行了
		if( !empty($shop_order['shop_order_json']) ){
			$shop_order['shop_order_json'] = cmd(array($shop_order['shop_order_json']), 'json decode');
		}else{
			$shop_order['shop_order_json'] = array();
		}
		
		if( isset($shop_order['shop_order_json']['event_order_payment_complete']) ){
        	throw new error('订单事件已经执行了，请勿重复执行');
        }else{
        	$shop_order['shop_order_json']['event_order_payment_complete'] = cmd(array(time()), 'time date');
        }
		
		//减库存，加销量
		//减库存方式。
        //0表示不减库存；
        //1表示下单预扣，如果买家在保留时间内未付款，那预扣库存会被释放出来重新供大家购买。有效时间以全局配置默认。
        //2表示付款减库存。退款则恢复库存。
        //3表示发货减库存,退货则恢复库存。
		object(parent::TABLE_SHOP_ORDER_GOODS)->update_stock_sales($order_id, 2);
		
		//更新事件记录
		object(parent::TABLE_SHOP_ORDER)->update_json($order_id, $shop_order['shop_order_json']);
	}
	
	
	
	/**
	 * 当订单支付了一部分
	 * 支付状态。0未支付；1支付成功; 2支付中(积分支付或者人民币支付任一个完成支付)
	 * shop_order_pay_state == 2 
	 * 
	 * SHOPEVENTORDERPAYMENTMOIETY
	 * {"class":"shop/event","method":"api_order_payment_moiety"}
	 * 
	 * @param	string	$order_id [商城订单ID]
	 * @return	void
	 */
	public function api_order_payment_moiety( $order_id = '' ){
		//object(parent::TABLE_LOCK)->start('test', '这是测试，事件已出发'.$order_id, 'moiety', 30, true);
		//file_put_contents(CACHE_PATH."/api_order_payment_moiety", cmd(array($order_id), "json encode"));
		
		
	}
	
	
	
	
	/**
	 * 当订单创建 
	 * shop_order_pay_state == 1
	 * 
	 * SHOPEVENTORDERFOUND
	 * {"class":"shop/event","method":"api_order_found"}
	 * 
	 * @param	string	$order_id [商城订单ID]
	 * @return	void
	 */
	public function api_order_found( $shop_order_id = '' ){
		$shop_order = object(parent::TABLE_SHOP_ORDER)->find_unbuffered($shop_order_id);
		if( empty($shop_order) ){
			throw new error('订单数据不存在');
		}
        
		//订单状态。0取消订单，1确认订单
		if( $shop_order['shop_order_state'] == 0 ){
			throw new error('订单已取消');
		}
        //是否已回收。0正常；1已回收
		if( $shop_order['shop_order_trash'] == 1 ){
			throw new error('订单已回收');
		}
		//用户删除状态。0未删，1已删
		if( $shop_order['shop_order_delete_state'] == 1 ){
			throw new error('订单已删除');
		}
		//判断事件是否已经执行了
		if( !empty($shop_order['shop_order_json']) ){
			$shop_order['shop_order_json'] = cmd(array($shop_order['shop_order_json']), 'json decode');
		}else{
			$shop_order['shop_order_json'] = array();
		}
		
        if( isset($shop_order['shop_order_json']['event_order_found']) ){
        	throw new error('订单事件已经执行了，请勿重复执行');
        }else{
        	$shop_order['shop_order_json']['event_order_found'] = cmd(array(time()), 'time date');
        }
		
		//减库存，加销量
		//减库存方式。
        //0表示不减库存；
        //1表示下单预扣，如果买家在保留时间内未付款，那预扣库存会被释放出来重新供大家购买。有效时间以全局配置默认。
        //2表示付款减库存。退款则恢复库存。
        //3表示发货减库存,退货则恢复库存。
		object(parent::TABLE_SHOP_ORDER_GOODS)->update_stock_sales($shop_order_id, 1);
		
		//更新事件记录
		object(parent::TABLE_SHOP_ORDER)->update_json($shop_order_id, $shop_order['shop_order_json']);
		
	}
	
	
	
		
	/**
	 * 当订单确认发货的时候触发事件 
	 * shop_order_shipping_state == 2
	 * 
	 * SHOPEVENTORDERSHIPPINGSEND
	 * {"class":"shop/event","method":"api_order_shipping_send"}
	 * 
	 * @param	string	$order_id [商城订单ID]
	 * @return	void
	 */
	public function api_order_shipping_send( $shop_order_id = '' ){
		// 已确认收货
		$shop_order = object(parent::TABLE_SHOP_ORDER)->find($shop_order_id);
		//订单状态。0取消订单，1确认订单
		if( $shop_order['shop_order_state'] == 0 ){
			throw new error('订单已取消');
		}
        //是否已回收。0正常；1已回收
		if( $shop_order['shop_order_trash'] == 1 ){
			throw new error('订单已回收');
		}
		//用户删除状态。0未删，1已删
		if( $shop_order['shop_order_delete_state'] == 1 ){
			throw new error('订单已删除');
		}
		
		if((int)$shop_order['shop_order_shipping_state'] !== 2){
			throw new error('订单非发货状态');
		}
		
		//判断事件是否已经执行了
		if( !empty($shop_order['shop_order_json']) ){
			$shop_order['shop_order_json'] = cmd(array($shop_order['shop_order_json']), 'json decode');
		}else{
			$shop_order['shop_order_json'] = array();
		}
		
        if( isset($shop_order['shop_order_json']['event_order_shipping_send']) ){
        	throw new error('订单事件已经执行了，请勿重复执行');
        }else{
        	$shop_order['shop_order_json']['event_order_shipping_send'] = cmd(array(time()), 'time date');
        }
		
		//减库存，加销量
		//减库存方式。
        //0表示不减库存；
        //1表示下单预扣，如果买家在保留时间内未付款，那预扣库存会被释放出来重新供大家购买。有效时间以全局配置默认。
        //2表示付款减库存。退款则恢复库存。
        //3表示发货减库存,退货则恢复库存。
		object(parent::TABLE_SHOP_ORDER_GOODS)->update_stock_sales($shop_order_id, 3);
		
		//更新事件记录
		object(parent::TABLE_SHOP_ORDER)->update_json($shop_order_id, $shop_order['shop_order_json']);
		
		
	}
	
		
	
	
		
	
	/**
	 * 当订单确认收货的时候触发事件 
	 * shop_order_shipping_state == 1
	 * 
	 * SHOPEVENTORDERSHIPPINGTAKE
	 * {"class":"shop/event","method":"api_order_shipping_take"}
	 * 
	 * @param	string	$order_id [商城订单ID]
	 * @return	void
	 */
	public function api_order_shipping_take( $order_id = '' ){
		// object(parent::TABLE_LOCK)->start('test', '这是测试，事件已出发'.$order_id, 'api_order_shipping_take', 30, true);
		//file_put_contents(CACHE_PATH."/api_order_shipping_take", cmd(array($order_id), "json encode"));
		// 已确认收货
		$shop_order = object(parent::TABLE_SHOP_ORDER)->find($order_id);
		if((int)$shop_order['shop_order_shipping_state'] !== 1)
			return false;
		
		// 推荐商品奖励发放
		object(parent::TABLE_SHOP_ORDER_GOODS)->invite_money_reward($order_id);

		//	易淘分销奖励发放
		if(object(parent::TABLE_USER_RECOMMEND)->verification_yitao_distribution()){
			object(parent::TABLE_SHOP_ORDER_GOODS)->version_yitao_invite_money_reward($order_id);
		}
		
		
		
	}
	
	
	
	
	
	/**
	 * 当团购订单拼团失败退钱
	 * 状态：0拼团失败，1拼团成功，2拼团中 （shop_order_group_state == 0）
	 * 退款状态：0未退款，1已退款（shop_order_group_refund_state == 0）
	 * 支付状态：支付：0否，1是(shop_order_group_pay == 1)
	 * SHOPEVENTORDERGROUPREFUND
	 * {"class":"shop/event","method":"api_order_group_refund"}
	 * 
	 * @param	void
	 * @return	void
	 */
	public function api_order_group_refund(){
		//开启事件
        $event_lock_id = object(parent::TABLE_LOCK)->start("shop.event", "团购订单拼团失败退钱", parent::LOCK_EVENT, 86400);
        if( empty($event_lock_id) ){
        	echo "团购订单拼团失败退钱的锁已被占用";exit;
        }
		
		ignore_user_abort ( true );//客户端断开连接时不中断脚本的执行 
		set_time_limit(0);//设置最大执行时间
		ini_set('memory_limit', '-1');//内存限制
		
		$i = 10;
		do {
			
			//随机获取 获取一条需要退款的团购订单
			$shop_order_group = object(parent::TABLE_SHOP_ORDER_GROUP)->find_random_refund();
			if( empty($shop_order_group) ){
				echo "没有需要退款的团购订单";exit;
			}
			
			//拼团失败退款
			$bool = object(parent::TABLE_SHOP_ORDER_GROUP)->fail_refund($shop_order_group);
			$i --;
			
		}while( $i > 0 );
		//继续触发事件，异步团购订单退款
		object(parent::REQUEST_SHOP)->event_order_group_refund();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>