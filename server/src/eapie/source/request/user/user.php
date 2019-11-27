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

class user extends \eapie\source\request\user
{

    
    /**
     * 获取当前登录用户数据
     * 
     * @param   void
     * @return  bool
     */
    public function api_self()
    {
        object(parent::REQUEST_USER)->check();
        //黑名单
        $blacklist = array('user_left_password', 'user_right_password', 'user_json');
        $data = cmd(array($_SESSION['user'], $blacklist), 'arr blacklist');
        
        return $data;
    }


    /**
     * 设置/获取，当前用户的支付宝信息
     * 请求参数为空，则是获取。请求参数不为空，则是设置
	 * [{"realname":"真实姓名","upload_payment_code":"bool值，true是上传收款码"}]
	 * 
     * USERSELFCONFIGWEIXINPAY
	 * {"class":"user/user","method":"api_self_config_weixinpay"}
     * 
     * @return array | bool
     */
    public function api_self_config_weixinpay( $data = array() ){
        //检测登录
        object(parent::REQUEST_USER)->check();

        if( !empty($data['upload_payment_code']) ){
            $response = object(parent::REQUEST_APPLICATION)->qiniu_image_upload();

        //初始化 储存键值
        if( empty($_SESSION['user']['user_json']['weixinpay']) || !is_array($_SESSION['user']['user_json']['weixinpay']) ){
            $_SESSION['user']['user_json']['weixinpay'] = array();
        }       
        
        //获取旧收款码
        if( !empty($_SESSION['user']['user_json']['weixinpay']['payment_code']) ){
            $old_payment_code = $_SESSION['user']['user_json']['weixinpay']['payment_code'];
        }

        //更新收款码
        $_SESSION['user']['user_json']['weixinpay']['payment_code'] = $response['image_id'];

        $bool = object(parent::TABLE_USER)->update_user($_SESSION['user_id'], 
				array(
					"user_json" => cmd(array($_SESSION['user']['user_json']), "json encode"),
					"user_update_time" => time()
				));
				
			if( empty($bool) ){
                //删除刚刚上传的图片
                object(parent::REQUEST_APPLICATION)->qiniu_image_remove($response);
				throw new error("操作失败");
			}else{
                //删除旧图片
                if( !empty($old_payment_code) ){
                    $response['image_id'] = $old_payment_code;
                    object(parent::REQUEST_APPLICATION)->qiniu_image_remove($response);
                }

				return true;
			}	


        }else

		if( !empty($data) ){

			//白名单
			$whitelist = array(
				"realname"
				);
			$data = cmd(array($data, $whitelist), 'arr whitelist');
			if( empty($data) ){
				throw new error("没有修改的数据");
			}
			
			if( empty($_SESSION['user']['user_json']) || !is_array($_SESSION['user']['user_json'])){
				$_SESSION['user']['user_json'] = array();
			}
			if( empty($_SESSION['user']['user_json']['weixinpay']) || !is_array($_SESSION['user']['user_json']['weixinpay']) ){
				$_SESSION['user']['user_json']['weixinpay'] = array();
			}
			$_SESSION['user']['user_json']['weixinpay']['realname'] = $data['realname'];
			$bool = object(parent::TABLE_USER)->update_user($_SESSION['user_id'], 
				array(
					"user_json" => cmd(array($_SESSION['user']['user_json']), "json encode"),
					"user_update_time" => time()
				));
				
			if( empty($bool) ){
				throw new error("操作失败");
			} else {
				return true;
			}	
				
		}else{
        	return empty($_SESSION['user']['user_json']['weixinpay']) ? array() : $_SESSION['user']['user_json']['weixinpay'];
		}
    }
    


