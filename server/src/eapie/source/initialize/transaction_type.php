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
class transaction_type extends table {
	
	
	/*交易类型*/
	
	
	//交易类型
	const 	TRANSACTION_TYPE_ADMIN_PLUS				=   'admin_plus';//人工收入
	const 	TRANSACTION_TYPE_ADMIN_MINUS			=   'admin_minus';//人工支出
	const 	TRANSACTION_TYPE_RECHARGE				=   'recharge';//充值
	const 	TRANSACTION_TYPE_TRANSFER				=   'transfer';//转账
	const 	TRANSACTION_TYPE_CONSUME				=   'consume';//消费
	
	const 	TRANSACTION_TYPE_RECOMMEND_CREDIT		=   'recommend_credit';//推荐奖励积分
	const 	TRANSACTION_TYPE_RECOMMEND_MONEY		=   'recommend_money';//推荐奖励钱包
	const 	TRANSACTION_TYPE_REFUND					=   'refund';//退款
	const 	TRANSACTION_TYPE_WITHDRAW				=   'withdraw';//提现
	const 	TRANSACTION_TYPE_CONVERSION				=   'conversion';//兑换
	const 	TRANSACTION_TYPE_SYSTEM_CONVERSION		=   'system_conversion';//系统兑换
	const 	TRANSACTION_TYPE_DAILY_ATTENDANCE		=   'daily_attendance';//每日签到
	const 	TRANSACTION_TYPE_SHOP_ORDER				=   'shop_order';//商城购物消费
	const 	TRANSACTION_TYPE_SHOP_ORDER_GROUP		=   'shop_order_group';//拼团订单
	const	TRANSACTION_TYPE_SHOP_ORDER_GROUP_REFUND	=   'shop_order_group_refund';//拼团订单退款
	
	const 	TRANSACTION_TYPE_HOUSE_TOP_ORDER		=   'house_top_order';//楼盘置顶订单
	const 	TRANSACTION_TYPE_AWARD_MONEY			=   'award_money';//钱包奖励
	const 	TRANSACTION_TYPE_HOUSE_PRODUCT_AWARD_MONEY	=   'house_product_award_money';//楼盘产品发布钱包奖励
	const 	TRANSACTION_TYPE_EXPRESS_ORDER			=   'express_order';//快递订单
	const 	TRANSACTION_TYPE_EXPRESS_ORDER_AWARD	=   'express_order_award';//快递订单的提成奖励
	const 	TRANSACTION_TYPE_SHOP_GROUP_ORDER		=   'shop_group_order';//商城团购订单
	
	
	
	const 	TRANSACTION_TYPE_RECOMMEND_SALES_REWARD		=   'recommend_sales_reward';//推荐分销奖励
	const 	TRANSACTION_TYPE_RECOMMEND_LEVEL_UP_REWARD		=   'recommend_level_up_reward';//推荐分销奖励
	const 	TRANSACTION_TYPE_AGENT_CREDIT_REWARD		=   'agent_credit_reward';//代理积分奖励
	const 	TRANSACTION_TYPE_PAY_AWARD_CREDIT		=   'pay_award_credit';//消费返利（返积分）
	const 	TRANSACTION_TYPE_RECOMMEND_SIGN_UP_AWARD_CREDIT	=   'recommend_sign_up_award_credit';//推荐注册奖励积分
	const   TRANSACTION_TYPE_INVITE_ROYALTY_REWARD		=		'invite_royalty_reward';//邀请分销提成奖励（爱尚购）
	const   TRANSACTION_TYPE_USER_CREDIT_YITAO_INVITE   =		'credit_invite_royalty';//邀请积分奖励（爱尚购）
	
	
	
	const   TRANSACTION_TYPE_SHOP_ORDER_AGENT_MONEY_DIVIDEND   =		'shop_order_agent_money_dividend';//商城订单业绩代理人分红（酱朔源）
	
	
	
}
?>