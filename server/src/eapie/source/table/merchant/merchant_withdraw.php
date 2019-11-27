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



namespace eapie\source\table\merchant;
use eapie\main;
class merchant_withdraw extends main {
	
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, "order", "merchant_money");
	
	
	
	/**
     * @var [arr] [数据检测]
     */
    public $check = array(
        'merchant_withdraw_id' => array(
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
		"merchant_withdraw_type" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少提现类型参数"),
                    'echo'=>array("提现类型的数据类型不合法"),
                    '!null'=>array("提现类型不能为空"),
                    'method'=>array(array(parent::TABLE_MERCHANT_WITHDRAW, 'check_type'), "提现类型输入有误，不能被识别")       
                ),
        ),
		"merchant_withdraw_method" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少提现方式参数"),
                    'echo'=>array("提现方式的数据类型不合法"),
                    '!null'=>array("提现方式不能为空"),
                    'method'=>array(array(parent::TABLE_MERCHANT_WITHDRAW, 'check_method'), "提现方式输入有误，不能被识别")       
                ),
        ),
        "merchant_withdraw_comment" => array(
            //参数检测
            'args'=>array(
                    'echo'=>array("备注信息的数据类型不合法"),
                    ),
        ),
		"merchant_withdraw_fail_info" => array(
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
            "merchant_money"=>"商家钱包",
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
    public function get_unique_id(){
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
		->table('merchant_withdraw')
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
		->table('merchant_withdraw')
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
		->table('merchant_withdraw')
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
	 * @param	array	$merchant_withdraw_id
	 * @return	array
	 */
	public function remove($merchant_withdraw_id = ''){
		if( empty($merchant_withdraw_id) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('merchant_withdraw')
		->where(array('merchant_withdraw_id=[+]', (string)$merchant_withdraw_id))
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
	 * @param	array	$merchant_withdraw_id
	 * @return	array
	 */
	public function find($merchant_withdraw_id = ''){
		if( empty($merchant_withdraw_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($merchant_withdraw_id), function($merchant_withdraw_id){
			return db(parent::DB_APPLICATION_ID)
			->table('merchant_withdraw')
			->where(array('merchant_withdraw_id=[+]', (string)$merchant_withdraw_id))
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
                ->table('merchant_withdraw')
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
			->table('merchant_withdraw')
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
                'on' => 'u.user_id = mw.user_id'
            );
            
			
			//商家数据
			$merchant = array(
                'table' => 'merchant m',
                'type' => 'left',
                'on' => 'm.merchant_id = mw.merchant_id'
            );
			

            //查询总条数
            $counts = db(parent::DB_APPLICATION_ID)
                ->table('merchant_withdraw mw')
				->joinon($user, $merchant)
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
                    'mw.*',
                    'm.merchant_logo_image_id',
                    'm.merchant_name',
                );
            }
			
            //查询数据
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('merchant_withdraw mw')
			->joinon($user, $merchant)
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
	 * 	"merchant_withdraw_id"		商家提现ID
	 * 	"merchant_withdraw_admin"	管理员ID			
	 * 	"merchant_withdraw_method"	提现方式							
	 * 	"user_id"					提交人ID
	 * 	"merchant_id"				商家ID
	 * 	"comment"					备注信息
	 * 	"withdraw_value"			提现费用
	 *  "withdraw_rmb"				提现人民币
	 * 	"pay_config"				支付配置
	 *  "merchant_money"			商家钱包 旧数据
	 *  "order_json"				订单JSON
	 * )
	 * 
	 * @param	array	$data
	 * @return	bool
	 */
	public function pass_merchant_money( $data = array() ){
		$lock_ids = array();
			
		//事务处理
		$merchant_withdraw_lock_id = object(parent::TABLE_LOCK)->start("merchant_withdraw_id", $data["merchant_withdraw_id"], parent::LOCK_STATE);
        if( empty($merchant_withdraw_lock_id) ){
            return false;//事务开启失败
        }
        $lock_ids[] = $merchant_withdraw_lock_id;
		
		$merchant_lock_id = object(parent::TABLE_LOCK)->start("merchant_id", $data["merchant_id"], parent::LOCK_MONEY);
        if( empty($merchant_lock_id) ){
        	object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;//事务开启失败
        }
		$lock_ids[] = $merchant_lock_id;
		
		db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
		
		//减少
        $merchant_money_id = object(parent::TABLE_MERCHANT_MONEY)->insert_minus(array(
            "merchant_id" => $data["merchant_id"],
            "merchant_money_join_id" => $data["merchant_money"]['merchant_money_id'],
            "merchant_money_value" => $data["merchant_money"]['merchant_money_value'] - $data["withdraw_value"],
            "merchant_money_minus" => $data["withdraw_value"],
            "merchant_money_type" => parent::TRANSACTION_TYPE_WITHDRAW
        ));
		//减少失败
        if( empty($merchant_money_id) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
		if( empty($data["comment"]) ){
			$data["comment"] = "商家钱包提现";
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
			"order_plus_method" => $data['merchant_withdraw_method'],//提现方式
            "order_plus_account_id" => $data["merchant_id"],//商家提现，则是商家ID
            "order_plus_value" => $data["withdraw_rmb"],
            "order_plus_transaction_id" => "",//交易号
            "order_plus_update_time" => time(),
            
            "order_action_user_id" => $data['user_id'],
            "order_minus_method" => "merchant_money",
            "order_minus_account_id" => $data["merchant_id"],
            "order_minus_value" => $data["withdraw_value"],
            "order_minus_transaction_id" => $merchant_money_id,
            "order_minus_update_time" => time(),
            
            "order_sign" => $data["merchant_withdraw_id"],
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
		$mw_update_data = array();
		$mw_update_data['merchant_withdraw_state'] = 1;
        $mw_update_data['merchant_withdraw_pass_time'] = time();
        $mw_update_data['merchant_withdraw_admin'] = $data['merchant_withdraw_admin'];
        $mw_update_data['order_id'] = $order_insert['order_id'];
		
		$mw_update_where = array();
		$mw_update_where[] = array('merchant_withdraw_id=[+]', $data['merchant_withdraw_id']);
		$mw_update_where[] = array('[and] merchant_withdraw_state=2');
        //更新数据，记录日志
        if( !object(parent::TABLE_MERCHANT_WITHDRAW)->update($mw_update_where, $mw_update_data) ){
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
		if( $data['merchant_withdraw_method'] == parent::PAY_METHOD_WEIXINPAY ){
			//$data["pay_config"]["partner_trade_no"] = $order_insert['order_id'];
			$data["pay_config"]["partner_trade_no"] = $data["merchant_withdraw_id"];//防止错误重复提现
			$weixin_pay = object(parent::PLUGIN_WEIXIN_PAY_MCHPAY)->submit($data["pay_config"]);
			if( !empty($weixin_pay['errno']) ){
				db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
        		object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
				return $weixin_pay;
			}
			
			$order_update['order_plus_transaction_id'] = $weixin_pay["data"]["transaction_id"];//获取收款的交易号
		}else
		if( $data['merchant_withdraw_method'] == parent::PAY_METHOD_ALIPAY ){
			//$data["pay_config"]["out_biz_no"] = $order_insert['order_id'];
			$data["pay_config"]["out_biz_no"] = $data["merchant_withdraw_id"];//防止错误重复提现
			$alipay_pay = object(parent::PLUGIN_ALIPAY)->transfer($data["pay_config"]);
			if( !empty($alipay_pay['errno']) ){
				db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
        		object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
				return $alipay_pay;
			}
			
			$order_update['order_plus_transaction_id'] = $alipay_pay["data"]["transaction_id"];//获取收款的交易号
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