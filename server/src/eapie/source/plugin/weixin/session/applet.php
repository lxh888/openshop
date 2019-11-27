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

//微信小程序
class applet
{

    /**
     * 获取接口调用凭证
     * @param  array  $config 配置参数
     * {
     *  id      [str] [必填] [小程序唯一凭证]
     *  secret  [str] [必填] [小程序唯一凭证密钥]
     * }
     * @return string 接口调用凭证
     */
    public function get_access_token($config = array())
    {
        if (empty($config['id']))
            return $this->_error(1);
        if (empty($config['secret']))
            return $this->_error(2);

        //请求参数
        $request = array(
            'grant_type' => 'client_credential',
            'appid'     => $config['id'],
            'secret'    => $config['secret']
        );

        //响应数据
        $response = object('eapie\source\plugin\http\curl')->request_get(array(
            'url' => 'https://api.weixin.qq.com/cgi-bin/token?' . http_build_query($request)
        ));

        // return $response;

        //http请求出错
        if ($response['errno'] !== 0)
            return $response;

        $response = cmd(array($response['data']), 'json decode');
        // return $response;
        //第三方请求出错
        if (!empty($response['errcode'])) {
            switch ($response['errcode']) {
                case 0:
                    break;
                case -1:
                    return $this->_error('获取小程序全局唯一后台接口调用凭据，系统繁忙');
                case 40001:
                    return $this->_error('获取小程序全局唯一后台接口调用凭据，AppSecret错误');
                case 40002:
                    return $this->_error('获取小程序全局唯一后台接口调用凭据，请确保 grant_type 字段值为 client_credential');
                case 40013:
                    return $this->_error('获取小程序全局唯一后台接口调用凭据，不合法的 AppID');
                default:
                    return $this->_error('获取小程序全局唯一后台接口调用凭据，未知错误');
                    break;
            }
        }

        return $this->_success($response['access_token']);
    }

    /**
     * 获取小程序码
     * @param  array $config 配置参数
     * {
     *  token       [str] [必填] [微信小程序接口调用凭证]
     *  scene       [str] [必填] [最大32个可见字符，只支持数字，大小写英文以及部分特殊字符]
     *  page        [str] [可选] [默认主页，必须是已经发布的小程序存在的页面]
     *  width       [int] [可选] [默认430，二维码的宽度，单位 px，最小 280px，最大 1280px]
     *  auto_color  [bol] [可选] [默认false，自动配置线条颜色]
     *  line_color  [arr] [可选] [auto_color 为 false 时生效，使用 rgb 设置颜色 例如 {"r":"xxx","g":"xxx","b":"xxx"} 十进制表示]
     *  is_hyaline  [bol] [可选] [默认false，是否需要透明底色]
     * }
     * @return binary 图片二进制内容
     */
    public function get_qrcode($token, $config = array())
    {
        if (empty($token))
            return $this->_error(3);
        if (empty($config))
            return $this->_error(4);

        //白名单
        $whitelist = array(
            'scene',
            'page',
            'width',
            'auto_color',
            'line_color',
            'is_hyaline'
        );
        $config = cmd(array($config, $whitelist), 'arr whitelist');

        $response = object('eapie\source\plugin\http\curl')->request_post(array(
            'url'  => 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $token,
            'data' => cmd(array($config), 'json encode')
        ));

        //http请求出错
        if ($response['errno'] !== 0)
            return $response;

        //第三方请求出错
        $json = cmd(array($response['data']), 'json decode');
        if (!empty($json['errcode'])) {
            switch ($json['errcode']) {
                case 45009:
                    return $this->_error('调用分钟频率受限(目前5000次/分钟，会调整)');
                    break;
                case 41030:
                    return $this->_error('所传page页面不存在，或者小程序没有发布');
                    break;
                default:
                    return $this->_error($json['errmsg']);
                    break;
            }
        }

        return $this->_success($response['data']);
    }

    /**
     * 获取小程序码
     * @param  array $config 配置参数
     * {
     *  token       [str] [必填] [微信小程序接口调用凭证]
     *  path        [str] [可选] [默认主页，必须是已经发布的小程序存在的页面]
     *  width       [int] [可选] [默认430，二维码的宽度，单位 px，最小 280px，最大 1280px]
     *  auto_color  [bol] [可选] [默认false，自动配置线条颜色]
     *  line_color  [arr] [可选] [auto_color 为 false 时生效，使用 rgb 设置颜色 例如 {"r":"xxx","g":"xxx","b":"xxx"} 十进制表示]
     *  is_hyaline  [bol] [可选] [默认false，是否需要透明底色]
     * }
     * @return binary 图片二进制内容
     */
    public function get_qrcode_length($token, $config = array())
    {
        if (empty($token))
            return $this->_error(3);
        if (empty($config))
            return $this->_error(4);

        //白名单
        $whitelist = array(
            'path',
            'width',
            'auto_color',
            'line_color',
            'is_hyaline'
        );
        $config = cmd(array($config, $whitelist), 'arr whitelist');

        $response = object('eapie\source\plugin\http\curl')->request_post(array(
            'url'  => 'https://api.weixin.qq.com/wxa/getwxacode?access_token=' . $token,
            'data' => cmd(array($config), 'json encode')
        ));

        //http请求出错
        if ($response['errno'] !== 0)
            return $response;

        //第三方请求出错
        $json = cmd(array($response['data']), 'json decode');
        if (!empty($json['errcode'])) {
            switch ($json['errcode']) {
                case 45009:
                    return $this->_error('调用分钟频率受限(目前5000次/分钟，会调整)');
                    break;
                case 41030:
                    return $this->_error('所传page页面不存在，或者小程序没有发布');
                    break;
                default:
                    return $this->_error($json['errmsg']);
                    break;
            }
        }

        return $this->_success($response['data']);
    }



