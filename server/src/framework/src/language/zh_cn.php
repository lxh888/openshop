<?php
namespace framework\src\language;
use framework\cao as cao;
final class zh_cn extends cao {
	
	
	/* 中国简体语言版 */
	
	
	/**
	 * 错误级别的信息
	 * 
	 * @param  int	$type
     * @return string
	 */
	static public function error_type_message( $type ){
		switch ( $type ) {
			case E_ERROR:
				$string = '致命性的运行时错误 - E_ERROR';
				break;
			case E_WARNING:
				$string = '运行时警告(非致命性错误) - E_WARNING';
				break;
			case E_PARSE:
				$string = '编译时解析错误 - E_PARSE';
				break;
			case E_NOTICE:
				$string = '运行时提醒 - E_NOTICE';
				break;
			case E_STRICT:
				$string = '编码标准化警告，允许PHP建议如何修改代码以确保最佳的互操作性向前兼容性 - E_STRICT';
				break;
			case E_CORE_ERROR:
				$string = 'PHP启动时初始化过程中的致命错误 - E_CORE_ERROR';
				break;
			case E_CORE_WARNING:
				$string = 'PHP启动时初始化过程中的警告(非致命性错) - E_CORE_WARNING';
				break;
			case E_COMPILE_ERROR:
				$string = '编译时致命性错 - E_COMPILE_ERROR';
				break;
			case E_COMPILE_WARNING:
				$string = '编译时警告(非致命性错) - E_COMPILE_WARNING';
				break;
			case  E_USER_ERROR :
				$string = '用户错误 - E_USER_ERROR';
				break;
			case  E_USER_WARNING :
				$string = '用户错误 - E_USER_WARNING';
				break;
			case  E_USER_NOTICE :
				$string = '用户错误 - E_USER_NOTICE';
				break;
			default:
				$string = '未知错误 - unknown error！';
				break;
			}

		return $string;
		}
	
	
	
	
	/**
	 * 错误标题
	 * 
	 * @param  string	$title
     * @return string
	 */
	static public function error_message_title( $title ){
		$string = $title.' - 操作出错！';
		return $string;		
		}
	
		
	
	
	
	
	/**
	 * 错误标题
	 * 
	 * @param  int	$type
     * @return string
	 */
	static public function error_trigger_title( $title ){
		$string = ( $title == '' ? '未知错误 - unknown error' : $title);
		return $string;
		}
	
	
	
	/**
	 * 错误级别
	 * 
	 * @param  int	$type
     * @return string
	 */
	static public function error_trigger_type( $type ){
		$string = '<strong>错误级别：</strong>'.$type;
		return $string;
		}
	
	
	/**
	 * 错误信息
	 * 
	 * @param  string	$message
     * @return string
	 */
	static public function error_trigger_message( $message ){
		$string = '<strong>错误信息：</strong>'.$message;
		return $string;
		}	
	
	
	/**
	 * 错误位置
	 * 
	 * @param  string	$location
     * @return string
	 */
	static public function error_trigger_location( $location ){
		$string = '<strong>错误位置：</strong>'.$location;
		return $string;
		}		
	
	
	/**
	 * 文件跟踪
	 * 
	 * @param  string	$backtrace
     * @return string
	 */
	static public function error_trigger_backtrace( $backtrace ){
		$string = '<strong>文件跟踪：</strong><pre style="padding: 0 20px;">'.$backtrace.'</pre>';
		return $string;
		}
	
	
	
	/**
	 * 错误时的上下文
	 * 
	 * @param  string	$context
     * @return string
	 */
	static public function error_trigger_context( $context ){
		$string = '<strong>当错误发生时在用的每个变量以及它们的值：</strong><pre style="padding: 0 20px;">'.$context.'</pre>';
		return $string;
		}
	
	
	

	
	
