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



namespace eapie\source\table\shop;
use eapie\main;
class shop_group_order extends main {
	
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, "shop_goods", "type");
	
	
	
	
	/**
	 * 数据检测
	 * 
	 * @var	array
	 */
	public $check = array(
		'shop_goods_type_id' => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少产品分类ID参数"),
					'echo'=>array("产品分类ID数据类型不合法"),
					'!null'=>array("产品分类ID不能为空"),
					),
			//检查编号是否存在		
			'exists_id'=>array(
					'method'=>array(array(parent::TABLE_SHOP_GOODS_TYPE, 'find_exists_id'), "产品分类ID有误，数据不存在",) 
            ),
		),
        'user_address_id' => array(
            'args' => array(
                'exist'=> array('缺少收货地址ID参数'),
                'echo' => array('收货地址ID数据类型不合法'),
                '!null'=> array('收货地址ID不能为空'),
            ),
            'exist' => array(
                'method'=>array(array(parent::TABLE_USER_ADDRESS, 'find'), '收货地址ID有误，数据不存在')
            ),
        ),
        'pay_method' => array(
            'args'=>array(
                'exist' => array('缺少支付方式参数'),
                'echo'  => array('支付方式的数据类型不合法'),
                '!null' => array('支付方式不能为空'),
                'method'=> array(array(parent::TABLE_SHOP_ORDER, 'check_pay_method'), '支付方式不合法')
            ),
        ),
        'group_id' => array(
            'args'=>array(
                'exist' => array('缺少拼团ID参数'),
                'echo'  => array('拼团ID类型不合法'),
                '!null' => array('拼团ID不能为空'),
                'method'=> array(array(parent::TABLE_SHOP_GROUP_GOODS, 'has_group'), '拼团ID不存在')
            ),
        ),
        'group_order_id' => array(
            'args' => array(
                'exist'=> array('缺少拼团订单ID参数'),
                'echo' => array('拼团订单ID数据类型不合法'),
                '!null'=> array('拼团订单ID不能为空'),
            ),
            'exist' => array(
                'method'=>array(array(parent::TABLE_SHOP_GROUP_ORDER, 'find_group'), '拼团订单ID有误，数据不存在')
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
     * 根据拼团订单ID，查询拼团ID
     * @param string $group_order_id 拼团订单ID
     * @return string $group_id 拼团ID
     */
    public function find_group($group_order_id){
        $group = db(parent::DB_APPLICATION_ID)
            ->table('shop_group_order')
            ->where(array('shop_group_order_id =[+]' ,$group_order_id))
            ->find('shop_group_goods_id');
        return $group;
    }

    /**
     * 查询拼团商品的goods_sku_id
     * @param string $goods_id 商品ID
     * @return string $return 商品SKUID
     */

    public function get_goods_sku_id($goods_id){
        $sku = db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_sku')
            ->where(array('shop_goods_id=[+]', $goods_id))
            ->find('shop_goods_sku_id');
        return $sku['shop_goods_sku_id'];
    }

    /**
     * 创建订单
     * @param  array  $order            [订单]
     * @param  array  $group_order      [商城拼团订单]
     * @return bool
     */
    public function create_order($order = array(), $group_order = array(), $shop_order_goods = array())
    {
        //开启事务
        db(parent::DB_APPLICATION_ID)->query('START TRANSACTION');

        //——插入订单记录——
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('order')
            ->insert($order);

        //回滚
        if (!$bool) {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            return false;
        }

        //——插入商城订单记录——
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_group_order')
            ->insert($group_order);

        //回滚
        if (!$bool) {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            return false;
        }

        //提交事务
        db(parent::DB_APPLICATION_ID)->query('COMMIT');
        //清除缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);

        return true;
    }

    /**
     * 按拼团ID查询所有订单数据
     * @param string $group_id
     * @param array $order 所有成功的订单数据
     */
    public function select_all_success_group_order($group_id){
        $order = db(parent::DB_APPLICATION_ID)
            ->table('shop_group_order')
            ->where(
                array('shop_group_goods_id=[+]', $group_id),
                array('shop_group_order_pay_state=1')
            )
            ->select();
        return $order;
    }

    /**
     * 拼团订单数据进入商城商品订单表
     * @param string $group_id 拼团成功的拼团ID
     * @return boolean $result  
     */
    public function add_success_group_order($group_id){
        //查询数据查询到拼团成功

        //选取所有拼团成功的拼团订单
        $cache = $this->select_all_success_group_order($group_id);
        //初始化订单
        $order = array();

        //序列化订单数据结构
        foreach ($cache as $key => $value) {
            // 判断为积分订单或商品订单
            $property = (int)$value['shop_group_order_property'];
            if($property === 0){
                // 普通商品
                $order['money_order'][] = array(
                    'shop_order_id' => $value['shop_order_id'],
                    'user_id' => $value['user_id'],
                    'shop_order_state' => 1,
                    'shop_order_json' => $value['shop_group_order_json'],
                    'shop_order_money' => $value['shop_group_order_price'],
                    'shop_order_goods_price' => $value['shop_group_order_price'],
                    'shop_order_discount_money' => 0,
                    'shop_order_pay_state' => 1,
                    'shop_order_pay_money_order_id' => $value['order_id'],
                    'shop_order_pay_parent' => 0,
                    'shop_order_pay_money_method' => $value['shop_order_pay_method'],
                    'shop_order_pay_money' => $value['shop_order_pay_price'],
                    'shop_order_pay_money_state' => 1,
                    'shop_order_pay_money_time' => $value['shop_group_order_pay_time'],
                    'shop_order_shipping_state' => 0,
                    'shop_order_update_time' => time(),
                    'shop_order_insert_time' => time(),
                    'user_address_consignee' => $value['user_address_consignee'],
                    'user_address_tel' => $value['user_address_tel'],
                    'user_address_phone' => $value['user_address_phone'],
                    'user_address_country' => $value['user_address_country'],
                    'user_address_province' => $value['user_address_province'],
                    'user_address_city' => $value['user_address_city'],
                    'user_address_district' => $value['user_address_district'],
                    'user_address_details' => $value['user_address_details'],
                    'user_address_zipcode' => $value['user_address_zipcode'],
                    'user_address_email' => $value['user_address_email'],
                    'shop_order_trash' => 0,
                );
            } elseif($property === 1) {
                // 积分商品订单
                $order['credit_order'][] = array(
                    'shop_order_id' => $value['shop_order_id'],
                    'user_id' => $value['user_id'],
                    'shop_order_state' => 1,
                    'shop_order_json' => $value['shop_group_order_json'],
                    'shop_order_credit' => $value['shop_group_order_price'],
                    'shop_order_goods_price' => $value['shop_group_order_price'],
                    'shop_order_discount_money' => 0,
                    'shop_order_pay_state' => 1,
                    'shop_order_pay_credit_order_id' => $value['order_id'],
                    'shop_order_pay_parent' => 0,
                    'shop_order_pay_credit_method' => $value['shop_order_pay_method'],
                    'shop_order_pay_credit' => $value['shop_order_pay_price'],
                    'shop_order_pay_credit_state' => 1,
                    'shop_order_pay_credit_time' => $value['shop_group_order_pay_time'],
                    'shop_order_shipping_state' => 0,
                    'shop_order_update_time' => time(),
                    'shop_order_insert_time' => time(),
                    'user_address_consignee' => $value['user_address_consignee'],
                    'user_address_tel' => $value['user_address_tel'],
                    'user_address_phone' => $value['user_address_phone'],
                    'user_address_country' => $value['user_address_country'],
                    'user_address_province' => $value['user_address_province'],
                    'user_address_city' => $value['user_address_city'],
                    'user_address_district' => $value['user_address_district'],
                    'user_address_details' => $value['user_address_details'],
                    'user_address_zipcode' => $value['user_address_zipcode'],
                    'user_address_email' => $value['user_address_email'],
                    'shop_order_trash' => 0,
                );
            }
        }
        unset($cache);
        //存入商城订单表


        // 锁表
        $lock_ids = array();

        // 开启事务
        db(parent::DB_APPLICATION_ID)->query('START TRANSACTION');
        
        if (!empty($order['credit_order'])) {
            //——插入商城积分商品订单记录——
            $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_order')
            ->batch()
            ->insert($order['credit_order']);

            //回滚
            if (!$bool) {
                db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                return false;
            }
        }

        if (!empty($order['money_order'])) {
            //——插入商城积分商品订单记录——
            $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_order')
            ->batch()
            ->insert($order['money_order']);

            //回滚
            if (!$bool) {
                db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                return false;
            }
        }
        

        // 拼团成功减库存

        $cache = object(parent::TABLE_SHOP_GROUP_GOODS)->find($group_id);
        $goods_id = $cache['shop_goods_id'];
        $num = count($order);
        

        $sku_id = object(parent::TABLE_SHOP_GROUP_ORDER)->get_goods_sku_id($goods_id);

        $bool = object(parent::TABLE_SHOP_GOODS_SKU)->decrease_stock($sku_id, $num);

        //回滚
        if (!$bool) {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            return false;
        }

        //提交事务
        db(parent::DB_APPLICATION_ID)->query('COMMIT');

        //清除缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);

        return array('count' => count($order));
    }

    /**
     * 拼团订单列表数据查询
     * @param array $config
     * @return array $data
     */
    public function select_page($config = array()){
        //查询配置
        $call_where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
        $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
        $call_limit   = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
        $select       = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();
        
        $limit = array(
            (isset($call_limit[0]) ? $call_limit[0] : 0),
            (isset($call_limit[1]) ? $call_limit[1] : 0)
        );
        
        //设置返回的数据
        $data = array(
            'row_count'   => 0,
            'limit_count' => $limit[0] + $limit[1],
            'page_size'   => $limit[1],
            'page_count'  => 0,
            'page_now'    => 0,
            'data'        => array()
        );
        
       //左连拼团商品表
       $join_group_goods = array(
            'table' => 'shop_group_goods sgg',
            'type' => 'left',
            'on' => 'sgg.shop_group_goods_id = sgo.shop_group_goods_id'
        );

        // user表
        $join_user = array(
            'table' => 'user u',
            'type' => 'left',
            'on' => 'sgo.user_id = u.user_id'
        );

        // user表
        $join_user_phone = array(
            'table' => 'user_phone up',
            'type' => 'left',
            'on' => 'sgo.user_id = up.user_id'
        );

        //获取总条数
        $total_count = db(parent::DB_APPLICATION_ID)
            ->table('shop_group_order sgo')
            ->call('where', $call_where)
            ->find('count(*) as count');

        if (empty($total_count['count'])) {
            return $data;
        } else {
            $data['row_count'] = $total_count['count'];
            if (!empty($data['page_size'])) {
                $data['page_count'] = ceil($data['row_count']/$data['page_size']);
                $data['page_now'] = ceil($limit[0]/$data['page_size']) + 1;//当前页数
            }
        }
        
        //查询数据
        $data['data'] = db(parent::DB_APPLICATION_ID)
            ->table('shop_group_order sgo')
            ->joinon($join_group_goods)
            ->joinon($join_user)
            ->joinon($join_user_phone)
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);
        $cache = array();

        //循环加入商品数据
        for($i = 0; $i < count($data['data']); $i++){
            $cache[$i] = db(parent::DB_APPLICATION_ID)
                ->table('shop_goods')
                ->where(array('shop_goods_id=[+]',$data['data'][$i]['goods_id']))
                ->find();

            if(isset($cache[$i]['shop_goods_name']) && $cache[$i]['shop_goods_name'] !== null && $cache[$i]['shop_goods_name'] !== ''){
                $data['data'][$i]['shop_goods_name'] = $cache[$i]['shop_goods_name'];
            }
        }
        return $data;
    }

    /**
     * 查询单条拼团订单数据
     * @param string $gourp_order_id [拼团订单ID]
     * @param array $data [单条拼团订单数据]
     */
    public function find($group_order_id){
        $data = db(parent::DB_APPLICATION_ID)
            ->table('shop_group_order')
            ->where(array('shop_group_order_id=[+]', $group_order_id))
            ->find();

        return $data;
    }

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
        ->table('shop_group_order')
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
     * 单条拼团订单数据查询带拼团商品数据
     * @param  array $config
     * @return array $data
     */
    public function find_join_group_goods($config = array()) {
		$where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
		$orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
		$limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
		$find = isset($config['find']) && is_array($config['find'])? $config['find'] : array();
			
        //左连拼团商品表
        $join_group_goods = array(
            'table' => 'shop_group_goods gg',
            'type' => 'left',
            'on' => 'gorder.shop_group_goods_id = gg.shop_group_goods_id'
        );
        
        //查询数据
        $data = db(parent::DB_APPLICATION_ID)
            ->table('shop_group_order gorder')
            ->joinon($join_group_goods)
			->call('where', $where)
            ->find($find);
		if( empty($data['goods_id']) ){
			return false;
		} 
		    
        //查询商品数据
        $cache = db(parent::DB_APPLICATION_ID)
            ->table('shop_goods')
            ->where(array('shop_goods_id=[+]', $data['goods_id']))
            ->find(array('shop_goods_name'));
			
        if(isset($cache['shop_goods_name']) && $cache['shop_goods_name'] !== null && $cache['shop_goods_name'] !== ''){
            $data['shop_goods_name'] = $cache['shop_goods_name'];
        }
        return $data;
    }
    
    /**
     * 支付——用户钱包
     * @param  array $order_id [订单ID]
     * @return bool
     */
    public function pay_by_user_money($order_id = '', $user_money = array())
    {
        $order = db(parent::DB_APPLICATION_ID)
            ->table('order')
            ->where(array('order_id=[+]', $order_id))
            ->find();

        return $this->pay_by_third_party($order, $user_money);
    }

    /**
     * 支付——第三方
     * @param  array $order         [订单数据]
     * @param  bool  $user_money    [用户钱包数据]
     * @return bool
     */
    public function pay_by_third_party($order = array(), $user_money = null)
    {
        // 锁表
        $lock_ids = array();

        // 开启事务
        db(parent::DB_APPLICATION_ID)->query('START TRANSACTION');

        // ——是否用户钱包支付——
        if ($user_money) {
            // 锁表，用户钱包
            $lock_id_money = object(parent::TABLE_LOCK)->start('user_id', $order['order_minus_account_id'], parent::LOCK_MONEY);
            if ($lock_id_money) {
                $lock_ids[] = $lock_id_money;
            } else {
                return false;
            }

            // 更新用户钱包数据
            $user_money_id = object(parent::TABLE_USER_MONEY)->insert_minus(array(
                'user_id' => $order['order_action_user_id'],
                'user_money_join_id' => $user_money['user_money_id'],
                'user_money_minus' => $order['order_minus_value'],
                'user_money_value' => $user_money['user_money_value'] - $order['order_minus_value'],
                'user_money_type' => parent::TRANSACTION_TYPE_SHOP_ORDER,
            ));

            // 回滚事务，关闭锁
            if (!$user_money_id) {
                db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                object(parent::TABLE_LOCK)->close($lock_ids);
                return false;
            }

            $order['order_minus_method'] = parent::PAY_METHOD_USER_MONEY;
            $order['order_minus_transaction_id'] = $user_money_id;
        }

        // ——更新订单——
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('order')
            ->where(array('order_id=[+]', $order['order_id']))
            ->where(array('order_state=1'))
            ->where(array('order_pay_state=0'))
            ->update(array(
                'order_minus_transaction_id' => $order['order_minus_transaction_id'],
                'order_pay_state' => 1,
                'order_pay_time' => time(),
            ));

        // 回滚事务，关闭锁
        if (!$bool) {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            object(parent::TABLE_LOCK)->close($lock_ids);
            return false;
        }

        // ——更新商城订单——
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_group_order')
            ->where(array('shop_order_id=[+]', $order['order_sign']))
            ->update(array(
                'shop_order_pay_method' => $order['order_minus_method'],
                'shop_order_pay_price' => $order['order_minus_value'],
                'shop_group_order_pay_state' => 1,
                'shop_group_order_pay_time' => time(),
                'shop_group_order_update_time' => time(),
            ));

        // 回滚事务，关闭锁
        if (!$bool) {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            object(parent::TABLE_LOCK)->close($lock_ids);
            return false;
        }

        // 拼团成功减库存，这里不减

        // 提交事务
        db(parent::DB_APPLICATION_ID)->query('COMMIT');
        // 关闭锁
        object(parent::TABLE_LOCK)->close($lock_ids);
        // 清除缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);

        return true;
    }

    /**
     * 通过资金ID,查找拼团订单
     */
    public function find_group_order_by_order_id($order_id){
        $data = db(parent::DB_APPLICATION_ID)
        ->table('shop_group_order')
        ->where(array('order_id=[+]', $order_id))
        ->find();

        return $data;
    }
}