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

//用户评论
class user_comment extends main
{


    /**
     * 缓存的键列表
     * 
     * @var string
     */
    const CACHE_KEY = array(__CLASS__);


    /**
     * 数据检测
     * 
     * @var array
     */
    public $check = array(
        'user_comment_id' => array(
            'args' => array(
                'exist' => array('缺少评论ID参数'),
                'echo'  => array('评论ID的数据类型不合法'),
                '!null' => array('评论ID不能为空'),
            ),
        ),
        'user_comment_root_id' => array(
            'args' => array(
                'echo'  => array('顶级评论ID的数据类型不合法'),
            ),
        ),
        'user_comment_parent_id' => array(
            'args' => array(
                'echo'  => array('父级评论ID的数据类型不合法'),
            ),
        ),
        'user_comment_module' => array(
            'args' => array(
                'exist' => array('缺少评论模块参数'),
                'echo'  => array('评论模块的数据类型不合法'),
                '!null' => array('评论模块不能为空'),
                'method'=> array(array(parent::TABLE_USER_COMMENT, 'check_module'), '模块标签不合法')
            ),
        ),
        'user_comment_key' => array(
            'args' => array(
                'exist' => array('缺少评论模块ID参数'),
                'echo'  => array('评论模块ID的数据类型不合法'),
                '!null' => array('评论模块ID不能为空'),
            ),
        ),
        'user_comment_value' => array(
            'args' => array(
                'exist' => array('缺少评论内容参数'),
                'echo'  => array('评论内容的数据类型不合法'),
                '!null' => array('评论内容不能为空'),
            ),
        ),
        'user_comment_state' => array(
            'args'=>array(
            	'exist' => array('缺少评论状态值的参数'),
                'echo'  => array('评论状态值的数据类型不合法'),
                'match'=>array('/^[0-3]$/', '评论状态值必须是0、1、2、3'),
            )
        ),
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


    /**
     * 获取模块标签列表
     * @return  array
     */
    public function get_module()
    {
        return array(
            parent::MODULE_SHOP_GOODS => '商城商品',
            parent::MODULE_MERCHANT => '商家',
        );
    }



		
	/**
	 * 获取模块数据类列表
	 * 模块处理类。用于检测主键
	 * 
	 * @param	void
	 * @return	array
	 */
	public function get_module_table(){
		return array(
            parent::MODULE_SHOP_GOODS => parent::TABLE_SHOP_GOODS,
        );
	}
	
	
	

		
	/**
     * 根据模块、主键，获取收藏数据
     * 
     * @param   string  $module
	 * @param   string  $key
     * @return  bool
     */
	public function get_module_key($module, $key){
		$module_table_list = $this->get_module_table();
		if( isset($module_table_list[$module]) ){
			if( !method_exists(object($module_table_list[$module]), "find") ){
				return false;
			}
            return object($module_table_list[$module])->find($key);
        }else{
            return false;
        }
	}
	
	



    /**
     * 回复的总条数
     * @return string
     */
    public function sql_join_reply_count($alias)
    {
        return db(parent::DB_APPLICATION_ID)
            ->table('user_comment son_uc')
            ->where(array('son_uc.user_comment_root_id = '.$alias.'.user_comment_id'))
            ->select(array('count(son_uc.user_comment_id)'), function($q){
                return $q['query']['select'];
            });
    }


    //===========================================
    // 增删改
    //===========================================


    public function insert($data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('user_comment')
            ->call('data', $call_data)
            ->insert($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }   


    /**
     * 批量插入数据
     * 
     * @param   array $data 数据，索引数组
     * @return  bool
     */
    public function insert_batch($data = array())
    {
        if (empty($data))
            return false;

        $bool = db(parent::DB_APPLICATION_ID)
        ->table('user_comment')
        ->batch()
        ->insert($data);
        
        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    public function update($where = array(), $data = array(), $call_data = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('user_comment')
            ->call('where', $where)
            ->call('data', $call_data)
            ->update($data);

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }   


    public function delete($call_where = array())
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('user_comment')
            ->call('where', $call_where)
            ->delete();

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    public function remove($user_comment_id = '')
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('user_comment')
            ->where(array('user_comment_id=[+]', $user_comment_id))
            ->delete();

        //清理缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }


    //===========================================
    // 查询
    //===========================================


    /**
     * 分页数据
     * @param  array $config 配置参数
     * @return array
     */
    public function select_page($config = array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config) {
            //查询配置
            $call_where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $call_limit   = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select       = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

            $limit = array(
                (isset($call_limit[0]) ? $call_limit[0] : 0),
                (isset($call_limit[1]) ? $call_limit[1] : 0)
            );

            //设置返回的数据
            $data = array(
                'row_count'   => 0,
                'limit_count' => $limit[0] + $limit[1],
                'page_size'   => $limit[1],
                'page_count'  => 0,
                'page_now'    => 0,
                'data'        => array()
            );

			//左连用户表
            $join_user = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = uc.user_id'
            );

			//顶级评论
            $join_root_comment = array(
                'table' => 'user_comment root_uc',
                'type' => 'left',
                'on' => 'root_uc.user_comment_id = uc.user_comment_root_id'
            );
			
			//父级评论
            $join_parent_comment = array(
                'table' => 'user_comment parent_uc',
                'type' => 'left',
                'on' => 'parent_uc.user_comment_id = uc.user_comment_parent_id'
            );
			
            //获取总条数
            $total_count = db(parent::DB_APPLICATION_ID)
            ->table('user_comment uc')
			->joinon($join_user, $join_root_comment, $join_parent_comment)
            ->call('where', $call_where)
            ->find('count(*) as count');

            //是否有数据
            if (empty($total_count['count'])) {
                return $data;
            } else {
                $data['row_count'] = $total_count['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count']/$data['page_size']);
                    $data['page_now'] = ceil($limit[0]/$data['page_size']) + 1;
                }
            }

            
			if( empty($select) ){
				$user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
				$select = array(
					'uc.*',
					'u.user_parent_id',
                    'u.user_nickname',
                    'u.user_logo_image_id',
                    '('.$user_phone_verify_list_sql.') as user_phone_verify_list',
                    'root_uc.user_comment_value as user_comment_root_value',
                    'parent_uc.user_comment_value as user_comment_parent_value',
				);
			}
			
			
            //查询数据
            $data['data'] = db(parent::DB_APPLICATION_ID)
            ->table('user_comment uc')
            ->joinon($join_user, $join_root_comment, $join_parent_comment)
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);

