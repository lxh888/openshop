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

//商城订单
class shop_order extends main
{


    /**
     * 缓存的键列表
     * @var string
     */
    const CACHE_KEY = array(__CLASS__, 'shop_goods', 'shop_goods_sku');


    /**
     * 数据检测
     * @var array
     */
    public $check = array(
        'shop_order_id' => array(
            'args' => array(
                'exist' => array('缺少订单ID参数'),
                'echo'  => array('订单ID的数据类型不合法'),
                '!null' => array('订单ID不能为空'),
            ),
        ),
        'shop_order_state' => array(
            //订单状态。0取消订单，1确认订单
            'args' => array(
                'exist' => array('缺少订单状态的参数'),
                'echo' => array("订单状态的数据类型不合法"),
                'match' => array('/^[01]{1}$/', "订单状态值必须是0或1"),
            ),
        ),
        'shop_order_buyer_note' => array(
            'args' => array(
                'exist' => array('缺少买家留言参数'),
                'echo' => array('买家留言数据类型不合法'),
            ),
        ),
        'shop_order_seller_note' => array(
            'args' => array(
                'exist' => array('缺少卖家留言参数'),
                'echo' => array('卖家留言数据类型不合法'),
            ),
        ),
        'pay_method' => array(
            'args' => array(
                'exist' => array('缺少支付方式参数'),
                'echo'  => array('支付方式的数据类型不合法'),
                '!null' => array('支付方式不能为空'),
                'method' => array(array(parent::TABLE_SHOP_ORDER, 'check_pay_method'), '支付方式不合法')
            ),
        ),
        'shop_order_shipping_no' => array(
            'args' => array(
                'exist' => array('缺少物流运单号参数'),
                'echo' => array('物流运单号的数据类型不合法'),
            ),
        ),
        'shop_order_shipping_state' => array(
            //状态。0未发货，等待发货；1确认收货; 2已发货，运送中
            'args' => array(
                'echo' => array("配送状态的数据类型不合法"),
                'match' => array('/^[0123]{1}$/', "配送状态值必须是0、1、2、3"),
            ),
        ),
        'shop_order_shipping_id' => array(
            'args' => array(
                'exist' => array('缺少发货物流ID参数'),
                'echo'  => array('发货物流ID的数据类型不合法'),
                '!null' => array('发货物流ID不能为空'),
            ),
        ),
        'shop_order_shipping_sign' => array(
            'args' => array(
                'exist' => array('缺少发货物流标识参数'),
                'echo'  => array('发货物流标识数据类型不合法'),
                '!null' => array('发货物流标识不能为空'),
            ),
        ),
    );


    /**
     * 获取一个id号
     * @return  string
     */
    public function get_unique_id()
    {
        return cmd(array(12), 'random autoincrement');
    }


    /**
     * 获取支付方式
     * @return  array
     */
    public function get_pay_method()
    {
        return array(
            parent::PAY_METHOD_WEIXINPAY => '微信支付',
            parent::PAY_METHOD_ALIPAY => '支付宝支付',
            parent::PAY_METHOD_USER_MONEY => '用户钱包',
            parent::PAY_METHOD_USER_CREDIT => '用户积分',
        );
    }


    //===========================================
    // 操作
    //===========================================


    public function insert($data = array(), $call_data = array())
    {
        if (empty($data) && empty($call_data))
            return false;

        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_order')
            ->call('data', $call_data)
            ->insert($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    public function update($call_where = array(), $data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_order')
            ->call('where', $call_where)
            ->call('data', $call_data)
            ->update($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


   
	/**
	 * 更新json数据
	 * 
	 * @param	string		$shop_order_id						订单ID
	 * @param	array		$shop_order_json						
	 * @return	bool
	 */
	public function update_json($shop_order_id, $shop_order_json = array()){
		//修改订单支付状态
        $where = array();
        $where[] = array("shop_order_id=[+]", $shop_order_id);
        $data = array();
		if( !empty($shop_order_json) ){
			$data['shop_order_json'] = cmd(array($shop_order_json), "json encode");
		}else{
			$data['shop_order_json'] = "";
		}
		
        return (bool)db(parent::DB_APPLICATION_ID)
        ->table('shop_order')
        ->call('where', $where)
        ->update($data);
	}
	

    /**
     * 创建订单
     * 
     * @param  array  $shop_order       [商城订单]
     * @param  array  $shop_order_goods [商城订单商品]
     * @return bool
     */
    public function found($shop_orders = array(), $shop_order_goods = array(), $sku_decrease_stock = array())
    {
        //开启事务
        db(parent::DB_APPLICATION_ID)->query('START TRANSACTION');

        //插入商城订单记录
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_order')
            ->batch()
            ->insert($shop_orders);

        //回滚
        if (empty($bool)) {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            return false;
        }

        //插入商城订单商品记录
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_order_goods')
            ->batch()
            ->insert($shop_order_goods);

        //回滚
        if (empty($bool)) {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            return false;
        }
		
		// ------------- 注意，这里不再减库存，走事件那边减库存
		
        //下单减库存
        /*if (!empty($sku_decrease_stock)) {
            foreach ($sku_decrease_stock as $val) {
                $bool = object(parent::TABLE_SHOP_GOODS_SKU)->decrease_stock($val['shop_goods_sku_id'], $val['shop_order_goods_number']);
                //回滚
                if (empty($bool)) {
                    db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                    return false;
                }
            }
        }*/

        //提交事务
        db(parent::DB_APPLICATION_ID)->query('COMMIT');
        object(parent::CACHE)->clear(self::CACHE_KEY);
        return true;
    }




    /**
     * ------Mr.Zhao------2019.07.26------
     * 
     * 创建订单(加核销优惠券)
     * 
     * @param  array  $shop_order       [商城订单]
     * @param  array  $shop_order_goods [商城订单商品]
     * @return bool
     */
    public function found_with_coupon($shop_orders = array(), $shop_order_goods = array(), $sku_decrease_stock = array(), $user_coupon_update_datas = array())
    {
        //开启事务
        db(parent::DB_APPLICATION_ID)->query('START TRANSACTION');

        //插入商城订单记录
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_order')
            ->batch()
            ->insert($shop_orders);

        //回滚
        if (empty($bool)) {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            return false;
        }

        //插入商城订单商品记录
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_order_goods')
            ->batch()
            ->insert($shop_order_goods);

        //回滚
        if (empty($bool)) {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            return false;
        }
	
		// ------------- 注意，这里不再减库存，走事件那边减库存
		
        //下单减库存
        /*if (!empty($sku_decrease_stock)) {
            foreach ($sku_decrease_stock as $val) {
                $bool = object(parent::TABLE_SHOP_GOODS_SKU)->decrease_stock($val['shop_goods_sku_id'], $val['shop_order_goods_number']);
                //回滚
                if (empty($bool)) {
                    db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                    return false;
                }
            }
        }*/


        // 优惠券核销
        if (!empty($user_coupon_update_datas)) {
            foreach ($user_coupon_update_datas as $key => $user_coupon_update_data) {
                $update_where = array(
                    array('user_coupon_id=[+]', $user_coupon_update_data['user_coupon_id'])
                );
                $bool = object(parent::TABLE_USER_COUPON)->update($update_where, $user_coupon_update_data);
                //回滚
                if (empty($bool)) {
                    db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                    return false;
                }
            }
        }


        //提交事务
        db(parent::DB_APPLICATION_ID)->query('COMMIT');
        object(parent::CACHE)->clear(self::CACHE_KEY);
        return true;
    }






    /**
     * 创建订单
     * @param  array  $order            [订单]
     * @param  array  $shop_order       [商城订单]
     * @param  array  $shop_order_goods [商城订单商品]
     * @return bool
     */
    public function create_order($order = array(), $shop_order = array(), $shop_order_goods = array())
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
            ->table('shop_order')
            ->batch()
            ->insert($shop_order);

        //回滚
        if (!$bool) {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            return false;
        }

        //——插入商城订单商品记录——
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_order_goods')
            ->batch()
            ->insert($shop_order_goods);

        //回滚
        if (!$bool) {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            return false;
        }

        //——下单减库存——
        foreach ($shop_order_goods as $val) {
            $json = cmd(array($val['shop_order_goods_json']), 'json decode');
            if ($json['stock_mode'] == 1) {
                $bool = object(parent::TABLE_SHOP_GOODS_SKU)->decrease_stock($val['shop_goods_sku_id'], $val['shop_order_goods_number']);

                //回滚
                if (!$bool) {
                    db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                    return false;
                }
            }
        }

        //提交事务
        db(parent::DB_APPLICATION_ID)->query('COMMIT');
        //清除缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);

        return true;
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

        /*
        //是否合并支付
        if ($shop_order['shop_order_pay_parent'] == 1) {
            //查询订单
            $shop_orders = db(parent::DB_APPLICATION_ID)
                ->table('shop_order')
                ->where('shop_order_parent_id=[+]', $shop_order['shop_order_id'])
                ->select();

            //循环订单
            foreach ($shop_orders as $val) {
                $order[] = array(
                    'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
                    'order_type' => parent::TRANSACTION_TYPE_SHOP_ORDER,
                    'order_comment' => '商城购物',
                    'order_action_user_id' => $user_id,
                    'order_plus_method' => '',
                    'order_plus_account_id' => '',
                    'order_plus_value' => '',
                    'order_minus_method' => $input['pay_method'],
                    'order_minus_account_id' => $user_id,
                    'order_minus_value' => $val['shop_order_total_money'],
                    'order_state' => 1,
                    'order_pay_state' => 1,
                    'order_insert_time' => time(),
                );
            }
        } else {
            $order[] = array(
                'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
                'order_type' => parent::TRANSACTION_TYPE_SHOP_ORDER,
                'order_comment' => '商城购物',
                'order_action_user_id' => $shop_order['user_id'],
                'order_plus_method' => '',
                'order_plus_account_id' => '',
                'order_plus_value' => '',
                'order_minus_method' => parent::PAY_METHOD_USER_MONEY,
                'order_minus_account_id' => $shop_order['user_id'],
                'order_minus_value' => $shop_order['shop_order_total_money'],
                'order_state' => 1,
                'order_pay_state' => 1,
                'order_insert_time' => time(),
            );
        }
        */
    }

    /**
     * 喜乐淘，查询分销奖励规则查询
     * @return array $data 分销奖励规则
     */
    public function xlt_distribution_reward_rule()
    {
        $data = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("xlt_distribution"), true);
        return $data;
    }

    /**
     * 喜乐淘，分销奖励
     * @param string $order_id [订单编号]
     * @return array $result [奖励数据]
     */
    public function xlt_distribution_reward($shop_order_id = null)
    {
        // 查询订单ID对应的消费者的user_id
        $user_id = $this->find($shop_order_id);
        $user_id = $user_id['user_id'];

        // 查询订单消费人的上级,查询上级**（这个是旧的筛选模型，只有这里在使用，如果出现关系链异常，那么就使用下面的模型 ---yangze）
        //$data = object(parent::TABLE_USER_RECOMMEND)->select_reward_user($user_id);

        // 新查询模型
        $data = object(parent::TABLE_USER_RECOMMEND)->select_superiro($user_id);

        // 查询奖励规则
        $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("xlt_distribution"), true);

        // 计算奖金，并序列化（喜乐淘的数据结构）
        $result = $this->xlt_reward_money($data, $config);
        return $result;
    }

    /**
     * 喜乐淘，计算上级奖励金额
     * @param array $config 分销奖励规则
     * @param array $data 用户数据
     * @return array $data
     */
    public function xlt_reward_money($data = array(), $config = array())
    {
        // 初始化缓存变量
        $status = 0;
        $transcendence_reward = 0;
        $first_is_king = false;
        $kings = array();

        for ($i = 0; $i < count($data); $i++) {
            // 查询身份ID
            $cache = object(parent::TABLE_ADMIN_USER)->find($data[$i]['user_id']);
            // 如果等级为一，并且为分销身份，直推奖励按配置发放
            if( $data[$i]['level'] == 1 ){
                // 有身份
                if (isset($cache['admin_id']) && isset($config[$cache['admin_id']])) {
                    if($cache['admin_id'] == "king"){
                        $first_is_king = true;
                    }
                    $data[$i]['admin_id'] = $cache['admin_id'];
                    $data[$i]['rmb_award'] = $config[$data[$i]['admin_id']]['rmd_award'];
                } else {
                    $data[$i]['admin_id'] = null;
                    $data[$i]['rmb_award'] = 0;
                }
            } else {
                $data[$i]['admin_id'] = null;
                $data[$i]['rmb_award'] = 0;
                // status=1 上级没有王者，超越奖发给第二级上级 记录在transcendence_reward里面
                // status=2 上级有王者，王者团队奖励只奖励两个人，(array)kings记录所有的王者，但只发放前两位
                if($data[$i]['level'] == 2 && isset($cache['admin_id']) && isset($config[$cache['admin_id']])){
                    $status = 1;
                    $transcendence_reward = $i;
                }
                if(isset($cache['admin_id']) && $cache['admin_id'] == "king"){
                    $status = 2;
                    $kings[] = $i;
                }
            }
        }

        // 超越奖判断
        if( $status == 1 ){
            $cache = object(parent::TABLE_ADMIN_USER)->find($data[$transcendence_reward]['user_id']);
            $data[$transcendence_reward]['admin_id'] = $cache['admin_id'];
            $data[$transcendence_reward]['rmb_award'] = $config['transcendence_award'];
        }

        if( $status == 2 ){
            $time = 1;
            foreach($kings as $key => $value){
                if($first_is_king && $time > 1){
                    break;
                }
                if(!$first_is_king && $time > 2){
                    break;
                }
                $cache = object(parent::TABLE_ADMIN_USER)->find($data[$value]['user_id']);
                $data[$value]['admin_id'] = $cache['admin_id'];
                $data[$value]['rmb_award'] = $config['transcendence_award'];
                $time++;
            }
        }
        return $data;
    }


    /**
     * 查询分销奖励规则查询
     * @return array $data 分销奖励规则
     */
    public function distribution_reward_rule()
    {
        $data = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("shop_distribution_reward"), true);
        return $data;
    }
    /**
     * 分销奖励
     * @param string $order_id [订单编号]
     * @return array $result [奖励数据]
     */
    public function distribution_reward($shop_order_id = null)
    {
        // 查询订单ID对应的消费者的user_id
        $user_id = $this->find($shop_order_id)['user_id'];

        // 查询订单消费人的上级（新模型查询）
        $data = object(parent::TABLE_USER_RECOMMEND)->select_superiro($user_id);
        
        // 查询奖励规则
        $config = $this->distribution_reward_rule();
        $result = $this->reward_money($data, $config);

        // 区域代理对应的奖金
        $area = array();
        $user = object(parent::TABLE_USER)->find($user_id);
        if (isset($user['user_register_province']) || isset($user['user_register_city']) || isset($user['user_register_area'])) {
            // 查询与消费用户所在区域的所有人
            $area_config = array();
            $area_config['where'] = array(
                array('user_register_province=[+]', $user['user_register_province']),
                array('[and]user_register_city=[+]', $user['user_register_city']),
                array('[and]user_register_area=[+]', $user['user_register_area'])
            );
            $area_data = object(parent::TABLE_USER)->select($area_config);

            // 循环数组
            foreach ($area_data as $key => $value) {
                // 查询身份ID
                $cache = object(parent::TABLE_ADMIN_USER)->find($value['user_id']);
                if (isset($cache['admin_id']) && $cache['admin_id'] == 'area_agent') {
                    $result[] = array(
                        'user_id' => $value['user_id'],
                        'admin_id' => $cache['admin_id'],
                        'rmb_award' => $config['area_agent']['additional_rewards'],
                    );
                }
            }
        }
        return $result;
    }

