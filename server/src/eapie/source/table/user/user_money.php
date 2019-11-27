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




namespace eapie\source\table\user;

use eapie\main;

class user_money extends main
{


    /**
     * 缓存的键列表
     *
     * @var    string
     */
    const CACHE_KEY = array(__CLASS__, "user", "order");


    /**
     * 数据检测
     *
     * @var    array
     */
    public $check = array(
        "user_money_label" => array(
            //参数检测
            'args' => array(
                'echo' => array("钱包标签的数据类型不合法"),
            ),
            //字符长度检测
            'length' => array(
                '<length' => array(200, "钱包标签的字符长度太多")
            ),
        ),


        "user_money_comment" => array(
            //参数检测
            'args' => array(
                'echo' => array("钱包备注的数据类型不合法"),
            ),
        ),

        "money" => array(
            //参数检测
            'args' => array(
                'echo' => array("钱包的数据类型不合法"),
                'match' => array('/^[0-9]{0,}$/iu', "钱包必须是整数"),
            ),
        ),


    );


    /**
     * 获取一个id号
     *
     * @param    void
     * @return    string
     */
    public function get_unique_id()
    {
        return cmd(array(22), 'random autoincrement');
    }


    /**
     * 插入新数据
     *
     * @param    array $data 数据
     * @param    array $call_data 数据
     * @return    bool
     */
    public function insert($data = array(), $call_data = array())
    {
        if (empty($data) && empty($call_data)) {
            return false;
        }

        $bool = (bool)db(parent::DB_APPLICATION_ID)
            ->table('user_money')
            ->call('data', $call_data)
            ->insert($data);

        if (!empty($bool)) {
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }

        return $bool;
    }


