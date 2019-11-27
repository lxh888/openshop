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
class order extends \eapie\source\request\shop {



	//商城订单



	/**
	 * 购物商品的订单确认与结算
	 * 
	 * SHOPORDERSELFCONFIRM
	 * {"class":"shop/order","method":"api_self_confirm"}
	 * 
	 * [{"cart_id":["购物车ID","购物车ID2"],"shop":[{"shop_id":"店铺ID","shipping_id":"配送方式，配送ID","money_coupon_id":"用户的人民币优惠券ID","credit_coupon_id":"用户的积分优惠券ID","comment":"订单备注"}],"address_id":"收货地址ID"}]
	 * 
	 * @param	array	$arguments
	 * @return	array
	 */
	public function api_self_confirm( $arguments = array() ){
		//检测登录
        object(parent::REQUEST_USER)->check();
		//获取购物车数据
		$cart_data = $this->_cart_data($arguments);
		//获取店铺信息
		$shop_data = $this->_shop_data($arguments);
		//获取收货地址信息 
		$address_data = $this->_address_data($arguments);
		//获取商品、规格信息
		$carts = object(parent::TABLE_SHOP_CART)->get_data($cart_data);
		$cart_list = $this->_cart_list($cart_data, $carts, $shop_data, true);
		return array(
			'address' => $address_data,
			'shop' => $cart_list['shop_list'],
			'statistic' => $cart_list['statistic']
		);
	}
	
	
	
