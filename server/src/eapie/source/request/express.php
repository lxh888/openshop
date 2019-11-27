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



namespace eapie\source\request;
use eapie\main;
use eapie\error;
class express extends main {
	
	
	const AUTHORITY_RIDER_READ 		= "express_rider_read";//骑手读取
    const AUTHORITY_RIDER_REMOVE 	= "express_rider_remove";//骑手删除
	const AUTHORITY_RIDER_EDIT 		= 'express_rider_edit';//骑手编辑
	const AUTHORITY_RIDER_ADD		= 'express_rider_add';//骑手添加
	
	
	const AUTHORITY_ORDER_SHIPPING 	= "express_order_shipping";//订单发货操作
	
	const AUTHORITY_ORDER_STATE 	= 'express_order_state';//修改订单状态（发货）
	const AUTHORITY_ORDER_TRASH  	= 'express_order_trash';//订单回收
	const AUTHORITY_ORDER_READ 		= "express_order_read";//快递订单读取
	const AUTHORITY_ORDER_DETAILS_READ 		= "express_order_details_read";//读取订单详细

	const AUTHORITY_ORDER_EXPORT = "express_order_export";//导出权限

	const AUTHORITY_WITHDRAW_READ = "express_user_withdraw_read";//用户提现读取
	
	
	
	/**
	 * 快递100--订阅推送
	 * Undocumented function
	 *
	 * @param [type] $data
	 * @return void
	 */
	public function express_hundred_curl($config=array())
	{
		$post_data = array();
		$post_data["schema"] = 'json' ;

		$company = $config['conpany'];
		$number = $config['number'];
		$key = $config['key'];
		$notify_url = $config['notify_url'];
		$param = "{'company':$company,'number':$number,'key':$key,'parameters':{'callbackurl':$notify_url}}";
		$post_data['param'] = $param;
		//callbackurl请参考callback.php实现，key经常会变，请与快递100联系获取最新key
		// $post_data["param"]='{"company":"yuantong", "number":"12345678","from":"广东深圳", "to":"北京朝阳",';
		// $post_data["param"]=$post_data["param"].'"key":"testkuaidi1031",';
		// $post_data["param"]=$post_data["param"].'"parameters":{"callbackurl":"http://www.yourdmain.com/kuaidi"}}';

		$url='http://www.kuaidi100.com/poll';

		$o="";
		foreach ($post_data as $k=>$v)
		{
			$o.= "$k=".urlencode($v)."&";		//默认UTF-8编码格式
		}

		$post_data=substr($o,0,-1);

		$post_require = array(
			'data'=>$post_data,
			'url'=>$url
		);

		$result = object('eapie\source\plugin\http\curl')->request_post($post_require);
		return $result;
	}


}