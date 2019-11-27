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



namespace eapie\source\table\express;
use eapie\main;
class express_order extends main {

    /**
     * 缓存的键列表
     * @var string
     */
    const CACHE_KEY = array(__CLASS__, 'order');

    public $check = array(
    	'express_order_id'=>array(
            //参数检测
            'args'=>array(
                'exist'=>array("缺少订单ID参数"),
                'echo'=>array("订单ID数据类型，不合法"),
                '!null'=>array("订单ID，不能为空"),
            ),
        ),
		
		'express_order_shipping_no' => array(
            'args' => array(
                'exist'=> array('缺少物流运单号参数'),
                'echo' => array('物流运单号的数据类型不合法'),
            ),
        ),
		'express_order_shipping_state' => array(
			//状态。0未发货，等待发货；1确认收货; 2已发货，运送中
            'args'=>array(
				'echo'=>array("配送状态的数据类型不合法"),
				'match'=>array('/^[012]{1}$/', "配送状态值必须是0、1、2"),
				),
        ),
	
	
	
        'user_address_id'=>array(
            //参数检测
            'args'=>array(
                'exist'=>array("缺少地址ID参数"),
                'echo'=>array("地址ID数据类型，不合法"),
                '!null'=>array("地址ID，不能为空"),
            ),
        ),
        'express_order_type'=>array(
            //参数检测
            'args'=>array(
                'exist'=>array("寄件类型参数,不存在"),
                'echo'=>array("寄件类型参数，不合法"),
                '!null'=>array("寄件类型参数，不能为空"),
            )
        ),
        'get_shipping'=>array(
            //参数检测
            'args'=>array(
                'exist'=>array("快递类型参数，不存在"),
                'echo'=>array("快递类型参数，不合法"),
                '!null'=>array("快递类型参数，不能为空"),
            )
        ),
        'get_province'=>array(
            //参数检测
            'args'=>array(
                'exist'=>array("收件人省份，不存在"),
                '!null'=>array("收件人省份，不能为空"),
            )
        ),
        'get_city'=>array(
            //参数检测
            'args'=>array(
                'exist'=>array("收件人城市，不存在"),
                '!null'=>array("收件人城市，不能为空"),
            )
        ),
        'get_district'=>array(
            //参数检测
            'args'=>array(
                'exist'=>array("收件人地区，不存在"),
                '!null'=>array("收件人地区，不能为空"),
            )
        ),
        'get_name'=>array(
            //参数检测
            'args'=>array(
                'exist'=>array("收件人姓名，不存在"),
                '!null'=>array("收件人姓名，不能为空"),
            )
        ),
        'get_phone'=>array(
            //参数检测
            'args'=>array(
                'exist'=>array("收件人姓名，不存在"),
                '!null'=>array("收件人姓名，不能为空"),
            )
        ),
        'get_details'=>array(
            //参数检测
            'args'=>array(
                'exist'=>array("收件人详细地址，不存在"),
                '!null'=>array("收件人详细地址，不能为空"),
            )
        ),
        'pay_type'=>array(
            //参数检测
            'args'=>array(
                'echo'=>array("支付类型，不合法"),
            )
        ),
        'express_order_id'=>array(
            //参数检测
            'args'=>array(
                'exist'=>array("订单ID，不存在"),
                'echo'=>array("订单ID，不合法"),
                '!null'=>array("订单ID，不能为空"),
            )
        ),
        'express_send_type'=>array(
            //参数检测
            'args'=>array(
                'echo'=>array("寄件类型，不合法"),
            )
        ),
        'express_order_money'=>array(
            //参数检测
            'args'=>array(
                'exist'=>array("收件人详细地址，不存在"),
                'echo'=>array("寄件金额，不合法"),
                '!null'=>array("收件人详细地址，不能为空"),
            )
        ),
        "discount_id" => array(
            //参数检测
            'args'=>array(
                'echo'=>array("优惠券参数错误"),     
            ),
        ),
        "pay_method" => array(
            //参数检测
            'args'=>array(
                'exist'=>array("缺少支付方式参数"),
                'echo'=>array("支付方式的数据类型不合法"),
                '!null'=>array("支付方式不能为空"),
                'method'=>array(array(parent::TABLE_EXPRESS_ORDER, 'check_method'), "支付方式输入有误，不能被识别")       
            ),
        ),
        "weixin_login_code" => array(
            //参数检测
            'args'=>array(
                'echo'=>array("微信支付，参数缺失")
             
            ),
        ),
        "weixin_trade_type" => array(
            //参数检测
            'args'=>array(
                'echo'=>array("微信支付，参数缺失")
                      
            ),
        ),  
        "express_order_when_start_time" => array(
            //导出起始时间
            'args'=>array(
                'exist'=>array("起始时间参数，不存在"),
                'echo'=>array("起始时间参数，不合法"),
                '!null'=>array("起始时间参数，不能为空"),
                'match'=>array('/^[0-9]{4,}\-[0-9]{1,2}\-[0-9]{1,2}\s{1,}[0-9]{1,2}\:[0-9]{1,2}\:[0-9]{1,2}$/', "订单导出开始时间格式有误"),
            )    
        ),  
        "express_order_when_end_time" => array(
            //导出截止时间
            'args'=>array(
                'exist'=>array("截止时间参数，不存在"),
                'echo'=>array("截止时间参数，不合法"),
                '!null'=>array("截止时间参数，不能为空"),
                'match'=>array('/^[0-9]{4,}\-[0-9]{1,2}\-[0-9]{1,2}\s{1,}[0-9]{1,2}\:[0-9]{1,2}\:[0-9]{1,2}$/', "订单导出截止时间格式有误"),
            )    
        ),
        'express_order_coupon_forbidden_state' => array(
			//状态。0未发货，等待发货；1确认收货; 2已发货，运送中
            'args'=>array(
				'echo'=>array("优惠券禁用状态的数据类型不合法"),
				'match'=>array('/^[01]{1}$/', "优惠券禁用状态值必须是0、1"),
				),
        )
    );

