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



namespace eapie\source\plugin\weixin\pay;
class unifiedorder {
	
	
	
	/**
	 * weixin_pay_h5_access		H5支付配置键名称
	 * weixin_pay_applet_access		小程序支付配置键名称
	 * 
	 * {
	 * "appid":"微信分配的小程序ID 、微信分配的公众账号ID（企业号corpid即为此appId）", 
	 * "mch_id":"微信支付分配的商户号",
	 * "nonce_str":"随机字符串",
	 * "sign":"签名",
	 * "sign_type":"签名类型，目前支持HMAC-SHA256和MD5，默认为MD5",
	 * "body":"商品简单描述，该字段须严格按照规范传递，具体请见参数规定。 https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=4_2",
	 * "out_trade_no":"商户订单号。商户系统内部的订单号,32个字符内、可包含字母",
	 * "total_fee":"订单总金额，单位为分",
	 * "spbill_create_ip":"终端IP 必须传正确的用户端IP,支持ipv4、ipv6格式",
	 * "notify_url":"接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。",
	 * "trade_type":"H5支付的交易类型为MWEB、小程序取值如下：JSAPI",
	 * "openid":"trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。openid如何获取，可参考 ",
	 * }
	 * 
	 * 
	 * 在配置中必须要配置的
	 * {"id":"appid微信分配的小程序ID 、微信分配的公众账号ID（企业号corpid即为此appId）","secret":"Appsecret","pay_key":"支付密匙","mch_id":"微信支付分配的商户号"}
	 * 
	 * $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("weixin_pay_jsapi_access"), true);
	 * $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("weixin_oauth_applet_access"), true);
	 */
	
	
		
			
	/**
	 * 统一下单 接口链接 
	 * 
	 * @param	void
	 * @return	array
	 */
	static private $_url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
	
	
	
	
	
	
	
	
	
