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

/**
 * 拼团订单
 */
class shop_order_group extends main
{

    /**
     * 缓存的键列表
     * 
     * @var string
     */
    const CACHE_KEY = array(__CLASS__, "shop_goods_group");


    /**
     * 数据检测
     * 
     * @var array
     */
    public $check = array(
        'shop_order_group_id' => array(
            'args' => array(
                'exist'=> array('缺少拼团订单ID参数'),
                'echo' => array('拼团订单ID不合法'),
                '!null'=> array('拼团订单ID不能为空'),
            ),
        ),
    );

    /**
     * 获取主键ID
     * @return string
     */
    public function get_unique_id()
    {
        return cmd(array(22), 'random autoincrement');
    }


    //===========================================
    // 操作数据
    //===========================================


    public function insert($data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_order_group')
            ->call('data', $call_data)
            ->insert($data);

        // 清理缓存
        if ($bool) {
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }

        return $bool;
    }

    public function update($where = array(), $data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_order_group')
            ->call('where', $where)
            ->call('data', $call_data)
            ->update($data);

        // 清理缓存
        if ($bool) {
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
        return $bool;
    }

    public function remove($shop_order_group_id = '')
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_order_group')
            ->where(array('shop_order_group_id = [+]', $shop_order_group_id))
            ->delete();

        // 清理缓存
        if ($bool) {
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }

        return $bool;
    }



	/**
     * 更新团购订单的状态
	 * 正在拼团的订单 && 拼团的商品状态
	 * 团购商品状态：0已结束，1进行中，2未开始 （shop_goods_group_state）
	 * 
     * @param  void
     * @return void
     */
    public function update_state(){
    	$sql_join_from_goods_state = object(parent::TABLE_SHOP_GOODS_GROUP)->sql_join_goods_state('sog');
    	$bool = db(parent::DB_APPLICATION_ID)
        ->table('shop_order_group as sog')
		->data(array(
            'shop_order_group_state = [-]',
            '( IF(('.$sql_join_from_goods_state.') = 1, 2, 0) )',
            true
        ))
        ->where(
        	array('sog.shop_order_group_state = 2')
		)
        ->update(array(
			'shop_order_group_update_time' => time()
		)/*, function($p){
			printexit($p);
		}*/);
		
        //清理缓存 && 异步退款
        if( $bool ){
            object(parent::CACHE)->clear(self::CACHE_KEY);
			//触发事件，异步团购订单退款
			object(parent::REQUEST_SHOP)->event_order_group_refund();
        }
    }






    //===========================================
    // 查询数据
    //===========================================
    
    /**
	 * 随机获取 一条需要退款的数据
	 * 状态：0拼团失败，1拼团成功，2拼团中 （shop_order_group_state == 0）
	 * 退款状态：0未退款，1已退款（shop_order_group_refund_state == 0）
	 * 支付状态：支付：0否，1是(shop_order_group_pay == 1)
	 * 
	 * @param	void
	 * @return	array
	 */
	public function find_random_refund(){
		return db(parent::DB_APPLICATION_ID)
        ->table('shop_order_group')
		//退款状态为0 拼团状态为0
        ->where(
        	array('shop_order_group_refund_state = 0'), 
        	array('[and] shop_order_group_state = 0'), 
        	array('[and] shop_order_group_pay = 1')
			)
        ->find();
	}
	
	
	
