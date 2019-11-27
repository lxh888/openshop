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
class mch_pay {
	
	
	/*企业付款*/
	
	
			
	/**
	 * 接口链接 
	 * 
	 * @param	void
	 * @return	array
	 */
	static private $_url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
	
		
	
	
	/**
	 * 提交一个订单
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function submit( $config = array() ){
		$err = $this->_check($config);
		if( !empty($err['errno']) ){
			return $err;
		}
		
		//白名单
		$whitelist = array(
			"device_info", 
			"nonce_str", 
			"partner_trade_no",
			"check_name",//NO_CHECK：不校验真实姓名  FORCE_CHECK：强校验真实姓名
			"re_user_name",
			"openid",//商户appid下，某用户的openid
			"amount",
			"spbill_create_ip",//Ip地址。该IP同在商户平台设置的IP白名单中的IP没有关联，该IP可传用户端或者服务端的IP。
			"desc",//String(100) 企业付款备注，必填。注意：备注中的敏感词会被转成字符*
			);
		$app = cmd(array($config, $whitelist), 'arr whitelist');
		if( empty($app['check_name']) ){
			$app['check_name'] = "NO_CHECK";
		}
		
		$app['mch_appid'] = $config["id"];//申请商户号的appid或商户号绑定的appid
		$app['mchid'] = $config["mch_id"];//微信支付分配的商户号
		if( empty($app['nonce_str']) ){
			$app['nonce_str'] = $this->get_nonce_str();
		}
		
		if( empty($app['openid']) ){
			$app['openid'] = $this->_get_openid($config);
			if( !empty($app['openid']['errno'])){
				return $app['openid'];//如果是错误信息
			}
		}
		
		$app['sign'] = $this->_get_sign($app, $config["pay_key"]);
		$curl_config = array(
			"mch_id" => $config["mch_id"],
			"url" => self::$_url,
			"xml" => $this->array_to_xml($app),
			"need_cert" => true
		);
		//获取地址
		$ssl_array = $this->_get_ssl_file_path($config, $config["mch_id"]);
		if( !empty($ssl_array['errno'])){
			return $ssl_array;//如果是错误信息
		}
		$curl_config = array_merge($curl_config, $ssl_array);
		
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
		}
		
		return $this->_success(array(
            	"transaction_id" => $simple_xml_array["payment_no"],
			) );
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
	 * 获取签名
	 * 对数组按照键名排序，保留键名到数据的关联。本函数主要用于关联数组。 
	 * ksort 
	 * 签名算法：
	 * 对参数按照key=value的格式，并按照参数名ASCII字典序排序
	 * 
	 * @param	array	$config
	 * @param	string	$pay_key	支付KTY
	 * @return	array
	 */
	private function _get_sign( $app, $pay_key){
		$sign_temp = "";
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
		
		return md5($sign_temp);
	}
	
	
	
	
		
	/**
	 * 获取证书文件的缓存地址
	 * 
	 * @param	WxPayConfig		$config		配置对象
	 * @param	string			$mch_id		商户ID
	 * @return	array
	 */
	private function _get_ssl_file_path($config, $mch_id){
		if( empty($config["ssl_cert"]) || !is_string($config["ssl_cert"]) ){
			return $this->_error(8);
		}
		if( empty($config["ssl_key"]) || !is_string($config["ssl_key"]) ){
			return $this->_error(9);
		}
		
		
		//获得缓存目录
		$directory = CACHE_PATH.DIRECTORY_SEPARATOR."plugin".
		DIRECTORY_SEPARATOR."weixin".
		DIRECTORY_SEPARATOR."pay".
		DIRECTORY_SEPARATOR.(string)$mch_id;
		
		
		//创建缓存目录
		if( !is_dir($directory) || !is_writable($directory) ){
			//0700 或者最高权限0777
			if( CACHE_PATH == '' || !mkdir($directory, 0777, true) ){
				return $this->_error(10);
			}
		}
		
		//生成文件名称
		$ssl_cert_path = $directory.DIRECTORY_SEPARATOR.cmd(array(24), 'random string').md5($config["ssl_cert"])."_cert.pem";
		$ssl_key_path = $directory.DIRECTORY_SEPARATOR.cmd(array(24), 'random string').md5($config["ssl_key"])."_key.pem";
		
		if( !file_put_contents($ssl_cert_path, $config["ssl_cert"], LOCK_EX) ){
			return $this->_error(11);
		}
		
		if( !file_put_contents($ssl_key_path, $config["ssl_key"], LOCK_EX) ){
			return $this->_error(12);
		}
		
		//析构
		$destruct_id = 'cache.plugin.weixin.mch_pay.clear:'.$ssl_cert_path.$ssl_key_path;
		destruct($destruct_id, true, array($ssl_cert_path, $ssl_key_path), function($ssl_cert_path, $ssl_key_path){
			//如果是文件则删除
			if( is_file($ssl_cert_path) ){
				unlink($ssl_cert_path);
			}
			if( is_file($ssl_key_path) ){
				unlink($ssl_key_path);
			}
		});
		
		return array(
			"ssl_cert_path" => $ssl_cert_path,
			"ssl_key_path" => $ssl_key_path,
		);
		
		
		//获得缓存目录
		/*$directory = CACHE_PATH.DIRECTORY_SEPARATOR."plugin".
		DIRECTORY_SEPARATOR."weixin".
		DIRECTORY_SEPARATOR."pay".
		DIRECTORY_SEPARATOR.(string)$mch_id;
		
		//生成文件名称
		$ssl_cert_path = $directory.DIRECTORY_SEPARATOR.md5($mch_id.$config["ssl_cert"]).md5($config["ssl_cert"].$mch_id)."_cert.pem";
		$ssl_key_path = $directory.DIRECTORY_SEPARATOR.md5($mch_id.$config["ssl_key"]).md5($config["ssl_key"].$mch_id)."_key.pem";
		
		//创建缓存目录
		if( !is_dir($directory) || !is_writable($directory) ){
			//0700 或者最高权限0777
			if( CACHE_PATH == '' || !mkdir($directory, 0777, true) ){
				return $this->_error(10);
			}
		}
		
		if( !is_file($ssl_cert_path) ){
			if( !file_put_contents($ssl_cert_path, $config["ssl_cert"], LOCK_EX) ){
				return $this->_error(11);
			}
		}
		if( !is_file($ssl_key_path) ){
			if( !file_put_contents($ssl_key_path, $config["ssl_key"], LOCK_EX) ){
				return $this->_error(12);
			}
		}
		
		return array(
			"ssl_cert_path" => $ssl_cert_path,
			"ssl_key_path" => $ssl_key_path,
		);*/
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
		if( empty($config["pay_key"]) || !is_string($config["pay_key"]) ){
			return $this->_error(3);
		}
		if( empty($config["mch_id"]) || !is_string($config["mch_id"]) ){
			return $this->_error(4);
		}
		
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
			case 10: $return = array('errno'=>10, 'error'=>'SSL 证书缓存目录创建失败!'); break;
			case 11: $return = array('errno'=>11, 'error'=>'SSL Cert 证书缓存文件创建失败!'); break;
			case 12: $return = array('errno'=>12, 'error'=>'SSL Key 证书缓存文件创建失败!'); break;
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
	
	
	
	
	
	
	
}
?>