     /**
     * 获取支付方式
     * 
     * @param   void
     * @return  array
     */
    public function get_method(){
        return array(
            parent::PAY_METHOD_WEIXINPAY => "微信支付",
            parent::PAY_METHOD_ALIPAY => "支付宝支付"
        );
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
     * @return  string
     */
    public function get_unique_id($num=0)
    {
        if($num>0){
            return cmd(array($num), 'random autoincrement');
        }
        return cmd(array(16), 'random autoincrement');
        
    }


    /**
     * 插入新数据
     * 
     * @param   array $data      数据
     * @param   array $call_data 数据
     * @return  bool
     */
    public function insert($data = array(), $call_data = array())
    {
        if (empty($data) && empty($call_data))
            return false;
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('express_order')
            ->call('data', $call_data)
            ->insert($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }



	/**
     * 更新数据
     * 
     * @param   array       $where
     * @param   array       $data
     * @param   array       $call_data
     * @return  bool
     */
    public function update($call_where = array(), $data = array(), $call_data = array()){
        $bool = db(parent::DB_APPLICATION_ID)
        ->table('express_order')
        ->call('where', $call_where)
        ->call('data', $call_data)
        ->update($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }







    /**
     * 获取订单不分页数据
     *  $config = array(
     *  'where' => array(), //条件
     *  'orderby' => array(), //排序
     *  'limit' => array(0, page_size), //取出条数，默认不限制
     * );
     * 
     * @param   array   $config
     * @return  array
     */
    public function select($config = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            $where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
            
            //用户数据
            $user = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = eo.user_id'
            );

            //骑手数据
			$rider = array(
				'table' => 'express_rider rider',
				'type' => 'left',
				'on' => 'rider.user_id = eo.express_rider_user_id'
			);

            return db(parent::DB_APPLICATION_ID)
            ->table('express_order eo')
            ->joinon($user, $rider)
            ->call('where', $where)
            ->call('orderby', $orderby)
            ->call('limit', $limit)
            ->select($select);
        });
    } 

    /**
     * 获取所有的分页数据
     * 
     * $config = array(
     *  'where' => array(), //条件
     *  'orderby' => array(), //排序
     *  'limit' => array(0, page_size), //取出条数，默认获取10条
     * );
     * 
     * limit的分页算法是：当前页数-1 * page_size
     * 序号的算法：key键+1，+每页显示的条数。等于分页后的序号。{key + 1 + page_size}
     * 
     * 返回的数据：
     * $data = array(
     *  'row_count' => //数据总条数
     *  'limit_count' => //已取出条数
     *  'page_size' => //每页的条数
     *  'page_count' => //总页数
     *  'page_now' => //当前页数
     *  'data' => //数据
     * );
     * 
     * @param   array   $config     配置信息
     * @return  array
     */
    public function select_page($config = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            $call_where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $call_limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
            
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
                'page_now' => 1,
                'data' => array()
            );
			