	/**
	 * 创建订单
	 * 1）根据店铺生成一个订单A
	 * 2）如果存在积分商品和人民币商品，生成两个订单A的两个子订单
	 * 3）只存在积分商品或者人民币商品任一一个，就只有订单A
	 * 
	 * SHOPORDERSELFFOUND
	 * {"class":"shop/order","method":"api_self_found"}
	 * 
	 * [{"cart_id":["购物车ID","购物车ID2"],"address_id": "收货地址ID","shop":[{"shop_id":"店铺ID","shipping_id":"配送方式，配送ID","money_coupon_id":"用户的人民币优惠券ID","credit_coupon_id":"用户的积分优惠券ID","comment":"订单备注"}]}]
	 * 
	 * @param	array	$arguments
	 * @return	array
	 */
	public function api_self_found( $arguments = array() ){
		//检测登录
        object(parent::REQUEST_USER)->check();
		// 锁表
        $lock_id = object(parent::TABLE_LOCK)->start('user_id', $_SESSION['user_id'], parent::LOCK_SHOP_ORDER_INSERT);
        if( empty($lock_id) ){
            throw new error('请勿重复操作');
        }
		
		//获取购物车数据
		$cart_data = $this->_cart_data($arguments);
		//获取店铺信息
		$shop_data = $this->_shop_data($arguments);
		//获取收货地址信息 
		$address_data = $this->_address_data($arguments);
		if( empty($address_data) ){
			throw new error('请选择收货地址');
		}
		
		//检测收货地址
        object(parent::ERROR)->check($address_data, 'consignee', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_consignee');
        object(parent::ERROR)->check($address_data, 'phone', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_phone');
        object(parent::ERROR)->check($address_data, 'province', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_province');
        object(parent::ERROR)->check($address_data, 'city', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_city');
        object(parent::ERROR)->check($address_data, 'district', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_district');
        object(parent::ERROR)->check($address_data, 'details', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_details');
		
		//获取商品、规格信息
		$carts = object(parent::TABLE_SHOP_CART)->get_data($cart_data);
		$cart_list = $this->_cart_list($cart_data, $carts, $shop_data);
		if( empty($cart_list) ){
			throw new error('没有下单数据');
		}
		
		//暂时只考虑 自营店铺
		if( isset($cart_list['shop_list'][0]['shop_id']) && $cart_list['shop_list'][0]['shop_id'] != '' ){
			throw new error('下单失败，目前只支持自营店铺下单');
		}
		
		$shop = $cart_list['shop_list'][0];
		
		
		//检查配送
		if( empty($shop['shipping']['id']) ){
			throw new error('请选择配送方式');
		}
		
		//为了预留多店铺功能
		$shop_order_inserts = array();
		$shop_order_inserts[] = array(
			'shop_order_id' => object(parent::TABLE_SHOP_ORDER)->get_unique_id(),
			'user_id' => $_SESSION['user_id'],
			'shop_id' => $shop['shop_id'],
			'shop_order_money' => $shop['total_money'],
			'shop_order_credit' => $shop['total_credit'],
			'shop_order_goods_money' => $shop['property_money']['total_price'],
			'shop_order_goods_credit' => $shop['property_credit']['total_price'],
			'shop_order_discount_money' => $shop['discount']['money'],//折扣人民币
			'shop_order_discount_credit' => $shop['discount']['credit'],//折扣积分
			'shop_order_shipping_property' => $shop['shipping']['property'],//运费类型
            'shop_order_shipping_price' => $shop['shipping']['price'],//运费
            'shop_order_buyer_note'		=> $shop['comment'],//买家备注
            'shipping_id'		=> $shop['shipping']['id'],//配送ID
            'shop_order_shipping_state' => 0,
            'shop_order_pay_parent'     => 0,
            'shop_order_pay_state'      => 0,
            'shop_order_state'          => 1,
            'shop_order_insert_time'    => time(),
            'shop_order_update_time'    => time(),
            //收货信息
            'user_address_consignee'	=> $address_data['consignee'],
            'user_address_phone'		=> $address_data['phone'],
            'user_address_province'		=> $address_data['province'],
            'user_address_city'			=> $address_data['city'],
            'user_address_district'		=> $address_data['district'],
			'user_address_details'		=> $address_data['details'],
		);
		
		$shop_order_id = $shop_order_inserts[0]['shop_order_id'];
		$shop_order_money = $shop_order_inserts[0]['shop_order_money'];
		$shop_order_credit = $shop_order_inserts[0]['shop_order_credit'];
		
		//获取商品
		$shop_order_goods_inserts = array();
		//下单减库存的SKU
		$sku_decrease_stock = array();
		
		//将人民币 与 积分 商品合并
		$goods_list = array_merge($shop['property_money']['goods'], $shop['property_credit']['goods']);
		if( empty($goods_list) ){
			throw new error('订单商品异常，数据为空');
		}
		
		//获取订单商品
		foreach($goods_list as $goods){
            // 查询推荐奖金(内置E麦商城判断)
            $recommend = object(parent::TABLE_SHOP_ORDER_GOODS)->calculation_recommend_money($goods['price'],$goods['recommend']['user_id'],$goods['id'],$_SESSION['user_id']);
            if($recommend){
                $recommend_user_id = $recommend['user_id'];
                $recommend_money = $recommend['money'];
            } else {
                $recommend_user_id = '';
                $recommend_money = 0;
            }
			$shop_order_goods_inserts[] = array(
				'shop_order_goods_id' => object(parent::TABLE_SHOP_ORDER_GOODS)->get_unique_id(),
				'shop_order_id' => $shop_order_id,
				'shop_goods_id' => $goods['id'],
				'shop_goods_sn' => $goods['sn'],
				'shop_goods_sku_id' => $goods['sku_id'],
				'shop_goods_property' => $goods['property'],
				'shop_goods_index' => $goods['index'],
				'shop_order_goods_name' => $goods['name'],
				'shop_order_goods_image_id' => $goods['logo'],
				'shop_order_goods_price' => $goods['price'],
				'shop_order_goods_number' => $goods['number'],
				'shop_order_goods_json' => cmd(array($goods), 'json encode'),
				'shop_order_goods_time' => time(),
                'recommend_user_id' => $recommend_user_id,
                'recommend_money' => $recommend_money,
			);

			//判断是否是下单减库存 
			//减库存方式。0表示不减库存；1表示下单预扣，如果买家在保留时间内未付款，那预扣库存会被释放出来重新供大家购买。有效时间以全局配置默认。2表示付款减库存。退款则恢复库存。3表示发货减库存,退货则恢复库存。
			if( $goods['shop_goods']['shop_goods_stock_mode'] == 1 ){
				$sku_decrease_stock[] = array(
					'shop_goods_sku_id' => $goods['sku_id'],
					'shop_order_goods_number' => $goods['number']
				);
			}
        }
		
		
		$bool = object(parent::TABLE_SHOP_ORDER)->found($shop_order_inserts, $shop_order_goods_inserts, $sku_decrease_stock);
		if( empty($bool) ){
			throw new error('下订单失败');
		}else{
			
			//删除购物车相关商品
            $shop_cart_ids = array();
            foreach( $cart_data as $val ){
                $shop_cart_ids[] = $val['shop_cart_id'];
            }
            object(parent::TABLE_SHOP_CART)->clear($_SESSION['user_id'], $shop_cart_ids);
			
			//触发事件
			object(parent::REQUEST_SHOP)->event_order_found($shop_order_id);
			
			 
			return array(
				'order_id' => $shop_order_id,
				'order_money' => $shop_order_money,
				'order_credit' => $shop_order_credit
			);
			//return $shop_order_id;
		}
	}
	
	
	
	/**
	 * 支付订单
	 * 
	 * SHOPORDERSELFPAYMENT
	 * {"class":"shop/order","method":"api_self_payment"}
	 * 
	 * [{"order_id":"订单ID","pay_method":"支付方式 weixinpay 微信支付、alipay 支付宝支付、user_money 用户钱包支付、user_credit 用户积分支付","pay_password":"支付密码，当支付方式为用户钱包或用户积分时必填","weixin_login_code":"微信支付是需要，用于换取用户OpenID","weixin_trade_type":"微信的交易类型，必须是大写，如JSAPI|APP|MWEB|NATIVE"}]
	 * 
	 * @param	array	$arguments
	 * @return	array
	 */
	public function api_self_payment( $arguments = array() ){
        object(parent::REQUEST_USER)->check();//检测登录
        
		//检测输入
        object(parent::ERROR)->check($arguments, 'order_id', parent::TABLE_SHOP_ORDER, array('args'), 'shop_order_id');
        object(parent::ERROR)->check($arguments, 'pay_method', parent::TABLE_SHOP_ORDER, array('args'));
		
		//查询商城订单信息
        $shop_order = object(parent::TABLE_SHOP_ORDER)->find( $arguments['order_id'] );
		if( empty($shop_order) || $shop_order['user_id'] != $_SESSION['user_id'] ){
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
        if( $shop_order['shop_order_pay_state'] == 1 ){
        	throw new error('订单已支付');
        }
            
		//查询配置信息
        $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('shop_order'), true);
        //判断订单超时时间
        if( !empty($config['order_timeout']) ){
            if( time() - $shop_order['shop_order_insert_time'] > $config['order_timeout'] ){
                $update_where = array(array('shop_order_id=[+]', $shop_order['shop_order_id']));
                $update_data = array(
                    'shop_order_state' => 0,
                    'shop_order_close_time' => time(),
                    'shop_order_update_time' => time(),
                );
                object(parent::TABLE_SHOP_ORDER)->update($update_where, $update_data);
                throw new error('订单已失效，请重新下单');
            }
        }
		
		//更新限时购商品状态
        object(parent::TABLE_SHOP_GOODS_WHEN)->update_state_clear();
		//查询商品信息
        $shop_order_goods = object(parent::TABLE_SHOP_ORDER_GOODS)->select_by_pay($shop_order['shop_order_id']);
		foreach($shop_order_goods as $val){
			if( !empty($val['shop_order_goods_json']) ){
				$val['shop_order_goods_json'] = cmd(array($val['shop_order_goods_json']), 'json decode');
			}
			if( !is_array($val['shop_order_goods_json']) ){
				$val['shop_order_goods_json'] = array();
			}
			
			//获取商品名称,包含规格信息
			$goods_name = '';
			if( isset($val['shop_order_goods_json']['name']) ){
				$goods_name .= $val['shop_order_goods_json']['name'];
			}
			if( isset($val['shop_order_goods_json']['spu_string']) ){
				$goods_name .= '['.$val['shop_order_goods_json']['spu_string'].']';
			}
			
			//检查商品基础状态
            if( empty($val['shop_goods_id']) || 
            $val['shop_goods_trash'] == 1 || 
            $val['shop_goods_state'] != 1 ||
			empty($val['shop_goods_sku_stock']) ){
            	throw new error('“'.$goods_name.'”商品已失效');
            }
            //检查规格状态
            if( $val['shop_order_goods_number'] > $val['shop_goods_sku_stock']){
            	throw new error('“'.$goods_name.'”商品库存不足');
            }
			
			//限时商品
			if( !empty($val['when_shop_goods_id']) ){
				if( empty($val['shop_goods_when_state']) || $val['shop_goods_when_state'] != 1 ){
					throw new error('“'.$goods_name.'”商品，限时购已结束');
				}
				if( !empty($val['shop_goods_when_state']) && $val['shop_goods_when_state'] == 2 ){
					throw new error('“'.$goods_name.'”商品，限时购还没有开始');
				}
			}
                
        }
		
		//获取订单的名称
		$subject_order_name = '商城购物-共'.count($shop_order_goods).'件商品,订单ID:'.$shop_order['shop_order_id'];
		
		
		//--------------------扩展备注----------------------------
		//根据 $shop_order['shop_id'] 获取店铺所属商家信息
		//最后将 钱或积分 增加到商家 数据中
		
		
		//资金订单
        $order = array(
            'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
            'order_type' => parent::TRANSACTION_TYPE_SHOP_ORDER,
            'order_comment' => '商城购物',
            'order_action_user_id' => $_SESSION['user_id'],
            'order_plus_method' => '',
            'order_plus_account_id' => '',
            //'order_plus_value' => 0, $shop_order['shop_order_money'] 或者 $shop_order['shop_order_credit']
            'order_minus_method' => $arguments['pay_method'],
            'order_minus_account_id' => $_SESSION['user_id'],
            'order_minus_transaction_id' => '',
            //'order_minus_value' => 0, $shop_order['shop_order_money'] 或者 $shop_order['shop_order_credit']
            'order_state' => 1,
            'order_pay_state' => 0,
            'order_sign' => $shop_order['shop_order_id'],
            'order_json' => array(),
            'order_insert_time' => time(),
        );
        // e麦商城，查询是否为新会员
        $is_new_member = false;
        // 是否为E麦商城
        if( $this->_is_emshop() ){
            // 是否为会员商品
            if( $this->_is_index_goods($shop_order['shop_order_id']) ){
                // 查询邀请记录
                $user_invite_data = object(parent::TABLE_USER_RECOMMEND)->find($_SESSION['user_id']);
                // 是否为会员
                if( !isset($user_invite_data['user_id']) || (isset($user_invite_data['user_recommend_state']) && $user_invite_data['user_recommend_state'] === 0)){
                    $is_new_member = true;
                }
            }
        }
		//微信支付\支付宝支付
		if( in_array($arguments['pay_method'], array(parent::PAY_METHOD_WEIXINPAY, parent::PAY_METHOD_ALIPAY)) ){
			if( empty($shop_order['shop_order_money']) ){
				throw new error ('订单不需要支付金额');
			}
			if( !empty($shop_order['shop_order_pay_money_state']) ){
				throw new error ('订单已经支付了金额');
			}
			
			$pay_config = object(parent::REQUEST_APPLICATION)->get_pay_config($arguments, $order['order_json'], array(
                'money_fen' => $shop_order['shop_order_money'],
                'subject' => $subject_order_name,
                'body' => $subject_order_name,
                'order_id' => $shop_order['shop_order_id'], //一定要传 商城购物订单ID  保证支付唯一性
                'standby_order_id' => $order['order_id']
            ));
			
			$order['order_minus_value'] = $shop_order['shop_order_money'];
			//将 order_json 转为字符串
			if( !empty($order['order_json']) ){
				$order['order_json'] = cmd(array($order['order_json']), 'json encode');
			}
			
            //插入订单数据
            if( object(parent::TABLE_ORDER)->insert($order) ){
                $pay_config['order_id'] = $order['order_id'];
                $pay_config['is_new_member'] = $is_new_member;
                return $pay_config;
            } else {
                throw new error ('操作失败');
            }
		}else
		//用户钱包支付
		if( $arguments['pay_method'] == parent::PAY_METHOD_USER_MONEY ){
            // 易淘
            $yitao_config = $this->verification_yitaoshop();
            if($yitao_config && $yitao_config['close_user_money'] == 1){
                throw new error ('用户钱包支付功能已关闭，请选择其他支付方式');
            }
			if( empty($shop_order['shop_order_money']) ){
				throw new error ('订单不需要支付金额');
			}
			if( !empty($shop_order['shop_order_pay_money_state']) ){
				throw new error ('订单已经支付了金额');
			}
			
			// 检测输入
            object(parent::ERROR)->check($arguments, 'pay_password', parent::TABLE_USER, array('args'));
			// 检测支付密码
            $res = object(parent::TABLE_USER)->check_pay_password($arguments['pay_password']);
            if( $res !== true ){
            	throw new error($res);
            }
            //检测余额
            $user_money = object(parent::TABLE_USER_MONEY)->find_now_data($_SESSION['user_id']);
            if( empty($user_money['user_money_value']) || $user_money['user_money_value'] < $shop_order['shop_order_money']){
            	throw new error('余额不足');
            }
			
			$order['order_minus_value'] = $shop_order['shop_order_money'];
			//将 order_json 转为字符串
			if( !empty($order['order_json']) ){
				$order['order_json'] = cmd(array($order['order_json']), 'json encode');
			}
			if( !object(parent::TABLE_ORDER)->insert($order) ){
				throw new error ('资金订单登记失败');
			}
			if( object(parent::TABLE_SHOP_ORDER)->payment($order, $user_money) ){
				return array('order_id' => $order['order_id'], 'is_new_member' => $is_new_member);
			}else{
				throw new error ('操作失败');
			}
		}else
		//用户积分支付
		if( $arguments['pay_method'] == parent::PAY_METHOD_USER_CREDIT ){
			if( empty($shop_order['shop_order_credit']) ){
				throw new error ('订单不需要支付积分');
			}
			if( !empty($shop_order['shop_order_pay_credit_state']) ){
				throw new error ('订单已经支付了积分');
			}
			
			// 检测输入
            object(parent::ERROR)->check($arguments, 'pay_password', parent::TABLE_USER, array('args'));
			// 检测支付密码
            $res = object(parent::TABLE_USER)->check_pay_password($arguments['pay_password']);
            if( $res !== true ){
            	throw new error($res);
            }
            //检测余额
            $user_credit = object(parent::TABLE_USER_CREDIT)->find_now_data($_SESSION['user_id']);
            if( empty($user_credit['user_credit_value']) || $user_credit['user_credit_value'] < $shop_order['shop_order_credit']){
            	throw new error('积分不足');
            }
			
			$order['order_minus_value'] = $shop_order['shop_order_credit'];
			//将 order_json 转为字符串
			if( !empty($order['order_json']) ){
				$order['order_json'] = cmd(array($order['order_json']), 'json encode');
			}
			if( !object(parent::TABLE_ORDER)->insert($order) ){
				throw new error ('资金订单登记失败');
			}
			if( object(parent::TABLE_SHOP_ORDER)->payment($order, $user_credit) ){
				return array('order_id' => $order['order_id'], 'is_new_member' => $is_new_member);
			}else{
				throw new error ('操作失败');
			}
		}else{
			throw new error ('支付方式异常');
		}
		
		
		
	}
	
	
	/**
     * 查询是否为易淘商城项目
     */
    public function verification_yitaoshop(){
        $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("yitaoshop"), true);
        if(!empty($config)){
            return $config;
        } else {
            return false;
        }
    }
	
	
	
	/**
	 * 购物车处理列表
	 * 
	 * @param	array	$cart_data
	 * @param	array	$carts
	 * @param	bool	$unset_db_data	是否删除数据库的数据
	 * @return array
	 */
	private  function _cart_list($cart_data, $carts, $shop_data, $unset_db_data = false){
		//拆单，先以店铺ID拆、然后以商品类型拆
		//开始整合数据
		$all_shop_total_credit = 0;//所有商铺的商品所需支付积分
		$all_shop_total_money = 0;//所有商铺的商品所需支付人民币
		$all_shop_goods_count = 0;//所有商铺的商品数量
		foreach($carts['shop_list'] as $shop_key => $shop_value){
			
			//人民币类型
			$carts['shop_list'][$shop_key]['property_money'] = array(
				'goods' => array(),//人民币商品
				'total_price' => 0,//总价格
				'count' => 0
			);
			
			//积分类型
			$carts['shop_list'][$shop_key]['property_credit'] = array(
				'goods' => array(),//人民币商品
				'total_price' => 0,//总积分
				'count' => 0
			);
			
			if( empty($cart_data) ){
				unset($carts['shop_list'][$shop_key]);
				continue;
			}
			//循环购物车数据
			foreach($cart_data as $cart_key => $cart_value){
				if($shop_value['shop_id'] != $cart_value['shop_id']){
					continue;
				}
				
				$goods = object(parent::TABLE_SHOP_CART)->get_goods($cart_value, $carts['shop_goods_list'], $carts['shop_goods_sku_list']);
				//状态码，“OK”合法商品、“SKUNOTEXIST”规格不存在、“SKUNOTSTOCK”库存不足、“GOODSNOTEXIST”商品不存在、“GOODSUNSHELVE”商品已下架
				if( $goods['state'] == 'GOODSNOTEXIST' ){
					throw new error('“'.$goods['name'].'['.$goods['spu_string'].']”商品不存在，请删除重试');//商品不存在
				}else
				if( $goods['state'] == 'GOODSUNSHELVE' ){
					throw new error('“'.$goods['name'].'['.$goods['spu_string'].']”商品已停止销售，请删除重试');//商品已下架
				}else
				if( $goods['state'] == 'SKUNOTEXIST' ){
					throw new error('“'.$goods['name'].'['.$goods['spu_string'].']”商品规格不存在，请删除重试');//规格不存在
				}else
				if( $goods['state'] == 'SKUNOTSTOCK' ){
					throw new error('“'.$goods['name'].'['.$goods['spu_string'].']”商品库存不足，请删除重试');//库存不足
				}else
				if( $goods['state'] == 'GOODSWHENEND' ){
					throw new error('“'.$goods['name'].'['.$goods['spu_string'].']”限时商品已结束，请删除重试');//库存不足
				}else
				if( $goods['state'] == 'GOODSWHENNOTSTART' ){
					throw new error('“'.$goods['name'].'['.$goods['spu_string'].']”限时商品未开始，请删除重试');//库存不足
				}
				
				
				if( $unset_db_data ){
					unset($goods['shop_goods'], $goods['shop_goods_sku']);
				}
				
				if( $goods['property'] == 1 ){
					$carts['shop_list'][$shop_key]['property_credit']['goods'][] = $goods;
					$carts['shop_list'][$shop_key]['property_credit']['total_price'] += $goods['price']*$goods['number'];
				}else{
					$carts['shop_list'][$shop_key]['property_money']['goods'][] = $goods;
					$carts['shop_list'][$shop_key]['property_money']['total_price'] += $goods['price']*$goods['number'];
				}
				
				unset($cart_data[$cart_key]);
			}
			
			//商品数量
			$credit_goods_count = count($carts['shop_list'][$shop_key]['property_credit']['goods']);
			$money_goods_count = count($carts['shop_list'][$shop_key]['property_money']['goods']);
			$carts['shop_list'][$shop_key]['property_credit']['count'] = $credit_goods_count;
			$carts['shop_list'][$shop_key]['property_money']['count'] = $money_goods_count;
			$carts['shop_list'][$shop_key]['total_count'] = $credit_goods_count + $money_goods_count;
			$carts['shop_list'][$shop_key]['total_money'] = $carts['shop_list'][$shop_key]['property_money']['total_price'];
			$carts['shop_list'][$shop_key]['total_credit'] = $carts['shop_list'][$shop_key]['property_credit']['total_price'];
			
			//没有商品，那么删除
			if( empty($carts['shop_list'][$shop_key]['total_count']) ){
				unset($carts['shop_list'][$shop_key]);
				continue;
			}

			//获取店铺的优惠券、配送金额
			if( !empty($shop_data) && isset($shop_data[$shop_value['shop_id']]) ){
				$shop = $shop_data[$shop_value['shop_id']];
				//优惠信息
				$carts['shop_list'][$shop_key]['discount'] = array(
					'money' => 0,//优惠 人民币(分)
					'credit' => 0,//优惠 积分
					'money_coupon_id' => $shop['money_coupon_id'],//人民币优惠券id
					'credit_coupon_id' => $shop['credit_coupon_id'],//积分优惠券id
					'money_coupon_error' => '',//钱包优惠券错误信息
					'credit_coupon_error' => '',//积分优惠券错误信息
				);
				
				//判断优惠券是否合法
				
				
				
				
				
				
				//获得配送信息
				$carts['shop_list'][$shop_key]['shipping'] = array(
					'id' => $shop['shipping_id'],//shipping_id
					'name' => empty($shop['shipping']['shipping_name'])? '' : $shop['shipping']['shipping_name'],
					'sign' => empty($shop['shipping']['shipping_sign'])? '' : $shop['shipping']['shipping_sign'],
					'property' => empty($shop['shipping']['shipping_property'])? 0 : $shop['shipping']['shipping_property'],
					'price' => empty($shop['shipping']['shipping_price'])? 0 : $shop['shipping']['shipping_price'],
					'comment' => empty($shop['shipping']['shipping_comment'])? '' : $shop['shipping']['shipping_comment'],
				);
				//获得配送价格
				if( isset($shop['shipping']['shipping_property']) ){
					if( $shop['shipping']['shipping_property'] == 1 ){
						$carts['shop_list'][$shop_key]['total_credit'] += $shop['shipping']['shipping_price'];
					}else{
						$carts['shop_list'][$shop_key]['total_money'] += $shop['shipping']['shipping_price'];
					}
				}
				
				//买家备注
				if( isset($shop['comment']) ){
					$carts['shop_list'][$shop_key]['comment'] = $shop['comment'];
				}
				
			}
			
			//统计
			$all_shop_total_credit += $carts['shop_list'][$shop_key]['total_credit'];
			$all_shop_total_money += $carts['shop_list'][$shop_key]['total_money'];
			$all_shop_goods_count += $carts['shop_list'][$shop_key]['total_count'];
			
		}
		
		//printexit($carts['shop_list']);
		return array(
			'statistic' => array(
				'total_credit' => $all_shop_total_credit, //所有商铺的商品所需支付积分
				'total_money' => $all_shop_total_money, //所有商铺的商品所需支付人民币
				'goods_count' => $all_shop_goods_count //所有商铺的商品数量
			),
			'shop_list' => $carts['shop_list']
		);
	}
	


	/**
	 * 获取购物车数据
	 * 
	 * @param	array	$arguments
	 * @return	array
	 */
	private function _cart_data($arguments){
		//先查询购物车数据
		if( !empty($arguments['cart_id']) && is_array($arguments['cart_id']) ){
			//清理数据并过滤数据
			$cart_ids = array();
			foreach($arguments['cart_id'] as $cart_id){
				if( (is_string($cart_id) || is_numeric($cart_id) ) && 
				!in_array($cart_id, $cart_ids) ){
					$cart_ids[] = cmd(array($cart_id), 'str addslashes');
				}
			}
		}
		
		if( empty($cart_ids) ){
			throw new error('购物车ID有误');
		}
		
		//获取数据
		$in_string = "\"".implode("\",\"", $cart_ids)."\"";
		$where = array(
			array("shop_cart_id IN([-])", $in_string, true),
			array("[and] user_id=[+]", $_SESSION['user_id'])
		);
		//获取数据
		$cart_data = object(parent::TABLE_SHOP_CART)->select(array("where"=>$where));
		if( empty($cart_data) ){
			throw new error('购物车ID异常，数据为空');
		}
		
		return $cart_data;
	}
    

    /**
     * 查询收货地址
     * $arguments = array(
	 * 	'address_id'	收货地址ID
	 * )
	 * 
     * @param	array	$arguments
     * @return	array
     */
    private function _address_data( $arguments = array() ){
    	if( empty($arguments['address_id']) ){
    		return array();
    	}
		
        //查询收货地址信息
        $address = object(parent::TABLE_USER_ADDRESS)->find_where(array(
			array('user_address_id=[+]', $arguments['address_id']),
			array('[and] user_id=[+]', $_SESSION['user_id'])
		), array(
			'user_address_id as id',
			'user_address_consignee as consignee',
			'user_address_phone as phone',
			'user_address_province as province',
			'user_address_city as city',
			'user_address_district as district',
			'user_address_details as details',
		));
        if( empty($address) ){
        	return array();
        }
		
        return $address;
    }
	
	
	
	
	
	/**
	 * 获取店铺的传入数据，获取配送、获取优惠券
	 * 
	 * @param	array	$arguments
	 * @return	array
	 */
	private function _shop_data($arguments){
		if( empty($arguments['shop']) || !is_array($arguments['shop']) ){
			return array();
		}
		
		//清理店铺id
		$shops = array();
		$shipping_ids = array();
		$user_coupon_ids = array();
		foreach($arguments['shop'] as $shop){
			if( empty($shop) || !is_array($shop) ){
				continue;
			}
			if( !isset($shop['shop_id']) ){
				$shop['shop_id'] = '';
			}
			if( isset($shops[$shop['shop_id']]) ){
				continue;
			}
			
			$temp_shop = array(
				'shop_id' => $shop['shop_id'],
				'shipping_id' => empty($shop['shipping_id'])? '' : $shop['shipping_id'],
				'money_coupon_id' => empty($shop['money_coupon_id'])? '' : $shop['money_coupon_id'],
				'credit_coupon_id' => empty($shop['credit_coupon_id'])? '' : $shop['credit_coupon_id'],
				'comment' => empty($shop['comment'])? '' : $shop['comment'],
			);
			
			if( !in_array($temp_shop['shipping_id'], $shipping_ids) ){
				$shipping_ids[] = $temp_shop['shipping_id'];
			}
			if( !in_array($temp_shop['money_coupon_id'], $user_coupon_ids) ){
				$user_coupon_ids[] = $temp_shop['money_coupon_id'];
			}
			if( !in_array($temp_shop['credit_coupon_id'], $user_coupon_ids) ){
				$user_coupon_ids[] = $temp_shop['credit_coupon_id'];
			}
			
			$shops[$shop['shop_id']] = $temp_shop;
		}
        
        if( empty($shops) ){
			return array();
        }
        
		//获取 配送
		$shipping_in_string = "\"".implode("\",\"", $shipping_ids)."\"";
		$shipping_data = object(parent::TABLE_SHIPPING)->select(array(
			'where' => array(
				array("shipping_id IN([-])", $shipping_in_string, true),
				array("[and] shipping_module=[+]", parent::MODULE_SHOP_ORDER)
			)
		));
		
		//获取优惠券数据，获取用户自己的
		$user_coupon_in_string = "\"".implode("\",\"", $user_coupon_ids)."\"";
		$user_coupon_data = object(parent::TABLE_USER_COUPON)->select_join(array(
			"where" => array(
				array("uc.user_coupon_id IN([-])", $user_coupon_in_string, true),
				array("[and] uc.user_id=[+]", $_SESSION['user_id']),
				array("[and] uc.user_coupon_state=1"),
				//使用的次数 等于0 表示无限制次数|使用的次数大于已使用次数
				array("[and] (uc.user_coupon_number=0 OR uc.user_coupon_number>user_coupon_use_number)"),
				//过期时间为0 表示无过期时间|或者大于当前时间
				array("[and] (uc.user_coupon_expire_time=0 OR uc.user_coupon_expire_time>".time().")")
			),
			"select" => array(
				'uc.*',
				'c.*'
			)
		));
			
		
		foreach($shops as $shop_key => $shop_value){
			if( empty($shipping_data) && empty($user_coupon_data) ){
				break;
			}
            if (!empty($shipping_data)) {
                foreach($shipping_data as $s_value){
                    if($s_value['shipping_id'] == $shop_value['shipping_id'] ){
                        $shops[$shop_key]['shipping'] = $s_value;
                        break;
                    }
                }
            }
            if (!empty($user_coupon_data)) {
                foreach($user_coupon_data as $uc_value){
                    if( empty($shops[$shop_key]['money_coupon']) && 
                    $uc_value['money_coupon_id'] == $shop_value['user_coupon_id'] &&
                    $uc_value['coupon_property'] == 0 ){
                        $shops[$shop_key]['money_coupon'] = $uc_value;
                    }
                    
                    if( empty($shops[$shop_key]['credit_coupon']) && 
                    $uc_value['credit_coupon_id'] == $shop_value['user_coupon_id'] &&
                    $uc_value['coupon_property'] == 1 ){
                        $shops[$shop_key]['credit_coupon'] = $uc_value;
                    }
                    
                    //都找到了 不需要再循环了
                    if( !empty($shops[$shop_key]['credit_coupon']) && !empty($shops[$shop_key]['money_coupon'])){
                        break;
                    }
                }
            }

		}
		
		//printexit($shops);
		
		return $shops;
		
	}





    /**
     * 是否为E麦商城
     * @return boolean $result
     */
    public function _is_emshop(){
        $data = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("emshop_config"), true);
        if( empty($data) )
            return false;
        
        return true;
    }
	
	
	
    /**
     * 是否为会员商品
     * @param string $shop_order_id
     * @return boolean $result 
     */
    public function _is_index_goods($shop_order_id){
        $shop_goods_index = object(parent::TABLE_SHOP_ORDER_GOODS)->find($shop_order_id);

        // 订单数据不存在
        if(!isset($shop_goods_index['shop_goods_index']))
            return false;

        // 判断交易类型是否为礼包类商品(会员类商品)
        $shop_goods_index = (int)$shop_goods_index['shop_goods_index'];
        if( isset($shop_goods_index) && $shop_goods_index === 1)
            return true;

        return false;
    }
	
	





    /**
     * 提交订单
     *
     * api: SHOPORDERSELFADD
     * req: {
     *  address_id  [str] [必填] [收货地址ID]
     *  pay_method  [str] [必填] [支付方式]
     *  shop        [arr] [必填] [店铺信息]
     *  recommend_user_id [int] [选填] [推广人ID]
     * }
     * 
     * @return string 订单ID
     */
//  public function api_self_add($input = array())
//  {
//      //检测登录
//      object(parent::REQUEST_USER)->check();
//      $user_id = $_SESSION['user_id'];
//
//      //检测输入
//      object(parent::ERROR)->check($input, 'address_id', parent::TABLE_USER_ADDRESS, array('args'), 'user_address_id');
//      object(parent::ERROR)->check($input, 'pay_method', parent::TABLE_SHOP_ORDER, array('args'));
//      if (empty($input['shop']) || !is_array($input['shop']))
//          throw new error('店铺信息不合法');
//
//      //收货地址信息
//      $user_address = $this->_get_user_address($input['address_id']);
//      //订单信息
//      $shop_order = array();
//      //订单商品信息
//      $shop_order_goods = array();
//
//      //——循环订单中的店铺——
//      foreach ($input['shop'] as $shop) {
//          //检测输入
//          if (!isset($shop['shop_id']) || !is_string($shop['shop_id']))
//              throw new error('店铺ID不合法');
//
//          //是否自营
//          if ($shop['shop_id'] !== '') {
//              //查询店铺信息
//              $shop_data = object(parent::TABLE_SHOP)->find($shop['shop_id']);
//              if (empty($shop_data))
//                  throw new error('店铺ID：'.$shop['shop_id'].'有误，数据不存在');
//              if ($shop_data['shop_state'] != 1 || $shop_data['shop_on_off'] != 1)
//                  throw new error('店铺：'.$shop_data['shop_name'].'，状态异常');
//          }
//
//          if (empty($shop['shop_goods']) || !is_array($shop['shop_goods']))
//              throw new error('商品信息不合法');
//
//          
//
//
//          //订单信息
//          $item_shop = array(
//              'shop_order_id' => object(parent::TABLE_SHOP_ORDER)->get_unique_id(),
//              'user_id'                   => $user_id,
//              'shop_id'                   => $shop['shop_id'],
//              'shop_order_money'          => 0,//订单价格 = 商品价格 - 折扣 + 运费
//              'shop_order_goods_price'    => 0,//商品价格
//              'shop_order_discount_money' => 0,//折扣
//              'shop_order_shipping_price' => 0,//运费
//              'shop_order_pay_parent'     => 0,
//              'shop_order_pay_money_method'     => $input['pay_method'],
//              'shop_order_pay_state'      => 0,
//              'shop_order_state'          => 1,
//              'shop_order_insert_time'    => time(),
//              'shop_order_update_time'    => time(),
//          );
//          $item_shop = array_merge($item_shop, $user_address);
//
//          //——循环店铺中的商品——
//          foreach ($shop['shop_goods'] as $goods) {
//              //检测输入
//              object(parent::ERROR)->check($goods, 'goods_sku_id', parent::TABLE_SHOP_GOODS_SKU, array('args', 'exists_id'), 'shop_goods_sku_id');
//              object(parent::ERROR)->check($goods, 'number', parent::TABLE_SHOP_CART, array('args'), 'shop_cart_number');
//
//              //查询商品信息
//              $data_goods = object(parent::TABLE_SHOP_GOODS_SKU)->find_join_goods_spu($goods['goods_sku_id']);
//              if (empty($data_goods))
//                  throw new error('商品规格ID：'.$goods['goods_sku_id'].'有误，商品已失效');
//
//              //是否超出库存
//              if ($goods['number'] > $data_goods['sku_stock'])
//                  throw new error('商品：'.$data_goods['goods_name'].'库存不足');
//
//              // 查询商品属性
//              $cache_goods_data = object(parent::TABLE_SHOP_GOODS)->find($data_goods['goods_id']);
//              
//              // 查询商品ID对应的goods_index
//              $shop_goods_index = $cache_goods_data['shop_goods_index'];
//              
//              // 查询商品ID对应的goods_property
//              $shop_goods_property = $cache_goods_data['shop_goods_property'];
//
//              //是否为限时购商品
//              $data_when = object(parent::TABLE_SHOP_GOODS_WHEN)->find($data_goods['goods_id']);
//              if ($data_when) {
//                  //检测状态
//                  $state = object(parent::TABLE_SHOP_GOODS_WHEN)->check_state($data_when);
//                  if ($state === false)
//                      throw new error('商品：'.$data_goods['goods_name'].'，限时购已结束');
//              }
//
//              //——查询商品图片——
//              if (empty($data_goods['image_id'])) {
//                  $data_goods['image_id'] = object(parent::TABLE_SHOP_GOODS_IMAGE)->get_main_img_id($data_goods['goods_id']);
//              }
//
//
//
//              //json信息
//              $json = array();
//              $json['shop_goods_sku'] = object(parent::TABLE_SHOP_GOODS_SKU)->find($goods['goods_sku_id']);
//              $json['shop_goods_spu'] = object(parent::TABLE_SHOP_GOODS_SPU)->select_by_shopordergoods($json['shop_goods_sku']['shop_goods_spu_id']);
//              $json['stock_mode'] = $data_goods['stock_mode'];
//
//              //订单商品信息
//              $item_goods = array(
//                  'shop_order_goods_id' => object(parent::TABLE_SHOP_ORDER_GOODS)->get_unique_id(),
//                  'shop_order_id'             => $item_shop['shop_order_id'],
//                  'shop_goods_id'             => $data_goods['goods_id'],
//                  'shop_goods_sn'             => $data_goods['goods_sn'],
//                  'shop_goods_sku_id'         => $data_goods['sku_id'],
//                  'shop_order_goods_name'     => $data_goods['goods_name'],
//                  'shop_order_goods_image_id' => $data_goods['image_id'],
//                  'shop_order_goods_price'    => $data_goods['sku_price'],
//                  'shop_order_goods_number'   => $goods['number'],
//                  'shop_order_goods_json'     => cmd(array($json), 'json encode'),
//                  'shop_order_goods_time'     => time(),
//                  'shop_goods_property'       => $shop_goods_property,    //交易商品类型 0普通商品，1是积分商品
//                  'shop_goods_index'          => $shop_goods_index,       //1礼包商品
//              );
//
//              // 是否有推荐人
//              if (isset($input['recommend_user_id'])) {
//                  $item_goods['shop_order_goods_invite_user_id'] = $input['recommend_user_id'];
//
//                  // 查询配置
//                  $recommend_config = object(parent::TABLE_SHOP_ORDER)->distribution_reward_rule();
//
//                  $royalty_state = (int)$recommend_config['royalty_state'];
//
//                  // 判断是否开启推荐奖金功能 1 => 开启  0 => 关闭
//                  if ($royalty_state === 1) {
//                      
//                      $royalty_random = (int)$recommend_config['random'];
//
//                      // 判断是否在设定的随机范围内，随机发放奖金  1 => 随机发放  0 => 按设定值发放奖金，如未设定，则对应身份无奖金，即为0
//                      if ($royalty_random === 0) {
//
//                          // 查询推荐人的身份ID
//                          $recommend_user_amdin_id = object(parent::TABLE_ADMIN_USER)->find($input['recommend_user_id'])['admin_id'];
//
//                          // 循环判断推荐人的身份id，并计算金额
//                          switch ($recommend_user_amdin_id) {
//                              case $recommend_config['area_agent']['admin_id']:
//                                  if (isset($recommend_config['area_agent']['other_equity']['royalty'])) {
//                                      $award_money = (int)$recommend_config['area_agent']['other_equity']['royalty'] / 100;
//                                      $item_goods['shop_order_goods_invite_money'] = $data_goods['sku_price'] * $award_money;
//                                  } else {
//                                      $item_goods['shop_order_goods_invite_money'] = 0;
//                                  }
//                                  break;
//                              case $recommend_config['chief_inspector']['admin_id']:
//                                  if (isset($recommend_config['chief_inspector']['other_equity']['royalty'])) {
//                                      $award_money = (int)$recommend_config['chief_inspector']['other_equity']['royalty'] / 100;
//                                      $item_goods['shop_order_goods_invite_money'] = $data_goods['sku_price'] * $award_money;
//                                  } else {
//                                      $item_goods['shop_order_goods_invite_money'] = 0;
//                                  }
//                                  break;
//                              case $recommend_config['member']['admin_id']:
//                                  if (isset($recommend_config['member']['other_equity']['royalty'])) {
//                                      $award_money = (int)$recommend_config['member']['other_equity']['royalty'] / 100;
//                                      $item_goods['shop_order_goods_invite_money'] = $data_goods['sku_price'] * $award_money;
//                                  } else {
//                                      $item_goods['shop_order_goods_invite_money'] = 0;
//                                  }
//                                  break;
//                              case $recommend_config['shop_manager']['admin_id']:
//                                  if (isset($recommend_config['shop_manager']['other_equity']['royalty'])) {
//                                      $award_money = (int)$recommend_config['shop_manager']['other_equity']['royalty'] / 100;
//                                      $item_goods['shop_order_goods_invite_money'] = $data_goods['sku_price'] * $award_money;
//                                  } else {
//                                      $item_goods['shop_order_goods_invite_money'] = 0;
//                                  }
//                                  break;
//                          }
//                      } else {
//                          // 取随机奖金的最大、最小值
//                          $max_money = (int)$recommend_config['max_royalty_random'];
//                          $min_money = (int)$recommend_config['min_royalty_random'];
//                          $award_money = mt_rand($min_money,$max_money);
//                          $item_goods['shop_order_goods_invite_money'] = $data_goods['sku_price'] * $award_money;
//                      }
//                  } else {
//                      // 未开启推荐奖金功能
//
//                  }
//              }
//
//              $shop_order_goods[] = $item_goods;
//              $item_shop['shop_order_goods_price'] += $item_goods['shop_order_goods_price'] * $item_goods['shop_order_goods_number'];
//          }
//
//          $item_shop['shop_order_money'] = $item_shop['shop_order_goods_price'] - $item_shop['shop_order_discount_money'] + $item_shop['shop_order_shipping_price'];
//          $shop_order[] = $item_shop;
//      }
//
//      //计算支付金额
//      $money_pay = 0;
//      foreach ($shop_order as $val) {
//          $money_pay += $val['shop_order_money'];
//      }
//
//      //是否合并支付
//      if (count($shop_order) > 1) {
//          //总订单
//          $shop_order_parent = array(
//              'shop_order_id' => object(parent::TABLE_SHOP_ORDER)->get_unique_id(),
//              'user_id' => $user_id,
//              'shop_order_money' => $money_pay,
//              'shop_order_pay_parent' => 1,
//              'shop_order_pay_money_method' => $input['pay_method'],
//              'shop_order_pay_state' => 0,
//              'shop_order_state' => 1,
//              'shop_order_insert_time' => time(),
//              'shop_order_update_time' => time(),
//          );
//
//          //循环店铺订单
//          foreach ($shop_order as &$val) {
//              $val['shop_order_parent_id'] = $shop_order_parent['shop_order_id'];
//              $val['shop_order_pay_parent'] = 1;
//          }
//
//          array_unshift($shop_order, $shop_order_parent);
//      }
//
//      //资金订单信息
//      $order = array(
//          'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
//          'order_type' => parent::TRANSACTION_TYPE_SHOP_ORDER,
//          'order_comment' => '商城购物',
//          'order_action_user_id' => $user_id,
//          'order_plus_method' => '',
//          'order_plus_account_id' => '',
//          'order_plus_value' => $money_pay,
//          'order_minus_method' => $input['pay_method'],
//          'order_minus_account_id' => $user_id,
//          'order_minus_value' => $money_pay,
//          'order_state' => 1,
//          'order_pay_state' => 0,
//          'order_sign' => $shop_order[0]['shop_order_id'],
//          'order_json' => array(),
//          'order_insert_time' => time(),
//      );
//
//      foreach ($shop_order as &$val) {
//          $val['shop_order_pay_money_order_id'] = $order['order_id'];
//      }
//
//      //输出数据
//      $output = array();
//
//      //判断支付方式
//      switch ($input['pay_method']) {
//          case parent::PAY_METHOD_WEIXINPAY://微信
//          case parent::PAY_METHOD_ALIPAY://支付宝
//              $subject = (count($shop_order) > 1) ? '合并支付' : $shop_order_goods[0]['shop_order_goods_name'];
//              $output = object(parent::REQUEST_APPLICATION)->get_pay_config($input, $order['order_json'], array(
//                  'money_fen' => $money_pay,
//                  'subject' => $subject,
//                  'body' => $subject,
//                  'order_id' => $order['order_id']
//              ));
//              break;
//          case parent::PAY_METHOD_USER_MONEY://用户钱包
//              $output['order_id'] = $order['order_id'];
//              $output['shop_order_id'] = $shop_order[0]['shop_order_id'];
//              $output['order_money'] = $money_pay;
//              break;
//      }
//
//      $order['order_json'] = cmd(array($order['order_json']), 'json encode');
//
//      //创建订单
//      if (object(parent::TABLE_SHOP_ORDER)->create_order($order, $shop_order, $shop_order_goods)) {
//          //删除购物车相关商品
//          $shop_goods_sku_ids = array();
//          foreach ($shop_order_goods as $val) {
//              $shop_goods_sku_ids[] = $val['shop_goods_sku_id'];
//          }
//          object(parent::TABLE_SHOP_CART)->clear_cart($user_id, $shop_goods_sku_ids);
//
//          return $output;
//      } else {
//          throw new error('操作失败');
//      }
//  }
//




    /**
     * 支付
     *
     * api: SHOPORDERSELFPAY
     * req: {
     *  id              [int] [必填] [订单ID]
     *  pay_method      [str] [必填] [支付方式]
     *  pay_password    [int] [可选] [支付密码。支付方式为用户钱包时，必填]
     * }
     * 
     * @return string 订单ID
     */
//  public function api_self_pay_test($input = array())
//  {
//      // 检测登录
//      object(parent::REQUEST_USER)->check();
//      $user_id = $_SESSION['user_id'];
//
//      // 检测输入
//      object(parent::ERROR)->check($input, 'id', parent::TABLE_SHOP_ORDER, array('args'), 'shop_order_id');
//      object(parent::ERROR)->check($input, 'pay_method', parent::TABLE_SHOP_ORDER, array('args'));
//
//      // 查询商城订单信息
//      $shop_order = object(parent::TABLE_SHOP_ORDER)->find($input['id']);
//      if (empty($shop_order) || $shop_order['user_id'] != $user_id)
//          throw new error('订单数据不存在');
//
//      if ($shop_order['shop_order_state'] == 0)
//          throw new error('订单已取消');
//
//      if ($shop_order['shop_order_trash'] == 1)
//          throw new error('订单已回收');
//
//      if ($shop_order['shop_order_delete_state'] == 1)
//          throw new error('订单已删除');
//
//      if ($shop_order['shop_order_pay_state'] == 1)
//          throw new error('订单已支付');
//
//      // 查询配置信息
//      $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('shop_order'), true);
//
//      // ——判断订单超时时间——
//      if (!empty($config['order_timeout'])) {
//          if (time() - $shop_order['shop_order_insert_time'] > $config['order_timeout']) {
//              $update_where = array(array('shop_order_id=[+]', $input['id']));
//              $update_data = array(
//                  'shop_order_state' => 0,
//                  'shop_order_close_time' => time(),
//                  'shop_order_update_time' => time(),
//              );
//              object(parent::TABLE_SHOP_ORDER)->update($update_where, $update_data);
//              throw new error('订单已失效，请重新下单');
//          }
//      }
//
//      // 查询商品信息
//      $shop_order_goods = object(parent::TABLE_SHOP_ORDER_GOODS)->select_by_pay($shop_order['shop_order_id']);
//
//      // 更新限时购商品状态
//      object(parent::TABLE_SHOP_GOODS_WHEN)->update_state_clear();
//
//      foreach ($shop_order_goods as $val) {
//          if (is_null($val['shop_goods_id']) || $val['shop_goods_trash'] == 1 || $val['shop_goods_state'] != 1)
//              throw new error('商品：'.$val['shop_order_goods_name'].'，已失效');
//
//          if (!is_null($val['shop_goods_when_state']) && $val['shop_goods_when_state'] != 1)
//              throw new error('商品：'.$val['shop_order_goods_name'].'，限时购已结束');
//      }
//
//      // 订单
//      $order = array(
//          'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
//          'order_type' => parent::TRANSACTION_TYPE_SHOP_ORDER,
//          'order_comment' => '商城购物',
//          'order_action_user_id' => $user_id,
//          'order_plus_method' => '',
//          'order_plus_account_id' => $shop_order['shop_id'],
//          'order_plus_value' => $shop_order['shop_order_money'],
//          'order_minus_method' => $input['pay_method'],
//          'order_minus_account_id' => $user_id,
//          'order_minus_value' => $shop_order['shop_order_money'],
//          'order_state' => 1,
//          'order_pay_state' => 0,
//          'order_sign' => $shop_order['shop_order_id'],
//          'order_json' => array(),
//          'order_insert_time' => time(),
//      );
//
//      // 判断支付方式
//      switch ($input['pay_method']) {
//          case parent::PAY_METHOD_WEIXINPAY://微信
//          case parent::PAY_METHOD_ALIPAY://支付宝
//              // 获取支付参数
//              $subject = $shop_order_goods[0]['shop_order_goods_name'];
//              $output = object(parent::REQUEST_APPLICATION)->get_pay_config($input, $order['order_json'], array(
//                  'money_fen' => $shop_order['shop_order_money'],
//                  'subject' => $subject,
//                  'body' => $subject,
//                  'order_id' => $order['order_id']
//              ));
//
//              // 插入订单数据
//              if (object(parent::TABLE_ORDER)->insert($order)) {
//                  $output['order_id'] = $order['order_id'];
//                  return $output;
//              } else {
//                  throw new error ('操作失败');
//              }
//
//              break;
//          case parent::PAY_METHOD_USER_MONEY://用户钱包
//              // 检测输入
//              object(parent::ERROR)->check($input, 'pay_password', parent::TABLE_USER, array('args'));
//
//              // 检测支付密码
//              $res = object(parent::TABLE_USER)->check_pay_password($input['pay_password']);
//              if ($res !== true)
//                  throw new error($res);
//
//              // 检测余额
//              $user_money = object(parent::TABLE_USER_MONEY)->find_now_data($user_id);
//              if (empty($user_money['user_money_value']) || $user_money['user_money_value'] < $shop_order['shop_order_money'])
//                  throw new error('余额不足');
//
//              // 插入订单数据
//              return object(parent::TABLE_SHOP_ORDER)->pay_by_user_money($shop_order['shop_order_pay_money_order_id'], $user_money);
//
//              break;
//      }
//  }





    /**
     * 取消订单
     * 
     * api: SHOPORDERSELFCANCEL
     * req: {
     *  id [int] [必填] [订单ID]
     * }
     * 
     * @return string 订单ID
     */
    public function api_self_cancel($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //检测输入
        object(parent::ERROR)->check($input, 'id', parent::TABLE_SHOP_ORDER, array('args'), 'shop_order_id');

        //查询原始数据
        $data = object(parent::TABLE_SHOP_ORDER)->find($input['id']);
        if (empty($data))
            throw new error('订单数据不存在');
        if ($data['shop_order_pay_state'] == 1)
            throw new error('订单已经支付，不能取消');
        if ($data['shop_order_pay_state'] == 2)
            throw new error('订单正在支付中，不能取消');
        if ($data['shop_order_state'] == 0)
            // return true;
            throw new error('订单当前已经是取消状态');

        $update_where = array(
            array('user_id=[+]', $_SESSION['user_id']),
            array('[and] shop_order_id=[+]', $input['id']),
        );

        $update_data = array(
            'shop_order_state' => 0,
            'shop_order_close_time' => time(),
        );

        //更新订单状态
        if (object(parent::TABLE_SHOP_ORDER)->update($update_where, $update_data)) {
            //是否下单减库存
            $data_shopordergoods = object(parent::TABLE_SHOP_ORDER_GOODS)->select(array(
                'where' => array('shop_order_id = [+]', $input['id'])
            ));

            foreach ($data_shopordergoods as $val) {
                $json = cmd(array($val['shop_order_goods_json']), 'json decode');
                if( !isset($json['shop_goods']['shop_goods_stock_mode']) ){
                    continue;
                }
                if ($json['shop_goods']['shop_goods_stock_mode'] == 1) {
                    $bool = object(parent::TABLE_SHOP_GOODS_SKU)->increase_stock($val['shop_goods_sku_id'], $val['shop_order_goods_number']);
                }
            }

            return $input['id'];
        } else {
            throw new error('操作失败');
        }
    }


    /**
     * 删除订单
     * 
     * api: SHOPORDERSELFREMOVE
     * req: {
     *  id [int] [必填] [订单ID]
     * }
     * 
     * @return string 订单ID
     */
    public function api_self_remove($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //检测输入
        object(parent::ERROR)->check($input, 'id', parent::TABLE_SHOP_ORDER, array('args'), 'shop_order_id');

        //查询原始数据
        $data = object(parent::TABLE_SHOP_ORDER)->find($input['id']);
        if (empty($data))
            throw new error('订单数据不存在');

        // 判断订单是否已取消或已完成
        if ($data['shop_order_pay_state'] == 2 && $data['shop_order_state']==1)
            throw new error('订单正在支付中，不能删除');
        if ($data['shop_order_shipping_state'] == 2 && $data['shop_order_state']==1)
            throw new error('订单正在运送中，不能删除');
        if ($data['shop_order_shipping_state'] == 0  && $data['shop_order_pay_state'] == 1 && $data['shop_order_state']==1)
            throw new error('订单正在发货中，不能删除');
            

        if ($data['shop_order_delete_state'] == 1)
            throw new error('订单已经删除');

        $update_where = array(
            array('user_id=[+]', $_SESSION['user_id']),
            array('[and] shop_order_id=[+]', $input['id']),
        );

        $update_data = array(
            'shop_order_delete_state' => 1,
            'shop_order_delete_time' => time(),
        );

        //更新订单状态
        if (object(parent::TABLE_SHOP_ORDER)->update($update_where, $update_data)) {

            // 如果订单状态是未取消，并且未支付
            if ($data['shop_order_pay_state'] == 0 && $data['shop_order_state'] == 1) {

                //是否下单减库存
                $data_shopordergoods = object(parent::TABLE_SHOP_ORDER_GOODS)->select(array(
                    'where' => array('shop_order_id = [+]', $input['id'])
                ));

                foreach ($data_shopordergoods as $val) {
                    $json = cmd(array($val['shop_order_goods_json']), 'json decode');
                    if ($json['shop_goods_stock_mode'] == 1) {
                        $bool = object(parent::TABLE_SHOP_GOODS_SKU)->increase_stock($val['shop_goods_sku_id'], $val['shop_order_goods_number']);
                    }
                }
            }

            return $input['id'];
        } else {
            throw new error('操作失败');
        }
    }


    /**
     * 确认收货
     *
     * api: SHOPORDERSELFRECEIVE
     * req: {
     *  id      [str] [必填] [订单ID]
     * }
     * 
     * @return string 订单ID
     */
    public function api_self_receive($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //检测输入
        object(parent::ERROR)->check($input, 'id', parent::TABLE_SHOP_ORDER, array('args'), 'shop_order_id');

        //查询原始数据
        $data = object(parent::TABLE_SHOP_ORDER)->find($input['id']);
        if (empty($data))
            throw new error('订单数据不存在');

        if ($data['shop_order_pay_state'] != 1)
            throw new error('订单尚未支付完成，不能收货');

        if ($data['shop_order_state'] == 0)
            throw new error('订单已取消，不能收货');

        $update_where = array(
            array('user_id=[+]', $_SESSION['user_id']),
            array('[and] shop_order_id=[+]', $input['id']),
        );

        $update_data = array(
            'shop_order_shipping_state' => 1,
            'shop_order_shipping_take_time' => time(),
        );

        // 推荐和五级分销奖励发放
        // object(parent::TABLE_USER_RECOMMEND)->recommend_money($input['id']);

        //更新订单状态
        if (object(parent::TABLE_SHOP_ORDER)->update($update_where, $update_data)) {
            //触发事件
            object(parent::REQUEST_SHOP)->event_order_shipping_take($input['id']);
            return $input['id'];
        } else {
            throw new error('操作失败');
        }
    }


    /**
     * 评论
     *
     * api: SHOPORDERSELFCOMMENT
     * req: {
     *  id      [str] [必填] [订单ID]
     *  comment [str] [必填] [评论]
     * }
     * 
     * @return string
     */
    public function api_self_comment($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //检测输入
        object(parent::ERROR)->check($input, 'id', parent::TABLE_SHOP_ORDER, array('args'), 'shop_order_id');
        object(parent::ERROR)->check($input, 'comment', parent::TABLE_USER_COMMENT, array('args'), 'user_comment_value');

        //查询商城订单信息
        $data_shoporder = object(parent::TABLE_SHOP_ORDER)->find($input['id']);
        if (empty($data_shoporder))
            throw new error('订单不存在');

        if ($data_shoporder['shop_order_comment_state'] == 1)
            throw new error('您已评价过了');

        if ($data_shoporder['shop_order_shipping_state'] != 1)
            throw new error('订单尚未收货，不能评价');

        //更新数据
        $update_where = array(
            array('user_id=[+]', $_SESSION['user_id']),
            array('[and] shop_order_id=[+]', $input['id']),
        );
        $update_data = array(
            'shop_order_comment_state' => 1,
            'shop_order_update_time' => time(),
        );

        //查询评论配置
        $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('shop_order_user_comment'), true);

        //是否开启评论
        if (empty($config['state']) || $config['state'] != 1)
            throw new error('订单评价已关闭');

        //是否审核评论
        $state = (isset($config['check']) && $config['check'] == 1) ? 2 : 1;

        //查询订单商品
        $data_shopordergoods = object(parent::TABLE_SHOP_ORDER_GOODS)->select_by_pay($data_shoporder['shop_order_id']);
        $insert_data = array();

        foreach ($data_shopordergoods as $val) {
            if ($val['shop_goods_id']) {
                $insert_data[] = array(
                    'user_comment_id' => object(parent::TABLE_USER_COMMENT)->get_unique_id(),
                    'user_id' => $_SESSION['user_id'],
                    'user_comment_module' => parent::MODULE_SHOP_GOODS,
                    'user_comment_key' => $val['shop_goods_id'],
                    'user_comment_ip' => HTTP_IP,
                    'user_comment_value' => $input['comment'],
                    'user_comment_state' => $state,
                    'user_comment_insert_time' => time(),
                    'user_comment_update_time' => time(),
                );
            }
        }
 
        //更新订单状态
        if (object(parent::TABLE_SHOP_ORDER)->update($update_where, $update_data)) {
            //插入评论数据
            if (!empty($insert_data))
                object(parent::TABLE_USER_COMMENT)->insert_batch($insert_data);

            return $input['id'];
        } else {
            throw new error('操作失败');
        }
    }


    //===========================================
    // 查询数据
    //===========================================


    /**
     * 原来的list，现在没有用了
     * 
     * 查询当前用户的订单列表数据
     *
     * api: SHOPORDERSELFLIST
     * req: {
     * 
     * }
     * 
     * @return array
     */
//  public function api_self_list_xxx($input = array())
//  {
//      //检测登录
//      object(parent::REQUEST_USER)->check();
//
//      //查询配置
//      $config = array(
//          'orderby' => array(),
//          'where'   => array(),
//          'limit'   => object(parent::REQUEST)->limit($input, parent::REQUEST_USER),
//      );
//
//      //字段
//      $config['select'] = array(
//          's.shop_name',
//          'so.shop_id',
//          'so.shop_order_id',
//          'so.shop_order_state',
//          'so.shop_order_buyer_note AS buyer_note',
//          'so.shop_order_seller_note AS seller_note',
//          'so.shop_order_money AS money',
//          'so.shop_order_pay_state AS pay_state',
//          'so.shop_order_shipping_state AS shipping_state',
//          'so.shop_order_comment_state AS comment_state',
//          'so.shop_order_insert_time AS insert_time',
//          'sog.shop_goods_id',
//          'sog.shop_goods_sn',
//          'sog.shop_order_goods_name',
//          'sog.shop_order_goods_image_id',
//          'sog.shop_order_goods_price',
//          'sog.shop_order_goods_number',
//          'sog.shop_order_goods_json',
//      );
//
//      //条件
//      $config['where'][] = array('so.user_id=[+]', $_SESSION['user_id']);
//      $config['where'][] = array('[and] so.shop_order_delete_state=0');
//      $config['where'][] = array('[and] so.shop_order_trash=0');
//
//      //排序
//      $config['orderby'][] = array('shop_order_insert_time', true);
//      $config['orderby'][] = array('shop_order_id', true);
//
//      //查询订单数据
//      $data = object(parent::TABLE_SHOP_ORDER)->select_page_by_user($config);
//
//      //格式化数据
//      $order = array();
//      foreach ($data['data'] as $val) {
//          $key = $val['shop_order_id'];
//          if (!array_key_exists($key, $order)) {
//              $order[$key] = array(
//                  'shop_order_id' => $val['shop_order_id'],
//                  'shop_order_state' => $val['shop_order_state'],
//                  'shop_id' => $val['shop_id'],
//                  'shop_name' => $val['shop_name'] ?: '自营',
//                  'buyer_note' => $val['buyer_note'],
//                  'seller_note' => $val['seller_note'],
//                  'money' => $val['money'],
//                  'pay_state' => $val['pay_state'],
//                  'shipping_state' => $val['shipping_state'],
//                  'comment_state' => $val['comment_state'],
//                  'insert_time' => date('Y-m-d', $val['insert_time']),
//                  'shop_goods' => array(),
//              );
//          }
//
//          $order[$key]['shop_goods'][] = array(
//              'id' => $val['shop_goods_id'],
//              'sn' => $val['shop_goods_sn'],
//              'name' => $val['shop_order_goods_name'],
//              'image_id' => $val['shop_order_goods_image_id'],
//              'price' => $val['shop_order_goods_price'],
//              'number' => $val['shop_order_goods_number'],
//              'json' => cmd(array($val['shop_order_goods_json']), 'json decode'),
//          );
//      }
//
//      $data['data'] = array_values($order);
//
//      return $data;
//  }






    /**
     * SHOPORDERSELFLIST
     * ------Mr.Zhao------2019.07.01------
     * 
     * 订单状态，当前后端过滤没有用到，前端自己过滤（接口不受影响）
     * 
     * @param   array   ['state':'0或不填此字段 全部;  1 代付款;  2 待发货;  3 待收货;  4 待评价']
     * @return  array
     */
    public function api_self_list($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //查询配置
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($input, parent::REQUEST_USER),
        );

        //字段
        $config['select'] = array(
            's.shop_name',
            'so.shop_id',
            'so.shop_order_id',
            'so.shop_order_state',
            'so.shop_order_buyer_note AS buyer_note',
            'so.shop_order_seller_note AS seller_note',
            'so.shop_order_money AS money',
            'so.shop_order_credit AS credit',
            'so.shop_order_pay_state AS pay_state',
            'so.shop_order_shipping_state AS shipping_state',
            'so.shop_order_comment_state AS comment_state',
            'so.shop_order_insert_time AS insert_time',
            'so.shop_order_pay_money_state AS pay_money_state',
            'so.shop_order_pay_credit_state AS pay_credit_state',
            'sog.shop_goods_id',
            'sog.shop_goods_sn',
            'sog.shop_order_goods_name',
            'sog.shop_order_goods_image_id',
            'sog.shop_goods_property',
            'sog.shop_order_goods_price',
            'sog.shop_order_goods_number',
            'sog.shop_order_goods_json',
        );

        
        // 0或不填 全部，1 待付款，2 待发货，3 待收货，4 待评价
        $state = array(
            0=>array(),
            1=>array('[and] so.shop_order_pay_state in (0,2)'),
            2=>array('[and] so.shop_order_shipping_state = 0 and so.shop_order_pay_state = 1'),
            3=>array('[and] so.shop_order_shipping_state = 2 and so.shop_order_pay_state = 1'),
            4=>array('[and] so.shop_order_shipping_state = 1 and so.shop_order_pay_state = 1 and so.shop_order_comment_state = 0'),
        );


        //条件
        $config['where'][] = array('so.user_id=[+]', $_SESSION['user_id']);
        $config['where'][] = array('[and] so.shop_order_delete_state=0');
        $config['where'][] = array('[and] so.shop_order_trash=0');

        // 判断查询订单的状态
        if(isset($input['state']) && is_numeric($input['state'])){
            $config['where'][] = $state[$input['state']];
        }

        //排序
        $config['orderby'][] = array('shop_order_insert_time', true);
        $config['orderby'][] = array('shop_order_id', true);


        // TODO:检查订单是否已失效


        //查询订单数据
        $data = object(parent::TABLE_SHOP_ORDER)->select_page_by_user($config);

        return $data;
    }







    //===========================================
    // 私有方法
    //===========================================


    /**
     * 查询收货地址
     * 
     * @param  string $user_address_id 收货地址ID
     * @return array
     */
    private function _get_user_address($user_address_id = '')
    {
        //查询收货地址信息
        $address = object(parent::TABLE_USER_ADDRESS)->find($user_address_id);
        if (empty($address))
            throw new error('收货地址ID有误，数据不存在');

        //检测收货地址
        object(parent::ERROR)->check($address, 'user_address_consignee', parent::TABLE_USER_ADDRESS, array('args'));
        object(parent::ERROR)->check($address, 'user_address_phone', parent::TABLE_USER_ADDRESS, array('args'));
        object(parent::ERROR)->check($address, 'user_address_province', parent::TABLE_USER_ADDRESS, array('args'));
        object(parent::ERROR)->check($address, 'user_address_city', parent::TABLE_USER_ADDRESS, array('args'));
        object(parent::ERROR)->check($address, 'user_address_district', parent::TABLE_USER_ADDRESS, array('args'));
        object(parent::ERROR)->check($address, 'user_address_details', parent::TABLE_USER_ADDRESS, array('args'));

        //白名单
        $whitelist = array(
            'user_address_consignee',
            'user_address_phone',
            'user_address_province',
            'user_address_city',
            'user_address_district',
            'user_address_details',
        );

        return cmd(array($address, $whitelist), 'arr whitelist');
    }


    /**
     * 易淘商城获取店主、总监、创始人优惠金额
     * api: SHOPORDERSELFDISCOUNTMONEY
     * {"class":"shop/order","method":"api_self_discout_money"}
     */
    public function api_self_discout_money($data=array()){

        //检测登录
        object(parent::REQUEST_USER)->check();

        $discount_money = 0;
        $where = array(
            array('user_id=[+]',(string)$_SESSION['user_id']),
            array('shop_order_state=1'),
            array('shop_order_pay_state=1'),
            array('shop_order_trash=0'),
            array('shop_order_admin_difference>0')
        );
        $discount_money = object(parent::TABLE_SHOP_ORDER)->get_discount($where,'shop_order_admin_difference');
        return ['discount_money'=>$discount_money];
    }

    /**
     * 易淘商城获取店主、总监、创始人、优惠金额列表
     * api: SHOPORDERSELFDISCOUNTMONEYLIST
     * {"class":"shop/order","method":"api_self_discount_money_list"}
     */
    public function api_self_discount_money_list($data=array()){

        //检测登录
        object(parent::REQUEST_USER)->check();

        //查询配置
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($data, parent::REQUEST_USER),
        );

        //排序
        $config['orderby'][] = array('insert_time', true);

        //条件
        $config['where'][] = array('user_id=[+]',(string)$_SESSION['user_id']);
        $config['where'][] = array('shop_order_state=1');
        $config['where'][] = array('shop_order_pay_state=1');
        $config['where'][] = array('shop_order_trash=0');
        $config['where'][] = array('shop_order_admin_difference>0');

        //字段
        $config['select'] = array(
            'so.shop_order_id AS order_id',
            'so.user_id AS uid',
            'so.shop_order_json AS info',
            'so.shop_order_money AS money',
            'so.shop_order_goods_price AS goods_price',
            'so.shop_order_goods_money AS goods_money',
            'so.shop_order_discount_money AS discount_money',
            'so.shop_order_pay_money AS pay_money',
            'so.shop_order_insert_time AS insert_time',
            "FROM_UNIXTIME(so.shop_order_insert_time,'%Y-%m-%d %T') AS create_time",
            'so.shop_order_admin_difference AS discounts',
            'sog.shop_order_id AS id',
            'sog.shop_order_goods_name AS goods_name'
        );

        $config['join'] = array(
            'table' => 'shop_order_goods sog',
            'type' => 'left',
            'on' => 'sog.shop_order_id = so.shop_order_id'
        );
        // return $config;

        $res = object(parent::TABLE_SHOP_ORDER)->select_discount_page($config);
        return $res;

    }


    /**
     * 获取最新订单数据
     * api: SHOPORDERGETNUMLIST
     * {"class":"shop/order","method":"api_get_num_list"}
     */
    public function api_get_num_list($input=array()){

        $num = 5;
        if(!empty($input['num']) && $input['num']>0){
            $num = (int)$input['num'];
        }

        $where = array(
            array('shop_order_pay_state=1'),
            array('shop_order_trash=0')
        );
        $order_by = array(
            array('insert_time', true)
        );
        $select = array(
            'shop_order_id AS id',
            'user_id AS uid',
            'shop_order_state AS state',
            'shop_order_json AS info',
            'shop_order_insert_time AS insert_time'
        );
        $config = array(
            'where'=>$where,
            'orderby'=>$order_by,
            'limit'=>$num,
            'select'=>$select
        );

        $data = object(parent::TABLE_SHOP_ORDER)->select_num($config);
        $arr = array();

        $qiniu_domain = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('qiniu_domain'),true);
        if(!empty($data)){
            foreach($data as $k=>$v){
                $user_info = object(parent::TABLE_USER)->find($v['uid']);
                $nick_name = isset($user_info['user_nickname']) && $user_info['user_nickname']?$user_info['user_nickname']:'匿名用户';
                $v['head_url'] = '';
                if(!empty($user_info['user_logo_image_id'])){
                    $v['head_url'] = $qiniu_domain.$user_info['user_logo_image_id'];
                }
                $v['message'] = $nick_name.'有新的订单';
                $arr[] = $v; 
            }
        }
        return $arr;
    }



