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



namespace eapie\source\table\user;
use eapie\main;
class user_money_earning extends main {
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, "user");
    
		
		
	/**
	 * 数据检测
	 * 
	 * @var	array
	 */
	public $check = array(
		"user_money_earning_label" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("钱包标签的数据类型不合法"),
					),
			//字符长度检测
			'length' => array(
					'<length'=>array(200, "钱包标签的字符长度太多")
					),		
		),
		
		
		"user_money_earning_comment" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("钱包备注的数据类型不合法"),
					),
		),
		
		"money" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("钱包的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/iu', "钱包必须是整数"),
					),
		),
		
		
	);
	
	
	
		
	/**
	 * 获取一个id号
	 * 
	 * @param	void
	 * @return	string
	 */
	public function get_unique_id(){
		return cmd(array(22), 'random autoincrement');
	}	
	
	
	
	
			
	
	/**
	 * 插入新数据
	 * 
	 * @param	array		$data			数据
	 * @param	array		$call_data		数据
	 * @return	bool
	 */
	public function insert($data = array(), $call_data = array()){
		if( empty($data) && empty($call_data) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('user_money_earning')
		->call('data', $call_data)
		->insert($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}	
		
	
	
	
		
	
	
	/**
	 * 用户赠送收益管理操作
	 * $data = array(
	 * 	"admin_user_id" => "操作人，管理员的用户ID"
	 *  "user_id" => "用户id"
	 *  "comment" => 备注信息
	 *  "value" => 积分数量
	 *  "type" => 交易类型
	 *  "user_money_earning" => 用户赠送收益旧数据
	 * )
	 * 
	 * @return bool
	 */
	public function insert_admin( $data = array() ){
		if( empty($data["admin_user_id"]) || 
		empty($data["user_id"]) || 
		empty($data['type']) ||
		empty($data['value']) ||
		!is_numeric($data['value']) ||
		$data['value'] < 0 ){
			return false;
		}
		
		$data['comment'] = empty($data['comment'])? "" : $data['comment'];
		
		$lock_ids = array();
		//事务处理，开始锁商家的积分数据
        $user_money_earning_lock_id = object(parent::TABLE_LOCK)->start("user_id", $data["user_id"], parent::LOCK_MONEY_EARNING);
        if( empty($user_money_earning_lock_id) ){
            return false;//事务开启失败
        }
		$lock_ids[] = $user_money_earning_lock_id;
		
		if( $data["type"] == "admin_plus" ){
			//人工添加
			db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
            
            //将赠送收益 提交给 用户
            $user_money_earning_id = object(parent::TABLE_USER_MONEY_EARNING)->insert_plus(array(
                "user_id" => $data['user_id'],
                "user_money_earning_plus" => $data['value'],
                "user_money_earning_type" => $data['type']
            ));
            
            //积分充值失败
            if( empty($user_money_earning_id) ){
                db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
                object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
                return false;
            }
			
			//生成订单
	        $order_insert = array(
	            "order_id" => $this->get_unique_id(),
	            "order_type" => $data['type'],//提现
	            "order_comment" => $data['comment'],
	            "order_plus_method" => "user_money_earning",
	            "order_plus_account_id" => $data['user_id'],
	            "order_plus_value" => $data['value'],
	            "order_plus_transaction_id" => $user_money_earning_id,//交易号
	            "order_plus_update_time" => time(),
	            
	            "order_action_user_id" => $data['admin_user_id'],
	            "order_minus_method" => "",
	            "order_minus_account_id" => "",
	            "order_minus_value" => "",
	            "order_minus_transaction_id" => "",
	            "order_minus_update_time" => 0,
	            
	            "order_state" => 1,//确定订单
	            "order_pay_state" => 1,//已支付
	            "order_pay_time" => time(),
	            "order_insert_time" => time(),
	            "order_json" => array()
	        );
			
	        if( !object(parent::TABLE_ORDER)->insert($order_insert) ){
	            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
        		object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            	return false;
	        }
			
			
		}else
		if( $data["type"] == "admin_minus" ){
			//人工减少
			
			if( empty($data['user_money_earning']) || 
			empty($data['user_money_earning']['user_id']) ){
				object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
				return false;
			}
			
			db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
			
			//减用户赠送收益的积分
	        $user_money_earning_id = object(parent::TABLE_USER_MONEY_EARNING)->insert_minus(array(
	            "user_id" => $data["user_money_earning"]['user_id'],
	            "user_money_earning_join_id" => $data["user_money_earning"]['user_money_earning_id'],
	            "user_money_earning_value" => ($data["user_money_earning"]['user_money_earning_value'] - $data["value"]),
	            "user_money_earning_minus" => $data["value"],
	            "user_money_earning_type" => $data['type']
	        ));
			
			//积分充值失败
            if( empty($user_money_earning_id) ){
                db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
                object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
                return false;
            }
			
			//生成订单
	        $order_insert = array(
	            "order_id" => $this->get_unique_id(),
	            "order_type" => $data['type'],//提现
	            "order_comment" => $data['comment'],
	            "order_plus_method" => "",
	            "order_plus_account_id" => "",
	            "order_plus_value" => "",
	            "order_plus_transaction_id" => "",
	            "order_plus_update_time" => 0,
	            
	            "order_action_user_id" => $data['admin_user_id'],
	            "order_minus_method" => "user_money_earning",
	            "order_minus_account_id" => $data['user_id'],
	            "order_minus_value" => $data['value'],
	            "order_minus_transaction_id" => $user_money_earning_id,//交易号
	            "order_minus_update_time" => time(),
	            
	            "order_state" => 1,//确定订单
	            "order_pay_state" => 1,//已支付
	            "order_pay_time" => time(),
	            "order_insert_time" => time(),
	            "order_json" => array()
	        );
			
	        if( !object(parent::TABLE_ORDER)->insert($order_insert) ){
	            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
        		object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            	return false;
	        }
			
			
		}else{
			object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
			return false;
		}
				
			
		db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
		object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
		//清理当前项目缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);
		return $order_insert["order_id"];	
		
	}
	
	
	
	
	    
    /**
     * 用户赠送收益转账到用户扶贫账户
     * 
     * @param   string      $data["user_id"]                			用户ID
     * @param   int         $data["user_money_earning"]      			用户之前的赠送收益
	 * @param   int         $data["user_money_service_money_fen"]      	用户服务费
     * @param   string      $data["transfer_money_value"]     			转账余额数量
     * @param   string      $data["order_comment"]              		订单备注
     * @param   string      $data["order_plus_account_id"]      		收款账户，用户ID    
	 * @param   string      $data["order_action_user_id"]      			操作用户ID  
     * @param   string      $data["order_json"]                 		配置信息
     * @return  bool
     */ 
	public function insert_transfer_user_money_help( $data = array() ){
		if( empty($data["user_id"]) ||
        empty($data["user_money_earning"]) ||
        !isset($data["transfer_money_value"]) ||
        !is_numeric($data["transfer_money_value"]) ||
        empty($data["order_plus_account_id"]) ||
        empty($data["order_minus_account_id"]) ){
            return false;
        }
        
        if( empty($data["order_comment"]) ){
            $data["order_comment"] = "用户的赠送收益转账到钱包余额";
        }		
    	
		$lock_ids = array();
			
		//事务处理
		$user_money_help_lock_id = object(parent::TABLE_LOCK)->start("user_id", $data["order_plus_account_id"], parent::LOCK_MONEY_HELP);
        if( empty($user_money_help_lock_id) ){
            return false;//事务开启失败
        }
        $lock_ids[] = $user_money_help_lock_id;
		
		$user_money_earning_lock_id = object(parent::TABLE_LOCK)->start("user_id", $data["order_minus_account_id"], parent::LOCK_MONEY_EARNING);
        if( empty($user_money_earning_lock_id) ){
        	object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;//事务开启失败
        }
		$lock_ids[] = $user_money_earning_lock_id;
		
		
		db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
		
		//------------服务费操作---------------
		if( $data['user_money_service_money_fen'] > 0 ){
			$user_money_service_lock_id = object(parent::TABLE_LOCK)->start("user_id", $data["user_id"], parent::LOCK_MONEY_SERVICE);
	        if( empty($user_money_service_lock_id) ){
	        	db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
	        	object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
	            return false;//事务开启失败
	        }
			$lock_ids[] = $user_money_service_lock_id;
			
			$user_money_earning_value = $data["user_money_earning"]['user_money_earning_value'] - $data['user_money_service_money_fen'];
			//减少用户赠送收益
	        $service_user_money_earning_id = object(parent::TABLE_USER_MONEY_EARNING)->insert_minus(array(
	            "user_id" => $data["user_id"],
	            "user_money_earning_join_id" => $data["user_money_earning"]['user_money_earning_id'],
	            "user_money_earning_value" => $user_money_earning_value,
	            "user_money_earning_minus" => $data['user_money_service_money_fen'],
	            "user_money_earning_type" => parent::TRANSACTION_TYPE_TRANSFER
	        ));
			//减少失败
	        if( empty($service_user_money_earning_id) ){
	            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
	            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
	            return false;
	        }
			
			//增加用户服务费
	        $user_money_service_id = object(parent::TABLE_USER_MONEY_SERVICE)->insert_plus(array(
	            "user_id" => $data['user_id'],
	            "user_money_service_plus" => $data['user_money_service_money_fen'],
	            "user_money_service_type" => parent::TRANSACTION_TYPE_TRANSFER
	        ));
	        
	        //用户服务费充值失败
	        if( empty($user_money_service_id) ){
	            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
	            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
	            return false;
	        }	
			
			$user_money_service_comment = " [".($data["order_json"]['user_money_earning_transfer_user_money_help']['ratio_user_money_service']*100)."%] ";
			
			//生成订单
	        $service_order_insert = array(
	            "order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
	            "order_type" => parent::TRANSACTION_TYPE_TRANSFER,
	            "order_comment" => "赠送收益转账到扶贫账户,收取服务费".$user_money_service_comment,
	            "order_plus_method" => "user_money_service",
	            "order_plus_account_id" => $data['user_id'],
	            "order_plus_value" => $data['user_money_service_money_fen'],
	            "order_plus_transaction_id" => $user_money_service_id,//交易号
	            "order_plus_update_time" => time(),
	            
	            "order_action_user_id" => $data['user_id'],
	            "order_minus_method" => "user_money_earning",
	            "order_minus_account_id" => $data['user_id'],
	            "order_minus_value" => $data['user_money_service_money_fen'],
	            "order_minus_transaction_id" => $service_user_money_earning_id,
	            "order_minus_update_time" => time(),
	            
	            "order_state" => 1,//确定订单
	            "order_pay_state" => 1,//已支付
	            "order_pay_time" => time(),
	            "order_insert_time" => time(),
	            "order_json" => cmd(array($data["order_json"]), "json encode")
	        );
			
	        if( !object(parent::TABLE_ORDER)->insert($service_order_insert) ){
	            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
        		object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            	return false;
	        }
			
			
			//--------获取转账金额---------------
			$user_money_earning_value = $user_money_earning_value - $data["transfer_money_value"];
			
			//减少用户赠送收益
	        $user_money_earning_id = object(parent::TABLE_USER_MONEY_EARNING)->insert_minus_transaction(array(
	            "user_id" => $data["user_id"],
	            "user_money_earning_join_id" => $service_user_money_earning_id,
	            "user_money_earning_value" => $user_money_earning_value,
	            "user_money_earning_minus" => $data["transfer_money_value"],
	            "user_money_earning_type" => parent::TRANSACTION_TYPE_TRANSFER,
	            "user_money_earning_time" => (time() + 1)
	        ));
			//减少失败
	        if( empty($user_money_earning_id["user_money_earning_id"]) ){
	            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
	            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
	            return false;
	        }
			
			$user_money_earning_id = $user_money_earning_id["user_money_earning_id"];
			
		}else{
			
			//减少用户赠送收益
	        $user_money_earning_id = object(parent::TABLE_USER_MONEY_EARNING)->insert_minus(array(
	            "user_id" => $data["order_minus_account_id"],
	            "user_money_earning_join_id" => $data["user_money_earning"]['user_money_earning_id'],
	            "user_money_earning_value" => ($data["user_money_earning"]['user_money_earning_value'] - $data['transfer_money_value']),
	            "user_money_earning_minus" => $data['transfer_money_value'],
	            "user_money_earning_type" => parent::TRANSACTION_TYPE_TRANSFER
	        ));
	        //减少失败
	        if( empty($user_money_earning_id) ){
	            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
	            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
	            return false;
	        }
			
			
		}
		
		
		//将余额 提交给 用户扶贫账户
        $user_money_help_id = object(parent::TABLE_USER_MONEY_HELP)->insert_plus(array(
            "user_id" => $data['order_plus_account_id'],
            "user_money_help_plus" => $data['transfer_money_value'],
            "user_money_help_type" => parent::TRANSACTION_TYPE_TRANSFER
        ));
        
        //积分充值失败
        if( empty($user_money_help_id) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }	
		
		
		if( !empty($data["order_json"]) ){
            $data["order_json"] = cmd(array($data["order_json"]), "json encode");
        }else{
            $data["order_json"] = "";
        }
        
        //插入订单
        $order_insert = array(
            "order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
            "order_type" => parent::TRANSACTION_TYPE_TRANSFER,//转账
            "order_comment" => $data["order_comment"],
            "order_plus_method" => "user_money_help",
            "order_plus_account_id" => $data['order_plus_account_id'],
            "order_plus_value" => $data['transfer_money_value'],
            "order_plus_transaction_id" => $user_money_help_id,//交易号
            "order_plus_update_time" => time(),
            
            "order_action_user_id" => $data['user_id'],
            "order_minus_method" => "user_money_earning",
            "order_minus_account_id" => $data["order_minus_account_id"],
            "order_minus_value" => $data['transfer_money_value'],
            "order_minus_transaction_id" => $user_money_earning_id,
            "order_minus_update_time" => time(),
            
            "order_state" => 1,//确定订单
            "order_pay_state" => 1,//已支付
            "order_pay_time" => time(),
            "order_insert_time" => time(),
            "order_json" => $data["order_json"]
        );
        
        $bool = object(parent::TABLE_ORDER)->insert($order_insert);
        
        if( !empty($bool) ){
            db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
            
            return $order_insert['order_id'];
        }else{
        	db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
        	object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
        	
            return false;
        }
		
		
	}
	 
	
	
		
	
	
	  
    
     
    /**
     * 用户赠送收益转账到用户余额
     * 
     * @param   string      $data["user_id"]                			用户ID
     * @param   int         $data["user_money_earning"]      			用户之前的赠送收益
	 * @param   int         $data["user_money_service_money_fen"]      	用户服务费
     * @param   string      $data["transfer_money_value"]     			转账余额数量
     * @param   string      $data["order_comment"]              		订单备注
     * @param   string      $data["order_plus_account_id"]      		收款账户，用户ID    
	 * @param   string      $data["order_action_user_id"]      			操作用户ID  
     * @param   string      $data["order_json"]                 		配置信息
     * @return  bool
     */ 
	public function insert_transfer_user_money( $data = array() ){
		if( empty($data["user_id"]) ||
        empty($data["user_money_earning"]) ||
        !isset($data["transfer_money_value"]) ||
        !is_numeric($data["transfer_money_value"]) ||
        empty($data["order_plus_account_id"]) ||
        empty($data["order_minus_account_id"]) ){
            return false;
        }
        
        if( empty($data["order_comment"]) ){
            $data["order_comment"] = "用户的赠送收益转账到钱包余额";
        }		
    	
		$lock_ids = array();
			
		//事务处理
		$user_money_lock_id = object(parent::TABLE_LOCK)->start("user_id", $data["order_plus_account_id"], parent::LOCK_MONEY);
        if( empty($user_money_lock_id) ){
            return false;//事务开启失败
        }
        $lock_ids[] = $user_money_lock_id;
		
		$user_money_earning_lock_id = object(parent::TABLE_LOCK)->start("user_id", $data["order_minus_account_id"], parent::LOCK_MONEY_EARNING);
        if( empty($user_money_earning_lock_id) ){
        	object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;//事务开启失败
        }
		$lock_ids[] = $user_money_earning_lock_id;
		
		
		db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
        
        //------------服务费操作---------------
		if( $data['user_money_service_money_fen'] > 0 ){
			$user_money_service_lock_id = object(parent::TABLE_LOCK)->start("user_id", $data["user_id"], parent::LOCK_MONEY_SERVICE);
	        if( empty($user_money_service_lock_id) ){
	        	db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
	        	object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
	            return false;//事务开启失败
	        }
			$lock_ids[] = $user_money_service_lock_id;
			
			
			$user_money_earning_value = $data["user_money_earning"]['user_money_earning_value'] - $data['user_money_service_money_fen'];
			//减少用户赠送收益
	        $service_user_money_earning_id = object(parent::TABLE_USER_MONEY_EARNING)->insert_minus(array(
	            "user_id" => $data["user_id"],
	            "user_money_earning_join_id" => $data["user_money_earning"]['user_money_earning_id'],
	            "user_money_earning_value" => $user_money_earning_value,
	            "user_money_earning_minus" => $data['user_money_service_money_fen'],
	            "user_money_earning_type" => parent::TRANSACTION_TYPE_TRANSFER
	        ));
			//减少失败
	        if( empty($service_user_money_earning_id) ){
	            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
	            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
	            return false;
	        }
			
			//增加用户服务费
	        $user_money_service_id = object(parent::TABLE_USER_MONEY_SERVICE)->insert_plus(array(
	            "user_id" => $data['user_id'],
	            "user_money_service_plus" => $data['user_money_service_money_fen'],
	            "user_money_service_type" => parent::TRANSACTION_TYPE_TRANSFER
	        ));
	        
	        //用户服务费充值失败
	        if( empty($user_money_service_id) ){
	            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
	            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
	            return false;
	        }	
			
			$user_money_service_comment = " [".($data["order_json"]['user_money_earning_transfer_user_money']['ratio_user_money_service']*100)."%] ";
			
			//生成订单
	        $service_order_insert = array(
	            "order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
	            "order_type" => parent::TRANSACTION_TYPE_TRANSFER,
	            "order_comment" => "赠送收益转账到用户钱包,收取服务费".$user_money_service_comment,
	            "order_plus_method" => "user_money_service",
	            "order_plus_account_id" => $data['user_id'],
	            "order_plus_value" => $data['user_money_service_money_fen'],
	            "order_plus_transaction_id" => $user_money_service_id,//交易号
	            "order_plus_update_time" => time(),
	            
	            "order_action_user_id" => $data['user_id'],
	            "order_minus_method" => "user_money_earning",
	            "order_minus_account_id" => $data['user_id'],
	            "order_minus_value" => $data['user_money_service_money_fen'],
	            "order_minus_transaction_id" => $service_user_money_earning_id,
	            "order_minus_update_time" => time(),
	            
	            "order_state" => 1,//确定订单
	            "order_pay_state" => 1,//已支付
	            "order_pay_time" => time(),
	            "order_insert_time" => time(),
	            "order_json" => cmd(array($data["order_json"]), "json encode")
	        );
			
	        if( !object(parent::TABLE_ORDER)->insert($service_order_insert) ){
	            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
        		object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            	return false;
	        }
			
			
			//--------获取转账金额---------------
			$user_money_earning_value = $user_money_earning_value - $data["transfer_money_value"];
			
			//减少用户赠送收益
	        $user_money_earning_id = object(parent::TABLE_USER_MONEY_EARNING)->insert_minus_transaction(array(
	            "user_id" => $data["user_id"],
	            "user_money_earning_join_id" => $service_user_money_earning_id,
	            "user_money_earning_value" => $user_money_earning_value,
	            "user_money_earning_minus" => $data["transfer_money_value"],
	            "user_money_earning_type" => parent::TRANSACTION_TYPE_TRANSFER,
	            "user_money_earning_time" => (time() + 1)
	        ));
			//减少失败
	        if( empty($user_money_earning_id["user_money_earning_id"]) ){
	            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
	            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
	            return false;
	        }
			
			$user_money_earning_id = $user_money_earning_id["user_money_earning_id"];
			
		}else{
			
			//减少用户赠送收益
	        $user_money_earning_id = object(parent::TABLE_USER_MONEY_EARNING)->insert_minus(array(
	            "user_id" => $data["order_minus_account_id"],
	            "user_money_earning_join_id" => $data["user_money_earning"]['user_money_earning_id'],
	            "user_money_earning_value" => ($data["user_money_earning"]['user_money_earning_value'] - $data['transfer_money_value']),
	            "user_money_earning_minus" => $data['transfer_money_value'],
	            "user_money_earning_type" => parent::TRANSACTION_TYPE_TRANSFER
	        ));
	        //减少失败
	        if( empty($user_money_earning_id) ){
	            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
	            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
	            return false;
	        }
			
		}
        	
	        	
		//将余额 提交给 用户余额
        $user_money_id = object(parent::TABLE_USER_MONEY)->insert_plus(array(
            "user_id" => $data['order_plus_account_id'],
            "user_money_plus" => $data['transfer_money_value'],
            "user_money_type" => parent::TRANSACTION_TYPE_TRANSFER
        ));
        
        //积分充值失败
        if( empty($user_money_id) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
		
		if( !empty($data["order_json"]) ){
            $data["order_json"] = cmd(array($data["order_json"]), "json encode");
        }else{
            $data["order_json"] = "";
        }
        
        //插入订单
        $order_insert = array(
            "order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
            "order_type" => parent::TRANSACTION_TYPE_TRANSFER,//转账
            "order_comment" => $data["order_comment"],
            "order_plus_method" => "user_money",
            "order_plus_account_id" => $data['order_plus_account_id'],
            "order_plus_value" => $data['transfer_money_value'],
            "order_plus_transaction_id" => $user_money_id,//交易号
            "order_plus_update_time" => time(),
            
            "order_action_user_id" => $data['user_id'],
            "order_minus_method" => "user_money_earning",
            "order_minus_account_id" => $data["order_minus_account_id"],
            "order_minus_value" => $data['transfer_money_value'],
            "order_minus_transaction_id" => $user_money_earning_id,
            "order_minus_update_time" => time(),
            
            "order_state" => 1,//确定订单
            "order_pay_state" => 1,//已支付
            "order_pay_time" => time(),
            "order_insert_time" => time(),
            "order_json" => $data["order_json"]
        );
        
        $bool = object(parent::TABLE_ORDER)->insert($order_insert);
        
        if( !empty($bool) ){
            db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
            
            return $order_insert['order_id'];
        }else{
        	db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
        	object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
        	
            return false;
        }
        	
			
		
		
		
		
	}
	 
	 

	
	
	
	
	
	
	
				
		
	/**
	 * 更新数据
	 * 
	 * @param	array		$where
	 * @param	array		$data
	 * @param	array		$call_data
	 * @return	bool
	 */
	public function update($where = array(), $data = array(), $call_data = array()){
		if( empty($where) || (empty($data) && empty($call_data)) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('user_money_earning')
		->call('where', $where)
		->call('data', $call_data)
		->update($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}	
	
	
	
	
	
		
	
				
	/**
	 * 删除数据
	 * 
	 * @param	array	$where
	 * @return	array
	 */
	public function delete($where = array()){
		if( empty($where) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('user_money_earning')
		->call('where', $where)
		->delete();
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}
		
		
		
	
		
	
				
	/**
	 * 根据唯一标识，删除数据
	 * 
	 * @param	array	$user_money_earning_id
	 * @return	array
	 */
	public function remove($user_money_earning_id = ''){
		if( empty($user_money_earning_id) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('user_money_earning')
		->where(array('user_money_earning_id=[+]', (string)$user_money_earning_id))
		->delete();
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}
	
	
	
	
		
			
	/**
	 * 获取一个数据
	 * 
	 * @param	array	$user_money_earning_id
	 * @return	array
	 */
	public function find($user_money_earning_id = ''){
		if( empty($user_money_earning_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_money_earning_id), function($user_money_earning_id){
			return db(parent::DB_APPLICATION_ID)
			->table('user_money_earning')
			->where(array('user_money_earning_id=[+]', (string)$user_money_earning_id))
			->find();
		});
		
	}
	
	
	
	
		
	
	/**
	 * 根据用户ID，获取最新的一个数据
	 * 
	 * @param	array	$user_id
	 * @return	array
	 */
	public function find_now_data($user_id = ''){
		if( empty($user_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id), function($user_id){
			return db(parent::DB_APPLICATION_ID)
			->table('user_money_earning')
			->where(array('user_id=[+]', (string)$user_id))
			->orderby( array('user_money_earning_time', true), array('user_money_earning_id') )
			->find();
		});
		
	}
	
	
	
	
			
	/**
	 * 返回用户的最新积分ID，SQL语句
	 * 
	 * @param	string		$user_id
	 * @return	string
	 */
	public function sql_now_id($user_id){
		return db(parent::DB_APPLICATION_ID)
		->table('user_money_earning')
		->where(array('user_id=[+]', (string)$user_id))
		->orderby( array('user_money_earning_time', true), array('user_money_earning_id') )
		->find(array('user_money_earning_id'), function($q){
			return $q['query']['find'];
		});
	}
		
		
		
	/**
	 * 返回用户的最新剩余积分，SQL语句
	 * 
	 * @param	string		$user_id
	 * @return	string
	 */
	public function sql_now_value($user_id){
		return db(parent::DB_APPLICATION_ID)
		->table('user_money_earning')
		->where(array('user_id=[+]', (string)$user_id))
		->orderby( array('user_money_earning_time', true), array('user_money_earning_id') )
		->find(array('user_money_earning_value'), function($q){
			return $q['query']['find'];
		});
	}	
	
	
	
	
						
	/**
	 * 返回用户的最新剩余钱包时间，SQL语句
	 * 
	 * @param	string		$user_id
	 * @return	string
	 */
	public function sql_now_time($user_id){
		return db(parent::DB_APPLICATION_ID)
		->table('user_money_earning')
		->where(array('user_id=[+]', (string)$user_id))
		->orderby( array('user_money_earning_time', true), array('user_money_earning_id') )
		->find(array('user_money_earning_time'), function($q){
			return $q['query']['find'];
		});
	}	
	
	
	
	
	
		
	
	/**
	 * 收入
	 * 
	 * $data = array(
	 * 	"user_id" => 用户ID
	 * 	"user_money_earning_plus" => 要添加的余额
	 *  "user_money_earning_type" => 交易类型的键名称
	 *  "user_money_earning_time" => 时间
	 * );
	 * 
	 * 
	 * @param	array		$data		数据
	 * @return	bool
	 */
	public function insert_plus($data){
		if( empty($data['user_id']) || 
		empty($data['user_money_earning_plus']) ||
		!is_numeric($data['user_money_earning_plus']) ||
		$data['user_money_earning_plus'] < 0 ){
			return false;
		}
		
		//如果交易类型不存在
		$type_list = object(parent::TABLE_ORDER)->get_type();
		if( empty($data['user_money_earning_type']) ||
		!isset( $type_list[$data['user_money_earning_type']] ) ){
			return false;
		}
		
		$find_now_data = $this->find_now_data($data['user_id']);//查询用户当前积分
		if( !empty($find_now_data) ){
			$data["user_money_earning_value"] = $find_now_data['user_money_earning_value'] + $data["user_money_earning_plus"];
			$data["user_money_earning_join_id"] = $find_now_data['user_money_earning_id'];
		}else{
			$data["user_money_earning_value"] = $data["user_money_earning_plus"];
			$data["user_money_earning_join_id"] = "";
		}
		
		if( empty($data['user_money_earning_time']) ){
			$data['user_money_earning_time'] = time();
			if( !empty($find_now_data['user_money_earning_time']) && 
			$find_now_data['user_money_earning_time'] >= $data['user_money_earning_time'] ){
				$data['user_money_earning_time'] = ($find_now_data['user_money_earning_time'] + 1);
			}
		}
		if( !empty($find_now_data['user_money_earning_time']) && 
		$find_now_data['user_money_earning_time'] >= $data['user_money_earning_time'] ){
			return false;
		}
		
		
		$where = array();
		//连接
		if( empty($data['user_money_earning_join_id']) ){
			$where[] = array("([-]) IS NULL", $this->sql_now_id($data['user_id']), TRUE);
		}else{
			$where[] = array("([-]) = '".$data['user_money_earning_join_id']."'", $this->sql_now_id($data['user_id']), TRUE);
			$where[] = array("[and] ([-]) < ".$data['user_money_earning_time'], $this->sql_now_time($data['user_id']), TRUE);
			$where[] = array("[and] ([-]) + ".$data['user_money_earning_plus']." = ".$data['user_money_earning_value'], $this->sql_now_value($data['user_id']), TRUE);
		}
		
		$data['user_money_earning_id'] = $this->get_unique_id();//获取唯一ID
		$insert_sql = db(parent::DB_APPLICATION_ID)
		->table('user_money_earning', function($p){
			return "INSERT INTO " . $p['query']['table'] . 
			"(".
			"`user_money_earning_id`".
			",`user_money_earning_join_id`".
			", `user_id`".
			", `user_money_earning_type`".
			", `user_money_earning_plus`".
			", `user_money_earning_value`".
			", `user_money_earning_time`".
			")";
		});
		
		$select_sql = db(parent::DB_APPLICATION_ID)
		->table('user_money_earning')
		->call('where', $where)
		->select(function($p) use ($data){
			return "SELECT ". 
			"\"".$data['user_money_earning_id']."\"".
			",\"".$data['user_money_earning_join_id']."\"".
			",\"".$data['user_id']."\"".
			",\"".$data['user_money_earning_type']."\"".
			",".$data['user_money_earning_plus'].
			",".$data['user_money_earning_value'].
			",".$data['user_money_earning_time'].
			" FROM DUAL " . $p['query']['where'];
		});
		
		//printexit( $insert_sql." ".$select_sql );
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)->query($insert_sql." ".$select_sql);
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
			return $data['user_money_earning_id'];
		}else{
			return false;
		}
		
		
	}
	
	
	
	
	
	
	
		
	/**
	 * 支出
	 * 
	 * $data = array(
	 * 	"user_id" => 用户ID
	 * 	"user_money_earning_minus" => 要减少的余额
	 *  "user_money_earning_value" => 用户要更新的总余额
	 *  "user_money_earning_type" => 交易类型的键名称
	 *  "user_money_earning_time" => 时间
	 * );
	 * 
	 * 
	 * @return	bool
	 */
	public function insert_minus($data){
		if( empty($data['user_id']) || 
		empty($data['user_money_earning_minus']) ||
		!is_numeric($data['user_money_earning_minus']) ||
		$data['user_money_earning_minus'] < 1 ||
		!isset($data['user_money_earning_value']) ||
		!is_numeric($data['user_money_earning_value']) ||
		$data['user_money_earning_value'] < 0 ||
		empty($data['user_money_earning_join_id']) ||
		(!is_string($data['user_money_earning_join_id']) && !is_numeric($data['user_money_earning_join_id'])) ){
			return false;
		}
		
		//如果交易类型不存在
		$type_list = object(parent::TABLE_ORDER)->get_type();
		if( empty($data['user_money_earning_type']) ||
		!isset( $type_list[$data['user_money_earning_type']] ) ){
			return false;
		}
		
		$find_now_data = $this->find_now_data($data['user_id']);//查询用户当前钱包余额
		if( empty($find_now_data['user_money_earning_id']) ||
		$find_now_data['user_money_earning_id'] != $data['user_money_earning_join_id']){
			return false;
		}
		
		if( empty($data['user_money_earning_time']) ){
			$data['user_money_earning_time'] = time();
			if( $find_now_data['user_money_earning_time'] >= $data['user_money_earning_time'] ){
				$data['user_money_earning_time'] = ($find_now_data['user_money_earning_time'] + 1);
			}
		}
		if( $find_now_data['user_money_earning_time'] >= $data['user_money_earning_time'] ){
			return false;
		}
		
		$where = array();
		$where[] = array("([-]) = '".$data['user_money_earning_join_id']."'", $this->sql_now_id($data['user_id']), TRUE);
		$where[] = array("[and] ([-]) < ".$data['user_money_earning_time'], $this->sql_now_time($data['user_id']), TRUE);
		$where[] = array("[and] ([-]) - ".$data['user_money_earning_minus']." = ".$data['user_money_earning_value'], $this->sql_now_value($data['user_id']), TRUE);
		
		$data['user_money_earning_id'] = $this->get_unique_id();//获取唯一ID
		$insert_sql = db(parent::DB_APPLICATION_ID)
		->table('user_money_earning', function($p){
			return "INSERT INTO " . $p['query']['table'] . 
			"(".
			"`user_money_earning_id`".
			",`user_money_earning_join_id`".
			", `user_id`".
			", `user_money_earning_type`".
			", `user_money_earning_minus`".
			", `user_money_earning_value`".
			", `user_money_earning_time`".
			")";
		});
		
		$select_sql = db(parent::DB_APPLICATION_ID)
		->table('user_money_earning')
		->call('where', $where)
		->select(function($p) use ($data){
			return "SELECT ". 
			"\"".$data['user_money_earning_id']."\"".
			",\"".$data['user_money_earning_join_id']."\"".
			",\"".$data['user_id']."\"".
			",\"".$data['user_money_earning_type']."\"".
			",".$data['user_money_earning_minus'].
			",".$data['user_money_earning_value'].
			",".$data['user_money_earning_time'].
			" FROM DUAL " . $p['query']['where'];
		});
		
		//printexit( $insert_sql." ".$select_sql );
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)->query($insert_sql." ".$select_sql);
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
			return $data['user_money_earning_id'];
		}else{
			return false;
		}
		
	}
	
	
	
	
	
	
		
	/**
	 * 事务支出
	 * 
	 * $data = array(
	 * 	"user_id" => 用户ID
	 * 	"user_money_earning_join_id" => 关联的ID
	 * 	"user_money_earning_minus" => 要减少的余额
	 *  "user_money_earning_value" => 用户要更新的总余额
	 *  "user_money_earning_type" => 交易类型的键名称
	 *  "user_money_earning_time" => 时间
	 * );
	 * 
	 * 
	 * @return	false | array	返回假或者返回整个数据
	 */
	public function insert_minus_transaction($data){
		if( empty($data['user_id']) || 
		empty($data['user_money_earning_minus']) ||
		!is_numeric($data['user_money_earning_minus']) ||
		$data['user_money_earning_minus'] < 1 ||
		!isset($data['user_money_earning_value']) ||
		!is_numeric($data['user_money_earning_value']) ||
		$data['user_money_earning_value'] < 0 ||
		empty($data['user_money_earning_join_id']) ||
		(!is_string($data['user_money_earning_join_id']) && !is_numeric($data['user_money_earning_join_id'])) ){
			return false;
		}	
			
		//如果交易类型不存在
		$type_list = object(parent::TABLE_ORDER)->get_type();
		if( empty($data['user_money_earning_type']) ||
		!isset( $type_list[$data['user_money_earning_type']] ) ){
			return false;
		}
		
		if( empty($data['user_money_earning_time']) ){
			$data['user_money_earning_time'] = time();
		}
		
		$where = array();
		//白名单
        $whitelist = array(
            'user_money_earning_id', 
            'user_money_earning_join_id', 
            'user_id', 
            'user_money_earning_type',
            'user_money_earning_minus',
            'user_money_earning_value',
            'user_money_earning_time',
        );
        $insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		$insert_data['user_money_earning_id'] = $this->get_unique_id();//获取唯一ID
		$bool = db(parent::DB_APPLICATION_ID)
		->table('user_money_earning')
		->insert($insert_data);
		//printexit( $insert_sql." ".$select_sql );
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
			return $insert_data;
		}else{
			return false;
		}
		
		
		
	}
	
	
	
	
	
	
	
	
	
		
	/**
	 * 联表。返回最新剩余金额，SQL语句
	 * 
	 * @param   string      $alias	别名称
	 * @return	string
	 */
	public function sql_join_user_now_value($alias = ""){
		if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
		
		return db(parent::DB_APPLICATION_ID)
		->table('user_money_earning ume')
		->where(array('ume.user_id = '.$alias.'user_id'))
		->orderby( array('ume.user_money_earning_time', true), array('ume.user_money_earning_id') )
		->find(array('ume.user_money_earning_value'), function($q){
			return $q['query']['find'];
		});
	}	
	
	
	/**
	 * 联表。返回最新剩余金额交易时间，SQL语句
	 * 
	 * @param   string      $alias	别名称
	 * @return	string
	 */
	public function sql_join_user_now_time($alias = ""){
		if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
		
		return db(parent::DB_APPLICATION_ID)
		->table('user_money_earning ume')
		->where(array('ume.user_id = '.$alias.'user_id'))
		->orderby( array('ume.user_money_earning_time', true), array('ume.user_money_earning_id') )
		->find(array('ume.user_money_earning_time'), function($q){
			return $q['query']['find'];
		});
	}	
	
	
	
			
	/**
	 * 获取所有用户的积分之和
	 * 
	 * @param	void
	 * @return	array
	 */
	public function find_now_where_sum($where = array()){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($where), function($where){
			$from_user_money_earning = db(parent::DB_APPLICATION_ID)
			->table('user_money_earning')
			->orderby( array('user_money_earning_time', true), array('user_money_earning_id') )
			->select(function($q){
				return $q['query']['select'];
			});
			$from_user_money_earning = db(parent::DB_APPLICATION_ID)
			->from("(".$from_user_money_earning.") from_uc")
			->groupby('from_uc.user_id')
			->select(function($q){
				return $q['query']['select'];
			});
			$data = db(parent::DB_APPLICATION_ID)
			->from("(".$from_user_money_earning.") ume")
			->call("where", $where)
			//->where( array('ume.user_money_earning_value >= [-]', ) )
			->find("sum(ume.user_money_earning_value) as sum");
			
			return empty($data["sum"])? 0 : (int)$data["sum"];
		});
		
	}
	
	
	
		
	/**
	 * 余额列表
	 *
	 * @param  array	$config		配置
	 * @return array
	 */
    public function select_user_page($config) {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $call_where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
			$call_orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $call_limit   = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select       = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();
            $limit = array(
                (isset($call_limit[0])? $call_limit[0] : 0),
                (isset($call_limit[1])? $call_limit[1] : 0)
            );

            //设置返回的数据
            $data = array(
                'row_count' => 0,
                'limit_count' => $limit[0] + $limit[1],
                'page_size' => $limit[1],
                'page_count' => 0,
                'page_now' => 0,
                'data' => array()
            );
						
            //查询总条数
            $counts = db(parent::DB_APPLICATION_ID)
            ->table('user u')
            ->call('where', $call_where)
            ->find('count(*) as count');
            if (empty($counts['count'])) {
                return $data;
            } else {
                $data['row_count'] = $counts['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count'] / $data['page_size']);
                    $data['page_now'] = ceil($limit[0] / $data['page_size']) + 1;
                }
            }
			
			if( empty($select) ){
				$sql_join_now_value = $this->sql_join_user_now_value("u");
				$sql_join_now_time = $this->sql_join_user_now_time("u");
				$user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
				$select = array(
					"u.*",
					'IFNULL(('.$sql_join_now_value.'), 0) as user_money_earning_value',
					'('.$user_phone_verify_list_sql.') as user_phone_verify_list',
					'('.$sql_join_now_time.') as user_money_earning_time',
				);
			}
			
            //查询数据
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('user u')
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);

            return $data;
            }
        );
    }

	
	
	
	
		
	/**
	 * 交易流水列表
	 *
	 * @param  array	$config		配置
	 * @return array
	 */
    public function select_serial_page($config) {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $call_where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
			$call_orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $call_limit   = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select       = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();
            $limit = array(
                (isset($call_limit[0])? $call_limit[0] : 0),
                (isset($call_limit[1])? $call_limit[1] : 0)
            );

            //设置返回的数据
            $data = array(
                'row_count' => 0,
                'limit_count' => $limit[0] + $limit[1],
                'page_size' => $limit[1],
                'page_count' => 0,
                'page_now' => 0,
                'data' => array()
            );
			
			//订单表
            /*$order = array(
                'table' => 'order as o',
                'type' => 'LEFT',
                'on' => 'o.order_plus_transaction_id = ume.user_money_earning_id OR o.order_minus_transaction_id = ume.user_money_earning_id'
            );
			
			//操作人表
            $action_user = array(
                'table' => 'user as oau',
                'type' => 'LEFT',
                'on' => 'oau.user_id = o.order_action_user_id'
            );*/
            
            //增加订单表
            $plus_order = array(
                'table' => 'order as plus_o',
                'type' => 'LEFT',
                'on' => 'plus_o.order_pay_state=1 AND plus_o.order_plus_transaction_id = ume.user_money_earning_id AND plus_o.order_plus_method="user_money_earning"'
            );
			
			
			//减少订单表
            $minus_order = array(
                'table' => 'order as minus_o',
                'type' => 'LEFT',
                'on' => 'minus_o.order_pay_state=1 AND minus_o.order_minus_transaction_id = ume.user_money_earning_id AND minus_o.order_minus_method="user_money_earning"'
            );
			
			
			//增加操作人表
            $plus_action_user = array(
                'table' => 'user as plus_oau',
                'type' => 'LEFT',
                'on' => 'plus_oau.user_id = plus_o.order_action_user_id'
            );
			
			//减少操作人表
            $minus_action_user = array(
                'table' => 'user as minus_oau',
                'type' => 'LEFT',
                'on' => 'minus_oau.user_id = minus_o.order_action_user_id'
            );
			
			
            
			
			//用户表
			$user = array(
                'table' => 'user u',
                'type' => 'LEFT',
                'on' => 'u.user_id = ume.user_id'
            );
			
            //查询总条数
            $counts = db(parent::DB_APPLICATION_ID)
            ->table('user_money_earning ume')
			->joinon($user, $plus_order, $plus_action_user, $minus_order, $minus_action_user)
            ->call('where', $call_where)
            ->find('count(distinct ume.user_money_earning_id) as count');
            if (empty($counts['count'])) {
                return $data;
            } else {
                $data['row_count'] = $counts['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count'] / $data['page_size']);
                    $data['page_now'] = ceil($limit[0] / $data['page_size']) + 1;
                }
            }
			
			
			if( empty($select) ){
				$plus_oau_user_phone_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("plus_oau");
				$minus_oau_user_phone_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("minus_oau");
				$user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
				$select = array(
					"ume.*",
					"IFNULL(plus_o.order_id, minus_o.order_id) as order_id",
					"IFNULL(plus_o.order_comment, minus_o.order_comment) as order_comment",
					"IFNULL(plus_oau.user_id, minus_oau.user_id) as order_action_user_id",
					"IFNULL(plus_oau.user_logo_image_id, minus_oau.user_logo_image_id) as order_action_user_logo_image_id",
					"IFNULL(plus_oau.user_nickname, minus_oau.user_nickname) as order_action_user_nickname",
                    'IFNULL(('.$plus_oau_user_phone_sql.'), ('.$minus_oau_user_phone_sql.')) as order_action_user_phone_verify_list',
                    '('.$user_phone_verify_list_sql.') as user_phone_verify_list',
					'u.user_logo_image_id',
                    'u.user_nickname',
				);
			}
			
            //查询数据
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('user_money_earning ume')
			->joinon($user, $plus_order, $plus_action_user, $minus_order, $minus_action_user)
			->groupby("ume.user_money_earning_id")
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);

            return $data;
            }
        );
    }

	
	
	
	
	
	
	
	
	
	
}
?>