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



namespace eapie\source\table\softstore;
use eapie\main;
class ss_order extends main {
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, "ss_order_product");
	
	
	
		
	/**
	 * 数据检测
	 * 
	 * @var	array
	 */
	public $check = array(
		"ss_order_id" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("订单ID参数不存在"),
					'echo'=>array("订单ID数据的类型不合法"),
					'!null'=>array("订单ID不能为空"),
					),
		),
		"ss_order_name" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("姓名参数不存在"),
					'echo'=>array("姓名数据的类型不合法"),
					'!null'=>array("姓名不能为空"),
					),
			//字符长度检测
			'length' => array(
					'<length'=>array(200, "姓名的字符长度太多")
					),	
		),
		"ss_order_phone" => array(
			//参数检测
			//参数检测
			'args'=>array(
					'match'=>array('/^[0-9]{11}$/iu', "手机格式不合法"),
					),
		),
		
		"ss_order_qq" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("QQ号的数据类型不合法"),
					),
			//字符长度检测
			'length' => array(
					'<length'=>array(200, "QQ号的字符长度太多")
					),	
		),
		
		"ss_order_wechat" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("微信号的数据类型不合法"),
					),
			//字符长度检测
			'length' => array(
					'<length'=>array(200, "微信号的字符长度太多")
					),	
		),
		
		
		"ss_order_company" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("公司名称的数据类型不合法"),
					),
			//字符长度检测
			'length' => array(
					'<length'=>array(500, "公司名称的字符长度太多")
					),			
		),
		
		
		"ss_order_region" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("地区信息的数据类型不合法"),
					),
			//字符长度检测
			'length' => array(
					'<length'=>array(500, "地区信息的字符长度太多")
					),			
		),
		
		
		"ss_order_contact_state" => array(
			//参数检测。订单的联系状态：0未联系，1已联系
			'args'=>array(
					'echo'=>array("联系状态的数据类型不合法"),
					'match'=>array('/^[01]{1}$/iu', "联系状态值必须是0、1"),
					),
		),
		
		"ss_order_contact_notes" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("联系备注的数据类型不合法"),
					),
		),
		
	);
	
	
	
				
			
	/**
	 * 根据ID，判断是否存在
	 * 
	 * @param	string 		$ss_order_id
	 */
	public function find_exists_id($ss_order_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($ss_order_id), function($ss_order_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('ss_order')
			->where(array('ss_order_id=[+]', (string)$ss_order_id))
			->find('ss_order_id');
		});
	}
	
		
	
	
	/**
	 * 获取一个数据的详情
	 * 
	 * @param	string	$ss_order_id
	 * @param	array	$config
	 * @return	array
	 */
	public function find_detail($ss_order_id, $config = array()){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($ss_order_id, $config), function($ss_order_id, $config){
			$where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
			$find = isset($config['find']) && is_array($config['find'])? $config['find'] : array();
			
			if( empty($find) ){
				$find = array(
					"u.user_nickname",
					"u.user_compellation",
					"u.user_parent_id",
					"so.*",
					"( sum(sop.ss_order_product_money * sop.ss_order_product_number) ) as ss_order_total_money",//获取订单的 总价格
				);
			}
			
			//用户数据
			$user = array(
				'table' => 'user u',
				'type' => 'left',
				'on' => 'u.user_id = so.user_id'
			);
			
			//订单产品数据
			$order_product = array(
				'table' => 'ss_order_product sop',
				'type' => 'left',
				'on' => 'sop.ss_order_id = so.ss_order_id'
			);
			
			
			$data = db(parent::DB_APPLICATION_ID)
			->table('ss_order so')
			->groupby("so.ss_order_id")
			->joinon($user, $order_product)
			->where(array('so.ss_order_id=[+]', (string)$ss_order_id))
			->call('where', $where)
			->find($find);
			
			//获取订单产品数据
			if( !empty($data['ss_order_id']) ){
				$order_product_config = array(
					"where" => array(
						array("ss_order_id=[+]", $data['ss_order_id'])
					),
					"orderby" => array(
						array("ss_product_name"),
						array("ss_order_product_time"),
					)
				);
				$data['ss_order_product'] = object(parent::TABLE_SOFTSTORE_ORDER_PRODUCT)->select($order_product_config);
			}
			
			return $data;
		});
		
	}
	
	
	
		
	/**
	 * 获取一个数据
	 * 
	 * @param	string	$ss_order_id
	 * @return	array
	 */
	public function find($ss_order_id = ''){
		if( empty($ss_order_id) ){
			return false;
			}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($ss_order_id), function($ss_order_id){
			return db(parent::DB_APPLICATION_ID)
			->table('ss_order')
			->where(array('ss_order_id=[+]', (string)$ss_order_id))
			->find();
		});
		
	}	
	
	
	
			
	/**
	 * 插入新数据
	 * 
	 * @param	array		$data			数据
	 * @param	array		$call_data		数据
	 * @return	int	返回订单ID
	 */
	public function insert($data = array(), $call_data = array()){
		if( empty($data) && empty($call_data) ){
			return false;
		}
		
		$bool = db(parent::DB_APPLICATION_ID)
		->table('ss_order')
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
		->table('ss_order')
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
	 * @param	array	$where
	 * @return	array
	 */
	public function delete($where = array()){
		if( empty($where) ){
			return false;
		}
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('ss_order')
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
	 * @param	array	$ss_order_id
	 * @return	array
	 */
	public function remove($ss_order_id = ''){
		if( empty($ss_order_id) ){
			return false;
		}
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('ss_order')
		->where(array('ss_order_id=[+]', (string)$ss_order_id))
		->delete();
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
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
			->table('ss_order')
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
		
			//用户数据
			$user = array(
				'table' => 'user u',
				'type' => 'left',
				'on' => 'u.user_id = so.user_id'
			);
			
			
			//订单产品数据
			$order_product = array(
				'table' => 'ss_order_product sop',
				'type' => 'left',
				'on' => 'sop.ss_order_id = so.ss_order_id'
			);
			
			
			//先获取总条数
			$find_data = db(parent::DB_APPLICATION_ID)
			->table('ss_order so')
			->joinon($user, $order_product)
			->call('where', $call_where)
			->find('count(distinct so.ss_order_id) as count');
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
					"u.user_nickname",
					"u.user_compellation",
					"u.user_parent_id",
					"so.*",
					"( sum(sop.ss_order_product_money * sop.ss_order_product_number) ) as ss_order_total_money",//获取订单的 总价格
				);
			}
						
			
			$data['data'] =  db(parent::DB_APPLICATION_ID)
			->table('ss_order so')
			->groupby("so.ss_order_id")
			->joinon($user, $order_product)
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
			->select($select);				
				
				
			return $data;
		});
	}
	
	
				
	

	
	
	
}
?>