	/**
     * 设置/获取，当前用户的支付宝信息
     * 请求参数为空，则是获取。请求参数不为空，则是设置
	 * [{"account":"支付宝账号","realname":"真实姓名","upload_payment_code":"bool值，true是上传收款码"}]
	 * 
     * USERSELFCONFIGALIPAY
	 * {"class":"user/user","method":"api_self_config_alipay"}
     * 
     * @return array | bool
     */
    public function api_self_config_alipay( $data = array() ){
        //检测登录
        object(parent::REQUEST_USER)->check();
        
        if( !empty($data['upload_payment_code']) ){
            $response = object(parent::REQUEST_APPLICATION)->qiniu_image_upload();

        //初始化 储存键值
        if( empty($_SESSION['user']['user_json']['alipay']) || !is_array($_SESSION['user']['user_json']['alipay']) ){
            $_SESSION['user']['user_json']['alipay'] = array();
        }       
        
        //获取旧收款码
        if( !empty($_SESSION['user']['user_json']['alipay']['payment_code']) ){
            $old_payment_code = $_SESSION['user']['user_json']['alipay']['payment_code'];
        }

        //更新收款码
        $_SESSION['user']['user_json']['alipay']['payment_code'] = $response['image_id'];

        $bool = object(parent::TABLE_USER)->update_user($_SESSION['user_id'], 
				array(
					"user_json" => cmd(array($_SESSION['user']['user_json']), "json encode"),
					"user_update_time" => time()
				));
				
			if( empty($bool) ){
                //删除刚刚上传的图片
                object(parent::REQUEST_APPLICATION)->qiniu_image_remove($response);
				throw new error("操作失败");
			}else{
                //删除旧图片
                if( !empty($old_payment_code) ){
                    $response['image_id'] = $old_payment_code;
                    object(parent::REQUEST_APPLICATION)->qiniu_image_remove($response);
                }

				return true;
			}	


        }else

		if( !empty($data) ){

			//白名单
			$whitelist = array(
				"account",
				"realname"
				);
			$data = cmd(array($data, $whitelist), 'arr whitelist');
			if( empty($data) ){
				throw new error("没有修改的数据");
			}
			
			if( empty($_SESSION['user']['user_json']) || !is_array($_SESSION['user']['user_json'])){
				$_SESSION['user']['user_json'] = array();
			}
			if( empty($_SESSION['user']['user_json']['alipay']) || !is_array($_SESSION['user']['user_json']['alipay']) ){
				$_SESSION['user']['user_json']['alipay'] = array();
			}
			$_SESSION['user']['user_json']['alipay']['account'] = $data['account'];
			$_SESSION['user']['user_json']['alipay']['realname'] = $data['realname'];
			$bool = object(parent::TABLE_USER)->update_user($_SESSION['user_id'], 
				array(
					"user_json" => cmd(array($_SESSION['user']['user_json']), "json encode"),
					"user_update_time" => time()
				));
				
			if( empty($bool) ){
				throw new error("操作失败");
			}else{
				return true;
			}	
				
		}else{
        	return empty($_SESSION['user']['user_json']['alipay']) ? array() : $_SESSION['user']['user_json']['alipay'];
		}
    }





	
	/**
     * 设置/获取，当前用户的银行卡, 信用卡号信息
     * 请求参数为空，则是获取。请求参数不为空，则是设置
	 * [{"account":"银行账号、卡号","realname":"真实姓名","bankname":"银行名称"}]
	 * 
     * USERSELFCONFIGBANKCARD
	 * {"class":"user/user","method":"api_self_config_bankcard"}
     * 
     * @return array | bool
     */
    public function api_self_config_bankcard( $data = array() ){
        //检测登录
        object(parent::REQUEST_USER)->check();
		if( !empty($data) ){

			//白名单
			$whitelist = array(
				"account",
				"realname",
				"bankname"
				);
			$data = cmd(array($data, $whitelist), 'arr whitelist');
			if( empty($data) ){
				throw new error("没有修改的数据");
			}
			
			if( empty($_SESSION['user']['user_json']) || !is_array($_SESSION['user']['user_json'])){
				$_SESSION['user']['user_json'] = array();
			}
			if( empty($_SESSION['user']['user_json']['bankcard']) || !is_array($_SESSION['user']['user_json']['bankcard']) ){
				$_SESSION['user']['user_json']['bankcard'] = array();
			}
			$_SESSION['user']['user_json']['bankcard']['account'] = $data['account'];
			$_SESSION['user']['user_json']['bankcard']['realname'] = $data['realname'];
			$_SESSION['user']['user_json']['bankcard']['bankname'] = $data['bankname'];
			$bool = object(parent::TABLE_USER)->update_user($_SESSION['user_id'], 
				array(
					"user_json" => cmd(array($_SESSION['user']['user_json']), "json encode"),
					"user_update_time" => time()
				));
				
			if( empty($bool) ){
				throw new error("操作失败");
			}else{
				return true;
			}	
				
		}else{
        	return empty($_SESSION['user']['user_json']['bankcard']) ? array() : $_SESSION['user']['user_json']['bankcard'];
		}
    }


	






