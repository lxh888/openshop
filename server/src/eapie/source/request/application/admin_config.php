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
class admin_config extends \eapie\source\request\application {
	
	
	
	/*应用配置*/
	
	
	
	
		
	/**
	 * 获取配置数据
	 * $data = array(
	 * 	"config_id" 获取某一个配置
	 * )
	 * 
	 * APPLICATIONADMINCONFIGDATA
	 * {"class":"application/admin_config","method":"api_data"} 
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_data($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_CONFIG_READ);
		
		$config = array(
			'app_android_version',
			'weixin_mp_access',
			'weixin_applet_access',
			'weixin_app_access',
			'emshop_config',
			'shop_distribution_reward',
			'shop_recommend_reward',
			'xlt_distribution',
			'display'
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
	 * APPLICATIONADMINCONFIGEDIT
	 * {"class":"application/admin_config","method":"api_edit"} 
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_CONFIG_EDIT);
		
		//商家钱包(交易账户)提现配置
		$old = array();
		
		
		
		if( isset($data['display']) ){
			$bool = $this->_display($data);
		}else
		if( isset($data['emshop_config']) ){
			$bool = $this->_emshop_config($data);
		}else
		if( isset($data['app_android_version']) ){
			$bool = $this->_app_android_version($data);
		}else
		if( isset($data['weixin_mp_access']) ){
			$bool = $this->_weixin_mp_access($data);
		}else	
		if( isset($data['weixin_applet_access']) ){
			$bool = $this->_weixin_applet_access($data);
		}else
		if( isset($data['weixin_app_access']) ){
			$bool = $this->_weixin_app_access($data);
		}else
		if( isset($data['shop_distribution_reward']) ){
			$bool = $this->_shop_distribution_reward($data);
		}else
		if( isset($data['shop_recommend_reward']) ){
			$bool = $this->_shop_recommend_reward($data);
		}else
		if(isset($data['shop_distribution_yitao'])){
			$bool = $this->_shop_distribution_yitao($data,'shop_distribution_yitao');
		}else
		if(isset($data['shop_distributions_yitao'])){
			$bool = $this->_shop_distribution_yitao($data,'shop_distributions_yitao');
		} else
		if(isset($data['xlt_distribution'])){
			$bool = $this->_shop_distribution_xlt($data,'shop_distributions_xlt');
		}

		// return $bool;
		//更新数据，记录日志
        if( !empty($bool) ){
            object(parent::TABLE_ADMIN_LOG)->insert($data, $old);
            return true;
        } else {
            throw new error('操作失败');
        }
	
	}

	private function _shop_distribution_xlt($data){
		$old = object(parent::TABLE_CONFIG)->find('xlt_distribution');
		if( empty($old) ){
			throw new error('未知配置编辑');
		}

		if( empty($data['xlt_distribution_config']['bronze']) ){
			throw new error('分销角色青铜数据为空');
		}
		if( empty($data['xlt_distribution_config']['bronze']['admin_id']) ){
			throw new error('分销角色奖励身份ID为空');
		}
		if( empty($data['xlt_distribution_config']['bronze']['rmd_award']) ){
			throw new error('分销角色奖励直属金额为空');
		}
		if( empty($data['xlt_distribution_config']['bronze']['level_up_condition']) ){
			throw new error('分销角色身份升级人数条件为空');
		}
		if( empty($data['xlt_distribution_config']['bronze']['level_up_award']) ){
			throw new error('分销角色身份升级奖励金额');
		}
		if( empty($data['xlt_distribution_config']['bronze']['money']) ){
			throw new error('分销角色身份升级消费金额条件为空');
		}

		if( empty($data['xlt_distribution_config']['silver']) ){
			throw new error('分销角色白银数据为空');
		}
		if( empty($data['xlt_distribution_config']['gold']['admin_id']) ){
			throw new error('分销角色奖励身份ID为空');
		}
		if( empty($data['xlt_distribution_config']['silver']['rmd_award']) ){
			throw new error('分销角色奖励直属金额为空');
		}
		if( empty($data['xlt_distribution_config']['silver']['level_up_condition']) ){
			throw new error('分销角色身份升级人数条件为空');
		}
		if( empty($data['xlt_distribution_config']['silver']['level_up_award']) ){
			throw new error('分销角色身份升级奖励金额');
		}
		if( empty($data['xlt_distribution_config']['silver']['money']) ){
			throw new error('分销角色身份升级消费金额条件为空');
		}


		if( empty($data['xlt_distribution_config']['gold']) ){
			throw new error('分销角色黄金数据为空');
		}
		if( empty($data['xlt_distribution_config']['gold']['admin_id']) ){
			throw new error('分销角色奖励身份ID为空');
		}
		if( empty($data['xlt_distribution_config']['gold']['rmd_award']) ){
			throw new error('分销角色奖励直属金额为空');
		}
		if( empty($data['xlt_distribution_config']['gold']['level_up_condition']) ){
			throw new error('分销角色身份升级人数条件为空');
		}
		if( empty($data['xlt_distribution_config']['gold']['level_up_award']) ){
			throw new error('分销角色身份升级奖励金额');
		}
		if( empty($data['xlt_distribution_config']['gold']['money']) ){
			throw new error('分销角色身份升级消费金额条件为空');
		}

		if( empty($data['xlt_distribution_config']['platinum']) ){
			throw new error('分销角色白金数据为空');
		}
		if( empty($data['xlt_distribution_config']['platinum']['admin_id']) ){
			throw new error('分销角色奖励身份ID为空');
		}
		if( empty($data['xlt_distribution_config']['platinum']['rmd_award']) ){
			throw new error('分销角色奖励直属金额为空');
		}
		if( empty($data['xlt_distribution_config']['platinum']['level_up_condition']) ){
			throw new error('分销角色身份升级人数条件为空');
		}
		if( empty($data['xlt_distribution_config']['platinum']['level_up_award']) ){
			throw new error('分销角色身份升级奖励金额');
		}
		if( empty($data['xlt_distribution_config']['platinum']['money']) ){
			throw new error('分销角色身份升级消费金额条件为空');
		}

		if( empty($data['xlt_distribution_config']['king']) ){
			throw new error('分销角色王者数据为空');
		}
		if( empty($data['xlt_distribution_config']['king']['admin_id']) ){
			throw new error('分销角色奖励身份ID为空');
		}
		if( empty($data['xlt_distribution_config']['king']['rmd_award']) ){
			throw new error('分销角色奖励直属金额为空');
		}

		if( empty($data['xlt_distribution_config']['transcendence_award']) ){
			throw new error('超越奖为空');
		}

		$whitelist = array(
			'admin_id', 
			'rmd_award',
			'level_up_condition',
			'level_up_award',
			'money'
		);
		$data['xlt_distribution_config']['bronze'] = cmd(array($data['xlt_distribution_config']['bronze'], $whitelist), 'arr whitelist');

		$whitelist = array(
			'admin_id', 
			'rmd_award',
			'level_up_condition',
			'level_up_award',
			'money'
		);
		$data['xlt_distribution_config']['silver'] = cmd(array($data['xlt_distribution_config']['silver'], $whitelist), 'arr whitelist');

		$whitelist = array(
			'admin_id', 
			'rmd_award',
			'level_up_condition',
			'level_up_award',
			'money'
		);
		$data['xlt_distribution_config']['gold'] = cmd(array($data['xlt_distribution_config']['gold'], $whitelist), 'arr whitelist');

		$whitelist = array(
			'admin_id', 
			'rmd_award',
			'level_up_condition',
			'level_up_award',
			'money'
		);
		$data['xlt_distribution_config']['platinum'] = cmd(array($data['xlt_distribution_config']['platinum'], $whitelist), 'arr whitelist');

		$whitelist = array(
			'admin_id', 
			'rmd_award'
		);
		$data['xlt_distribution_config']['king'] = cmd(array($data['xlt_distribution_config']['king'], $whitelist), 'arr whitelist');


		$whitelist = array(
			'bronze', 
			'silver',
			'gold',
			'platinum',
			'king',
			'transcendence_award'
		);
		$whitelist_data = cmd(array($data['xlt_distribution_config'], $whitelist), 'arr whitelist');
		$value = cmd(array($whitelist_data), 'json encode');
		return $this->_update_value('xlt_distribution', $value, $old);
	}

	private function _shop_distribution_reward($data){
		$old = object(parent::TABLE_CONFIG)->find('shop_distribution_reward');
		if( empty($old) ){
			throw new error('未知配置编辑');
		}
		if( !isset($data['shop_distribution_reward']['is_open']) ||
		!is_numeric($data['shop_distribution_reward']['is_open']) || 
		!in_array($data['shop_distribution_reward']['is_open'], array(0, 1)) ){
			throw new error('分销奖金发放功能是否开启配置不合法');
		}
		if( empty($data['shop_distribution_reward']['member']['name']) ){
			throw new error('分销角色会员名称不合法');
		}
		if( empty($data['shop_distribution_reward']['member']['rmb_award']) ){
			throw new error('分销角色会员奖金配置不合法');
		}
		if( empty($data['shop_distribution_reward']['member']['admin_id']) ){
			throw new error('分销角色会员身份ID不合法');
		}
		if( empty($data['shop_distribution_reward']['shop_manager']['name']) ){
			throw new error('分销角色店长名称不合法');
		}
		if( empty($data['shop_distribution_reward']['shop_manager']['rmb_award']) ){
			throw new error('分销角色店长奖金配置不合法');
		}
		if( empty($data['shop_distribution_reward']['shop_manager']['admin_id']) ){
			throw new error('分销角色店长身份ID不合法');
		}
		if( empty($data['shop_distribution_reward']['chief_inspector']['name']) ){
			throw new error('分销角色总监名称不合法');
		}
		if( empty($data['shop_distribution_reward']['chief_inspector']['rmb_award']) ){
			throw new error('分销角色总监奖励规则不合法');
		}
		if( empty($data['shop_distribution_reward']['chief_inspector']['admin_id']) ){
			throw new error('分销角色总监身份ID不合法');
		}
		if( empty($data['shop_distribution_reward']['area_agent']['name']) ){
			throw new error('分销角色区域代理名称不合法');
		}
		if( empty($data['shop_distribution_reward']['area_agent']['rmb_award']) ){
			throw new error('分销角色区域代理奖励规则不合法');
		}
		if( empty($data['shop_distribution_reward']['area_agent']['admin_id']) ){
			throw new error('分销角色区域代理身份ID不合法');
		}
		if( !isset($data['shop_distribution_reward']['area_agent']['additional_rewards']) || 
		!is_numeric($data['shop_distribution_reward']['area_agent']['additional_rewards']) ){
			throw new error('分销角色区域代理额外奖金配置不合法');
		}

		$whitelist = array(
			'name', 
			'rmb_award',
			'admin_id',
			'condition'
		);
		$data['shop_distribution_reward']['member'] = cmd(array($data['shop_distribution_reward']['member'], $whitelist), 'arr whitelist');
		
		$whitelist = array(
			'name', 
			'rmb_award',
			'admin_id',
			'condition',
			'other_equity'
		);
		$data['shop_distribution_reward']['shop_manager'] = cmd(array($data['shop_distribution_reward']['shop_manager'], $whitelist), 'arr whitelist');
		$data['shop_distribution_reward']['chief_inspector'] = cmd(array($data['shop_distribution_reward']['chief_inspector'], $whitelist), 'arr whitelist');
		$whitelist = array(
			'name', 
			'rmb_award',
			'admin_id',
			'additional_rewards',
			'condition',
			'other_equity'
		);
		$data['shop_distribution_reward']['area_agent'] = cmd(array($data['shop_distribution_reward']['area_agent'], $whitelist), 'arr whitelist');

		//白名单
        $whitelist = array( 
			'is_open',
			'member',
			'shop_manager',
			'chief_inspector',
			'area_agent'
		);
		$whitelist_data = cmd(array($data['shop_distribution_reward'], $whitelist), 'arr whitelist');
		$value = cmd(array($whitelist_data), 'json encode');
		return $this->_update_value('shop_distribution_reward', $value, $old);
	}

	private function _shop_recommend_reward($data){
		$old = object(parent::TABLE_CONFIG)->find('shop_recommend_reward');

		if( empty($old) ){
			throw new error('未知配置编辑');
		}
		if( !isset($data['shop_recommend_reward']['is_open']) ||
		!is_numeric($data['shop_recommend_reward']['is_open']) || 
		!in_array($data['shop_recommend_reward']['is_open'], array(0, 1)) ){
			throw new error('推荐商品奖金发放功能是否开启配置不合法');
		}
		if( empty($data['shop_recommend_reward']['shop_manager']['admin_id']) ){
			throw new error('身份ID不合法');
		}
		if( empty($data['shop_recommend_reward']['shop_manager']['royalty']) ){
			throw new error('店长奖金比例不合法');
		}
		if( empty($data['shop_recommend_reward']['chief_inspector']['admin_id']) ){
			throw new error('身份ID不合法');
		}
		if( empty($data['shop_recommend_reward']['chief_inspector']['royalty']) ){
			throw new error('总监奖金比例不合法');
		}
		if( empty($data['shop_recommend_reward']['area_agent']['admin_id']) ){
			throw new error('身份ID不合法');
		}
		if( empty($data['shop_recommend_reward']['area_agent']['royalty']) ){
			throw new error('区域代理奖金比例不合法');
		}
		if( !isset($data['shop_recommend_reward']['method']) ||
		!is_numeric($data['shop_recommend_reward']['method']) || 
		!in_array($data['shop_recommend_reward']['method'], array(0, 1, 2, 3))){
			throw new error('推荐奖金发放方式配置不合法');
		}
		if( empty($data['shop_recommend_reward']['max_royalty_random']) ){
			throw new error('推荐奖金发放最大比例不合法');
		}
		if( empty($data['shop_recommend_reward']['min_royalty_random']) ){
			throw new error('推荐奖金发放最小比例不合法');
		}
		if( !isset($data['shop_recommend_reward']['quota_recommend_money']) || 
		!is_numeric($data['shop_recommend_reward']['quota_recommend_money']) ){
			throw new error('推荐奖金发放固定发放金额配置不合法');
		}

		//白名单
		$whitelist = array(
			'name',
			'admin_id',
			'royalty',
		);
		$data['shop_recommend_reward']['shop_manager'] = cmd(array($data['shop_recommend_reward']['shop_manager'], $whitelist), 'arr whitelist');
		$data['shop_recommend_reward']['chief_inspector'] = cmd(array($data['shop_recommend_reward']['chief_inspector'], $whitelist), 'arr whitelist');
		$data['shop_recommend_reward']['area_agent'] = cmd(array($data['shop_recommend_reward']['area_agent'], $whitelist), 'arr whitelist');


		//白名单
        $whitelist = array(
			'is_open',
			'shop_manager',
			'chief_inspector',
			'area_agent',
            'method', 
            'max_royalty_random',
			'min_royalty_random',
			'quota_recommend_money'
        );
		$whitelist_data = cmd(array($data['shop_recommend_reward'], $whitelist), 'arr whitelist');
		$value = cmd(array($whitelist_data), 'json encode');

		return $this->_update_value('shop_recommend_reward', $value, $old);
	}
	
	
	
	
	private function _emshop_config($data){
		$old = object(parent::TABLE_CONFIG)->find('emshop_config');
		if( empty($old) ){
			throw new error('未知配置编辑');
		}
		if( empty($data['emshop_config']['daily_attendance']) ){
			throw new error('签到送积分配置不合法');
		}
		if( !isset($data['emshop_config']['daily_attendance']['user']) ||
		!is_numeric($data['emshop_config']['daily_attendance']['user']) ){
			throw new error('普通用户签到奖励送积分配置不合法');
		}
		if( !isset($data['emshop_config']['daily_attendance']['member']) ||
		!is_numeric($data['emshop_config']['daily_attendance']['member']) ){
			throw new error('会员签到奖励送积分配置不合法');
		}
		
		if( empty($data['emshop_config']['register_credit']) ){
			throw new error('推荐注册送积分配置不合法');
		}
		if( !isset($data['emshop_config']['register_credit']['user']) ||
		!is_numeric($data['emshop_config']['register_credit']['user']) ){
			throw new error('普通用户推荐奖励送积分配置不合法');
		}
		if( !isset($data['emshop_config']['register_credit']['member']) ||
		!is_numeric($data['emshop_config']['register_credit']['member']) ){
			throw new error('会员推荐奖励送积分配置不合法');
		}
		//白名单
        $whitelist = array(
            'user', 
            'member',
        );
        $data['emshop_config']['register_credit'] = cmd(array($data['emshop_config']['register_credit'], $whitelist), 'arr whitelist');
		$data['emshop_config']['daily_attendance'] = cmd(array($data['emshop_config']['daily_attendance'], $whitelist), 'arr whitelist');
		
		
		if( !isset($data['emshop_config']['register_coupon']) ){
			throw new error('注册送优惠券配置不合法');
		}
		if( !isset($data['emshop_config']['register_coupon']['state']) ||
		!is_numeric($data['emshop_config']['register_coupon']['state']) || 
		!in_array($data['emshop_config']['register_coupon']['state'], array(0, 1)) ){
			throw new error('注册送优惠券状态不合法，必须是1或0');
		}
		
		if( !isset($data['emshop_config']['register_coupon']['keyword']) ||
		!is_string($data['emshop_config']['register_coupon']['keyword'])  ){
			throw new error('注册送优惠券标签关键字不合法，必须是一个字符串');
		}
		//白名单
        $whitelist = array(
            'state', 
            'keyword',
        );
        $data['emshop_config']['register_coupon'] = cmd(array($data['emshop_config']['register_coupon'], $whitelist), 'arr whitelist');
		
		
		//白名单
        $whitelist = array(
            'daily_attendance', 
            'register_credit',
            'register_coupon'
        );
        $whitelist_data = cmd(array($data['emshop_config'], $whitelist), 'arr whitelist');
		$value = cmd(array($whitelist_data), 'json encode');
		
		return $this->_update_value('emshop_config', $value, $old);
	}
	
	
	private function _app_android_version($data){
		$old = object(parent::TABLE_CONFIG)->find('app_android_version');
		if( empty($old) ){
			throw new error('未知配置编辑');
		}
		
		object(parent::ERROR)->check( $data['app_android_version'], 'name', parent::TABLE_CONFIG, array('args'), "app_android_version[name]" );
		object(parent::ERROR)->check( $data['app_android_version'], 'info', parent::TABLE_CONFIG, array('args'), "app_android_version[info]" );
		object(parent::ERROR)->check( $data['app_android_version'], 'number', parent::TABLE_CONFIG, array('args'), "app_android_version[number]" );
		object(parent::ERROR)->check( $data['app_android_version'], 'download', parent::TABLE_CONFIG, array('args'), "app_android_version[download]" );
		object(parent::ERROR)->check( $data['app_android_version'], 'required', parent::TABLE_CONFIG, array('args'), "app_android_version[required]" );
		
		//白名单
        $whitelist = array(
            'name', 
            'info',
            'number',
            'download',
            'required',
        );
        $whitelist_data = cmd(array($data['app_android_version'], $whitelist), 'arr whitelist');
		$value = cmd(array($whitelist_data), 'json encode');
		return $this->_update_value('app_android_version', $value, $old);
	}
	
	
	
	private function _weixin_mp_access($data){
		$old = object(parent::TABLE_CONFIG)->find('weixin_mp_access');
		if( empty($old) ){
			throw new error('未知配置编辑');
		}
		
		object(parent::ERROR)->check( $data['weixin_mp_access'], 'id', parent::TABLE_CONFIG, array('args'), "weixin_mp_access[id]" );
		object(parent::ERROR)->check( $data['weixin_mp_access'], 'secret', parent::TABLE_CONFIG, array('args'), "weixin_mp_access[secret]" );
		
		//白名单
        $whitelist = array(
            'id', 
            'secret',
        );
        $whitelist_data = cmd(array($data['weixin_mp_access'], $whitelist), 'arr whitelist');
		$value = cmd(array($whitelist_data), 'json encode');
		
		return $this->_update_value('weixin_mp_access', $value, $old);
	}
	
	
	
	private function _weixin_applet_access($data){
		$old = object(parent::TABLE_CONFIG)->find('weixin_applet_access');
		if( empty($old) ){
			throw new error('未知配置编辑');
		}
		
		object(parent::ERROR)->check( $data['weixin_applet_access'], 'id', parent::TABLE_CONFIG, array('args'), "weixin_applet_access[id]" );
		object(parent::ERROR)->check( $data['weixin_applet_access'], 'secret', parent::TABLE_CONFIG, array('args'), "weixin_applet_access[secret]" );
		
		//白名单
        $whitelist = array(
            'id', 
            'secret',
        );
        $whitelist_data = cmd(array($data['weixin_applet_access'], $whitelist), 'arr whitelist');
		$value = cmd(array($whitelist_data), 'json encode');
		
		return $this->_update_value('weixin_applet_access', $value, $old);
	}
	
	
	
	private function _weixin_app_access($data){
		$old = object(parent::TABLE_CONFIG)->find('weixin_app_access');
		if( empty($old) ){
			throw new error('未知配置编辑');
		}
		
		object(parent::ERROR)->check( $data['weixin_app_access'], 'id', parent::TABLE_CONFIG, array('args'), "weixin_app_access[id]" );
		object(parent::ERROR)->check( $data['weixin_app_access'], 'secret', parent::TABLE_CONFIG, array('args'), "weixin_app_access[secret]" );
		
		//白名单
        $whitelist = array(
            'id', 
            'secret',
        );
        $whitelist_data = cmd(array($data['weixin_app_access'], $whitelist), 'arr whitelist');
		$value = cmd(array($whitelist_data), 'json encode');
		
		return $this->_update_value('weixin_app_access', $value, $old);
	}
	
	
	
	private function _display($data){
		$old = object(parent::TABLE_CONFIG)->find('display');
		if( empty($old) ){
			throw new error('未知配置编辑');
		}
		
		if( !is_string($data['display']) && !is_numeric($data['display']) ){
			throw new error('值数据类型不合法');
		}
		
		if( !in_array($data['display'], array(0, 1)) ){
			throw new error('值必须是0或1');
		}
		
		return $this->_update_value('display', $data['display'], $old);
	}
	
	
	
	
	/**
	 * @param	string	$config_id	配置ID
	 * @param	array	$value		新值
	 * @param	array	$old		旧值
	 */
	private function _update_value($config_id, $value, $old){
		if( $old['config_value'] == $value ){
        	throw new error('没有需要更新的数据');
        }
		return object(parent::TABLE_CONFIG)->update_value($config_id, $value);
	}
	
	
	
