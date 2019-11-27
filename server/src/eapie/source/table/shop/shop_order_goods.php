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

class shop_order_goods extends main
{


    /**
     * 缓存的键列表
     * @var string
     */
    const CACHE_KEY = array(__CLASS__, 'shop_goods', 'shop_goods_sku');

    // 数据检测
    public $check = array(
        "shop_goods_additional_money" => array(
            //参数检测
            'args' => array(
                'echo' => array("商品规格的附加金额数据类型不合法"),
                'match' => array('/^[0-9]{0,}$/', "商品规格的附加金额必须是整数"),
            ),
        ),

        "shop_goods_additional_credit" => array(
            //参数检测
            'args' => array(
                'echo' => array("商品规格的附加积分数据类型不合法"),
                'match' => array('/^[0-9]{0,}$/', "商品规格的附加积分必须是整数"),
            ),
        )
    );

    /**
     * 获取一个id号
     * @return  string
     */
    public function get_unique_id()
    {
        return cmd(array(22), 'random autoincrement');
    }


    //===========================================
    // 操作数据
    //===========================================


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
            ->table('shop_order_goods')
            ->call('data', $call_data)
            ->insert($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }

    /**
     * 发放推荐奖金
     * @param string $shop_order_id 业务订单ID
     */
    public function invite_money_reward($shop_order_id)
    {
        // 测试用
        $result = array();

        // 查询商品
        $shop_goods = object(parent::TABLE_SHOP_ORDER_GOODS)->find($shop_order_id);

        // 判断是否为邀请消费，是则发放邀请奖金
        $invite_user_id = $shop_goods['recommend_user_id'];
        $invite_money = $shop_goods['recommend_money'];

        // 类型为积分商品，奖励金额为0，无邀请人，直接返回
        if ($shop_goods['shop_goods_property'] === 1 && $invite_money === 0 && $invite_user_id === '')
            return false;

        // 奖金已经发放
        if ($shop_goods['recommend_money_order_id'] !== "")
            return false;

        // 初始化数组
        $invite_money_award = array(
            'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
            'order_plus_method' => parent::PAY_METHOD_USER_MONEY,
            'order_plus_account_id' => $invite_user_id,
            'order_plus_value' => $invite_money,
            'order_plus_transaction_id' => '',
            'order_sign' => $shop_order_id,
            'order_state' => 1,
            'order_pay_state' => 1,
            'order_pay_time' => time(),
            'order_insert_time' => time(),
            'order_type' => parent::TRANSACTION_TYPE_RECOMMEND_SALES_REWARD,
        );
        $result['invite_money_reward'] = object(parent::TABLE_USER_MONEY)->reward_money($invite_money_award);


        // 奖金发放成功，记录奖励资金订单ID
        if ($result['invite_money_reward']) {
            $where = array(
                array('shop_order_id=[+]', $shop_order_id)
            );
            $order_update_data = array(
                'shop_order_goods_update_time' => time(),
                'recommend_money_order_id' => $invite_money_award['order_id']
            );
            object(parent::TABLE_SHOP_ORDER_GOODS)->update($where, $order_update_data);
        }


        // 回调事件里面无需返回值，测试使用
        return $result;
    }


    /**
     * 更新数据
     * 
     * @param   array       $where
     * @param   array       $data
     * @param   array       $call_data
     * @return  bool
     */
    public function update($where = array(), $data = array(), $call_data = array())
    {
        if (empty($where) || (empty($data) && empty($call_data))) {
            return false;
        }

        $bool = (bool) db(parent::DB_APPLICATION_ID)
            ->table('shop_order_goods')
            ->call('where', $where)
            ->call('data', $call_data)
            ->update($data);

        if (!empty($bool)) {
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }

        return $bool;
    }

