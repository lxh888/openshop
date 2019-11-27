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



namespace eapie\source\request\shop;
use eapie\main;
use eapie\error;
class admin_config extends \eapie\source\request\shop {
	
	
	/*商家配置*/
	
	
	
	
	/**
	 * 获取配置数据
	 * $data = array(
	 * 	"config_id" 获取某一个配置
	 * )
	 * 
	 * SHOPADMINCONFIGDATA
	 * {"class":"shop/admin_config","method":"api_data"} 
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_data($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_CONFIG_READ);
		
		$config = array(
			'shop_order_user_comment'
		);
		
		if( isset($data['config_id']) ){
			object(parent::ERROR)->check($data, 'config_id', parent::TABLE_CONFIG, array('args'));
			if( !in_array($data['config_id'], $config)){
				return NULL; 
			}
			$output = object(parent::TABLE_CONFIG)->find($data['config_id']);
			if( empty($output)){
				return NULL;
			}
			
			$output = object(parent::TABLE_CONFIG)->data($output);
		}else{
			
			$select_config = array(
				"where" => array(),
				"orderby" => array()
			);
			$select_config['where'][] = array("config_id IN ([-])", "\"".implode("\",\"", $config)."\"", true);
			$select_config['orderby'][] = array('config_sort', false);
			$select_config['orderby'][] = array('config_id', false);
			
			$output = object(parent::TABLE_CONFIG)->select($select_config);
			if( empty($output)){
				return NULL;
			}
			
			foreach($output as $key => $value){
				$output[$key] = object(parent::TABLE_CONFIG)->data($value);
			}
			
		}
		
		return $output;
	}
	
	
	
	
		
	/**
	 * 编辑配置信息
	 * 
	 * SHOPADMINCONFIGEDIT
	 * {"class":"shop/admin_config","method":"api_edit"} 
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_CONFIG_EDIT);
		
		//商家钱包(交易账户)提现配置
		$old = array();
		if( isset($data['shop_order_user_comment']) ){
			$old = object(parent::TABLE_CONFIG)->find('shop_order_user_comment');
			if( empty($old) ){
				throw new error('未知配置编辑');
			}else{
				$old = object(parent::TABLE_CONFIG)->data($old);
			}

			object(parent::ERROR)->check( $data['shop_order_user_comment'], 'check', parent::TABLE_CONFIG, array('args'), "shop_order_user_comment[check]" );
			object(parent::ERROR)->check( $data['shop_order_user_comment'], 'state', parent::TABLE_CONFIG, array('args'), "shop_order_user_comment[state]" );
			
			//白名单
	        $whitelist = array(
	            'check', 
	            'state',
	        );
	        $whitelist_data = cmd(array($data['shop_order_user_comment'], $whitelist), 'arr whitelist');
			$value = cmd(array($whitelist_data), 'json encode');
			//过滤不需要更新的数据
	        if( !empty($whitelist_data) ){
	        	 foreach($whitelist_data as $k => $v){
		            if( isset($old['config_value'][$k]) 
		            && $old['config_value'][$k] == $v ){
		            	unset($whitelist_data[$k]);
		            }
		        }
	        }
			if (empty($whitelist_data)){
	        	throw new error('没有需要更新的数据');
	        }
			$bool = object(parent::TABLE_CONFIG)->update_value('shop_order_user_comment', $value);
			
		}
		
		
		//更新数据，记录日志
        if( !empty($bool) ){
            object(parent::TABLE_ADMIN_LOG)->insert($data, $old);
            return true;
        } else {
            throw new error('操作失败');
        }
	
	}
	
	
	
	
	
	
	
	
	
	
	
}
?>