	/**
	 * 请求信息
	 * 
	 * @param	string	$request	链接
     * @return	string
	 */
	static public function error_log_request( $request ){
		$string = '请求信息：'.$request.' - '.date('Y-m-d H:i:s',time());
		return $string;
		}
	
	
	
	/**
	 * 错误标题
	 * 
	 * @param  int	$type
     * @return string
	 */
	static public function error_log_title( $title ){
		$string = '错误标题：'.( $title == '' ? '未知错误 - unknown error' : $title);
		return $string;
		}
	
	
	
	/**
	 * 错误级别
	 * 
	 * @param  int	$type
     * @return string
	 */
	static public function error_log_type( $type ){
		$string = '错误级别：'.$type;
		return $string;
		}
	
	
	/**
	 * 错误信息
	 * 
	 * @param  string	$message
     * @return string
	 */
	static public function error_log_message( $message ){
		$string = '错误信息：'.$message;
		return $string;
		}	
	
	
	/**
	 * 错误位置
	 * 
	 * @param  string	$location
     * @return string
	 */
	static public function error_log_location( $location ){
		$string = '错误位置：'.$location;
		return $string;
		}		
	
	
	/**
	 * 文件跟踪
	 * 
	 * @param  mixed	$file_backtrace
     * @return string
	 */
	static public function error_log_backtrace( $file_backtrace ){
		$string = '文件跟踪：'.(is_string($file_backtrace)? $file_backtrace : print_r($file_backtrace, true));
		return $string;
		}
	
	
	
	/**
	 * 错误时的上下文
	 * 
	 * @param  string	$context
     * @return string
	 */
	static public function error_log_context( $context ){
		$string = '当错误发生时在用的每个变量以及它们的值：'.$context;
		return $string;
		}
	
		
	
	
	
	
	
	
	
	
	
	/**
	 * 输出的html代码。<title><meta>
	 * 
	 * @param  void
     * @return string
	 */
	static public function htmlprint_title(){
		$string = '';
		$string .= '<title>信息打印 - '.FRAMEWORK_VERSION.'</title>';
		$string .= '<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
		<meta name="apple-mobile-app-capable" content="yes">
		<meta name="apple-mobile-app-status-bar-style" content="black">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="author" content="cononico">
		<meta name="application-name" content="Cononico" >
		<meta content="yes" name="apple-mobile-web-app-capable">
		<meta content="yes" name="apple-touch-fullscreen">
		<meta content="telephone=no" name="format-detection">
		<meta content="black" name="apple-mobile-web-app-status-bar-style">
		<style>
		*{font-family:Menlo, Monaco, Consolas, "Courier New", monospace;}
		</style>';
		return $string;
		}
	
	
	
	/**
	 * 输出的html代码。<h2>
	 * 
	 * @param  string	$data
     * @return string
	 */
	static public function htmlprint_h2( $data ){
		$string = '<h2 style="margin:0;padding:20px 20px 10px 20px;word-wrap:break-word;border-bottom:1px solid #CCCCCC;white-space: pre-wrap !important;">'.$data.'</h2>';
		return $string;
		}
	
	
	
	/**
	 * 输出的html代码。<div>
	 * 
	 * @param  string	$data
     * @return string
	 */
	static public function htmlprint_div( $data ){
		$string = '<div style="margin:20px;border:1px solid #CCCCCC;border-radius:10px;-moz-box-shadow: 0px 0px 10px 0px #CCCCCC;-webkit-box-shadow: 0px 0px 10px 0px #CCCCCC;box-shadow: 0px 0px 10px 0px #CCCCCC;white-space: pre-wrap !important;">'.$data.'</div>';
		return $string;
		}
	
	
	
