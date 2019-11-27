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
class admin_withdraw extends \eapie\source\request\user 
{
	
	

	/**
     * 用户钱包提现列表
     *
     * api: USERADMINWITHDRAWLIST
	 * {"class":"user/admin_withdraw","method":"api_list"}
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
     * @param  array	 $data 		请求参数
     * @return array
     */
	public function api_list( $data = array() ){
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
            'id_desc' => array('user_withdraw_id', true),
            'id_asc' => array('user_withdraw_id', false),
            'user_id_desc' => array('user_id', true),
            'user_id_asc' => array('user_id', false),
            'user_nickname_desc' => array('user_nickname', true),
            'user_nickname_asc' => array('user_nickname', false),
            'user_phone_verify_list_desc' => array('user_phone_verify_list', true),
            'user_phone_verify_list_asc' => array('user_phone_verify_list', false),
			
			
			'user_id_desc' => array('user_id', true),
            'user_id_asc' => array('user_id', false),
            'user_name_desc' => array('user_name', true),
            'user_name_asc' => array('user_name', false),
            
            'state_desc' => array('user_withdraw_state', true),
            'state_asc' => array('user_withdraw_state', false),
            'type_desc' => array('user_withdraw_type', true),
            'type_asc' => array('user_withdraw_type', false),
			'method_desc' => array('user_withdraw_method', true),
            'method_asc' => array('user_withdraw_method', false),
            
			'value_desc' => array('user_withdraw_value', true),
            'value_asc' => array('user_withdraw_value', false),
			
			'pass_time_desc' => array('user_withdraw_pass_time', true),
            'pass_time_asc' => array('user_withdraw_pass_time', false),
			'fail_time_desc' => array('user_withdraw_pass_time', true),
            'fail_time_asc' => array('user_withdraw_pass_time', false),
            
            'insert_time_desc' => array('user_withdraw_insert_time', true),
            'insert_time_asc' => array('user_withdraw_insert_time', false),
        ));

        //避免排序重复
        $config["orderby"][] = array('user_withdraw_id', false);
		$type_list = object(parent::TABLE_USER_WITHDRAW)->get_type();
		$method_list = object(parent::TABLE_USER_WITHDRAW)->get_method();
		
        //搜索
        if (!empty($data['search'])) {
			if (isset($data['search']['user_id']) && is_string($data['search']['user_id'])) {
                $config['where'][] = array('[and] uw.user_id=[+]', $data['search']['user_id']);
            }
			if (isset($data['search']['user_phone']) && is_string($data['search']['user_phone'])) {
				$user_data = object(parent::TABLE_USER_PHONE)->find_verify_data($data['search']['user_phone'], array("u.user_id"));
        		if( empty($user_data['user_id']) ){
        			$user_id = "";
        		}else{
        			$user_id = $user_data['user_id'];
        		}
                $config['where'][] = array('[and] uw.user_id=[+]', $user_id);
            }
			
			if( isset($data['search']['user_withdraw_state']) && 
			(is_string($data['search']['user_withdraw_state']) || is_numeric($data['search']['user_withdraw_state'])) &&
			in_array($data['search']['user_withdraw_state'], array("0", "1", "2")) ){
				$config["where"][] = array('[and] uw.user_withdraw_state=[+]', $data['search']['user_withdraw_state']);
			}
        }

        //查询数据
		$data_list = object(parent::TABLE_USER_WITHDRAW)->select_page($config);
		if( !empty($data_list["data"]) && !empty($type_list) ){
			foreach($data_list["data"] as $key => $value){
				if( isset($type_list[$value["user_withdraw_type"]]) ){
					$data_list["data"][$key]["user_withdraw_type_name"] = $type_list[$value["user_withdraw_type"]];
				}
				if( isset($method_list[$value["user_withdraw_method"]]) ){
					$data_list["data"][$key]["user_withdraw_method_name"] = $method_list[$value["user_withdraw_method"]];
				}
			}
		}
		
        return $data_list;
    }
	
	
	
	
	
	/**
	 * 商家提现审核不通过
	 * 
	 * USERADMINWITHDRAWFAIL
	 * {"class":"user/admin_withdraw","method":"api_fail"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_fail( $data = array() ){
		//检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_WITHDRAW_STATE);

		//校验数据
        object(parent::ERROR)->check($data, 'user_withdraw_id', parent::TABLE_USER_WITHDRAW, array('args'));
		object(parent::ERROR)->check($data, 'user_withdraw_fail_info', parent::TABLE_USER_WITHDRAW, array('args'));
		
		//事务处理
		$user_withdraw_lock_id = object(parent::TABLE_LOCK)->start("user_withdraw_id", $data["user_withdraw_id"], parent::LOCK_STATE);
        if( empty($user_withdraw_lock_id) ){
            return false;//事务开启失败
        }
		
		//查询原始数据
		$original = object(parent::TABLE_USER_WITHDRAW)->find($data['user_withdraw_id']);
		
        if( empty($original) ){
        	throw new error('ID有误，数据不存在');
        	object(parent::TABLE_LOCK)->close($user_withdraw_lock_id);//关闭锁
        } 
		
		if( $original['user_withdraw_state'] != 2 ){
			object(parent::TABLE_LOCK)->close($user_withdraw_lock_id);//关闭锁
			throw new error('该数据已经审核，请勿重复操作');
		}
		
		//格式化数据
		$update_data = array();
		$update_data['user_withdraw_state'] = 0;
        $update_data['user_withdraw_fail_time'] = time();
		if( isset($data['user_withdraw_fail_info']) ){
			$update_data['user_withdraw_fail_info'] = $data['user_withdraw_fail_info'];
		}
		
        //更新数据，记录日志
        if (object(parent::TABLE_USER_WITHDRAW)->update(array(array('user_withdraw_id=[+]', $data['user_withdraw_id'])), $update_data)) {
            object(parent::TABLE_ADMIN_LOG)->insert($data, $update_data);
			object(parent::TABLE_LOCK)->close($user_withdraw_lock_id);//关闭锁
            return $data['user_withdraw_id'];
        } else {
        	object(parent::TABLE_LOCK)->close($user_withdraw_lock_id);//关闭锁
            throw new error('操作失败');
        }
		
		
	}
	
	
	
	
	
	
	
	/**
	 * 用户提现通过
	 * 
	 * USERADMINWITHDRAWPASS
	 * {"class":"user/admin_withdraw","method":"api_pass"}
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function api_pass( $data = array() ){
		//检查权限
        object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_WITHDRAW_STATE);
		//校验数据
        object(parent::ERROR)->check($data, 'user_withdraw_id', parent::TABLE_USER_WITHDRAW, array('args'));
		
		//查询原始数据
        $user_withdraw_data = object(parent::TABLE_USER_WITHDRAW)->find($data['user_withdraw_id']);
        if( empty($user_withdraw_data) ){
        	throw new error('ID有误，数据不存在');
        }
		
		if( empty($user_withdraw_data['user_id']) || 
		empty($user_withdraw_data['user_id']) ){
			throw new error('ID有误，数据不合法');
		}
		
		if( $user_withdraw_data['user_withdraw_state'] != 2 ){
			throw new error('该数据已经审核，请勿重复操作');
		}
		
		if( empty($user_withdraw_data['user_withdraw_value']) ){
			throw new error('提现金额异常');
		}
		
		// return $user_withdraw_data;
		
		$user_data = object(parent::TABLE_USER)->find($user_withdraw_data['user_id']);
		// return $user_data;
		
		//检测认证
        /*$user_identity_data = object(parent::TABLE_USER_IDENTITY)->find($user_withdraw_data['user_id']);
        if (empty($user_identity_data) || $user_identity_data['user_identity_state'] != 1 || !object(parent::TABLE_USER_IDENTITY)->check_state($user_withdraw_data['user_id'], $user_identity_data['user_identity_update_time'])){
        	throw new error('该提现提交人没有实名认证');
		}*/
		// return $user_data;
		
		$user_json = array();
		if( !empty($user_data["user_json"]) ){
			$user_json = cmd(array($user_data["user_json"]), "json decode");
		}
		if( !is_array($user_json)) {
			$user_json = array();
		}
		
		$application = object(parent::MAIN)->api_application();
		$application_name = '';
		if( !empty($application['application_name']) ){
			$application_name = $application['application_name'];
		}
		
		// return $user_json;
		$order_json = array();
		//根据 提现方式 判断账号信息
		if($user_withdraw_data['user_withdraw_method'] == parent::PAY_METHOD_WEIXINPAY){
			if( empty($user_json['weixinpay']) || 
			!is_array($user_json['weixinpay']) ||
			!isset($user_json['weixinpay']["openid"]) ||
			!isset($user_json['weixinpay']["trade_type"]) ){
				throw new error('用户微信提现配置异常');
			}
			
			$order_json['weixinpay'] = $user_json['weixinpay'];
			
			//获取支付配置
			$pay_config = object(parent::REQUEST_APPLICATION)->get_pay_config(
				array(
					"withdraw_method" => $user_withdraw_data['user_withdraw_method'],
					"weixin_trade_type"=> $user_json['weixinpay']["trade_type"],
					"weixin_login_openid"=>$user_json['weixinpay']["openid"]
				), 
				$order_json, 
				array(
					"money_fen" => $user_withdraw_data['user_withdraw_value'],
					"subject" => $application_name." 用户提现",
					"body" => $application_name." 用户提现",
				)
			);
			
		}else
		if($user_withdraw_data['user_withdraw_method'] == parent::PAY_METHOD_ALIPAY){
			if( empty($user_json['alipay']) || 
			!is_array($user_json["alipay"]) ||
			!isset($user_json["alipay"]["account"]) ||
			!isset($user_json["alipay"]["realname"]) ){
				throw new error('用户支付宝提现配置异常');
			}
			
			$order_json["alipay"] = $user_json["alipay"];
			
			//获取支付配置
			$pay_config = object(parent::REQUEST_APPLICATION)->get_pay_config(
				array(
					"withdraw_method" => $user_withdraw_data['user_withdraw_method'],
					"alipay_account"=> $user_json["alipay"]["account"],
					"alipay_realname"=> $user_json["alipay"]["realname"]
				), 
				$order_json, 
				array(
					"money_fen" => $user_withdraw_data['user_withdraw_rmb'],
					"subject" => $application_name." 用户提现",
					"body" => $application_name." 用户提现",
				)
			);
			
		}else
		if($user_withdraw_data['user_withdraw_method'] == parent::PAY_METHOD_BANKCARD){
			//银行卡 提现
			//获取银行卡信息，以作备案
			if( !empty($user_json['bankcard']) && is_array($user_json['bankcard']) ){
				$order_json['bankcard'] = $user_json['bankcard'];
			}
			$pay_config = array();
		}else{
			throw new error('操作失败，提现方式异常');
		}
		
		
		if( $user_withdraw_data['user_withdraw_type'] == "user_money"){
			//是否余额不足
	        $user_money = object(parent::TABLE_USER_MONEY)->find_now_data($user_withdraw_data['user_id']);
	        if( empty($user_money['user_money_value']) || $user_withdraw_data['user_withdraw_value'] > $user_money['user_money_value']){
	        	throw new error('用户钱包余额不足');
	        }
			
			$bool = object(parent::TABLE_USER_WITHDRAW)->pass_user_money(array(
				"user_withdraw_id" => $user_withdraw_data['user_withdraw_id'],
				"user_withdraw_admin" =>$_SESSION['user_id'],
				"user_withdraw_method" => $user_withdraw_data['user_withdraw_method'],
				"user_id" => $user_withdraw_data['user_id'],
				"withdraw_value" => $user_withdraw_data['user_withdraw_value'],
				"withdraw_rmb" => $user_withdraw_data['user_withdraw_rmb'],
				"pay_config" => $pay_config,
				"user_money" => $user_money,
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
			object(parent::TABLE_ADMIN_LOG)->insert($data, $user_withdraw_data);
			return $bool;
		}
		
	}	
}
?>