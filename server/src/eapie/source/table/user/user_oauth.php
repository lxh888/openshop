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

// 第三方登录
class user_oauth extends main 
{

   	
	/**
	 * 缓存的键列表
	 * 
	 * @var	string
	 */
	const CACHE_KEY = array(__CLASS__, "user");
	

	
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
     * @param   array       $data           数据
     * @param   array       $call_data      数据
     * @return  bool
     */
    public function insert($data = array(), $call_data = array())
    {
        if (empty($data) && empty($call_data))
            return false;
        
        $bool = db(parent::DB_APPLICATION_ID)
            ->table('user_oauth')
            ->call('data', $call_data)
            ->insert($data);

        //清理当前项目缓存
        if ($bool)
            object(parent::CACHE)->clear(self::CACHE_KEY);

        return $bool;
    } 

    // ==========================================

    /**
     * 查询一条数据
     * @param  array  $where [description]
     * @return [type]        [description]
     */
    public function find($user_id='')
    {
        if(empty($user_id)){
            return false;
        }
        return object(parent::CACHE)->data(__CLASS__, __METHOD__,array($user_id),function($user_id){
            return db(parent::DB_APPLICATION_ID)
                ->table('user_oauth')
                ->where(array('user_id=[+]', $user_id))
                ->find();
        });
    }

    public function find_one($call_where=array()){

        return object(parent::CACHE)->data(__CLASS__, __METHOD__,array($call_where),function($call_where){
                return db(parent::DB_APPLICATION_ID)
                    ->table('user_oauth')
                    ->call('where',$call_where)
                    ->find();
            }
        );
    }

    /**
     * 查一条数据——根据第三方平台提供的用户信息唯一标识 
     * 
     * @param  [astr] $key [唯一标识 ]
     * @return array
     */
    public function find_where($key = '')
    {
        if (empty($key) || !is_string($key))
            return null;

        return object(parent::CACHE)->data(__CLASS__, __METHOD__,
            array($key),
            function($key){
                return db(parent::DB_APPLICATION_ID)
                    ->table('user_oauth')
                    ->where(array('user_oauth_key=[+]', $key))
                    ->find();
            }
        );
    }






	/**
     * 查一条数据——根据第三方平台提供的用户信息唯一标识 
     * 
     * @param  [astr] $key [唯一标识 ]
     * @return array
     */
    public function find_platform_key($platform = "", $key = "") {
    	
        if ( empty($platform) || 
        !is_string($platform) || 
        empty($key) || 
        !is_string($key) ){
        	return false;
        }

        return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($platform, $key), function($platform, $key){
            return db(parent::DB_APPLICATION_ID)
                ->table('user_oauth')
                ->where(array('user_oauth_platform=[+]', $platform), array('user_oauth_key=[+]', $key))
                ->find();
            }
        );
    }



	/**
     * 查一条数据——根据第三方平台提供的用户信息唯一标识 
     * 
     * @param  [astr] $key [唯一标识 ]
     * @return array
     */
	public function find_platform_user($platform = "", $user_id = ""){
		if ( empty($platform) || 
        !is_string($platform) || 
        empty($user_id) || 
        !is_string($user_id) ){
        	return false;
        }
		
		return object(parent::CACHE)->data(__CLASS__, __METHOD__, array($platform, $user_id), function($platform, $user_id){
            return db(parent::DB_APPLICATION_ID)
                ->table('user_oauth')
                ->where(array('user_oauth_platform=[+]', $platform), array('user_id=[+]', $user_id))
                ->find();
            }
        );
		
	}



    public function update( $call_where=array(),$data=array(),$call_data=array() )
    {
        if(empty($call_where) || empty($data)){
            return false;
        }
        $bool = (bool)db(parent::DB_APPLICATION_ID)
        ->table('user_oauth')
        ->call('where', $call_where)
        ->call('data', $call_data)
        ->update($data);
        
        if( !empty($bool) ){
            //清理当前项目缓存
            object(parent::CACHE)->clear(self::CACHE_KEY);
        }
        
        return $bool;

    }



}