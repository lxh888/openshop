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
class order extends \eapie\source\request\application {
	
	
	/* 订单 */
	
	
	
	/**
	 * 订单的微信支付回调地址
	 * 
	 * APPLICATION ORDER WEIXINPAY NOTIFY URL
	 * {"class":"application/order","method":"api_weixinpay_notify_url"}
	 * 
	 * 
	 * [{
	 * "application_id":"chuanglianzhongyi",
	 * "application_name":"创联众益",
	 * "application_info":"",
	 * "application_json":{"host":"120.55.83.203","user":"clzy","pass":"Ptrg619JctG1moJQ","base":"clzy","prefix":""},
	 * "application_on_off":"1",
	 * "application_state":"1",
	 * "application_warning":"",
	 * "application_sort":"0",
	 * "application_insert_time":"1547434268",
	 * "application_update_time":"1547434268"
	 * },{
	 * "appid":"wx471f4de190c69a8b",
	 * "attach":"附加数据-小程序支付测试",
	 * "bank_type":"CFT",
	 * "cash_fee":"1",
	 * "fee_type":"CNY",
	 * "is_subscribe":"N",
	 * "mch_id":"1514039211",
	 * "nonce_str":"4yMLyCRz2q",
	 * "openid":"ouwyp5aeAlvhPZQSjhj9F78U8mxY",
	 * "out_trade_no":"20150806125346ABTTTTT",
	 * "result_code":"SUCCESS",
	 * "return_code":"SUCCESS",
	 * "sign":"91CD393553347C7917878FD3B6A7ECA9",
	 * "time_end":"20190126171936",
	 * "total_fee":"1",
	 * "trade_type":"JSAPI",
	 * "transaction_id":"4200000267201901261474017548"}]
	 */
	public function api_weixinpay_notify_url(){
		$input_get_array = object(parent::PLUGIN_WEIXIN_PAY_UNIFIEDORDER)->input_get_array();
		//file_put_contents(CACHE_PATH."/text.api_weixinpay_notify_url", cmd(array($input_get_array), "json encode"));
		
		if( empty($input_get_array["out_trade_no"]) || 
		empty($input_get_array["transaction_id"]) || 
		empty($input_get_array["nonce_str"]) ){
			return false;
		}
		
		//验证支付宝签名
		$config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('weixin_pay_access'), true);
		if ( !object(parent::PLUGIN_WEIXIN_PAY_UNIFIEDORDER)->check_sign($input_get_array, $config) ){
			return false;
		}
		
		$bool = object(parent::TABLE_ORDER)->update_notify_trade_success(array(
			"order_id" => $input_get_array["out_trade_no"],
			"order_minus_transaction_id" => $input_get_array["transaction_id"],
			"standby_order_id" => $input_get_array["nonce_str"],
			"pay_method" => parent::PAY_METHOD_WEIXINPAY,
			"pay_notify_data" => $input_get_array
		));
		