    /**
     * 获取订单详情,无过滤，要使用请自建版本
     * api: SHOPORDERGETORDERDETAILS
     * {"class":"shop/order","method":"api_self_get_order_details"}
     */
    public function api_self_get_order_details($data){
        //检测登录
        object(parent::REQUEST_USER)->check();
		object(parent::ERROR)->check($data, 'shop_order_id', parent::TABLE_SHOP_ORDER, array('args'));
        //查询订单数据
        $data = object(parent::TABLE_SHOP_ORDER)->find_details($data['shop_order_id']);
        //白名单
        $whitelist = array(
            'shop_order_goods',
            'shop_order_discount_money',
            'shop_order_id',
            'shop_order_insert_time',
            'shop_order_goods_money',
            'shop_order_pay_money',
            'shop_order_pay_money_method',
            'shop_order_shipping_send_time',
            'shop_order_receive_address',
            'shop_order_pay_time',
            'shop_order_state',
            'shop_order_pay_state',
            'shop_order_write_off_state',
            'shop_order_agent_region_id',
            'shop_order_shipping_state',
            'shop_order_money',
            'shop_order_credit'
        );
        $data = cmd(array($data, $whitelist), 'arr whitelist');
        if(!empty($data['shop_order_agent_region_id'])){
            $data['shop_order_agent_region'] = object(parent::TABLE_AGENT_REGION)->find($data['shop_order_agent_region_id']);
        } else {
            $data['shop_order_agent_region'] = '';
        }
        if ($data['shop_order_pay_money_method']) {
            $methods = object(parent::TABLE_SHOP_ORDER)->get_pay_method();
            $data['shop_order_pay_money_method_name'] = $methods[$data['shop_order_pay_money_method']];
        } else {
            $data['shop_order_pay_money_method_name'] = '';
        }

        $data['shop_order_pay_time'] = date('Y年m月d日 H:i', $data['shop_order_pay_time']);
        $data['shop_order_insert_time'] = date('Y年m月d日 H:i', $data['shop_order_insert_time']);
        $data['shop_order_shipping_send_time'] = date('Y年m月d日 H:i', $data['shop_order_shipping_send_time']);

        unset($data['shop_order_agent_region_id']);
        return $data;
    }


