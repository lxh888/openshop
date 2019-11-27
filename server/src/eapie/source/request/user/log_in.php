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
class log_in extends \eapie\source\request\user {
    
    
    /**
     * 设置错误次数
     * 达到这个错误次数则需要输出验证码
     * 
     * @var int
     */
    private $_verify_code_alert_error = 3;
    
    
    /**
     * 用户登录
     * 
     * 验证码参数
     * $data['image_verify_key']    键
     * $data['image_verify_code']   验证码
     * 
     * USERLOGIN
     * [{"phone":"必须|手机号","password":"必须|登录密码","image_verify_key":"当需要验证码时|验证码键名称","image_verify_code":"当需要验证码时|验证码"}]
     * 
     * @param   array   $data
     * @return  bool
     */
    public function api($data = array()){
        //检查是否已初始化
        object(parent::REQUEST_SESSION)->check();
        
        //数据检测 
        object(parent::ERROR)->check($data, 'phone', parent::TABLE_USER_PHONE, array('args', 'length'), 'user_phone_id');
        object(parent::ERROR)->check($data, 'password', parent::TABLE_USER, array('args'), 'user_password');
        
        //根据手机号获取用户id
        $phone_data = object(parent::TABLE_USER_PHONE)->find_login_data($data['phone']);
        if( empty($phone_data['user_id']) ){
            throw new error ("该手机号没有注册用户或者不是登录手机号");
        }
        
        if( !empty($phone_data["user_phone_json"]) ){
            $phone_data["user_phone_json"] = cmd(array($phone_data["user_phone_json"]), "json decode");
        }
        
        //检查验证码
        if( $this->_log_in_alert_error($phone_data) ){
            object(parent::REQUEST_SESSION)->image_verify_code_check($data);
        }
        
        
        //根据用户id获取用户密码
        $user_data = object(parent::TABLE_USER)->find_password($phone_data['user_id']);
        if(empty($user_data['user_left_password']) || empty($user_data['user_right_password'])){
            $this->_log_in_error($phone_data);
            throw new error ("用户数据异常");
        }
        
        if($user_data['user_left_password'] !== md5($data['password'].$phone_data['user_id']) ||
        $user_data['user_right_password'] !== md5($phone_data['user_id'].$data['password']) ){
            $this->_log_in_error($phone_data);
            throw new error ("密码错误");
        }
        
        //清空错误信息
        $this->_log_in_error($phone_data, true);
        
        //插入登录日志
        object(parent::REQUEST_USER)->_log_in_(
            $phone_data['user_id'], 
            "phone_log_in", 
            array("user_phone" => $phone_data)
        );
        
        
        return $phone_data['user_id'];
    }
    
    
    
    /**
     * 获取用户登录的时候是否需要图片验证码的状态值
     * 返回验证码状态
     * 
     * USERLOGINIMAGEVERIFYCODESTATE
     * [{"phone":"必须|登录手机号"}]
     * 
     * 0表示不需要验证码或者是非注册手机号；1表示需要验证码。
     * 
     * @param   array   $data
     * @return  int
     */
    public function api_image_verify_code_state($data = array()){
        //数据检测 
        $err = object(parent::ERROR)->check($data, 'phone', parent::TABLE_USER_PHONE, array('args', 'length'), 'user_phone_id', true);
        if( !empty($err) && is_array($err)){
            return 0;//存在错误
            }
        
        $phone_data = object(parent::TABLE_USER_PHONE)->find_login_data($data['phone']);
        if( empty($phone_data['user_id']) ){
            return 0;
        }
        
        if( !empty($phone_data["user_phone_json"]) ){
            $phone_data["user_phone_json"] = cmd(array($phone_data["user_phone_json"]), "json decode");
        }
        
        if( $this->_log_in_alert_error($phone_data) ){
            return 1;
        }else{
            return 0;
        }
        
    }

    /**
     * 微信登录
     *
     * api: USERLOGINWEIXIN
     * 
     * @param  [str] $type  [必填] [微信APP，或者微信小程序，(app,applet)]
     * @param  [str] $input [必填] [微信提供的数据]
     * @return [str] [用户ID]
     */
    public function api_login_weixin($type = '', $input = array())
    {
        //检查是否已初始化
        object(parent::REQUEST_SESSION)->check();

        //判断登录类型
        if ($type === 'app') {
            $wx_data = $input;
        } elseif ($type === 'applet') {
            $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('weixin_applet_access'), true);
            $config = array_merge($config, $input);
            $wx_data = object(parent::PLUGIN_WEIXIN_SESSION_APPLET)->get_userinfo($config);
			if( !empty($wx_data['data']) ){
				$wx_data = $wx_data['data'];
			}
        } else {
            throw new error('微信登录类型错误');
        }
        // return $wx_data;
        if (empty($wx_data['unionId']))
            throw new error('微信授权信息获取失败');
		
		//查询数据
		$data = object(parent::TABLE_USER_OAUTH)->find_platform_key("weixin", $wx_data['unionId']);
        //$data = object(parent::TABLE_USER_OAUTH)->find_where(array('user_oauth_key=[+]', $wx_data['unionId']));
        if ( !empty($data) ) {
            object(parent::REQUEST_USER)->_log_in_($data['user_id'], 'weixin_log_in', array('user_oauth' => $input));
            return $data['user_id'];
        } else {
            throw new error('微信未绑定手机号');
        }
		
    }


    // ==========================================
    
    /**
     * 判断是否超过了错误限制次数
     * 
     * @param   array   $user_phone_data
     * @return  bool
     */
    private function _log_in_alert_error($user_phone_data){
        //判断是否需要判断验证码
        if( isset($user_phone_data["user_phone_json"]["log_in_error_count"]) &&
        is_integer($user_phone_data["user_phone_json"]["log_in_error_count"]) && 
        $user_phone_data["user_phone_json"]["log_in_error_count"] >= $this->_verify_code_alert_error ){
            return true;
        }else{
            return false;
        }
        
    }
    
    
    
    
    
    /**
     * 记录/清理 登录错误次数
     * 
     * @param   array   $user_phone_data
     * @return  bool
     */
    private function _log_in_error($user_phone_data, $clear = false){
        
        if( !isset($user_phone_data["user_phone_id"]) ){
            return false;
        }
        
        if( empty($user_phone_data["user_phone_json"]) || !is_array($user_phone_data["user_phone_json"]) ){
            $user_phone_data["user_phone_json"] = array();
        }
        
        if( empty($clear) ){
            if( !isset($user_phone_data["user_phone_json"]["log_in_error_count"]) ||
            !is_integer($user_phone_data["user_phone_json"]["log_in_error_count"]) ){
                $user_phone_data["user_phone_json"]["log_in_error_count"] = 1;
            }else{
                $user_phone_data["user_phone_json"]["log_in_error_count"] ++;
            }
        }else{
            if( isset($user_phone_data["user_phone_json"]["log_in_error_count"]) ) unset($user_phone_data["user_phone_json"]["log_in_error_count"]);
        }
        
        $where = array();
        $where[] = array('user_phone_id=[+]', (string)$user_phone_data["user_phone_id"]);
        $data = array();
        $data["user_phone_json"] = cmd(array($user_phone_data["user_phone_json"]), 'json encode');
        
        object(parent::TABLE_USER_PHONE)->update($where, $data);
        
    }





}
?>