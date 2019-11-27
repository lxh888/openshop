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
class merchant_tally extends main {
	
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__);
	
	
	
	/**
	 * 数据检测
	 * 
	 * @var	array
	 */
	public $check = array(
		'merchant_tally_id' => array(
            'args' => array(
                'exist' => array('缺少线下订单ID参数'),
                'echo'  => array('线下订单ID类型不合法'),
                '!null' => array('线下订单ID不能为空')
            )
        ),
		'merchant_tally_goods_name' => array(
            'args' => array(
                'exist' => array('缺少商品名称参数'),
                'echo'  => array('商品名称数据类型不合法'),
                '!null' => array('商品名称不能为空')
            )
        ),
        'merchant_tally_goods_money' => array(
            'args'=>array(
                'exist' => array('缺少商品单价参数'),
                'echo'  => array('商品单价数据类型不合法'),
                '!null' => array('商品单价不能为空'),
                'match' => array('/^[1-9]\d*$/', '商品单价不合法'),
            ),   
        ),
        'merchant_tally_goods_number' => array(
            'args'=>array(
                'exist' => array('缺少商品数量参数'),
                'echo'  => array('商品数量数据类型不合法'),
                '!null' => array('商品数量不能为空'),
                'match' => array('/^[1-9]\d*$/', '商品数量不合法'),
            ),   
        ),
        'merchant_tally_client_phone' => array(
            'args' => array(
                'exist'=> array('缺少顾客手机号'),
                'match'=> array('/^1\d{10}$/', '顾客手机号不合法'),
            ),
        ),
        'merchant_tally_comment' => array(
            'args' => array(
                'echo'  => array('备注数据类型不合法'),
            )
        ),
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
	 * 插入新数据
	 * 
	 * @param	array		$data			数据
	 * @param	array		$call_data		数据
	 * @return	bool
	 */
	public function insert($data = array(), $call_data = array()){
		if( empty($data) && empty($call_data) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('merchant_tally')
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
	 * @param	array		$where
	 * @param	array		$data
	 * @param	array		$call_data
	 * @return	bool
	 */
	public function update($where = array(), $data = array(), $call_data = array()){
		if( empty($where) || (empty($data) && empty($call_data)) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('merchant_tally')
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
	 * @param	array	$where
	 * @return	array
	 */
	public function delete($where = array()){
		if( empty($where) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('merchant_tally')
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
	 * @param	array	$merchant_tally_id
	 * @return	array
	 */
	public function remove($merchant_tally_id = ''){
		if( empty($merchant_tally_id) ){
			return false;
		}
		
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('merchant_tally')
		->where(array('merchant_tally_id=[+]', (string)$merchant_tally_id))
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
	 * @param	array	$merchant_tally_id
	 * @return	array
	 */
	public function find($merchant_tally_id = ''){
		if( empty($merchant_tally_id) ){
			return false;
		}
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($merchant_tally_id), function($merchant_tally_id){
			return db(parent::DB_APPLICATION_ID)
			->table('merchant_tally')
			->where(array('merchant_tally_id=[+]', (string)$merchant_tally_id))
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
                ->table('merchant_tally')
                ->call('where', $call_where)
                ->find();
        });
    }
	
			
		
	/**
	 * 获取多个数据
	 *  $config = array(
	 * 	'where' => array(), //条件
	 * 	'orderby' => array(), //排序
	 * 	'limit'	=> array(0, page_size), //取出条数，默认不限制
	 * );
	 * 
	 * @param	array	$config
	 * @return	array
	 */
	public function select($config = array()){
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
			$where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
			$orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
			$limit = isset($config['limit']) && is_array($config['limit'])? $config['limit'] : array();
			$select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
			
			return db(parent::DB_APPLICATION_ID)
			->table('merchant_tally')
			->call('where', $where)
			->call('orderby', $orderby)
			->call('limit', $limit)
			->select($select);
			
		});
		
	}	
	
	
	
	
	/**
	 * 查询某商家账单明细
	 *
	 * @param  array	$config		查询配置
	 * @return array
	 */
    public function select_page($config){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function ($config) {
            $call_where   = isset($config['where']) && is_array($config['where']) ? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby']) ? $config['orderby'] : array();
            $call_limit   = isset($config['limit']) && is_array($config['limit']) ? $config['limit'] : array();
            $select       = isset($config['select']) && is_array($config['select']) ? $config['select'] : array();

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
                'on' => 'u.user_id = mt.user_id'
            );
			
			//商家数据
			$merchant = array(
                'table' => 'merchant m',
                'type' => 'left',
                'on' => 'm.merchant_id = mt.merchant_id'
            );
			
			
            //查询总条数
            $counts = db(parent::DB_APPLICATION_ID)
            ->table('merchant_tally mt')
			->joinon($user, $merchant)
            ->call('where', $call_where)
            ->find('count(*) as count');

            if (empty($counts['count'])) {
                return $data;
            } else {
                $data['row_count'] = $counts['count'];
                if (!empty($data['page_size'])) {
                    $data['page_count'] = ceil($data['row_count'] / $data['page_size']);
                    $data['page_now'] = ceil($limit[0] / $data['page_size']) + 1;
                }
            }
			
			
			if( empty($select) ){
				$user_phone_verify_list_sql = object(parent::TABLE_USER_PHONE)->sql_join_verify_list("u");
                $select = array(
                    'u.user_parent_id',
                    'u.user_nickname',
                    'u.user_logo_image_id',
                    '('.$user_phone_verify_list_sql.') as user_phone_verify_list',
                    'mt.*',
                    'm.merchant_logo_image_id',
                    'm.merchant_name',
                    "voucher_i.image_width as merchant_tally_voucher_image_width",
                    "voucher_i.image_height as merchant_tally_voucher_image_height",
                );
            }
			
			//图片数据
            $voucher = array(
                'table' => 'image voucher_i',
                'type' => 'LEFT',
                'on' => 'voucher_i.image_id = mt.merchant_tally_voucher'
            );
			
            //查询数据
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('merchant_tally mt')
			->joinon($user, $merchant, $voucher)
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);

            return $data;
        });
    }

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>