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




namespace eapie\source\plugin\getui;

require_once(dirname(__FILE__) . '/' . 'GETUI_PHP_SDK_4.1.0.0/IGt.Push.php');

/**
 * 个推·消息推送
 * 文档：http://docs.getui.com/getui/start/getting/
 */
class getui
{

    const HOST = 'http://sdk.open.api.igexin.com/apiex.htm';

    private $app_id = '';
    private $app_key = '';
    private $app_secret = '';
    private $master_secret = '';

    /**
     * 构造函数
     * @param array $input  [构造参数]
     */
    public function init($input = array())
    {
        if (empty($input['app_id'])) {
            return $this->_error(1);
        }
        if (empty($input['app_key'])) {
            return $this->_error(2);
        }
        if (empty($input['app_secret'])) {
            return $this->_error(3);
        }
        if (empty($input['master_secret'])) {
            return $this->_error(4);
        }

        $this->app_id = $input['app_id'];
        $this->app_key = $input['app_key'];
        $this->app_secret = $input['app_secret'];
        $this->master_secret = $input['master_secret'];

        return $this->_success();
    }

    /**
     * 对指定应用群推消息
     * @param  array  $input [请求参数]
     * @return array
     */
    public function push_message_to_app($input = array())
    {
        $igt = new \IGeTui(self::HOST, $this->app_key, $this->master_secret);

        $template = $this->template_notification($input);
        if ($template['errno'] === 0) {
            $template = $template['data'];
        } else {
            return $template;
        }

        //个推信息体
        //基于应用消息体
        $message = new \IGtAppMessage();
        $message->set_isOffline(true);
        $message->set_offlineExpireTime(10 * 60 * 1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
        $message->set_data($template);

        $appIdList = array($this->app_id);

        $message->set_appIdList($appIdList);

        $rep = $igt->pushMessageToApp($message);
        return $this->_success($rep);
    }

    /**
     * 对单个用户推送消息
     * @param  array  $input [请求参数]
     * @return array
     */
    public function push_message_to_single($input = array())
    {
        $igt = new \IGeTui(self::HOST, $this->app_key, $this->master_secret);

        $template = $this->template_notification($input);
        if ($template['errno'] === 0) {
            $template = $template['data'];
        } else {
            return $template;
        }

        //个推信息体
        //基于应用消息体
        $message = new \IGtSingleMessage();
        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(10 * 60 * 1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
        $message->set_data($template);

        //接收方
        $target = new \IGtTarget();
        $target->set_appId($this->app_id);
        $target->set_clientId($input['ClientID']);

        $rep = $igt->pushMessageToSingle($message, $target);
        return $this->_success($rep);
    }

    /**
     * 点击通知打开应用模板
     * @param  array  $input [模板参数配置]
     * @return object
     */
    public function template_notification($input = array())
    {
        // 通知栏logo
        if (empty($input['logo'])) {
            return $this->_error(5);
        }
        // 通知栏标题
        if (empty($input['title'])) {
            return $this->_error(6);
        }
        // 通知栏内容
        if (empty($input['text'])) {
            return $this->_error(7);
        }
        // 透传内容
        if (empty($input['transmission_content'])) {
            return $this->_error(8);
        }

        $template =  new \IGtNotificationTemplate();
        $template->set_appId($this->app_id);
        $template->set_appkey($this->app_key);
        $template->set_logo($input['logo']);
        $template->set_title($input['title']);
        $template->set_text($input['text']);
        $template->set_transmissionContent($input['transmission_content']);

        // 收到消息是否立即启动应用：1为立即启动，2则广播等待客户端自启动
        if (isset($input['transmission_type'])) {
            $template->set_transmissionType(2);
        } else {
            $template->set_transmissionType(1);
        }
        // 通知栏消息布局样式(0 系统样式 1 个推样式) 默认为0
        if (isset($input['notify_style'])) {
            $template->set_notifyStyle(1);
        }
        // 通知栏logo链接
        if (isset($input['logo_url'])) {
            $template->set_logoURL($input['logo_url']);
        }
        // 是否响铃
        if (isset($input['ring'])) {
            $template->set_isRing(true);
        }
        // 是否震动
        if (isset($input['vibrate'])) {
            $template->set_isVibrate(true);
        }
        // 通知栏是否可清除
        if (isset($input['clearable'])) {
            $template->set_isClearable(true);
        }
        //$template->set_duration(BEGINTIME,ENDTIME);   //设置ANDROID客户端在此时间区间内展示消息
        
        return $this->_success($template);
    }

    /**
     * 成功
     * 
     * @param  mixed $data 输出数据
     * @return array
     */
    private function _success($data = null)
    {
        return array('errno' => 0, 'data'  => $data);
    }

    /**
     * 错误提示
     * 
     * @param   int         $errno      错误码
     * @param   string      $message    附加的错误信息
     * @return  array
     */
    private function _error( $errno = 0 , $message = '')
    {
        $return = array();

        switch ($errno) {
            case 1:
                $return = array('errno'=>1, 'error'=>'AppID 异常!');
                break;
            case 2:
                $return = array('errno'=>2, 'error'=>'AppKey 异常!');
                break;
            case 3:
                $return = array('errno'=>3, 'error'=>'AppSecret 异常!');
                break;
            case 4:
                $return = array('errno'=>4, 'error'=>'MasterSecret 异常!');
                break;
            case 5:
                $return = array('errno'=>5, 'error'=>'通知的图标名称不能为空!');
                break;
            case 6:
                $return = array('errno'=>6, 'error'=>'通知标题不能为空!');
                break;
            case 7:
                $return = array('errno'=>7, 'error'=>'通知内容不能为空!');
                break;
            case 8:
                $return = array('errno'=>8, 'error'=>'透传内容不能为空!');
                break;
            default:
                $return = array('errno'=>'default', 'error'=>'未知错误');
                break;
        }

        return $return;
    }
}