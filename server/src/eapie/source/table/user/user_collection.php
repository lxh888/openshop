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
class user_collection extends main {
	
    
    /*用户收藏表*/
    
    
    /**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__);
    
    	
	/**
     * 数据检测
     * 
     * @var array
     */
    public $check = array(
    	'user_collection_id' => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少收藏ID参数"),
                    'echo'=>array("收藏ID的数据类型不合法"),
                    '!null'=>array("收藏ID不能为空"),
                    ),
        ),
		"user_collection_module" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少收藏模块标签参数"),
                    'echo'=>array("收藏模块标签的数据类型不合法"),
                    '!null'=>array("收藏模块标签不能为空"),
                    'method'=>array(array(parent::TABLE_USER_COLLECTION, 'check_module'), "模块标签输入有误，不能被识别")       
                ),
        ),
		'user_collection_key' => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少收藏模块主键参数"),
                    'echo'=>array("收藏模块主键的数据类型不合法"),
                    '!null'=>array("收藏模块主键不能为空"),
                    ),
        ),
        'user_collection_label' => array(
            //参数检测
            'args'=>array(
                    'echo'=>array("收藏标签的数据类型不合法"),
                    ),
        ),
		'user_collection_comment' => array(
            //参数检测
            'args'=>array(
                    'echo'=>array("注释信息的数据类型不合法"),
                    ),
        ),
        "user_collection_sort" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("收藏排序的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/i', "收藏排序必须是整数"),
					),
		),
	);	
	
		
	
	/**
	 * 获取模块标签列表
	 * 模块名称
	 * 
	 * @param	void
	 * @return	array
	 */
	public function get_module(){
		return array(
            parent::MODULE_SHOP_GOODS => "商城商品",
            parent::MODULE_MERCHANT => "商家",
        );
	}
	
	
	
	/**
	 * 获取模块数据类列表
	 * 模块处理类。用于检测主键
	 * 
	 * @param	void
	 * @return	array
	 */
	public function get_module_table(){
		return array(
            parent::MODULE_SHOP_GOODS => parent::TABLE_SHOP_GOODS,
            parent::MODULE_MERCHANT => parent::TABLE_MERCHANT,
        );
	}
	
	
	
	
	/**
     * 检测模块标签
     * 
     * @param   string  $data
     * @return  bool
     */
    public function check_module($data){
        $module_list = $this->get_module();
        if( isset($module_list[$data]) ){
            return true;
        }else{
            return false;
        }
    }
	
	
	/**
     * 根据模块、主键，获取收藏数据
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
	
	
	
	
	
		
	/**
	 * 获取一个id号
	 * 
	 * @param	void
	 * @return	string
	 */
	public function get_unique_id(){
		return cmd(array(22), 'random autoincrement');
	}	
	
	

   /**
     * 根据收藏ID、用户ID获取数据
     * 
     * @param   array   $user_collection_id			收藏ID
	 * @param	string	$user_id					用户ID
     * @param	array	$find						字段别名称
     * @return  array
     */
    public function find_id_user($user_collection_id = "", $user_id = "", $find = array()){
        if( empty($user_collection_id) 
        || empty($user_id) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_collection_id, $user_id, $find), function($user_collection_id, $user_id, $find){
            if( empty($find) ){
            	$find = array("*");
            }
				
            return db(parent::DB_APPLICATION_ID)
            ->table('user_collection')
            ->where( 
            	array('user_collection_id=[+]', (string)$user_collection_id), 
            	array('[and] user_id=[+]', (string)$user_id)
			)
            ->find($find);
        });
        
    }   
	  
	  
	  
	  
    /**
     * 根据模块、键、用户ID获取数据
     * 
     * @param   array   $user_collection_module		模块
	 * @param	string	$user_collection_key		键
	 * @param	string	$user_id					用户ID
	 * @param	array	$find						字段别名称
     * @return  array
     */
    public function find_module_key_user($user_collection_module = "", $user_collection_key = "", $user_id = "", $find = array()){
        if( empty($user_collection_module) 
        || empty($user_collection_key) 
        || empty($user_id) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, 
        array($user_collection_module, $user_collection_key, $user_id, $find), 
        function($user_collection_module, $user_collection_key, $user_id, $find){
        	if( empty($find) ){
            	$find = array("*");
            }
			
            return db(parent::DB_APPLICATION_ID)
            ->table('user_collection')
            ->where( 
            	array('user_collection_module=[+]', (string)$user_collection_module), 
            	array('[and] user_collection_key=[+]', (string)$user_collection_key),
            	array('[and] user_id=[+]', (string)$user_id)
			)
            ->find($find);
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
        ->table('user_collection')
        ->call('data', $call_data)
        ->insert($data);
        
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
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
        ->table('user_collection')
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
     * 删除数据
     * 
     * @param   array   $where
     * @return  array
     */
    public function delete($where = array()){
        if( empty($where) ){
            return false;
        }
        
        $bool = (bool)db(parent::DB_APPLICATION_ID)
        ->table('user_collection')
        ->call('where', $where)
        ->delete();
        
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
        return $bool;
    }
        
        
        	
	
        
    
                
    /**
     * 根据唯一标识，删除数据
     * 
     * @param   array   $user_collection_id
     * @return  array
     */
    public function remove($user_collection_id = ''){
        if( empty($user_collection_id) ){
            return false;
        }
        
        $bool = (bool)db(parent::DB_APPLICATION_ID)
        ->table('user_collection')
        ->where(array('user_collection_id=[+]', (string)$user_collection_id))
        ->delete();
        
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
        return $bool;
    }
    
        	
	
       
    /**
     * 获取一个数据
     * 
     * @param   array   $user_collection_id
     * @return  array
     */
    public function find($user_collection_id = ''){
        if( empty($user_collection_id) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_collection_id), function($user_collection_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('user_collection')
            ->where(array('user_collection_id=[+]', (string)$user_collection_id))
            ->find();
        });
        
    }
    
    	
	
	
    
        
    /**
     * 获取多个用户数据
     *  $config = array(
     *  'where' => array(), //条件
     *  'orderby' => array(), //排序
     *  'limit' => array(0, page_size), //取出条数，默认不限制
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
            ->table('user_collection')
            ->call('where', $where)
            ->call('orderby', $orderby)
            ->call('limit', $limit)
            ->select($select);
            
        });
    }   

    /**
     * 获取多个用户数据
     *  $config = array(
     *  'where' => array(), //条件
     *  'orderby' => array(), //排序
     *  'limit' => array(0, page_size), //取出条数，默认不限制
     * );
     * 
     * @param   array   $config
     * @return  array
     */
    public function select_join_shop_goods($config = array()){

        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            $where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
            
            // 商品列表
            $shop_goods = array(
                'table' => 'shop_goods sg',
                'type' => 'left',
                'on' => 'uc.user_collection_key = sg.shop_goods_id'
            );

            return db(parent::DB_APPLICATION_ID)
            ->table('user_collection uc')
            ->joinon($shop_goods)
            ->call('where', $where)
            ->call('orderby', $orderby)
            ->call('limit', $limit)
            ->select($select);
        });
        
    }   


    /**
     * 获取用户优惠券分页数据
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
                'page_now' => 1,
                'data' => array()
            );
			
            //先获取总条数
            $find_data = db(parent::DB_APPLICATION_ID)
            ->table('user_collection')
            ->call('where', $call_where)
            ->find('count( user_collection_id ) as count');
            if( empty($find_data['count']) ){
                return $data;
            }else{
                $data['row_count'] = $find_data['count'];
                if( !empty($data['page_size']) ){
                    $data['page_count'] = ceil($data['row_count']/$data['page_size']);
                    $data['page_now'] = ceil($limit[0]/$data['page_size']) + 1;//当前页数
                }
            }
            
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('user_collection')
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);
            
            return $data;
        });
        
    }

	/**
     * 查询收藏数量
     * @param  array $call_where [查询条件]
     * @return integer
     */
    public function get_count($call_where)
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where), function($call_where){
            $row = db(parent::DB_APPLICATION_ID)
                ->table('user_collection')
                ->call('where', $call_where)
                ->find('COUNT(user_collection_id) AS count');

            return $row ? $row['count'] : 0;
        });
    }
	
	
	
	
	
	
	
	
	
}