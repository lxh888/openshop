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



namespace eapie\source\table\brand;

use eapie\main;

//品牌
class brand extends main
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
        'brand_id' => array(
            'args' => array(
                'exist'=> array('缺少品牌ID参数'),
                'echo' => array('品牌ID数据类型不合法'),
                '!null'=> array('品牌ID不能为空'),
            ),
            'exist' => array(
                'method'=>array(array(parent::TABLE_BRAND, 'find'), '品牌ID有误，数据不存在')
            ),
        ),
        'brand_name' => array(
            'args' => array(
                'exist'=> array('缺少品牌名称参数'),
                'echo' => array('品牌名称数据类型不合法'),
                '!null'=> array('品牌名称不能为空'),
            ),
        ),
        'brand_info' => array(
            'args' => array(
                'exist'=> array('缺少品牌简介参数'),
                'echo' => array('品牌简介数据类型不合法'),
                '!null'=> array('品牌简介不能为空'),
            ),
        ),
        'brand_sort' => array(
            'args' => array(
                'exist' => array('缺少收品牌排序参数'),
                'match'=> array('/^\d{10}$/', '品牌排序不合法'),
            ),
        ),
        'brand_state' => array(
            'args' => array(
                'exist' => array('缺少品牌状态参数'),
                'match'=> array('/^(0|1)$/', '品牌状态不合法'),
            ),
        ),
        'type_json' => array(
            'args'=>array(
                'method'  => array(array(parent::TABLE_TYPE, 'check_json'), 'JSON配置格式有误') ,
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


    public function insert($data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('brand')
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
            ->table('brand')
            ->call('where', $call_where)
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
            ->table('brand')
            ->call('where', $call_where)
            ->delete();

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    public function remove($id = '')
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('brand')
            ->where(array('brand_id=[+]', $id))
            ->delete();

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
     * @param  string $id
     * @return array
     */
    public function find($id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($id), function ($id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('brand')
                ->where(array('brand_id=[+]', $id))
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
                ->table('brand')
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
                ->table('brand')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select);
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
                ->table('brand b')
                ->call('where', $call_where)
                ->find('count(*) as count');

            //是否有数据
            if (empty($total_count['count'])) {
                return $data;
            } else {
                $data['row_count'] = $total_count['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count']/$data['page_size']);
                    $data['page_now'] = ceil($limit[0]/$data['page_size']) + 1;
                }
            }

            //左连图片表
            $join_image = array(
                'table' => 'image i',
                'type' => 'left',
                'on' => 'i.image_id = b.brand_logo_image_id'
            );

            //查询数据
            $data['data'] = db(parent::DB_APPLICATION_ID)
                ->table('brand b')
                ->joinon($join_image)
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $call_limit)
                ->select($select);

            return $data;
        });
    }



    //===========================================
    // 检测数据
    //===========================================




}