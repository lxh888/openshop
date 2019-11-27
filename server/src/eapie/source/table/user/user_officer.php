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
class user_officer extends main{

    /**用户身份表 */

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
        
    );


    /**
     * 获取一个id号
     * 
     * @param   void
     * @return  string
     */
    public function get_unique_id($num=0){
        if($num>0){
            return cmd(array($num), 'random autoincrement');
        }
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
        ->table('user_officer')
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
        ->table('user_officer')
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
        ->table('user_officer')
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
        ->table('user_officer')
        ->call('where', $where)
        ->delete();
        
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
        return $bool;
    }


    /**
     * 根据user_id查询一条身份信息
     */
    public function find($user_id='')
    {
        if( empty($user_id) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_id), function($user_id){
            return db(parent::DB_APPLICATION_ID)
            ->table('user_officer')
            ->where(array('user_id=[+]', (string)$user_id))
            ->find();
        });
    }


    /**
     * 根据user_id查询一条身份信息
     */
    public function find_by_officer_name($user_officer_name='')
    {
        if( empty($user_officer_name) ){
            return false;
        }
        
        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($user_officer_name), function($user_officer_name){
            return db(parent::DB_APPLICATION_ID)
            ->table('user_officer')
            ->where(array('user_officer_name=[+]', (string)$user_officer_name))
            ->find();
        });
    }

    /**
     * 查询分页数据
     */
    public function select_page($config=array())
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
				'page_now' => 0,
				'data' => array()
			);
		
		
			//用户数据
            $user = array(
                'table' => 'user u',
                'type' => 'left',
                'on' => 'u.user_id = uo.user_id'
            );
			
			
			
			//先获取总条数
			$find_data = db(parent::DB_APPLICATION_ID)
			->table('user_officer uo')
			->joinon($user)
			->call('where', $call_where)
			->find('count(distinct uo.user_id) as count');
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
					"uo.*",
					'u.user_logo_image_id',
                    'u.user_nickname',
                    'u.user_compellation',
                    'u.user_state',
                    '('.$user_phone_verify_list_sql.') as user_phone_verify_list',
				);
			}
						
			
			$data['data'] =  db(parent::DB_APPLICATION_ID)
			->table('user_officer uo')
			->joinon($user)
			->call('where', $call_where)
			->call('orderby', $call_orderby)
			->call('limit', $call_limit)
			->select($select);				
				
			return $data;
		});
    }
}
?>