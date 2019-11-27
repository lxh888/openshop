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



namespace eapie\source\request\session;
use eapie\main;
use eapie\error;
class phone_verify_code extends \eapie\source\request\session {
	
	
	
	const	SEND_CONFIG = "send";//发送配置
	const	CHECK = "check";//检查
	
	
	
	/**
	 * 初始化变量
	 * 
	 * @param	string | int		$phone		手机号
	 * @return	void
	 */
	private function _init_var($phone){
		if( !isset($_SESSION['session_private']) || !is_array($_SESSION['session_private']) ){
			$_SESSION['session_private'] = array();
			}
		if( !isset($_SESSION['session_private']['verify_code']) || !is_array($_SESSION['session_private']['verify_code']) ){
			$_SESSION['session_private']['verify_code'] = array();
			}
		if( !isset($_SESSION['session_private']['verify_code']['phone_verify_code']) || 
		!is_array($_SESSION['session_private']['verify_code']['phone_verify_code']) ){
			$_SESSION['session_private']['verify_code']['phone_verify_code'] = array();
			}
		
		$phone = (string)$phone;
		if( !isset($_SESSION['session_private']['verify_code']['phone_verify_code'][$phone]) || 
		!is_array($_SESSION['session_private']['verify_code']['phone_verify_code'][$phone]) ){
			$_SESSION['session_private']['verify_code']['phone_verify_code'][$phone] = array();
			}
	}
	
	
	
	/**
	 * 初始化数据
	 * 
	 * @param	array	$user_phone_data
	 * @return	void
	 */
	private function _init_data(&$user_phone_data){
		//获得配置信息
		if( !empty($user_phone_data["user_phone_json"]) ){
			$user_phone_data["user_phone_json"] = cmd(array($user_phone_data["user_phone_json"]), "json decode");
		}else{
			$user_phone_data["user_phone_json"] = array();
		}
		
		if( empty($user_phone_data["user_phone_json"]["verify_code_send"]) || 
		!is_array($user_phone_data["user_phone_json"]["verify_code_send"]) ) 
		$user_phone_data["user_phone_json"]["verify_code_send"] = array();
		
		//储存验证码的数组
		if( empty($user_phone_data["user_phone_json"]["verify_code"]) || 
		!is_array($user_phone_data["user_phone_json"]["verify_code"]) ) 
		$user_phone_data["user_phone_json"]["verify_code"] = array();
	}
	
	
	
	/**
	 * 检查手机号的发送验证码的合法性
	 * 阿里云短信验证码 ：使用同一个签名，对同一个手机号码发送短信验证码，支持1条/分钟，5条/小时 ，累计10条/天。
	 * 阿里云的计算时间不是以00:00为准的，而是以你当天收到限制内最后一条短信的时间开始计算，24小时以后才能再次收到短信。
	 * 
	 * @param	array	$user_phone_data
	 * @return	void | throw error
	 */
	private function _verify_code_send_check(&$user_phone_data){
		
		if( !empty($user_phone_data["user_phone_json"]["verify_code_send"]) && 
		is_array($user_phone_data["user_phone_json"]["verify_code_send"]) ){
			$hour_number = 0;//收集当前小时的发送次数 5条/小时
			$day_number = 0;//收集当天的条数 10条/天
			$end_time = 0;//收集最后发送时间 
			
			//去掉非当天的发送信息
			//$day_first = cmd(array(time()), "time day_first");//获取最初时间戳
			$hour_time = 3600;//一个小时的秒数
			$day_time = 86400;//24小时的秒数
			foreach($user_phone_data["user_phone_json"]["verify_code_send"] as $key => $value){
				
				//判断是否在24小时之内的发送数据
				if( ($value + $day_time) < time() ){
					unset($user_phone_data["user_phone_json"]["verify_code_send"][$key]);//删除这个数值
					continue;
				}else{
					$day_number ++;
				}
				
				//判断是否非1小时内的发送数据
				if( ($value + $hour_time) >= time() ){
					$hour_number ++;//加上1小时秒数，大于等于 当前时间，说明是1小时内的发送数据
				}
				
				//最后时间
				$end_time = $value;
			}
			
			//判断开始时间 + 60  大于当前时间，说明是1分钟内的发送数据, 那么报错
			if( ($end_time + 60) >= time() ){
				throw new error("手机验证码发送失败，操作太频繁，一分钟后再尝试");
			}
			//10条/天。
			if( $day_number >= 10 ){
				throw new error("手机验证码发送失败，操作太频繁，一天后再尝试");
			}
			//5条/小时。
			if( $hour_number >= 10 ){
				throw new error("手机验证码发送失败，操作太频繁，一小时后再尝试");
			}
			
		}
		
		if( empty($user_phone_data["user_phone_json"]["verify_code_send"]) || 
		!is_array($user_phone_data["user_phone_json"]["verify_code_send"]) ){
			$user_phone_data["user_phone_json"]["verify_code_send"] = array();
		}else{
			//重置索引
			$user_phone_data["user_phone_json"]["verify_code_send"] = array_values($user_phone_data["user_phone_json"]["verify_code_send"]);
		}
		
		//添加当前操作数据的时间
		$user_phone_data["user_phone_json"]["verify_code_send"][] = time();
		
	}
	
	
	
	
	