    /**
     * 获取当前登录用户的父级信息
     * 父级的手机、昵称、姓名
     * 
     * USERSELFPARENT
     * 
     * @param   array   void
     * @return  array
     */
    public function api_self_parent()
    {
        object(parent::REQUEST_USER)->check();
        if (empty($_SESSION['user']['user_parent_id'])) {
            throw new error("没有父级");
        }

        //查询父级的基础信息
        $user_parent_data = object(parent::TABLE_USER)->find($_SESSION['user']['user_parent_id']);
        if (empty($user_parent_data)) {
            throw new error("父级ID有误，数据异常");
        }
        
        //查询父级的登录手机号
        $parent_phone_data = object(parent::TABLE_USER_PHONE)->find_user_login_data($_SESSION['user']['user_parent_id']);
        if (empty($parent_phone_data["user_phone_id"])) {
            throw new error("父级的登录手机号不合法");
        }

        $user_parent_data["user_phone"] = $parent_phone_data["user_phone_id"];
        //白名单
        $whitelist = array(
            'user_id', 
            'user_nickname', 
            'user_compellation', 
            'user_sex', 
            'user_wechat_qrcode', 
            'user_qq', 
            'user_email', 
            'user_phone'
        );
        return cmd(array($user_parent_data, $whitelist), 'arr whitelist');
    }

    /**
     * 获取当前登录用户的子级
     * 
     * api: USERSELFSONLIST
     * req: {
     *  
     * }
     * 
     * @return  array
     */
    public function api_self_son_list($input = array())
    {
        //检测登录
        object(parent::REQUEST_USER)->check();

        //查询配置
        $config = array(
            'select'  => array(),
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($input, parent::REQUEST_USER),
        );

        $subquery = object(parent::TABLE_USER_PHONE)->sql_join_verify_phone('u');
        $config['select'] = array(
            'u.user_id',
            'u.user_logo_image_id AS image_id',
            'u.user_nickname AS nickname',
            'u.user_register_time AS time',
            '('.$subquery.') as phone',
        );
        $config['where'][] = array('u.user_parent_id=[+]', $_SESSION['user_id']);
        $config['orderby'][] = array('u.user_register_time', true);

        //查询数据
        $data = object(parent::TABLE_USER)->select_page($config);

        //格式化数据
        foreach ($data['data'] as &$v) {
            $v['phone'] = $v['phone'] ?: '';
            $v['time'] = date('Y-m-d', $v['time']);
        }

        return $data;
    }

    /**
     * 获取当前登录用户的子级总数（已分享注册的人数）
     * 
     * USERSELFSONCOUNT
     * 
     * @param   array   void
     * @return  array
     */
    public function api_self_son_count()
    {
        object(parent::REQUEST_USER)->check();
        $data = object(parent::TABLE_USER)->find_son_count($_SESSION["user_id"]);
        return empty($data['count']) ? 0 : $data['count'];
    }

