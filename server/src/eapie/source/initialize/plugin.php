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



namespace eapie\source\initialize;
class plugin extends pay_method {
	
	
	
	/*插件*/
	
	//插件
	const	PLUGIN_VERIFYCODE 					= 	'eapie\source\plugin\verifycode\verifycode';//验证码
	const 	PLUGIN_QINIU						=	'eapie\source\plugin\qiniu\qiniu';//七牛类地址
	const 	PLUGIN_ALIYUN_DYSMS					=	'eapie\source\plugin\aliyun\dysms\dysms';//阿里云插件
	const 	PLUGIN_ALIPAY						=	'eapie\source\plugin\alipay\alipay';//支付宝
	const 	PLUGIN_WEIXIN_PAY_UNIFIEDORDER		=	'eapie\source\plugin\weixin\pay\unifiedorder';//微信支付
	const 	PLUGIN_WEIXIN_PAY_MCHPAY			=	'eapie\source\plugin\weixin\pay\mch_pay';//微信企业支付
	const 	PLUGIN_WEIXIN_SESSION_APPLET		=	'eapie\source\plugin\weixin\session\applet';//微信小程序会话
	const 	PLUGIN_WEIXIN_SESSION_OAUTH2		=	'eapie\source\plugin\weixin\session\oauth2';//微信网页授权
	const 	PLUGIN_PHPQRCODE					=	'eapie\source\plugin\qrcode\qrcode';//七牛类地址
	const 	PLUGIN_HTTP_CURL					=	'eapie\source\plugin\http\curl';//请求
	const 	PLUGIN_HTTP_SOCKET					=	'eapie\source\plugin\http\socket';//套接字
	const 	PLUGIN_AIP							=	'eapie\source\plugin\aip\aip';//请求
	const 	PLUGIN_EXCEL						=	'eapie\source\plugin\office\excel';
	const 	PLUGIN_KUAIDI100					=	'eapie\source\plugin\kuaidi100\express';
	const 	PLUGIN_RESOURCE						=	'eapie\source\plugin\resource\resource';//插件资源
	const   PLUGIN_WEIXIN_MESSAGE_SEND          =   'eapie\source\plugin\weixin\message\send';//发送微信通知消息
	
	
	
	
	
	
}
?>