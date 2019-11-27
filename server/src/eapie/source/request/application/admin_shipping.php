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

class admin_shipping extends \eapie\source\request\application
{



	/**
	 * ------Mr.Zhao------2019.08.12------
	 * 
	 * 获取物流类型--不分页
	 *
	 * api: APPLICATIONADMINSHIPPINGOPTIONS
	 * {"class":"application/admin_shipping","method":"api_options"}
	 * 
	 * @param	array	$input
	 * @return	array
	 */
	public function api_options($input = array())
	{
		//检查权限
		object(parent::REQUEST_ADMIN)->check();

		$has_module = isset($input['module']);
		$has_key = isset($input['key']);

		if (!$has_module && !$has_key) {
			throw new error('缺少参数');
		}
		if ($has_module) {
			//检测输入
			object(parent::ERROR)->check($input, 'module', parent::TABLE_SHIPPING, array('args'), 'type_module');
		}

		if ($has_key) {
			//检测输入
			object(parent::ERROR)->check($input, 'key', parent::TABLE_SHIPPING, array('args'), 'key');
		}

		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => array(),
			'select' => array(
				"shipping_id AS id",
				"shipping_name AS name",
				"shipping_sign AS sign",
				"shipping_default AS defaults",
				"shipping_property AS property",
				"shipping_price AS price",
				"shipping_key AS `key`",
				"shipping_module AS `module`"
			)
		);

		//条件
		$config['where'][] = array('shipping_state=1');
		if ($has_module) {
			$config['where'][] = array('[and] shipping_module=[+]', $input['module']);
		}

		if ($has_key) {
			$config['where'][] = array('[and] shipping_key=[+]', $input['key']);
		}

		return object(parent::TABLE_SHIPPING)->select($config);
	}





}