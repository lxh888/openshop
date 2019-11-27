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
class cache extends \eapie\engine\init {
	
	
	
	/**
	 * 数据缓存
	 * object(parent::CACHE)->data(__CLASS__, __METHOD__, array(参数), function(参数){
		});
	 * 
	 * 数据清理
	 * object(parent::CACHE)->clear(__CLASS__);
	 * 清理多个表的数据
	 * object(parent::CACHE)->clear(array("user", "shop_goods"));
	 * 
	 */
	
	
	
	
	/**
	 * 获取给定对象/类的类“basename”。
	 *
	 * @param  string|object  $class
	 * @return string
	 */
	private function _class_basename($class_name) {
		return basename(str_replace('\\', '/', $class_name)); 
	}
	
	
	
	
	/**
	 * 清理系统的缓存
	 * 每次项目有更新，则要清理缓存
	 * 
	 * @param	string	| array		$class_names	类名称，亦是表名称
	 * @return 	bool
	 */
	public function clear_system($class_names){
		//拼凑应用前缀
		$application_id = "{system}";
		
		$redis_keys = array();
		if( is_string($class_names) || is_numeric($class_names)){
			$redis_keys = object(parent::REDIS)->get_redis_key( $application_id.'<'.$this->_class_basename($class_names).'>' );
		}else
		if( is_array($class_names) && !empty($class_names) ){
			foreach($class_names as $class){
				$keys = object(parent::REDIS)->get_redis_key( $application_id.'<'.$this->_class_basename($class).'>' );
				if( empty($redis_keys) ){
					$redis_keys = $keys;
				}else
				if( !empty($keys) && is_array($keys) ){
					foreach($keys as $k){
						if( !in_array($k, $redis_keys) ){
							$redis_keys[] = $k;//不存在时添加
						}
					}
				}
				unset($keys);
			}
		}
		
		if( !empty($redis_keys) ){
			object(parent::REDIS)->delete($redis_keys);
			return true;
		}
		
		
	}
	
	
	
		
	/**
	 * 系统数据缓存
	 * 
	 * @param	string		__CLASS__
	 * @param	string		__METHOD__
	 * @param	array		$func_get_args		参数列表
	 * @param	closure		$closure			闭包函数
	 */
	public function data_system($class, $method, $func_get_args, $closure){
		//如果是开发环境，那么关闭缓存
		if( is_file(__DIR__ . DIRECTORY_SEPARATOR . "developer") ){
			return call_user_func_array ( $closure, $func_get_args);
		}
		
		//拼凑应用前缀
		$application_id = "{system}";
		//获得应用类
		$application_class = $application_id.'<'.$this->_class_basename($class).'>';
		//获得用户储存键
		$redis_key = $application_id.$method."(".cmd(array($func_get_args), "json encode").")";
		//根据储存键名称，获取缓存数据
		$data = object(parent::REDIS)->get_array($redis_key);
		if( !empty($data["redis"]) ){
			return $data["redis"];
		}
		$data = call_user_func_array ( $closure, $func_get_args);
		
		//先获取 缓存的数据，存在则直接返回
		object(parent::REDIS)->set_redis_key($application_class, $redis_key);
		object(parent::REDIS)->set_array($redis_key, array("redis"=>$data));
		
		return $data;
	}
	
	
	
	
	
			
	/**
	 * 清理该应用项目的缓存
	 * 每次项目有更新，则要清理缓存
	 * 
	 * @param	string	| array		$class_names		类名称，亦是表名称
	 * @param	string				$application_id		应用ID
	 * @return 	bool
	 */
	public function clear($class_names, $application_id = ""){
		
		if( empty($application_id) ){
			//获取应用ID
			$application = object(parent::MAIN)->api_application();
			if( empty($application['application_id']) ){
				return false;
			}
			$application_id = $application['application_id'];
		}
		
		//拼凑应用前缀
		$application_id = "{application}[".$application_id."]";
		
		$redis_keys = array();
		if( is_string($class_names) || is_numeric($class_names)){
			$redis_keys = object(parent::REDIS)->get_redis_key( $application_id.'<'.$this->_class_basename($class_names).'>' );
		}else
		if( is_array($class_names) && !empty($class_names) ){
			foreach($class_names as $class){
				$keys = object(parent::REDIS)->get_redis_key( $application_id.'<'.$this->_class_basename($class).'>' );
				if( empty($redis_keys) ){
					$redis_keys = $keys;
				}else
				if( !empty($keys) && is_array($keys) ){
					foreach($keys as $k){
						if( !in_array($k, $redis_keys) ){
							$redis_keys[] = $k;//不存在时添加
						}
					}
				}
				unset($keys);
			}
		}
		
		if( !empty($redis_keys) ){
			object(parent::REDIS)->delete($redis_keys);
			return true;
		}
		
	}
	
	
	
	
	
	
	/**
	 * 数据缓存
	 * 
	 * @param	string		__CLASS__
	 * @param	string		__METHOD__
	 * @param	array		$func_get_args		参数列表
	 * @param	closure		$closure			闭包函数
	 */
	public function data($class, $method, $func_get_args, $closure){
		//如果是开发环境，那么关闭缓存
		if( is_file(__DIR__ . DIRECTORY_SEPARATOR . "developer") ){
			return call_user_func_array ( $closure, $func_get_args);
		}
		//获取应用ID
		$application = object(parent::MAIN)->api_application();
		if( empty($application['application_id']) ){
			return call_user_func_array ( $closure, $func_get_args);
		}
		//拼凑应用前缀
		$application_id = "{application}[".$application['application_id']."]";
		//获得应用类
		$application_class = $application_id.'<'.$this->_class_basename($class).'>';
		//获得用户储存键
		$redis_key = $application_id.$method."(".cmd(array($func_get_args), "json encode").")";
		//根据储存键名称，获取缓存数据
		$data = object(parent::REDIS)->get_array($redis_key);
		if( !empty($data["redis"]) ){
			return $data["redis"];
		}
		$data = call_user_func_array ( $closure, $func_get_args);
		
		//先获取 缓存的数据，存在则直接返回
		object(parent::REDIS)->set_redis_key($application_class, $redis_key);
		object(parent::REDIS)->set_array($redis_key, array("redis"=>$data));
		
		return $data;
	}
	
	
	/**
	 * 创建一个零时文件，在程序执行完后自动删除
	 * 
	 * @param	string	$dir					目录。不存在则创建
	 * @param	string	$suffix = 'tempfile'	文件后缀
	 * @return 	string	$tempfile_path
	 */
	public function tempfile($dir = '', $suffix = 'tempfile'){
		//tempfile
		if( empty($dir) ){
			$dir = 'default';
		}
		
		//获得缓存目录
		$directory = CACHE_PATH.DIRECTORY_SEPARATOR."tempfile".DIRECTORY_SEPARATOR.$dir;
		
		//创建缓存目录
		if( !is_dir($directory) || !is_writable($directory) ){
			//0700 或者最高权限0777
			if( CACHE_PATH == '' || !mkdir($directory, 0777, true) ){
				return false;
			}
		}
		
		//获得缓存目录
		$tempfile_path = $directory.DIRECTORY_SEPARATOR.cmd(array(24), 'random string').'.'.$suffix;
		//析构
		destruct('cache.tempfile.clear:'.$tempfile_path, true, array($tempfile_path), function($tempfile_path){
			//如果是文件则删除
			if( is_file($tempfile_path) ){
				unlink($tempfile_path);
			}
		});
		
		return $tempfile_path;
	}
	
	
	
	
	
	
	
	
	
	
}
?>