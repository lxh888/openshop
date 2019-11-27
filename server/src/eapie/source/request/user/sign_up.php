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
class sign_up extends \eapie\source\request\user {
	
	
	/**
	 * 用户注册。
	 * $data["log_in"] 如果等于 true，那么注册成功则需要登录。
	 * $data["phone"] 手机号
	 * $data["phone_verify_key"] 手机验证码键名称
	 * $data["phone_verify_code"] 手机验证码
	 * $data["parent_id"] 推荐人ID
	 * $data["parent_phone"] 推荐人手机号
	 * $data["address"]["province"] 省
	 * $data["address"]["city"] 市
	 * $data["address"]["area"] 区\县
	 * [{"phone":"必须|手机号","password":"必须|登录密码","confirm_password":"必须|确认登录密码","phone_verify_key":"必须|手机验证码键名称，如注册sign_up、重置密码reset_password","phone_verify_code":"必须|手机验证码","parent_id":"选填|推荐人用户ID","parent_phone":"选填|推荐人手机号"}]
	 * 
	 * 
	 * @param	void
	 * @return 	string	注册成功返回注册用户的ID
	 */
	public function api($data = array()){
		//检查是否已初始化
		object(parent::REQUEST_SESSION)->check();
		//数据检测 $data["phone"] 是手机号在检测验证码的时候已经检测了
		object(parent::ERROR)->check($data, 'password', parent::TABLE_USER, array('args'), 'user_password');
		object(parent::ERROR)->check($data, 'confirm_password', parent::TABLE_USER, array('args'), 'user_confirm_password');
		
		if( $data["password"] !== $data["confirm_password"] ){
			throw new error ("两次密码输入不一致");
		}
		
		//检测验证码，这里面会检测 $data["phone"] 的合法性
		object(parent::REQUEST_SESSION)->phone_verify_code_check($data);
		
		//获取手机数据
		$user_phone_data = object(parent::TABLE_USER_PHONE)->find($data["phone"]);
		if( empty($user_phone_data) ){
			throw new error ("手机号登记数据获取异常");
		}
		//判断手机是否已经存在用户并且已经认证
		if( !empty($user_phone_data["user_id"]) && 
		empty($user_phone_data["user_phone_type"]) && 
		!empty($user_phone_data["user_phone_state"]) ){
			throw new error("手机号已经被注册，但没有设置登录权限");
		}else
		if( !empty($user_phone_data["user_id"]) && !empty($user_phone_data["user_phone_state"]) ){
			throw new error("手机号已经被注册");
		}
		
		
		//新建用户数据
		$user_insert = array(
			"user_id" => object(parent::TABLE_USER)->get_unique_id(),
		);
		
		

		//判断推荐人
		if( isset($data["parent_id"]) && !empty($data['parent_id']) ){
			object(parent::ERROR)->check($data, 'parent_id', parent::TABLE_USER, array('format', 'exists_id'), 'user_parent_id');
			$user_insert["user_parent_id"] = $data["parent_id"];
		}
		
		//判断推荐人手机号
		if( isset($data["parent_phone"]) && !empty($data['parent_phone']) ){
			object(parent::ERROR)->check($data, 'parent_phone', parent::TABLE_USER_PHONE, array('format'), 'user_parent_phone_id');
			$find_parent_phone_data = object(parent::TABLE_USER_PHONE)->find_login_data($data['parent_phone'], array("up.user_phone_id","up.user_id"));
			if( empty($find_parent_phone_data["user_id"]) ){
				throw new error("推荐人手机号码有误，该推荐人数据不存在");
			}
			
			$user_insert["user_parent_id"] = $find_parent_phone_data["user_id"];
		}
		
		
		//获得用户密码
		$user_insert["user_left_password"] = md5($data['password'].$user_insert['user_id']);
		$user_insert["user_right_password"] = md5($user_insert['user_id'].$data['password']);
			
		$user_insert['user_register_time'] = time();
		$user_insert['user_update_time'] = time();

		//地址数据
		if( isset($data['address']) ){
			if( empty($data['address']['province']) || !is_string($data['address']['province']) ){
				throw new error("用户所在省份不合法");
			}
			if( empty($data['address']['city']) || !is_string($data['address']['city']) ){
				throw new error("用户所在城市不合法");
			}
			if( empty($data['address']['area']) || !is_string($data['address']['area']) ){
				throw new error("用户所在区县不合法");
			}
			$user_insert['user_json'] = cmd(array(array(
				'address' => $data['address']
			)), 'json encode');
		}
		
		
		if( !object(parent::TABLE_USER)->insert($user_insert) ){
			throw new error ("用户注册失败");
		}
		
		//更新手机信息
		$update_where = array();
		$update_where[] = array("user_phone_id=[+]", $data["phone"]);
		$update_where[] = array('[and] user_phone_state=0');
		$update_data = array(
			"user_id" => $user_insert['user_id'],
			"user_phone_state" => 1,
			"user_phone_type" => 1,
			"user_phone_update_time" => time(),
		);

		

		if( !object(parent::TABLE_USER_PHONE)->update($update_where, $update_data) ){
			object(parent::TABLE_USER)->remove($user_insert['user_id']);//删除这个用户数据
			throw new error ("用户注册失败，手机登记信息更新异常");
		}

		
		//测试用，添加用户是默认商家
//		    $mch=array(
//		        'merchant_user_id'=>time(),
//                'user_id'=>$user_insert['user_id'],
//                'merchant_id'=>9,
//                'merchant_user_name'=>$data['phone'],
//                'merchant_user_state'=>1,
//                'merchant_user_info'=>$data['phone'],
//                'merchant_user_sort'=>10,
//                'merchant_user_json'=>'json'
//            );
//        (new admin_user())->mch_add_ceshi($mch);

		// 注册赠送优惠券事件
		$this->_registered_coupon_events($user_insert);

		// 注册时间
		if (!empty($user_insert['user_parent_id'])) {
			$this->_registered_events($user_insert);
		}
		
		if( !empty($data["log_in"]) ){
			//要登录，那么需要更新一下需要保存的日志数据
			$user_phone_data["user_id"] = $user_insert['user_id'];
			$user_phone_data["user_phone_state"] = 1;

			//插入登录日志
			object(parent::REQUEST_USER)->_log_in_(
				$user_insert['user_id'], 
				"phone_sign_up", 
				array("user_phone" => $user_phone_data)
			);
		}
		
		return $user_insert['user_id'];
	}

	
	/**
	 * 注册事件
	 * 
	 * @param  array $user [用户数据]
	 */
	private function _registered_events($user){
		//推荐奖励，用户钱包
		object(parent::TABLE_USER_MONEY)->recommend_reward($user);
		
		// E麦商城，邀请注册按身份发放奖金
		object(parent::TABLE_USER_CREDIT)->invite_reward_credit_by_identity($user);
		
		// E麦商城，注册赠送优惠券
		// object(parent::TABLE_USER_COUPON)->register_coupon($user);

		//快递邀请新用户--得优惠券
		object(parent::TABLE_USER_COUPON)->invitation_award($user);
	}