    /**
     * 编辑用户基本信息
     * 
     * api: USERSELFEDIT
     * req: {
     *     user_nickname        [str] [可选] [昵称]
     *     user_compellation    [str] [可选] [姓名]
     *     user_sex             [int] [可选] [性别，0|1|2]
     *     user_wechat          [str] [可选] [微信号]
     *     user_qq              [int] [可选] [QQ号]
     *     user_email           [str] [可选] [邮箱]
     * }
     * 
     * return [bol]
     */
    public function api_self_edit($input = array())
    {
        // 检测登录
        object(parent::REQUEST_USER)->check();

        $update_data = array();

        //检测数据
        if (isset($input['user_nickname'])) {
            object(parent::ERROR)->check($input, 'user_nickname', parent::TABLE_USER, array('args'));
            $update_data['user_nickname'] = $input['user_nickname'];
        }

        if (isset($input['name'])) {
            object(parent::ERROR)->check($input, 'name', parent::TABLE_USER, array('args'), 'user_name');
            $update_data['user_name'] = $input['name'];
        }

        if (isset($input['user_sex'])) {
            object(parent::ERROR)->check($input, 'user_sex', parent::TABLE_USER, array('args'));
            $update_data['user_sex'] = $input['user_sex'];
        }

        if (isset($input['user_wechat'])) {
            object(parent::ERROR)->check($input, 'user_wechat', parent::TABLE_USER, array('args'));
            $update_data['user_wechat'] = $input['user_wechat'];
        }

        if (isset($input['user_qq'])) {
            object(parent::ERROR)->check($input, 'user_qq', parent::TABLE_USER, array('args'));
            $update_data['user_qq'] = $input['user_qq'];
        }

        if (isset($input['user_email'])) {
            object(parent::ERROR)->check($input, 'user_email', parent::TABLE_USER, array('args'));
            $update_data['user_email'] = $input['user_email'];
        }

        if (isset($input['company'])) {
            object(parent::ERROR)->check($input, 'company', parent::TABLE_USER, array('args'), 'user_company');
            $update_data['user_company'] = $input['company'];
        }

        //过滤不需要更新的数据
        $original = $_SESSION['user'];
        if (!empty($update_data)) {
            foreach ($update_data as $key => $val) {
                if (isset($original[$key]) && $original[$key] == $val) {
                    unset($update_data[$key]);
                }
            }
        }
        if (empty($update_data))
            throw new error('没有需要更新的数据');

        // 更新用户信息
        $update_data['user_update_time'] = time();
        $update_where = array(array('user_id = [+]', $_SESSION['user_id']));
        if (!object(parent::TABLE_USER)->update($update_where, $update_data)) {
            throw new error ('更新失败');
        }

        return true;
    }

    /**
     * 修改用户头像
     *
     * api: USERSELFEDITLOGO
     * req: {
     *  user_logo_image_id [str] [必填] [用户头像图片ID]
     * }
     * 
     * @param  array  $input [description]
     * @return [type]        [description]
     */
    public function api_self_edit_logo($input = array())
    {
        object(parent::REQUEST_USER)->check();
        //校验数据
        object(parent::ERROR)->check($input, 'user_logo_image_id', parent::TABLE_USER, array('args'));

        //对比旧数据
        $original = $_SESSION['user'];
        if (isset($original['user_logo_image_id']) && $original['user_logo_image_id'] === $input['user_logo_image_id'])
            throw new error('请上传新头像');

        //更新数据
        $update_data['user_logo_image_id'] = $input['user_logo_image_id'];
        $update_data['user_update_time'] = time();
        $update_where = array(array('user_id=[+]', $_SESSION['user_id']));
        if (!object(parent::TABLE_USER)->update($update_where, $update_data))
            throw new error ('更新失败');

        //删除旧图片
        if (!empty($original['user_logo_image_id'])) {
            //获取配置
            $qiniu_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("qiniu_access"), true);
            if (!empty($qiniu_config)) {
                //请求七牛云
                $qiniu_config['key'] = $original['user_logo_image_id'];
                $qiniu_uptoken = object(parent::PLUGIN_QINIU)->delete($qiniu_config);
                if( empty($qiniu_uptoken["errno"]) ){
                    //删除本地记录
                    object(parent::TABLE_IMAGE)->remove($original['user_logo_image_id']);
                }
            }
        }