    /**
     * （新）获取订单详情
     * api: SHOPORDERSELFDETAILS
     * {"class":"shop/order","method":"api_self_details"}
     * 
     * {"shop_order_id":"业务订单ID"}
     */
    public function api_self_details($data){
        // 检测登录
        object(parent::REQUEST_USER)->check();

        // 检测请求参数
        object(parent::ERROR)->check($data, 'shop_order_id', parent::TABLE_SHOP_ORDER, array('args'));

        // 查询商品详情
        $config = array();
        $config['where'] = array( 
            array('shop_order_id=[+]',$data['shop_order_id']),
            //array('user_id=[+]',$_SESSION['user_id'])
        );
        $config['find'] = array(
            'so.shop_order_id',
            
            'so.shop_order_insert_time as creat_time',
            'so.shop_order_pay_time as pay_time',
            'so.shop_order_shipping_send_time as shipping_time',
            

            'so.shop_order_receive_address as receive_address',                      // *注意不是所有项目都有核销地址
            'so.shop_order_state as order_sate',
            'so.shop_order_pay_state as pay_state',
            'so.shop_order_pay_money_state as money_pay_state',
            'so.shop_order_pay_credit_state as credit_pay_state',
            'so.shop_order_write_off_state as write_off_state',                      // *注意不是所有项目都有核销状态
            'so.shop_order_agent_region_id as agent_region_id',                      // *注意不是所有项目都有取货代理区域地址
            'so.shop_order_shipping_state as shipping_state',
            
            'so.shop_order_pay_money_method as money_pay_method',

            'so.shop_order_goods_money as goods_money',
            'so.shop_order_pay_money as pay_money',
            'so.shop_order_money as order_money',
            'so.shop_order_discount_money as discount_money',
            'so.shop_order_credit as order_credit',
        );

        $result = object(parent::TABLE_SHOP_ORDER)->find_order_where($config);

        // 支付方式（人民币支付方式）
        $pay_method_list = object(parent::TABLE_SHOP_ORDER)->get_pay_method();
        if (isset($result['money_pay_method']) && isset($pay_method_list[$result['money_pay_method']])) {
            $result['money_pay_method'] = $pay_method_list[$data['money_pay_method']];
        }


        return $result;
    }
}