	/**
	 * 发送手机验证码
	 * 手机号如果不存在，那么创建手机号
	 * 
	 * $data['phone']				手机号
	 * $data['phone_verify_key']	键
	 * $data['phone_verify_code']	验证码
	 * 
	 * @param	string		$data			验证码的键与验证码
	 * @return 	array
	 */
	protected function _check_($data = array()){
		//检查是否已初始化
		object(parent::REQUEST_SESSION)->check();
		object(parent::ERROR)->check($data, 'phone', parent::TABLE_USER_PHONE, array('args', 'length'), 'user_phone_id');
		object(parent::ERROR)->check($data, 'phone_verify_key', parent::TABLE_USER_PHONE, array('args'));
		object(parent::ERROR)->check($data, 'phone_verify_code', parent::TABLE_USER_PHONE, array('args'));
		
		//初始化变量
		$this->_init_var($data['phone']);
		
		$phone = (string)$data["phone"];
		$phone_verify_key = (string)$data["phone_verify_key"];
		
		//获取会话的验证码
		if( !isset($_SESSION['session_private']['verify_code']['phone_verify_code'][$phone]) ||
		!is_array($_SESSION['session_private']['verify_code']['phone_verify_code'][$phone]) ||
		!isset($_SESSION['session_private']['verify_code']['phone_verify_code'][$phone][$phone_verify_key]) ||
		!is_array($_SESSION['session_private']['verify_code']['phone_verify_code'][$phone][$phone_verify_key]) ||
		empty($_SESSION['session_private']['verify_code']['phone_verify_code'][$phone][$phone_verify_key][0]) ||
		empty($_SESSION['session_private']['verify_code']['phone_verify_code'][$phone][$phone_verify_key][1]) ){
			throw new error("验证码异常，请重新获取手机验证码");
		}
		
		//有效期是5分钟。加了5分钟的秒数  还小于当前时间，那么就是失效了
		if( ($_SESSION['session_private']['verify_code']['phone_verify_code'][$phone][$phone_verify_key][0]+300) < time()){
			throw new error ("手机验证码已经失效，请重新获取");
		}
		
		if( (string)$data["phone_verify_code"] !== (string)$_SESSION['session_private']['verify_code']['phone_verify_code'][$phone][$phone_verify_key][1]){
			throw new error ("手机验证码输入错误");
		}
		
		return true;
	}
	
	
	
	
	/**
	 * 发送手机验证码
	 * 手机号如果不存在，那么创建手机号
	 * 
	 * SESSIONPHONEVERIFYCODESEND
	 * [{"phone":"必须|发送验证码的手机号","phone_verify_key":"必须|手机验证码键名称，如注册sign_up、重置密码reset_password"}]
	 * 
	 * $data["phone_verify_key"]	验证码的键
	 * $data["phone"]				手机号
	 * 
	 * @param	array		$data					
	 * @return 	array
	 */
	public function api_send($data = array()){
		//检查是否已初始化
		object(parent::REQUEST_SESSION)->check();
		object(parent::ERROR)->check($data, 'phone', parent::TABLE_USER_PHONE, array('args', 'length'), "user_phone_id");
		object(parent::ERROR)->check($data, 'phone_verify_key', parent::TABLE_USER_PHONE, array('args'));
		
		$phone = (string)$data["phone"];
		$phone_verify_key = (string)$data["phone_verify_key"];
		
		$method_name = "_send_".$phone_verify_key;
		
		//判断是否存在该方式的验证码操作
		if( !method_exists($this, $method_name) ){
			throw new error("验证码键名称输入有误");
		}
		
		//获取手机数据
		$user_phone_data = object(parent::TABLE_USER_PHONE)->find($phone);
		if( empty($user_phone_data) ){
			$insert = array(
				"user_phone_id" => $phone,
				"user_id" => "",
				"user_phone_type" => 1,
				"user_phone_state" => 0,
				"user_phone_json" => "",
				"user_phone_insert_time" => time(),
				"user_phone_update_time" => time()
			);
			if( !object(parent::TABLE_USER_PHONE)->insert($insert) ){
				throw new error("手机号登记失败");
			}
			
			$user_phone_data = $insert;
		}
		
		
		$this->_init_data($user_phone_data);
		$code = cmd(array(6), "random number");//生成随机的验证码 
		$this->_init_var($phone);//初始化变量
		$_SESSION['session_private']['verify_code']['phone_verify_code'][$phone][$phone_verify_key] = array(
			time(),//有效期5分钟
			$code //验证码
		);
		
		//储存验证码
		$user_phone_data["user_phone_json"]["verify_code"][$phone_verify_key] = $code;
		
		call_user_func_array(array($this, $method_name), array(self::CHECK, $user_phone_data));
		$this->_verify_code_send_check($user_phone_data);
		
		//生成手机验证
		//获取配置
		$dysms_access = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("dysms_access"), true);
		if( empty($dysms_access) ){
			throw new error("短信权限配置异常");
		}
		$client_object = object(parent::PLUGIN_ALIYUN_DYSMS)->client($dysms_access);
		if( empty($client_object) ){
			throw new error("短信接口初始化失败");
		}else
		if( is_array($client_object) && !empty($client_object["errno"]) ){
			throw new error($client_object["error"]);
		}
		
