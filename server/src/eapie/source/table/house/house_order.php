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

// 楼盘订单
class house_order extends main
{


    /**
     * 缓存的键列表
     * 
     * @var string
     */
    const CACHE_KEY = array(__CLASS__);

    /**
     * 数据检测
     * @var array
     */
    public $check = array(
        'house_order_id' => array(
            'args' => array(
                'exist'=> array('缺少ID参数'),
                'echo' => array('ID数据类型不合法'),
                '!null'=> array('ID不能为空'),
            ),
            'exist' => array(
                'method'=>array(array(parent::TABLE_HOUSE_ORDER, 'find'), 'ID有误，数据不存在')
            )
        ),
        'house_order_client_name' => array(
            'args' => array(
                'exist'=> array('缺少客户姓名参数'),
                'echo' => array('客户姓名数据类型不合法'),
                '!null'=> array('客户姓名不能为空'),
            )
        ),
        'house_order_client_phone' => array(
            'args' => array(
                'exist'=> array('缺少客户手机号参数'),
                'match'=> array('/^1\d{10}$/', '客户手机号不合法'),
            )
        ),
        'house_order_agent_name' => array(
            'args' => array(
                'exist'=> array('缺少经纪人姓名参数'),
                'echo' => array('经纪人姓名数据类型不合法'),
                '!null'=> array('经纪人姓名不能为空'),
            )
        ),
        'house_order_agent_phone' => array(
            'args' => array(
                'exist'=> array('缺少经纪人手机号参数'),
                'match'=> array('/^1\d{10}$/', '经纪人手机号不合法'),
            )
        ),
        'house_product_verify_state' => array(
            'args' => array(
                'exist'=> array('缺少楼盘项目核实状态参数'),
                'match'=> array('/^(0|1)$/', '楼盘项目核实状态不合法'),
            )
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
     * 插入数据
     * 
     * @param   array $data      数据
     * @param   array $call_data 数据
     * @return  bool
     */
    public function insert($data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('house_order')
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
     * @param   array $call_where   更新条件
     * @param   array $data         更新数据
     * @param   array $call_data
     * @return  bool
     */
    public function update($call_where = array(), $data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('house_order')
            ->call('where', $call_where)
            ->call('data', $call_data)
            ->update($data);

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
                ->table('house_order')
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
                ->table('house_order')
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

            // 左连楼盘表
            $join_houseprodut = array(
                'table' => 'house_product hp',
                'type' => 'left',
                'on' => 'ho.house_product_id = hp.house_product_id'
            );

            //获取总条数
            $total_count = db(parent::DB_APPLICATION_ID)
                ->table('house_order ho')
                ->joinon($join_houseprodut)
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
                ->table('house_order ho')
                ->joinon($join_houseprodut)
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $call_limit)
                ->select($select);

            $data['data'] = $list;

            return $data;
        });
    }

}