	/**
	 * 提交一个订单
	 * 
	 * 注意，如果是公众号支付 那么有该值
	 * $config['MPJSAPI'] = true;
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function submit( $config = array() ){
		$err = $this->_check($config);
		if( !empty($err['errno']) ){
			return $err;
		}
		//printexit($config);
		
		//白名单
		$whitelist = array(
			"sub_mch_id",//微信支付分配的子商户号
			
			//sub_appid [非必填]子商户公众账号ID。微信分配的子商户公众账号ID，如需在支付完成后获取sub_openid则此参数必传。
			//sub_openid [非必填]用户子标识。trade_type=JSAPI，此参数必传，用户在子商户appid下的唯一标识。openid和sub_openid可以选传其中之一，如果选择传sub_openid,则必须传sub_appid。下单前需要调用【网页授权获取用户信息】接口获取到用户的Openid。 
			//sub_secret 子商户公众账号secret
			
			//receipt [非必填]开发票入口开放标识。Y，传入Y时，支付成功消息和支付详情页将出现开票入口。需要在微信支付商户平台或微信公众平台开通电子发票功能，传此字段才可生效
		
			"device_info", 
			"nonce_str", 
			"body",
			"detail",
			"attach",//附加信息
			"out_trade_no",
			"fee_type",
			"total_fee",
			"spbill_create_ip",
			"time_start",
			"time_expire",
			"goods_tag",
			"notify_url",
			"trade_type",
			"product_id",
			"limit_pay",
			"openid",
			"receipt",
			"scene_info"
			);
		$app = cmd(array($config, $whitelist), 'arr whitelist');
		$app['appid'] = $config["id"];
		$app['mch_id'] = $config["mch_id"];
		if( empty($app['nonce_str']) ){
			$app['nonce_str'] = $this->get_nonce_str();
		}
		
		if($app['trade_type'] == "JSAPI"){
			
			//微信支付接口境内商户版、境内服务商版模式判断
			//sub_appid [非必填]子商户公众账号ID。微信分配的子商户公众账号ID，如需在支付完成后获取sub_openid则此参数必传。
			//sub_openid [非必填]用户子标识。trade_type=JSAPI，此参数必传，用户在子商户appid下的唯一标识。openid和sub_openid可以选传其中之一，如果选择传sub_openid,则必须传sub_appid。下单前需要调用【网页授权获取用户信息】接口获取到用户的Openid。 
			if( !empty($config['sub_appid']) ){
				$app['sub_appid'] = $config['sub_appid'];
				if( empty($config['sub_openid']) ){
					$app['sub_openid'] = $this->_get_sub_openid($config);
					if( !empty($app['sub_openid']['errno'])){
						return $app['sub_openid'];//如果是错误信息
					}
				}else{
					$app['sub_openid'] = $config['sub_openid'];
				}
				
			}else
			if( empty($app['openid']) ){
				$app['openid'] = $this->_get_openid($config);
				if( !empty($app['openid']['errno'])){
					return $app['openid'];//如果是错误信息
				}
			}
		}
		
		$app['sign_type'] = $this->_get_sign_type();
		$app['sign'] = $this->_get_sign($app, $config["pay_key"]);
		
		
		//printexit($app, cmd(array($app), 'json encode'));
		$curl_config = array(
			"mch_id" => $app['mch_id'],
			"url" => self::$_url,
			"xml" => $this->array_to_xml($app),
		);
		
		$response = $this->_curl_post_xml($curl_config);
		if( !empty($response['errno']) ){
			return $response;
		}
		//做安全防御。对于PHP，由于simplexml_load_string 函数的XML解析问题出现在libxml库上，所以加载实体前可以调用这样一个函数
		libxml_disable_entity_loader(true);
		$simple_xml_object = simplexml_load_string( $response["data"] , 'SimpleXMLElement', LIBXML_NOCDATA);//解析微信服务器发送过来的xml数据包
		$simple_xml_array = cmd(array($simple_xml_object), "json encode decode"); 
		
		if( $simple_xml_array["return_code"] == "FAIL"){
			return array("errno"=>110, "error" => $simple_xml_array['return_msg']);
		}
		if( !empty($simple_xml_array["result_code"]) && $simple_xml_array["result_code"] == "FAIL" ){
			return array("errno"=>110, "error" => "[".$simple_xml_array["err_code"]."]".$simple_xml_array['err_code_des']);
			//return $this->_error(7);
		}
		
		//要判断 随机码 是否匹配 不匹配则是错误定单
		/**
		 *  SimpleXMLElement Object(
            [return_code] => SUCCESS
            [return_msg] => OK
            [appid] => wx471f4de190c69a8b
            [mch_id] => 1514039211
            [nonce_str] => ehq6YB4c4Xp1tSVB
            [sign] => 7279E0B62C8CC632A73180E537337C4D
            [result_code] => SUCCESS
            [prepay_id] => wx261257475894821add8acd2e2538570681
            [trade_type] => JSAPI
        )
		*/
		$simple_xml_array["sign_type"] = $this->_get_sign_type();
		$simple_xml_array["time_stamp"] = time();
		
		if($app['trade_type'] == "JSAPI"){
			$app_sign_arr = array(
				"appId" => $simple_xml_array["appid"],
				"timeStamp" => $simple_xml_array["time_stamp"],
				"nonceStr" => $simple_xml_array["nonce_str"],
				"package" => "prepay_id=".$simple_xml_array["prepay_id"],
				"signType" => $simple_xml_array["sign_type"]
			);
			
			//如果是服务商支付，那么appid 就是子级的   如果是公众号支付  则不是
			if( !empty($simple_xml_array["sub_appid"]) && empty($config['MPJSAPI']) ){
				$app_sign_arr['appId'] = $simple_xml_array["sub_appid"];
			}
			
			$simple_xml_array['pay_sign'] = $this->_get_sign($app_sign_arr, $config["pay_key"]);
		}else
		if($app['trade_type'] == "APP"){
			/*{"return_code":"SUCCESS","return_msg":"OK","appid":"wx1bf7bd77088c66f3","mch_id":"1501636111","nonce_str":"tBYL99bVfbdRS1Mv","sign":"AD790B5CA578D3A47E249AE63FE11677","result_code":"SUCCESS","prepay_id":"wx101649553597713c8c9d6f853352132128","trade_type":"APP","sign_type":"MD5","time_stamp":1554886195,"pay_sign":"92970e13ce59161c52ca0206937e2ec4"}*/
			$app_sign_arr = array(
				"appid" => $simple_xml_array["appid"],
				"partnerid" => $simple_xml_array['mch_id'],
				"package" => "Sign=WXPay",
				"prepayid" => $simple_xml_array["prepay_id"],
				"noncestr" => $simple_xml_array["nonce_str"],
				"timestamp" => $simple_xml_array["time_stamp"]
			);
			
			//如果是服务商支付，那么appid
			if( !empty($simple_xml_array["sub_appid"]) ){
				$app_sign_arr['appid'] = $simple_xml_array["sub_appid"];
			}
			
			$simple_xml_array['pay_sign'] = $this->_get_sign($app_sign_arr, $config["pay_key"]);
			$simple_xml_array['debug'] = "这是APP支付";
		}
		
