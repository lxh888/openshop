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



namespace eapie\engine;
class redis extends \eapie\engine\init {
	
	
	/**
	 * RANDOMKEY 随机获取一个数据键
	 * MGET key1[,...] 获取键数据
	 */
	
	
	
	/**
	 * redis 对象资源
	 * 
	 * @var
	 */
	static private $_redis = NULL;
	
	
	
	
	/**
	 * 连接 redis
	 * 
	 * @return	object
	 */
	public function __construct(){
		if( empty(self::$_redis) ){
			
			//如果是开发环境，那么关闭缓存
			/*if( is_file(__DIR__ . DIRECTORY_SEPARATOR . "developer") ){
				self::$_redis = NULL;
				return;
			}*/
			
			if( !class_exists("Redis") ){
				self::$_redis = NULL;
				return;
			}
			
			self::$_redis = new \Redis();
			self::$_redis->connect('127.0.0.1', 6379);
			self::$_redis->auth('');//密码
			
		}
		
	}
	
	
	
	/**
	 * 释放资源
	 * 
	 * @return	void
	 */
	public function __destruct () {
		if( !empty(self::$_redis)  ){
			self::$_redis->close();
		}
   	}
	
	
	
	/**
     * 储存项目缓存键
	 * 
     * @return bool
     */
	public function set_redis_key($project = "", $key = ""){
		if( empty(self::$_redis) ){
    		return false;
    	}
		
		if( empty($project) || 
		(!is_string($project) && !is_numeric($project) ) || 
		empty($key) || 
		(!is_string($key) && !is_numeric($key)) ){
			return false;
		}
		
		$project_keys = self::$_redis->get($project);
		$project_keys = cmd(array($project_keys), 'json decode');
		if( !empty($project_keys) && is_array($project_keys)){
			if( in_array($key, $project_keys) ){
				return true;//已经存在这个键了
			}else{
				$project_keys[] = $key;
			}
			
		}else{
			//没有数据，则初始化
			$project_keys = array($key);
		}
		
		return self::$_redis->set($project, cmd(array($project_keys), 'json encode') );
	}
	
	
	
	/**
     * 获取项目缓存键
	 * 
     * @return bool
     */
	public function get_redis_key($project = ""){
		return $this->get_array($project);
	}
	
	
	
	/**
     * 执行原生的redis操作
	 * 
     * @return \Redis
     */
    public function redis_object(){
    	if( empty(self::$_redis) ){
    		return false;
    	}
		
        return self::$_redis;
    }
	
	
	
	/**
	 * 设置有效期为5秒的键值
	 * 
	 * @param	string		$key		键
	 * @param	int			$s			秒
	 * @param	array		$value		值。传进来
	 * @return	bool
	 */
	public function setex_array( $key = "", $s = 30, $value = array() ){
		if( empty(self::$_redis) ){
    		return false;
    	}
		
		if( empty($key) || 
		!is_string($key) || 
		empty($value) || 
		!is_array($value) ){
			return false;
		}
		
		return self::$_redis->setex($key, $s, cmd(array($value), 'json encode'));
	}
	
	
	
	
	/**
	 * 设置键值缓存
	 * 
	 * @param	string		$key		键
	 * @param	array		$value		值。传进来
	 * @return	bool
	 */
	public function set_array( $key = "", $value = array() ){
		if( empty(self::$_redis) ){
    		return false;
    	}
		
		if( empty($key) || 
		!is_string($key) || 
		empty($value) || 
		!is_array($value) ){
			return false;
		}
		
		return self::$_redis->set($key, cmd(array($value), 'json encode'));
	}
	
	
	
	/**
	 * 获取 值 数据
	 * 
	 * @param	string		$key		键
	 * @return	mixed
	 */
	public function get_array($key = ""){
		if( empty(self::$_redis) ){
    		return false;
    	}
		
		if( empty($key) || 
		(!is_string($key) && !is_numeric($key)) ){
			return false;
		}
		
		$value = self::$_redis->get($key);
		$value = cmd(array($value), 'json decode');
		if( empty($value) || !is_array($value) ){
			return false;
		}
		
		return $value;
	}
	
	
	
	/**
	 * 设置有效期为5000毫秒(同5秒)的键值
	 * 
	 * @param	string		$key		键
	 * @param	int			$s			秒
	 * @param	mixed		$value		值。传进来
	 * @return	bool
	 */
	public function psetex_array( $key = "", $ms = 30, $value = ""){
		if( empty(self::$_redis) ){
    		return false;
    	}
		
		if( empty($value) || !is_array($value) ){
			return false;
		}
		
		return self::$_redis->psetex($key, $ms, cmd(array($value), 'json encode')); 
	}
	
	
	
	
	/**
	 * 删除键值。传入键名称，字符串
	 * 可以传入数组 array('key1','key2')删除多个键
	 * 如果键为空，则删除所有的储存  flushAll
	 * 
	 * @param	mixed		$key		键
	 * @return	bool
	 */
	public function delete($key = ""){
		set_time_limit(0);//设置最大执行时间
		ini_set('memory_limit', '-1');//内存限制
		
		if( empty(self::$_redis) ){
    		return false;
    	}
		
		if( (!is_array($key) && !is_string($key) && !is_numeric($key)) ||
		( is_string($key) && "" == $key ) ||
		( is_array($key) && empty($key)) ){
			self::$_redis->flushAll();
		}else{
			//self::$_redis->delete($key);
			self::$_redis->del($key);
		}
		
		return true;
	}
	
	
	
	
	
	
}
?>