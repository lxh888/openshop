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

class house_order_image extends main
{

    /**
     * 缓存的键列表
     * 
     * @var string
     */
    const CACHE_KEY = array(
        __CLASS__, 
        "house_order"
    );
    
    /**
     * 数据检测
     * @var array
     */
    public $check = array(
        'house_order_image_type' => array(
            'args' => array(
                'exist'=> array('缺少图片类型参数'),
                'echo' => array('图片类型数据类型不合法'),
                '!null'=> array('图片类型不能为空'),
                'method'=>array(array(parent::TABLE_HOUSE_ORDER_IMAGE, 'check_image_type'), '图片类型不合法') 
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
        if (empty($data) && empty($call_data))
            return false;

        $bool = db(parent::DB_APPLICATION_ID)
            ->table('house_order_image')
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
     * 获取多条数据
     * @param   array   $config
     * @return  array
     */
    public function select($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config) {
            $where  = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $orderby= isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $limit  = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            return db(parent::DB_APPLICATION_ID)
                ->table('house_order_image')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select);
        });
    }


    //===========================================
    // 查询数据
    //===========================================


    /**
     * 检测图片类型
     * @param  string $val 值
     * @return bool
     */
    public function check_image_type($val = '')
    {
        $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('house_order_image_type'), true);
        return is_string($val) && in_array($val, $config);
    }



}