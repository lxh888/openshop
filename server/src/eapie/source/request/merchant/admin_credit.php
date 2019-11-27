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



namespace eapie\source\request\merchant;
use eapie\main;
use eapie\error;
class admin_credit extends \eapie\source\request\merchant {
	
	/*商家积分管理接口*/
	
	
	
	
		
	
	/**
	 * 根据检索条件获取商家积分总数
	 * 
	 * MERCHANTADMINCREDITTOTAL
	 * {"class":"merchant/admin_credit","method":"api_total"}
	 * 
	 * @param	array		$search
	 * @return	int
	 */
	public function api_total($search = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_CREDIT_READ);
		
		$where = array();
		if(!empty($search)){
			if( isset($search['merchant_id']) && is_string($search['merchant_id']) ){
				$config["where"][] = array('[and] mc.merchant_id=[+]', $search['merchant_id']);
			}
			if (isset($search['merchant_name']) && is_string($search['merchant_name'])) {
				$merchant_data = object(parent::TABLE_MERCHANT)->find_like_name($search['merchant_name']);
				if( empty($merchant_data['merchant_id']) ){
        			$merchant_id = "";
        		}else{
        			$merchant_id = $merchant_data['merchant_id'];
        		}
				$where[] = array('[and] mc.merchant_id=[+]', $merchant_id);
            }
			
			if (isset($search['min_value']) && 
			is_numeric($search['min_value']) && 
			(int)$search['min_value'] >= 0 ) {
				$where[] = array('mc.merchant_credit_value > [-]', ( (int)$search['min_value'] - 1) );
            }
			
			if (isset($search['max_value']) && 
			is_numeric($search['max_value']) && 
			(int)$search['max_value'] >= 0 ) {
				$where[] = array('mc.merchant_credit_value < [-]', ( (int)$search['max_value'] + 1) );
            }
			
		}
		
		return object(parent::TABLE_MERCHANT_CREDIT)->find_now_where_sum($where);
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
	 * MERCHANTADMINCREDITLIST
	 * {"class":"merchant/admin_credit","method":"api_list"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_CREDIT_READ);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'merchant_id_desc' => array('merchant_id', true),
			'merchant_id_asc' => array('merchant_id', false),
			
			'merchant_name_desc' => array('merchant_name', true),
			'merchant_name_asc' => array('merchant_name', false),
			
			'value_desc' => array('merchant_credit_value', true),
			'value_asc' =>  array('merchant_credit_value', false),
			
			'time_desc' => array('merchant_credit_time', true),
			'time_asc' => array('merchant_credit_time', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('merchant_id', false);
		
		if(!empty($data['search'])){
			if( isset($data['search']['merchant_id']) && is_string($data['search']['merchant_id']) ){
				$config["where"][] = array('[and] m.merchant_id=[+]', $data['search']['merchant_id']);
			}
			if (isset($data['search']['merchant_name']) && is_string($data['search']['merchant_name'])) {
                $config['where'][] = array('[and] m.merchant_name LIKE "%[-]%"', $data['search']['merchant_name']);
            }
			
			if (isset($data['search']['min_value']) && 
			is_numeric($data['search']['min_value']) && 
			(int)$data['search']['min_value'] >= 0 ) {
				$sql_join_now_value = object(parent::TABLE_MERCHANT_CREDIT)->sql_join_merchant_now_value("m");
                $config['where'][] = array('[and] ('.$sql_join_now_value.') > []', ( (int)$data['search']['min_value'] - 1) );
            }
			
			if (isset($data['search']['max_value']) && 
			is_numeric($data['search']['max_value']) && 
			(int)$data['search']['max_value'] >= 0 ) {
				$sql_join_now_value = object(parent::TABLE_MERCHANT_CREDIT)->sql_join_merchant_now_value("m");
                $config['where'][] = array('[and] ('.$sql_join_now_value.') < []', ( (int)$data['search']['max_value'] + 1) );
            }
			
		}
		
		
		return object(parent::TABLE_MERCHANT_CREDIT)->select_merchant_page($config);
		
	}
	
	
	
	
		
	/**
	 * 获取流水号列表
	 * 
	 * MERCHANTADMINCREDITSERIALLIST
	 * {"class":"merchant/admin_credit","method":"api_serial_list"}
	 * 
	 * @param	array	$data
	 * @return	array
	 */
	public function api_serial_list($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_CREDIT_EDIT);
		
		$config = array(
			'orderby' => array(),
			'where' => array(),
			'limit' => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
		);
		
