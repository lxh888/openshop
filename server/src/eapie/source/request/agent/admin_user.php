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



namespace eapie\source\request\agent;
use eapie\error;
class admin_user extends \eapie\source\request\agent {
	
	
	
		
	/**
	 * 获取一条代理用户数据
	 * $data = arrray(
	 * 	agent_user_id
	 * )
	 * 
	 * AGENTADMINUSERGET
	 * {"class":"agent/admin_user","method":"api_get"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_get( $data = array() ){
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_READ);
		object(parent::ERROR)->check($data, 'agent_user_id', parent::TABLE_AGENT_USER, array('args'));
		
		$get_data = object(parent::TABLE_AGENT_USER)->find_join($data['agent_user_id']);
		if( empty($get_data) ){
			throw new error("数据不存在");
		}
		
		if( !empty($get_data['agent_user_json']) ){
			$get_data['agent_user_json'] = cmd(array($get_data['agent_user_json']), 'json decode');
		}
		
		return $get_data;
	}
	
	
	
	
		
	
	/**
	 * 获取数据列表
	 * 
	 * AGENTADMINUSERLIST
	 * {"class":"agent/admin_user","method":"api_list"}
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'region_desc' => array('agent_region_id', true),
			'region_asc' => array('agent_region_id', false),
			
			'user_id_desc' => array('user_id', true),
			'user_id_asc' => array('user_id', false),
			
			'phone_desc' => array('agent_user_phone', true),
			'phone_asc' => array('agent_user_phone', false),
			'pass_time_desc' => array('agent_user_pass_time', true),
			'pass_time_asc' => array('agent_user_pass_time', false),
			
			'state_desc' => array('agent_user_state', true),
			'state_asc' =>  array('agent_user_state', false),
			
			'insert_time_desc' => array('agent_user_insert_time', true),
			'insert_time_asc' => array('agent_user_insert_time', false),
			'update_time_desc' => array('agent_user_update_time', true),
			'update_time_asc' => array('agent_user_update_time', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('agent_user_id', false);
		
		if(!empty($data['search'])){
			if( isset($data['search']['agent_region_id']) && is_string($data['search']['agent_region_id']) ){
				$config["where"][] = array('[and] au.agent_region_id=[+]', $data['search']['agent_region_id']);
			}
			
			if( isset($data['search']['agent_region_province']) && is_string($data['search']['agent_region_province']) ){
                $config['where'][] = array('[and] ar.agent_region_province LIKE "%[-]%"', $data['search']['agent_region_province']);
            }
			if( isset($data['search']['agent_region_city']) && is_string($data['search']['agent_region_city']) ){
                $config['where'][] = array('[and] ar.agent_region_city LIKE "%[-]%"', $data['search']['agent_region_city']);
            }
			if( isset($data['search']['agent_region_district']) && is_string($data['search']['agent_region_district']) ){
                $config['where'][] = array('[and] ar.agent_region_district LIKE "%[-]%"', $data['search']['agent_region_district']);
            }
			
			//状态
			if( isset($data['search']['state']) && 
			(is_string($data['search']['state']) || is_numeric($data['search']['state'])) &&
			in_array($data['search']['state'], array("0", "1", "2")) ){
				$config["where"][] = array('[and] au.agent_user_state=[+]', $data['search']['state']);
			}
			
		}
		
		return object(parent::TABLE_AGENT_USER)->select_page($config);
		
	}
	
	
	
	
	
	
		
	/**
	 * 添加代理地区
	 * 
	 * AGENTADMINUSERADD
	 * {"class":"agent/admin_user","method":"api_add"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_ADD);
		//数据检测 
		if( isset($data['agent_user_interview_phone']) )
		object(parent::ERROR)->check($data, 'agent_user_interview_phone', parent::TABLE_AGENT_USER, array('args'));
		if( isset($data['agent_user_interview_address']) )
		object(parent::ERROR)->check($data, 'agent_user_interview_address', parent::TABLE_AGENT_USER, array('args'));
		if( isset($data['agent_user_interview_time']) )
		object(parent::ERROR)->check($data, 'agent_user_interview_time', parent::TABLE_AGENT_USER, array('args'));
		object(parent::ERROR)->check($data, 'agent_user_state', parent::TABLE_AGENT_USER, array('args'));
		
		//判断 代理地区
		if( isset($data['agent_region_id']) ){
			object(parent::ERROR)->check($data, 'agent_region_id', parent::TABLE_AGENT_REGION, array('args', 'exists'));
		}else{
			$data['agent_region_id'] = '';
		}
		
		//查询用户ID
        if( empty($data['user']) ) throw new error('请填写用户ID或用户手机号');
        $user_data = object(parent::TABLE_USER)->find_id_or_phone($data['user']);
        if( empty($user_data['user_id']) ){
        	throw new error('用户不存在');
        }
		$is_exist = object(parent::TABLE_AGENT_USER)->find_region($user_data['user_id'], $data['agent_region_id']);
		if( !empty($is_exist) ){
			throw new error("该用户已经是代理了该地区，请勿重复添加");
		}
		
		$agent_user_json = array();
		if( isset($data['agent_user_json']['user_credit_award']) ){
			object(parent::ERROR)->check($data['agent_user_json']['user_credit_award'], 'ratio', parent::TABLE_AGENT_USER, array('args'), 'user_credit_award[ratio]');
			object(parent::ERROR)->check($data['agent_user_json']['user_credit_award'], 'algorithm', parent::TABLE_AGENT_USER, array('args'), 'user_credit_award[algorithm]');
			object(parent::ERROR)->check($data['agent_user_json']['user_credit_award'], 'state', parent::TABLE_AGENT_USER, array('args'), 'user_credit_award[state]');
			$agent_user_json['user_credit_award'] = $data['agent_user_json']['user_credit_award'];
		}
		
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'agent_region_id',
			'agent_user_interview_phone',
			'agent_user_interview_address', 
			'agent_user_interview_time',
			'agent_user_state',
			'agent_user_award_state',
			);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		//获取id号
		$insert_data['agent_user_id'] = object(parent::TABLE_AGENT_USER)->get_unique_id();
		$insert_data['user_id'] = $user_data['user_id'];
		$insert_data['agent_user_award_state'] = empty($data['agent_user_award_state']) ? 0 : 1;
		//时间
		$insert_data['agent_user_update_time'] = time();
		$insert_data['agent_user_insert_time'] = time();
		
		if( isset($insert_data['agent_user_state']) && ($insert_data['agent_user_state'] == 1 || $insert_data['agent_user_state'] == 0) ){
			$insert_data['agent_user_state_time'] = time();
		}
		
		if( !empty($agent_user_json) ){
			$insert_data['agent_user_json'] = cmd(array($agent_user_json), 'json encode');
		}
		
		if( isset($insert_data['agent_user_interview_time']) ){
			$insert_data['agent_user_interview_time'] = cmd(array($insert_data['agent_user_interview_time']), "time mktime");
		}
		
		if( object(parent::TABLE_AGENT_USER)->insert($insert_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $insert_data);
			return $insert_data['agent_user_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	

			
	/**
	 * 编辑代理用户
	 * 
	 * AGENTADMINUSEREDIT
	 * {"class":"agent/admin_user","method":"api_edit"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_EDIT);
		//数据检测 
		object(parent::ERROR)->check($data, 'agent_user_id', parent::TABLE_AGENT_USER, array('args'));
		if( isset($data['agent_user_interview_phone']) )
		object(parent::ERROR)->check($data, 'agent_user_interview_phone', parent::TABLE_AGENT_USER, array('args'));
		if( isset($data['agent_user_interview_address']) )
		object(parent::ERROR)->check($data, 'agent_user_interview_address', parent::TABLE_AGENT_USER, array('args'));
		
		if( isset($data['agent_user_state']) )
		object(parent::ERROR)->check($data, 'agent_user_state', parent::TABLE_AGENT_USER, array('args'));
		
		//判断 代理地区
		if( isset($data['agent_region_id']) && $data['agent_region_id'] != '' ){
			object(parent::ERROR)->check($data, 'agent_region_id', parent::TABLE_AGENT_REGION, array('args', 'exists'));
		}
		
		//获取旧数据
        $original = object(parent::TABLE_AGENT_USER)->find($data['agent_user_id']);
        // return $original;
		if( empty($original) ){
			throw new error("ID有误，数据不存在");
		}
		
		$agent_user_json = array();
		if( !empty($original['agent_user_json']) ){
			$agent_user_json = $original['agent_user_json'];
			$agent_user_json = cmd(array($agent_user_json), 'json decode');
			if( !is_array($agent_user_json) ){
				$agent_user_json = array();
			}
		}
		
		if( isset($data['agent_user_json']['user_credit_award']) ){
			object(parent::ERROR)->check($data['agent_user_json']['user_credit_award'], 'ratio', parent::TABLE_AGENT_USER, array('args'), 'user_credit_award[ratio]');
			object(parent::ERROR)->check($data['agent_user_json']['user_credit_award'], 'algorithm', parent::TABLE_AGENT_USER, array('args'), 'user_credit_award[algorithm]');
			object(parent::ERROR)->check($data['agent_user_json']['user_credit_award'], 'state', parent::TABLE_AGENT_USER, array('args'), 'user_credit_award[state]');
			$agent_user_json['user_credit_award'] = $data['agent_user_json']['user_credit_award'];
		}
		
		if( !empty($agent_user_json) ){
			$data['agent_user_json'] = cmd(array($agent_user_json), 'json encode');
		}
		
		if( isset($data['agent_user_interview_time']) ){
			object(parent::ERROR)->check($data, 'agent_user_interview_time', parent::TABLE_AGENT_USER, array('args'));
			$data['agent_user_interview_time'] = cmd(array($data['agent_user_interview_time']), "time mktime");
		}
		

		//白名单 私密数据不能获取
		$whitelist = array(
			'agent_user_interview_phone', 
			'agent_user_interview_address', 
			'agent_user_interview_time', 
			'agent_user_fail',
			'agent_user_json',
			'agent_user_state',
			'agent_user_award_state',
			'agent_region_id'
        );
        $update_data = cmd(array($data, $whitelist), 'arr whitelist');
        // return $update_data;
		foreach($update_data as $key => $value){
			if( isset($original[$key]) ){
				if($original[$key] == $value){
					unset($update_data[$key]);
				}
			}
		}
		
		
		if( empty($update_data) ){
			throw new error("没有需要更新的数据");
		}
		
		if( isset($update_data['agent_region_id']) ){
			$is_exist = object(parent::TABLE_AGENT_USER)->find_region($original['user_id'], $data['agent_region_id']);
			if( !empty($is_exist) ){
				throw new error("该用户已经是代理了该地区，请勿重复添加");
			}
		}
		
		if( isset($update_data['agent_user_state']) ){
			if( $original['agent_user_state'] != 2 ){
				throw new error("该代理用户不是待审核状态");
			}
			
			if( $update_data['agent_user_state'] == 0 ){
				object(parent::ERROR)->check($update_data, 'agent_user_fail', parent::TABLE_AGENT_USER, array('args'));
			}
			
			$update_data['agent_user_state_time'] = time();
		}
		// return $update_data;
		
		//更新时间
		$update_data['agent_user_update_time'] = time();
		$update_data['agent_user_award_state'] = empty($data['agent_user_award_state']) ? 0 : 1;
		if( object(parent::TABLE_AGENT_USER)->update( array(array('agent_user_id=[+]', (string)$data['agent_user_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['agent_user_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
		
			
	/**
	 * 删除管理员
	 * 
	 * AGENTADMINUSERREMOVE
	 * {"class":"agent/admin_user","method":"api_remove"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_remove( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_USER_REMOVE);
		//数据检测 
		object(parent::ERROR)->check($data, 'agent_user_id', parent::TABLE_AGENT_USER, array('args'));
		
		//查询旧数据
        $original = object(parent::TABLE_AGENT_USER)->find($data['agent_user_id']);
        if( empty($original) ) throw new error('数据不存在');
		
		if( object(parent::TABLE_AGENT_USER)->remove($data['agent_user_id']) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $original);
			return $data['agent_user_id'];
		}else{
			throw new error("操作失败");
		}
		
	}


	/**
	 * 代理审核
	 * api: 
	 * Undocumented function
	 *
	 * @param [type] $data
	 * @return void
	 */
	public function api_audit($data){

		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_AGENT_STATE);
		//数据检测 
		object(parent::ERROR)->check($data, 'agent_user_id', parent::TABLE_AGENT_USER, array('args'));
		if(empty($data['agent_user_state']) || !in_array($data['agent_user_state'],array(0,1))){
			throw new error('审核参数错误');
		}
		
		if($data['agent_user_state'] == 1){
			if(empty($data['agent_region_id'])){
				throw new error('请选择代理地区');
			}
			$agent_region = object(parent::TABLE_AGENT_REGION)->find($data['agent_region_id']);
			if(empty($agent_region) || $agent_region['agent_region_state'] != 1){
				throw new error('代理地区不存在');
			}
			$audit_data = array(
				'agent_user_state'=>$data['agent_user_state'],
				'agent_region_id'=>$agent_region['agent_region_id']
			);
		}else{
			$audit_data = array(
				'agent_user_state'=>0,
			);
		}

		$where = array(
			array('agent_user_id =[+]',$data['agent_user_id'])
		);
		if(object(parent::TABLE_AGENT_USER)->update($where,$audit_data)){
			return array('agent_user_id'=>$data['agent_user_id']);
		}
		throw new error('审核失败');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>