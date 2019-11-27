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



namespace eapie\source\table\user;

use eapie\main;

class user_coupon extends main
{

    /**
     * 缓存的键列表
     * @var string
     */
    const CACHE_KEY = array(__CLASS__);

    /**
     * 数据检测
     * Undocumented variable
     *
     * @var array
     */
    public $check = array(

        'agent_state'=>array(

            //参数检测
            'args'=>array(

            )
        )
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
     * 查一条记录，根据主键
     * @param  string $id 商家用户表ID
     * @return array
     */
    public function find($id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($id), function ($id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('user_coupon')
                ->where(array('user_coupon_id=[+]', $id))
                ->find();
        });
    }


    /**
     * 获取用户优惠券数据
     *  $config = array(
     *  'where' => array(), //条件
     *  'orderby' => array(), //排序
     *  'limit' => array(0, page_size), //取出条数，默认不限制
     * );
     * 
     * @param   array   $config
     * @return  array
     */
    public function select($config = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            $where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
            
            return db(parent::DB_APPLICATION_ID)
            ->table('user_coupon')
            ->call('where', $where)
            ->call('orderby', $orderby)
            ->call('limit', $limit)
            ->select($select);
        });
    } 




	/**
     * 获取用户优惠券数据
     *  $config = array(
     *  'where' => array(), //条件
     *  'orderby' => array(), //排序
     *  'limit' => array(0, page_size), //取出条数，默认不限制
     * );
     * 
     * @param   array   $config
     * @return  array
     */
    public function select_join($config = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            $where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
            
			//父级(关联优惠券)数据
	        $join_coupon = array(
	            'table' => 'coupon c',
	            'type' => 'INNER',
	            'on' => 'c.coupon_id = uc.coupon_id'
	        );
			
            return db(parent::DB_APPLICATION_ID)
            ->table('user_coupon as uc')
			->joinon($join_coupon)
            ->call('where', $where)
            ->call('orderby', $orderby)
            ->call('limit', $limit)
            ->select($select);
        });
    }


	/**
     * ------Mr.Zhao------2019.08.05------
     * 
     * 返回用户有效优惠券ID
     * 
     * @param   array      
     * @return  string
     */
    public function sql_effective_id($where = array()){
    	
        $call_where = $where;
        $where[] = array('user_coupon_state = 1 AND ( user_coupon_expire_time >=' . time() . ' OR user_coupon_expire_time=0 ) AND c.coupon_state = 1');
        
        $join_coupon = array(
            'table' => 'coupon c',
            'type' => 'INNER',
            'on' => 'c.coupon_id = uc.coupon_id'
        );

        return db(parent::DB_APPLICATION_ID)
        ->table('user_coupon as uc')
        ->joinon($join_coupon)
		->call('where', $call_where)
        ->select(array('uc.user_coupon_id'), function($q){
            return $q['query']['select'];
        });
        
    }





    /**
     * 获取用户优惠券分页数据
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
                'page_now' => 1,
                'data' => array()
            );
            
            //父级(关联优惠券)数据
            $parent_coupon = array(
                'table' => 'coupon c',
                'type' => 'left',
                'on' => 'c.coupon_id = uc.coupon_id'
            );
            
			
            //先获取总条数
            $find_data = db(parent::DB_APPLICATION_ID)
            ->table('user_coupon uc')
            ->joinon($parent_coupon)
            ->call('where', $call_where)
            ->find('count( distinct uc.user_coupon_id) as count');
            if( empty($find_data['count']) ){
                return $data;
                }else{
                    $data['row_count'] = $find_data['count'];
                    if( !empty($data['page_size']) ){
                        $data['page_count'] = ceil($data['row_count']/$data['page_size']);
                        $data['page_now'] = ceil($limit[0]/$data['page_size']) + 1;//当前页数
                    }
                }
            
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('user_coupon uc')
            ->joinon($parent_coupon)
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);
            
            return $data;
        });
        
    }



    /**
     * 根据条件获取优惠券领取数量
     */
    public function select_count($call_where=array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where), function($call_where){
              
            //先获取总条数
            $count = db(parent::DB_APPLICATION_ID)
            ->table('user_coupon ')
            ->call('where', $call_where)
            ->find('count( distinct user_coupon_id) as count');
            return $count;
        });
    }

    /**
     * 用户新增优惠券--抽奖或邀请
     * Undocumented function
     *
     * @param array $data
     * @param array $call_data
     * @return void
     */
    public function insert($data = array(),$call_data = array()){
        if(empty($data) && empty($call_data))
            return false;
            
        $bool = db(parent::DB_APPLICATION_ID)
        ->table('user_coupon')
        ->call('data', $call_data)
        ->insert($data);

        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);
        return $bool;
    }


    /**
     * 根据条件--匹配一张用户优惠券
     * Undocumented function
     *
     * @param array $where
     * @param array $select
     * @return void
     */
    public function find_where($where=[],$select=[])
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($where,$select), function ($where,$select) {
            $find_data = db(parent::DB_APPLICATION_ID)
                ->table('user_coupon')
                ->call('where', $where)
                ->find($select);
            return $find_data;
        });    
    }


    /**
     * 更新数据
     * 
     * @param   array $call_where   更新条件
     * @param   array $data         更新数据
     * @param   array $call_data
     * @return  bool
     */
    public function update($call_where = array(), $data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('user_coupon')
            ->call('where', $call_where)
            ->call('data', $call_data)
            ->update($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }

    /**
     * 根据条件--关联匹配一张用户优惠券
     * Undocumented function
     *
     * @param array $where
     * @param array $select
     * @return void
     */
    public function find_join($where=[],$select=[])
    {
        //父级(关联优惠券)数据
        $parent_coupon = array(
            'table' => 'coupon c',
            'type' => 'left',
            'on' => 'c.coupon_id = uc.coupon_id'
        );

        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($where,$parent_coupon,$select), function ($where,$parent_coupon,$select) {
            $find_data = db(parent::DB_APPLICATION_ID)
                ->table('user_coupon uc')
                ->joinon($parent_coupon)
                ->call('where', $where)
                ->find($select);
            return $find_data;
        });    
    }
	
	
	
	
	
	/**
	 * 快递邀请新用户--得优惠券
	 *
	 * @param  array $user [用户数据]
	 * @return void
	 */
	public function invitation_award($user){
		// 查询推荐人信息
        $user_parent = object(parent::TABLE_USER)->find($user['user_parent_id']);
        if (empty($user_parent))
            return false;

        //查询配置信息
        $data_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('invite_reward_user_coupon'), true);
        if (empty($data_config['state']) || $data_config['state'] != 1)
			return false;
		
		// 开启事务
        db(parent::DB_APPLICATION_ID)->query('START TRANSACTION');	

        $time = time();	
        // 90天后的时间戳
        $expire_time = strtotime("+90 days");
        // 三个月后的时间戳
        // $expire_time = strtotime("+3 month");

		$where = array(
			array('coupon_id = 1')
		);

		$coupon = object(parent::TABLE_COUPON)->find_where($where);
		if(!$coupon){
			$coupon = array(
				'coupon_id'=>1,
				'coupon_name'=>'邀请奖励',
				'coupon_module'=>'express_coupon',
				'coupon_type'=>4,
				'coupon_label'=>'满3kg可用',
				'coupon_discount'=>65,
				'coupon_state'=>1,
				'coupon_insert_time'=>$time,
				'coupon_update_time'=>$time
			);
			$bool = object(parent::TABLE_COUPON)->insert($coupon);
		}else{
			$bool = true;
		}

		if(empty($coupon) || !$bool){
			// 回滚
			db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
			return false;
		}	

		//用户奖励
		$insert_data = array(
			'user_coupon_id'=>object(parent::TABLE_USER_COUPON)->get_unique_id(),
			'user_id'=>$user['user_id'],
			'coupon_id'=>$coupon['coupon_id'],
			'user_coupon_json'=>$coupon,
			'user_coupon_state'=>1,
			'user_coupon_number'=>1,
			'user_coupon_insert_time'=>$time,
			'user_coupon_update_time'=>$time,
			'user_coupon_expire_time'=>$expire_time,
		);

		//邀请人奖励
		$invite_insert_data = array(
			'user_coupon_id'=>object(parent::TABLE_USER_COUPON)->get_unique_id(),
			'user_id'=>$user_parent['user_id'],
			'coupon_id'=>$coupon['coupon_id'],
			'user_coupon_json'=>$coupon,
			'user_coupon_state'=>1,
			'user_coupon_number'=>1,
			'user_coupon_insert_time'=>$time,
			'user_coupon_update_time'=>$time,
			'user_coupon_expire_time'=>$expire_time,
		);
		
		if(!object(parent::TABLE_USER_COUPON)->insert($insert_data) || !object(parent::TABLE_USER_COUPON)->insert($invite_insert_data)){
			// 回滚
			db(parent::DB_APPLICATION_ID)->query('ROLLBACK');
			return false;
		}

		// 提交事务
		db(parent::DB_APPLICATION_ID)->query('COMMIT');
		return true;
	}
	
	
    

    /**
	 * E麦商城，注册赠送优惠券
     * 
     * ------Mr.Zhao------2019.07.06------
	 *
	 * @param  array $user [用户数据]
	 * @return bool
	 */
    public function register_coupon($user)
    {

        // 查询用户信息
        $user_data = object(parent::TABLE_USER)->find($user['user_id']);
        if (empty($user_data))
            return false;

        return $this->_give_coupon($user,'register_coupon');

    }


    /**
     * ------Mr.Zhao------2019.07.08------
     * E麦商城，购买会员产品赠送优惠券
     * @param   array   ['user_id'=>'']
     */
    public function member_goods_coupon($user)
    {
        return $this->_give_coupon($user, 'member_goods_coupon');
    }
    

    /**
     * ------Mr.Zhao------2019.07.08------
     * 
     * 赠送优惠券---批量插入
     * @param   array   用户信息
     * @param   string  事件名称（配置信息的key）
     * @return  bool
     */
    private function _give_coupon ($user,$event_name){

        //查询配置信息
        $data_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('emshop_config'), true);
        if (empty($data_config[$event_name]['state']) || $data_config[$event_name]['state'] != 1)
            return false;

        $time = time();

        $keyword = $data_config[$event_name]['keyword'];

        $coupon_config = array(
            'where' => array(
                array('coupon_label like "[-]%"', $keyword)
            )
        );

        // 注册要送的优惠券
        $coupon = object(parent::TABLE_COUPON)->select($coupon_config);
        if (empty($coupon)) {
            return false;
        }

        $insert_datas = array();
        foreach ($coupon as $key => $value) {

            // $number = (int) substr(strstr(strstr($value['coupon_label'], ']', 1), '['), 1);

            //用户优惠券
            $insert_data = array(
                'user_coupon_id' => object(parent::TABLE_USER_COUPON)->get_unique_id(),
                'user_id' => $user['user_id'],
                'coupon_id' => $value['coupon_id'],
                'user_coupon_json' => cmd(array($value), 'json encode'),
                'user_coupon_state' => 1,
                'user_coupon_number' => 1,//$number,
                'user_coupon_insert_time' => $time,
                'user_coupon_update_time' => $time,
            );
            $insert_datas[] = $insert_data;
        }

        // 批量插入数据
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('user_coupon')
            ->batch()
            ->insert($insert_datas);

        return $bool;
    }

    /**
     * ------Mr.CHEN------2019.08.26------
     * 优利小程序，注册赠送优惠券
     * @param   array   ['user_id'=>'']
     */
    public function sign_up_send_coupon($user=array(),$lable='sign_up_coupon'){
        return $this->_send_sign_up_coupon($user, $lable);
    }

    /**
     * ------Mr.CHEN------2019.08.26------
     * 
     * 赠送优惠券---批量插入
     * @param   array   用户信息
     * @param   string  事件名称（配置信息的key）
     * @return  bool
     */
    private function _send_sign_up_coupon ($user,$event_name){

        //查询配置信息
        $data_config = object(parent::TABLE_CONFIG)->data(object(parent::TABLE_CONFIG)->find('youli_config'), true);
        if (empty($data_config[$event_name]['state']) || $data_config[$event_name]['state'] != 1)
            return false;

        $time = time();

        $keyword = (string)$data_config[$event_name]['keyword'];

        $coupon_config = array(
            'where' => array(
                array('coupon_label =[+]', $keyword)
            )
        );

        // 注册要送的优惠券
        $coupon = object(parent::TABLE_COUPON)->select($coupon_config);
        if (empty($coupon)) {
            return false;
        }

        $insert_datas = array();
        foreach ($coupon as $key => $value) {

            // $number = (int) substr(strstr(strstr($value['coupon_label'], ']', 1), '['), 1);

            //用户优惠券
            $insert_data = array(
                'user_coupon_id' => object(parent::TABLE_USER_COUPON)->get_unique_id(),
                'user_id' => $user['user_id'],
                'coupon_id' => $value['coupon_id'],
                'user_coupon_json' => cmd(array($value), 'json encode'),
                'user_coupon_state' => 1,
                'user_coupon_number' => 1,//$number,
                'user_coupon_expire_time' => $value['coupon_end_time'],
                'user_coupon_insert_time' => $time,
                'user_coupon_update_time' => $time,
            );
            $insert_datas[] = $insert_data;
        }

        // 批量插入数据
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('user_coupon')
            ->batch()
            ->insert($insert_datas);

        return $bool;
    }



	/**
     * 获取用户当前商品可用优惠券数据
     * 
     * ------Mr.Zhao------2019.07.11------
     * 
     * @param   string   $user_id
     * @param   int      $money
     * @param   int      $credit
     * @return  array
     */
    public function select_available_join_coupon($req = array())
    {
        $where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
        $call_select = isset($req['select']) && is_array($req['select']) ? $req['select'] : array();
        $call_orderby = isset($req['orderby']) && is_array($req['orderby']) ? $req['orderby'] : array();

        // 字段过滤
        $user_id = cmd(array($req['user_id']), 'str addslashes');

        $credit_where = array();
        $money_where = array();

        if (!$where) {
            $where = array(
                array('uc.user_id = [+]', $user_id),
                array('uc.user_coupon_state = 1'),
            );
        }
        if (isset($req['credit'])) {
            // 字段过滤
            $credit = cmd(array($req['credit']), 'str addslashes');
            $credit_where[] = array("[or] (user_coupon_state = 1 AND c.coupon_state = 1 AND user_id = '" . $user_id . "' AND c.coupon_property = 1 AND (c.coupon_limit_min = 0 OR c.coupon_limit_min <=" . $credit . ")" . " AND (c.coupon_limit_max = 0 OR c.coupon_limit_max >= " . $credit . ") AND ( user_coupon_expire_time >=" . time() . " OR user_coupon_expire_time=0 ))");
        }

        if (isset($req['money'])) {
            // 字段过滤
            $money = cmd(array($req['money']), 'str addslashes');
            $credit_where[] = array("[or] (user_coupon_state = 1 AND c.coupon_state = 1 AND user_id = '" . $user_id . "' AND c.coupon_property = 0 AND (c.coupon_limit_min = 0 OR c.coupon_limit_min <=" . $money . ")" . " AND (c.coupon_limit_max = 0 OR c.coupon_limit_max >= " . $money . ") AND ( user_coupon_expire_time >=" . time() . " OR user_coupon_expire_time=0 ))");
        }

        if (!isset($req['credit']) && !isset($req['money'])) {
            $credit_where[] = array('user_id =[+]', $user_id);
            $money_where[] = array('user_id =[+]', $user_id);
        }

        //父级(关联优惠券)数据
        $join_coupon = array(
            'table' => 'coupon c',
            'type' => 'INNER',
            'on' => 'c.coupon_id = uc.coupon_id'
        );

        return db(parent::DB_APPLICATION_ID)
            ->table('user_coupon as uc')
            ->joinon($join_coupon)
            ->call('where', $where)
            ->call('where', $credit_where)
            ->call('where', $money_where)
            ->call('orderby', $call_orderby)
            ->select($call_select);
        // ->select($call_select,function($info){
        //     printexit($info['query']['sql']);});
    }
}