	/**
	 * 输出的html代码。<p>
	 * 
	 * @param  string	$data
     * @return string
	 */
	static public function htmlprint_p( $data ){
		$string = '<p style="padding:0 20px;word-wrap:break-word;white-space: pre-wrap !important;">'.$data.'</p>';
		return $string;
		}
	
	
	/**
	 * 输出的html代码。<footer>
	 * 
	 * @param  void
     * @return string
	 */
	static public function htmlprint_footer(){
		$string = '<div style="postion:relative;bottom:0px;margin:0px;width:100%;text-align:right;"><div style="margin-right:20px;margin-bottom:20px;padding:0;white-space: pre-wrap !important;">'.FRAMEWORK_VERSION.'</div></div>';
		return $string;
		}
	
	
	
	/**
	 * 输出打印。
	 * 
	 * @param  string	$class_name	类名称
	 * @param  string	$class_name	类名称
     * @return string
	 */
	static public function printexit_title( $class_name, $count_args, $file_line ){
		$string = $class_name.' 打印输出,共有 '.$count_args.' 组信息：<br/><p style="margin:10px 0;padding:0;font-size:14px;color:#AAAAAA;font-weight:normal;white-space: pre-wrap !important;">[ 执行路径 - '.$file_line.' ]</p>';
		return $string;
		}
	
	
	
	/**
	 * 输出打印。
	 * 
	 * @param  string	$sort	排序
	 * @param  string	$type	类型
	 * @param  string	$other	其他信息
     * @return string
	 */
	static public function printexit_sort( $sort, $type, $other = '' ){
		$string = '<pre style="padding:0 20px;word-wrap:break-word;"><span style="padding:4px;border-radius:5px;background:#CCCCCC;color:#FFFFFF;font-weight:bold;white-space: pre-wrap !important;">[ 第 '.$sort.' 组 '.$type.' '.$other.' ]</span></pre>';
		return $string;
		}
	
	
	/**
	 * 输出打印。
	 * 
	 * @param  string	$content	数据
     * @return string
	 */
	static public function printexit_content( $content ){
		$string = '<pre style="padding:0 20px;word-wrap:break-word;white-space: pre-wrap !important;">'.$content.'</pre>';
		return $string;
		}
	
	
	/**
	 * 输出执行文件路径及行号位置。
	 * 
	 * @param  string $path	文件路径
	 * @param  int $line	行号
     * @return string
	 */
	static public function debug_file_line( $path, $line ){
		if( $line == '' ){
			$string = $path;
			}else{
				$string = $path.' 所在 '.$line.' 行';
				}
		return $string;
		}
	
	
	/**
	 * 输出执行文件路径及行号位置。
	 * 
	 * @param  string	$path_line	文件路径和行号
	 * @param  string 	$method		方法名称或者函数名称
     * @return string
	 */
	static public function debug_file_backtrace( $path_line, $method ){
		if( $method == '' ){
			$string = $path_line;
			}else{
				$string = '[ '.$path_line.' ] '.$method;
				}
		return $string;
		}
	
	
	

	
	
	
	/**
	 * 对象未定义。
	 * 
	 * @param	string  	$class_name			未处理的类名称
	 * @param	string  	$class_namespace	已处理的类名称
     * @return string
	 */
	static public function object_class_exists($class_name, $class_namespace){
		return $class_name.' 类不合法！( '.$class_namespace.' 类未定义)';
		}
	
	
	
	
	/**
	 * 方法名称不存在
	 * 
	 * @param	void
	 * @return	string
	 */
	static public function func_args_exists(){
		return '方法名称不存在或者不合法！';
		}
	
	
	
	
	
	/**
	 * 方法名称不存在
	 * 
	 * @param	string	$method_name	方法名称类型不合法
	 * @return	string
	 */
	static public function func_args_illegal($method_name){
		return $method_name.' 方法名称不合法！';
		}
	
	
	
	
	/**
	 * 方法名称未定义
	 * 
	 * @param	string	$method_name	方法名称类型不合法
	 * @return	string
	 */
	static public function func_method_exists($method_name){
		return $method_name.' 方法名称未定义！';
		}
	
	
	
