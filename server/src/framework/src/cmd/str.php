<?php
namespace framework\src\cmd;
use framework\cao as cao;
final class str extends cao {
	
	
	
	
	/**
	 * 如果是字符串可输出，则输出
	 * 
	 * @param	string		...	多个
	 * @return	echo
	 */
	static protected function _echo_(){
		$func_get_args = func_get_args();
		if(!empty($func_get_args)){
			foreach($func_get_args as $value){
				if(is_string($value) || is_numeric($value)){
					echo (string)$value;
				}
			}
		}
	}
	
	
	
		
	/**
     * php自动识别编码，若里面有中文的话，将其转换为 $charset 指定编码
	 * 
     * @param	mixed		$data					数据。
	 * @param	string		$charset				编码。
	 * @param	array		$encoding_list 			字符编码列表。 编码顺序可以由数组或者逗号分隔的列表字符串指定。 
     * @return	mixed
     */	
	static protected function _charset_( $data = NULL, $charset = '', $encoding_list = array() ){
		if( empty($data) ){
			return $data;
			}
		
		//编码为空则获取默认编码。
		if( empty($charset) ){
			$charset = mb_internal_encoding();
		}else{
			$charset = strtoupper($charset);//转为大写
			}
		
		//默认配置
		if( empty($encoding_list) ){
			$encoding_list = array( 
			    'UTF-8', 'GB2312', 'GBK', 'ASCII', 
			    'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5', 
			    'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10', 
			    'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16', 
			    'Windows-1251', 'Windows-1252', 'Windows-1254'
			    );
			}
		
		if( is_array($data) ){
			foreach ($data as $key => $value){
				$data[$key] = self::_charset_( $value );//递归
				}
		}else
		if(is_object($data)){
			//如果是对象	
			foreach($data as $key => $value){
				$data->$key = self::_charset_( $value );//递归
				}	
		}else{
			$encoding_type = mb_detect_encoding($data , $encoding_list);
			if( $encoding_type != $charset){
				$data = mb_convert_encoding($data ,$charset , $encoding_type);
			    }
			}
		
		return $data;
		}
	
	
	
	
	
	
	
	
	/**
	 * [数据过滤]使用反斜线引用字符串 
	 * 单引号（'）、双引号（"）、反斜线（\）与 NUL（ NULL  字符）
	 * 如果 magic_quotes_gpc 为关闭时返回 0，否则返回 1。
	 * 在 PHP 5.4.O 起将始终返回 FALSE 。5.4.0 始终返回 FALSE ，因为这个魔术引号功能已经从 PHP 中移除了。
	 * 如果配置已经打开了字符的转义功能，不用过滤，直接返回。否则需要过滤。
	 * 
	 * @param  mixed	$data
	 * @return mixed
	 */
	static protected function _addslashes_( $data = NULL ){
		if( !isset($data) || $data == '' ){
			return $data;
			}
		if( !get_magic_quotes_gpc() ){
			
			if(is_array($data)){
				//如果是数组
				foreach($data as $key => $value){
					$data[$key] = self::_addslashes_( $value );//递归
					}
			}else
			if(is_object($data)){
				//如果是对象		
				foreach($data as $key => $value){
					$data->$key = self::_addslashes_( $value );//递归
					}
				}else{
					$data = addslashes($data);
					}
			 
			}
		return $data;
		}
	
	
	
	
	/**
	 * [数据反过滤]反引用一个引用字符串
	 * 
	 * @param  mixed	$data
	 * @return mixed
	 */
	static protected function _stripslashes_( $data = NULL ){
		if( !isset($data) || $data == '' ){
			return $data;
			}
		if(is_array($data)){
			//如果是数组
			foreach($data as $key => $value){
				$data[$key] = self::_stripslashes_( $value );//递归
				}
		}else
		if(is_object($data)){
			//如果是对象	
			foreach($data as $key => $value){
				$data->$key = self::_stripslashes_( $value );//递归
				}	
			}else{
				$data = stripslashes($data);
				}
		return $data; 
		}
	
	
	
	
	
	/**
	 * 将字符串转化为大写
	 * 
	 * @param  mixed	$data
	 * @return mixed
	 */
	static protected function _toupper_( $data = NULL ){
		if( !isset($data) || $data == '' ){
			return $data;
			}
		
		if( is_array($data) ){
			//如果是数组
			foreach($data as $key => $value){
				$data[$key] = self::_toupper_( $value );//递归
				}
		}else
		if( is_object($data) ){
			//如果是对象	
			foreach($data as $key => $value){
				$data->$key = self::_toupper_( $value );//递归
				}	
			}else{
				$data = strtoupper($data);
				}
			
		return $data; 
		}
	
		
		
		
	/**
	 * 将字符串转化为小写
	 * 
	 * @param  mixed	$data
	 * @return mixed
	 */
	static protected function _tolower_( $data = NULL ){
		if( !isset($data) || $data == '' ){
			return $data;
			}
		
		if( is_array($data) ){
			//如果是数组
			foreach($data as $key => $value){
				$data[$key] = self::_tolower_( $value );//递归
				}
		}else
		if( is_object($data) ){
			//如果是对象	
			foreach($data as $key => $value){
				$data->$key = self::_tolower_( $value );//递归
				}	
			}else{
				$data = strtolower($data);
				}
			
		return $data; 
		}	
		
		
		
		

	
		
	
	
}
?>