	/**
	 * 获取openid
	 * 
	 * @param WxPayConfig $config  配置对象
	 * @return string
	 */
	public function get_openid($config){
		if (empty($config['id']))
            return $this->_error(1);
        if (empty($config['secret']))
            return $this->_error(2);
		if( empty($config["js_code"]) || !is_string($config["js_code"]) ){
			return $this->_error(5);
		}
		
		/*$url = "https://api.weixin.qq.com/sns/jscode2session?appid=".$config['id'].
		"&secret=".$config["secret"].
		"&js_code=".$config["js_code"].
		"&grant_type=authorization_code";*/
		
		//请求参数
        $request = array(
            'appid'      => $config['id'],
            'secret'     => $config['secret'],
            'js_code'    => $config['js_code'],
            'grant_type' => 'authorization_code'
        );

        //响应数据
        $response = object('eapie\source\plugin\http\curl')->request_get(array(
            'url' => 'https://api.weixin.qq.com/sns/jscode2session?' . http_build_query($request)
        ));
		if( !empty($response['errno']) ){
			return $response;
		}
		$response = cmd(array($response['data']), "json decode");
		
		//第三方请求出错
        if( !empty($response['errcode']) ){
            switch ($response['errcode']) {
                case 0:
                    break;
                case -1:
                    return $this->_error('系统繁忙');
                    break;
                case 40029:
                    return $this->_error('code 无效');
                    break;
                case 45011:
                    return $this->_error('频率限制，每个用户每分钟100次');
                    break;
                default:
                    return $this->_error('未知错误');
                    break;
            }
        }

		if( empty($response["openid"]) ){
			return $this->_error('openid 获取失败');
		}
		
		return $this->_success($response);
	}



    /**
     * 获取微信用户数据
     * 
     * @param  array $config 配置参数
     * {
     *  id              [str] [必填] [小程序唯一凭证]
     *  secret          [str] [必填] [小程序唯一凭证密钥]
     *  code            [str] [必填] [登录凭证，wx.login]
     *  iv              [str] [必填] [加密算法的初始向量，wx.getUserInfo]
     *  encryptedData   [str] [必填] [加密数据，wx.getUserInfo]
     * }
     * @return array
     */
    public function get_userinfo($config)
    {
        if (empty($config['id']))
            return $this->_error(1);
        if (empty($config['secret']))
            return $this->_error(2);
        if (empty($config['code']))
            return $this->_error(5);
        if (empty($config['iv']))
            return $this->_error(6);
        if (empty($config['encryptedData']))
            return $this->_error(7);

        //请求参数
        $request = array(
            'appid'      => $config['id'],
            'secret'     => $config['secret'],
            'js_code'    => $config['code'],
            'grant_type' => 'authorization_code'
        );

        //响应数据
        $response = object('eapie\source\plugin\http\curl')->request_get(array(
            'url' => 'https://api.weixin.qq.com/sns/jscode2session?' . http_build_query($request)
        ));

        //http请求出错
        if ($response['errno'] !== 0)
            return $response;

        $response = cmd(array($response['data']), 'json decode');

        //第三方请求出错
        if (!empty($response['errcode'])) {
            switch ($response['errcode']) {
                case 0:
                    break;
                case -1:
                    return $this->_error('系统繁忙');
                    break;
                case 40029:
                    return $this->_error('code 无效');
                    break;
                case 45011:
                    return $this->_error('频率限制，每个用户每分钟100次');
                    break;
                default:
                    return $this->_error('未知错误');
                    break;
            }
        }

        if (empty($response['session_key']))
            throw new error('第三方请求异常');

        //解密微信数据
        $decrypt_data = \openssl_decrypt(
            base64_decode($config['encryptedData']),
            'AES-128-CBC',
            base64_decode($response['session_key']),
            OPENSSL_RAW_DATA,
            base64_decode($config['iv'])
        );

        return $this->_success(cmd(array($decrypt_data), 'json decode'));
    }

    // 私有方法 =====================================

    /**
     * 错误提示
     * 
     * @param   int   $errno 错误码
     * @return  array
     */
    private function _error($errno = 1)
    {
        //不是数字，那么就是错误信息
        if (!is_int($errno)) {
            return array('errno' => 110, 'error' => $errno);
        }
        $return = array();
        switch ($errno) {
            case 1:
                $return = array('errno' => 1, 'error' => 'App ID 配置异常!');
                break;
            case 2:
                $return = array('errno' => 2, 'error' => 'App Secret 配置异常!');
                break;
            case 3:
                $return = array('errno' => 3, 'error' => '缺少微信小程序接口调用凭证');
                break;
            case 4:
                $return = array('errno' => 4, 'error' => '缺少微信小程序二维码配置!');
                break;
            case 5:
                $return = array('errno' => 5, 'error' => '缺少登录凭证code');
                break;
            case 6:
                $return = array('errno' => 6, 'error' => '缺少加密算法向量iv');
                break;
            case 7:
                $return = array('errno' => 7, 'error' => '缺少加密数据encryptedData');
                break;
            default:
                $return = array('errno' => 'default', 'error' => '未知错误');
                break;
        }
        return $return;
    }



    /**
     * 成功时，返回的数据
     */
    private function _success($data = NULL)
    {
        return array(
            'errno' => 0,
            'data'  => $data
        );
    }
}