<?php
namespace framework\src\cmd;
use framework\cao as cao;
final class html extends cao {
	
	
	
	
	/**
	 * 将特殊的 HTML 实体转换回普通字符 
	 * 
	 * @param 	mixed	$data
	 * @param	bool	$is_htmlentities	是否是所有html编码转义
	 * @return mixed
	 */
	static protected function _decode_( $data = NULL, $is_htmlentities = false ){
		if( !isset($data) || $data == '' ){
			return $data;
			}

		if (is_array($data)){
			//如果是数组
			foreach ($data as $key => $value){
				$data[$key] = self::_decode_($value, $is_htmlentities);//递归
				}
		}else 
		if(is_object($data)){
			//如果是对象
			foreach($data as $key => $value){
				$data->$key = self::_decode_($value, $is_htmlentities);//递归
				}
		}else{
			if( empty($is_htmlentities) ){
				if( !function_exists("htmlspecialchars_decode") ){
					$data = strtr($data, array_flip(get_html_translation_table(HTML_SPECIALCHARS, ENT_COMPAT|ENT_QUOTES |ENT_NOQUOTES |ENT_HTML401 |ENT_XML1 |ENT_XHTML |ENT_HTML5)));
					}else{
						$data = htmlspecialchars_decode($data);
						}
				}else{
					$data = html_entity_decode($data, ENT_COMPAT|ENT_QUOTES |ENT_NOQUOTES |ENT_HTML401 |ENT_XML1 |ENT_XHTML |ENT_HTML5);
					}
			
			}
		return $data;
		}
	
	
	
	
	/**
	 * 将特殊字符转换为HTML实体
	 * 
	 * @param	mixed	$data
	 * @param	bool	$is_htmlentities	是否是所有html编码转义
	 * @return mixed
	 */
	static protected function _encode_( $data = NULL, $is_htmlentities = false ){
		if( !isset($data) || $data == '' ){
			return $data;
			}
		if (is_array($data)){
			//如果是数组
			foreach ($data as $key => $value){
				$data[$key] = self::_encode_($value, $is_htmlentities);//递归
				}
		}else
		if(is_object($data)){
			//如果是对象
			foreach($data as $key => $value){
				$data->$key = self::_encode_($value, $is_htmlentities);//递归
				}
		}else{
			if( empty($is_htmlentities) ){
				$data = htmlspecialchars($data);
				}else{
				$data = htmlentities($data);
					}
			}
		return $data;
		}
	
		
	
}
?>