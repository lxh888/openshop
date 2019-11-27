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
class user_withdraw extends main {
	
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, "order", "user_money");
	
	
	
	/**
     * @var [arr] [数据检测]
     */
    public $check = array(
        'user_withdraw_id' => array(
            'args'=>array(
                'exist' => array('缺少提现表ID名称参数'),
                'echo'  => array('提现表ID的数据类型不合法'),
                '!null' => array('提现表ID不能为空')
            )
        ),
        "money_fen" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少提现金额参数"),
					'echo'=>array("提现金额的数据类型不合法"),
					'match'=>array('/^[1-9]\d*$/', "提现金额必须是大于0的整数"),
					),
		),
		"user_withdraw_type" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少提现类型参数"),
                    'echo'=>array("提现类型的数据类型不合法"),
                    '!null'=>array("提现类型不能为空"),
                    'method'=>array(array(parent::TABLE_USER_WITHDRAW, 'check_type'), "提现类型输入有误，不能被识别")       
                ),
        ),
		"user_withdraw_method" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少提现方式参数"),
                    'echo'=>array("提现方式的数据类型不合法"),
                    '!null'=>array("提现方式不能为空"),
                    'method'=>array(array(parent::TABLE_USER_WITHDRAW, 'check_method'), "提现方式输入有误，不能被识别")       
                ),
        ),
        "user_withdraw_comment" => array(
            //参数检测
            'args'=>array(
                    'echo'=>array("备注信息的数据类型不合法"),
                    ),
        ),
		"user_withdraw_fail_info" => array(
            //参数检测
            'args'=>array(
                    'echo'=>array("审核不通过原因的数据类型不合法"),
                    ),
        ),
    );
	
    
	    
    /**
     * 获取提现类型
     * 
     * @param   void
     * @return  array
     */
    public function get_type(){
        return array(
            "user_money"=>"用户钱包",
        );
    }
    
    
    /**
     * 提现方式
     * 
     * @param   void
     * @return  array
     */
    public function get_method(){
        return array(
            parent::PAY_METHOD_WEIXINPAY => "微信",
            parent::PAY_METHOD_ALIPAY => "支付宝",
            parent::PAY_METHOD_BANKCARD => "银行卡",
        );
    }
    
	
    /**
     * 检测交易类型
     * 
     * @param   string  $data
     * @return  array
     */
	public function check_type($data){
		$type_list = $this->get_type();
        if( isset($type_list[$data]) ){
            return true;
        }else{
            return false;
        }
	}
	
	
	
    /**
     * 检测支付方式
     * 
     * @param   string  $data
     * @return  array
     */
    public function check_method($data){
        $method_list = $this->get_method();
        if( isset($method_list[$data]) ){
            return true;
        }else{
            return false;
        }
    }
    
    
	
	
	
	
	
	
	
 	/**
     * 获取一个id号
     * 
     * @param   void
     * @return  string
     */
    public function get_unique_id($num=0){
        if($num > 0){
            return cmd(array($num), 'random autoincrement');
        }
        return cmd(array(12), 'random autoincrement');
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
		->table('user_withdraw')
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
		->table('user_withdraw')
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
		->table('user_withdraw')
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
	 * @param	array	$user_withdraw_id
	 * @return	array
	 */
	public function remove($user_withdraw_id = ''){
		if( empty($user_withdraw_id) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('user_withdraw')
		->where(array('user_withdraw_id=[+]', (string)$user_withdraw_id))
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
	 * @param	array	$user_withdraw_id
	 * @return	array
	 */
	public function find($user_withdraw_id = ''){
		if( empty($user_withdraw_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_withdraw_id), function($user_withdraw_id){
			return db(parent::DB_APPLICATION_ID)
			->table('user_withdraw')
			->where(array('user_withdraw_id=[+]', (string)$user_withdraw_id))
			->find();
		});
		
	}


	/**
     * 查一条记录，根据条件
     * @param  array  $call_where 查询条件
     * @return array
     */
    public function find_where($call_where = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where), function ($call_where) {
            return db(parent::DB_APPLICATION_ID)
                ->table('user_withdraw')
                ->call('where', $call_where)
                ->find();
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
			->table('user_withdraw')
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
			
		});
		
	}	
	
	
	/**
     * 查询分页数据
     *
     * @param  array $config 查询配置
     * @return array
     */
    public function select_page($config = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $call_where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
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


			 //用户数据
            $user = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = uw.user_id'
            );
			
            //查询总条数
            $counts = db(parent::DB_APPLICATION_ID)
            ->table('user_withdraw uw')
			->joinon($user)
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
				$user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
                $select = array(
                    'u.user_parent_id',
                    'u.user_nickname',
                    'u.user_logo_image_id',
                    '('.$user_phone_verify_list_sql.') as user_phone_verify_list',
                    'u.user_json',
                    'uw.*',
                );
            }
			
            //查询数据
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('user_withdraw uw')
			->joinon($user)
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);

            return $data;
        });
    }
	
	
	
	
	
	/**
     * 查询分页数据
     *
     * @param  array $config 查询配置
     * @return array
     */
    public function select_page_join_agent_user($config = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $call_where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
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


			 //用户数据
            $user = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = uw.user_id'
            );
            
            $agent = array(
                'table' => 'agent_user au',
                'type' => 'left',
                'on' => 'au.user_id = uw.user_id'
            );

            //查询总条数
            $counts = db(parent::DB_APPLICATION_ID)
                ->table('user_withdraw uw')
				->joinon($user)
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

			
			// if( empty($select) ){
			// 	$user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
            //     $select = array(
            //         'u.user_parent_id',
            //         'u.user_nickname',
            //         'u.user_logo_image_id',
            //         '('.$user_phone_verify_list_sql.') as user_phone_verify_list',
            //         'uw.*',
            //         'm.user_logo_image_id',
            //         'm.user_name',
            //     );
            // }
			
            //查询数据
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('user_withdraw uw')
			->joinon($user,$agent)
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);

            return $data;
        });
    }
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * 提现成功
	 * $data = array(
	 * 	"user_withdraw_id"		用户提现ID
	 * 	"user_withdraw_admin"	管理员ID			
	 * 	"user_withdraw_method"	提现方式							
	 * 	"user_id"					提交人ID
	 * 	"comment"					备注信息
	 * 	"withdraw_value"			提现费用
	 *  "withdraw_rmb"				提现人民币
	 * 	"pay_config"				支付配置
	 *  "user_money"			商家钱包 旧数据
	 *  "order_json"				订单JSON   ["bankcard"]		用户银行卡配置
	 * )
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function pass_user_money( $data = array() ){
		$lock_ids = array();
			
		//事务处理
		$user_withdraw_lock_id = object(parent::TABLE_LOCK)->start("user_withdraw_id", $data["user_withdraw_id"], parent::LOCK_STATE);
        if( empty($user_withdraw_lock_id) ){
            return false;//事务开启失败
        }
        $lock_ids[] = $user_withdraw_lock_id;
		
		$user_lock_id = object(parent::TABLE_LOCK)->start("user_id", $data["user_id"], parent::LOCK_MONEY);
        if( empty($user_lock_id) ){
        	object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;//事务开启失败
        }
		$lock_ids[] = $user_lock_id;
		
		db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
		
		//减少
        $user_money_id = object(parent::TABLE_USER_MONEY)->insert_minus(array(
            "user_id" => $data["user_id"],
            "user_money_join_id" => $data["user_money"]['user_money_id'],
            "user_money_value" => $data["user_money"]['user_money_value'] - $data["withdraw_value"],
            "user_money_minus" => $data["withdraw_value"],
            "user_money_type" => parent::TRANSACTION_TYPE_WITHDRAW
        ));
		//减少失败
        if( empty($user_money_id) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
		if( empty($data["comment"]) ){
			$data["comment"] = "用户钱包提现";
		}
		
		if( !empty($data["order_json"]) ){
            $data["order_json"] = cmd(array($data["order_json"]), "json encode");
        }else{
            $data["order_json"] = "";
        }
		
		//插入订单
        $order_insert = array(
            "order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
            "order_type" => parent::TRANSACTION_TYPE_WITHDRAW,//提现
			"order_comment" => $data["comment"],
			"order_plus_method" => $data['user_withdraw_method'],//提现方式
            "order_plus_account_id" => $data["user_id"],//用户提现，则是用户ID
            "order_plus_value" => $data["withdraw_rmb"],
            "order_plus_transaction_id" => "",//交易号
            "order_plus_update_time" => time(),
            
            "order_action_user_id" => $data['user_id'],
            "order_minus_method" => "user_money",
            "order_minus_account_id" => $data["user_id"],
            "order_minus_value" => $data["withdraw_value"],
            "order_minus_transaction_id" => $user_money_id,
            "order_minus_update_time" => time(),
            
            "order_sign" => $data["user_withdraw_id"],
            "order_state" => 1,//确定订单
            "order_pay_state" => 1,//已支付
            "order_pay_time" => time(),
            "order_insert_time" => time(),
            "order_json" => $data["order_json"]
        );
        
        if( !object(parent::TABLE_ORDER)->insert($order_insert) ){
        	db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
        	object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
		
		//修改状态
		$uw_update_data = array();
		$uw_update_data['user_withdraw_state'] = 1;
        $uw_update_data['user_withdraw_pass_time'] = time();
        $uw_update_data['user_withdraw_admin'] = $data['user_withdraw_admin'];
        $uw_update_data['order_id'] = $order_insert['order_id'];
		
		$uw_update_where = array();
		$uw_update_where[] = array('user_withdraw_id=[+]', $data['user_withdraw_id']);
		$uw_update_where[] = array('[and] user_withdraw_state=2');
        //更新数据，记录日志
        if( !object(parent::TABLE_USER_WITHDRAW)->update($uw_update_where, $uw_update_data) ){
        	db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
		//订单修改
		$order_where = array();
		$order_where[] = array("order_id=[+]", $order_insert['order_id']);
		$order_update = array(
			"order_plus_update_time" => time(),
		);
		
		//微信支付
		if( $data['user_withdraw_method'] == parent::PAY_METHOD_WEIXINPAY ){
			//$data["pay_config"]["partner_trade_no"] = $order_insert['order_id'];
			$data["pay_config"]["partner_trade_no"] = $data["user_withdraw_id"];//防止错误重复提现
			$weixin_pay = object(parent::PLUGIN_WEIXIN_PAY_MCHPAY)->submit($data["pay_config"]);
			if( !empty($weixin_pay['errno']) ){
				db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
        		object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
				return $weixin_pay;
			}
			
			$order_update['order_plus_transaction_id'] = $weixin_pay["data"]["transaction_id"];//获取收款的交易号
		}else
		if( $data['user_withdraw_method'] == parent::PAY_METHOD_ALIPAY ){
			//$data["pay_config"]["out_biz_no"] = $order_insert['order_id'];
			$data["pay_config"]["out_biz_no"] = $data["user_withdraw_id"];//防止错误重复提现
			$alipay_pay = object(parent::PLUGIN_ALIPAY)->transfer($data["pay_config"]);
			if( !empty($alipay_pay['errno']) ){
				db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
        		object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
				return $alipay_pay;
			}
			
			$order_update['order_plus_transaction_id'] = $alipay_pay["data"]["transaction_id"];//获取收款的交易号
		}else
		if( $data['user_withdraw_method'] == parent::PAY_METHOD_BANKCARD ){
			//银行卡 提现
			//不做任何操作
			//记录银行卡号
			if( !empty($data["order_json"]) ){
	            $data["order_json"] = cmd(array($data["order_json"]), "json decode");
	        }
			if( !empty($data["order_json"]['bankcard']['account']) && 
			(is_string($data["order_json"]['bankcard']['account']) || is_numeric($data["order_json"]['bankcard']['account'])) ){
				$order_update['order_plus_transaction_id'] = $data["order_json"]['bankcard']['account'];
			}
			
		}else{
			db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
        	object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
			return false;
		}
		
		//不管修改成功与否，都要提交事务
		db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
		
        $bool = object(parent::TABLE_ORDER)->update($order_where, $order_update);
		if( !empty($bool) ){
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
            return $order_insert['order_id'];
        }else{
        	object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
	}
	
	
	
	
	
	
	
	
	
	
	
}
?>