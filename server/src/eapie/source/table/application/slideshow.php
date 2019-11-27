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
class slideshow extends main {
	
	
	/*轮播图表*/
	
		
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
        'slideshow_id' => array(
            'args'=>array(
                'exist' => array('缺少轮播图ID参数'),
                'echo'  => array('轮播图ID类型不合法'),
                '!null' => array('轮播图ID不能为空')
            ),
            'exists_id' => array(
                'method'=> array(array(parent::TABLE_SLIDESHOW, 'find_exists_id'), '商家ID有误，商家不存在') 
            )
        ),
        "slideshow_module" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少轮播图模块名称参数"),
                    'echo'=>array("轮播图模块名称的数据类型不合法"),
                    '!null'=>array("轮播图模块名称不能为空"),
                    'method'=>array(array(parent::TABLE_SLIDESHOW, 'check_module'), "轮播图模块名称输入有误，不能被识别")       
                ),
        ),
        'slideshow_name' => array(
            'args'=>array(
                'echo'  => array('轮播图标题数据类型不合法'),
            ),
            'length' => array('<length'=>array(200, '轮播图标题字符长度太多'))    
        ),
        
		'slideshow_info' => array(
            'args'=>array(
                'echo'  => array('轮播图简介数据类型不合法'),
            ),
        ),
        'slideshow_label' => array(
            'args'=>array(
                'echo'  => array('轮播图标签的数据类型不合法'),
            ),
        ),
        
        'slideshow_comment' => array(
            'args'=>array(
                'echo'  => array('轮播图注释信息数据类型不合法'),
            ),
        ),
		
		
        'slideshow_json' => array(
            'args'=>array(
                'method'  => array(array(parent::TABLE_SLIDESHOW, 'check_json'), '轮播图的JSON参数格式输入有误') ,
            ),
        ),
		"slideshow_sort" => array(
			//参数检测
			'args'=>array(
				'echo' => array("轮播图排序的数据类型不合法"),
				'match' => array('/^[0-9]{0,}$/i', "轮播图排序必须是整数"),
				),
		),
        'slideshow_state' => array(
            'args'=>array(
                'match'=>array('/^[01]$/', '轮播图状态值必须是0或1'),
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
     * 获取分类模块
     * 
     * @param   void
     * @return  array
     */
    public function get_module(){
        return array(
            parent::MODULE_SHOP_GOODS_TYPE => "商城商品分类",
            parent::MODULE_HOME => "首页",
        );
    }
    
	
		
    /**
     * 检测支付方式
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
     * 根据ID，判断数据是否存在
	 * 
     * @param  string	 $slideshow_id		轮播图ID
     * @return bool
     */
    public function find_exists_id($slideshow_id = '') {
    	if( empty($slideshow_id) ){
    		return false;
    	}
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($slideshow_id), function ($slideshow_id) {
                return (bool)db(parent::DB_APPLICATION_ID)
                    ->table('slideshow')
                    ->where(array('slideshow_id=[+]', $slideshow_id))
                    ->find('slideshow_id');
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
		->table('slideshow')
		->call('data', $call_data)
		->insert($data);
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}	
	
	
	
		
				
	/**
	 * 根据ID，删除数据
	 * 
	 * @param	array	$slideshow_id
	 * @return	array
	 */
	public function remove($slideshow_id = ''){
		if( empty($slideshow_id) ){
			return false;
		}
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('slideshow')
		->where(array('slideshow_id=[+]', (string)$slideshow_id))
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
		->table('slideshow')
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
	 * 获取一个数据——根据类别ID
	 * 
	 * @param	string	$slideshow_id
	 * @return	array
	 */
	public function find($slideshow_id = ''){
		if( empty($slideshow_id) ){
			return false;
		}
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($slideshow_id), function($slideshow_id){
			return db(parent::DB_APPLICATION_ID)
			->table('slideshow')
			->where(array('slideshow_id=[+]', $slideshow_id))
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
			->table('slideshow')
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
		});
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
     * 查——分页数据
	 * 
     * @param	array	$config		配置参数
     * @return	array
     */
    public function select_page($config = array()){
    	return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
    		//查询配置
	        $call_where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
	        $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
	        $call_limit   = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
	        $select       = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();
	        $limit = array(
	            (isset($call_limit[0]) ? $call_limit[0] : 0),
	            (isset($call_limit[1]) ? $call_limit[1] : 0)
	        );
	
	        //设置返回的数据
	        $data = array(
	            'row_count'   => 0,
	            'limit_count' => $limit[0] + $limit[1],
	            'page_size'   => $limit[1],
	            'page_count'  => 0,
	            'page_now'    => 0,
	            'data'        => array()
	        );
			
			
			//图片数据
            $image = array(
                'table' => 'image i',
                'type' => 'LEFT',
                'on' => 'i.image_id = s.image_id'
            );
			
			
	        //获取总条数
	        $total_count = db(parent::DB_APPLICATION_ID)
            ->table('slideshow s')
			->joinon($image)
            ->call('where', $call_where)
            ->find('count(*) as count');
	        if (empty($total_count['count'])) {
	            return $data;
	        }else{
	            $data['row_count'] = $total_count['count'];
	            if(!empty($data['page_size'])) {
	                $data['page_count'] = ceil($data['row_count']/$data['page_size']);
	                $data['page_now'] = ceil($limit[0]/$data['page_size']) + 1;//当前页数
	            }
	        }
	
			
			if( empty($select) ){
				$select = array(
					"s.*",
					"i.*"
				);
			}
			
	        //查询数据
	        $rows = db(parent::DB_APPLICATION_ID)
            ->table('slideshow s')
			->joinon($image)
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);
	
	        $data['data'] = $rows;
	
	        return $data;
    	});
		
    }

  
	
	
	
	
	
	
}
?>