	/**
	 * E麦商城，注册赠送优惠券事件	【不管有无推荐人，注册都送】
	 * 
	 * @param	array	$user[用户数据]
	 */
	private function  _registered_coupon_events($user)
	{
		object(parent::TABLE_USER_COUPON)->register_coupon($user);
	}
	
	
	
	
	
	/**
	 * 邀请注册海报
	 * 
	 * USERSIGNUPSELFRECOMMENDPOSTER
	 * {"class":"user/sign_up","method":"api_self_recommend_poster"}
	 * 
	 * 请求参数：
	 *[{"type":"二维码类型:app|applet|web","weixin_applet_config 微信小程序配置(小程序码必填)":{"scene":"最大32个可见字符，只支持数字，大小写英文以及部分特殊字符","page":"默认主页，必须是已经发布的小程序存在的页面","width":"默认430，二维码的宽度，单位 px，最小 280px，最大 1280px","auto_color":"默认false，自动配置线条颜色","line_color":"auto_color 为 false 时生效，使用 rgb 设置颜色 例如 {\"r\":\"xxx\",\"g\":\"xxx\",\"b\":\"xxx\"} 十进制表示","is_hyaline":"默认false，是否需要透明底色"}}]
	 * 
	 * user_sign_up_recommend_poster 配置如：{"web":{"website_url":"http://fndswpfw.cn","poster_url":"http://qiniu.jiangyoukuaidi.eonfox.com/7d303a05dd3b8869bad0d515597891933191","copy_merge":{"poster_x":"65","poster_y":"270","qrcode_x":"0","qrcode_y":"0","qrcode_w":"260","qrcode_h":"260","pct":"100"},"ttf_text":{"size":"30","angle":"0","x":"66","y":"90","color":["91","91","91"]}}}
	 * 
	 * @param	array	$data
	 * @return image
	 */
	public function api_self_recommend_poster( $data = array() ){
		
		//检测登录
        object(parent::REQUEST_USER)->check();
		//检测数据
        if( empty($data['type']) || 
        !is_string($data['type']) || 
        !in_array($data['type'], array('app', 'applet', 'web') )){
        	throw new error('二维码类型错误');
		}
		
		//查询手机号
        $user_phone = object(parent::TABLE_USER_PHONE)->find_user_login_data($_SESSION['user_id']);
		if( empty($user_phone) ){
			throw new error('手机号异常');
		}
		$user_phone = $user_phone['user_phone_id'];
		
		
		//查询用户
        $user_info = object(parent::TABLE_USER)->find($_SESSION['user_id']);
		// $user_nickname = $user_phone;
		$user_nickname = '';
		// if( isset($user_info['user_nickname']) && $user_info['user_nickname'] != '' ){
		// 	//throw new error('缺少用户昵称');
		// 	$user_nickname = $user_info['user_nickname'];
		// }
		
		//获取字体资源
		$resource_font = object(parent::PLUGIN_RESOURCE)->get_ttf_path('msyhbd.ttf');     
		if( !empty($resource_font['errno']) ||  empty($resource_font['data']) ){
			throw new error('字体资源文件缺失');
		}
		
		$config_recommend = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("user_sign_up_recommend"), true);
		// return $config_recommend;
		//配置信息
		$config = array(
			"poster_url" => "",//海报图片的URL
			//合并参数
			"copy_merge" => array(
				"poster_x" => 0,//海报x坐标
				"poster_y" => 0,//海报y坐标
				"qrcode_x" => 0,//二维码X坐标
				"qrcode_y" => 0,//二维码Y坐标
				"qrcode_w" => 0,//二维码宽度
				"qrcode_h" => 0,//二维码高度
				"pct" => 0 //合并程度，其值范围从 0 到 100
			),
			//写入文字
			"ttf_text" => array(
				"size" => 0,//字体的尺寸
				"angle" => 0,//角度制表示的角度
				"x" => 0,//x坐标
				"y" => 0,//y坐标
				"color" => array(0, 0, 0) //字体颜色
			),
			"qrcode_size" => 7,//验证码大小
		);
		
