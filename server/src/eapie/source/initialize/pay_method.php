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
class pay_method extends module {
	
	
	/*支付方式*/
	
	
	//支付方式
	const 	PAY_METHOD_WEIXINPAY			=	'weixinpay';
	const 	PAY_METHOD_ALIPAY				=	'alipay';
	const 	PAY_METHOD_MERCHANT_CREDIT		=	'merchant_credit';//商家积分
	const 	PAY_METHOD_MERCHANT_MONEY		=	'merchant_money';//商家钱包
	const 	PAY_METHOD_USER_CREDIT			=	'user_credit';//用户积分
	const 	PAY_METHOD_USER_MONEY			=	'user_money';//用户钱包
	const 	PAY_METHOD_USER_MONEY_ANNUITY	=	'user_money_annuity';//养老资金
	const 	PAY_METHOD_USER_MONEY_EARNING	=	'user_money_earning';//赠送收益
	const 	PAY_METHOD_USER_MONEY_HELP		=	'user_money_help';//扶贫基金
	const 	PAY_METHOD_USER_MONEY_SERVICE	=	'user_money_service';//服务费
	const 	PAY_METHOD_USER_MONEY_SHARE		=	'user_money_share';//消费共享金
	const 	PAY_METHOD_BANKCARD				=	'bankcard';//银行卡
	
	
	
	
}
?>