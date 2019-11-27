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
class ss_product extends main {
	
	
	
	/*管理员ID不为空，表示是管理员上传；企业ID不为空，表示企业用户上传；业务角色ID不为空，表示业务员上传*/
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(
		__CLASS__, 
		"ss_order", 
		"ss_order_product", 
		"ss_product_attr", 
		"ss_product_file", 
		"ss_product_image",
		"ss_product_type",
		"ss_type"
	);
	
	
	
	
	/**
	 * 数据检测
	 * 
	 * @var	array
	 */
	public $check = array(
		'ss_product_id' => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少产品ID参数"),
					'echo'=>array("产品ID数据类型不合法"),
					'!null'=>array("产品ID不能为空"),
					),
			//检查编号是否存在		
			'exists_id'=>array(
					'method'=>array(array(parent::TABLE_SOFTSTORE_PRODUCT, 'find_exists_id'), "产品ID有误，数据不存在",) 
			),
			//检查编号是否合法	
			'legal_id'=>array(
					'method'=>array(array(parent::TABLE_SOFTSTORE_PRODUCT, 'find_legal_id'), "产品ID有误，未发布或者不存在") 
			),
		),
		
		
		"ss_product_name" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少产品名称参数"),
					'echo'=>array("产品名称数据类型不合法"),
					'!null'=>array("产品名称不能为空"),
					),
			//字符长度检测
			'length' => array(
					'<length'=>array(200, "产品名称的字符长度太多")
					),		
		),
		
		
		"ss_product_info" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("产品简介的数据类型不合法"),
					),
		),
		"ss_product_source" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("产品来源的数据类型不合法"),
					),
		),
		
		"ss_product_details" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("产品详细的数据类型不合法"),
					),
		),
		
		
		
		"ss_product_sort" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("产品排序的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/iu', "分类排序必须是整数"),
					),
		),
		
		
		"ss_product_state" => array(
			//参数检测。状态。0未通过审核；1已审核并发布；2待审核；3编辑中
			'args'=>array(
					'echo'=>array("产品状态的数据类型不合法"),
					'match'=>array('/^[0123]{1}$/iu', "产品状态值必须是0、1、2、3"),
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
	 * @param	string 		$ss_product_id
	 */
	public function find_exists_id($ss_product_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($ss_product_id), function($ss_product_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('ss_product')
			->where(array('ss_product_id=[+]', (string)$ss_product_id))
			->find('ss_product_id');
		});
	}
	
		
		
		
	/**
	 * 获取一个数据
	 * 
	 * @param	string	$ss_product_id
	 * @return	array
	 */
	public function find($ss_product_id = ''){
		if( empty($ss_product_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($ss_product_id), function($ss_product_id){
			return db(parent::DB_APPLICATION_ID)
			->table('ss_product')
			->where(array('ss_product_id=[+]', (string)$ss_product_id))
			->find();
		});
		
	}	
	
	
	
	/**
	 * 根据ID，判断产品是否合法（1已审核并发布）
	 * ss_product_state 状态。0未通过审核；1已审核并发布；2待审核；3编辑中
	 * 
	 * @param	string 		$ss_product_id
	 */
	public function find_legal_id($ss_product_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($ss_product_id), function($ss_product_id){
			return (bool)db(parent::DB_APPLICATION_ID)
			->table('ss_product')
			->where(array('ss_product_id=[+]', (string)$ss_product_id), array("ss_product_state=1"))
			->find('ss_product_id');
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
		->table('ss_product')
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
	 * @param	array	$ss_product_id
	 * @return	array
	 */
	public function remove($ss_product_id = ''){
		if( empty($ss_product_id) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('ss_product')
		->where(array('ss_product_id=[+]', (string)$ss_product_id))
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
		->table('ss_product')
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
			->table('ss_product')
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
				'on' => 'u.user_id = sp.user_id'
			);
			
			
			//先获取总条数
			$find_data = db(parent::DB_APPLICATION_ID)
			->table('ss_product sp')
			->joinon($user)
			->call('where', $call_where)
			->find('count(distinct sp.ss_product_id) as count');
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
					"sp.ss_product_id",
					"sp.user_id",
					"sp.ss_product_name",
					"sp.ss_product_info",
					"sp.ss_product_source",
					"sp.ss_product_state",
					"sp.ss_product_sort",
					"sp.ss_product_trash",
					"sp.ss_product_trash_time",
					"sp.ss_product_update_time",
					"sp.ss_product_insert_time",
				);
			}
			
			$data['data'] =  db(parent::DB_APPLICATION_ID)
			->table('ss_product sp')
			->joinon($user)
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
			->select($select);
			
			return $data;
		});
		
		
	}
	
	

	
	
	/**
	 * 获取附加数据
	 * 
	 * @param	array		$data
	 * @param	array		$config
	 * @return	array	
	 */
	public function get_additional_data($data = array(), $config = array()){
		if( empty($data) ){
			return $data;
		}
		$product_ids = array();
		foreach($data as $key => $value){
			if( !isset($value['ss_product_id']) ){
				//分类id不存在，则直接返回
				break;
			}
			$data[$key]["ss_product_type"] = array();//初始化键值
			$data[$key]["ss_product_image_main"] = array();
			$data[$key]["ss_product_attr"] = array();
			$product_ids[] = $value['ss_product_id'];
		}	
		
		//没有可查询的数据
		if( empty($product_ids) ){
			return $data;
		}
		
		//标识符的目的是，有些数据不一致
		$identifier = md5(cmd(array($product_ids), "json encode").cmd(array($data), "json encode")).
		md5(cmd(array($data), "json encode").cmd(array($product_ids), "json encode"));
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($product_ids, $config, $identifier), function($product_ids, $config, $identifier) use ($data){
		
		//获取分类数据
		$in_string = "\"".implode("\",\"", $product_ids)."\"";
		
		
		//获取产品分类数据
		if(empty($config['ss_product_type']['where']) || !is_array($config['ss_product_type']['where']))
		$config['ss_product_type']['where'] = array();
		if(empty($config['ss_product_type']['orderby']) || !is_array($config['ss_product_type']['orderby']))
		$config['ss_product_type']['orderby'] = array();
		if(empty($config['ss_product_type']['limit']) || !is_array($config['ss_product_type']['limit']))
		$config['ss_product_type']['limit'] = array();
		if(empty($config['ss_product_type']['select']) || !is_array($config['ss_product_type']['select']))
		$config['ss_product_type']['select'] = array();
		$config['ss_product_type']['where'][] = array("[and] spt.ss_product_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
		//$config['ss_product_type']['where'][] = array("[and] st.ss_type_state=1");
		$product_type_data = object(parent::TABLE_SOFTSTORE_PRODUCT_TYPE)->select_join($config['ss_product_type']);
		
		
		//获取产品主图的数据
		if(empty($config['ss_product_image']['where']) || !is_array($config['ss_product_image']['where']))
		$config['ss_product_image']['where'] = array();
		if(empty($config['ss_product_image']['orderby']) || !is_array($config['ss_product_image']['orderby']))
		$config['ss_product_image']['orderby'] = array();
		if(empty($config['ss_product_image']['limit']) || !is_array($config['ss_product_image']['limit']))
		$config['ss_product_image']['limit'] = array();
		if(empty($config['ss_product_image']['select']) || !is_array($config['ss_product_image']['select']))
		$config['ss_product_image']['select'] = array();
		
		$config['ss_product_image']['where'][] = array("[and] ss_product_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
		$config['ss_product_image']['where'][] = array("[and] ss_product_image_state=1");
		$config['ss_product_image']['where'][] = array("[and] ss_product_image_main=1");
		$product_image_data = object(parent::TABLE_SOFTSTORE_PRODUCT_IMAGE)->select($config['ss_product_image']);
		//获取产品非主图一张，作为替补
		$product_image_not_main_data = object(parent::TABLE_SOFTSTORE_PRODUCT_IMAGE)->select_not_main($product_ids, 1);
		
		
		//获取当前产品的所有属性
		if(empty($config['ss_product_attr']['where']) || !is_array($config['ss_product_attr']['where']))
		$config['ss_product_attr']['where'] = array();
		if(empty($config['ss_product_attr']['orderby']) || !is_array($config['ss_product_attr']['orderby']))
		$config['ss_product_attr']['orderby'] = array();
		if(empty($config['ss_product_attr']['limit']) || !is_array($config['ss_product_attr']['limit']))
		$config['ss_product_attr']['limit'] = array();
		if(empty($config['ss_product_attr']['select']) || !is_array($config['ss_product_attr']['select']))
		$config['ss_product_attr']['select'] = array();
		
		$config['ss_product_attr']['where'][] = array("[and] ss_product_id IN([-])", $in_string, true);//是不加单引号并且强制不过滤
		if( empty($config['ss_product_attr']['orderby']) ){
			$config['ss_product_attr']['orderby'][] = array("ss_product_attr_sort");
			$config['ss_product_attr']['orderby'][] = array("ss_product_attr_insert_time");
		}
		$product_attr_data = object(parent::TABLE_SOFTSTORE_PRODUCT_ATTRIBUTE)->select($config['ss_product_attr']);
		
		
		foreach($data as $parent_key => $parent_value){
			//已经删完了则终止
			if( empty($product_type_data) && 
			empty($product_image_data) && 
			empty($product_image_not_main_data) &&
			empty($product_attr_data) ){
				break;
			}
			
			//获得主图，只获取一张
			if( !empty($product_image_data) ){
				foreach($product_image_data as $image_key => $image_value){
					if($image_value['ss_product_id'] == $parent_value['ss_product_id']){
						$data[$parent_key]['ss_product_image_main'] = $image_value;
						unset($product_image_data[$image_key]);
					}
				}
			}
			//判断替补主图
			if( !empty($product_image_not_main_data) ){
				foreach($product_image_not_main_data as $not_main_image_key => $not_main_image_value){
					if($not_main_image_value['ss_product_id'] == $parent_value['ss_product_id']){
						if( !empty($data[$parent_key]['ss_product_image_main']) ){
							unset($product_image_not_main_data[$not_main_image_key]);
						}else{
							$data[$parent_key]['ss_product_image_main'] = $not_main_image_value;
							unset($product_image_not_main_data[$not_main_image_key]);
						}
					}
				}
			}
			
			//获得分类
			if( !empty($product_type_data) ){
				foreach($product_type_data as $type_key => $type_value){
					if($type_value['ss_product_id'] == $parent_value['ss_product_id']){
						$data[$parent_key]['ss_product_type'][] = $type_value;
						unset($product_type_data[$type_key]);
					}
				}
			}
			
			
			//获取当前产品属性
			if( !empty($product_attr_data) ){
				foreach($product_attr_data as $attr_key => $attr_value){
					if($attr_value['ss_product_id'] == $parent_value['ss_product_id']){
						$data[$parent_key]['ss_product_attr'][] = $attr_value;
						unset($product_attr_data[$attr_key]);
					}
				}
			}
			
			
			//清理规格属性，将父级与子级分开
			//最小值  以必选项为准，然后所有价格叠加为最大价格
			//并且要清理有错误的库存
			
			//如果不存在必选，以最小价格
			//最小值  以必选项为准，然后所有价格叠加为最大价格
			//并且要清理有错误的库存
			//如果不存在必选，以最小价格
			
			$attr_required = array();//必选的
			$attr_not_required = array();//非必选的
			if( !empty($data[$parent_key]['ss_product_attr']) ){
				$temp_product_attr = $data[$parent_key]['ss_product_attr'];
				$data[$parent_key]['ss_product_attr'] = array();
				
				//先获取父级
				foreach($temp_product_attr as $temp_attr_k => $temp_attr_v){
					if( empty($temp_attr_v["ss_product_attr_parent_id"]) ){
						$temp_attr_v["son"] = array();
						$data[$parent_key]['ss_product_attr'][] = $temp_attr_v;
						unset($temp_product_attr[$temp_attr_k]);
					}
				}
				
				//获取子级，并且获取价格
				if( !empty($data[$parent_key]['ss_product_attr']) && !empty($temp_product_attr) ){
					foreach($data[$parent_key]['ss_product_attr'] as $attr_k => $attr_v){
						
						if( empty($temp_product_attr) ){
							break;//没有属性子级了
						}
						
						foreach($temp_product_attr as $temp_attr_k => $temp_attr_v){
							if($temp_attr_v["ss_product_attr_parent_id"] == $attr_v["ss_product_attr_id"] ){
								$data[$parent_key]['ss_product_attr'][$attr_k]["son"][] = $temp_attr_v;
								unset($temp_product_attr[$temp_attr_k]);
								
								//判断是否存在必选
								if( !empty($temp_attr_v["ss_product_attr_required"]) ){
									$attr_required[] = $temp_attr_v;
								}else{
									$attr_not_required[] = $temp_attr_v;
								}
								
							}	
						}
						
					}
				}
				
			}
			
			
			//计算价格。最小、最大价格
			if( !empty($attr_required) || !empty($attr_not_required) ){
				//如果 非必选的 库存售价中 存在 必选，那么删除这个必选
				if( !empty($attr_not_required) ){
					foreach($attr_not_required as $attr_n_r_v){
						//转换为整数
						$attr_n_r_v["ss_product_attr_money"] = (int)$attr_n_r_v["ss_product_attr_money"];
						
						//最小价格。非叠加
						if( !isset($data[$parent_key]["ss_product_min_money"]) ||
						$data[$parent_key]["ss_product_min_money"] > $attr_n_r_v["ss_product_attr_money"]){
							$data[$parent_key]["ss_product_min_money"] = $attr_n_r_v["ss_product_attr_money"];
						}
						
						//最大价格
						if( isset($data[$parent_key]["ss_product_max_money"]) ){
							$data[$parent_key]["ss_product_max_money"] += $attr_n_r_v["ss_product_attr_money"];
						}else{
							$data[$parent_key]["ss_product_max_money"] = $attr_n_r_v["ss_product_attr_money"];
						}
						
					}
				}
				
				
				//如果存在必选
				if( !empty($attr_required) ){
					//必选最小价格有优先级
					if( isset($data[$parent_key]["ss_product_min_money"]) ) unset($data[$parent_key]["ss_product_min_money"]);
					foreach($attr_required as $attr_r_v){
						//转换为整数
						$attr_r_v["ss_product_attr_money"] = (int)$attr_r_v["ss_product_attr_money"];
						
						//最小价格。叠加
						if( !isset($data[$parent_key]["ss_product_min_money"]) ){
							$data[$parent_key]["ss_product_min_money"] = $attr_r_v["ss_product_attr_money"];
						}else{
							$data[$parent_key]["ss_product_min_money"] += $attr_r_v["ss_product_attr_money"];
						}
						
						//最大价格
						if( isset($data[$parent_key]["ss_product_max_money"]) ){
							$data[$parent_key]["ss_product_max_money"] += $attr_r_v["ss_product_attr_money"];
						}else{
							$data[$parent_key]["ss_product_max_money"] = $attr_r_v["ss_product_attr_money"];
						}
						
					}
				}
				
				
				
			}

			
			
			
			
		}


		return $data;

		});
		
		
	}
	
	
	
	
	
	
	
}
?>