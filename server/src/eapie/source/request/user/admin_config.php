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



namespace eapie\source\request\user;
use eapie\main;
use eapie\error;
class admin_config extends \eapie\source\request\user {
	
	
	/*用户设置接口*/
	
	
	
	/**
	 * 获取配置数据
	 * $data = array(
	 * 	"config_id" 获取某一个配置
	 * )
	 * 
	 * USERADMINCONFIGDATA
	 * {"class":"user/admin_config","method":"api_data"} 
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_data($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_CONFIG_READ);
		
		$config = array(
			'rmb_withdraw_user_money_earning',
			'daily_attendance_earn_user_credit',
			'user_money_earning_transfer_user_money',
			'user_money_earning_transfer_user_money_help',
			'rmb_consume_user_credit',
			'user_money_share_conversion_annuity_earning_help',
			'user_identity',
			'user_credit_conversion_user_money_share',
			'parent_recommend_user_credit',
			'rmb_withdraw_user_money',
			'recommend_reward_user_money'
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
	 * USERADMINCONFIGEDIT
	 * {"class":"user/admin_config","method":"api_edit"} 
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_CONFIG_EDIT);
		
		$old = array();
		
		
		//用户推荐奖励钱包配置 
		if( isset($data['recommend_reward_user_money']) ){
			$old = object(parent::TABLE_CONFIG)->find('recommend_reward_user_money');
			if( empty($old) ){
				throw new error('未知配置编辑');
			}else{
				$old = object(parent::TABLE_CONFIG)->data($old);
			}

			object(parent::ERROR)->check( $data['recommend_reward_user_money'], 'money', parent::TABLE_CONFIG, array('args'), "recommend_reward_user_money[money]" );
			object(parent::ERROR)->check( $data['recommend_reward_user_money'], 'money_min', parent::TABLE_CONFIG, array('args'), "recommend_reward_user_money[money_min]" );
			object(parent::ERROR)->check( $data['recommend_reward_user_money'], 'money_max', parent::TABLE_CONFIG, array('args'), "recommend_reward_user_money[money_max]" );
			object(parent::ERROR)->check( $data['recommend_reward_user_money'], 'random', parent::TABLE_CONFIG, array('args'), "recommend_reward_user_money[random]" );
			object(parent::ERROR)->check( $data['recommend_reward_user_money'], 'state', parent::TABLE_CONFIG, array('args'), "recommend_reward_user_money[state]" );
			
			//白名单
	        $whitelist = array(
	            'money', 
	            'money_min', 
	            'money_max',
	            'random',
	            'state',
	        );
	        $whitelist_data = cmd(array($data['recommend_reward_user_money'], $whitelist), 'arr whitelist');
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
			$bool = object(parent::TABLE_CONFIG)->update_value('recommend_reward_user_money', $value);
			
		}else
		
		
		
		//用户的钱包提现配置
		if( isset($data['rmb_withdraw_user_money']) ){
			$old = object(parent::TABLE_CONFIG)->find('rmb_withdraw_user_money');
			if( empty($old) ){
				throw new error('未知配置编辑');
			}else{
				$old = object(parent::TABLE_CONFIG)->data($old);
			}

			object(parent::ERROR)->check( $data['rmb_withdraw_user_money'], 'min_user_money', parent::TABLE_CONFIG, array('args'), "rmb_withdraw_user_money[min_user_money]" );
			object(parent::ERROR)->check( $data['rmb_withdraw_user_money'], 'max_user_money', parent::TABLE_CONFIG, array('args'), "rmb_withdraw_user_money[max_user_money]" );
			object(parent::ERROR)->check( $data['rmb_withdraw_user_money'], 'ratio_service_money', parent::TABLE_CONFIG, array('args'), "rmb_withdraw_user_money[ratio_service_money]" );
			object(parent::ERROR)->check( $data['rmb_withdraw_user_money'], 'algorithm', parent::TABLE_CONFIG, array('args'), "rmb_withdraw_user_money[algorithm]" );
			object(parent::ERROR)->check( $data['rmb_withdraw_user_money'], 'pay_password_state', parent::TABLE_CONFIG, array('args'), "rmb_withdraw_user_money[pay_password_state]" );
			object(parent::ERROR)->check( $data['rmb_withdraw_user_money'], 'user_identity_state', parent::TABLE_CONFIG, array('args'), "rmb_withdraw_user_money[user_identity_state]" );
			object(parent::ERROR)->check( $data['rmb_withdraw_user_money'], 'state', parent::TABLE_CONFIG, array('args'), "rmb_withdraw_user_money[state]" );
			
			//白名单
	        $whitelist = array(
	            'min_user_money', 
	            'max_user_money', 
	            'ratio_service_money',
	            'algorithm',
	            'pay_password_state',
	            'user_identity_state',
	            'state',
	        );
	        $whitelist_data = cmd(array($data['rmb_withdraw_user_money'], $whitelist), 'arr whitelist');
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
			$bool = object(parent::TABLE_CONFIG)->update_value('rmb_withdraw_user_money', $value);
			
		}else
			
			
		//用户的赠送收益金额提现配置	
		if( isset($data['rmb_withdraw_user_money_earning']) ){
			$old = object(parent::TABLE_CONFIG)->find('rmb_withdraw_user_money_earning');
			if( empty($old) ){
				throw new error('未知配置编辑');
			}else{
				$old = object(parent::TABLE_CONFIG)->data($old);
			}

			object(parent::ERROR)->check( $data['rmb_withdraw_user_money_earning'], 'min_user_money_earning', parent::TABLE_CONFIG, array('args'), "rmb_withdraw_user_money_earning[min_user_money_earning]" );
			object(parent::ERROR)->check( $data['rmb_withdraw_user_money_earning'], 'max_user_money_earning', parent::TABLE_CONFIG, array('args'), "rmb_withdraw_user_money_earning[max_user_money_earning]" );
			object(parent::ERROR)->check( $data['rmb_withdraw_user_money_earning'], 'ratio_user_money_service', parent::TABLE_CONFIG, array('args'), "rmb_withdraw_user_money_earning[ratio_user_money_service]" );
			object(parent::ERROR)->check( $data['rmb_withdraw_user_money_earning'], 'algorithm', parent::TABLE_CONFIG, array('args'), "rmb_withdraw_user_money_earning[algorithm]" );
			object(parent::ERROR)->check( $data['rmb_withdraw_user_money_earning'], 'pay_password_state', parent::TABLE_CONFIG, array('args'), "rmb_withdraw_user_money_earning[pay_password_state]" );
			object(parent::ERROR)->check( $data['rmb_withdraw_user_money_earning'], 'user_identity_state', parent::TABLE_CONFIG, array('args'), "rmb_withdraw_user_money_earning[user_identity_state]" );
			object(parent::ERROR)->check( $data['rmb_withdraw_user_money_earning'], 'state', parent::TABLE_CONFIG, array('args'), "rmb_withdraw_user_money_earning[state]" );
			
			//白名单
	        $whitelist = array(
	            'min_user_money_earning', 
	            'max_user_money_earning', 
	            'ratio_user_money_service',
	            'algorithm',
	            'pay_password_state',
	            'user_identity_state',
	            'state',
	        );
	        $whitelist_data = cmd(array($data['rmb_withdraw_user_money_earning'], $whitelist), 'arr whitelist');
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
			$bool = object(parent::TABLE_CONFIG)->update_value('rmb_withdraw_user_money_earning', $value);
			
		}else
			
			
			
			
		//用户每日签到赠送积分	
		if( isset($data['daily_attendance_earn_user_credit']) ){
			$old = object(parent::TABLE_CONFIG)->find('daily_attendance_earn_user_credit');
			if( empty($old) ){
				throw new error('未知配置编辑');
			}else{
				$old = object(parent::TABLE_CONFIG)->data($old);
			}			
			object(parent::ERROR)->check( $data['daily_attendance_earn_user_credit'], 'credit', parent::TABLE_CONFIG, array('args'), "daily_attendance_earn_user_credit[credit]" );
			object(parent::ERROR)->check( $data['daily_attendance_earn_user_credit'], 'state', parent::TABLE_CONFIG, array('args'), "daily_attendance_earn_user_credit[state]" );
			//白名单
	        $whitelist = array(
	            'credit', 
	            'state',
	        );
	        $whitelist_data = cmd(array($data['daily_attendance_earn_user_credit'], $whitelist), 'arr whitelist');
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
			$bool = object(parent::TABLE_CONFIG)->update_value('daily_attendance_earn_user_credit', $value);		
			
		}else
		
		
		//消费用户推荐人与商家用户推荐人的平台积分奖励配置
		if( isset($data['parent_recommend_user_credit']) ){
			$old = object(parent::TABLE_CONFIG)->find('parent_recommend_user_credit');
			if( empty($old) ){
				throw new error('未知配置编辑');
			}else{
				$old = object(parent::TABLE_CONFIG)->data($old);
			}	
			
			object(parent::ERROR)->check( $data['parent_recommend_user_credit'], 'ratio_user_credit', parent::TABLE_CONFIG, array('args'), "parent_recommend_user_credit[ratio_user_credit]" );
			object(parent::ERROR)->check( $data['parent_recommend_user_credit'], 'ratio_merchant_user_credit', parent::TABLE_CONFIG, array('args'), "parent_recommend_user_credit[ratio_merchant_user_credit]" );
			object(parent::ERROR)->check( $data['parent_recommend_user_credit'], 'algorithm', parent::TABLE_CONFIG, array('args'), "parent_recommend_user_credit[algorithm]" );
			object(parent::ERROR)->check( $data['parent_recommend_user_credit'], 'state', parent::TABLE_CONFIG, array('args'), "parent_recommend_user_credit[state]" );
			
			//白名单
	        $whitelist = array(
	            'ratio_user_credit', 
	            'ratio_merchant_user_credit', 
	            'algorithm', 
	            'state',
	        );
	        $whitelist_data = cmd(array($data['parent_recommend_user_credit'], $whitelist), 'arr whitelist');
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
			$bool = object(parent::TABLE_CONFIG)->update_value('parent_recommend_user_credit', $value);	
			
		}else
		
		
		//用户消费平台赠送积分配置
		if( isset($data['rmb_consume_user_credit']) ){
			$old = object(parent::TABLE_CONFIG)->find('rmb_consume_user_credit');
			if( empty($old) ){
				throw new error('未知配置编辑');
			}else{
				$old = object(parent::TABLE_CONFIG)->data($old);
			}	
			object(parent::ERROR)->check( $data['rmb_consume_user_credit'], 'ratio_credit', parent::TABLE_CONFIG, array('args'), "rmb_consume_user_credit[ratio_credit]" );
			object(parent::ERROR)->check( $data['rmb_consume_user_credit'], 'ratio_rmb', parent::TABLE_CONFIG, array('args'), "rmb_consume_user_credit[ratio_rmb]" );
			object(parent::ERROR)->check( $data['rmb_consume_user_credit'], 'algorithm', parent::TABLE_CONFIG, array('args'), "rmb_consume_user_credit[algorithm]" );
			object(parent::ERROR)->check( $data['rmb_consume_user_credit'], 'state', parent::TABLE_CONFIG, array('args'), "rmb_consume_user_credit[state]" );
			
			//白名单
	        $whitelist = array(
	            'ratio_credit', 
	            'ratio_rmb', 
	            'algorithm', 
	            'state',
	        );
	        $whitelist_data = cmd(array($data['rmb_consume_user_credit'], $whitelist), 'arr whitelist');
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
			$bool = object(parent::TABLE_CONFIG)->update_value('rmb_consume_user_credit', $value);	
			
		}else
		
		
		//【系统自动转换】用户积分兑换为共享金配置
		if( isset($data['user_credit_conversion_user_money_share']) ){
			$old = object(parent::TABLE_CONFIG)->find('user_credit_conversion_user_money_share');
			if( empty($old) ){
				throw new error('未知配置编辑');
			}else{
				$old = object(parent::TABLE_CONFIG)->data($old);
			}	
		
			object(parent::ERROR)->check( $data['user_credit_conversion_user_money_share'], 'basic_conversion_ratio', parent::TABLE_CONFIG, array('args'), "user_credit_conversion_user_money_share[basic_conversion_ratio]" );
			object(parent::ERROR)->check( $data['user_credit_conversion_user_money_share'], 'min_conversion_ratio', parent::TABLE_CONFIG, array('args'), "user_credit_conversion_user_money_share[min_conversion_ratio]" );
			object(parent::ERROR)->check( $data['user_credit_conversion_user_money_share'], 'max_conversion_ratio', parent::TABLE_CONFIG, array('args'), "user_credit_conversion_user_money_share[max_conversion_ratio]" );
			object(parent::ERROR)->check( $data['user_credit_conversion_user_money_share'], 'precision_conversion_ratio', parent::TABLE_CONFIG, array('args'), "user_credit_conversion_user_money_share[precision_conversion_ratio]" );
			object(parent::ERROR)->check( $data['user_credit_conversion_user_money_share'], 'multiple_user_credit', parent::TABLE_CONFIG, array('args'), "user_credit_conversion_user_money_share[multiple_user_credit]" );
			object(parent::ERROR)->check( $data['user_credit_conversion_user_money_share'], 'state', parent::TABLE_CONFIG, array('args'), "user_credit_conversion_user_money_share[state]" );
			
			if( $data['user_credit_conversion_user_money_share']['min_conversion_ratio'] > $data['user_credit_conversion_user_money_share']['max_conversion_ratio']){
				throw new error('最小的转换率不能大于最大的转换率');
			}
			
			if( $data['user_credit_conversion_user_money_share']['precision_conversion_ratio'] < 1 ){
				throw new error('转换率的精度不能小于1');
			}
			
			if( $data['user_credit_conversion_user_money_share']['min_conversion_ratio'] * $data['user_credit_conversion_user_money_share']['multiple_user_credit'] < 1 ){
				throw new error('最小的转换率与积分倍数设置不合理');
			}
			
			//白名单
	        $whitelist = array(
	            'basic_conversion_ratio', 
	            'min_conversion_ratio', 
	            'max_conversion_ratio', 
	            'precision_conversion_ratio', 
	            'multiple_user_credit', 
	            'state',
	        );
	        $whitelist_data = cmd(array($data['user_credit_conversion_user_money_share'], $whitelist), 'arr whitelist');
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
			$bool = object(parent::TABLE_CONFIG)->update_value('user_credit_conversion_user_money_share', $value);
			
		}else
		
		
		//用户实名认证配置
		if( isset($data['user_identity']) ){
			$old = object(parent::TABLE_CONFIG)->find('user_identity');
			if( empty($old) ){
				throw new error('未知配置编辑');
			}else{
				$old = object(parent::TABLE_CONFIG)->data($old);
			}	
			object(parent::ERROR)->check( $data['user_identity'], 'expire_time', parent::TABLE_CONFIG, array('args'), "user_identity[expire_time]" );
			object(parent::ERROR)->check( $data['user_identity'], 'expire_state', parent::TABLE_CONFIG, array('args'), "user_identity[expire_state]" );
			object(parent::ERROR)->check( $data['user_identity'], 'auto_state', parent::TABLE_CONFIG, array('args'), "user_identity[auto_state]" );
			
			//白名单
	        $whitelist = array(
	            'expire_time', 
	            'expire_state', 
	            'auto_state', 
	        );
	        $whitelist_data = cmd(array($data['user_identity'], $whitelist), 'arr whitelist');
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
			
			$bool = object(parent::TABLE_CONFIG)->update_value('user_identity', $value);
			
		}else
		
		
		//用户的赠送收益金额转账到用户钱包
		if( isset($data['user_money_earning_transfer_user_money']) ){
			$old = object(parent::TABLE_CONFIG)->find('user_money_earning_transfer_user_money');
			if( empty($old) ){
				throw new error('未知配置编辑');
			}else{
				$old = object(parent::TABLE_CONFIG)->data($old);
			}	
			
			object(parent::ERROR)->check( $data['user_money_earning_transfer_user_money'], 'min_user_money_earning', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money[min_user_money_earning]" );
			object(parent::ERROR)->check( $data['user_money_earning_transfer_user_money'], 'max_user_money_earning', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money[max_user_money_earning]" );
			object(parent::ERROR)->check( $data['user_money_earning_transfer_user_money'], 'ratio_user_money_service', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money[ratio_user_money_service]" );
			object(parent::ERROR)->check( $data['user_money_earning_transfer_user_money'], 'algorithm', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money[algorithm]" );
			object(parent::ERROR)->check( $data['user_money_earning_transfer_user_money'], 'pay_password_state', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money[pay_password_state]" );
			object(parent::ERROR)->check( $data['user_money_earning_transfer_user_money'], 'user_identity_state', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money[user_identity_state]" );
			object(parent::ERROR)->check( $data['user_money_earning_transfer_user_money'], 'state', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money[state]" );
			
			//白名单
	        $whitelist = array(
	            'min_user_money_earning', 
	            'max_user_money_earning',
	            'ratio_user_money_service',
	            'algorithm',
	            'pay_password_state',
	            'user_identity_state',
	            'state', 
	        );
	        $whitelist_data = cmd(array($data['user_money_earning_transfer_user_money'], $whitelist), 'arr whitelist');
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
			
			$bool = object(parent::TABLE_CONFIG)->update_value('user_money_earning_transfer_user_money', $value);
			
		}else
		
		
		//用户的赠送收益金额转账到用户扶贫账户
		if( isset($data['user_money_earning_transfer_user_money_help']) ){
			$old = object(parent::TABLE_CONFIG)->find('user_money_earning_transfer_user_money_help');
			if( empty($old) ){
				throw new error('未知配置编辑');
			}else{
				$old = object(parent::TABLE_CONFIG)->data($old);
			}	
			
			object(parent::ERROR)->check( $data['user_money_earning_transfer_user_money_help'], 'min_user_money_earning', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money_help[min_user_money_earning]" );
			object(parent::ERROR)->check( $data['user_money_earning_transfer_user_money_help'], 'max_user_money_earning', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money_help[max_user_money_earning]" );
			object(parent::ERROR)->check( $data['user_money_earning_transfer_user_money_help'], 'ratio_user_money_service', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money_help[ratio_user_money_service]" );
			object(parent::ERROR)->check( $data['user_money_earning_transfer_user_money_help'], 'algorithm', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money_help[algorithm]" );
			object(parent::ERROR)->check( $data['user_money_earning_transfer_user_money_help'], 'pay_password_state', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money_help[pay_password_state]" );
			object(parent::ERROR)->check( $data['user_money_earning_transfer_user_money_help'], 'user_identity_state', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money_help[user_identity_state]" );
			object(parent::ERROR)->check( $data['user_money_earning_transfer_user_money_help'], 'state', parent::TABLE_CONFIG, array('args'), "user_money_earning_transfer_user_money_help[state]" );
			
			//白名单
	        $whitelist = array(
	            'min_user_money_earning', 
	            'max_user_money_earning',
	            'ratio_user_money_service',
	            'algorithm',
	            'pay_password_state',
	            'user_identity_state',
	            'state', 
	        );
	        $whitelist_data = cmd(array($data['user_money_earning_transfer_user_money_help'], $whitelist), 'arr whitelist');
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
			
			$bool = object(parent::TABLE_CONFIG)->update_value('user_money_earning_transfer_user_money_help', $value);
			
		}else


		//用户消费共享金兑换为养老金、赠送收益、扶贫资金账户配置
		if( isset($data['user_money_share_conversion_annuity_earning_help']) ){
			$old = object(parent::TABLE_CONFIG)->find('user_money_share_conversion_annuity_earning_help');
			if( empty($old) ){
				throw new error('未知配置编辑');
			}else{
				$old = object(parent::TABLE_CONFIG)->data($old);
			}	
			
			object(parent::ERROR)->check( $data['user_money_share_conversion_annuity_earning_help'], 'multiple_user_money_share', parent::TABLE_CONFIG, array('args'), "user_money_share_conversion_annuity_earning_help[multiple_user_money_share]" );
			object(parent::ERROR)->check( $data['user_money_share_conversion_annuity_earning_help'], 'ratio_user_money_help', parent::TABLE_CONFIG, array('args'), "user_money_share_conversion_annuity_earning_help[ratio_user_money_help]" );
			object(parent::ERROR)->check( $data['user_money_share_conversion_annuity_earning_help'], 'ratio_user_money_annuity', parent::TABLE_CONFIG, array('args'), "user_money_share_conversion_annuity_earning_help[ratio_user_money_annuity]" );
			object(parent::ERROR)->check( $data['user_money_share_conversion_annuity_earning_help'], 'ratio_user_money_earning', parent::TABLE_CONFIG, array('args'), "user_money_share_conversion_annuity_earning_help[ratio_user_money_earning]" );
			object(parent::ERROR)->check( $data['user_money_share_conversion_annuity_earning_help'], 'state', parent::TABLE_CONFIG, array('args'), "user_money_share_conversion_annuity_earning_help[state]" );
			
			//白名单
	        $whitelist = array(
	            'multiple_user_money_share', 
	            'ratio_user_money_help', 
	            'ratio_user_money_annuity', 
	            'ratio_user_money_earning',
	            'state', 
	        );
	        $whitelist_data = cmd(array($data['user_money_share_conversion_annuity_earning_help'], $whitelist), 'arr whitelist');
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
			
			$bool = object(parent::TABLE_CONFIG)->update_value('user_money_share_conversion_annuity_earning_help', $value);
			
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