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
class weixin extends \eapie\source\request\session {
	
	
	
	/**
	 * 获取小程序的微信用户信息(包括openid)
	 * 
	 * SESSIONWEIXINAPPLETUSERINFO
	 * {"class":"session/weixin","method":"api_applet_userinfo"}
	 * 
	 * [{"js_code":"微信的js_code换取open id","weixin_data":"微信提供的数据"}]
	 * @param	array	$input
	 */
	public function api_applet_userinfo( $input = array() ){
		if( empty($input['js_code']) || 
		(!is_string($input['js_code']) && !is_numeric($input['js_code'])) ){
			throw new error('微信的js_code参数不合法！');
		}
		
		$config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('weixin_applet_access'), true);
		if( empty($config) ){
			throw new error('小程序配置不合法！');
		}
		
		if( !empty($input['weixin_data']) ){
			if( empty($input['weixin_data']) || !is_array($input['weixin_data'])  ){
				throw new error('微信的用户数据参数不合法！');
			}
			
			$config['code'] = $input['js_code'];
	        $config = array_merge($config, $input['weixin_data']);
	        $wx_data = object(parent::PLUGIN_WEIXIN_SESSION_APPLET)->get_userinfo($config);
			if( !empty($wx_data['errno']) ){
				throw new error($wx_data['error']);
			}
			
			return $wx_data['data'];
		}else{
			$config['js_code'] = $input['js_code'];
			$wx_data = object(parent::PLUGIN_WEIXIN_SESSION_APPLET)->get_openid($config);
			if( !empty($wx_data['errno']) ){
				throw new error($wx_data['error']);
			}
			
			if( isset($wx_data['data']['openid']) ){
				$wx_data['data']['openId'] = $wx_data['data']['openid'];
			}
			
			return $wx_data['data'];
		}
		
		
	}
	
	
	
	
	
	
	/**
	 * 获取会话里面的 access_token 信息
	 * 
	 * SESSIONWEIXINACCESSTOKEN
	 * {"class":"session/weixin","method":"api_access_token"}
	 * 
	 * @param	void
	 */
	public function api_access_token(){
		//检查是否已初始化
		object(parent::REQUEST_SESSION)->check();
		
		if( !empty($_SESSION['session_public']['weixin_oauth2']['errno']) ){
			throw new error($_SESSION['session_public']['weixin_oauth2']['error']);
		}
		
		if( empty($_SESSION['session_public']['weixin_oauth2']['access_token']) ){
			throw new error('数据不存在！');
		}
		
		return $_SESSION['session_public']['weixin_oauth2']['access_token'];
	}
	
	
	
	/**
	 * 微信网页授权
	 * 
	 * $data = array(
	 * 	'notify_url'	回调地址
	 * );
	 * http://developer.eapie.eonfox.com/index.php/token/leftdfc261d6ec18fda345ca8c2575b28f8b1358a4c888ada75d3115603284799307832ea7b0bfd7bc06a64eefc6c40d6fbc/application/jiangyoukuaidi/data/SESSIONWEIXINAUTHORIZE
	 * 
	 * SESSIONWEIXINAUTHORIZE
	 * {"class":"session/weixin","method":"api_authorize"}
	 * 
	 * @param	array	$data
	 */
	public function api_authorize( $data = array() ){
		//检查是否已初始化
		object(parent::REQUEST_SESSION)->check();
		
		//获取公众号配置
		$weixin_mp_access = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("weixin_mp_access"), true);
		if( !isset($weixin_mp_access['id']) ){
			throw new error('appid 异常');
		}
		
		if( isset($data['notify_url']) && is_string($data['notify_url']) ){
			if( !isset($_SESSION['session_public']['weixin_oauth2']) ){
				$_SESSION['session_public']['weixin_oauth2'] = array();
			}
			$_SESSION['session_public']['weixin_oauth2']['notify_url'] = cmd(array($data['notify_url']), 'url decode');
		}
		
		//printexit($data, $_SESSION['session_public']);
		
		//接口访问状态信息
		$application = object(parent::MAIN)->api_application();
		$redirect_uri = http(function($http) use ($application){
			$http["path"] = array();
			$http["path"][] = "index.php";
			$http["path"][] = "token";
			$http["path"][] = $_SESSION['session_left_token'];
			$http["path"][] = "application";
			$http["path"][] = $application["application_id"];
			$http["path"][] = "data";
			$http["path"][] = "SESSIONWEIXINREDIRECTURI";
				
			return http( http($http), array(
				'index' => 1,//显示入口文件
				//将多余的 GET 参数删除
				'delete' => array(
					'query' => array('token', 'data')
				)
			));
		});
		//printexit($redirect_uri);
		
		$redirect_uri = cmd(array($redirect_uri), 'url encode');
		
		//更新一下会话
		object(parent::PLUGIN_WEIXIN_SESSION_OAUTH2)->authorize(array(
			'appid' => $weixin_mp_access['id'],
			'redirect_uri' => $redirect_uri,
			'state' => 'openid',
			'scope' => 'snsapi_userinfo'
		));
		
	}
	
	
	
	/**
	 * 微信网页授权的回调地址
	 * 
	 * SESSIONWEIXINREDIRECTURI
	 * {"class":"session/weixin","method":"api_redirect_uri"}
	 * 
	 * 如果用户同意授权，页面将跳转至 redirect_uri/?code=CODE&state=STATE。
	 * code说明 ： code作为换取access_token的票据，每次用户授权带上的code将不一样，code只能使用一次，5分钟未被使用自动过期。
	 * 
	 * @param	void	
	 */
	public function api_redirect_uri(){
		//检查是否已初始化
		object(parent::REQUEST_SESSION)->check();
		
		$http_query = http(function( $http ){
			return $http['query'];
		});
		
		if( empty($http_query['code']) ){
			throw new error('微信网页授权 code 异常');
		}
		
		//获取公众号配置
		$weixin_mp_access = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("weixin_mp_access"), true);
		if( !isset($weixin_mp_access['id']) ){
			throw new error('appid 异常');
		}
		if( !isset($weixin_mp_access['secret']) ){
			throw new error('Appsecret 异常');
		}
		
		
		//通过code换取网页授权access_token
		$access_token_data = object(parent::PLUGIN_WEIXIN_SESSION_OAUTH2)->access_token(array(
			'appid' => $weixin_mp_access['id'],
			'secret' => $weixin_mp_access['secret'],
			'code' => $http_query['code']
		));
		
		if( !isset($_SESSION['session_public']['weixin_oauth2']) ){
			$_SESSION['session_public']['weixin_oauth2'] = array();
		}
		
		//如果存在错误
		if( !empty($access_token_data['errno']) ){
			$_SESSION['session_public']['weixin_oauth2']['errno'] = $access_token_data['errno'];
			$_SESSION['session_public']['weixin_oauth2']['error'] = $access_token_data['error'];
		}else{
			$_SESSION['session_public']['weixin_oauth2']['errno'] = 0;
			$_SESSION['session_public']['weixin_oauth2']['error'] = '';
			$_SESSION['session_public']['weixin_oauth2']['access_token'] = $access_token_data['data'];
		}
		
		if( !empty($_SESSION['session_public']['weixin_oauth2']['notify_url']) ){
			//跳转
			header('Location:'.$_SESSION['session_public']['weixin_oauth2']['notify_url']);
			exit;
		}else{
			return true;
		}
		
	}
	
	
	
	
	
	
	
	
	
	
}
?>