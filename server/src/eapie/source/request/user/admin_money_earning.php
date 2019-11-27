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
class admin_money_earning extends \eapie\source\request\user {
	
	
	/*赠送收益操作*/
	
		
	/**
	 * 根据检索条件获取用户赠送收益资金总数
	 * 
	 * USERADMINMONEYEARNINGTOTAL
	 * {"class":"user/admin_money_earning","method":"api_total"}
	 * 
	 * @param	array		$search
	 * @return	int
	 */
	public function api_total($search = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MONEY_EARNING_READ);
		
		$where = array();
		if(!empty($search)){
			if( isset($search['user_id']) && is_string($search['user_id']) ){
				$where[] = array('[and] ume.user_id=[+]', $search['user_id']);
			}
			
			if (isset($search['user_nickname']) && is_string($search['user_nickname'])) {
				$user_data = object(parent::TABLE_USER)->find_like_nickname($search['user_nickname']);
				if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
				$where[] = array('[and] ume.user_id=[+]', $user_id);
            }
			
			if (isset($search['user_phone']) && is_string($search['user_phone'])) {
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($search['user_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $config['where'][] = array('[and] ume.user_id=[+]', $user_id);
            }
			
			if (isset($search['min_value']) && 
			is_numeric($search['min_value']) && 
			(int)$search['min_value'] >= 0 ) {
				$where[] = array('ume.user_money_earning_value > [-]', ( (int)$search['min_value'] - 1) );
            }
			
			if (isset($search['max_value']) && 
			is_numeric($search['max_value']) && 
			(int)$search['max_value'] >= 0 ) {
				$where[] = array('ume.user_money_earning_value < [-]', ( (int)$search['max_value'] + 1) );
            }
			
		}
		
		return object(parent::TABLE_USER_MONEY_EARNING)->find_now_where_sum($where);
	}
	
	
	
		
	/**
	 * 获取数据列表
	 * 需要判断浏览权限
	 * 
	 * $request = array(
	 * 	'search' => array(),//搜索、筛选
	 * 	'sort' => array(),//排序
	 *  'size' => 0,//每页的条数
	 * 	'page' => 0, //当前页数，如果是等于 all 那么则查询所有
	 *  'start' => 0, //开始的位置，如果存在，则page无效
	 * );
	 * limit的分页算法是：当前页数-1 * page_size
	 * 序号的算法：key键+1，+每页显示的条数。等于分页后的序号。{key + 1 + page_size}
	 * 
	 * 返回的数据：
	 * $data = array(
	 * 	'row_count' => //数据总条数
	 * 	'limit_count' => //已取出条数
	 * 	'page_size' => //每页的条数
	 *  'page_count' => //总页数
	 * 	'data' => //数据
	 * );
	 * 
	 * USERADMINMONEYEARNINGLIST
	 * {"class":"user/admin_money_earning","method":"api_list"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MONEY_EARNING_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'user_id_desc' => array('user_id', true),
			'user_id_asc' => array('user_id', false),
			
			'user_nickname_desc' => array('user_nickname', true),
			'user_nickname_asc' => array('user_nickname', false),
			
			'value_desc' => array('user_money_earning_value', true),
			'value_asc' =>  array('user_money_earning_value', false),
			
			'time_desc' => array('user_money_earning_time', true),
			'time_asc' => array('user_money_earning_time', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('user_id', false);
		
		if(!empty($data['search'])){
			if( isset($data['search']['user_id']) && is_string($data['search']['user_id']) ){
				$config["where"][] = array('[and] u.user_id=[+]', $data['search']['user_id']);
			}
			
			if (isset($data['search']['user_nickname']) && is_string($data['search']['user_nickname'])) {
                $config['where'][] = array('[and] u.user_nickname LIKE "%[-]%"', $data['search']['user_nickname']);
            }
			if (isset($data['search']['user_phone']) && is_string($data['search']['user_phone'])) {
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['user_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $config['where'][] = array('[and] u.user_id=[+]', $user_id);
            }
			
			
			if (isset($data['search']['min_value']) && 
			is_numeric($data['search']['min_value']) && 
			(int)$data['search']['min_value'] >= 0 ) {
				$sql_join_now_value = object(parent::TABLE_USER_MONEY_EARNING)->sql_join_user_now_value("u");
                $config['where'][] = array('[and] ('.$sql_join_now_value.') > []', ( (int)$data['search']['min_value'] - 1) );
            }
			
			if (isset($data['search']['max_value']) && 
			is_numeric($data['search']['max_value']) && 
			(int)$data['search']['max_value'] >= 0 ) {
				$sql_join_now_value = object(parent::TABLE_USER_MONEY_EARNING)->sql_join_user_now_value("u");
                $config['where'][] = array('[and] ('.$sql_join_now_value.') < []', ( (int)$data['search']['max_value'] + 1) );
            }
			
		}
		
		
		return object(parent::TABLE_USER_MONEY_EARNING)->select_user_page($config);
		
	}
	
	
	
	
		
	/**
	 * 获取流水号列表
	 * 
	 * USERADMINMONEYEARNINGSERIALLIST
	 * {"class":"user/admin_money_earning","method":"api_serial_list"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_serial_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MONEY_EARNING_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'user_id_desc' => array('user_id', true),
			'user_id_asc' => array('user_id', false),
			
			'user_nickname_desc' => array('user_nickname', true),
			'user_nickname_asc' => array('user_nickname', false),
			
			'type_desc' => array('user_money_earning_type', true),
			'type_asc' => array('user_money_earning_type', false),
			
			'time_desc' => array('user_money_earning_time', true),
			'time_asc' =>  array('user_money_earning_time', false),
			
			'order_action_user_nickname_desc' => array('order_action_user_nickname', true),
			'order_action_user_nickname_asc' => array('order_action_user_nickname', false),
			
			'order_action_user_phone_verify_list_desc' => array('order_action_user_phone_verify_list', true),
			'order_action_user_phone_verify_list_asc' => array('order_action_user_phone_verify_list', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('user_money_earning_id', false);
		$type_list = object(parent::TABLE_ORDER)->get_type();
		
		if(!empty($data['search'])){
			if( isset($data['search']['user_id']) && is_string($data['search']['user_id']) ){
				$config["where"][] = array('[and] u.user_id=[+]', $data['search']['user_id']);
			}
			
			if (isset($data['search']['user_nickname']) && is_string($data['search']['user_nickname'])) {
                $config['where'][] = array('[and] u.user_nickname LIKE "%[-]%"', $data['search']['user_nickname']);
            }
			if (isset($data['search']['user_phone']) && is_string($data['search']['user_phone'])) {
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['user_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $config['where'][] = array('[and] u.user_id=[+]', $user_id);
            }
			
			
			
			if (isset($data['search']['order_action_user_id']) && is_string($data['search']['order_action_user_id'])) {
				//数据过滤
				$data['search']['order_action_user_id'] = cmd(array($data['search']['order_action_user_id']), 'str addslashes');
                $config['where'][] = array('[and] (plus_oau.user_id="'.$data['search']['order_action_user_id'].'" OR minus_oau.user_id="'.$data['search']['order_action_user_id'].'")', NULL, true);
            }
			if (isset($data['search']['order_action_user_nickname']) && is_string($data['search']['order_action_user_nickname'])) {
				$data['search']['order_action_user_nickname'] = cmd(array($data['search']['order_action_user_nickname']), 'str addslashes');
				$config['where'][] = array('[and] (plus_oau.user_nickname LIKE "%'.$data['search']['order_action_user_nickname'].'%" OR minus_oau.user_nickname LIKE "%'.$data['search']['order_action_user_nickname'].'%")', NULL, true);
            }
			if (isset($data['search']['order_action_user_phone']) && is_string($data['search']['order_action_user_phone'])) {
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['order_action_user_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
        		$config['where'][] = array('[and] (plus_oau.user_id="'.$user_id.'" OR minus_oau.user_id="'.$user_id.'")', NULL, true);
            }
			
			
			
			if( isset($data['search']['user_money_earning_id']) && is_string($data['search']['user_money_earning_id']) ){
				$config["where"][] = array('[and] ume.user_money_earning_id=[+]', $data['search']['user_money_earning_id']);
			}
			if( isset($data['search']['type']) && is_string($data['search']['type']) ){
				$config["where"][] = array('[and] ume.user_money_earning_type=[+]', $data['search']['type']);
			}
			if( isset($data['search']['type_name']) && is_string($data['search']['type_name']) && !empty($type_list) ){
				foreach($type_list as $type_k => $type_v){
					if(mb_strstr($type_v, $data['search']['type_name']) !== false){
						$config["where"][] = array('[and] ume.user_money_earning_type=[+]', $type_k);
						break;
					}
				}
			}
		
		}
		$data_list = object(parent::TABLE_USER_MONEY_EARNING)->select_serial_page($config);
		
		if( !empty($data_list["data"]) && !empty($type_list) ){
			foreach($data_list["data"] as $key => $value){
				if( isset($type_list[$value["user_money_earning_type"]]) ){
					$data_list["data"][$key]["user_money_earning_type_name"] = $type_list[$value["user_money_earning_type"]];
				}
			}
		}
		
		return $data_list;
	}
	
	
	
	
	
	
	
	
	/**
	 * 添加/减少用户赠送收益
	 * USERADMINMONEYEARNINGEDIT
	 * {"class":"user/admin_money_earning","method":"api_edit"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_MONEY_EARNING_EDIT);
		object(parent::ERROR)->check( $data, 'user_id', parent::TABLE_USER, array('args', 'exists_id'));
		object(parent::ERROR)->check( $data, 'value', parent::TABLE_ORDER, array('args') ,'money_fen');
		object(parent::ERROR)->check( $data, 'type', parent::TABLE_ORDER, array('args'));
		object(parent::ERROR)->check( $data, 'comment', parent::TABLE_ORDER, array('args') );
		
		//备注信息
		$data["comment"] = !empty($data["comment"])? "管理员操作 - ".$data["comment"] : "管理员操作";
		
		if($data['type'] == parent::TRANSACTION_TYPE_ADMIN_MINUS){
			//如果是人工减少
			//获取商户的库存积分  判断要赠送的积分
			$user_money_earning_data = object(parent::TABLE_USER_MONEY_EARNING)->find_now_data($data['user_id']);
			
			if( empty($user_money_earning_data["user_money_earning_value"]) 
			|| ($user_money_earning_data["user_money_earning_value"] - $data['value']) < 0 ){
				throw new error('该用户赠送收益的余额不足');
			}
			
			
		}else
		if($data['type'] == parent::TRANSACTION_TYPE_ADMIN_PLUS){
			$user_money_earning_data = array();
		}else{
			throw new error('交易类型异常');
		}
		
		$bool = object(parent::TABLE_USER_MONEY_EARNING)->insert_admin(array(
			"admin_user_id" => $_SESSION["user_id"],
			"user_id" => $data['user_id'],
			"comment" => $data['comment'],
			"value" => $data['value'],
			"type" => $data['type'],
			"user_money_earning" => $user_money_earning_data,
		));
			
		if( empty($bool) ){
			throw new error('操作失败');
		}else{
			object(parent::TABLE_ADMIN_LOG)->insert($data, $data);
			return $bool;
		}
		
	}
	
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>