	/**
	 * 模板编译，模板内容为空
	 * 
	 * @param	string	$path	模板路径
	 * @return	string
	 */
	static public function template_compile_empty( $path ){
		return $path.' 读取模板文件内容失败！检查模板文件内容是否为空！';
		}
	
	
	
	/**
	 * 模板编译失败
	 * 
	 * @param	string	$php_cache_file_path	缓存文件的路径
	 * @param	string	$path					模板路径
	 * @return	string
	 */
	static public function template_compile_failure( $php_cache_file_path, $path ){
		return $php_cache_file_path. ' 生成编译文件失败！检查模板文件 '.$path;
		}
	
	
	
	/**
	 * 编译目录获取失败
	 * 
	 * @param	string	$php_cache_path			编译目录的路径
	 * @return	string
	 */
	static public function template_cache_folder_name_illegal(){
		return '模板渲染配置所在缓存目录的文件夹名称不合法！';
		}
	
	
	
	/**
	 * 编译目录创建失败
	 * @param	string	$php_cache_path			编译目录的路径
	 * @return	string
	 */
	static public function template_cache_php_mkdir_failure( $php_cache_path ){
		return $php_cache_path." 编译目录创建失败！";
		}
	
	
	
	
	/**
	 * 缓存目录创建失败
	 * @param	string	$html_cache_path		缓存目录的路径
	 * @return	string
	 */
	static public function template_cache_html_mkdir_failure( $html_cache_path ){
		return $html_cache_path." 缓存目录创建失败！";
		}
	
	
	
	
	/**
	 * 模板文件为空了
	 * 
	 * @param	string	void
	 * @return	string
	 */
	static public function template_path_empty(){
		return "模板不能为空！";
		}
	
	
	
	/**
	 * 模板类型出错
	 * 
	 * @param	string	void
	 * @return	string
	 */
	static public function template_path_type(){
		return "模板参数类型必须是字符串！";
		}
	
	
	/**
	 * 模板文件不存在
	 * 
	 * @param	string	$path	模板文件路径
	 * @return	string
	 */
	static public function template_path_exists($path){
		return $path." 模板文件不存在！";
		}
	
	
	
	
	
	/**
	 * 解析类型不能为空
	 * 
	 * @param	void
	 * @return	string
	 */
	static public function template_config_cache_empty(){
		return "请检查模板解析的配置信息，[cache]解析类型不能为空!";
		}
	
	
	
	
	/**
	 * 解析类型参数不合法
	 * 
	 * @param	void
	 * @return	string
	 */
	static public function template_config_cache_illegal(){
		return "请检查模板解析的配置信息，[cache]解析类型参数不合法!";
		}
	
	
	
	/**
	 * 标识不存在。
	 * 
     * @return string
	 */
	static public function db_key_exists(){
		return '标识不存在！需要传入一个合法的标识！';
		}
	
	/**
	 * 对象未定义。
	 * 
	 * @param	string  	$class_name			未处理的类名称
	 * @param	string  	$class_namespace	已处理的类名称
     * @return string
	 */
	static public function db_class_exists($class_name, $class_namespace){
		return $class_name.' 类不合法！( '.$class_namespace.' 类未定义) 检查配置信息的数据库类型是否正确！';
		}
	

	
	/**
	 * 缓存目录的文件夹名称不合法
	 * 
	 * @return string
	 */
	static public function db_cache_folder_name_illegal(){
		return '数据库配置所在缓存目录的文件夹名称不合法！';
	}
	
	
	/**
	 * 缓存目录创建失败
	 * 
	 * @return string
	 */
	static public function db_cache_dir_mkdir($directory){
		return '缓存目录路径 ( '.$directory.' ) 创建失败！';
	}
	
	
	/**
	 * 方法名称不能为空
	 * 
	 * @return string
	 */
	static public function db_call_method_empty(){
		return '方法名称不能为空！';
	}
	
	
	/**
	 * 方法名称不合法
	 * 
	 * @return string
	 */
	static public function db_call_method_illegal($method_name){
		return '方法名称“ '.$method_name.' ”不合法！';
	}
	
	
	
