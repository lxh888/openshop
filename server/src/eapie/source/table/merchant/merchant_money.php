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
class merchant_money extends main {
	
	
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__);
	

	
	
		
	/**
	 * 数据检测
	 * 
	 * @var	array
	 */
	public $check = array(
		"merchant_money_label" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("钱包余额标签的数据类型不合法"),
					),
			//字符长度检测
			'length' => array(
					'<length'=>array(200, "钱包余额标签的字符长度太多")
					),		
		),
		
		
		"merchant_money_comment" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("钱包余额备注的数据类型不合法"),
					),
		),
		
		"number" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("钱包余额的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/iu', "钱包余额必须是整数"),
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
		->table('merchant_money')
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
	 * 收入不用传入  merchant_money_value 、 merchant_money_join_id
	 * 
	 * $data = array(
	 * 	"merchant_id" => 商家ID
	 * 	"merchant_money_plus" => 要添加的钱包余额个数
	 *  "merchant_money_type" => 交易类型的键名称
	 *  "merchant_money_time" => 时间
	 * );
	 * 
	 * 
	 * @param	array		$data		数据
	 * @return	bool
	 */
	public function insert_plus($data){
		if( empty($data['merchant_id']) || 
		empty($data['merchant_money_plus']) ||
		!is_numeric($data['merchant_money_plus']) ||
		$data['merchant_money_plus'] < 0 ){
			return false;
		}
		
		//如果交易类型不存在
		$type_list = object(parent::TABLE_ORDER)->get_type();
		if( empty($data['merchant_money_type']) ||
		!isset( $type_list[$data['merchant_money_type']] ) ){
			return false;
		}
		
		$find_now_data = $this->find_now_data($data['merchant_id']);//查询用户当前钱包余额
		if( !empty($find_now_data) ){
			$data["merchant_money_value"] = $find_now_data['merchant_money_value'] + $data["merchant_money_plus"];
			$data["merchant_money_join_id"] = $find_now_data['merchant_money_id'];
		}else{
			$data["merchant_money_value"] = $data["merchant_money_plus"];
			$data["merchant_money_join_id"] = "";
		}
		
		if( empty($data['merchant_money_time']) ){
			$data['merchant_money_time'] = time();
			if( !empty($find_now_data['merchant_money_time']) && 
			$find_now_data['merchant_money_time'] >= $data['merchant_money_time'] ){
				$data['merchant_money_time'] = ($find_now_data['merchant_money_time'] + 1);
			}
			
		}
		
		if( !empty($find_now_data['merchant_money_time']) && 
		$find_now_data['merchant_money_time'] >= $data['merchant_money_time'] ){
			return false;
		}
		
		
		$where = array();
		//连接
		if( empty($data['merchant_money_join_id']) ){
			$where[] = array("([-]) IS NULL", $this->sql_now_id($data['merchant_id']), TRUE);
		}else{
			$where[] = array("([-]) = '".$data['merchant_money_join_id']."'", $this->sql_now_id($data['merchant_id']), TRUE);
			$where[] = array("[and] ([-]) < ".$data['merchant_money_time'], $this->sql_now_time($data['merchant_id']), TRUE);
			$where[] = array("[and] ([-]) + ".$data['merchant_money_plus']." = ".$data['merchant_money_value'], $this->sql_now_value($data['merchant_id']), TRUE);
		}
		
		$data['merchant_money_id'] = $this->get_unique_id();//获取唯一ID
		$insert_sql = db(parent::DB_APPLICATION_ID)
		->table('merchant_money', function($p){
			return "INSERT INTO " . $p['query']['table'] . 
			"(".
			"`merchant_money_id`".
			",`merchant_money_join_id`".
			", `merchant_id`".
			", `merchant_money_type`".
			", `merchant_money_plus`".
			", `merchant_money_value`".
			", `merchant_money_time`".
			")";
		});
		
		$select_sql = db(parent::DB_APPLICATION_ID)
		->table('merchant_money')
		->call('where', $where)
		->select(function($p) use ($data){
			return "SELECT ". 
			"\"".$data['merchant_money_id']."\"".
			",\"".$data['merchant_money_join_id']."\"".
			",\"".$data['merchant_id']."\"".
			",\"".$data['merchant_money_type']."\"".
			",".$data['merchant_money_plus'].
			",".$data['merchant_money_value'].
			",".$data['merchant_money_time'].
			" FROM DUAL " . $p['query']['where'];
		});
		
		//printexit( $insert_sql." ".$select_sql );
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)->query($insert_sql." ".$select_sql);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
			return $data['merchant_money_id'];
		}else{
			return false;
		}
		
		
	}
	
	
	
	/**
	 * 支出
	 * 
	 * $data = array(
	 * 	"merchant_id" => 商家ID
	 * 	"merchant_money_join_id" => 商家之前的钱包余额ID
	 *  "merchant_money_value" => 商家要更新的总钱包余额
	 * 	"merchant_money_minus" => 要减少的钱包余额个数
	 *  "merchant_money_type" => 交易类型的键名称
	 *  "merchant_money_time" => 时间
	 * );
	 * 
	 * 
	 * @return	bool
	 */
	public function insert_minus($data){
		if( empty($data['merchant_id']) || 
		empty($data['merchant_money_minus']) ||
		!is_numeric($data['merchant_money_minus']) ||
		$data['merchant_money_minus'] < 1 ||
		!isset($data['merchant_money_value']) ||
		!is_numeric($data['merchant_money_value']) ||
		$data['merchant_money_value'] < 0 ||
		empty($data['merchant_money_join_id']) ||
		(!is_string($data['merchant_money_join_id']) && !is_numeric($data['merchant_money_join_id'])) ){
			return false;
		}
		
		//如果交易类型不存在
		$type_list = object(parent::TABLE_ORDER)->get_type();
		if( empty($data['merchant_money_type']) ||
		!isset( $type_list[$data['merchant_money_type']] ) ){
			return false;
		}
		
		$find_now_data = $this->find_now_data($data['merchant_id']);//查询用户当前钱包余额
		if( empty($find_now_data['merchant_money_id']) ||
		$find_now_data['merchant_money_id'] != $data['merchant_money_join_id']){
			return false;
		}
		
		if( empty($data['merchant_money_time']) ){
			$data['merchant_money_time'] = time();
			if( $find_now_data['merchant_money_time'] >= $data['merchant_money_time'] ){
				$data['merchant_money_time'] = ($find_now_data['merchant_money_time'] + 1);
			}
		}
		if( $find_now_data['merchant_money_time'] >= $data['merchant_money_time'] ){
			return false;
		}
		
		
		$where = array();
		$where[] = array("([-]) = '".$data['merchant_money_join_id']."'", $this->sql_now_id($data['merchant_id']), TRUE);
		$where[] = array("[and] ([-]) < ".$data['merchant_money_time'], $this->sql_now_time($data['merchant_id']), TRUE);
		$where[] = array("[and] ([-]) - ".$data['merchant_money_minus']." = ".$data['merchant_money_value'], $this->sql_now_value($data['merchant_id']), TRUE);
		
		$data['merchant_money_id'] = $this->get_unique_id();//获取唯一ID
		$insert_sql = db(parent::DB_APPLICATION_ID)
		->table('merchant_money', function($p){
			return "INSERT INTO " . $p['query']['table'] . 
			"(".
			"`merchant_money_id`".
			",`merchant_money_join_id`".
			", `merchant_id`".
			", `merchant_money_type`".
			", `merchant_money_minus`".
			", `merchant_money_value`".
			", `merchant_money_time`".
			")";
		});
		
		$select_sql = db(parent::DB_APPLICATION_ID)
		->table('merchant_money')
		->call('where', $where)
		->select(function($p) use ($data){
			return "SELECT ". 
			"\"".$data['merchant_money_id']."\"".
			",\"".$data['merchant_money_join_id']."\"".
			",\"".$data['merchant_id']."\"".
			",\"".$data['merchant_money_type']."\"".
			",".$data['merchant_money_minus'].
			",".$data['merchant_money_value'].
			",".$data['merchant_money_time'].
			" FROM DUAL " . $p['query']['where'];
		});
		
		//printexit( $insert_sql." ".$select_sql );
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)->query($insert_sql." ".$select_sql);
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
			
			return $data['merchant_money_id'];
		}else{
			return false;
		}
		
	}
	
	
	

		
	
	/**
	 * 商家钱包操作
	 * $data = array(
	 * 	"admin_user_id" => "操作人，管理员的用户ID"
	 *  "merchant_id" => "要添加的商家id"
	 *  "comment" => 备注信息
	 *  "value" => 积分数量
	 *  "type" => 交易类型
	 *  "merchant_money" => 商家钱包旧数据
	 * )
	 * 
	 * @return bool
	 */
	public function insert_admin( $data = array() ){
		if( empty($data["admin_user_id"]) || 
		empty($data["merchant_id"]) || 
		empty($data['type']) ||
		empty($data['value']) ||
		!is_numeric($data['value']) ||
		$data['value'] < 0 ){
			return false;
		}
		
		$data['comment'] = empty($data['comment'])? "" : $data['comment'];
		
		$lock_ids = array();
		//事务处理，开始锁商家的积分数据
        $merchant_money_lock_id = object(parent::TABLE_LOCK)->start("merchant_id", $data["merchant_id"], parent::LOCK_MONEY);
        if( empty($merchant_money_lock_id) ){
            return false;//事务开启失败
        }
		$lock_ids[] = $merchant_money_lock_id;
		
		
		if( $data["type"] == parent::TRANSACTION_TYPE_ADMIN_PLUS ){
			//人工添加
			db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
            
            //将充值积分 提交给 商家
            $merchant_money_id = object(parent::TABLE_MERCHANT_MONEY)->insert_plus(array(
                "merchant_id" => $data['merchant_id'],
                "merchant_money_plus" => $data['value'],
                "merchant_money_type" => $data['type']
            ));
            
            //积分充值失败
            if( empty($merchant_money_id) ){
                db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
                object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
                return false;
            }
			
			//生成订单
	        $order_insert = array(
	            "order_id" => $this->get_unique_id(),
	            "order_type" => $data['type'],//提现
	            "order_comment" => $data['comment'],
	            "order_plus_method" => "merchant_money",
	            "order_plus_account_id" => $data['merchant_id'],
	            "order_plus_value" => $data['value'],
	            "order_plus_transaction_id" => $merchant_money_id,//交易号
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
			
			if( empty($data['merchant_money']) || 
			empty($data['merchant_money']['merchant_id']) ){
				object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
				return false;
			}
			
			db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
			
			//减商家的积分
	        $merchant_money_id = object(parent::TABLE_MERCHANT_MONEY)->insert_minus(array(
	            "merchant_id" => $data["merchant_money"]['merchant_id'],
	            "merchant_money_join_id" => $data["merchant_money"]['merchant_money_id'],
	            "merchant_money_value" => ($data["merchant_money"]['merchant_money_value'] - $data["value"]),
	            "merchant_money_minus" => $data["value"],
	            "merchant_money_type" => $data['type']
	        ));
			
			//积分充值失败
            if( empty($merchant_money_id) ){
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
	            "order_minus_method" => "merchant_money",
	            "order_minus_account_id" => $data['merchant_id'],
	            "order_minus_value" => $data['value'],
	            "order_minus_transaction_id" => $merchant_money_id,//交易号
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
		->table('merchant_money')
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
		->table('merchant_money')
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
	 * @param	array	$merchant_money_id
	 * @return	array
	 */
	public function remove($merchant_money_id = ''){
		if( empty($merchant_money_id) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('merchant_money')
		->where(array('merchant_money_id=[+]', (string)$merchant_money_id))
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
	 * @param	array	$merchant_money_id
	 * @return	array
	 */
	public function find($merchant_money_id = ''){
		if( empty($merchant_money_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($merchant_money_id), function($merchant_money_id){
			return db(parent::DB_APPLICATION_ID)
			->table('merchant_money')
			->where(array('merchant_money_id=[+]', (string)$merchant_money_id))
			->find();
		});
		
	}
	
	
	
	
	
	/**
	 * 根据商家ID，获取最新的一个数据
	 * 
	 * @param	array	$merchant_id
	 * @return	array
	 */
	public function find_now_data($merchant_id = ''){
		if( empty($merchant_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($merchant_id), function($merchant_id){
			return db(parent::DB_APPLICATION_ID)
			->table('merchant_money')
			->where(array('merchant_id=[+]', (string)$merchant_id))
			->orderby( array('merchant_money_time', true), array('merchant_money_id') )
			->find();
		});
		
	}
	
	
	
	
	
	
	
	
	
		
		
	/**
	 * 获取多个商家数据
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
			->table('merchant_money')
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
			
		});
		
	}	

	/**
	 * 查询某商家账单明细
	 * @author green
	 * @create 2019-01-30T11:57:17+0800
	 *
	 * @param  [arr] $config [查询配置]
	 * @return array
	 */
    public function select_page($config)
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__,
            array($config),
            function ($config) {
                $call_where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
                $call_orderby = array(array('merchant_money_time', true));
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
                    ->table('merchant_money mm')
                    ->call('where', $call_where)
                    ->find('count(distinct merchant_money_id) as count');

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
                    ->table('merchant_money mm')
                    ->call('where', $call_where)
                    ->call('orderby', $call_orderby)
                    ->call('limit', $call_limit)
                    ->select($select);

                return $data;
            }
        );
    }


    /**
	 * 查询某商家账单明细
	 *
	 * @param  [arr] $config [查询配置]
	 * @return array
	 */
    public function select_order_page($config = array())
    {
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

            //连表
            $join_order = array(
            	'table' => 'order o',
				'type' => 'left',
				'on' => '(o.order_plus_method="merchant_money" AND o.order_plus_transaction_id = mm.merchant_money_id) OR (o.order_minus_method="merchant_money" AND o.order_minus_transaction_id = mm.merchant_money_id)'
            );

            //查询总条数
            $counts = db(parent::DB_APPLICATION_ID)
                ->table('merchant_money AS mm')
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
                ->table('merchant_money AS mm')
                ->joinon($join_order)
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $call_limit)
                ->select($select);

            return $data;
        });
    }


	/**
	 * 返回商家的最新钱包余额ID，SQL语句
	 * 
	 * @param	string		$merchant_id
	 * @return	string
	 */
	public function sql_now_id($merchant_id){
		return db(parent::DB_APPLICATION_ID)
		->table('merchant_money')
		->where(array('merchant_id=[+]', (string)$merchant_id))
		->orderby( array('merchant_money_time', true), array('merchant_money_id') )
		->find(array('merchant_money_id'), function($q){
			return $q['query']['find'];
		});
	}
		
		
		
	/**
	 * 返回商家的最新剩余钱包余额，SQL语句
	 * 
	 * @param	string		$merchant_id
	 * @return	string
	 */
	public function sql_now_value($merchant_id){
		return db(parent::DB_APPLICATION_ID)
		->table('merchant_money')
		->where(array('merchant_id=[+]', (string)$merchant_id))
		->orderby( array('merchant_money_time', true), array('merchant_money_id') )
		->find(array('merchant_money_value'), function($q){
			return $q['query']['find'];
		});
	}	
	
	
	
	/**
	 * 返回商家的最新剩余钱包时间，SQL语句
	 * 
	 * @param	string		$merchant_id
	 * @return	string
	 */
	public function sql_now_time($merchant_id){
		return db(parent::DB_APPLICATION_ID)
		->table('merchant_money')
		->where(array('merchant_id=[+]', (string)$merchant_id))
		->orderby( array('merchant_money_time', true), array('merchant_money_id') )
		->find(array('merchant_money_time'), function($q){
			return $q['query']['find'];
		});
	}	
	
	
	
		
	/**
	 * 联表。返回商家的最新剩余积分，SQL语句
	 * 
	 * @param   string      $alias	别名称
	 * @return	string
	 */
	public function sql_join_merchant_now_value($alias = ""){
		if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
		
		return db(parent::DB_APPLICATION_ID)
		->table('merchant_money mm')
		->where(array('mm.merchant_id = '.$alias.'merchant_id'))
		->orderby( array('mm.merchant_money_time', true), array('mm.merchant_money_id') )
		->find(array('mm.merchant_money_value'), function($q){
			return $q['query']['find'];
		});
	}	
	
	
	/**
	 * 联表。返回商家的最新剩余积分交易时间，SQL语句
	 * 
	 * @param   string      $alias	别名称
	 * @return	string
	 */
	public function sql_join_merchant_now_time($alias = ""){
		if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
		
		return db(parent::DB_APPLICATION_ID)
		->table('merchant_money mm')
		->where(array('mm.merchant_id = '.$alias.'merchant_id'))
		->orderby( array('mm.merchant_money_time', true), array('mm.merchant_money_id') )
		->find(array('mm.merchant_money_time'), function($q){
			return $q['query']['find'];
		});
	}	
	
	
	
	
	
	
	
	
	/**
	 * 获取所有商家的钱包之和
	 * 
	 * @param	void
	 * @return	array
	 */
	public function find_now_where_sum($where = array()){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($where), function($where){
			$from_merchant_money = db(parent::DB_APPLICATION_ID)
			->table('merchant_money')
			->orderby( array('merchant_money_time', true), array('merchant_money_id') )
			->select(function($q){
				return $q['query']['select'];
			});
			$from_merchant_money = db(parent::DB_APPLICATION_ID)
			->from("(".$from_merchant_money.") from_mm")
			->groupby('from_mm.merchant_id')
			->select(function($q){
				return $q['query']['select'];
			});
			$data = db(parent::DB_APPLICATION_ID)
			->from("(".$from_merchant_money.") mm")
			->call("where", $where)
			//->where( array('mm.merchant_money_value >= [-]', ) )
			->find("sum(mm.merchant_money_value) as sum");
			
			return empty($data["sum"])? 0 : (int)$data["sum"];
		});
		
	}
	
	
	
		
	/**
	 * 商家钱包列表
	 *
	 * @param  array	$config		配置
	 * @return array
	 */
    public function select_merchant_page($config) {
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
            ->table('merchant m')
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
				$sql_join_now_value = $this->sql_join_merchant_now_value("m");
				$sql_join_now_time = $this->sql_join_merchant_now_time("m");
				$select = array(
					"m.*",
					'IFNULL(('.$sql_join_now_value.'), 0) as merchant_money_value',
					'('.$sql_join_now_time.') as merchant_money_time',
				);
			}
			
            //查询数据
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('merchant m')
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
                'on' => 'o.order_plus_transaction_id = mm.merchant_money_id OR o.order_minus_transaction_id = mm.merchant_money_id'
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
                'on' => 'plus_o.order_pay_state=1 AND plus_o.order_plus_transaction_id = mm.merchant_money_id AND plus_o.order_plus_method="merchant_money"'
            );
			
			
			//减少订单表
            $minus_order = array(
                'table' => 'order as minus_o',
                'type' => 'LEFT',
                'on' => 'minus_o.order_pay_state=1 AND minus_o.order_minus_transaction_id = mm.merchant_money_id AND minus_o.order_minus_method="merchant_money"'
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
            
			
			//商家表
			$merchant = array(
                'table' => 'merchant m',
                'type' => 'LEFT',
                'on' => 'm.merchant_id = mm.merchant_id'
            );
			
			
			
			
            //查询总条数
            $counts = db(parent::DB_APPLICATION_ID)
            ->table('merchant_money mm')
			->joinon($merchant, $plus_order, $plus_action_user, $minus_order, $minus_action_user)
            ->call('where', $call_where)
            ->find('count(distinct mm.merchant_money_id) as count');
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
				$select = array(
					"mm.*",
					"IFNULL(plus_o.order_id, minus_o.order_id) as order_id",
					"IFNULL(plus_o.order_comment, minus_o.order_comment) as order_comment",
					"IFNULL(plus_oau.user_id, minus_oau.user_id) as order_action_user_id",
					"IFNULL(plus_oau.user_logo_image_id, minus_oau.user_logo_image_id) as order_action_user_logo_image_id",
					"IFNULL(plus_oau.user_nickname, minus_oau.user_nickname) as order_action_user_nickname",
                    'IFNULL(('.$plus_oau_user_phone_sql.'), ('.$minus_oau_user_phone_sql.')) as order_action_user_phone_verify_list',
					'm.merchant_logo_image_id',
                    'm.merchant_name',
				);
			}
			
            //查询数据
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('merchant_money mm')
			->joinon($merchant, $plus_order, $plus_action_user, $minus_order, $minus_action_user)
			->groupby("mm.merchant_money_id")
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);

            return $data;
            }
        );
    }

	
	
	
	
	/**
	 * 商家收款，回调时
	 * 
	 * @param	array		$transaction_id			第三方的交易号
	 * @param	array		$order_data
	 * @param	array		$lock_ids
	 * @return	bool
	 */
	public function plus_notify_trade_success($transaction_id, &$order_data, &$lock_ids){
		//事务处理，开始锁商家的积分数据
        $plus_lock_id = object(parent::TABLE_LOCK)->start("merchant_id", $order_data["order_plus_account_id"], parent::LOCK_MONEY);
        if( empty($plus_lock_id) ){
            $order_data['order_json']['error'][] = "merchant_id为“".$order_data["order_plus_account_id"]."”[LOCK_MONEY]锁开启失败";
			object(parent::TABLE_ORDER)->update_json($order_data["order_id"], $order_data['order_json']);
			object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;//事务开启失败
        }
		$lock_ids[] = $plus_lock_id;
		
		
		db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
            
        //将充值余额 提交给 商家
        $merchant_money_id = object(parent::TABLE_MERCHANT_MONEY)->insert_plus(array(
            "merchant_id" => $order_data['order_plus_account_id'],
            "merchant_money_plus" => $order_data['order_plus_value'],
            "merchant_money_type" => $order_data['order_type']
        ));
		
		//充值失败
        if( empty($merchant_money_id) ){
            $order_data['order_json']['error'][] =  "merchant_id为“".$order_data["order_plus_account_id"]."”商家余额收入失败";
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_ORDER)->update_json($order_data["order_id"], $order_data['order_json']);
			object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
		//修改订单支付状态 (订单ID, 收款交易号, 支付交易号)
        $bool = object(parent::TABLE_ORDER)->update_pay_success($order_data["order_id"], $merchant_money_id, $transaction_id, $order_data["order_json"]);
        if( empty($bool) ){
            //file_put_contents(CACHE_PATH."/test.订单交易状态更新失败", cmd(array($order_data), "json encode")); 
            $order_data['order_json']['error'][] =  "order_id“".$order_data["order_id"]."”订单交易状态更新失败";
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_ORDER)->update_json($order_data["order_id"], $order_data['order_json']);
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }
		
		db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
		
		//商家赠送用户积分
        object(parent::TABLE_MERCHANT_CREDIT)->consume_give_user_credit(
        	$order_data["order_plus_account_id"],
        	$order_data['order_minus_account_id'],
        	$order_data['order_minus_value'],
        	$order_data["order_id"],
        	$order_data['order_action_user_id'],
        	$order_data['order_comment'],
        	$order_data['order_json'],
        	$lock_ids
			);
    	object(parent::TABLE_ORDER)->update_json($order_data["order_id"], $order_data['order_json']);//更新一下信息
		
		object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
        return true;
	}
	
	
	
	
	
	
	
	
	
	
}
?>