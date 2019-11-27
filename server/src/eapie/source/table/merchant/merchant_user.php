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
use eapie\error;

class merchant_user extends main
{
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, "merchant");
	
	
    /**
     * @var [arr] [数据检测]
     */
    public $check = array(
        'merchant_user_id' => array(
            'args'=>array(
                'exist' => array('缺少商家用户表ID名称参数'),
                'echo'  => array('商家用户表ID类型不合法'),
                '!null' => array('商家用户表ID不能为空')
            )
        ),
        'merchant_user_name' => array(
            'args'=>array(
                'exist' => array('缺少商家用户名称参数'),
                'echo'  => array('商家用户名称类型不合法'),
                '!null' => array('商家用户名称不能为空')
            )
        ),
        'merchant_user_info' => array(
            'args'=>array(
                'echo'  => array('商家用户简介类型不合法'),
            )
        ),
        'merchant_user_state' => array(
            'args'=>array(
                'match'=>array('/^[0-2]$/', '商家用户状态值必须是0、1、2')
            )
        ),
        'merchant_user_sort' => array(
            'args'=>array(
                'match'=>array('/^\d+$/', '商家用户排序值必须是数字')
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
        return cmd(array(22), 'random autoincrement');
    }







    // ==========================================

    /**
     * 增
     * 
     * @param   [arr] $data      [插入的数据]
     * @param   [arr] $call_data [绑定的数据]
     * @return  bool
     */
    public function insert($data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
        ->table('merchant_user')
        ->call('data', $call_data)
        ->insert($data);

        //清理当前项目缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }

    /**
     * 改
     * @param  [str] $merchant_user_id [商家用户表ID]
     * @param  [arr] $data             [更新的数据]
     * @param  [arr] $call_data        [更新的绑定数据]
     * @return bool
     */
    public function update($merchant_user_id = '', $data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('merchant_user')
            ->where(array('merchant_user_id=[+]', $merchant_user_id))
            ->call('data', $call_data)
            ->update($data);

        //清理当前项目缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }

    /**
     * 删除
     * @param  [str] $merchant_user_id [商家用户表ID]
     * @return bool
     */
    public function delete($merchant_user_id = '')
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('merchant_user')
            ->where(array('merchant_user_id=[+]', $merchant_user_id))
            ->delete();

        //清理当前项目缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }

    // 查询数据 =====================================

    /**
     * 查——单个，根据主键
     * @param  string $id 商家用户表ID
     * @return array
     */
    public function find($id = ''){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($id), function ($id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('merchant_user')
                ->where(array('merchant_user_id=[+]', $id))
                ->find();
        });
    }



	/**
     * 查——单个，根据商家ID，获取一个商家用户
	 * 
     * @param  string	$merchant_id	商家用户表ID
     * @return array
     */
    public function find_merchant($merchant_id = ''){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($merchant_id), function ($merchant_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('merchant_user')
            ->where(array('merchant_id=[+]', $merchant_id))
            ->find();
        });
    }




    /**
     * 查一条记录
     * 
     * @param  array  $call_where 查询条件
     * @return array
     */
    public function find_where($call_where = array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where), function ($call_where) {
            return db(parent::DB_APPLICATION_ID)
                ->table('merchant_user')
                ->call('where', $call_where)
                ->find();
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
    public function select($config = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            $where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
            $select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
            
            return db(parent::DB_APPLICATION_ID)
            ->table('merchant_user')
            ->call('where', $where)
            ->call('orderby', $orderby)
            ->call('limit', $limit)
            ->select($select);
        });
    }   


	/**
     * 查——某商家的全部用户ID
	 * 
     * @param  [str] $merchant_id [商家ID]
     * @return array
     */
    public function select_all_user_id($merchant_id = ''){
    	if( empty($merchant_id) ){
    		return false;
    	}
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($merchant_id), function($merchant_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('merchant_user')
            ->where(array('merchant_id=[+]', $merchant_id), array('[and] merchant_user_state=1'))
            ->select(array('distinct user_id'));
            }
        );
    }



	/**
     * 查多个商家用户ID
	 * 
     * @param  array	$call_where		条件
	 * @param  int		$limit			个数
     * @return array
     */
    public function select_where_limit_user_id($call_where = array(), $limit = 1){
    	if( empty($call_where) && 
    	empty($limit) ){
    		return false;
    	}
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($call_where, $limit), function($call_where, $limit){
            return db(parent::DB_APPLICATION_ID)
            ->table('merchant_user')
			->call('where', $call_where)
			->limit(0, $limit)
            ->select(array('distinct user_id'));
            }
        );
    }


    /**
     * 查——某商家的全部用户
     * @param  [str] $merchant_id [商家ID]
     * @return array
     */
    public function select_all($merchant_id = ''){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__,
            array($merchant_id),
            function ($merchant_id) {
                return db(parent::DB_APPLICATION_ID)
                    ->table('merchant_user')
                    ->where(array('merchant_id=[+]', $merchant_id))
                    ->orderby('merchant_user_sort desc ')
                    ->select();
            }
        );
    }

    /**
     * 查询用户所属的商家ID
     *
     * @param  [str] $user_id [用户ID]
     * @return array [索引数组，多个商家ID]
     */
    public function get_mch_ids($user_id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__,
            array($user_id),
            function ($user_id) {
                //查询数据
                $rows = db(parent::DB_APPLICATION_ID)
                    ->table('merchant_user')
                    ->where(array('user_id=[+]', $user_id))
                    ->where(array('merchant_user_state=1'))
                    ->select('merchant_id');
                //格式化数据
                $ids = array();
                if ($rows) {
                    foreach ($rows as $v) {
                        array_push($ids, $v['merchant_id']);
                    }
                }
                return $ids;
            });
    }

    // 检测 ==========================================

    
    
    /**
     * 检测——用户是否存在
	 * 
     * @param	string		$user_id   	判断是否是商家用户
     * @return	bool
     */
    public function check_user( $user_id = '' ){
    	return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id), function ($user_id){
            //连接商家表
            $join_merchant = array(
                'table' => 'merchant as m',
                'type' => 'inner',
                'on' => 'mu.merchant_id = m.merchant_id	AND m.merchant_state=1'
            );
            //查询数据
            return (bool)db(parent::DB_APPLICATION_ID)
            ->table('merchant_user AS mu')
            ->joinon($join_merchant)
            ->where(array('mu.merchant_user_state=1'))
            ->where(array('mu.user_id=[+]', $user_id))
            ->find('mu.merchant_user_id');
        });
    }
    
    
    
    /**
     * 检测——用户是否存在
     * @param  [str] $user_id       [用户ID]
     * @param  [str] $merchant_id   [商家ID]
     * @param  [bol] $check_state   [是否判断用户状态]
     * @return bool
     */
    public function check_exist($user_id = '', $merchant_id = '', $check_state = false)
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id, $merchant_id, $check_state), function ($user_id, $merchant_id, $check_state) {
            //查询条件
            $where = array();
            $where[] = array('user_id=[+]', $user_id);
            $where[] = array('[and] merchant_id=[+]', $merchant_id);

            //是否检测用户状态
            if ($check_state)
                $where[] = array('[and] merchant_user_state=1');

            //查询数据
            $row = db(parent::DB_APPLICATION_ID)
                ->table('merchant_user')
                ->call('where', $where)
                ->find('merchant_user_id');

            return boolval($row);
        });
    }

    /**
     * 检测——商家用户表ID是否存在
     * @param  [str] $id [商家用户表ID]
     * @return bool
     */
    public function check_exists_id($id = '')
    {
        if (empty($id) || !is_string($id))
            return false;

        return object(parent::CACHE)->data(__CLASS__, __METHOD__,
            array($id),
            function ($id) {
                //查询数据
                $row = db(parent::DB_APPLICATION_ID)
                    ->table('merchant_user')
                    ->where(array('merchant_user_id=[+]', $id))
                    ->find('merchant_user_id');

                return boolval($row);
            }
        );
    }

    /**
     * 检测——商家状态，用户状态，是否都合法
     * @param  string $user_id     用户ID
     * @param  string $merchant_id 商家ID
     * @return Boolean
     */
    public function check_state($user_id = '', $merchant_id = '')
    {
        if (empty($user_id) || !is_string($user_id) || empty($merchant_id) || !is_string($merchant_id))
            return false;

        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id, $merchant_id), function ($user_id, $merchant_id) {
            //连接商家表
            $join_merchant = array(
                'table' => 'merchant as m',
                'type' => 'inner',
                'on' => 'mu.merchant_id = m.merchant_id'
            );
            //查询数据
            $row = db(parent::DB_APPLICATION_ID)
                ->table('merchant_user AS mu')
                ->joinon($join_merchant)
                ->where(array('mu.merchant_user_state=1'))
                ->where(array('m.merchant_state=1'))
                ->where(array('mu.user_id=[+]', $user_id))
                ->where(array('mu.merchant_id=[+]', $merchant_id))
                ->find('mu.merchant_user_id');

            return boolval($row);
        });
    }

    /**
     * 检测身份
     * @author green
     * @return string [店铺ID]
     */
    public function check_identity()
    {
        // 查询商家用户信息
        $merchant_user = $this->find_where(array(array('user_id = [+]', $_SESSION['user_id'])));
        if (empty($merchant_user)) {
            throw new error('不是商家用户');
        }
        if ($merchant_user['merchant_user_state'] == 0) {
            throw new error('已封禁');
        }

        return $merchant_user['merchant_id'];
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
            
            //用户数据
            $user = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = mu.user_id'
            );
            
			
			//商家数据
			$merchant = array(
                'table' => 'merchant m',
                'type' => 'left',
                'on' => 'm.merchant_id = mu.merchant_id'
            );
			
			
            //先获取总条数
            $find_data = db(parent::DB_APPLICATION_ID)
            ->table('merchant_user mu')
            ->joinon($user, $merchant)
            ->call('where', $call_where)
            ->find('count(distinct mu.user_id) as count');
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
                    'mu.*',
                    'm.merchant_logo_image_id',
                    'm.merchant_name',
                );
            }
            
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('merchant_user mu')
            ->joinon($user, $merchant)
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);
            
            return $data;
        });
        
    }
    
    
    
        
    /**
     * 返回商家用户个数
     * 
     * @param   string      $alias	别名称
     * @return  string
     */
    public function sql_join_merchant_id_count($alias = ""){
		if( is_string($alias) && $alias != "" ){
            $alias .= ".";
        }
		
		//用户表
        $user = array(
            'table' => 'user as u',
            'type' => 'INNER',
            'on' => 'u.user_id = mu.user_id'
        );
		
		
        return db(parent::DB_APPLICATION_ID)
        ->table('merchant_user mu')
		->joinon($user)
        ->where(array('mu.merchant_id = '.$alias.'merchant_id'))
        ->select(array('count(distinct mu.user_id) as count'), function($q){
            return $q['query']['select'];
        });
    }
    
    









}