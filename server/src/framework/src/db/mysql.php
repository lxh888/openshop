<?php
namespace framework\src\db;
use framework\cao as cao;
final class mysql extends cao {
	
	
	/* 数据库的连接、操作类 */
	
	
	/**
     * 当前的连接标识
     *
     * @var string
     */
	static private $_id = NULL;
	
	
	
	
	/**
	 * 当前的连接资源
	 * 
	 * @var array
	 */
	static private $_resource = array();	
	
	
	
	/**
	 * 当前的实例化对象
	 * 
	 * @var array
	 */
	static private $_new = NULL;
	
	
	/**
	 * 返回当前实例化 
	 * 
	 * @param	void
     * @return	object
	 */
	static private function _register(){
		if(self::$_new === NULL){
			$db = parent::db();
			self::$_new = $db['class'][__CLASS__]['register'];
			}
		return self::$_new;
		}
	
	
	
	
	/**
	 * 处理错误信息
	 * 
	 * @param	bool			$message				错误信息
	 * @param	bool			$alert					是否报错。默认false 不报错。否则报错
	 * @param	array			$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @return	void
	 */
	static private function _error($message = '', $alert = false, $register_backtrace ){
		//获取错误信息
		$sort = count(self::$_resource[self::$_id]['runtime'])-1;//获取排序
		self::$_resource[self::$_id]['error'][$sort][] = array(
			'location' => parent::_location_( $register_backtrace ),
			'message' => $message
			);
		self::$_resource[self::$_id]['last_error'] = $message;
		//默认false 不报错。否则报错
		if( !empty($alert) ){
			parent::_error_message_(
				$message,
				$register_backtrace
				);
			}
		}
	
	
	
	
	
	
	/**
	 * 获取缓存目录
	 * 
	 * @param	string	$folder_name			子文件夹名称
	 * @param	array	$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @return	string
	 */
	static private function _cache_dir($folder_name, $register_backtrace){
		//判断缓存目录的文件夹名称
		if( empty(self::$_resource[self::$_id]['config']['cache_folder_name']) ||
		!is_string(self::$_resource[self::$_id]['config']['cache_folder_name'])
		 ){
			self::_error(parent::language('db_cache_folder_name_illegal'), true, $register_backtrace);
		}
		
		//拼凑缓存主目录路径
		$directory = CACHE_PATH.DIRECTORY_SEPARATOR.
		self::$_resource[self::$_id]['config']['cache_folder_name'].DIRECTORY_SEPARATOR.
		self::$_resource[self::$_id]['config']['type'].DIRECTORY_SEPARATOR.
		$folder_name.DIRECTORY_SEPARATOR;
		
		return $directory;
	}
	
	
	
	/**
	 * 获取并生成缓存目录
	 * 
	 * @param	string	$folder_name			子文件夹名称
	 * @param	array	$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @return	string
	 */
	static private function _cache_mkdir($folder_name, $register_backtrace){
		//获取缓存目录
		$directory = self::_cache_dir($folder_name, $register_backtrace);
		self::_mkdir(
			$directory, 
			true, 
			parent::language('db_cache_dir_mkdir', $directory),
			$register_backtrace
		);
		
		return $directory;
	}
	
	
	
	
	/**
	 * 创建目录
	 * 
	 * @param	string		$directory			要创建的目录
	 * @param	bool		$is_alert			错误时，是否弹出错误。默认false,返回布尔值。为true会报错
	 * @param	string		$error_message		发生错误时的信息
	 * @param	array		$register_backtrace	函数名称、类名称、类方法名称，用于查找注册位置
	 * @return 
	 */
	static private function _mkdir($directory, $is_alert = false, $error_message = '', $register_backtrace = array()){
		//清除目录文件状态缓存
		clearstatcache(true, $directory);
		if( !is_dir($directory) || !is_writable($directory) ){
			//0700 或者最高权限0777
			if( CACHE_PATH == '' || !mkdir($directory, 0777, true) ){
				if( empty($is_alert) ){
					return false;
				}else{
					//处理错误信息
					self::_error($error_message, true, $register_backtrace);
					}
				}
			}
		
		return true;
	}
	
		
	
	/**
	 * 获取文件的内容，并且进行反序列化
	 * 
	 * @param	string		$path_file		文件地址
	 * @return	mixed
	 */
	static private function _file_unserialize($path_file){
		//清除文件状态缓存
		clearstatcache(true, $path_file);
		if( !is_file($path_file) || !is_writable($path_file) ){
			return false;
		}	
		$contents = unserialize(file_get_contents($path_file));
		if( $contents === false ){
			return false;//返回
		}else{
			return $contents;
		}
		
		/*$default_error_config = parent::config('error');//记录默认配置
		$error_config = $default_error_config;//当前配置
		//关闭错误
		$error_config['start'] = false;
		$error_config['catch'] = false;
		parent::config('error', $error_config);
		parent::config(true);//更新
		if( !@$fh = fopen($path_file, 'r') ){
			//还原配置
			config('error', $default_error_config);
			config(true);
		 	return false;//返回
		}
		
		$contents = @unserialize( fread($fh, filesize($path_file)) );
		//还原配置
		parent::config('error', $default_error_config);
		parent::config(true);
		fclose($fh);//关闭文件指针
		if( $contents === false ){
			return false;//返回
		}else{
			return $contents;
		}*/
	}
	
	
	
	
	
	/**
	 * 返回连接资源 
	 * 
	 * @param  	array	$config	配置信息
	 * @param	array	$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
     * @return void
	 */
	static private function _connect( $config, $register_backtrace ){
		//先关闭存在的链接，在创建新链接
		if( !empty(self::$_resource[self::$_id]['register']) ){
			self::_close($register_backtrace);
			}
		
		//链接标识
		self::$_resource[self::$_id]['id'] = self::$_id;
		//注册时的配置
		self::$_resource[self::$_id]['config'] = $config;
		
		//检查锁
		self::_lock_check_validity('database', $register_backtrace);
		
		//检查数据库名称
		if( empty($config['base']) ){
			//处理错误信息
			self::_error(parent::language('db_base_exists'), true, $register_backtrace);
		}
		//检查默认字符编码
		if( empty($config['charset']) ){
			self::_error(parent::language('db_charset_exists'), true, $register_backtrace);
		}
		
		//获取排序
		$sort = count(self::$_resource[self::$_id]['runtime'])-1;
		
		//注册链接(主机,用户名,密码,数据库名,端口名)
		self::$_resource[self::$_id]['register'] = mysqli_connect(
		( empty($config['persistent'])? $config['host'] : 'p:'.$config['host'] ), //判断是否是持久链接
		$config['user'], 
		$config['pass'], 
		'', 
		$config['port']
		);
		
		//如果存在错误	
		if( empty(self::$_resource[self::$_id]['register']) ){
		 	//处理错误信息
		 	self::_error( parent::language('db_connect', cmd(array(mysqli_connect_errno()), 'str charset'), cmd(array(mysqli_connect_error()), 'str charset') ), true, $register_backtrace );
        }else{
        	
        	//防止有错误多次循环进来，所以要判断else
				
			//选择数据库
			//初始连接时，数据库不存在则自动创建
			if( !empty($config['base_no_exists_create']) ){
				if( !mysqli_query(self::$_resource[self::$_id]['register'], 'CREATE DATABASE IF NOT EXISTS `'.$config['base'].'`;') ){
				//处理错误信息
				self::_error( parent::language('db_create_base', $config['base'], mysqli_error(self::$_resource[self::$_id]['register']) ), true, $register_backtrace );
				}
			}	
				
			//选择数据库
			if( !mysqli_select_db( self::$_resource[self::$_id]['register'], $config['base']) ){
				//处理错误信息
				self::_error(parent::language('db_base_select', mysqli_error(self::$_resource[self::$_id]['register']) ), true, $register_backtrace);
			}
				
			//设置访问数据库的字符集
			if( !mysqli_set_charset(self::$_resource[self::$_id]['register'], $config['charset']) ){
				//处理错误信息
				self::_error( parent::language('db_charset_set', $config['charset'], mysqli_error(self::$_resource[self::$_id]['register']) ), true, $register_backtrace );
				}
			
			//链接频率
			if( !isset(self::$_resource[self::$_id]['frequency']) ){
				self::$_resource[self::$_id]['frequency'] = 0;
				}
			self::$_resource[self::$_id]['frequency'] ++;
			
			//获取注册位置
			self::$_resource[self::$_id]['location'] = parent::_location_( $register_backtrace );
			
			//自动关闭连接和自动备份数据
			parent::destruct('db:'.__CLASS__, function(){
				//printexit(destruct());
				call_user_func_array(array(__CLASS__, 'destruct'), array());
				});	
				
        	}
		
		
		}
	
	
	
	/**
	 * 关闭存在的链接资源
	 * 
	 * @param  void
     * @return void
	 */
	static private function _close(){
		//先检查需要回滚的数据
		self::_work_rollback();
		//再检查是否存在锁表，如果存在key，并与文件相同，那么就要关闭
		self::_lock_close();
		//如果还有连接。就关闭连接	
		if( !empty(self::$_resource[self::$_id]['register']) ){
			mysqli_close( self::$_resource[self::$_id]['register'] );
			self::$_resource[self::$_id]['register'] = null;
			}
		}
	
	
	
	
	
	/**
	 * 返回当前连接资源
	 * 
	 * @param	array 		$config					配置
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @return	resource
	 */
	static private function _link($register_backtrace){
		
		//先ping 判断链接是否超时
		/*if( !empty( self::$_resource[self::$_id]['register'] ) && !mysqli_ping(self::$_resource[self::$_id]['register']) ){
			//在这里就开始关闭，如果使用self::_close()会影响锁表和事务
			mysqli_close( self::$_resource[self::$_id]['register'] );
			unset(self::$_resource[self::$_id]['register']);
			}*/
			
		if( !isset( self::$_id ) || !isset( self::$_resource[self::$_id]['register'] ) ){
			self::_config_(
				(isset( self::$_id )? self::$_id : ''), 
				(isset( self::$_resource[self::$_id]['config'] )? self::$_resource[self::$_id]['config'] : parent::config('db')), 
				null, 
				$register_backtrace
				);
			}
		
		return self::$_resource[self::$_id]['register'];
		}
	
	
	
	/**
	 * 搜集方法的执行信息 
	 * 
	 * @param	array		$log	日志信息
	 * 
	 */
	static private function _method_log( $log = array() ){
		//初始化日志信息
		if( empty($log) ){
			if(empty(self::$_resource[self::$_id]['method_log'])){
				self::$_resource[self::$_id]['method_log'] = array();
				}
		}else{
			//获取当前排序的运行时间
			self::$_resource[self::$_id]['runtime'][$log['sort']] += $log['runtime'];
			//判断是否记录query语句信息。
			if( !empty(self::$_resource[self::$_id]['config']['method_log']) ){
				self::$_resource[self::$_id]['method_log'][] = $log;
				}
			}
		}
	
	
	
	
	/**
	 * 搜集 query 日志信息
	 * 
	 * @param	array		$log	日志信息	
	 */
	static private function _query_log( $log = array() ){
		//初始化日志信息
		if( empty($log) ){
			if( empty(self::$_resource[self::$_id]['query_log']) ){
				self::$_resource[self::$_id]['query_log'] = array();
				}
		}else{
			//判断是否记录query语句信息。
			if( !empty(self::$_resource[self::$_id]['config']['query_log']) ){
				self::$_resource[self::$_id]['query_log'][] = $log;
				}
			}
		}
	
	
	/**
	 * 标准的自动识别的执行一个sql语句
	 * 如果$res是布尔值，为真时，如果有产生AUTO_INCREMENT的自增值，则返回该值增值，否则返回1
	 * 为假时，如果数据没有变化。返回0
	 * 如果结果是资源，当$type是false，则返回一个索引数组(默认)；当$type是true，则返回一个对象集。
	 * 用法：
	 * _query(sql, true) 可以传一个布尔值，如果是数据，会根据布尔值返回不同数据类型。
	 * _query(sql) 就一个sql字符串语句
	 * 
	 * @param  string		$sql					sql语句
	 * @param  bool			$type					资源返回类型。默认false 返回一个索引数组。否则返回一个对象集数组。
	 * @param  bool			$alert					是否报错。默认false 不报错。否则报错
	 * @param  array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @return mixed
	 */	
	static private function _query($sql, $type = false, $alert = false, $register_backtrace){
		//检查锁
		self::_lock_check_validity('database', $register_backtrace);
		
		//执行时间开始
		$microtime = microtime(true);
		//获取注册位置;获取sql语句
		$resource = mysqli_query(self::_link($register_backtrace), $sql);
		if( !$resource ){
			//处理错误信息
			self::_error($sql.' - SQL语句执行出错 - '.mysqli_error(self::_link($register_backtrace)), $alert, $register_backtrace);
			$return_data = null;//有错误
			
		}else{
			
			
			if( is_bool($resource) ){
				//如果没有产生AUTO_INCREMENT的值，则返回0。如果大于0，则表示产生，才赋值
				if( mysqli_affected_rows( self::_link($register_backtrace) ) > 0 ){
				   	$state = mysqli_insert_id( self::_link($register_backtrace) );//取得上一步INSERT操作产生的ID(AUTO_INCREMENT 的值)
				   	if( empty($state) ){
				   		$return_data = true;//操作成功，返回true
				   	}else{
				   		$return_data = $state;//操作成功
				   		} 
			   	}else{
					$return_data = false;//如果数据没有变化。操作没有影响到行数，或不存在
				   	}
				
			}else{
				
				
				$return_data = array();//定义一个空数组。搜集结果
				if( empty($type) ){
					while( $assoc = mysqli_fetch_assoc($resource) ){
						$return_data[] = $assoc;//系统会以0开始自动编写$array[]数组的key编码
						}
					mysqli_free_result($resource);//这里马上释放$resource内存资源。释放资源(必须)
				}else{
					while( $object = mysqli_fetch_object($resource) ){
						$return_data[] = $object;//系统会以0开始自动编写$array[]数组的key编码
						}
					mysqli_free_result($resource);//这里马上释放$resource内存资源。释放资源(必须)
					}
				
				
				}
			
			
		}
		
			
		//记录日志信息
		self::_query_log(array(
		'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
		'sql' => $sql,
		'location' => parent::_location_( $register_backtrace ),
		'action' => (is_array($return_data)? (empty($type)? 'query array' : 'query object') : 'query affected'),
		'resource' => (empty($return_data)? false : true),
		'error' => (empty(self::$_resource[self::$_id]['last_error'])? '' : self::$_resource[self::$_id]['last_error']),
		'runtime' => (microtime(true)-$microtime)
		));
		
		return $return_data;
		}	
	
	
	
	
	
	
	/**
     * 配置信息的参数值
	 * $closure	闭包函数。获取当前连接资源信息：
	 * 		[key] 链接键
	 * 		[register]连接注册资源	resource
	 * 		[location]注册的位置信息	string
     * 		[config]连接资源配置信息	array
	 * 		[frequency]连接频率	int
	 * 		[last_error]获取最后一条错误信息	string
	 * 		[last_sql]获取最后执行的一条sql语句	string
	 * 		[query]获取拼凑字符串结果:[sql]拼凑的SQL语句[runtime]拼凑的运行时间[field]获得数据表字段
	 * 
	 * 用法:
	 * config('key') 这是传入一个key键，标识调用所属key键的配置
	 * config('key',array()) 这是传入一个key键，和一个数组类型的配置
	 * config('key',array(), true) 传入一个布尔型，代表是否覆盖
     *
	 * @param	string		$identify				链接键,链接标识(支持链接多个不同地址的数据库)。默认为空
     * @param	array		$config					配置
	 * @param	bool		$coverage				是否覆盖已存在链接。默认false不覆盖，否则true覆盖。是否重新链接。
	 * @param	closure		$closure				闭包函数
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @return	object
     */
	static protected function _config_($identify, $config, $coverage = false, $register_backtrace = array()){
		//获取当前链接键
		self::$_id = $identify;
		//这是定义所有的运行时间。其中键名称是排序号，是代表排序下每一条sql语句的时间。
		self::$_resource[self::$_id]['runtime'][] = 0;//每次使用db(...) 一次将会生成一次新的排序
		self::$_resource[self::$_id]['query'] = array();//这个只保留排序的sql平凑数据，每次使用db(...)将会更新
		//判断并获得当前链接资源
		if( empty(self::$_resource[$identify]['register']) || !empty($coverage) ){
			call_user_func_array( array(__CLASS__, '_connect'), array($config, $register_backtrace ) );
			}
		self::_method_log();//获得所有排序的
		self::_query_log();//获得所有排序的
		
		return self::_register();
		}
	
		
		
	
	
	
	/**
	 * 自动关闭数据库连接
	 * 999999997:__CLASS__
	 * 
	 * @param  void
     * @return void
	 */
	static public function destruct(){
		if( !empty(self::$_resource) && is_array(self::$_resource) ){
			foreach(self::$_resource as $identify => $value){
				self::$_id = $identify;
				self::_close();
				//unset( self::$_resource[$identify] ); 关闭不删除，参数扩展用
				}
			}
		}
		
		
	
	
	/**
	 * 获取资源
	 * 
	 * @param  void
     * @return array
	 */
	static public function resource(){
		return self::$_resource;
		}
				
		
	/**
	 * 获取当前标识的资源
	 * 
	 * @param  void
     * @return array
	 */
	static public function info(){
		return self::$_resource[self::$_id];
		}


	
	
