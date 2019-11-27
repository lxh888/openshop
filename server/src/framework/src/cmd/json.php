<?php
namespace framework\src\cmd;
use framework\cao as cao;
final class json extends cao {
	



	
	/**
	 * 对变量进行 JSON 编码
	 * 
	 * @param	string		$data
	 * @param	bool 		$assoc  = true	为真返回数组，否则为假返回对象
	 * @return	mixed
	 */
	static protected function _decode_array_( $data = NULL ){
		if( isset($data) && is_string($data) ){
			$decode = json_decode($data, true);
			if( !empty($decode) && is_array($decode) ){
				return $decode;
			}
		}
		
		return array();
	}
	
	

	
	
	/**
	 * 对变量进行 JSON 编码
	 * 
	 * @param	string		$data
	 * @param	bool 		$assoc  = true	为真返回数组，否则为假返回对象
	 * @return	mixed
	 */
	static protected function _decode_( $data = NULL, $assoc  = true ){
		if( isset($data) && is_string($data) ){
			$decode = json_decode($data, $assoc);
			if( $decode != '' ){
				return $data = $decode;
				}else{
					return $data;
					}
			}else{
				return $data;
				}
		}
	
	
	
	/**
	 * 对变量进行 JSON 编码
	 * 
	 * @param	mixed	$data
	 * @param	int		$options 
	 * @return	string
	 */
	static protected function _encode_( $data = NULL, $options = '' ){
		if( !isset($data) || $data == '' ){
			return $data;
			}
		//要处理有汉字的数据
		if ( version_compare(PHP_VERSION,'5.4.0','>=') ){
			$options = empty($options)? JSON_UNESCAPED_UNICODE : $options;
			$data = json_encode($data, $options);
			}else{
				//小于5.4的版本
				//$data = json_encode($data);
				$data = parent::cmd(array(json_encode( parent::cmd(array($data), 'url encode'))), 'url decode');
				}
		return $data;
		}
	
	
	
}
?>