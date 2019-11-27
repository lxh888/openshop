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

//用户关注
class user_follow extends main
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
        'user_follow_id' => array(
            'args' => array(
                'exist' => array('缺少关注ID参数'),
                'echo'  => array('关注ID的数据类型不合法'),
                '!null' => array('关注ID不能为空'),
            ),
        ),
        "user_follow_module" => array(
            'args' => array(
                'exist' => array('缺少关注模块标签参数'),
                'echo'  => array('关注模块标签的数据类型不合法'),
                '!null' => array('关注模块标签不能为空'),
                'method'=> array(array(parent::TABLE_USER_FOLLOW, 'check_module'), '模块标签不合法')       
            ),
        ),
        'user_follow_key' => array(
            'args' => array(
                'exist' => array('缺少关注模块主键参数'),
                'echo'  => array('关注模块主键的数据类型不合法'),
                '!null' => array('关注模块主键不能为空'),
            ),
        ),
        'user_follow_label' => array(
            'args' => array(
                'echo' => array('关注标签的数据类型不合法'),
            ),
        ),
        'user_follow_comment' => array(
            'args' => array(
                'echo' => array('注释信息的数据类型不合法'),
            ),
        ),
        'user_follow_sort' => array(
            'args' => array(
                'echo'  => array('关注排序的数据类型不合法'),
                'match' => array('/^\d+$/', '关注排序必须是整数'),
            ),
        ),
    );


    //===========================================
    // 增删改
    //===========================================


    public function insert($data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('user_follow')
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
            ->table('user_follow')
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
            ->table('user_follow')
            ->call('where', $call_where)
            ->delete();

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    public function remove($user_follow_id = '')
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('user_follow')
            ->where(array('user_follow_id=[+]', $user_follow_id))
            ->delete();

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }



    //===========================================
    // 
    //===========================================


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


    /**
     * 获取模块标签列表
     * 
     * @param   void
     * @return  array
     */
    public function get_module()
    {
        return array(
            parent::MODULE_HOUSE_PRODUCT => '楼盘项目',
        );
    }


    /**
     * 获取模块数据类列表
     * 模块处理类。用于检测主键
     * 
     * @param   void
     * @return  array
     */
    public function get_module_table()
    {
        return array(
            parent::MODULE_HOUSE_PRODUCT => parent::TABLE_HOUSE_PRODUCT,
        );
    }

 

    /**
     * 根据模块、主键，获取关注数据
     * 
     * @param   string  $module
     * @param   string  $key
     * @return  bool
     */
    public function get_module_key($module, $key){
        $module_table_list = $this->get_module_table();
        if( isset($module_table_list[$module]) ){
            if( !method_exists(object($module_table_list[$module]), "find") ){
                return false;
            }
            return object($module_table_list[$module])->find($key);
        }else{
            return false;
        }
    }

   
    //===========================================
    // 查询数据
    //===========================================



    /**
     * 获取一条记录，根据主键ID
     * 
     * @param   array   $user_follow_id
     * @return  array
     */
    public function find($user_follow_id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_follow_id), function($user_follow_id){
            return db(parent::DB_APPLICATION_ID)
                ->table('user_follow')
                ->where(array('user_follow_id=[+]', $user_follow_id))
                ->find();
        });
    }


    /**
     * 查一条记录，根据条件
     * 
     * @param  array  $call_where 查询条件
     * @return array
     */
    public function find_where($call_where = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where), function ($call_where) {
            return db(parent::DB_APPLICATION_ID)
                ->table('user_follow')
                ->call('where', $call_where)
                ->find();
        });
    }


    /**
     * 获取多个数据
     *  $config = array(
     *  'where' => array(), //条件
     *  'orderby' => array(), //排序
     *  'limit' => array(0, page_size), //取出条数，默认不限制
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
                ->table('user_follow')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select);
        });
    }


    /**
     * 查询关注数量
     * @param  array $call_where [查询条件]
     * @return integer
     */
    public function get_count($call_where)
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where), function($call_where){
            $row = db(parent::DB_APPLICATION_ID)
                ->table('user_follow')
                ->call('where', $call_where)
                ->find('COUNT(user_follow_id) AS count');

            return $row ? $row['count'] : 0;
        });
    }


    //===========================================
    // 检测数据
    //===========================================


    /**
     * 检测模块标签
     * 
     * @param   string  $module
     * @return  bool
     */
    public function check_module($module)
    {
        $module_list = $this->get_module();
        return array_key_exists($module, $module_list);
    }




}