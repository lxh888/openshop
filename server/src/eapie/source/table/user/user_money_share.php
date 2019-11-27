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
class user_money_share extends main {
	
	
	/*消费共享金*/
	
	
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
		"user_money_share_label" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("钱包标签的数据类型不合法"),
					),
			//字符长度检测
			'length' => array(
					'<length'=>array(200, "钱包标签的字符长度太多")
					),		
		),
		
		
		"user_money_share_comment" => array(
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
		->table('user_money_share')
		->call('data', $call_data)
		->insert($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
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
		->table('user_money_share')
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
		->table('user_money_share')
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
	 * @param	array	$user_money_share_id
	 * @return	array
	 */
	public function remove($user_money_share_id = ''){
		if( empty($user_money_share_id) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('user_money_share')
		->where(array('user_money_share_id=[+]', (string)$user_money_share_id))
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
	 * @param	array	$user_money_share_id
	 * @return	array
	 */
	public function find($user_money_share_id = ''){
		if( empty($user_money_share_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_money_share_id), function($user_money_share_id){
			return db(parent::DB_APPLICATION_ID)
			->table('user_money_share')
			->where(array('user_money_share_id=[+]', (string)$user_money_share_id))
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
			->table('user_money_share')
			->where(array('user_id=[+]', (string)$user_id))
			->orderby( array('user_money_share_time', true), array('user_money_share_id') )
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
		->table('user_money_share')
		->where(array('user_id=[+]', (string)$user_id))
		->orderby( array('user_money_share_time', true), array('user_money_share_id') )
		->find();
    }
	
	
	
	
			
	/**
	 * 返回用户的最新积分ID，SQL语句
	 * 
	 * @param	string		$user_id
	 * @return	string
	 */
	public function sql_now_id($user_id){
		return db(parent::DB_APPLICATION_ID)
		->table('user_money_share')
		->where(array('user_id=[+]', (string)$user_id))
		->orderby( array('user_money_share_time', true), array('user_money_share_id') )
		->find(array('user_money_share_id'), function($q){
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
		->table('user_money_share')
		->where(array('user_id=[+]', (string)$user_id))
		->orderby( array('user_money_share_time', true), array('user_money_share_id') )
		->find(array('user_money_share_value'), function($q){
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
		->table('user_money_share')
		->where(array('user_id=[+]', (string)$user_id))
		->orderby( array('user_money_share_time', true), array('user_money_share_id') )
		->find(array('user_money_share_time'), function($q){
			return $q['query']['find'];
		});
	}	
	
	
	
	
		
	
	
	/**
	 * 用户消费共享金转换为养老、赠送收益、扶贫
	 * 
	 * @param   string		$user_id       				用户ID
	 * @param   int			$conversion_money			要兑换的金额
	 * @param	array		$user_money_share_data		用户共享金
	 * @param	array		$config						配置信息
	 * @return	bool
	 */
	public function insert_conversion_annuity_earning_help($user_id, $conversion_money, $user_money_share_data, $config){
		if( empty($user_id) || 
		empty($conversion_money) || 
		empty($user_money_share_data) || 
		empty($config) ||
		!isset($config['ratio_user_money_annuity']) ||
		!isset($config['ratio_user_money_help']) || 
		!isset($config['ratio_user_money_earning']) ){
			return false;
		}
		
		
		$lock_ids = array();
		//消费共享金操作
		$money_share_lock_id = object(parent::TABLE_LOCK)->start("user_id", $user_id, parent::LOCK_MONEY_SHARE);
        if( empty($money_share_lock_id) ){
        	return false;
        }else{
        	$lock_ids[] = $money_share_lock_id;
        }
		//养老金操作
		$money_annuity_lock_id = object(parent::TABLE_LOCK)->start("user_id", $user_id, parent::LOCK_MONEY_ANNUITY);
        if( empty($money_annuity_lock_id) ){
        	object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
        	return false;
        }else{
        	$lock_ids[] = $money_annuity_lock_id;
        }
		//赠送收益操作
		$money_earning_lock_id = object(parent::TABLE_LOCK)->start("user_id", $user_id, parent::LOCK_MONEY_EARNING);
        if( empty($money_earning_lock_id) ){
        	object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
        	return false;
        }else{
        	$lock_ids[] = $money_earning_lock_id;
        }
		//扶贫资金操作
		$money_help_lock_id = object(parent::TABLE_LOCK)->start("user_id", $user_id, parent::LOCK_MONEY_HELP);
        if( empty($money_help_lock_id) ){
        	object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
        	return false;
        }else{
        	$lock_ids[] = $money_help_lock_id;
        }
		
		//舍去法取整
		$money_annuity = floor($conversion_money * $config['ratio_user_money_annuity']);//养老金
		$money_help = floor($conversion_money * $config['ratio_user_money_help']);//扶贫资金账户
		$money_earning = floor($conversion_money * $config['ratio_user_money_earning']);//赠送收益账户
		
		$user_money_share_minus = ($money_annuity + $money_help + $money_earning);
		$user_money_share_value = $user_money_share_data["user_money_share_value"] - $user_money_share_minus;
		
		if( $money_annuity < 1 || $money_help < 1 || $money_earning < 1 || $user_money_share_value < 0){
			object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
			return false;
		}			
					
		//备注
		$money_annuity_comment = " [".($config['ratio_user_money_annuity']*100)."%] ";
		$money_help_comment = " [".($config['ratio_user_money_help']*100)."%] ";
		$money_earning_comment = " [".($config['ratio_user_money_earning']*100)."%] ";
		
		//开启事务
		db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
		
		//获取所剩 用户消费共享金
		$user_money_share_value = $user_money_share_data["user_money_share_value"] - $money_annuity;
		//减少
        $annuity_user_money_share_id = object(parent::TABLE_USER_MONEY_SHARE)->insert_minus(array(
            "user_id" => $user_id,
            "user_money_share_join_id" => $user_money_share_data['user_money_share_id'],
            "user_money_share_value" => $user_money_share_value,
            "user_money_share_minus" => $money_annuity,
            "user_money_share_type" => parent::TRANSACTION_TYPE_CONVERSION,
            "user_money_share_time" => (time() + 1)
        ));
        //减少失败
        if( empty($annuity_user_money_share_id) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
		//将 消费共享金 提交给 养老金
        $user_money_annuity_id = object(parent::TABLE_USER_MONEY_ANNUITY)->insert_plus(array(
            "user_id" => $user_id,
            "user_money_annuity_plus" => $money_annuity,
            "user_money_annuity_type" => parent::TRANSACTION_TYPE_CONVERSION,
            "user_money_annuity_time" => (time() + 1)
        ));
		//充值失败
        if( empty($user_money_annuity_id) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
		//生成订单
        $order_insert = array(
            "order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
            "order_type" => parent::TRANSACTION_TYPE_CONVERSION,//系统转换
            "order_comment" => "消费共享金转换为养老金".$money_annuity_comment,
            "order_plus_method" => "user_money_annuity",
            "order_plus_account_id" => $user_id,
            "order_plus_value" => $money_annuity,
            "order_plus_transaction_id" => $user_money_annuity_id,//交易号
            "order_plus_update_time" => time(),
            
            "order_action_user_id" => $user_id,
            "order_minus_method" => "user_money_share",
            "order_minus_account_id" => $user_id,
            "order_minus_value" => $money_annuity,
            "order_minus_transaction_id" => $annuity_user_money_share_id,
            "order_minus_update_time" => time(),
            
            "order_state" => 1,//确定订单
            "order_pay_state" => 1,//已支付
            "order_pay_time" => time(),
            "order_insert_time" => time(),
            "order_json" => cmd(array($config), "json encode")
        );
		
        if( !object(parent::TABLE_ORDER)->insert($order_insert) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }		
				
		//获取所剩 用户消费共享金
		$user_money_share_value = $user_money_share_value - $money_help;
		//减少
        $help_user_money_share = object(parent::TABLE_USER_MONEY_SHARE)->insert_minus_transaction(array(
            "user_id" => $user_id,
            "user_money_share_join_id" => $annuity_user_money_share_id,
            "user_money_share_value" => $user_money_share_value,
            "user_money_share_minus" => $money_help,
            "user_money_share_type" => parent::TRANSACTION_TYPE_CONVERSION,
            "user_money_share_time" => (time() + 2)
        ));
        //减少失败
        if( empty($help_user_money_share['user_money_share_id']) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
		//将 消费共享金 提交给 扶贫资金账户
        $user_money_help_id = object(parent::TABLE_USER_MONEY_HELP)->insert_plus(array(
            "user_id" => $user_id,
            "user_money_help_plus" => $money_help,
            "user_money_help_type" => parent::TRANSACTION_TYPE_CONVERSION,
            "user_money_help_time" => (time() + 2)
        ));
		//充值失败
        if( empty($user_money_help_id) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
		//生成订单
        $order_insert = array(
            "order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
            "order_type" => parent::TRANSACTION_TYPE_CONVERSION,//系统转换
            "order_comment" => "消费共享金转换为扶贫资金".$money_help_comment,
            "order_plus_method" => "user_money_help",
            "order_plus_account_id" => $user_id,
            "order_plus_value" => $money_help,
            "order_plus_transaction_id" => $user_money_help_id,//交易号
            "order_plus_update_time" => time(),
            
            "order_action_user_id" => $user_id,
            "order_minus_method" => "user_money_share",
            "order_minus_account_id" => $user_id,
            "order_minus_value" => $money_help,
            "order_minus_transaction_id" => $help_user_money_share['user_money_share_id'],
            "order_minus_update_time" => time(),
            
            "order_state" => 1,//确定订单
            "order_pay_state" => 1,//已支付
            "order_pay_time" => time(),
            "order_insert_time" => time(),
            "order_json" => cmd(array($config), "json encode")
        );
		
        if( !object(parent::TABLE_ORDER)->insert($order_insert) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
		
		//获取所剩 用户消费共享金
		$user_money_share_value = $user_money_share_value - $money_earning;
		//减少
        $earning_user_money_share = object(parent::TABLE_USER_MONEY_SHARE)->insert_minus_transaction(array(
            "user_id" => $user_id,
            "user_money_share_join_id" => $help_user_money_share['user_money_share_id'],
            "user_money_share_value" => $user_money_share_value,
            "user_money_share_minus" => $money_earning,
            "user_money_share_type" => parent::TRANSACTION_TYPE_CONVERSION,
            "user_money_share_time" => (time() + 3)
        ));
        //减少失败
        if( empty($earning_user_money_share) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
		//将 消费共享金 提交给 赠送收益账户
        $user_money_earning_id = object(parent::TABLE_USER_MONEY_EARNING)->insert_plus(array(
            "user_id" => $user_id,
            "user_money_earning_plus" => $money_earning,
            "user_money_earning_type" => parent::TRANSACTION_TYPE_CONVERSION,
            "user_money_earning_time" => (time() + 3)
        ));
		//充值失败
        if( empty($user_money_earning_id) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
		//生成订单
        $order_insert = array(
            "order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
            "order_type" => parent::TRANSACTION_TYPE_CONVERSION,//系统转换
            "order_comment" => "消费共享金转换为赠送收益".$money_earning_comment,
            "order_plus_method" => "user_money_earning",
            "order_plus_account_id" => $user_id,
            "order_plus_value" => $money_earning,
            "order_plus_transaction_id" => $user_money_earning_id,//交易号
            "order_plus_update_time" => time(),
            
            "order_action_user_id" => $user_id,
            "order_minus_method" => "user_money_share",
            "order_minus_account_id" => $user_id,
            "order_minus_value" => $money_earning,
            "order_minus_transaction_id" => $earning_user_money_share['user_money_share_id'],
            "order_minus_update_time" => time(),
            
            "order_state" => 1,//确定订单
            "order_pay_state" => 1,//已支付
            "order_pay_time" => time(),
            "order_insert_time" => time(),
            "order_json" => cmd(array($config), "json encode")
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
	 * 收入
	 * 
	 * $data = array(
	 * 	"user_id" => 用户ID
	 * 	"user_money_share_plus" => 要添加的余额
	 *  "user_money_share_type" => 交易类型的键名称
	 *  "user_money_share_time" => 时间
	 * );
	 * 
	 * 
	 * @param	array		$data		数据
	 * @return	bool
	 */
	public function insert_plus($data){
		if( empty($data['user_id']) || 
		empty($data['user_money_share_plus']) ||
		!is_numeric($data['user_money_share_plus']) ||
		$data['user_money_share_plus'] < 0 ){
			return false;
		}
		
		//如果交易类型不存在
		$type_list = object(parent::TABLE_ORDER)->get_type();
		if( empty($data['user_money_share_type']) ||
		!isset( $type_list[$data['user_money_share_type']] ) ){
			return false;
		}
		
		$find_now_data = $this->find_now_data($data['user_id']);//查询用户当前积分
		if( !empty($find_now_data) ){
			$data["user_money_share_value"] = $find_now_data['user_money_share_value'] + $data["user_money_share_plus"];
			$data["user_money_share_join_id"] = $find_now_data['user_money_share_id'];
		}else{
			$data["user_money_share_value"] = $data["user_money_share_plus"];
			$data["user_money_share_join_id"] = "";
		}
		
		if( empty($data['user_money_share_time']) ){
			$data['user_money_share_time'] = time();
			if( !empty($find_now_data['user_money_share_time']) && 
			$find_now_data['user_money_share_time'] >= $data['user_money_share_time'] ){
				$data['user_money_share_time'] = ($find_now_data['user_money_share_time'] + 1);
			}
		}
		if( !empty($find_now_data['user_money_share_time']) && 
		$find_now_data['user_money_share_time'] >= $data['user_money_share_time'] ){
			return false;
		}
		
		
		$where = array();
		//连接
		if( empty($data['user_money_share_join_id']) ){
			$where[] = array("([-]) IS NULL", $this->sql_now_id($data['user_id']), TRUE);
		}else{
			$where[] = array("([-]) = '".$data['user_money_share_join_id']."'", $this->sql_now_id($data['user_id']), TRUE);
			$where[] = array("[and] ([-]) < ".$data['user_money_share_time'], $this->sql_now_time($data['user_id']), TRUE);
			$where[] = array("[and] ([-]) + ".$data['user_money_share_plus']." = ".$data['user_money_share_value'], $this->sql_now_value($data['user_id']), TRUE);
		}
		
		$data['user_money_share_id'] = $this->get_unique_id();//获取唯一ID
		$insert_sql = db(parent::DB_APPLICATION_ID)
		->table('user_money_share', function($p){
			return "INSERT INTO " . $p['query']['table'] . 
			"(".
			"`user_money_share_id`".
			",`user_money_share_join_id`".
			", `user_id`".
			", `user_money_share_type`".
			", `user_money_share_plus`".
			", `user_money_share_value`".
			", `user_money_share_time`".
			")";
		});
		
		$select_sql = db(parent::DB_APPLICATION_ID)
		->table('user_money_share')
		->call('where', $where)
		->select(function($p) use ($data){
			return "SELECT ". 
			"\"".$data['user_money_share_id']."\"".
			",\"".$data['user_money_share_join_id']."\"".
			",\"".$data['user_id']."\"".
			",\"".$data['user_money_share_type']."\"".
			",".$data['user_money_share_plus'].
			",".$data['user_money_share_value'].
			",".$data['user_money_share_time'].
			" FROM DUAL " . $p['query']['where'];
		});
		
		//printexit( $insert_sql." ".$select_sql );
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)->query($insert_sql." ".$select_sql);
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
			return $data['user_money_share_id'];
		}else{
			return false;
		}
		
		
	}
	
	
	
	
	
	
	
		
	/**
	 * 支出
	 * 
	 * $data = array(
	 * 	"user_id" => 用户ID
	 * 	"user_money_share_minus" => 要减少的余额
	 *  "user_money_share_value" => 用户要更新的总余额
	 *  "user_money_share_type" => 交易类型的键名称
	 *  "user_money_share_time" => 时间
	 * );
	 * 
	 * 
	 * @return	bool
	 */
	public function insert_minus($data){
		if( empty($data['user_id']) || 
		empty($data['user_money_share_minus']) ||
		!is_numeric($data['user_money_share_minus']) ||
		$data['user_money_share_minus'] < 1 ||
		!isset($data['user_money_share_value']) ||
		!is_numeric($data['user_money_share_value']) ||
		$data['user_money_share_value'] < 0 ||
		empty($data['user_money_share_join_id']) ||
		(!is_string($data['user_money_share_join_id']) && !is_numeric($data['user_money_share_join_id'])) ){
			return false;
		}
		
		//如果交易类型不存在
		$type_list = object(parent::TABLE_ORDER)->get_type();
		if( empty($data['user_money_share_type']) ||
		!isset( $type_list[$data['user_money_share_type']] ) ){
			return false;
		}
		
		$find_now_data = $this->find_now_data($data['user_id']);//查询用户当前钱包余额
		if( empty($find_now_data['user_money_share_id']) ||
		$find_now_data['user_money_share_id'] != $data['user_money_share_join_id']){
			return false;
		}
		
		if( empty($data['user_money_share_time']) ){
			$data['user_money_share_time'] = time();
			if( $find_now_data['user_money_share_time'] >= $data['user_money_share_time'] ){
				$data['user_money_share_time'] = ($find_now_data['user_money_share_time'] + 1);
			}
		}
		if( $find_now_data['user_money_share_time'] >= $data['user_money_share_time'] ){
			return false;
		}
		
		
		$where = array();
		$where[] = array("([-]) = '".$data['user_money_share_join_id']."'", $this->sql_now_id($data['user_id']), TRUE);
		$where[] = array("[and] ([-]) < ".$data['user_money_share_time'], $this->sql_now_time($data['user_id']), TRUE);
		$where[] = array("[and] ([-]) - ".$data['user_money_share_minus']." = ".$data['user_money_share_value'], $this->sql_now_value($data['user_id']), TRUE);
		
		$data['user_money_share_id'] = $this->get_unique_id();//获取唯一ID
		$insert_sql = db(parent::DB_APPLICATION_ID)
		->table('user_money_share', function($p){
			return "INSERT INTO " . $p['query']['table'] . 
			"(".
			"`user_money_share_id`".
			",`user_money_share_join_id`".
			", `user_id`".
			", `user_money_share_type`".
			", `user_money_share_minus`".
			", `user_money_share_value`".
			", `user_money_share_time`".
			")";
		});
		
		$select_sql = db(parent::DB_APPLICATION_ID)
		->table('user_money_share')
		->call('where', $where)
		->select(function($p) use ($data){
			return "SELECT ". 
			"\"".$data['user_money_share_id']."\"".
			",\"".$data['user_money_share_join_id']."\"".
			",\"".$data['user_id']."\"".
			",\"".$data['user_money_share_type']."\"".
			",".$data['user_money_share_minus'].
			",".$data['user_money_share_value'].
			",".$data['user_money_share_time'].
			" FROM DUAL " . $p['query']['where'];
		});
		
		//printexit( $insert_sql." ".$select_sql );
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)->query($insert_sql." ".$select_sql);
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
			return $data['user_money_share_id'];
		}else{
			return false;
		}
		
		
	}
	
	
	
	
	
	
	
	/**
	 * 事务支出
	 * 
	 * $data = array(
	 * 	"user_id" => 用户ID
	 * 	"user_money_share_join_id" => 关联的ID
	 * 	"user_money_share_minus" => 要减少的余额
	 *  "user_money_share_value" => 用户要更新的总余额
	 *  "user_money_share_type" => 交易类型的键名称
	 *  "user_money_share_time" => 时间
	 * );
	 * 
	 * 
	 * @return	false | array	返回假或者返回整个数据
	 */
	public function insert_minus_transaction($data){
		if( empty($data['user_id']) || 
		empty($data['user_money_share_minus']) ||
		!is_numeric($data['user_money_share_minus']) ||
		$data['user_money_share_minus'] < 1 ||
		!isset($data['user_money_share_value']) ||
		!is_numeric($data['user_money_share_value']) ||
		$data['user_money_share_value'] < 0 ||
		empty($data['user_money_share_join_id']) ||
		(!is_string($data['user_money_share_join_id']) && !is_numeric($data['user_money_share_join_id'])) ){
			return false;
		}	
			
		//如果交易类型不存在
		$type_list = object(parent::TABLE_ORDER)->get_type();
		if( empty($data['user_money_share_type']) ||
		!isset( $type_list[$data['user_money_share_type']] ) ){
			return false;
		}
		
		if( empty($data['user_money_share_time']) ){
			$data['user_money_share_time'] = time();
		}
		
		$where = array();
		//白名单
        $whitelist = array(
            'user_money_share_id', 
            'user_money_share_join_id', 
            'user_id', 
            'user_money_share_type',
            'user_money_share_minus',
            'user_money_share_value',
            'user_money_share_time',
        );
        $insert_data = cmd(array($data, $whitelist), 'arr whitelist');
		$insert_data['user_money_share_id'] = $this->get_unique_id();//获取唯一ID
		$bool = db(parent::DB_APPLICATION_ID)
		->table('user_money_share')
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
	 * 获取需要转换的用户列表 _test
	 * 
	 * @param	array	$event_data		事件数据		
	 * @param	int		$limit_number	要查询的条数。每次循环的用户数
	 * @return	array
	 */
	public function select_system_conversion_user(&$event_data = array(), $limit_number = 10){
		if( empty($event_data) ||
		empty($event_data['event_stamp']) ||
		empty($event_data["event_json"]['multiple_user_credit']) ||
		empty($limit_number) ){
			return false;
		}
		
		//获取 用户 表的 limit 已查询用户数量
		if( empty($event_data["event_json"]['table_user_have_limit']) ){
			$event_data["event_json"]['table_user_have_limit'] = 0;
		}
		
		$user_limit_number = 0;//要查询的个数
		$return_user_data = array();//要返回的数据
		
		//如果 返回的用户数量 小于 查询的数量 则继续查询
		while( count($return_user_data) < $limit_number ){
			
			// 需要查询的用户数 - 已经查询合理的用户数 = 需要查询的用户数
			$user_limit_number = $limit_number - count($return_user_data);
			if( empty($user_limit_number) || $user_limit_number <= 0 ){
				break;
			}
			
			//查询用户，以 用户ID 排序
			$user_data = db(parent::DB_APPLICATION_ID)
	        ->table('user')
	        ->orderby( array('user_id') )
	        ->limit( $event_data["event_json"]['table_user_have_limit'], $user_limit_number )
	        ->select(array("user_id"));
			
			//更新 已查询的个数
			$event_data["event_json"]['table_user_have_limit'] = $event_data["event_json"]['table_user_have_limit'] + $user_limit_number;
			
			//获取 用户ID 集
			if( empty($user_data) ){
				break;
			}
			
			$user_ids = array();
			foreach($user_data as $value){
				$user_ids[] = $value['user_id'];
			}
			
			//获取分类数据
			$in_string = "\"".implode("\",\"", $user_ids)."\"";
			
			//清理订单已经存在的用户
			$money_share_order_user = db(parent::DB_APPLICATION_ID)
			->table('order')
			->where(
				array('order_type=[+]', parent::TRANSACTION_TYPE_SYSTEM_CONVERSION), //系统转换
				array('[and] order_plus_method=[+]', "user_money_share", true),//消费共享金等
				array('[and] order_state=1'),
				array('[and] order_insert_time=[+]', $event_data['event_stamp']),//以插入时间为准
				array("[and] order_plus_account_id IN([-])", $in_string, true)//是不加单引号并且强制不过滤
			)
			->select( array('order_plus_account_id') );
			
			if( !empty($money_share_order_user) ){
				foreach($money_share_order_user as $order_value){
					if( empty($user_data) ){
						break;
					}
					foreach($user_data as $user_key => $user_value){
						if($order_value['order_plus_account_id'] == $user_value['user_id']){
							unset($user_data[$user_key]);
							break;
						}
					}
				}
			}
			
			//如果 已经删除 完了，那么就 跳转到下一次循环
			if( empty($user_data) ){
				continue;
			}
			
			$user_ids = array();
			foreach($user_data as $value){
				$user_ids[] = $value['user_id'];
			}
			//获取分类数据
			$in_string = "\"".implode("\",\"", $user_ids)."\"";
			
			//用户的消费积分 必须 大于等于 $event_data["event_json"]['multiple_user_credit']
			
			//返回用户的积分
			$sql_user_credit_value = object(parent::TABLE_USER_CREDIT)->sql_join_user_now_value("u");
			$user_credit_user = db(parent::DB_APPLICATION_ID)
            ->table('user u')
            ->where( array("u.user_id IN([-])", $in_string, true) )
            ->select(array(
            	'u.user_id',
				'IFNULL(('.$sql_user_credit_value.'), 0) as user_credit_value'
			));
			
			
			foreach($user_data as $user_key => $user_value){
				$user_credit = array();//获取积分
				if( !empty($user_credit_user) ){
					foreach($user_credit_user as $credit_key => $credit_value){
						if($user_value['user_id'] == $credit_value['user_id']){
							$user_credit = $credit_value;
							unset($user_credit_user[$credit_key]);
							break;
						}
					}
				}
				//不存在积分，则删除
				if( empty($user_credit) ){
					unset($user_data[$user_key]);
				}
				
				//用户的消费积分 必须 大于等于 $event_data["event_json"]['multiple_user_credit']
				if( $user_credit['user_credit_value'] < $event_data["event_json"]['multiple_user_credit'] ){
					unset($user_data[$user_key]);
				}
			}
			
			//不为空，则合并
			if( !empty($user_data) ){
				$return_user_data = array_merge($return_user_data, $user_data);
			}
			
		}
		
		//更新 $event_data  这里不需要更新，引入的数据，外面更新
		//object(parent::TABLE_EVENT)->update_data($event_data);
		
		return $return_user_data;
	}
	
	
	
	
	
	
	/**
	 * 获取需要转换的用户列表 
	 * 
	 * @param	array	$event_data		事件数据		
	 * @param	int		$limit_number	要查询的条数。每次循环的用户数
	 * @return	array
	 */
	public function select_system_conversion_user_outmoded($event_data = array(), $limit_number = 10){
		if( empty($event_data) ||
		empty($event_data['event_stamp']) ||
		empty($event_data["event_json"]['multiple_user_credit']) ||
		empty($limit_number) ){
			return false;
		}
		
		$sql_money_share_order = db(parent::DB_APPLICATION_ID)
		->table('order')
		->where(
			array('order_type=[+]', parent::TRANSACTION_TYPE_SYSTEM_CONVERSION), //系统转换
			array('order_plus_method=[+]', "user_money_share", true),//消费共享金等
			array('order_state=1'),
			array('order_insert_time=[+]', $event_data['event_stamp'])//以插入时间为准
		)
		->select( array('order_plus_account_id'), function($q){
			return $q['query']['select'];
		});
		
		//用户的消费积分 必须 大于等于 $event_data["event_json"]['multiple_user_credit']
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
		$sql_user_credit = db(parent::DB_APPLICATION_ID)
		->from("(".$from_user_credit.") uc")
		->where( array('uc.user_credit_value >= [-]', $event_data["event_json"]['multiple_user_credit']) )
		->select("user_id", function($q){
			return $q['query']['select'];
		});
		
		$where = array();
		$where[] = array("user_id IN ( [-] ) ", $sql_user_credit, true);
		$where[] = array("[and] user_id NOT IN ( [-] )", $sql_money_share_order, true);
		
		return db(parent::DB_APPLICATION_ID)
        ->table('user')
		->call('where', $where)
        /*->call('where', $where_or)
		->call('where', $where_and)*/
        ->orderby( array('user_id') )
        ->limit( 0, $limit_number )
        ->select(array("user_id")/*, function($q){
			printexit($q['query']['select']);
		}*/);
		
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
		->table('user_money_share ums')
		->where(array('ums.user_id = '.$alias.'user_id'))
		->orderby( array('ums.user_money_share_time', true), array('ums.user_money_share_id') )
		->find(array('ums.user_money_share_value'), function($q){
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
		->table('user_money_share ums')
		->where(array('ums.user_id = '.$alias.'user_id'))
		->orderby( array('ums.user_money_share_time', true), array('ums.user_money_share_id') )
		->find(array('ums.user_money_share_time'), function($q){
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
			$from_user_money_share = db(parent::DB_APPLICATION_ID)
			->table('user_money_share')
			->orderby( array('user_money_share_time', true), array('user_money_share_id') )
			->select(function($q){
				return $q['query']['select'];
			});
			$from_user_money_share = db(parent::DB_APPLICATION_ID)
			->from("(".$from_user_money_share.") from_uc")
			->groupby('from_uc.user_id')
			->select(function($q){
				return $q['query']['select'];
			});
			$data = db(parent::DB_APPLICATION_ID)
			->from("(".$from_user_money_share.") ums")
			->call("where", $where)
			//->where( array('ums.user_money_share_value >= [-]', ) )
			->find("sum(ums.user_money_share_value) as sum");
			
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
					'IFNULL(('.$sql_join_now_value.'), 0) as user_money_share_value',
					'('.$user_phone_verify_list_sql.') as user_phone_verify_list',
					'('.$sql_join_now_time.') as user_money_share_time',
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
                'on' => 'o.order_plus_transaction_id = ums.user_money_share_id OR o.order_minus_transaction_id = ums.user_money_share_id'
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
                'on' => 'plus_o.order_pay_state=1 AND plus_o.order_plus_transaction_id = ums.user_money_share_id AND plus_o.order_plus_method="user_money_share"'
            );
			
			
			//减少订单表
            $minus_order = array(
                'table' => 'order as minus_o',
                'type' => 'LEFT',
                'on' => 'minus_o.order_pay_state=1 AND minus_o.order_minus_transaction_id = ums.user_money_share_id AND minus_o.order_minus_method="user_money_share"'
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
                'on' => 'u.user_id = ums.user_id'
            );
			
            //查询总条数
            $counts = db(parent::DB_APPLICATION_ID)
            ->table('user_money_share ums')
			->joinon($user, $plus_order, $plus_action_user, $minus_order, $minus_action_user)
            ->call('where', $call_where)
            ->find('count(distinct ums.user_money_share_id) as count');
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
					"ums.*",
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
            ->table('user_money_share ums')
			->joinon($user, $plus_order, $plus_action_user, $minus_order, $minus_action_user)
			->groupby("ums.user_money_share_id")
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