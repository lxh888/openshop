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



namespace eapie\source\table\application;
use eapie\main;
class type extends main {
	
	
	/*分类表*/
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(
		__CLASS__, 
		"shop_goods", 
		"shop_goods_type"
	);
	
	
	
		
	/**
	 * 数据检测
	 * 
	 * @var	array
	 */
	public $check = array(
		'type_id' => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少分类ID参数"),
					'echo'=>array("分类ID数据类型不合法"),
					'!null'=>array("分类ID不能为空"),
					),
			//检查编号是否存在		
			'exists_id'=>array(
					'method'=>array(array(parent::TABLE_TYPE, 'find_exists_id'), "分类ID有误，数据不存在",) 
			),
			
		),
		
		"type_parent_id" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("分类的父ID数据类型不合法"),
					),
					
		),
		"type_name" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少分类名称参数"),
					'echo'=>array("分类名称数据类型不合法"),
					'!null'=>array("分类名称不能为空"),
					),
			//字符长度检测
			'length' => array(
					'<length'=>array(200, "分类名称的字符长度太多")
					),		
		),
		
		
		"type_info" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("分类简介的数据类型不合法"),
					),
		),
		
		"type_module" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少分类模块名称参数"),
                    'echo'=>array("分类模块名称的数据类型不合法"),
                    '!null'=>array("分类模块名称不能为空"),
                    'method'=>array(array(parent::TABLE_TYPE, 'check_module'), "分类模块名称输入有误，不能被识别")       
                ),
        ),
        
		'type_label' => array(
            'args'=>array(
                'echo'  => array('分类标签数据类型不合法'),
            ),
        ),
        
		'type_comment' => array(
            'args'=>array(
                'echo'  => array('分类注释信息数据类型不合法'),
            ),
        ),
        
		'type_json' => array(
            'args'=>array(
                'method'  => array(array(parent::TABLE_TYPE, 'check_json'), '分类的JSON配置格式输入有误') ,
            ),
        ),
		
		
		"type_sort" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("分类排序的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/i', "分类排序必须是整数"),
					),
		),
		
		
		"type_state" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("分类状态的数据类型不合法"),
					'match'=>array('/^[01]{1}$/i', "分类状态必须是0或1"),
					),
		),
		'type_merchant_usable' => array(
			//参数检测
			'args'=>array(
				'echo'=>array('商家是否可使用的数据类型不合法'),
				'match'=>array('/^[01]{1}$/i', '商家是否可使用必须是0或1'),
			),
		),
		'type_logo_image_id' => array(
            'args'=>array(
                'echo'  => array('分类LOGO图片ID类型不合法')
            )
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
     * 获取模块
     * 
     * @param   void
     * @return  array
     */
    public function get_module(){
        return array(
        	parent::MODULE_MERCHANT_TYPE => "商家分类",
            parent::MODULE_SHOP_GOODS_TYPE => "商城商品分类",
            parent::MODULE_BRAND_TYPE => "品牌分类",
            parent::MODULE_APP_ARTICLE_TYPE => "应用软件文章分类",
            parent::MODULE_CMS_ARTICLE_TYPE => "内容管理系统文章分类",
            parent::MODULE_EXPRESS_ORDER_TYPE => "快递系统订单分类",
            parent::MODULE_HOME => "首页分类",
            parent::MODULE_MERCHANT_GOODS_TYPE => "商家商品分类",
        );
    }
    
	
		
    /**
     * 检测模块
     * 
     * @param   string  $data
     * @return  array
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
     * 判断JSON数据
	 * 
     * @param  string	 $slideshow_id		轮播图ID
     * @return bool
     */
	public function check_json($json){
		if( !is_string($json) ){
			return false;
		}
		$json_array = cmd(array($json), "json decode");
		return is_array($json_array)? true : false;
	}
	
			
			
	/**
	 * 根据ID，判断是否存在
	 * 
	 * @param	string 		$type_id
	 */
	public function find_exists_id($type_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($type_id), function($type_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('type')
			->where(array('type_id=[+]', $type_id))
			->find('type_id');
		});
	}
	
		
			
	
	/**
	 * 根据ID，判断是否存在子级
	 * 
	 * @param	string 		$type_id
	 */
	public function find_exists_son_id($type_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($type_id), function($type_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('type')
			->where(array('type_parent_id=[+]', $type_id))
			->find('type_id');
		});
	}
	
	
	
	
	/**
	 * 根据ID，判断是否存在子级
	 * 
	 * @param	string 		$type_id
	 * @param	string 		$merchant_id
	 */
	public function find_merchant_exists_son_id($type_id, $merchant_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($type_id, $merchant_id), function($type_id, $merchant_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('type')
			->where(array('type_parent_id=[+]', $type_id), array('[and] merchant_id=[+]', $merchant_id))
			->find('type_id');
		});
	}
	
	
		
	
	/**
	 * 获取一个数据——根据类别ID
	 * 
	 * @param	string	$type_id
	 * @return	array
	 */
	public function find($id = '')
	{
		if (!$id || !is_string($id))
			return null;

		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($id), function($id){
			return db(parent::DB_APPLICATION_ID)
				->table('type')
				->where(array('type_id=[+]', $id))
				->find();
		});
		
	}	



	/**
	 * 根据条件获取一个数据
	 * 
	 * @param	array	$where
	 * @return	array
	 */
	public function find_where($where){
		if( empty($where) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($where), function($where){
			return db(parent::DB_APPLICATION_ID)
			->table('type')
			->call('where', $where)
			->find();
		});
		
	}	





	/**
	 * 获取一个数据——根据类别名称
	 * 
	 * @param  string $name 类别名称
	 * @return array
	 */
	public function find_by_name($name = '')
	{
		if (!$name || !is_string($name))
			return null;

		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($name), function($name){
			return db(parent::DB_APPLICATION_ID)
				->table('type')
				->where(array('type_name=[+]', $name))
				->find();
		});
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
		->table('type')
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
	 * @param	array	$type_id
	 * @return	array
	 */
	public function remove($type_id = ''){
		if( empty($type_id) ){
			return false;
		}
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('type')
		->where(array('type_id=[+]', (string)$type_id))
		->delete();
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
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
		->table('type')
		->call('data', $call_data)
		->insert($data);
		
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
			->table('type')
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
		});
	}		
	
	
	
	
	/**
	 * 获取多条数据，包括子级数据
	 *  $config = array(
	 * 	'where' => array(), //条件
	 * 	'orderby' => array(), //排序
	 * 	'limit'	=> array(0, page_size), //取出条数，默认不限制
	 *  'select' => array(),//查询的字段，可以是数组和字符串
	 * );
	 * 
	 * @param	array	$parent_config	父级配置
	 * @param	array	$son_config	子级配置
	 * @return	array
	 */
	public function select_parent_son_all($parent_config = array(), $son_config = array()){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($parent_config, $son_config), function($parent_config, $son_config){
			$parent_where = isset($parent_config['where']) && is_array($parent_config['where'])? $parent_config['where'] : array();
			$parent_orderby = isset($parent_config['orderby']) && is_array($parent_config['orderby'])? $parent_config['orderby'] : array();
			$parent_limit = isset($parent_config['limit']) && is_array($parent_config['limit'])? $parent_config['limit'] : array();
			$parent_select = isset($parent_config['select']) && is_array($parent_config['select'])? $parent_config['select'] : array();
			
			$son_where = isset($son_config['where']) && is_array($son_config['where'])? $son_config['where'] : array();
			$son_orderby = isset($son_config['orderby']) && is_array($son_config['orderby'])? $son_config['orderby'] : array();
			$son_limit = isset($son_config['limit']) && is_array($son_config['limit'])? $son_config['limit'] : array();
			$son_select = isset($son_config['select']) && is_array($son_config['select'])? $son_config['select'] : array();
			
			$data = db(parent::DB_APPLICATION_ID)
			->table('type')
			->call('where', $parent_where)
			->call('orderby', $parent_orderby)
			->call('limit', $parent_limit)
			->select($parent_select);

			//没有父类
			if( empty($data) ){
				return $data;
			}
			
			//获取子分类
			if( !isset($son_where) || !is_array($son_where)){
				$son_where = array();
			}
			$type_parent_ids = array();
			foreach($data as $key => $value){
				if( !isset($value['type_id']) || !empty($value['type_parent_id']) ){
					//分类id不存在，或者不是顶级，则直接返回
					break;
				}
				$data[$key]["son"] = array();//初始化键值
				$type_parent_ids[] = $value['type_id'];
			}
			
			//没有父类
			if( empty($type_parent_ids) ){
				return $data;
			}
			
			$son_in_string = "\"".implode("\",\"", $type_parent_ids)."\"";
			$son_where[] = array("[and] type_parent_id IN([-])", $son_in_string, true);//是不加单引号并且强制不过滤
			$son_data = db(parent::DB_APPLICATION_ID)
			->table('type')
			->call('where', $son_where)
			->call('orderby', $son_orderby)
			->call('limit', $son_limit)
			->select($son_select);
			
			//没有子数据
			if( empty($son_data) ){
				return $data;
			}
			
			
			foreach($data as $parent_key => $parent_value){
				if( empty($son_data) ){
					break;
				}
				
				//循环子级
				foreach($son_data as $son_key => $son_value){
					if($son_value['type_parent_id'] == $parent_value['type_id']){
						$data[$parent_key]['son'][] = $son_value;
						unset($son_data[$son_key]);
					}
				}
				
			}
			
			
			return $data;
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
		
			//父级数据
			$type_parent = array(
				'table' => 'type parent_t',
				'type' => 'left',
				'on' => 'parent_t.type_id = t.type_parent_id'
			);
			
			//图片数据
            $image = array(
                'table' => 'image i',
                'type' => 'LEFT',
                'on' => 'i.image_id = t.type_logo_image_id'
            );
			
			
			//先获取总条数
			$find_data = db(parent::DB_APPLICATION_ID)
			->table('type t')
			->joinon($type_parent, $image)
			->call('where', $call_where)
			->find('count(distinct t.type_id) as count');
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
					't.*',
					'parent_t.type_name as type_parent_name',
					'parent_t.type_state as type_parent_state',
					'('.$this->sql_son_count("t").') as type_son_count',//获取 子分类的 总数
					"i.image_width as type_logo_image_width",
					"i.image_height as type_logo_image_height"
				);
			}
						
			
			$data['data'] =  db(parent::DB_APPLICATION_ID)
			->table('type t')
			->joinon($type_parent, $image)
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
			->select($select);				
				
			return $data;
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
	public function select_pages($config = array()){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
			$call_where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
			$call_orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $call_limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
            $call_joinon = isset($config['joinon']) && is_array($config['joinon'])?$config['joinon']:array(); 
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
		
			
			
			
			//先获取总条数
			$find_data = db(parent::DB_APPLICATION_ID)
			->table('type t')
			->call('joinon',$call_joinon)
			->call('where', $call_where)
			->find('count(distinct t.type_id) as count');
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
					't.*',
					'parent_t.type_name as type_parent_name',
					'parent_t.type_state as type_parent_state',
					'('.$this->sql_son_count("t").') as type_son_count',//获取 子分类的 总数
					"i.image_width as type_logo_image_width",
                    "i.image_height as type_logo_image_height"
				);
			}
						
			
			$data['data'] =  db(parent::DB_APPLICATION_ID)
			->table('type t')
			->call('joinon',$call_joinon)
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
			->select($select);				
				
			return $data;
		});
	}
	
	
	
	
	/**
	 * 获取子分类的总数，SQL语句
	 * 
	 * @param	string		$alias
	 * @return	string
	 */
	public function sql_son_count($alias = ""){
		if( is_string($alias) && $alias != "" ){
			$alias .= ".";
		}
		
		return db(parent::DB_APPLICATION_ID)
		->table('type son')
		->where(array('son.type_parent_id = '.$alias.'type_id'))
		->find(array('count(distinct son.type_id) as count'), function($q){
			return $q['query']['find'];
		});
	}
	
	/**
	 * 获取子分类
	 */
	public function select_son($config=array()){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
			$where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
			$select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
			
			return db(parent::DB_APPLICATION_ID)
				->table('type')
				->call('where',$where)
				->call('orderby',$orderby)
				->select($select);
		});
	}
	
	
	
	/**
     * 根据条件获取总数量
     */
    public function find_count( $call_where=array() )
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where), function($call_where){
            //先获取总条数
			return db(parent::DB_APPLICATION_ID)
			->table('type')
			->call('where', $call_where)
			->find('count(distinct type_id) as count');
        });
        
    }
	
	
	
	/**
	 * 传入子级，获取商家分类其父级
	 * 
	 * @param	array	$son_types
	 * @return array
	 */
	public function select_merchant_parent( $son_types = array() ){
		if( empty($son_types) ){
			return array();
		}
		
		$type_parent_ids = array();
		foreach($son_types as $key => $value){
			if( empty($value['type_parent_id']) || empty($value['merchant_id']) ){
				//分类id不存在，则直接返回
				continue;
			}
			
			$type_parent_ids[] = $value['type_parent_id'];
		}	
		
		if( empty($type_parent_ids) ){
			return array();
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($type_parent_ids, $son_types), function($type_parent_ids, $son_types){
			//获取分类数据
			$in_string = "\"".implode("\",\"", $type_parent_ids)."\"";
            //先获取总条数
			$data = db(parent::DB_APPLICATION_ID)
			->table('type')
			->where( array("[and] type_id IN([-])", $in_string, true), array("[and] merchant_id<>''") )
			->select();
			
			if( empty($data) ){
				return array();
			}
			
			foreach($data as &$parent_value){
				$parent_value['son'] = array();
				foreach($son_types as $key => $value){
					if( !empty($value['type_parent_id']) && $value['type_parent_id'] == $parent_value['type_id'] ){
						$parent_value['son'][] = $value;
					}
				}
			}
			
			return $data;
        });
		
	}
	
	
	
	
	
	
	
	
	
}
?>