            return $data;
        });
    }

    /**
     * 数量
     * @return num
     */
    public function select_count($config=array()){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config) {
            //查询配置
            $call_where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
        
             //获取总条数
             return db(parent::DB_APPLICATION_ID)
            ->table('user_comment')
            ->call('where', $call_where)
            ->find('count(*) as count');
        });    
    }


    /**
     * 查一条记录，根据主键
     * @param  string $id 商家用户表ID
     * @return array
     */
    public function find($user_comment_id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_comment_id), function ($user_comment_id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('user_comment')
                ->where(array('user_comment_id=[+]', $user_comment_id))
                ->find();
        });
    }


    //===========================================
    // 检测
    //===========================================


    /**
     * 检测模块标签
     * 
     * @param   string  $val
     * @return  bool
     */
    public function check_module($val)
    {
        $module_list = $this->get_module();
        return isset($module_list[$val]);
    }


    //===========================================
    // SQL
    //===========================================


    /**
     * 返回某模块内容的评论数
     * @author green
     *
     * @param  string $alias [description]
     * @return [type]        [description]
     */
    public function sql_join_count($alias, $module, $key)
    {
        return db(parent::DB_APPLICATION_ID)
            ->table('user_comment uc')
            ->where(array("uc.user_comment_module = '$module'"))
            ->where(array("uc.user_comment_key = $alias.$key"))
            ->where(array('uc.user_comment_state = 1'))
            ->find(array('COUNT(*)'), function($q) {
                return $q['query']['find'];
            });
    }
}