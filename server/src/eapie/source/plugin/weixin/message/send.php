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



namespace eapie\source\plugin\weixin\message;
class send{

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

    /**
     * 失败时，返回的数据
     * 
     * @param   int   $errno 错误码
     * @return  array
     */
    private function _error($errno = 1)
    {
        //不是数字，那么就是错误信息
        if (!is_int($errno))
        {
            return array('errno'=>110, 'error'=> $errno);
        }
        $return = array();
        switch ($errno) {
            case 1:
                $return = array('errno'=>1, 'error'=>'App ID 或 App Secret 配置异常!');
                break;
            case 2:
                $return = array('errno'=>2, 'error'=>'获取微信公众号接口调用凭证失败');
                break;
            case 3:
                $return = array('errno'=>3, 'error'=>'缺少微信小程序接口调用凭证');
                break;
            case 4:
                $return = array('errno'=>4, 'error'=>'缺少微信小程序二维码配置!');
                break;
            case 5:
                $return = array('errno'=>5, 'error'=>'缺少登录凭证code');
                break;
            case 6:
                $return = array('errno'=>6, 'error'=>'缺少加密算法向量iv');
                break;
            case 7:
                $return = array('errno'=>7, 'error'=>'缺少加密数据encryptedData');
                break;
            default:
                $return = array('errno'=>'default', 'error'=>'未知错误');
                break;
            }
        return $return;
    }

    /**
     * 发送微信公众号模板消息
     */
    public function template_message($data=array(),$time=30){
  
        //获取微信公众号配置
        $wx_main_config = object('eapie\source\table\application\config')->data(object('eapie\source\table\application\config')->find('weixin_mp_access'),true);
        if(empty($wx_main_config['id']) || empty($wx_main_config['secret']))
            return $this->_error(1);
        
        //获取公众号的全局唯一接口调用凭据ACCESS_TOKEN
        $access_token_config = array(
            'id'=>$wx_main_config['id'],
            'secret'=>$wx_main_config['secret']
        );
        $access_token = object('eapie\source\plugin\weixin\session\applet')->get_access_token($access_token_config);
        // return $access_token;
        if($access_token['errno'] !== 0)
            return $this->_error(2);

        $access_token = $access_token['data'];//公众号的全局唯一接口调用凭据ACCESS_TOKEN

        //发送模板消息
        $http_url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token";//post请求地址

        $send_config = array(
            'url'=>$http_url,
            'data'=>$data,
            'timeout_second'=>$time
        );
        $send_res = object('eapie\source\plugin\http\curl')->request_post($send_config);
        if($send_res['errno'] !== 0){
            return $this->_error($send_res['data']);
        }
        return $this->_success($send_res['data']);
    }
}
?>