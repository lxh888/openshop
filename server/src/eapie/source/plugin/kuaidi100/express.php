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



namespace eapie\source\plugin\kuaidi100;
class express {

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
			case 2:	$return = array('errno'=>1, 'error'=>'缺少必要的参数!'); break;
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
	 * 获取 input 数据
	 * 
	 * @return	array
	 */
	public function input_get_array(){
		//获取输入流
        $input_stream = file_get_contents('php://input');
        parse_str($input_stream, $params);
		if( !is_array($params) ){
			return false;
		}
		return $params;
	}



    /**
	 * 快递100--订阅推送
	 * https://www.kuaidi100.com/openapi/api_subscribe.shtml
	 *
	 * @param [type] $data
	 * @return void
	 */
	public function subscription_push($config = array())
	{
		$post_data = array();
		$post_data["schema"] = "json";

		if (empty($config["conpany"]) || empty($config["number"]) || empty($config["key"]) || empty($config["notify_url"])) {
			return $this->_error(2);
		}

		$param = array(
			'company' => $config["conpany"],
			'number' => $config["number"],
			'key' => $config["key"],
			'parameters' => array(
				'callbackurl' => $config["notify_url"]
			)
		);

		$post_data['param'] = cmd(array($param), 'json encode');

		$url = 'http://www.kuaidi100.com/poll';

		$o = "";
		foreach ($post_data as $k => $v) {
			$o .= "$k=" . urlencode($v) . "&";		//默认UTF-8编码格式
		}

		$post_data = substr($o, 0, -1);

		$post_require = array(
			'data' => $post_data,
			'url' => $url
		);

		$result = object('eapie\source\plugin\http\curl')->request_post($post_require);

		if ($result['errno'] == 0) {
			return $this->_success($result['data']);
		} else {
			return $this->_error($result['errno'], $result['error']);
		}
	}


	/**
	 * 快递100--实时查询
	 */
	public function real_time_query($config = array())
	{
		//参数设置
		$post_data = array();
		
		if( empty($config['conpany']) || empty($config['number']) || empty($config['key']) ){
			return $this->_error(2);
		}
		
		// 快递100分配的公司编号
		$post_data["customer"] = $config["customer"];

		// 查询的快递公司的编码， 一律用小写字母
		$company = $config['conpany'];

		// 查询的快递单号， 单号的最大长度是32个字符
		$number = $config['number'];

		// 授权码
		$key = $config['key'];

		
		$arr = array(
			'com'=>$company,
			'num'=>$number,
			'key'=>$key,
		);

		// 寄件人或收件人手机号（顺丰单号必填）
		if(isset($config['phone']) && is_string($config['phone'])){
			$arr['phone'] = $config['phone'];
		}

		// 	出发地城市
		if(isset($config['from']) && is_string($config['from'])){
			$arr['from'] = $config['from'];
		}

		// 目的地城市，到达目的地后会加大监控频率
		if(isset($config['to']) && is_string($config['to'])){
			$arr['to'] = $config['to'];
		}

		// 添加此字段表示开通行政区域解析功能
		if(isset($config['resultv2']) ){
			$arr['resultv2'] = $config['resultv2'];
		}
		
		// 拼接字段
		$post_data['param'] = cmd(array($arr), 'json encode');


		$url='http://poll.kuaidi100.com/poll/query.do';

		$post_data["sign"] = md5($post_data["param"].$key.$post_data["customer"]);

		$post_data["sign"] = strtoupper($post_data["sign"]);

		$o="";
		foreach ($post_data as $k=>$v)
		{
			$o.= "$k=".urlencode($v)."&";		//默认UTF-8编码格式
		}
		$post_data=substr($o,0,-1);




		$post_require = array(
			'data'=>$post_data,
			'url'=>$url
		);

		$result = object('eapie\source\plugin\http\curl')->request_post($post_require);

		if($result['errno'] == 0){
			return $this->_success($result['data']);
		}else{
			return $this->_error($result['errno'], $result['error']);
		}

	}

}