		$config["orderby"] = object(parent::REQUEST)->orderby($data, array(
			'merchant_id_desc' => array('merchant_id', true),
			'merchant_id_asc' => array('merchant_id', false),
			
			'merchant_name_desc' => array('merchant_name', true),
			'merchant_name_asc' => array('merchant_name', false),
			
			'type_desc' => array('merchant_credit_type', true),
			'type_asc' => array('merchant_credit_type', false),
			
			'time_desc' => array('merchant_credit_time', true),
			'time_asc' =>  array('merchant_credit_time', false),
			
			'order_action_user_nickname_desc' => array('order_action_user_nickname', true),
			'order_action_user_nickname_asc' => array('order_action_user_nickname', false),
			
			'order_action_user_phone_verify_list_desc' => array('order_action_user_phone_verify_list', true),
			'order_action_user_phone_verify_list_asc' => array('order_action_user_phone_verify_list', false),
		));
		
		//避免排序重复
		$config["orderby"][] = array('merchant_credit_id', false);
		$type_list = object(parent::TABLE_ORDER)->get_type();
		
		if(!empty($data['search'])){
			if( isset($data['search']['merchant_id']) && is_string($data['search']['merchant_id']) ){
				$config["where"][] = array('[and] m.merchant_id=[+]', $data['search']['merchant_id']);
			}
			if (isset($data['search']['merchant_name']) && is_string($data['search']['merchant_name'])) {
                $config['where'][] = array('[and] m.merchant_name LIKE "%[-]%"', $data['search']['merchant_name']);
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
			
			
			if( isset($data['search']['merchant_credit_id']) && is_string($data['search']['merchant_credit_id']) ){
				$config["where"][] = array('[and] mc.merchant_credit_id=[+]', $data['search']['merchant_credit_id']);
			}
			if( isset($data['search']['type']) && is_string($data['search']['type']) ){
				$config["where"][] = array('[and] mc.merchant_credit_type=[+]', $data['search']['type']);
			}
			if( isset($data['search']['type_name']) && is_string($data['search']['type_name']) && !empty($type_list) ){
				foreach($type_list as $type_k => $type_v){
					if(mb_strstr($type_v, $data['search']['type_name']) !== false){
						$config["where"][] = array('[and] mc.merchant_credit_type=[+]', $type_k);
						break;
					}
				}
			}
		
		}
		$data_list = object(parent::TABLE_MERCHANT_CREDIT)->select_serial_page($config);
		
		if( !empty($data_list["data"]) && !empty($type_list) ){
			foreach($data_list["data"] as $key => $value){
				if( isset($type_list[$value["merchant_credit_type"]]) ){
					$data_list["data"][$key]["merchant_credit_type_name"] = $type_list[$value["merchant_credit_type"]];
				}
			}
		}
		
		return $data_list;
	}
	
	
	
	
	
	
	
	
	/**
	 * 添加/减少积分
	 * MERCHANTADMINCREDITEDIT
	 * {"class":"merchant/admin_credit","method":"api_edit"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_edit($data = array()){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_CREDIT_EDIT);
		object(parent::ERROR)->check( $data, 'merchant_id', parent::TABLE_MERCHANT, array('args') );
		object(parent::ERROR)->check( $data, 'value', parent::TABLE_ORDER, array('args') ,'credit_number');
		object(parent::ERROR)->check( $data, 'type', parent::TABLE_ORDER, array('args'));
		object(parent::ERROR)->check( $data, 'comment', parent::TABLE_ORDER, array('args') );
		
		//获取商家数据
		$merchant_data = object(parent::TABLE_MERCHANT)->find_base_data($data['merchant_id']);
		if( empty($merchant_data) ){
			throw new error('商家不合法');
		}
		
		//备注信息
		$data["comment"] = !empty($data["comment"])? "管理员操作 《".$merchant_data["merchant_name"]."》 - ".$data["comment"] : "管理员操作 《".$merchant_data["merchant_name"]."》 ";
		
		if($data['type'] == parent::TRANSACTION_TYPE_ADMIN_MINUS){
			//如果是人工减少
			//获取商户的库存积分  判断要赠送的积分
			$merchant_credit_data = object(parent::TABLE_MERCHANT_CREDIT)->find_now_data($data['merchant_id']);
			if( empty($merchant_credit_data["merchant_credit_value"]) 
			|| ($merchant_credit_data["merchant_credit_value"] - $data['value']) < 0 ){
				throw new error('该商户积分不足');
			}
		}else
		if($data['type'] == parent::TRANSACTION_TYPE_ADMIN_PLUS){
			$merchant_credit_data = array();
		}else{
			throw new error('交易类型异常');
		}
		
		$bool = object(parent::TABLE_MERCHANT_CREDIT)->insert_admin(array(
			"admin_user_id" => $_SESSION["user_id"],
			"merchant_id" => $data['merchant_id'],
			"comment" => $data['comment'],
			"value" => $data['value'],
			"type" => $data['type'],
			"merchant_credit" => $merchant_credit_data,
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