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



namespace eapie\source\request\user;
use eapie\main;
use eapie\error;
class phone extends \eapie\source\request\user {
	
	
	
	/**
	 * 检测用户手机号是否已经认证
	 * $data = array(
	 * 	"phone"
	 * )
	 * 
	 * USERPHONEVERIFYEXIST
	 * [{"phone":"必须|手机号"}]
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_verify_exist($data = array()){
		object(parent::ERROR)->check($data, 'phone', parent::TABLE_USER_PHONE, array('args', 'length'), 'user_phone_id');
		$find_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['phone'], array("up.user_phone_id"));
		if( empty($find_data) ){
			throw new error("手机号码未认证");
		}
		
		return true;
	}
	
	
	
		
	/**
	 * 检测用户手机号是否注册
	 * $data = array(
	 * 	"phone"
	 * )
	 * 
	 * USERPHONELOGINEXIST
	 * [{"phone":"必须|手机号"}]
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_login_exist($data = array()){
		object(parent::ERROR)->check($data, 'phone', parent::TABLE_USER_PHONE, array('args', 'length'), 'user_phone_id');
		
		$find_data = object(parent::TABLE_USER_PHONE)->find_login_data($data['phone'], array("up.user_phone_id"));
		if( empty($find_data) ){
			throw new error("手机号码未注册");
		}
		
		return true;
	}
	
	
	
	
	/**
	 * 获取用户的一个半隐藏的登录手机号码
	 * 根据用户ID获取登录手机数据，并且手机数据是中间隐藏的
	 * $data = array(
	 * 	"user_id"
	 * )
	 * 
	 * USERPHONELOGINHALFHIDDEN
	 * [{"user_id":"必须|用户ID,注意，如果该用户ID没有绑定登录手机号或者用户不存在，都返回错误信息"}]
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_login_half_hidden($data = array()){
		object(parent::ERROR)->check($data, 'user_id', parent::TABLE_USER, array('args') );
		
		//获取用户手机号
		$phone_data = object(parent::TABLE_USER_PHONE)->find_user_login_data($data['user_id']);
		if( !isset($phone_data["user_phone_id"]) ){
			throw new error("登录手机号码不存在");
		}
		
		return substr($phone_data["user_phone_id"], 0, 3) . "****" .substr($phone_data["user_phone_id"], - 4);
	}
	
	
	
	
	
		
	
	/**
	 * 获取当前登录用户已认证的手机号
	 * 
	 * USERPHONESELFVERIFY
	 * 
	 * 
	 * @param	array	void
	 * @return 	array
	 */
	public function api_self_verify(){
		//检查是否已初始化
		object(parent::REQUEST_USER)->check();
		$config = array(
			"where" => array(),
			"orderby" => array(),
			"select" => array(),
		);
		
		$config["where"][] = array('user_id=[+]', (string)$_SESSION['user_id']);
		$config["where"][] = array('[and] user_phone_state=1');
		$config["orderby"][] = array("user_phone_sort", false);
		$config["orderby"][] = array("user_phone_type", true);
		$config["orderby"][] = array("user_phone_update_time", false);
		$config["orderby"][] = array("user_phone_insert_time", false);
		$config["orderby"][] = array("user_phone_id", false);
		
		
		$config["select"] = array(
			"user_phone_id",
			"user_phone_type",
			"user_phone_state",
			"user_phone_sort",
			"user_phone_update_time",
			"user_phone_insert_time"
		);
		$user_phone_data = object(parent::TABLE_USER_PHONE)->select($config);
		
		return $user_phone_data;
	}
	
	
	