			//用户数据
            $user = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = eo.user_id'
            );
			
			
			//骑手数据
			$rider = array(
				'table' => 'user rider',
				'type' => 'left',
				'on' => 'rider.user_id = eo.express_rider_user_id'
			);
			
			
            //先获取总条数
            $find_data = db(parent::DB_APPLICATION_ID)
            ->table('express_order eo')
			->joinon($user, $rider)
            ->call('where', $call_where)
            ->find('count(distinct eo.express_order_id) as count'/*, function($p){
            	printexit($p['query']);
            }*/);
            if( empty($find_data['count']) ){
                return $data;
            }else{
                $data['row_count'] = $find_data['count'];
                if( !empty($data['page_size']) ){
                    $data['page_count'] = ceil($data['row_count']/$data['page_size']);
                    $data['page_now'] = ceil($limit[0]/$data['page_size']) + 1;//当前页数
                }
            }
            
			if( empty($select) ){
				$user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
				$rider_user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("rider");
                $select = array(
                    'u.user_parent_id',
                    'u.user_nickname',
                    'u.user_logo_image_id',
                    "u.user_compellation",
                    "u.user_qq",
                    "u.user_email",
                    '('.$user_phone_verify_list_sql.') as user_phone_verify_list',
                    "rider.user_logo_image_id as rider_user_logo_image_id",
                    "rider.user_nickname as rider_user_nickname",
                    "rider.user_compellation as rider_user_compellation",
                    "rider.user_qq as rider_user_qq",
                    "rider.user_email as rider_user_email",
                    '('.$rider_user_phone_verify_list_sql.') as rider_user_phone_verify_list',
                    'eo.*',
                );
            }
			
			
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('express_order eo')
			->joinon($user, $rider)
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);
            
            return $data;
        });
        
    }

    /**
     * 获取订单总量
     * Undocumented function
     *
     * @param array $where
     * @return void
     */
    public function get_count($where=[])
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($where), function($where){
            $find_data = db(parent::DB_APPLICATION_ID)
                ->table('express_order')
                ->call('where', $where)
                // ->find('express_order_id as id,count(express_order_id) as count',function($query){
                //     printexit($query);
                // });
                ->find('express_order_id as id,count(express_order_id) as count');
            return $find_data;
        });
    }


		
	/**
	 * 获取一个数据的详情
	 * 
	 * @param	string	$express_order_id
	 * @param	array	$config
	 * @return	array
	 */
	public function find_details($express_order_id, $config = array()){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($express_order_id, $config), function($express_order_id, $config){
			$where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
			$find = isset($config['find']) && is_array($config['find'])? $config['find'] : array();
			
			if( empty($find) ){
				$user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
				$rider_user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("rider");
				$find = array(
					"u.user_logo_image_id",
                    "u.user_nickname",
                    "u.user_compellation",
                    "u.user_qq",
                    "u.user_email",
                    '('.$user_phone_verify_list_sql.') as user_phone_verify_list',
                    "rider.user_logo_image_id as rider_user_logo_image_id",
                    "rider.user_nickname as rider_user_nickname",
                    "rider.user_compellation as rider_user_compellation",
                    "rider.user_qq as rider_user_qq",
                    "rider.user_email as rider_user_email",
                    '('.$rider_user_phone_verify_list_sql.') as rider_user_phone_verify_list',
					"eo.*",
				);
			}
			
			
			//用户数据
			$user = array(
				'table' => 'user u',
				'type' => 'left',
				'on' => 'u.user_id = eo.user_id'
			);
			
			
			//骑手数据
			$rider = array(
				'table' => 'user rider',
				'type' => 'left',
				'on' => 'rider.user_id = eo.express_rider_user_id'
			);
			
			
			$data = db(parent::DB_APPLICATION_ID)
			->table('express_order eo')
			->groupby("eo.express_order_id")
			->joinon($user, $rider)
			->where(array('eo.express_order_id=[+]', (string)$express_order_id))
			->call('where', $where)
			->find($find);
			
			//获取订单商品数据
			if( !empty($data['express_order_id']) ){
				$pay_method_list = $this->get_method();
				if( isset($pay_method_list[$data['express_order_pay_method']]) ){
					$data['express_order_pay_method_name'] = $pay_method_list[$data['express_order_pay_method']];
				}
			}
			
			return $data;
		});
		
	}
	


    /**
     * limit 获取一条
     * Undocumented function
     *
     * @return void
     */
    public function find_one($where=[],$rand)
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($where,$rand), function($where,$rand){
            $find_data = db(parent::DB_APPLICATION_ID)
                ->table('express_order')
                ->call('where', $where)
                ->limit($rand,1)
                ->select();
            return $find_data;
        });
    }



    /**
     * 根据条件获取一条
     *
     * @return void
     */
    public function find($express_order_id='', $find = array()){
        if(empty($find)){
            $find = array(
                "*"
            );
        }
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($express_order_id, $find), function($express_order_id, $find){
            $find_data = db(parent::DB_APPLICATION_ID)
            ->table('express_order')
            ->where(array('express_order_id =[+]',$express_order_id))
            ->find($find);
            return $find_data;
        });
    }




    /**
     * 根据条件获取一条
     * Undocumented function
     *
     * @return void
     */
    public function find_where($call_where=array(),$select=array())
    {
        if(empty($select)){
            $select = array(
                "*"
            );
        }
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where,$select), function($call_where,$select){
            $find_data = db(parent::DB_APPLICATION_ID)
                ->table('express_order')
                ->call('where', $call_where)
                ->find($select);
            return $find_data;
        });
    }

    /**
     * 
     * 根据主键，修改某条数据
     * Undocumented function
     * 
     * @return void
     */
    public function update_one($express_order_id='',$data=array())
    {
        if(!$express_order_id)
            return false;
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('express_order')
            ->where(array('express_order_id =[+]', $express_order_id))
            ->update($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    /**
     * 获取某字段总数
     * Undocumented function
     * 
     * @param [type] $where
     * @param [type] $field
     * @return void
     */
    public function find_count($where=array(),$field='')
    {
        if( empty($where) || empty($field)){
            return false;
        }
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($where,$field), function($where,$field){
            return db(parent::DB_APPLICATION_ID)
            ->table('express_order')
            ->call('where', $where)
            ->find("count($field) as count");
        });
    }




    /**
     * 统一下单
     * Undocumented function
     *
     * @return void
     */
    public function create_order( $insert = array(), $express_order_id = '', $update_data = array() ){

        //  事务处理（表内开启关闭事务）
        
        //开启事务
        db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");
		
        //新增订单
		$bool = object(parent::TABLE_ORDER)->insert($insert);
       /* $bool = db(parent::DB_APPLICATION_ID)
                ->table('order')
                ->insert($insert);*/

        //新增失败，回滚        
        if(!$bool){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            return false;
        }
		
		
        //修改业务订单状态
        $express_order_bool = db(parent::DB_APPLICATION_ID)
						        ->table('express_order')
						        ->where(array('express_order_id =[+]', $express_order_id)) 
						        ->update($update_data);

        if(!$express_order_bool){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            return false;
        }


        //提交事务
        db(parent::DB_APPLICATION_ID)->query('COMMIT');
        //清除缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);

        return true;
        // if(){
            
        //     object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
        //     throw new error('支付方式错误');
        // }
        // if(object(parent::TABLE_EXPRESS_ORDER)->update_one($express_order['express_order_id'],$insert_data)){
        //     //验证支付金额
        //     if($pay_money == 0){
        //         object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
        //         db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
        //         return ['state'=>0];//不需要支付的情况
        //     }else{
        //         if ($input['pay_method'] === parent::PAY_METHOD_ALIPAY || $input['pay_method'] === parent::PAY_METHOD_WEIXINPAY){    //支付方式为支付宝或者微信支付
        //             $return_api['state'] = 1;   //需支付状态
        //             db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
        //             object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
        //             return $return_api;
        //         } 
                
        //     }
        // // }
        // db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
        // object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
        // throw new error('支付方式错误');

    }

    /**
     * 支付回调
     * Undocumented function
     *
     * @return void
     */
    public function pay_by_third_party($order_data=array())
    {
        // 锁表
        $lock_ids = array();

        // 开启事务
        db(parent::DB_APPLICATION_ID)->query('START TRANSACTION');

        // 获取order表订单，
        $order = object(parent::TABLE_ORDER)->find($order_data['order_id']);

        if(!$order){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁 
            return false;
        }

        $where = array(
            array('express_order_id =[+]',$order['order_sign']),
            array('order_id =[+]',$order['order_id'])
        );

        $express_order = $this->find_where($where);
        if(!$express_order){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁 
            return false;
        }
        $time = time();

        //更新订单状态
        $bool_order = db(parent::DB_APPLICATION_ID)
            ->table('order')
            ->where(array('order_id=[+]', $order['order_id']))
            ->where(array('order_state=1'))
            ->where(array('order_pay_state=0'))
            ->update(array(
                'order_minus_transaction_id' => $order['order_minus_transaction_id'],
                'order_pay_state' => 1,
                'order_pay_time' => $time,
            ));

        $bool_express_order = db(parent::DB_APPLICATION_ID)
            ->table('express_order')
            ->where(array('order_id=[+]', $order['order_id']))
            ->where(array('express_order_id=[+]',$express_order['express_order_id']))
            ->where(array('express_order_pay_state=0'))
            ->update(array(
                'express_order_pay_method' => $order['order_minus_method'],
                'express_order_pay_state' => 1,
                'express_order_pay_time' => $time,
                'express_order_update_time'=>$time
            ));

        if(!$bool_order || !$bool_express_order){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁 
            return false;
        } 

        $order_plus_method = "";
        $order_plus_account_id = "";
        $order_plus_transaction_id = "";
        
        //是否有代理，以及代理提成 
        if($express_order['user_parent_id'] && !$express_order['user_coupon_id'] && $express_order['express_order_agent_royalty'] > 0)
        {  

            //  代理是否存在
            $where = array(
                array('user_id =[+]',$express_order['user_parent_id']),
                array('agent_user_state = 1')
            );
            $agent = object(parent::TABLE_AGENT_USER)->find_where($where);
            if(empty($agent)){
                db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
                object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁 
                return false;
            } 
            //  锁表，用户钱包
            $lock_id_money = object(parent::TABLE_LOCK)->start('user_id', $express_order['user_parent_id'], parent::LOCK_MONEY);
            if ($lock_id_money) {
                $lock_ids[] = $lock_id_money;
            } else {
                db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
                object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁 
                return false;
            }
            $order_plus_method = "user_money";
            //  用户账户表，数据更新
            $insert_money = array(
                'user_id'=>$_SESSION['user_id'],
                'user_money_plus'=>$express_order['express_order_agent_royalty'],
                'user_money_type'=>parent::TRANSACTION_TYPE_EXPRESS_ORDER_AWARD//交易类型--代理提成--需要定义
            );
            if($user_money_id = object(parent::TABLE_USER_MONEY)->insert_plus($insert_money)){
                $order_plus_transaction_id = $user_money_id;
                //  order表--提成数据;
                $insert = array(
                    'order_id'=>$this->get_unique_id(12),
                    'order_type'=>parent::TRANSACTION_TYPE_EXPRESS_ORDER_AWARD,//类型--用户提成--需要定义
                    'order_comment'=>'用户下单，代理提成',
                    'order_plus_method'=>$order_plus_method,
                    'order_plus_account_id'=>$order_plus_account_id,
                    'order_plus_value'=>$express_order['express_order_agent_royalty'],
                    'order_plus_transaction_id'=>$order_plus_transaction_id,//用户钱包id
                    'order_plus_update_time'=>$time,
                    'order_action_user_id'=>$_SESSION['user_id'],
                    'order_sign'=>$express_order['id'],
                    'order_json'=>$order['order_json'],
                    'order_pay_state'=>1,
                    'order_pay_time'=>$time,
                    'order_insert_time'=>$time,
                ); 
                if(object(parent::TABLE_ORDER)->insert($insert)){
                    db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
                    object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
                    object(parent::CACHE)->clear(self::CACHE_KEY);// 清除缓存
                    return true;
                }
            }
        }else{
            if($express_order['user_coupon_id']){
                $user_coupon_where = array();
                $user_coupon_where[] = array('[and] user_coupon_id=[+]', $express_order['user_coupon_id']);
                $user_coupon_where[] = array('[and] user_id=[+]',$express_order['user_id']);

                $user_coupon = object(parent::TABLE_USER_COUPON)->find_where($user_coupon_where);
                if(empty($user_coupon)){
                    db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
                    object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁 
                    return false;
                }

                // 可使用次数
                $number = $user_coupon['user_coupon_number'];
                // 已使用次数
                $use_number = $user_coupon['user_coupon_use_number'];

                $update_data = array(
                    'user_coupon_state' => $use_number+1 == $number ? 0 : 1,
                    'user_coupon_use_number' => $use_number+1,
                    'user_coupon_use_time' => $time,
                    'user_coupon_update_time'=>$time
                );
                if($number>0 && $number>$use_number){
                    $bool_user_coupon = object(parent::TABLE_USER_COUPON)->update($user_coupon_where,$update_data);
                }

                // $bool_user_coupon = db(parent::DB_APPLICATION_ID)
                // ->table('user_coupon')
                // ->where(array('user_coupon_id=[+]', $express_order['user_coupon_id']))
                // ->where(array('user_id=[+]',$express_order['user_id']))
                // ->where(array('user_coupon_state=1'))
                // // ->where(array('user_coupon_use_state=0'))
                // ->update(array(
                //     'user_coupon_use_state' => 1,
                //     'user_coupon_use_time' => $time,
                //     'user_coupon_update_time'=>$time
                // ));

                if(isset($bool_user_coupon) && !$bool_user_coupon){
                    db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
                    object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁 
                    return false;
                }
            }
            db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            object(parent::CACHE)->clear(self::CACHE_KEY);// 清除缓存
            return true;
        }
    }


    /**
     * 快递100回调--修改订单物流状态
     * Undocumented function
     *
     * @param array $data
     * @return void
     */
    public function update_notify_shipping_state($data=array())
    {
        if(empty($data['lastResult']['nu'])){return false;}
        $express_order_shipping_no = $data['lastResult']['nu'];
        $where = array(
            array('express_order_shipping_no =[+]',$express_order_shipping_no)
        );
        if($express_order = $this->find_where($where)){
            $order_json = cmd(array($express_order['express_order_json']),'json_decode');
            if($data['status'] == 'polling' || $data['status'] == 'shutdown')
            {
                $time = time();
                $order_json['express_record'] = $data['lastResult']['data'];
                $update_data = array(
                    'express_order_json'=>cmd(array($order_json),'json_encode'),
                );
                if($data['status'] == 'shutdow'){
                    $update_data['express_order_shipping_state'] = 1;
                    $update_data['express_order_shipping_take_time'] = $time;
                }
                
                $bool = db(parent::DB_APPLICATION_ID)
                    ->table('express_order')
                    ->where(array('express_order_id =[+]', $express_order['express_order_id']))
                    ->update($update_data);

                //清理缓存
                if ($bool)
                    object(parent::CACHE)->clear(self::CACHE_KEY);

                return $bool;
            }
        }
    }



    /**
     * -------Mr.Zhao------2019.08.07------
     * 快递100查询物流状态
     * @param   array   订单数据
     * @return  array   物流信息
     */
    public function get_kuaidi100_messgae($express_order=array())
    {
        
        // 调用快递100接口要用的参数
        $req = array();

        // 快递运单号
        $number = $express_order['shipping_no'];
        if (!$number) {
            return array();
            // throw new error('快递运单号为空');
        }

        $req['number'] = $number;

        // 快递名称英文
        $req['conpany'] = $express_order['express_sign'];

        // 寄件人电话
        $req['phone'] = $express_order['send_phone'];

        // 寄件人省
        $from_province = $express_order['send_province'];
        // 寄件人市
        $from_city = $express_order['send_city'];
        // 寄件人区
        $from_district = $express_order['send_district'];

        // 出发地城市
        $req['from'] = $from_province . $from_city . $from_district;

        // 收件人省
        $toprovince = $express_order['get_province'];
        // 收件人市
        $tocity = $express_order['gey_city'];
        // 收件人区
        $todistrict = $express_order['get_district'];

        // 目的地城市
        $req['to'] = $toprovince . $tocity . $todistrict;


        $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("kuaidi100_access"), true);

        if (empty($config)) {
            throw new error('快递100配置信息异常');
        }

        // 快递100分配的公司编号
        $req['customer'] = $config['customer'];

        // 授权码
        $req['key'] = $config['key'];
        // printexit($req);

        // 添加此字段表示开通行政区域解析功能
        $req['resultv2'] = 1;

        $response = object(parent::PLUGIN_KUAIDI100)->real_time_query($req);

        return $response;

    }



}