    /**
     * 用户钱包管理操作
     * $data = array(
     *    "admin_user_id" => "操作人，管理员的用户ID"
     *  "user_id" => "用户id"
     *  "comment" => 备注信息
     *  "value" => 积分数量
     *  "type" => 交易类型
     *  "user_money" => 旧数据
     * )
     *
     * @return bool
     */
    public function insert_admin($data = array())
    {
        if (empty($data["admin_user_id"]) ||
            empty($data["user_id"]) ||
            empty($data['type']) ||
            empty($data['value']) ||
            !is_numeric($data['value']) ||
            $data['value'] < 0) {
            return false;
        }

        $data['comment'] = empty($data['comment']) ? "" : $data['comment'];

        $lock_ids = array();
        //事务处理，开始锁商家的积分数据
        $user_money_lock_id = object(parent::TABLE_LOCK)->start("user_id", $data["user_id"], parent::LOCK_MONEY);
        if (empty($user_money_lock_id)) {
            return false;//事务开启失败
        }
        $lock_ids[] = $user_money_lock_id;

        if ($data["type"] == "admin_plus") {
            //人工添加
            db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务

            //将赠送收益 提交给 用户
            $user_money_id = object(parent::TABLE_USER_MONEY)->insert_plus(array(
                "user_id" => $data['user_id'],
                "user_money_plus" => $data['value'],
                "user_money_type" => $data['type']
            ));

            //积分充值失败
            if (empty($user_money_id)) {
                db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
                object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
                return false;
            }

            //生成订单
            $order_insert = array(
                "order_id" => $this->get_unique_id(),
                "order_type" => $data['type'],//提现
                "order_comment" => $data['comment'],
                "order_plus_method" => "user_money",
                "order_plus_account_id" => $data['user_id'],
                "order_plus_value" => $data['value'],
                "order_plus_transaction_id" => $user_money_id,//交易号
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

            if (!object(parent::TABLE_ORDER)->insert($order_insert)) {
                db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
                object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
                return false;
            }


        } else
            if ($data["type"] == "admin_minus") {
                //人工减少

                if (empty($data['user_money']) ||
                    empty($data['user_money']['user_id'])) {
                    object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
                    return false;
                }

                db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务

                //减用户赠送收益的积分
                $user_money_id = object(parent::TABLE_USER_MONEY)->insert_minus(array(
                    "user_id" => $data["user_money"]['user_id'],
                    "user_money_join_id" => $data["user_money"]['user_money_id'],
                    "user_money_value" => ($data["user_money"]['user_money_value'] - $data["value"]),
                    "user_money_minus" => $data["value"],
                    "user_money_type" => $data['type']
                ));

                //积分充值失败
                if (empty($user_money_id)) {
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
                    "order_minus_method" => "user_money",
                    "order_minus_account_id" => $data['user_id'],
                    "order_minus_value" => $data['value'],
                    "order_minus_transaction_id" => $user_money_id,//交易号
                    "order_minus_update_time" => time(),

                    "order_state" => 1,//确定订单
                    "order_pay_state" => 1,//已支付
                    "order_pay_time" => time(),
                    "order_insert_time" => time(),
                    "order_json" => array()
                );

                if (!object(parent::TABLE_ORDER)->insert($order_insert)) {
                    db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
                    object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
                    return false;
                }


            } else {
                object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
                return false;
            }


        db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
        object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
        //清理当前项目缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);
        return $order_insert["order_id"];

    }

    //代理分红
    public function dividend_edit($user_id,$money,$pay_time)
    {
        db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务

        // 发放用户资产
        $user_money_id = object(parent::TABLE_USER_MONEY)->insert_plus(array(
            'user_id' => $user_id,
            'user_money_plus' => $money,
            'user_money_type' => parent::TRANSACTION_TYPE_SHOP_ORDER_AGENT_MONEY_DIVIDEND
        ));

        //失败
        if( empty($user_money_id) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            return false;
        }

        //生成订单
        $order_insert = array(
            "order_id" => $this->get_unique_id(),
            "order_type" => parent::TRANSACTION_TYPE_SHOP_ORDER_AGENT_MONEY_DIVIDEND,
            "order_comment" => "月末分红",
            "order_plus_method" => parent::PAY_METHOD_USER_MONEY,
            "order_plus_account_id" => $user_id,
            "order_plus_value" => $money,
            "order_plus_transaction_id" => $user_money_id,//交易号
            "order_plus_update_time" => time(),

            "order_state" => 1,//确定订单
            "order_pay_state" => 1,//已支付
            "order_pay_time" => $pay_time,
            "order_insert_time" => time(),
        );
        if( !object(parent::TABLE_ORDER)->insert($order_insert) ){
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            return false;
        }
        db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
        return true;
    }


    /**
     * 更新数据
     *
     * @param    array $where
     * @param    array $data
     * @param    array $call_data
     * @return    bool
     */
    public function update($where = array(), $data = array(), $call_data = array())
    {
        if (empty($where) || (empty($data) && empty($call_data))) {
            return false;
        }

        $bool = (bool)db(parent::DB_APPLICATION_ID)
            ->table('user_money')
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
     * 删除数据
     *
     * @param    array $where
     * @return    array
     */
    public function delete($where = array())
    {
        if (empty($where)) {
            return false;
        }

        $bool = (bool)db(parent::DB_APPLICATION_ID)
            ->table('user_money')
            ->call('where', $where)
            ->delete();

        if (!empty($bool)) {
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }

        return $bool;
    }


    /**
     * 根据唯一标识，删除数据
     *
     * @param    array $user_money_id
     * @return    array
     */
    public function remove($user_money_id = '')
    {
        if (empty($user_money_id)) {
            return false;
        }

        $bool = (bool)db(parent::DB_APPLICATION_ID)
            ->table('user_money')
            ->where(array('user_money_id=[+]', (string)$user_money_id))
            ->delete();

        if (!empty($bool)) {
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }

        return $bool;
    }


    /**
     * 获取一个数据
     *
     * @param    array $user_money_id
     * @return    array
     */
    public function find($user_money_id = '')
    {
        if (empty($user_money_id)) {
            return false;
        }

        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_money_id), function ($user_money_id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('user_money')
                ->where(array('user_money_id=[+]', (string)$user_money_id))
                ->find();
        });

    }


