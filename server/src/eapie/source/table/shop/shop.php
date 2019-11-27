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
class shop extends main{


    /**
     * 缓存的键列表
     * @var string
     */
    const CACHE_KEY = array(__CLASS__);


    /**
     * 数据检测
     * @var array
     */
    public $check = array(
        'shop_id' => array(
            'args' => array(
                'exist'=> array('缺少店铺ID参数'),
                'echo' => array('店铺ID数据类型不合法'),
                '!null'=> array('店铺ID不能为空'),
            ),
        ),
    );


    //===========================================
    // 操作数据
    //===========================================




    //===========================================
    // 查询数据
    //===========================================


    /**
     * 查一条记录，根据主键
     * @param  string $id 商家用户表ID
     * @return array
     */
    public function find($id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($id), function ($id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('shop')
                ->where(array('shop_id=[+]', $id))
                ->find();
        });
    }


    /**
     * 根据条件查询一条数据
     * @param array $call_where 查询条件
     * @return array
     */
    public function find_where($call_where = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where), function ($call_where) {
            return db(parent::DB_APPLICATION_ID)
                ->table('shop')
                ->call('where',$call_where)
                ->find();
        });
    }



    //===========================================
    // 检测数据
    //===========================================


    /**
     * 检测店铺状态是否正常
     * 
     * @param  string $shop_id 店铺ID
     * @return bool
     */
    public function check_state($shop_id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_id), function ($shop_id) {
            $row = db(parent::DB_APPLICATION_ID)
                ->table('shop')
                ->where(array('shop_state=1'))
                ->where(array('shop_on_off=1'))
                ->where(array('shop_id=[+]', $shop_id))
                ->find('shop_id');

            return boolval($row);
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
                ->table('shop')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select);
        });
    }



    /**
     * 店铺列表、分页
     */
    public function select_page($config)
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
				'page_now' => 0,
				'data' => array()
			);
			
			
			//先获取总条数
			$find_data = db(parent::DB_APPLICATION_ID)
			->table('shop s')
			->call('where', $call_where)
			->find('count(distinct s.shop_id) as count');
			if( empty($find_data['count']) ){
				return $data;
            }else{
                $data['row_count'] = $find_data['count'];
                if( !empty($data['page_size']) ){
                    $data['page_count'] = ceil($data['row_count']/$data['page_size']);
                    $data['page_now'] = ceil($limit[0]/$data['page_size']) + 1;//当前页数
                }
            }

            $merchant_join = array(
                'table' => 'merchant m',
                'type' => 'left',
                'on' => 'm.merchant_id = s.merchant_id'
            );
                    
            $user_join = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = s.user_id'
            );
			
			$data['data'] =  db(parent::DB_APPLICATION_ID)
			->table('shop s')
			->joinon($merchant_join, $user_join)
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
			->select($select);				
				
			return $data;
		});
    }




    /**
     * 获取多个店铺的name
     * @param   array   
     * @return  array   
     */
    public function get_name($data = array())
    {
        $shop_ids = array();
        foreach ($data as $v) {
            if (!in_array($v['shop_id'], $shop_ids) && $v['shop_id']) {
                $shop_ids[] = $v['shop_id'];
            }
        }

        if(empty($shop_ids)){
            return $data;
        }

        // printexit($shop_ids);

        //获取分类数据
        $in_string = "\"" . implode("\",\"", $shop_ids) . "\"";
        $config = array(
            'where' => array(),
            'select' => array(
                'distinct( shop_id ) as shop_id',
                'shop_name',
            )
        );

        $config['where'][] = array("[and] shop_id IN([-])", $in_string, true); //是不加单引号并且强制不过滤

        $shop_data = object(parent::TABLE_SHOP)->select($config);

        foreach ($data as &$v) {
            if (empty($shop_data)) {
                break;
            }
            foreach ($shop_data as $v1) {
                if ($v1['shop_id'] == $v['shop_id']) {
                    $v['shop_name'] = $v1['shop_name'];
                    break;
                }
            }
        }
        return $data;
    }





}