    /**
     * 计算上级奖励金额
     * @param array $config 分销奖励规则
     * @param array $data 用户数据
     * @return array $data
     * {"user_id":"上级用户ID","admin_id":"上级身份ID","level":"上级等级","rmb_award":"上级人民币奖励金额"}
     *  等级说明：0：未知，1：直推， 2 ~ 5：递推
     */
    public function reward_money($data = array(), $config = array())
    {

        for ($i = 0; $i < count($data); $i++) {
            // 取出等级，$config里面的等级数组的引索是从0开始的，保持数据位和引索一致
            $level = (int) $data[$i]['level'] - 1;
            $cache = object(parent::TABLE_ADMIN_USER)->find($data[$i]['user_id']);
            // 查询身份ID
            if (isset($cache['admin_id'])) {
                $data[$i]['admin_id'] = $cache['admin_id'];
            } else {
                $data[$i]['admin_id'] = null;
            }


            switch ($data[$i]['admin_id']) {
                // 这里不考虑区域代理
                case $config['chief_inspector']['admin_id']:
                    if (isset($config['chief_inspector']['rmb_award'][$level])) {
                        $data[$i]['rmb_award'] = $config['chief_inspector']['rmb_award'][$level];
                    } else {
                        $data[$i]['rmb_award'] = $config['chief_inspector']['rmb_award'][5];
                    }
                    break;
                case $config['member']['admin_id']:
                    if (isset($config['member']['rmb_award'][$level])) {
                        $data[$i]['rmb_award'] = $config['member']['rmb_award'][$level];
                    } else {
                        $data[$i]['rmb_award'] = 0;
                    }
                    break;
                case $config['shop_manager']['admin_id']:
                    if (isset($config['shop_manager']['rmb_award'][$level])) {
                        $data[$i]['rmb_award'] = $config['shop_manager']['rmb_award'][$level];
                    } else {
                        $data[$i]['rmb_award'] = 0;
                    }
                    break;
                default:
                    $data[$i]['rmb_award'] = 0;
                    break;
            }
        }
        // 输出
        return $data;
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
            ->table('shop_order')
            ->where(array('shop_order_id=[+]', $order['order_sign']))
            ->update(array(
                'shop_order_pay_money_method' => $order['order_minus_method'],
                'shop_order_pay_money' => $order['order_minus_value'],
                'shop_order_pay_state' => 1,
                'shop_order_pay_time' => time(),
                'shop_order_update_time' => time(),
            ));

        // 回滚事务，关闭锁
        if (!$bool) {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            object(parent::TABLE_LOCK)->close($lock_ids);
            return false;
        }

        // ——支付减库存——
        $data_shopordergoods = db(parent::DB_APPLICATION_ID)
            ->table('shop_order_goods')
            ->where(array('shop_order_id = [+]', $order['order_sign']))
            ->select(array(
                'shop_goods_sku_id',
                'shop_order_goods_number',
                'shop_order_goods_json'
            ));

        foreach ($data_shopordergoods as $val) {
            $json = cmd(array($val['shop_order_goods_json']), 'json decode');
            if ($json['stock_mode'] == 2) {
                $bool = db(parent::DB_APPLICATION_ID)
                    ->table('shop_goods_sku')
                    ->where(array('shop_goods_sku_id = [+]', $val['shop_goods_sku_id']))
                    ->where(array('shop_goods_sku_stock >= [-]', $val['shop_order_goods_number']))
                    ->data(array('shop_goods_sku_stock = [-]', 'shop_goods_sku_stock - ' . $val['shop_order_goods_number'], true))
                    ->data(array('shop_goods_sku_update_time = [-]', time()), true)
                    ->update();

                // 回滚事务，关闭锁
                if (!$bool) {
                    db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                    object(parent::TABLE_LOCK)->close($lock_ids);
                    return false;
                }
            }
        }

        // 提交事务
        db(parent::DB_APPLICATION_ID)->query('COMMIT');
        // 关闭锁
        object(parent::TABLE_LOCK)->close($lock_ids);
        // 清除缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);

        return true;
    }

    /**
     *
     * @Author green
     *
     * @param  [type] $gg [description]
     * @param  [type] $mm [description]
     * @return [type]     [description]
     */
    private function _update_pay_state($gg, $mm)
    { }


    //===========================================
    // 查询数据
    //===========================================



    /**
     * 获取一个数据，不带缓存，用于支付相关
     * 
     * @param   string  $order_id
     * @return  array
     */
    public function find_unbuffered($shop_order_id = '')
    {
        if (empty($shop_order_id)) {
            return false;
        }
        return db(parent::DB_APPLICATION_ID)
            ->table('shop_order')
            ->where(array('shop_order_id=[+]', $shop_order_id))
            ->find();
    }



