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

//商家楼盘经纪人的客户表
class house_client extends main
{

    /**
     * 缓存的键列表
     * @var string
     */
    const CACHE_KEY = array(__CLASS__);

    /**
     * 数据检测
     * @var array
     */
    public $check = array(
        'name' => array(
            'args' => array(
                'exist'=> array('缺少客户姓名参数'),
                'echo' => array('客户姓名不合法'),
                '!null'=> array('客户姓名不能为空'),
            ),
        ),
        'phone' => array(
            'args' => array(
                'exist'=> array('缺少客户手机号参数'),
                'match'=> array('/^1\d{10}$/', '客户手机号不合法'),
            ),
        ),
        'sex' => array(
            'args' => array(
                'exist'=> array('缺少客户性别参数'),
                'match'=> array('/^(0|1|2)$/', '客户性别不合法'),
            ),
        ),
        'age' => array(
            'args' => array(
                'exist'=> array('缺少客户年龄参数'),
                'match'=> array('/^\d{1,3}$/', '客户年龄不合法'),
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
            ->table('house_client')
            ->call('data', $call_data)
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
     * 查一条记录，根据主键
     * @param  string $id 商家用户表ID
     * @return array
     */
    public function find($id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($id), function ($id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('house_client')
                ->where(array('house_order_id=[+]', $id))
                ->find();
        });
    }

    /**
     * 查一条记录，根据条件
     * @param  array  $call_where 查询条件
     * @return array
     */
    public function find_where($call_where = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where), function ($call_where) {
            return db(parent::DB_APPLICATION_ID)
                ->table('house_client')
                ->call('where', $call_where)
                ->find();
        });
    }


    /**
     * 分页数据
     * @param  array $config 配置参数
     * @return array
     */
    public function select_page($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config) {
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

            //获取总条数
            $total_count = db(parent::DB_APPLICATION_ID)
                ->table('house_client')
                ->call('where', $call_where)
                ->find('count(*) as count');

            if (empty($total_count['count'])) {
                return $data;
            } else {
                $data['row_count'] = $total_count['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count']/$data['page_size']);
                    $data['page_now'] = ceil($limit[0]/$data['page_size']) + 1;
                }
            }

            //查询数据
            $list = db(parent::DB_APPLICATION_ID)
                ->table('house_client')
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $call_limit)
                ->select($select);

            $data['data'] = $list;

            return $data;
        });
    }


    //===========================================
    // 检测数据
    //===========================================




}