    public function find($shop_order_group_id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_order_group_id), function($shop_order_group_id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('shop_order_group')
                ->where(array('shop_order_group_id = [+]', $shop_order_group_id))
                ->find();
        });
    }



    public function find_where($call_where = array(), $find = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where, $find), function($call_where, $find) {
            return db(parent::DB_APPLICATION_ID)
                ->table('shop_order_group')
                ->call('where', $call_where)
                ->find($find);
        });
    }

    public function select($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config) {
            $where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            return db(parent::DB_APPLICATION_ID)
                ->table('shop_order_group')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select);
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
                ->table('shop_order_group sog')
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
                ->table('shop_order_group sog')
                ->call('joinon', $call_joinon)
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $limit)
                ->select($select);

            return $data;
        });
    }


    //===========================================
    // SQL语句
    //===========================================


    public function sql_select($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config) {
            $where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            return db(parent::DB_APPLICATION_ID)
                ->table('shop_order_group')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select, function($e) {
                    return $e['query']['select'];
                });
        });
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
        $pay_method = $order['order_minus_method'];

        // 查询拼团订单
        $shop_order_group = object(parent::TABLE_SHOP_ORDER_GROUP)->find($order['order_sign']);
        if (empty($shop_order_group)) return false;

        // 锁表——拼团订单
        $lock_ids = array();
        $lock_shop_order_group = object(parent::TABLE_LOCK)->start('shop_order_group_id', $shop_order_group['shop_order_group_id'], parent::LOCK_PAY_STATE);
        if ($lock_shop_order_group) {
            $lock_ids[] = $lock_shop_order_group;
        } else {
            throw new error('请勿重复操作');
        }
        // 锁表——拼团商品
        $lock_shop_goods_group = object(parent::TABLE_LOCK)->start('shop_goods_group_id', $shop_order_group['shop_goods_group_id'], parent::LOCK_PAY_STATE);
        if ($lock_shop_goods_group) {
            $lock_ids[] = $lock_shop_goods_group;
        } else {
            throw new error('请勿重复操作');
        }
        // 锁表——用户钱包
        if ($pay_method === parent::PAY_METHOD_USER_MONEY) {
            $id = object(parent::TABLE_LOCK)->start('user_id', $order['order_minus_account_id'], parent::LOCK_MONEY);
            if ($id) {
                $lock_ids[] = $id;
            } else {
                throw new error('请勿重复操作');
            }
        }
        // 锁表——用户积分
        if ($pay_method === parent::PAY_METHOD_USER_CREDIT) {
            $id = object(parent::TABLE_LOCK)->start('user_id', $order['order_minus_account_id'], parent::LOCK_CREDIT);
            if ($id) {
                $lock_ids[] = $id;
            } else {
                throw new error('请勿重复操作');
            }
        }

        // 开启事务
        db(parent::DB_APPLICATION_ID)->query('START TRANSACTION');

        // 更新拼团订单状态
        $res = $this->_payment_update_shopordergroup_state($order['order_id'], $shop_order_group, $pay_method);
        if ($res === false) {
            echo '失败：更新商城订单状态';
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            object(parent::TABLE_LOCK)->close($lock_ids);
            return false;
        }

        // 增加拼团人数
        $res = $this->_payment_increase_people($shop_order_group['shop_goods_group_id']);
        if (!$res) {
            echo '失败：增加拼团人数';
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            object(parent::TABLE_LOCK)->close($lock_ids);
            return false;
        }

        // 减少买家资产
        $res = $this->_payment_decrease_buyer_wealth($shop_order_group, $pay_method);
        if ($res === false) {
            echo '失败：减少买家资产';
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            object(parent::TABLE_LOCK)->close($lock_ids);
            return false;
        } else {
            $order['order_minus_transaction_id'] = $res;
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

        return true;
    }

    /**
     * 更新拼团订单支付状态
     * @author green
     *
     * @param  string $order_id         [订单ID]
     * @param  array  $shop_order_group [拼团订单数据]
     * @param  string $pay_method       [支付方式]
     * @return bool
     */
    private function _payment_update_shopordergroup_state($order_id, $shop_order_group, $pay_method)
    {
        $timestamp = time();
        $where = array(
            array('shop_order_group_id = [+]', $shop_order_group['shop_order_group_id']),
            array('shop_order_group_pay = 0'),
        );
        $data = array(
            'order_id' => $order_id,
            'shop_order_group_pay' => 1,
            'shop_order_group_pay_time' => $timestamp,
            'shop_order_group_pay_method' => $pay_method,
            'shop_order_group_update_time' => $timestamp,
        );

        return $this->update($where, $data);
    }

    /**
     * 减少买家资产
     * @Author green
     *
     * @param  array  $shop_order_group       [商城订单]
     * @param  string $pay_method       [支付方式]
     * @return mixed                    [false,或者数据主键ID]
     */
    private function _payment_decrease_buyer_wealth($shop_order_group, $pay_method)
    {
        // 钱包支付
        if ($pay_method === parent::PAY_METHOD_USER_MONEY) {
            // 查询钱包数据
            $user_money = object(parent::TABLE_USER_MONEY)->find_now_data($shop_order_group['user_id']);

            // 更新钱包
            $id = object(parent::TABLE_USER_MONEY)->insert_minus(array(
                'user_id' => $shop_order_group['user_id'],
                'user_money_join_id' => $user_money['user_money_id'],
                'user_money_minus' => $shop_order_group['shop_order_group_price'],
                'user_money_value' => $user_money['user_money_value'] - $shop_order_group['shop_order_group_price'],
                'user_money_type' => parent::TRANSACTION_TYPE_SHOP_ORDER_GROUP,
            ));

            return $id;
        }

        // 积分支付
        if ($pay_method === parent::PAY_METHOD_USER_CREDIT) {
            // 查询积分数据
            $user_credit = object(parent::TABLE_USER_CREDIT)->find_now_data($shop_order_group['user_id']);
            // 更新积分
            $id = object(parent::TABLE_USER_CREDIT)->insert_minus(array(
                'user_id' => $shop_order_group['user_id'],
                'user_credit_join_id' => $user_credit['user_credit_id'],
                'user_credit_minus' => $shop_order_group['shop_order_group_price'],
                'user_credit_value' => $user_credit['user_credit_value'] - $shop_order_group['shop_order_group_price'],
                'user_credit_type' => parent::TRANSACTION_TYPE_SHOP_ORDER_GROUP,
            ));

            return $id;
        }

        return '';
    }

    /**
     * 增加拼团人数
     * @author green
     *
     * @param  string $shop_goods_group_id [拼团商品ID]
     * @return bool
     */
    private function _payment_increase_people($shop_goods_group_id = '')
    {
        // 查询拼团商品信息
        $shop_goods_group = object(parent::TABLE_SHOP_GOODS_GROUP)->find($shop_goods_group_id);
        $people_now = $shop_goods_group['shop_goods_group_people_now'] + 1;

        $update = array(
            'shop_goods_group_people_now' => $people_now,
            'shop_goods_group_update_time' => time(),
        );

        // 是否拼团成功
        if ($people_now == $shop_goods_group['shop_goods_group_people']) {
            $update['shop_goods_group_success'] = 1;
            $update['shop_goods_group_success_time'] = time();
            $update['shop_goods_group_state'] = 0;
        }

        // 更新
        $res = db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_group')
            ->where(array('shop_goods_group_id = [+]', $shop_goods_group_id))
            ->update($update);

        if (isset($update['shop_goods_group_success'])) {
            $this->destruct_success($shop_goods_group_id);
        }

        return $res;
    }


    //===========================================
    // 拼团成功
    //===========================================


    /**
     * 拼团成功
     * @author green
     * 
     * @param  string $shop_goods_group_id [拼团商品ID]
     * @return bool
     */
    public function destruct_success($shop_goods_group_id)
    {
        // 获取数据库配置
        $info = db(parent::DB_APPLICATION_ID)->info();
        $db_config = $info['config'];
        $db_application_id = parent::DB_APPLICATION_ID;

        destruct(__METHOD__ . '('.$shop_goods_group_id.')', true, array($db_config, $db_application_id), function($db_config, $db_application_id) use($shop_goods_group_id) {
            // 连接数据库
            db($db_application_id, true, $db_config);

            // 查询拼团商品信息
            $shop_goods_group = object(parent::TABLE_SHOP_GOODS_GROUP)->find($shop_goods_group_id);

            // 查询商品信息
            $shop_goods = object(parent::TABLE_SHOP_GOODS)->find($shop_goods_group['shop_goods_id']);

            // 查询拼团订单
            $list_ShopOrderGroup = $this->select(array('where' => array(
                array('shop_goods_group_id = [+]', $shop_goods_group_id),
                array('shop_order_group_pay = 1'),
                array('shop_order_group_trash = 0'),
            )));

            // 订单信息
            $timestamp = time();
            $shop_order = array(
                'shop_order_id' => '',
                'user_id' => '',
                'owner_user_id' => $shop_goods['user_id'],
                'shop_id' => $shop_goods['shop_id'],
                'shop_order_index' => $shop_goods['shop_goods_index'],
                'shop_order_property' => $shop_goods['shop_goods_property'],
                'shop_order_money' => $shop_goods_group['shop_goods_group_price'],
                'shop_order_pay_state' => 1,
                'shop_order_state' => 1,
                'shop_order_insert_time' => $timestamp,
                'shop_order_update_time' => $timestamp,
            );

            // 订单商品信息
            $shop_order_goods = array(
                'shop_order_goods_id' => '',
                'shop_order_id' => '',
                'owner_user_id' =>  $shop_goods['user_id'],
                'shop_goods_id' => $shop_goods['shop_goods_id'],
                'shop_goods_sku_id' => $shop_goods_group['shop_goods_sku_id'],
                'shop_goods_sn' => $shop_goods['shop_goods_sn'],
                'shop_goods_property' => $shop_goods['shop_goods_property'],
                'shop_goods_index' => $shop_goods['shop_goods_index'],
                'shop_order_goods_name' => $shop_goods['shop_goods_name'],
                'shop_order_goods_image_id' => object(parent::TABLE_SHOP_GOODS_IMAGE)->get_main_img_id($shop_goods['shop_goods_id']),
                'shop_order_goods_price' => $shop_goods_group['shop_goods_group_price'],
                'shop_order_goods_number' => 1,
                'shop_order_goods_json' => cmd(array($shop_goods), 'json encode'),
                'shop_order_goods_time' => $timestamp,
            );

            // 循环拼团订单
            foreach ($list_ShopOrderGroup as $i) {
                // 插入商城订单
                $shop_order['shop_order_id'] = object(parent::TABLE_SHOP_ORDER)->get_unique_id();
                $shop_order['user_id'] = $i['user_id'];
                $shop_order['shop_order_pay_time'] = $i['shop_order_group_pay_time'];
                $shop_order['user_address_consignee'] = $i['user_address_consignee'];
                $shop_order['user_address_phone'] = $i['user_address_phone'];
                $shop_order['user_address_province'] = $i['user_address_province'];
                $shop_order['user_address_city'] = $i['user_address_city'];
                $shop_order['user_address_district'] = $i['user_address_district'];
                $shop_order['user_address_details'] = $i['user_address_details'];
                $res = object(parent::TABLE_SHOP_ORDER)->insert($shop_order);

                // 插入商城订单商品
                $shop_order_goods['shop_order_goods_id'] = object(parent::TABLE_SHOP_ORDER_GOODS)->get_unique_id();
                $shop_order_goods['shop_order_id'] = $shop_order['shop_order_id'];
                $res = object(parent::TABLE_SHOP_ORDER_GOODS)->insert($shop_order_goods);

                // 更新拼团订单状态
                $this->update(array(array('shop_order_group_id = [+]', $i['shop_order_group_id'])), array(
                    'shop_order_id' => $shop_order['shop_order_id'],
                    'shop_order_group_state' => 1,
                    'shop_order_group_update_time' => $timestamp,
                ));
            }
        });
    }


    //===========================================
    // 拼团失败
    //===========================================

	/**
     * 拼团失败退款
     *
     * @param  array $shop_order_group 拼团订单数据
     * @return bool
     */
	public function fail_refund($shop_order_group){
		db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务
		
		// 退回用户资产
        $user_money_id = object(parent::TABLE_USER_MONEY)->insert_plus(array(
            'user_id' => $shop_order_group['user_id'],
            'user_money_plus' => $shop_order_group['shop_order_group_price'],
            'user_money_type' => parent::TRANSACTION_TYPE_SHOP_ORDER_GROUP_REFUND,
        ));
		
		//失败
        if( empty($user_money_id) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            return false;
        }
		
		//生成订单
        $order_insert = array(
            "order_id" => $this->get_unique_id(),
            "order_type" => parent::TRANSACTION_TYPE_SHOP_ORDER_GROUP_REFUND,
            "order_comment" => "商城拼团失败而退款",
            "order_plus_method" => parent::PAY_METHOD_USER_MONEY,
            "order_plus_account_id" => $shop_order_group['user_id'],
            "order_plus_value" => $shop_order_group['shop_order_group_price'],
            "order_plus_transaction_id" => $user_money_id,//交易号
            "order_plus_update_time" => time(),
            
            "order_sign" => $shop_order_group['shop_order_group_id'],//拼团订单ID
            
            "order_state" => 1,//确定订单
            "order_pay_state" => 1,//已支付
            "order_pay_time" => time(),
            "order_insert_time" => time(),
        );
		
        if( !object(parent::TABLE_ORDER)->insert($order_insert) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
        	return false;
        }
		
		//更新 团购订单状态
        $where = array(
            array('shop_order_group_id = [+]', $shop_order_group['shop_order_group_id']),
            array('[and] shop_order_group_refund_state = 0'), 
        	array('[and] shop_order_group_state = 0'), 
        	array('[and] shop_order_group_pay = 1')
        );
        $data = array(
            'shop_order_group_refund_state' => 1,
            'shop_order_group_refund_time' => time(),
            'shop_order_group_update_time' => time(),
            'shop_order_group_refund_order' => $order_insert['order_id']
        );
		
		if( !$this->update($where, $data) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
        	return false;
        }
		
		
		db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
		return true;
	}





    /**
     * 拼团失败
     * @author green
     *
     * @param  string $shop_goods_group_id [拼团商品主键ID]
     * @return void
     */
    public function destruct_fail($shop_goods_group_id)
    {
        // 获取数据库配置
        $info = db(parent::DB_APPLICATION_ID)->info();
        $db_config = $info['config'];
        $db_application_id = parent::DB_APPLICATION_ID;

        destruct(__METHOD__ . '('.$shop_goods_group_id.')', true, array($db_config, $db_application_id), function($db_config, $db_application_id) use($shop_goods_group_id) {
            // 查询拼团订单
            $shop_order_groups = $this->select(array(
                'select' => array(
                    'user_id',
                    'shop_order_group_price'
                ),
                'where' => array(
                    array('shop_goods_group_id = [+]', $shop_goods_group_id),
                    array('shop_order_group_pay = 1'),
                    array('shop_order_group_trash = 0'),
                ),
            ));

            // 循环拼团订单
            foreach ($shop_order_groups as $i) {
                // 退回用户资产
                $user_money_id = object(parent::TABLE_USER_MONEY)->insert_plus(array(
                    'user_id' => $i['user_id'],
                    'user_money_plus' => $i['shop_order_group_price'],
                    'user_money_type' => '拼团失败退款',
                ));

                 // 插入订单
                $res = object(parent::TABLE_ORDER)->insert(array(
                    'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
                    'order_type' => '拼团失败退款',
                    'order_comment' => '商城拼团',
                    'order_plus_method' => parent::PAY_METHOD_USER_MONEY,
                    'order_plus_account_id' => $i['user_id'],
                    'order_plus_value' => $i['shop_order_group_price'],
                    'order_plus_transaction_id' => $user_money_id,
                    'order_state' => 1,
                    'order_pay_state' => 1,
                    'order_sign' => $i['shop_order_group_id'],
                    'order_insert_time' => time(),
                ));
            }

            // 更新拼团订单状态
            $where = array(
                array('shop_goods_group_id = [+]', $shop_goods_group_id),
                array('shop_order_group_pay = 1'),
            );
            $data = array(
                'shop_order_group_state' => 0,
                'shop_order_group_update_time' => time(),
            );
            $this->update($where, $data);
        });
    }

}