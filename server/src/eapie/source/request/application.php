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



namespace eapie\source\request;
use eapie\main;
use eapie\error;
class application extends main {
	
	
	
	/**
	 * 获取当前应用的数据
	 * 
	 * @param	void
	 * @return	array
	 */
	public function self_data(){
		$application = object(parent::MAIN)->api_application();
		///白名单 私密数据不能获取
		$whitelist = array(
			'application_id',
			'application_info',
			'application_name',
			'application_insert_time',
			'application_update_time', 
			);
		return cmd(array($application, $whitelist), 'arr whitelist');
	}
	
	
	
	
	const AUTHORITY_TYPE_READ 			= "application_type_read";//读取分类列表的权限
	const AUTHORITY_TYPE_ADD 			= "application_type_add";//分类添加权限
	const AUTHORITY_TYPE_REMOVE 		= "application_type_remove";//分类删除权限
	const AUTHORITY_TYPE_EDIT 			= "application_type_edit";//分类编辑权限
	
	
	const AUTHORITY_CACHE_READ			= 	"application_cache_read";//缓存信息读取
	const AUTHORITY_CACHE_CLEAR			= 	"application_cache_clear";//清理缓存权限
	
	
	const AUTHORITY_CONFIG_PAY_READ		= 	"application_config_pay_read";//支付配置读取
	const AUTHORITY_CONFIG_PAY_EDIT		= 	"application_config_pay_edit";//支付配置编辑
	const AUTHORITY_CONFIG_READ			= 	"application_config_read";//基础配置读取
	const AUTHORITY_CONFIG_EDIT			= 	"application_config_edit";//基础配置编辑
	
	
	const AUTHORITY_SLIDESHOW_ADD 		=   "application_slideshow_add";//轮播图添加
	const AUTHORITY_SLIDESHOW_READ		= 	"application_slideshow_read";//轮播图读取
	const AUTHORITY_SLIDESHOW_EDIT		= 	"application_slideshow_edit";//轮播图编辑
	const AUTHORITY_SLIDESHOW_REMOVE	= 	"application_slideshow_remove";//轮播图删除

	const AUTHORITY_COUPON_ADD 		    =   "application_coupon_add";//优惠券添加
	const AUTHORITY_COUPON_READ		    = 	"application_coupon_read";//优惠券读取
	const AUTHORITY_COUPON_EDIT		    = 	"application_coupon_edit";//优惠券编辑
	const AUTHORITY_COUPON_REMOVE	    = 	"application_coupon_remove";//优惠券删除
	
	
	
