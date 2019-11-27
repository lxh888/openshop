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

// 用户评价
class user_evaluate extends main
{

    /**
     * 缓存的键列表
     * 
     * @var string
     */
    const CACHE_KEY = array(__CLASS__);

    /**
     * 数据检测
     * 
     * @var array
     */
    public $check = array(
        'user_evaluate_id' => array(
            'args' => array(
                'exist' => array('缺少评价ID参数'),
                'echo'  => array('评价ID的数据类型不合法'),
                '!null' => array('评价ID不能为空'),
            ),
        ),
        'user_evaluate_module' => array(
            'args' => array(
                'exist' => array('缺少评价模块参数'),
                'echo'  => array('评价模块的数据类型不合法'),
                '!null' => array('评价模块不能为空'),
                //'method'=> array(array(parent::TABLE_USER_EVALUATE, 'check_module'), '模块标签不合法')
            ),
        ),
        'user_evaluate_key' => array(
            'args' => array(
                'exist' => array('缺少评价模块ID参数'),
                'echo'  => array('评价模块ID的数据类型不合法'),
                '!null' => array('评价模块ID不能为空'),
            ),
        ),
        'user_evaluate_score' => array(
            'args'=>array(
                'exist' => array('缺少评分参数'),
                'echo'  => array('评分的数据类型不合法'),
                'match' => array('/^\d{1,20}$/', '评分不合法'),
            )
        ),
        'user_evaluate_value' => array(
            'args' => array(
                'exist' => array('缺少评价参数'),
                'echo'  => array('评价的数据类型不合法'),
                '!null' => array('评价不能为空'),
            ),
        ),
    );  

    /**
     * 获取一个id号
     * 
     * @param   void
     * @return  string
     */
    public function get_unique_id()
    {
        return cmd(array(22), 'random autoincrement');
    }


    //===========================================
    // 增删改
    //===========================================


    public function insert($data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('user_evaluate')
            ->call('data', $call_data)
            ->insert($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }

    public function update($where = array(), $data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('user_evaluate')
            ->call('where', $where)
            ->call('data', $call_data)
            ->update($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }   

    public function delete($call_where = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('user_evaluate')
            ->call('where', $call_where)
            ->delete();

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }

    public function remove($user_evaluate_id = '')
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('user_evaluate')
            ->where(array('user_evaluate_id=[+]', $user_evaluate_id))
            ->delete();

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    //===========================================
    // 查询
    //===========================================


    /**
     * 查一条记录，根据条件
     * @param  array  $call_where 查询条件
     * @return array
     */
    public function find_where($call_where = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where), function ($call_where) {
            return db(parent::DB_APPLICATION_ID)
                ->table('user_evaluate')
                ->call('where', $call_where)
                ->find();
        });
    }

    /**
     * 查询
     * @param  array  $select     [查询字段]
     * @param  array  $call_where [查询条件]
     * @return array
     */
    public function select_where($select = array(), $call_where = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($select, $call_where), function ($select, $call_where) {
            return db(parent::DB_APPLICATION_ID)
                ->table('user_evaluate')
                ->call('where', $call_where)
                ->select($select);
        });
    }

    /**
     * 查询评价评分
     * @param  string $module   [模块]
     * @param  array $keys      [模块主键ID，索引数组]
     * @return array
     */
    public function select_score($module, $keys)
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($module, $keys), function ($module, $keys) {
            $keys_str = '"'.implode('","', $keys).'"';
            return db(parent::DB_APPLICATION_ID)
                ->table('user_evaluate')
                ->where(array('user_evaluate_module = [+]', $module))
                ->where(array('user_evaluate_key in ([-])', $keys_str, true))
                ->groupby('user_evaluate_key')
                ->select(array('user_evaluate_key AS `key`', 'FLOOR(AVG(user_evaluate_score)) AS `score`'));
        });
    }

    //===========================================
    // 检测
    //===========================================


    /**
     * 检测模块标签
     * 
     * @param   string  $val
     * @return  bool
     */
    public function check_module($val)
    {
        $module_list = array(
            'house_product' => '楼盘项目',
        );
        return isset($module_list[$val]);
    }
}