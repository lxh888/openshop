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
class socket {
	
	
	
			
	
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
			case 1: $return = array('errno'=>1, 'error'=>'配置异常!'); break;
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
	 * 异步发送POST数据
	 * 
	 * $config = array(
	 * 	'host'
	 *  'port'
	 * 	'data' POST数据
	 *  'path'
	 * )
	 * 
	 */
	public function async_post( $config = array() ){
		if( empty($config['host']) ){
			return $this->_error("参数不合法");
		}
		
		//默认80端口
		if( !isset($config['port']) ){
			$config['port'] = 80;
			$host = $config['host'];
		}else{
			$host = $config['host'].":".$config['port'];
		}
		
		$fp = fsockopen( $config['host'] , $config['port'], $errno, $errstr, 30);
        if( !$fp ){
			return $this->_error("[".$errno."]fsockopen请求失败:".$errstr);
        }
		
		//非阻塞
		stream_set_blocking($fp, 0);
			
		//要发送的数据
		$post_string = '';
		$len = 0;
		if( !empty($config['data']) && is_array($config['data']) ){
			$post_string = http_build_query($config['data']);//生成请求字符串
    		$len = strlen($post_string);//字符串长度
		}
		
		//拼接header部分 
		if( empty($config['path']) ) $config['path'] = '/index.php';
		$header = "POST ".$config['path']." HTTP/1.1\r\n"; 
		$header .= "Host: ".$host."\r\n"; 
		$header .= "Content-type: application/x-www-form-urlencoded\r\n"; 
		$header .= "User-Agent: Cao.php/Async-Request\r\n"; 
		$header .= "Connection: Close\r\n"; 
		$header .= "Content-Length: ".$len."\r\n"; 
		$header .= "\r\n";
		$header .= $post_string."\r\n"; 
		fwrite($fp, $header);
		
		//输出请求结果（测试时用） 
//				$receive = ''; 
//				while (!feof($fp)) 
//				{
//					  $receive .= '<br>'.fgets($fp, 128); 
//				} 
//				echo "<br />".$receive; 
		
		fclose($fp);
		return $this->_success(true);
		
	}
	
	
	
	
	
	
	
	
	
	
}
?>