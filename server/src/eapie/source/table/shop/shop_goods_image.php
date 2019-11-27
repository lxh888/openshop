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
class shop_goods_image extends main {
	
	
		
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, "shop_goods", "image");
	
	
		
	/**
	 * 数据检测
	 * 
	 * @var	array
	 */
	public $check = array(
		'shop_goods_image_id' => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少商品图片ID参数"),
					'echo'=>array("商品图片ID数据类型不合法"),
					'!null'=>array("商品图片ID不能为空"),
					),
			//检查编号是否存在		
			'exists_id'=>array(
					'method'=>array(array(parent::TABLE_SHOP_GOODS_IMAGE, 'find_exists_id'), "商品图片ID有误，数据不存在",) 
			),
			
		),
	
		"shop_goods_image_main" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("商品图片是否为主图的数据类型不合法"),
					'match'=>array('/^[01]{1}$/iu', "商品图片是否为主图必须是0或1"),
					),
		),
		
		
		
	);
	
	
	
	
			
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
	 * 根据ID，判断是否存在
	 * 
	 * @param	string 		$shop_goods_image_id
	 * @return bool
	 */
	public function find_exists_id($shop_goods_image_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_goods_image_id), function($shop_goods_image_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('shop_goods_image')
			->where(array('shop_goods_image_id=[+]', (string)$shop_goods_image_id))
			->find('shop_goods_image_id');
		});
		
	}
		
	
		
	/**
	 * 获取一个数据
	 * 
	 * @param	string	$shop_goods_image_id
	 * @return	array
	 */
	public function find($shop_goods_image_id = ''){
		if( empty($shop_goods_image_id) ){
			return false;
			}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_goods_image_id), function($shop_goods_image_id){
			return db(parent::DB_APPLICATION_ID)
			->table('shop_goods_image')
			->where(array('shop_goods_image_id=[+]', (string)$shop_goods_image_id))
			->find();
		});
		
	}	


	/**
	 * 获取某商品的一个主图图片ID
	 * 
	 * @param   string $shop_goods_id 商品ID
     * @return  string
	 */
	public function get_main_img_id($shop_goods_id = '')
	{
		
		//内连图片表
		$join_image = array(
			'table' => 'image i',
			'type' => 'INNER',
			'on' => 'i.image_id = sgi.image_id'
		);
		
		$data = db(parent::DB_APPLICATION_ID)
	        ->table('shop_goods_image sgi')
			->joinon($join_image)
	        ->where(array('sgi.shop_goods_id=[+]', $shop_goods_id), array('sgi.shop_goods_image_main = 1'))
			->orderby(array('i.image_sort'), array('sgi.shop_goods_image_time'), array('sgi.shop_goods_image_id'))
	        ->find(array('sgi.image_id'));

	    return empty($data['image_id'])? '': $data['image_id'];
	}



	/**
	 * 获取多个商品主图
	 */
	public function get_main_imgs($data=array())
	{
		$goods_ids = array();
        foreach ($data as $v) {
            if (!in_array($v['shop_goods_id'], $goods_ids) && $v['shop_goods_id']) {
                $goods_ids[] = $v['shop_goods_id'];
            }
        }

        if(empty($goods_ids)){
            return $data;
        }

        //获取分类数据
        $in_string = "\"" . implode("\",\"", $goods_ids) . "\"";

		//内连图片表
		$join_image = array(
			'table' => 'image i',
			'type' => 'INNER',
			'on' => 'i.image_id = sgi.image_id'
		);
		
		$img_data = db(parent::DB_APPLICATION_ID)
	        ->table('shop_goods_image sgi')
			->joinon($join_image)
	        ->where(array("[and] shop_goods_id IN([-])", $in_string, true), array('sgi.shop_goods_image_main = 1'))
			->orderby(array('i.image_sort'), array('sgi.shop_goods_image_time'), array('sgi.shop_goods_image_id'))
	        ->select(array('sgi.image_id','shop_goods_image_id','shop_goods_id'));

		foreach ($data as &$v) {
			if (empty($img_data)) {
				break;
			}
			foreach ($img_data as $v1) {
				if ($v1['shop_goods_id'] == $v['shop_goods_id']) {
					$v['goods']['image_id'] = $v1['image_id'];
					break;
				}
			}
		}
		
	    return $data;
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
			->table('shop_goods_image')
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
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
				'on' => 'sg.shop_goods_id = sgi.shop_goods_id'
			);
			
			//用户数据
			$user = array(
				'table' => 'user u',
				'type' => 'left',
				'on' => 'u.user_id = sgi.user_id'
			);	
				
			//图片数据
			$image = array(
				'table' => 'image i',
				'type' => 'left',
				'on' => 'i.image_id = sgi.image_id'
			);	
				
				
			if( empty($select) ){
				$select = array(
					"u.user_nickname",
					"sg.shop_goods_name",
					"sgi.*",
					"i.*"
				);
			}	
				
			return db(parent::DB_APPLICATION_ID)
			->table('shop_goods_image sgi')
			->joinon($shop_goods, $user, $image)
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
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
		->table('shop_goods_image')
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
            ->table('shop_goods_image')
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
		->table('shop_goods_image')
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
	 * @param	array	$shop_goods_image_id
	 * @return	array
	 */
	public function remove($shop_goods_image_id = ''){
		if( empty($shop_goods_image_id) ){
			return false;
		}
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('shop_goods_image')
		->where(array('shop_goods_image_id=[+]', (string)$shop_goods_image_id))
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
				'on' => 'sg.shop_goods_id = sgi.shop_goods_id'
			);
			
			//用户数据
			$user = array(
				'table' => 'user u',
				'type' => 'left',
				'on' => 'u.user_id = sgi.user_id'
			);
			
			//图片数据
			$image = array(
				'table' => 'image i',
				'type' => 'left',
				'on' => 'i.image_id = sgi.image_id'
			);	
			
			
			//先获取总条数
			$find_data = db(parent::DB_APPLICATION_ID)
			->table('shop_goods_image sgi')
			->joinon($shop_goods, $user, $image)
			->call('where', $call_where)
			->find('count(distinct sgi.shop_goods_image_id) as count');
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
					"sg.shop_goods_name",
					"sgi.*",
					"i.*"
				);
			}
			
			$data['data'] =  db(parent::DB_APPLICATION_ID)
			->table('shop_goods_image sgi')
			->joinon($shop_goods, $user, $image)
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
			->select($select);
				
			return $data;
		});
		
		
	}
	
	
	
	/**
	 * 获取图片ID
	 * 
	 * @param   string      $alias	别名称
     * @return  string
	 */
	public function sql_join_main_id( $alias = ""){
		if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
		
		//图片数据
		$image = array(
			'table' => 'image i',
			'type' => 'INNER',
			'on' => 'i.image_id = sgi.image_id'
		);
		
		return db(parent::DB_APPLICATION_ID)
        ->table('shop_goods_image sgi')
		->joinon($image)
        ->where(array('sgi.shop_goods_id = '.$alias.'shop_goods_id'), array('sgi.shop_goods_image_main = 1'))
		->orderby(array('i.image_sort'), array('sgi.shop_goods_image_time'), array('sgi.shop_goods_image_id'))
        ->find(array('sgi.image_id'), function($q){
            return $q['query']['find'];
        });
		
	}
	
	
	
	/**
	 * 获取图片ID
	 * 
	 * @param   string      $alias	别名称
     * @return  string
	 */
	public function sql_join_cart_main_id( $alias = ""){
		if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
		
		//图片数据
		$image = array(
			'table' => 'image i',
			'type' => 'INNER',
			'on' => 'i.image_id = sgi.image_id'
		);
		
		return db(parent::DB_APPLICATION_ID)
        ->table('shop_goods_image sgi')
		->joinon($image)
        ->where(array('sgi.shop_goods_id = '.$alias.'shop_goods_id'), array('sgi.shop_goods_image_main = 1'))
		->orderby(array('i.image_sort'), array('sgi.shop_goods_image_time'), array('sgi.shop_goods_image_id'))
        ->find(array('sgi.image_id'), function($q){
            return $q['query']['find'];
        });
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
}
?>