		return $this->_success($simple_xml_array);
	}
	
	
	
	/**
	 * 检查签名
	 * 
	 * @param	array	$input_get_array
	 * @param	array	$config
	 * @return	bool
	 */
	public function check_sign($input_get_array = array(), $config = array()){
		if( empty($input_get_array) || empty($config["pay_key"]) ){
			return false;
		}
		$sign = strtoupper($input_get_array['sign']);
		unset($input_get_array['sign']);
		$get_sign = strtoupper( $this->_get_sign($input_get_array, $config["pay_key"]) );
		
		//$bool = $sign === $get_sign? true : false;
		//file_put_contents(CACHE_PATH."/text.check_sign", cmd(array( array($input_get_array, $sign, $get_sign, $bool) ), "json encode"));
		
		if( $sign === $get_sign ){
			return true;
		}else{
			return false;
		}
		
	}
	
	
	
	
	
	
	/**
	 * 获取安全验证的随机数
	 * 
	 * @return	string
	 */
	public function get_nonce_str(){
		return cmd(array(24), 'random string');
	}
	
	
	
	
	
	
	/**
	 * 获取 xml 数据
	 * 
	 * @return	array
	 */
	public function input_get_array(){
		//ini_set('always_populate_raw_post_data', -1);
		$xml_data = file_get_contents("php://input");
		//做安全防御。对于PHP，由于simplexml_load_string 函数的XML解析问题出现在libxml库上，所以加载实体前可以调用这样一个函数
		//return array($xml_data);
		libxml_disable_entity_loader(true);
		$simple_xml_object = simplexml_load_string( $xml_data, 'SimpleXMLElement',  LIBXML_NOCDATA );
		$simple_xml_array = array();
		if( is_object($simple_xml_object) ){
			$simple_xml_array = cmd(array($simple_xml_object), "json encode decode"); 
		}
		return $simple_xml_array;
	}
	
	
	
	
	
	/**
	 * 回调错误信息
	 * 
	 * @param	string	$error		错误信息
	 * @return	exit
	 */
	public function notify_url_error($error = ""){
		if( $error == "" ) $error = "未知错误";
		
		echo '<xml> 
			<return_code><![CDATA[FAIL]]></return_code>
			<return_msg><![CDATA['.$error.']]></return_msg>
			</xml>';
			exit;
	}
	
	
	
	
	/**
	 * 回调成功信息
	 * 
	 * @param	void
	 * @return	exit
	 */
	public function notify_url_success(){
		echo '<xml> 
			<return_code><![CDATA[SUCCESS]]></return_code>
			<return_msg><![CDATA[OK]]></return_msg>
			</xml>';
			exit;
	}
	
	
	
	
	
	/**
	 * 数组转xml字符
	 * 
	 * @param	array	$data
	 * @return  string 	xml字符串
	 */
	public function array_to_xml($data){
		if(!is_array($data) || count($data) <= 0){
			return false;
		}
		$xml = "<xml>";
		foreach ($data as $key=>$val){
			if ( is_numeric($val) ){
				$xml.="<".$key.">".$val."</".$key.">";
			}else{
				$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
			}
		}
		$xml.="</xml>";
		return $xml; 
	}
	
	
	
	
		
	
	
	/**
	 * 检查配置
	 * 
	 * @param	array	$config
	 * @return array | bool
	 */
	private function _check($config = array()){
		if( empty($config["id"]) || !is_string($config["id"]) ){
			return $this->_error(1);
		}
		if( empty($config["secret"]) || !is_string($config["secret"]) ){
			return $this->_error(2);
		}
		if( empty($config["pay_key"]) || !is_string($config["pay_key"]) ){
			return $this->_error(3);
		}
		if( empty($config["mch_id"]) || !is_string($config["mch_id"]) ){
			return $this->_error(4);
		}
		
	}
	
	
	
	
		
	/**
	 * 获取openid
	 * 
	 * @param WxPayConfig $config  配置对象
	 * @return string
	 */
	private function _get_sub_openid($config){
		if( empty($config["js_code"]) || !is_string($config["js_code"]) ){
			return $this->_error(5);
		}
		
		$config["url"] = "https://api.weixin.qq.com/sns/jscode2session?appid=".$config['sub_appid'].
		"&secret=".$config["sub_secret"].
		"&js_code=".$config["js_code"].
		"&grant_type=authorization_code";
		
		$response = $this->_curl_get($config);
		if( !empty($response['errno']) ){
			return $response;
		}
		$res_arr = cmd(array($response['data']), "json decode");
		if( empty($res_arr["openid"]) ){
			return $this->_error(6);
		}
		
		return $res_arr["openid"];
	}
		
	
	
	
		
	/**
	 * 获取openid
	 * 
	 * @param WxPayConfig $config  配置对象
	 * @return string
	 */
	private function _get_openid($config){
		if( empty($config["js_code"]) || !is_string($config["js_code"]) ){
			return $this->_error(5);
		}
		
		$config["url"] = "https://api.weixin.qq.com/sns/jscode2session?appid=".$config['id'].
		"&secret=".$config["secret"].
		"&js_code=".$config["js_code"].
		"&grant_type=authorization_code";
		
		$response = $this->_curl_get($config);
		if( !empty($response['errno']) ){
			return $response;
		}
		$res_arr = cmd(array($response['data']), "json decode");
		if( empty($res_arr["openid"]) ){
			return $this->_error(6);
		}
		
		return $res_arr["openid"];
	}
	
	
	
	
	/**
	 * 获取签名类型
	 * 
	 * @param	void
	 * @return	string
	 */
	private function _get_sign_type(){
		return "MD5";
	}
	
	
		
	/**
	 * 获取签名
	 * 对数组按照键名排序，保留键名到数据的关联。本函数主要用于关联数组。 
	 * ksort 
	 * 签名算法：
	 * 对参数按照key=value的格式，并按照参数名ASCII字典序排序
	 * 
	 * @param	array	$config
	 * @param	array	$pay_key	支付KTY
	 * @return	array
	 */
	private function _get_sign( $app, $pay_key){
		$sign_type = $this->_get_sign_type();
		$sign_temp = "";
		if($sign_type == "MD5"){
			//先按照参数名ASCII字典序排序
			ksort($app);
			foreach( $app as $key => $value ){
				if($sign_temp == ""){
					$sign_temp .= $key."=".$value;
				}else{
					$sign_temp .= "&".$key."=".$value;
				}
			}
			$sign_temp .= "&key=".$pay_key;
		}
		
		return md5($sign_temp);
	}
	
	
	
	
	
	
	/**
	 * 错误提示
	 * 
	 * @param	int			$errno		错误码
	 * @return	array
	 */
	private function _error( $errno = 1 ){
		if( !is_numeric($errno) ){
			return array('errno'=>110, 'error'=> $errno);//不是数字，那么就是错误信息
		}
		$return = array();
		switch( $errno ){
			case 1: $return = array('errno'=>1, 'error'=>'App ID 配置异常!'); break;
			case 2: $return = array('errno'=>2, 'error'=>'App Secret 配置异常!'); break;
			case 3: $return = array('errno'=>3, 'error'=>'Pay Key 配置异常!'); break;
			case 4: $return = array('errno'=>4, 'error'=>'商户号配置异常!'); break;
			case 5: $return = array('errno'=>5, 'error'=>'微信临时登录凭证js_code异常!'); break;
			case 6: $return = array('errno'=>6, 'error'=>'Open ID获取失败!'); break;
			case 7: $return = array('errno'=>7, 'error'=>'微信支付业务结果FAIL!'); break;
			case 8: $return = array('errno'=>8, 'error'=>'SSL Cert 配置错误!'); break;
			case 9: $return = array('errno'=>9, 'error'=>'SSL Key 配置错误!'); break;
			default: $return = array('errno'=>'default', 'error'=>'未知错误'); break;
			}
		return $return;
	}
	
	
	
	/**
	 * 成功时，返回的数据
	 */
	private function _success( $data = NULL ){
		return array(
			'errno' => 0,
			'data' => $data
		);
	}
	
	
	
	

	
	
	/**
	 * 以post方式提交xml到对应的接口url
	 * 
	 * $config = array(
	 * 	"need_cert" 是否需要证书，默认不需要
	 *  "timeout_second" url执行超时时间，默认30s
	 * 	"xml" 需要post的xml数据
	 *  "url" 请求地址
	 * )
	 * 
	 * @param WxPayConfig $config  配置对象
	 * @return array
	 */
	private function _curl_get($config){
		$ch = curl_init();
		$curl_version = curl_version();
		$ua = "WXPaySDK/3.0.9 (".PHP_OS.") PHP/".PHP_VERSION." CURL/".$curl_version['version']." "
		.$config['mch_id'];
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过ssl检查项
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_URL, $config["url"]);
		// 添加header
		curl_setopt($ch, CURLOPT_HEADER, 0);
		//获取代理
		curl_setopt($ch,CURLOPT_USERAGENT, $ua);
		
		//设置超时，否则会阻塞
		$second = 30;
		if( isset($config['timeout_second']) ) $second = $config['timeout_second'];
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		if(ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')){
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,1); //加入重定向处理
		}
		curl_setopt($ch, CURLOPT_ENCODING ,'gzip'); //加入gzip解析
	    $data =  curl_exec($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
	    curl_close($ch);
		if( $errno == 0 ){
			return array('errno'=> 0, 'data' => $data);
			}else{
				return array('errno'=> $errno, 'error' => $error);
				}
	}
	
	
	
	
		
	
	/**
	 * 以post方式提交xml到对应的接口url
	 * 
	 * $config = array(
	 * 	"need_cert" 是否需要证书，默认不需要
	 *  "timeout_second" url执行超时时间，默认30s
	 * 	"xml" 需要post的xml数据
	 *  "url" 请求地址
	 * )
	 * 
	 * @param WxPayConfig $config  配置对象
	 * @return array
	 */
	private function _curl_post_xml($config) {		
		$ch = curl_init();
		$curl_version = curl_version();
		$ua = "WXPaySDK/3.0.9 (".PHP_OS.") PHP/".PHP_VERSION." CURL/".$curl_version['version']." "
		.$config['mch_id'];

		//设置超时
		$second = 30;
		if( isset($config['timeout_second']) ) $second = $config['timeout_second'];
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);

		$proxy_host = "0.0.0.0";
		$proxy_port = 0;
		if( isset($config['proxy_host']) ) $proxy_host = $config['proxy_host'];
		if( isset($config['proxy_port']) ) $proxy_port = $config['proxy_port'];
		//如果有配置代理这里就设置代理
		if($proxy_host != "0.0.0.0" && $proxy_port != 0){
			curl_setopt($ch,CURLOPT_PROXY, $proxy_host);
			curl_setopt($ch,CURLOPT_PROXYPORT, $proxy_port);
		}
		
		$url = "";
		$xml = "";
		if( isset($config['url']) ) $url = $config['url'];
		if( isset($config['xml']) ) $xml = $config['xml'];
		
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
		curl_setopt($ch,CURLOPT_USERAGENT, $ua); 
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	
		if( !empty($config['need_cert']) ){
			//设置证书
			//使用证书：cert 与 key 分别属于两个.pem文件
			//证书文件请放入服务器的非web目录下
			$ssl_cert_path = "";
			$ssl_key_path = "";
			if( isset($config['ssl_cert_path']) ) $ssl_cert_path = $config['ssl_cert_path'];
			if( isset($config['ssl_key_path']) ) $ssl_key_path = $config['ssl_key_path'];
			curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLCERT, $ssl_cert_path);
			curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLKEY, $ssl_key_path);
		}
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
		$data = curl_exec($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		curl_close($ch);
		//返回结果
		if( $errno == 0 ){
			return array('errno'=> 0, 'data' => $data);
			}else{
				return array('errno'=> $errno, 'error' => $error);
				}
	}
	
		
	
	
	
	
	


	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>