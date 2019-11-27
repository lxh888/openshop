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



namespace eapie\source\plugin\aip;
class aip {
	
	
	
	public function  __construct (){
		require_once __DIR__."/aip-speech-php-sdk-1.6.0/AipSpeech.php";
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
			case 2: $return = array('errno'=>2, 'error'=>'API Key 配置异常!'); break;
			case 3: $return = array('errno'=>3, 'error'=>'Secret Key 配置异常!'); break;
			case 4: $return = array('errno'=>4, 'error'=>'合成的文本不能为空!'); break;
			case 4: $return = array('errno'=>4, 'error'=>'合成的文本不是字符串!'); break;
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
	 * 检查配置
	 * 
	 * @param	array	$config
	 * @return array | bool
	 */
	private function _check($config = array()){
		if( empty($config["id"]) || !is_string($config["id"]) ){
			return $this->_error(1);
		}
		if( empty($config["key"]) || !is_string($config["key"]) ){
			return $this->_error(2);
		}
		if( empty($config["secret"]) || !is_string($config["secret"]) ){
			return $this->_error(3);
		}
	}
	
	
	/**
	 * 合成语音
	 * 
	 * @param	string		$text
	 * @param	array		$config
	 * @return	array
	 */
	public function synthesis($text = "", $config){
		$err = $this->_check($config);
		if( !empty($err['errno']) ){
			return $err;
		}

		//检测语音内容
		if (empty($text))
			return $this->_error(4);

		if (!is_string($text))
			return $this->_error(5);


		$lang = isset($config["lang"]) && is_string($config["lang"])? $config["lang"] : "zh";
		$ctp = isset($config["ctp"]) && is_numeric($config["ctp"])? $config["ctp"] : 1;
		
		$options = array();
		if( isset($config["cuid"]) ){
			//用户唯一标识，用来区分用户，填写机器 MAC 地址或 IMEI 码，长度为60以内
			$options["cuid"] = $config["cuid"];
		}
		//语速，取值0-9，默认为5中语速
		$options['spd'] = isset($config["spd"]) && is_numeric($config["spd"])? $config["spd"] : 6;
		//音调，取值0-9，默认为5中语调
		$options['pit'] = isset($config["pit"]) && is_numeric($config["pit"])? $config["pit"] : 6;
		//音量，取值0-15，默认为5中音量
		$options['vol'] = isset($config["vol"]) && is_numeric($config["vol"])? $config["vol"] : 15;
		//发音人选择, 0为女声，1为男声，3为情感合成-度逍遥，4为情感合成-度丫丫，默认为普通女
		$options['per'] = isset($config["per"]) && is_numeric($config["per"])? $config["per"] : 0;


		//调用SDK
		$client = new \AipSpeech($config["id"], $config["key"], $config["secret"]);
		$result = $client->synthesis($text, 'zh', 1, $options);
		/*array(
			//'cuid' => '',//用户唯一标识，用来区分用户，填写机器 MAC 地址或 IMEI 码，长度为60以内
			'spd' => 6,//语速，取值0-9，默认为5中语速
			'pit' => 6,//音调，取值0-9，默认为5中语调
		    'vol' => 15,//音量，取值0-15，默认为5中音量
		    'per' => 0,//发音人选择, 0为女声，1为男声，3为情感合成-度逍遥，4为情感合成-度丫丫，默认为普通女
		)*/

		// 识别正确返回语音二进制 错误则返回json 参照下面错误码
		if( !is_array($result) ){
			return $this->_success($result);
		}else{
			return $this->_error("[".$result["err_no"]."]".$result["err_msg"]);
		}
		
	}
	
	
}
?>