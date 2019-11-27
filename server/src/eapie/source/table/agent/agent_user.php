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



namespace eapie\source\table\agent;
use eapie\main;
class agent_user extends main {
 	
	
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
        'agent_user_id' => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少代理用户ID参数"),
                    'echo'=>array("代理用户ID数据类型不合法"),
                    '!null'=>array("代理用户ID不能为空"),
                    ),
            
        ),
        'agent_user_interview_phone' => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少面试的联系方式的参数"),
                    '!null'=>array("面试的联系方式的参数不能为空"),
                    'echo'=>array("面试的联系方式的数据类型不合法"),
                    ),
        ),
        "agent_user_interview_time" => array(
			//参数检测
			'args'=>array(
					'exist'=>array("缺少面试时间参数"),
					'echo'=>array("面试时间的数据类型不合法"),
					'match'=>array('/^[0-9]{4,}\-[0-9]{1,2}\-[0-9]{1,2}\s{1,}[0-9]{1,2}\:[0-9]{1,2}\:[0-9]{1,2}$/', "面试时间格式有误"),
					),
		),
		"agent_user_interview_address" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("面试地址的数据类型不合法"),
					),
		),
		
		"agent_user_fail" => array(
			//参数检测
			'args'=>array(
					'echo'=>array("审核失败信息的数据类型不合法"),
					),
		),
        "agent_user_state" => array(
			//审核状态。0未通过审核，1通过审核，2待审核
			'args'=>array(
					'echo'=>array("代理用户状态的数据类型不合法"),
					'match'=>array('/^[012]{1}$/i', "代理用户状态必须是0、1、2"),
					),
		),
        'agent_user_json' => array(
            'args'=>array(
                'method'  => array(array(parent::TABLE_AGENT_USER, 'check_json'), '代理用户的JSON配置格式输入有误') ,
            ),
        ),
        
		"user_credit_award[ratio]" => array(
			//参数检测
            'args'=>array(
            		'exist'=>array("缺少积分赠送比例的参数"),
                    'echo'=>array("积分赠送比例的格式输入有误"),
                    'match'=>array('/^(0\.[0-9]{1,}|0|1)$/', "积分赠送比例的格式输入有误。注意必须是小于1的小数或者为0、1"),
                    ),
		),
		"user_credit_award[algorithm]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("缺少积分赠送运算法则参数"),
                    'echo'=>array("积分赠送运算法则的数据类型不合法"),
                    'match'=>array('/^(round|ceil|floor)$/', "积分赠送运算法则异常"),
                    ),
        ),
		"user_credit_award[state]" => array(
            //参数检测
            'args'=>array(
            		'exist'=>array("积分赠送状态值参数"),
                    'echo'=>array("积分赠送状态值的数据类型不合法"),
                    'match'=>array('/^[01]$/', '积分赠送状态值必须是0或1'),
                    ),
        ),
        "agent_user_phone" => array(
            //参数检测
            'args'=>array(
                'exsit'=>array('请输入手机号'),
                'echo'=>array('请核对手机号'),
                'match'=>array('/^[12][345789]\d{9}$/','手机号格式错误')
            ),
        ),
		"agent_user_name" => array(
            //参数检测
            'args'=>array(
                'exsit'=>array('请输入姓名'),
                'echo'=>array('请核对姓名'),
                // 'match'=>array('/^[12][345789]\d{9}$/','姓名错误')
            ),
        ),
        "agent_user_card" => array(
            //参数检测
            'args'=>array(
                'exsit'=>array('请输入身份证号'),
                'echo'=>array('请核对身份证号'),
                // 'match'=>array('/^[12][345789]\d{9}$/','身份证号格式错误')
            ),
        ),
        "agent_user_province" => array(
            //参数检测
            'args'=>array(
                'exsit'=>array('请选择省份'),
                'echo'=>array('请核对省份'),
                // 'match'=>array('/^[12][345789]\d{9}$/','省份错误')
            ),
        ),
        "agent_user_city" => array(
            //参数检测
            'args'=>array(
                'exsit'=>array('请选择城市'),
                'echo'=>array('请核对城市'),
                // 'match'=>array('/^[12][345789]\d{9}$/','城市错误')
            ),
        ),
		"agent_user_district" => array(
            //参数检测
            'args'=>array(
                'exsit'=>array('请选择地区'),
                'echo'=>array('请核对地区'),
                // 'match'=>array('/^[12][345789]\d{9}$/','地区错误')
            ),
        ),
    );
	
	
	
	
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
     * 获取一个id号
     * @return  string
     */
    public function get_unique_id($num=0)
    {
        if($num>0){
            return cmd(array($num), 'random autoincrement');
        }
        return cmd(array(16), 'random autoincrement');
        
    }


    /**
     * 
     * 根据条件，修改某条数据
     * Undocumented function
     * 
     * @return void
     */
    public function update($where=array(), $data=array(), $call_data = array())
    {
        if(empty($where))
            return false;
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('agent_user')
            ->call('where', $where)
            ->call('data', $call_data)
            ->update($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    /**
     * 插入新数据
     * 
     * @param   array       $data           数据
     * @param   array       $call_data      数据
     * @return  bool
     */
    public function insert($data = array(), $call_data = array()){
        if( empty($data) && empty($call_data) ){
            return false;
        }
        
        $bool = (bool)db(parent::DB_APPLICATION_ID)
        ->table('agent_user')
        ->call('data', $call_data)
        ->insert($data);
        
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
        return $bool;
    } 

    /**
	 * 根据唯一标识，删除数据
	 * 
	 * @param	array	$agent_user_id
	 * @return	array
	 */
    public function remove($agent_user_id = '')
    {
		if( empty($agent_user_id) ){
			return false;
		}
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('agent_user')
		->where(array('agent_user_id=[+]', (string)$agent_user_id))
		->delete();
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}

    /**
     * 获取用户代理资料
     * 
     * @param   array   $agent_user_id
     * @return  array
     */
    public function find($agent_user_id = '')
    {
        if( empty($agent_user_id) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($agent_user_id), function($agent_user_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('agent_user')
            ->where(array('agent_user_id=[+]', (string)$agent_user_id))
            ->find();
        });
    }



	/**
     * 判断代理地区是否存在数据
     * 
     * @param   array   $agent_region_id
     * @return  array
     */
	public function find_exists_region( $agent_region_id = '' ){
		if( empty($agent_region_id) ){
            return false;
        }
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($agent_region_id), function($agent_region_id){
            return (bool)db(parent::DB_APPLICATION_ID)
            ->table('agent_user')
            ->where(array('agent_region_id=[+]', (string)$agent_region_id))
            ->find('user_id');
        });
    }
    
    /**
     * 获取代理地区一条有效数据
     * 
     * @param   array   $agent_region_id
     * @return  array
     */
	public function find_agent_region_user( $agent_region_id = '' ){
		if( empty($agent_region_id) ){
            return false;
        }
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($agent_region_id), function($agent_region_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('agent_user')
            ->where(array('agent_region_id=[+]', (string)$agent_region_id))
			->where(array('agent_user_state=1'))
            ->find();
        });
	}



	/**
     * 判断代理地区是否存在有效的数据
     * 
     * @param   array   $agent_region_id
     * @return  array
     */
	public function find_exists_validity_region( $agent_region_id = '' ){
		if( empty($agent_region_id) ){
            return false;
        }
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($agent_region_id), function($agent_region_id){
            return (bool)db(parent::DB_APPLICATION_ID)
            ->table('agent_user')
            ->where(array('agent_region_id=[+]', (string)$agent_region_id))
			->where(array('agent_user_state=1'))
            ->find('user_id');
        });
	}




	/**
     * 获取用户代理资料
     * 
     * @param   array   $agent_user_id
     * @return  array
     */
	public function find_join($agent_user_id = '', $find = array()){
		if( empty($agent_user_id) ){
            return false;
        }
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($agent_user_id, $find), function($agent_user_id, $find){
			//用户数据
            $user = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = au.user_id'
            );
			
			if( empty($find) ){
				$user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
                $find = array(
                    'u.user_parent_id',
                    'u.user_nickname',
                    'u.user_logo_image_id',
                    '('.$user_phone_verify_list_sql.') as user_phone_verify_list',
                    'au.*',
                );
            }
			
            return db(parent::DB_APPLICATION_ID)
            ->table('agent_user as au')
			->joinon($user)
            ->where(array('au.agent_user_id=[+]', (string)$agent_user_id))
            ->find($find);
        });
		
	}




	
	/**
	 * 根据 用户id和地址id 查询数据
	 * 
	 * @param	string		$user_id
	 * @param	string		$agent_region_id
	 */
	public function find_region($user_id, $agent_region_id){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id, $agent_region_id), function($user_id, $agent_region_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('agent_user')
            ->where(
            	array('user_id=[+]', $user_id),
            	array('[and] agent_region_id=[+]', $agent_region_id)
			)
            ->find();
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
                ->table('agent_user')
                ->call('where', $call_where)
                ->find();
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
            
            //用户数据
            $user = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = au.user_id'
            );
			
			//代理地区
            $agent_region = array(
                'table' => 'agent_region ar',
                'type' => 'left',
                'on' => 'ar.agent_region_id = au.agent_region_id'
            );
			
            //先获取总条数
            $find_data = db(parent::DB_APPLICATION_ID)
            ->table('agent_user au')
            ->joinon($user, $agent_region)
            ->call('where', $call_where)
            ->find('count(distinct au.agent_user_id) as count');
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
                    'au.*',
                    'ar.agent_region_scope',
                    'ar.agent_region_province',
                    'ar.agent_region_city',
                    'ar.agent_region_district',
                    'ar.agent_region_details',
                    'ar.agent_region_state',
                );
            }
            
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('agent_user au')
            ->joinon($user, $agent_region)
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);
            
            return $data;
        });
    }





			
	        
    /**
     * 返回代理地区的 用户个数
     * 
     * @param   string      $alias	别名称
     * @return  string
     */
    public function sql_join_region_count($alias = ""){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        
        return db(parent::DB_APPLICATION_ID)
        ->table('agent_user temp_au')
        ->where( array('temp_au.agent_region_id = '.$alias.'agent_region_id') )
        ->select(array('count(distinct temp_au.user_id) as agent_user_count'), function($q){
            return $q['query']['select'];
        });
    }
    


    /**
     * ------Mr.Zhao------2019.06.29------
     * 根据多个agent_region_id获取多个有效的代理用户数据
     * 
     * @param   array   代理地区ID
     * @return  array   有效的代理用户
     */
    public function select_by_agent_region_ids($agent_region_ids = array())
    {
        $where = array();

        $in_string = "\"" . implode("\",\"", $agent_region_ids) . "\"";

        $where[] = array("[and] agent_region_id IN([-])", $in_string, true); //是不加单引号并且强制不过滤
        $where[] = array("[and] agent_user_state=1"); 

        $data = db(parent::DB_APPLICATION_ID)
            ->table('agent_user')
            ->call('where', $where)
            ->select();

        // 排除无效代理用户（不参与赠送积分的）
        foreach ($data as $key => $value) {
            if( !empty($value['agent_user_json']) ){
                $data[$key]['agent_user_json'] = cmd(array($value['agent_user_json']), "json decode");
            }
            if( !is_array($data[$key]['agent_user_json']) || empty($data[$key]['agent_user_json']['user_credit_award']['state']) ){
                unset($data[$key]);
            }
        }

        return $data;
    }

    

    /**
     * ------2019.06.27------Mr.Zhaojian------
     * 代理用户积分奖励
     * 
     * @param   array   $event_json
     * @param   array   $error
     * @return  array   [状态，操作记录]
     * 
     */
    public function event_agent_user_credit_award($event_json,&$error)
    {

        // 代理区域
        $province = isset($event_json['agent_region_province']) ? $event_json['agent_region_province'] : "";
        $city = isset($event_json['agent_region_city']) ? $event_json['agent_region_city'] : "";
        $district = isset($event_json['agent_region_district']) ? $event_json['agent_region_district'] : "";

        $error_prefix = '[user_credit_plus]';

        $agent_region = object(parent::TABLE_AGENT_REGION)->select_scope_province_city_district($province, $city, $district);
        if(empty($agent_region)){
            $error[] = $error_prefix."没有代理区域";
            return array('status'=>false);
        }

        // 查询要奖励积分的代理用户
        $agent_region_ids = array();
        foreach ($agent_region as $key => $value) {
            if (!in_array($value['agent_region_id'], $agent_region_ids)) { }
            $agent_region_ids[] = $value['agent_region_id'];
        }
        $agent_user = object(parent::TABLE_AGENT_USER)->select_by_agent_region_ids($agent_region_ids);

        // 判断代理用户是否为空
        if (empty($agent_user)) {
            $error[] = $error_prefix."没有区域代理用户或者代理用户未开启积分赠送";
            return array('status'=>false);
        }


        // 三种不同的取整方式
        $_compute = array(
            'ceil' => function ($num) {
                return ceil($num);
            },
            'floor' => function ($num) {
                return floor($num);
            },
            'round' => function ($num) {
                return round($num);
            }
        );

        // 遍历代理用户赠送积分
        $error_prefix = '[user_credit_plus]';
        $info = array();

        db(parent::DB_APPLICATION_ID)->query("START TRANSACTION");//开启事务

        foreach ($agent_user as $k => $vals) {

            // 代理用户积分奖励比例
            $ratio = $vals['agent_user_json']['user_credit_award']['ratio'];

            // 代理用户积分奖励保留整数的方式
            $algorithm = $vals['agent_user_json']['user_credit_award']['algorithm'];

            // 计算奖励积分
            $user_credit_plus = $event_json['user_credit'] * $ratio;
            $user_credit_plus = $_compute[$algorithm]($user_credit_plus);

            // 收款账号ID
            $order_plus_account_id = $vals['user_id'];

            // 判断积分数量是否小于0
            if ($user_credit_plus < 1) {
                $error[] = $error_prefix."代理用户积分奖励,user_id为“".$order_plus_account_id."”奖励积分小于1";
                continue;
            }

            // 备注信息
            $order_comment = '代理用户奖励积分，用户奖励比值[' . ($ratio * 100) . '%]';

            // 插入数据
            $bool = object(parent::TABLE_USER_CREDIT)->event_agent_user_credit_award(
                $order_plus_account_id,
                $event_json['user_id'],
                $user_credit_plus,
                $order_comment,
                $vals['agent_user_json'],
                $error_prefix,
                $error
            );

            if (!empty($bool)) {
                $info[] = $order_plus_account_id;
            }

            if (!$bool) {
                $error[] = $error_prefix . "操作失败";
                db(parent::DB_APPLICATION_ID)->query("ROLLBACK"); //回滚事务
                return array('status'=>false);
            }

        }

        db(parent::DB_APPLICATION_ID)->query("COMMIT");//提交事务
        return array('status'=>true,'info'=>$info);

    }

    /**
     * 查询代理用户，根据地区
     * @Author green
     *
     * @param  string $province [省]
     * @param  string $city     [市]
     * @param  string $district [区]
     * @return array
     */
    public function select_agent_by_region($province = '', $city = '', $district = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($province, $city, $district), function($province, $city, $district) {
            // 查询代理用户数据
            $data = db(parent::DB_APPLICATION_ID)
                ->table('agent_user au')
                ->joinon(array(
                    'table' => 'agent_region ar',
                    'type' => 'INNER',
                    'on' => 'au.agent_region_id = ar.agent_region_id',
                ))
                ->where(
                    array('agent_user_state = 1'),
                    array('agent_user_award_state = 1'),
                    array('agent_region_state = 1')
                )
                ->where(
                    array('[and] agent_region_province = [+]', $province),
                    array('agent_region_city = ""'),
                    array('agent_region_district = ""')
                )
                ->where(
                    array('[or] agent_region_province = [+]', $province),
                    array('agent_region_city = [+]', $city),
                    array('agent_region_district = ""')
                )
                ->where(
                    array('[or] agent_region_province = [+]', $province),
                    array('agent_region_city = [+]', $city),
                    array('agent_region_district = [+]', $district)
                )
                ->select(array(
                    'user_id',
                    'agent_user_json',
                    'agent_region_scope AS scope',
                    'agent_region_province AS province',
                    'agent_region_city AS city',
                    'agent_region_district AS district',
                ));

            return $data;
        });
    }
    /**
	 * 
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
    public function select($config = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            $where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
            
            return db(parent::DB_APPLICATION_ID)
            ->table('agent_region')
            ->call('where', $where)
            ->call('orderby', $orderby)
            ->call('limit', $limit)
            ->select($select);
        });
    }  

    /**
	 * 
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
    public function select_by_user_id($config = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            $where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
            
            return db(parent::DB_APPLICATION_ID)
            ->table('agent_user')
            ->call('where', $where)
            ->call('orderby', $orderby)
            ->call('limit', $limit)
            ->select($select);
        });
    }  

    /**
     * 查询代理用户--所代理的地区
     */
    public function select_where($where=array()){
        if( empty($where) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($where), function($where){
            return db(parent::DB_APPLICATION_ID)
            ->table('agent_user')
            ->call('where',$where)
            ->select();
        });
    }

    /**
     * 获取当月未分红的代理用户
     *
     */
    public function select_not_dividend($pay_time = 123,$start = 0){
        $sql_order_agent =  object(parent::TABLE_ORDER)->sql_join_order_agent_money($pay_time, 'au');
        $join = array(
            'table' => 'agent_region ar',
            'type' => 'left',
            'on' => 'ar.agent_region_id = au.agent_region_id'
        );
        return db(parent::DB_APPLICATION_ID)
            ->table('agent_user as au')
            ->where(
                array('au.agent_user_state = 1'),
                array("au.user_id NOT IN ([-]) ", $sql_order_agent, true)
            )
            ->joinon($join)
            ->select(/*function($p){
                printexit($p['query']);
            }*/);
    }
}