	/**
	 * 关闭当前标识的链接资源
	 * 先检查需要回滚的数据
	 * 再检查是否存在锁表，如果存在key，并与文件相同，那么就要关闭
	 * 
	 * @param  void
     * @return void
	 */
	static public function close(){
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		if( !empty(self::$_resource[self::$_id]['register']) ){
			self::_close($register_backtrace);
			}
	}
	
	
	
	
	
	/**
	 * 标准的自动识别的执行一个sql语句
	 * 如果$res是布尔值，为真时，如果有产生AUTO_INCREMENT的自增值，则返回该值增值，否则返回1
	 * 为假时，如果数据没有变化。返回0
	 * 如果结果是资源，当$type是false，则返回一个索引数组(默认)；当$type是true，则返回一个对象集。
	 * 用法：
	 * query(sql, true) 可以传一个布尔值，如果是数据，会根据布尔值返回不同数据类型。
	 * query(sql) 就一个sql字符串语句
	 * 
	 * @param  string		$sql					sql语句
	 * @param  bool			$type					资源返回类型
	 * @param  array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @param  closure		$closure				闭包函数
	 * @return mixed
	 */
	static public function query(){
		
		//执行时间开始
		$microtime = microtime(true);
		$func_get_args = parent::_args_( func_get_args() );
		$sql = isset( $func_get_args['string'][0] )? $func_get_args['string'][0] : '';	
		$type = isset( $func_get_args['boolean'][0] )? $func_get_args['boolean'][0] : false;
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//执行SQL返回结果
		$return_data = self::_query($sql, $type, true, $register_backtrace);
		
		//如果闭包函数存在
		if(!empty($closure)){
			$closure_return_data = call_user_func_array($closure, array($return_data, self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,//获取当前排序
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),//获取sql执行代码位置
			'runtime' => (microtime(true)-$microtime),//执行时间
			'args' => array($sql, $type),
			'sql' => null,
			));
			
		/*根据情况返回*/
		if( isset($closure_return_data) ){
			return $closure_return_data;//如果闭包函数返回值存在，则返回这个闭包函数返回值
			}else{
				return $return_data;
				}	
		}
	
	
	
	
	
	/**
	 * 动态给方法传入参数
	 * 只对 'joinon','where','having','data'... 方法有效
	 * 
	 * @param	string		$method_name
	 * @param	array		$method_tags
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @param	closure		$closure				闭包函数
	 */
	static public function call(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$method_name = isset( $func_get_args['string'][0] )? $func_get_args['string'][0] : '';
		$method_tags = isset( $func_get_args['array'][0] )? $func_get_args['array'][0] : array();
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//检测方法
		if( empty($method_name) ){
			//处理错误信息 
			self::_error(parent::language('db_call_method_empty'), true, $register_backtrace);
			}
		
		//处理数据
		//先转为小写，再清理两边空白
		$method_name = trim(strtolower($method_name));
		
		//能使用的
		$methods = array('joinon','where','having','data','orderby','groupby','table','limit');
		if( !in_array($method_name, $methods) ){
			//处理错误信息  
			self::_error(parent::language('db_call_method_illegal', $method_name), true, $register_backtrace);
			}
		
		//执行方法
		call_user_func_array(array(__CLASS__, $method_name ), $method_tags);
		
		//如果闭包函数存在
		if(!empty($closure)){
			call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => array($method_name, $method_tags),
			'sql' => null,
			));
			
