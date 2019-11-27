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



namespace eapie\source\plugin\weixin\session;
class oauth2 {
	
	
	/* 微信网页授权 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140842 */
	
	
	/**
     * 成功时，返回的数据
	 * 
	 * @param	multi	$data
     */
    private function _success($data = NULL){
        return array(
            'errno' => 0,
            'data'  => $data
        );
    }
	
	
	/**
     * 错误提示
     * 
     * @param   int   $errno 错误码
     * @return  array
     */
    private function _error($errno = 1){
        //不是数字，那么就是错误信息
        if( !is_numeric($errno) ){
            return array('errno'=>110, 'error'=> $errno);
        }
		
        $return = array();
        switch( $errno ){
            case 1: $return = array('errno'=>1, 'error'=>'App ID 配置异常!');break;
            case 2: $return = array('errno'=>2, 'error'=>'App Secret 配置异常!');break;
			case 3: $return = array('errno'=>3, 'error'=>'redirect_uri 回调地址异常!');break;
			case 4: $return = array('errno'=>4, 'error'=>'code 异常!');break;
            default: $return = array('errno'=>'default', 'error'=>'未知错误');break;
        }
        return $return;
    }
	
	
	
	
	/**
	 * 用户同意授权，获取code
	 * 
	 * $data = array(
	 * 	'redirect_uri',
	 * 	'appid',
	 * 	'scope',
	 *  'state'
	 * );
	 * 
	 * 
	 * appid			公众号的唯一标识
	 * redirect_uri		授权后重定向的回调链接地址， 请使用 urlEncode 对链接进行处理
	 * response_type	返回类型，请填写code
	 * scope			应用授权作用域，snsapi_base （不弹出授权页面，直接跳转，只能获取用户openid），snsapi_userinfo （弹出授权页面，可通过openid拿到昵称、性别、所在地。并且， 即使在未关注的情况下，只要用户授权，也能获取其信息 ）
	 * state			重定向后会带上state参数，开发者可以填写a-zA-Z0-9的参数值，最多128字节
	 * #wechat_redirect	无论直接打开还是做页面302重定向时候，必须带此参数
	 * 
	 * @param	array	$data
	 */
	public function authorize( $data = array() ){
		if( empty($data['appid']) ){
			return $this->_error(1);
		}
		if( empty($data['redirect_uri']) ){
			return $this->_error(3);
		}
		if( empty($data['scope']) ) $data['scope'] = 'snsapi_base';
		if( empty($data['state']) ) $data['state'] = 'state';
		
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$data['appid'].
		'&redirect_uri='.$data['redirect_uri'].
		'&response_type=code&scope='.$data['scope'].
		'&state='.$data['state'].
		'#wechat_redirect';
		
		//跳转
		header('Location:'.$url);
		exit;
	}
	
	
	
	
	/**
	 * 通过code换取网页授权access_token
	 * 
	 * appid		公众号的唯一标识
	 * secret		公众号的appsecret
	 * code			填写第一步获取的code参数
	 * grant_type	填写为authorization_code
	 * 
	 * {
		    "access_token": "22_UTkyu35-JYMO_bVl1MxGVo3tYxGdPeRt3KhP1FqLD2Q2_OBpvmZAFg41Nepwt13pJ92-4R6u0XHqde5-tvzlIA",
		    "expires_in": 7200,
	  	
			//由于access_token拥有较短的有效期，当access_token超时后，可以使用refresh_token进行刷新，refresh_token有效期为30天，
			//当refresh_token失效之后，需要用户重新授权。
		    "refresh_token": "22_ovsX8AnefHPqY7ElgQXO1V3PzPf_N5rYmc-GhL6Bta0aWImRvrHXcgeEXi8TPAWbwdmMswvxhxwfOSTXmI_1Ag",
			
		    "openid": "oO3M05uU2gvqO97xEHWPezmtXcko",
		    "scope": "snsapi_userinfo"
		}
	 * 
	 * 
	 * @param	array	$data
	 */
	public function access_token( $data = array() ){
		if( empty($data['appid']) ){
			return $this->_error(1);
		}
		if( empty($data['secret']) ){
			return $this->_error(2);
		}
		if( empty($data['code']) ){
			return $this->_error(4);
		}
		
		//通过code换取网页授权access_token
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$data['appid']
		.'&secret='.$data['secret']
		.'&code='.$data['code']
		.'&grant_type=authorization_code';
		
		//响应数据
        $response = object('eapie\source\plugin\http\curl')->request_get(array(
            'url' => $url
        ));
		
		if( !empty($response['errno']) ){
			return $response;
		}
		
		$response_data = cmd(array($response['data']), 'json decode');
		
		return $this->_success($response_data);
	}
	
	
	
	
	
	
	
	
	
	
	
}
?>