    /**
     * 查一条记录，根据主键
     * @param  string $id 商家用户表ID
     * @return array
     */
    public function find($id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($id), function ($id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('shop_order')
                ->where(array('shop_order_id=[+]', $id))
                ->find();
        });
    }


    /**
     * 查一条记录，根据条件
     * @param  array  $call_where 查询条件
     * @return array
     */
    public function find_where($call_where = array(), $find = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where, $find), function ($call_where, $find) {
            if (empty($find) || !is_array($find)) {
                $find = array();
            }

            return db(parent::DB_APPLICATION_ID)
                ->table('shop_order')
                ->call('where', $call_where)
                ->find($find);
        });
    }


    /**
     * 获取一个数据的详情
     * 
     * @param	string	$shop_order_id
     * @param	array	$config
     * @return	array
     */
    public function find_details($shop_order_id, $config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_order_id, $config), function ($shop_order_id, $config) {
            $where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $find = isset($config['find']) && is_array($config['find']) ? $config['find'] : array();

            if (empty($find)) {
                $user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
                $find = array(
                    "u.user_logo_image_id",
                    "u.user_nickname",
                    "u.user_compellation",
                    "u.user_qq",
                    "u.user_email",
                    '(' . $user_phone_verify_list_sql . ') as user_phone_verify_list',
                    "so.*",
                    "s.shipping_name",
                    "s.shipping_sign"
                );
            }

            //用户数据
            $user = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = so.user_id'
            );

            //配送数据
            $shipping = array(
                'table' => 'shipping s',
                'type' => 'left',
                'on' => 's.shipping_id = so.shipping_id'
            );

            $data = db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->groupby("so.shop_order_id")
                ->joinon($user, $shipping)
                ->where(array('so.shop_order_id=[+]', (string) $shop_order_id))
                ->call('where', $where)
                ->find($find);

            //获取订单商品数据
            if (!empty($data['shop_order_id'])) {
                $pay_method_list = $this->get_pay_method();
                if (isset($pay_method_list[$data['shop_order_pay_money_method']])) {
                    $data['shop_order_pay_money_method_name'] = $pay_method_list[$data['shop_order_pay_money_method']];
                }
                if (isset($pay_method_list[$data['shop_order_pay_credit_method']])) {
                    $data['shop_order_pay_credit_method_name'] = $pay_method_list[$data['shop_order_pay_credit_method']];
                }

                $shop_order_goods_config = array(
                    "where" => array(
                        array("shop_order_id=[+]", $data['shop_order_id'])
                    ),
                    "orderby" => array(
                        array("shop_order_goods_name"),
                        array("shop_order_goods_time"),
                    )
                );
                $data['shop_order_goods'] = object(parent::TABLE_SHOP_ORDER_GOODS)->select($shop_order_goods_config);
                /*if( !empty($data['shop_order_goods']) ){
					foreach($data['shop_order_goods'] as $key => $value){
						if( !empty($value['shop_order_goods_json']) ){
							$data['shop_order_goods'][$key]['shop_order_goods_json'] = cmd(array($value['shop_order_goods_json']), 'json decode');
						}
					}
				}*/
            }

            return $data;
        });
    }


    /**
     * 获取一个数据的详情
     * 
     * @param	string	$shop_order_id
     * @param	array	$config
     * @return	array
     */
    public function find_order_where($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $find = isset($config['find']) && is_array($config['find']) ? $config['find'] : array();

            //用户数据
            $user = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = so.user_id'
            );

            //配送数据
            $shipping = array(
                'table' => 'shipping s',
                'type' => 'left',
                'on' => 's.shipping_id = so.shipping_id'
            );

            $data = db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->groupby("so.shop_order_id")
                ->joinon($user, $shipping)
                ->call('where', $where)
                ->find($find);

            //获取订单商品数据
            if (!empty($data['shop_order_id'])) {
                $shop_order_goods_config = array(
                    "where" => array(
                        array("shop_order_id=[+]", $data['shop_order_id'])
                    ),
                    "orderby" => array(
                        array("shop_order_goods_name"),
                        array("shop_order_goods_time"),
                    )
                );
                $data['shop_order_goods'] = object(parent::TABLE_SHOP_ORDER_GOODS)->select($shop_order_goods_config);
            }

            return $data;
        });
    }




    /**
     * 查询订单及物流信息
     * ------Mr.Zhao------2019.08.01------
     */
    public function find_join_shipping($id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($id), function ($id) {
            $find = array(
                "so.*",
                "s.shipping_name",
                "s.shipping_sign"
            );


            //配送数据
            $shipping = array(
                'table' => 'shipping s',
                'type' => 'left',
                'on' => 's.shipping_id = so.shipping_id'
            );

            $data = db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->joinon($shipping)
                ->where(array('shop_order_id=[+]', $id))
                ->find($find);

            return $data;
        });
    }




    /**
     * 获取多个用户数据
     *  $config = array(
     *  'where' => array(), //条件
     *  'orderby' => array(), //排序
     *  'limit' => array(0, page_size), //取出条数，默认不限制
     * );
     * 
     * @param   array   $config
     * @return  array
     */
    public function select($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $limit   = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select  = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            return db(parent::DB_APPLICATION_ID)
                ->table('shop_order')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select);
        });
    }




    /**
     * 查询分页数据
     * 
     * @param   array   $config     配置信息
     * @return  array
     */
    public function zrhzfw_select_page($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $call_where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $call_limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            $limit = array(
                (isset($call_limit[0]) ? $call_limit[0] : 0),
                (isset($call_limit[1]) ? $call_limit[1] : 0)
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

            //左连店铺表
            $join_shop = array(
                'table' => 'shop s',
                'type' => 'left',
                'on' => 's.shop_id = so.shop_id',
            );

            //用户数据
            $user = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = so.user_id'
            );

            //左连订单核销表
            $join_shop_order_write_off = array(
                'table' => 'shop_order_write_off sowo',
                'type' => 'left',
                'on' => 'sowo.shop_order_id = so.shop_order_id',
            );

            //获取总条数
            $total_count = db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->joinon($join_shop, $user, $join_shop_order_write_off)
                ->call('where', $call_where)
                ->find('count(*) as count');

            //是否有数据
            if (empty($total_count['count'])) {
                return $data;
            } else {
                $data['row_count'] = $total_count['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count'] / $data['page_size']);
                    $data['page_now'] = ceil($limit[0] / $data['page_size']) + 1;
                }
            }

            if (empty($select)) {
                $user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
                $cashier_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("cashier");
                $select = array(
                    'u.user_parent_id',
                    'u.user_nickname',
                    'u.user_logo_image_id',
                    '(' . $user_phone_verify_list_sql . ') as user_phone_verify_list',
                    'so.*',
                    'sowo.shop_order_write_off_code',
                    'sowo.shop_order_write_off_cashier_id',
                    'sowo.shop_order_write_off_user_id',
                    'sowo.shop_order_write_off_state',
                    'sowo.shop_order_write_off_time',
                    'cashier.user_nickname as cashier_user_nickname',
                    'cashier.user_logo_image_id as cashier_user_logo_image_id',
                    '(' . $cashier_phone_verify_list_sql . ') as cashier_user_phone_verify_list',
                );
            }


            //用户数据
            $cashier_user = array(
                'table' => 'user cashier',
                'type' => 'left',
                'on' => 'cashier.user_id = sowo.shop_order_write_off_user_id'
            );

            //查询数据
            $data['data'] =  db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->joinon($join_shop, $user, $join_shop_order_write_off, $cashier_user)
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
     * @param   array   $config     配置信息
     * @return  array
     */
    public function select_page($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $call_where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $call_limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $call_joinon = isset($config['joinon']) && is_array($config['joinon']) ? $config['joinon'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            $limit = array(
                (isset($call_limit[0]) ? $call_limit[0] : 0),
                (isset($call_limit[1]) ? $call_limit[1] : 0)
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

            // 左连店铺表
            $call_joinon[] = array(
                'table' => 'shop s',
                'type' => 'left',
                'on' => 's.shop_id = so.shop_id',
            );

            // 左连用户表
            $call_joinon[] = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = so.user_id'
            );

            //获取总条数
            $total_count = db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->call('joinon', $call_joinon)
                ->call('where', $call_where)
                ->find('count(*) as count');

            //是否有数据
            if (empty($total_count['count'])) {
                return $data;
            } else {
                $data['row_count'] = $total_count['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count'] / $data['page_size']);
                    $data['page_now'] = ceil($limit[0] / $data['page_size']) + 1;
                }
            }

            if (empty($select)) {
                $user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
                $select = array(
                    'u.user_parent_id',
                    'u.user_nickname',
                    'u.user_logo_image_id',
                    '(' . $user_phone_verify_list_sql . ') as user_phone_verify_list',
                    'so.*',
                );
            }

            //查询数据
            $data['data'] =  db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->call('joinon', $call_joinon)
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $call_limit)
                ->select($select);

            return $data;
        });
    }


    /**
     * 查询分页数据--关联查询
     * 
     * @param   array   $config     配置信息
     * @return  array
     */
    public function select_join_page($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $call_where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $call_limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();
            $call_joinon = isset($config['joinon']) && is_array($config['joinon']) ? $config['joinon'] : array();

            $limit = array(
                (isset($call_limit[0]) ? $call_limit[0] : 0),
                (isset($call_limit[1]) ? $call_limit[1] : 0)
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

            //获取总条数
            $total_count = db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->call('where', $call_where)
                ->find('count(*) as count');

            //是否有数据
            if (empty($total_count['count'])) {
                return $data;
            } else {
                $data['row_count'] = $total_count['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count'] / $data['page_size']);
                    $data['page_now'] = ceil($limit[0] / $data['page_size']) + 1;
                }
            }

            if(!empty($call_joinon)){
                $data['data'] =  db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->call('joinon',$call_joinon)
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $call_limit)
                ->select($select);
            }else{
                $data['data'] =  db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $call_limit)
                ->select($select);
            }
            return $data;
        });
    }

    /**
     * 查询分页数据——普通用户
     * 
     * @param   array   $config     配置信息
     * @return  array
     */
    public function select_page_by_user($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $call_where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $call_limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            $limit = array(
                (isset($call_limit[0]) ? $call_limit[0] : 0),
                (isset($call_limit[1]) ? $call_limit[1] : 0)
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

            //获取总条数
            $total_count = db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->call('where', $call_where)
                ->find('count(*) as count');

            //是否有数据
            if (empty($total_count['count'])) {
                return $data;
            } else {
                $data['row_count'] = $total_count['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count'] / $data['page_size']);
                    $data['page_now'] = ceil($limit[0] / $data['page_size']) + 1;
                }
            }

            //左连店铺表
            $join_shop = array(
                'table' => 'shop s',
                'type' => 'left',
                'on' => 's.shop_id = so.shop_id',
            );

            //左连订单商品表
            $join_shopordergoods = array(
                'table' => 'shop_order_goods sog',
                'type' => 'left',
                'on' => 'sog.shop_order_id = so.shop_order_id',
            );

            //查询数据
            // $data['data'] =  db(parent::DB_APPLICATION_ID)
            //     ->table('shop_order so')
            //     ->joinon($join_shop)
            //     ->joinon($join_shopordergoods)
            //     ->call('where', $call_where)
            //     ->call('orderby', $call_orderby)
            //     ->call('limit', $call_limit)
            //     ->select($select);

            // return $data;
			
			

            // ------Mr.Zhao------2019.07.01
            $res =  db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->joinon($join_shop)
                ->joinon($join_shopordergoods)
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $call_limit)
                ->select($select);
			
            // 格式化数据
            $order = array();
            foreach ($res as $val) {
                $key = $val['shop_order_id'];
                if (!array_key_exists($key, $order)) {
                    $order[$key] = array(
                        'id' => $val['shop_order_id'],
                        'state' => $val['shop_order_state'],
                        'shop_id' => $val['shop_id'],
                        'shop_name' => $val['shop_name'] ? $val['shop_name'] : '',
                        'buyer_note' => $val['buyer_note'],
                        'seller_note' => $val['seller_note'],
                        'money' => $val['money'],
                        'credit' => $val['credit'],
                        'pay_state' => $val['pay_state'],
                        'pay_credit_state' => $val['pay_credit_state'],
                        'pay_money_state' => $val['pay_money_state'],
                        'shipping_state' => $val['shipping_state'],
                        'comment_state' => $val['comment_state'],
                        'insert_time' => date('Y-m-d', $val['insert_time']),
                        'shop_goods' => array(),
                    );
					
					if( isset($val['goods_arrival_state']) ){
						$order[$key]['goods_arrival_state'] = $val['goods_arrival_state'];
					}
					
                }


                // 添加状态，便于前端判断和展示
                if ($val['pay_state'] == 0 && $val['shipping_state'] == 0) {
                    $order[$key]['status'] = '待付款';
                } elseif ($val['pay_state'] == 2 && $val['shipping_state'] == 0) {
                    $order[$key]['status'] = '支付中';
                } elseif ($val['shipping_state'] == 0 && $val['pay_state'] == 1) {
                    $order[$key]['status'] = '待发货';
                } elseif ($val['shipping_state'] == 2 && $val['pay_state'] == 1) {
                    $order[$key]['status'] = '待收货';
                } elseif ($val['shipping_state'] == 2 && $val['pay_state'] == 1 && $val['comment_state'] == 0) {
                    $order[$key]['status'] = '待评价';
                }


                $json = cmd(array($val['shop_order_goods_json']), 'json decode');
                $image_id = $val['shop_order_goods_image_id'];
                // 判断是否存在商品规格图片
                if (isset($json['shop_goods_sku']['image_id']) && !empty(trim($json['shop_goods_sku']['image_id']))) {
                    $image_id = $json['shop_goods_sku']['image_id'];
                }

                $order[$key]['shop_goods'][] = array(
                    'id' => $val['shop_goods_id'],
                    'sn' => $val['shop_goods_sn'],
                    'name' => $val['shop_order_goods_name'],
                    'image_id' => $image_id,
                    'property' => $val['shop_goods_property'],
                    'price' => $val['shop_order_goods_price'],
                    'number' => $val['shop_order_goods_number'],
                    // 'sku' => $sku,
                    'spu_string' => isset($json['spu_string']) ? $json['spu_string'] : '',
                    'spu_array' => isset($json['spu_array']) ? $json['spu_array'] : array(),
                );
            }


            $data['data'] = array_values($order);
            return $data;
        });
    }


    /**
     * 分页查询
     * @author green
     *
     * @param  array  $config [查询配置]
     * @return array
     */
    public function select_paginate($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config) {
            // 查询配置
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();
            $call_joinon = isset($config['joinon']) && is_array($config['joinon']) ? $config['joinon'] : array();
            $call_where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $limit = array(
                (isset($config['limit'][0]) ? $config['limit'][0] : 0),
                (isset($config['limit'][1]) ? $config['limit'][1] : 0),
            );

            // 返回数据
            $data = array(
                'row_count' => 0,
                'limit_count' => $limit[0] + $limit[1],
                'page_size' => $limit[1],
                'page_count' => 0,
                'page_now' => 0,
                'data' => array()
            );

            // 查询数据条数
            $total_count = db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->call('joinon', $call_joinon)
                ->call('where', $call_where)
                ->find('count(*) as count');

            // 计算分页信息
            if (empty($total_count['count'])) {
                return $data;
            } else {
                $data['row_count'] = $total_count['count'];
                if ($data['page_size']) {
                    $data['page_count'] = ceil($data['row_count'] / $data['page_size']);
                    $data['page_now'] = ceil($limit[0] / $data['page_size']) + 1;
                }
            }

            // 查询数据
            $data['data'] = db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->call('joinon', $call_joinon)
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $limit)
                ->select($select);

            return $data;
        });
    }



    /**
     * 查询分页数据——普通用户
     * 
     * @param   array   $config     配置信息
     * @return  array
     */
    public function zrhzfw_select_page_by_user($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $call_where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $call_limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            $limit = array(
                (isset($call_limit[0]) ? $call_limit[0] : 0),
                (isset($call_limit[1]) ? $call_limit[1] : 0)
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


            //左连订单核销表
            $join_shop_order_write_off = array(
                'table' => 'shop_order_write_off sowo',
                'type' => 'left',
                'on' => 'sowo.shop_order_id = so.shop_order_id',
            );

            //获取总条数
            $total_count = db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->joinon($join_shop_order_write_off)
                ->call('where', $call_where)
                ->find('count(*) as count');

            //是否有数据
            if (empty($total_count['count'])) {
                return $data;
            } else {
                $data['row_count'] = $total_count['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count'] / $data['page_size']);
                    $data['page_now'] = ceil($limit[0] / $data['page_size']) + 1;
                }
            }

            //左连店铺表
            $join_shop = array(
                'table' => 'shop s',
                'type' => 'left',
                'on' => 's.shop_id = so.shop_id',
            );

            //左连订单商品表
            $join_shopordergoods = array(
                'table' => 'shop_order_goods sog',
                'type' => 'left',
                'on' => 'sog.shop_order_id = so.shop_order_id',
            );


            $res =  db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->joinon($join_shop, $join_shop_order_write_off)
                ->joinon($join_shopordergoods)
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $call_limit)
                ->select($select);

            // 格式化数据
            $order = array();
            foreach ($res as $val) {
                $key = $val['shop_order_id'];
                if (!array_key_exists($key, $order)) {
                    $order[$key] = array(
                        'id' => $val['shop_order_id'],
                        'state' => $val['shop_order_state'],
                        'shop_id' => $val['shop_id'],
                        'shop_name' => $val['shop_name'] ? $val['shop_name'] : '',
                        'buyer_note' => $val['buyer_note'],
                        'seller_note' => $val['seller_note'],
                        'money' => $val['money'],
                        'credit' => $val['credit'],
                        'pay_state' => $val['pay_state'],
                        'pay_credit_state' => $val['pay_credit_state'],
                        'pay_money_state' => $val['pay_money_state'],
                        'shipping_state' => $val['shipping_state'],
                        'comment_state' => $val['comment_state'],
                        'insert_time' => date('Y-m-d', $val['insert_time']),
                        'shop_goods' => array(),

                        'write_off_code' => $val['write_off_code'],
                        'write_off_state' => $val['write_off_state'],
                        'write_off_time' => date('Y年m月d日 H:i', $val['write_off_time']),
                    );
                }


                // 添加状态，便于前端判断和展示 1 代付款;  2 待核销;  3 已核销
                if ($val['shop_order_state'] == 0) {
                    $order[$key]['status_text'] = '已取消';
                    $order[$key]['status'] = 0;
                } else
                if ($val['pay_state'] == 0 && $val['shipping_state'] == 0) {
                    $order[$key]['status_text'] = '待付款';
                    $order[$key]['status'] = 1;
                } else
                if ($val['pay_state'] == 2 && $val['shipping_state'] == 0) {
                    $order[$key]['status_text'] = '支付中';
                    $order[$key]['status'] = 1;
                } else
                if (empty($val['write_off_state']) && $val['pay_state'] == 1) {
                    $order[$key]['status_text'] = '未核销';
                    $order[$key]['status'] = 2;
                } else
                if (!empty($val['write_off_state']) && $val['pay_state'] == 1) {
                    $order[$key]['status_text'] = '已核销';
                    $order[$key]['status'] = 3;
                } else {
                    $order[$key]['status_text'] = '未知';
                    $order[$key]['status'] = 0;
                }


                $json = cmd(array($val['shop_order_goods_json']), 'json decode');
                $image_id = $val['shop_order_goods_image_id'];
                // 判断是否存在商品规格图片
                if (isset($json['shop_goods_sku']['image_id']) && !empty(trim($json['shop_goods_sku']['image_id']))) {
                    $image_id = $json['shop_goods_sku']['image_id'];
                }

                $order[$key]['shop_goods'][] = array(
                    'id' => $val['shop_goods_id'],
                    'sn' => $val['shop_goods_sn'],
                    'name' => $val['shop_order_goods_name'],
                    'image_id' => $image_id,
                    'property' => $val['shop_goods_property'],
                    'price' => $val['shop_order_goods_price'],
                    'number' => $val['shop_order_goods_number'],
                    // 'sku' => $sku,
                    'spu_string' => isset($json['spu_string']) ? $json['spu_string'] : '',
                    'spu_array' => isset($json['spu_array']) ? $json['spu_array'] : array(),
                );
            }


            $data['data'] = array_values($order);
            return $data;
        });
    }




    /**
     * 优利小程序--团长查看下级订单
     */
    public function youli_select_page_by_officer( $config=array() )
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $call_where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $call_limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            $limit = array(
                (isset($call_limit[0]) ? $call_limit[0] : 0),
                (isset($call_limit[1]) ? $call_limit[1] : 0)
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

            //获取总条数
            $total_count = db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->call('where', $call_where)
                ->find('count(distinct so.shop_order_id) as count');
            // return $total_count;
            //是否有数据
            if (empty($total_count['count'])) {
                return $data;
            } else {
                $data['row_count'] = $total_count['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count'] / $data['page_size']);
                    $data['page_now'] = ceil($limit[0] / $data['page_size']) + 1;
                }
            }

            //user
            $user_left = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = so.user_id'
            );

            //user_phone
            $user_phone = array(
                'table' => 'user_phone up',
                'type' => 'left',
                'on' => 'up.user_id = so.user_id'
            );

            //shop_order_goods
            $goods_left = array(
                'table' => 'shop_order_goods sog',
                'type' => 'left',
                'on' => 'sog.shop_order_id = so.shop_order_id'
            );
            
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('shop_order so')
            ->joinon($user_left,$user_phone,$goods_left)
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);
            return $data;
        });
    }




    //===========================================
    // 检测
    //===========================================


    /**
     * 检测支付方式
     * @return bool
     */
    public function check_pay_method($val = '')
    {
        $methods = $this->get_pay_method();
        return isset($methods[$val]);
    }




    /**
     * 用户钱包 支付 商城购物订单
     * 
     * @param	array	$order			资金订单数据
     * @param	array	$account_data 	账户数据，$user_money 或者是 $user_credit
     * @return	bool
     */
    public function clzy_payment($order, $account_data = array())
    {
        //获取商城订单
        $shop_order = object(parent::TABLE_SHOP_ORDER)->find($order['order_sign']);
        //file_put_contents(CACHE_PATH."/获取商城订单 clzy_payment", cmd(array($shop_order), "json encode"));
        if (empty($shop_order)) {
            return false;
        }

        // 锁表
        $lock_ids = array();
        //锁表
        $lock_shop_order = object(parent::TABLE_LOCK)->start('shop_order_id', $shop_order['shop_order_id'], parent::LOCK_PAY_STATE);
        if (empty($lock_shop_order)) {
            return false; //锁表开启失败
        } else {
            $lock_ids[] = $lock_shop_order;
        }

        if (isset($account_data['user_money_value'])) {
            $user_money = $account_data;
        } else
		if (isset($account_data['user_credit_value'])) {
            $user_credit = $account_data;
        }

        //开启事务
        db(parent::DB_APPLICATION_ID)->query('START TRANSACTION');

        if (!empty($user_money)) {
            //锁表，用户钱包
            $lock_user_money = object(parent::TABLE_LOCK)->start('user_id', $order['order_minus_account_id'], parent::LOCK_MONEY);
            if ($lock_user_money) {
                $lock_ids[] = $lock_user_money;
            } else {
                return false;
            }

            //更新用户钱包数据
            $user_money_id = object(parent::TABLE_USER_MONEY)->insert_minus(array(
                'user_id' => $order['order_action_user_id'],
                'user_money_join_id' => $user_money['user_money_id'],
                'user_money_minus' => $order['order_minus_value'],
                'user_money_value' => $user_money['user_money_value'] - $order['order_minus_value'],
                'user_money_type' => $order['order_type'],
            ));

            //回滚事务，关闭锁
            if (empty($user_money_id)) {
                db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                object(parent::TABLE_LOCK)->close($lock_ids);
                return false;
            }

            $order['order_minus_transaction_id'] = $user_money_id;
        } else
		if (!empty($user_credit)) {
            //锁表，用户钱包
            $lock_user_credit = object(parent::TABLE_LOCK)->start('user_id', $order['order_minus_account_id'], parent::LOCK_CREDIT);
            if ($lock_user_credit) {
                $lock_ids[] = $lock_user_credit;
            } else {
                return false;
            }

            //更新用户积分数据
            $user_credit_id = object(parent::TABLE_USER_CREDIT)->insert_minus(array(
                'user_id' => $order['order_action_user_id'],
                'user_credit_join_id' => $user_credit['user_credit_id'],
                'user_credit_minus' => $order['order_minus_value'],
                'user_credit_value' => $user_credit['user_credit_value'] - $order['order_minus_value'],
                'user_credit_type' => $order['order_type'],
            ));

            //回滚事务，关闭锁
            if (empty($user_credit_id)) {
                db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                object(parent::TABLE_LOCK)->close($lock_ids);
                return false;
            }

            $order['order_minus_transaction_id'] = $user_credit_id;
        }


        //给商家增加
        if (
            !empty($order['order_plus_account_id']) &&
            !empty($order['order_plus_method']) &&
            $order['order_plus_method'] == parent::PAY_METHOD_MERCHANT_MONEY
        ) {
            //将充值余额 提交给 商家
            $merchant_money_id = object(parent::TABLE_MERCHANT_MONEY)->insert_plus(array(
                "merchant_id" => $order['order_plus_account_id'],
                "merchant_money_plus" => $order['order_plus_value'],
                "merchant_money_type" => $order['order_type']
            ));

            //充值失败
            if (empty($merchant_money_id)) {
                file_put_contents(CACHE_PATH . "/将充值余额提交给商家出错", cmd(array(array($merchant_money_id, $order)), "json encode"));

                db(parent::DB_APPLICATION_ID)->query("ROLLBACK"); //回滚
                object(parent::TABLE_LOCK)->close($lock_ids); //关闭锁
                return false;
            }

            $order['order_plus_transaction_id'] = $merchant_money_id;


            //商家赠送用户积分
            $merchant_credit_bool = object(parent::TABLE_MERCHANT_CREDIT)->consume_give_user_credit_not_transaction(
                $order["order_plus_account_id"],
                $order['order_minus_account_id'],
                $order['order_minus_value'],
                $order["order_id"],
                $order['order_action_user_id'],
                $order['order_comment'],
                $order['order_json'],
                $lock_ids
            );

            //商家赠送用户积分失败
            if (empty($merchant_credit_bool)) {
                file_put_contents(CACHE_PATH . "/商家赠送用户积分失败", cmd(array(array($merchant_credit_bool, $order)), "json encode"));
                db(parent::DB_APPLICATION_ID)->query("ROLLBACK"); //回滚
                object(parent::TABLE_LOCK)->close($lock_ids); //关闭锁
                return false;
            }
        }


        $bool = db(parent::DB_APPLICATION_ID)
            ->table('order')
            ->where(array('order_id=[+]', $order['order_id']), array('[and] order_state=1'), array('[and] order_pay_state=0'))
            ->update(array(
                'order_minus_transaction_id' => $order['order_minus_transaction_id'],
                'order_plus_transaction_id' => $order['order_plus_transaction_id'],
                'order_pay_state' => 1,
                'order_pay_time' => time(),
            ));

        //file_put_contents(CACHE_PATH."/支付回调测试", cmd(array(array($bool, $order['order_minus_transaction_id'])), "json encode"));
        //回滚事务，关闭锁
        if (empty($bool)) {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            object(parent::TABLE_LOCK)->close($lock_ids);
            return false;
        }


        $shop_order_where = array();
        $shop_order_where[] = array('shop_order_id=[+]', $shop_order['shop_order_id']);

        // 更新支付状态
        if (in_array($order['order_minus_method'], array(parent::PAY_METHOD_WEIXINPAY, parent::PAY_METHOD_ALIPAY, parent::PAY_METHOD_USER_MONEY))) {
            $shop_order_where[] = array('[and] shop_order_pay_money_state=0');
            $shop_order_update = array(
                'shop_order_pay_money_method' => $order['order_minus_method'],
                'shop_order_pay_money' => $order['order_minus_value'],
                'shop_order_pay_money_state' => 1,
                'shop_order_pay_money_order_id' => $order['order_id'],
                'shop_order_pay_money_time' => time(),
                'shop_order_pay_time' => time(),
                'shop_order_update_time' => time(),
            );

            //当不需要积分
            if ($shop_order['shop_order_credit'] == 0) {
                $shop_order_where[] = array('[and] shop_order_pay_credit=0');
                $shop_order_update['shop_order_pay_state'] = 1; //状态。0未支付；1支付成功; 2支付中(积分支付或者人民币支付任一个完成支付)
            } else
            if ($shop_order['shop_order_pay_credit_state'] == 1) {
                $shop_order_where[] = array('[and] shop_order_pay_credit_state=1');
                $shop_order_update['shop_order_pay_state'] = 1; //状态。0未支付；1支付成功; 2支付中(积分支付或者人民币支付任一个完成支付)
            } else {
                $shop_order_where[] = array('[and] shop_order_pay_credit_state=0');
                $shop_order_update['shop_order_pay_state'] = 2; //状态。0未支付；1支付成功; 2支付中(积分支付或者人民币支付任一个完成支付)
            }
        } else
        if ($order['order_minus_method'] == parent::PAY_METHOD_USER_CREDIT) {
            $shop_order_where[] = array('[and] shop_order_pay_credit_state=0');
            $shop_order_update = array(
                'shop_order_pay_credit_method' => $order['order_minus_method'],
                'shop_order_pay_credit' => $order['order_minus_value'],
                'shop_order_pay_credit_state' => 1,
                'shop_order_pay_credit_order_id' => $order['order_id'],
                'shop_order_pay_credit_time' => time(),
                'shop_order_pay_time' => time(),
                'shop_order_update_time' => time(),
            );

            //当不需要支付人民币
            if ($shop_order['shop_order_money'] == 0) {
                $shop_order_where[] = array('[and] shop_order_pay_money=0');
                $shop_order_update['shop_order_pay_state'] = 1; //状态。0未支付；1支付成功; 2支付中(积分支付或者人民币支付任一个完成支付)
            } else
            if ($shop_order['shop_order_pay_money_state'] == 1) {
                $shop_order_where[] = array('[and] shop_order_pay_money_state=1');
                $shop_order_update['shop_order_pay_state'] = 1; //状态。0未支付；1支付成功; 2支付中(积分支付或者人民币支付任一个完成支付)
            } else {
                $shop_order_where[] = array('[and] shop_order_pay_money_state=0');
                $shop_order_update['shop_order_pay_state'] = 2; //状态。0未支付；1支付成功; 2支付中(积分支付或者人民币支付任一个完成支付)
            }
        } else {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            object(parent::TABLE_LOCK)->close($lock_ids);
            return false;
        }

        //更新商城订单
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_order')
            ->call('where', $shop_order_where)
            ->update($shop_order_update);


        //提交事务
        db(parent::DB_APPLICATION_ID)->query('COMMIT');
		
        // 关闭锁
        object(parent::TABLE_LOCK)->close($lock_ids);

        //判断支付状态，触发不同的事件
        if ($shop_order_update['shop_order_pay_state'] == 1) {
            //支付已完成
            object(parent::REQUEST_SHOP)->event_order_payment_complete($shop_order['shop_order_id']);
            // 易淘
            if (object(parent::TABLE_USER_RECOMMEND)->verification_yitao_distribution()) {
                $this->_send_pay_message($shop_order);
            }
        } else
		if ($shop_order_update['shop_order_pay_state'] == 2) {
            //支付进行中
            object(parent::REQUEST_SHOP)->event_order_payment_moiety($shop_order['shop_order_id']);
        }


        // 清除缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);
        return true;
    }




	







    //===========================================
    // 支付
    //===========================================


    /**
     * 支付
     * @Author green
     * 
     * @param   array   $order          资金订单数据
     * @return  bool
     */
    public function payment($order)
    {
        $payment_method = $order['order_minus_method'];

        // 获取商城订单
        $shop_order = object(parent::TABLE_SHOP_ORDER)->find($order['order_sign']);
        if (empty($shop_order)) return false;

        // 锁表——商城订单
        $lock_ids = array();
        $lock_shop_order = object(parent::TABLE_LOCK)->start('shop_order_id', $shop_order['shop_order_id'], parent::LOCK_PAY_STATE);
        if ($lock_shop_order) {
            $lock_ids[] = $lock_shop_order;
        } else {
            throw new error('请勿重复操作');
        }
        // 锁表——用户钱包
        if ($payment_method === parent::PAY_METHOD_USER_MONEY) {
            $id = object(parent::TABLE_LOCK)->start('user_id', $order['order_minus_account_id'], parent::LOCK_MONEY);
            if ($id) {
                $lock_ids[] = $id;
            } else {
                throw new error('请勿重复操作');
            }
        }
        // 锁表——用户积分
        if ($payment_method === parent::PAY_METHOD_USER_CREDIT) {
            $id = object(parent::TABLE_LOCK)->start('user_id', $order['order_minus_account_id'], parent::LOCK_CREDIT);
            if ($id) {
                $lock_ids[] = $id;
            } else {
                throw new error('请勿重复操作');
            }
        }


        // 开启事务
        db(parent::DB_APPLICATION_ID)->query('START TRANSACTION');

        // 更新商城订单状态
        $res = $this->_payment_update_shop_order_state($order['order_id'], $shop_order, $payment_method);
        if ($res === false) {
            echo '失败：更新商城订单状态';
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            object(parent::TABLE_LOCK)->close($lock_ids);
            return false;
        }

        // 是否合并支付
        if ($shop_order['shop_order_pay_parent'] === '1') {
            $res = $this->_payment_suborder($order['order_id'], $shop_order['shop_order_id'], $payment_method);
            if ($res === false) {
                db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                object(parent::TABLE_LOCK)->close($lock_ids);
                return false;
            }
        } else {
            // 增加卖家资产
            $res = $this->_payment_increase_seller_wealth($shop_order, $payment_method);
            if ($res === false) {
                echo '失败：增加卖家资产';
                db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                object(parent::TABLE_LOCK)->close($lock_ids);
                return false;
            } else {
                $order['order_plus_transaction_id'] = $res;
            }

            // 减少买家资产
            $res = $this->_payment_decrease_buyer_wealth($shop_order, $payment_method);
            if ($res === false) {
                echo '失败：减少买家资产';
                db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                object(parent::TABLE_LOCK)->close($lock_ids);
                return false;
            } else {
                $order['order_minus_transaction_id'] = $res;
            }

            // 减库存
           // $this->_payment_decrease_stock(array($shop_order['shop_order_id']));
        }

        // 更新订单状态
        if (!isset($order['order_plus_transaction_id'])) {
            $order['order_plus_transaction_id'] = '';
        }
        $res = db(parent::DB_APPLICATION_ID)
            ->table('order')
            ->call('where', array(
                array('order_id = [+]', $order['order_id']),
                array('order_state = 1'),
                array('order_pay_state = 0'),
            ))
            ->update(array(
                'order_minus_transaction_id' => $order['order_minus_transaction_id'],
                'order_plus_transaction_id' => $order['order_plus_transaction_id'],
                'order_pay_state' => 1,
                'order_pay_time' => time(),
            ));

        if (!$res) {
            echo '失败：更新订单状态';
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            object(parent::TABLE_LOCK)->close($lock_ids);
            return false;
        }

        // 提交事务
        db(parent::DB_APPLICATION_ID)->query('COMMIT');
        // 关闭锁
        object(parent::TABLE_LOCK)->close($lock_ids);
        // 清除缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);
        // 触发事件
        $this->_payment_event($shop_order['shop_order_id']);

        return true;
    }

    /**
     * 支付子订单
     * @Author green
     *
     * @param  string $shop_order_parent_id [商城合并支付订单主键ID]
     * @param  string $payment_method       [支付方式]
     * @return bool
     */
    private function _payment_suborder($order_id, $shop_order_parent_id, $payment_method)
    {
        $shop_order_ids = array();

        // 查询子订单
        $list_shop_order = $this->select(array(
            'select' => array(
                'user_id',
                'shop_id',
                'shop_order_id',
                'shop_order_money',
                'shop_order_credit',
                'shop_order_pay_money_state',
                'shop_order_pay_credit_state',
            ),
            'where' => array(
                array('shop_order_parent_id = [+]', $shop_order_parent_id),
            ),
        ));

        // 循环子订单
        foreach ($list_shop_order as $shop_order) {
            $shop_order_ids[] = $shop_order['shop_order_id'];

            // 更新商城订单状态
            $res = $this->_payment_update_shop_order_state($order_id, $shop_order, $payment_method);
            if ($res === false) return false;

            // 增加卖家资产
            $plus_transaction_id = $this->_payment_increase_seller_wealth($shop_order, $payment_method);
            if ($plus_transaction_id === false) return false;

            // 减少买家资产
            $minus_transaction_id = $this->_payment_decrease_buyer_wealth($shop_order, $payment_method);
            if ($minus_transaction_id === false) return false;

            // 插入订单记录
            $order_value = ($payment_method === parent::PAY_METHOD_USER_CREDIT) ? $shop_order['shop_order_credit'] : $shop_order['shop_order_money'];
            $timestamp = time();
            $res = object(parent::TABLE_ORDER)->insert(array(
                'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
                'order_type' => parent::TRANSACTION_TYPE_SHOP_ORDER,
                'order_comment' => '商城购物',
                'order_action_user_id' => $shop_order['user_id'],
                'order_plus_method' => '',
                'order_plus_account_id' => $shop_order['shop_id'],
                'order_plus_transaction_id' => $plus_transaction_id,
                'order_plus_value' => $order_value,
                'order_minus_method' => $payment_method,
                'order_minus_account_id' => $shop_order['user_id'],
                'order_minus_transaction_id' => $minus_transaction_id,
                'order_minus_value' => $order_value,
                'order_sign' => $shop_order['shop_order_id'],
                'order_state' => 1,
                'order_pay_state' => 1,
                'order_pay_time' => $timestamp,
                'order_insert_time' => $timestamp,
            ));
            if (!$res) return false;
        }

        // 减库存
        $this->_payment_decrease_stock($shop_order_ids);
    }

    /**
     * 更新商城订单状态
     * @Author green
     *
     * @param  string $order_id         [订单主键ID]
     * @param  array  $shop_order       [商城订单]
     * @param  string $payment_method   [支付方式]
     * @return bool
     */
    private function _payment_update_shop_order_state($order_id, $shop_order, $payment_method)
    {
        $timestamp = time();
        $data = array();
        $where = array();

        // 判断支付方式
        if (in_array($payment_method, array(parent::PAY_METHOD_WEIXINPAY, parent::PAY_METHOD_ALIPAY, parent::PAY_METHOD_USER_MONEY))) {
            // 钱支付
            $data = array(
                'shop_order_pay_money_method'   => $payment_method,
                'shop_order_pay_money'          => $shop_order['shop_order_money'],
                'shop_order_pay_money_order_id' => $order_id,
                'shop_order_pay_money_state'    => 1,
                'shop_order_pay_money_time'     => $timestamp,
                'shop_order_pay_time'           => $timestamp,
                'shop_order_update_time'        => $timestamp,
            );
            $where[] = array('shop_order_pay_money_state = 0');

            // 是否需要支付积分
            if ($shop_order['shop_order_credit'] === '0') {
                $data['shop_order_pay_state'] = 1;
                $where[] = array('shop_order_pay_credit = 0');
            } else {
                // 积分支付是否完成
                if ($shop_order['shop_order_pay_credit_state'] === '1') {
                    $data['shop_order_pay_state'] = 1;
                    $where[] = array('shop_order_pay_credit_state = 1');
                } else {
                    $data['shop_order_pay_state'] = 2;
                    $where[] = array('shop_order_pay_credit_state = 0');
                }
            }
        } elseif ($payment_method === parent::PAY_METHOD_USER_CREDIT) {
            // 积分支付
            $data = array(
                'shop_order_pay_credit_method'  => $payment_method,
                'shop_order_pay_credit'         => $shop_order['shop_order_credit'],
                'shop_order_pay_credit_order_id' => $order_id,
                'shop_order_pay_credit_state'   => 1,
                'shop_order_pay_credit_time'    => $timestamp,
                'shop_order_pay_time'           => $timestamp,
                'shop_order_update_time'        => $timestamp,
            );
            $where[] = array('shop_order_pay_credit_state = 0');

            // 是否需要钱支付
            if ($shop_order['shop_order_money'] === '0') {
                $data['shop_order_pay_state'] = 1;
                $where[] = array('shop_order_pay_money = 0');
            } else {
                // 钱支付是否完成
                if ($shop_order['shop_order_pay_money_state'] === '1') {
                    $data['shop_order_pay_state'] = 1;
                    $where[] = array('shop_order_pay_money_state = 1');
                } else {
                    $data['shop_order_pay_state'] = 2;
                    $where[] = array('shop_order_pay_money_state = 0');
                }
            }
        } else {
            return false;
        }

        // 更新数据
        $where[] = array('shop_order_id = [+]', $shop_order['shop_order_id']);
        $res = $this->update($where, $data);
        if (!$res) {
            echo json_encode([$where, $data], 256);
            exit();
            echo '失败：更新商城订单状态';
        }

        return $res;
    }

    /**
     * 增加卖家资产
     * @Author green
     *
     * @param  array  $shop_order       [商城订单]
     * @param  string $payment_method   [支付方式]
     * @return mixed                    [false,或者数据主键ID]
     */
    private function _payment_increase_seller_wealth($shop_order, $payment_method)
    {
        // 金钱
        if (in_array($payment_method, array(parent::PAY_METHOD_WEIXINPAY, parent::PAY_METHOD_ALIPAY, parent::PAY_METHOD_USER_MONEY))) {
            // 自营不考虑
            if (empty($shop_order['shop_id'])) return '';

            $id = object(parent::TABLE_MERCHANT_MONEY)->insert_plus(array(
                'merchant_id' => $shop_order['shop_id'],
                'merchant_money_plus' => $shop_order['shop_order_money'],
                'merchant_money_type' => parent::TRANSACTION_TYPE_SHOP_ORDER,
            ));

            return $id;
        }

        return '';
    }

    /**
     * 减少买家资产
     * @Author green
     *
     * @param  array  $shop_order       [商城订单]
     * @param  string $payment_method   [支付方式]
     * @return mixed                    [false,或者数据主键ID]
     */
    private function _payment_decrease_buyer_wealth($shop_order, $payment_method)
    {
        // 钱包支付
        if ($payment_method === parent::PAY_METHOD_USER_MONEY) {
            // 查询钱包数据
            $user_money = object(parent::TABLE_USER_MONEY)->find_now_data($shop_order['user_id']);
            // 更新钱包
            $id = object(parent::TABLE_USER_MONEY)->insert_minus(array(
                'user_id' => $shop_order['user_id'],
                'user_money_join_id' => $user_money['user_money_id'],
                'user_money_minus' => $shop_order['shop_order_money'],
                'user_money_value' => $user_money['user_money_value'] - $shop_order['shop_order_money'],
                'user_money_type' => parent::TRANSACTION_TYPE_SHOP_ORDER,
            ));

            return $id;
        }

        // 积分支付
        if ($payment_method === parent::PAY_METHOD_USER_CREDIT) {
            // 查询积分数据
            $user_credit = object(parent::TABLE_USER_CREDIT)->find_now_data($shop_order['user_id']);
            // 更新积分
            $id = object(parent::TABLE_USER_CREDIT)->insert_minus(array(
                'user_id' => $shop_order['user_id'],
                'user_credit_join_id' => $user_credit['user_credit_id'],
                'user_credit_minus' => $shop_order['shop_order_credit'],
                'user_credit_value' => $user_credit['user_credit_value'] - $shop_order['shop_order_credit'],
                'user_credit_type' => parent::TRANSACTION_TYPE_SHOP_ORDER,
            ));

            return $id;
        }

        return '';
    }

    /**
     * 减少库存
     * @Author green
     *
     * @param  array $shop_order_ids [商城订单主键ID，索引数组]
     * @return bool
     */
    private function _payment_decrease_stock($shop_order_ids)
    {
        $shop_order_ids = '"' . implode('","', $shop_order_ids) . '"';

        // 查询订单商品
        $list_shop_order_goods = db(parent::DB_APPLICATION_ID)
            ->table('shop_order_goods')
            ->where(array('shop_order_id IN ([-])', $shop_order_ids, true))
            ->select(array(
                'shop_goods_sku_id',
                'shop_order_goods_number',
                'shop_order_goods_json',
            ));

        // 循环商品
        foreach ($list_shop_order_goods as $i) {
            $shop_order_goods_json = cmd(array($i['shop_order_goods_json']), 'json decode');

            // 判断减库存方式
            if (isset($shop_order_goods_json['shop_goods']['shop_goods_stock_mode']) && $shop_order_goods_json['shop_goods']['shop_goods_stock_mode'] === '2') {
                // 更新库存数据
                object(parent::TABLE_SHOP_GOODS_SKU)->decrease_stock($i['shop_goods_sku_id'], $i['shop_order_goods_number']);
            }
        }
    }

    /**
     * 支付事件
     * @Author green
     *
     * @param  string $shop_order_id [商城订单主键ID]
     * @return void
     */
    private function _payment_event($shop_order_id = '')
    {
        // 查询订单数据
        $shop_order = object(parent::TABLE_SHOP_ORDER)->find($shop_order_id);

        // 是否合并支付
        if ($shop_order['shop_order_pay_parent'] === '1') {
            // 查询子订单
            $list_shop_order = $this->select(array(
                'select' => array(
                    'shop_order_id',
                ),
                'where' => array(
                    array('shop_order_parent_id = [+]', $shop_order['shop_order_id']),
                ),
            ));

            // 循环子订单
            foreach ($list_shop_order as $shop_order) {
                $this->_payment_event($shop_order['shop_order_id']);
            }

            return;
        }

        // 判断支付状态
        if ($shop_order['shop_order_pay_state'] == 1) {
            // 支付完成
            object(parent::REQUEST_SHOP)->event_order_payment_complete($shop_order['shop_order_id']);
            // 易淘
            if (object(parent::TABLE_USER_RECOMMEND)->verification_yitao_distribution()) {
                $this->_send_pay_message($shop_order);
            }
        } elseif ($shop_order['shop_order_pay_state'] == 2) {
            // 支付一部分
            object(parent::REQUEST_SHOP)->event_order_payment_moiety($shop_order['shop_order_id']);
        }
    }


    //===========================================
    // 支付
    //===========================================


    /**
     * 取消
     * @author green
     *
     * @param  string $user_id       [用户ID]
     * @param  string $shop_order_id [商城订单ID]
     * @return bool
     */
    public function cancel($user_id = '', $shop_order_id = '')
    {
        $timestamp = time();
        // 查询订单
        $shop_order = $this->find($shop_order_id);
        // 查询订单商品
        $shop_order_goods = object(parent::TABLE_SHOP_ORDER_GOODS)->select(array(
            'select' => array('shop_goods_sku_id','shop_order_goods_number','shop_order_goods_json'),
            'where' => array('shop_order_id = [+]', $shop_order_id)
        ));

        // 开启事务
        db(parent::DB_APPLICATION_ID)->query('START TRANSACTION');

        // 更新商城订单状态
        $data = array(
            'shop_order_state' => 0,
            'shop_order_close_time' => $timestamp,
            'shop_order_update_time' => $timestamp,
        );
        $where = array(
            array('user_id = [+]', $user_id),
            array('[and] shop_order_id = [+]', $shop_order_id),
        );
        $res = $this->update($where, $data);
        if (!$res) {
            echo '更新订单状态失败';
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            return false;
        }

        // 恢复库存
        foreach ($shop_order_goods as $goods) {
            $goods_json = json_decode($goods['shop_order_goods_json'], true);
            if (isset($goods_json['stock_mode']) && $goods_json['stock_mode'] == 1) {
                $res = object(parent::TABLE_SHOP_GOODS_SKU)->increase_stock($goods['shop_goods_sku_id'], $goods['shop_order_goods_number']);
                if (!$res) {
                    echo '更新库存失败';
                    db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                    return false;
                }
            }
        }

        // 恢复优惠券
        if (!empty($shop_order['user_coupon_id'])) {
            // 查询优惠券信息
            $user_coupon = object(parent::TABLE_USER_COUPON)->find($shop_order['user_coupon_id']);
            $data = array(
                'user_coupon_use_number' => $user_coupon['user_coupon_use_number'] - 1,
                'user_coupon_state' => 1,
                'user_coupon_update_time' => $timestamp,
            );
            $where = array(
                array('user_coupon_id = [+]', $user_coupon['user_coupon_id']),
            );
            $res = object(parent::TABLE_USER_COUPON)->update($where, $data);
            if (!$res) {
                echo '更新优惠券失败';
                db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                return false;
            }
        }

        // 提交事务
        db(parent::DB_APPLICATION_ID)->query('COMMIT');
        // 清除缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);

        return true;

    }




















    /**
     * 易淘商城获取分销提成奖励
     * Undocumented function
     *
     * @return void
     */
    public function invite_royalty_reward($shop_order_id = null, $reward_type = 0, $shop_order_goods = array())
    {
        if (empty($shop_order_id) || !in_array($reward_type, [1, 2]))
            return false;

        $shop_order = $this->find($shop_order_id);  //获取订单信息
        $user_id = $shop_order['user_id'];   //获取下单用户

        // 查询订单消费人的上级
        // $data = object(parent::TABLE_USER_RECOMMEND)->select_superiro($user_id);
        $data = object(parent::TABLE_USER_RECOMMEND)->select_reward_user($user_id);

        //  获取每个邀请人应得提成奖励
        $res = $this->royalty_reward($data, $shop_order, $reward_type, $shop_order_goods);
        return $res;
    }

    /**
     * 易淘商城--计算邀请链奖金
     * Undocumented function
     *
     * @param array $invite_user
     * @param [type] $shop_order
     * @param [type] $reward_type
     * @return void
     */
    protected function royalty_reward($invite_user = array(), $shop_order, $reward_type, $shop_order_goods)
    {
        $invite_info = array(); //邀请奖励信息
        $shop_order_admin_user = object(parent::TABLE_ADMIN_USER)->find($shop_order['user_id']);
        if (empty($shop_order_admin_user)) {
            return $invite_info;
        }

        $user_info = object(parent::TABLE_USER)->find($shop_order['user_id']);    //获取下单用户信息
        if (!empty($shop_order_admin_user) && $shop_order_admin_user['admin_id'] === 'founder') {
            $agent_info = array();
        } else {
            $user_json = cmd(array($user_info['user_json']), 'json decode');     //下单用户json数据
            $user_address_province = !empty($user_json['address']['province']) ? $user_json['address']['province'] : '';    //下单用户省份
            $user_address_city = !empty($user_json['address']['city']) ? $user_json['address']['city'] : '';    //下单用户所在市
            $user_address_area = !empty($user_json['address']['area']) ? $user_json['address']['area'] : '';    //下单用户所在区、县、市
            $agent_region = object(parent::TABLE_AGENT_REGION)->find_province_city_district($user_address_province, $user_address_city, $user_address_area);      //获取用户所在代理地区信息
            $agent_info = array();
            if (!empty($agent_region)) {
                //  用户所在地区的创始人
                $agent_info = object(parent::TABLE_AGENT_USER)->find_agent_region_user($agent_region['agent_region_id']);
            }
        }

        //shop_order购买商品数量
        $shop_order_goods_number = (int) $shop_order_goods['shop_order_goods_number'];

        switch ($reward_type) {
            case 1: //发放门槛商品奖励--购买的商品为门槛商品

                $fonder_reward = $shop_order['shop_order_money'];
                //获取门槛商品提成及区域代理费配置
                if (in_array($shop_order_admin_user['admin_id'], ['shop_manager', 'chief_inspector', 'founder'])) {
                    //再次购买--提成配置
                    $royalty_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("shop_distributions_yitao"), true);
                } else {
                    //初次购买--提成配置
                    $royalty_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("shop_distribution_yitao"), true);
                }
                if (!isset($royalty_config['is_open']) || $royalty_config['is_open'] != 1) {
                    return false;
                }

                //一级提成
                $one_level_royal = isset($royalty_config['shop_manager_reward']['one_level_royal']) && $royalty_config['shop_manager_reward']['one_level_royal'] > 0 ? (int) $royalty_config['shop_manager_reward']['one_level_royal'] : 100;
                //二级提成
                $two_level_royal = isset($royalty_config['shop_manager_reward']['two_level_royal']) && $royalty_config['shop_manager_reward']['two_level_royal'] > 0 ? (int) $royalty_config['shop_manager_reward']['two_level_royal'] : 100;
                //三级提成
                $region_money = isset($royalty_config['shop_manager_reward']['region_money']) && $royalty_config['shop_manager_reward']['region_money'] > 0 ? (int) $royalty_config['shop_manager_reward']['region_money'] : 100;

                $admin_info = array();  //存储店主或总监信息
                $founder_info = array();    //存储创始人信息

                //循环邀请关系
                for ($i = 0; $i < count($invite_user); $i++) {
                    // 取出等级
                    $level = (int) $invite_user[$i]['level'] - 1;
                    $admin_user = object(parent::TABLE_ADMIN_USER)->find($invite_user[$i]['user_id']);
                    // 查询身份ID

                    //保存两条店主或者总监信息及一个创始人信息
                    if (!empty($admin_user['admin_id']) && in_array($admin_user['admin_id'], array('shop_manager', 'chief_inspector','founder')) && $admin_user['admin_user_state'] == 1) {
                        
                        if($admin_user['admin_id'] !== 'founder'){
                            if (count($admin_info) < 2) {
                                $user = array(
                                    'user_id' => $invite_user[$i]['user_id'],
                                    'user_invite' => $invite_user[$i],
                                    'level' => $level,
                                    'admin_id' => $admin_user['admin_id']
                                );
                                $admin_info[] = $user;
                            }
                        }else{
                            if (empty($founder_info)) {
                                $founder_info = array(
                                    'user_id' => $invite_user[$i]['user_id'],
                                    'user_invite' => $invite_user[$i],
                                    'level' => $level,
                                    'admin_id' => $admin_user['admin_id'],
                                );
                            }
                            break;
                        }
                    }
                }



                //门槛商品二级提成
                if (!empty($admin_info)) {
                    for ($i = 0; $i < count($admin_info); $i++) {
                        if ($i === 0) {
                            $admin_info[$i]['rmb_award'] = $one_level_royal * $shop_order_goods_number;
                            $admin_info[$i]['type'] = 2;
                            $fonder_reward -= $one_level_royal * $shop_order_goods_number;
                            $invite_info[] = $admin_info[$i];
                        }

                        if ($i === 1) {
                            $admin_info[$i]['rmb_award'] = $two_level_royal * $shop_order_goods_number;
                            $admin_info[$i]['type'] = 2;
                            $fonder_reward -= $two_level_royal * $shop_order_goods_number;
                            $invite_info[] = $admin_info[$i];
                        }
                    }
                }




                //判断该订单用户的区域创始人是否是该用户的直接邀请创始人，不是则有区域管理费


                //区域管理费--如果创始人区域与邀请创始人不是同一人
                if ($shop_order_admin_user['admin_id'] != 'founder' && !empty($founder_info) && !empty($agent_info) && $agent_info['user_id'] !== $founder_info['user_id']) {
                    $fonder_reward -= $region_money * $shop_order_goods_number;
                    $agent_user_info = array(
                        'user_id' => $agent_info['user_id'],
                        'rmb_award' => $region_money * $shop_order_goods_number,
                        'admin_id' => 'founder',
                        'type' => 3
                    );
                    $invite_info[] = $agent_user_info;
                }


                //创始人如果库存不为空--则用户购买门槛商品应得的钱
                if ($shop_order_admin_user['admin_id'] === 'founder') {
                    $founder_info['user_id'] = $shop_order_admin_user['user_id'];
                    if (isset($founder_info['level'])) {
                        unset($founder_info['level']);
                    }
                }

                if (!empty($founder_info)) {
                    $agent_where = array(
                        array('user_id=[+]', $founder_info['user_id']),
                        array('agent_user_state=1'),
                        array("agent_user_scoket>=$shop_order_goods_number")
                    );
                    //获取创始人代理信息--库存
                    $founder_agent_info = object(parent::TABLE_AGENT_USER)->find_where($agent_where);

                    //判断创始人代理信息是否为空
                    if (!empty($founder_agent_info) && $founder_agent_info['agent_user_scoket'] >= $shop_order_goods_number) {
                        $founder_socket = (int) $founder_agent_info['agent_user_scoket'];    //区域负责人现有库存
                        $founder_remain_socket = $founder_socket - $shop_order_goods_number;  //区域负责人剩余
                        $founder_info['rmb_award'] = $fonder_reward;
                        $founder_info['type'] = 6;
                        if (!empty($invite_info)) {
                            array_push($invite_info, $founder_info);
                        } else {
                            $invite_info[] = $founder_info;
                        }
                        $where = array(
                            array('agent_user_id=[+]', $founder_agent_info['agent_user_id']),
                        );
                        $update_data = array(
                            'agent_user_scoket' => (int) $founder_remain_socket,
                        );
                        object(parent::TABLE_AGENT_USER)->update($where, $update_data);

                        $stock_config = array(
                            'user_id'=>$founder_info['user_id'],
                            'key'=>$founder_agent_info['agent_user_id'],
                            'type'=>2,
                            'num'=>$shop_order_goods_number,
                            'remain_num'=>$founder_remain_socket
                        );
                        object(parent::TABLE_SHOP_GOODS_STOCK_LOG)->insert_info($stock_config);
                    }
                }
				
                return $invite_info;
                break;

            case 2:  //普通商品分销奖励--购买的商品为非门槛商品

                $shop_manager = array();    //店主
                $chief_inspector = array();     //总监
                $founder = array();     //创始人

                for ($i = 0; $i < count($invite_user); $i++) {
                    $level = (int) $invite_user[$i]['level'] - 1;
                    $admin_user = object(parent::TABLE_ADMIN_USER)->find($invite_user[$i]['user_id']);

                    //创始人
                    if (!empty($admin_user['admin_id']) && $admin_user['admin_id'] === 'founder' && $admin_user['admin_user_state'] == 1) {
                        if (count($founder) < 2) {
                            $invite_user[$i]['admin_id'] = $admin_user['admin_id'];
                            $invite_user[$i]['idk'] = $i;
                            $founder[] = $invite_user[$i];
                        } else {
                            break;
                        }
                    }

                    //总监
                    if ($shop_order_admin_user['admin_id'] !== 'founder' && !empty($admin_user['admin_id']) && $admin_user['admin_id'] === 'chief_inspector' && $admin_user['admin_user_state'] == 1) {
                        if (count($chief_inspector) < 2) {
                            if (count($founder) == 0 || $i < $founder[0]['idk']) {
                                $invite_user[$i]['admin_id'] = $admin_user['admin_id'];
                                $invite_user[$i]['idk'] = $i;
                                $chief_inspector[] = $invite_user[$i];
                            }
                        }
                    }

                    //店主
                    if (!in_array($shop_order_admin_user['admin_id'], array('chief_inspector', 'founder')) && !empty($admin_user['admin_id']) && $admin_user['admin_id'] === 'shop_manager' && $admin_user['admin_user_state'] == 1) {
                        if (count($shop_manager) < 3) {
                            $founder_num = count($founder);
                            $chief_num = count($chief_inspector);
                            if (($founder_num == 0 && $chief_num == 0) || ($founder_num > 0 && $chief_num == 0 && $i < $founder[0]['idk']) || ($founder_num == 0 && $chief_num > 0 && $i < $chief_inspector[0]['idk']) || ($founder_num > 0 && $chief_num > 0 && $i < $founder[0]['idk'] && $i < $chief_inspector[0]['idk'])) {
                                $invite_user[$i]['admin_id'] = $admin_user['admin_id'];
                                $invite_user[$i]['idk'] = $i;
                                $shop_manager[] = $invite_user[$i];
                            }
                        }
                    }


                    if (count($founder) >= 2 && count($chief_inspector) >= 2 && count($shop_manager) >= 3) {
                        break;
                    }
                }


                //获取订单--商品sku
                $shop_order_id = $shop_order['shop_order_id']; //订单ID
                $shop_order_goods = object(parent::TABLE_SHOP_ORDER_GOODS)->find($shop_order_id); //获取唯一shop_order_goods

                //判断是否存在订单--以及订单对应的商品唯一sku
                if (empty($shop_order_goods) || empty($shop_order_goods['shop_goods_sku_id'])) {
                    return $invite_info;
                }
                //商品sku_id
                $shop_goods_sku_id = $shop_order_goods['shop_goods_sku_id'];

                //获取sku详细信息
                $shop_goods_sku = object(parent::TABLE_SHOP_GOODS_SKU)->find($shop_goods_sku_id);
                if (empty($shop_goods_sku)) {
                    return $invite_info;
                }

                $shop_goods_sku_json = cmd(array($shop_goods_sku['shop_goods_sku_json']), 'json decode');
                if (empty($shop_goods_sku_json) || empty($shop_goods_sku_json['royalty_reward'])) {
                    return $invite_info;
                }
                $royalty_reward = $shop_goods_sku_json['royalty_reward'];

                //店主差价
                if (!in_array($shop_order_admin_user['admin_id'], array('shop_manager', 'chief_inspector', 'founder')) && !empty($royalty_reward['shop_manager_difference'])) {
                    $shop_manager_difference = $royalty_reward['shop_manager_difference'];
                } else {
                    $shop_manager_difference = 0;
                }
                //店主一、二级提成
                if (!empty($shop_goods_sku_json) && !empty($royalty_reward['shop_manager_royalty'])) {
                    $shop_manager_royalty = $royalty_reward['shop_manager_royalty'];
                    $shop_manager_one_royalty = !empty($shop_manager_royalty[0]) ? $shop_manager_royalty[0] : 0;
                    $shop_manager_two_royalty = !empty($shop_manager_royalty[1]) ? $shop_manager_royalty[1] : 0;
                } else {
                    $shop_manager_one_royalty = 0;
                    $shop_manager_two_royalty = 0;
                }

                //店主提成--差价奖励
                if (!empty($shop_manager)) {
                    for ($m = 0; $m < count($shop_manager); $m++) {
                        if (!in_array($shop_order_admin_user['admin_id'], array('shop_manager', 'chief_inspector', 'founder'))) {
                            if ($m === 0) {
                                $shop_manager[$m]['rmb_award'] = $shop_manager_difference * $shop_order_goods_number;  //店主差价
                                $shop_manager[$m]['type'] = 1;
                            }
                            if ($m === 1) {
                                $shop_manager[$m]['rmb_award'] = $shop_manager_one_royalty * $shop_order_goods_number; //店主一级提成
                                $shop_manager[$m]['type'] = 2;
                            }
                            if ($m === 2) {
                                $shop_manager[$m]['rmb_award'] = $shop_manager_two_royalty * $shop_order_goods_number; //店主二级提成
                                $shop_manager[$m]['type'] = 2;
                            }
                            $invite_info[] = $shop_manager[$m];
                        }
                        if ($shop_order_admin_user['admin_id'] === 'shop_manager') {
                            if ($m === 0) {
                                $shop_manager[$m]['rmb_award'] = $shop_manager_one_royalty * $shop_order_goods_number;  //店主差价
                                $shop_manager[$m]['type'] = 2;
                            }
                            if ($m === 1) {
                                $shop_manager[$m]['rmb_award'] = $shop_manager_two_royalty * $shop_order_goods_number; //店主一级提成
                                $shop_manager[$m]['type'] = 2;
                            }
                            $invite_info[] = $shop_manager[$m];
                        }
                    }
                }


                //总监差价
                if (!in_array($shop_order_admin_user['admin_id'], array('chief_inspector', 'founder')) && !empty($royalty_reward['chief_inspector_difference'])) {
                    $chief_inspector_difference = $royalty_reward['chief_inspector_difference'];
                } else {
                    $chief_inspector_difference = 0;
                }
                
                //总监提成
                if (!empty($royalty_reward['chief_inspector_royalty'])) {
                    $chief_inspector_royaltys = $royalty_reward['chief_inspector_royalty'];
                    $chief_inspector_royalty = (!empty($chief_inspector_royaltys[0]) ? $chief_inspector_royaltys[0] : 0) * $shop_order_goods_number;
                } else {
                    $chief_inspector_royalty = 0;
                }
                //总监提成--差价奖励
                if (!empty($chief_inspector)) {
                    //如果没有店主，则总监差价=店主差价+总监差价
                    if(empty($shop_manager)){
                        $chief_inspector_difference += $shop_manager_difference; 
                    }
                    for ($m = 0; $m < count($chief_inspector); $m++) {
                        if (!in_array($shop_order_admin_user['admin_id'], array('chief_inspector', 'founder'))) {
                            if ($m === 0) {
                                $chief_inspector[$m]['rmb_award'] = $chief_inspector_difference * $shop_order_goods_number;  //差价
                                $chief_inspector[$m]['type'] = 1;
                            }
                            if ($m === 1) {
                                $chief_inspector[$m]['rmb_award'] = $chief_inspector_royalty * $shop_order_goods_number;
                                $chief_inspector[$m]['type'] = 2;
                            }
                            if (empty($invite_info)) {
                                $invite_info[] = $chief_inspector[$m];
                            } else {
                                array_push($invite_info, $chief_inspector[$m]);
                            }
                        }
                        if ($shop_order_admin_user['admin_id'] === 'chief_inspector') {
                            if ($m === 0) {
                                $chief_inspector[$m]['rmb_award'] = $chief_inspector_royalty * $shop_order_goods_number;
                                $chief_inspector[$m]['type'] = 2;
                            }
                            if (empty($invite_info)) {
                                $invite_info[] = $chief_inspector[$m];
                            } else {
                                array_push($invite_info, $chief_inspector[$m]);
                            }
                        }
                    }
                }

                //创始人差价
                if ($shop_order_admin_user['admin_id'] !== 'founder' && !empty($royalty_reward['founder_difference'])) {
                    $founder_difference = $royalty_reward['founder_difference'];
                } else {
                    $founder_difference = 0;
                }

                //创始人提成
                if (!empty($royalty_reward['founder_royalty'])) {
                    $founder_royaltys = $royalty_reward['founder_royalty'];
                    $founder_royalty = !empty($founder_royaltys[0]) ? $founder_royaltys[0] : 0;
                } else {
                    $founder_royalty = 0;
                }
                //创始人区域管理费
                if (!empty($royalty_reward['founder_region_money'])) {
                    $founder_region_money = $royalty_reward['founder_region_money'];
                } else {
                    $founder_region_money = 0;
                }


                //创始人提成--差价奖励
                if (!empty($founder)) {
                    //如果没有总监
                    if(empty($chief_inspector)){
                        if(empty($shop_manager)){
                            $founder_difference = $founder_difference + $chief_inspector_difference + $shop_manager_difference;
                        }else{
                            $founder_difference += $chief_inspector_difference;
                        }
                    }
                    for ($m = 0; $m < count($founder); $m++) {
                        //用户身份如果不是创始人则有利差和提成
                        if ($shop_order_admin_user['admin_id'] !== 'founder') {
                            //如果下单用户是创始人
                            if ($m === 0) {
                                //差价
                                $founder[$m]['rmb_award'] = $founder_difference * $shop_order_goods_number;  
                                $founder[$m]['type'] = 1;
                            }
                            if ($m === 1) {
                                //提成
                                $founder[$m]['rmb_award'] = $founder_royalty * $shop_order_goods_number;
                                $founder[$m]['type'] = 2;
                            }
                            if (empty($invite_info)) {
                                $invite_info[] = $founder[$m];
                            } else {
                                array_push($invite_info, $founder[$m]);
                            }
                        } else {
                            //  如果下单用户是区域负责人、则只有提成
                            if ($m === 0) {
                                //提成
                                $founder[$m]['rmb_award'] = $founder_royalty * $shop_order_goods_number;  //提成
                                $founder[$m]['type'] = 2;
                            }
                            if (empty($invite_info)) {
                                $invite_info[] = $founder[$m];
                            } else {
                                array_push($invite_info, $founder[$m]);
                            }
                        }
                    }
                }

                //判断是否给间推创始人区域管理费
                if (!empty($agent_info)) {
                    $agent_reward_info = array(
                        'user_id' => $agent_info['user_id'],
                        'rmb_award' => $founder_region_money * $shop_order_goods_number,
                        'admin_id' => 'founder',
                        'type' => 3
                    );
                    if (empty($invite_info)) {
                        $invite_info[] = $agent_reward_info;
                    } else {
                        array_push($invite_info, $agent_reward_info);
                    }
                }


                return $invite_info;
                break;
            default:
                return $invite_info;
                break;
        }
    }

    //测试用 -- 勿删
    public function update_the_order($shop_order_id = '10011', $info = array())
    {
        $info = cmd(array($info), "json encode");
        db(parent::DB_APPLICATION_ID)
            ->table('shop_order')
            ->where(array('shop_order_id =[+]', (string) $shop_order_id))
            ->update(['shop_order_json' => $info]);
    }


    //===========================================
    // 获取一个值
    //===========================================


    //查询店主、总监、区域负责人（创始人）购买商品优惠金额
    public function get_discount($call_where = array(), $field = '')
    {
        if (empty($call_where) || empty($field)) {
            return 0;
        }

        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where, $field), function ($call_where, $field) {
            $row = db(parent::DB_APPLICATION_ID)
                ->table('shop_order')
                ->call('where', $call_where)
                ->find("SUM($field) AS sum");
            return $row['sum'] ?: 0;
        });
    }

    /**
     * 获取总金额
     * @author green
     *
     * @param  array  $call_where  [查询条件]
     * @param  array  $call_joinon [连表信息]
     * @return integer
     */
    public function get_sum_money($call_where = array(), $call_joinon = array())
    {
         return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where, $call_joinon), function($call_where, $call_joinon) {
            // 左连店铺表
            $call_joinon[] = array(
                'table' => 'shop s',
                'type' => 'left',
                'on' => 's.shop_id = so.shop_id',
            );

            // 左连用户表
            $call_joinon[] = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = so.user_id',
            );

            $data = db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->call('joinon', $call_joinon)
                ->call('where', $call_where)
                ->find('SUM(shop_order_money) as money');

            return intval($data['money']);
        });
    }


    //===========================================
    // 查询数据
    //===========================================



    //查询订单优惠金额列表
    public function select_discount_page($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $call_where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $call_limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            $limit = array(
                (isset($call_limit[0]) ? $call_limit[0] : 0),
                (isset($call_limit[1]) ? $call_limit[1] : 0)
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
            $join = array();
            if (!empty($config['join'])) {
                $join = $config['join'];
            }

            //先获取总条数
            $find_data = db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->joinon($join)
                ->call('where', $call_where)
                ->find('count(*) as count');
            if (empty($find_data['count'])) {
                return $data;
            } else {
                $data['row_count'] = $find_data['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count'] / $data['page_size']);
                    $data['page_now'] = ceil($limit[0] / $data['page_size']) + 1; //当前页数
                }
            }


            $data['data'] =  db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->joinon($join)
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $call_limit)
                ->select($select);

            return $data;
        });
    }


    /**
     * 查询指定数量订单
     */
    public function select_num($config)
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $limit   = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : 5;
            $select  = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            return db(parent::DB_APPLICATION_ID)
                ->table('shop_order')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->limit($limit)
                ->select($select);
        });
    }


    /**
     * 发送购买成功用户模板消息--易淘
     */

    private function _send_pay_message($shop_order = array())
    {

        //发送消息给用户
        $user_oauth = object(parent::TABLE_USER_OAUTH)->find($shop_order['user_id']);
        $goods_where = array(
            array('shop_order_id =[+]', $shop_order['shop_order_id'])
        );
        $goods_order = object(parent::TABLE_SHOP_ORDER_GOODS)->find_where($goods_where);
        $goods_name = !empty($goods_order['shop_order_goods_name']) ? $goods_order['shop_order_goods_name'] : '未知商品';
        if (!empty($user_oauth['user_oauth_wx_key'])) {

            $shop_order_money = ($shop_order['shop_order_money'] / 100) . '元';
            $pay_time = date('Y年m月d日  H时i分s秒', time());
            $shop_order_id = $shop_order['shop_order_id'];

            $data = array(
                "touser" => $user_oauth['user_oauth_wx_key'], //微信登录--用户openID
                "template_id" => "vRVlvPdn5HIbLs9HPSXdgXOv-SlKTPikIlggh8CEf90", //模板ID
                "url" => "http://yitaoss.com/#/pagesB/my-order/my-order", //点击跳转链接地址  
                "topcolor" => "#FF0000",
                "data" => array(
                    "first" => array(
                        "value" => "恭喜您购买成功，我们将尽快为您发货",
                        "color" => "red"
                    ),
                    "keyword1" => array(
                        "value" => $goods_name,
                        "color" => "#173177"
                    ),
                    "keyword2" => array(
                        "value" => $shop_order_money,
                        "color" => "#173177"
                    ),
                    "keyword3" => array(
                        "value" => $pay_time,
                        "color" => "#173177"
                    ),
                    "keyword4" => array(
                        "value" => $shop_order_id,
                        "color" => "#173177"
                    ),
                    "remark" => array(
                        "value" => "您已经成功购买" . $goods_name . "商品，我们将尽快为您发货，如有疑问您可以咨询客服热线：0816-3509868",
                        "color" => "#173177"
                    )
                )

            );
            $data = cmd(array($data), 'json encode');
            $time_out = 30;

            object(parent::PLUGIN_WEIXIN_MESSAGE_SEND)->template_message($data, $time_out);
        }


        //用户下单--发送消息给管理员
        $where = array(
            array('admin_id=[+]', 'admin1'),
            array('admin_user_state=1')
        );
        $config = array(
            'where' => $where,
        );
        $admin_users = object(parent::TABLE_ADMIN_USER)->select($config);
        if (!empty($admin_users)) {
            //获取下单人电话
            $user = object(parent::TABLE_USER)->find_id_or_phone($shop_order['user_id']);
            $user_phone = isset($user['user_phone']) && !empty($user['user_phone']) ? $user['user_phone'] : '';

            //查询下单用户信息
            $user_info = object(parent::TABLE_USER)->find_join($shop_order['user_id']);
            //注册用户姓名--昵称
            $user_name = !empty($user_info['user_compellation']) ? $user_info['user_compellation'] : !empty($user_info['user_nickname']) ? $user_info['user_nickname'] : '匿名新用户';

            for ($i = 0; $i < count($admin_users); $i++) {
                $user_oauth = object(parent::TABLE_USER_OAUTH)->find($admin_users[$i]['user_id']);
                if (!empty($user_oauth['user_oauth_wx_key'])) {
                    $shop_order_money = ($shop_order['shop_order_money'] / 100) . '元';
                    $shop_order_id = $shop_order['shop_order_id'];
                    $address = $shop_order['user_address_province'] . $shop_order['user_address_city'] . $shop_order['user_address_district'] . $shop_order['user_address_details'];
                    $data = array(
                        "touser" => $user_oauth['user_oauth_wx_key'], //微信登录--用户openID
                        "template_id" => "OqgQkaJslceqIR_gvSjjIScBABdgXjeDQbaBZ2teLcM", //模板ID
                        "url" => "http://admin.eonfox.com/default.html?app=yitaoshop#/shop-orderList/?sort=update_time_desc", //点击跳转链接地址  
                        "topcolor" => "#FF0000",
                        "data" => array(
                            "first" => array(
                                "value" => "爱尚购平台有新的订单，请登录管理后台查看",
                                "color" => "#ccc"
                            ),
                            "keyword1" => array(
                                "value" => $goods_name,
                                "color" => "#173177"
                            ),
                            "keyword2" => array(
                                "value" => $shop_order_money,
                                "color" => "#173177"
                            ),
                            "keyword3" => array(
                                "value" => $user_name,
                                "color" => "#173177"
                            ),
                            "keyword4" => array(
                                "value" => $user_phone,
                                "color" => "#173177"
                            ),
                            "keyword5" => array(
                                "value" => $address,
                                "color" => "#173177"
                            ),
                            "remark" => array(
                                "value" => "我们把最好、最新鲜的产品交给消费者。全国咨询热线：0816-3509868",
                                "color" => "#173177"
                            )
                        )

                    );
                    $data = cmd(array($data), 'json encode');
                    $time_out = 30;

                    object(parent::PLUGIN_WEIXIN_MESSAGE_SEND)->template_message($data, $time_out);
                }
            }
        }
    }

    public function find_num($call_where=array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where), function ($call_where) {
            return db(parent::DB_APPLICATION_ID)
                ->table('shop_order')
                ->call('where', $call_where)
                ->find('count(*) as count');
        });
    }

    public function find_sum($call_where=array(),$field='shop_order_money'){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where,$field), function ($call_where,$field) {
            return db(parent::DB_APPLICATION_ID)
                ->table('shop_order')
                ->call('where', $call_where)
                ->find("sum($field) as sum");
        });
    }

    //根据条件统计订单表--总金额（$field）（别名）
    public function find_sum_alias($call_where=array(),$field='shop_order_money'){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where,$field), function ($call_where,$field) {
            // 左连店铺表
            $call_joinon[] = array(
                'table' => 'shop s',
                'type' => 'left',
                'on' => 's.shop_id = so.shop_id',
            );

            // 左连用户表
            $call_joinon[] = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = so.user_id'
            );
            return db(parent::DB_APPLICATION_ID)
                ->table('shop_order so')
                ->call('joinon', $call_joinon)
                ->call('where', $call_where)
                ->find("sum($field) as sum");
        });
    }

    /**
     * 获取代理区域的总销售金额
     */
    public function select_sum($province,$city,$district){
        $start = mktime(0,0,0,date('m'),1,date('Y'));
        $end = mktime(23,59,59,date('m'),date('t'),date('Y'));
        return db(parent::DB_APPLICATION_ID)
            ->table('shop_order')
            ->where(
                array("shop_order_pay_state=[]", 1),
                array("shop_order_pay_money_state=[]",1),
                array("shop_order_pay_time >=[]",$start),
                array("shop_order_pay_time <=[]",$end),
                array("user_address_province <=[]",$province),
                array("user_address_city <=[]",$city),
                array("user_address_district <=[]",$district)

            )
            ->select('sum(shop_order_pay_money) as sum ,user_address_district,user_address_city,user_address_province,user_address_country');
    }
}