	/**
	 * 获取 推荐人积分奖励配置
	 * 
	 * @param	int		$credit_number		积分数量
	 * @param	array	$config				配置信息
	 * @param	bool	$throw				是否报错
	 */
	public function get_parent_recommend_user_credit($credit_number, $config = array(), $throw = false){
		if( empty($config) ){
			$config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("parent_recommend_user_credit"), true);
		}
		$config['user_credit_plus'] = 0;
		$config['merchant_user_credit_plus'] = 0;
		if( !empty($config['state']) ){
			//用户消费赠送积分开启中
			
			if( empty($throw) ){
				$err = object(parent::ERROR)->check( $config, 'ratio_user_credit', parent::TABLE_CONFIG, array('args'), "parent_recommend_user_credit[ratio_user_credit]" , NULL, true);
				if( !empty($err) && is_array($err)){
					if( isset($err[1]) ){
						$config['error'] = $err[1];
					}else{
						$config['error'] = "推荐人积分奖励配置异常";
					}
					return $config;//存在错误
				}
				$err = object(parent::ERROR)->check( $config, 'ratio_merchant_user_credit', parent::TABLE_CONFIG, array('args'), "parent_recommend_user_credit[ratio_merchant_user_credit]" , NULL, true);
				if( !empty($err) && is_array($err)){
					if( isset($err[1]) ){
						$config['error'] = $err[1];
					}else{
						$config['error'] = "推荐人积分奖励配置异常";
					}
					return $config;//存在错误
				}
				$err = object(parent::ERROR)->check( $config, 'algorithm', parent::TABLE_CONFIG, array('args'), "parent_recommend_user_credit[algorithm]" , NULL, true);	
				if( !empty($err) && is_array($err)){
					if( isset($err[1]) ){
						$config['error'] = $err[1];
					}else{
						$config['error'] = "推荐人积分奖励配置异常";
					}
					return $config;//存在错误
				}
			}else{
				object(parent::ERROR)->check( $config, 'ratio_user_credit', parent::TABLE_CONFIG, array('args'), "parent_recommend_user_credit[ratio_user_credit]" );
				object(parent::ERROR)->check( $config, 'ratio_merchant_user_credit', parent::TABLE_CONFIG, array('args'), "parent_recommend_user_credit[ratio_merchant_user_credit]" );
				object(parent::ERROR)->check( $config, 'algorithm', parent::TABLE_CONFIG, array('args'), "parent_recommend_user_credit[algorithm]" );
			}
			
			//获得要赠送的积分
			if( $config["algorithm"] == "ceil" ){
				$config['user_credit_plus'] = ceil($credit_number*$config['ratio_user_credit']);
			}else
			if( $config["algorithm"] == "floor" ){
				$config['user_credit_plus'] = floor($credit_number*$config['ratio_user_credit']);
			}else
			if( $config["algorithm"] == "round" ){
				$config['user_credit_plus'] = round($credit_number*$config['ratio_user_credit']);
			}
			
			if( $config["algorithm"] == "ceil" ){
				$config['merchant_user_credit_plus'] = ceil($credit_number*$config['ratio_merchant_user_credit']);
			}else
			if( $config["algorithm"] == "floor" ){
				$config['merchant_user_credit_plus'] = floor($credit_number*$config['ratio_merchant_user_credit']);
			}else
			if( $config["algorithm"] == "round" ){
				$config['merchant_user_credit_plus'] = round($credit_number*$config['ratio_merchant_user_credit']);
			}
			
			
		}else{
			$config['state'] = 0;
		}
		
