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

class house_product_image extends main
{
    

    /**
     * 缓存的键列表
     * 
     * @var string
     */
    const CACHE_KEY = array(
        __CLASS__, 
        "house_product"
    );

    /**
     * 数据检测
     * @var array
     */
    public $check = array(
        'house_product_image_type' => array(
            'args' => array(
                'exist'=> array('缺少图片类型参数'),
                'echo' => array('图片类型数据类型不合法'),
                '!null'=> array('图片类型不能为空'),
                'method'=>array(array(parent::TABLE_HOUSE_PRODUCT_IMAGE, 'check_image_type'), '图片类型不合法') 
            )
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


    //===========================================
    // 操作数据
    //===========================================


    /**
     * 插入数据
     * 
     * @param   array $data      数据
     * @param   array $call_data 数据
     * @return  bool
     */
    public function insert($data = array(), $call_data = array())
    {
        if (empty($data) && empty($call_data))
            return false;

        $bool = db(parent::DB_APPLICATION_ID)
            ->table('house_product_image')
            ->call('data', $call_data)
            ->insert($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    /**
     * 删除数据，根据主键ID
     * 
     * @param   array   $house_product_image_id
     * @return  array
     */
    public function remove($house_product_image_id = '')
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('house_product_image')
            ->where(array('house_product_image_id=[+]', $house_product_image_id))
            ->delete();

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }



    //===========================================
    // 查询数据
    //===========================================


    /**
     * 获取多条数据
     * @param   array   $config
     * @return  array
     */
    public function select($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config) {
            $where  = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $orderby= isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $limit  = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            return db(parent::DB_APPLICATION_ID)
                ->table('house_product_image')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select);
        });
    }


    /**
     * 查一条记录，根据条件
     * @param  array  $call_where 查询条件
     * @return array
     */
    public function find_where($call_where = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where), function ($call_where) {
            return db(parent::DB_APPLICATION_ID)
                ->table('house_product_image')
                ->call('where', $call_where)
                ->find();
        });
    }



    /**
     * 检测图片类型
     * @param  string $val 值
     * @return bool
     */
    public function check_image_type($val = '')
    {
        $config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('house_product_image_type'), true);
        return is_string($val) && array_key_exists($val, $config);
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
			$house_product = array(
				'table' => 'house_product hp',
				'type' => 'left',
				'on' => 'hp.house_product_id = hpi.house_product_id'
			);
			
			//用户数据
			$user = array(
				'table' => 'user u',
				'type' => 'left',
				'on' => 'u.user_id = hpi.user_id'
			);	
				
			//图片数据
			$image = array(
				'table' => 'image i',
				'type' => 'left',
				'on' => 'i.image_id = hpi.image_id'
			);	
				
				
			if( empty($select) ){
				$select = array(
					'u.user_parent_id',
                    'u.user_nickname',
                    'u.user_logo_image_id',
					"hp.house_product_name",
					"hpi.*",
					"i.*"
				);
			}
				
			return db(parent::DB_APPLICATION_ID)
			->table('house_product_image hpi')
			->joinon($house_product, $user, $image)
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
		});
	}		
	
	







    /**
     * 子查询——获取楼盘项目室外图图片ID，一个
     * 
     * @param   string      $alias  别名称
     * @return  string
     */
    public function sql_join_product_main_id($alias = '')
    {
        if (is_string($alias) && $alias != '')
            $alias .= '.';

        //图片数据
        $image = array(
            'table' => 'image i',
            'type' => 'INNER',
            'on' => 'i.image_id = hpi.image_id'
        );
        
        return db(parent::DB_APPLICATION_ID)
            ->table('house_product_image hpi')
            ->joinon($image)
            ->where(array('hpi.house_product_id = '.$alias.'house_product_id'), array('hpi.house_product_image_type = "outside"'))
            ->orderby(array('i.image_sort'), array('hpi.house_product_image_time'), array('hpi.house_product_image_id'))
            ->find(array('hpi.image_id'), function($q){
                return $q['query']['find'];
            });
    }


}