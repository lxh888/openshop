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



namespace eapie\source\plugin\http;
class curl {
	
	/**
	 * 错误提示
	 * 
	 * @param	int			$errno		错误码
	 * @return	array
	 */
	private function _error( $errno = 1 , $message = ""){
		if( !is_numeric($errno) ){
			return array('errno'=>110, 'error'=> $errno);//不是数字，那么就是错误信息
		}
		if( !empty($errno) && !empty($message) ){
			return array('errno'=>$errno, 'error'=> $message);
		}
		
		$return = array();
		switch( $errno ){
			case 1: $return = array('errno'=>1, 'error'=>'URL 配置异常!'); break;
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
	 * 以get方式提交url
	 * 
	 * $config = array(
	 * 	"need_cert" 是否需要证书，默认不需要
	 *  "timeout" 执行超时时间，默认30s  设置cURL允许执行的最长秒数
	 * 	"timeout_ms" 设置cURL允许执行的最长毫秒数
	 *  "connecttimeout"  在发起连接前等待的时间，如果设置为0，则无限等待
	 *  "connecttimeout_ms" 尝试连接等待的时间，以毫秒为单位。如果设置为0，则无限等待
	 *  "url" 请求地址
	 *  "user_agent" 
	 * )
	 * 
	 * @param	array	$config		配置对象
	 * @return array
	 */
	public function request_get($config = array()){
		
		if( empty($config['url']) || !is_string($config['url']) ){
			return $this->_error(1);
		}
		
		$ch = curl_init();
		if( empty($config['user_agent']) ){
			$config['user_agent'] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:64.0) Firefox/64.0";
		}
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过ssl检查项
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_URL, $config["url"]);
		// 添加header
		curl_setopt($ch, CURLOPT_HEADER, 0);
		//获取代理
		curl_setopt($ch,CURLOPT_USERAGENT, $config['user_agent']);
		
		//设置超时，否则会阻塞
		$second = 30;
		if( isset($config['timeout']) ) $second = $config['timeout'];
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		
		if( isset($config['timeout_ms']) ){
			curl_setopt($ch, CURLOPT_TIMEOUT_MS, $config['timeout_ms']);
		}
		
		if( isset($config['connecttimeout']) ){
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $config['connecttimeout']);
		}
		if( isset($config['connecttimeout_ms']) ){
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $config['connecttimeout_ms']);
		}
		
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
			return $this->_success($data);
			}else{
				return $this->_error($errno, $error);
				}
	}
	
	
	
	
	
			
	
	/**
	 * 以post方式提交xml到对应的接口url
	 * 
	 * $config = array(
	 * 
	 * 	"need_cert" 是否需要证书，默认不需要
	 * 	//使用证书：cert 与 key 分别属于两个.pem文件
	 * 	"ssl_cert_path" 
	 *  'ssl_key_path'
	 * 
	 *  "timeout_second" url执行超时时间，默认30s
	 * 	"data" 需要post的数据
	 *  "url" 请求地址
	 * )
	 * 
	 * @param WxPayConfig $config  配置对象
	 * @return array
	 */
	public function request_post( $config = array() ) {
		if( empty($config['url']) || !is_string($config['url']) ){
			return $this->_error(1);
		}
				
		$ch = curl_init();
		if( empty($config['user_agent']) ){
			$config['user_agent'] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:64.0) Firefox/64.0";
		}

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
		
		curl_setopt($ch,CURLOPT_URL, $config['url']);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
		curl_setopt($ch,CURLOPT_USERAGENT, $config['user_agent']); 
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
		if( isset($config['data']) ) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $config['data']);
		}
		
		//运行curl
		$data = curl_exec($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		curl_close($ch);
		//返回结果
		if( $errno == 0 ){
			return $this->_success($data);
			}else{
				return $this->_error($errno, $error);
				}
	}
	
		
	
	
	/**
     * 发送短信验证码
     */
	public function send_code($url,$params=NULL,$header=NULL,$return_header=false){
		$url_parts=parse_url($url);
		
		// parse_str($url_parts['query'],$org_params);
		settype($params,'array');
		// $params=array_merge($org_params,$params);
		
		$url=
			$url_parts['scheme'].'://'.
			$url_parts['host'].
			(isset($url_parts['port'])?':'.$url_parts['port']:'').
			(isset($url_parts['path'])?$url_parts['path']:'').
			($params?'?'.http_build_query($params):'').
			(isset($url_parts['fragment'])?'#'.$url_parts['fragment']:'');

		return $this->_send($url,NULL,$header,$return_header,30);
    }
    

    //===========================================
    // 私有方法
    //===========================================


    private function _send($url,$post=NULL,$header=NULL,$return_header=false,$time_out=30){
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// if($this->CookieFile){
		// 	curl_setopt($ch, CURLOPT_COOKIEFILE, $this->CookieFile);
		// 	curl_setopt($ch, CURLOPT_COOKIEJAR, $this->CookieFile);
		// }
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	
		curl_setopt($ch, CURLOPT_TIMEOUT, $time_out);
		
		if($header){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}
		if($return_header){
			curl_setopt($ch, CURLOPT_HEADER, true);
		}
		if($post){

			if(is_array($post)){

				settype($post,'array');

				$query=http_build_query($post);
				$post=urldecode($query);

			}
			curl_setopt($ch, CURLOPT_POST, 1);   
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$res = curl_exec($ch);
		curl_close($ch);
		return $res;

	}
	
	
}
?>