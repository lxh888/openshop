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
class user_phone extends main {
    
    
    /**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, "user");
	
    
    
    
    
    /**
     * 数据检测
     * 
     * @var array
     */
    public $check = array(
        'user_phone_id' => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少手机号码的参数"),
                    '!null'=>array("手机号码的参数不能为空"),
                    'match'=>array('/^[0-9]{11}$/iu', "手机格式不合法"),
                    ),
            //字符长度检测
            'length' => array(
                    'length'=>array(11, "手机号码的字符长度太多")
                    ),
        ),
    
        
        'user_parent_phone_id' => array(
            //参数检测
            'format'=>array(
                    'echo'=>array("推荐人手机号码的数据类型不合法"),
                    'match'=>array('/^[0-9]{11}$/iu', "推荐人手机号码格式不合法"),
                    'length'=>array(11, "推荐人手机号码的字符长度太多")
                    ),
        ),
        
        
        "user_phone_type" => array(
            //参数检测
            'args'=>array(
                    'echo'=>array("手机号码类型的数据类型不合法"),
                    'match'=>array('/^[01]{1}$/i', "手机号码类型必须是0或1"),
                    ),
        ),
        
        
        "user_phone_sort" => array(
            //参数检测
            'args'=>array(
                    'echo'=>array("手机号码排序的数据类型不合法"),
                    'match'=>array('/^[0-9]{0,}$/i', "手机号码排序必须是整数"),
                    ),
        ),
        
        
        "phone_verify_key" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少手机验证码键名称参数"),
                    'echo'=>array("手机验证码键名称的数据类型不合法"),
                    '!null'=>array("手机验证码键名称不能为空"),
                    ),
        ),
        
        
        "phone_verify_code" => array(
            //参数检测
            'args'=>array(
                    'exist'=>array("缺少手机验证码参数"),
                    'echo'=>array("手机验证码的数据类型不合法"),
                    '!null'=>array("手机验证码不能为空"),
                    ),
        ),
        
        
        
    );
    
    
    
    
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
        ->table('user_phone')
        ->call('data', $call_data)
        ->insert($data);
        
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
        return $bool;
    }   
        
    
    
    
        
        
    /**
     * 更新数据
     * 
     * @param   array       $where
     * @param   array       $data
     * @param   array       $call_data
     * @return  bool
     */
    public function update($where = array(), $data = array(), $call_data = array()){
        if( empty($where) || (empty($data) && empty($call_data)) ){
            return false;
        }
        
        $bool = (bool)db(parent::DB_APPLICATION_ID)
        ->table('user_phone')
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
        ->table('user_phone')
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
     * @param   array   $user_phone_id
     * @return  array
     */
    public function remove($user_phone_id = ''){
        if( empty($user_phone_id) ){
            return false;
        }
        
        $bool = (bool)db(parent::DB_APPLICATION_ID)
        ->table('user_phone')
        ->where(array('user_phone_id=[+]', (string)$user_phone_id))
        ->delete();
        
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
        return $bool;
    }
    
    /**
     * 获取一个数据
     * 
     * @param   array   $user_phone_id
     * @return  array
     */
    public function find_by_user_id($user_id = ''){
        if( empty($user_id) ){
            return false;
        }
        
        $data = object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id), function($user_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('user_phone')
            ->where(array('user_id=[+]', (string)$user_id))
            ->find('user_phone_id');
        });
        
        if(isset($data['user_phone_id']))
            return $data['user_phone_id'];
        return null;
    }
    
    
        
    /**
     * 获取一个数据
     * 
     * @param   array   $user_phone_id
     * @return  array
     */
    public function find($user_phone_id = ''){
        if( empty($user_phone_id) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_phone_id), function($user_phone_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('user_phone')
            ->where(array('user_phone_id=[+]', (string)$user_phone_id))
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
                ->table('user_phone')
                ->call('where', $call_where)
                ->find();
        });
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
            ->table('user_phone')
            ->call('where', $where)
            ->call('orderby', $orderby)
            ->call('limit', $limit)
            ->select($select);
            
        });
        
    }   


    
    
    
                
    /**
     * 根据手机ID，判断用户是否存在
     * 
     * @param   string      $user_phone_id
     */
    public function find_user_exists($user_phone_id){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_phone_id), function($user_phone_id){
            return (bool)db(parent::DB_APPLICATION_ID)
            ->table('user_phone')
            ->where(array('user_phone_id=[+]', $user_phone_id))
            ->find('user_id');
        });
        
    }
    
        
    
    /**
     * 根据用户ID，获取一条登录手机数据
     * 
     * @param   string      $user_id
     * @param   array       $find
     * @return  array
     */
    public function find_user_login_data($user_id, $find = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id, $find), function($user_id, $find){
            
            //用户表
            $user = array(
                'table' => 'user as u',
                'type' => 'INNER',
                'on' => 'u.user_id = up.user_id'
            );
            
            if( empty($find) ){
                $find = array(
                    "up.*",
                    "u.user_logo_image_id",
                    "u.user_nickname",
                    "u.user_compellation",
                    "u.user_qq",
                    "u.user_email",
                    "u.user_json",
                );
            }
            
            
            $data = db(parent::DB_APPLICATION_ID)
            ->table('user_phone up')
            ->joinon($user)
            //user_phone_type=1 表示是登录手机号。user_phone_state=1已认证。
            ->where(array('up.user_id=[+]', (string)$user_id), array('[and] up.user_phone_type=1'), array('[and] up.user_phone_state=1'))
            ->orderby(array("user_phone_sort", false), array("user_phone_update_time", false), array("user_phone_insert_time", false) )
            ->find($find);
            
            if( !empty($data['user_phone_json']) ){
                $data['user_phone_json'] = cmd(array($data['user_phone_json']), 'json decode');
            }
            
            return $data;
        });
    }
    
    
    
                
    /**
     * 根据认证手机ID，获取用户数据
     * 
     * @param   string      $user_phone_id
     * @param   array       $find
     * @return  string
     */
    public function find_verify_data($user_phone_id, $find = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_phone_id, $find), function($user_phone_id, $find){
            
            //用户表
            $user = array(
                'table' => 'user as u',
                'type' => 'INNER',
                'on' => 'u.user_id = up.user_id'
            );
            
            if( empty($find) ){
                $find = array(
                    "up.*",
                    "u.user_nickname",
                    "u.user_compellation",
                    "u.user_qq",
                    "u.user_email",
                );
            }
            
            $data = db(parent::DB_APPLICATION_ID)
            ->table('user_phone up')
            ->joinon($user)
            //user_phone_state=1已认证。
            ->where(array('up.user_phone_id=[+]', (string)$user_phone_id), array('[and] up.user_phone_state=1'))
            ->find($find);
            
            if( !empty($data['user_phone_json']) ){
                $data['user_phone_json'] = cmd(array($data['user_phone_json']), 'json decode');
            }
            
            return $data;
        });
        
    }
    
    
    
    
    
    
    
    
    /**
     * 根据登录手机ID，获取用户
     * 
     * @param   string      $user_phone_id
     * @param   array       $find
     * @return  array
     */
    public function find_login_data($user_phone_id, $find = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_phone_id, $find), function($user_phone_id, $find){
            
            //用户表
            $user = array(
                'table' => 'user as u',
                'type' => 'LEFT',
                'on' => 'u.user_id = up.user_id'
            );
            
            if( empty($find) ){
                $find = array(
                    "up.*",
                    "u.user_nickname",
                    "u.user_compellation",
                    "u.user_qq",
                    "u.user_email",
                );
            }
            
            return db(parent::DB_APPLICATION_ID)
            ->table('user_phone up')
            ->joinon($user)
            //user_phone_type=1 表示是登录手机号。user_phone_state=1已认证。
            ->where(array('up.user_phone_id=[+]', (string)$user_phone_id), array('[and] up.user_phone_type=1'), array('[and] up.user_phone_state=1'))
            ->find($find);
        });
        
    }
    
    
    /**
     * 查询用户信息
     *
     * @param  string $user_phone_id 手机号
     * @return array
     */
    public function find_join_user($user_phone_id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_phone_id), function ($user_phone_id) {
            $join_user = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = up.user_id'
            );

            return db(parent::DB_APPLICATION_ID)
                ->table('user_phone up')
                ->joinon($join_user)
                ->where(array('up.user_phone_id=[+]', $user_phone_id))
                ->find();
        });
    }
    
        
    /**
     * 返回用户的 登录手机号个数
     * 
     * @param   string      $alias	别名称
     * @return  string
     */
    public function sql_join_login_count($alias = ""){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        
        return db(parent::DB_APPLICATION_ID)
        ->table('user_phone up')
        //user_phone_type 手机类型。0联系手机号，1登录手机号
        //user_phone_state  状态。0未认证，1已认证。注意，user_phone_type为1，那么该参数必须是为1
        ->where(array('up.user_id = '.$alias.'user_id'), array('[and] up.user_phone_type=1'), array('[and] up.user_phone_state=1'))
        ->find(array('count(distinct up.user_phone_id) as count'), function($q){
            return $q['query']['find'];
        });
        
    }
    
    
    
    
    
    
    
    
    /**
     * 返回用户的 认证手机号个数
     * 
     * @param   string      $alias	别名称
     * @return  string
     */
    public function sql_join_verify_count($alias = ""){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        
        return db(parent::DB_APPLICATION_ID)
        ->table('user_phone up')
        //user_phone_type 手机类型。0联系手机号，1登录手机号
        //user_phone_state  状态。0未认证，1已认证。注意，user_phone_type为1，那么该参数必须是为1
        ->where(array('up.user_id = '.$alias.'user_id'), array('[and] up.user_phone_state=1'))
        ->find(array('count(distinct up.user_phone_id) as count'), function($q){
            return $q['query']['find'];
        });
        
    }
    
    
	
	
	        
    /**
     * 返回用户的 登录手机号列表
     * 
     * @param   string      $alias	别名称
	 * @param   string      $field	字段名称
     * @return  string
     */
    public function sql_join_login_list($alias = "", $field = "user_id"){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        
        return db(parent::DB_APPLICATION_ID)
        ->table('user_phone up')
        //user_phone_type 手机类型。0联系手机号，1登录手机号
        //user_phone_state  状态。0未认证，1已认证。注意，user_phone_type为1，那么该参数必须是为1
        ->where(array('up.user_id = '.$alias.$field), array('[and] up.user_phone_type=1'), array('[and] up.user_phone_state=1'))
		//->groupby("up.user_id") 不分组，分组要报错
        ->select(array("GROUP_CONCAT(up.user_phone_id SEPARATOR ',') as list"), function($q){
            return $q['query']['select'];
        });
        
    }
	
	/**
     * 返回用户的 认证手机号列表
     * 
     * @param   string      $alias	别名称
	 * @param   string      $field	字段名称
     * @return  string
     */
    public function sql_join_verify_list($alias = "", $field = "user_id"){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        
        return db(parent::DB_APPLICATION_ID)
        ->table('user_phone up')
        //user_phone_type 手机类型。0联系手机号，1登录手机号
        //user_phone_state  状态。0未认证，1已认证。注意，user_phone_type为1，那么该参数必须是为1
        ->where(array('up.user_id = '.$alias.$field), array('[and] up.user_phone_state=1'))
		->orderby(array("up.user_phone_sort"), array("up.user_phone_type", true), array("up.user_phone_update_time", true), array("up.user_phone_id", true))
		//->groupby("up.user_id") 不分组，分组要报错
        ->select(array("GROUP_CONCAT(up.user_phone_id SEPARATOR ',') as list"), function($q){
            return $q['query']['select'];
        });
        
    }
    
    
    
    
    	        
    /**
     * 返回用户的 一个登录手机号
     * 
     * @param   string      $alias	别名称
     * @return  string
     */
    public function sql_join_login_phone($alias = ""){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        
        return db(parent::DB_APPLICATION_ID)
        ->table('user_phone up')
        //user_phone_type 手机类型。0联系手机号，1登录手机号
        //user_phone_state  状态。0未认证，1已认证。注意，user_phone_type为1，那么该参数必须是为1
        ->where(array('up.user_id = '.$alias.'user_id'), array('[and] up.user_phone_type=1'), array('[and] up.user_phone_state=1'))
        ->orderby(array("up.user_phone_sort"), array("up.user_phone_update_time", true), array("up.user_phone_id", true))
        ->find(array("up.user_phone_id"), function($q){
            return $q['query']['find'];
        });
        
    }
    
    
	
	
	
	/**
     * 返回用户的 一个认证手机号
     * 
     * @param   string      $alias	别名称
     * @return  string
     */
    public function sql_join_verify_phone($alias = ""){
        if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
        
        return db(parent::DB_APPLICATION_ID)
        ->table('user_phone up')
        //user_phone_type 手机类型。0联系手机号，1登录手机号
        //user_phone_state  状态。0未认证，1已认证。注意，user_phone_type为1，那么该参数必须是为1
        ->where(array('up.user_id = '.$alias.'user_id'), array('[and] up.user_phone_state=1'))
        ->orderby(array("up.user_phone_sort"), array("up.user_phone_type", true), array("up.user_phone_update_time", false), array("up.user_phone_id", true))
        ->find(array("up.user_phone_id"), function($q){
            return $q['query']['find'];
        });
        
    }
	
	
	
    
    
    
    
    
    
}
?>