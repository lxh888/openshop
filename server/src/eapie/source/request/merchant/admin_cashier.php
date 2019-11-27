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
class admin_cashier extends \eapie\source\request\merchant {
	
	
	/**
     * 员工列表数据
     * 
     * MERCHANTADMINCASHIERLIST
     * {"class":"merchant/admin_cashier","method":"api_list"}
	 * 
	 * [{"search":{merchant_cashier_id:"员工ID",merchant_cashier_name:"员工名称",user_id:"用户",user_nickname:"用户昵称",user_phone:"用户手机号",user_parent_id:"用户父级"},"sort":["员工ID cashier_id_desc|cashier_id_asc","商家ID merchant_id_desc|merchant_id_desc", "用户ID user_id_desc|user_id_asc","操作人ID cashier_action_user_desc|cashier_action_user_asc","员工名称 cashier_name_desc|cashier_name_asc","员工状态 cashier_state_desc|cashier_state_asc","排序 cashier_sort_desc|cashier_sort_asc","更新时间  update_time_desc|update_time_asc","插入时间  insert_time_desc|insert_time_asc"]}]
	 * 
	 * @param	array	$data
     */
    public function api_list($data = array()){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_CASHIER_LIST);

        //查询配置
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
        );
		
		
        //排序
        $config["orderby"] = object(parent::REQUEST)->orderby($data, array(
            'cashier_id_desc' => array('merchant_cashier_id', true),
            'cashier_id_asc' => array('merchant_cashier_id', false),
            'merchant_id_desc' => array('merchant_id', true),
            'merchant_id_asc' => array('merchant_id', false),
            'user_id_desc' => array('user_id', true),
            'user_id_asc' => array('user_id', false),
            'cashier_action_user_desc' => array('merchant_cashier_action_user', true),
            'cashier_action_user_asc' => array('merchant_cashier_action_user', false),
            'cashier_name_desc' => array('merchant_cashier_name', true),
            'cashier_name_asc' => array('merchant_cashier_name', false),
            'cashier_state_desc' => array('merchant_cashier_state', true),
            'cashier_state_asc' => array('merchant_cashier_state', false),
            
            'cashier_sort_desc' => array('merchant_cashier_sort', true),
            'cashier_sort_asc' => array('merchant_cashier_sort', false),
            'update_time_desc' => array('merchant_cashier_update_time', true),
            'update_time_asc' => array('merchant_cashier_update_time', false),
            'insert_time_desc' => array('merchant_cashier_insert_time', true),
            'insert_time_asc' => array('merchant_cashier_insert_time', false),
        ));

        //避免排序重复
        $config["orderby"][] = array('merchant_cashier_id', false);
		
        //搜索
        if (!empty($data['search'])) {
            if (isset($data['search']['merchant_cashier_id']) && is_string($data['search']['merchant_cashier_id'])) {
                $config['where'][] = array('[and] mc.merchant_cashier_id=[+]', $data['search']['merchant_cashier_id']);
            }
			
            if (isset($data['search']['merchant_cashier_name']) && is_string($data['search']['merchant_cashier_name'])) {
                $config['where'][] = array('[and] mc.merchant_cashier_name LIKE "%[-]%"', $data['search']['merchant_cashier_name']);
            }
			
			if (isset($data['search']['user_id']) && is_string($data['search']['user_id'])) {
                $config['where'][] = array('[and] mc.user_id=[+]', $data['search']['user_id']);
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
                $config['where'][] = array('[and] mc.user_id=[+]', $user_id);
            }
			
			if (isset($data['search']['user_parent_id']) && is_string($data['search']['user_parent_id'])) {
                $config['where'][] = array('[and] u.user_parent_id=[+]', $data['search']['user_parent_id']);
            }
			
			if (isset($data['search']['merchant_id']) && is_string($data['search']['merchant_id'])) {
                $config['where'][] = array('[and] mc.merchant_id=[+]', $data['search']['merchant_id']);
            }
			
			if (isset($data['search']['merchant_name']) && is_string($data['search']['merchant_name'])) {
                $config['where'][] = array('[and] m.merchant_name LIKE "%[-]%"', $data['search']['merchant_name']);
            }
			
            if( isset($data['search']['state']) && 
            (is_string($data['search']['state']) || is_numeric($data['search']['state'])) &&
            in_array($data['search']['state'], array("0", "1", "2")) ){
                $config["where"][] = array('[and] mc.merchant_cashier_state=[+]', $data['search']['state']);
            }
			
			
        }

        //查询数据
        $data = object(parent::TABLE_MERCHANT_CASHIER)->select_page($config);
		/*if( empty($data) ){
			return $data;
		}
		
		foreach($data as $key = $value){
			if( isset($value['merchant_cashier_json']) && $value['merchant_cashier_json'] != ''){
				$value['merchant_cashier_json'] = cmd(array(), 'json encode');
			}
		}*/
        return $data;
    }
	
   	
	
	
	/**
     * 审核员工数据
     * 
     * [{"merchant_cashier_id":"收银员ID","merchant_cashier_state":"要修改的状态值"}]
	 * 
     * MERCHANTADMINCASHIERSTATE
     * {"class":"merchant/admin_cashier","method":"api_state"}
     */
    public function api_state($data){
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_CASHIER_STATE);
		
		//检测数据
        object(parent::ERROR)->check($data, 'merchant_cashier_id', parent::TABLE_MERCHANT_CASHIER, array('args'));
		object(parent::ERROR)->check($data, 'merchant_cashier_state', parent::TABLE_MERCHANT_CASHIER, array('args'));
		
        $merchant_cashier = object(parent::TABLE_MERCHANT_CASHIER)->find($data['merchant_cashier_id']);
		if( empty($merchant_cashier) ){
			throw new error("未找到员工数据");
		}
        if( $merchant_cashier['merchant_cashier_state'] != 2 ){
        	throw new error("该员工非等待审核状态");
        }
        
		
        $update_data = array(
        	'merchant_cashier_action_user' => $_SESSION['user_id'],//操作人
            'merchant_cashier_state' => $data['merchant_cashier_state'],
            'merchant_cashier_update_time' => time()
        );
		
        $update_where = array(
            array('merchant_cashier_id=[+]',$data['merchant_cashier_id'])
        );
		
        return object(parent::TABLE_MERCHANT_CASHIER)->update( $update_where, $update_data );
    }
	
	
	
	/**
     * 删除商家员工
     * 
     * MERCHANTADMINCASHIERREMOVE
	 * {"class":"merchant/admin_cashier","method":"api_remove"}
	 * [{"merchant_cashier_id":"商家员工表ID"}]
     * 
     * @param  [arr]  $input [请求参数]
     * @return [str] [商家用户表ID]
     */
    public function api_remove( $input = array() ){
        //检测权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_CASHIER_REMOVE);
		
        //检测数据
        object(parent::ERROR)->check($input, 'merchant_cashier_id', parent::TABLE_MERCHANT_CASHIER, array('args'));
		
        //判断改数据是否存在
        $original = object(parent::TABLE_MERCHANT_CASHIER)->find($input['merchant_cashier_id']);
        if (empty($original))
        throw new error('商家员工表ID有误，该数据不存在');
		
		
        //删除数据，记录日志
        if( object(parent::TABLE_MERCHANT_CASHIER)->remove($input['merchant_cashier_id']) ){
            object(parent::TABLE_ADMIN_LOG)->insert($input, $original);
            return $input['merchant_cashier_id'];
        }else{
            throw new error('删除失败');
        }
		
		
    }
	
	
	
}
?>