	/**
	 * 获取支付配置数据
	 * $data = array(
	 * 	"config_id" 获取某一个配置
	 * )
	 * 
	 * APPLICATIONADMINCONFIGPAYDATA
	 * {"class":"application/admin_config","method":"api_pay_data"} 
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_pay_data($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_CONFIG_PAY_READ);
		
		$config = array(
			'alipay_access',
			'alipay_withdraw_access',
			'weixin_pay_access'
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
	 * 编辑支付配置信息
	 * 
	 * APPLICATIONADMINCONFIGPAYEDIT
	 * {"class":"application/admin_config","method":"api_pay_edit"} 
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_pay_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_CONFIG_PAY_EDIT);
		
		//商家钱包(交易账户)提现配置
		$old = array();
		if( isset($data['alipay_access']) ){
			$old = object(parent::TABLE_CONFIG)->find('alipay_access');
			if( empty($old) ){
				throw new error('未知配置编辑');
			}else{
				$old = object(parent::TABLE_CONFIG)->data($old);
			}

			object(parent::ERROR)->check( $data['alipay_access'], 'id', parent::TABLE_CONFIG, array('args'), "alipay_access[id]" );
			object(parent::ERROR)->check( $data['alipay_access'], 'rsa_private_key', parent::TABLE_CONFIG, array('args'), "alipay_access[rsa_private_key]" );
			object(parent::ERROR)->check( $data['alipay_access'], 'alipayrsa_public_key', parent::TABLE_CONFIG, array('args'), "alipay_access[alipayrsa_public_key]" );
			object(parent::ERROR)->check( $data['alipay_access'], 'state', parent::TABLE_CONFIG, array('args'), "alipay_access[state]" );
			
			//白名单
	        $whitelist = array(
	            'id', 
	            'rsa_private_key', 
	            'alipayrsa_public_key', 
	            'state',
	        );
	        $whitelist_data = cmd(array($data['alipay_access'], $whitelist), 'arr whitelist');
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
			$bool = object(parent::TABLE_CONFIG)->update_value('alipay_access', $value);
			
		}else
		
		
		if( isset($data['alipay_withdraw_access']) ){
			$old = object(parent::TABLE_CONFIG)->find('alipay_withdraw_access');
			if( empty($old) ){
				throw new error('未知配置编辑');
			}else{
				$old = object(parent::TABLE_CONFIG)->data($old);
			}

			object(parent::ERROR)->check( $data['alipay_withdraw_access'], 'id', parent::TABLE_CONFIG, array('args'), "alipay_withdraw_access[id]" );
			object(parent::ERROR)->check( $data['alipay_withdraw_access'], 'rsa_private_key', parent::TABLE_CONFIG, array('args'), "alipay_withdraw_access[rsa_private_key]" );
			object(parent::ERROR)->check( $data['alipay_withdraw_access'], 'alipayrsa_public_key', parent::TABLE_CONFIG, array('args'), "alipay_withdraw_access[alipayrsa_public_key]" );
			object(parent::ERROR)->check( $data['alipay_withdraw_access'], 'state', parent::TABLE_CONFIG, array('args'), "alipay_withdraw_access[state]" );
			
			//白名单
	        $whitelist = array(
	            'id', 
	            'rsa_private_key', 
	            'alipayrsa_public_key', 
	            'state',
	        );
	        $whitelist_data = cmd(array($data['alipay_withdraw_access'], $whitelist), 'arr whitelist');
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
			$bool = object(parent::TABLE_CONFIG)->update_value('alipay_withdraw_access', $value);
			
		}else
		
		
		//微信支付商户配置
		if( isset($data['weixin_pay_access']) ){
			$old = object(parent::TABLE_CONFIG)->find('weixin_pay_access');
			if( empty($old) ){
				throw new error('未知配置编辑');
			}else{
				$old = object(parent::TABLE_CONFIG)->data($old);
			}

			object(parent::ERROR)->check( $data['weixin_pay_access'], 'mch_id', parent::TABLE_CONFIG, array('args'), "weixin_pay_access[mch_id]" );
			object(parent::ERROR)->check( $data['weixin_pay_access'], 'pay_key', parent::TABLE_CONFIG, array('args'), "weixin_pay_access[pay_key]" );
			object(parent::ERROR)->check( $data['weixin_pay_access'], 'spbill_create_ip', parent::TABLE_CONFIG, array('args'), "weixin_pay_access[spbill_create_ip]" );
			object(parent::ERROR)->check( $data['weixin_pay_access'], 'ssl_cert', parent::TABLE_CONFIG, array('args'), "weixin_pay_access[ssl_cert]" );
			object(parent::ERROR)->check( $data['weixin_pay_access'], 'ssl_key', parent::TABLE_CONFIG, array('args'), "weixin_pay_access[ssl_key]" );
			object(parent::ERROR)->check( $data['weixin_pay_access'], 'state', parent::TABLE_CONFIG, array('args'), "weixin_pay_access[state]" );
			
			if( isset($data['weixin_pay_access']['service_appid']) && 
			!is_string($data['weixin_pay_access']['service_appid']) && 
			!is_numeric($data['weixin_pay_access']['service_appid']) ){
				throw new error('微信支付服务商的APPID不合法');
			}
			if( isset($data['weixin_pay_access']['service_mch_id']) && 
			!is_string($data['weixin_pay_access']['service_mch_id']) && 
			!is_numeric($data['weixin_pay_access']['service_mch_id']) ){
				throw new error('微信支付服务商的商户号不合法');
			}
			
			//白名单
	        $whitelist = array(
	            'mch_id', 
	            'pay_key', 
	            'spbill_create_ip', 
	            'ssl_cert',
	            'ssl_key',
	            'state',
	            'service_appid',
	            'service_mch_id'
	        );
	        $whitelist_data = cmd(array($data['weixin_pay_access'], $whitelist), 'arr whitelist');
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
			$bool = object(parent::TABLE_CONFIG)->update_value('weixin_pay_access', $value);
			
		}
				
		
		//更新数据，记录日志
        if( !empty($bool) ){
            object(parent::TABLE_ADMIN_LOG)->insert($data, $old);
            return true;
        } else {
            throw new error('操作失败');
        }
	
	}
	
	
	
	
	private function _shop_distribution_yitao($data,$field=''){
        // $old = object(parent::TABLE_CONFIG)->find('shop_distribution_yitao');
        $old = object(parent::TABLE_CONFIG)->find($field);
		if( empty($old) ){
			throw new error('未知配置编辑');
		}
		if( !isset($data[$field]['is_open']) ||
		!is_numeric($data[$field]['is_open']) || 
		!in_array($data[$field]['is_open'], array(0, 1)) ){
			throw new error('分销奖金发放功能是否开启配置不合法');
		}
		if( !isset($data[$field]['shop_manager_reward']['one_level_royal']) ){
			throw new error('门槛商品一级提成不能为空');
		}
		if( !isset($data[$field]['shop_manager_reward']['two_level_royal']) ){
			throw new error('门槛商品二级提成不能为空');
		}
		if( !isset($data[$field]['shop_manager_reward']['region_money']) ){
			throw new error('门槛商品区域管理费不能为空');
		}

		$whitelist = array(
			'one_level_royal', 
			'two_level_royal',
			'region_money'
		);
		$data[$field]['shop_manager_reward'] = cmd(array($data[$field]['shop_manager_reward'], $whitelist), 'arr whitelist');
		

		//白名单
        $whitelist = array( 
			'is_open',
			'shop_manager_reward',
			'shop_reward'
		);
		$whitelist_data = cmd(array($data[$field], $whitelist), 'arr whitelist');
		$value = cmd(array($whitelist_data), 'json encode');
		return $this->_update_value($field, $value, $old);
	}
    
	
	
	
	
	
	
	
	
	
}
?>