	/**
	 * 获取当前登录用户未认证的手机号
	 * 手机号未认证的
	 * api_self_not_verify_phone
	 * USERPHONESELFNOTVERIFY
	 * 
	 * @param	array	void
	 * @return 	array
	 */
	public function api_self_not_verify(){
		//检查是否已初始化
		object(parent::REQUEST_USER)->check();
		$config = array(
			"where" => array(),
			"orderby" => array(),
			"select" => array(),
		);
		
		$config["where"][] = array('user_id=[+]', (string)$_SESSION['user_id']);
		$config["where"][] = array('[and] user_phone_state=0');
		$config["orderby"][] = array("user_phone_sort", false);
		$config["orderby"][] = array("user_phone_update_time", false);
		$config["orderby"][] = array("user_phone_insert_time", false);
		$config["orderby"][] = array("user_phone_id", false);
		
		$config["select"] = array(
			"user_phone_id",
			"user_phone_type",
			"user_phone_state",
			"user_phone_sort",
			"user_phone_update_time",
			"user_phone_insert_time"
		);
		$user_phone_data = object(parent::TABLE_USER_PHONE)->select($config);
		
		return $user_phone_data;
	}
	
	
	/**
	 * 微信小程序二维码——推荐
	 *
	 * api: USERPHONEQRCODERECOMMEND
	 * req: {
	 *  phone [str] [必填] [手机号]
	 * }
	 * @param  array  $input [description]
	 * @return [type]        [description]
	 */
	public function api_qrcode_recommend($input = array())
	{
		//检测数据
		object(parent::ERROR)->check($input, 'phone', parent::TABLE_USER_PHONE, array('args', 'length'), 'user_phone_id');

		//查询数据
		$data = object(parent::TABLE_USER_PHONE)->find($input['phone']);
		if (empty($data))
			throw new error('无效手机号');

		//查询微信小程序配置
		$config_applet = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('weixin_applet_access'), true);
        if (empty($config_applet))
            throw new error('微信小程序配置异常');

		//是否已缓存
		$user_phone_json = cmd(array($data['user_phone_json']), 'json decode');
		$user_phone_json = $user_phone_json ?: array();
		if (!empty($user_phone_json['user_recommend']['weixin_applet']['image_id'])
			&& !empty($user_phone_json['user_recommend']['weixin_applet']['appid'])
		) {
			$image_id = $user_phone_json['user_recommend']['weixin_applet']['image_id'];
			//是否同一小程序应用
			if ($user_phone_json['user_recommend']['weixin_applet']['appid'] === $config_applet['id']) {
				return $image_id;
			} else {
				// 删除图片
				object(parent::REQUEST_APPLICATION)->qiniu_image_remove(array('image_id' => $image_id));
			}
		}

		//获取微信小程序接口调用凭证
		$token = object(parent::PLUGIN_WEIXIN_SESSION_APPLET)->get_access_token($config_applet);
		if ($token['errno'] === 0) {
			$token = $token['data'];
		} else {
			throw new error($token['error']);
		}

		//获取微信小程序二维码
		$config_qrcode = array();
		if (isset($input['scene']))
			$config_qrcode['scene'] = $input['scene'];
		if (isset($input['page']))
			$config_qrcode['page'] = $input['page'];
		if (isset($input['width']))
			$config_qrcode['width'] = $input['width'];
		if (isset($input['auto_color']))
			$config_qrcode['width'] = $input['auto_color'];
		if (isset($input['line_color']))
			$config_qrcode['line_color'] = $input['line_color'];
		if (isset($input['is_hyaline']))
			$config_qrcode['is_hyaline'] = $input['is_hyaline'];
		$qrcode = object(parent::PLUGIN_WEIXIN_SESSION_APPLET)->get_qrcode($token, $config_qrcode);
		if ($qrcode['errno'] === 0) {
			$qrcode = $qrcode['data'];
		} else {
			throw new error($qrcode['error']);
		}

		//保存二维码到七牛云
		$response = object(parent::REQUEST_APPLICATION)->qiniu_image_upload(array('binary' => $qrcode));            
        $image_id = $response['image_id'];

		//保存二维码图片ID到数据库
		$weixin_applet = array(
			'image_id' => $image_id,
			'appid' => $config_applet['id']
		);
		if (empty($user_phone_json['user_recommend'])) {
			$user_phone_json['user_recommend'] = array();
		}
		$user_phone_json['user_recommend']['weixin_applet'] = $weixin_applet;
		$update_data = array();
		$update_data['user_phone_json'] = cmd(array($user_phone_json), 'json encode');
		$update_data['user_phone_update_time'] = time();
		object(parent::TABLE_USER_PHONE)->update(array(array('user_phone_id=[+]', $input['phone'])), $update_data);

		return $image_id;
	}


	/**
	 * 查询用户信息
	 * 
	 * api: USERPHONEGET
	 * req: {
	 *  phone [int] [必填] [手机号]
	 * }
	 * 
	 * @return array
	 */
	public function api_get($input = array())
	{
		//检测输入
		object(parent::ERROR)->check($input, 'phone', parent::TABLE_USER_PHONE, array('args', 'length'), 'user_phone_id');

		//查询数据
		$data = object(parent::TABLE_USER_PHONE)->find_join_user($input['phone']);
		if (empty($data))
			return [];

		$output = array(
			'nick' => $data['user_nickname']
		);

		return $output;
	}


	// 私有方法 =====================================




}