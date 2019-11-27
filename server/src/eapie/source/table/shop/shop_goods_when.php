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
class shop_goods_when extends main {
	
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, "shop_goods");
	
	
	
	
	/**
	 * 数据检测
	 * 
	 * @var	array
	 */
	public $check = array(
		'shop_goods_id' => array(
			//检查编号是否存在		
			'register_id'=>array(
					'!method'=>array(array(parent::TABLE_SHOP_GOODS_WHEN, 'find_exists_id'), "商品ID已经添加，请勿重复",) 
			),
		),
		"shop_goods_when_name" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("限时商品名称的数据类型不合法"),
					),
		),
		"shop_goods_when_info" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("限时商品简介的数据类型不合法"),
					),
		),
		"shop_goods_when_sort" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("限时商品排序的数据类型不合法"),
					),
		),
		"shop_goods_when_start_time" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少限时商品开始时间参数"),
					'echo'=>array("限时商品开始时间的数据类型不合法"),
					'match'=>array('/^[0-9]{4,}\-[0-9]{1,2}\-[0-9]{1,2}\s{1,}[0-9]{1,2}\:[0-9]{1,2}\:[0-9]{1,2}$/', "限时商品开始时间格式有误"),
					),
		),
		"shop_goods_when_end_time" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少限时商品结束时间参数"),
					'echo'=>array("限时商品结束时间的数据类型不合法"),
					'match'=>array('/^[0-9]{4,}\-[0-9]{1,2}\-[0-9]{1,2}\s{1,}[0-9]{1,2}\:[0-9]{1,2}\:[0-9]{1,2}$/', "限时商品结束时间格式有误"),
					),
		),
		
		
	);
	
	
	
	
	
		
				
	/**
	 * 根据ID，判断是否存在
	 * 
	 * @param	string 		$shop_goods_id
	 */
	public function find_exists_id($shop_goods_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_goods_id), function($shop_goods_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('shop_goods_when')
			->where(array('shop_goods_id=[+]', (string)$shop_goods_id))
			->find('shop_goods_id');
		});
	}
	
	
	
	
			
		
	/**
	 * 插入新数据
	 * 
	 * @param	array		$data			数据
	 * @param	array		$call_data		数据
	 * @return	bool
	 */
	public function insert($data = array(), $call_data = array()){
		if( empty($data) && empty($call_data) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('shop_goods_when')
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
		->table('shop_goods_when')
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
	 * @param	array	$shop_goods_id
	 * @return	array
	 */
	public function remove($shop_goods_id = ''){
		if( empty($shop_goods_id) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('shop_goods_when')
		->where(array('shop_goods_id=[+]', (string)$shop_goods_id))
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
	 * @param	string	$shop_goods_id
	 * @return	array
	 */
	public function find($shop_goods_id = ''){
		if( empty($shop_goods_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_goods_id), function($shop_goods_id){
			return db(parent::DB_APPLICATION_ID)
			->table('shop_goods_when')
			->where(array('shop_goods_id=[+]', (string)$shop_goods_id))
			->find();
		});
		
	}	
	
	
	
					
	/**
	 * 获取多条数据
	 *  $config = array(
	 * 	'where' => array(), //条件
	 * 	'orderby' => array(), //排序
	 * 	'limit'	=> array(0, page_size), //取出条数，默认不限制
	 *  'select' => array(),//查询的字段，可以是数组和字符串
	 * );
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function select($config = array()){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
			$where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
			$orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
			$limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
			$select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
			
			return db(parent::DB_APPLICATION_ID)
			->table('shop_goods_when')
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
	 * 	'where' => array(), //条件
	 * 	'orderby' => array(), //排序
	 * 	'limit'	=> array(0, page_size), //取出条数，默认获取10条
	 * );
	 * 
	 * limit的分页算法是：当前页数-1 * page_size
	 * 序号的算法：key键+1，+每页显示的条数。等于分页后的序号。{key + 1 + page_size}
	 * 
	 * 返回的数据：
	 * $data = array(
	 * 	'row_count' => //数据总条数
	 * 	'limit_count' => //已取出条数
	 * 	'page_size' => //每页的条数
	 * 	'page_count' => //总页数
	 *  'page_now' => //当前页数
	 * 	'data' => //数据
	 * );
	 * 
	 * @param	array	$config		配置信息
	 * @return	array
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
				'on' => 'sg.shop_goods_id = sgw.shop_goods_id'
			);
			
			//先获取总条数
			$find_data = db(parent::DB_APPLICATION_ID)
			->table('shop_goods_when sgw')
			->joinon($shop_goods)
			->call('where', $call_where)
			->find('count(distinct sg.shop_goods_id) as count');
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
					"sgw.*",
					"sg.*",
				);
			}
			
			$data['data'] =  db(parent::DB_APPLICATION_ID)
			->table('shop_goods_when sgw')
			->joinon($shop_goods)
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
			->select($select);
			
			return $data;
		});
		
		
	}
	
	

	


	
	/**
     * 清理所有状态
     * 
     * @param   void
     * @return boolean
     */
    public function update_state_clear(){
    	$time = time();
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('shop_goods_when')
		->data( array("shop_goods_when_state = [-]", "(CASE WHEN shop_goods_when_start_time>".$time." THEN '2' WHEN shop_goods_when_end_time<".$time." THEN '0' ELSE '1' END)", true) )
		->all()
		->update(/*function($p){
			printexit($p);
		}*/);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
    }
	
	
	
	
	
	   	        
    /**
     * 返回一个数据
     * 
     * @param   string      $alias	别名称
     * @return  string
     */
    public function sql_join_goods_id($alias = ""){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        
        return db(parent::DB_APPLICATION_ID)
        ->table('shop_goods_when join_sgw')
        ->where(array('join_sgw.shop_goods_id = '.$alias.'shop_goods_id'))
        ->find(array("join_sgw.shop_goods_id"), function($q){
            return $q['query']['find'];
        });
    }
    
	
	/**
     * 返回限时购的商品第
     * @return  string [sql语句]
     */
    public function sql_goods_id()
    {
        return db(parent::DB_APPLICATION_ID)
	        ->table('shop_goods_when')
	        ->where(array('shop_goods_when_state = 1'))
	        ->select(array('shop_goods_id'), function($q){
	            return $q['query']['select'];
	        });
    }
	
	
	/**
	 * 检测状态
	 * @param  array $data [限时购原始数据]
	 * @return bool
	 */
	public function check_state($data)
	{
		$time = time();
		$update_where = array(array('shop_goods_id=[+]', $data['shop_goods_id']));

		//已结束
		if ($data['shop_goods_when_end_time'] < $time) {
			if ($data['shop_goods_when_state'] != 0)
				$this->update($update_where, array('shop_goods_when_state'=>0, 'shop_goods_when_update_time'=>$time));

			return false;
		}

		//未开始
		if ($data['shop_goods_when_start_time'] > $time) {
			if ($data['shop_goods_when_state'] != 2)
				$this->update($update_where, array('shop_goods_when_state'=>2, 'shop_goods_when_update_time'=>$time));

			return false;
		}

		//进行中
		if ($data['shop_goods_when_state'] != 1)
			$this->update($update_where, array('shop_goods_when_state'=>1, 'shop_goods_when_update_time'=>$time));

		return true;
	}


}