		if( empty($bool) ){
			return object(parent::PLUGIN_WEIXIN_PAY_UNIFIEDORDER)->notify_url_error("交易失败");
		}else{
			return object(parent::PLUGIN_WEIXIN_PAY_UNIFIEDORDER)->notify_url_success();
		}
		
	}


	/**
	 * 支付宝支付回调
	 * 
	 * APPLICATIONORDERALIPAYNOTIFYURL
	 * {"class":"application/order","method":"api_alipay_notify_url"}
	 * 
	 * 
	 */
	public function api_alipay_notify_url($input = array()){
		$input_get_array = object(parent::PLUGIN_ALIPAY)->input_get_array();
		if( empty($input_get_array['out_trade_no']) ||
		empty($input_get_array['trade_no']) ||
		empty($input_get_array['passback_params']) ){
			return false;
		}
		
		//验证支付宝签名
		$config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('alipay_access'), true);
		if (!object(parent::PLUGIN_ALIPAY)->check_sign($input_get_array, $config)){
			return false;
		}
		
        //执行成功逻辑
		$bool = object(parent::TABLE_ORDER)->update_notify_trade_success(array(
			'order_id' => $input_get_array['out_trade_no'],
			'order_minus_transaction_id' => $input_get_array['trade_no'],
			'standby_order_id' => $input_get_array['passback_params'],
			'pay_method' => parent::PAY_METHOD_ALIPAY,
			"pay_notify_data" => $input_get_array
		));
		
		//程序执行完后必须打印输出“success”（不包含引号）。如果商户反馈给支付宝的字符不是success这7个字符，支付宝服务器会不断重发通知，直到超过24小时22分钟。一般情况下，25小时以内完成8次通知（通知的间隔频率一般是：4m,10m,10m,1h,2h,6h,15h）
		return $bool ? 'success' : 'fail';
	}

	
	/**
	 * 用户登录下查询自己订单支付状态
	 * 
	 * APPLICATIONORDERSELFPAYSTATE
	 * [{"order_id":"订单ID"}]
	 * 
	 * {"class":"application/order","method":"api_self_pay_state"}
	 * 
	 * 0表示未支付；1表示支付成功。
	 * 
	 * @param	array		$data
	 * @return string	返回订单ID
	 */
	public function api_self_pay_state($data = array()){
		//检查是否已初始化
		object(parent::REQUEST_USER)->check();
		object(parent::ERROR)->check( $data, 'order_id', parent::TABLE_ORDER, array('args') );
		//$order_data = object(parent::TABLE_ORDER)->find( $data["order_id"] );
		$order_data = object(parent::TABLE_ORDER)->find_unbuffered($data["order_id"]);//不带缓存的查询
		if( empty($order_data) ){
			return 0;//订单不存在
		}
		
		if( $order_data["order_action_user_id"] != $_SESSION['user_id'] && 
		$order_data["order_plus_account_id"] != $_SESSION['user_id'] && 
		$order_data["order_minus_account_id"] != $_SESSION['user_id'] ){
			return 0;//订单操作人不存在或者不是该操作人
		}
		
		//已经支付：订单支付状态。0未支付，1已支付
		if( empty($order_data["order_pay_state"]) ){
			return 0;
		} else {
			return 1;
		}
		
	}
	
	/**
	 * 快递100--修改订单物流状态
	 * 
	 * api: APPLICATIONORDERKUAIDI100NOTIFYURL
	 * {"class":"application/order","method":"api_kuaidi100_notify_url"}
	 * @return void
	 */
	public function api_kuaidi100_notify_url($input = array()){
		//验证
		
		
		//	调用修改订单--物流状态
		object(parent::TABLE_EXPRESS_ORDER)->update_notify_shipping_state($input);
	}
	
	
	/**
	 * 用户登录下购买所属商家积分，生成订单
	 * 
	 * APPLICATIONORDERSELFBUYMERCHANTCREDIT
	 * [{"merchant_id":"商家ID","credit_number":"必须|积分数量","pay_method":"支付方式 weixinpay 微信支付、alipay 支付宝支付","weixin_login_code":"微信支付是需要，用于换取用户OpenID","weixin_trade_type":"微信的交易类型，必须是大写，如JSAPI|APP|MWEB|NATIVE"}]
	 * 
	 * {"class":"application/order","method":"api_self_buy_merchant_credit"}
	 * 
	 * @param	array		$data
	 * @return string	返回订单ID
	 */
	public function api_self_buy_merchant_credit($data = array()){

		//检查是否已初始化
		object(parent::REQUEST_USER)->check();
		object(parent::ERROR)->check( $data, 'merchant_id', parent::TABLE_MERCHANT, array('args') );
		object(parent::ERROR)->check( $data, 'credit_number', parent::TABLE_ORDER, array('args') );
		object(parent::ERROR)->check( $data, 'pay_method', parent::TABLE_ORDER, array('args') );
		
		//判断当前用户是否是 该 商家ID 的用户
        if ( !object(parent::TABLE_MERCHANT_USER)->check_exist($_SESSION["user_id"], $data['merchant_id'], true) )
            throw new error('商家不存在');
		
		//获取商家数据
		$merchant_data = object(parent::TABLE_MERCHANT)->find_base_data($data['merchant_id']);
		if( empty($merchant_data["merchant_state"]) ||  $merchant_data["merchant_state"] != 1){
			throw new error('商家未认证');
		}
		
		//获取配置
		$rmb_buy_merchant_credit = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("rmb_buy_merchant_credit"), true);
		object(parent::ERROR)->check( $rmb_buy_merchant_credit, 'ratio_credit', parent::TABLE_CONFIG, array('args'), "rmb_buy_merchant_credit[ratio_credit]" );
		object(parent::ERROR)->check( $rmb_buy_merchant_credit, 'ratio_rmb', parent::TABLE_CONFIG, array('args'), "rmb_buy_merchant_credit[ratio_rmb]" );
		object(parent::ERROR)->check( $rmb_buy_merchant_credit, 'algorithm', parent::TABLE_CONFIG, array('args'), "rmb_buy_merchant_credit[algorithm]" );
		if( empty($rmb_buy_merchant_credit['state']) ){
			throw new error ("人民币购买商家积分的功能已经关闭");
		}
		
		if( $data['credit_number'] < $rmb_buy_merchant_credit["ratio_credit"] ){
			//throw new error ( "积分数量不能小于".$rmb_buy_merchant_credit["ratio_credit"]."个" );
			throw new error ( "积分的购买数量太少" );
		}
		
		//备注信息
		$data["comment"] = !empty($data["comment"])? " 《".$merchant_data["merchant_name"]."》 - ".$data["comment"] : " 《".$merchant_data["merchant_name"]."》 ";
		
		$insert = array(
			"order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
			"order_type" => parent::TRANSACTION_TYPE_RECHARGE,//充值
			"order_comment" => "商家积分充值".$data["comment"],
			"order_plus_method" => "merchant_credit",//收款方式
			"order_plus_account_id" => $data['merchant_id'],//收款账户ID
			"order_plus_value" => $data['credit_number'],//收款值
			"order_plus_update_time" => time(),
			
			"order_action_user_id" => $_SESSION['user_id'],
			"order_minus_method" => $data["pay_method"],
			"order_minus_account_id" => $_SESSION['user_id'],
			"order_minus_update_time" => time(),
			"order_state" => 1,//确定订单
			"order_pay_state" => 0,//未支付
			"order_insert_time" => time(),
			"order_json" => array(
				"rmb_buy_merchant_credit" => $rmb_buy_merchant_credit
			)
		);
		
		//获得支付金额
		if( $rmb_buy_merchant_credit["algorithm"] == "ceil" ){
			$insert["order_minus_value"] = ceil(($data['credit_number']/$rmb_buy_merchant_credit['ratio_credit'])*$rmb_buy_merchant_credit['ratio_rmb']);
		}else
		if( $rmb_buy_merchant_credit["algorithm"] == "floor" ){
			$insert["order_minus_value"] = floor(($data['credit_number']/$rmb_buy_merchant_credit['ratio_credit'])*$rmb_buy_merchant_credit['ratio_rmb']);
		}else
		if( $rmb_buy_merchant_credit["algorithm"] == "round" ){
			$insert["order_minus_value"] = round(($data['credit_number']/$rmb_buy_merchant_credit['ratio_credit'])*$rmb_buy_merchant_credit['ratio_rmb']);
		}
		
		// 是否用户钱包支付
		if ($data['pay_method'] === parent::PAY_METHOD_USER_MONEY) {
			// 检测输入
            object(parent::ERROR)->check($data, 'pay_password', parent::TABLE_USER, array('args'));

            // 检测支付密码
            $res = object(parent::TABLE_USER)->check_pay_password($data['pay_password']);
            if ($res !== true)
                throw new error($res);

            // 检测余额
            $user_money = object(parent::TABLE_USER_MONEY)->find_now_data($_SESSION['user_id']);
            if (empty($user_money['user_money_value']) || $user_money['user_money_value'] < $insert['order_minus_value'])
                throw new error('余额不足');

            return object(parent::TABLE_MERCHANT_CREDIT)->pay_by_user_money($insert, $user_money);
		} else {
			//获取支付配置
			$return_api = object(parent::REQUEST_APPLICATION)->get_pay_config($data, $insert["order_json"], array(
				"money_fen" => $insert["order_minus_value"],
				"subject" => "商家充值-积分充值",
				"body" => "商家积分充值",
				"order_id" => $insert['order_id']
			));
			
			$return_api['order_id'] = $insert['order_id'];
			
			if( !empty($insert["order_json"]) && is_array($insert["order_json"]) ){
				$insert["order_json"] = cmd(array($insert["order_json"]), "json encode");
			}
			
			if( object(parent::TABLE_ORDER)->insert($insert) ){
				return $return_api;
			}else{
				throw new error ("操作失败");
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * 登录用户以商家积分赠送给指定用户
	 * 
	 * APPLICATIONORDERSELFMERCHANTCREDITGIVEUSERCREDIT
	 * [{"merchant_id":"商家ID","credit_number":"必须|转账积分数量","phone":"要赠送的用户手机号","comment":"备注信息"}]
	 * 
	 * {"class":"application/order","method":"api_self_merchant_credit_give_user_credit"}
	 * 
	 * @param	array	$data
	 * @return	string	订单ID
	 */
	public function api_self_merchant_credit_give_user_credit($data = array()){
		//检查是否已初始化
		object(parent::REQUEST_USER)->check();
		object(parent::ERROR)->check( $data, 'merchant_id', parent::TABLE_MERCHANT, array('args') );
		object(parent::ERROR)->check( $data, 'credit_number', parent::TABLE_ORDER, array('args') );
		object(parent::ERROR)->check( $data, 'phone', parent::TABLE_USER_PHONE, array('args', 'length'), 'user_phone_id');
		object(parent::ERROR)->check( $data, 'comment', parent::TABLE_ORDER, array('args') );
		
		//判断当前用户是否是 该 商家ID 的用户
        if ( !object(parent::TABLE_MERCHANT_USER)->check_exist($_SESSION["user_id"], $data['merchant_id'], true) ){
        	throw new error('商家不存在');
        }
		$find_verify_data_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['phone'], array("up.user_phone_id","up.user_id"));
		if( empty($find_verify_data_data["user_id"]) ){
			throw new error("手机号码有误，用户数据不存在");
		}
		
		if ( object(parent::TABLE_MERCHANT_USER)->check_exist($find_verify_data_data["user_id"], $data['merchant_id'], true) ){
        	throw new error('不能将商家积分赠送给商家用户');
        }
		
		if( $find_verify_data_data["user_id"] == $_SESSION["user_id"]){
			throw new error('不能将商家积分赠送给自己');
		}
		
		//获取商家数据
		$merchant_data = object(parent::TABLE_MERCHANT)->find_base_data($data['merchant_id']);
		if( empty($merchant_data["merchant_state"]) ||  $merchant_data["merchant_state"] != 1){
			throw new error('商家未认证');
		}
		//获取商家积分数据
		$merchant_credit_data = object(parent::TABLE_MERCHANT_CREDIT)->find_now_data($data['merchant_id']);
		if( empty($merchant_credit_data) ||
		($merchant_credit_data["merchant_credit_value"] - $data["credit_number"]) < 0 ){
			throw new error('商家积分不足');
		}
		
		//备注信息
		$data["comment"] = !empty($data["comment"])? " 《".$merchant_data["merchant_name"]."》 [".$data['phone']."] - ".$data["comment"] : " 《".$merchant_data["merchant_name"]."》  [".$data['phone']."]";
		
		$order_id = object(parent::TABLE_MERCHANT_CREDIT)->insert_transfer_user_credit(array(
			"merchant_id" => $data['merchant_id'],
			"merchant_credit_id" => $merchant_credit_data["merchant_credit_id"],
			"merchant_credit_value" => $merchant_credit_data["merchant_credit_value"],
			"transfer_credit_value" => $data["credit_number"],
			"order_comment" => "商家赠送用户积分".$data["comment"],
			"order_plus_account_id" => $find_verify_data_data["user_id"],
			"order_action_user_id" =>$_SESSION['user_id']
		));
		
		if( empty($order_id) ){
			throw new error('操作失败');
		}else{
			return $order_id;
		}
		
	}
	
	
	
	
	
	
	
	
	
	/**
	 * 用户登录状态下购买余额，生成订单
	 * 
	 * APPLICATIONORDERSELFBUYUSERMONEY
	 * [{"money_fen":"必须|要购买余额(人民币，分)","pay_method":"支付方式 weixinpay 微信支付、alipay 支付宝支付","weixin_login_code":"微信支付是需要，用于换取用户OpenID","weixin_trade_type":"微信的交易类型，必须是大写，如JSAPI|APP|MWEB|NATIVE"}]
	 * 
	 * {"class":"application/order","method":"api_self_buy_user_money"}
	 * 
	 * @param	array		$data
	 * @return string	返回订单ID
	 */
	public function api_self_buy_user_money($data = array()){
		//检查是否已初始化
		object(parent::REQUEST_USER)->check();
		object(parent::ERROR)->check( $data, 'money_fen', parent::TABLE_ORDER, array('args') );
		object(parent::ERROR)->check( $data, 'pay_method', parent::TABLE_ORDER, array('args') );
		
		//获取配置
		if( $data['money_fen'] < 1 ){
			throw new error ( "购买余额不能小于0" );
		}
		
		
		$insert = array(
			"order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
			"order_type" => parent::TRANSACTION_TYPE_RECHARGE,//充值
			"order_comment" => "用户钱包余额充值",
			"order_plus_method" => "user_money",//收款方式
			"order_plus_account_id" => $_SESSION['user_id'],//收款账户ID
			"order_plus_value" => $data['money_fen'],//收款值
			"order_plus_update_time" => time(),
			
			"order_action_user_id" => $_SESSION['user_id'],
			"order_minus_method" => $data["pay_method"],
			"order_minus_account_id" => $_SESSION['user_id'],
			"order_minus_value" => $data['money_fen'],//支付款人民币
			"order_minus_update_time" => time(),
			"order_state" => 1,//确定订单
			"order_pay_state" => 0,//未支付
			"order_insert_time" => time(),
			"order_json" => array()
		);
		
		
		//获取支付配置
		$return_api = object(parent::REQUEST_APPLICATION)->get_pay_config($data, $insert["order_json"], array(
			"money_fen" => $insert["order_minus_value"],
			"subject" => "用户充值-钱包余额充值",
			"body" => "用户钱包余额充值",
			"order_id" => $insert['order_id']
		));
		
		
		$return_api['order_id'] = $insert['order_id'];
		if( !empty($insert["order_json"]) && is_array($insert["order_json"]) ){
			$insert["order_json"] = cmd(array($insert["order_json"]), "json encode");
		}
		if( object(parent::TABLE_ORDER)->insert($insert) ){
			return $return_api;
		}else{
			throw new error ("操作失败");
		}
		
		
	}
	
	
	
	
	
	
	
	
	
	/**
	 * 用户登录状态下第三方平台支付到商家钱包(商家收款)，生成订单
	 * 
	 * APPLICATION ORDER SELF PAY MERCHANT MONEY
	 * APPLICATIONORDERSELFPAYMERCHANTMONEY
	 * 
	 * [{"merchant_id":"商家ID","cashier":"收银员的user_id","money_fen":"必须|要支付余额(人民币，分)","pay_method":"支付方式 weixinpay 微信支付、alipay 支付宝支付","weixin_login_code":"微信支付是需要，用于换取用户OpenID","weixin_trade_type":"微信的交易类型，必须是大写，如JSAPI|APP|MWEB|NATIVE","comment":"备注信息"}]
	 * {"class":"application/order","method":"api_self_pay_merchant_money"}
	 * 
	 * @param	array		$data
	 * @return string	返回订单ID
	 */
	public function api_self_pay_merchant_money($data = array()){
		object(parent::REQUEST_USER)->check();
		object(parent::ERROR)->check( $data, 'merchant_id', parent::TABLE_MERCHANT, array('args') );
		//object(parent::ERROR)->check( $data, 'cashier', parent::TABLE_MERCHANT_CASHIER, array('args'), 'user_id');
		object(parent::ERROR)->check( $data, 'pay_method', parent::TABLE_ORDER, array('args') );
		object(parent::ERROR)->check( $data, 'money_fen', parent::TABLE_ORDER, array('args') );
		object(parent::ERROR)->check( $data, 'comment', parent::TABLE_ORDER, array('args') );
		
		//获取配置
		if( $data['money_fen'] < 1 ){
			throw new error ( "支付余额不能小于0" );
		}
		
		//获取商家数据
		$merchant_data = object(parent::TABLE_MERCHANT)->find($data['merchant_id']);
		if( empty($merchant_data["merchant_state"]) ||  $merchant_data["merchant_state"] != 1){
			throw new error('商家未认证');
		}
		
		
		//判断 该收银员的用户ID 是否是该商家的收银员
		/*$cashier = object(parent::TABLE_MERCHANT_CASHIER)->find_by_uid_and_merchant_id(
			array(
				'user_id' => $data['cashier'],
				'merchant_id' => $data['merchant_id']
			)
		);
		if (empty($cashier)) {
			throw new error('不是此商家的收银员');
		}*/

		
		
		
		//获取商户的库存积分  判断要赠送的积分
		$merchant_credit_data = object(parent::TABLE_MERCHANT_CREDIT)->find_now_data($data['merchant_id']);
		
		//获取配置
		if( !empty($merchant_data["merchant_json"]) ){
			$merchant_data["merchant_json"] = cmd(array($merchant_data["merchant_json"]), "json decode");
		}
		$config_rmb_consume_user_credit = array();
		if( !empty($merchant_data["merchant_json"]["config_rmb_consume_user_credit"]) ){
			$config_rmb_consume_user_credit = $merchant_data["merchant_json"]["config_rmb_consume_user_credit"];
		}
		$rmb_consume_user_credit = object(parent::REQUEST_APPLICATION)->get_rmb_consume_user_credit($data['money_fen'], $config_rmb_consume_user_credit, TRUE);
		//用户消费赠送积分开启中
		if( !empty($rmb_consume_user_credit['state']) ){
			if( empty($merchant_credit_data) || ($merchant_credit_data["merchant_credit_value"] - $rmb_consume_user_credit['user_credit_plus']) < 0 ){
				throw new error('商家积分不足');
			}
		}
		
		
		if( !empty($_SESSION['user']['user_nickname']) ){
			$user_comment = $_SESSION['user']['user_nickname'];
		} else {
			//获取当前用户登录手机号
			$user_phone_data = object(parent::TABLE_USER_PHONE)->find_user_login_data($_SESSION['user_id']);
			if( empty($user_phone_data["user_phone_id"]) ){
				throw new error('登录手机号异常');
			}
			$user_comment = $user_phone_data["user_phone_id"];
		}
		
		//备注信息
		$data["comment"] = !empty($data["comment"])? " 《".$merchant_data["merchant_name"]."》 [".$user_comment."] - ".$data["comment"] : " 《".$merchant_data["merchant_name"]."》  [".$user_comment."]";
		
		$nonce_str = cmd(array(24), 'random string');
		
		$insert = array(
			"order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
			"order_type" => parent::TRANSACTION_TYPE_RECHARGE,//充值
			"order_comment" => "商家钱包余额收款".$data["comment"],
			"order_plus_method" => "merchant_money",//收款方式
			"order_plus_account_id" => $data['merchant_id'],//收款账户ID
			"order_plus_value" => $data['money_fen'],//收款值
			"order_plus_update_time" => time(),
			
			//"order_action_user_id" => $data['cashier'],
			"order_action_user_id" => $_SESSION['user_id'],
			"order_minus_method" => $data["pay_method"],
			"order_minus_account_id" => $_SESSION['user_id'],
			"order_minus_value" => $data['money_fen'],//支付款人民币
			"order_minus_update_time" => time(),
			"order_state" => 1,//确定订单
			"order_pay_state" => 0,//未支付
			"order_insert_time" => time(),
			"order_json" => array(
				"rmb_consume_user_credit" => $rmb_consume_user_credit,
				)
		);
		
		
		//获取支付配置
		$return_api = object(parent::REQUEST_APPLICATION)->get_pay_config($data, $insert["order_json"], array(
			"money_fen" => $insert["order_minus_value"],
			"subject" => "商家收款-钱包余额收款",
			"body" => "商家钱包余额收款",
			"order_id" => $insert['order_id']
		));
		
		
		$return_api['order_id'] = $insert['order_id'];
		if( !empty($insert["order_json"]) && is_array($insert["order_json"]) ){
			$insert["order_json"] = cmd(array($insert["order_json"]), "json encode");
		}
		
		if( object(parent::TABLE_ORDER)->insert($insert) ){
			return $return_api;
		}else{
			throw new error ("操作失败");
		}
		
		
	}
	
	
	
	
	/**
	 * 登录用户以用户钱包余额支付商家钱包余额(商家收款)
	 * 
	 * APPLICATION ORDER SELF USER MONEY  PAY MERCHANT MONEY
	 * APPLICATIONORDERSELFUSERMONEYPAYMERCHANTMONEY
	 * 
	 * [{"merchant_id":"商家ID","cashier":"收银员的user_id","money_fen":"必须|要支付余额(人民币，分)","comment":"备注信息"}]
	 * 
	 * {"class":"application/order","method":"api_self_user_money_pay_merchant_money"}
	 * 
	 * @param	array	$data
	 * @return	string	订单ID
	 */
	public function api_self_user_money_pay_merchant_money($data = array()){
		//检查是否已初始化
		object(parent::REQUEST_USER)->check();
		object(parent::ERROR)->check( $data, 'merchant_id', parent::TABLE_MERCHANT, array('args') );
		//object(parent::ERROR)->check( $data, 'cashier', parent::TABLE_MERCHANT_CASHIER, array('args'), 'user_id');
		object(parent::ERROR)->check( $data, 'money_fen', parent::TABLE_ORDER, array('args') );
		object(parent::ERROR)->check( $data, 'comment', parent::TABLE_ORDER, array('args') );
		object(parent::ERROR)->check( $data, 'pay_password', parent::TABLE_USER, array('args'));
		
		
		//获取配置
		if( $data['money_fen'] < 1 ){
			throw new error ( "支付余额不能小于0" );
		}
		
		//获取商家数据
		$merchant_data = object(parent::TABLE_MERCHANT)->find($data['merchant_id']);
		if( empty($merchant_data["merchant_state"]) ||  $merchant_data["merchant_state"] != 1){
			throw new error('商家未认证');
		}


		//判断 该收银员的用户ID 是否是该商家的收银员
		/*$cashier = object(parent::TABLE_MERCHANT_CASHIER)->find_by_uid_and_merchant_id(
			array(
				'user_id' => $data['cashier'],
				'merchant_id' => $data['merchant_id']
			)
		);
		if (empty($cashier)) {
			throw new error('不是此商家的收银员');
		}*/




		//检测支付密码
        $res = object(parent::TABLE_USER)->check_pay_password($data['pay_password']);
        if ($res !== true)
        throw new error($res);
		
		//获取用户余额数据
		$user_money_data = object(parent::TABLE_USER_MONEY)->find_now_data($_SESSION['user_id']);
		if( empty($user_money_data) ||
		($user_money_data["user_money_value"] - $data["money_fen"]) < 0 ){
			throw new error('用户余额不足');
		}
		
		//获取商户的库存积分  判断要赠送的积分
		$merchant_credit_data = object(parent::TABLE_MERCHANT_CREDIT)->find_now_data($data['merchant_id']);
		
		//获取配置
		if( !empty($merchant_data["merchant_json"]) ){
			$merchant_data["merchant_json"] = cmd(array($merchant_data["merchant_json"]), "json decode");
		}
		$config_rmb_consume_user_credit = array();
		if( !empty($merchant_data["merchant_json"]["config_rmb_consume_user_credit"]) ){
			$config_rmb_consume_user_credit = $merchant_data["merchant_json"]["config_rmb_consume_user_credit"];
		}
		$rmb_consume_user_credit = object(parent::REQUEST_APPLICATION)->get_rmb_consume_user_credit($data['money_fen'], $config_rmb_consume_user_credit, TRUE);
		//用户消费赠送积分开启中
		if( !empty($rmb_consume_user_credit['state']) ){
			if( empty($merchant_credit_data) || ($merchant_credit_data["merchant_credit_value"] - $rmb_consume_user_credit['user_credit_plus']) < 0 ){
				throw new error('商家积分不足');
			}
		}
		
		if( !empty($_SESSION['user']['user_nickname']) ){
			$user_comment = $_SESSION['user']['user_nickname'];
		} else {
			//获取当前用户登录手机号
			$user_phone_data = object(parent::TABLE_USER_PHONE)->find_user_login_data($_SESSION['user_id']);
			if( empty($user_phone_data["user_phone_id"]) ){
				throw new error('登录手机号异常');
			}
			$user_comment = $user_phone_data["user_phone_id"];
		}
		
		//备注信息
		$data["comment"] = !empty($data["comment"])? " 《".$merchant_data["merchant_name"]."》 [".$user_comment."] - ".$data["comment"] : " 《".$merchant_data["merchant_name"]."》  [".$user_comment."]";
		
		$order_id = object(parent::TABLE_USER_MONEY)->insert_transfer_merchant_money(array(
			"merchant_credit" => $merchant_credit_data,
			"user_id" => $_SESSION['user_id'],
			"user_money" => $user_money_data,
			"transfer_money_value" => $data["money_fen"],
			"order_comment" => "商家收款".$data["comment"],
			"order_plus_account_id" => $data['merchant_id'],
			"order_action_user_id" => $_SESSION['user_id'],
			//"order_action_user_id" => $data['cashier'],
			"order_json" => array(
				"rmb_consume_user_credit" => $rmb_consume_user_credit,
				)
		));
		
		if( empty($order_id) ){
			throw new error('操作失败');
		}else{
			return $order_id;
		}
		
		
		
	}
	
	
	
	
	/**
	 * 检测用户今日是否已经签到(用户每日签到获得积分)
	 * 
	 * APPLICATIONORDERSELFDAILYATTENDANCEEARNUSERCREDITEXIST
	 * {"class":"application/order","method":"api_self_daily_attendance_earn_user_credit_exist"}
	 * 
	 * 报错未签到，已经签到返回true
	 * 
	 * APPLICATION ORDER SELF DAILYATTENDANCE EARN USERCREDIT EXIST
	 * 
	 * @param	array	$data
	 * @return	string	订单ID
	 */
	public function api_self_daily_attendance_earn_user_credit_exist(){
		//检查是否已初始化
		object(parent::REQUEST_USER)->check();
		$daily_attendance_data = object(parent::TABLE_ORDER)->find_daily_attendance_earn_user_credit($_SESSION["user_id"]);
		if( empty($daily_attendance_data) ){
			throw new error ("没有签到");
		}
		
		return true;
	}
	
    
	
	
	
	/**
	 * 用户每日签到获得积分
	 * 
	 * APPLICATIONORDERSELFDAILYATTENDANCEEARNUSERCREDIT
	 * {"class":"application/order","method":"api_self_daily_attendance_earn_user_credit"}
	 * 
	 * APPLICATION ORDER SELF DAILYATTENDANCE EARN USERCREDIT
	 * 
	 * @param	array	$data
	 * @return	string	订单ID
	 */
	public function api_self_daily_attendance_earn_user_credit(){
		//检查是否已初始化
		object(parent::REQUEST_USER)->check();
		
		//获取配置
		$daily_attendance_earn_user_credit = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("daily_attendance_earn_user_credit"), true);
		object(parent::ERROR)->check( $daily_attendance_earn_user_credit, 'credit', parent::TABLE_CONFIG, array('args'), "daily_attendance_earn_user_credit[credit]" );
		if( empty($daily_attendance_earn_user_credit['state']) ){
			throw new error ("每日签到已经关闭");
		}
		
		$daily_attendance_data = object(parent::TABLE_ORDER)->find_daily_attendance_earn_user_credit($_SESSION["user_id"]);
		if( !empty($daily_attendance_data) ){
			throw new error ("今日已经签到");
		}

		// 是否存在Emshop配置
		$emshop_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("emshop_config"), true);

		$emshop_config = cmd(array($emshop_config), "json decode");
		if(!empty($emshop_config)){

			// 查询用户是否为会员
			// (备注：买过会员礼包就是会员，但是会员不一定作为邀请人有邀请关系)
			// 所以不能查询recommend_user_id
			// 查询admin_id,判断是否为分销身份

			$admin_id = object(parent::TABLE_ADMIN_USER)->find($_SESSION['user_id']);
			$distribution_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("shop_distribution_reward"), true);
			if( isset($admin_id['admin_id']) && isset($distribution_config[$admin_id['admin_id']]) ){
				// 会员
				$order_id = object(parent::TABLE_USER_CREDIT)->insert_daily_attendance_user_credit(array(
						"user_id" => $_SESSION['user_id'],
						"order_plus_account_id" => $_SESSION['user_id'],
						"daily_attendance_credit_value" => $emshop_config['daily_attendance']['member'],
						"order_comment" => "每日签到",
						"order_json" => array(
							"emshop_config" => $emshop_config
						)
				));
				
				if( empty($order_id) ){
					throw new error('操作失败');
				}else{
					return array(
						'order_id' => $order_id,
						'credit_value' => $emshop_config['daily_attendance']['member']
					);
				}
			} else {
				// 非会员
				$order_id = object(parent::TABLE_USER_CREDIT)->insert_daily_attendance_user_credit(array(
						"user_id" => $_SESSION['user_id'],
						"order_plus_account_id" => $_SESSION['user_id'],
						"daily_attendance_credit_value" => $emshop_config['daily_attendance']['user'],
						"order_comment" => "每日签到",
						"order_json" => array(
							"emshop_config" => $emshop_config
						)
				));
				
				if( empty($order_id) ){
					throw new error('操作失败');
				}else{
					return array(
						'order_id' => $order_id,
						'credit_value' => $emshop_config['daily_attendance']['user']
					);
				}
			}
		}


		$order_id = object(parent::TABLE_USER_CREDIT)->insert_daily_attendance_user_credit(array(
				"user_id" => $_SESSION['user_id'],
				"order_plus_account_id" => $_SESSION['user_id'],
				"daily_attendance_credit_value" => $daily_attendance_earn_user_credit["credit"],
				"order_comment" => "每日签到",
				"order_json" => array(
					"daily_attendance_earn_user_credit" => $daily_attendance_earn_user_credit
				)
		));
		
		if( empty($order_id) ){
			throw new error('操作失败');
		}else{
			return array(
				'order_id' => $order_id,
				'credit_value' => $daily_attendance_earn_user_credit["credit"]
			);
		}
	}
	
	
	
	
	
	
	
	/**
	 * 用户的赠送收益转账到用户钱包（预付款）
	 * 满100元才能 转账（管理员可自定义设置）
	 * 
	 * APPLICATIONORDERSELFUSERMONEYEARNINGTRANSFERUSERMONEY
	 * [{"money_fen":"必须|要转账余额(人民币，分)","comment":"备注信息"}]
	 * 
	 * 
	 * {"class":"application/order","method":"api_self_user_money_earning_transfer_user_money"}
	 * 
	 * 
	 * APPLICATION ORDER SELF USERMONEYEARNING TRANSFER USERMONEY
	 * 
	 * @param	array	$data
	 * @return	string	订单ID
	 */
	public function api_self_user_money_earning_transfer_user_money( $data = array() ){
		//检查是否已初始化
		object(parent::REQUEST_USER)->check();
		object(parent::ERROR)->check( $data, 'money_fen', parent::TABLE_ORDER, array('args') );
		object(parent::ERROR)->check( $data, 'comment', parent::TABLE_ORDER, array('args') );
		
		//获取配置
		if( $data['money_fen'] < 1 ){
			throw new error ( "转账金额不能小于0" );
		}
		
		//获取配置
		$config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("user_money_earning_transfer_user_money"), true);
		object(parent::ERROR)->check( $config, 'min_user_money_earning', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money[min_user_money_earning]" );
		object(parent::ERROR)->check( $config, 'max_user_money_earning', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money[max_user_money_earning]" );
		object(parent::ERROR)->check( $config, 'ratio_user_money_service', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money[ratio_user_money_service]" );
		object(parent::ERROR)->check( $config, 'algorithm', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money[algorithm]" );
		if( empty($config['state']) ){
			throw new error ("赠送收益转账到钱包的功能已经关闭");
		}
		
		// ——是否需要实名认证——
		if( !empty($config['user_identity_state']) ){
			$user_identity = object(parent::TABLE_USER_IDENTITY)->find($_SESSION['user_id']);
        	if( empty($user_identity) || 
        	$user_identity['user_identity_state'] != 1 || 
        	!object(parent::TABLE_USER_IDENTITY)->check_state($_SESSION['user_id'], $user_identity['user_identity_update_time']) )
            	throw new error('未实名认证');
		}
		
		// ——是否需要支付密码——
		if( !empty($config['pay_password_state']) ){
			object(parent::ERROR)->check( $data, 'pay_password', parent::TABLE_USER, array('args'));
			//检测支付密码
	        $res = object(parent::TABLE_USER)->check_pay_password($data['pay_password']);
	        if ($res !== true)
	        throw new error($res);
		}
		
		//获取用户余额数据
		$user_money_earning_data = object(parent::TABLE_USER_MONEY_EARNING)->find_now_data($_SESSION['user_id']);
		if( empty($user_money_earning_data) ||
		($user_money_earning_data["user_money_earning_value"] - $data["money_fen"]) < 0 ){
			throw new error('赠送收益的余额不足');
		}
		
		if($data["money_fen"] < $config["min_user_money_earning"]){
			throw new error( '转账的赠送收益金额不能小于'.($config["min_user_money_earning"]/100).'元' );
		}
		if($data["money_fen"] > $config["max_user_money_earning"]){
			throw new error( '转账的赠送收益金额不能大于'.($config["max_user_money_earning"]/100).'元' );
		}
		
		//获得用户服务费
		$user_money_service_money_fen = 0;
		if( $config["algorithm"] == "ceil" ){
			$user_money_service_money_fen = ceil( $data['money_fen']*$config['ratio_user_money_service'] );
		}else
		if( $config["algorithm"] == "floor" ){
			$user_money_service_money_fen = floor( $data['money_fen']*$config['ratio_user_money_service'] );
		}else
		if( $config["algorithm"] == "round" ){
			$user_money_service_money_fen = round( $data['money_fen']*$config['ratio_user_money_service'] );
		}
		
		//用户提现费用
		$transfer_money_value = $data['money_fen'] - $user_money_service_money_fen;
		if( $transfer_money_value < 0){
			throw new error('转账金额太少或者服务费异常');
		}
		
		
		//备注信息
		$data["comment"] = !empty($data["comment"])? " - ".$data["comment"] : "";
		//配置信息
		$order_json = array(
			"user_money_earning_transfer_user_money" => $config
		);
		$order_id = object(parent::TABLE_USER_MONEY_EARNING)->insert_transfer_user_money(array(
			"user_id" => $_SESSION['user_id'],
			"user_money_earning" => $user_money_earning_data,
			"user_money_service_money_fen"=> $user_money_service_money_fen,
			"transfer_money_value" => $transfer_money_value,
			"order_comment" => "用户的赠送收益转账到用户钱包".$data["comment"],
			"order_plus_account_id" => $_SESSION['user_id'],
			"order_minus_account_id" => $_SESSION['user_id'],
			"order_json" => $order_json,
		));
		
		if( empty($order_id) ){
			throw new error('操作失败');
		}else{
			return $order_id;
		}
		
		
	}
	
	
	
	
	
		
	
	
	/**
	 * 用户的赠送收益转账到用户扶贫账户
	 * 满100元才能 转账（管理员可自定义设置）
	 * 
	 * APPLICATIONORDERSELFUSERMONEYEARNINGTRANSFERUSERMONEYHELP
	 * [{"money_fen":"必须|要转账余额(人民币，分)","comment":"备注信息"}]
	 * 
	 * 
	 * {"class":"application/order","method":"api_self_user_money_earning_transfer_user_money_help"}
	 * 
	 * @param	array	$data
	 * @return	string	订单ID
	 */
	public function api_self_user_money_earning_transfer_user_money_help( $data = array() ){
		//检查是否已初始化
		object(parent::REQUEST_USER)->check();
		object(parent::ERROR)->check( $data, 'money_fen', parent::TABLE_ORDER, array('args') );
		object(parent::ERROR)->check( $data, 'comment', parent::TABLE_ORDER, array('args') );
		
		//获取配置
		if( $data['money_fen'] < 1 ){
			throw new error ( "转账金额不能小于0" );
		}
		
		//获取配置
		$config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("user_money_earning_transfer_user_money_help"), true);
		object(parent::ERROR)->check( $config, 'min_user_money_earning', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money_help[min_user_money_earning]" );
		object(parent::ERROR)->check( $config, 'max_user_money_earning', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money_help[max_user_money_earning]" );
		object(parent::ERROR)->check( $config, 'ratio_user_money_service', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money_help[ratio_user_money_service]" );
		object(parent::ERROR)->check( $config, 'algorithm', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money_help[algorithm]" );
		if( empty($config['state']) ){
			throw new error ("赠送收益转账到扶贫账户的功能已经关闭");
		}
		
		
		// ——是否需要实名认证——
		if( !empty($config['user_identity_state']) ){
			$user_identity = object(parent::TABLE_USER_IDENTITY)->find($_SESSION['user_id']);
        	if( empty($user_identity) || 
        	$user_identity['user_identity_state'] != 1 || 
        	!object(parent::TABLE_USER_IDENTITY)->check_state($_SESSION['user_id'], $user_identity['user_identity_update_time']) )
            	throw new error('未实名认证');
		}
		
		// ——是否需要支付密码——
		if( !empty($config['pay_password_state']) ){
			object(parent::ERROR)->check( $data, 'pay_password', parent::TABLE_USER, array('args'));
			//检测支付密码
	        $res = object(parent::TABLE_USER)->check_pay_password($data['pay_password']);
	        if ($res !== true)
	        throw new error($res);
		}
		
		//获取用户余额数据
		$user_money_earning_data = object(parent::TABLE_USER_MONEY_EARNING)->find_now_data($_SESSION['user_id']);
		if( empty($user_money_earning_data) ||
		($user_money_earning_data["user_money_earning_value"] - $data["money_fen"]) < 0 ){
			throw new error('赠送收益的余额不足');
		}
		
		if( $data["money_fen"] < $config["min_user_money_earning"] ){
			throw new error( '转账的赠送收益金额不能小于'.($config["min_user_money_earning"]/100).'元' );
		}
		
		if( $data["money_fen"] > $config["max_user_money_earning"] ){
			throw new error( '转账的赠送收益金额不能大于'.($config["max_user_money_earning"]/100).'元' );
		}
		
		//获得用户服务费
		$user_money_service_money_fen = 0;
		if( $config["algorithm"] == "ceil" ){
			$user_money_service_money_fen = ceil( $data['money_fen']*$config['ratio_user_money_service'] );
		}else
		if( $config["algorithm"] == "floor" ){
			$user_money_service_money_fen = floor( $data['money_fen']*$config['ratio_user_money_service'] );
		}else
		if( $config["algorithm"] == "round" ){
			$user_money_service_money_fen = round( $data['money_fen']*$config['ratio_user_money_service'] );
		}
		
		//用户提现费用
		$transfer_money_value = $data['money_fen'] - $user_money_service_money_fen;
		if( $transfer_money_value < 0){
			throw new error('转账金额太少或者服务费异常');
		}
		
		//备注信息
		$data["comment"] = !empty($data["comment"])? " - ".$data["comment"] : "";
		//配置信息
		$order_json = array(
			"user_money_earning_transfer_user_money_help" => $config
		);
		$order_id = object(parent::TABLE_USER_MONEY_EARNING)->insert_transfer_user_money_help(array(
			"user_id" => $_SESSION['user_id'],
			"user_money_earning" => $user_money_earning_data,
			"user_money_service_money_fen"=> $user_money_service_money_fen,
			"transfer_money_value" => $transfer_money_value,
			"order_comment" => "用户的赠送收益转账到扶贫账户".$data["comment"],
			"order_plus_account_id" => $_SESSION['user_id'],
			"order_minus_account_id" => $_SESSION['user_id'],
			"order_json" => $order_json,
		));
		
		if( empty($order_id) ){
			throw new error('操作失败');
		}else{
			return $order_id;
		}
		
		
	}
	
	
	
	
	/**
	 * 用户钱包提现
	 *
	 * api: APPLICATIONORDERSELFUSERMONEYWITHDRAW
	 * req: {
	 *  withdraw_method 	[str] [必填] [提现方式]
	 *  money_fen 			[int] [必填] [提现金额，单位分]
	 *  comment 			[str] [可选] [备注信息]
	 * }
	 * 
	 * @return string	订单ID
	 */
	public function api_self_user_money_withdraw($input = array())
	{
		//检测登录
		object(parent::REQUEST_USER)->check();

		//检测输入
		object(parent::ERROR)->check( $input, 'money_fen', parent::TABLE_ORDER, array('args') );
		object(parent::ERROR)->check( $input, 'withdraw_method', parent::TABLE_ORDER, array('args') );
		object(parent::ERROR)->check( $input, 'comment', parent::TABLE_ORDER, array('args') );
		
		//查询配置
		$config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('rmb_withdraw_user_money'), true);
		if (empty($config['state']))
			throw new error ('提现功能已关闭');

		//最小提现的金额
		if($input['money_fen'] < $config['min_user_money'])
			throw new error('提现金额不能小于' . $config['min_user_money'] / 100 . '元');

		//最大提现的金额
		if($input['money_fen'] > $config['max_user_money'])
			throw new error('提现金额不能大于' . $config['max_user_money'] / 100 . '元');

		//是否需要实名认证
		if (!empty($config['user_identity_state'])) {
			if(!object(parent::TABLE_USER_IDENTITY)->check_state($_SESSION['user_id']))
				throw new error ('未实名认证');
		}

		//是否需要支付密码
		if (!empty($config['pay_password_state'])) {
			//检测输入
			object(parent::ERROR)->check($input, 'pay_password', parent::TABLE_USER, array('args'));

			//检测支付密码
            $res = object(parent::TABLE_USER)->check_pay_password($input['pay_password']);
            if ($res !== true)
                throw new error($res);
		}

		//是否手续费
		$poundage = 0;
		if (!empty($config['ratio_service_money']))
			$poundage = $config['ratio_service_money'] * $input['money_fen'];

		//获取用户余额数据
		$data_usermoney = object(parent::TABLE_USER_MONEY)->find_now_data($_SESSION['user_id']);
		if (empty($data_usermoney) || $data_usermoney['user_money_value'] < $input['money_fen'] + $poundage)
			throw new error('余额不足');

		//配置信息
		$order_json = array(
			'rmb_withdraw_user_money' => $config
		);

		//获取支付配置
		$pay_config = object(parent::REQUEST_APPLICATION)->get_pay_config($input, $order_json, array(
			'money_fen' => $input['money_fen'],
			'subject' => '用户提现-钱包提现',
			'body' => '用户提现-钱包提现',
		));

		//备注信息
		$input['comment'] = empty($input['comment']) ? '钱包提现' : '钱包提现 - '.$input['comment'];
		if ($poundage) {
			$input['comment'] .= " [手续费：{$poundage}，费率：{$config['ratio_service_money']}]";
		}

		$order_id = object(parent::TABLE_ORDER)->insert_user_money_withdraw(array(
			'user_id' => $_SESSION['user_id'],
			'comment'=> $input['comment'],
			'withdraw_poundage'=> $poundage,
			'withdraw_money_fen'=> $input['money_fen'] + $poundage,
			'withdraw_method' => $input['withdraw_method'],
			'order_json' => $order_json,
			'pay_config' => $pay_config,
			'user_money' => $data_usermoney,
		));

		if (!empty($order_id['errno'])) {
			throw new error($order_id['error']);
		} elseif (empty($order_id)) {
			throw new error('操作失败');
		} else {
			return $order_id;
		}
	}
	
	
	
	/**
	 * 用户的赠送收益提现
	 * APPLICATIONORDERSELFUSERMONEYEARNINGWITHDRAW
	 * [{"money_fen":"必须|要转账余额(人民币，分)","comment":"备注信息","withdraw_method":"提现方式 weixinpay 微信支付、alipay 支付宝支付","weixin_login_code":"微信支付是需要，用于换取用户OpenID","weixin_login_openid":"用户OpenID，当weixin_login_code参数不存在时需要","weixin_trade_type":"微信的交易类型，必须是大写，如JSAPI|APP|MWEB|NATIVE","alipay_account":"当支付宝提现时必填，支付宝账号","alipay_realname":"当支付宝提现时必填，真实姓名"}]
	 * 
	 * {"class":"application/order","method":"api_self_user_money_earning_withdraw"}
	 * 
	 * 
	 * APPLICATION ORDER SELF USERMONEYEARNING WITHDRAW
	 * 
	 * @param	array	$data
	 * @return	string	订单ID
	 */
	public function api_self_user_money_earning_withdraw( $data = array() ){
		// 检查登录
		object(parent::REQUEST_USER)->check();
		$user_id = $_SESSION['user_id'];

		object(parent::ERROR)->check( $data, 'money_fen', parent::TABLE_ORDER, array('args') );
		object(parent::ERROR)->check( $data, 'withdraw_method', parent::TABLE_ORDER, array('args') );
		object(parent::ERROR)->check( $data, 'comment', parent::TABLE_ORDER, array('args') );
		
		//获取配置
		$config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("rmb_withdraw_user_money_earning"), true);
		object(parent::ERROR)->check( $config, 'min_user_money_earning', parent::TABLE_CONFIG, array('args'), "rmb_withdraw_user_money_earning[min_user_money_earning]" );
		object(parent::ERROR)->check( $config, 'max_user_money_earning', parent::TABLE_CONFIG, array('args'), "rmb_withdraw_user_money_earning[max_user_money_earning]" );
		object(parent::ERROR)->check( $config, 'ratio_user_money_service', parent::TABLE_CONFIG, array('args'), "rmb_withdraw_user_money_earning[ratio_user_money_service]" );
		object(parent::ERROR)->check( $config, 'algorithm', parent::TABLE_CONFIG, array('args'), "rmb_withdraw_user_money_earning[algorithm]" );
		if( empty($config['state']) ){
			throw new error ("赠送收益的提现功能已经关闭");
		}
		
		//最小提现的金额
		if($data["money_fen"] < $config["min_user_money_earning"]){
			throw new error('提现的赠送收益金额不能小于'.($config["min_user_money_earning"]/100).'元');
		}
		//最大提现的金额
		if($data["money_fen"] > $config["max_user_money_earning"]){
			throw new error('提现的赠送收益金额不能大于'.($config["max_user_money_earning"]/100).'元');
		}
		
		//获得用户服务费
		$user_money_service_money_fen = 0;
		if( $config["algorithm"] == "ceil" ){
			$user_money_service_money_fen = ceil( $data['money_fen']*$config['ratio_user_money_service'] );
		}else
		if( $config["algorithm"] == "floor" ){
			$user_money_service_money_fen = floor( $data['money_fen']*$config['ratio_user_money_service'] );
		}else
		if( $config["algorithm"] == "round" ){
			$user_money_service_money_fen = round( $data['money_fen']*$config['ratio_user_money_service'] );
		}
		
		//用户提现费用
		$withdraw_money_fen = $data['money_fen'] - $user_money_service_money_fen;
		if( $withdraw_money_fen < 100){
			throw new error('提现金额太少或者服务费异常');
		}

		// ——是否需要实名认证——
		if (!empty($config['user_identity_state'])) {
			$user_identity = object(parent::TABLE_USER_IDENTITY)->find($user_id);
        	if (empty($user_identity) || $user_identity['user_identity_state'] != 1 || !object(parent::TABLE_USER_IDENTITY)->check_state($user_id, $user_identity['user_identity_update_time']))
            	throw new error('未实名认证');
		}

		// ——是否需要支付密码——
		if (!empty($config['pay_password_state'])) {
			//检测输入
			object(parent::ERROR)->check($data, 'pay_password', parent::TABLE_USER, array('args'));

			//检测支付密码
            $res = object(parent::TABLE_USER)->check_pay_password($data['pay_password']);
            if ($res !== true)
                throw new error($res);
		}
		
		//获取用户余额数据
		$user_money_earning_data = object(parent::TABLE_USER_MONEY_EARNING)->find_now_data($_SESSION['user_id']);
		if( empty($user_money_earning_data) ||
		($user_money_earning_data["user_money_earning_value"] - $data["money_fen"]) < 0 ){
			throw new error('赠送收益的余额不足');
		}
		
		//配置信息
		$order_json = array(
			"rmb_withdraw_user_money_earning" => $config
		);
		
		//获取支付配置
		$pay_config = object(parent::REQUEST_APPLICATION)->get_pay_config($data, $order_json, array(
			"money_fen" => $withdraw_money_fen,
			"subject" => "用户提现-用户的赠送收益提现",
			"body" => "用户提现-用户的赠送收益提现",
		));
		
		//备注信息
		$data["comment"] = !empty($data["comment"])? "赠送收益提现 - ".$data["comment"] : "赠送收益提现";
		
		$order_id = object(parent::TABLE_ORDER)->insert_user_money_earning_withdraw(array(
			"user_id" => $_SESSION['user_id'],
			"comment"=> $data["comment"],
			"user_money_service_money_fen"=> $user_money_service_money_fen,
			"withdraw_money_fen"=> $withdraw_money_fen,
			"withdraw_method" => $data['withdraw_method'],
			"order_json" => $order_json,
			"pay_config" => $pay_config,
			"user_money_earning" => $user_money_earning_data		//用户的赠送收益 旧数据
		));
		
		if( !empty($order_id['errno']) ){
			throw new error($order_id['error']);
		}else
		if( empty($order_id) ){
			throw new error('操作失败');
		}else{
			return $order_id;
		}
		
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>