	/**
	 * 数据库连接出错。
	 * 
     * @return string
	 */
	static public function db_connect($errno, $error){
		return '数据库连接失败，请检查数据库配置信息是否正确 - Connect Error('.$errno.') '. $error;
	}
	
	
	
	/**
	 * 数据库名称不合法。
	 * 
     * @return string
	 */
	static public function db_base_exists(){
		return '数据库名称不合法！';
		}	
	
	
	/**
	 * 数据库创建失败
	 * 
	 * @return	string
	 */
	static public function db_create_base($base_name, $error){
		return '初始化连接时，“ '.$base_name.' ” 数据库自动创建失败! - Error( '.$error.' )';
	}
	
	
	/**
	 * 数据库选择错误。
	 * 
     * @return string
	 */
	static public function db_base_select($error){
		return '选择用于数据库查询的默认数据库失败! - Error( '.$error.' )';
	}
	
	
	/**
	 * 字符编码名称不合法。
	 * 
     * @return string
	 */
	static public function db_charset_exists(){
		return '字符编码名称不合法！';
		}
	
	
	/**
	 * 字符编码设置错误。
	 * 
     * @return string
	 */
	static public function db_charset_set($charset, $error){
		return '设置访问数据库的默认字符编码 “ '.$charset.' ” 失败! - Error( '.$error.' )';
	}
	
	/**
	 * 会话初始化失败。
	 * 
     * @return string
	 */
	static public function db_session_start(){
		return '会话初始化失败!';
	}
	
	
	/**
	 * 会话更新失败。
	 * 
     * @return string
	 */
	static public function db_session_update_id_illegal(){
		return '会话更新失败，会话的id不合法!';
	}
	
	
	/**
	 * 会话配置中表名称不合法。
	 * 
     * @return string
	 */
	static public function db_session_table_illegal(){
		return '会话表名称配置不合法！';
		}
	
	/**
	 * 会话表不存在。
	 * 
	 * @param	string		$database_name		数据库名称
	 * @param	string		$table_name			数据表名称
     * @return	string
	 */
	static public function db_session_table_exists($database_name, $table_name){
		return "在 “".$database_name."” 数据库中，会话表 “".$table_name."” 不存在！请手动创建！";
		}
	
	
	/**
	 * 会话表字段不合法
	 * 
	 * @return	string
	 */
	static public function db_session_table_field_illegal(){
		return '表字段列表配置不合法!';
	}
	
	
	/**
	 * 会话表字段缺少必须类型
	 * 
	 * @return	string
	 */
	static public function db_session_table_field_type($field_type){
		return '表字段列表配置中缺少 “'.$field_type.'” 类型的字段！';
	}
	
	
	/**
	 * 会话配置中的初始化id值没有定义方法
	 * 
	 * @return	string
	 */
	static public function db_session_id_define(){
		return '会话配置中的初始化id值没有定义方法！初始化id值的方法是一个闭包函数，检查是否已经定义。';
	}
	
	
	/**
	 * 会话配置中的初始化id值不合法
	 * 
	 * @return	string
	 */
	static public function db_session_id_illegal(){
		return '会话配置中的初始化id值的不合法！初始化id值的方法是一个闭包函数，返回值必须时一个string类型，检查闭包函数的返回值是否正确。';
	}
	
	
	
	/**
	 * 会话配置中的初始化失效时间没有定义方法
	 * 
	 * @return	string
	 */
	static public function db_session_expire_time_define(){
		return '会话配置中的失效时间没有定义方法！初始化失效时间的方法是一个闭包函数，检查是否已经定义。';
	}
	
	
	/**
	 * 会话配置中的失效时间不合法
	 * 
	 * @return	string
	 */
	static public function db_session_expire_time_illegal(){
		return '会话配置中的失效时间不合法！初始化失效时间的方法是一个闭包函数，返回值必须时一个int类型，检查闭包函数的返回值是否正确。';
	}
	
	
	