    /**
     * 批量插入数据
     * 
     * @param   array $data 数据，索引数组
     * @return  bool
     */
    public function insert_batch($data = array())
    {
        if (empty($data))
            return false;

        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_order_goods')
            ->batch()
            ->insert($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    //===========================================
    // 查询数据
    //===========================================


    /**
     * 连商品订单表，获取多条数据
     *  $config = array(
     *  'where' => array(), //条件
     *  'orderby' => array(), //排序
     *  'limit' => array(0, page_size), //取出条数，默认不限制
     *  'select' => array(),//查询的字段，可以是数组和字符串
     * );
     * 
     * @param   array   $config
     * @return  array
     */
    public function select_join_shop_order($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $limit   = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select  = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();


            //左连商品限时购表
            $join_shopgoods = array(
                'table' => 'shop_order so',
                'type' => 'left',
                'on' => 'so.shop_order_id = sog.shop_order_id'
            );

            return db(parent::DB_APPLICATION_ID)
                ->table('shop_order_goods sog')
                ->joinon($join_shopgoods)
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select);
        });
    }

    /**
     * 获取多条数据
     *  $config = array(
     *  'where' => array(), //条件
     *  'orderby' => array(), //排序
     *  'limit' => array(0, page_size), //取出条数，默认不限制
     *  'select' => array(),//查询的字段，可以是数组和字符串
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
                ->table('shop_order_goods')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select);
        });
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
                ->table('shop_order_goods')
                ->where(array('shop_order_id=[+]', $id))
                ->find();
        });
    }

    /**
     * 查询一条记录，根据where条件
     */
    public function find_where($call_where = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where), function ($call_where) {
            return db(parent::DB_APPLICATION_ID)
                ->table('shop_order_goods')
                ->call('where', $call_where)
                ->find();
        });
    }



	/**
	 * 成功之后，减库存和加销量
	 * 不考虑出错情况
	 * 
	 * @param	string	$shop_order_id
	 * @param	int		$shop_goods_stock_mode
	 */
	public function update_stock_sales($shop_order_id, $shop_goods_stock_mode){
		//减库存，不再事务类，防止并发出错
        $shop_order_goods = db(parent::DB_APPLICATION_ID)
        ->table('shop_order_goods')
        ->where(array('shop_order_id = [+]', $shop_order_id))
        ->select(array(
            'shop_order_goods_id',
        	'shop_goods_id',
            'shop_goods_sku_id',
            'shop_order_goods_number',
            'shop_order_goods_json'
        ));
		
		
		//防止隐形错误
        if (!empty($shop_order_goods)) {
            foreach ($shop_order_goods as $val) {
                if (!empty($val['shop_order_goods_json'])) {
                    $json = cmd(array($val['shop_order_goods_json']), 'json decode');
                }
                if (!isset($json['shop_goods']['shop_goods_stock_mode'])) {
                    continue;
                }

                //减库存方式。
                //0表示不减库存；
                //1表示下单预扣，如果买家在保留时间内未付款，那预扣库存会被释放出来重新供大家购买。有效时间以全局配置默认。
                //2表示付款减库存。退款则恢复库存。3表示发货减库存,退货则恢复库存。
                if( $json['shop_goods']['shop_goods_stock_mode'] == $shop_goods_stock_mode ){
                    $bool = db(parent::DB_APPLICATION_ID)
                    ->table('shop_goods_sku')
                    ->where(array('shop_goods_sku_id = [+]', $val['shop_goods_sku_id']))
                    ->where(array('shop_goods_sku_stock >= [-]', $val['shop_order_goods_number']))
                    ->data(array('shop_goods_sku_stock = [-]', 'shop_goods_sku_stock - ' . $val['shop_order_goods_number'], true))
                    ->data(array('shop_goods_sku_update_time = [-]', time(), true))
                    ->update();

                    //获取配置，判断是否加库存明细
                    $stock_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("stock_data"), true);
                    if(!empty($stock_config['state']) && $stock_config['state'] == 1){
                        //获取shop_order
                        $shop_order = object(parent::TABLE_SHOP_ORDER)->find($shop_order_id);
                        //获取商品规格
                        $goods_sku = object(parent::TABLE_SHOP_GOODS_SKU)->find($val['shop_goods_sku_id']);
                        
                        $stock_data_config = array(
                            'buy_user_id'=>!empty($shop_order['user_id'])?$shop_order['user_id']:'',
                            'shop_goods_id'=>$val['shop_goods_id'],
                            'key'=>$val['shop_order_goods_id'],
                            'sku_id'=>$val['shop_goods_sku_id'],
                            'type'=>2,
                            'num'=>$val['shop_order_goods_number'],
                            'remain_num'=>$goods_sku['shop_goods_sku_stock'],
                            'goods'=>$json['shop_goods'],
                            'goods_sku'=>$goods_sku
                        );
                        object(parent::TABLE_SHOP_GOODS_STOCK_LOG)->insert_info($stock_data_config);
                    }
                }
				
				//支付成功、加销量
				if( $shop_goods_stock_mode == 2 ){
					//加销量
					$bool = db(parent::DB_APPLICATION_ID)
	                ->table('shop_goods')
	                ->where(array('shop_goods_id = [+]', $val['shop_goods_id']))
	                ->data(array('shop_goods_update_time = [-]', time(), true))
					->data(array('shop_goods_sales = [-]', 'shop_goods_sales + ' . $val['shop_order_goods_number'], true))
	                ->update();
				}
				
            }
        }
		
		
		// 清除缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);
		
	}







    /**
     * 查询商品信息，订单支付
     * @param  string $shop_order_id [商场订单ID]
     * @return array
     */
    public function select_by_pay($shop_order_id)
    {
        //左连商品表
        $join_shopgoods = array(
            'table' => 'shop_goods sg',
            'type' => 'left',
            'on' => 'sg.shop_goods_id = sog.shop_goods_id'
        );

        //左连商品规格表
        $join_shopgoodssku = array(
            'table' => 'shop_goods_sku sgs',
            'type' => 'left',
            'on' => 'sgs.shop_goods_sku_id = sog.shop_goods_sku_id'
        );

        //左连商品限时购表
        $join_shopgoodswhen = array(
            'table' => 'shop_goods_when sgw',
            'type' => 'left',
            'on' => 'sgw.shop_goods_id = sog.shop_goods_id'
        );

        return db(parent::DB_APPLICATION_ID)
            ->table('shop_order_goods sog')
            ->joinon($join_shopgoods, $join_shopgoodssku, $join_shopgoodswhen)
            ->where(array('sog.shop_order_id=[+]', $shop_order_id))
            ->select(array(
                'sog.shop_order_goods_name',
                'sog.shop_order_goods_number',
                'sog.shop_order_goods_json',

                'sg.shop_goods_id',
                'sg.shop_goods_state',
                'sg.shop_goods_trash',

                'sgs.shop_goods_sku_stock',
                'sgw.shop_goods_id as when_shop_goods_id',
                'sgw.shop_goods_when_state',
            ));
    }

    /**
     * 推荐赏金计算是调用，判断是否为E麦商城，如果为E麦商城，仅普通商品发放奖金,拼团及限时购不发放
     */
    private function _is_emshop()
    {
        $data = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("emshop_config"), true);

        if (empty($data))
            return true;
    }

    /**
     * 计算推荐赏金
     * @param string $recommend_user_id [邀请人用户ID]
     * @param string $user_id [消费者的用户ID]
     * @param int $money [订单金额]
     * @param int $recommend_money [邀请奖金]
     */
    public function calculation_recommend_money($money, $recommend_user_id, $goods_id, $user_id){
        // 如果不是E麦商城
        if ($this->_is_emshop())
            return false;
        // 初始化奖金
        $award_user_id = '';
        $recommend_money = 0;

        // 查询配置
        $recommend_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find("shop_recommend_reward"), true);

        // 判断是否开启推荐奖金功能 1 => 开启  0 => 关闭
        $is_open = (int) $recommend_config['is_open'];
        if ($is_open === 0) 
            return false;

        // 查询消费者的身份
        $pay_user_is_member = false;
        $pay_user_amdin = object(parent::TABLE_ADMIN_USER)->find($user_id);
        if(empty($pay_user_amdin)){
            $pay_user_is_member = false;
        } else {
            if($pay_user_amdin['admin_id'] == 'member' || 
            $pay_user_amdin['admin_id'] == 'shop_manager' || 
            $pay_user_amdin['admin_id'] == 'chief_inspector' || 
            $pay_user_amdin['admin_id'] == 'area_agent'){
                $pay_user_is_member = true;
            }
        }

        // 查询消费者的身份
        $recommend_is_member = false;
        $recommend_user_amdin = object(parent::TABLE_ADMIN_USER)->find($recommend_user_id);
        if(empty($recommend_user_amdin)){
            $recommend_is_member = false;
        } else {
            if($recommend_user_amdin['admin_id'] == 'member' || 
            $recommend_user_amdin['admin_id'] == 'shop_manager' || 
            $recommend_user_amdin['admin_id'] == 'chief_inspector' || 
            $recommend_user_amdin['admin_id'] == 'area_agent'){
                $recommend_is_member = true;
            }
        }

        // 如果消费者是会员，那么奖励金额直接发给会员
        if($pay_user_is_member){
            $award_user_id = $user_id;
        } else {
            // 如果消费者不是会员，如果推荐人是会员发给推荐人
            if($recommend_is_member){
                $award_user_id = $recommend_user_id;
            } else {
                return false;
            }
        }

        // 查询奖励金额计算方式   0 => 按身份发放 1 => 随机发放  2 => 定额发放  3 => 单独商品设置
        $method = (int) $recommend_config['method'];
        // 0 => 按身份发放
        if ($method === 0) {
            // 查询推荐人的身份ID
            $recommend_user_amdin_id = object(parent::TABLE_ADMIN_USER)->find($recommend_user_id);

            // 无身份
            if (!isset($recommend_user_amdin_id['admin_id']))
                $recommend_money = 0;

            $recommend_user_amdin_id = $recommend_user_amdin_id['admin_id'];

            // 计算金额
            if (isset($recommend_config[$recommend_user_amdin_id])) {
                $recommend_money = $money * $recommend_config[$recommend_user_amdin_id]['royalty'];
            } else {
                $recommend_money = 0;
            }
        }

        // 1 => 随机发放
        if ($method === 1) {
            // 取随机奖金的最大、最小值
            $max_money = (int) $recommend_config['max_royalty_random'];
            $min_money = (int) $recommend_config['min_royalty_random'];
            $award_money = mt_rand($min_money, $max_money);
            $recommend_money = $money * $award_money;
        }

        // 2 => 定额发放
        if ($method === 2) {
            $recommend_money = (int) $recommend_config['quota_recommend_money'];
        }

        // 3=>  单独商品设置奖金
        if ($method === 3) {
            $recommend_money = object(parent::TABLE_SHOP_GOODS)->find($goods_id);
            $recommend_money = $recommend_money['shop_goods_recommend_money'];
        }

        return array('user_id' => $award_user_id, 'money' => $recommend_money);
    }


    /**
     * 易淘分销奖励发放
     * Undocumented function
     *
     * @param [type] $shop_order_id
     * @return void
     */
    public function version_yitao_invite_money_reward($shop_order_id)
    {
        // 测试用
        $result = array();

        // 查询商品
        $shop_order_goods = object(parent::TABLE_SHOP_ORDER_GOODS)->find($shop_order_id);
        $inser_user_money_data = array();

        // 获取订单信息
        $shop_order = object(parent::TABLE_SHOP_ORDER)->find($shop_order_id);  
        if(empty($shop_order)){
            return false;
        }
        $user_id = $shop_order['user_id'];   //获取下单用户
        $user_info = object(parent::TABLE_USER)->find($user_id);
        //下单用户姓名--昵称
		$user_name = !empty($user_info['user_compellation'])?$user_info['user_compellation'] : !empty($user_info['user_nickname'])?$user_info['user_nickname']:'';
        
        //下单用户手机号
        $user_phone = '';
        $user_phone_info = object(parent::TABLE_USER)->find_id_or_phone($user_id);

        if(!empty($user_phone_info['user_phone'])){
            $user_phone = $user_phone_info['user_phone'];
        }
        
        // 订单数据不存在
        if (!isset($shop_order_goods['shop_order_id']))
            return false;

        $shop_goods_index = (int) $shop_order_goods['shop_goods_index'];
        $shop_goods_property = (int) $shop_order_goods['shop_goods_property'];



        // 判断是否为门槛商品
        if ($shop_goods_index === 1) {
            //发放门槛商品奖励
            $reward_type = 1;
        } else {
            //发放分销提成奖励
            $reward_type = 2;
        }

        // 计算订单的奖金
        $rmb_award = object(parent::TABLE_SHOP_ORDER)->invite_royalty_reward($shop_order_id, $reward_type, $shop_order_goods);

        // object(parent::TABLE_SHOP_ORDER)->update_the_order('10011', $rmb_award);

        // 插入用户钱包
        $time = date('Y年m月d日 H:i:s',time());
        if(!$rmb_award){return false;}
        for ($i = 0; $i < count($rmb_award); $i++) {
            $plus_money = !empty($rmb_award[$i]['rmb_award']) ? (int) $rmb_award[$i]['rmb_award'] : 0;

            # 判断是否有奖金
            if (isset($plus_money) && $plus_money > 0) {

                $user_oauth = object(parent::TABLE_USER_OAUTH)->find($rmb_award[$i]['user_id']);
                if (!empty($user_oauth['user_oauth_wx_key'])) {
                    //发送注册消息给用户
                    $data_message = array(
                        "touser" => $user_oauth['user_oauth_wx_key'], //微信登录--用户openID
                        "template_id" => "h2wYrrT0r1vwDmhPSuSAqFrMK-ts_WvR7xaLKq5xMOg", //模板ID
                        "url" => "http://yitaoss.com/#/pagesA/transactionDetail/transactionDetail?method=user_money", //点击跳转链接地址  
                        "topcolor" => "#FF0000",
                        "data" => array(
                            "first" => array(
                                "value" => "您有一笔新的交易，请查收",
                                "color" => "#173177"
                            ),
                            "keyword1" => array(
                                "value" => ($plus_money/100).'元',
                                "color" => "#173177"
                            ),
                            "keyword2" => array(
                                "value" => '团队：'.$user_name.' '.$user_phone.'购买商品得佣金',
                                "color" => "#173177"
                            ),
                            "keyword3" => array(
                                "value" => $time,
                                "color" => "#173177"
                            ),
                            "remark" => array(
                                "value" => "收益已到账",
                                "color" => "#173177"
                            )
                        )
                    );
                    $data_message = cmd(array($data_message), 'json encode');
                    $time_out = 30;
                    object(parent::PLUGIN_WEIXIN_MESSAGE_SEND)->template_message($data_message, $time_out);
                }


                # 初始化数组
                $inser_user_money_data[$i] = array(
                    'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
                    'order_plus_method' => parent::PAY_METHOD_USER_MONEY,
                    'order_plus_account_id' => $rmb_award[$i]['user_id'],
                    'order_plus_value' => $plus_money,
                    'order_plus_transaction_id' => '',
                    'order_sign' => $shop_order_id,
                    'order_state' => 1,
                    'order_pay_state' => 1,
                    'order_pay_time' => time(),
                    'order_insert_time' => time(),
                    'order_type' => parent::TRANSACTION_TYPE_INVITE_ROYALTY_REWARD,
                    'order_comment' => '分销奖励'
                );
                $result['distribution_money_reward'][$i] = object(parent::TABLE_USER_MONEY)->reward_money($inser_user_money_data[$i]);
            }

            if (!empty($rmb_award[$i]['admin_id']) && $rmb_award[$i]['admin_id'] == 'shop_manager') {
                # 初始化积分
                $inser_user_credit_data[$i] = array(
                    'user_credit_id' => object(parent::TABLE_USER_CREDIT)->get_unique_id(),
                    'user_id' => $rmb_award[$i]['user_id'],
                    'user_credit_plus' => $plus_money,
                    'user_credit_type' => parent::TRANSACTION_TYPE_USER_CREDIT_YITAO_INVITE
                );
                // object(parent::TABLE_SHOP_ORDER)->update_the_order('10011', $inser_user_credit_data);
                object(parent::TABLE_USER_CREDIT)->insert_plus($inser_user_credit_data[$i]);
                
            }
        }

        if ($reward_type === 1) {
            //如果是购买门槛商品--升级用户为店主
            $shop_order = object(parent::TABLE_SHOP_ORDER)->find($shop_order_id);
            $admin_user = object(parent::TABLE_ADMIN_USER)->find($shop_order['user_id']);
            if (!empty($admin_user) && !in_array($admin_user['admin_id'],['shop_manager','chief_inspector','founder'])) {
                $where = array(
                    array('user_id =[+]', $admin_user['user_id'])
                );
                object(parent::TABLE_ADMIN_USER)->update($where, array('admin_id' => 'shop_manager'));
            }
        }

        // 更新身份
        for ($i = 0; $i < count($rmb_award); $i++) {

            $admin_info = object(parent::TABLE_ADMIN_USER)->find($rmb_award[$i]['user_id']);    //获取该邀请链用户的admin_user信息
            //判断该奖励获得者是否是店主
            if (!empty($admin_info) && $admin_info['admin_id'] === 'shop_manager') {
                //如果是店主，获取当前店主所得提成总金额
                $where = array(
                    array('user_id =[+]', $rmb_award[$i]['user_id'])
                );

                //获取用户积分
                $user_integration = object(parent::TABLE_USER_CREDIT)->find_now_data_unbuffered($rmb_award[$i]['user_id']);
                if (!empty($user_integration['user_credit_value']) && $user_integration['user_credit_value'] >= 500000) {
                    object(parent::TABLE_ADMIN_USER)->update($where, array('admin_id' => 'chief_inspector'));
                }
            }
        }


        return $result;
    }


    /**
     * 
     */
    public function xiletao_reward_send($shop_order_id='')
    {
        //获取shop_order
        $shop_order = object(parent::TABLE_SHOP_ORDER)->find($shop_order_id);
        if(empty($shop_order)){return false;}

        //获取shop_order_goods
        $shop_order_goods = $this->find($shop_order_id);
        if(empty($shop_order_goods)){return false;}
        
        //下单用户ID
        $user_id = $shop_order['user_id'];

        //用户user
        $user_info = object(parent::TABLE_USER)->find($user_id);

        //用户电话信息
        $user_phone = object(parent::TABLE_USER_PHONE)->find_by_user_id($user_id);

        $res_rewrds = object(parent::TABLE_SHOP_ORDER)->xiletao_invite_rewards($shop_order,$shop_order_goods,$user_info,$user_phone); 
    }
}