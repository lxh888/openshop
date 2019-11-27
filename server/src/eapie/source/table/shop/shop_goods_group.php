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

/**
 * 拼团商品
 * @author green
 */
class shop_goods_group extends main
{

    /**
     * 缓存的键列表
     * 
     * @var string
     */
    const CACHE_KEY = array(__CLASS__, 'shop_goods', 'shop_order_group');

    /**
     * 数据检测
     * 
     * @var array
     */
    public $check = array(
        'shop_goods_group_id' => array(
            'args' => array(
                'exist'=> array('缺少拼团商品ID参数'),
                'echo' => array('拼团商品ID不合法'),
                '!null'=> array('拼团商品ID不能为空'),
            ),
        ),
        'shop_goods_group_name' => array(
            'args'=>array(
                'echo'=>array('名称不合法'),
            ),
        ),
        'shop_goods_group_info' => array(
            'args'=>array(
                'echo'=>array('简介不合法'),
            ),
        ),
        'shop_goods_group_sort' => array(
            'args'=>array(
                'echo'=>array('排序不合法'),
            ),
        ),
        'shop_goods_group_price' => array(
            'args'=>array(
                'exist'=> array('缺少拼团价格参数'),
                'echo' => array('拼团价格不合法'),
                '!null'=> array('拼团价格不能为空'),
            ),
        ),
        'shop_goods_group_people' => array(
            'args'=>array(
                'exist'=> array('缺少拼团人数参数'),
                'echo'=>array('拼团人数不合法'),
                '!null'=> array('拼团人数不能为空'),
            ),
        ),
        'shop_goods_group_start_time' => array(
            'args'=>array(
                'exist'=>array('缺少开始时间参数'),
                'echo'=>array('开始时间的数据类型不合法'),
                'match'=>array('/^[0-9]{4,}\-[0-9]{1,2}\-[0-9]{1,2}\s{1,}[0-9]{1,2}\:[0-9]{1,2}\:[0-9]{1,2}$/', '开始时间格式有误'),
            ),
        ),
        'shop_goods_group_end_time' => array(
            'args'=>array(
                'exist'=>array('缺少结束时间参数'),
                'echo'=>array('结束时间的数据类型不合法'),
                'match'=>array('/^[0-9]{4,}\-[0-9]{1,2}\-[0-9]{1,2}\s{1,}[0-9]{1,2}\:[0-9]{1,2}\:[0-9]{1,2}$/', '结束时间格式有误'),
            ),
        ),
    );

    /**
     * 获取主键ID
     * @return string
     */
    public function get_unique_id()
    {
        return cmd(array(22), 'random autoincrement');
    }


    //===========================================
    // 操作数据
    //===========================================


    public function insert($data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_group')
            ->call('data', $call_data)
            ->insert($data);

        // 清理缓存
        if ($bool) {
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }

        return $bool;
    }

    public function update($where = array(), $data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_group')
            ->call('where', $where)
            ->call('data', $call_data)
            ->update($data);

        // 清理缓存
        if ($bool) {
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
        return $bool;
    }

    public function remove($shop_goods_group_id = '')
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_group')
            ->where(array('shop_goods_group_id = [+]', $shop_goods_group_id))
            ->delete();

        // 清理缓存
        if ($bool) {
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }

        return $bool;
    }

    /**
     * 更新状态
     * @author green
     * 
     * @param  void
     * @return void
     */
    public function update_state()
    {
        $time = time();
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('shop_goods_group')
            ->data(array(
                'shop_goods_group_state = [-]',
                "(CASE WHEN shop_goods_group_start_time > {$time} THEN 2 WHEN shop_goods_group_end_time < {$time} THEN 0 ELSE 1 END
                )",
                true
            ))
            ->where(array('shop_goods_group_state <> 0'))
            ->all()
            ->update();

        // 清理缓存
        if( $bool ){
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
		
		//更新团购订单的状态
		//始终在更新状态时执行
		object(parent::TABLE_SHOP_ORDER_GROUP)->update_state();
    }