		//获取发送配置
		$dysms_send_sign_up = call_user_func_array(array($this, $method_name), array(self::SEND_CONFIG, $user_phone_data, $phone, $code));
		$sms_response = $client_object->send($dysms_send_sign_up);
		if( !empty($sms_response["errno"]) ){
			if($sms_response["errno"] == "isv.BUSINESS_LIMIT_CONTROL"){
				throw new error("手机验证码发送太频繁，请稍后再试");
			}
			throw new error("手机验证码发送失败 （".$sms_response["error"]."）");
			//throw new error("手机验证码发送失败");
		}
		
		//更新配置
		$update_where = array();
		$update_where[] = array("user_phone_id=[+]", $phone);
		$update_data = array(
			"user_phone_json" => cmd(array($user_phone_data["user_phone_json"]), "json encode"),
			"user_phone_update_time" => time()
		);
		object(parent::TABLE_USER_PHONE)->update($update_where, $update_data);
		
		return true;
	}
	
	
	
	
	
	
	/**
	 * 用户注册
	 * 
	 * @param	const		$command
	 * @param	array		$user_phone_data
	 * @param	string		$phone				手机号
	 * @param	int			$code				验证码
	 * @return	bool
	 */
	private function _send_sign_up($command, $user_phone_data, $phone = NULL, $code = NULL){
		if(self::CHECK === $command){
			//检测
			//判断手机是否已经存在用户并且已经认证
			if( !empty($user_phone_data["user_id"]) && !empty($user_phone_data["user_phone_type"]) && !empty($user_phone_data["user_phone_state"]) ){
				throw new error("手机号已经被注册");
			}
			if( !empty($user_phone_data["user_id"]) && !empty($user_phone_data["user_phone_state"]) ){
				throw new error("手机号已经被其他用户认证");
			}
			
			
		}else
		if(self::SEND_CONFIG === $command){
			//发送配置
			$config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("dysms_send_sign_up"), true);
			if( empty($config) ){
				throw new error("手机注册的短信参数配置异常");
			}
			$config["phone_numbers"] = $phone;
			$config["template_param"] = array("code" => $code);
			return $config;
		}
		
	}
	
	
	
	
	/**
	 * 重置密码
	 * 
	 * @param	const		$command
	 * @param	array		$user_phone_data
	 * @param	string		$phone				手机号
	 * @param	int			$code				验证码
	 * @return	bool
	 */
	private function _send_reset_password($command, $user_phone_data, $phone = NULL, $code = NULL){
		if(self::CHECK === $command){
			//检测
			//判断手机是否已经存在用户并且已经认证
			if( empty($user_phone_data["user_id"]) || empty($user_phone_data["user_phone_state"]) ){
				throw new error("手机号没有注册");
			}
			//注意，只要是认证了的，都可以重置密码。并且用认证得到手机重置密码后，该手机会自动设为 登录手机号
			
		}else
		if(self::SEND_CONFIG === $command){
			//发送配置
			$config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("dysms_send_reset_password"), true);
			if( empty($config) ){
				throw new error("重置密码的短信参数配置异常");
			}
			$config["phone_numbers"] = $phone;
			$config["template_param"] = array("code" => $code);
			return $config;
		}
		
	}
	
	
	
	
	/**
	 * 重置支付密码
	 * 
	 * @param	const		$command
	 * @param	array		$user_phone_data
	 * @param	string		$phone				手机号
	 * @param	int			$code				验证码
	 * @return	bool
	 */
	private function _send_reset_pay_password($command, $user_phone_data, $phone = NULL, $code = NULL){
		if(self::CHECK === $command){
			//检测
			//判断手机是否已经存在用户并且已经认证
			if( empty($user_phone_data["user_id"]) || empty($user_phone_data["user_phone_state"]) ){
				throw new error("手机号没有注册");
			}
			//注意，只要是认证了的，都可以重置密码。并且用认证得到手机重置密码后，该手机会自动设为 登录手机号
			
		}else
		if(self::SEND_CONFIG === $command){
			//发送配置
			$config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("dysms_send_reset_pay_password"), true);
			if( empty($config) ){
				throw new error("重置支付密码的短信参数配置异常");
			}
			$config["phone_numbers"] = $phone;
			$config["template_param"] = array("code" => $code);
			return $config;
		}
		
	}
	
	
	
	
	
	
}
?>