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
class admin_region extends \eapie\source\request\agent {
	
	
	/**
	 * 获取代理地区选项列表
	 *  $data = array(
	 * 	'sort' => ["sort_asc", "name_desc"] 排序
	 * );
	 * 
	 * AGENTADMINREGIONOPTION
	 * {"class":"agent/admin_region","method":"api_option"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_option($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check();
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => array(),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'scope_desc' => array('agent_region_scope', true),
			'scope_asc' => array('agent_region_scope', false),
			
			'province_desc' => array('agent_region_province', true),
			'province_asc' => array('agent_region_province', false),
			'city_desc' => array('agent_region_city', true),
			'city_asc' => array('agent_region_city', false),
			'district_desc' => array('agent_region_district', true),
			'district_asc' => array('agent_region_district', false),
			
			'state_desc' => array('agent_region_state', true),
			'state_asc' => array('agent_region_state', false),
			
			'insert_time_desc' => array('agent_region_insert_time', true),
			'insert_time_asc' => array('agent_region_insert_time', false),
			'update_time_desc' => array('agent_region_update_time', true),
			'update_time_asc' => array('agent_region_update_time', false),
			'sort_desc' => array('agent_region_sort', true),
			'sort_asc' => array('agent_region_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('agent_region_id', false);
		
		return object(parent::TABLE_AGENT_REGION)->select($config);
	}
	
	
	
	
	
	
	/**
	 * 添加代理地区
	 * 
	 * AGENTADMINREGIONADD
	 * {"class":"agent/admin_region","method":"api_add"}
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_add($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_REGION_ADD);
		//数据检测 
		object(parent::ERROR)->check($data, 'agent_region_info', parent::TABLE_AGENT_REGION, array('args'));
		object(parent::ERROR)->check($data, 'agent_region_state', parent::TABLE_AGENT_REGION, array('args'));
		object(parent::ERROR)->check($data, 'agent_region_sort', parent::TABLE_AGENT_REGION, array('args'));
		object(parent::ERROR)->check($data, 'agent_region_scope', parent::TABLE_AGENT_REGION, array('args'));
		
		if( isset($data['agent_region_details']) && $data['agent_region_details'] != '' ){
			object(parent::ERROR)->check($data, 'agent_region_details', parent::TABLE_AGENT_REGION, array('args'));
		}
		
		//判断 省市区
		if( $data['agent_region_scope'] == 3 ){
			object(parent::ERROR)->check($data, 'agent_region_province', parent::TABLE_AGENT_REGION, array('args'));
			object(parent::ERROR)->check($data, 'agent_region_city', parent::TABLE_AGENT_REGION, array('args'));
			object(parent::ERROR)->check($data, 'agent_region_district', parent::TABLE_AGENT_REGION, array('args'));
		}else
		if( $data['agent_region_scope'] == 2 ){
			object(parent::ERROR)->check($data, 'agent_region_province', parent::TABLE_AGENT_REGION, array('args'));
			object(parent::ERROR)->check($data, 'agent_region_city', parent::TABLE_AGENT_REGION, array('args'));
			$data['agent_region_district'] = '';
		}else
		if( $data['agent_region_scope'] == 1 ){
			object(parent::ERROR)->check($data, 'agent_region_province', parent::TABLE_AGENT_REGION, array('args'));
			$data['agent_region_city'] = '';
			$data['agent_region_district'] = '';
		}
		
		$is_exist = object(parent::TABLE_AGENT_REGION)->find_province_city_district($data['agent_region_province'], $data['agent_region_city'], $data['agent_region_district']);
		if( !empty($is_exist) ){
			throw new error("该代理地区已经存在，请勿重复添加");
		}
		
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'agent_region_scope',
			'agent_region_province', 
			'agent_region_city', 
			'agent_region_district',
			'agent_region_info',
			'agent_region_state',
			'agent_region_sort',
			'agent_region_details'
			);
		$insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		
		//获取id号
		$insert_data['agent_region_id'] = object(parent::TABLE_AGENT_REGION)->get_unique_id();
		//时间
		$insert_data['agent_region_insert_time'] = time();
		$insert_data['agent_region_update_time'] = time();
		
		if( object(parent::TABLE_AGENT_REGION)->insert($insert_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $insert_data);
			return $insert_data['agent_region_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
		
	/**
	 * 编辑代理地区
	 * 
	 * AGENTADMINREGIONEDIT
	 * {"class":"agent/admin_region","method":"api_edit"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_REGION_EDIT);
		object(parent::ERROR)->check($data, 'agent_region_id', parent::TABLE_AGENT_REGION, array('args'));
		//数据检测 
		if( isset($data['agent_region_info']) )
		object(parent::ERROR)->check($data, 'agent_region_info', parent::TABLE_AGENT_REGION, array('args'));
		if( isset($data['agent_region_details']) )
		object(parent::ERROR)->check($data, 'agent_region_details', parent::TABLE_AGENT_REGION, array('args'));
		if( isset($data['agent_region_state']) )
		object(parent::ERROR)->check($data, 'agent_region_state', parent::TABLE_AGENT_REGION, array('args'));
		if( isset($data['agent_region_sort']) )
		object(parent::ERROR)->check($data, 'agent_region_sort', parent::TABLE_AGENT_REGION, array('args'));
		
		
		//获取旧数据
		$original = object(parent::TABLE_AGENT_REGION)->find($data['agent_region_id']);
		if( empty($original) ){
			throw new error("ID有误，数据不存在");
		}
		
		//白名单 私密数据不能获取
		$whitelist = array(
			'agent_region_info',
			'agent_region_details',
			'agent_region_state', 
			'agent_region_sort',
			);
		$update_data = cmd(array($data, $whitelist), 'arr whitelist');
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
		
		//更新时间
		$update_data['agent_region_update_time'] = time();
		if( object(parent::TABLE_AGENT_REGION)->update( array(array('agent_region_id=[+]', $data['agent_region_id'])), $update_data) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			return $data['agent_region_id'];
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
	
		
	/**
	 * 获取数据列表
	 * 
	 * AGENTADMINREGIONLIST
	 * {"class":"agent/admin_region","method":"api_list"}
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_REGION_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'scope_desc' => array('agent_region_scope', true),
			'scope_asc' => array('agent_region_scope', false),
			
			'province_desc' => array('agent_region_province', true),
			'province_asc' => array('agent_region_province', false),
			'city_desc' => array('agent_region_city', true),
			'city_asc' => array('agent_region_city', false),
			'district_desc' => array('agent_region_district', true),
			'district_asc' => array('agent_region_district', false),
			
			'state_desc' => array('agent_region_state', true),
			'state_asc' =>  array('agent_region_state', false),
			
			'insert_time_desc' => array('agent_region_insert_time', true),
			'insert_time_asc' => array('agent_region_insert_time', false),
			'update_time_desc' => array('agent_region_update_time', true),
			'update_time_asc' => array('agent_region_update_time', false),
			
			'sort_desc' => array('agent_region_sort', true),
			'sort_asc' => array('agent_region_sort', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('agent_region_id', false);
		
		if(!empty($data['search'])){
			if( isset($data['search']['agent_region_id']) && is_string($data['search']['agent_region_id']) ){
				$config["where"][] = array('[and] ar.agent_region_id=[+]', $data['search']['agent_region_id']);
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
			in_array($data['search']['state'], array("0", "1")) ){
				$config["where"][] = array('[and] ar.agent_region_state=[+]', $data['search']['state']);
			}
			
		}
		
		return object(parent::TABLE_AGENT_REGION)->select_page($config);
		
	}
	
	
	
	
	
	/**
	 * 获取一条数据
	 * $data = arrray(
	 * 	agent_region_id 代理地区ID
	 * )
	 * 
	 * AGENTADMINREGIONGET
	 * {"class":"agent/admin_region","method":"api_get"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_get( $data = array() ){
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_REGION_READ);
		object(parent::ERROR)->check($data, 'agent_region_id', parent::TABLE_AGENT_REGION, array('args'));
		
		$get_data = object(parent::TABLE_AGENT_REGION)->find($data['agent_region_id']);
		if( empty($get_data) ){
			throw new error("数据不存在");
		}
		
		return $get_data;
	}
	
	
	
	
	
    /**
	 * 删除代理地区
	 * 
	 * AGENTADMINREGIONREMOVE
	 * {"class":"agent/admin_region","method":"api_remove"}
	 */
	public function api_remove( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_AGENT_REMOVE);
		//数据检测 
		object(parent::ERROR)->check($data, 'agent_region_id', parent::TABLE_AGENT_REGION, array('args'));
		
		//查询旧数据
        $original = object(parent::TABLE_AGENT_REGION)->find($data['agent_region_id']);
        if (empty($original)) throw new error('数据不存在');
		
		//该地区下存在代理人员则无法删除
		if( object(parent::TABLE_AGENT_USER)->find_exists_region($data['agent_region_id']) ){
			throw new error("该地区下存在代理人员，请先清理代理人员才能删除该地区");
			}
		
		if( object(parent::TABLE_AGENT_REGION)->remove($data['agent_region_id']) ){
			//插入操作日志
			object(parent::TABLE_ADMIN_LOG)->insert($data, $original);
			return $data['agent_region_id'];
		}else{
			throw new error("操作失败");
		}
		
    }
    
	
	
	
	
	
	
}
?>