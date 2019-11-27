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



namespace eapie\source\request\application;

use eapie\main;
use eapie\error;

class audio extends \eapie\source\request\application
{
    /**
     * 生成语音文件 
     * 
     * api:APPLICATIONAUDIOSELFMERCHANT
     * req:{
     *  message_value   [str]   [必填]  [语音文本]
     * }
     * 
     * @param   array
     * @return  string      二进制语音文件
     */
    public function api_self_merchant($data)
    {

        //检查是否已初登录
        object(parent::REQUEST_USER)->check();

        if (!isset($data['message_value'])) {
            throw new error('没有要生成语音文件的文字');
        }


        $cashier = object(parent::TABLE_MERCHANT_CASHIER)->find_byuid($_SESSION['user_id']);
        $merchant_user =  object(parent::TABLE_MERCHANT_USER)->check_user($_SESSION['user_id']);


        if (!$merchant_user && empty($cashier)) {
            throw new error('不是收银员或者商家用户');
        }



        $reg = '/商家收款已到账，([\d\.]+)元/i';
        if (!preg_match($reg, $data['message_value'])) {
            throw new error('语音文本内容有误');
        }

        //获取配置
        $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('aip_access'), true);

        $config['spd'] = 4.5;
        $config['pit'] = 5;

        //生成音频
        $audio = object(parent::PLUGIN_AIP)->synthesis($data['message_value'], $config);
        if (!empty($audio['errno']))
            throw new error($audio['error']);
        // header("Content-type:audio/mpeg");
        header("Accept-Ranges:bytes");

        header("Content-type: audio/mp3");
        echo $audio['data'];
        exit;


        /*
        // 判断是否合法商家
        if ($data['merchant_id'] && object(parent::TABLE_MERCHANT_USER)->check_exist($_SESSION['user_id'], $data['merchant_id'], true)) {
            //获取配置
            $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('aip_access'), true);

            $config['spd'] = 4.5;
            $config['pit'] = 5;

            //生成音频
            $audio = object(parent::PLUGIN_AIP)->synthesis($data['message_value'], $config);
            if (!empty($audio['errno']))
                throw new error($audio['error']);
            header("Content-type:audio/mpeg");
            header("Accept-Ranges:bytes");

            // //header("Content-type: audio/mp1");
            echo $audio['data'];
            exit;
        } else {
            throw new error('非法商家用户');
        }

*/
    }
}