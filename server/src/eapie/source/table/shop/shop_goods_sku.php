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



namespace eapie\source\table\shop;
use eapie\main;
class shop_goods_sku extends main {
    
    
        
    /**
     * 缓存的键列表
     * 
     * @var string
     */
    const CACHE_KEY = array(__CLASS__, "shop_goods", "shop_goods_spu");
    

    
    
        
    /**
     * 数据检测
     * 
     * @var array
     */
    public $check = array(
        'shop_goods_sku_id' => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少商品规格ID参数"),
                    'echo'=>array("商品规格ID数据类型不合法"),
                    '!null'=>array("商品规格ID不能为空"),
                    ),
            //检查编号是否存在      
            'exists_id'=>array(
                    'method'=>array(array(parent::TABLE_SHOP_GOODS_SKU, 'find_exists_id'), "商品规格ID有误，数据不存在",) 
            ),
            
        ),
        
        
        "shop_goods_sku_info" => array(
            //参数检测
            'args'=>array(
                    'echo'=>array("商品规格的描述数据类型不合法"),
                    ),
        ),
        
        
        "shop_goods_sku_stock" => array(
            //参数检测
            'args'=>array(
                    'echo'=>array("商品规格的库存数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/', "商品规格的库存必须是整数"),
                    ),
        ),
        
        
		"shop_goods_sku_price" => array(
            //参数检测
            'args'=>array(
                    'echo'=>array("商品规格的单价数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/', "商品规格的单价必须是整数"),
                    ),
        ),

		"shop_goods_sku_additional_money" => array(
            //参数检测
            'args'=>array(
                    'echo'=>array("商品规格的附加金额数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/', "商品规格的附加金额必须是整数"),
                    ),
        ),

		"shop_goods_sku_additional_credit" => array(
            //参数检测
            'args'=>array(
                    'echo'=>array("商品规格的附加积分数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/', "商品规格的附加积分必须是整数"),
                    ),
        ),

        "shop_goods_sku_cost_price" => array(
            //参数检测
            'args'=>array(
                    'echo'=>array("商品规格的成本数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/', "商品规格的成本必须是整数"),
                    ),
        ),
        
        "shop_goods_sku_market_price" => array(
            //参数检测
            'args'=>array(
                    'echo'=>array("商品规格的市场价数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/', "商品规格的市场价必须是整数"),
                    ),
        ),


        "shop_goods_sku_admin_id" => array(
            //参数检测
            'args'=>array(
                'exist'=>array("缺少商品规格对应的身份ID参数"),
                'echo'=>array("商品规格对应的身份ID数据类型不合法"),
                '!null'=>array("商品规格对应的身份ID不能为空"),
            ),
        ),
        "shop_goods_sku_royalty" => array(
            //参数检测
            'args'=>array(
                'exist'=>array("缺少商品规格对应的奖金参数"),
            ),
        ),
        "shop_goods_sku_region_price" => array(
            //参数检测
            'args'=>array(
                'exist'=>array("缺少商品规格对应的区域管理费参数"),
                'echo'=>array("商品规格对应的区域管理费数据类型不合法"),
                '!null'=>array("商品规格对应的区域管理费不能为空"),
            ),
        ),
    );
    
    
    
        
            
    /**
     * 获取一个id号
     * 
     * @param   void
     * @return  string
     */
    public function get_unique_id(){
        return cmd(array(22), 'random autoincrement');
    }
    
    
    
    
    
            
    /**
     * 根据ID，判断是否存在
     * 
     * @param   string      $shop_goods_sku_id
     */
    public function find_exists_id($shop_goods_sku_id){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_goods_sku_id), function($shop_goods_sku_id){
            return (bool)db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_sku')
            ->where(array('shop_goods_sku_id=[+]', $shop_goods_sku_id))
            ->find('shop_goods_sku_id');
        });
    }
    
    
    
    
        
                
    /**
     * 获取一个数据
     * 
     * @param   string  $shop_goods_sku_id
     * @return  array
     */
    public function find($shop_goods_sku_id = ''){
        if( empty($shop_goods_sku_id) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_goods_sku_id), function($shop_goods_sku_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_sku')
            ->where(array('shop_goods_sku_id=[+]', (string)$shop_goods_sku_id))
            ->find();
        });
        
    }


    /**
     * 根据条件获取对应商品的sku
     * Undocumented function
     *
     * @param array $call_where
     * @return void
     */
    public function find_where($call_where=array())
    {
        if( empty($call_where) ){
            return false;
        }

        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where), function($call_where){
            return db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_sku')
            ->call('where',$call_where)
            ->find();
        });
    }

	/**
     * 获取一个数据
     * 
     * @param   string  $shop_goods_sku_id
	 * @param   string  $shop_goods_id
     * @return  array
     */
    public function find_goods($shop_goods_sku_id = '', $shop_goods_id = ''){
        if( empty($shop_goods_sku_id) || empty($shop_goods_id) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_goods_sku_id, $shop_goods_id), function($shop_goods_sku_id, $shop_goods_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_sku')
            ->where(array('shop_goods_sku_id=[+]', (string)$shop_goods_sku_id), array('[and] shop_goods_id=[+]', (string)$shop_goods_id))
            ->find();
        });
        
    }



    /**
     * 连表查询商品详情，根据主键ID
     *
     * @param  array $goods_sku_id 商品规格ID
     * @return array
     */
    public function find_join_goods_spu($goods_sku_id = '', $select = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($goods_sku_id, $select), function ($goods_sku_id, $select) {

            //左连商品表
            $join_goods = array(
                'table' => 'shop_goods sg',
                'type' => 'left',
                'on' => 'sg.shop_goods_id = sku.shop_goods_id'
            );

            //左连商品属性表
            $join_spu = array(
                'table' => 'shop_goods_spu spu',
                'type' => 'left',
                'on' => 'FIND_IN_SET(spu.shop_goods_spu_id, sku.shop_goods_spu_id)'
            );

            if (empty($select)) {
                //字段
                $select = array(
                    'sg.shop_id',
                    'sg.shop_goods_id AS goods_id',
                    'sg.shop_goods_sn AS goods_sn',
                    'sg.shop_goods_name AS goods_name',
                    'sg.shop_goods_stock_mode AS stock_mode',
                    'sku.shop_goods_sku_id AS sku_id',
                    'sku.shop_goods_sku_name AS sku_name',
                    'sku.shop_goods_sku_stock AS sku_stock',
                    'sku.shop_goods_sku_price AS sku_price',
                    'sku.image_id',
                    'sku.shop_goods_spu_id AS spu_id',
                    'GROUP_CONCAT(shop_goods_spu_name) AS spu_name',
                );
            }
            

            //条件
            $call_where = array(
                array('sku.shop_goods_sku_id=[+]', $goods_sku_id),
                array('[and] sg.shop_goods_state=1'),
                array('[and] sg.shop_goods_trash=0'),
            );

            return db(parent::DB_APPLICATION_ID)
                ->table('shop_goods_sku sku')
                ->joinon($join_goods)
                ->joinon($join_spu)
                ->call('where', $call_where)
                ->groupby('sku.shop_goods_sku_id')
                ->find($select);
        });
    }
    

    /**
     * 连表查询商品详情，根据主键ID
     *
     * @param  array $goods_sku_id 商品规格ID
     * @return array
     */
    public function emshop_find_join_goods_spu($goods_sku_id = '', $select = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($goods_sku_id, $select), function ($goods_sku_id, $select) {

            //左连商品表
            $join_goods = array(
                'table' => 'shop_goods sg',
                'type' => 'left',
                'on' => 'sg.shop_goods_id = sku.shop_goods_id'
            );

            //左连商品属性表
            $join_spu = array(
                'table' => 'shop_goods_spu spu',
                'type' => 'left',
                'on' => 'FIND_IN_SET(spu.shop_goods_spu_id, sku.shop_goods_spu_id)'
            );

            if (empty($select)) {
                //字段
                $select = array(
                    'sg.shop_id',
                    'sg.shop_goods_id AS goods_id',
                    'sg.shop_goods_sn AS goods_sn',
                    'sg.shop_goods_name AS goods_name',
                    'sg.shop_goods_stock_mode AS stock_mode',
                    'sku.shop_goods_sku_id AS sku_id',
                    'sku.shop_goods_sku_name AS sku_name',
                    'sku.shop_goods_sku_stock AS sku_stock',
                    'sku.shop_goods_sku_price AS sku_price',
                    'sku.shop_goods_sku_additional_money AS additional_money',
                    'sku.shop_goods_sku_additional_credit AS additional_credit',
                    'sku.image_id',
                    'sku.shop_goods_spu_id AS spu_id',
                    'GROUP_CONCAT(shop_goods_spu_name) AS spu_name',
                );
            }
            

            //条件
            $call_where = array(
                array('sku.shop_goods_sku_id=[+]', $goods_sku_id),
                array('[and] sg.shop_goods_state=1'),
                array('[and] sg.shop_goods_trash=0'),
            );

            return db(parent::DB_APPLICATION_ID)
                ->table('shop_goods_sku sku')
                ->joinon($join_goods)
                ->joinon($join_spu)
                ->call('where', $call_where)
                ->groupby('sku.shop_goods_sku_id')
                ->find($select);
        });
    }


    
    /**
     * 根据两个where条件获取数据
     * 
     * @param   array   $where
     * @param   array   $where2
     * @return  array
     */
    public function select_two_where($where = array(), $where2 = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($where, $where2), function($where, $where2){
            return db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_sku')
            ->call('where', $where)
            ->call('where', $where2)
            ->select();
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
    public function select($config = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            $where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
                
            return db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_sku')
            ->call('where', $where)
            ->call('orderby', $orderby)
            ->call('limit', $limit)
            ->select($select);
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
    public function select_join($config = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            $where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
                
            //商品数据
            $shop_goods = array(
                'table' => 'shop_goods sg',
                'type' => 'left',
                'on' => 'sg.shop_goods_id = sgs.shop_goods_id'
            );
            
            if( empty($select) ){
                $select = array(
                    "sg.shop_goods_name",
                    "sgs.*"
                );
            }   
                
            return db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_sku sgs')
            ->joinon($shop_goods)
            ->call('where', $where)
            ->call('orderby', $orderby)
            ->call('limit', $limit)
            ->select($select);
        });
    }       
    
    
    /**
     * 连表查询多个商品详情
     *
     * @param  array $goods_sku_ids 商品规格ID，索引数组
     * @return array
     */
    public function select_join_goods_spu($goods_sku_ids = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($goods_sku_ids), function ($goods_sku_ids) {

            //左连商品表
            $join_goods = array(
                'table' => 'shop_goods sg',
                'type' => 'left',
                'on' => 'sg.shop_goods_id = sku.shop_goods_id'
            );

            //左连商品属性表
            $join_spu = array(
                'table' => 'shop_goods_spu spu',
                'type' => 'left',
                'on' => 'find_in_set(spu.shop_goods_spu_id, sku.shop_goods_spu_id)'
            );

            //子查询，查询商品主图片之一
            $sql_join_cart_main_id = object(parent::TABLE_SHOP_GOODS_IMAGE)->sql_join_cart_main_id('sku');

            //字段
            $select = array(
                'sg.shop_id',
                'sg.shop_goods_id AS goods_id',
                'sg.shop_goods_name AS goods_name',
                'sg.shop_goods_sn AS goods_sn',
                'sg.shop_goods_property AS property',
                'sg.shop_goods_state',
                'sg.shop_goods_trash',
                'sku.shop_goods_sku_id AS sku_id',
                'sku.shop_goods_sku_name AS sku_name',
                'sku.image_id AS sku_image_id',
                'sku.shop_goods_sku_stock AS sku_stock',
                'sku.shop_goods_sku_price AS sku_price',
                'GROUP_CONCAT(spu.shop_goods_spu_name) as spu_name',
                '('.$sql_join_cart_main_id.') as image_id',
            );


            $goods_sku_ids_str = "'".implode("','", $goods_sku_ids)."'";;

            //条件
            $call_where = array(
                array('sku.shop_goods_sku_id in ([-])', $goods_sku_ids_str, true),
            );

            return db(parent::DB_APPLICATION_ID)
                ->table('shop_goods_sku sku')
                ->joinon($join_goods)
                ->joinon($join_spu)
                ->call('where', $call_where)
                ->groupby('sku.shop_goods_sku_id')
                ->select($select);
        });
    }
    
            
        
    /**
     * 插入新数据
     * 
     * @param   array       $data           数据
     * @param   array       $call_data      数据
     * @return  bool
     */
    public function insert($data = array(), $call_data = array()){
        if( empty($data) && empty($call_data) ){
            return false;
        }
        
        $bool = (bool)db(parent::DB_APPLICATION_ID)
        ->table('shop_goods_sku')
        ->call('data', $call_data)
        ->insert($data);
        
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
        return $bool;
    }   
    
    /**
     * 批量插入数据
     * 
     * @param   array $data 数据，索引数组
     * @return  bool
     */
    public function insert_batch($data = array())
    {
        if (empty($data))
            return false;

        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_sku')
            ->batch()
            ->insert($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }
    
    
    
        
    /**
     * 更新数据
     * 
     * @param   array       $where
     * @param   array       $data
     * @param   array       $call_data
     * @return  bool
     */
    public function update($where = array(), $data = array(), $call_data = array()){
        if( empty($where) || (empty($data) && empty($call_data)) ){
            return false;
        }
        $bool = (bool)db(parent::DB_APPLICATION_ID)
        ->table('shop_goods_sku')
        ->call('where', $where)
        ->call('data', $call_data)
        ->update($data);
        
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
        return $bool;
    }   
        
    
    
    
    
                    
    /**
     * 根据唯一标识，删除数据
     * 
     * @param   array   $shop_goods_sku_id
     * @return  array
     */
    public function remove($shop_goods_sku_id = ''){
        if( empty($shop_goods_sku_id) ){
            return false;
        }
        $bool = (bool)db(parent::DB_APPLICATION_ID)
        ->table('shop_goods_sku')
        ->where(array('shop_goods_sku_id=[+]', (string)$shop_goods_sku_id))
        ->delete();
        
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
        return $bool;
    }       
        
    
    
    
        
    
    /**
     * 获取所有的分页数据
     * 
     * $config = array(
     *  'where' => array(), //条件
     *  'orderby' => array(), //排序
     *  'limit' => array(0, page_size), //取出条数，默认获取10条
     * );
     * 
     * limit的分页算法是：当前页数-1 * page_size
     * 序号的算法：key键+1，+每页显示的条数。等于分页后的序号。{key + 1 + page_size}
     * 
     * 返回的数据：
     * $data = array(
     *  'row_count' => //数据总条数
     *  'limit_count' => //已取出条数
     *  'page_size' => //每页的条数
     *  'page_count' => //总页数
     *  'page_now' => //当前页数
     *  'data' => //数据
     * );
     * 
     * @param   array   $config     配置信息
     * @return  array
     */
    public function select_page($config = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            $call_where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $call_limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
                
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
        
        
            //商品数据
            $shop_goods = array(
                'table' => 'shop_goods sg',
                'type' => 'left',
                'on' => 'sg.shop_goods_id = sgs.shop_goods_id'
            );
        
            
            //先获取总条数
            $find_data = db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_sku sgs')
            ->joinon($shop_goods)
            ->call('where', $call_where)
            ->find('count(distinct sgs.shop_goods_sku_id) as count');
            if( empty($find_data['count']) ){
                return $data;
                }else{
                    $data['row_count'] = $find_data['count'];
                    if( !empty($data['page_size']) ){
                        $data['page_count'] = ceil($data['row_count']/$data['page_size']);
                        $data['page_now'] = ceil($limit[0]/$data['page_size']) + 1;//当前页数
                    }
                }
                        
            if( empty($select) ){
                $select = array(
                    "sg.shop_goods_name",
                    "sgs.*"
                );
            }
                        
            
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_sku sgs')
            ->joinon($shop_goods)
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);              
                
            return $data;
        });
    }


    /**
     * 返回商品的有库存的最大价格
     * 
     * @param   string      $alias  别名称
     * @return  string
     */
    public function sql_join_stock_max_price($alias = ""){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        
        return db(parent::DB_APPLICATION_ID)
        ->table('shop_goods_sku sgsku')
        ->where( array('sgsku.shop_goods_id = '.$alias.'shop_goods_id'), array('sgsku.shop_goods_sku_stock > 0') )
        ->orderby(array("sgsku.shop_goods_sku_price", true), array("sgsku.shop_goods_sku_id", true))
        ->find(array("sgsku.shop_goods_sku_price"), function($q){
            return $q['query']['find'];
        });
        
    }
    
    /**
     * 返回商品的当前用户身份有库存的最大价格
     * 
     * @param   string      $alias  别名称
     * @return  string
     */
    public function sql_join_stock_admin_max_price($alias = "",$admin=""){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        
        return db(parent::DB_APPLICATION_ID)
        ->table('shop_goods_sku sgsku')
        ->where( array('sgsku.shop_goods_id = '.$alias.'shop_goods_id'), array('sgsku.shop_goods_sku_stock > 0'), array('sgsku.shop_goods_sku_admin_id =[+]',$admin))
        ->orderby(array("sgsku.shop_goods_sku_price", true), array("sgsku.shop_goods_sku_id", true))
        ->find(array("sgsku.shop_goods_sku_price"), function($q){
            return $q['query']['find'];
        });
        
    }
    
    /**
     * 返回商品的有库存的最小价格
     * 
     * @param   string      $alias  别名称
     * @return  string
     */
    public function sql_join_stock_min_price($alias = ""){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        
        return db(parent::DB_APPLICATION_ID)
        ->table('shop_goods_sku sgsku')
        ->where( array('sgsku.shop_goods_id = '.$alias.'shop_goods_id'), array('sgsku.shop_goods_sku_stock > 0') )
        ->orderby(array("sgsku.shop_goods_sku_price", false), array("sgsku.shop_goods_sku_id", true))
        ->find(array("sgsku.shop_goods_sku_price"), function($q){
            return $q['query']['find'];
        });
        
    }


    /**
     * 返回商品的有库存的最小价格
     * 
     * @param   string      $alias  别名称
     * @return  string
     */
    public function sql_join_stock_admin_min_price($alias = "",$admin=""){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        
        return db(parent::DB_APPLICATION_ID)
        ->table('shop_goods_sku sgsku')
        ->where( array('sgsku.shop_goods_id = '.$alias.'shop_goods_id'), array('sgsku.shop_goods_sku_stock > 0'), array('sgsku.shop_goods_sku_admin_id =[+]',$admin))
        ->orderby(array("sgsku.shop_goods_sku_price", false), array("sgsku.shop_goods_sku_id", true))
        ->find(array("sgsku.shop_goods_sku_price"), function($q){
            return $q['query']['find'];
        });
        
    }
    
    
    
    
    /**
     * 返回商品的库存
     * 
     * @param   string      $alias  别名称
     * @return  string
     */
    public function sql_join_stock_sum($alias = ""){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        
        return db(parent::DB_APPLICATION_ID)
        ->table('shop_goods_sku sgsku')
        ->groupby('sgsku.shop_goods_sku_id')
        ->where( array('sgsku.shop_goods_id = '.$alias.'shop_goods_id') )
        ->orderby( array("sgsku.shop_goods_sku_id", true) )
        ->find( array("sum(sgsku.shop_goods_sku_stock) as sum"), function($q){
            return $q['query']['find'];
        });
        
    }
    
    
	
	
	
	/**
     * 根据价格返回有库存的商品ID
     * 
     * @param   string      $alias  别名称
	 * @param   int      	$money	价格,单位分
     * @return  string
     */
    public function sql_stock_price_goods_id($min_money = 0, $max_money = NULL){
    	
		$where = array();
		$where[] = array('sgsku.shop_goods_sku_stock > 0');
		$where[] = array('sgsku.shop_goods_sku_price >= [-]', $min_money);
		if( !is_null($max_money) ){
			$where[] = array('sgsku.shop_goods_sku_price <= [-]', $max_money);
		}
		
        return db(parent::DB_APPLICATION_ID)
        ->table('shop_goods_sku sgsku')
		->call('where', $where)
        ->select(array('sgsku.shop_goods_id'), function($q){
            return $q['query']['select'];
        });
        
    }


    //===========================================
    // 操作
    //===========================================


    /**
     * 加库存
     * @param  string  $shop_goods_sku_id   [规格ID]
     * @param  integer $number              [数量]
     * @return bool
     */
	public function increase_stock($shop_goods_sku_id, $number)
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_sku')
            ->where(array('shop_goods_sku_id = [+]', $shop_goods_sku_id))
            ->data(array('shop_goods_sku_stock = [-]', 'shop_goods_sku_stock + '.$number, true))
            ->update(array(
                'shop_goods_sku_update_time' => time()
            ));

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    /**
     * 减库存
     * @param  string  $shop_goods_sku_id   [规格ID]
     * @param  integer $number              [数量]
     * @return bool
     */
    public function decrease_stock($shop_goods_sku_id, $number)
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_sku')
            ->where(array('shop_goods_sku_id = [+]', $shop_goods_sku_id))
            ->where(array('shop_goods_sku_stock >= [-]', $number))
            ->data(array('shop_goods_sku_stock = [-]', 'shop_goods_sku_stock - '.$number, true))
            ->update(array(
                'shop_goods_sku_update_time' => time()
            ));

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    /**
     * 获取多个商品的sku
     * @param   array   
     * @return  array   
     */
    public function get_data($data = array())
    {
        $sku_ids = array();
        foreach ($data as $v) {
            if (!in_array($v['shop_goods_sku_id'], $sku_ids) && $v['shop_goods_sku_id']) {
                $sku_ids[] = $v['shop_goods_sku_id'];
            }
        }

        if(empty($sku_ids)){
            return $data;
        }

        // printexit($shop_ids);

        //获取分类数据
        $in_string = "\"" . implode("\",\"", $sku_ids) . "\"";
        $config = array(
            'where' => array(),
            'select' => array(
                '*',
            )
        );

        $config['where'][] = array("[and] shop_goods_sku_id IN([-])", $in_string, true); //是不加单引号并且强制不过滤

        $sku_data = object(parent::TABLE_SHOP_GOODS_SKU)->select($config);

        foreach ($data as &$v) {
            if (empty($sku_data)) {
                break;
            }
            foreach ($sku_data as $v1) {
                if ($v1['shop_goods_sku_id'] == $v['shop_goods_sku_id']) {
                    $v['sku'] = $v1;
                    $v['shop_goods_spu_id'] = $v1['shop_goods_spu_id'];
                    break;
                }
            }
        }
        return $data;
    }
}