		//二维码路径
		//获取一个临时文件路径，当程序执行完之后，该缓存文件会自动删除
		$qrcode_tempfile = object(parent::CACHE)->tempfile('user_sign_up_recommend_poster', 'png');
		if( empty($qrcode_tempfile) ){
			throw new error('临时文件创建失败');
		}
		
		if($data['type'] == 'applet'){
			if( !isset($config_recommend['applet']) ){
				throw new error('配置异常');
			}
			
			$config_temp = $config_recommend['applet'];
			
			//获取微信小程序二维码
            $config_qrcode = array();
            if( isset($data['weixin_applet_config']['scene']) ){
                $config_qrcode['scene'] = $data['weixin_applet_config']['scene'];
            } else {
                $config_qrcode['scene'] = $user_phone;
            }
            if (isset($data['weixin_applet_config']['page']))
                $config_qrcode['page'] = $data['weixin_applet_config']['page'];
            if (isset($data['weixin_applet_config']['width']))
                $config_qrcode['width'] = $data['weixin_applet_config']['width'];
            if (isset($data['weixin_applet_config']['auto_color']))
                $config_qrcode['width'] = $data['weixin_applet_config']['auto_color'];
            if (isset($data['weixin_applet_config']['line_color']))
                $config_qrcode['line_color'] = $data['weixin_applet_config']['line_color'];
            if (isset($data['weixin_applet_config']['is_hyaline']))
                $config_qrcode['is_hyaline'] = $data['weixin_applet_config']['is_hyaline'];
			$image_id = object(parent::REQUEST_USER)->weixin_applet_qrcode( $config_qrcode );
			
			//获取二维码路径
			$qiniu_nrl = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("qiniu_domain"), true);
			$request_data = object(parent::PLUGIN_HTTP_CURL)->request_get(array(
				'url' => $qiniu_nrl.$image_id,
			));
			if( !empty($request_data['errno']) ){
				throw new error($request_data['error']);
			}
			
