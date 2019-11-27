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
use eapie\main;
use eapie\error;

class admin_agent_user extends \eapie\source\request\agent{



    /**
	 * 管理员更新区域负责人（代理）库存
	 * Undocumented function
	 * api: ADMINAGENTUSERREPLACESCOKET
	 * {"class":"agent/admin_agent_user","method":"api_replace_scoket"}
	 * @param array $data
	 * @return void
	 */
	public function api_replace_scoket($data = array()){

		//检测权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_ADMIN_USER_ADD_SOCKET);

		if(empty($data['agent_user_id']) || empty($data['scoket']) || !is_numeric($data['scoket'])){
			throw new error('参数错误');
		}

        $agent_user = object(parent::TABLE_AGENT_USER)->find($data['agent_user_id']);
        if(empty($agent_user)){
            throw new error('代理信息不存在');
        }

		$where = array(
			array('agent_user_id=[+]',$data['agent_user_id']),
		);
		$update_data = array(
			'agent_user_scoket'=>(int)$data['scoket'],
		);

		if(object(parent::TABLE_AGENT_USER)->update($where,$update_data)){

            $config = array(
                'user_id'=>$agent_user['user_id'],
                'key'=>$data['agent_user_id'],
                'type'=>3,
				'num'=>$data['scoket'],
				'remain_num'=>$data['scoket']
            );
            object(parent::TABLE_SHOP_GOODS_STOCK_LOG)->insert_info($config);

			return $data['agent_user_id'];
		}
		throw new error('更新库存失败');
	}


	/**
	 * 区域负责人审核
	 * Undocumented function
	 * 
	 * api:	AGENTADMINAGENTUSERAUDIT
	 * {"class":"agent/admin_agent_user","method":"api_audit"}
	 *
	 * @param [type] $data
	 * @return void
	 */
	public function api_audit($data=array()){

		//检验是否有审核权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_AGENT_STATE);

		object(parent::ERROR)->check($data, 'agent_user_id', parent::TABLE_AGENT_USER, array('args'), 'agent_user_id');
		object(parent::ERROR)->check($data, 'agent_user_fail', parent::TABLE_AGENT_USER, array('args'), 'agent_user_fail');
		object(parent::ERROR)->check($data, 'agent_user_state', parent::TABLE_AGENT_USER, array('args'), 'agent_user_state');

		$agent_user = object(parent::TABLE_AGENT_USER)->find($data['agent_user_id']);

		if(empty($agent_user)) throw new error('代理信息参数错误');

		// return $data;
		if($data['agent_user_state'] != 1){
			$agent_user_update_data = array(
				'agent_user_state'=>0,
				'agent_user_fail'=>isset($data['agent_user_fail'])?$data['agent_user_fail']:''
			);
		}else{
			if(!empty($data['agent_region_id'])){

				$agent_region = object(parent::TABLE_AGENT_REGION)->find($data['agent_region_id']);
				if(empty($agent_region)) throw new error('代理地区参数错误'); 
	
				$agent_user_where = array(
					array('agent_region_id =[+]',$data['agent_region_id']),
					array('agent_user_state=1')
				);
				if(object(parent::TABLE_AGENT_USER)->find_where($agent_user_where)){
					throw new error('当前区域已有负责人');
				}
				$agent_user_update_data = array(
					'agent_user_state'=>1,
					'agent_region_id'=>$data['agent_region_id']
				);
			}else{
				$agent_user_update_data = array(
					'agent_user_state'=>1
				);
			}
		}
		

		$update_where = array(
			array('agent_user_id =[+]',$agent_user['agent_user_id'])
		);
		if(object(parent::TABLE_AGENT_USER)->update($update_where,$agent_user_update_data)){
			if($agent_user_update_data['agent_user_state'] == 1){
				$admin_user_where = array(
					array('user_id=[+]',$agent_user['user_id'])
				);
				$admin_user_update = array(
					'admin_id'=>'founder'
				);
				object(parent::TABLE_ADMIN_USER)->update($admin_user_where,$admin_user_update);
			}
			return array('id'=>$agent_user['agent_user_id']);
		}
		throw new error('操作失败');
	}
}
?>