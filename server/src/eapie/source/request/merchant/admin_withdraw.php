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
class admin_withdraw extends \eapie\source\request\merchant {
	
	
	
	/**
     * 商家提现列表
     *
     * api: MERCHANTADMINLIST
     * req: {
     *  search   [arr] [可选] [搜索、筛选]
     *  sort     [arr] [可选] [排序]
     *  size     [int] [可选] [每页的条数]
     *  page     [int] [可选] [当前页数，如果是等于 all 那么则查询所有]
     *  start    [int] [可选] [开始的位置，如果存在，则page无效]
     * }
     *  
     * limit的分页算法是：当前页数-1 * page_size
     * 序号的算法：key键+1，+每页显示的条数。等于分页后的序号。{key + 1 + page_size}
     *
     * @param  [arr] $input [请求参数]
     * @return array(
     *  row_count   [int] [数据总条数]
     *  limit_count [int] [已取出条数]
     *  page_size   [int] [每页的条数]
     *  page_count  [int] [总页数]
     *  page_now    [int] [当前页数]
     *  data        [arr] [数据]
     * )
     * 
	 * MERCHANTADMINWITHDRAWLIST
	 * {"class":"merchant/admin_withdraw","method":"api_list"}
	 * 
     * @param  array	 $data 		请求参数
     * @return array
     */
    public function api_list( $data = array() ) {
        //检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_WITHDRAW_READ);

        //查询配置
        $config = array(
            'orderby' => array(),
            'where'   => array(),
            'limit'   => object(parent::REQUEST)->limit($data, parent::REQUEST_ADMIN),
        );
		
        //排序
        $config["orderby"] = object(parent::REQUEST)->orderby($data, array(
            'id_desc' => array('merchant_withdraw_id', true),
            'id_asc' => array('merchant_withdraw_id', false),
            'user_id_desc' => array('user_id', true),
            'user_id_asc' => array('user_id', false),
            'user_parent_id_desc' => array('user_parent_id', true),
            'user_parent_id_asc' => array('user_parent_id', false),
            'user_nickname_desc' => array('user_nickname', true),
            'user_nickname_asc' => array('user_nickname', false),
            'user_phone_verify_list_desc' => array('user_phone_verify_list', true),
            'user_phone_verify_list_asc' => array('user_phone_verify_list', false),
			
			
			'merchant_id_desc' => array('merchant_id', true),
            'merchant_id_asc' => array('merchant_id', false),
            'merchant_name_desc' => array('merchant_name', true),
            'merchant_name_asc' => array('merchant_name', false),
            
            'state_desc' => array('merchant_withdraw_state', true),
            'state_asc' => array('merchant_withdraw_state', false),
            'type_desc' => array('merchant_withdraw_type', true),
            'type_asc' => array('merchant_withdraw_type', false),
			'method_desc' => array('merchant_withdraw_method', true),
            'method_asc' => array('merchant_withdraw_method', false),
            
			'value_desc' => array('merchant_withdraw_value', true),
            'value_asc' => array('merchant_withdraw_value', false),
			
			'pass_time_desc' => array('merchant_withdraw_pass_time', true),
            'pass_time_asc' => array('merchant_withdraw_pass_time', false),
			'fail_time_desc' => array('merchant_withdraw_pass_time', true),
            'fail_time_asc' => array('merchant_withdraw_pass_time', false),
            
            'insert_time_desc' => array('merchant_withdraw_insert_time', true),
            'insert_time_asc' => array('merchant_withdraw_insert_time', false),
        ));

        //避免排序重复
        $config["orderby"][] = array('merchant_withdraw_id', false);
		$type_list = object(parent::TABLE_MERCHANT_WITHDRAW)->get_type();
		$method_list = object(parent::TABLE_MERCHANT_WITHDRAW)->get_method();
		
        //搜索
        if (!empty($data['search'])) {
        	if (isset($data['search']['merchant_id']) && is_string($data['search']['merchant_id'])) {
                $config['where'][] = array('[and] m.merchant_id=[+]', $data['search']['merchant_id']);
            }
        	if (isset($data['search']['merchant_name']) && is_string($data['search']['merchant_name'])) {
                $config['where'][] = array('[and] m.merchant_name LIKE "%[-]%"', $data['search']['merchant_name']);
            }
			
			if (isset($data['search']['user_nickname']) && is_string($data['search']['user_nickname'])) {
                $config['where'][] = array('[and] u.user_nickname LIKE "%[-]%"', $data['search']['user_nickname']);
            }
			if (isset($data['search']['user_id']) && is_string($data['search']['user_id'])) {
                $config['where'][] = array('[and] mw.user_id=[+]', $data['search']['user_id']);
            }
			if (isset($data['search']['user_phone']) && is_string($data['search']['user_phone'])) {
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['user_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $config['where'][] = array('[and] mw.user_id=[+]', $user_id);
            }
			
			if( isset($data['search']['merchant_withdraw_state']) && 
			(is_string($data['search']['merchant_withdraw_state']) || is_numeric($data['search']['merchant_withdraw_state'])) &&
			in_array($data['search']['merchant_withdraw_state'], array("0", "1", "2")) ){
				$config["where"][] = array('[and] mw.merchant_withdraw_state=[+]', $data['search']['merchant_withdraw_state']);
				}
        }
		
        //查询数据
        $data_list = object(parent::TABLE_MERCHANT_WITHDRAW)->select_page($config);
		if( !empty($data_list["data"]) && !empty($type_list) ){
			foreach($data_list["data"] as $key => $value){
				if( isset($type_list[$value["merchant_withdraw_type"]]) ){
					$data_list["data"][$key]["merchant_withdraw_type_name"] = $type_list[$value["merchant_withdraw_type"]];
				}
				if( isset($method_list[$value["merchant_withdraw_method"]]) ){
					$data_list["data"][$key]["merchant_withdraw_method_name"] = $method_list[$value["merchant_withdraw_method"]];
				}
			}
		}
		
        return $data_list;
    }
	
	
	
	
	
	/**
	 * 商家提现审核不通过
	 * 
	 * MERCHANTADMINWITHDRAWFAIL
	 * {"class":"merchant/admin_withdraw","method":"api_fail"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_fail( $data = array() ){
		//检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_WITHDRAW_STATE);
		//校验数据
        object(parent::ERROR)->check($data, 'merchant_withdraw_id', parent::TABLE_MERCHANT_WITHDRAW, array('args'));
		object(parent::ERROR)->check($data, 'merchant_withdraw_fail_info', parent::TABLE_MERCHANT_WITHDRAW, array('args'));
		
		//事务处理
		$merchant_withdraw_lock_id = object(parent::TABLE_LOCK)->start("merchant_withdraw_id", $data["merchant_withdraw_id"], parent::LOCK_STATE);
        if( empty($merchant_withdraw_lock_id) ){
            return false;//事务开启失败
        }
		
		//查询原始数据
        $original = object(parent::TABLE_MERCHANT_WITHDRAW)->find($data['merchant_withdraw_id']);
        if( empty($original) ){
        	throw new error('ID有误，数据不存在');
        	object(parent::TABLE_LOCK)->close($merchant_withdraw_lock_id);//关闭锁
        } 
		
		if( $original['merchant_withdraw_state'] != 2 ){
			object(parent::TABLE_LOCK)->close($merchant_withdraw_lock_id);//关闭锁
			throw new error('该数据已经审核，请勿重复操作');
		}
		
		//格式化数据
		$update_data = array();
		$update_data['merchant_withdraw_state'] = 0;
        $update_data['merchant_withdraw_fail_time'] = time();
		if( isset($data['merchant_withdraw_fail_info']) ){
			$update_data['merchant_withdraw_fail_info'] = $data['merchant_withdraw_fail_info'];
		}
		
        //更新数据，记录日志
        if (object(parent::TABLE_MERCHANT_WITHDRAW)->update(array(array('merchant_withdraw_id=[+]', $data['merchant_withdraw_id'])), $update_data)) {
            object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			object(parent::TABLE_LOCK)->close($merchant_withdraw_lock_id);//关闭锁
            return $data['merchant_withdraw_id'];
        } else {
        	object(parent::TABLE_LOCK)->close($merchant_withdraw_lock_id);//关闭锁
            throw new error('操作失败');
        }
		
		
	}
	
	
	
	
	
	
	
	/**
	 * 商家提现通过
	 * 
	 * MERCHANTADMINWITHDRAWPASS
	 * {"class":"merchant/admin_withdraw","method":"api_pass"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_pass( $data = array() ){
		//检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_WITHDRAW_STATE);
		//校验数据
        object(parent::ERROR)->check($data, 'merchant_withdraw_id', parent::TABLE_MERCHANT_WITHDRAW, array('args'));
		
		//查询原始数据
        $merchant_withdraw_data = object(parent::TABLE_MERCHANT_WITHDRAW)->find($data['merchant_withdraw_id']);
        if( empty($merchant_withdraw_data) ){
        	throw new error('ID有误，数据不存在');
        }
		
		if( empty($merchant_withdraw_data['merchant_id']) || 
		empty($merchant_withdraw_data['user_id']) ){
			throw new error('ID有误，数据不合法');
		}
		
		if( $merchant_withdraw_data['merchant_withdraw_state'] != 2 ){
			throw new error('该数据已经审核，请勿重复操作');
		}
		
		if( empty($merchant_withdraw_data['merchant_withdraw_value']) ){
			throw new error('提现金额异常');
		}
		
		//获取商家数据
		$merchant_data = object(parent::TABLE_MERCHANT)->find($merchant_withdraw_data['merchant_id']);
		if( empty($merchant_data) ){
			throw new error('商家不存在');
		}
		if( empty($merchant_data["merchant_state"]) ||  $merchant_data["merchant_state"] != 1){
			throw new error('商家未认证');
		}
		//检测商家和商家用户状态
        if (!object(parent::TABLE_MERCHANT_USER)->check_state($merchant_withdraw_data['user_id'], $merchant_withdraw_data['merchant_id'])){
        	throw new error('该提现提交人是非法商家用户');
        }
        //检测认证
        $user_identity_data = object(parent::TABLE_USER_IDENTITY)->find($merchant_withdraw_data['user_id']);
        if (empty($user_identity_data) || $user_identity_data['user_identity_state'] != 1 || !object(parent::TABLE_USER_IDENTITY)->check_state($merchant_withdraw_data['user_id'], $user_identity_data['user_identity_update_time'])){
        	throw new error('该提现提交人没有实名认证');
        }
        
		$merchant_json = array();
		if( !empty($merchant_data["merchant_json"]) ){
			$merchant_json = cmd(array($merchant_data["merchant_json"]), "json decode");
		}
		if( !is_array($merchant_json)) {
			$merchant_json = array();
		}
		
		$order_json = array();
		//根据 提现方式 判断账号信息
		if($merchant_withdraw_data['merchant_withdraw_method'] == parent::PAY_METHOD_WEIXINPAY){
			if( empty($merchant_json["config_withdraw_weixinpay"]) || 
			!is_array($merchant_json["config_withdraw_weixinpay"]) ||
			!isset($merchant_json["config_withdraw_weixinpay"]["openid"]) ||
			!isset($merchant_json["config_withdraw_weixinpay"]["trade_type"]) ){
				throw new error('商家微信提现配置异常');
			}
			
			$order_json["config_withdraw_weixinpay"] = $merchant_json["config_withdraw_weixinpay"];
			
			//获取支付配置
			$pay_config = object(parent::REQUEST_APPLICATION)->get_pay_config(
				array(
					"withdraw_method" => $merchant_withdraw_data['merchant_withdraw_method'],
					"weixin_trade_type"=> $merchant_json["config_withdraw_weixinpay"]["trade_type"],
					"weixin_login_openid"=>$merchant_json["config_withdraw_weixinpay"]["openid"]
				), 
				$order_json, 
				array(
					"money_fen" => $merchant_withdraw_data['merchant_withdraw_value'],
					"subject" => "商家提现",
					"body" => "商家提现",
				)
			);
			
		}else
		if($merchant_withdraw_data['merchant_withdraw_method'] == parent::PAY_METHOD_ALIPAY){
			if( empty($merchant_json["config_withdraw_alipay"]) || 
			!is_array($merchant_json["config_withdraw_alipay"]) ||
			!isset($merchant_json["config_withdraw_alipay"]["account"]) ||
			!isset($merchant_json["config_withdraw_alipay"]["realname"]) ){
				throw new error('商家支付宝提现配置异常');
			}
			
			$order_json["config_withdraw_alipay"] = $merchant_json["config_withdraw_alipay"];
			
			//获取支付配置
			$pay_config = object(parent::REQUEST_APPLICATION)->get_pay_config(
				array(
					"withdraw_method" => $merchant_withdraw_data['merchant_withdraw_method'],
					"alipay_account"=> $merchant_json["config_withdraw_alipay"]["account"],
					"alipay_realname"=> $merchant_json["config_withdraw_alipay"]["realname"]
				), 
				$order_json, 
				array(
					"money_fen" => $merchant_withdraw_data['merchant_withdraw_rmb'],
					"subject" => "商家提现",
					"body" => "商家提现",
				)
			);
			
		}else{
			throw new error('操作失败，提现方式异常');
		}
		
		
		if( $merchant_withdraw_data['merchant_withdraw_type'] == "merchant_money"){
			//是否余额不足
	        $merchant_money = object(parent::TABLE_MERCHANT_MONEY)->find_now_data($merchant_withdraw_data['merchant_id']);
	        if( empty($merchant_money['merchant_money_value']) || $merchant_withdraw_data['merchant_withdraw_value'] > $merchant_money['merchant_money_value']){
	        	throw new error('商家钱包余额不足');
	        }
			
			$bool = object(parent::TABLE_MERCHANT_WITHDRAW)->pass_merchant_money(array(
				"merchant_withdraw_id" => $merchant_withdraw_data['merchant_withdraw_id'],
				"merchant_withdraw_admin" =>$_SESSION['user_id'],
				"merchant_withdraw_method" => $merchant_withdraw_data['merchant_withdraw_method'],
				"user_id" => $merchant_withdraw_data['user_id'],
				"merchant_id" => $merchant_withdraw_data['merchant_id'],
				"withdraw_value" => $merchant_withdraw_data['merchant_withdraw_value'],
				"withdraw_rmb" => $merchant_withdraw_data['merchant_withdraw_rmb'],
				"pay_config" => $pay_config,
				"merchant_money" => $merchant_money,
				"order_json" => $order_json
			));
			
		}else{
			throw new error('操作失败，提现类型异常');
		}
		
		//更新数据，记录日志
		if( !empty($bool['errno']) ){
			throw new error($bool['error']);
		}else
		if( empty($bool) ){
			throw new error('操作失败');
		}else{
			object(parent::TABLE_ADMIN_LOG)->insert($data, $merchant_withdraw_data);
			return $bool;
		}
		
	}
	
	
	
	
	
	
	
	
	
	
	
}
?>