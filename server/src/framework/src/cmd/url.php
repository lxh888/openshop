<?php
namespace framework\src\cmd;
use framework\cao as cao;
final class url extends cao {
	
	
	
	/**
	 * 编码 URL 字符串
	 * 
	 * @param	mixed	$data
	 * @return	mixed
	 */
	static protected function _encode_( $data = NULL ){
		if( !isset($data) || $data == '' ){
			return $data;
			}
		if( is_array($data) ){
			//如果是数组
			foreach ($data as $key => $value){
				$data[$key] = self::_encode_($value);//递归
				}
		}else 
		if(is_object($data)){
			//如果是对象
			foreach($data as $key => $value){
				$data->$key = self::_encode_($value);//递归
				}
			}else{
				$data = urlencode($data);
				}
		return $data;
		}
	
	
	
	/**
	 * 解码已编码的 URL 字符串 
	 * 
	 * @param	mixed		$data
	 * @return	mixed
	 */
	static protected function _decode_( $data = NULL ){
		if( !isset($data) || $data == '' ){
			return $data;
			} 
		if( is_array($data) ){
			//如果是数组
			foreach ($data as $key => $value){
				$data[$key] = self::_decode_($value);//递归
				}
		}else 
		if(is_object($data)){
			//如果是对象
			foreach($data as $key => $value){
				$data->$key = self::_decode_($value);//递归
				}
			}else{
				$data = urldecode($data);
				}
		return $data;
		}
	
	
	
	
		
	
}
?>