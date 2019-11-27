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



namespace eapie\source\table\house;

use eapie\main;

//置顶订单
class house_top_order extends main
{


    /**
     * 缓存的键列表
     * @var string
     */
    const CACHE_KEY = array(__CLASS__, 'house_product', 'house_product_top');


    /**
     * 数据检测
     * @var array
     */
    public $check = array(
        'shop_order_id' => array(
            'args'=>array(
                'exist' => array('缺少订单ID参数'),
                'echo'  => array('订单ID的数据类型不合法'),
                '!null' => array('订单ID不能为空'),
            ),
        ),
        'shop_order_buyer_note' => array(
            'args' => array(
                'exist'=> array('缺少买家留言参数'),
                'echo' => array('买家留言数据类型不合法'),
            ),
        ),
        'shop_order_seller_note' => array(
            'args' => array(
                'exist'=> array('缺少卖家留言参数'),
                'echo' => array('卖家留言数据类型不合法'),
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
    );


    /**
     * 获取一个id号
     * @return  string
     */
    public function get_unique_id()
    {
        return cmd(array(22), 'random autoincrement');
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
        );
    }


    //===========================================
    // 操作
    //===========================================


    public function update($call_where = array(), $data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('house_top_order')
            ->call('where', $call_where)
            ->call('data', $call_data)
            ->update($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    /**
     * 创建订单
     * @param  array  $order            [订单]
     * @param  array  $house_top_order  [置顶订单]
     * @return bool
     */
    public function create_order($order = array(), $house_top_order = array())
    {
        //开启事务
        db(parent::DB_APPLICATION_ID)->query('START TRANSACTION');

        //插入订单记录
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('order')
            ->insert($order);

        //回滚
        if (!$bool) {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            return false;
        }

        //插入置顶订单记录
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('house_top_order')
            ->insert($house_top_order);

        //回滚
        if (!$bool) {
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            return false;
        }

        //提交事务
        db(parent::DB_APPLICATION_ID)->query('COMMIT');

        return true;
    }


    /**
     * 支付——用户钱包
     * @param  string $order_id [订单ID]
     * @return bool
     */
    public function pay_by_user_money($order_id = '')
    {
        // 查询订单信息
        $order = db(parent::DB_APPLICATION_ID)
            ->table('order')
            ->where(array('order_id=[+]', $order_id))
            ->find();

        return $this->pay_by_third_party($order, true);
    }


    /**
     * 支付——第三方
     * @param  array $order_data    [订单数据]
     * @param  bool  $is_usermoney  [是否用户钱包支付]
     * @return bool
     */
    public function pay_by_third_party($order_data = array(), $is_usermoney = false)
    {
        // 查询置顶订单
        $house_top_order = db(parent::DB_APPLICATION_ID)
            ->table('house_top_order')
            ->where(array('order_id=[+]', $order_data['order_id']))
            ->find();

        // 查询楼盘项目置顶
        $house_product_top = db(parent::DB_APPLICATION_ID)
            ->table('house_product_top')
            ->where(array('house_product_id=[+]', $house_top_order['house_product_id']))
            ->find();

        // 开启事务
        db(parent::DB_APPLICATION_ID)->query('START TRANSACTION');

        // ——是否用户钱包支付——
        if ($is_usermoney) {
            // 更新用户钱包
            $data_user_money = object(parent::TABLE_USER_MONEY)->find_now_data($house_top_order['user_id']);
            $user_money_id = object(parent::TABLE_USER_MONEY)->insert_minus(array(
                'user_id' => $house_top_order['user_id'],
                'user_money_join_id' => $data_user_money['user_money_id'],
                'user_money_minus' => $house_top_order['house_top_option_price'],
                'user_money_value' => $data_user_money['user_money_value'] - $house_top_order['house_top_option_price'],
                'user_money_type' => parent::TRANSACTION_TYPE_SHOP_ORDER,
                'user_money_time' => time(),
            ));

            //回滚事务
            if (!$user_money_id) {
                db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
                return false;
            }

            $order_data['pay_method'] = parent::PAY_METHOD_USER_MONEY;
            $order_data['order_minus_transaction_id'] = $user_money_id;
        }

        // ——修改，置顶订单——
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('house_top_order')
            ->where(array('order_id=[+]', $order_data['order_id']))
            ->update(array(
                'house_top_order_pay_method' => $order_data['pay_method'],
                'house_top_order_pay_money' => $order_data['order_minus_value'],
                'house_top_order_pay_state' => 1,
                'house_top_order_pay_time' => time(),
                'house_top_order_update_time' => time(),
            ));

        if (!$bool) {
            //回滚事务
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            return false;
        }

        // ——修改，楼盘项目置顶——
        if (empty($house_product_top)) {
            // 插入记录
            $bool = db(parent::DB_APPLICATION_ID)
                ->table('house_product_top')
                ->insert(array(
                    'house_product_id' => $house_top_order['house_product_id'],
                    'house_product_top_start_time' => time(),
                    'house_product_top_end_time' => strtotime("+{$house_top_order['house_top_option_month']} month"),
                    'house_product_top_insert_time' => time(),
                    'house_product_top_update_time' => time(),
                ));
        } else {
            // 更新记录
            $update_data = array(
                'house_product_top_update_time' => time(),
            );

            // 是否已过期
            if (time() > $data['house_product_top_end_time']) {
                $update_data['house_product_top_start_time'] = time();
                $update_data['house_product_top_end_time'] = strtotime("+{$house_top_order['house_top_option_month']} month");
            } else {
                $update_data['house_product_top_end_time'] = strtotime("+{$house_top_order['house_top_option_month']} month", $data['house_product_top_end_time']);
            }

            $bool = db(parent::DB_APPLICATION_ID)
                ->table('house_product_top')
                ->where(array('house_product_id=[+]', $house_product_top['house_product_id']))
                ->update($update_data);
        }

        if (!$bool) {
            // 回滚事务
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            return false;
        }

        // ——修改，订单——
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('order')
            ->where(array('order_id=[+]', $order_data['order_id']))
            ->update(array(
                'order_minus_transaction_id' => $order_data['order_minus_transaction_id'],
                'order_pay_state' => 1,
                'order_pay_time' => time(),
            ));

        if (!$bool) {
            //回滚事务
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            return false;
        }

        //提交事务
        db(parent::DB_APPLICATION_ID)->query('COMMIT');

        //清理缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);

        return true;
    }


    //===========================================
    // 查询
    //===========================================



}