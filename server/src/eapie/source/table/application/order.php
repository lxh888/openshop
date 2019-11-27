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



namespace eapie\source\table\application;
use eapie\main;
class order extends main {
    
    
    
    /* 用户订单 */
    
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(
		__CLASS__, 
		"merchant_credit",
		"merchant_money",
		"user_credit", 
		"user_money_share", 
		"user_money_annuity",
		"user_money_earning",
		"user_money_help",
		"user_money_service",
		"user_money"
	);
	

	
	
    
    
    /**
     * 数据检测
     * 
     * @var array
     */
    public $check = array(
        "order_id" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少订单ID参数"),
                    'echo'=>array("订单ID的数据类型不合法"),
                    '!null'=>array("订单ID不能为空"),
                ),
        ),
        
        "credit_number" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少积分数量参数"),
                    'echo'=>array("积分数量的数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/', "积分数量的必须是整数"),
                    ),
        ),
		
		'order_action_user_id' => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少操作人的用户ID参数"),
                    'echo'=>array("操作人的用户ID数据类型不合法"),
                    '!null'=>array("操作人的用户ID不能为空"),
                    ),
            //检查编号是否存在      
            'exists'=>array(
                    'method'=>array(array(parent::TABLE_USER, 'find_exists_id'), "操作人的用户ID有误，操作人不存在") 
            ),
            
		),
		
        "money_fen" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少金额参数"),
					'echo'=>array("金额的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/iu', "金额必须是整数"),
					),
		),
		
        "comment" => array(
            //参数检测
            'args'=>array(
                    'echo'=>array("备注信息的数据类型不合法"),
                    ),
        ),
        
		
		"type" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少交易类型参数"),
                    'echo'=>array("交易类型的数据类型不合法"),
                    '!null'=>array("交易类型不能为空"),
                    'method'=>array(array(parent::TABLE_ORDER, 'check_type'), "交易类型输入有误，不能被识别")       
                ),
        ),
		
		
        "pay_method" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少支付方式参数"),
                    'echo'=>array("支付方式的数据类型不合法"),
                    '!null'=>array("支付方式不能为空"),
                    'method'=>array(array(parent::TABLE_ORDER, 'check_method'), "支付方式输入有误，不能被识别")       
                ),
        ),
        
        "withdraw_method" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少提现方式参数"),
                    'echo'=>array("提现方式的数据类型不合法"),
                    '!null'=>array("提现方式不能为空"),
                    'method'=>array(array(parent::TABLE_ORDER, 'check_method'), "提现方式输入有误，不能被识别")       
                ),
        ),
		
        "weixin_login_code" => array(
            'args'=>array(
                    'exist'=>array("缺少微信临时登录凭证code的参数"),
                    'echo'=>array("微信临时登录凭证code的数据类型不合法"),
                    '!null'=>array("微信临时登录凭证code不能为空"),
            )
        ),
        
		 "weixin_login_openid" => array(
            'args'=>array(
                    'exist'=>array("缺少微信登录用户凭证openid的参数"),
                    'echo'=>array("微信登录用户凭证openid的数据类型不合法"),
                    '!null'=>array("微信登录用户凭证openid不能为空"),
            )
        ),
		
        "weixin_trade_type" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少微信支付交易类型参数"),
                    'echo'=>array("微信支付交易类型的数据类型不合法"),
                    '!null'=>array("微信支付交易类型不能为空"),
                    //JSAPI--JSAPI支付（或小程序支付）、NATIVE--Native支付、APP--app支付，MWEB--H5支付，不同trade_type决定了调起支付的方式
                    'match'=>array('/^(MPJSAPI|JSAPI|APP|MWEB|NATIVE)$/', "微信支付交易类型输入不正确"),
                ),
        ),
        
        "alipay_trade_type" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少支付宝交易类型参数"),
                    'echo'=>array("支付宝交易类型的数据类型不合法"),
                    '!null'=>array("支付宝交易类型不能为空"),
                    //APP--APP支付、WAP--手机网站支付
                    'match'=>array('/^(WAP|APP)$/', "支付宝交易类型输入不正确"),
                ),
        ),
		
		
		
		"alipay_account" => array(
            'args'=>array(
                    'exist'=>array("缺少支付宝账号的参数"),
                    'echo'=>array("支付宝账号的数据类型不合法"),
                    '!null'=>array("支付宝账号不能为空"),
            )
        ),
        
		"alipay_realname" => array(
            'args'=>array(
                    'exist'=>array("缺少支付宝账号真实姓名的参数"),
                    'echo'=>array("支付宝账号真实姓名的数据类型不合法"),
                    '!null'=>array("支付宝账号真实姓名不能为空"),
            )
        ),
		
		
        "config_credit" => array(
            //参数检测
            'args'=>array(
                    'echo'=>array("人名币购买积分配置不合法"),
                    ),
        ),
    
    );
    
    
    
    
    
    
    /**
     * 获取交易类型
     * 交易类型。充值、转账、红包、购货、退款，还有管理员后台的操作：人工收入、人工支出
     * 
     * @param   void
     * @return  array
     */
    public function get_type(){
        return array(
            parent::TRANSACTION_TYPE_ADMIN_PLUS =>"人工收入",
            parent::TRANSACTION_TYPE_ADMIN_MINUS =>"人工支出",
            parent::TRANSACTION_TYPE_RECHARGE =>"充值",//第三方平台支付的，称之为充值
            parent::TRANSACTION_TYPE_TRANSFER =>"转账",//自家平台的支付，称之为转账
            parent::TRANSACTION_TYPE_CONSUME =>"消费",
            parent::TRANSACTION_TYPE_RECOMMEND_CREDIT =>"推荐积分奖励",
            parent::TRANSACTION_TYPE_RECOMMEND_MONEY =>"推荐钱包奖励",
            parent::TRANSACTION_TYPE_AWARD_MONEY =>"钱包奖励",
            parent::TRANSACTION_TYPE_HOUSE_PRODUCT_AWARD_MONEY =>"楼盘产品发布钱包奖励",
            parent::TRANSACTION_TYPE_REFUND =>"退款",
            parent::TRANSACTION_TYPE_WITHDRAW => "提现",
            parent::TRANSACTION_TYPE_CONVERSION =>"兑换",//用户手动转换
            parent::TRANSACTION_TYPE_SYSTEM_CONVERSION =>"系统兑换",
            parent::TRANSACTION_TYPE_DAILY_ATTENDANCE =>"每日签到",
            parent::TRANSACTION_TYPE_SHOP_ORDER =>"商城购物",
            parent::TRANSACTION_TYPE_SHOP_ORDER_GROUP =>"拼团订单",
            parent::TRANSACTION_TYPE_SHOP_ORDER_GROUP_REFUND =>"拼团订单退款",
            
            parent::TRANSACTION_TYPE_HOUSE_TOP_ORDER =>"楼盘置顶订单",
            
            parent::TRANSACTION_TYPE_AWARD_MONEY =>"钱包奖励",
            parent::TRANSACTION_TYPE_HOUSE_PRODUCT_AWARD_MONEY =>"楼盘产品发布钱包奖励",
            parent::TRANSACTION_TYPE_EXPRESS_ORDER =>"快递订单",
            parent::TRANSACTION_TYPE_RECOMMEND_SALES_REWARD => "推荐分销奖励",
            parent::TRANSACTION_TYPE_AGENT_CREDIT_REWARD =>   '代理积分奖励',
            parent::TRANSACTION_TYPE_PAY_AWARD_CREDIT =>   '消费返利（返积分）',
            parent::TRANSACTION_TYPE_INVITE_ROYALTY_REWARD=>    '分销奖励',
            parent::TRANSACTION_TYPE_USER_CREDIT_YITAO_INVITE=>     '邀请积分奖励（爱尚购）',

			parent::TRANSACTION_TYPE_SHOP_ORDER_AGENT_MONEY_DIVIDEND=>  '商城订单业绩代理人分红',
        );
    }
    
	
    
    /**
     * 获取支付方式
     * 
     * @param   void
     * @return  array
     */
    public function get_method(){
        return array(
            parent::PAY_METHOD_WEIXINPAY => "微信支付",
            parent::PAY_METHOD_ALIPAY => "支付宝支付",
            parent::PAY_METHOD_MERCHANT_CREDIT => "商家积分",
            parent::PAY_METHOD_MERCHANT_MONEY => '商家钱包',
            parent::PAY_METHOD_USER_CREDIT => "用户积分",
            parent::PAY_METHOD_USER_MONEY => "用户钱包",
            parent::PAY_METHOD_USER_MONEY_ANNUITY => '养老资金',
            parent::PAY_METHOD_USER_MONEY_EARNING => '赠送收益',
            parent::PAY_METHOD_USER_MONEY_HELP => '扶贫基金',
            parent::PAY_METHOD_USER_MONEY_SERVICE => '服务费',
            parent::PAY_METHOD_USER_MONEY_SHARE => '消费共享金',
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
     * 根据订单ID，判断订单是否存在
     * 
     * @param   string      $order_id
     */
    public function find_exists_id($order_id){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($order_id), function($order_id){
            return (bool)db(parent::DB_APPLICATION_ID)
            ->table('order')
            ->where(array('order_id=[+]', $order_id))
            ->find('order_id');
        });
    }
    
    
    /**
     * 查一条记录，根据条件
	 * 
     * @param  array  $call_where 查询条件
     * @return array
     */
    public function find_where($call_where = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where), function($call_where) {
            return db(parent::DB_APPLICATION_ID)
            ->table('order')
            ->call('where', $call_where)
            ->find();
        });
    }
    
    
        
    /**
     * 获取一个数据
     * 
     * @param   string  $order_id
     * @return  array
     */
    public function find($order_id = ''){
        if( empty($order_id) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($order_id), function($order_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('order')
            ->where(array('order_id=[+]', (string)$order_id))
            ->find();
        });
    }


    /**
     * 获取一个数据，不带缓存，用于微信支付的回调
     * 
     * @param   string  $order_id
     * @return  array
     */
    public function find_unbuffered($order_id = ''){
        if( empty($order_id) ){
            return false;
        }
        return db(parent::DB_APPLICATION_ID)
        ->table('order')
        ->where(array('order_id=[+]', (string)$order_id))
        ->find();
    }


    /**
     * 获取一条数据，根据条件，不带缓存
     * 
     * @param   array $call_where
     * @return  array
     */
    public function find_where_unbuffered($call_where = array())
    {
        return db(parent::DB_APPLICATION_ID)
            ->table('order')
            ->call('where', $call_where)
            ->find();
    }


	
	    
    /**
     * [创联众益]回调处理，交易成功
	 * 
     * @param   string      $data["order_id"]
     * @param   string      $data["order_minus_transaction_id"]     付款方的支付交易号 如 
	 * @param   string      $data["standby_order_id"]				备用的order_id 安全验证的随机码
	 * @param	string		$data["pay_method"]						支付方式
	 * @param	string		$data["pay_notify_data"]				回调数据
     * @return  bool
     */
    public function clzy_notify_trade_success($data = array()){
        if( empty($data["order_id"]) || 
        empty($data["order_minus_transaction_id"]) || 
        empty($data["standby_order_id"]) || 
        empty($data["pay_method"]) ||
		empty($data['pay_notify_data']) ){
            return false;
        }
        
		//file_put_contents(CACHE_PATH."/clzy_notify_trade_success", cmd(array($data), "json encode"));
		//获取订单  
		$order_data = $this->find_unbuffered( $data["order_id"] );
        if( empty($order_data) ){
            //订单数据不存在  使用备用订单ID
            $order_data = $this->find_unbuffered( $data["standby_order_id"] );
			if( empty($order_data) ){
	            return false;//订单数据不存在
	        }
        }
		//file_put_contents(CACHE_PATH."/clzy_notify_trade_success.order_data", cmd(array($order_data), "json encode"));
		//file_put_contents(CACHE_PATH."/获取商城订单调试", cmd(array($order_data), "json encode"));
		$lock_ids = array();
        //事务处理，开始锁商家的订单数据
        $order_lock_id = object(parent::TABLE_LOCK)->start("order_id", $data["order_id"], parent::LOCK_PAY_STATE);
        if( empty($order_lock_id) ){
            return false;//事务开启失败
        }
        $lock_ids[] = $order_lock_id;
        
        
		if( !empty($order_data["order_json"]) ){
			$order_data["order_json"] = cmd(array($order_data["order_json"]), "json decode");
		}else{
			$order_data["order_json"] = array();
		}
		
		
		//检查备用订单ID的合法性
		if( $data["pay_method"] == parent::PAY_METHOD_WEIXINPAY){
			if( empty($order_data["order_json"]["weixin_pay_nonce_str"]) ||
			$order_data["order_json"]["weixin_pay_nonce_str"] !== $data["standby_order_id"] ){
				object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
				//file_put_contents(CACHE_PATH."/clzy_notify_trade_success.weixin_pay_nonce_str验证不合法", cmd(array($order_data), "json encode"));
	            return false;//验证不合法
			}
		}else
		if( $data["pay_method"] == parent::PAY_METHOD_ALIPAY){
			if( empty($order_data["order_json"]["alipay_passback_params"]) ||
			$order_data["order_json"]["alipay_passback_params"] !== $data["standby_order_id"] ){
				object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
				//file_put_contents(CACHE_PATH."/clzy_notify_trade_success.alipay_passback_params验证不合法", cmd(array($order_data), "json encode"));
	            return false;//验证不合法
			}
		}else{
			object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
			return false;
		}
		
		//file_put_contents(CACHE_PATH."/clzy_notify_trade_success.验证通过", cmd(array(array($order_data, $data)), "json encode"));
		
        //已经支付：订单支付状态。0未支付，1已支付
        if( !empty($order_data["order_pay_state"]) ){
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return true;
        }
        
		//更新回调数据
		if( !empty($data['pay_notify_data']) ){
			$order_data["order_json"]["pay_notify_data"] = $data['pay_notify_data'];
			$this->update_json($data["order_id"], $order_data["order_json"]);
		}
		
		$order_data['pay_method'] = $data['pay_method'];
        $order_data['order_minus_transaction_id'] = $data['order_minus_transaction_id'];
		
		//商城购物
        if( $order_data['order_type'] === parent::TRANSACTION_TYPE_SHOP_ORDER ){
        	//file_put_contents(CACHE_PATH."/商城购物-回调进入", cmd(array($order_data), "json encode"));
			$bool = object(parent::TABLE_SHOP_ORDER)->clzy_payment($order_data);
        }else
        //积分充值
        if( $order_data["order_plus_method"] == parent::PAY_METHOD_MERCHANT_CREDIT ){
        	$bool = object(parent::TABLE_MERCHANT_CREDIT)->buy_notify_trade_success($data["order_minus_transaction_id"], $order_data, $lock_ids);	
        }else
		//商家余额
		if( $order_data["order_plus_method"] == parent::PAY_METHOD_MERCHANT_MONEY ){
			$bool = object(parent::TABLE_MERCHANT_MONEY)->plus_notify_trade_success($data["order_minus_transaction_id"], $order_data, $lock_ids);	
		}else
		//用户余额充值
		if( $order_data["order_plus_method"] == parent::PAY_METHOD_USER_MONEY ){
			$bool = object(parent::TABLE_USER_MONEY)->plus_notify_trade_success($data["order_minus_transaction_id"], $order_data, $lock_ids);
		}
        
		
		object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
			return true;
        }else{
        	return false;
        }
        
	}

	
	
	
    
    /**
     * 回调处理，交易成功
     * 
     * @param   string      $data["order_id"]
     * @param   string      $data["order_minus_transaction_id"]     付款方的支付交易号 如 
	 * @param   string      $data["standby_order_id"]				备用的order_id 安全验证的随机码
	 * @param	string		$data["pay_method"]						支付方式
	 * @param	string		$data["pay_notify_data"]				回调数据
     * @return  bool
     */
    public function update_notify_trade_success($data = array()){
        if( empty($data["order_id"]) || 
        empty($data["order_minus_transaction_id"]) || 
        empty($data["standby_order_id"]) || 
        empty($data["pay_method"]) ||
		empty($data['pay_notify_data']) ){
            return false;
        }
        
		
		//获取订单  
		$order_data = $this->find_unbuffered( $data["order_id"] );
        if( empty($order_data) ){
            //订单数据不存在  使用备用订单ID
            $order_data = $this->find_unbuffered( $data["standby_order_id"] );
			if( empty($order_data) ){
	            return false;//订单数据不存在
	        }
        }
		
		//file_put_contents(CACHE_PATH."/获取商城订单调试", cmd(array($order_data), "json encode"));
		$lock_ids = array();
        //事务处理，开始锁商家的订单数据
        $order_lock_id = object(parent::TABLE_LOCK)->start("order_id", $data["order_id"], parent::LOCK_PAY_STATE);
        if( empty($order_lock_id) ){
            return false;//事务开启失败
        }
        $lock_ids[] = $order_lock_id;
        
        //获取订单  
		/*$order_data = $this->find_unbuffered( $data["order_id"] );
        if( empty($order_data) ){
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;//订单数据不存在
        }*/
        
		if( !empty($order_data["order_json"]) ){
			$order_data["order_json"] = cmd(array($order_data["order_json"]), "json decode");
		}else{
			$order_data["order_json"] = array();
		}
		
		
		//检查备用订单ID的合法性
		if( $data["pay_method"] == parent::PAY_METHOD_WEIXINPAY){
			if( empty($order_data["order_json"]["weixin_pay_nonce_str"]) ||
			$order_data["order_json"]["weixin_pay_nonce_str"] !== $data["standby_order_id"] ){
				object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
	            return false;//验证不合法
			}
		}else
		if( $data["pay_method"] == parent::PAY_METHOD_ALIPAY){
			if( empty($order_data["order_json"]["alipay_passback_params"]) ||
			$order_data["order_json"]["alipay_passback_params"] !== $data["standby_order_id"] ){
				object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
	            return false;//验证不合法
			}
		}else{
			object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
			return false;
		}
		
        //已经支付：订单支付状态。0未支付，1已支付
        if( !empty($order_data["order_pay_state"]) ){
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return true;
        }
        
        
		//更新回调数据
		if( !empty($data['pay_notify_data']) ){
			$order_data["order_json"]["pay_notify_data"] = $data['pay_notify_data'];
			$this->update_json($data["order_id"], $order_data["order_json"]);
		}
		
		
		$order_data['pay_method'] = $data['pay_method'];
        $order_data['order_minus_transaction_id'] = $data['order_minus_transaction_id'];
		
		
        
        // 判断支付对象
        if ($order_data['order_type'] === parent::TRANSACTION_TYPE_SHOP_ORDER) {
            // 商城订单
			$bool = object(parent::TABLE_SHOP_ORDER)->payment($order_data);
        } elseif ($order_data['order_type'] === parent::TRANSACTION_TYPE_SHOP_ORDER_GROUP) {
            // 商城拼团订单
            $bool = object(parent::TABLE_SHOP_ORDER_GROUP)->payment($order_data);
        }else
        //楼盘置顶
        if ($order_data['order_type'] === parent::TRANSACTION_TYPE_HOUSE_TOP_ORDER) {
            $bool = object(parent::TABLE_HOUSE_TOP_ORDER)->pay_by_third_party($order_data);
        }else
		//江油快递
		if ($order_data['order_type'] === parent::TRANSACTION_TYPE_EXPRESS_ORDER) {
			$bool = object(parent::TABLE_EXPRESS_ORDER)->pay_by_third_party($order_data);
		}else
		//商城拼团
		if($order_data['order_type'] === parent::TRANSACTION_TYPE_SHOP_GROUP_ORDER) {
			$bool = object(parent::TABLE_SHOP_GROUP_ORDER)->pay_by_third_party($order_data);
		}else
        //积分充值
        if( $order_data["order_plus_method"] == parent::PAY_METHOD_MERCHANT_CREDIT ){
        	$bool = object(parent::TABLE_MERCHANT_CREDIT)->buy_notify_trade_success($data["order_minus_transaction_id"], $order_data, $lock_ids);	
        }else
		//商家余额
		if( $order_data["order_plus_method"] == parent::PAY_METHOD_MERCHANT_MONEY ){
			$bool = object(parent::TABLE_MERCHANT_MONEY)->plus_notify_trade_success($data["order_minus_transaction_id"], $order_data, $lock_ids);	
		}else
		//用户余额充值
		if( $order_data["order_plus_method"] == parent::PAY_METHOD_USER_MONEY ){
			$bool = object(parent::TABLE_USER_MONEY)->plus_notify_trade_success($data["order_minus_transaction_id"], $order_data, $lock_ids);
		}
		
		object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
			
        }else{
        	return false;
        }
        
	}

	/**
	 * 查询 order_id 对应的 order_sign
	 * @param string $order_id
	 * @return string $result['order_sign']   =>  shop_order_id
	 */
	public function find_order_sign($order_id){
		$result = db(parent::DB_APPLICATION_ID)
			->table('order')
			->where(array('order_id=[+]', (string)$order_id))
			->find();

		return $result['order_sign'];
	}
    
    
    

	
    /**
     * 用户钱包提现
	 * 
     * @param  array $data [description]
     * @return string       [订单ID]
     */
    public function insert_user_money_withdraw($data)
    {
        $lock_ids = array();
  
        //事务处理
        $user_money_earning_lock_id = object(parent::TABLE_LOCK)->start('user_id', $data['user_id'], parent::LOCK_MONEY_EARNING);
        if( empty($user_money_earning_lock_id) ){
            return false;//事务开启失败
        }
        $lock_ids[] = $user_money_earning_lock_id;
        $user_money_service_lock_id = object(parent::TABLE_LOCK)->start('user_id', $data['user_id'], parent::LOCK_MONEY_SERVICE);
        if( empty($user_money_service_lock_id) ){
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;//事务开启失败
        }
        $lock_ids[] = $user_money_service_lock_id;

        db(parent::DB_APPLICATION_ID)->query('START TRANSACTION');//开始事务


        //减少用户钱包
        $user_money_id = object(parent::TABLE_USER_MONEY)->insert_minus(array(
            'user_id' => $data['user_id'],
            'user_money_join_id' => $data['user_money']['user_money_id'],
            'user_money_value' => $data['user_money']['user_money_value'] - $data['withdraw_money_fen'],
            'user_money_minus' => $data['withdraw_money_fen'],
            'user_money_type' => parent::TRANSACTION_TYPE_WITHDRAW
        ));
        //减少失败
        if (empty($user_money_id)) {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }

        //插入订单
        $order_insert = array(
            "order_id" => $this->get_unique_id(),
            "order_type" => parent::TRANSACTION_TYPE_WITHDRAW,//提现
            "order_comment" => $data["comment"],
            "order_action_user_id" => $data['user_id'],
            "order_plus_method" => $data['withdraw_method'],//提现方式
            "order_plus_account_id" => $data["user_id"],
            "order_plus_value" => $data["withdraw_money_fen"],
            "order_plus_transaction_id" => "",//交易号
            "order_plus_update_time" => time(),
            "order_minus_method" => "user_money_earning",
            "order_minus_account_id" => $data['user_id'],
            "order_minus_value" => $data["withdraw_money_fen"],
            "order_minus_transaction_id" => $user_money_id,
            "order_minus_update_time" => time(),
            "order_state" => 1,//确定订单
            "order_pay_state" => 1,//已支付
            "order_pay_time" => time(),
            "order_insert_time" => time(),
            "order_json" => cmd(array($data['order_json']), 'json encode')
        );

        if (!$this->insert($order_insert)) {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }

        //订单修改
        $order_where = array();
        $order_where[] = array('order_id=[+]', $order_insert['order_id']);
        $order_update = array(
            'order_plus_update_time' => time(),
        );

        if( $data['withdraw_method'] == parent::PAY_METHOD_WEIXINPAY ){
            //微信提现
            $data['pay_config']['partner_trade_no'] = $order_insert['order_id'];
            $weixin_pay = object(parent::PLUGIN_WEIXIN_PAY_MCHPAY)->submit($data['pay_config']);
            if (!empty($weixin_pay['errno'])) {
                db(parent::DB_APPLICATION_ID)->query('ROLLBACK');//回滚
                object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
                return $weixin_pay;
            }

            $order_update['order_plus_transaction_id'] = $weixin_pay['data']['transaction_id'];//获取收款的交易号
        } elseif ( $data['withdraw_method'] == parent::PAY_METHOD_ALIPAY ){
            //支付宝提现
            $data['pay_config']['out_biz_no'] = $order_insert['order_id'];
            $alipay_pay = object(parent::PLUGIN_ALIPAY)->transfer($data['pay_config']);
            if (!empty($alipay_pay['errno'])) {
                db(parent::DB_APPLICATION_ID)->query('ROLLBACK');//回滚
                object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
                return $alipay_pay;
            }

            $order_update['order_plus_transaction_id'] = $alipay_pay["data"]["transaction_id"];//获取收款的交易号
        } else {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }

        //不管修改成功与否，都要提交事务
        db(parent::DB_APPLICATION_ID)->query('COMMIT');

        $bool = $this->update($order_where, $order_update);
        if (!$bool) {
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }

        object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
        //清理当前项目缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);

        return $order_insert['order_id'];
    }

	 
	
	/**
	 * 用户的赠送收益提现
	 * 
	 * $data = array(
	 * 	"user_id"						//用户ID、操作用户
	 * 	"comment"						//备注信息
	 * 	"user_money_service_money_fen"	//用户服务费
	 *  "withdraw_money_fen"			//用户提现费用
	 *  "withdraw_method"				//提现方式
	 *  "order_json"					//基础配置信息
	 *  "pay_config"					//支付配置
	 *  "user_money_earning"			//用户的赠送收益 旧数据
	 * )
	 * 
	 * @param	array		$data
	 * @return	bool | order_id
	 */
	public function insert_user_money_earning_withdraw($data){
		$lock_ids = array();
			
		//事务处理
		$user_money_earning_lock_id = object(parent::TABLE_LOCK)->start("user_id", $data["user_id"], parent::LOCK_MONEY_EARNING);
        if( empty($user_money_earning_lock_id) ){
            return false;//事务开启失败
        }
        $lock_ids[] = $user_money_earning_lock_id;
		/*$user_money_service_lock_id = object(parent::TABLE_LOCK)->start("user_id", $data["user_id"], parent::LOCK_MONEY_SERVICE);
        if( empty($user_money_service_lock_id) ){
        	object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;//事务开启失败
        }
		$lock_ids[] = $user_money_service_lock_id;*/
		
		
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
	            "user_money_earning_type" => parent::TRANSACTION_TYPE_WITHDRAW
	        ));
			//减少失败
	        if( empty($service_user_money_earning_id) ){
	            file_put_contents(CACHE_PATH."/test.用户赠送收益减少失败", cmd(array($data), "json encode"));   
				
	            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
	            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
	            return false;
	        }
			
			//增加用户服务费
	        $user_money_service_id = object(parent::TABLE_USER_MONEY_SERVICE)->insert_plus(array(
	            "user_id" => $data['user_id'],
	            "user_money_service_plus" => $data['user_money_service_money_fen'],
	            "user_money_service_type" => parent::TRANSACTION_TYPE_WITHDRAW
	        ));
	        
	        //用户服务费充值失败
	        if( empty($user_money_service_id) ){
	            file_put_contents(CACHE_PATH."/test.用户服务费收入失败", cmd(array($data), "json encode"));   
	            
	            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
	            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
	            return false;
	        }	
			
			$user_money_service_comment = " [".($data["order_json"]['rmb_withdraw_user_money_earning']['ratio_user_money_service']*100)."%] ";
			
			//生成订单
	        $service_order_insert = array(
	            "order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
	            "order_type" => parent::TRANSACTION_TYPE_WITHDRAW,//提现
	            "order_comment" => "赠送收益提现,收取服务费".$user_money_service_comment,
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
			
			
			//--------获取现金---------------
			$user_money_earning_value = $user_money_earning_value - $data["withdraw_money_fen"];
			
			//减少用户赠送收益
	        $user_money_earning_id = object(parent::TABLE_USER_MONEY_EARNING)->insert_minus_transaction(array(
	            "user_id" => $data["user_id"],
	            "user_money_earning_join_id" => $service_user_money_earning_id,
	            "user_money_earning_value" => $user_money_earning_value,
	            "user_money_earning_minus" => $data["withdraw_money_fen"],
	            "user_money_earning_type" => parent::TRANSACTION_TYPE_WITHDRAW,
	            "user_money_earning_time" => (time() + 1)
	        ));
			//减少失败
	        if( empty($user_money_earning_id["user_money_earning_id"]) ){
	            file_put_contents(CACHE_PATH."/test.用户赠送收益减少失败", cmd(array($data), "json encode"));   
				
	            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
	            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
	            return false;
	        }
			
			$user_money_earning_id = $user_money_earning_id["user_money_earning_id"];
			
		}else{
			
			//--------获取现金---------------
			$user_money_earning_value = $data["user_money_earning"]['user_money_earning_value'] - $data["withdraw_money_fen"];
			
			//减少用户赠送收益
	        $user_money_earning_id = object(parent::TABLE_USER_MONEY_EARNING)->insert_minus(array(
	            "user_id" => $data["user_id"],
	            "user_money_earning_join_id" => $data["user_money_earning"]['user_money_earning_id'],
	            "user_money_earning_value" => $user_money_earning_value,
	            "user_money_earning_minus" => $data["withdraw_money_fen"],
	            "user_money_earning_type" => parent::TRANSACTION_TYPE_WITHDRAW
	        ));
			//减少失败
	        if( empty($user_money_earning_id) ){
	            file_put_contents(CACHE_PATH."/test.用户赠送收益减少失败", cmd(array($data), "json encode"));   
				
	            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
	            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
	            return false;
	        }
			
		}

		if( empty($data["comment"]) ){
			$data["comment"] = "赠送收益提现";
		}
		
		//插入订单
        $order_insert = array(
            "order_id" => $this->get_unique_id(),
            "order_type" => parent::TRANSACTION_TYPE_WITHDRAW,//提现
			"order_comment" => $data["comment"],
			"order_plus_method" => $data['withdraw_method'],//提现方式
            "order_plus_account_id" => $data["user_id"],
            "order_plus_value" => $data["withdraw_money_fen"],
            "order_plus_transaction_id" => "",//交易号
            "order_plus_update_time" => time(),
            
            "order_action_user_id" => $data['user_id'],
            "order_minus_method" => "user_money_earning",
            "order_minus_account_id" => $data['user_id'],
            "order_minus_value" => $data["withdraw_money_fen"],
            "order_minus_transaction_id" => $user_money_earning_id,
            "order_minus_update_time" => time(),
            
            "order_state" => 1,//确定订单
            "order_pay_state" => 1,//已支付
            "order_pay_time" => time(),
            "order_insert_time" => time(),
            "order_json" => cmd(array($data["order_json"]), "json encode")
        );
        
        if( !$this->insert($order_insert) ){
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
		if( $data['withdraw_method'] == parent::PAY_METHOD_WEIXINPAY ){
			$data["pay_config"]["partner_trade_no"] = $order_insert['order_id'];
			$weixin_pay = object(parent::PLUGIN_WEIXIN_PAY_MCHPAY)->submit($data["pay_config"]);
			if( !empty($weixin_pay['errno']) ){
				db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
        		object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
				return $weixin_pay;
			}
			
			$order_update['order_plus_transaction_id'] = $weixin_pay["data"]["transaction_id"];//获取收款的交易号
		}else
		if( $data['withdraw_method'] == parent::PAY_METHOD_ALIPAY ){
			$data["pay_config"]["out_biz_no"] = $order_insert['order_id'];
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
		
        $bool = $this->update($order_where, $order_update);
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
	 * @param	string		$merchant_id				商家ID
	 * @param	string		$user_id					用户ID
	 * @param	int			$credit_number				积分个数
	 * @param	string		$order_id					订单ID
	 * @param   array       $config						配置
	 * @param   array       $order_json					订单配置信息
	 * 
	 * @return	如果返回真，有可能是不需要赠送奖励，或者赠送成功
	 */
//	public function parent_recommend_user_credit($merchant_id, $user_id, $credit_number, $order_id, &$config, &$order_json){
//		$config = object(parent::REQUEST_APPLICATION)->get_parent_recommend_user_credit($credit_number, array(), false);
//		if( empty($config['state']) || !empty($config['error']) ){
//			$order_json['parent_recommend_user_credit'] = $config;
//			return false;//有错误，或者没有开启则返回
//		}
//		
//		//创联众益项目奖励积分
//		$event_id = object(parent::TABLE_EVENT)->insert(array(
//			"event_name" => "parent_recommend_user_credit",
//			"event_stamp" => $order_id,
//			"event_json" => array(
//				//参数
//				"user_id" => $user_id,
//				"merchant_id" => $merchant_id,
//				"config" => $config,
//			)
//		));
//		
//		if( empty($event_id) ){
//			$config['event'] = "推荐人积分奖励的事件登记失败";
//			$order_json['parent_recommend_user_credit'] = $config;
//			return false;//有错误，或者没有开启则返回
//      }
//		
//		$start_event = object(parent::PROJECT_CLZY)->get_event_url("parent_recommend_user_credit", $event_id, true);
//		if( !empty($start_event['errno']) ){
//			$config['event'] = "推荐人积分奖励的事件登记成功,但触发异常：".$start_event['error'];
//			$order_json['parent_recommend_user_credit'] = $config;
//			return false;//有错误，或者没有开启则返回
//		}
//		
//		//向事件发送数据
//		/*$start_event = object(parent::PROJECT_CLZY)->get_event_url("parent_recommend_user_credit", $event_id);
//		if( !empty($start_event['errno']) ){
//			$config['event'] = "推荐人积分奖励的事件登记成功,但触发失败：".$start_event['error'];
//			$order_json['parent_recommend_user_credit'] = $config;
//			return false;//有错误，或者没有开启则返回
//		}*/
//		
//		/*$http_class_id = parent::PLUGIN_HTTP_CURL;
//		$http_url = $start_event['data'];
//		destruct('推荐人积分奖励的事件', function() use ($http_class_id, $http_url){
//			//object($http_class_id)->request_get( array("url" => $http_url, "timeout" => 30) );
//			file_get_contents($http_url);
//		});*/
//		
//		
//		//object(parent::PLUGIN_HTTP_CURL)->request_get(array("url"=>$start_event['data'], "timeout"=>2));
//		//$config['event'] = "推荐人积分奖励的事件登记成功，已触发链接：".$start_event['data'];
//		
//		$config['event'] = "推荐人积分奖励的事件登记成功，已触发事件ID：".$event_id;
//		$order_json['parent_recommend_user_credit'] = $config;
//		return true;
//		
//		//file_get_contents()
//	}
//	
	
	
	
	
	/**
	 * [推荐人] 可获得 [被推荐人(无论是普通用户还是其他商家用户)] 与 商家 交易时的所得积分的1%。
	 * 
	 * @param	string		$user_id			用户ID
	 * @param	array		$config				配置
	 * @param	array		$order_json			订单配置
	 * @param	array		$lock_ids			锁数组
	 * @return	bool
	 */
	/*private function _parent_recommend_user($user_id, &$config, &$order_json, &$lock_ids){
		if( $config['user_credit_plus'] < 1 ){
			$config['error_user_credit_plus'][] = "推荐人积分奖励数量小于1";
			$order_json['parent_recommend_user_credit'] = $config;
			return false;
		}
		
		$order_comment = '推荐人积分奖励，用户奖励比例['.($config['ratio_user_credit']*100).'%]';
		$this->_parent_recommend($user_id, $config['user_credit_plus'], $order_comment, "error_user_credit_plus", $config, $order_json, $lock_ids);
	}*/
	
	
	
	
	
	/**
	 * [推荐人] 可获得 [被推荐商家用户] 与 [其他普通用户或是其他商家用户] 交易时的送出积分的0.5%。
	 * 
	 * @param	string		$merchant_id		商家ID
	 * @param	string		$user_id			用户ID
	 * @param	array		$config				配置
	 * @param	array		$order_json			订单配置
	 * @param	array		$lock_ids			锁数组
	 * @return	bool
	 */
	/*private function _parent_recommend_merchant($merchant_id, $user_id, &$config, &$order_json, &$lock_ids){
		if( $config['merchant_user_credit_plus'] < 1 ){
			$config['error_merchant_user_credit_plus'][] = "商家用户的推荐人积分奖励数量小于1";
			$order_json['parent_recommend_user_credit'] = $config;
			return false;
		}

		$order_comment = '商家用户的推荐人积分奖励，用户奖励比例['.($config['ratio_merchant_user_credit']*100).'%]';
		
		//获取所有的商家用户的父级用户ID
		$user_ids = object(parent::TABLE_MERCHANT_USER)->select_all_user_id($merchant_id);
		if( !empty($user_ids) ){
			foreach($user_ids as $key => $value){
				$this->_parent_recommend($value['user_id'], $config['merchant_user_credit_plus'], $order_comment, "error_merchant_user_credit_plus", $config, $order_json, $lock_ids);
			}
		}
		
		
	}*/
	
	
	
	
	
	
        
    /**
     * 更新数据
     * 
     * @param   array       $where
     * @param   array       $data
     * @param   array       $call_data
     * @return  bool
     */
    public function update($where = array(), $data = array(), $call_data = array()){
        if( empty($where) || (empty($data) && empty($call_data)) ){
            return false;
        }
        
        $bool = (bool)db(parent::DB_APPLICATION_ID)
        ->table('order')
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
	 * 更新json数据
	 * 
	 * @param	string		$order_id						订单ID
	 * @param	array		$order_json						
	 * @return	bool
	 */
	public function update_json($order_id, $order_json = array()){
		//修改订单支付状态
        $where = array();
        $where[] = array("order_id=[+]", (string)$order_id);
        $data = array();
		if( !empty($order_json) ){
			$data['order_json'] = cmd(array($order_json), "json encode");
		}else{
			$data['order_json'] = "";
		}
		
        return (bool)db(parent::DB_APPLICATION_ID)
        ->table('order')
        ->call('where', $where)
        ->update($data);
	}
	
	
	/**
	 * 支付成功
	 * 
	 * @param	string		$order_id						订单ID
	 * @param	string		$order_plus_transaction_id		收款交易号
	 * @param	string		$order_minus_transaction_id		支付交易号
	 * @param	array		$order_json						
	 * @return	bool
	 */
	public function update_pay_success($order_id, $order_plus_transaction_id, $order_minus_transaction_id, $order_json = array()){
		//修改订单支付状态
        $where = array();
        $where[] = array("order_id=[+]", (string)$order_id);
        $where[] = array("[and] order_plus_transaction_id=''");
        $where[] = array("[and] order_state=1");//确定的订单才可以交易
        $where[] = array("[and] order_pay_state=0");
        $data = array(
            "order_plus_transaction_id" => (string)$order_plus_transaction_id,
            "order_minus_transaction_id" => (string)$order_minus_transaction_id,
            "order_pay_state" => 1,
            "order_pay_time" => time(),
        );
        
		if( !empty($order_json) ){
			$data['order_json'] = cmd(array($order_json), "json encode");
		}
		
        return (bool)db(parent::DB_APPLICATION_ID)
        ->table('order')
        ->call('where', $where)
        ->update($data);
	}
	
	
	
	
	
        
    
        
    /**
     * 插入新数据
     * 
     * @param   array       $data           数据
     * @param   array       $call_data      数据
     * @return  bool
     */
    public function insert($data = array(), $call_data = array()){
        if( empty($data) && empty($call_data) ){
            return false;
        }
        
        $bool = (bool)db(parent::DB_APPLICATION_ID)
        ->table('order')
        ->call('data', $call_data)
        ->insert($data);
        
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
        return $bool;
    }   
    
    
    
    
        
        
    /**
     * 删除数据
     * 
     * @param   array   $where
     * @return  array
     */
    public function delete($where = array()){
        if( empty($where) ){
            return false;
        }
        
        $bool = (bool)db(parent::DB_APPLICATION_ID)
        ->table('order')
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
     * @param   array   $order_id
     * @return  array
     */
    public function remove($order_id = ''){
        if( empty($order_id) ){
            return false;
        }
        
        $bool = (bool)db(parent::DB_APPLICATION_ID)
        ->table('order')
        ->where(array('order_id=[+]', (string)$order_id))
        ->delete();
        
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
        return $bool;
    }
    
        
    			
	/**
	 * 获取多条数据
	 *  $config = array(
	 * 	'where' => array(), //条件
	 * 	'orderby' => array(), //排序
	 * 	'limit'	=> array(0, page_size), //取出条数，默认不限制
	 *  'select' => array(),//查询的字段，可以是数组和字符串
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
			->table('order')
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
		});
	}		
	
	
	
	
    
    // green ==========================================

    /**
     * 查询交易明细
     * @param  [arr] $config [查询配置]
     * @return array
     */
    public function select_trade_detail_page($config = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
                $call_where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
                $call_where_user_plus = isset($config['where_user_plus']) && is_array($config['where_user_plus']) ? $config['where_user_plus'] : array();
                $call_where_user_minus = isset($config['where_user_minus']) && is_array($config['where_user_minus']) ? $config['where_user_minus'] : array();
                $call_where_mch_plus = isset($config['where_mch_plus']) && is_array($config['where_mch_plus']) ? $config['where_mch_plus'] : array();
                $call_where_mch_minus = isset($config['where_mch_minus']) && is_array($config['where_mch_minus']) ? $config['where_mch_minus'] : array();
                $call_orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
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

                //连接查询
                
                //增加的用户积分
                $plus_user_credit = array(
                    'table' => 'user_credit plus_uc',
                    'type' => 'LEFT',
                    'on' => 'plus_uc.user_credit_id = o.order_plus_transaction_id AND o.order_plus_method="user_credit"'
                );
                //减少的用户积分
                $minus_user_credit = array(
                    'table' => 'user_credit minus_uc',
                    'type' => 'LEFT',
                    'on' => 'minus_uc.user_credit_id = o.order_minus_transaction_id AND o.order_minus_method="user_credit"'
                );


                //查询总条数
                $counts = db(parent::DB_APPLICATION_ID)
                    ->table('order o')
                    ->joinon($plus_user_credit, $minus_user_credit)
                    ->call('where', $call_where)
                    ->call('where', $call_where_user_plus)
                    ->call('where', $call_where_user_minus)
                    ->call('where', $call_where_mch_plus)
                    ->call('where', $call_where_mch_minus)
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

                //默认查询
                if (empty($select)) {
                    $select = array();
                }

                //查询数据
                $data['data'] =  db(parent::DB_APPLICATION_ID)
                    ->table('order o')
                    ->joinon($plus_user_credit, $minus_user_credit)
                    ->call('where', $call_where)
                    ->call('where', $call_where_user_plus)
                    ->call('where', $call_where_user_minus)
                    ->call('where', $call_where_mch_plus)
                    ->call('where', $call_where_mch_minus)
                    ->call('orderby', $call_orderby)
                    ->call('limit', $call_limit)
                    ->select($select);

                return $data;
            }
        );
    }

	/**
	 * 获取 上一天的 购买积分总数(全部商家)
	 * 以0:00为界
	 * 
	 * @param	void
	 * @return	int
	 */
	public function find_yesterday_merchant_credit_all_sum(){
		$day_first = cmd(array(time()), "time day_first");//获取当天最初时间戳，即0点0时0分
		$yesterday_first = cmd(array($day_first - 1), "time day_first");//获取上一天最初时间戳，即0点0时0分
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($day_first, $yesterday_first), function($day_first, $yesterday_first){
			$data = db(parent::DB_APPLICATION_ID)
			->table('order')
			->where(
				array("order_plus_method=[+]", "merchant_credit"),//购买方式是商家积分
				array("[and] order_type=[+]", parent::TRANSACTION_TYPE_RECHARGE),//充值
				array('[and] order_pay_time<[-]', $day_first), 
				array('[and] order_pay_time>[-]', ($yesterday_first - 1) ) //减1 目的是 要等于 最初时间戳的 0点0时0分
				)
			->find(array("sum(order_plus_value) as sum"));
			
			return (empty($data["sum"])? 0 : $data["sum"]);
        });
		
	}




	/**
	 * 获取 今天的 购买积分总数(全部商家)
	 * 以0:00为界
	 * 
	 * @param	void
	 * @return	int
	 */
	public function find_day_merchant_credit_all_sum(){
		$day_first = cmd(array(time()), "time day_first");//获取当天最初时间戳，即0点0时0分
		$day_end = cmd(array(time()), "time day_end");//获取当天最后时间戳，(今日的23时59分59秒时间戳)
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($day_first, $day_end), function($day_first, $day_end){
			$data = db(parent::DB_APPLICATION_ID)
			->table('order')
			->where(
				array("order_plus_method=[+]", "merchant_credit"),//购买方式是商家积分
				array("[and] order_type=[+]", "recharge"),//充值
				array('[and] order_pay_time>[-]', ($day_first - 1) ), //减1 目的是 要等于 最初时间戳的 0点0时0分
				array('[and] order_pay_time<[-]', ($day_end + 1) ) //加1 目的是 要等于 今日的23时59分59秒时间戳
				)
			->find(array("sum(order_plus_value) as sum"));
			
			return (empty($data["sum"])? 0 : $data["sum"]);
        });
		
	}




	/**
	 * 获取指定时间戳（时间戳当天）的 购买积分总数(全部商家)
	 * 以0:00为界
	 * 
	 * @param	int		$timestamp		时间戳
	 * @return	int
	 */
	public function find_timestamp_merchant_credit_all_sum($timestamp){
		$day_first = cmd(array( $timestamp ), "time day_first");//获取当天最初时间戳，即0点0时0分
		$day_end = cmd(array( $timestamp ), "time day_end");//获取当天最后时间戳，(今日的23时59分59秒时间戳)
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($day_first, $day_end), function($day_first, $day_end){
			$data = db(parent::DB_APPLICATION_ID)
			->table('order')
			->where(
				array("order_plus_method=[+]", "merchant_credit"),//购买方式是商家积分
				array("[and] order_type=[+]", parent::TRANSACTION_TYPE_RECHARGE),//充值
				array('[and] order_pay_time>[-]', ($day_first - 1) ), //减1 目的是 要等于 最初时间戳的 0点0时0分
				array('[and] order_pay_time<[-]', ($day_end + 1) ) //加1 目的是 要等于 今日的23时59分59秒时间戳
				)
			->find(array("sum(order_plus_value) as sum"));
			
			return (empty($data["sum"])? 0 : $data["sum"]);
        });
			
	}



	
	/**
	 * 获取指定时间戳（时间戳当天）的 购买积分所花费的人民币之和(全部商家)
	 * 以0:00为界
	 * 
	 * @param	int		$timestamp		时间戳
	 * @param	string	$order_type		默认充值 recharge
	 * @return	int		单位，分
	 */
	public function find_timestamp_merchant_credit_rmb_all_sum($timestamp, $order_type = ""){
		if( empty($order_type) ) $order_type = parent::TRANSACTION_TYPE_RECHARGE;
		$day_first = cmd(array( $timestamp ), "time day_first");//获取当天最初时间戳，即0点0时0分
		$day_end = cmd(array( $timestamp ), "time day_end");//获取当天最后时间戳，(今日的23时59分59秒时间戳)
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($day_first, $day_end, $order_type), function($day_first, $day_end, $order_type){
			$data = db(parent::DB_APPLICATION_ID)
			->table('order')
			->where(
				array("order_plus_method=[+]", "merchant_credit"),//购买方式是商家积分
				array("[and] order_type=[+]", $order_type),
				array('[and] order_pay_time>[-]', ($day_first - 1) ), //减1 目的是 要等于 最初时间戳的 0点0时0分
				array('[and] order_pay_time<[-]', ($day_end + 1) ) //加1 目的是 要等于 今日的23时59分59秒时间戳
				)
			->find(array("sum(order_minus_value) as sum"));
			
			return (empty($data["sum"])? 0 : $data["sum"]);
        });
	}

	
	
	
	/**
	 * 获取指定时间戳（时间戳当天）的 所有用户增加的共享金之和(全部用户)
	 * 以0:00为界
	 * 
	 * @param	int		$timestamp		时间戳
	 * @param	string	$order_type		默认系统兑换 system_conversion
	 * @return	int		单位，分
	 */
	public function find_timestamp_user_money_share_all_sum($timestamp, $order_type = ""){
		if( empty($order_type) ) $order_type = parent::TRANSACTION_TYPE_SYSTEM_CONVERSION;
		$day_first = cmd(array( $timestamp ), "time day_first");//获取当天最初时间戳，即0点0时0分
		$day_end = cmd(array( $timestamp ), "time day_end");//获取当天最后时间戳，(今日的23时59分59秒时间戳)
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($day_first, $day_end, $order_type), function($day_first, $day_end, $order_type){
			$data = db(parent::DB_APPLICATION_ID)
			->table('order')
			->where(
				array("order_plus_method=[+]", "user_money_share"),//用户共享金
				array("[and] order_type=[+]", $order_type),
				array('[and] order_pay_time>[-]', ($day_first - 1) ), //减1 目的是 要等于 最初时间戳的 0点0时0分
				array('[and] order_pay_time<[-]', ($day_end + 1) ) //加1 目的是 要等于 今日的23时59分59秒时间戳
				)
			->find(array("sum(order_plus_value) as sum"));
			
			return (empty($data["sum"])? 0 : $data["sum"]);
        });
	}
	




	/**
	 * 获取指定用户ID 当天签到送积分的订单
	 * 
	 * @param	string		$user_id	用户ID
	 * @return	array
	 */
	public function find_daily_attendance_earn_user_credit($user_id = ""){
		if( empty($user_id) ){
			return false;
		}
		
		$day_first = cmd(array(time()), "time day_first");//获取当天最初时间戳，即0点0时0分
		$day_end = cmd(array(time()), "time day_end");//获取当天最后时间戳，(今日的23时59分59秒时间戳)
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id, $day_first, $day_end), function($user_id, $day_first, $day_end){
			return db(parent::DB_APPLICATION_ID)
			->table('order')
			->where(
				array("order_type=[+]", parent::TRANSACTION_TYPE_DAILY_ATTENDANCE),//每日签到
				array("[and] order_plus_method=[+]", "user_credit"),//用户积分
				array("[and] order_plus_account_id=[+]", $user_id),//用户ID
				array('[and] order_state=1'),
				array('[and] order_pay_state=1'),
				array('[and] order_insert_time>[-]', ($day_first - 1) ), //减1 目的是 要等于 最初时间戳的 0点0时0分
				array('[and] order_insert_time<[-]', ($day_end + 1) ) //加1 目的是 要等于 今日的23时59分59秒时间戳
			)
			->find();
	    });
		
	}




	/**
	 * 获取流水信息
	 * 
	 * @param	array		$call_where		查询条件
	 * @param	string		$find_value		查询的值
     * @return  string
	 */
	public function sql_where_serial($call_where = "", $find_value){
		return db(parent::DB_APPLICATION_ID)
        ->table('order o')
		->call('where', $call_where)
        ->find(array($find_value), function($q){
            return $q['query']['find'];
        });
	}





    
    /**
	 * 
	 * ----- Mr.Zhao ----- 2019.06.06 -----
	 * 
     * 获取所有分页数据
     * $config = array(
	 * 	'where' => array(), //条件
	 * 	'orderby' => array(), //排序
	 * 	'limit'	=> array(0, page_size), //取出条数，默认获取10条
	 * );
	 * 
	 * limit的分页算法是：当前页数-1 * page_size
	 * 序号的算法：key键+1，+每页显示的条数。等于分页后的序号。{key + 1 + page_size}
	 * 
	 * 返回的数据：
	 * $data = array(
	 * 	'row_count' => //数据总条数
	 * 	'limit_count' => //已取出条数
	 * 	'page_size' => //每页的条数
	 * 	'page_count' => //总页数
	 *  'page_now' => //当前页数
	 * 	'data' => //数据
	 * );
	 * 
	 * @param	array	$config		配置信息
	 * @return	array
     */
    public function select_page($config = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $call_where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            
            $call_orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
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
            ->table('order o')
            ->call('where', $call_where)
            ->find('count(distinct o.order_id) as count'/*, function($p){
            	printexit($p);
            }*/);

            if( empty($counts['count']) ){
                return $data;
            }else{
                $data['row_count'] = $counts['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count'] / $data['page_size']);
                    $data['page_now'] = ceil($limit[0] / $data['page_size']) + 1;
                }
            }

            //默认查询
            if( empty($select) ){
                $select = array(
                	"o.*"
				);
            }

            //查询数据
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('order o')
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select/*, function($p){
            	printexit($p);
            }*/);

            return $data;
        });
    }

    /**
     * 获取收款人ID信息
     *
     * @param	array		$call_where		查询条件
     * @param	string		$find_value		查询的字段
     * @return  string
     */
    public function sql_join_order_agent_money($pay_time, $alias){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        return db(parent::DB_APPLICATION_ID)
            ->table('order as o_temp')
            ->where(
                array('o_temp.order_plus_account_id = '.$alias.'user_id'),
                array('o_temp.order_plus_method = [+]', parent::PAY_METHOD_USER_MONEY),
                array('o_temp.order_pay_time = []', $pay_time),
                array('o_temp.order_type = []', parent::TRANSACTION_TYPE_SHOP_ORDER_AGENT_MONEY_DIVIDEND),
                array('o_temp.order_state = 1')
            )
        ->select($alias.'user_id', function($p){
            return $p['query']['select'];
        });
    }


}
?>