<?php
namespace framework\src\cmd;
use framework\cao as cao;
final class href extends cao {
	
	
	/**
	 * 替换符号
	 * 
	 * @var	array
	 */
	static private $_replace = array(
		'+',
		'/',
		'?',
		'%',
		'#',
		'&',
		'=',
		'-',
		'.',
		':',
		'"',
		'\'',
		'{',
		'}',
		'\\',
		',',
	);
	
	
	/**
	 * 区分符
	 * 
	 * @var	string
	 */
	static private $_div = '_';
	
	
	
	
	/**
	 * 获取符号组
	 * [0] 区分符，[1]替换字符组
	 * 
	 * @param	void
	 * @return array
	 */
	static protected function _get_code_(){
		return array(self::$_div, self::$_replace);
	}
	
	
	
	/**
	 * 编码请求链接
	 * 处理特殊字符,对特殊字符进行编码。
	 * 
	 * @param  string	$data
	 * @return string
	 */
	static protected function _encode_( $data = NULL ){
		if( !isset($data) || !is_string($data) || $data == '' ){
			return $data;
			}
		//$data = urlencode(base64_encode(urlencode($data)));
		//先替换 主符号
		$data = str_replace( self::$_div, self::$_div.'01'.self::$_div, $data );
		foreach(self::$_replace as $key=>$value){
			$data = str_replace( $value, self::$_div.$key.self::$_div, $data );
			}
		
		return $data;
		}
	
	
	
	/**
	 * 解码请求链接
	 * 对处理过的特殊字符进行解码。
	 * 
	 * @param  string	$data
	 * @return string
	 */
	static protected function _decode_( $data = NULL ){
		if( !isset($data) || !is_string($data) || $data == '' ){
			return $data;
			}
		//$data = urldecode(base64_decode(urldecode($data)));
		foreach(self::$_replace as $key=>$value){
			$data = str_replace( self::$_div.$key.self::$_div, $value, $data );
			}
		//最后替换 主符号
		$data = str_replace( self::$_div.'01'.self::$_div, self::$_div, $data );	
		
		return $data;
		}
		
		
		
		
		
	
}
?>