	/**
	 * 数据库锁表失败，写入文件出错
	 * 
	 * @return	string
	 */
	static public function db_lock_start($path_file){
		return '锁表失败！锁表信息写入 ( '.$path_file.' ) 文件失败!';
	}
	
	
	/**
	 * 数据库锁表文件超时
	 * 
	 * @return	string
	 */
	static public function db_lock_timeout($path_file){
		return '锁表失败！锁表文件 ( '.$path_file.' ) 出现超时情况!';
	}
	
	
	
	/**
	 * 表名称不存在
	 * 
	 * @return	string
	 */
	static public function db_work_table_exists(){
		return '事务操作失败！缺少表 db()::table() 数据！';
	}
	
	
	
	/**
	 * 导入的数据不能为空
	 * 
	 * @return	string
	 */
	static public function db_import_data_empty(){
		return '导入数据失败！SQL数据不能为空！';
	}
	
	
	
	/**
	 * 导入数据的文件地址不合法
	 * 
	 * @return	string
	 */
	static public function db_import_file_illegal(){
		return '导入数据失败！SQL数据的文件地址不合法！';
	}	
	
	
	
	/**
	 * 导入数据的文件不存在
	 * 
	 * @return	string
	 */
	static public function db_import_file_exists(){
		return '导入数据失败！SQL数据的文件不存在！';
	}	
	
	
	
	
	
	
	/**
	 * 导出的文件路径不能为空
	 * 
	 * @return	string
	 */
	static public function db_export_path_empty(){
		return '导出数据失败！储存SQL文件的路径地址不能为空！';
	}
	
	
	/**
	 * 导出的文件路径目录创建出错
	 * 
	 * @return	string
	 */
	static public function db_export_path_mkdir($directory){
		return '导出数据失败！创建储存目录 ( '.$directory.' ) 失败！';
	}
	
	
	/**
	 * 导出的数据备份类型不能为空
	 * 
	 * @return	string
	 */
	static public function db_export_type_empty(){
		return '导出数据失败！数据备份类型不能为空！';
	}
	
	
	/**
	 * 导出的数据备份类型不合法
	 * 
	 * @return	string
	 */
	static public function db_export_type_illegal($type){
		return '导出数据失败！数据备份类型名称( '.$type.' )不合法！';
	}
	
	
	
	
	
	/**
	 * 运行时间
	 * 
     * @return string
	 */
	static public function debug_runtime(){
		return '运行时间';
		}
	
	
	/**
	 * 分配给 PHP 的内存量 
	 * 
     * @return string
	 */
	static public function debug_memory_get_usage(){
		return '分配给PHP的内存量';
		}
	
	
	/**
	 * 分配给 PHP 的真实内存量 
	 * 
     * @return string
	 */
	static public function debug_memory_get_usage_true(){
		return '分配给PHP的真实内存量';
		}
	
	
	
	/**
	 * 分配给 PHP 的内存峰值
	 * 
     * @return string
	 */
	static public function debug_memory_get_peak_usage(){
		return '分配给PHP的内存峰值';
		}
	
	
	/**
	 * 分配给 PHP 的真实内存峰值
	 * 
     * @return string
	 */
	static public function debug_memory_get_peak_usage_true(){
		return '分配给PHP的真实内存峰值';
		}
	
	
	/**
	 * 当前的PHP版本
	 * 
     * @return string
	 */
	static public function debug_phpversion(){
		return '当前PHP版本';
		}
	
	
	/**
	 * 被包含的文件
	 * 
     * @return string
	 */
	static public function debug_get_included_files(){
		return '被包含文件';
		}
	
	/**
	 * 被包含的文件大小
	 * 
     * @return string
	 */
	static public function debug_get_included_files_size(){
		return '被包含文件大小';
		}
	
	
	
	
	
	
}
?>