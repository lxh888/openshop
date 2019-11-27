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



namespace eapie\source\table\agent;

use eapie\main;

/**
 * 代理用户积分奖励日志
 */
class agent_user_credit_award_log extends main
{

    /**
     * 缓存的键列表
     * @var string
     */
    const CACHE_KEY = array(__CLASS__);

    /**
     * 获取主键ID
     * @return  string
     */
    public function get_unique_id()
    {
        return cmd(array(22), 'random autoincrement');
    }


    //===========================================
    // 操作
    //===========================================


    public function insert($data = array(), $call_data = array())
    {
        $res = db(parent::DB_APPLICATION_ID)
            ->table('agent_user_credit_award_log')
            ->call('data', $call_data)
            ->insert($data);

        // 清理缓存
        if ($res) {
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }

        return $res;
    }

    public function update($call_where = array(), $data = array(), $call_data = array())
    {
        $res = db(parent::DB_APPLICATION_ID)
            ->table('agent_user_credit_award_log')
            ->call('where', $call_where)
            ->call('data', $call_data)
            ->update($data);

        //清理缓存
        if ($res) {
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }

        return $res;
    }

    /**
     * 记录积分奖励日志
     * @Author green
     * 
     * @param  string $order_id [订单ID]
     * @return bool
     */
    public function destruct_insert($order_id = '')
    {
        // 获取数据库配置，防止标识资源被销毁
        $info = db(parent::DB_APPLICATION_ID)->info();
        $db_config = $info['config'];
        $db_application_id = parent::DB_APPLICATION_ID;

        destruct(__METHOD__ . '('.$order_id.')', true, array($db_config, $db_application_id), function($db_config, $db_application_id) use($order_id) {
            // 重置一下 数据库链接
            db($db_application_id, true, $db_config);

            // 查询订单信息
            $order = object(parent::TABLE_ORDER)->find_unbuffered($order_id);
            if (empty($order['order_pay_state'])) {
                return false;
            }

            // 查询配置
            $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('agent_user_credit_award'), true);
            if (empty($config['state'])) {
                return false;
            }

            // 查询用户认证信息
            $user_identity = object(parent::TABLE_USER_IDENTITY)->find($order['order_plus_account_id']);
            if (empty($user_identity) || $user_identity['user_identity_state'] !== '1' || $user_identity['user_identity_trash'] === '1') {
                return false;
            }

            // 插入数据
            $timestamp = time();
            $data = array(
                'id' => object(parent::TABLE_AGENT_USER_CREDIT_AWARD_LOG)->get_unique_id(),
                'user_id' => $order['order_plus_account_id'],
                'user_credit' => $order['order_plus_value'],
                'order_id' => $order_id,
                'order_time' => $order['order_pay_time'],
                'province' => $user_identity['user_identity_card_province'],
                'city' => $user_identity['user_identity_card_city'],
                'district' => $user_identity['user_identity_card_district'],
                'state' => 0,
                'config' => cmd(array($config), 'json encode'),
                'insert_time' => $timestamp,
                'update_time' => $timestamp,
            );
            return $this->insert($data);
        });
    }

    /**
     * 根据区域更新状态
     * @Author green
     *
     * @param  string $region [区域]
     * @return bool
     */
    public function update_state_by_region($region = '')
    {
        $res = db(parent::DB_APPLICATION_ID)
            ->table('agent_user_credit_award_log')
            ->call('where', array(
                array('CONCAT(province,city,district) = [+]', $region),
                array('state = 0'),
            ))
            ->update(array(
                'state' => 1,
                'update_time' => time(),
            ));

        // 清理缓存
        if ($res) {
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }

        return $res;
    }


    //===========================================
    // 查询
    //===========================================


    public function select($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config) {
            $where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            return db(parent::DB_APPLICATION_ID)
                ->table('agent_user_credit_award_log')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select);
        });
    }

    /**
     * 查询昨天区域积分总数
     * @Author green
     *
     * @param  integer $limit   [限制量]
     * @return array
     */
    public function select_yesterday_sum_credit_by_region($limit = 10)
    {
        $timestamp_end = strtotime(date('Y-m-d'));
        $timestamp_start = $timestamp_end - 24 * 60 * 60;

        $data = db(parent::DB_APPLICATION_ID)
            ->table('agent_user_credit_award_log')
            ->call('where', array(
                array('state = 0'),
                array('order_time > [-]', $timestamp_start),
                array('order_time < [-]', $timestamp_end),
            ))
            ->groupby('province', 'city', 'district')
            ->limit($limit)
            ->select(array(
                'province',
                'city',
                'district',
                'SUM(user_credit) AS credit',
            ));

        return $data;
    }

}