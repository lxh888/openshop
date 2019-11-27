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
class lock {
	
	
	/*锁常量*/
	
	//锁事务名称,字数不能超过20字
	const 	LOCK_SHOP_ORDER_INSERT	= 	'shop_order_insert';//插入购物商城的订单数据
	const 	LOCK_STATE				= 	'state';//状态操作
	const 	LOCK_PAY_STATE			= 	'pay_state';//支付状态操作
	const 	LOCK_CREDIT				= 	'credit';//积分操作
	const 	LOCK_MONEY				= 	'money';//余额操作
	const 	LOCK_EVENT				= 	'event';//事件操作
	const 	LOCK_MONEY_ANNUITY		= 	'money_annuity';//养老金操作
	const 	LOCK_MONEY_EARNING		= 	'money_earning';//赠送收益操作
	const 	LOCK_MONEY_HELP			= 	'money_help';//扶贫资金操作
	const 	LOCK_MONEY_SERVICE		= 	'money_service';//服务费操作
	const 	LOCK_MONEY_SHARE		= 	'money_share';//消费共享金操作
	
	
	
	
	
}
?>