		return $config;
	}
	
	
	
	
	/**
	 * 获取 用户消费赠送积分配置
	 * 
	 * @param	int			$money_fen				消费金额
	 * @param	array		$config					配置信息
	 * @return	array
	 */
	public function get_rmb_consume_user_credit($money_fen, $config, $throw = false){
		if( empty($config) ){
			$config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("rmb_consume_user_credit"), true);
		}
		$config['user_credit_plus'] = 0;
		if( !empty($config['state']) ){
			
			if( empty($throw) ){
				$err = object(parent::ERROR)->check( $config, 'ratio_credit', parent::TABLE_CONFIG, array('args'), "rmb_consume_user_credit[ratio_credit]" , NULL, true);			
				if( !empty($err) && is_array($err) ){
					if( isset($err[1]) ){
						$config['error'] = $err[1];
					}else{
						$config['error'] = "用户消费赠送积分配置异常";
					}
					return $config;//存在错误
				}
				$err = object(parent::ERROR)->check( $config, 'ratio_rmb', parent::TABLE_CONFIG, array('args'), "rmb_consume_user_credit[ratio_rmb]" , NULL, true);
				if( !empty($err) && is_array($err) ){
					if( isset($err[1]) ){
						$config['error'] = $err[1];
					}else{
						$config['error'] = "用户消费赠送积分配置异常";
					}
					return $config;//存在错误
				}
				$err = object(parent::ERROR)->check( $config, 'algorithm', parent::TABLE_CONFIG, array('args'), "rmb_consume_user_credit[algorithm]" , NULL, true);
				if( !empty($err) && is_array($err) ){
					if( isset($err[1]) ){
						$config['error'] = $err[1];
					}else{
						$config['error'] = "用户消费赠送积分配置异常";
					}
					return $config;//存在错误
				}
			}else{
				//用户消费赠送积分开启中
				object(parent::ERROR)->check( $config, 'ratio_credit', parent::TABLE_CONFIG, array('args'), "rmb_consume_user_credit[ratio_credit]" );
				object(parent::ERROR)->check( $config, 'ratio_rmb', parent::TABLE_CONFIG, array('args'), "rmb_consume_user_credit[ratio_rmb]" );
				object(parent::ERROR)->check( $config, 'algorithm', parent::TABLE_CONFIG, array('args'), "rmb_consume_user_credit[algorithm]" );
			}
			
			//获得要赠送的积分
			if( $config["algorithm"] == "ceil" ){
				$config['user_credit_plus'] = ceil(($money_fen/$config['ratio_rmb'])*$config['ratio_credit']);
			}else
			if( $config["algorithm"] == "floor" ){
				$config['user_credit_plus'] = floor(($money_fen/$config['ratio_rmb'])*$config['ratio_credit']);
			}else
			if( $config["algorithm"] == "round" ){
				$config['user_credit_plus'] = round(($money_fen/$config['ratio_rmb'])*$config['ratio_credit']);
			}
			
		}else{
			$config['state'] = 0;
		}
		
		return $config;
	}
	
	
		
	
	/**
	 * 获取支付配置
	 * $config = array(
	 * 	"money_fen"			支付或提现的钱
	 * 	"subject"			商品的标题/交易标题/订单标题/订单关键字等。
	 * 	"body"				商品描述。商品简单描述，该字段须严格按照规范传递
	 *  "attach"			附加数据。附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
	 *  "order_id"			订单ID
	 *  "standby_order_id" 	备用订单ID。当由业务订单ID和资金订单ID的时候，这里填资金订单ID
	 * )
	 * 
	 * @param	array		$data					数据
	 * @param	array		$config					配置信息
	 * @param	array		$order_json				订单配置
	 */
	public function get_pay_config($data, &$order_json, $config){
		
		if( empty($_SESSION['admin']) ){
			$_SESSION['admin'] = object(parent::TABLE_ADMIN)->find_info();
		}
		
		
		if( (isset($data['pay_method']) && $data['pay_method'] == parent::PAY_METHOD_WEIXINPAY) ||
		(isset($data['withdraw_method']) && $data['withdraw_method'] == parent::PAY_METHOD_WEIXINPAY) ){
			object(parent::ERROR)->check( $data, 'weixin_trade_type', parent::TABLE_ORDER, array('args') );
			
			//获取微信支付配置
			if( $data["weixin_trade_type"] == "APP" ){
				$weixin_trade_access = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("weixin_app_access"), true);
			}else
			if( $data["weixin_trade_type"] == "JSAPI" ){
				$weixin_trade_access = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("weixin_applet_access"), true);
				
				if( isset($data['weixin_login_openid']) ){
					object(parent::ERROR)->check( $data, 'weixin_login_openid', parent::TABLE_ORDER, array('args') );
					$weixin_trade_access['openid'] = $data['weixin_login_openid'];
				}else{
					object(parent::ERROR)->check( $data, 'weixin_login_code', parent::TABLE_ORDER, array('args') );
					$weixin_trade_access['js_code'] = $data['weixin_login_code'];
				}
				
			}else
			//公众号支付
			if( $data["weixin_trade_type"] == "MPJSAPI" ){
				$weixin_trade_access = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("weixin_mp_access"), true);
				
				//服务商模式下使用，表示判断是公众号支付
				$weixin_trade_access['MPJSAPI'] = true;
				//恢复一下
				$data["weixin_trade_type"] = "JSAPI";
				if( isset($data['weixin_login_openid']) ){
					object(parent::ERROR)->check( $data, 'weixin_login_openid', parent::TABLE_ORDER, array('args') );
					$weixin_trade_access['openid'] = $data['weixin_login_openid'];
				}else{
					object(parent::ERROR)->check( $data, 'weixin_login_code', parent::TABLE_ORDER, array('args') );
					$weixin_trade_access['js_code'] = $data['weixin_login_code'];
				}
				
			}else
			if( $data["weixin_trade_type"] == "MWEB" ){
				$weixin_trade_access = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("weixin_mp_access"), true);
			}else{
				throw new error('支付类型异常');
			}
			
			
			$weixin_pay_access = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("weixin_pay_access"), true);
			
			if( !is_array($weixin_pay_access) || !is_array($weixin_trade_access)) throw new error('支付配置异常');
			if( empty($weixin_pay_access['state']) ){
				throw new error('已关闭微信支付');
			}
			
			$pay_config = array_merge($weixin_pay_access, $weixin_trade_access);
			
			
			if( empty($config['money_fen']) ) throw new error('交易金额异常');
			
			if( isset($data['withdraw_method']) ){
				
				//微信提现需要openid  当 js_code 不存在的时候
				if( !isset($pay_config['js_code']) ){
					object(parent::ERROR)->check( $data, 'weixin_login_openid', parent::TABLE_ORDER, array('args') );
					$pay_config['openid'] = $data['weixin_login_openid'];
				}
				
				//校验用户姓名选项
				if( isset($data['weixin_realname']) ){
					$pay_config['re_user_name'] = $data['weixin_realname'];
					$pay_config['check_name'] = 'FORCE_CHECK';//强校验真实姓名
				}
				
				$pay_config["amount"] = $config["money_fen"];//费用
				
				if( !empty($_SESSION['admin']['admin_id']) && $_SESSION['admin']['admin_id'] == "admin1"){
					$pay_config["amount"] = 100;//费用测试
				}
				
				//获取随机码
				if( !empty($config['standby_order_id']) ){
					$pay_config['nonce_str'] = $config['standby_order_id'];
				}else{
					$pay_config['nonce_str'] = object(parent::PLUGIN_WEIXIN_PAY_MCHPAY)->get_nonce_str();
				}
				
				$order_json["weixin_pay_nonce_str"] = $pay_config['nonce_str'];		
				if( isset($pay_config['mch_id']) ) $order_json["weixin_pay_mch_id"] = $pay_config['mch_id'];
				if( isset($config["subject"]) ) $pay_config["desc"] = $config["subject"];
				return $pay_config;
			} else {
				
				/*参数详解：{
				 * "pay_key":"支付密匙",
				 * "mch_id":"微信支付分配的商户号",
				 * "spbill_create_ip":"该IP同在商户平台设置的IP白名单中的IP没有关联，该IP可传用户端或者服务端的IP。",
				 * "ssl_cert":"证书cert",
				 * "ssl_key":"证书key",
				 * 
				 * "service_mch_id":"服务商的微信支付分配的商户号",
				 * "service_appid":"服务商商户的APPID",
				 * }*/
				
				//获取随机码
				if( !empty($config['standby_order_id']) ){
					$pay_config['nonce_str'] = $config['standby_order_id'];
				}else{
					$pay_config['nonce_str'] = object(parent::PLUGIN_WEIXIN_PAY_UNIFIEDORDER)->get_nonce_str();
				}
				
				$order_json["weixin_pay_nonce_str"] = $pay_config['nonce_str'];
				
				if( empty($config['order_id']) ) throw new error('订单ID异常');
				
				if( isset($pay_config['mch_id']) ) $order_json["weixin_pay_mch_id"] = $pay_config['mch_id'];
				if( isset($config["subject"]) ) $pay_config["body"] = $config["subject"];
				if( isset($config["body"]) ) $pay_config["detail"] = $config["body"];
				
				$pay_config["out_trade_no"] = $config['order_id'];
				$pay_config["total_fee"] = $config['money_fen'];//费用
				//$pay_config["total_fee"] = 1;//费用测试
				
				if( !empty($_SESSION['admin']['admin_id']) && $_SESSION['admin']['admin_id'] == "admin1"){
					$pay_config["total_fee"] = 1;//费用测试
				}
				
				$pay_config['trade_type'] = $data["weixin_trade_type"];//JSAPI
				
				//pay_method
				$application = object(parent::MAIN)->api_application();
				$pay_config["notify_url"] = http(function($http) use ($application){
					$http["path"] = array();
					$http["path"][] = "index.php";
					$http["path"][] = "temp";
					$http["path"][] = "application";
					$http["path"][] = $application["application_id"];
					$http["path"][] = "data";
					$http["path"][] = "APPLICATIONORDERWEIXINPAYNOTIFYURL";
					return http($http);
				});
				
				
				/**
				 * 如果是服务商商户
				 * 
				 * $weixin_trade_access['service_appid']	"id"
				 * $weixin_trade_access['service_mch_id']	"mch_id"
				 * $weixin_trade_access['id']   	则是子商户公众账号ID  "sub_appid"
				 * $weixin_trade_access['mch_id']   子商户号 			 "sub_mch_id"
				 * $weixin_trade_access['secret']	子商户公众账号secret	 "sub_secret"
				 * $weixin_trade_access['openid']	子商户公众账号openid	 "sub_openid"
				 */ 
				if( !empty($pay_config['service_appid']) ){
					$pay_config['sub_appid'] = $pay_config['id'];
					$pay_config['sub_mch_id'] = $pay_config['mch_id'];
					$pay_config['sub_secret'] = $pay_config['secret'];
					if( !empty($pay_config['openid']) ){
						$pay_config['sub_openid'] = $pay_config['openid'];
						unset($pay_config['openid']);
					}
					$pay_config['id'] = $pay_config['service_appid'];
					$pay_config['mch_id'] = $pay_config['service_mch_id'];
				}
				
				//printexit($pay_config);
				
				$weixin_pay = object(parent::PLUGIN_WEIXIN_PAY_UNIFIEDORDER)->submit($pay_config);
				if( !empty($weixin_pay['errno']) ){
					throw new error($weixin_pay['error']);
				}
				
				return $weixin_pay["data"];
			}
			
		}else
		if( (isset($data['pay_method']) && $data['pay_method'] == parent::PAY_METHOD_ALIPAY) ||
		(isset($data['withdraw_method']) && $data['withdraw_method'] == parent::PAY_METHOD_ALIPAY) ){
			
			
			//如果是提现
			if( isset($data['withdraw_method']) ){
				object(parent::ERROR)->check( $data, 'alipay_account', parent::TABLE_ORDER, array('args') );
				object(parent::ERROR)->check( $data, 'alipay_realname', parent::TABLE_ORDER, array('args') );
				
				//提现配置 alipay_withdraw_access 
				$pay_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("alipay_withdraw_access"), true);
				if( empty($pay_config) ){
					throw new error('支付配置异常');
				}
				if( empty($pay_config['state']) ){
					throw new error('已关闭支付宝提现');
				}
				
				//$pay_config["amount"] = 0.1;//费用测试
				$pay_config["amount"] = ($config["money_fen"]/100);//费用
				
				if( !empty($_SESSION['admin']['admin_id']) && $_SESSION['admin']['admin_id'] == "admin1"){
					$pay_config["amount"] = 0.1;//费用测试
				}
				
				$pay_config["payee_account"] = $data['alipay_account'];
				$pay_config["payee_real_name"] = $data['alipay_realname'];
				if( isset($pay_config['id']) ) $order_json["alipay_pay_app_id"] = $pay_config['id'];
				if( isset($config["subject"]) ) $pay_config["ext_param_order_title"] = $config["subject"];
				if( isset($config["body"]) ) $pay_config["remark"] = $config["body"];
				return $pay_config;
			}else{
				object(parent::ERROR)->check( $data, 'alipay_trade_type', parent::TABLE_ORDER, array('args') );
				
				$pay_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("alipay_access"), true);
				if( empty($pay_config) ){
					throw new error('支付配置异常');
				}
				if( empty($pay_config['state']) ){
					throw new error('已关闭支付宝支付');
				}
				
				//获取随机码
				if( !empty($config['standby_order_id']) ){
					$pay_config['passback_params'] = $config['standby_order_id'];
				}else{
					$pay_config['passback_params'] = object(parent::PLUGIN_ALIPAY)->get_passback_params();
				}
				
				$order_json["alipay_passback_params"] = $pay_config['passback_params'];
				if( isset($pay_config['id']) ) $order_json["alipay_pay_app_id"] = $pay_config['id'];
				
				if( empty($config['order_id']) ) throw new error('订单ID异常');
				if( isset($config["subject"]) ) $pay_config["subject"] = $config["subject"];
				
				$pay_config["out_trade_no"] = $config['order_id'];
				$pay_config["total_amount"] = ($config["money_fen"]/100);//费用
				//$pay_config["total_amount"] = 0.01;//费用测试
				
				if( !empty($_SESSION['admin']['admin_id']) && $_SESSION['admin']['admin_id'] == "admin1"){
					$pay_config["total_amount"] = 0.01;//费用测试
				}
				
				//pay_method
				$application = object(parent::MAIN)->api_application();
				$pay_config["notify_url"] = http(function($http) use ($application){
					$http["path"] = array();
					$http["path"][] = "index.php";
					$http["path"][] = "temp";
					$http["path"][] = "application";
					$http["path"][] = $application["application_id"];
					$http["path"][] = "data";
					$http["path"][] = "APPLICATIONORDERALIPAYNOTIFYURL";
					return http($http);
				});
				
				//根据支付方式返回数据
				$alipay_pay = object(parent::PLUGIN_ALIPAY)->pay($pay_config, $data["alipay_trade_type"]);
				if( !empty($alipay_pay['errno']) ){
					throw new error($alipay_pay['error']);
				}
				
				return array("alipay"=>$alipay_pay["data"]);
				
			}
			
			
		}else{
			throw new error('支付方式异常');
		}
		
		//"alipay_account":"支付宝账号","alipay_realname":"真实姓名"
		
	}
	
	
	
	
	
	
	
	
	
	
	/**
	 * 上传图片到七牛云
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function qiniu_image_upload($config = array())
	{
		if (empty($config['user_id']))
			$config['user_id'] = $_SESSION['user_id'];

		if (empty($config['file_key']))
			$config['file_key'] = 'file';

		if (empty($config['qiniu_config_expires']))
			$config['qiniu_config_expires'] = 3600;//一个小时

		if (empty($config['qiniu_config_policy'])) {
			$config['qiniu_config_policy'] = array(
            	'returnBody' => '{"key":"$(key)","hash":"$(etag)","type":$(mimeType),"size":$(fsize),"name":$(fname),"bucket":"$(bucket)","width":"$(imageInfo.width)","height":"$(imageInfo.height)","format":"$(imageInfo.format)"}',
            	//'mimeLimit' =>'image/*' 限定用户上传的文件类型。
        	);
		}

        //获取配置
        $qiniu_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('qiniu_access'), true);
        if (empty($qiniu_config))
        	throw new error('上传配置异常');

        //格式化数据
        $insert_data = array();
        $insert_data['image_id'] = object(parent::TABLE_IMAGE)->get_unique_id();
        $insert_data['user_id'] = $config['user_id'];
        $insert_data['image_state'] = 1;
        $insert_data['image_update_time'] = time();
        $insert_data['image_insert_time'] = time();

        // —— 是否二进制文件 ——
        if (isset($config['binary'])) {
        	$qiniu_config['binary'] = true;
        	$qiniu_config['content'] = $config['binary'];
        } else {
        	// 检测文件信息
        	object(parent::ERROR)->check($_FILES, $config['file_key'], parent::TABLE_IMAGE, array('args'), 'upload');
	        object(parent::ERROR)->check($_FILES[$config['file_key']], 'size', parent::TABLE_IMAGE, array('args', 'empty'), 'image_size');
	        object(parent::ERROR)->check($_FILES[$config['file_key']], 'type', parent::TABLE_IMAGE, array('args', 'mime_limit'), 'image_type');

        	$qiniu_config['file_path'] = $_FILES[$config['file_key']]['tmp_name'];
        }

		$qiniu_config['key'] = $insert_data['image_id'];
        $qiniu_config['expires'] = $config['qiniu_config_expires']; 
        $qiniu_config['policy'] = $config['qiniu_config_policy'];

        // 上传到七牛云
        $upload_data = object(parent::PLUGIN_QINIU)->upload($qiniu_config);
        if (!empty($upload_data['errno']))
            throw new error($upload_data['error']);

        // —— 是否二进制文件 ——
        if (isset($config['binary'])) {
        	$insert_data['image_name'] = '二进制内容';
        } else {
        	$insert_data['image_name'] = $_FILES[$config['file_key']]['name'];
        	$insert_data['image_path'] = $qiniu_config['bucket'];
        	$insert_data['image_hash'] = $upload_data['data']['hash'];
	        $insert_data['image_width'] = $upload_data['data']['width'];
	        $insert_data['image_height'] = $upload_data['data']['height'];
	        $insert_data['image_format'] = $upload_data['data']['format'];
        }

        //生成一个图片表数据
        if (!object(parent::TABLE_IMAGE)->insert($insert_data)){
            //删除 七牛云上传的
            $qiniu_uptoken = object(parent::PLUGIN_QINIU)->delete($qiniu_config);
            throw new error('图片登记失败');
        }

		return array(
			'image_id' => $insert_data['image_id'],
			'qiniu_config' => $qiniu_config
		);
	}
	
	
	/**
	 * 删除七牛云图片
	 * $config = array(
	 * 	"image_id" => 图片ID
	 *  "qiniu_config" => 配置信息
	 * );
	 * 
	 * @return	NULL
	 */
	public function qiniu_image_remove($config = array()){
		if( empty($config["image_id"]) ){
			return false;
		}
		
		if( empty($config["qiniu_config"]) ){
			//获取配置
            $config["qiniu_config"] = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("qiniu_access"), true);
		}
		if( empty($config["qiniu_config"]) ){
			return false;
		}
		
		//请求七牛云
        $config["qiniu_config"]['key'] = $config["image_id"];
        $qiniu_uptoken = object(parent::PLUGIN_QINIU)->delete($config["qiniu_config"]);
        if( empty($qiniu_uptoken["errno"]) ){
            //删除本地记录
            return object(parent::TABLE_IMAGE)->remove($config["image_id"]);
        }else{
        	return false;
        }
		
	}
	
	
	
	
	/**
	 * 快递100 订阅推送（订阅）
	 * 
	 * @param	string	$shipping_no	运单号
	 * @param	string	$shipping_sign	快递的类型，快递名称英文
	 * @param	array	$config			快递100的配置
	 */
	public function kuaidi100_subscription_push($shipping_no, $shipping_sign, $config = array() ){
		if( empty($config) ){
			//获取配置
            $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("kuaidi100_access"), true);  
		}
		if( empty($config) ){
			return false;
		}
		
		$application = object(parent::MAIN)->api_application();
		$notify_url = http(function($http) use ($application){
			return $http['protocol'].'://'.$http['host'].'/index.php/temp/application/'.$application["application_id"].'/data/APPLICATIONORDERKUAIDI100NOTIFYURL';
		});

		$result = object(parent::PLUGIN_KUAIDI100)->subscription_push(array(
            "conpany"=> $shipping_sign,
            "number"=> $shipping_no,
            "notify_url"=> $notify_url,
            "key"=> $config["key"],
            "customer"=> $config["customer"]
		));
		
		return $result;

	}
	
	
	
	
	
}
?>