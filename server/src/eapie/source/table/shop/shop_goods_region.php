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
class shop_goods_region extends main {
	
	
	
	/**
     * 缓存的键列表
     * 
     * @var string
     */
    const CACHE_KEY = array(__CLASS__, "shop_goods");
	
	
	 /**
     * 数据检测
     *
     * @var array
     */
    public $check = array(
		'shop_goods_region_id' => array(
            //参数检测
            'args' => array(
                    'exist'=>array("缺少商品销售地区ID参数"),
                    'echo'=>array("商品销售地区ID数据类型不合法"),
                    '!null'=>array("商品销售地区ID不能为空"),
                    ),
            //检查编号是否存在      
            'exists' => array(
                    'method'=>array(array(parent::TABLE_SHOP_GOODS_REGION, 'find_exists_id'), "商品销售地区ID有误，数据不存在") 
            ),
        ),
        'shop_goods_region_info' => array(
            'args'=>array(
                'echo'  => array('商品销售地区的简介类型不合法'),
            )
        ),
		'shop_goods_region_scope' => array(
            'args' => array(
                'match'=>array('/^[1-3]$/', '商品销售地区范围值必须是1、2、3')
            )
        ),
        
		'shop_goods_region_province' => array(
            'args' => array(
                'exist' => array('缺少商品销售地区的省份参数'),
                'echo'  => array('商品销售地区的省份数据类型不合法'),
                '!null' => array('商品销售地区的省份不能为空')
            ),
        ),
        'shop_goods_region_city' => array(
            'args' => array(
                'exist' => array('缺少商品销售地区的市级参数'),
                'echo'  => array('商品销售地区的市级数据类型不合法'),
                '!null' => array('商品销售地区的市级不能为空')
            ),
        ),
        'shop_goods_region_district' => array(
            'args' => array(
                'exist' => array('缺少商品销售地区的区级参数'),
                'echo'  => array('商品销售地区的区级数据类型不合法'),
                '!null' => array('商品销售地区的区级不能为空')
            ),
        ),
		'shop_goods_region_state' => array(
            'args' => array(
                'match'=>array('/^[0-2]$/', '商品销售地区状态值必须是0、1、2')
            )
        ),
		'shop_goods_region_sort' => array(
            'args' => array(
                'match'=>array('/^\d+$/', '商品销售地区排序值必须是数字')
            )
        ),
		
	
	);
	
	
	
	
	/**
     * 获取一个id号
	 * 
     * @return  string
     */
    public function get_unique_id(){
        return cmd(array(22), 'random autoincrement');
        
    }

		
           
            
    /**
     * 根据ID，判断是否存在
     * 
     * @param   string      $shop_goods_region_id
     */
    public function find_exists_id($shop_goods_region_id){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_goods_region_id), function($shop_goods_region_id){
            return (bool)db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_region')
            ->where(array('shop_goods_region_id=[+]', $shop_goods_region_id))
            ->find('shop_goods_region_id');
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
        ->table('shop_goods_region')
        ->call('data', $call_data)
        ->insert($data);
        
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
        return $bool;
    } 
	
	
	
		
	/**
	 * 根据唯一标识，删除数据
	 * 
	 * @param	array	$shop_goods_region_id
	 * @return	array
	 */
    public function remove($shop_goods_region_id = ''){
		if( empty($shop_goods_region_id) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('shop_goods_region')
		->where(array('shop_goods_region_id=[+]', (string)$shop_goods_region_id))
		->delete();
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}
	
	
	
			
	/**
	 * 更新数据
	 * 
	 * @param	array		$where
	 * @param	array		$data
	 * @param	array		$call_data
	 * @return	bool
	 */
	public function update($where = array(), $data = array(), $call_data = array()){
		if( empty($where) || (empty($data) && empty($call_data)) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('shop_goods_region')
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
     * 获取一条数据
     * 
     * @param   array   $shop_goods_region_id
     * @return  array
     */
    public function find($shop_goods_region_id = ''){
        if( empty($shop_goods_region_id) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_goods_region_id), function($shop_goods_region_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_region')
            ->where(array('shop_goods_region_id=[+]', (string)$shop_goods_region_id))
            ->find();
        });
    }

	
		
	/**
	 * 根据 商品ID、省市区 查询数据
	 * 
	 * @param	string		$shop_goods_id
	 * @param	string		$shop_goods_region_province
	 * @param	string		$shop_goods_region_city
	 * @param	string		$shop_goods_region_district
	 * @return	array
	 */
	public function find_goods_scope_pcd($shop_goods_id, $shop_goods_region_province, $shop_goods_region_city, $shop_goods_region_district){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_goods_id, $shop_goods_region_province, $shop_goods_region_city, $shop_goods_region_district), function($shop_goods_id, $shop_goods_region_province, $shop_goods_region_city, $shop_goods_region_district){
            return db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_region')
			->where(
				array('[and] shop_goods_region_scope=1'),
            	array('[and] shop_goods_region_province=[+]', $shop_goods_region_province),
            	array('[and] shop_goods_id=[+]', $shop_goods_id)
			)
			->where(
				array('[or] shop_goods_region_scope=2'),
            	array('[and] shop_goods_region_province=[+]', $shop_goods_region_province),
            	array('[and] shop_goods_region_city=[+]', $shop_goods_region_city),
            	array('[and] shop_goods_id=[+]', $shop_goods_id)
			)
            ->where(
            	array('[or] shop_goods_region_scope=3'),
            	array('[and] shop_goods_region_province=[+]', $shop_goods_region_province),
            	array('[and] shop_goods_region_city=[+]', $shop_goods_region_city),
            	array('[and] shop_goods_region_district=[+]', $shop_goods_region_district),
            	array('[and] shop_goods_id=[+]', $shop_goods_id)
			)
            ->find();
        });
	}
    
	
	