			file_put_contents($qrcode_tempfile, $request_data['data']);
			
		}
		else if( $data['type'] == 'web' ){
			if( !isset($config_recommend['web']) ){
				throw new error('配置异常');
			}
			
			$config_temp = $config_recommend['web'];
			if( isset($config_temp['qrcode_size']) )
			$config['qrcode_size'] = $config_temp['qrcode_size'];
			
			//二维码配置参数
            $phpqrcode_config = array(
				'data' =>  $config_temp['website_url'].'/?type=sign_up_recommend&user_id='.$_SESSION['user_id'].'&user_phone='.$user_phone,
				'size' => $config['qrcode_size'],
				'path' => $qrcode_tempfile
			);
			// return $phpqrcode_config;
			// exit;
			//输出二维码
			object(parent::PLUGIN_PHPQRCODE)->output($phpqrcode_config);
		}
		else if( $data['type'] == 'app' ){
			if( !isset($config_recommend['app']) ){
				throw new error('配置异常');
			}
			
			$config_temp = $config_recommend['app'];
			if( isset($config_temp['qrcode_size']) )
			$config['qrcode_size'] = $config_temp['qrcode_size'];
			
			$res_data = array(
				'website_url' => $config_temp['website_url'],
				'type' => 'sign_up_recommend',
				'user_id' => $_SESSION['user_id'],
				'user_phone' => $user_phone
			);
			
			//二维码配置参数
            $phpqrcode_config = array(
				'data' =>  cmd(array($res_data), 'json encode'),
				'size' => $config['qrcode_size'],
				'path' => $qrcode_tempfile
			);
			
			//输出二维码
			object(parent::PLUGIN_PHPQRCODE)->output($phpqrcode_config);
		}
		
		//合并配置
		if( isset($config_temp['poster_url']) )
		$config['poster_url'] = $config_temp['poster_url'];
		if( isset($config_temp['copy_merge']['poster_x']) )
		$config['copy_merge']['poster_x'] = $config_temp['copy_merge']['poster_x'];
		if( isset($config_temp['copy_merge']['poster_y']) )
		$config['copy_merge']['poster_y'] = $config_temp['copy_merge']['poster_y'];
		if( isset($config_temp['copy_merge']['qrcode_x']) )
		$config['copy_merge']['qrcode_x'] = $config_temp['copy_merge']['qrcode_x'];
		if( isset($config_temp['copy_merge']['qrcode_y']) )
		$config['copy_merge']['qrcode_y'] = $config_temp['copy_merge']['qrcode_y'];
		if( isset($config_temp['copy_merge']['qrcode_w']) )
		$config['copy_merge']['qrcode_w'] = $config_temp['copy_merge']['qrcode_w'];
		if( isset($config_temp['copy_merge']['qrcode_h']) )
		$config['copy_merge']['qrcode_h'] = $config_temp['copy_merge']['qrcode_h'];
		if( isset($config_temp['copy_merge']['pct']) )
		$config['copy_merge']['pct'] = $config_temp['copy_merge']['pct'];
		
		if( isset($config_temp['ttf_text']['size']) )
		$config['ttf_text']['size'] = $config_temp['ttf_text']['size'];
		if( isset($config_temp['ttf_text']['angle']) )
		$config['ttf_text']['angle'] = $config_temp['ttf_text']['angle'];
		if( isset($config_temp['ttf_text']['x']) )
		$config['ttf_text']['x'] = $config_temp['ttf_text']['x'];
		if( isset($config_temp['ttf_text']['y']) )
		$config['ttf_text']['y'] = $config_temp['ttf_text']['y'];
		if( isset($config_temp['ttf_text']['color'][0]) )
		$config['ttf_text']['color'][0] = $config_temp['ttf_text']['color'][0];
		if( isset($config_temp['ttf_text']['color'][1]) )
		$config['ttf_text']['color'][1] = $config_temp['ttf_text']['color'][1];
		if( isset($config_temp['ttf_text']['color'][2]) )
		$config['ttf_text']['color'][2] = $config_temp['ttf_text']['color'][2];
		
		
		//获取海报图片类型
		if( empty($config['poster_url']) ){
			throw new error('海报图片不存在');
		}
		$dest_getimagesize = getimagesize($config['poster_url']);
		if( !empty($dest_getimagesize[2]) && $dest_getimagesize[2] == 1){
			$dest = imagecreatefromgif($config['poster_url']);
		}else
		if( !empty($dest_getimagesize[2]) && $dest_getimagesize[2] == 2){
			$dest = imagecreatefromjpeg($config['poster_url']);
		}else
		if( !empty($dest_getimagesize[2]) && $dest_getimagesize[2] == 3){
			$dest = imagecreatefrompng($config['poster_url']);
		}else{
			throw new error('海报图片类型不支持');
		}
		
		//获取二维码图片类型
		$src_getimagesize = getimagesize($qrcode_tempfile);
		if( !empty($src_getimagesize[2]) && $src_getimagesize[2] == 1){
			$src = imagecreatefromgif($qrcode_tempfile);
		}else
		if( !empty($src_getimagesize[2]) && $src_getimagesize[2] == 2){
			$src = imagecreatefromjpeg($qrcode_tempfile);
		}else
		if( !empty($src_getimagesize[2]) && $src_getimagesize[2] == 3){
			$src = imagecreatefrompng($qrcode_tempfile);
		}else{
			throw new error('二维码图片类型不支持');
		}
		
		//$dest = imagecreatefromjpeg( $config['poster_url'] );
        //$src = imagecreatefrompng( $qrcode_tempfile );
		//$src = imagecreatefromjpeg( $qrcode_tempfile );
		
		if( empty($dest) ){
			throw new error('海报资源为空');
		}

		if( empty($src) ){
			throw new error('二维码资源为空');
		}
		
		//合并图片及二维码
		imagecopymerge( $dest, $src, $config['copy_merge']['poster_x'], $config['copy_merge']['poster_y'], $config['copy_merge']['qrcode_x'], $config['copy_merge']['qrcode_y'], $config['copy_merge']['qrcode_w'], $config['copy_merge']['qrcode_h'], $config['copy_merge']['pct'] );
		
		imagettftext($dest, 
			$config['ttf_text']['size'], 
			$config['ttf_text']['angle'], 
			$config['ttf_text']['x'], 
			$config['ttf_text']['y'], 
			imagecolorallocate($dest, $config['ttf_text']['color'][0], $config['ttf_text']['color'][1], $config['ttf_text']['color'][2]), 
			$resource_font['data'], 
			$user_nickname); //写入文字
			
		header('Content-Type: image/png');
        imagepng ( $dest );
        imagedestroy ( $dest );
        imagedestroy ( $src );
        exit();
	}
	
	
	
	
	
	/**
	 * 邀请注册海报
	 * 
	 * USERSIGNUPRECOMMENDPOSTER
	 * {"class":"user/sign_up","method":"api_recommend_poster"}
	 * 
	 * 请求参数：
	 *[{"user_id":"用户ID","type":"二维码类型:app|applet|web","weixin_applet_config 微信小程序配置(小程序码必填)":{"scene":"最大32个可见字符，只支持数字，大小写英文以及部分特殊字符","page":"默认主页，必须是已经发布的小程序存在的页面","width":"默认430，二维码的宽度，单位 px，最小 280px，最大 1280px","auto_color":"默认false，自动配置线条颜色","line_color":"auto_color 为 false 时生效，使用 rgb 设置颜色 例如 {\"r\":\"xxx\",\"g\":\"xxx\",\"b\":\"xxx\"} 十进制表示","is_hyaline":"默认false，是否需要透明底色"}}]
	 * 
	 * user_sign_up_recommend_poster 配置如：{"web":{"website_url":"http://fndswpfw.cn","poster_url":"http://qiniu.jiangyoukuaidi.eonfox.com/7d303a05dd3b8869bad0d515597891933191","copy_merge":{"poster_x":"65","poster_y":"270","qrcode_x":"0","qrcode_y":"0","qrcode_w":"260","qrcode_h":"260","pct":"100"},"ttf_text":{"size":"30","angle":"0","x":"66","y":"90","color":["91","91","91"]}}}
	 * 
	 * @param	array	$data
	 * @return image
	 */
	public function api_recommend_poster( $data = array() ){
		
		//用户数据
        object(parent::ERROR)->check($data, 'user_id', parent::TABLE_USER, array('args'));
		
		//检测数据
        if( empty($data['type']) || 
        !is_string($data['type']) || 
        !in_array($data['type'], array('app', 'applet', 'web') )){
        	throw new error('二维码类型错误');
		}
		
		//查询手机号
        $user_phone = object(parent::TABLE_USER_PHONE)->find_user_login_data($data['user_id']);
		if( empty($user_phone) ){
			throw new error('手机号异常');
		}
		$user_phone = $user_phone['user_phone_id'];
		
		
		//查询用户
        $user_info = object(parent::TABLE_USER)->find($data['user_id']);
		// $user_nickname = $user_phone;
		$user_nickname = '';
		// if( isset($user_info['user_nickname']) && $user_info['user_nickname'] != '' ){
		// 	//throw new error('缺少用户昵称');
		// 	$user_nickname = $user_info['user_nickname'];
		// }
		
		//获取字体资源
		$resource_font = object(parent::PLUGIN_RESOURCE)->get_ttf_path('msyhbd.ttf');     
		if( !empty($resource_font['errno']) ||  empty($resource_font['data']) ){
			throw new error('字体资源文件缺失');
		}
		
		$config_recommend = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("user_sign_up_recommend"), true);
		// return $config_recommend;
		//配置信息
		$config = array(
			"poster_url" => "",//海报图片的URL
			//合并参数
			"copy_merge" => array(
				"poster_x" => 0,//海报x坐标
				"poster_y" => 0,//海报y坐标
				"qrcode_x" => 0,//二维码X坐标
				"qrcode_y" => 0,//二维码Y坐标
				"qrcode_w" => 0,//二维码宽度
				"qrcode_h" => 0,//二维码高度
				"pct" => 0 //合并程度，其值范围从 0 到 100
			),
			//写入文字
			"ttf_text" => array(
				"size" => 0,//字体的尺寸
				"angle" => 0,//角度制表示的角度
				"x" => 0,//x坐标
				"y" => 0,//y坐标
				"color" => array(0, 0, 0) //字体颜色
			),
			"qrcode_size" => 7,//验证码大小
		);
		
		//二维码路径
		//获取一个临时文件路径，当程序执行完之后，该缓存文件会自动删除
		$qrcode_tempfile = object(parent::CACHE)->tempfile('user_sign_up_recommend_poster', 'png');
		if( empty($qrcode_tempfile) ){
			throw new error('临时文件创建失败');
		}
		
		if($data['type'] == 'applet'){
			if( !isset($config_recommend['applet']) ){
				throw new error('配置异常');
			}
			
			$config_temp = $config_recommend['applet'];
			
			//获取微信小程序二维码
            $config_qrcode = array();
            if( isset($data['weixin_applet_config']['scene']) ){
                $config_qrcode['scene'] = $data['weixin_applet_config']['scene'];
            } else {
                $config_qrcode['scene'] = $user_phone;
            }
            if (isset($data['weixin_applet_config']['page']))
                $config_qrcode['page'] = $data['weixin_applet_config']['page'];
            if (isset($data['weixin_applet_config']['width']))
                $config_qrcode['width'] = $data['weixin_applet_config']['width'];
            if (isset($data['weixin_applet_config']['auto_color']))
                $config_qrcode['width'] = $data['weixin_applet_config']['auto_color'];
            if (isset($data['weixin_applet_config']['line_color']))
                $config_qrcode['line_color'] = $data['weixin_applet_config']['line_color'];
            if (isset($data['weixin_applet_config']['is_hyaline']))
                $config_qrcode['is_hyaline'] = $data['weixin_applet_config']['is_hyaline'];
			$image_id = object(parent::REQUEST_USER)->weixin_applet_qrcode( $config_qrcode , $user_info);
			
			//获取二维码路径
			$qiniu_nrl = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("qiniu_domain"), true);
			$request_data = object(parent::PLUGIN_HTTP_CURL)->request_get(array(
				'url' => $qiniu_nrl.$image_id,
			));
			if( !empty($request_data['errno']) ){
				throw new error($request_data['error']);
			}
			//header('Content-Type: image/png');
			//echo $request_data['data'];exit;
			file_put_contents($qrcode_tempfile, $request_data['data']);
		}
		else if( $data['type'] == 'web' ){
			if( !isset($config_recommend['web']) ){
				throw new error('配置异常');
			}
			
			$config_temp = $config_recommend['web'];
			if( isset($config_temp['qrcode_size']) )
			$config['qrcode_size'] = $config_temp['qrcode_size'];
			
			//二维码配置参数
            $phpqrcode_config = array(
				'data' =>  $config_temp['website_url'].'/?type=sign_up_recommend&user_id='.$data['user_id'].'&user_phone='.$user_phone,
				'size' => $config['qrcode_size'],
				'path' => $qrcode_tempfile
			);
			// return $phpqrcode_config;
			// exit;
			//输出二维码
			object(parent::PLUGIN_PHPQRCODE)->output($phpqrcode_config);
		}
		else if( $data['type'] == 'app' ){
			if( !isset($config_recommend['app']) ){
				throw new error('配置异常');
			}
			
			$config_temp = $config_recommend['app'];
			if( isset($config_temp['qrcode_size']) )
			$config['qrcode_size'] = $config_temp['qrcode_size'];
			
			$res_data = array(
				'website_url' => $config_temp['website_url'],
				'type' => 'sign_up_recommend',
				'user_id' => $data['user_id'],
				'user_phone' => $user_phone
			);
			
			//二维码配置参数
            $phpqrcode_config = array(
				'data' =>  cmd(array($res_data), 'json encode'),
				'size' => $config['qrcode_size'],
				'path' => $qrcode_tempfile
			);
			
			//输出二维码
			object(parent::PLUGIN_PHPQRCODE)->output($phpqrcode_config);
		}
		
		//合并配置
		if( isset($config_temp['poster_url']) )
		$config['poster_url'] = $config_temp['poster_url'];
		if( isset($config_temp['copy_merge']['poster_x']) )
		$config['copy_merge']['poster_x'] = $config_temp['copy_merge']['poster_x'];
		if( isset($config_temp['copy_merge']['poster_y']) )
		$config['copy_merge']['poster_y'] = $config_temp['copy_merge']['poster_y'];
		if( isset($config_temp['copy_merge']['qrcode_x']) )
		$config['copy_merge']['qrcode_x'] = $config_temp['copy_merge']['qrcode_x'];
		if( isset($config_temp['copy_merge']['qrcode_y']) )
		$config['copy_merge']['qrcode_y'] = $config_temp['copy_merge']['qrcode_y'];
		if( isset($config_temp['copy_merge']['qrcode_w']) )
		$config['copy_merge']['qrcode_w'] = $config_temp['copy_merge']['qrcode_w'];
		if( isset($config_temp['copy_merge']['qrcode_h']) )
		$config['copy_merge']['qrcode_h'] = $config_temp['copy_merge']['qrcode_h'];
		if( isset($config_temp['copy_merge']['pct']) )
		$config['copy_merge']['pct'] = $config_temp['copy_merge']['pct'];
		
		if( isset($config_temp['ttf_text']['size']) )
		$config['ttf_text']['size'] = $config_temp['ttf_text']['size'];
		if( isset($config_temp['ttf_text']['angle']) )
		$config['ttf_text']['angle'] = $config_temp['ttf_text']['angle'];
		if( isset($config_temp['ttf_text']['x']) )
		$config['ttf_text']['x'] = $config_temp['ttf_text']['x'];
		if( isset($config_temp['ttf_text']['y']) )
		$config['ttf_text']['y'] = $config_temp['ttf_text']['y'];
		if( isset($config_temp['ttf_text']['color'][0]) )
		$config['ttf_text']['color'][0] = $config_temp['ttf_text']['color'][0];
		if( isset($config_temp['ttf_text']['color'][1]) )
		$config['ttf_text']['color'][1] = $config_temp['ttf_text']['color'][1];
		if( isset($config_temp['ttf_text']['color'][2]) )
		$config['ttf_text']['color'][2] = $config_temp['ttf_text']['color'][2];
		
		
		//获取海报图片类型
		if( empty($config['poster_url']) ){
			throw new error('海报图片不存在');
		}
		$dest_getimagesize = getimagesize($config['poster_url']);
		if( !empty($dest_getimagesize[2]) && $dest_getimagesize[2] == 1){
			$dest = imagecreatefromgif($config['poster_url']);
		}else
		if( !empty($dest_getimagesize[2]) && $dest_getimagesize[2] == 2){
			$dest = imagecreatefromjpeg($config['poster_url']);
		}else
		if( !empty($dest_getimagesize[2]) && $dest_getimagesize[2] == 3){
			$dest = imagecreatefrompng($config['poster_url']);
		}else{
			throw new error('海报图片类型不支持');
		}
		
		//获取二维码图片类型
		$src_getimagesize = getimagesize($qrcode_tempfile);
		if( !empty($src_getimagesize[2]) && $src_getimagesize[2] == 1){
			$src = imagecreatefromgif($qrcode_tempfile);
		}else
		if( !empty($src_getimagesize[2]) && $src_getimagesize[2] == 2){
			$src = imagecreatefromjpeg($qrcode_tempfile);
		}else
		if( !empty($src_getimagesize[2]) && $src_getimagesize[2] == 3){
			$src = imagecreatefrompng($qrcode_tempfile);
		}else{
			throw new error('二维码图片类型不支持');
		}
		
		//$dest = imagecreatefromjpeg( $config['poster_url'] );
        //$src = imagecreatefrompng( $qrcode_tempfile );
		//$src = imagecreatefromjpeg( $qrcode_tempfile );
		
		if( empty($dest) ){
			throw new error('海报资源为空');
		}

		if( empty($src) ){
			throw new error('二维码资源为空');
		}
		
		//合并图片及二维码
		imagecopymerge( $dest, $src, $config['copy_merge']['poster_x'], $config['copy_merge']['poster_y'], $config['copy_merge']['qrcode_x'], $config['copy_merge']['qrcode_y'], $config['copy_merge']['qrcode_w'], $config['copy_merge']['qrcode_h'], $config['copy_merge']['pct'] );
		
		imagettftext($dest, 
			$config['ttf_text']['size'], 
			$config['ttf_text']['angle'], 
			$config['ttf_text']['x'], 
			$config['ttf_text']['y'], 
			imagecolorallocate($dest, $config['ttf_text']['color'][0], $config['ttf_text']['color'][1], $config['ttf_text']['color'][2]), 
			$resource_font['data'], 
			$user_nickname); //写入文字
			
		header('Content-Type: image/png');
        imagepng ( $dest );
        imagedestroy ( $dest );
        imagedestroy ( $src );
        exit();
	}
	
	
		
	



}