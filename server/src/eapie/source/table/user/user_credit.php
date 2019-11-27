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
class user_credit extends main {
	
	
	
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
		"user_credit_label" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("积分标签的数据类型不合法"),
					),
			//字符长度检测
			'length' => array(
					'<length'=>array(200, "积分标签的字符长度太多")
					),		
		),
		
		
		"user_credit_comment" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("积分备注的数据类型不合法"),
					),
		),
		
		"number" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("积分的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/iu', "积分必须是整数"),
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
		->table('user_credit')
		->call('data', $call_data)
		->insert($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}	
		
	
	
	
	/**
	 * 收入
	 * 
	 * $data = array(
	 * 	"user_id" => 用户ID
	 * 	"user_credit_plus" => 要添加的积分个数
	 *  "user_credit_type" => 交易类型的键名称
	 *  "user_credit_time" => 时间
	 * );
	 * 
	 * 
	 * @param	array		$data		数据
	 * @return	bool
	 */
	public function insert_plus($data){
		if( empty($data['user_id']) || 
		empty($data['user_credit_plus']) ||
		!is_numeric($data['user_credit_plus']) ||
		$data['user_credit_plus'] < 0 ){
			return false;
		}
		
		//如果交易类型不存在
		$type_list = object(parent::TABLE_ORDER)->get_type();
		if( empty($data['user_credit_type']) ||
		!isset( $type_list[$data['user_credit_type']] ) ){
			return false;
		}
		
		$find_now_data = $this->find_now_data($data['user_id']);//查询用户当前积分
		if( !empty($find_now_data) ){
			$data["user_credit_value"] = $find_now_data['user_credit_value'] + $data["user_credit_plus"];
			$data["user_credit_join_id"] = $find_now_data['user_credit_id'];
		}else{
			$data["user_credit_value"] = $data["user_credit_plus"];
			$data["user_credit_join_id"] = "";
		}
		
		if( empty($data['user_credit_time']) ){
			$data['user_credit_time'] = time();
			if( !empty($find_now_data['user_credit_time']) && 
			$find_now_data['user_credit_time'] >= $data['user_credit_time'] ){
				$data['user_credit_time'] = ($find_now_data['user_credit_time'] + 1);
			}
		}
		
		if( !empty($find_now_data['user_credit_time']) && 
		$find_now_data['user_credit_time'] >= $data['user_credit_time'] ){
			return false;
		}
		
		
		$where = array();
		//连接
		if( empty($data['user_credit_join_id']) ){
			$where[] = array("([-]) IS NULL", $this->sql_now_id($data['user_id']), TRUE);
		}else{
			$where[] = array("([-]) = '".$data['user_credit_join_id']."'", $this->sql_now_id($data['user_id']), TRUE);
			$where[] = array("[and] ([-]) < ".$data['user_credit_time'], $this->sql_now_time($data['user_id']), TRUE);
			$where[] = array("[and] ([-]) + ".$data['user_credit_plus']." = ".$data['user_credit_value'], $this->sql_now_value($data['user_id']), TRUE);
		}
		
		$data['user_credit_id'] = $this->get_unique_id();//获取唯一ID
		$insert_sql = db(parent::DB_APPLICATION_ID)
		->table('user_credit', function($p){
			return "INSERT INTO " . $p['query']['table'] . 
			"(".
			"`user_credit_id`".
			",`user_credit_join_id`".
			", `user_id`".
			", `user_credit_type`".
			", `user_credit_plus`".
			", `user_credit_value`".
			", `user_credit_time`".
			")";
		});
		
		$select_sql = db(parent::DB_APPLICATION_ID)
		->table('user_credit')
		->call('where', $where)
		->select(function($p) use ($data){
			return "SELECT ". 
			"\"".$data['user_credit_id']."\"".
			",\"".$data['user_credit_join_id']."\"".
			",\"".$data['user_id']."\"".
			",\"".$data['user_credit_type']."\"".
			",".$data['user_credit_plus'].
			",".$data['user_credit_value'].
			",".$data['user_credit_time'].
			" FROM DUAL " . $p['query']['where'];
		});
		
		//printexit( $insert_sql." ".$select_sql );
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)->query($insert_sql." ".$select_sql);
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
			
			return $data['user_credit_id'];
		}else{
			return false;
		}
		
		
	}
	
	
	
	/**
	 * 支出
	 * 
	 * $data = array(
	 * 	"user_id" => 用户ID
	 * 	"user_credit_join_id" => 用户之前的积分ID
	 * 	"user_credit_minus" => 要减少的积分个数
	 *  "user_credit_value" => 用户要更新的总积分
	 *  "user_credit_type" => 交易类型的键名称
	 *  "user_credit_time" => 时间
	 * );
	 * 
	 * 
	 * @return	bool
	 */
	public function insert_minus($data){
		if( empty($data['user_id']) || 
		empty($data['user_credit_minus']) ||
		!is_numeric($data['user_credit_minus']) ||
		$data['user_credit_minus'] < 1 ||
		!isset($data['user_credit_value']) ||
		!is_numeric($data['user_credit_value']) ||
		$data['user_credit_value'] < 0 ||
		empty($data['user_credit_join_id']) ||
		(!is_string($data['user_credit_join_id']) && !is_numeric($data['user_credit_join_id'])) ){
			return false;
		}
		
		//如果交易类型不存在
		$type_list = object(parent::TABLE_ORDER)->get_type();
		if( empty($data['user_credit_type']) ||
		!isset( $type_list[$data['user_credit_type']] ) ){
			return false;
		}
		
		$find_now_data = $this->find_now_data($data['user_id']);//查询用户当前钱包余额
		if( empty($find_now_data['user_credit_id']) ||
		$find_now_data['user_credit_id'] != $data['user_credit_join_id']){
			return false;
		}
		if( empty($data['user_credit_time']) ){
			$data['user_credit_time'] = time();
			if( $find_now_data['user_credit_time'] >= $data['user_credit_time'] ){
				$data['user_credit_time'] = ($find_now_data['user_credit_time'] + 1);
			}
		}
		if( $find_now_data['user_credit_time'] >= $data['user_credit_time'] ){
			return false;
		}
		
		
		$where = array();
		$where[] = array("([-]) = '".$data['user_credit_join_id']."'", $this->sql_now_id($data['user_id']), TRUE);
		$where[] = array("[and] ([-]) < ".$data['user_credit_time'], $this->sql_now_time($data['user_id']), TRUE);
		$where[] = array("[and] ([-]) - ".$data['user_credit_minus']." = ".$data['user_credit_value'], $this->sql_now_value($data['user_id']), TRUE);
		
		$data['user_credit_id'] = $this->get_unique_id();//获取唯一ID
		$insert_sql = db(parent::DB_APPLICATION_ID)
		->table('user_credit', function($p){
			return "INSERT INTO " . $p['query']['table'] . 
			"(".
			"`user_credit_id`".
			",`user_credit_join_id`".
			", `user_id`".
			", `user_credit_type`".
			", `user_credit_minus`".
			", `user_credit_value`".
			", `user_credit_time`".
			")";
		});
		
		$select_sql = db(parent::DB_APPLICATION_ID)
		->table('user_credit')
		->call('where', $where)
		->select(function($p) use ($data){
			return "SELECT ". 
			"\"".$data['user_credit_id']."\"".
			",\"".$data['user_credit_join_id']."\"".
			",\"".$data['user_id']."\"".
			",\"".$data['user_credit_type']."\"".
			",".$data['user_credit_minus'].
			",".$data['user_credit_value'].
			",".$data['user_credit_time'].
			" FROM DUAL " . $p['query']['where'];
		});
		
		//printexit( $insert_sql." ".$select_sql );
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)->query($insert_sql." ".$select_sql);
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
			
			return $data['user_credit_id'];
		}else{
			return false;
		}
		
	}
	
	
	

	
		
		
		
	
	/**
	 * 积分操作
	 * $data = array(
	 * 	"admin_user_id" => "操作人，管理员的用户ID"
	 *  "user_id" => "要添加的用户id"
	 *  "comment" => 备注信息
	 *  "value" => 积分数量
	 *  "type" => 交易类型
	 *  "user_credit" => 用户积分旧数据
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
        $user_credit_lock_id = object(parent::TABLE_LOCK)->start("user_id", $data["user_id"], parent::LOCK_CREDIT);
        if( empty($user_credit_lock_id) ){
            return false;//事务开启失败
        }
		$lock_ids[] = $user_credit_lock_id;
			
		if( $data["type"] == parent::TRANSACTION_TYPE_ADMIN_PLUS ){
			//人工添加
			db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
            
            //将充值积分 提交
            $user_credit_id = object(parent::TABLE_USER_CREDIT)->insert_plus(array(
                "user_id" => $data['user_id'],
                "user_credit_plus" => $data['value'],
                "user_credit_type" => $data['type']
            ));
            
            //积分充值失败
            if( empty($user_credit_id) ){
                db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
                object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
                return false;
            }
			
			//生成订单
	        $order_insert = array(
	            "order_id" => $this->get_unique_id(),
	            "order_type" => $data['type'],//提现
	            "order_comment" => $data['comment'],
	            "order_plus_method" => "user_credit",
	            "order_plus_account_id" => $data['user_id'],
	            "order_plus_value" => $data['value'],
	            "order_plus_transaction_id" => $user_credit_id,//交易号
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
		if( $data["type"] == parent::TRANSACTION_TYPE_ADMIN_MINUS ){
			//人工减少
			
			if( empty($data['user_credit']) || 
			empty($data['user_credit']['user_id']) ){
				object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
				return false;
			}
			
			db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
			
			//减积分
	        $user_credit_id = object(parent::TABLE_USER_CREDIT)->insert_minus(array(
	            "user_id" => $data["user_credit"]['user_id'],
	            "user_credit_join_id" => $data["user_credit"]['user_credit_id'],
	            "user_credit_value" => ($data["user_credit"]['user_credit_value'] - $data["value"]),
	            "user_credit_minus" => $data["value"],
	            "user_credit_type" => $data['type']
	        ));
			
			//积分充值失败
            if( empty($user_credit_id) ){
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
	            "order_minus_method" => "user_credit",
	            "order_minus_account_id" => $data['user_id'],
	            "order_minus_value" => $data['value'],
	            "order_minus_transaction_id" => $user_credit_id,//交易号
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
	 * E麦商城，邀请注册按身份发放积分
	 * @param array user
	 */
	public function invite_reward_credit_by_identity($user){
		
		// 如果没有推荐人
		if(!isset($user['user_parent_id']) || $user['user_parent_id'] === '' || empty($user['user_parent_id']))
			return false;

		$emshop_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("emshop_config"), true);
		$emshop_config = cmd(array($emshop_config), "json decode");
		
		// 如果不是E麦商城项目
		if(empty($emshop_config))
			return false;

		$admin_id = object(parent::TABLE_ADMIN_USER)->find($user['user_parent_id']);
		$distribution_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("shop_distribution_reward"), true);
		
		if( isset($admin_id['admin_id']) && isset($distribution_config[$admin_id['admin_id']]) ){
			// 会员
			$order_id = $this->insert_invite_register_user_credit(array(
				"user_id" => $user['user_parent_id'],
				"order_plus_account_id" => $user['user_parent_id'],
				"daily_attendance_credit_value" => $emshop_config['register_credit']['member'],
				"order_comment" => "邀请注册送积分",
				"order_json" => array(
					"emshop_config" => $emshop_config
				)
			));
			return $order_id;
			if( empty($order_id) ){
				return false;
			}else{
				return $order_id;
			}
		} else {
			// 非会员
			$order_id = $this->insert_invite_register_user_credit(array(
				"user_id" => $user['user_parent_id'],
				"order_plus_account_id" => $user['user_parent_id'],
				"daily_attendance_credit_value" => $emshop_config['register_credit']['user'],
				"order_comment" => "邀请注册送积分",
				"order_json" => array(
					"emshop_config" => $emshop_config
				)
			));
			
			if( empty($order_id) ){
				return false;
			}else{
				return $order_id;
			}
		}
	}

	/**
     * 邀请注册送用户积分
     * 
	 * @param   string      $data["user_id"] 							操作人用户ID
     * @param   string      $data["daily_attendance_credit_value"]     	签到送积分数量
     * @param   string      $data["order_comment"]             	 		订单备注
     * @param   string      $data["order_plus_account_id"]      		收款账户，商家ID    
     * @param   string      $data["order_json"]                 		配置信息
     * @return  bool
     */
    public function insert_invite_register_user_credit( $data = array() ){
    	if( !isset($data["daily_attendance_credit_value"]) ||
        !is_numeric($data["daily_attendance_credit_value"]) ||
        empty($data["user_id"]) ||
        empty($data["order_plus_account_id"]) ){
            return false;
        }
        
        if( empty($data["order_comment"]) ){
            $data["order_comment"] = "每日签到赠送用户积分";
        }
		
		 $user_lock_id = object(parent::TABLE_LOCK)->start("user_id", $data["order_plus_account_id"], parent::LOCK_CREDIT);
        if( empty($user_lock_id) ){
            return false;//事务开启失败
        }
        
        db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
        
        //将充值积分 提交给 用户
        $user_credit_id = object(parent::TABLE_USER_CREDIT)->insert_plus(array(
            "user_id" => $data['order_plus_account_id'],
            "user_credit_plus" => $data['daily_attendance_credit_value'],
            "user_credit_type" => parent::TRANSACTION_TYPE_DAILY_ATTENDANCE
        ));
        
        //积分充值失败
        if( empty($user_credit_id) ){
            file_put_contents(CACHE_PATH."/test.用户积分收入失败", cmd(array($data), "json encode"));   
            
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($user_lock_id);//关闭锁
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
            "order_type" => parent::TRANSACTION_TYPE_DAILY_ATTENDANCE,//每日签到送积分
            "order_comment" => $data["order_comment"],
            "order_plus_method" => "user_credit",
            "order_plus_account_id" => $data['order_plus_account_id'],
            "order_plus_value" => $data['daily_attendance_credit_value'],
            "order_plus_transaction_id" => $user_credit_id,//交易号
            "order_plus_update_time" => time(),
            
            "order_action_user_id" => $data["user_id"],
            "order_minus_method" => "",
            "order_minus_account_id" => "",
            "order_minus_value" => "",
            "order_minus_transaction_id" => "",
            "order_minus_update_time" => 0,
            
            "order_state" => 1,//确定订单
            "order_pay_state" => 1,//已支付
            "order_pay_time" => time(),
            "order_insert_time" => time(),
            "order_json" => $data["order_json"]
        );
        
        $bool = object(parent::TABLE_ORDER)->insert($order_insert);
        
        if( !empty($bool) ){
            db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
            object(parent::TABLE_LOCK)->close($user_lock_id);//关闭锁
            
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
            
            return $order_insert['order_id'];
        }else{
        	db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
        	object(parent::TABLE_LOCK)->close($user_lock_id);//关闭锁
        	
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
		->table('user_credit')
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
		->table('user_credit')
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
	 * @param	array	$user_credit_id
	 * @return	array
	 */
	public function remove($user_credit_id = ''){
		if( empty($user_credit_id) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('user_credit')
		->where(array('user_credit_id=[+]', (string)$user_credit_id))
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
	 * @param	array	$user_credit_id
	 * @return	array
	 */
	public function find($user_credit_id = ''){
		if( empty($user_credit_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_credit_id), function($user_credit_id){
			return db(parent::DB_APPLICATION_ID)
			->table('user_credit')
			->where(array('user_credit_id=[+]', (string)$user_credit_id))
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
			->table('user_credit')
			->where(array('user_id=[+]', (string)$user_id))
			->orderby( array('user_credit_time', true), array('user_credit_id') )
			->find();
		});
		
	}
	
	
	
	
	 /**
     * 根据用户ID，获取最新的一个数据，不带缓存
     * 
     * @param   string  $user_id
     * @return  array
     */
    public function find_now_data_unbuffered($user_id = ''){
        if( empty($user_id) ){
            return false;
        }
        return db(parent::DB_APPLICATION_ID)
		->table('user_credit')
		->where(array('user_id=[+]', (string)$user_id))
		->orderby( array('user_credit_time', true), array('user_credit_id') )
		->find();
    }
    
	
	
	
	
	/**
	 * 获取所有用户的积分之和
	 * 
	 * @param	void
	 * @return	array
	 */
	public function find_now_all_sum(){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array(), function(){
			$from_user_credit = db(parent::DB_APPLICATION_ID)
			->table('user_credit')
			->orderby( array('user_credit_time', true), array('user_credit_id') )
			->select(function($q){
				return $q['query']['select'];
			});
			$from_user_credit = db(parent::DB_APPLICATION_ID)
			->from("(".$from_user_credit.") from_uc")
			->groupby('from_uc.user_id')
			->select(function($q){
				return $q['query']['select'];
			});
			$data = db(parent::DB_APPLICATION_ID)
			->from("(".$from_user_credit.") uc")
			->find("sum(uc.user_credit_value) as sum");
			
			return empty($data["sum"])? 0 : (int)$data["sum"];
		});
		
	}
	
	
	
	
	
	
	
	
		
		
	/**
	 * 获取多个用户数据
	 *  $config = array(
	 * 	'where' => array(), //条件
	 * 	'orderby' => array(), //排序
	 * 	'limit'	=> array(0, page_size), //取出条数，默认不限制
	 * );
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function select($config = array()){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
			$where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
			$orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
			$limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
			$select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
			
			return db(parent::DB_APPLICATION_ID)
			->table('user_credit')
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
			
		});
		
	}	

	/**
	 * 查询交易明细
	 * @author green
	 * @create 2019-01-30T12:11:22+0800
	 *
	 * @param  [arr] $config [查询配置]
	 * @return array
	 */
    public function select_page($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__,
            array($config),
            function ($config) {
                $call_where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
                $call_orderby = array(array('user_credit_time', true));
                $call_limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
                $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

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
                    ->table('user_credit')
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

                //查询数据
                $data['data'] =  db(parent::DB_APPLICATION_ID)
                    ->table('user_credit')
                    ->call('where', $call_where)
                    ->call('orderby', $call_orderby)
                    ->call('limit', $call_limit)
                    ->select($select);

                return $data;
            }
        );
    }
	
	
		
	/**
	 * 返回用户的最新积分ID，SQL语句
	 * 
	 * @param	string		$user_id
	 * @return	string
	 */
	public function sql_now_id($user_id){
		return db(parent::DB_APPLICATION_ID)
		->table('user_credit')
		->where(array('user_id=[+]', (string)$user_id))
		->orderby( array('user_credit_time', true), array('user_credit_id') )
		->find(array('user_credit_id'), function($q){
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
		->table('user_credit')
		->where(array('user_id=[+]', (string)$user_id))
		->orderby( array('user_credit_time', true), array('user_credit_id') )
		->find(array('user_credit_value'), function($q){
			return $q['query']['find'];
		});
	}	
	
	
	
			
	/**
	 * 返回用户的最新剩余积分时间，SQL语句
	 * 
	 * @param	string		$user_id
	 * @return	string
	 */
	public function sql_now_time($user_id){
		return db(parent::DB_APPLICATION_ID)
		->table('user_credit')
		->where(array('user_id=[+]', (string)$user_id))
		->orderby( array('user_credit_time', true), array('user_credit_id') )
		->find(array('user_credit_time'), function($q){
			return $q['query']['find'];
		});
	}	
	
	
	
		
	/**
	 * 联表。返回用户的最新剩余积分，SQL语句
	 * 
	 * @param   string      $alias	别名称
	 * @return	string
	 */
	public function sql_join_user_now_value($alias = ""){
		if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
		
		return db(parent::DB_APPLICATION_ID)
		->table('user_credit uc')
		->where(array('uc.user_id = '.$alias.'user_id'))
		->orderby( array('uc.user_credit_time', true), array('uc.user_credit_id') )
		->find(array('uc.user_credit_value'), function($q){
			return $q['query']['find'];
		});
	}	
	
	
	/**
	 * 联表。返回用户的最新剩余积分交易时间，SQL语句
	 * 
	 * @param   string      $alias	别名称
	 * @return	string
	 */
	public function sql_join_user_now_time($alias = ""){
		if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
		
		return db(parent::DB_APPLICATION_ID)
		->table('user_credit uc')
		->where(array('uc.user_id = '.$alias.'user_id'))
		->orderby( array('uc.user_credit_time', true), array('uc.user_credit_id') )
		->find(array('uc.user_credit_time'), function($q){
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
			$from_user_credit_2 = db(parent::DB_APPLICATION_ID)
			->table('user_credit uc2')
			->where(array('uc2.user_id=uc.user_id'), array('[AND] uc2.user_credit_time>uc.user_credit_time'))
			->select(function($q){
				return $q['query']['select'];
			});
			
			//用户表
			$user = array(
                'table' => 'user u',
                'type' => 'INNER',
                'on' => 'u.user_id = uc.user_id'
            );
			
			$data = db(parent::DB_APPLICATION_ID)
			->table('user_credit uc')
			->joinon($user)
			->where(array('NOT EXISTS('.$from_user_credit_2.')'))
			->call("where", $where)
			//->where( array('uc.user_credit_value >= [-]', ) )
			->find("sum(uc.user_credit_value) as sum");
			
			
			/*$from_user_credit = db(parent::DB_APPLICATION_ID)
			->table('user_credit')
			->orderby( array('user_credit_time', true), array('user_credit_id') )
			->select(function($q){
				return $q['query']['select'];
			});
			$from_user_credit = db(parent::DB_APPLICATION_ID)
			->from("(".$from_user_credit.") from_uc")
			->groupby('from_uc.user_id')
			->select(function($q){
				return $q['query']['select'];
			});
			$data = db(parent::DB_APPLICATION_ID)
			->from("(".$from_user_credit.") uc")
			->call("where", $where)
			//->where( array('uc.user_credit_value >= [-]', ) )
			->find("sum(uc.user_credit_value) as sum");*/
			
			return empty($data["sum"])? 0 : (int)$data["sum"];
		});
		
	}
	
	
	
	
	
	
	
	
		
	/**
	 * 用户积分列表
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
					'IFNULL(('.$sql_join_now_value.'), 0) as user_credit_value',
					'('.$sql_join_now_time.') as user_credit_time',
					'('.$user_phone_verify_list_sql.') as user_phone_verify_list',
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
	 * 用户积分列表（不分页）
	 * 
	 * ------Mr.Zhao------2019.07.05------
	 *
	 * @param  array	$config		配置
	 * @return array
	 */
    public function select_user($config) {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $call_where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
			$call_orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $call_limit   = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select       = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();
            // $limit = array(
            //     (isset($call_limit[0])? $call_limit[0] : 0),
            //     (isset($call_limit[1])? $call_limit[1] : 1000)
            // );

			if( empty($select) ){
				$sql_join_now_value = $this->sql_join_user_now_value("u");
				$sql_join_now_time = $this->sql_join_user_now_time("u");
				$user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
				$select = array(
					"u.*",
					'IFNULL(('.$sql_join_now_value.'), 0) as user_credit_value',
					'('.$sql_join_now_time.') as user_credit_time',
					'('.$user_phone_verify_list_sql.') as user_phone_verify_list',
				);
			}
			
            //查询数据
            $data =  db(parent::DB_APPLICATION_ID)
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
			
			//订单支付时间一般在半个小时内完成支付 1800  大于减60秒
			
			//增加订单表
            $plus_order = array(
                'table' => 'order as plus_o',
                'type' => 'LEFT',
                'on' => 'plus_o.order_pay_state=1 AND plus_o.order_plus_transaction_id = uc.user_credit_id AND plus_o.order_plus_method="user_credit"'
            );
			
			
			//减少订单表
            $minus_order = array(
                'table' => 'order as minus_o',
                'type' => 'LEFT',
                'on' => 'minus_o.order_pay_state=1 AND minus_o.order_minus_transaction_id = uc.user_credit_id AND minus_o.order_minus_method="user_credit"'
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
                'on' => 'u.user_id = uc.user_id'
            );
			
			
            //查询总条数
            $counts = db(parent::DB_APPLICATION_ID)
            ->table('user_credit uc')
			->joinon($user, $plus_order, $plus_action_user, $minus_order, $minus_action_user)
            ->call('where', $call_where)
            ->find('count(distinct uc.user_credit_id) as count');
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
					"uc.*",
					"IFNULL(plus_o.order_id, minus_o.order_id) as order_id",
					"IFNULL(plus_o.order_comment, minus_o.order_comment) as order_comment",
					"IFNULL(plus_oau.user_id, minus_oau.user_id) as order_action_user_id",
					"IFNULL(plus_oau.user_logo_image_id, minus_oau.user_logo_image_id) as order_action_user_logo_image_id",
					"IFNULL(plus_oau.user_nickname, minus_oau.user_nickname) as order_action_user_nickname",
                    'IFNULL(('.$plus_oau_user_phone_sql.'), ('.$minus_oau_user_phone_sql.')) as order_action_user_phone_verify_list',
                    '('.$user_phone_verify_list_sql.') as user_phone_verify_list',
                    'u.user_id',
					'u.user_logo_image_id',
                    'u.user_nickname',
				);
			}
			
            //查询数据
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('user_credit uc')
			->joinon($user, $plus_order, $plus_action_user, $minus_order, $minus_action_user)
			->groupby("uc.user_credit_id")
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select/*, function($p){
            	printexit($p);
            }*/);

            return $data;
            }
        );
    }

	
	
	
			
	/**
	 * 平台赠送用户积分
	 * 
	 * @param	string		$user_id					用户ID
	 * @param	int			$money_fen					人民币，分
	 * @param	int			$order_action_user_id		订单操作人ID
	 * @param	string		$order_comment				订单注释
	 * @param   array       $order_json					订单配置信息
	 * @param   array       $lock_ids					锁ID
	 * @return	bool
	 */
	public function consume_user_credit_plus($user_id, $money_fen, $order_action_user_id, $order_comment, &$order_json, &$lock_ids){
		$config = object(parent::REQUEST_APPLICATION)->get_rmb_consume_user_credit($money_fen, array(), false);
		if( empty($config['state']) || !empty($config['error']) ){
			$order_json['rmb_consume_user_credit'] = $config;
			return false;//有错误，或者没有开启则返回
		}
		
		$user_credit_lock_id = object(parent::TABLE_LOCK)->start("user_id", $user_id, parent::LOCK_CREDIT);
        if( empty($user_credit_lock_id) ){
        	$config['error'] = "user_id为“".$user_id."”[LOCK_CREDIT]锁开启失败";
			$order_json['rmb_consume_user_credit'] = $config;
            return false;//事务开启失败
        }
		$lock_ids[] = $user_credit_lock_id;
		
		//积分充值失败
        if( $config['user_credit_plus'] < 1 ){
            $config['error'] = "所需要增加积分奖励数量小于1";   
			$order_json['rmb_consume_user_credit'] = $config;
            return false;
        }
		
		
		db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
		
		$user_credit_id = object(parent::TABLE_USER_CREDIT)->insert_plus(array(
            "user_id" => $user_id,
            "user_credit_plus" => $config['user_credit_plus'],
            "user_credit_type" => parent::TRANSACTION_TYPE_CONSUME
        ));
        //积分充值失败
        if( empty($user_credit_id) ){
            $config['error'] = "用户的消费积分赠送失败";   
			$order_json['rmb_consume_user_credit'] = $config;
			db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            return false;
        }
		
		//插入订单
        $order_insert = array(
            "order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
            "order_type" => parent::TRANSACTION_TYPE_CONSUME,//消费
            "order_comment" => $order_comment,
            "order_plus_method" => "user_credit",
            "order_plus_account_id" => $user_id,
            "order_plus_value" => $config["user_credit_plus"],
            "order_plus_transaction_id" => $user_credit_id,//交易号
            "order_plus_update_time" => time(),
            
            "order_action_user_id" => $order_action_user_id,
            "order_minus_method" => "",
            "order_minus_account_id" => "",
            "order_minus_value" => "",
            "order_minus_transaction_id" => "",
            "order_minus_update_time" => time(),
            
            "order_state" => 1,//确定订单
            "order_pay_state" => 1,//已支付
            "order_pay_time" => time(),
            "order_insert_time" => time(),
            "order_json" => cmd(array(array(
					"rmb_consume_user_credit" => $config
				)), "json encode")
        );
		
        if( !object(parent::TABLE_ORDER)->insert($order_insert) ){
        	db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
        	$config['error'] = "订单生成失败";   
			$order_json['rmb_consume_user_credit'] = $config;
            return false;
        }
		
		db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
		
		$order_json['rmb_consume_user_credit'] = $config;
		
		//当积分发生增加时，记录积分奖励
		object(parent::TABLE_AGENT_USER_CREDIT_AWARD_LOG)->destruct_insert($order_insert['order_id']);
		
		return true;		
	}
	

	
	
	



    /**
     * [不开事务]增加用户积分，通过平台
	 * 
	 * @param	string		$user_id					用户ID
	 * @param	int			$money_fen					人民币，分
	 * @param	int			$order_action_user_id		订单操作人ID
	 * @param	string		$order_comment				订单注释
	 * @param   array       $order_json					订单配置信息
	 * @param   array       $lock_ids					锁ID
	 * @return	bool
     */
	public function consume_user_credit_plus_not_transaction($user_id, $money_fen, $order_action_user_id, $order_comment, &$order_json, &$lock_ids){
        $config = object(parent::REQUEST_APPLICATION)->get_rmb_consume_user_credit($money_fen, array(), false);
        if( empty($config['state']) || !empty($config['error']) ){
            $order_json['rmb_consume_user_credit'] = $config;
            return false;//有错误，或者没有开启则返回
        }
        
        $user_credit_lock_id = object(parent::TABLE_LOCK)->start("user_id", $user_id, parent::LOCK_CREDIT);
        if( empty($user_credit_lock_id) ){
            $config['error'] = "user_id为“".$user_id."”[LOCK_CREDIT]锁开启失败";
            $order_json['rmb_consume_user_credit'] = $config;
            return false;//事务开启失败
        }
        $lock_ids[] = $user_credit_lock_id;
        
        //积分充值失败
        if( $config['user_credit_plus'] < 1 ){
            $config['error'] = "所需要增加积分奖励数量小于1";   
            $order_json['rmb_consume_user_credit'] = $config;
            return false;
        }
        
        
        $user_credit_id = object(parent::TABLE_USER_CREDIT)->insert_plus(array(
            "user_id" => $user_id,
            "user_credit_plus" => $config['user_credit_plus'],
            "user_credit_type" => parent::TRANSACTION_TYPE_CONSUME
        ));
        //积分充值失败
        if( empty($user_credit_id) ){
            $config['error'] = "用户的消费积分赠送失败";   
            $order_json['rmb_consume_user_credit'] = $config;
            return false;
        }
        
        //插入订单
        $order_insert = array(
            "order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
            "order_type" => parent::TRANSACTION_TYPE_CONSUME,//消费
            "order_comment" => $order_comment,
            "order_plus_method" => "user_credit",
            "order_plus_account_id" => $user_id,
            "order_plus_value" => $config["user_credit_plus"],
            "order_plus_transaction_id" => $user_credit_id,//交易号
            "order_plus_update_time" => time(),
            
            "order_action_user_id" => $order_action_user_id,
            "order_minus_method" => "",
            "order_minus_account_id" => "",
            "order_minus_value" => "",
            "order_minus_transaction_id" => "",
            "order_minus_update_time" => time(),
            
            "order_state" => 1,//确定订单
            "order_pay_state" => 1,//已支付
            "order_pay_time" => time(),
            "order_insert_time" => time(),
            "order_json" => cmd(array(array(
                    "rmb_consume_user_credit" => $config
                )), "json encode")
        );

        if (!object(parent::TABLE_ORDER)->insert($order_insert)) {
            $config['error'] = '平台赠送用户积分，订单生成失败';   
            $order_json['rmb_consume_user_credit'] = $config;
            return false;
        }

        $order_json['rmb_consume_user_credit'] = $config;
        
		//当积分发生增加时，记录积分奖励
		object(parent::TABLE_AGENT_USER_CREDIT_AWARD_LOG)->destruct_insert($order_insert['order_id']);
		
        return true;
    }
	    
    
    
    /**
     * 每日签到送用户积分
     * 
	 * @param   string      $data["user_id"] 							操作人用户ID
     * @param   string      $data["daily_attendance_credit_value"]     	签到送积分数量
     * @param   string      $data["order_comment"]             	 		订单备注
     * @param   string      $data["order_plus_account_id"]      		收款账户，商家ID    
     * @param   string      $data["order_json"]                 		配置信息
     * @return  bool
     */
    public function insert_daily_attendance_user_credit( $data = array() ){
    	if( !isset($data["daily_attendance_credit_value"]) ||
        !is_numeric($data["daily_attendance_credit_value"]) ||
        empty($data["user_id"]) ||
        empty($data["order_plus_account_id"]) ){
            return false;
        }
        
        if( empty($data["order_comment"]) ){
            $data["order_comment"] = "每日签到赠送用户积分";
        }
		
		 $user_lock_id = object(parent::TABLE_LOCK)->start("user_id", $data["order_plus_account_id"], parent::LOCK_CREDIT);
        if( empty($user_lock_id) ){
            return false;//事务开启失败
        }
        
        db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
        
        //将充值积分 提交给 用户
        $user_credit_id = object(parent::TABLE_USER_CREDIT)->insert_plus(array(
            "user_id" => $data['order_plus_account_id'],
            "user_credit_plus" => $data['daily_attendance_credit_value'],
            "user_credit_type" => parent::TRANSACTION_TYPE_DAILY_ATTENDANCE
        ));
        
        //积分充值失败
        if( empty($user_credit_id) ){
            file_put_contents(CACHE_PATH."/test.用户积分收入失败", cmd(array($data), "json encode"));   
            
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($user_lock_id);//关闭锁
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
            "order_type" => parent::TRANSACTION_TYPE_DAILY_ATTENDANCE,//每日签到送积分
            "order_comment" => $data["order_comment"],
            "order_plus_method" => "user_credit",
            "order_plus_account_id" => $data['order_plus_account_id'],
            "order_plus_value" => $data['daily_attendance_credit_value'],
            "order_plus_transaction_id" => $user_credit_id,//交易号
            "order_plus_update_time" => time(),
            
            "order_action_user_id" => $data["user_id"],
            "order_minus_method" => "",
            "order_minus_account_id" => "",
            "order_minus_value" => "",
            "order_minus_transaction_id" => "",
            "order_minus_update_time" => 0,
            
            "order_state" => 1,//确定订单
            "order_pay_state" => 1,//已支付
            "order_pay_time" => time(),
            "order_insert_time" => time(),
            "order_json" => $data["order_json"]
        );
        
        $bool = object(parent::TABLE_ORDER)->insert($order_insert);
        
        if( !empty($bool) ){
            db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
            object(parent::TABLE_LOCK)->close($user_lock_id);//关闭锁
            
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
            
            return $order_insert['order_id'];
        }else{
        	db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
        	object(parent::TABLE_LOCK)->close($user_lock_id);//关闭锁
        	
            return false;
        }
        
        
    }
    
	
 
	
	
	
	
	
		
	/**
	 * 消费共享金：由消费积分转换而来，消费积分乘以转换率获得 （自动分配：0:00）
	 * 在自动转换的时候，转换率则不变化了。
		算法：用户的消费积分（就是积分余额） * 当天转换率n = 人民币
		必须满 100 积分，并且是100的整除 才能转换
		比如 199积分  只能收到 100积分 * 转化率 =  消费共享金
		而200积分 收到 200积分*转换率 =  消费共享金
		
		0:00自动分配 消费共享金（满100元才进行分配）
		关于消费共享金 手续费扣除：
		20%进入扶贫资金账户，20%进入养老金，60%进入赠送收益账户
	 * 
	 * 
	 * 分配成功后一定要清理缓存
	 * 
	 * $data = array(
	 * 		"user_id"				用户ID
	 * 		"conversion_ratio"		转换率 
	 * 		"conversion_time"		转换时间
	 * 		"conversion_credit"		要转换的积分
	 * 		"user_credit_data"		用户当前积分数据
	 * 		"order_json"			订单JSON信息。是数组
	 * )
	 * 
	 * @param	array		$data					数据
	 * @return	bool
	 */
	public function insert_system_conversion_user_money_share($data){
		if( empty($data["user_id"]) ||
		empty($data["conversion_ratio"]) ||
		empty($data["conversion_time"]) || 
		empty($data["conversion_credit"]) ||
		empty($data["user_credit_data"]) ){
			return false;
		}
		
		$lock_ids = array();
		//加锁 开启事件
        $credit_lock_id = object(parent::TABLE_LOCK)->start("user_id", $data["user_id"], parent::LOCK_CREDIT);
        if( empty($credit_lock_id) ){
        	return false;
        }else{
        	$lock_ids[] = $credit_lock_id;
        }
		
		//消费共享金操作
		$money_share_lock_id = object(parent::TABLE_LOCK)->start("user_id", $data["user_id"], parent::LOCK_MONEY_SHARE);
        if( empty($money_share_lock_id) ){
        	object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
        	return false;
        }else{
        	$lock_ids[] = $money_share_lock_id;
        }
		
		/**
		 * 把消费积分看作人民币。
		 * (用户的个人消费积分* 100 分) * 转换率(元) = 获得消费共享金 j (元)
		 * 消费积分(分) - (j * 100)(分)  获得所剩消费积分
		 */
		$conversion_credit_yuan = $data["conversion_credit"] / 100;//将单位转换为元
		$user_money_share_plus_yuan = $conversion_credit_yuan * $data["conversion_ratio"];//单位元
		$user_money_share_plus = $user_money_share_plus_yuan * 100;//将单位转为  分
		if( preg_match("/\./", $user_money_share_plus) ){
			$user_money_share_plus = floor($user_money_share_plus);//如果存在小数
		}
		$user_credit_minus = $user_money_share_plus;//单位都是分
		
		$money_share_comment = " [".($data["conversion_ratio"]*100)."%] ";
		
		//开启事务
		db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
		
		//将 消费共享金 提交给 用户
        $user_money_share_id = object(parent::TABLE_USER_MONEY_SHARE)->insert_plus(array(
            "user_id" => $data["user_id"],
            "user_money_share_plus" => $user_money_share_plus,
            "user_money_share_type" => parent::TRANSACTION_TYPE_SYSTEM_CONVERSION,
            "user_money_share_time" => (time() + 1)
        ));
		
		//充值失败
        if( empty($user_money_share_id) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
		$user_credit_value = ($data["user_credit_data"]['user_credit_value'] - $user_credit_minus);
		$user_credit_minus = $user_credit_minus;
		if( $user_credit_value < 0 ){
			$user_credit_value = 0;
			$user_credit_minus = $data["user_credit_data"]['user_credit_value'];
		}
		
		//减少用户积分
        $user_credit_id = object(parent::TABLE_USER_CREDIT)->insert_minus(array(
            "user_id" => $data["user_id"],
            "user_credit_join_id" => $data["user_credit_data"]['user_credit_id'],
            "user_credit_value" => $user_credit_value,
            "user_credit_minus" => $user_credit_minus,
            "user_credit_type" => parent::TRANSACTION_TYPE_SYSTEM_CONVERSION,
            "user_credit_time" => (time() + 1)
        ));
		
        //积分减少失败
        if( empty($user_credit_id) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
		
		if( !empty($data["order_json"]) && is_array($data["order_json"]) ){
			$data["order_json"] = cmd(array($data["order_json"]), "json encode");
		} else {
			$data["order_json"] = "";
		}
		
		//生成订单
        $order_insert = array(
            "order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
            "order_type" => parent::TRANSACTION_TYPE_SYSTEM_CONVERSION,//系统转换
            "order_comment" => "用户积分系统转换为消费共享金".$money_share_comment,
            "order_plus_method" => "user_money_share",
            "order_plus_account_id" => $data["user_id"],
            "order_plus_value" => $user_money_share_plus,
            "order_plus_transaction_id" => $user_money_share_id,//交易号
            "order_plus_update_time" => time(),
            
            "order_action_user_id" => $data["user_id"],
            "order_minus_method" => "user_credit",
            "order_minus_account_id" => $data["user_id"],
            "order_minus_value" => $user_credit_minus,
            "order_minus_transaction_id" => $user_credit_id,
            "order_minus_update_time" => time(),
            
            "order_state" => 1,//确定订单
            "order_pay_state" => 1,//已支付
            "order_pay_time" => time(),
            "order_insert_time" => $data["conversion_time"],
            "order_json" => $data["order_json"]
        );
		
        if( !object(parent::TABLE_ORDER)->insert($order_insert) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
		db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
		object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
		object(parent::CACHE)->clear(self::CACHE_KEY);//清理当前项目缓存
		return true;
	} 
	
	
		
	
	
	
	
	
	/**
	 * 给推荐人积分奖励
	 * 
	 *  [推荐人] 可获得 [被推荐人(无论是普通用户还是其他商家用户)] 与 商家 交易时的所得积分的1%。
		[推荐人] 可获得 [被推荐商家用户] 与 [其他普通用户或是其他商家用户] 交易时的送出积分的0.5%。
		
		交易情况：
		1）商家收款时
		2）商家售卖商品时(目前只有自营)
		3）商家转赠积分时
		
		注意，商家积分未设置时，无法产生推荐积分的赠送事件。
	 * 
	 * 【析构，程序最后执行】
	 * @param	string		$merchant_id				商家ID
	 * @param	string		$user_id					用户ID
	 * @param	int			$credit_number				积分个数
	 * @param	string		$order_id					关联 资金订单ID  查询该订单ID是否存在，存在才继续执行
	 * 
	 * @return	如果返回真，有可能是不需要赠送奖励，或者赠送成功
	 */
	public function destruct_event_parent_recommend_user_credit($merchant_id, $user_id, $credit_number, $order_id){
		
		//获取数据库配置，防止标识资源被销毁
		$info = db(parent::DB_APPLICATION_ID)->info();
		$db_config = $info['config'];
		$db_application_id = parent::DB_APPLICATION_ID;
		destruct(__METHOD__ . '('.$order_id.')', true, array($db_config, $db_application_id), function($db_config, $db_application_id) use ($merchant_id, $user_id, $credit_number, $order_id){
			//重置一下 数据库链接
			db($db_application_id, true, $db_config);
			
			//判断order_id查询,如果为空，那么返回假
			$order_data = object(parent::TABLE_ORDER)->find_unbuffered($order_id);
			if( empty($order_data['order_pay_state']) ){
				return false;
			}
			
			if( !empty($order_data['order_json']) ){
				$order_data['order_json'] = cmd(array($order_data['order_json']), 'json decode');
			}else{
				$order_data['order_json'] = array();
			}
			
			$config = object(parent::REQUEST_APPLICATION)->get_parent_recommend_user_credit($credit_number, array(), false);
			if( empty($config['state']) || !empty($config['error']) ){
				$order_data['order_json']['parent_recommend_user_credit'] = $config;
				object(parent::TABLE_ORDER)->update_json($order_id, $order_data['order_json']);//更新一下信息
				return false;//有错误，或者没有开启则返回
			}	
				
			//创联众益项目奖励积分
			$event_id = object(parent::TABLE_EVENT)->insert(array(
				"event_name" => "parent_recommend_user_credit",
				"event_stamp" => $order_id,
				"event_json" => array(
					//参数
					"user_id" => $user_id,
					"merchant_id" => $merchant_id,
					"config" => $config,
				)
			));
			
			if( empty($event_id) ){
				$config['event'] = "推荐人积分奖励的事件登记失败";
				$order_data['order_json']['parent_recommend_user_credit'] = $config;
				object(parent::TABLE_ORDER)->update_json($order_id, $order_data['order_json']);//更新一下信息
				return false;//有错误，或者没有开启则返回
	        }
			
			$start_event = object(parent::PROJECT_CLZY)->get_event_url("parent_recommend_user_credit", $event_id, true);
			if( !empty($start_event['errno']) ){
				$config['event'] = "推荐人积分奖励的事件登记成功,但触发异常：".$start_event['error'];
				$order_data['order_json']['parent_recommend_user_credit'] = $config;
				object(parent::TABLE_ORDER)->update_json($order_id, $order_data['order_json']);//更新一下信息
				return false;//有错误，或者没有开启则返回
			}
			
			$config['event'] = "推荐人积分奖励的事件登记成功，已触发事件ID：".$event_id;
			$order_data['order_json']['parent_recommend_user_credit'] = $config;
			object(parent::TABLE_ORDER)->update_json($order_id, $order_data['order_json']);//更新一下信息
			return true;
			
		});
		
		
	}
	
	
		
	/**
	 * 父级推荐
	 * 
	 * @param	string		$user_parent_id		推荐人ID
	 * @param	string		$user_id			用户ID
	 * @param	int			$user_credit_plus	用户积分添加
	 * @param	string		$order_comment		订单备注
	 * @param	array		$config				配置
	 * @param	string		$error_prefix		错误前缀
	 * @param	int			$error				错误信息
	 * @param	int			$s					秒
	 * @return	bool
	 */
	public function event_parent_recommend_user_credit($user_parent_id, $user_id, $user_credit_plus, $order_comment, $config, $error_prefix, &$error){
		
		$lock_ids = array();
		
		$user_lock_id = object(parent::TABLE_LOCK)->start("user_id", $user_parent_id, parent::LOCK_CREDIT);
        if( empty($user_lock_id) ){
            $error[] = $error_prefix."推荐人平台积分奖励,user_id为“".$user_parent_id."”[LOCK_CREDIT]锁开启失败";
			return false;
        }
		$lock_ids[] = $user_lock_id;
		
		
		db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
		
		$user_credit_id = object(parent::TABLE_USER_CREDIT)->insert_plus(array(
            "user_id" => $user_parent_id,
            "user_credit_plus" => $user_credit_plus,
            "user_credit_type" => parent::TRANSACTION_TYPE_RECOMMEND_CREDIT
        ));
		
		//积分充值失败
        if( empty($user_credit_id) ){
        	$error[] = $error_prefix."推荐人平台积分奖励,user_id为“".$user_parent_id."”推荐人积分奖励失败";
			db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
			object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
			return false;
        }
		
		//插入订单
        $order_insert = array(
            "order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
            "order_type" => parent::TRANSACTION_TYPE_RECOMMEND_CREDIT,//推荐奖励
            "order_comment" => $order_comment,
            "order_plus_method" => "user_credit",
            "order_plus_account_id" => $user_parent_id,
            "order_plus_value" => $user_credit_plus,
            "order_plus_transaction_id" => $user_credit_id,//交易号
            "order_plus_update_time" => time(),
            
            "order_action_user_id" => $user_id,
            "order_minus_method" => "",
            "order_minus_account_id" => "",
            "order_minus_value" => "",
            "order_minus_transaction_id" => "",
            "order_minus_update_time" => time(),
            
            "order_state" => 1,//确定订单
            "order_pay_state" => 1,//已支付
            "order_pay_time" => time(),
            "order_insert_time" => time(),
            "order_json" => cmd(array(array(
					"parent_recommend_user_credit" => $config
				)), "json encode")
        );
		
		if( !object(parent::TABLE_ORDER)->insert($order_insert) ){
			$error[] = $error_prefix."推荐人平台积分奖励,user_id为“".$user_parent_id."”订单生成失败";
			db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
			object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
		
		db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
		object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
		//清理当前项目缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);
		return true;
		
		
	}
	


	/**
     * 奖励积分给代理用户
     * @Author green
     * 
     * @param   string  $user_id [代理用户ID]
     * @param   integer $credit  [奖励的积分数量]
     * @param   array   $config  [代理配置]
     * @return  bool
     */
    public function event_award_credit_to_agent($user_id, $order)
    {
        $lock_ids = array();

        // 锁表
        $user_lock_id = object(parent::TABLE_LOCK)->start('user_id', $user_id, parent::LOCK_CREDIT);
        if (empty($user_lock_id)) {
            return '锁表失败';
        }
        $lock_ids[] = $user_lock_id;

        //开始事务
        db(parent::DB_APPLICATION_ID)->query('START TRANSACTION');

        // 插入积分
        $user_credit_id = object(parent::TABLE_USER_CREDIT)->insert_plus(array(
            'user_id' => $user_id,
            'user_credit_plus' => $order['order_plus_value'],
            'user_credit_type' => parent::TRANSACTION_TYPE_AGENT_CREDIT_REWARD,
        ));
        if (empty($user_credit_id)) {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            object(parent::TABLE_LOCK)->close($lock_ids);
            return '插入积分数据失败';
        }

        // 插入订单
        $order['order_plus_transaction_id'] = $user_credit_id;
        $res = object(parent::TABLE_ORDER)->insert($order);
        if (!$res) {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            object(parent::TABLE_LOCK)->close($lock_ids);
            return '插入订单数据失败';
        }

        // 提交事务，关闭锁
        db(parent::DB_APPLICATION_ID)->query('COMMIT');
        object(parent::TABLE_LOCK)->close($lock_ids);

        // 清理当前项目缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);
        return true;
	}
	
	/**
	 * 优利小程序消费奖励积分
	 */
	public function youli_pay_award_credit($order_plus_account_id,$pay_money){
		$config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("youli_credit"), true);
		if(empty($config))
			return false;
		
		if((int)$config['scale'] <= 0 || (int)$config['is_open'] !== 1)
			return false;

		// 以分为保存的支付金额需要修改一次
		$user_credit_plus = (int)$config['scale'] * (int)$pay_money;

		$lock_ids = array();
		
		$user_lock_id = object(parent::TABLE_LOCK)->start("user_id", $order_plus_account_id, parent::LOCK_CREDIT);
        if( empty($user_lock_id) ){
            $error[] = "消费用户积分奖励,user_id为：“".$order_plus_account_id."”[LOCK_CREDIT]锁开启失败";
			return false;
        }
		$lock_ids[] = $user_lock_id;

		// 开始事务
		db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");


		$user_credit_id = object(parent::TABLE_USER_CREDIT)->insert_plus(array(
				"user_id" => $order_plus_account_id,
				"user_credit_plus" => $user_credit_plus,
				"user_credit_type" => parent::TRANSACTION_TYPE_PAY_AWARD_CREDIT
			)
		);

		//积分奖励失败
		if (empty($user_credit_id)) {
			$error[] = $error_prefix . "消费用户积分奖励,user_id为：“" . $order_plus_account_id . "”积分奖励失败";
			// 回滚
			db(parent::DB_APPLICATION_ID)->query("ROLLBACK"); 
			// 关闭锁
			object(parent::TABLE_LOCK)->close($lock_ids); 
			return false;
		}


		//插入订单
        $order_insert = array(
            "order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
            "order_type" => parent::TRANSACTION_TYPE_PAY_AWARD_CREDIT,//代理用户积分奖励
            "order_comment" => "优利小程序消费返积分",
            "order_plus_method" => "user_credit",
            "order_plus_account_id" => $order_plus_account_id,
            "order_plus_value" => $user_credit_plus,
            "order_plus_transaction_id" => $user_credit_id,//交易号
            "order_plus_update_time" => time(),
            "order_minus_method" => "",
            "order_minus_account_id" => "",
            "order_minus_value" => "",
            "order_minus_transaction_id" => "",
            "order_minus_update_time" => time(),
            
            "order_state" => 1,//确定订单
            "order_pay_state" => 1,//已支付
            "order_pay_time" => time(),
            "order_insert_time" => time(),
            "order_json" => cmd(array(array(
				"agent_user_credit_award" => $config
			)), "json encode")
		);
		
		if( !object(parent::TABLE_ORDER)->insert($order_insert) ){
			$error[] = $error_prefix."消费用户积分奖励,user_id为“".$order_plus_account_id."”订单生成失败";
			// 回滚
			db(parent::DB_APPLICATION_ID)->query("ROLLBACK");
			// 关闭锁
			object(parent::TABLE_LOCK)->close($lock_ids);
            return false;
		}
		
		// 提交
		db(parent::DB_APPLICATION_ID)->query("COMMIT");
		// 关闭锁
		object(parent::TABLE_LOCK)->close($lock_ids);
		// 清理当前项目缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);
		return true;
	}

	/**
	 * ------Mr.Zhao------2019.06.29------
	 * TODO：考虑跟父级推荐event_parent_recommend_user_credit，封装成一个函数
	 * 
	 * 代理用户赠送积分
	 * 
	 * @param	string		$order_plus_account_id		代理用户ID
	 * @param	string		$order_action_user_id		操作人用户ID
	 * @param	int			$user_credit_plus			用户积分添加
	 * @param	string		$order_comment				订单备注
	 * @param	array		$config						配置
	 * @param	string		$error_prefix				错误前缀
	 * @param	int			$error						错误信息
	 * @param	int			$s							秒
	 * @return	bool
	 */
	/*
	public function event_agent_user_credit_award($order_plus_account_id, $order_action_user_id, $user_credit_plus, $order_comment, $config, $error_prefix, &$error){
		
		$lock_ids = array();
		
		$user_lock_id = object(parent::TABLE_LOCK)->start("user_id", $order_plus_account_id, parent::LOCK_CREDIT);
        if( empty($user_lock_id) ){
            $error[] = $error_prefix."代理用户积分奖励,user_id为“".$order_plus_account_id."”[LOCK_CREDIT]锁开启失败";
			return false;
        }
		$lock_ids[] = $user_lock_id;
		
		
		db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
		
		$user_credit_id = object(parent::TABLE_USER_CREDIT)->insert_plus(array(
			"user_id" => $order_plus_account_id,
            "user_credit_plus" => $user_credit_plus,
            "user_credit_type" => parent::TRANSACTION_TYPE_AGENT_CREDIT_REWARD
		)
		);
		
		//积分奖励失败
		if (empty($user_credit_id)) {
			$error[] = $error_prefix . "代理用户积分奖励,user_id为“" . $order_plus_account_id . "”积分奖励失败";
			db(parent::DB_APPLICATION_ID)->query("ROLLBACK"); //回滚
			object(parent::TABLE_LOCK)->close($lock_ids); //关闭锁
			return false;
		}
		
		//插入订单
        $order_insert = array(
            "order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
            "order_type" => parent::TRANSACTION_TYPE_AGENT_CREDIT_REWARD,//代理用户积分奖励
            "order_comment" => $order_comment,
            "order_plus_method" => "user_credit",
            "order_plus_account_id" => $order_plus_account_id,
            "order_plus_value" => $user_credit_plus,
            "order_plus_transaction_id" => $user_credit_id,//交易号
            "order_plus_update_time" => time(),
            
            "order_action_user_id" => $order_action_user_id,
            "order_minus_method" => "",
            "order_minus_account_id" => "",
            "order_minus_value" => "",
            "order_minus_transaction_id" => "",
            "order_minus_update_time" => time(),
            
            "order_state" => 1,//确定订单
            "order_pay_state" => 1,//已支付
            "order_pay_time" => time(),
            "order_insert_time" => time(),
            "order_json" => cmd(array(array(
					"agent_user_credit_award" => $config
				)), "json encode")
        );
		
		if( !object(parent::TABLE_ORDER)->insert($order_insert) ){
			$error[] = $error_prefix."代理用户积分奖励,user_id为“".$order_plus_account_id."”订单生成失败";
			db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
			object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
		
		db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
		object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
		//清理当前项目缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);
		return true;
	}
	*/

	/**
	 * 当积分发生增加时，代理用户积分奖励
	 * 1）商家用户库存积分充值，系统赠送给该商家用户的用户积分。这个用户积分其代理获得比例积分奖励。
	 * 2）商家收款的时候，商家赠送给支付用户的用户积分。这个用户积分其代理获得比例积分奖励。
	 * 3）消费用户在在线商城中购买人民币消费的商品，售卖该商品的商家赠送消费用户的用户积分。这个用户积分其代理获得比例积分奖励。
	 * 4）商家用户将库存积分赠送给A用户。这个A用户所得积分其代理获得比例积分奖励。
	 * 
	 * @param	string		$user_id			用户ID
	 * @param	int			$credit_number		积分数量
	 * @param	string		$order_id			订单ID
	 * @return void
	 */
	/*
	public function destruct_event_agent_user_credit_award( $user_id, $credit_number, $order_id ){
		//获取数据库配置，防止标识资源被销毁
		$info = db(parent::DB_APPLICATION_ID)->info();
		$db_config = $info['config'];
		$db_application_id = parent::DB_APPLICATION_ID;
		destruct(__METHOD__ . '('.$order_id.')', true, array($db_config, $db_application_id), function($db_config, $db_application_id) use ($user_id, $credit_number, $order_id){
			//重置一下 数据库链接
			db($db_application_id, true, $db_config);
			
			//判断order_id查询,如果为空，那么返回假
			$order_data = object(parent::TABLE_ORDER)->find_unbuffered($order_id);
			
			if( empty($order_data['order_pay_state']) ){
				return false;
			}
			
			if( !empty($order_data['order_json']) ){
				$order_data['order_json'] = cmd(array($order_data['order_json']), 'json decode');
			}else{
				$order_data['order_json'] = array();
			}
			
			//先获取配置  检查是否 开启状态
	        $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('agent_user_credit_award'), true);
	        if( empty($config['state']) ){
				$order_data['order_json']['agent_user_credit_award'] = $config;
				object(parent::TABLE_ORDER)->update_json($order_id, $order_data['order_json']);//更新一下信息
	        	return false;
	        }
			
			//查询  用户的实名认证
			if( !object(parent::TABLE_USER_IDENTITY)->check_state($user_id) ){
				$config['event'] = "增加积分的用户没有实名认证";
				$order_data['order_json']['agent_user_credit_award'] = $config;
				object(parent::TABLE_ORDER)->update_json($order_id, $order_data['order_json']);//更新一下信息
				return false;
			}
			
			$user_identity_data = object(parent::TABLE_USER_IDENTITY)->find($user_id);
			if( empty($user_identity_data) ){
				$config['event'] = "增加积分的用户实名认证数据异常";
				$order_data['order_json']['agent_user_credit_award'] = $config;
				object(parent::TABLE_ORDER)->update_json($order_id, $order_data['order_json']);//更新一下信息
				return false;
			}
			
			//检查是否存在该 地区 的代理
			$agent_region_data = object(parent::TABLE_AGENT_REGION)->find_scope_province_city_district(
				$user_identity_data['user_identity_card_province'],
				$user_identity_data['user_identity_card_city'],
				$user_identity_data['user_identity_card_district']
			);
			if( empty($agent_region_data['agent_region_id']) ){
				$config['event'] = "增加积分的用户所属地区没有代理";
				$order_data['order_json']['agent_user_credit_award'] = $config;
				object(parent::TABLE_ORDER)->update_json($order_id, $order_data['order_json']);//更新一下信息
				return false;
			}
			
			//根据 代理地区id 获取代理用户，状态必须合法
			$exists = object(parent::TABLE_AGENT_USER)->find_exists_validity_region($agent_region_data['agent_region_id']);
			if( empty($exists) ){
				$config['event'] = "增加积分的用户所属代理地区没有代理人";
				$order_data['order_json']['agent_user_credit_award'] = $config;
				object(parent::TABLE_ORDER)->update_json($order_id, $order_data['order_json']);//更新一下信息
				return false;
			}
			
			//代理奖励积分
			$event_id = object(parent::TABLE_EVENT)->insert(array(
				"event_name" => "agent_user_credit_award",
				"event_stamp" => $order_id,
				"event_json" => array(
					//参数
					"user_id" => $user_id,
					'agent_region_province' => $user_identity_data['user_identity_card_province'],
					'agent_region_city' => $user_identity_data['user_identity_card_city'],
					'agent_region_district' => $user_identity_data['user_identity_card_district'],
					'user_credit' => $credit_number,//相关的增加积分
					"config" => $config,
				)
			));
			
			if( empty($event_id) ){
				$config['event'] = "代理人积分奖励的事件登记失败";
				$order_data['order_json']['agent_user_credit_award'] = $config;
				object(parent::TABLE_ORDER)->update_json($order_id, $order_data['order_json']);//更新一下信息
				return false;//有错误，或者没有开启则返回
	        }
			
			$start_event = object(parent::PROJECT_CLZY)->get_event_url("agent_user_credit_award", $event_id, true);
			if( !empty($start_event['errno']) ){
				$config['event'] = "代理人积分奖励的事件登记成功,但触发异常：".$start_event['error'];
				$order_data['order_json']['agent_user_credit_award'] = $config;
				object(parent::TABLE_ORDER)->update_json($order_id, $order_data['order_json']);//更新一下信息
				return false;//有错误，或者没有开启则返回
			}
			
			$config['event'] = "代理人积分奖励的事件登记成功，已触发事件ID：".$event_id;
			$order_data['order_json']['agent_user_credit_award'] = $config;
			object(parent::TABLE_ORDER)->update_json($order_id, $order_data['order_json']);//更新一下信息
			return true;
			
			
		});
	}
	*/


}