    /**
     * 根据用户ID，获取最新的一个数据
     *
     * @param    array $user_id
     * @return    array
     */
    public function find_now_data($user_id = '')
    {
        if (empty($user_id)) {
            return false;
        }

        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id), function ($user_id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('user_money')
                ->where(array('user_id=[+]', (string)$user_id))
                ->orderby(array('user_money_time', true), array('user_money_id'))
                ->find();
        });

    }

    /**
     * 查询累计收入
     * @param  array $call_where [查询条件]
     * @return integer
     */
    public function get_sum_plus($call_where = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where), function ($call_where) {
            $row = db(parent::DB_APPLICATION_ID)
                ->table('user_money')
                ->call('where', $call_where)
                ->find('SUM(user_money_plus) AS sum');

            return $row['sum'] ?: 0;
        });
    }


    //===========================================
    // 检测
    //===========================================


    /**
     * 检测余额
     * @author green
     *
     * @param  string $user_id [用户ID]
     * @param  string $pay_money [支付金额]
     * @return [mixed]              [true | 错误信息]
     */
    public function check_balance($user_id = '', $pay_money = '')
    {
        $data = $this->find_now_data($user_id);
        if (!$data) {
            return '余额不足';
        }
        if ($data['user_money_value'] < $pay_money) {
            return '余额不足';
        }

        return true;
    }


    /**
     * 返回用户的最新积分ID，SQL语句
     *
     * @param    string $user_id
     * @return    string
     */
    public function sql_now_id($user_id)
    {
        return db(parent::DB_APPLICATION_ID)
            ->table('user_money')
            ->where(array('user_id=[+]', (string)$user_id))
            ->orderby(array('user_money_time', true), array('user_money_id'))
            ->find(array('user_money_id'), function ($q) {
                return $q['query']['find'];
            });
    }


    /**
     * 返回用户的最新剩余钱包余额，SQL语句
     *
     * @param    string $user_id
     * @return    string
     */
    public function sql_now_value($user_id)
    {
        return db(parent::DB_APPLICATION_ID)
            ->table('user_money')
            ->where(array('user_id=[+]', (string)$user_id))
            ->orderby(array('user_money_time', true), array('user_money_id'))
            ->find(array('user_money_value'), function ($q) {
                return $q['query']['find'];
            });
    }


    /**
     * 返回用户的最新剩余钱包时间，SQL语句
     *
     * @param    string $user_id
     * @return    string
     */
    public function sql_now_time($user_id)
    {
        return db(parent::DB_APPLICATION_ID)
            ->table('user_money')
            ->where(array('user_id=[+]', (string)$user_id))
            ->orderby(array('user_money_time', true), array('user_money_id'))
            ->find(array('user_money_time'), function ($q) {
                return $q['query']['find'];
            });
    }


    /**
     * 收入
     *
     * $data = array(
     *    "user_id" => 用户ID
     *    "user_money_plus" => 要添加的余额
     *  "user_money_type" => 交易类型的键名称
     *  "user_money_time" => 时间
     * );
     *
     *
     * @param    array $data 数据
     * @return    bool
     */
    public function insert_plus($data)
    {
        if (empty($data['user_id']) ||
            empty($data['user_money_plus']) ||
            !is_numeric($data['user_money_plus']) ||
            $data['user_money_plus'] < 0) {
            return false;
        }

        //如果交易类型不存在
        $type_list = object(parent::TABLE_ORDER)->get_type();
        if (empty($data['user_money_type']) ||
            !isset($type_list[$data['user_money_type']])) {
            return false;
        }

        $find_now_data = $this->find_now_data($data['user_id']);//查询用户当前积分
        if (!empty($find_now_data)) {
            $data["user_money_value"] = $find_now_data['user_money_value'] + $data["user_money_plus"];
            $data["user_money_join_id"] = $find_now_data['user_money_id'];
        } else {
            $data["user_money_value"] = $data["user_money_plus"];
            $data["user_money_join_id"] = "";
        }

        if (empty($data['user_money_time'])) {
            $data['user_money_time'] = time();
            if (!empty($find_now_data['user_money_time']) &&
                $find_now_data['user_money_time'] >= $data['user_money_time']) {
                $data['user_money_time'] = ($find_now_data['user_money_time'] + 1);
            }
        }

        if (!empty($find_now_data['user_money_time']) &&
            $find_now_data['user_money_time'] >= $data['user_money_time']) {
            return false;
        }


        $where = array();
        //连接
        if (empty($data['user_money_join_id'])) {
            $where[] = array("([-]) IS NULL", $this->sql_now_id($data['user_id']), TRUE);
        } else {
            $where[] = array("([-]) = '" . $data['user_money_join_id'] . "'", $this->sql_now_id($data['user_id']), TRUE);
            $where[] = array("[and] ([-]) < " . $data['user_money_time'], $this->sql_now_time($data['user_id']), TRUE);
            $where[] = array("[and] ([-]) + " . $data['user_money_plus'] . " = " . $data['user_money_value'], $this->sql_now_value($data['user_id']), TRUE);
        }

        $data['user_money_id'] = $this->get_unique_id();//获取唯一ID
        $insert_sql = db(parent::DB_APPLICATION_ID)
            ->table('user_money', function ($p) {
                return "INSERT INTO " . $p['query']['table'] .
                    "(" .
                    "`user_money_id`" .
                    ",`user_money_join_id`" .
                    ", `user_id`" .
                    ", `user_money_type`" .
                    ", `user_money_plus`" .
                    ", `user_money_value`" .
                    ", `user_money_time`" .
                    ")";
            });

        $select_sql = db(parent::DB_APPLICATION_ID)
            ->table('user_money')
            ->call('where', $where)
            ->select(function ($p) use ($data) {
                return "SELECT " .
                    "\"" . $data['user_money_id'] . "\"" .
                    ",\"" . $data['user_money_join_id'] . "\"" .
                    ",\"" . $data['user_id'] . "\"" .
                    ",\"" . $data['user_money_type'] . "\"" .
                    "," . $data['user_money_plus'] .
                    "," . $data['user_money_value'] .
                    "," . $data['user_money_time'] .
                    " FROM DUAL " . $p['query']['where'];
            });

        //printexit( $insert_sql." ".$select_sql );

        $bool = (bool)db(parent::DB_APPLICATION_ID)->query($insert_sql . " " . $select_sql);
        if (!empty($bool)) {
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
            return $data['user_money_id'];
        } else {
            return false;
        }


    }


    /**
     * 支出
     *
     * $data = array(
     *    "user_id" => 用户ID
     *    "user_money_minus" => 要减少的余额
     *  "user_money_value" => 用户要更新的总余额
     *  "user_money_type" => 交易类型的键名称
     *  "user_money_time" => 时间
     * );
     *
     *
     * @return    bool
     */
    public function insert_minus($data)
    {
        if (empty($data['user_id']) ||
            empty($data['user_money_minus']) ||
            !is_numeric($data['user_money_minus']) ||
            $data['user_money_minus'] < 0 ||
            !isset($data['user_money_value']) ||
            !is_numeric($data['user_money_value']) ||
            $data['user_money_value'] < 0 ||
            empty($data['user_money_join_id']) ||
            (!is_string($data['user_money_join_id']) && !is_numeric($data['user_money_join_id']))) {
            return false;
        }

        //如果交易类型不存在
        $type_list = object(parent::TABLE_ORDER)->get_type();
        if (empty($data['user_money_type']) ||
            !isset($type_list[$data['user_money_type']])) {
            return false;
        }

        $find_now_data = $this->find_now_data($data['user_id']);//查询用户当前钱包余额

        if (empty($find_now_data['user_money_id']) ||
            $find_now_data['user_money_id'] != $data['user_money_join_id']) {
            return false;
        }

        if (empty($data['user_money_time'])) {
            $data['user_money_time'] = time();
            if ($find_now_data['user_money_time'] >= $data['user_money_time']) {
                $data['user_money_time'] = ($find_now_data['user_money_time'] + 1);
            }
        }
        if ($find_now_data['user_money_time'] >= $data['user_money_time']) {
            return false;
        }


        $where = array();
        $where[] = array("([-]) = '" . $data['user_money_join_id'] . "'", $this->sql_now_id($data['user_id']), TRUE);
        $where[] = array("[and] ([-]) < " . $data['user_money_time'], $this->sql_now_time($data['user_id']), TRUE);
        $where[] = array("[and] ([-]) - " . $data['user_money_minus'] . " = " . $data['user_money_value'], $this->sql_now_value($data['user_id']), TRUE);

        $data['user_money_id'] = $this->get_unique_id();//获取唯一ID
        $insert_sql = db(parent::DB_APPLICATION_ID)
            ->table('user_money', function ($p) {
                return "INSERT INTO " . $p['query']['table'] .
                    "(" .
                    "`user_money_id`" .
                    ",`user_money_join_id`" .
                    ", `user_id`" .
                    ", `user_money_type`" .
                    ", `user_money_minus`" .
                    ", `user_money_value`" .
                    ", `user_money_time`" .
                    ")";
            });

        $select_sql = db(parent::DB_APPLICATION_ID)
            ->table('user_money')
            ->call('where', $where)
            ->select(function ($p) use ($data) {
                return "SELECT " .
                    "\"" . $data['user_money_id'] . "\"" .
                    ",\"" . $data['user_money_join_id'] . "\"" .
                    ",\"" . $data['user_id'] . "\"" .
                    ",\"" . $data['user_money_type'] . "\"" .
                    "," . $data['user_money_minus'] .
                    "," . $data['user_money_value'] .
                    "," . $data['user_money_time'] .
                    " FROM DUAL " . $p['query']['where'];
            });

        //printexit( $insert_sql." ".$select_sql );

        $bool = (bool)db(parent::DB_APPLICATION_ID)->query($insert_sql . " " . $select_sql);
        if (!empty($bool)) {
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
            return $data['user_money_id'];
        } else {
            return false;
        }

    }


    /**
     * 联表。返回最新剩余金额，SQL语句
     *
     * @param   string $alias 别名称
     * @return    string
     */
    public function sql_join_user_now_value($alias = "")
    {
        if (is_string($alias) && $alias != "") {
            $alias .= ".";
        }

        return db(parent::DB_APPLICATION_ID)
            ->table('user_money um')
            ->where(array('um.user_id = ' . $alias . 'user_id'))
            ->orderby(array('um.user_money_time', true), array('um.user_money_id'))
            ->find(array('um.user_money_value'), function ($q) {
                return $q['query']['find'];
            });
    }


    /**
     * 联表。返回最新剩余金额交易时间，SQL语句
     *
     * @param   string $alias 别名称
     * @return    string
     */
    public function sql_join_user_now_time($alias = "")
    {
        if (is_string($alias) && $alias != "") {
            $alias .= ".";
        }

        return db(parent::DB_APPLICATION_ID)
            ->table('user_money um')
            ->where(array('um.user_id = ' . $alias . 'user_id'))
            ->orderby(array('um.user_money_time', true), array('um.user_money_id'))
            ->find(array('um.user_money_time'), function ($q) {
                return $q['query']['find'];
            });
    }


    /**
     * 获取所有用户的合计
     *
     * @param    void
     * @return    array
     */
    public function find_now_where_sum($where = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($where), function ($where) {
            $from_user_money_2 = db(parent::DB_APPLICATION_ID)
                ->table('user_money um2')
                ->where(array('um2.user_id=um.user_id'), array('[AND] um2.user_money_time>um.user_money_time'))
                ->select(function ($q) {
                    return $q['query']['select'];
                });

            //用户表
            $user = array(
                'table' => 'user u',
                'type' => 'INNER',
                'on' => 'u.user_id = um.user_id'
            );

            $data = db(parent::DB_APPLICATION_ID)
                ->table('user_money um')
                ->joinon($user)
                ->where(array('NOT EXISTS(' . $from_user_money_2 . ')'))
                ->call("where", $where)
                //->where( array('um.user_money_value >= [-]', ) )
                //->orderby(array('user_money_value', true))
                ->find("sum(um.user_money_value) as sum", function ($p) {
                    //printexit($p);
                });

            return empty($data["sum"]) ? 0 : (int)$data["sum"];
        });

    }


    /**
     * 余额列表
     *
     * @param  array $config 配置
     * @return array
     */
    public function select_user_page($config)
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
                'page_now' => 0,
                'data' => array()
            );

            //查询总条数
            $counts = db(parent::DB_APPLICATION_ID)
                ->table('user u')
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

            if (empty($select)) {
                $sql_join_now_value = $this->sql_join_user_now_value("u");
                $sql_join_now_time = $this->sql_join_user_now_time("u");
                $user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
                $select = array(
                    "u.*",
                    'IFNULL((' . $sql_join_now_value . '), 0) as user_money_value',
                    '(' . $user_phone_verify_list_sql . ') as user_phone_verify_list',
                    '(' . $sql_join_now_time . ') as user_money_time',
                );
            }

            //查询数据
            $data['data'] = db(parent::DB_APPLICATION_ID)
                ->table('user u')
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $call_limit)
                ->select($select
                // ,function($e){
                // 	printexit($e);
                // }
                );

            return $data;
        }
        );
    }


    /**
     * 余额列表（不分页）
     *
     * ------Mr.Zhao------2019.07.05------
     *
     * @param  array $config 配置
     * @return array
     */
    public function select_user($config)
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $call_where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $call_limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();
            // $limit = array(
            //     (isset($call_limit[0])? $call_limit[0] : 0),
            //     (isset($call_limit[1])? $call_limit[1] : 1000)
            // );

            if (empty($select)) {
                $sql_join_now_value = $this->sql_join_user_now_value("u");
                $sql_join_now_time = $this->sql_join_user_now_time("u");
                $user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
                $select = array(
                    "u.*",
                    'IFNULL((' . $sql_join_now_value . '), 0) as user_money_value',
                    '(' . $user_phone_verify_list_sql . ') as user_phone_verify_list',
                    '(' . $sql_join_now_time . ') as user_money_time',
                );
            }

            //查询数据
            $data = db(parent::DB_APPLICATION_ID)
                ->table('user u')
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
     * @param  array $config 配置
     * @return array
     */
    public function select_serial_page($config)
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
                'page_now' => 0,
                'data' => array()
            );

            //订单表
            /*$order = array(
                'table' => 'order as o',
                'type' => 'LEFT',
                'on' => 'o.order_plus_transaction_id = um.user_money_id OR o.order_minus_transaction_id = um.user_money_id'
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
                'on' => 'plus_o.order_pay_state=1 AND plus_o.order_plus_transaction_id = um.user_money_id AND plus_o.order_plus_method="user_money"'
            );


            //减少订单表
            $minus_order = array(
                'table' => 'order as minus_o',
                'type' => 'LEFT',
                'on' => 'minus_o.order_pay_state=1 AND minus_o.order_minus_transaction_id = um.user_money_id AND minus_o.order_minus_method="user_money"'
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


            //用户表
            $user = array(
                'table' => 'user u',
                'type' => 'LEFT',
                'on' => 'u.user_id = um.user_id'
            );

            //查询总条数
            $counts = db(parent::DB_APPLICATION_ID)
                ->table('user_money um')
                ->joinon($user, $plus_order, $plus_action_user, $minus_order, $minus_action_user)
                ->call('where', $call_where)
                ->find('count(distinct um.user_money_id) as count');
            if (empty($counts['count'])) {
                return $data;
            } else {
                $data['row_count'] = $counts['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count'] / $data['page_size']);
                    $data['page_now'] = ceil($limit[0] / $data['page_size']) + 1;
                }
            }


            if (empty($select)) {
                $plus_oau_user_phone_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("plus_oau");
                $minus_oau_user_phone_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("minus_oau");
                $user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
                $select = array(
                    "um.*",
                    "IFNULL(plus_o.order_id, minus_o.order_id) as order_id",
                    "IFNULL(plus_o.order_sign, minus_o.order_sign) as order_sign",
                    "IFNULL(plus_o.order_comment, minus_o.order_comment) as order_comment",
                    "IFNULL(plus_oau.user_id, minus_oau.user_id) as order_action_user_id",
                    "IFNULL(plus_oau.user_logo_image_id, minus_oau.user_logo_image_id) as order_action_user_logo_image_id",
                    "IFNULL(plus_oau.user_nickname, minus_oau.user_nickname) as order_action_user_nickname",
                    'IFNULL((' . $plus_oau_user_phone_sql . '), (' . $minus_oau_user_phone_sql . ')) as order_action_user_phone_verify_list',
                    '(' . $user_phone_verify_list_sql . ') as user_phone_verify_list',
                    'u.user_logo_image_id',
                    'u.user_nickname',
                );
            }

            //查询数据
            $data['data'] = db(parent::DB_APPLICATION_ID)
                ->table('user_money um')
                ->joinon($user, $plus_order, $plus_action_user, $minus_order, $minus_action_user)
                ->groupby("um.user_money_id")
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $call_limit)
                ->select($select);

            return $data;
        }
        );
    }


    /**
     * 用户余额转账到商家余额
     *
     * @param   string $data ["user_id"]                    用户ID
     * @param   int $data ["user_money"]                用户之前的钱包数据
     * @param   string $data ["transfer_money_value"]        转账余额数量
     * @param   string $data ["order_comment"]              订单备注
     * @param   string $data ["order_plus_account_id"]      收款账户，商家ID
     * @param   string $data ["order_action_user_id"]        操作用户ID
     * @param   string $data ["order_json"]                 配置信息
     * @param   array $data ["merchant_credit"]            商家积分数据
     * @return  bool
     */
    public function insert_transfer_merchant_money($data = array())
    {
        if (empty($data["user_id"]) ||
            empty($data["user_money"]) ||
            !isset($data["transfer_money_value"]) ||
            !is_numeric($data["transfer_money_value"]) ||
            empty($data["order_plus_account_id"]) ||
            empty($data["order_action_user_id"]) ||
            !isset($data["merchant_credit"])) {
            return false;
        }

        if (empty($data["order_comment"])) {
            $data["order_comment"] = "用户余额转账到商家余额";
        }


        $lock_ids = array();
        //事务处理，开始锁商家的数据
        $user_lock_id = object(parent::TABLE_LOCK)->start("user_id", $data["user_id"], parent::LOCK_MONEY);
        if (empty($user_lock_id)) {
            return false;//事务开启失败
        }
        $lock_ids[] = $user_lock_id;

        $merchant_lock_id = object(parent::TABLE_LOCK)->start("merchant_id", $data["order_plus_account_id"], parent::LOCK_MONEY);
        if (empty($merchant_lock_id)) {
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;//事务开启失败
        }
        $lock_ids[] = $merchant_lock_id;


        db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务

        //减少用户余额
        $user_money_id = object(parent::TABLE_USER_MONEY)->insert_minus(array(
            "user_id" => $data['user_id'],
            "user_money_join_id" => $data["user_money"]['user_money_id'],
            "user_money_value" => ($data["user_money"]['user_money_value'] - $data['transfer_money_value']),
            "user_money_minus" => $data['transfer_money_value'],
            "user_money_type" => parent::TRANSACTION_TYPE_TRANSFER
        ));
        //减少失败
        if (empty($user_money_id)) {
            file_put_contents(CACHE_PATH . "/test.用户余额减少失败", cmd(array($data), "json encode"));

            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }

        //将余额 提交给 商家
        $merchant_money_id = object(parent::TABLE_MERCHANT_MONEY)->insert_plus(array(
            "merchant_id" => $data['order_plus_account_id'],
            "merchant_money_plus" => $data['transfer_money_value'],
            "merchant_money_type" => parent::TRANSACTION_TYPE_TRANSFER
        ));

        //积分充值失败
        if (empty($merchant_money_id)) {
            file_put_contents(CACHE_PATH . "/test.商家余额收入失败", cmd(array($data), "json encode"));

            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }

        if (!empty($data["order_json"])) {
            $data["order_json"] = cmd(array($data["order_json"]), "json encode");
        } else {
            $data["order_json"] = "";
        }

        //插入订单
        $order_insert = array(
            "order_id" => object(parent::TABLE_ORDER)->get_unique_id(),
            "order_type" => parent::TRANSACTION_TYPE_TRANSFER,//转账
            "order_comment" => $data["order_comment"],
            "order_plus_method" => "merchant_money",
            "order_plus_account_id" => $data['order_plus_account_id'],
            "order_plus_value" => $data['transfer_money_value'],
            "order_plus_transaction_id" => $merchant_money_id,//交易号
            "order_plus_update_time" => time(),

            "order_action_user_id" => $data["order_action_user_id"],
            "order_minus_method" => "user_money",
            "order_minus_account_id" => $data['user_id'],
            "order_minus_value" => $data['transfer_money_value'],
            "order_minus_transaction_id" => $user_money_id,
            "order_minus_update_time" => time(),

            "order_state" => 1,//确定订单
            "order_pay_state" => 1,//已支付
            "order_pay_time" => time(),
            "order_insert_time" => time(),
            "order_json" => $data["order_json"]
        );

        $bool = object(parent::TABLE_ORDER)->insert($order_insert);
        if (empty($bool)) {
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }/*else{
        	//插入消息
			object(parent::TABLE_MESSAGE)->insert(array("merchant_id"=>$data['order_plus_account_id'],"value"=>$data['transfer_money_value']), parent::MESSAGE_MERCHANT_MONEY_PROCEEDS);
        }*/


        if (!empty($order_insert['order_json'])) {
            $order_insert['order_json'] = cmd(array($order_insert["order_json"]), "json decode");
        }

        //商家赠送用户积分
        $bool = object(parent::TABLE_MERCHANT_CREDIT)->consume_give_user_credit_not_transaction(
            $order_insert["order_plus_account_id"],
            $order_insert['order_minus_account_id'],
            $order_insert['order_minus_value'],
            $order_insert["order_id"],
            $order_insert['order_action_user_id'],
            $order_insert['order_comment'],
            $order_insert['order_json'],
            $lock_ids
        );
        object(parent::TABLE_ORDER)->update_json($order_insert["order_id"], $order_insert['order_json']);//更新一下信息


        //商家赠送积分，如果开启赠送积分状态下，那么需要检测错误
        if (isset($order_insert['order_json']['merchant_config_rmb_consume_user_credit']['state']) &&
            !empty($order_insert['order_json']['merchant_config_rmb_consume_user_credit']['state'])) {
            //file_put_contents(CACHE_PATH."/赠送积分调试.txt", cmd(array(array($order_insert['order_json'], $bool)), "json encode"));
            if (empty($bool)) {
                db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
                object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
                return false;
            }
        }


        db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
        object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
        //清理当前项目缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);
        return $order_insert['order_id'];
    }


    /**
     * 用户余额充值，回调时
     *
     * @param    array $transaction_id 第三方的交易号
     * @param    array $order_data
     * @param    array $lock_ids
     * @return    bool
     */
    public function plus_notify_trade_success($transaction_id, &$order_data, &$lock_ids)
    {

        //事务处理，开始锁商家的积分数据
        $plus_lock_id = object(parent::TABLE_LOCK)->start("user_id", $order_data["order_plus_account_id"], parent::LOCK_MONEY);
        if (empty($plus_lock_id)) {
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;//事务开启失败
        }
        $lock_ids[] = $plus_lock_id;

        db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开始事务

        //将充值余额 提交给 用户
        $user_money_id = object(parent::TABLE_USER_MONEY)->insert_plus(array(
            "user_id" => $order_data['order_plus_account_id'],
            "user_money_plus" => $order_data['order_plus_value'],
            "user_money_type" => $order_data['order_type']
        ));

        //充值失败
        if (empty($user_money_id)) {
            //file_put_contents(CACHE_PATH."/test.用户钱包收入失败", cmd(array($data), "json encode"));   
            $order_data['order_json']['error'][] = "用户id为“" . $order_data["order_plus_account_id"] . "”钱包收入失败";
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_ORDER)->update_json($order_data["order_id"], $order_data['order_json']);
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        }

        //修改订单支付状态 (订单ID, 收款交易号, 支付交易号)
        $bool = object(parent::TABLE_ORDER)->update_pay_success($order_data["order_id"], $user_money_id, $transaction_id, $order_data['order_json']);
        if (empty($bool)) {
            //file_put_contents(CACHE_PATH."/test.订单交易状态更新失败", cmd(array($data), "json encode")); 
            $order_data['order_json']['error'][] = "order_id“" . $order_data["order_id"] . "”订单交易状态更新失败";
            //object(parent::TABLE_USER_MONEY)->remove($user_money_id);//删除这个积分记录，以免回滚失败
            db(parent::DB_APPLICATION_ID)->query("ROLLBACK");//回滚
            object(parent::TABLE_ORDER)->update_json($order_data["order_id"], $order_data['order_json']);
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
            return false;
        } else {
            db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交
            object(parent::TABLE_LOCK)->close($lock_ids);//关闭锁
        }

        return true;
    }


    /**
     * 奖励金钱
     * @param  array $order [订单信息]
     * @return bool
     */
    public function reward_money($order = array())
    {
        $lock_ids = array();

        //锁表，用户钱包
        $plus_lock_id = object(parent::TABLE_LOCK)->start('user_id', $order['order_plus_account_id'], parent::LOCK_MONEY);
        if (empty($plus_lock_id))
            return false;

        $lock_ids[] = $plus_lock_id;

        //开启事务
        db(parent::DB_APPLICATION_ID)->query('START TRANSACTION');

        //插入，用户钱包
        $user_money_id = object(parent::TABLE_USER_MONEY)->insert_plus(array(
            'user_id' => $order['order_plus_account_id'],
            'user_money_plus' => $order['order_plus_value'],
            'user_money_type' => $order['order_type'],
        ));

        if (empty($user_money_id)) {
            //回滚事务
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            //关闭锁
            object(parent::TABLE_LOCK)->close($lock_ids);
            return false;
        }

        //插入订单记录
        $order['order_plus_transaction_id'] = $user_money_id;
        $bool = object(parent::TABLE_ORDER)->insert($order);

        if (empty($bool)) {
            //回滚事务
            db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
            //关闭锁
            object(parent::TABLE_LOCK)->close($lock_ids);
            return false;
        }

        //提交事务
        db(parent::DB_APPLICATION_ID)->query('COMMIT');
        //关闭锁
        object(parent::TABLE_LOCK)->close($lock_ids);

        //清理当前项目缓存
        object(parent::CACHE)->clear(self::CACHE_KEY);
        return true;
    }


    /**
     * 获取提现列表--分页
     * Undocumented function
     *
     * @param array $config
     * @return void
     */
    public function select_withdraw_page($config = array())
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

            //先获取总条数
            $find_data = db(parent::DB_APPLICATION_ID)
                ->table('user_money')
                ->call('where', $call_where)
                ->find('count(*) as count');
            if (empty($find_data['count'])) {
                return $data;
            } else {
                $data['row_count'] = $find_data['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count'] / $data['page_size']);
                    $data['page_now'] = ceil($limit[0] / $data['page_size']) + 1;//当前页数
                }
            }

            $data['data'] = db(parent::DB_APPLICATION_ID)
                ->table('user_money')
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $call_limit)
                ->select($select);

            return $data;
        });
    }


    /**
     * 获取提现总金额
     * Undocumented function
     *
     * @param array $where
     * @return void
     */
    public function withdraw_money($where = array())
    {

        if (empty($where)) {
            return false;
        }
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($where), function ($where) {
            return db(parent::DB_APPLICATION_ID)
                ->table('user_money')
                ->call('where', $where)
                ->find("count(user_money_minus) as count");
        });
    }


    /**
     * ----- Mr.Zhao ----- 2019.06.13 -----
     *
     * 获取多个数据
     *  $config = array(
     *  'where' => array(), //条件
     *  'orderby' => array(), //排序
     *  'limit' => array(0, page_size), //取出条数，默认不限制
     * );
     *
     * @param   array $config
     * @return  array
     */
    public function select($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            return db(parent::DB_APPLICATION_ID)
                ->table('user_money')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select);
        });
    }


    /**
     * 推荐奖励，用户钱包
     *
     * @param  array $user [用户数据]
     * @return bool
     */
    public function recommend_reward($user)
    {
        // 查询推荐人信息
        $user_parent = object(parent::TABLE_USER)->find($user['user_parent_id']);
        if (empty($user_parent))
            return false;

        //查询配置信息
        $data_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('recommend_reward_user_money'), true);
        if (empty($data_config['state']))
            return false;

        //是否不随机
        if (empty($data_config['random'])) {
            if (empty($data_config['money']) || !is_numeric($data_config['money']))
                return false;

            $money = $data_config['money'];
        } else {
            if (empty($data_config['money_min']) || !is_numeric($data_config['money_min']))
                return false;

            if (empty($data_config['money_max']) || !is_numeric($data_config['money_max']) || $data_config['money_max'] < $data_config['money_min'])
                return false;

            $money = mt_rand($data_config['money_min'], $data_config['money_max']);
        }

        //订单信息
        $order = array(
            'order_id' => object(parent::TABLE_ORDER)->get_unique_id(),
            'order_type' => parent::TRANSACTION_TYPE_RECOMMEND_MONEY,
            'order_comment' => $user_parent['user_nickname'],
            'order_action_user_id' => $user['user_id'],
            'order_plus_method' => parent::PAY_METHOD_USER_MONEY,
            'order_plus_account_id' => $user['user_parent_id'],
            'order_plus_value' => $money,
            'order_plus_transaction_id' => '',
            'order_json' => cmd(array($data_config), 'json encode'),
            'order_state' => 1,
            'order_pay_state' => 1,
            'order_pay_time' => time(),
            'order_insert_time' => time(),
        );

        //奖励金钱
        object(parent::TABLE_USER_MONEY)->reward_money($order);
    }


}