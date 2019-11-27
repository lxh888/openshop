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

class coupon extends main
{


    /**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(
		__CLASS__, 
		"express",//快递
    );
    

    /**
	 * 数据检测
	 * 
	 * @var	array
	 */
	public $check = array(
    	'coupon_id' => array(
            //参数检测
            'args'=>array(
                'exist'=>array("缺少优惠券ID参数"),
                'echo'=>array("优惠券ID的数据类型不合法"),
                '!null'=>array("优惠券ID不能为空"),
            ),
        ),
        'coupon_name' => array(
            //参数检测
			'args'=>array(
					'exist'=>array("缺少优惠券名称参数"),
					'echo'=>array("优惠券名称数据类型不合法"),
					'!null'=>array("优惠券名称不能为空"),
					'<length'=>array(200, "优惠券名称的字符长度太多") //字符长度检测
					),
        ),
		
		"coupon_info" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("优惠券简介的数据类型不合法"),
					),
		),
    	'coupon_end_time' => array(
            //参数检测
            'args'=>array(
                'exist'=>array("缺少优惠券结束时间参数"),
                'echo'=>array("优惠券结束时间的数据类型不合法"),
                'match'=>array('/^[0-9]{4,}\-[0-9]{1,2}\-[0-9]{1,2}\s{1,}[0-9]{1,2}\:[0-9]{1,2}\:[0-9]{1,2}$/', "优惠券结束时间格式有误"),
					
            ),
        ),
    	'coupon_start_time' => array(
            //参数检测
            'args'=>array(
                'exist'=>array("缺少优惠券开始时间参数"),
                'echo'=>array("优惠券开始时间的数据类型不合法"),
                'match'=>array('/^[0-9]{4,}\-[0-9]{1,2}\-[0-9]{1,2}\s{1,}[0-9]{1,2}\:[0-9]{1,2}\:[0-9]{1,2}$/', "优惠券开始时间格式有误"),
            ),
        ),
    	'coupon_property' => array(
            //参数检测
            'args'=>array(
                'exist'=>array("缺少优惠券所属商品类型参数"),
                'echo'=>array("优惠券所属商品类型的数据类型不合法"),
                '!null'=>array("优惠券所属商品类型不能为空"),
            ),
        ),
    	'coupon_type' => array(
            //参数检测
            'args'=>array(
                'exist'=>array("缺少优惠券类型参数"),
                'echo'=>array("优惠券类型的数据类型不合法"),
                'method'=>array(array(parent::TABLE_COUPON, 'check_type'), "优惠券类型输入有误，不能被识别")       
            ),
        ),
        'coupon_module' => array(
            //参数检测
            'args'=>array(
                'exist'=>array("缺少优惠券模块名称参数"),
                'echo'=>array("优惠券模块名称的数据类型不合法"),
                'method'=>array(array(parent::TABLE_COUPON, 'check_module'), "优惠券模块名称输入有误，不能被识别")       
            ),
        ),
        'coupon_key' => array(
            'args'=>array(
                'echo'  => array('优惠券模块主键数据类型不合法'),
            ),
        ),
		
		'coupon_label' => array(
            'args'=>array(
                'echo'  => array('优惠券标签数据类型不合法'),
            ),
        ),
        
		'coupon_comment' => array(
            'args'=>array(
                'echo'  => array('优惠券注释信息数据类型不合法'),
            ),
        ),
		
		"coupon_state" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("优惠券状态的数据类型不合法"),
					'match'=>array('/^[01]{1}$/i', "优惠券状态必须是0或1"),
					),
		),
		
		"coupon_limit_min" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("优惠券最小限制的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/i', "优惠券最小限制必须是整数"),
					),
		),
		
		"coupon_limit_max" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("优惠券最大限制的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/i', "优惠券最大限制必须是整数"),
					),
		),
		
		"coupon_discount" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("优惠券折扣的数据类型不合法"),
					'match'=>array('/^[0-9]{0,}$/i', "优惠券折扣必须是整数"),
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
     * 获取模块
     * 
     * @param   void
     * @return  array
     */
    public function get_module(){
        return array(
            parent::MODULE_EXPRESS_COUPON => '快递优惠券模块',
            parent::MODULE_SHOP_ORDER => '购物商城',
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
     * 获取类型
     * 类型。0未知|1满减|2代金|3抵扣|4折扣
	 * 
     * @param   void
     * @return  array
     */
    public function get_type(){
        return array(
            1 => '满减',
            2 => '代金',
            3 => '抵扣',
            4 => '折扣',
        );
    }



    /**
     * 检测模块
     * 
     * @param   string  $data
     * @return  array
     */
    public function check_type($data){
        $module_list = $this->get_type();
        if( isset($module_list[$data]) ){
            return true;
        }else{
            return false;
        }
    }

    		
	/**
	 * 根据唯一标识，删除数据
	 * 
	 * @param	array	$coupon_id
	 * @return	array
	 */
	public function remove($coupon_id = ''){
		if( empty($coupon_id) ){
			return false;
		}
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('coupon')
		->where(array('coupon_id=[+]', (string)$coupon_id))
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
		->table('coupon')
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
     * 新增优惠券
     * Undocumented function
     * 
     * @param array $data   新增数据
     * @param array $call_data
     * @return void
     */
    public function insert($data = array(),$call_data = array())
    {
        if( empty($data) && empty($call_data) ){
			return false;
        }
        
        $bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('coupon')
		->call('data', $call_data)
		->insert($data);
		
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
    public function select_page($config = array())
    {
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
            ->table('coupon as c')
            ->call('where', $call_where)
            ->find('count(c.coupon_id) as count');
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
                $select = array('*');
            }
            
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('coupon as c')
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);
            
            return $data;
        });
    }



    /**
     * 查询优惠券
     * Undocumented function
     *
     * @param array $data
     * @param array $call_data
     * @return void
     */
    public function find_where($call_where=array(),$select=array())
    {
        if(empty($select)){
            $select = array(
                "*"
            );
        }
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where,$select), function($call_where,$select){
            $find_data = db(parent::DB_APPLICATION_ID)
                ->table('coupon')
                ->call('where', $call_where)
                ->find($select);
            return $find_data;
        });
    }


    /**
     * 获取一个数据
     * 
     * @param   string  $order_id
     * @return  array
     */
    public function find($coupon_id = ''){
        if( empty($coupon_id) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($coupon_id), function($coupon_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('coupon')
            ->where(array('coupon_id=[+]', (string)$coupon_id))
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
            $where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            return db(parent::DB_APPLICATION_ID)
                ->table('coupon')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select);
        });
    }


}