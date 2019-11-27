<?php
namespace framework\src\cmd;
use framework\cao as cao;
final class arr extends cao {
	
	
	
	/**
	 * 替换key键名称
	 * cmd(arr, array( array('key'=>'k','key2'=>'k2') ),'arr key_replace');
	 * 意思是，在arr数组中，存在key键的，那么替换成k。
	 * 
	 * @param  string		$p			前缀
	 * @param  array		$k_a		替换数组
	 * @param  bool			$remove		是否删除前缀
	 * @return array
	 */
	static protected function _key_prefix_($p, $k_a = array(), $remove = false){
		$arr = array();//为了不改变数组的循序
		if( !is_string($p) || empty($p) || !is_array($k_a) || empty($k_a) ){
			$arr = $a;
			return $arr;
			}
		
		//开始替换
		foreach( $k_a as $k_key => $k_value){
			if( empty($remove) ){
				//增加
				$arr[$p.$k_key] = $k_value;
			}else{
				
				//删除
				if( mb_strpos($k_key, $p) === 0 ){
					$arr_key = str_replace($p, "", $k_key);
					$arr[$arr_key] = $k_value;
				}else{
					$arr[$k_key] = $k_value;
				}
				
			}
		}
		
		return $arr;
		}
	
	
	
	
	
	
	
	
	/**
	 * 替换key键名称
	 * cmd(arr, array( array('key'=>'k','key2'=>'k2') ),'arr key_replace');
	 * 意思是，在arr数组中，存在key键的，那么替换成k。
	 * 
	 * @param  array		$a			被替换数组
	 * @param  array		$k_a		替换数组
	 * @return array
	 */
	static protected function _key_replace_($a, $k_a = array()){
		//return array('a','b','c','d');
		//printexit($a, $k_a);
		$arr = array();//为了不改变数组的循序
		if( !is_array($a) || empty($a) || !is_array($k_a) || empty($k_a) ){
			$arr = $a;
			return $arr;
			}
		//开始替换key键名称
		foreach( $a as $key => $value ){
			foreach( $k_a as $k_key => $k_value){
				//判断key是否相同，相同则替换
				if( $k_key == $key ){
					//unset($a[$key]);//删除旧的
					$arr[$k_value] = $value;
					continue 2;
					}
				}
			$arr[$key] = $value;
			}
		return $arr;
		}
	
	
	
	/**
	 * 只返回白名单中的键值单元
	 * 意思是，$w_a 是要被保留的索引数组。其value如果与$a的key相等，则会被保留，其他的则被删除
	 * $type 为false，是判断 $a 的key键名称。为true则是判断 $a 的value值名称。
	 * 
	 * @param  array		$a			被清理的数组
	 * @param  array		$w_a		白名单数组
	 * @return array
	 */
	static protected function _whitelist_($a, $w_a = array(), $type = false){
		//return array("测试","开发");
		if( !is_array($a) || !is_array($w_a) ){
			return $a;
			}
		if( empty($a) || empty($w_a) ){
			return array();
			}
		
		if( empty($type) ){
			foreach( $a as $key => $value ){
				if( !in_array($key, $w_a) ){
					unset($a[$key]);	
					}
				}
		}else{
			foreach( $a as $key => $value ){
				if( !in_array($value, $w_a) ){
					unset($a[$key]);	
					}
				}
			}
		return $a;
		}
	
	
	/**
	 * 删除黑名单中的键值单元
	 * 意思是，$b_a 是要被清理的索引数组。其value如果与$a的key相等，则会被删除
	 * $type 为false，是判断 $a 的key键名称。为true则是判断 $a 的value值名称。
	 * 
	 * @param  array		$a			被清理的数组
	 * @param  array		$b_a		黑名单数组
	 * @return array
	 */
	static protected function _blacklist_($a, $b_a = array(), $type = false){
		if( !is_array($a) || empty($a) || !is_array($b_a) || empty($b_a) ){
			return $a;
			}
		
		if( empty($type) ){
			foreach( $a as $key => $value ){
				if( in_array($key, $b_a) ){
					unset($a[$key]);	
					}
				}
		}else{
			foreach( $a as $key => $value ){
				if( in_array($value, $b_a) ){
					unset($a[$key]);	
					}
				}
			}
		
		return $a;
		}
	
	
	
	
	/**
	 * 将二维数组转换为一维索引数组。关联数组将把所有的value值不分key键的放在一起
	 * 如果'field'为空，那么如果二维存在一维，那么也将会把value值放在一起。
	 * 如果传入字段Key 表示只将二维数组中某一个字段合并成一位数组，其他舍弃
	 * $a = array( 0=>array('name'=>'欢','b','c'), 1=>array('name'=>'和','b','c') );	
	 * $field key名称
	 * 
	 * @param  array		$a			必须是二维数组数据
	 * @param  string		$field		key名称(字段)
	 * @return array					返回一个一维索引数组
	 */
	static protected function _indexedvalue_($a = array(), $field = ''){
		if( empty($a) || !is_array($a) ){
			return $a;
			}
		
		$arr = array();	
		foreach($a as $key => $value){
			if( !empty($field) ){
				if( isset($value[$field]) ){
					$arr[] = $value[$field];
					}
			}else{
				if( is_array($a[$key]) ){
					foreach($a[$key] as $v){
						$arr[] = $v;
						}
				}else{
					$arr[] = $a[$key];
					}
				}
			}
		
		return $arr;
		}
	
	
	
	
	
	
	
}
?>