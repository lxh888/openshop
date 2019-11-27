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
class user extends main 
{
    
    
    /*用户表*/
    
    /**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, "user_phone");
    
    
    
    /**
     * 数据检测
     * 
     * @var array
     */
    public $check = array(
        'user_id' => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少用户ID参数"),
                    'echo'=>array("用户ID数据类型不合法"),
                    '!null'=>array("用户ID不能为空"),
                    ),
            //检查编号是否存在      
            'exists_id'=>array(
                    'method'=>array(array(parent::TABLE_USER, 'find_exists_id'), "用户ID有误，用户不存在") 
            ),
            
        ),
        
        'user_parent_id' => array(
            //参数检测
            'format'=>array(
                    'echo'=>array("推荐人ID的数据类型不合法"),
                    ),
            //检查编号是否存在      
            'exists_id'=>array(
                    'method'=>array(array(parent::TABLE_USER, 'find_exists_id'), "推荐人ID有误，该推荐人数据不存在")       
                ),
        ),
        
        
        'user_parent' => array(
            //参数检测
            'format'=>array(
                    'echo'=>array("推荐人的数据类型不合法"),
                    ),
        ),
        
        'user' => array(
            //参数检测
            'format'=>array(
                    'echo'=>array("用户ID或者手机号的数据类型不合法"),
                    ),
        ),
        
        
        "user_password" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少用户密码参数"),
                    'echo'=>array("用户密码数据类型不合法"),
                    '!null'=>array("用户密码不能为空"),
                    ),
        ),
        
        
        "user_former_password" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少用户旧密码参数"),
                    'echo'=>array("用户旧密码数据类型不合法"),
                    '!null'=>array("用户旧密码不能为空"),
                    ),
                    
        ),
        
        
        
        "user_confirm_password" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少用户确认密码参数"),
                    'echo'=>array("用户确认密码数据类型不合法"),
                    '!null'=>array("用户确认密码不能为空"),
                    ),
        ),
        
        'user_name' => array(
            'args' => array(
                'echo' => array('姓名的数据类型不合法'),
                '<length' => array(200, '姓名的字符长度太多')
            ),   
        ),
        'user_nickname' => array(
            'args' => array(
                'echo' => array('昵称的数据类型不合法'),
                '<length' => array(200, '昵称的字符长度太多')
            ),   
        ),
        'user_compellation' => array(
            'args' => array(
                'echo' => array('姓名的数据类型不合法'),
                '<length' => array(200, '姓名的字符长度太多')
            ),   
        ),
        'user_wechat' => array(
            'args' => array(
                'echo' => array('微信账号的数据类型不合法'),
                '<length' => array(200, '微信账号的字符长度太多')
            ),   
        ),
        'user_qq' => array(
            'args' => array(
                'echo' => array('QQ账号的数据类型不合法'),
                '<length' => array(200, 'QQ账号的字符长度太多')
            ),   
        ),
        'user_email' => array(
            'args' => array(
                'echo' => array('邮箱账号的数据类型不合法'),
                '<length' => array(200, '邮箱账号的字符长度太多')
            ),   
        ),
        'user_company' => array(
            'args' => array(
                'echo' => array('公司名称的数据类型不合法'),
                '<length' => array(200, '公司名称的字符长度太多')
            ),   
        ),

        "user_sex" => array(
            //参数检测
            'args'=>array(
                    'echo'=>array("用户性别的数据类型不合法"),
                    'match'=>array('/^[012]{1}$/i', "用户性别必须是0、1、2"),
                    ),
        ),
        
        
        "user_state" => array(
            //参数检测
            'args'=>array(
                    'echo'=>array("用户状态的数据类型不合法"),
                    'match'=>array('/^[01]{1}$/i', "用户状态必须是0或1"),
                    ),
        ),
        
        'user_logo_image_id' => array(
            'args'=>array(
                'exist' => array('缺少用户头像图片ID参数'),
                'echo'  => array('用户头像图片ID数据类型不合法'),
                '!null' => array('用户头像图片ID不能为空')
            )
        ),
        'pay_password' => array(
            'args'=>array(
                'exist' => array('缺少支付密码参数'),
                'echo'  => array('支付密码数据类型不合法'),
                '!null' => array('支付密码不能为空'),
                'match' => array('/^\d{6}$/', '支付密码不合法'),
            )
        ),
    );
    
    
    
    
    /**
     * 获取一个id号
     * 
     * @param   void
     * @return  string
     */
    public function get_unique_id(){
        return cmd(array(10), 'random autoincrement');
    }   
    
    
    
    
    
        
    /**
     * 更新数据
     * 
     * @param   array       $where
     * @param   array       $data
     * @param   array       $call_data
     * @return  bool
     */
    public function update($where = array(), $data = array(), $call_data = array())
    {
        if( empty($where) || (empty($data) && empty($call_data)) ){
            return false;
        }
        
        $bool = (bool)db(parent::DB_APPLICATION_ID)
        ->table('user')
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
     * 更新某一条数据
     * 
     * @param   array   	$user_id
     * @param   array   	$data
     * @param   array       $call_data
     * @return  bool
     */
    public function update_user($user_id = "", $data = array(), $call_data = array()){
        if( empty($user_id) || (empty($data) && empty($call_data)) ){
            return false;
        }
        
        $bool = (bool)db(parent::DB_APPLICATION_ID)
        ->table('user')
        ->where(array('user_id=[+]', (string)$user_id))
        ->call('data', $call_data)
        ->update($data);
        
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
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
        ->table('user')
        ->call('data', $call_data)
        ->insert($data);
        
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
        return $bool;
    }   
    
    
    
    
    
        
    /**
     * 删除数据
     * 
     * @param   array   $where
     * @return  array
     */
    public function delete($where = array()){
        if( empty($where) ){
            return false;
        }
        
        $bool = (bool)db(parent::DB_APPLICATION_ID)
        ->table('user')
        ->call('where', $where)
        ->delete();
        
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
        return $bool;
    }
        
        
        
        
        
        
            
    /**
     * 根据唯一标识，删除数据
     * 
     * @param   array   $user_id
     * @return  array
     */
    public function remove($user_id = ''){
        if( empty($user_id) ){
            return false;
        }
        
        $bool = (bool)db(parent::DB_APPLICATION_ID)
        ->table('user')
        ->where(array('user_id=[+]', (string)$user_id))
        ->delete();
        
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
        return $bool;
    }
    
        
            
    
            
            
    /**
     * 根据用户ID，判断用户是否存在
     * 
     * @param   string      $user_id
     */
    public function find_exists_id($user_id){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id), function($user_id){
            return (bool)db(parent::DB_APPLICATION_ID)
            ->table('user')
            ->where(array('user_id=[+]', $user_id))
            ->find('user_id');
        });
    }
    
        


    /**
     * 根据用户ID或者认证手机号，获取用户ID和手机号
     * 
     * @param   string      $user_id_or_phone
     * @return  array
     */
    public function find_id_or_phone($user_id_or_phone = ''){
        if( empty($user_id_or_phone) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id_or_phone), function($user_id_or_phone){
            //用户手机
            $user_phone = array(
                'table' => 'user_phone up',
                'type' => 'INNER',
                'on' => 'up.user_id = u.user_id AND up.user_phone_state=1'
            );
            
            return db(parent::DB_APPLICATION_ID)
            ->table('user u')
            ->joinon($user_phone)
            ->where(array('u.user_id=[+]', (string)$user_id_or_phone), array('[or] up.user_phone_id=[+]', (string)$user_id_or_phone))
            ->find(array("u.user_id", "up.user_phone_id as user_phone"));
        });
    }


    
    /**
     * 获取一个用户子级条数
     * 如果父级数据不存在，则返回空。意思是父级必须是要存在
     * 
     * @param   array   $user_id
     * @return  array
     */
    public function find_son_count($user_id = ''){
        if( empty($user_id) ){
            return false;
        }
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id), function($user_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('user')
            ->where(array("user_parent_id=[+]", $user_id))
            ->find('count(distinct user_id) as count');
        });
    }
    
    
    
	
	
	/**
     * 获取一个用户数据，包括父级的数据
     * 
     * @param   array   $user_id
     * @return  array
     */
    public function find_join_parent($user_id = '', $find = array()){
        if( empty($user_id) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id, $find), function($user_id, $find){
            //父级数据
            $parent_user = array(
                'table' => 'user parent_u',
                'type' => 'INNER',
                'on' => 'parent_u.user_id = u.user_parent_id'
            );
            
            if( empty($find) ){
                $find = array(
                    "u.user_id as user_id",
                    "u.user_nickname as user_nickname",
                    "parent_u.user_id as user_parent_id",
                    "parent_u.user_nickname as user_parent_nickname",
                );
            }
            
            return db(parent::DB_APPLICATION_ID)
            ->table('user u')
            ->joinon($parent_user)
            ->where(array('u.user_id=[+]', (string)$user_id))
            ->find($find);
        });
    }
	
	
	public function find_son_num($parent_user_id){
        if( empty($parent_user_id) ){
            return false;
        }

        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($parent_user_id), function($parent_user_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('user')
            ->where(array('user_parent_id=[+]', (string)$parent_user_id))
            ->find('count(user_id) as count');
        });
    }
	
	
    
    /**
     * 获取一个用户数据，包括父级的数据
     * 如果父级数据不存在，则返回空。意思是父级必须是要存在
     * 
     * @param   array   $user_id
     * @return  array
     */
    public function find_self_parent($user_id = '', $find = array()){
        if( empty($user_id) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id, $find), function($user_id, $find){
            
            //用户手机
            $parent_user_phone = array(
                'table' => 'user_phone parent_up',
                'type' => 'INNER',
                'on' => 'parent_up.user_id = parent_u.user_id AND parent_up.user_phone_type=1 AND parent_up.user_phone_state=1'
            );
            
            //父级数据
            $parent_user = array(
                'table' => 'user parent_u',
                'type' => 'INNER',
                'on' => 'parent_u.user_id = u.user_parent_id'
            );
            
            
            if( empty($find) ){
                $find = array(
                    "u.*",
                    "parent_u.user_nickname as user_parent_nickname",
                    "parent_u.user_compellation as user_parent_compellation",
                    "parent_up.user_phone_id as user_parent_phone",
                );
            }
            
            return db(parent::DB_APPLICATION_ID)
            ->table('user u')
            ->groupby('u.user_id')
            ->joinon($parent_user, $parent_user_phone)
            ->where(array('u.user_id=[+]', (string)$user_id))
            ->find($find);
        });
    }
    
    
    /**
     * 获取一个用户数据
     * 
     * @param   array   $user_id
     * @return  array
     */
    public function find_nickname($user_id = '')
    {
        if( empty($user_id) ){
            return false;
        }
        
        $data =  object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id), function($user_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('user')
            ->where(array('user_id=[+]', (string)$user_id))
            ->find('user_nickname');
        });
        if(isset($data['user_nickname']))
            return $data['user_nickname'];
        return null;
    }
    
    /**
     * 获取一个用户数据
     * 
     * @param   array   $user_id
     * @return  array
     */
    public function find($user_id = '')
    {
        if( empty($user_id) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id), function($user_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('user')
            ->where(array('user_id=[+]', (string)$user_id))
            ->find();
        });
    }
    
    /**
	 * 直接查询查询推荐人ID
	 * @param string $user_id [用户ID]
	 * @return string $parent_user_id [推荐人ID]
	 * @return boolean  true [无上级为空结束查询]
	 */
	public function find_recommend_user_id($user_id) {
		$result = db(parent::DB_APPLICATION_ID)
			->table('user')
			->where(array('user_id=[+]', (string)$user_id))
			->find();
		if (empty($result['user_parent_id']) || $result['user_parent_id'] === '') {
			return false;
		} else{
			return $result['user_parent_id'];
		}
	}
        
    /**
     * 获取一个用户数据
     * 
     * @param   array   $user_id
     * @return  array
     */
    public function find_join($user_id = ''){
        if( empty($user_id) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id), function($user_id){
        	
			//图片数据
            $logo_image = array(
                'table' => 'image logo_i',
                'type' => 'LEFT',
                'on' => 'logo_i.image_id = u.user_logo_image_id'
            );
			
			
			if( empty($find) ){
				$find = array(
					"u.*",
					"logo_i.image_width as user_logo_image_width",
					"logo_i.image_height as user_logo_image_height",
				);
			}
			
            return db(parent::DB_APPLICATION_ID)
            ->table('user u')
			->joinon($logo_image)
            ->where(array('u.user_id=[+]', (string)$user_id))
            ->find($find);
        });
    }
    
    
    /**
     * 根据用户名称，获取一个用户数据
     * 
     * @param   string   $user_nickname
     * @return  array
     */
    public function find_like_nickname($user_nickname = ''){
        if( empty($user_nickname) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_nickname), function($user_nickname){
            return db(parent::DB_APPLICATION_ID)
            ->table('user')
            ->where(array('[and] user_nickname LIKE "%[-]%"', (string)$user_nickname))
            ->find();
        });
    }
	
    
    /**
     * 获取一个用户密码
     * 
     * @param   array   $user_id
     * @return  array
     */
    public function find_password($user_id = ''){
        if( empty($user_id) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id), function($user_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('user')
            ->where(array('user_id=[+]', (string)$user_id))
            ->find("user_left_password", "user_right_password");
        });
    }
    
    
    
        
    /**
     * 用户数据初始化，如果用户数据存在，比如$_SESSION['用户表']存在，那么不执行
     * 
     * @param   void
     * @return  array   返回查询后的用户数据
     */
    public function find_user(){
        
        if( empty($_SESSION['user_id']) || isset($_SESSION['user']) ){
            //删除 用户记录表 的ID。用户未登录时，清理用户记录表 编号
            if( !isset($_SESSION['user']) && isset($_SESSION['session_private']['user_log_id'])){
                unset($_SESSION['session_private']['user_log_id']);
            }
            
            return false;
        }
        
        //获取日志、用户数据，如果没有数据则不合法，清理用户id
        if( !empty($_SESSION['user_id']) ){
            $user_log_data = object(parent::TABLE_USER_LOG)->find_now_data();
        }
        
        //判断日志
        if( empty($user_log_data['user_log_id']) ){
            $_SESSION['user_id'] = '';
        }else{
            
            //这里肯定存在登录日志，退出日志操作。注意，这里不摧毁记录，因为有可能用户再使用中被停用的情况，而不是登录时
            if( empty($user_log_data['user_id']) || empty($user_log_data['user_state']) ){
                object(parent::TABLE_USER_LOG)->update_log_id_out($user_log_data['user_log_id']);
                //如果用户数据不存在，或者用户状态为封禁，那么清空用户id
                $_SESSION['user_id'] = '';
            }else{
                $_SESSION['session_private']['user_log_id'] = $user_log_data['user_log_id'];
            }
            
        }
        
        
        //合并用户数据
        if( !empty($_SESSION['user_id']) ){
            $_SESSION['user'] = $user_log_data;
            
            //处理用户的配置信息
            if( !empty($_SESSION['user']['user_json']) ){
                $_SESSION['user']['user_json'] = cmd(array($_SESSION['user']['user_json']), 'json decode');
            }
            
        }
        
        //更新用户退出时间
        object(parent::TABLE_USER_LOG)->destruct_out_time();
        
        //可能需要对用户数据进一步判断，所以返回用户数据
        return $user_log_data;
    }
    
    
    
    
    
    
    
    
    /**
     * 获取多个用户数据
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
            ->table('user')
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
                'page_now' => 1,
                'data' => array()
            );
            
            //父级数据
            $parent_user = array(
                'table' => 'user parent_u',
                'type' => 'left',
                'on' => 'parent_u.user_id = u.user_parent_id'
            );
			
            //先获取总条数
            $find_data = db(parent::DB_APPLICATION_ID)
            ->table('user u')
            ->joinon($parent_user)
            ->call('where', $call_where)
            ->find('count(distinct u.user_id) as count');
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
                $user_phone_verify_count_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_count("u");
                $user_phone_login_count_sql = object(parent::TABLE_USER_PHONE)->sql_join_login_count("u");
				$user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
				$user_parent_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u", "user_parent_id");
				$user_recommend_count_sql = $this->sql_join_son_count("u");
				
                $select = array(
                    'u.*',
                    '('.$user_phone_verify_count_sql.') as user_phone_verify_count',
                    '('.$user_phone_login_count_sql.') as user_phone_login_count',
                    '('.$user_phone_verify_list_sql.') as user_phone_verify_list',
                    '('.$user_recommend_count_sql.') as user_recommend_count',
                    '('.$user_parent_phone_verify_list_sql.') as user_parent_phone_verify_list',
                    'parent_u.user_logo_image_id as user_parent_logo_image_id',
                    'parent_u.user_nickname as user_parent_nickname',
                    'parent_u.user_compellation as user_parent_compellation',
                    'parent_u.user_state as user_parent_state',
                );
            }
            
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('user u')
            ->joinon($parent_user)
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);
            
            return $data;
        });
        
    }
    
    
    /**
	 * 易淘商城专用 
	 * 获取所有的分页数据
	 *
	 * @param   array   $config     配置信息
     * @return  array
	 */
    public function yitaoshop_select_page($config = array()){
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
            
            //父级数据
            $parent_user = array(
                'table' => 'user parent_u',
                'type' => 'left',
                'on' => 'parent_u.user_id = u.user_parent_id'
            );
			
			
            //管理角色
            $admin_user = array(
                'table' => 'admin_user au',
                'type' => 'left',
                'on' => 'au.user_id = u.user_id AND au.admin_user_state = 1'
            );
			
            //先获取总条数
            $find_data = db(parent::DB_APPLICATION_ID)
            ->table('user u')
            ->joinon($parent_user, $admin_user)
            ->call('where', $call_where)
            ->find('count(distinct u.user_id) as count');
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
                $user_phone_verify_count_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_count("u");
                $user_phone_login_count_sql = object(parent::TABLE_USER_PHONE)->sql_join_login_count("u");
				$user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
				$user_parent_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u", "user_parent_id");
				$user_recommend_count_sql = $this->sql_join_son_count("u");
				
                $select = array(
					'au.*',
                    'u.*',
                    '('.$user_phone_verify_count_sql.') as user_phone_verify_count',
                    '('.$user_phone_login_count_sql.') as user_phone_login_count',
                    '('.$user_phone_verify_list_sql.') as user_phone_verify_list',
                    '('.$user_recommend_count_sql.') as user_recommend_count',
                    '('.$user_parent_phone_verify_list_sql.') as user_parent_phone_verify_list',
                    'parent_u.user_logo_image_id as user_parent_logo_image_id',
                    'parent_u.user_nickname as user_parent_nickname',
                    'parent_u.user_compellation as user_parent_compellation',
                    'parent_u.user_state as user_parent_state',
                );
            }
            
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('user u')
            ->joinon($parent_user, $admin_user)
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);
            
            return $data;
        });
        
    }    
    
    
    
    /**
     * 返回用户的子级ID，SQL语句
     * 
     * @param   string      $user_id
     * @return  string
     */
    public function sql_select_son_id($user_id = ""){
        if( empty($user_id) ){
            return "";
        }
        
        return db(parent::DB_APPLICATION_ID)
        ->table('user')
        ->where(array('user_parent_id=[+]', $user_id))
        ->select(array('distinct user_id'), function($q){
            return $q['query']['select'];
        });
    }
    
    
    
    /**
     * 返回用户的子级ID，SQL语句
     * 
     * @param   string      $alias	别名称
     * @return  string
     */
    public function sql_join_son_count($alias = ""){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        
        return db(parent::DB_APPLICATION_ID)
        ->table('user son_u')
		//->groupby('son_u.user_id')
        ->where(array('son_u.user_parent_id = '.$alias.'user_id'))
        ->find(array('count(distinct son_u.user_id) as c'), function($q){
            return $q['query']['find'];
        });
    }
    
    
    
    
    
    //===========================================
    // 检测
    //===========================================
    
    
    
    
    /**
     * 检测支付密码
     * 
     * @param integer 	$password 	[支付密码]
	 * @param string 	$user_id 	[用户ID]
     * @return bool
     */
    public function check_pay_password($password, $user_id = ''){
        //查询用户数据
        if( empty($user_id) ){
        	$user_id = $_SESSION['user_id'];
        }
        
        $data = object(parent::TABLE_USER)->find($user_id);
        $user_json = cmd(array($data['user_json']), 'json decode');

        if (empty($user_json['pay_password']))
            return '未设置支付密码';

        $left = md5($password.$user_id);
        $right = md5($user_id.$password);
        if ($left != $user_json['pay_password']['left'] || $right != $user_json['pay_password']['right']) {
            //记录支付密码错误时间
            $user_json['pay_password']['error_time'][] = time();
            object(parent::TABLE_USER)->update(array(array('user_id=[+]', $user_id)), array(
                'user_json' => cmd(array($user_json), 'json encode'),
                'user_update_time' => time()
            ));

            return '支付密码错误';
        }

        return true;
    }
    
    
    
    
    
}