        return $input['user_logo_image_id'];
    }

    /**
     * 二维码——推荐二维码
     *
     * api: USERSELFQRCODERECOMMEND
     *
     * 两个参数：二维码类型，二维码配置
     * 【type，config】
     * 
     * req: {
     *  type    [str] [必填] [类别。app, applet（微信小程序）, web（h5）]
     * }
     *
     * app二维码
     * req: {
     *  level   [str] [可选] [级别,容错率,(L,M,Q,H)]
     *  size    [int] [可选] [二维码大小，默认3]
     *  padding [int] [可选] [二维码内边距,默认0]
     * }
     * @return 二维码图片ID，七牛云
     *
     * 微信小程序二维码，清除参数参数官方文档
     * req: {
     *  scene       [str] [可选] [默认当前用户手机号]
     *  page
     *  width
     *  auto_color
     *  line_color
     *  is_hyaline
     * }
     * 
     * @param  [arr] $input [请求参数]
     * @return 二维码图片
     */
    public function api_self_qrcode_recommend($type = '', $input = array()){
        //检测登录
        object(parent::REQUEST_USER)->check();
		
        //检测数据
        if (empty($type) || !in_array($type, array('app', 'applet', 'web') ))
            throw new error('二维码类型错误');

        //查询用户数据
        $user_id = $_SESSION['user_id'];
        $user_phone = '';
        $login_data = object(parent::TABLE_USER_PHONE)->find_user_login_data($user_id);
        if (!empty($login_data['user_phone_id']))
            $user_phone = $login_data['user_phone_id'];

        //是否微信小程序二维码
        if( $type === 'applet' ){
        	//获取微信小程序二维码
            $config_qrcode = array();
            if( isset($input['scene']) ){
                $config_qrcode['scene'] = $input['scene'];
            }else{
                $config_qrcode['scene'] = $user_phone;
            }
            if( isset($input['page']) )
                $config_qrcode['page'] = $input['page'];
            if( isset($input['width']) )
                $config_qrcode['width'] = $input['width'];
            if( isset($input['auto_color']) )
                $config_qrcode['width'] = $input['auto_color'];
            if( isset($input['line_color']) )
                $config_qrcode['line_color'] = $input['line_color'];
            if( isset($input['is_hyaline']) )
                $config_qrcode['is_hyaline'] = $input['is_hyaline'];
        	$image_id = object(parent::REQUEST_USER)->weixin_applet_qrcode( $config_qrcode );
			
            return $image_id;
        }

        

        //二维码配置参数
        $config = array();
        if (isset($input['level']))
            $config['level'] = $input['level'];
        if (isset($input['size']))
            $config['size'] = $input['size'];
        if (isset($input['padding']))
            $config['padding'] = $input['padding'];

        if($type === 'web'){
            $recommend_url = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('user_recommend_url'), true);
            // return $user_phone;
            $config['data']=$recommend_url['web'].'?'.'user_id='.$user_id.'&user_phone='.$user_phone;
            
        }else{
            //二维码数据
            $config['data'] = array(
                'errno' => 0,
                'type'  => 'user_recommend',
                'data'  => array(
                    'user_id'    => $user_id,
                    'user_phone' => $user_phone
                ),
            );
        }
        object(parent::PLUGIN_PHPQRCODE)->output($config);
    }




    /**
     * 当前登录用户上传文件并且更新头像
     * USERSELFLOGOUPLOAD
     * 
     * 前台以 file 键名称请求
     * {"class":"user/user","method":"api_self_logo_upload"}
     * 
     * @param  array  $data
     * @return image_id
     */
    public function api_self_logo_upload($data = array()) {
        object(parent::REQUEST_USER)->check();
		$response = object(parent::REQUEST_APPLICATION)->qiniu_image_upload();
        //对比旧数据
        $original = $_SESSION['user'];
        //更新用户头像
        $update_data['user_logo_image_id'] = $response['image_id'];
        $update_data['user_update_time'] = time();
        $update_where = array(array('user_id=[+]', $_SESSION['user_id']));
        if ( !object(parent::TABLE_USER)->update($update_where, $update_data) ){
        	object(parent::REQUEST_APPLICATION)->qiniu_image_remove($response);
            throw new error ('更新失败');
        }

        //删除旧图片
        if (!empty($original['user_logo_image_id'])) {
        	$response['image_id'] = $original['user_logo_image_id'];
        	object(parent::REQUEST_APPLICATION)->qiniu_image_remove($response);
        }

        return $update_data['user_logo_image_id'];
    }

    /**
     * 绑定微信
     *
     * api: USERSELFBINDWEIXIN
     * 
     * @param  [str] $type  [必填] [微信APP，或者微信小程序，(app,applet)]
     * @param  [str] $input [必填] [微信提供的数据]
     * @return [int]        [状态码，0未登录，1绑定成功，2该用户绑定其它unionid, 3该unionid绑定其他用户]
     */
    public function api_self_bind_weixin($type = '', $input = array())
    {
        // 检测登录
        if (!object(parent::REQUEST_USER)->check(true))
            return 0;
        
        $openID = '';
        //判断登录类型
        if ($type === 'app') {
            $wx_data = $input;
        } elseif ($type === 'applet') {
            $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('weixin_applet_access'), true);
            $config = array_merge($config, $input);
            $wx_data = object(parent::PLUGIN_WEIXIN_SESSION_APPLET)->get_userinfo($config);
            $wx_data = $wx_data['data'];
        } elseif ($type === 'web'){
            if(empty($input['access_token'])) return false;
            $access_token = $input['access_token'];
            $openID = $input['openid'];
            $url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openID&lang=zh_CN";
            $config = array(
                'url'=>$url
            );
            $res = object(parent::PLUGIN_HTTP_CURL)->request_get($config);
            if($res['errno'] !== 0){
                throw new error($res['error']);
            }
            
            $wx_userInfo = cmd(array($res['data']),'json decode');
            if(empty($wx_userInfo['nickname']) || empty($wx_userInfo['headimgurl'])){ return false;}
            $wx_data['nickName'] =  $wx_userInfo['nickname'];
            $wx_data['avatarUrl'] = $wx_userInfo['headimgurl'];
            $wx_data['unionId'] = $wx_userInfo['unionid'];
            $wx_data['sex'] = isset($wx_userInfo['sex'])?$wx_userInfo['sex']:0;
            $wx_data['openId'] = $wx_userInfo['openid'];
        } else {
            throw new error('微信登录类型错误');
        }

        if( empty($wx_data['unionId']) ){
            throw new error('微信用户信息获取异常');
        }

        //查询授权数据
        $oauth = object(parent::TABLE_USER_OAUTH)->find_platform_key('weixin', $wx_data['unionId']);
        if( $oauth )
            return $oauth['user_id'] === $_SESSION['user_id'] ? 1 : 3;

		// 判断 user_id 是否绑定了微信
		$user_oauth = object(parent::TABLE_USER_OAUTH)->find_platform_user('weixin', $_SESSION['user_id']);
		if( !empty($user_oauth) ){
			return 2;//该用户绑定其它unionid
		}	

        //保存授权信息
        $insert_data['user_oauth_id'] = object(parent::TABLE_USER_OAUTH)->get_unique_id();
        $insert_data['user_id'] = $_SESSION['user_id'];
        $insert_data['user_oauth_platform'] = 'weixin';
        $insert_data['user_oauth_wx_key'] = $openID;
        $insert_data['user_oauth_key'] = $wx_data['unionId'];
        $insert_data['user_oauth_value'] = cmd(array($wx_data), 'json encode');
        $insert_data['user_oauth_insert_time'] = time();
        $insert_data['user_oauth_update_time'] = time();
        if (object(parent::TABLE_USER_OAUTH)->insert($insert_data)) {
            //同步第三方用户信息
            $this->_update_userinfo(array(
                'nick' => $wx_data['nickName'],
                'avatar' => $wx_data['avatarUrl']
            ));
            return 1;
        } else {
            throw new error('绑定失败');
        }
    }

    /**
     * 绑定ClientID，个推专用
     * api: USERSELFBINDCLIENTID
     * req: {
     *     cid [str] [必填] [客户端ID]
     * }
     * @return bool
     */
    public function api_self_bind_clientid($input = array())
    {
        // 检测登录
        object(parent::REQUEST_USER)->check();
        $user_id = $_SESSION['user_id'];

        // 检测请求参数
        if (empty($input['cid'])) {
            throw new error('缺少cid参数');
        }
        if (!is_string($input['cid'])) {
            throw new error('cid不合法');
        }

        // 查询用户信息
        $user = object(parent::TABLE_USER)->find($user_id);
        $user_json = cmd(array($user['user_json']), 'json decode_array');
        $user_json['ClientID'] = $input['cid'];

        // 更新用户信息
        $update_data = array();
        $update_data['user_json'] = cmd(array($user_json), 'json encode');
        $update_data['user_update_time'] = time();
        $update_where = array(array('user_id = [+]', $user_id));

        return object(parent::TABLE_USER)->update($update_where, $update_data);
    }

    /**
     * 检测推荐人手机号是否合法
     * api: USEREXISTLOGINPHONECHECK
     * req: {
     *  phone [int] [必填] [手机号]
     * }
     * @param bool
     */
    public function api_exist_login_phone_check($input = array())
    {
        // 检测输入
        object(parent::ERROR)->check($input, 'phone', parent::TABLE_USER_PHONE, array('args'), 'user_phone_id');

        // 查询手机号信息
        $user_phone = object(parent::TABLE_USER_PHONE)->find($input['phone']);
        if (empty($user_phone) || $user_phone['user_phone_type'] !== '1' || $user_phone['user_phone_state'] !== '1')
            return false;

        return true;
    }


    //===========================================
    // 私有方法
    //===========================================


    /**
     * 根据第三方用户信息更新本地信息
     *
     * {
     *  nick    [str] [可选] [用户昵称]
     *  avatar  [str] [可选] [用户头像网络链接]
     * }
     * 
     * @return boolean
     */
    private function _update_userinfo($input = array())
    {
        //对比原始数据
        $original = $_SESSION['user'];
        $update_data = array();
        if ($original['user_nickname'] === '' && !empty($input['nick']))
            $update_data['user_nickname'] = $input['nick'];
        if ($original['user_logo_image_id'] === '' && !empty($input['avatar'])) {
            $binary = file_get_contents($input['avatar']);
            $response = object(parent::REQUEST_APPLICATION)->qiniu_image_upload(array('binary' => $binary));
            $update_data['user_logo_image_id'] = $response['image_id'];
        }

        if (empty($update_data))
            return false;

        //更新用户信息
        $update_data['user_update_time'] = time();
        $update_where = array(array('user_id=[+]', $_SESSION['user_id']));
        if (!object(parent::TABLE_USER)->update($update_where, $update_data))
            throw new error ('更新失败');

        return true;
    }


    /**
     * 保存用户授权参数（openID）
     * 
     * api: USERSELFSAVEACCESSTOKEN
     * {"class":"user/user","method":"api_self_save_access_token"}
     * 
     * Undocumented function
     *
     * @param array $input
     * @return void
     */
    public function api_self_save_access_token($input=array())
    {
        //验证登录
        // object(parent::REQUEST_USER)->check();

        //获取用户信息
        $user_info =  object(parent::TABLE_USER)->find($_SESSION['user_id']);

        $user_json = cmd(array($user_info['user_json']),'json decode');

        if(empty($input['access_token']) || empty($input['openid']) || empty($input['unionid'])){
            throw new error('请重新授权');
        }
        $user_json['token_info'] = $input;

        $user_json = cmd(array($user_json),'json encode');
        $where = array(
            array('user_id =[+]',$_SESSION['user_id']),
        );
        
        $access_token = $input['access_token'];
        $openID = $input['openid'];
        // $url = "https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN";
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openID&lang=zh_CN";
        $config = array(
            'url'=>$url
        );
        $res = object(parent::PLUGIN_HTTP_CURL)->request_get($config);
        return $res;
        if($res['errno'] !== 0){
            throw new error($res['error']);
        }
        $user_oauth = object(parent::TABLE_USER_OAUTH)->find($_SESSION['user_id']);
        if(!empty($user_oauth)){
            $update_data = array(

            );
            $update_where = array(
                array('user_oauth_id=[+]',$user_oauth['user_id']),
                array('user_id =[+]',$user_oauth['user_id'])
            );
            object(parent::TABLE_USER_OAUTH)->update($update_where,$update_data);
        }else{
            $insert_data = array(

            );
            object(parent::TABLE_USER_OAUTH)->insert($insert_data);
        }
        if(object(parent::TABLE_USER)->update($where,array('user_json'=>$user_json))){
            return $_SESSION['user_id'];
        }

        throw new error('更新信息失败');
    }
}