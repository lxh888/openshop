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



namespace eapie\source\table\express;

use eapie\main;

class express_rider extends main {
	
	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__);
	

	/**
     * @var [arr] [数据检测]
     */
    public $check = array(
    	'user_id' => array(
            //检查编号是否存在      
            'exists'=>array(
                '!method'=>array(array(parent::TABLE_EXPRESS_RIDER, 'find_exists_user'), "该用户已经是快递骑手，请勿重复添加") 
            ),
        ),
        'express_rider_name' => array(
            'args'=>array(
                'exist' => array('缺少快递骑手名称参数'),
                'echo'  => array('快递骑手名称类型不合法'),
                '!null' => array('快递骑手名称不能为空')
            )
        ),
        'express_rider_phone' => array(
            'args'=>array(
                'exist' => array('缺少快递骑手的手机号参数'),
                'echo'  => array('快递骑手的手机号类型不合法'),
                '!null' => array('快递骑手的手机号不能为空')
            )
        ),
        'express_rider_info' => array(
            'args'=>array(
                'echo'  => array('快递骑手简介类型不合法'),
            )
        ),
        'express_rider_province' => array(
            'args'=>array(
                'echo'  => array('分配的省份类型不合法'),
            )
        ),
        'express_rider_city' => array(
            'args'=>array(
                'echo'  => array('分配的城市类型不合法'),
            )
        ),
        'express_rider_district' => array(
            'args'=>array(
                'echo'  => array('分配的地区类型不合法'),
            )
        ),
        'express_rider_on_off' => array(
            'args'=>array(
                'match'=>array('/^[01]$/', '快递骑手开启接单状态必须是0、1')
            )
        ),
        'express_rider_state' => array(
            'args'=>array(
                'match'=>array('/^[0-2]$/', '快递骑手状态值必须是0、1、2')
            )
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
        ->table('express_rider')
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
		->table('express_rider')
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
	 * 根据唯一标识，删除数据
	 * 
	 * @param	array	$user_id
	 * @return	array
	 */
	public function remove($user_id = ''){
		if( empty($user_id) ){
			return false;
		}
		$bool = (bool)db(parent::DB_APPLICATION_ID)
		->table('express_rider')
		->where(array('user_id=[+]', (string)$user_id))
		->delete();
		
		if( !empty($bool) ){
			//清理当前项目缓存
			object(parent::CACHE)->clear(self::CACHE_KEY);
		}
		
		return $bool;
	}		
		
	




    /**
     * 获取用户骑手资料
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
            ->table('express_rider')
            ->where(array('user_id=[+]', (string)$user_id))
            ->find();
        });
    }
	
	
	
	                
    /**
     * 根据判断用户是否存在
     * 
     * @param   string      $user_id
     */
    public function find_exists_user($user_id){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id), function($user_id){
            return (bool)db(parent::DB_APPLICATION_ID)
            ->table('express_rider')
            ->where(array('user_id=[+]', $user_id))
            ->find('user_id');
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
            
            //用户数据
            $user = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = er.user_id'
            );
			
            //先获取总条数
            $find_data = db(parent::DB_APPLICATION_ID)
            ->table('express_rider er')
            ->joinon($user)
            ->call('where', $call_where)
            ->find('count(distinct er.user_id) as count');
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
                    'er.*',
                );
            }
            
            $data['data'] =  db(parent::DB_APPLICATION_ID)
            ->table('express_rider er')
            ->joinon($user)
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->call('limit', $call_limit)
            ->select($select);
            
            return $data;
        });
        
    }

    /**
     * 根据条件获取所有骑手
     */
    public function select( $config=array() ){
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($config), function($config){
            $call_where = isset($config['where']) && is_array($config['where'])? $config['where'] : array();
            $call_orderby = isset($config['orderby']) && is_array($config['orderby'])? $config['orderby'] : array();
            $select = isset($config['select']) && is_array($config['select'])? $config['select'] : array();
            
            
            $data =  db(parent::DB_APPLICATION_ID)
            ->table('express_rider ')
            ->call('where', $call_where)
            ->call('orderby', $call_orderby)
            ->select($select);
            
            return $data;
        });
    }
    
    
    
	
	
	
	
	
}