		return parent::_response_(self::_register(), $closure, array(self::$_resource[self::$_id]) );
		}





	/**
	 * 清除参数
	 * clear('data') 清除一条数据。清除[query][data]数据
	 * clear('data','table',...) 清除多条数据
	 * 
	 * @param	string		$query_name
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @param	closure		$closure				闭包函数
	 */
	static public function clear(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$method_name = isset( $func_get_args['string'][0] )? $func_get_args['string'][0] : '';
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//指定清除
		if( !empty($func_get_args['string']) ){
			foreach($func_get_args['string'] as $key => $value){
				if( isset(self::$_resource[self::$_id]['query'][$value]) ){
					unset(self::$_resource[self::$_id]['query'][$value]);//存在则删除
					}
				}
		}else{
			self::$_resource[self::$_id]['query'] = array();//为空则代表清空
			}
		
		//如果闭包函数存在
		if(!empty($closure)){
			call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => $func_get_args['string'],
			'sql' => null,
			));
			
		return parent::_response_(self::_register(), $closure, array(self::$_resource[self::$_id]) );
		}








	/**
	 * 选择数据库
	 * 
	 * @param	string		$base_name				数据库名称
	 * @param	bool		$create					为true则表示要执行不存在则创建。默认false，只选择存在的数据库。
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @param	closure		$closure				闭包函数
	 * @return	object
	 */
	static public function base(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$base_name = isset( $func_get_args['string'][0] )? $func_get_args['string'][0] : '';	
		$create = isset( $func_get_args['boolean'][0] )? $func_get_args['boolean'][0] : false;
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//为true则表示要执行不存在则创建。默认false，只选择存在的数据库。
		if( $base_name != '' && !empty($create) ){
			self::_query('CREATE DATABASE IF NOT EXISTS `'.$base_name.'`;', null, true, $register_backtrace);
			}
		
		//开始设置
		if( $base_name == '' ){
			//处理错误信息
			self::_error(parent::language('db_base_exists'), true, $register_backtrace);
			}
		
		if( !mysqli_select_db( self::_link($register_backtrace), $base_name) ){
			//处理错误信息
			self::_error(parent::language('db_base_select', mysqli_error(self::_link($register_backtrace)) ), true, $register_backtrace);
			}
		
		
		//将配置信息也修改了
		self::$_resource[self::$_id]['config']['base'] = $base_name;
		
		//如果闭包函数存在
		if(!empty($closure)){
			call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => $base_name,
			'sql' => null,
			));
		
		return parent::_response_(self::_register(), $closure, array(self::$_resource[self::$_id]) );
		}
	 
	
	
	
	/**
	 * 设置默认字符编码 
	 * 
	 * @param	string		$charset				编码
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @param	closure		$closure				闭包函数
	 * @return	object
	 */
	static public function charset(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$charset = isset( $func_get_args['string'][0] )? $func_get_args['string'][0] : '';	
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//开始设置
		if( $charset == '' ){
			//处理错误信息
			self::_error(parent::language('db_charset_exists'), true, $register_backtrace);
			}
		
		//设置访问数据库的字符集
		if( !mysqli_set_charset(self::_link($register_backtrace), $charset) ){
			//处理错误信息
			self::_error( parent::language('db_charset_set', $charset, mysqli_error(self::_link($register_backtrace)) ), true, $register_backtrace );
			}
		
		//将配置信息也修改了
		self::$_resource[self::$_id]['config']['charset'] = $charset;
		
		//如果闭包函数存在
		if(!empty($closure)){
			call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => $charset,
			'sql' => null,
			));
		
		return parent::_response_(self::_register(), $closure, array(self::$_resource[self::$_id]) );
		}




	/**
	 * 设置数据表名称的前缀
	 * 
	 * @param	string		$prefix					前缀字符串
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @param	closure		$closure				闭包函数
	 * @return	object
	 */
	static public function prefix(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$prefix = isset( $func_get_args['string'][0] )? $func_get_args['string'][0] : '';	
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//将配置信息也修改了
		self::$_resource[self::$_id]['config']['prefix'] = $prefix;
		
		//如果闭包函数存在
		if(!empty($closure)){
			call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => $prefix,
			'sql' => null,
			));
		
		return parent::_response_(self::_register(), $closure, array(self::$_resource[self::$_id]) );
	}



	/**
	 * 拼凑表名称，支持多个表名。[该方法可以使用多次]
	 * ->table('table_name1 as n1','table_name2',...[function(){}])
	 * 
	 * 在 数据搜集中的显示：
	 * [query] => Array(
	 * 		[table] => Array(
	 * 				  [args] => array() 这里面就是所有的表名称
	 * 				  [sql] => '',
	 * 				  )
	 * )
	 * 
	 * @param	string		multi-					可以传入多个参数
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @param	closure		$closure				闭包函数
	 * @return	object
	 */
	static public function table(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//这是搜集数据库表名称
		if( empty(self::$_resource[self::$_id]['query']['base']) ){
			self::$_resource[self::$_id]['query']['base'] = array();
			}
		//初始化数据库表名称的子参数
		if( empty(self::$_resource[self::$_id]['query']['base'][self::$_resource[self::$_id]['config']['base']]) ){
			self::$_resource[self::$_id]['query']['base'][self::$_resource[self::$_id]['config']['base']] = array();
			}
		
		//初始化
		if( empty(self::$_resource[self::$_id]['query']['table']) ){
			self::$_resource[self::$_id]['query']['table'] = '';
		}else{
			self::$_resource[self::$_id]['query']['table'] .= ',';
			}
		
		//拼凑表名
		if( !empty($func_get_args['string']) ){
			foreach($func_get_args['string'] as $value){
				//先删除as等关键字替换成空格，不区分大小写。然后清理两边空白，最后以空格转换为数组
				$table_array = preg_split('/\s+/', trim(str_ireplace(' as ', ' ', $value)));
				//判断表名称是否存在。[0]是表名称,[1]是表别名
				if( empty($table_array[0]) ){
					continue;
					}
				//获得表名称参数,不存在才获取
				if( !in_array(self::$_resource[self::$_id]['config']['prefix'].$table_array[0], self::$_resource[self::$_id]['query']['base'][self::$_resource[self::$_id]['config']['base']] ) ){
					self::$_resource[self::$_id]['query']['base'][self::$_resource[self::$_id]['config']['base']][] = self::$_resource[self::$_id]['config']['prefix'].$table_array[0];
					}
				
				//获取平凑的SQL语句字符串
				if( empty($table_array[1]) ){
					self::$_resource[self::$_id]['query']['table'] .=
					'`'.self::$_resource[self::$_id]['config']['base'].'`.'.'`'.self::$_resource[self::$_id]['config']['prefix'].$table_array[0].'`,';
					}else{
						self::$_resource[self::$_id]['query']['table'] .= 
						'`'.self::$_resource[self::$_id]['config']['base'].'`.'.'`'.self::$_resource[self::$_id]['config']['prefix'].$table_array[0].'` AS '.$table_array[1].',';
					}
				}
			}
		
		//截取最后的逗号，并完善语句
		if( self::$_resource[self::$_id]['query']['table'] != '' ){
			self::$_resource[self::$_id]['query']['table'] = substr(self::$_resource[self::$_id]['query']['table'], 0, -1);
			}
		
		//如果闭包函数存在
		if(!empty($closure)){
			call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => $func_get_args['string'],
			'sql' => self::$_resource[self::$_id]['query']['table'],
			));
		
		return parent::_response_(self::_register(), $closure, array(self::$_resource[self::$_id]) );
		}


	
	
	/**
	 * 是一个字符串。放在 table 之前的。[可以多次使用，最后的会覆盖之前的]
	 * from('xxxxxx') 该用法具有优先级，会覆盖 ->table() 所产生的数据
	 * 
	 * 平凑时，即：
	 * FROM ...
	 * 
	 * @param	string		$from
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @param	closure		$closure				闭包函数
	 * @return	object
	 */
	static public function from(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//初始化
		self::$_resource[self::$_id]['query']['from'] = isset( $func_get_args['string'][0] )? $func_get_args['string'][0] : '';
		
		//如果闭包函数存在
		if(!empty($closure)){
			call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,//获取当前的排序
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => $func_get_args['args'],
			'sql' => self::$_resource[self::$_id]['query']['from'],
			));
		
		return parent::_response_(self::_register(), $closure, array(self::$_resource[self::$_id]) );
		}




	
	/**
	 * 拼凑表join on语句。[该方法可以使用多次]
	 * 
	 * INNER(默认) 	|JOIN运算式：连接组合两个表中的字段记录。
	 * LEFT 	|JOIN运算式：连接组合两个表中的字段记录，并将包含了LEFT JOIN左边表中的全部记录。
	 * RIGHT 	|JOIN运算式：连接组合两个表中的字段记录，并将包含了RIGHT JOIN右边表中的全部记录。
	 * $join1['table']='config as c';//表名
	 * $join1['type']='left';//类型
	 * $join1['on']='u.id=c.user_id';//条件
	 * $join1['prefix']='user_';//可以自定义表前缀
	 * $join1['base']='blog';//可以自定义数据库 
	 * 
	 * $join2['table']='user_log as l';//表名
	 * $join2['type']='left';//类型
	 * $join2['on']='u.id=l.user_id';//条件
	 * ->joinon($join1,$join2);
	 * 
	 * @param	array		multi-					可以传入多个参数
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @param	closure		$closure				闭包函数
	 * @return object
	 */
	static public function joinon(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//这是搜集数据库表名称
		if( empty(self::$_resource[self::$_id]['query']['base']) ){
			self::$_resource[self::$_id]['query']['base'] = array();
			}
		
		//初始化
		if( empty(self::$_resource[self::$_id]['query']['joinon']) ){
			self::$_resource[self::$_id]['query']['joinon'] = '';
			}
		
		//拼凑语句
		if( !empty($func_get_args['array']) ){
			foreach($func_get_args['array'] as $value){
				
				if( !isset($value['base']) ){
					$value['base'] = self::$_resource[self::$_id]['config']['base'];//如果数据库未定义，则使用默认
					}
				
				//初始化数据库名称
				if( empty(self::$_resource[self::$_id]['query']['base'][$value['base']]) ){
					self::$_resource[self::$_id]['query']['base'][$value['base']] = array();
					}
				
				//判断表名称是否存在
				if( empty($value['table']) ){
					continue;//为空则执行下一个循环
					} 
				
				//获得表名称，先删除as等关键字替换成空格，不区分大小写。
				//然后清理两边空白，最后以空格转换为数组
				$table_array = preg_split('/\s+/', trim(str_ireplace(' as ', ' ', $value['table'])));
				//判断表名称是否存在。[0]是表名称,[1]是表别名
				if( empty($table_array[0]) ){
					continue;
					}
				
				if( empty($value['type']) ){
					$value['type'] = 'INNER';//默认类型
					}else{
						$value['type']=strtoupper($value['type']);//将字符串转化为大写
						}
					
				if( empty($value['on']) ){
					$value['on'] = '';
					}
				
				if( !isset($value['prefix']) ){
					$value['prefix'] = self::$_resource[self::$_id]['config']['prefix'];//如果表前缀未定义，则使用默认
					}
				
				//获得表名称参数,不存在才获取
				if( !in_array($value['prefix'].$table_array[0], self::$_resource[self::$_id]['query']['base'][$value['base']]) ){
					self::$_resource[self::$_id]['query']['base'][$value['base']][] = $value['prefix'].$table_array[0];
					}
				//拼凑SQL语句
				if( empty($table_array[1]) ){
					self::$_resource[self::$_id]['query']['joinon'] .= ' '.$value['type'].' JOIN `'.$value['base'].'`.`'.$value['prefix'].$table_array[0].'` ON '.$value['on'];
				}else{
					self::$_resource[self::$_id]['query']['joinon'] .= ' '.$value['type'].' JOIN `'.$value['base'].'`.`'.$value['prefix'].$table_array[0].'` AS '.$table_array[1].' ON '.$value['on'];
					}
				
				}
			self::$_resource[self::$_id]['query']['joinon'] = trim(self::$_resource[self::$_id]['query']['joinon']);
			}
		
		//如果闭包函数存在
		if(!empty($closure)){
			call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => $func_get_args['array'],
			'sql' => self::$_resource[self::$_id]['query']['joinon'],
			));
		
		return parent::_response_(self::_register(), $closure, array(self::$_resource[self::$_id]) );
		}



	
	
	/**
	 * where和having语句的拼凑
	 * 
	 * @param  array	$args	参数
	 * @return array('again', 'sql')
	 */
	static private function _where_having($args){
		$data = array(
		'hyphen' => 'AND',//默认连接符的运算字符
		'sql' => ''//默认批量获取sql语句
		);
		$i = 1;
		foreach($args as $value){
			
			if( empty($value[0]) ){
				continue;//为空也就不是数组则执行下一个循环
			}else{
				$value[0] = trim($value[0]);
				}
			//判断参数不存在则默认为空,否则数据过滤	
			if( !isset($value[1]) ){
				$value[1] = '';
				}
			
			//第3个参数($value[2])针对字符串，为true不过滤，false要过滤(默认)。
			if( !is_integer($value[1]) && !is_float($value[1]) ){
				if( empty($value[2]) ){
					$value[1] = parent::cmd(array($value[1]), 'str addslashes');//数据过滤
					}
				}
			
			/*if( isset($value[2]) ){
				$value[1] = parent::cmd(array($value[1]), 'str addslashes');//数据过滤
				if( empty($value[2]) ){
					$value[1] = '\''.$value[1].'\'';
					}
				}else
			if( !is_integer($value[1]) && !is_float($value[1]) ){
				//根据情况加单引号。不是整数和浮点数则要加单引号	
				$value[1] = '\''.parent::cmd(array($value[1]), 'str addslashes').'\'';//数据要过滤
				}*/
			
			//Array([0] => [ or] u.name!=[] [1] =>  or [2] => u.name!=[]) 
			preg_match('/^\[([\w\s]+)\][\s+]?(.*)/i', $value[0], $match);
			
			//第二个自动删除多余的，后面的自动补全为and
			if( $i == 1 ){
				//如果是增加拼凑，就不应该为空,如果不为空，第一个参数就是$where_again，那么再获得参数2
				if( !empty($match) ){
					$data['hyphen'] = strtoupper($match[1]);
					$value[0] = $match[2];
					}
				}else{
					
					//如果后面的参数为空，则补全为and,否则转为大写
					if( empty($match) ){
						$value[0] = 'AND '.$value[0];
						}else{
							$value[0] = strtoupper(trim($match[1])).' '.$match[2];
							}
						
					}
				
			/* 
			 * //无额外操作
			 * ->where(array("age=123"))
			 * //根据判断，整数和浮点数不加，字符串要加
			 * ->where(array("age=[]", 123))
			 * //加单引号
			 * ->where(array("age=[+]", 123))
			 * //不加单引号
			 * ->where(array("age=[-]", 123))
			 * */
			 
			//preg_replace 会替换转义，所以只清理空格符，用str_replace进行替换
			$value[0] = preg_replace(array(
			'/\[\s{0,}\+\s{0,}\]/i',
			'/\[\s{0,}\-\s{0,}\]/i',
			'/\[\s{0,}\]/i',
			), array('[+]', '[-]', '[]'), $value[0]);
			
			//[+]加单引号
			if( mb_strpos($value[0], '[+]') !== false ){
				$value[1] = '\''.$value[1].'\'';
				$data['sql'] .= ' '.str_replace('[+]', $value[1], $value[0]);
			}else
			//[-]不加单引号
			if( mb_strpos($value[0], '[-]') !== false ){
				$data['sql'] .= ' '.str_replace('[-]', $value[1], $value[0]);
			}else
			if( mb_strpos($value[0], '[]') !== false ){
				//不是整数也不是浮点数，则加单引号
				if( !is_integer($value[1]) && !is_float($value[1]) ){
					$value[1] = '\''.$value[1].'\'';
					}
				$data['sql'] .= ' '.str_replace('[]', $value[1], $value[0]);
			}else{
				//什么也不加
				$data['sql'] .= ' '.$value[0];
				}
			
			//printexit($value[0], $data['sql']); 
			//加单引号。先把[ + ]空格去掉，再将[+]替换成$value[1]
			/*if( preg_match('/\[\s{0,}\+\s{0,}\]/i', $value[0]) ){
				$value[1] = '\''.$value[1].'\'';
				$data['sql'] .= ' '.preg_replace('/\[\s{0,}\+\s{0,}\]/i', $value[1], $value[0]);
			}else 
			//不加单引号。先把[ - ]空格去掉，再将[-]替换成$value[1]
			if( preg_match('/\[\s{0,}\-\s{0,}\]/i', $value[0]) ){
				$data['sql'] .= ' '.preg_replace('/\[\s{0,}\-\s{0,}\]/i', $value[1], $value[0]);
			}else
			//先把[  ]空格去掉，再将[]替换成$value[1]
			if( preg_match('/\[\s{0,}\]/i', $value[0]) ){
				//不是整数也不是浮点数，则加单引号
				if( !is_integer($value[1]) && !is_float($value[1]) ){
					$value[1] = '\''.$value[1].'\'';
					}
				$data['sql'] .= ' '.preg_replace('/\[\s{0,}\]/i', $value[1], $value[0]);
				}else{
					$data['sql'] .= ' '.$value[0];
				}*/
			
			//printexit($value[1], $data['sql']);
			//$data['sql'] .= ' '.str_replace('[]', $value[1], preg_replace('/\[\s{0,}\]/i', '[]', $value[0]));
			//$data['sql'] .= ' '.preg_replace('/\[\s{0,}\]/i', $value[1], $value[0]);//这个有BUG 会把\\替换成\
			
			$i++;
			}

		return $data;
		}
	


	

	/** 
	 * 拼凑表where语句。[该方法可以使用多次] 只接受数组
	 * 要对字段和数据进行过滤
	 * ->where($where1,$where2,function($str_sql){
	 * printexit($str_sql);
	 * })
	 * ->where(array('id!=[]','123'),array('and name=[]','name'))
	 * ->where(array('[and] id=[]','123')) //这里相当于第二组了 ( '...' ) and ( '...' )
	 * 这里会判断 Lib('Db/Mysqli/query')->query['where'] 是否不会空，
	 * 如果不为空，然后检测第一个参数是否为字符串，比如 'and'，然后后面的参数则括号括起来。
	 * array('name=[]',$_POST['name']) 这是最安全的写法，$_POST['name']将会进行过滤
	 * array('name='.$_POST['name']) 这个将不会进行数据过滤
	 * 更多写法：
	 * array('name=[]',$_POST['name'],1) 解析为 name=aho 第3个参数为true不加单引号，为false(或不存在)根据情况加单引号
	 * array('[and]name=[]','aho')  解析为 and name='aho'
	 * 
	 * //根据判断，整数和浮点数不加，字符串要加
	 * ->where(array("age=[]", 123))
	 * //加单引号
	 * ->where(array("age=[+]", 123))
	 * //不加单引号
	 * ->where(array("age=[-]", 123))
	 * 
	 * 第3个参数针对字符串，为true不过滤，false要过滤(默认)。
	 * 
	 * 
	 * 
	 * @param	array		multi-					可以传入多个参数
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @param	closure		$closure				闭包函数
	 * @return object
	 */
	static public function where(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//拼凑语句
		if( !empty($func_get_args['array']) ){
			$data = self::_where_having($func_get_args['array']);
			}
		
		//获取sql语句
		if( empty($data['sql']) ){
			if( empty(self::$_resource[self::$_id]['query']['where']) ){
				self::$_resource[self::$_id]['query']['where'] = '';
				}
		}else{
			if( empty(self::$_resource[self::$_id]['query']['where']) ){
				self::$_resource[self::$_id]['query']['where'] = 'WHERE ( '.trim($data['sql']).' )';
			}else{
				self::$_resource[self::$_id]['query']['where'] .= ' '.$data['hyphen'].' ( '.trim($data['sql']).' )';
				}
			}
		
		//如果闭包函数存在
		if(!empty($closure)){
			call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => $func_get_args['array'],
			'sql' => self::$_resource[self::$_id]['query']['where'],
			));
		
		return parent::_response_(self::_register(), $closure, array(self::$_resource[self::$_id]) );
		}



	
	/**
	 * 拼凑表having语句。[该方法可以使用多次] 只接受数组。跟where类似。
	 * 要对字段和数据进行过滤
	 * ->having($having1,$having2,function($str_sql){
	 * Output($str_sql);
	 * })
	 * ->having(array('id!=[]','123'),array('[and] []=[]','name','123'))  
	 * ->having(array('[and] id=[]','123')) //这里相当于第二组了 ( '...' ) and ( '...' )
	 * 这里会判断 Lib('Db/Mysqli/query')->query['having'] 是否不会空，
	 * 如果不为空，然后检测第一个参数是否为字符串，比如 'and'，然后后面的参数则括号括起来。
	 * array('name=[]',$_POST['name']) 这是最安全的写法，$_POST['name']将会进行过滤
	 * array('name='.$_POST['name']) 这个将不会进行数据过滤
	 * 更多写法：
	 * array('name=[]',$_POST['name'],1) 解析为 name=aho 第3个参数为true不加单引号，为false(或不存在)根据情况加单引号
	 * array('[and]name=[]','aho')  解析为 and name='aho'
	 * 
	 * @param	array		multi-					可以传入多个参数
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @param	closure		$closure				闭包函数
	 * @return	object
	 */
	static public function having(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//拼凑语句
		if( !empty($func_get_args['array']) ){
			$data = self::_where_having($func_get_args['array']);
			}
		
		//获取sql语句
		if( empty($data['sql']) ){
			if( empty(self::$_resource[self::$_id]['query']['having']) ){
				self::$_resource[self::$_id]['query']['having'] = '';
				}
		}else{
			if( empty(self::$_resource[self::$_id]['query']['having']) ){
				self::$_resource[self::$_id]['query']['having'] = 'HAVING ( '.trim($data['sql']).' )';
			}else{
				self::$_resource[self::$_id]['query']['having'] .= ' '.$data['hyphen'].' ( '.trim($data['sql']).' )';
				}
			}
		
		//如果闭包函数存在
		if(!empty($closure)){
			call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => $func_get_args['array'],
			'sql' => self::$_resource[self::$_id]['query']['having'],
			));
		
		return parent::_response_(self::_register(), $closure, array(self::$_resource[self::$_id]) );
		}


	
	
	

	/**
	 * 分组[该方法可使用多次]
	 * ->groupby('u.id','s.session' [,...])
	 * 
	 * @param	string		multi-					可以传入多个参数
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @param	closure		$closure				闭包函数
	 * @return	object
	 */
	static public function groupby(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//拼凑
		$groupby = '';//搜集数据
		if( !empty($func_get_args['string']) ){
			foreach($func_get_args['string'] as $value){
				$groupby .= $value.',';
				}
			if( $groupby != '' ){
				$groupby = trim(substr($groupby, 0, -1));//去掉逗号和两边空白
				}
			}
		
		//初始化
		if( empty(self::$_resource[self::$_id]['query']['groupby']) ){
			if( $groupby != '' ){
				self::$_resource[self::$_id]['query']['groupby'] = 'GROUP BY '.$groupby;
			}else{
				self::$_resource[self::$_id]['query']['groupby'] = '';
				}
		}else{
			self::$_resource[self::$_id]['query']['groupby'] .= ','.$groupby;	
			}
		
		//如果闭包函数存在
		if(!empty($closure)){
			call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => $func_get_args['string'],
			'sql' => self::$_resource[self::$_id]['query']['groupby'],
			));
		
		return parent::_response_(self::_register(), $closure, array(self::$_resource[self::$_id]) );
		}
	
		
	
	
	
	
	/**
	 * 分组[该方法可使用多次]
	 * ->orderby('u.id asc','s.session desc' [,...])
	 * 
	 * @param	string		multi-					可以传入多个参数
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @param	closure		$closure				闭包函数
	 * @return	object
	 */
	static public function orderby(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//拼凑，搜集数据
		$orderby = '';
		
		if( !empty($func_get_args['string']) ){
			foreach($func_get_args['string'] as $value){
				$value = trim($value);
				if( $value == ''){
					continue;//跳转到下一次循环
					}
				$value = preg_replace(array('/asc$/i', '/desc$/i'), array('ASC', 'DESC'), $value);
				$orderby .= $value.',';
				}
			}
		
		if( !empty($func_get_args['array']) ){
			foreach($func_get_args['array'] as $value){
				if( empty($value[0]) || !is_string($value[0]) ){
					continue;
				}
				$value[0] = trim($value[0]);
				if( $value[0] == ''){
					continue;
					}
				if( empty($value[1]) ){
					$value[1] = 'ASC';
				}else{
					$value[1] = 'DESC';
					}
				$orderby .= $value[0].' '.$value[1].',';
				}
			
		}
		
		//去掉最后的逗号
		if( $orderby != '' ){
			$orderby = substr($orderby, 0, -1);
			}
		
		//初始化
		if( empty(self::$_resource[self::$_id]['query']['orderby']) ){
			if( $orderby != '' ){
				self::$_resource[self::$_id]['query']['orderby'] = 'ORDER BY '.$orderby;
			}else{
				self::$_resource[self::$_id]['query']['orderby'] = '';
				}
		}else{
			self::$_resource[self::$_id]['query']['orderby'] .= ','.$orderby;	
			}
		
		//如果闭包函数存在
		if(!empty($closure)){
			call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => array($func_get_args['string'], $func_get_args['array']),
			'sql' => self::$_resource[self::$_id]['query']['orderby'],
			));
		
		return parent::_response_(self::_register(), $closure, array(self::$_resource[self::$_id]) );
		}
	

	
	
	
	/**
	 * 分组[该方法只能在一次拼凑中用一次,多次使用后面的覆盖前面的]
	 * ->limit(2)
	 * ->limit(2,5)
	 * 最少1个数据，最多只会接受2个参数，并且要检测是否为数字。
	 * 
	 * @param	int			multi-					可以传入多个参数
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @param	closure		$closure				闭包函数
	 * @return	object
	 */
	static public function limit(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//初始化
		self::$_resource[self::$_id]['query']['limit'] = '';
		//拼凑
		if( !empty($func_get_args['identify']) ){
			$i = 0;//只拿2个字符
			foreach($func_get_args['identify'] as $value){
				//如果是字符串，就去掉两边的空白
				if( is_string($value) ){
					$value = trim($value);
					}
				//不合法则执行下一个循环
				if( $value === '' || !is_numeric($value) ){
					continue;
					}
				if( $i < 2 ){
					//第一个是没有连接符号逗号的
					if( $i == 0 ){
						self::$_resource[self::$_id]['query']['limit'] .= $value;
					}else{
						self::$_resource[self::$_id]['query']['limit'] .= ','.$value;
						}
					$i++;
					}
				if( $i == 2 ){
					break;//结束循环
					}
				}
			}
		
		//完善语句
		if( self::$_resource[self::$_id]['query']['limit'] != '' ){
			self::$_resource[self::$_id]['query']['limit'] = 'LIMIT '.self::$_resource[self::$_id]['query']['limit'];
			}
		
		//如果闭包函数存在
		if(!empty($closure)){
			call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => $func_get_args['identify'],
			'sql' => self::$_resource[self::$_id]['query']['limit'],
			));
		
		return parent::_response_(self::_register(), $closure, array(self::$_resource[self::$_id]) );
		}
	
	
	/**
	 * 批量操作。在insert时，使用data有效
	 * 
	 * @param	void
	 * @param	closure		$closure				闭包函数
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @return	object
	 */
	static public function batch(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//设置默认值,这里是将数据收集到数组中，不转换为字符串
		self::$_resource[self::$_id]['query']['batch'] = true;
		
		//如果闭包函数存在
		if(!empty($closure)){
			call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => NULL,
			'sql' => NULL,
			));
		
		return parent::_response_(self::_register(), $closure, array(self::$_resource[self::$_id]) );
	}
	
	
	
	
	
	/**
	 * 拼凑。[该方法可以使用多次]
	 * ->data(array('key','value',1),array('key','value',1) [,...]) 只接受数组，可以接受多个。
	 * 第三个参数为true,强制加单引号，为false强制不加单引号。不存在没有设置该参数则根据情况加单引号
	 * @param  	array		multi-					可以传入多个参数
	 * @param	closure		$closure				闭包函数
	 * @return	object
	 */
	static public function data(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//拼凑
		$temp_data = array();
		if( !empty($func_get_args['array']) ){
			foreach($func_get_args['array'] as $value){
				if( !isset($value[0]) || (!is_numeric($value[0]) && !is_string($value[0])) ){
					continue;//数据不合法则执行下一个循环
					}
				
				//不存在则是空值
				if( !isset($value[1]) ){
					$value[1] = '';
					}
				
				//或者值不合法
				if( !is_numeric($value[1]) && !is_string($value[1]) ){
					continue;//数据不合法则执行下一个循环
					}
				
				//第3个参数($value[2])针对字符串，为true不过滤，false要过滤(默认)。
				if( !is_integer($value[1]) && !is_float($value[1]) ){
					if( empty($value[2]) ){
						$value[1] = parent::cmd(array($value[1]), 'str addslashes');//数据过滤
						}
					}
				
				//printexit($value);
				/* 
				 * //无额外操作
				 * ->data(array("age=123"))
				 * //根据判断，整数和浮点数不加，字符串要加
				 * ->data(array("age=[]", 123))
				 * //加单引号
				 * ->data(array("age=[+]", 123))
				 * //不加单引号
				 * ->data(array("age=[-]", 123))
				 * */
				 
				//preg_replace 会替换转义，所以只清理空格符，用str_replace进行替换
				$value[0] = preg_replace(array(
				'/\=\s{0,}\[\s{0,}\+\s{0,}\]/i',
				'/\=\s{0,}\[\s{0,}\-\s{0,}\]/i',
				'/\=\s{0,}\[\s{0,}\]/i',
				), array('=[+]', '=[-]', '=[]'), $value[0]);
				
				//[+]加单引号
				if( mb_strpos($value[0], '=[+]') !== false ){
					$value[0] = trim(str_replace('=[+]', '', $value[0]));//key键替换并清空两边空白
					$value[0] = parent::cmd(array($value[0]), 'str addslashes');//key键过滤
					$temp_data[$value[0]] = '\''.$value[1].'\'';
				}else
				//[-]不加单引号
				if( mb_strpos($value[0], '=[-]') !== false ){
					$value[0] = trim(str_replace('=[-]', '', $value[0]));//key键替换并清空两边空白
					$value[0] = parent::cmd(array($value[0]), 'str addslashes');//key键过滤
					$temp_data[$value[0]] = $value[1];
				}else
				if( mb_strpos($value[0], '=[]') !== false ){
					$value[0] = trim(str_replace('=[]', '', $value[0]));//key键替换并清空两边空白
					$value[0] = parent::cmd(array($value[0]), 'str addslashes');//key键过滤
					//不是整数也不是浮点数，则加单引号
					if( !is_integer($value[1]) && !is_float($value[1]) ){
						$value[1] = '\''.$value[1].'\'';
						}
					$temp_data[$value[0]] = $value[1];
				}else{
					//什么也不加
					$exp_arr = explode('=', $value[0], 2);
					$exp_arr[0] = trim(str_replace('=[]', '', $exp_arr[0]));//key键替换并清空两边空白
					$exp_arr[1] = isset($exp_arr[1])? $exp_arr[1] : '\'\'';//不存在则为空
					$temp_data[$exp_arr[0]] = $exp_arr[1];
					}
				
				//$value[2]为true,强制加单引号，为false强制不加单引号。不存在没有设置该参数则根据情况加单引号
				/*if( isset($value[2]) ){
					$value[1] = parent::cmd(array($value[1]), 'str addslashes');//数据过滤
					if( !empty($value[2]) ){
						$value[1] = '\''.$value[1].'\'';
						}
					}else
				if( !is_integer($value[1]) && !is_float($value[1]) ){
					//根据情况加单引号。不是整数和浮点数则要加单引号。否则就是整数或浮点数，就不需要数据过滤和加单引号	
					$value[1] = '\''.parent::cmd(array($value[1]), 'str addslashes').'\'';//数据过滤
					}
				
				$value[0] = parent::cmd(array($value[0]), 'str addslashes');//key键过滤
				self::$_resource[self::$_id]['query']['data'][$value[0]] = $value[1];*/
				}
			}
		
		$method_log_sql = NULL;
		if( !empty($temp_data) ){
			if( empty(self::$_resource[self::$_id]['query']['batch']) ){
				//设置默认值,这里是将数据收集到数组中，不转换为字符串
				if( empty(self::$_resource[self::$_id]['query']['data']) ){
					self::$_resource[self::$_id]['query']['data'] = array();
				}
				self::$_resource[self::$_id]['query']['data'] = array_merge(self::$_resource[self::$_id]['query']['data'], $temp_data);
				$method_log_sql = self::$_resource[self::$_id]['query']['data'];
			}else{
				if( empty(self::$_resource[self::$_id]['query']['batch_data']) ){
					self::$_resource[self::$_id]['query']['batch_data'] = array();
				}
				self::$_resource[self::$_id]['query']['batch_data'][] = $temp_data;
				$method_log_sql = self::$_resource[self::$_id]['query']['batch_data'];
			}
		}
		
		
		//如果闭包函数存在
		if(!empty($closure)){
			call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => $func_get_args['array'],
			'sql' => $method_log_sql,
			));
		
		return parent::_response_(self::_register(), $closure, array(self::$_resource[self::$_id]) );
		}
	
	
	
	/**
	 * 在update或delete没有where语句时有效
	 * 
	 * @param	void
	 * @param	closure		$closure				闭包函数
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @return	object
	 */
	static public function all(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//设置默认值,这里是将数据收集到数组中，不转换为字符串
		self::$_resource[self::$_id]['query']['all'] = true;
		
		//如果闭包函数存在
		if(!empty($closure)){
			call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => NULL,
			'sql' => NULL,
			));
		
		return parent::_response_(self::_register(), $closure, array(self::$_resource[self::$_id]) );
		}
	
	
	
	
	/**
	 * 开始查询[该方法如果多次使用，后面的会覆盖前面的]
	 * ->find('u.id as uid,u*')  除开闭包函数，只接受一直参数，并且是字符串。后面的会覆盖前面的
	 * 
	 * @param	string			$select
	 * @param	bool			$type					资源返回类型
	 * @param	closure			$closure				闭包函数
	 * @param	array			$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @return	object
	 */
	static public function find(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$type = isset( $func_get_args['boolean'][0] )? $func_get_args['boolean'][0] : false;
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		
		$select = '';
		if( !empty($func_get_args['string']) ){
			foreach($func_get_args['string'] as $value){
				$value = trim(trim($value),',');
				if( $value == ''){
					continue;//跳转到下一次循环
					}
				$select .= $value.',';
			}
		}
		
		if( !empty($func_get_args['array']) ){
			//先将数组合并
			$select_array = array();
			foreach($func_get_args['array'] as $value){
				$select_array = array_merge($select_array, $value);
			}
			if( !empty($select_array) ){
				//先移除重复的值
				$select_array = array_unique($select_array);
				foreach($select_array as $value){
					$value = trim(trim($value),',');
					if( $value == ''){
						continue;//跳转到下一次循环
						}
					$select .= $value.',';
				}
			}
		}
		
		//去掉最后的逗号
		if( $select != '' ){
			$select = substr($select, 0, -1);
		}else{
			$select = '*';
			}
		
		//拼凑
		//from 相对于 table具有优先级
		if( empty(self::$_resource[self::$_id]['query']['from']) ){
			if( !isset(self::$_resource[self::$_id]['query']['table']) ){
				self::$_resource[self::$_id]['query']['table'] = '';
				}
			$from = ' FROM '.self::$_resource[self::$_id]['query']['table'];
			}else{
				$from = ' FROM '.self::$_resource[self::$_id]['query']['from'];
				}
		$joinon = empty(self::$_resource[self::$_id]['query']['joinon'])?'':' '.self::$_resource[self::$_id]['query']['joinon'];
		$where = empty(self::$_resource[self::$_id]['query']['where'])?'':' '.self::$_resource[self::$_id]['query']['where'];
		$groupby = empty(self::$_resource[self::$_id]['query']['groupby'])?'':' '.self::$_resource[self::$_id]['query']['groupby'];
		$having = empty(self::$_resource[self::$_id]['query']['having'])?'':' '.self::$_resource[self::$_id]['query']['having'];
		$orderby = empty(self::$_resource[self::$_id]['query']['orderby'])?'':' '.self::$_resource[self::$_id]['query']['orderby'];
		$limit = empty(self::$_resource[self::$_id]['query']['limit'])?' LIMIT 0,1':' '.self::$_resource[self::$_id]['query']['limit'];
		self::$_resource[self::$_id]['query']['find'] = 'SELECT '.$select.$from.$joinon.$where.$groupby.$having.$orderby.$limit;
		self::$_resource[self::$_id]['query']['sql'] = self::$_resource[self::$_id]['query']['find'].';';//获取执行SQL语句
		$sql = self::$_resource[self::$_id]['query']['sql'];//为了避免在闭包函数执行的影响
		
		//先进行锁表的检测。在执行闭包函数之前执行。有利于所不同数据库的表。
		self::_lock_check_validity('select', $register_backtrace);
		
		//如果闭包函数存在
		if(!empty($closure)){
			$closure_data = call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => array($select, $type),
			'sql' => self::$_resource[self::$_id]['query']['find'],
			));
		
		//根据情况返回
		if( isset($closure_data) ){
			return $closure_data;//如果闭包函数返回值存在，则返回这个闭包函数返回值
			}else{
				//否则正常执行
				$return_data = self::_query($sql, $type, true, $register_backtrace);//开始执行
				if( isset($return_data[0]) ){
					return $return_data[0];//返回第一组数组
					}else{
						return array();
						}
				}
			
		}
	
	
	
	
	/**
	 * 开始查询[该方法只能在一次拼凑中用一次]
	 * ->select('u.id as uid,u*')  除开闭包函数，只接受一直参数，并且是字符串。后面的会覆盖前面的
	 * 
	 * @param	string
	 * @param	closure		$closure				闭包函数
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @return	object
	 */
	static public function select(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$type = isset( $func_get_args['boolean'][0] )? $func_get_args['boolean'][0] : false;
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		$select = '';
		if( !empty($func_get_args['string']) ){
			foreach($func_get_args['string'] as $value){
				$value = trim(trim($value),',');
				if( $value == ''){
					continue;//跳转到下一次循环
					}
				$select .= $value.',';
			}
		}
		
		if( !empty($func_get_args['array']) ){
			//先将数组合并
			$select_array = array();
			foreach($func_get_args['array'] as $value){
				$select_array = array_merge($select_array, $value);
			}
			if( !empty($select_array) ){
				//先移除重复的值
				$select_array = array_unique($select_array);
				foreach($select_array as $value){
					$value = trim(trim($value),',');
					if( $value == ''){
						continue;//跳转到下一次循环
						}
					$select .= $value.',';
				}
			}
		}
		
		//去掉最后的逗号
		if( $select != '' ){
			$select = substr($select, 0, -1);
		}else{
			$select = '*';
			}
		
		//printexit($select);
		//拼凑。from 相对于 table具有优先级
		if( empty(self::$_resource[self::$_id]['query']['from']) ){
			if( !isset(self::$_resource[self::$_id]['query']['table']) ){
				self::$_resource[self::$_id]['query']['table'] = '';
				}
			$from = ' FROM '.self::$_resource[self::$_id]['query']['table'];
			}else{
				$from = ' FROM '.self::$_resource[self::$_id]['query']['from'];
				}
		$joinon = empty(self::$_resource[self::$_id]['query']['joinon'])?'':' '.self::$_resource[self::$_id]['query']['joinon'];
		$where = empty(self::$_resource[self::$_id]['query']['where'])?'':' '.self::$_resource[self::$_id]['query']['where'];
		$groupby = empty(self::$_resource[self::$_id]['query']['groupby'])?'':' '.self::$_resource[self::$_id]['query']['groupby'];
		$having = empty(self::$_resource[self::$_id]['query']['having'])?'':' '.self::$_resource[self::$_id]['query']['having'];
		$orderby = empty(self::$_resource[self::$_id]['query']['orderby'])?'':' '.self::$_resource[self::$_id]['query']['orderby'];
		$limit = empty(self::$_resource[self::$_id]['query']['limit'])?'':' '.self::$_resource[self::$_id]['query']['limit'];
		self::$_resource[self::$_id]['query']['select'] = 'SELECT '.$select.$from.$joinon.$where.$groupby.$having.$orderby.$limit;
		self::$_resource[self::$_id]['query']['sql'] = self::$_resource[self::$_id]['query']['select'].';';//获取执行SQL语句
		$sql = self::$_resource[self::$_id]['query']['sql'];//为了避免在闭包函数执行的影响
		
		//先进行锁表的检测。在执行闭包函数之前执行。有利于所不同数据库的表。
		self::_lock_check_validity('select', $register_backtrace);
		
		//如果闭包函数存在
		if(!empty($closure)){
			$closure_data = call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => array($select, $type),
			'sql' => self::$_resource[self::$_id]['query']['select'],
			));
		
		//根据情况返回
		if( isset($closure_data) ){
			return $closure_data;//如果闭包函数返回值存在，则返回这个闭包函数返回值
			}else{
				//否则正常执行
				return self::_query($sql, $type, true, $register_backtrace);//开始执行
				}
		
		}
	
	
	
	/**
	 * insert和update语句的获取data数据键值
	 * 
	 * @param  array	$args	参数
	 * @return void
	 */
	static private function _insert_update($args){
		if( empty($args) || !is_array($args)){
			return false;
		}
		$temp_data = array();
		foreach($args as $key => $value){
			if( !isset($key) || (!is_numeric($key) && !is_string($key)) ){
				continue;//数据不合法则执行下一个循环
				}
			
			//或者值不合法
			if( !is_numeric($value) && !is_string($value) ){
				continue;//数据不合法则执行下一个循环
				}
			
			//根据情况加单引号
			if( isset($value) && !is_integer($value) && !is_float($value) && $value != '' ){
				//根据情况加单引号。不是整数和浮点数则要加单引号。否则就是整数或浮点数，就不需要数据过滤和加单引号	
				$value = '\''.parent::cmd(array($value), 'str addslashes').'\'';//并且数据过滤
				}else
			if( !isset($value) || $value === '' ){
				//不存在则是空值
				$value = '\'\'';
				}
			
			$key = parent::cmd(array($key), 'str addslashes');//key键过滤
			//self::$_resource[self::$_id]['query']['data'][$key] = $value;
			$temp_data[$key] = $value;
		}
		
		if( !empty($temp_data) ){
			if( empty(self::$_resource[self::$_id]['query']['batch']) ){
				//设置默认值,这里是将数据收集到数组中，不转换为字符串
				if( empty(self::$_resource[self::$_id]['query']['data']) ){
					self::$_resource[self::$_id]['query']['data'] = array();
				}
				self::$_resource[self::$_id]['query']['data'] = array_merge(self::$_resource[self::$_id]['query']['data'], $temp_data);
			}else{
				//批量操作
				if( empty(self::$_resource[self::$_id]['query']['batch_data']) ){
					self::$_resource[self::$_id]['query']['batch_data'] = array();
				}
				self::$_resource[self::$_id]['query']['batch_data'][] = $temp_data;
			}
		}
		
	}
	
	
	
	
	/**
	 * 开始查询[该方法只能在一次拼凑中用一次] 所传入的值会根据情况加单引号。如果不想加单引号需要用->data()
	 * ->insert(array('key'=>'value','key'=>'value','key'=>'value')) 只接受一个数组。前面的数组会被后面的覆盖。其中value会被过滤
	 * 这里是具有优先级的，会覆盖->data()传入的参数
	 * 
	 * @param	array		$args					只接受一个数组参数
	 * @param	closure		$closure				闭包函数
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @return	object
	 */
	static public function insert(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		if( !empty($func_get_args['array']) ){
			foreach($func_get_args['array'] as $args){
				//获得data数据
				if( empty(self::$_resource[self::$_id]['query']['batch']) ){
					self::_insert_update($args);
				}else
				if( is_array($args) ){
					//批量
					foreach($args as $args_value){
						self::_insert_update($args_value);
					}
				}
			}
		}
		
		//获得合法性参数
		if( empty(self::$_resource[self::$_id]['query']['batch']) ){
			$data_key = '';
			$data_value = '';
			if( !empty(self::$_resource[self::$_id]['query']['data']) ){
				foreach(self::$_resource[self::$_id]['query']['data'] as $key => $value){
					$data_key .= '`'.$key.'`,';
					$data_value .= $value.',';
					}
				$data_key = substr($data_key, 0, -1);
				$data_value = substr($data_value, 0, -1);	
				}
			self::$_resource[self::$_id]['query']['keys'] = '( '.$data_key.' )';
			self::$_resource[self::$_id]['query']['values'] = '( '.$data_value.' )';
			self::$_resource[self::$_id]['query']['keys_values'] = '( '.$data_key.' ) VALUES ( '.$data_value.' )';
		}else{
			//批量处理
			$keys_string = '';
			$values_string = '';
			if( !empty(self::$_resource[self::$_id]['query']['batch_data']) ){
				$keys_array = array();
				$values_array = array();
				foreach(self::$_resource[self::$_id]['query']['batch_data'] as $batch_args){
					if( empty($batch_args) || !is_array($batch_args) ){
						continue;
					}
					$temp_value = '';
					if( empty($keys_array) ){
						foreach($batch_args as $key => $value){
							if( !isset($keys_array[$key]) ){
								$keys_string .= '`'.$key.'`,';
								$temp_value .= $value.',';
								$keys_array[$key] = $key;
							}
						}
					}else{
						
						if( count($batch_args) != count($keys_array) ){
							self::$_resource[self::$_id]['query']['batch_error'] = true;
							break;
						}
						
						//如果已经拿到 keys 以该数组排序循环
						foreach($keys_array as $keys_key => $keys_value){
							if( !isset($batch_args[$keys_key]) ){
								self::$_resource[self::$_id]['query']['batch_error'] = true;
								break;
							}else{
								$temp_value .= $batch_args[$keys_key].',';
							}
						}
					}
					
					if( $temp_value != ''){
						$values_array[] = '( '.substr($temp_value, 0, -1).' )';
					} 
					
				}
				if( $keys_string != '' ) $keys_string = substr($keys_string, 0, -1);
				if( !empty($values_array) ){
					$values_string = implode(",", $values_array);
				}
				
			}
			
			self::$_resource[self::$_id]['query']['keys'] = '( '.$keys_string.' )';
			self::$_resource[self::$_id]['query']['values'] = $values_string;
			self::$_resource[self::$_id]['query']['keys_values'] = '( '.$keys_string.' ) VALUES '.$values_string;
		}
		
		
		//拼凑。from 相对于 table具有优先级
		if( empty(self::$_resource[self::$_id]['query']['from']) ){
			if( !isset(self::$_resource[self::$_id]['query']['table']) ){
				self::$_resource[self::$_id]['query']['table'] = '';
				}
			$from = ' '.self::$_resource[self::$_id]['query']['table'];
			}else{
				$from = ' '.self::$_resource[self::$_id]['query']['from'];
				}
		
		//拼凑全部语句，判断表名称
		self::$_resource[self::$_id]['query']['insert'] = 'INSERT INTO'.$from.' '.self::$_resource[self::$_id]['query']['keys_values'];
		self::$_resource[self::$_id]['query']['sql'] = self::$_resource[self::$_id]['query']['insert'].';';//获取执行SQL语句
		$sql = self::$_resource[self::$_id]['query']['sql'];//为了避免在闭包函数执行的影响
		
		//先进行锁表的检测。在执行闭包函数之前执行。有利于所不同数据库的表。
		self::_lock_check_validity('insert', $register_backtrace);
		
		//如果闭包函数存在
		if(!empty($closure)){
			$closure_data = call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => $args,
			'sql' => self::$_resource[self::$_id]['query']['insert'],
			));
		
		//根据情况返回
		if(isset($closure_data)){
			return $closure_data;//如果闭包函数返回值存在，则返回这个闭包函数返回值
			}else{
				//这一步才开始判断 where语句是否为空，并且并非all操作
				if( !empty(self::$_resource[self::$_id]['query']['batch_error']) ){
					//处理错误信息
					self::_error(__FUNCTION__.'() 的 batch() 操作有错误，请检查插入的键值参数！', true, $register_backtrace);
					}
				
				//否则正常执行
				return self::_query($sql, NULL, true, $register_backtrace);//开始执行
				}
		
		}
	
	
	
	
	
	/**
	 * 开始查询[该方法只能在一次拼凑中用一次] 所传入的值会根据情况加单引号。如果不想加单引号需要用->data()
	 * ->update(array('key'=>'value','key'=>'value','key'=>'value')) 只接受一个数组。前面的数组会被后面的覆盖。其中value会被过滤
	 * 
	 * @param	array		$args					只接受一个数组参数
	 * @param	closure		$closure				闭包函数
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @return object
	 */
	static public function update(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//获得data数据
		$args = array();
		if( !empty($func_get_args['array']) ){
			foreach($func_get_args['array'] as $value){
				$args = array_merge($args, $value);
			}
		}
		
		//处理data数据
		if( !empty($args) ){
			self::_insert_update($args);
			}
		
		//获得合法性参数
		self::$_resource[self::$_id]['query']['set'] = '';
		if( !empty(self::$_resource[self::$_id]['query']['data']) ){
			foreach(self::$_resource[self::$_id]['query']['data'] as $key => $value){
				self::$_resource[self::$_id]['query']['set'] .= '`'.$key.'`='.$value.',';
				}
			self::$_resource[self::$_id]['query']['set'] = substr(self::$_resource[self::$_id]['query']['set'], 0, -1);
			}
		$set = self::$_resource[self::$_id]['query']['set'] == ''? '':' SET '.self::$_resource[self::$_id]['query']['set'];
		
		//拼凑。from 相对于 table具有优先级
		if( empty(self::$_resource[self::$_id]['query']['from']) ){
			if( !isset(self::$_resource[self::$_id]['query']['table']) ){
				self::$_resource[self::$_id]['query']['table'] = '';
				}
			$from = ' '.self::$_resource[self::$_id]['query']['table'];
			}else{
				$from = ' '.self::$_resource[self::$_id]['query']['from'];
				}
		$where = empty(self::$_resource[self::$_id]['query']['where'])?'':' '.self::$_resource[self::$_id]['query']['where'];
		$orderby = empty(self::$_resource[self::$_id]['query']['orderby'])?'':' '.self::$_resource[self::$_id]['query']['orderby'];
		$limit = empty(self::$_resource[self::$_id]['query']['limit'])?'':' '.self::$_resource[self::$_id]['query']['limit'];
		
		//拼凑全部语句，判断表名称
		self::$_resource[self::$_id]['query']['update'] = 'UPDATE'.$from.$set.$where.$orderby.$limit;
		self::$_resource[self::$_id]['query']['sql'] = self::$_resource[self::$_id]['query']['update'].';';//获取执行SQL语句
		$sql = self::$_resource[self::$_id]['query']['sql'];//为了避免在闭包函数执行的影响
		
		//先进行锁表的检测。在执行闭包函数之前执行。有利于锁不同数据库的表。
		self::_lock_check_validity('update', $register_backtrace);
		
		//如果闭包函数存在
		if(!empty($closure)){
			$closure_data = call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => $args,
			'sql' => self::$_resource[self::$_id]['query']['update'],
			));
		
		//根据情况返回
		if(isset($closure_data)){
			return $closure_data;//如果闭包函数返回值存在，则返回这个闭包函数返回值
			}else{
				//这一步才开始判断 where语句是否为空，并且并非all操作
				if( empty(self::$_resource[self::$_id]['query']['all']) && empty($where) ){
					//处理错误信息
					self::_error(__FUNCTION__.'() 的 where() 条件为空 ,无法执行该语句！可以使用 all() 将操作该表字段的所有记录，请慎重使用！但 all()在有 where() 条件 的情况下无效！', true, $register_backtrace);
					}
				//否则正常执行
				return self::_query($sql, NULL, true, $register_backtrace);//开始执行
				}
		
		}
	
	
	
	
	
	/**
	 * 删除数据
	 * 
	 * @param	void
	 * @param	closure		$closure				闭包函数
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置
	 * @return	object
	 */
	static public function delete(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//拼凑。from 相对于 table具有优先级
		if( empty(self::$_resource[self::$_id]['query']['from']) ){
			if( !isset(self::$_resource[self::$_id]['query']['table']) ){
				self::$_resource[self::$_id]['query']['table'] = '';
				}
			$from = ' '.self::$_resource[self::$_id]['query']['table'];
			}else{
				$from = ' '.self::$_resource[self::$_id]['query']['from'];
				}
		$where = empty(self::$_resource[self::$_id]['query']['where'])?'':' '.self::$_resource[self::$_id]['query']['where'];
		$orderby = empty(self::$_resource[self::$_id]['query']['orderby'])?'':' '.self::$_resource[self::$_id]['query']['orderby'];
		$limit = empty(self::$_resource[self::$_id]['query']['limit'])?'':' '.self::$_resource[self::$_id]['query']['limit'];
		
		//拼凑全部语句，判断表名称
		self::$_resource[self::$_id]['query']['delete'] = 'DELETE FROM'.$from.$where.$orderby.$limit;
		self::$_resource[self::$_id]['query']['sql'] = self::$_resource[self::$_id]['query']['delete'].';';//获取执行SQL语句
		$sql = self::$_resource[self::$_id]['query']['sql'];//为了避免在闭包函数执行的影响
		
		//先进行锁表的检测。在执行闭包函数之前执行。有利于锁不同数据库的表。
		self::_lock_check_validity('delete', $register_backtrace);
		
		//如果闭包函数存在
		if(!empty($closure)){
			$closure_data = call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => NULL,
			'sql' => self::$_resource[self::$_id]['query']['delete'],
			));
		
		//根据情况返回
		if(isset($closure_data)){
			return $closure_data;//如果闭包函数返回值存在，则返回这个闭包函数返回值
			}else{
				//这一步才开始判断 where语句是否为空，并且并非all操作
				if( empty(self::$_resource[self::$_id]['query']['all']) && empty($where) ){
					//处理错误信息
					self::_error(__FUNCTION__.'() 的 where() 条件为空 ,无法执行该语句！可以使用 all() 将操作该表字段的所有记录，请慎重使用！但 all()在有 where() 条件 的情况下无效！', true, $register_backtrace);
					}
				//否则正常执行
				return self::_query($sql, NULL, true, $register_backtrace);//开始执行
				}
		}
	
	
	/**
	 * 会话操作
	 * 
	 * @param	array			$data					会话需要更新的数据
	 * @param	bool | NULL		$exists					开启会话时，会话数据是否存在
	 * @param	closure			$closure				闭包函数。
	 * @param	array			$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	object
	 */	
	static public function session(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$data = isset($func_get_args['args'][0])? $func_get_args['args'][0] : NULL;
		$exists = isset($func_get_args['args'][1])? $func_get_args['args'][1] : NULL;
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//判断是更新还是开启会话
		if( empty($data) || !is_array($data) ){
			if(!is_string($data) && !is_numeric($data)){
				$data = '';
				}else{
					$data = (string)$data;
				}
			$return_data = self::_session_start($data, $exists, $register_backtrace);
		}else{
			$return_data = self::_session_update($data, $register_backtrace);
		}
		
		//如果闭包函数存在
		if(!empty($closure)){
			$closure_return_data = call_user_func_array($closure, array($return_data, self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,//获取当前排序
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),//获取sql执行代码位置
			'runtime' => (microtime(true)-$microtime),//执行时间
			'args' => empty($data)? null : $data,
			'sql' => null,
			));
			
		/*根据情况返回*/
		if( isset($closure_return_data) ){
			return $closure_return_data;//如果闭包函数返回值存在，则返回这个闭包函数返回值
			}else{
				return $return_data;
				}
		
		
	}
		
	
	/**
	 * 开启会话
	 * 
	 * @param	string			$session_id				会话id
	 * @param	bool | NULL		$exists					开启会话时，会话数据是否存在
	 * @param	array			$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	array
	 */
	static private function _session_start($session_id, $exists, $register_backtrace){
		/* 先检查配置信息是否合法 */
		
		//printexit($session_id, $exists, $register_backtrace);
		
		//检查表名称
		if( empty(self::$_resource[self::$_id]['config']['session']['table']['name']) ||
		!is_string(self::$_resource[self::$_id]['config']['session']['table']['name']) ){
			//处理错误信息
			self::_error(parent::language('db_session_table_illegal'), true, $register_backtrace);
			}
		
		//判断字段配置是否合法
		if( empty(self::$_resource[self::$_id]['config']['session']['table']['field']) || 
		!is_array(self::$_resource[self::$_id]['config']['session']['table']['field'])){
			//报错
			self::_error(parent::language('db_session_table_field_illegal'), true, $register_backtrace);
			}
		
		//表名称和表前缀拼凑
		$table_name = self::$_resource[self::$_id]['config']['session']['table']['name'];
		if( isset(self::$_resource[self::$_id]['config']['prefix']) &&
		is_string(self::$_resource[self::$_id]['config']['prefix']) ){
			$table_name = self::$_resource[self::$_id]['config']['prefix'].$table_name;
		}
		
		
		//前面指定不存在的数据，那么才检测表是否存在
		if( empty($exists) ){
		
			//检测表是否存在，不存在，再判断是否要创建
			$sql = "select TABLE_NAME from INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA='".self::$_resource[self::$_id]['config']['base']."' and TABLE_NAME='".$table_name."';";
			//有错误就报错，所以第三个参数为true
			$is_exist = self::_query($sql, false, true, $register_backtrace);
			if( empty($is_exist) ){
				//如果不存在这个表
				if( empty(self::$_resource[self::$_id]['config']['session']['found']) ){
					//不自动创建，则报错
					self::_error(parent::language('db_session_table_exists', self::$_resource[self::$_id]['config']['base'], $table_name), true, $register_backtrace);
				}else{
					//创建数据表
					self::_session_create($table_name, $register_backtrace);
				}
			}
		
		}
		
		
		//获得表字段的名称
		$field_list = self::_session_field($register_backtrace);
		
		//获取session的id值。会话id都不存在，那么数据和锁当然都为false
		if($session_id == ''){
			$session_id = self::_session_closure('id', 'string', array('db_session_id_define', 'db_session_id_illegal'), $register_backtrace);
			$exists = false;
		}
		
		//如果 $exists 为NULL，说明数据没有判断，那么还需要再自行查询判断
		if( $exists === NULL ){
			//那么查询这个会话  不要求锁。
			self::clear();//先清理数据
			self::table(self::$_resource[self::$_id]['config']['session']['table']['name']);
			self::where(array($field_list['id']."=[]", $session_id));
			$sql_find = self::find($field_list['id'], function($info){
				return $info['query']['sql'];
			});
			$exists = (bool)self::_query($sql_find, false, true, $register_backtrace);
		}
			
		//printexit($session_id, $exists);
		//数据如果不存在，那么插入新数据
		if( empty($exists) ){
			//如果数据不存在，那么就重新获取一个id
			$session_id = self::_session_closure('id', 'string', array('db_session_id_define', 'db_session_id_illegal'), $register_backtrace);
			
			//获取 失效时间
			$expire_time = self::_session_closure('expire_time', 'int', array('db_session_expire_time_define', 'db_session_expire_time_illegal'), $register_backtrace);
			
			$field_data = array();
			$field_data[$field_list['id']] = $session_id;
			$field_data[$field_list['lock']] = 0;
			$field_data[$field_list['found_time']] = time();
			$field_data[$field_list['now_time']] = time();
			$field_data[$field_list['expire_time']] = $expire_time;
			
			self::clear();//先清理数据
			self::table(self::$_resource[self::$_id]['config']['session']['table']['name']);
			self::where(array($field_list['id']."=[]", $session_id));
			$sql_insert = self::insert($field_data, function($info){
				return $info['query']['insert'];
			});
			//INSERT 中 ON DUPLICATE KEY UPDATE的使用
			$update_data = array();
			$update_data[$field_list['now_time']] = time();
			$update_data[$field_list['expire_time']] = $expire_time;
			self::clear();//先清理数据
			$sql_update = self::update($update_data, function($info){
				return $info['query']['set'];
			});
			$bool = self::_query($sql_insert.' ON DUPLICATE KEY UPDATE '.$sql_update, false, true, $register_backtrace);
			//如果失败返回假
			if( empty($bool) ){
				return false;
			}	
			
		}
		
		
		//如果更新锁失败或者查询也失败。那就循环插入并更新
		//会话锁超时时间，为0代表只要已经被锁直接返回超时信息。单位秒
		$lock_timeout_time = empty(self::$_resource[self::$_id]['config']['session']['lock_timeout_time'])? 
		0 : self::$_resource[self::$_id]['config']['session']['lock_timeout_time'];
		//获取会话锁时间，默认是不锁
		$lock_expire_time = empty(self::$_resource[self::$_id]['config']['session']['lock_expire_time'])? 
		0 : self::$_resource[self::$_id]['config']['session']['lock_expire_time'];
		
		$count_number = 0;//次数
		$total_s = 0;//累计等待的时间，单位秒
		while(true){
			$count_number ++;
			
			//获取 失效时间
			$expire_time = self::_session_closure('expire_time', 'int', array('db_session_expire_time_define', 'db_session_expire_time_illegal'), $register_backtrace);
			
			//更新当前id的session数据，设 锁=1
			self::clear();//先清理数据
			self::table(self::$_resource[self::$_id]['config']['session']['table']['name']);
			self::where(array($field_list['id']."=[]", $session_id), array('[and] '.$field_list['lock'].'=0') );
			self::where(
				array('[or] '.$field_list['id']."=[]", $session_id),
				array('[and] '.$field_list['lock'].'=1'), 
				array('[and] '.$field_list['now_time'].'<[]', time() ), //相减不能是负数的，所以要判断大小，是否够减
				//如果会话锁存在 $lock_expire_time 秒，自动覆盖。即   程序当前时间 - 会话的当前时间 > $lock_expire_time 秒 
				array('[and] ('.time().'-'.$field_list['now_time'].')>'.$lock_expire_time)
			);
			$sql_update = self::update(array(
					$field_list['lock'] => 1, 
					$field_list['now_time'] => time(),
					$field_list['expire_time'] => $expire_time
				),function($info){
					return $info['query']['sql'];
			});
			$lock_bool = (bool)self::_query($sql_update, false, true, $register_backtrace);
			
			if( empty($lock_bool) ){
				//第一次更新失败，那么查询是不是真的存在
				if($count_number == 1){
					self::clear();//先清理数据
					self::table(self::$_resource[self::$_id]['config']['session']['table']['name']);
					self::where(array($field_list['id']."=[]", $session_id));
					$sql_find = self::find($field_list['id'], function($info){
						return $info['query']['sql'];
					});
					$find = (bool)self::_query($sql_find, false, true, $register_backtrace);
					if( empty($find) ){
						//不存在，初始化失败。返回假
						return false;
					}
				}
				
				//如果修改失败，则再 $lock_timeout_time 秒内继续等待执行。直到$lock_timeout_time秒后跳出，返回false。
				if( $total_s >= $lock_timeout_time ){
					return false;
				}
				
				sleep(1);//先延迟1秒
				$total_s ++;
				
			}else{
				//修改成功， 跳出循环
				break;
			}
			
		}
			
		//获取当前id的session数据
		self::clear();//先清理数据
		self::table(self::$_resource[self::$_id]['config']['session']['table']['name']);
		self::where(array($field_list['id']."=[]", $session_id));
		$sql_find = self::find(function($info){
			return $info['query']['sql'];
		});
		$session_data = self::_query($sql_find, false, true, $register_backtrace);	
		
		//printexit($session_data);
		
		//再判断数据，如果为空那么就是报错，初始化失败
		if( empty($session_data[0]) ){
			self::_error(parent::language('db_session_start'), true, $register_backtrace);
			}
		
		//printexit($session_data);
		
		//处理特殊数据 json、serialize
		
		//json
		if( !empty($field_list['json']) ){
			foreach($field_list['json'] as $json_field){
				$session_data[0][$json_field] = $session_data[0][$json_field] == ''? 
				NULL : parent::cmd(array($session_data[0][$json_field]), 'json decode');
				if($session_data[0][$json_field] !== NULL && 
				!is_array($session_data[0][$json_field]) ){
					$session_data[0][$json_field] = NULL;
				}
			}
		}
		
		//serialize
		if( !empty($field_list['serialize']) ){
			foreach($field_list['serialize'] as $serialize_field){
				$session_data[0][$serialize_field] = $session_data[0][$serialize_field] == ''? 
				NULL : unserialize($session_data[0][$serialize_field]);
				if($session_data[0][$serialize_field] !== NULL &&
				$session_data[0][$serialize_field] === false){
					$session_data[0][$serialize_field] = NULL;
				}
			}
		}
		
		//printexit(self::$_id);
		
		//[自动更新] 标识的规则 : db.session.update:session的id
		$destruct_update_id = 'db.session.update:'.$session_id;
		//传入 配置、表名称、失效时间字段名称
		$db_id = self::$_id;
		$field_lock = $field_list['lock'];
		parent::destruct($destruct_update_id, array( self::$_resource[self::$_id]['config'] ), function($db_config) use ($db_id, $field_lock){
			$_SESSION[$field_lock] = 0;
			db($db_id, $db_config)->session($_SESSION);
		});
		
		
		//根据情况生成 自动清理 的析构
		if( empty(self::$_resource[self::$_id]['config']['session']['clear']) ){
			//没有开启自动清理，则自动返回
			return $session_data[0];
		}
		
		//[自动清理] 标识的规则 : db.session.clear:session的id
		$destruct_clear_id = 'db.session.clear:'.$session_id;
		//传入 配置、表名称、失效时间字段名称
		$args = array(self::$_resource[self::$_id]['config'], self::$_resource[self::$_id]['config']['session']['table']['name'], $field_list['expire_time']);
		$db_id = self::$_id;
		parent::destruct($destruct_clear_id, $args, function($db_config, $table_name, $expire_time) use ($db_id){
			//删除失效时间的会话数据
			db($db_id, $db_config)
			->table($table_name)
			->where(array($expire_time."<[]", time()))
			->delete();
		});
		
		return $session_data[0];
	}
	
	
	
	
	/**
	 * 更新会话
	 * 
	 * @param	array	$data				要更新的会话数据
	 * @param	array	$register_backtrace	函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	void
	 */
	static private function _session_update($data, $register_backtrace){
		//获得表字段的名称
		$field_list = self::_session_field($register_backtrace);
		
		//判断 session 的id是否存在
		if( empty($data[$field_list['id']]) || !is_string($data[$field_list['id']]) ){
			self::_error(parent::language('db_session_update_id_illegal'), true, $register_backtrace);
		}
		
		//获取白名单的字段
		$black_list = parent::cmd(array(
			//获得所有表字段
			parent::cmd(array($field_list), 'arr indexedvalue'),
			//id、创建时间剔除
			array($field_list['id'], $field_list['found_time']),
			//
			true
		), 'arr blacklist');
		
		//剔除非白名单的
		$session_data = parent::cmd(array($data, $black_list), 'arr whitelist');
		
		//更新失效时间
		$session_data[$field_list['expire_time']] = self::_session_closure('expire_time', 'int', array('db_session_expire_time_define', 'db_session_expire_time_illegal'), $register_backtrace);
		
		//当前时间
		$session_data[$field_list['now_time']] = time();
		
		//处理特殊数据 json、serialize
		
		//json
		if( !empty($field_list['json']) ){
			foreach($field_list['json'] as $json_field){
				$session_data[$json_field] = empty($session_data[$json_field])? 
				'' : parent::cmd(array($session_data[$json_field]), 'json encode');
			}
		}
		
		//serialize
		if( !empty($field_list['serialize']) ){
			foreach($field_list['serialize'] as $serialize_field){
				$session_data[$serialize_field] = empty($session_data[$serialize_field])? 
				'' : serialize($session_data[$serialize_field]);
			}
		}
		
		self::clear();//先清理数据
		self::table(self::$_resource[self::$_id]['config']['session']['table']['name']);
		self::where(array($field_list['id']."=[]", $data[$field_list['id']] ));
		$bool = self::_query(self::update($session_data, function($info){
				return $info['query']['sql'];
			}), false, true, $register_backtrace);
		
		return $bool;
	}
	
	
	/**
	 * 传入一个字段名称，获取定义的闭包函数返回值
	 * 
	 * @param	string	$field				字段名称
	 * @param	string	$data_type			限制的数据类型  string | int
	 * @param	array	$language_name		报错时返回错误信息的错误方法名称。是个索引数组，多个名称
	 * @param	array	$register_backtrace	函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	string
	 */
	static private function _session_closure($field, $data_type, $language_name, $register_backtrace){
		
		if( empty(self::$_resource[self::$_id]['config']['session'][$field]) || gettype(self::$_resource[self::$_id]['config']['session'][$field]) != 'object' || get_class(self::$_resource[self::$_id]['config']['session'][$field]) != 'Closure' ){
			//会话配置中的没有定义方法 
			self::_error(parent::language($language_name[0]), true, $register_backtrace);
			}
		$data = call_user_func_array(self::$_resource[self::$_id]['config']['session'][$field], array());
		if($data_type == 'string'){
			if( !is_string($data) || trim($data) == ''){
				//闭包函数的返回值不合法
				self::_error(parent::language($language_name[1]), true, $register_backtrace);
			}
		}else
		if($data_type == 'int'){
			if( !is_int($data) ){
				//闭包函数的返回值不合法
				self::_error(parent::language($language_name[1]), true, $register_backtrace);
			}
		}
		
		return $data;
	}
	
		
		
	/**
	 * 获取定义的字段名称
	 * 
	 * @param	array	$register_backtrace	函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	string
	 */
	static private function _session_field($register_backtrace){
		$field_list = array(
			'id' => NULL,
			'unique' => NULL,
			'lock' => NULL,
			'found_time' => NULL,
			'now_time' => NULL,
			'expire_time' => NULL,
			
			//下面是需要特殊处理的搜集
			'json' => array(),
			'serialize' => array(),
			'var' => array(),
			'state' => array()
		);
		
		foreach(self::$_resource[self::$_id]['config']['session']['table']['field'] as $field => $field_type){
			if( !is_string($field_type) ){
				continue;
			}
			$field_type = trim(strtolower($field_type));
			if( $field_type == ''){
				continue;
			}
			
			if( $field_type == 'id' ){
				$field_list['id'] = $field;
			}else
			if( $field_type == 'lock' ){
				$field_list['lock'] = $field;
			}else	
			if( $field_type == 'found_time' ){
				$field_list['found_time'] = $field;
			}else
			if( $field_type == 'now_time' ){
				$field_list['now_time'] = $field;
			}else
			if( $field_type == 'expire_time' ){
				$field_list['expire_time'] = $field;
			}else
			if( $field_type == 'unique' ){
				$field_list['unique'][] = $field;
			}else	
			if( $field_type == 'json' ){
				$field_list['json'][] = $field;
			}else
			if( $field_type == 'serialize' ){
				$field_list['serialize'][] = $field;
			}else
			if( $field_type == 'var' ){
				$field_list['var'][] = $field;
			}else
			if( $field_type == 'state' ){
				$field_list['state'][] = $field;
			}

			
		}
		
		//判断缺少值
		if( empty($field_list['id']) ){
			self::_error(parent::language('db_session_table_field_type', 'id'), true, $register_backtrace);//报错
		}else
		if( empty($field_list['lock']) ){
			self::_error(parent::language('db_session_table_field_type', 'lock'), true, $register_backtrace);//报错
		}else	
		if( empty($field_list['found_time']) ){
			self::_error(parent::language('db_session_table_field_type', 'found_time'), true, $register_backtrace);//报错
		}else
		if( empty($field_list['now_time']) ){
			self::_error(parent::language('db_session_table_field_type', 'now_time'), true, $register_backtrace);//报错
		}else
		if( empty($field_list['expire_time']) ){
			self::_error(parent::language('db_session_table_field_type', 'expire_time'), true, $register_backtrace);//报错
		}
		
		return $field_list;
	}	
		
		
	
	
	
	/**
	 * 创建会话数据表
	 * 
	 * 'id'			//[必须]session_id。创建类型是varchar(255) 。主键和唯一键索引
	 * 'expire_time'//[必须]失效的时间。创建类型是bigint(20) unsigned 默认值为0。普通索引
	 * 'found_time'	//[必须]创建时间。创建类型是bigint(20) unsigned 默认值为0。普通索引
	 * 'now_time'	//[必须]当前时间。创建类型是bigint(20) unsigned 默认值为0。普通索引
	 * 
	 * 'json'		//json数据。创建类型是text。无索引
	 * 'serialize'	//序列化数据。创建类型是text。无索引
	 * 'var' 		//可变长字符串数据。创建类型是varchar(255) 默认值为''。普通索引
	 * 'state'		//状态。创建类型是tinyint(1)
	 * 
	 * @param	string	$table_name			表名称
	 * @param	array	$register_backtrace	函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	void
	 */
	static private function _session_create($table_name, $register_backtrace){
		//获得表字段的名称
		$field_list = self::_session_field($register_backtrace);
		
		//[必须的，并且只取一个]
		$id = '';//不加逗号
		$lock = '';//前面加逗号
		$found_time = '';//前面加逗号
		$expire_time ='';//前面加逗号
		$now_time = '';//前面加逗号
		
		//[可以是多个，不是必须的]
		$var = '';//可以为多个，前面加逗号
		$text = '';//可以为多个，前面加逗号
		$state = '';//可以为多个，前面加逗号
		
		//[索引、主键]
		$primary_key = '';//前面加逗号
		$unique_key = '';//前面加逗号
		$index_key = '';//可以为多个，前面加逗号
		
		//获取主键、唯一键  注意，长度是 150
		$id = "\t`".$field_list['id']."` varchar(150) NOT NULL DEFAULT '' COMMENT '唯一ID、主键'";
		$primary_key = ",\r\n\tPRIMARY KEY (`".$field_list['id']."`)";
		$unique_key = ",\r\n\tUNIQUE KEY `".$field_list['id']."` (`".$field_list['id']."`)";
		
		//锁
		$lock = ",\r\n\t`".$field_list['lock']."` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '会话锁，同个会话将排队执行'";
		$index_key.=",\r\n\tKEY `".$field_list['lock']."` (`".$field_list['lock']."`)";
		
		//创建时间
		$found_time = ",\r\n\t`".$field_list['found_time']."` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'";
		$index_key.=",\r\n\tKEY `".$field_list['found_time']."` (`".$field_list['found_time']."`)";
		
		//当前时间
		$now_time = ",\r\n\t`".$field_list['now_time']."` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '当前时间'";
		$index_key.=",\r\n\tKEY `".$field_list['now_time']."` (`".$field_list['now_time']."`)";
		
		//失效时间
		$expire_time = ",\r\n\t`".$field_list['expire_time']."` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '失效时间'";
		$index_key .= ",\r\n\tKEY `".$field_list['expire_time']."` (`".$field_list['expire_time']."`)";
			
			
		//state
		if( !empty($field_list['state']) ){
			foreach($field_list['state'] as $state_field){
				$state .= ",\r\n\t`".$state_field."` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态数据'";
				$index_key .= ",\r\n\tKEY `".$state_field."` (`".$state_field."`)";
			}
		}
			
		//unique	
		if( !empty($field_list['unique']) ){
			foreach($field_list['unique'] as $unique_field){
				$var .= ",\r\n\t`".$unique_field."` varchar(200) DEFAULT NULL COMMENT '唯一变量数据'";
				$unique_key .= ",\r\n\tUNIQUE KEY `".$unique_field."` (`".$unique_field."`) USING BTREE";
			}
		}		
			
		//var
		if( !empty($field_list['var']) ){
			foreach($field_list['var'] as $var_field){
				$var .= ",\r\n\t`".$var_field."` varchar(200) NOT NULL DEFAULT '' COMMENT '变量数据'";
				$index_key .= ",\r\n\tKEY `".$var_field."` (`".$var_field."`)";
			}
		}
		
		//json
		if( !empty($field_list['json']) ){
			foreach($field_list['json'] as $json_field){
				$text .= ",\r\n\t`".$json_field."` text NOT NULL COMMENT 'json数据'";
			}
		}
		
		//serialize
		if( !empty($field_list['serialize']) ){
			foreach($field_list['serialize'] as $serialize_field){
				$text .= ",\r\n\t`".$serialize_field."` text NOT NULL COMMENT 'serialize数据'";
			}
		}
		
		
		$sql = "CREATE TABLE `".$table_name."` (\r\n";
		$sql .= $id . $lock . $found_time . $now_time . $expire_time . $state . $var . $text . $primary_key . $unique_key . $index_key;
		$sql .= "\r\n) ENGINE=".self::$_resource[self::$_id]['config']['session']['table']['engine']." DEFAULT CHARSET=".self::$_resource[self::$_id]['config']['charset']." COMMENT='".self::$_resource[self::$_id]['config']['session']['table']['comment']."';";
		
		//判断是否生成log文件
		if( !empty(self::$_resource[self::$_id]['config']['session']['found_log_file']) ){
			//生成缓存目录
			$directory = self::_cache_mkdir('session', $register_backtrace);
			$file_path = $directory.parent::cmd(array(22), 'random autoincrement').'.sql';
			file_put_contents($file_path, $sql, LOCK_EX);
		}
		
		//执行SQL语句
		self::_query($sql, false, true, $register_backtrace);
		//printexit($sql, self::$_resource[self::$_id]['config']['session']);
	}	
		
	
	
	/**
	 * 锁表操作。该锁对 query() 或  multi_query()等操作无效
	 * 如果锁表时间超时，则报错。
	 * 注意，该锁是阻止其他进程操作，而不会影响本次进程操作。所以又称独行锁。
	 * lock(array(type...), array(table...));
	 * $table = array(
	 * 		'数据库名称1'=> array('表名称1','表名称2'),
	 * 		'数据库名称2'=> array('表名称1','表名称2')
	 * 		);
	 * 如果是指令：
	 * ->lock('close') 关闭锁表操作
	 * 
	 * 如果是
	 * 
	 * 
	 * @param	string		$instruct				指令。
	 * @param  	array		$type					array('select'=>TRUE,'insert'=>TRUE,'update'=>TRUE,'delete'=>TRUE) 锁表类型。
	 * @param  	array		$table					被锁的表名称。默认取自 self::$_resource[self::$_id]['query']['base']
	 * @param	closure		$closure				闭包函数。
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	object
	 */
	static public function lock(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//收集锁表类型参数
		//锁表类型。FALSE为关闭状态，TRUE开启锁状态
		$args = array(
			'database' => false,//数据库操作锁
			'select' => false,//查询锁
			'insert' => false,//新增锁
			'update' => false,//更新锁
			'delete' => false//删除锁
		);
		//是否存在关闭命令，存在则需要执行关闭
		$close = false;
		
		//更新参数
		if( !empty($func_get_args['string'][0]) ){
			//先转为小写，再分割称字符串
			$str_array = str_split(strtolower($func_get_args['string'][0]));
			$args['database'] = in_array('b', $str_array);
			$args['select'] = in_array('s', $str_array);
			$args['insert'] = in_array('i', $str_array);
			$args['update'] = in_array('u', $str_array);
			$args['delete'] = in_array('d', $str_array);
			$close = in_array('c', $str_array);
			}
		
		if( $close ){
			//关闭锁表操作
			self::_lock_close($register_backtrace);
		}else{
			//开启锁表
			self::_lock_start($args, $register_backtrace);
		}
		
		//如果闭包函数存在
		if(!empty($closure)){
			call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => (empty($close)? $args : 'close'),
			'sql' => NULL,
			));
		
		return parent::_response_(self::_register(), $closure, array(self::$_resource[self::$_id]) );
		}
	
	
	
	/**
	 * 获取锁文件
	 * 
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	void
	 */
	static private function _lock_file($register_backtrace){
		//生成缓存目录
		$directory = self::_cache_mkdir('lock', $register_backtrace);
		//生成文件名称及路径
		if( empty(self::$_resource[self::$_id]['config']['lock_file_name_md5']) ){
			$path_file = $directory.self::$_resource[self::$_id]['config']['host'].'('.self::$_resource[self::$_id]['config']['port'].').serialize';
		}else{
			$path_file = $directory.
			md5(self::$_resource[self::$_id]['config']['host'].':'.self::$_resource[self::$_id]['config']['port']).
			md5(self::$_resource[self::$_id]['config']['port'].':'.self::$_resource[self::$_id]['config']['host']).
			'.serialize';
		}
		
		return $path_file;
	}
	
	
	
	/**
	 * 开启锁表操作
	 * 
	 * @param	array		$type					锁表类型
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	void
	 */
	static private function _lock_start($type, $register_backtrace){
		//获得锁表文件地址
		$path_file = self::_lock_file($register_backtrace);
		
		//检测锁表
		self::_lock_check_exist( $path_file, $register_backtrace );
		
		//不存在锁表的key,生成一个锁表id
		if( empty(self::$_resource[self::$_id]['lock']) ){
			self::$_resource[self::$_id]['lock'] = parent::cmd(array(22), 'random autoincrement');
		}
		
		//生成配置信息
		$content = array(
			'key' => self::$_resource[self::$_id]['lock'],
			'type' => $type,
			//获取数据库、表信息
			'value' => (empty(self::$_resource[self::$_id]['query']['base'])? array() : self::$_resource[self::$_id]['query']['base']),
			);
		
		//还要写入当前数据库名称
		if( empty($content['value'][self::$_resource[self::$_id]['config']['base']]) ){
			$content['value'][self::$_resource[self::$_id]['config']['base']] = array();
		}
		
		//写入文件
		/*$contents = "<?php\r\n\treturn '".parent::cmd(array(serialize($content)), 'str addslashes')."';\r\n?>";*/
		if( !file_put_contents($path_file, serialize($content), LOCK_EX) ){
			//处理错误信息
			self::_error(parent::language('db_lock_start', $path_file), true, $register_backtrace);
			}
		
		}
	
	
	
	
	/**
	 * 检测锁表文件是否存在，如果存在而key不一样则需要排队等待
	 * 
	 * @param	string		$path_file				文件
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	void
	 */
	static private function _lock_check_exist( $path_file = '', $register_backtrace){
		if( empty(self::$_resource[self::$_id]['config']['lock_time']) ){
			self::$_resource[self::$_id]['config']['lock_time'] = 0;
			}
		
		//检测是否存在锁文件
		$lock_contents = array();
		$lock_contents = self::_file_unserialize($path_file);
		
		//如果需要递归判断
		if( (isset($lock_contents['key']) && !isset(self::$_resource[self::$_id]['lock']) ) || 
		(isset($lock_contents['key']) && isset(self::$_resource[self::$_id]['lock']) && $lock_contents['key'] != self::$_resource[self::$_id]['lock']) ){
			if( (time() - filemtime( $path_file )) > self::$_resource[self::$_id]['config']['lock_time'] ){
				//lock_unlink 配置
				if( !empty(self::$_resource[self::$_id]['config']['lock_unlink']) ){
					unlink($path_file);//删除这个文件
				}
				//lock_alert 配置
				$alert = empty(self::$_resource[self::$_id]['config']['lock_alert'])? false : true;
				//处理错误信息 
				self::_error(parent::language('db_lock_timeout', $path_file), $alert, $register_backtrace);
			}else{
				usleep ( 500000 );//先延迟.5秒
				}
			
			//多余的变量值释放掉
			if( isset($lock_contents) ){
				unset($lock_contents);
				}
			return self::_lock_check_exist( $path_file, $register_backtrace );//然后再递归
			}
		
		return;
		}
	
	
	
	/**
	 * 检查锁表是否操作有效性
	 * 
	 * @param	string		$type					类型。如select、update...
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	void
	 */
	static private function _lock_check_validity($type, $register_backtrace){
		//判断是 锁数据库 还是锁 数据表。如果是锁表，表数据为空则返回
		if( $type != 'database' ){
			if( empty(self::$_resource[self::$_id]['query']['base']) ){
				return false;
			}
		}
		
		//获得锁表文件地址
		$path_file = self::_lock_file($register_backtrace);
		
		//设置超时的时间
		if( empty(self::$_resource[self::$_id]['config']['lock_time']) ){
			self::$_resource[self::$_id]['config']['lock_time'] = 0;
			}
		
		//循环判断，延迟处理
		while(true){
			
			//检测是否存在锁文件
			$lock_contents = array();
			$lock_contents = self::_file_unserialize($path_file);
			//printexit($lock_contents);
			//如果为空，则无需检测，直接返回。
			if( empty($lock_contents) || 
			//key不能为空
			empty($lock_contents['key']) || 
			//数据库、表列表信息不能为空
			empty($lock_contents['value']) ||
			//如果类型为空则表示不限制，则无需检测
			empty($lock_contents['type'][$type]) ){
				break;
				}
			
			//如果key与当前相同，则无需检测，直接返回
			if( !empty(self::$_resource[self::$_id]['lock']) && self::$_resource[self::$_id]['lock'] == $lock_contents['key'] ){
				break;
				}
			
			//依次检测 self::$_resource[self::$_id]['query']['base'] 是否与 	$lock_contents['type'][$type] 相同的表名称	
			$in = false;
			//判断是 锁数据库 还是锁 数据表
			if( $type == 'database' ){
				//先判断当前数据库是否被锁
				if( isset($lock_contents['value'][self::$_resource[self::$_id]['config']['base']]) ){
					$in = true;	
					}else
				if( !empty(self::$_resource[self::$_id]['query']['base']) ){
						//再判断联表数据库名称
						foreach(self::$_resource[self::$_id]['query']['base'] as $base_name => $table_list){
							//如果存在数据库名称，代表锁住
							if( isset($lock_contents['value'][$base_name]) ){
								$in = true;
								break 1;//跳出单层循环
								}
						}
					}
			}else{
				//self::$_resource[self::$_id]['query']['base'] 是否为空 前面已经判断了
				
				//如果是锁表
				foreach(self::$_resource[self::$_id]['query']['base'] as $base_name => $table_list){
					if( empty($lock_contents['value'][$base_name]) ){
						continue;//为空说明不存在，则没有限制无需检测，跳转到下次循环
						}
					
					foreach($table_list as $table){
						if( in_array($table, $lock_contents['value'][$base_name]) ){
							$in = true;
							break 2;//跳出双重循环
							}
						}
				}
				
			}
			
			//为true就是存在，则延迟，并且 继续循环。否则跳出循环	
			if( $in ){
				if( (time() - filemtime( $path_file )) > self::$_resource[self::$_id]['config']['lock_time'] ){
					//lock_unlink 配置
					if( !empty(self::$_resource[self::$_id]['config']['lock_unlink']) ){
						unlink($path_file);//删除这个文件
					}
					//lock_alert 配置
					$alert = empty(self::$_resource[self::$_id]['config']['lock_alert'])? false : true;
					//处理错误信息 
					self::_error(parent::language('db_lock_timeout', $path_file), $alert, $register_backtrace);
				}else{
					usleep ( 500000 );//先延迟.5秒
					}
				
			}else{
				break;
				}	
			
			}// end while


		}
	
	
	

	/**
	 * 关闭锁表操作
	 * 
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	void
	 */
	static private function _lock_close( $register_backtrace = array() ){
		if( !empty(self::$_resource[self::$_id]['lock']) ){
			
			//获得锁表文件地址
			$path_file = self::_lock_file($register_backtrace);
			
			//检测是否存在锁文件
			$lock_contents = array();
			$lock_contents = self::_file_unserialize($path_file);
			if( is_file($path_file) ){
				if( isset($lock_contents['key']) && $lock_contents['key'] == self::$_resource[self::$_id]['lock'] ){
					unlink($path_file);//删除这个文件
				}
			}
			self::$_resource[self::$_id]['lock'] = NULL;
			
			}
		
		}

	
	
	
	/**
	 * 事务操作。传入指令
	 * 在关闭链接时，就要检查 self::$_resource[self::$_id]['work'](文件地址) 存在则需要执行回滚
	 * ->work('backups') 备份回滚要恢复的数据
	 * ->work('freeze') 备份回滚要删除的数据  freeze冻结
	 * ->work('rollback') 数据回滚。手动恢复数据
	 * ->work('close') 关闭事务操作
	 * 
	 * ->work('b') 备份回滚要恢复的数据
	 * ->work('f') 备份回滚要删除的数据  freeze冻结
	 * ->work('r') 数据回滚。手动恢复数据
	 * ->work('c') 关闭事务操作
	 * 传入的字符串都要清理两边空白
	 * 
	 * @param	closure		$closure				闭包函数
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	object
	 */
	static public function work(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//开启事务操作
		self::_work_start( $register_backtrace );
		
		//多个命令同时存在执行的优先级 b>f>r>c
		//优先级概念：备份先，再回滚，最后关闭
		if( !empty($func_get_args['string'][0]) ){
			//先转为小写，再分割称字符串
			$str_array = str_split(strtolower($func_get_args['string'][0]));
			
			//备份回滚要恢复的数据
			if(in_array('b', $str_array)) self::_work_backups( $register_backtrace );
			//备份回滚要删除的数据  
			if(in_array('f', $str_array)) self::_work_freeze( $register_backtrace );
			//数据回滚。手动恢复数据
			if(in_array('r', $str_array)) self::_work_rollback( $register_backtrace );
			//关闭事务操作
			if(in_array('c', $str_array)) self::_work_close( $register_backtrace );
		}
		
		//如果闭包函数存在
		if(!empty($closure)){
			call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => (isset($func_get_args['string'][0])? $func_get_args['string'][0] : $func_get_args['array']),
			'sql' => NULL,
			));
		
		return parent::_response_(self::_register(), $closure, array(self::$_resource[self::$_id]) );
		}
	
	
	
	/**
	 * 开启事务操作
	 * 
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	void
	 */
	static private function _work_start( $register_backtrace ){
		if( !empty(self::$_resource[self::$_id]['work']) ){
			return true;//事务文件路径存在，则返回
			}
		
		//生成缓存目录
		$directory = self::_cache_mkdir('work', $register_backtrace);
		
		//生成事务文件路径
		self::$_resource[self::$_id]['work'] = $directory.parent::cmd(array(22), 'random autoincrement').'.sql';
		
		//写入文件头信息
		$content = "-- \r\n";
		$content .= "-- 备份日期：".date("Y-m-d H:i:s",time())."\r\n";
		$content .= "-- 执行信息：本次操作,是对".self::$_resource[self::$_id]['config']['base']."数据库的数据备份 \r\n";
		$content .= "-- \r\n\r\n\r\n\r\n\r\n";
		file_put_contents(self::$_resource[self::$_id]['work'], $content, FILE_APPEND | LOCK_EX);//生成这个文件
		}
	
	
	
	/**
	 * 备份回滚要恢复的数据
	 * 
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	void
	 */
	static private function _work_backups( $register_backtrace ){
		//判断表名称
		if( empty(self::$_resource[self::$_id]['query']['table']) ){
			//处理错误信息  
			self::_error(parent::language('db_work_table_exists'), true, $register_backtrace);
			}
		$where = empty(self::$_resource[self::$_id]['query']['where'])? '' : ' '.self::$_resource[self::$_id]['query']['where'];
		
		//这里将数据以1000条查询出来，备份到文件中。大于1000就循环查询几次。
		$limit = 1000;//所需要取出的条数
		$already_limit = 0;//已经取出的条数
		$i = TRUE;//是否继续循环查询数据
		while($i){
			$select_sql = 'SELECT * FROM '.self::$_resource[self::$_id]['query']['table'].$where.' LIMIT '.$already_limit.','.$limit;
			$select = self::_query($select_sql, NULL, true, $register_backtrace);
			if( empty($select) ) {
				//如果表中没有数据，则跳出该循环
				$i = FALSE;
				}else{
					
					//获得删除语句。如果是第一次，则获取删除的SQL语句
					if( empty($already_limit) ){
						$content = "\r\nDELETE FROM ".self::$_resource[self::$_id]['query']['table'].$where.";\r\n";
					}else{
						$content = '';//搜集字符串
						}
					$already_limit = $already_limit + $limit;//更新已经取出的数据
					
					/*有可能一个条件备份多条数据*/
					foreach($select as $find){
						$data_key = '';
						$data_value = '';
						foreach($find as $field_key => $field_value){
							$data_key .= '`'.$field_key.'`,';
							if( $field_value === NULL ){
								$data_value .= 'NULL,';
							}else
							if( $field_value == '' ){
								$data_value .= '\'\',';
							}else{
								$field_value = str_replace(array( "\r", "\n"), array( "\\r", "\\n"), $field_value);//换行符替换
								$data_value .= '\''.parent::cmd(array($field_value), 'str addslashes').'\',';//数据要过滤
								}
						}
						$data_key = substr($data_key, 0, -1);
						$data_value = substr($data_value, 0, -1);
						//获得新增语句
						$content .= "INSERT INTO ".self::$_resource[self::$_id]['query']['table']." ( ".$data_key." ) values ( ".$data_value." );\r\n";
						}
					
					file_put_contents(self::$_resource[self::$_id]['work'], $content, FILE_APPEND | LOCK_EX);

					
					}
			}
		
		}	
	
	
		
	
	/**
	 * 备份回滚要删除的数据  freeze 冻结
	 * 
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	void
	 */
	static private function _work_freeze( $register_backtrace ){
		//判断表名称
		if( empty(self::$_resource[self::$_id]['query']['table']) ){
			//处理错误信息
			self::_error(parent::language('db_work_table_exists'), true, $register_backtrace);
			}
		$where = empty(self::$_resource[self::$_id]['query']['where'])? '' : ' '.self::$_resource[self::$_id]['query']['where'];
		$content = "\r\nDELETE FROM ".self::$_resource[self::$_id]['query']['table'].$where.";\r\n";
		file_put_contents(self::$_resource[self::$_id]['work'], $content, FILE_APPEND | LOCK_EX);
		}	
	
	
	
	
	
	/**
	 * 数据回滚。手动恢复数据
	 * 
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	void 
	 */
	static private function _work_rollback( $register_backtrace = array() ){
		//为空或者文件不存在，则返回假
		if( !empty(self::$_resource[self::$_id]['work']) && is_file(self::$_resource[self::$_id]['work']) ){
			//导入数据。这里使用导入 import() 如果有错误，自然会报错
			$log = self::import( self::$_resource[self::$_id]['work'], true);
			self::_work_close($register_backtrace);//如果恢复成功，关闭事务操作
			//将回滚日志生成文件
			if( !empty(self::$_resource[self::$_id]['config']['work_rollback_log_file']) ){
				self::_work_rollback_log_file($log, $register_backtrace);
				}
			} 
		}
	
	
	
	/**
	 * 将回滚日志生成文件。
	 * 这里不报错。
	 * 
	 * @param  string	$log		日志信息
     * @return void
	 */
	static private function _work_rollback_log_file( $log, $register_backtrace ){
			
		//生成缓存目录
		$directory = self::_cache_mkdir('work', $register_backtrace);
		
		$file = 'work_rollback_'.date('Ymd').'.log';//根据每天来统计
		$path = $directory.DIRECTORY_SEPARATOR.$file;
		
		$br = "\r\n";//换行
		$tab = "\t";//水平制表符
		$file_string = '--'.$br;//保存到文件中的信息
		$file_string .= '执行链接：'.parent::http().$br;
		if( is_array($log) ){
			if( isset($log['start_time']) ){
				$file_string .= '开始时间：'.$log['start_time'].$br;
				}
			if( isset($log['host']) ){
				$file_string .= '主机：'.$log['host'].$br;
				}
			if( isset($log['port']) ){
				$file_string .= '端口：'.$log['port'].$br;
				}
			if( isset($log['user']) ){
				$file_string .= '用户：'.$log['user'].$br;
				}
			if( isset($log['base']) ){
				$file_string .= '数据库：'.$log['base'].$br;
				}
			if( isset($log['error_count']) ){
				$file_string .= '执行错误次数：'.$log['error_count'].$br;
				}
			if( isset($log['run_count']) ){
				$file_string .= '执行总次数：'.$log['run_count'].$br;
				}
			if( isset($log['end_time']) ){
				$file_string .= '最后时间：'.$log['end_time'].$br;
				}
			if( isset($log['runtime']) ){
				$file_string .= '执行时间：'.$log['runtime'].$br;
				}
			if( !empty($log['error']) ){
				$file_string .= '错误信息：'.print_r($log['error'], true).$br;
				}
			}
		$file_string .= '--'.$br;//保存到文件中的信息
		//printexit($error,$file_string);
		
		//FILE_APPEND 如果文件 filename已经存在，追加数据而不是覆盖
		//file_put_contents(要被写入数据的文件名,要写入的数据,flags 的值)
		file_put_contents($path, $file_string, FILE_APPEND | LOCK_EX);
		}
	
	
	
	
	
	/**
	 * 关闭事务操作
	 * 
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	void
	 */
	static private function _work_close( $register_backtrace = array() ){
		if( !empty(self::$_resource[self::$_id]['work']) ){
			if( is_file(self::$_resource[self::$_id]['work']) ){
				unlink(self::$_resource[self::$_id]['work']);//删除文件
				}
			self::$_resource[self::$_id]['work'] = NULL;
			}
		}
	
	
	
	
	/**
	 * 导入数据
	 * 
	 * @param	string		$sql					SQL字符串或者SQL文件地址
	 * @param	bool		$is_file				为false则是字符串，为true则是文件
	 * @param	closure		$closure				闭包函数。
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	object
	 */
	static public function import(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$sql = isset( $func_get_args['string'][0] )? $func_get_args['string'][0] : '';
		$is_file = isset( $func_get_args['boolean'][0] )? $func_get_args['boolean'][0] : false;
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		$return_data = array();//记录执行信息
		//头部信息
		$return_data['start_time'] = date('Y-m-d H:i:s',time());//开始时间
		$return_data['host'] = self::$_resource[self::$_id]['config']['host'];//主机
		$return_data['port'] = self::$_resource[self::$_id]['config']['port'];//端口
		$return_data['user'] = self::$_resource[self::$_id]['config']['user'];//用户名
		$return_data['base'] = self::$_resource[self::$_id]['config']['base'];//数据库
		
		if( empty($is_file) ){
			//判断sql字符串是否合法
			if( empty($sql) ){
				//处理错误信息 
				self::_error(parent::language('db_import_data_empty'), true, $register_backtrace);
				}
			self::_import_string($sql, $return_data, $register_backtrace);
		}else{
			//判断sql字符串的文件地址是否合法
			if( empty($sql) || !is_string($sql) ){
				self::_error(parent::language('db_import_file_illegal'), true, $register_backtrace);
				}
			//判断文件是否存在
			if( !is_file($sql) ){
				self::_error(parent::language('db_import_file_exists'), true, $register_backtrace);
				}
			self::_import_file($sql, $return_data, $register_backtrace);
			}
			
			
		//尾部信息
		$return_data['end_time'] = date('Y-m-d H:i:s',time());//导入完成时间
		$return_data['runtime'] = microtime(true)-$microtime;//耗费时间
		
		//如果闭包函数存在
		if(!empty($closure)){
			$closure_return_data = call_user_func_array($closure, array($return_data, self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,//获取当前排序
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),//获取sql执行代码位置
			'runtime' => (microtime(true)-$microtime),//执行时间
			'args' => null,
			'sql' => null,
			));
			
		/*根据情况返回*/
		if( isset($closure_return_data) ){
			return $closure_return_data;//如果闭包函数返回值存在，则返回这个闭包函数返回值
			}else{
				return $return_data;
				}	
		}
		
	
	
	/**
	 * 执行sql字符串
	 * 
	 * @param	string		$sql
	 * @param	array	&	$return_data
	 * @param	array		$register_backtrace
	 * @return void
	 */
	static private function _import_string($sql, &$return_data, $register_backtrace){
		//导入数据库的MySQL命令
		//$sql_contents = preg_split('/\r\n\-\-([^\r\n]+)/i', $sql);//以空格和\r\n转换为数组
		$sql_contents = preg_split('/\r\n/i', $sql);
		//printexit($sql_contents);
		$return_data['error_count'] = 0;//错误次数
		$return_data['run_count'] = 0;//执行次数
		$sql_temp = '';//临时sql
		foreach($sql_contents as $key => $line){
			
			$line = trim($line);
			//如果是注释或者为空，则跳转到下一次循环
			if ( substr($line, 0, 2) == '--' || $line == '' ){
				continue;
				}
    		//收集sql语句
			$sql_temp .= $line;
			//最后是mysql分隔符。代表是sql语句结束。并且
			if( substr($line, -1, 1) == ';' ){
				//printexit($sql_temp,$a1,$a2);
				$int = self::_query($sql_temp, null, false, $register_backtrace);
				if( $int === null ){
					$return_data['error_count'] += 1;//错误一次，加一次
					$return_data['error'][] = 'Error [ '.self::$_resource[self::$_id]['last_error'].' ] - '.$sql_temp;//错误信息
					}
				$return_data['run_count'] ++;
				$sql_temp = '';
				}

			}
		
	}
	
	
	/**
	 * 执行文件中的sql字符串
	 * 
	 * @param	string		$sql
	 * @param	array	&	$return_data
	 * @param	array		$register_backtrace
	 * @return void
	 */
	static private function _import_file($sql, &$return_data, $register_backtrace){
		//每行读取
		$resource = fopen($sql, 'rb');
		if( empty($resource) ){
            self::_error(parent::language('db_import_file_exists'), true, $register_backtrace);
            }
		
		$return_data['error_count'] = 0;//错误次数
		$return_data['run_count'] = 0;//执行次数
		$sql_temp = '';//临时sql
		
        //输出文本中所有的行，直到文件结束为止。
        //feof — 测试文件指针是否到了文件结束的位置 
        while( !feof($resource) ){
        	
        	//fgets()函数从文件指针中读取一行，并且清空两边空白
            $line = trim(fgets($resource)); 
            //如果是注释或者为空，则跳转到下一次循环
			if ( substr($line, 0, 2) == '--' || $line == '' ){
				continue;
				}
            
			//收集sql语句
			$sql_temp .= $line;
			//最后是mysql分隔符。代表是sql语句结束。并且
			if( substr($line, -1, 1) == ';' ){
				//printexit($sql_temp);
				$int = self::_query($sql_temp, null, false, $register_backtrace);
				if( $int === null ){
					$return_data['error_count'] += 1;//错误一次，加一次
					$return_data['error'][] = 'Error [ '.self::$_resource[self::$_id]['last_error'].' ] - '.$sql_temp;//错误信息
					}
				$return_data['run_count'] ++;
				$sql_temp = '';
				}
			
        }
		
		//关闭一个已打开的文件指针 
        fclose($resource);
	}
	
	
	
	
	
	/**
	 * 导出数据
	 * 数据备份类型。可选参数：
	 * export(path, 's') 备份表结构
	 * export(path, 'd') 备份表数据  
	 * export(path, 'sd')或者export(path, 'ds') 备份表结构、表数据
	 * $table 只获得里面指定的表信息，包括视图(是一维数组)。array('表名称1','表名称2','表名称3',...) 
	 * 
	 * @param	string		$path					储存SQL文件的路径地址。
	 * @param	string		$type					数据备份类型。
	 * @param	array		$table					只获得里面指定的表信息，如果为空数组，代表获取所有表信息。
	 * @param	closure		$closure				闭包函数。
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置。 
	 * @return	object
	 */
	static public function export(){
		$microtime = microtime(true);//执行时间开始
		$func_get_args = parent::_args_( func_get_args() );
		$path = isset( $func_get_args['string'][0] )? $func_get_args['string'][0] : '';
		$type = isset( $func_get_args['string'][1] )? $func_get_args['string'][1] : '';
		$table = isset( $func_get_args['array'][0] )? $func_get_args['array'][0] : array();
		$closure = isset( $func_get_args['closure'][0] )? $func_get_args['closure'][0] : NULL;	
		$register_backtrace = array('class'=>array(__CLASS__, __FUNCTION__), 'function' => __FUNCTION__);
		
		//判断储存SQL文件的路径地址是否合法
		if( empty($path) ){
			//处理错误信息
			self::_error(parent::language('db_export_path_empty'), true, $register_backtrace);
			}
		
		//判断数据备份类型
		if( empty($type) ){
			//处理错误信息
			self::_error(parent::language('db_export_type_empty'), true, $register_backtrace);
			}
		//先转为小写，再分割称字符串
		$str_array = str_split(strtolower($type));
		$type = in_array('s', $str_array)? 's' : '';
		$type .= in_array('d', $str_array)? 'd' : '';
		//最终拼接有3种情况，s、d、sd
		if( empty($type) ){
			//处理错误信息 
			self::_error(parent::language('db_export_type_illegal', implode($str_array) ), true, $register_backtrace);
			}
		
		//判断目录是否存在，不存在则创建
		$directory = dirname($path);
		self::_mkdir(
			$directory, 
			true, 
			parent::language('db_export_path_mkdir', $directory ),
			$register_backtrace
		);
		
		//写入头信息
		$content = "-- \r\n";
		$content .= "-- 备份日期：".date("Y-m-d H:i:s", time())."\r\n";
		$content .= "-- 版权所有：".FRAMEWORK_VERSION."\r\n";
		$content .= "-- 执行信息：本次操作，是对".self::$_resource[self::$_id]['config']['base']."数据库的数据备份\r\n";
		$content .= "-- \r\n\r\n\r\n\r\n\r\n";
		file_put_contents($path, $content, LOCK_EX);
		
		//获得所有视图名(返回的是一维数组)
		$version = mysqli_get_server_info( self::_link($register_backtrace) );//取得 MySQL 服务器信息(MySQL版本)
		if( version_compare($version, '5.1', '>=') ){
			//5.1版本后才有视图功能
			$view_list = self::_query('SHOW TABLE STATUS WHERE COMMENT=\'VIEW\';', null, true, $register_backtrace);
			}
		//初始化视图名列表。将二维数组转为一个索引数组，只拿出Name字段的值
		if( !empty($view_list) ){
			$view_list = parent::cmd(array($view_list, 'Name'), 'arr indexedvalue');
		}else{
			$view_list = array();
			}
		
		//获得所有表，不包括视图(返回的是一维数组)
		$table_list = self::_query('SHOW TABLES;', null, true, $register_backtrace);
		//将二维数组转为一个索引数组，并且如果存在视图，删除视图名
		if( !empty($table_list) ){
			$table_list = parent::cmd(array($table_list), array($view_list, true), 'arr indexedvalue blacklist');
			}
		
		//如果有指定的数组，那么删除没指定的表名称和视图名称
		if( !empty($table) ){
			$table_list = parent::cmd(array($table_list, $table, true), 'arr whitelist');
			$view_list = parent::cmd(array($view_list, $table, true), 'arr whitelist');
			}
		//printexit($table_list, $view_list);
		
		//如果数据表和视图为空，return 文件名路径地址
		if( empty($table_list) && empty($view_list) ){
			//如果为空，则写入空提示
			$content = "\r\n";
			$content .= "-- \r\n";
			$content .= "-- ".self::$_resource[self::$_id]['config']['base']." 数据库中,表为空！视图亦为空！\r\n";
			$content .= "-- \r\n";
			$content .= "\r\n\r\n\r\n\r\n\r\n";
			$content .= "-- \r\n";
			$content .= "-- 执行完成!\r\n";
			$content .= "-- 完成日期：".date("Y-m-d H:i:s",time())."\r\n";
			$content .= "-- 存储路径: ".$path."\r\n";
			$content .= "-- 耗费时间：".(microtime(true)-$microtime)." 秒 \r\n";
			$content .= "-- \r\n\r\n\r\n";
			file_put_contents($path, $content, FILE_APPEND | LOCK_EX);
			
		}else{
			
			//导出结构
			if( in_array($type, array('s', 'sd', 'ds')) ){
				$content = "-- \r\n";
				$content .= "-- 取消外键约束 (Mysql中如果表和表之间建立的外键约束，则无法删除表及修改表结构。)\r\n";
				$content .= "-- \r\n";
				$content .= "SET FOREIGN_KEY_CHECKS=0;\r\n\r\n\r\n\r\n\r\n";
				file_put_contents($path, $content, FILE_APPEND | LOCK_EX);
				if( !empty($table_list) ){
					self::_export_table_structure($path, $table_list, $register_backtrace);
					} 
				if( !empty($view_list) ){
					self::_export_view_structure($path, $view_list, $register_backtrace);
					} 
				} 
			
			//导出数据
			if( in_array($type, array('d', 'sd', 'ds')) && !empty($table_list) ){
				self::_export_table_data($path, $table_list, $register_backtrace);
				}
			
			//尾部信息
			$content = "\r\n\r\n\r\n\r\n\r\n";
			$content .= "-- \r\n";
			$content .= "-- 执行完成!\r\n";
			$content .= "-- 完成日期：".date("Y-m-d H:i:s",time())."\r\n";
			$content .= "-- 存储路径: ".$path."\r\n";
			$content .= "-- 耗费时间：".(microtime(true)-$microtime)." 秒 \r\n";
			$content .= "-- \r\n\r\n\r\n";
			file_put_contents($path, $content, FILE_APPEND | LOCK_EX);
			
			}
		
		//如果闭包函数存在
		if(!empty($closure)){
			call_user_func_array($closure, array(self::$_resource[self::$_id]));
			}
		
		//运行的日志信息
		self::_method_log(array(
			'sort' => count(self::$_resource[self::$_id]['runtime'])-1,
			'function' => __FUNCTION__,
			'location' => parent::_location_( $register_backtrace ),
			'runtime' => microtime(true)-$microtime,
			'args' => array($path, $type, $table),
			'sql' => NULL,
			));
		
		return parent::_response_(self::_register(), $closure, array(self::$_resource[self::$_id]) );
		}
	
	
	
	
	
	/**
	 * 导出表结构
	 * 
	 * @param	string		$path					储存SQL文件的路径地址。
	 * @param	array		$table_list				表名称
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	void 
	 */
	static private function _export_table_structure($path, $table_list, $register_backtrace){
		foreach($table_list as $value){
			$table_show = self::_query('SHOW CREATE TABLE `'.$value.'`;', null, true, $register_backtrace);
			$content = "-- \r\n";
			$content .= "-- 备份表的结构 `".$value."`\r\n";
			$content .= "-- \r\n";
			$content .= "DROP TABLE IF EXISTS `".$value."`;\r\n";
			$content = $content.parent::cmd(array($table_show[0]['Create Table']), 'html decode').";\r\n\r\n";
			file_put_contents($path, $content, FILE_APPEND | LOCK_EX);//追加到文件
			}
		}



	/**
	 * 导出视图结构
	 * 
	 * @param	string		$path					储存SQL文件的路径地址。
	 * @param	array		$view_list				视图名称
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	void 
	 */
	static private function _export_view_structure($path, $view_list, $register_backtrace){
		foreach($view_list as $value){
			$view_show = self::_query('SHOW CREATE VIEW `'.$value.'`;', null, true, $register_backtrace);
			$content = "\r\n\r\n\r\n";
			$content .= "-- \r\n";
			$content .= "-- 备份视图的结构 `".$value."`\r\n";
			$content .= "-- \r\n";
			$content .= "DROP VIEW IF EXISTS `".$value."`;\r\n";
			$content = $content.parent::cmd(array($view_show[0]['Create View']), 'html decode').";\r\n\r\n";
			file_put_contents($path, $content, FILE_APPEND | LOCK_EX);//追加到文件
			}
		}
	
	
	/**
	 * 导出表数据
	 * 
	 * @param	string		$path					储存SQL文件的路径地址。
	 * @param	array		$view_list				视图名称
	 * @param	array		$register_backtrace		函数名称、类名称、类方法名称，用于查找注册位置。
	 * @return	void 
	 */
	static private function _export_table_data($path, $table_list, $register_backtrace){
		foreach($table_list as $value){
			//这里将数据以1000条查询出来，备份到文件中。大于1000就循环查询几次。
			$limit = 1000;//所需要取出的条数
			$already_limit = 0;//已经取出的条数
			$i = TRUE;//是否继续循环查询数据
			//存在数据则写入备份的数据
			while($i){
				$select_sql = 'SELECT * FROM `'.self::$_resource[self::$_id]['config']['base'].'`.`'.$value.'` LIMIT '.$already_limit.','.$limit;
				$select = self::_query($select_sql, NULL, true, $register_backtrace);
				if( empty($select) ) {
					//如果表中没有数据，则跳出该循环，继续下一张表
					$i = FALSE;
					}else{
						
						
						//第一次要写入备注头
						if( empty($already_limit) ){
							$content = "-- \r\n";
							$content .= "-- 备份表的数据 `".$value."`\r\n";
							$content .= "-- \r\n";
							file_put_contents($path, $content, FILE_APPEND | LOCK_EX);//追加到文件
							}
						$already_limit = $already_limit + $limit;//更新已经取出的数据
						
						//写入数据。获取(返回一个数组),并处理该表的字段
						$table_field = self::_query('DESC `'.$value.'`;', null, true, $register_backtrace);
						//初始化字段列表。将二维数组转为一个索引数组，只拿出Field字段的值
						if( !empty($table_field) ){
							$table_field = parent::cmd(array($table_field, 'Field'), 'arr indexedvalue');
						}else{
							$table_field = array();
							}
						
						//开始拼凑
						$table_field_string = 'INSERT INTO `'.$value.'` (';
						foreach($table_field as $field){
							$table_field_string .= '`'.$field.'`, ';
							}
						$table_field_string = substr($table_field_string, 0, -2);//去掉最后一个逗号和空格
						$table_field_string .= ") VALUES ";
						
						//读取字段数据
						$string = '';//搜集value值
						foreach($select as $data){
							$string .= "\r\n (";
							foreach($data as $value_string){
								if( $value_string === NULL ){
									$string .= 'NULL, ';
								}else
								if( $value_string == '' ){
									$string .= '\'\', ';
								}else{
									//将\r\n转义,并且将有HTML字符串转换成html标签,以及数据库输入过滤,addslashes()使用反斜线引用字符串
									$value_string = str_replace(array( "\r", "\n"), array( "\\r", "\\n"), $value_string);//换行符替换
									$value_string = parent::cmd(array($value_string), 'str addslashes');
									$string .= '\''.$value_string.'\', ';
									}
								}
							$string = substr($string, 0, -2);//去掉最后一个逗号和空格
							$string .= '), ';
							}
						$table_field_string .= substr($string, 0, -2);//获得(value,value,...),(value,value,...)
						$table_field_string .= ";\r\n\r\n";
						file_put_contents($path, $table_field_string, FILE_APPEND | LOCK_EX);
						
						
						}
					
				}// end while
			}// end foreach
		
		}	
	
	
}
?>