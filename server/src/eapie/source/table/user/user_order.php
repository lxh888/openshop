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

//收货地址
class user_order extends main
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
        'user_order_id' => array(
            'args' => array(
                'exist'=> array('缺少订单ID参数'),
                'echo' => array('订单ID数据类型不合法'),
                '!null'=> array('订单ID不能为空'),
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
            ->table('order')
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
            ->table('order')
            ->call('where', $call_where)
            ->call('data', $call_data)
            ->update($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    /**
     * 删除数据，根据主键
     * 
     * @param   string $id 主键ID
     * @return  bool
     */
    public function remove($id = '')
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('order')
            ->where(array('user_address_id=[+]', $id))
            ->delete();

        //清理缓存
        if ($bool) object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    /**
     * 删除数据，根据条件
     * 
     * @param   array $call_where 条件
     * @return  bool
     */
    public function delete($call_where = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('order')
            ->call('where', $call_where)
            ->delete();

        //清理缓存
        if ($bool) object(parent::CACHE)->clear(self::CACHE_KEY);

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
                ->table('order')
                ->where(array('user_address_id=[+]', $id))
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
                ->table('order')
                ->call('where', $call_where)
                ->find();
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
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config) {
            $where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();

            return db(parent::DB_APPLICATION_ID)
                ->table('order')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select);
        });
    }


    /**
     * 获取订单列表--分页
     * Undocumented function
     *  
     * @return void
     */
    public function select_page($config=[])
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            $call_where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
			$call_orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $call_limit   = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select       = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();
            $limit = array(
                (isset($call_limit[0])? $call_limit[0] : 0),
                (isset($call_limit[1])? $call_limit[1] : 0)
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
            ->table('order')
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
			
            //查询数据
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('order')
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);
            return $data;
        });    
    }
}