	/**
	 * 根据 省市区 查询数据
	 * 
	 * @param	string		$shop_goods_region_province
	 * @param	string		$shop_goods_region_city
	 * @param	string		$shop_goods_region_district
	 */
	public function select_scope_province_city_district($shop_goods_region_province, $shop_goods_region_city, $shop_goods_region_district){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_goods_region_province, $shop_goods_region_city, $shop_goods_region_district), function($shop_goods_region_province, $shop_goods_region_city, $shop_goods_region_district){
            $select = array(
                'shop_goods_region_id'
            );
            return db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_region')
			->where(
				array('[and] shop_goods_region_scope=1'),
            	array('[and] shop_goods_region_province=[+]', $shop_goods_region_province)
			)
			->where(
				array('[or] shop_goods_region_scope=2'),
            	array('[and] shop_goods_region_province=[+]', $shop_goods_region_province),
            	array('[and] shop_goods_region_city=[+]', $shop_goods_region_city)
			)
            ->where(
            	array('[or] shop_goods_region_scope=3'),
            	array('[and] shop_goods_region_province=[+]', $shop_goods_region_province),
            	array('[and] shop_goods_region_city=[+]', $shop_goods_region_city),
            	array('[and] shop_goods_region_district=[+]', $shop_goods_region_district)
			)
            ->select($select);
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
    public function select($config = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $limit   = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select  = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();
            
            return db(parent::DB_APPLICATION_ID)
                ->table('shop_goods_region')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select);
        });
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
                'page_now' => 1,
                'data' => array()
            );
            
			//商品数据
			$shop_goods = array(
				'table' => 'shop_goods sg',
				'type' => 'left',
				'on' => 'sg.shop_goods_id = sgr.shop_goods_id'
			);
			
            //先获取总条数
            $find_data = db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_region sgr')
			->joinon($shop_goods)
            ->call('where', $call_where)
            ->find('count(distinct sgr.shop_goods_region_id) as count');
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
                    'sgr.*',
                    "sg.*"
                );
            }
            
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_region sgr')
			->joinon($shop_goods)
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);
            
            return $data;
        });
    }
	
	
	
	

	
	/**
     * 返回一个省市区的商品id SQL语句
     * @return  string [sql语句]
     */
    public function sql_goods_ids($shop_goods_region_province, $shop_goods_region_city, $shop_goods_region_district)
    {
        return db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_region')
            ->where(
				array('[and] shop_goods_region_scope=1'),
            	array('[and] shop_goods_region_state=1'),
            	array('[and] shop_goods_region_province=[+]', $shop_goods_region_province)
			)
			->where(
                array('[or] shop_goods_region_scope=2'),
            	array('[and] shop_goods_region_state=1'),
            	array('[and] shop_goods_region_province=[+]', $shop_goods_region_province),
            	array('[and] shop_goods_region_city=[+]', $shop_goods_region_city)
			)
            ->where(
            	array('[or] shop_goods_region_scope=3'),
            	array('[and] shop_goods_region_state=1'),
            	array('[and] shop_goods_region_province=[+]', $shop_goods_region_province),
            	array('[and] shop_goods_region_city=[+]', $shop_goods_region_city),
            	array('[and] shop_goods_region_district=[+]', $shop_goods_region_district)
			)
	        ->select(array('shop_goods_id'), function($q){
	            return $q['query']['select'];
	        });
    }
	
	
	
	
	
	
}
?>