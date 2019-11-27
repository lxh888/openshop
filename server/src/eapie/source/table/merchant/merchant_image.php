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



namespace eapie\source\table\merchant;
use eapie\main;
class merchant_image extends main {

    /**
     * 缓存的键列表
     * 
     * @var string
     */
    const CACHE_KEY = array(__CLASS__, 'merchant', 'image');


    /**
     * 数据检测
     * 
     * @var array
     */
    public $check = array(
        'merchant_image_id' => array(
            'args'=>array(
                'exist'=>array('缺少商家图片ID参数'),
                'echo'=>array('商家图片ID数据类型不合法'),
                '!null'=>array('商家图片ID不能为空'),
            ),
            'exists'=>array(
                'method'=>array(array(parent::TABLE_MERCHANT_IMAGE, 'find'), '商家图片ID有误，数据不存在') 
            ),
        )
    );


    /**
     * 获取一个id号
     * 
     * @param   void
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
     * 插入新数据
     * 
     * @param   array       $data           数据
     * @param   array       $call_data      数据
     * @return  bool
     */
    public function insert($data = array(), $call_data = array())
    {
        if (empty($data) && empty($call_data))
            return false;

        $bool = db(parent::DB_APPLICATION_ID)
            ->table('merchant_image')
            ->call('data', $call_data)
            ->insert($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }  


    /**
     * 根据唯一标识，删除数据
     * 
     * @param   array   $merchant_image_id
     * @return  array
     */
    public function remove($merchant_image_id = ''){
        $bool = db(parent::DB_APPLICATION_ID)
        ->table('merchant_image')
        ->where(array('merchant_image_id=[+]', $merchant_image_id))
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
     * 获取一个数据
     * 
     * @param   string  $id
     * @return  array
     */
    public function find($id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($id), function($id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('merchant_image')
                ->where(array('merchant_image_id=[+]', $id))
                ->find();
        });
    }   


    /**
     * 获取多条数据
     *  $config = array(
     *  'where' => array(), //条件
     *  'orderby' => array(), //排序
     *  'limit' => array(0, page_size), //取出条数，默认不限制
     *  'select' => array(),//查询的字段，可以是数组和字符串
     * );
     * 
     * @param   array   $config
     * @return  array
     */
    public function select($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config) {
            $where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            return db(parent::DB_APPLICATION_ID)
                ->table('merchant_image')
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
				'page_now' => 0,
				'data' => array()
			);
			
			//商家数据
			$merchant = array(
				'table' => 'merchant m',
				'type' => 'left',
				'on' => 'm.merchant_id = mi.merchant_id'
			);
			
			//用户数据
			$user = array(
				'table' => 'user u',
				'type' => 'left',
				'on' => 'u.user_id = mi.user_id'
			);
			
			//图片数据
			$image = array(
				'table' => 'image i',
				'type' => 'left',
				'on' => 'i.image_id = mi.image_id'
			);	
			
			
			//先获取总条数
			$find_data = db(parent::DB_APPLICATION_ID)
			->table('merchant_image mi')
			->joinon($merchant, $user, $image)
			->call('where', $call_where)
			->find('count(distinct mi.merchant_image_id) as count');
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
					"m.merchant_name",
					"mi.*",
					"i.*"
				);
			}
			
			$data['data'] =  db(parent::DB_APPLICATION_ID)
			->table('merchant_image mi')
			->joinon($merchant, $user, $image)
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
			->select($select);
				
			return $data;
		});
    }


}