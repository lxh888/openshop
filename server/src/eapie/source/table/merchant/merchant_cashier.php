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

class merchant_cashier extends main
{

    /**
     * 缓存的键列表
     * 
     * @var string
     */
    const CACHE_KEY = array(__CLASS__);


    /**
     * @var [arr]   [数据检测]
     */
    public $check = array(
        'merchant_cashier_id' => array(
            'args' => array(
                'exist' => array('缺少收银员表ID名称参数'),
                'echo'  => array('收银员表ID类型不合法'),
                '!null' => array('收银员表ID不能为空')
            )
        ),
        'merchant_id' => array(
            'args' => array(
                'exist' => array('缺少商家表ID名称参数'),
                'echo'  => array('商家表ID类型不合法'),
                '!null' => array('商家表ID不能为空')
            )
        ),
        'user_id' => array(
            'args' => array(
                'exist' => array('缺少收银员的用户表ID名称参数'),
                'echo'  => array('收银员的用户表ID类型不合法'),
                '!null' => array('收银员的用户表ID不能为空')
            )
        ),
        'merchant_cashier_action_user' => array(
            'args' => array(
                'exist' => array('缺少商家操作用户表ID名称参数'),
                'echo'  => array('商家操作用户表ID类型不合法'),
                '!null' => array('商家操作用户表ID不能为空')
            )
        ),
        'merchant_cashier_name' => array(
            'args' => array(
                'exist' => array('缺少收银员名称参数'),
                'echo'  => array('收银员名称类型不合法'),
                '!null' => array('收银员名称不能为空')
            )
        ),
        'merchant_cashier_info' => array(
            'args' => array(
                'echo'  => array('收银员简介类型不合法'),
            )
        ),
        'merchant_cashier_state' => array(
            'args' => array(
                'match' => array('/^[0-2]$/', '收银员状态值必须是0、1、2')
            )
        ),
        'merchant_cashier_sort' => array(
            'args' => array(
                'match' => array('/^\d+$/', '收银员排序值必须是数字')
            )
        )
    );



    /**
     * 获取一个id号
     * 
     * @param	void
     * @return	string
     */
    public function get_unique_id()
    {
        return cmd(array(22), 'random autoincrement');
    }


    /**
     * 插入新数据
     * 
     * @param	array		$data			数据
     * @param	array		$call_data		数据
     * @return	bool
     */
    public function insert($data = array(), $call_data = array())
    {
        if (empty($data) && empty($call_data)) {
            return false;
        }

        $bool = (bool)db(parent::DB_APPLICATION_ID)
            ->table('merchant_cashier')
            ->call('data', $call_data)
            ->insert($data);

        if (!empty($bool)) {
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }

        return $bool;
    }


    /**
     * 根据唯一标识删除数据
     * @param  [str] $merchant_user_id [商家收银员表ID]
     * @return bool
     */
    public function remove($merchant_cashier_id = '')
    {
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('merchant_cashier')
            ->where(array('merchant_cashier_id=[+]', $merchant_cashier_id))
            ->delete();

        //清理当前项目缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    }



    /**
     * 更新数据
     * 
     * @param	array		$where
     * @param	array		$data
     * @param	array		$call_data
     * @return	bool
     */
    public function update($where = array(), $data = array(), $call_data = array())
    {
        if (empty($where) || (empty($data) && empty($call_data))) {
            return false;
        }

        $bool = (bool)db(parent::DB_APPLICATION_ID)
            ->table('merchant_cashier')
            ->call('where', $where)
            ->call('data', $call_data)
            ->update($data);

        if (!empty($bool)) {
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }

        return $bool;
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
                ->table('merchant_cashier')
                ->call('where', $where)
                ->call('orderby', $orderby)
                ->call('limit', $limit)
                ->select($select);
        });
    }


    /**
     * 查——单个，根据主键
     * @param  string $id 收银员表ID
     * @return array
     */
    public function find($id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($id), function ($id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('merchant_cashier')
                ->where(array('merchant_cashier_id=[+]', $id))
                ->find();
        });
    }

    /**
     * 查单条数据，根据user_id
     * @param   string  $user_id    用户ID
     * @return  array
     */
    public function find_byuid($user_id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id), function ($user_id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('merchant_cashier')
                ->where(array('user_id=[+]', $user_id))
                ->find();
        });
    }


    /**
     * 查单条数据，根据merchant_cashier_action_user
     * @param   string  $user_id    用户ID
     * @return  array
     */
    public function find_by_action_user_id($id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($id), function ($id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('merchant_cashier')
                ->where(array('merchant_cashier_action_user=[+]', $id))
                ->find();
        });
    }

    /**
     * 查——单个，根据merchant_cashier_id
     * @param  array $[user_id,商家ID]
     * @return array
     */
    public function find_by_merchant_cashier_id($id = '')
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($id), function ($id) {
            return db(parent::DB_APPLICATION_ID)
                ->table('merchant_cashier')
                ->where(array('merchant_cashier_id=[+]',$id))
                ->find();
        });
    }

    /**
     * 查——单个，根据user_id和商家ID
     * @param  array $[user_id,商家ID]
     * @return array
     */
    public function find_by_uid_and_merchant_id($data=array())
    {
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($data), function ($data) {
            return db(parent::DB_APPLICATION_ID)
                ->table('merchant_cashier')
                ->where(array('user_id=[+]', $data['user_id']))
                ->where(array('[and] merchant_id=[+]',$data['merchant_id']))
                ->find();
        });
    }



    /**
     * 获取所有分页数据
     * $config = array(
	 * 	'where' => array(), //条件
	 * 	'orderby' => array(), //排序
	 * 	'limit'	=> array(0, page_size), //取出条数，默认获取10条
	 * );
	 * 
	 * limit的分页算法是：当前页数-1 * page_size
	 * 序号的算法：key键+1，+每页显示的条数。等于分页后的序号。{key + 1 + page_size}
	 * 
	 * 返回的数据：
	 * $data = array(
	 * 	'row_count' => //数据总条数
	 * 	'limit_count' => //已取出条数
	 * 	'page_size' => //每页的条数
	 * 	'page_count' => //总页数
	 *  'page_now' => //当前页数
	 * 	'data' => //数据
	 * );
	 * 
	 * @param	array	$config		配置信息
	 * @return	array
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
			
			//用户数据
            $user = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = mc.user_id'
            );
            
			//商家数据
			$merchant = array(
                'table' => 'merchant m',
                'type' => 'left',
                'on' => 'm.merchant_id = mc.merchant_id'
            );
			
			
			//先获取总条数
			$find_data = db(parent::DB_APPLICATION_ID)
			->table('merchant_cashier mc')
			->joinon($user, $merchant)
			->call('where', $call_where)
			->find('count(*) as count');
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
				 if( empty($select) ){
				$user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
                $select = array(
                    'u.user_parent_id',
                    'u.user_nickname',
                    'u.user_logo_image_id',
                    '('.$user_phone_verify_list_sql.') as user_phone_verify_list',
                    'mc.*',
                    'm.merchant_logo_image_id',
                    'm.merchant_name',
                );
            }
			}
			
			$data['data'] =  db(parent::DB_APPLICATION_ID)
			->table('merchant_cashier mc')
			->joinon($user, $merchant)
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
			->select($select);
			
			return $data;
		});
		
		
	}

}