    //===========================================
    // 查询数据
    //===========================================


    public function find($shop_goods_group_id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($shop_goods_group_id), function($shop_goods_group_id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('shop_goods_group')
                ->where(array('shop_goods_group_id = [+]', $shop_goods_group_id))
                ->find();
        });
    }

    public function find_where($call_where = array(), $find = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where, $find), function($call_where, $find) {
            return db(parent::DB_APPLICATION_ID)
                ->table('shop_goods_group')
                ->call('where', $call_where)
                ->find($find);
        });
    }

    public function select($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config) {
            $where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            return db(parent::DB_APPLICATION_ID)
                ->table('shop_goods_group')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select);
        });
    }       

    public function select_page($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config) {
            $call_where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $call_limit = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            $limit = array(
                (isset($call_limit[0])? $call_limit[0] : 0),
                (isset($call_limit[1])? $call_limit[1] : 0)
            );

            // 设置返回的数据
            $data = array(
                'row_count' => 0,
                'limit_count' => $limit[0] + $limit[1],
                'page_size' => $limit[1],
                'page_count' => 0,
                'page_now' => 0,
                'data' => array()
            );

            // 商品数据
            $shop_goods = array(
                'table' => 'shop_goods sg',
                'type' => 'left',
                'on' => 'sg.shop_goods_id = sgw.shop_goods_id'
            );

            // 查询总条数
            $find_data = db(parent::DB_APPLICATION_ID)
                ->table('shop_goods_group sgw')
                ->joinon($shop_goods)
                ->call('where', $call_where)
                ->find('count(distinct sg.shop_goods_id) as count');

            if (empty($find_data['count'])) {
                return $data;
            } else {
                $data['row_count'] = $find_data['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count']/$data['page_size']);
                    $data['page_now'] = ceil($limit[0]/$data['page_size']) + 1;
                }
            }

            if (empty($select)) {
                $select = array(
                    "sgw.*",
                    "sg.*",
                );
            }

            $data['data'] =  db(parent::DB_APPLICATION_ID)
                ->table('shop_goods_group sgw')
                ->joinon($shop_goods)
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $call_limit)
                ->select($select);

            return $data;
        });
    }

    /**
     * 分页查询
     * @author green
     *
     * @param  array  $config [查询配置]
     * @return array
     */
    public function select_paginate($config = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config) {
            // 查询配置
            $select = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();
            $call_joinon = isset($config['joinon']) && is_array($config['joinon']) ? $config['joinon'] : array();
            $call_where = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $limit = array(
                (isset($config['limit'][0]) ? $config['limit'][0] : 0),
                (isset($config['limit'][1]) ? $config['limit'][1] : 0),
            );

            // 返回数据
            $data = array(
                'row_count' => 0,
                'limit_count' => $limit[0] + $limit[1],
                'page_size' => $limit[1],
                'page_count' => 0,
                'page_now' => 0,
                'data' => array()
            );

            // 查询数据条数
            $total_count = db(parent::DB_APPLICATION_ID)
                ->table('shop_goods_group sgg')
                ->call('joinon', $call_joinon)
                ->call('where', $call_where)
                ->find('count(*) as count');

            // 计算分页信息
            if (empty($total_count['count'])) {
                return $data;
            } else {
                $data['row_count'] = $total_count['count'];
                if ($data['page_size']) {
                    $data['page_count'] = ceil($data['row_count'] / $data['page_size']);
                    $data['page_now'] = ceil($limit[0] / $data['page_size']) + 1;
                }
            }

            // 查询数据
            $data['data'] = db(parent::DB_APPLICATION_ID)
                ->table('shop_goods_group sgg')
                ->call('joinon', $call_joinon)
                ->call('where', $call_where)
                ->call('orderby', $call_orderby)
                ->call('limit', $limit)
                ->select($select);

            return $data;
        });
    }


    /**
     * 根据商品ID查询
     * @author green
     *
     * @param  string $shop_goods_id [商品主键ID]
     * @return array
     */
    public function find_by_shopgoodsid($shop_goods_id = '')
    {
        return $this->find_where(array(array('shop_goods_id = [+]', $shop_goods_id)));
    }


    //===========================================
    // 检查
    //===========================================


    /**
     * 检查拼团商品状态
     * @author green
     *
     * @param  array    $data   [拼团数据]
     * @param  integer  $state  [要检查的状态]
     * @return integet          [拼团状态]
     */
    public function check_state($data = array())
    {
        $time = time();
        $update_where = array(array('shop_goods_id = [+]', $data['shop_goods_id']));

        // 已结束
        if ($data['shop_goods_group_end_time'] < $time) {
            if ($data['shop_goods_group_state'] !== '0') {
                $this->update($update_where, array('shop_goods_group_state' => 0, 'shop_goods_group_update_time' => $time));
            }

            return 0;
        }

        // 未开始
        if ($data['shop_goods_group_start_time'] > $time) {
            if ($data['shop_goods_group_state'] !== '2') {
                $this->update($update_where, array('shop_goods_group_state' => 2, 'shop_goods_group_update_time' => $time));
            }

            return 2;
        }

        // 进行中
        if ($data['shop_goods_group_state'] !== '1') {
            $this->update($update_where, array('shop_goods_group_state' => 1, 'shop_goods_group_update_time' => $time));
        }

        return 1;
    }


    //===========================================
    // SQL语句
    //===========================================





    //===========================================
    // 析构方法
    //===========================================


    /**
     * 拼团成功
     * @author green
     *
     * @param  string $shop_goods_group_id [拼团商品主键ID]
     * @return void
     */
    public function destruct_team_success($shop_goods_group_id = '')
    {
        // 获取数据库信息
        $info = db(parent::DB_APPLICATION_ID)->info();
        $db_config = $info['config'];
        $db_application_id = parent::DB_APPLICATION_ID;

        // 注册析构方法
        destruct('shop_goods_group>destruct_team_success', true, function($db_config, $db_application_id) use($shop_goods_group_id) {

        });
    }

    /**
     * 拼团失败
     * @author green
     *
     * @param  string $shop_goods_group_id [拼团商品主键ID]
     * @return void
     */
    public function destruct_team_fail($shop_goods_group_id = '')
    {
        // 获取数据库信息
        $info = db(parent::DB_APPLICATION_ID)->info();
        $db_config = $info['config'];
        $db_application_id = parent::DB_APPLICATION_ID;

        // 注册析构方法
        destruct('shop_goods_group>destruct_team_fail', true, function($db_config, $db_application_id) use($shop_goods_group_id) {
            // 连接数据库
            db($db_application_id, true, $db_config);

            // 查询拼团订单
            $shop_order_group = object(parent::TABLE_SHOP_ORDER_GROUP)->select();
        });
    }



	/**
     * 返回一个团购商品状态数据
     * 
     * @param   string      $alias	别名称
     * @return  string
     */
    public function sql_join_goods_state($alias = ""){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        
        return db(parent::DB_APPLICATION_ID)
        ->table('shop_goods_group join_sgg')
        ->where(array('join_sgg.shop_goods_group_id = '.$alias.'shop_goods_group_id'))
        ->select(array("join_sgg.shop_goods_group_state"), function($q){
            return $q['query']['select'];
        });
		
    }
    
	
	   	 
		 
		 
		        
    /**
     * 返回一个团购商品状态数据
     * 
     * @param   string      $alias	别名称
     * @return  string
     */
    public function sql_join_from_goods_state($alias = ""){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        
        $select = db(parent::DB_APPLICATION_ID)
        ->table('shop_goods_group join_sgg')
        ->where(array('join_sgg.shop_goods_group_id = '.$alias.'shop_goods_group_id'))
        ->select(array("join_sgg.shop_goods_group_state"), function($q){
            return $q['query']['select'];
        });
		
		//MySql 要多包一层
		return db(parent::DB_APPLICATION_ID)
        ->from('('.$select.') as temp_sgg')
		->select(array("*"), function($q){
            return $q['query']['select'];
        });
		
    }
    
	
	





}