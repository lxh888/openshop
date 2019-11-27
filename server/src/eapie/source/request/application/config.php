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
class config extends \eapie\source\request\application {
	
	
	
	
	/**
	 * 获取应用配置
	 * 
	 * APPLICATIONCONFIG
	 * {"class":"application/config","method":"api"}
	 * 
	 * @param	void
	 * @return	array
	 */
	public function api(){
		$config = array(
			'qiniu_domain',
			'app_android_version',
			
			'rmb_buy_merchant_credit',
			'rmb_withdraw_user_money_earning',
			'user_money_earning_transfer_user_money',
			'user_money_earning_transfer_user_money_help',
			
			'daily_attendance_earn_user_credit',
			'user_identity',
			'user_recommend_domain',
			'user_money_share_conversion_annuity_earning_help',//用户消费共享金转换为养老金、赠送收益、扶贫资金账户配置
			
			'display',//用于审核隐藏和显示

			
			'credit',
			'set_interval',
			'express_agent_agreement',
			'agent_user_display'	//代理用户显示状态
		);
		
		$select_data = object(parent::TABLE_CONFIG)->select(array(
			"where" => array( array("config_id IN ([-])", "\"".implode("\",\"", $config)."\"", true) ))
			);
		if( empty($select_data)){
			return NULL;
		}
		
		$data = array();
		foreach($select_data as $value){
			$data[$value["config_id"]] = object(parent::TABLE_CONFIG)->data($value, true);
		}
		return $data;
	}
	
	
	
	
	
	
	/**
	 * 获取七牛云 域名
	 * 
	 * APPLICATIONCONFIGQINIUDOMAIN
	 * 
	 * @param	void
	 * @return	array
	 */
	public function api_qiniu_domain(){
		$qiniu_domain = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("qiniu_domain"), true);
		if( empty($qiniu_domain) ){
			//throw new error("配置异常");
			return NULL;
		}
		
		return $qiniu_domain;
	}
	
	
	
	/**
	 * 获取订单配置
	 * 
	 * APPLICATIONCONFIGORDER
	 * {"class":"application/config","method":"api_order"}
	 * 
	 * @param	void
	 * @return	array
	 */
	public function api_order(){
		$config = array(
			"rmb_buy_merchant_credit",
			"user_money_earning_transfer_user_money"
		);
		
		$select_data = object(parent::TABLE_CONFIG)->select(array(
			"where" => array( array("config_id IN ([-])", "\"".implode("\",\"", $config)."\"", true) ))
			);
		if( empty($select_data)){
			return NULL;
		}
		
		$data = array();
		foreach($select_data as $value){
			$data[$value["config_id"]] = object(parent::TABLE_CONFIG)->data($value, true);
		}
		return $data;
	}
	
	
	
	
	/**
	 * 获取用户配置
	 * 
	 * APPLICATIONCONFIGUSER
	 * {"class":"application/config","method":"api_user"}
	 * 
	 * @param	void
	 * @return	array
	 */
	public function api_user(){
		$config = array(
			"user_identity",
			"user_recommend_domain",
		);
		
		$select_data = object(parent::TABLE_CONFIG)->select(array(
			"where" => array( array("config_id IN ([-])", "\"".implode("\",\"", $config)."\"", true) ))
			);
		if( empty($select_data)){
			return NULL;
		}
		
		$data = array();
		foreach($select_data as $value){
			$data[$value["config_id"]] = object(parent::TABLE_CONFIG)->data($value, true);
		}
		return $data;
	}
	
	
	
	
	
	
}
?>