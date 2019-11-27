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



namespace eapie\source\table\house;
use eapie\main;
class house_product_top extends main {


    /**
     * 缓存的键列表
     * 
     * @var string
     */
    const CACHE_KEY = array(__CLASS__, 'house_product');


    /**
     * 数据检测
     * @var array
     */
    public $check = array(
		"house_product_top_sort" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("限时楼盘产品排序的数据类型不合法"),
					),
		),
	
	);


    /**
     * 获取一个id号
     * @return  string
     */
    public function get_unique_id()
    {
        return cmd(array(22), 'random autoincrement');
    }



				
		
	/**
	 * 获取一个数据
	 * 
	 * @param	string	$house_product_id
	 * @return	array
	 */
	public function find($house_product_id = ''){
		if( empty($house_product_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($house_product_id), function($house_product_id){
			return db(parent::DB_APPLICATION_ID)
			->table('house_product_top')
			->where(array('house_product_id=[+]', (string)$house_product_id))
			->find();
		});
		
	}	
	
	


    //===========================================
    // 操作
    //===========================================


    public function insert($data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('house_product_top')
            ->call('data', $call_data)
            ->insert($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    public function update($call_where = array(), $data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('house_product_top')
            ->call('where', $call_where)
            ->call('data', $call_data)
            ->update($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    //===========================================
    // 查询
    //===========================================


    /**
     * 查询置顶楼盘的数量
     * 
     * @return integer
     */
    public function get_count()
    {
        $row = db(parent::DB_APPLICATION_ID)
            ->table('house_product_top')
            ->where(array('house_product_top_end_time < [-]', time()))
            ->find('count(house_product_id) as count');

        return $row ? $row['count'] : 0;
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
			$house_product = array(
				'table' => 'house_product hp',
				'type' => 'left',
				'on' => 'hp.house_product_id = hpt.house_product_id'
			);
			
			//用户数据
            $user = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = hp.user_id'
            );
			
			//先获取总条数
			$find_data = db(parent::DB_APPLICATION_ID)
			->table('house_product_top hpt')
			->joinon($house_product, $user)
			->call('where', $call_where)
			->find('count(distinct hp.house_product_id) as count');
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
				$user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
				$select = array(
					'u.user_parent_id',
                    'u.user_nickname',
                    'u.user_logo_image_id',
                    '('.$user_phone_verify_list_sql.') as user_phone_verify_list',
					"hpt.*",
					"hp.*",
				);
			}
			
			$data['data'] =  db(parent::DB_APPLICATION_ID)
			->table('house_product_top hpt')
			->joinon($house_product, $user)
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
			->select($select);
			
			return $data;
		});
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
}