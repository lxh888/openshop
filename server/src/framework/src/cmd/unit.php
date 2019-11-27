<?php
namespace framework\src\cmd;
use framework\cao as cao;
final class unit extends cao {
	
	
	/*处理单位数据的工具类*/
	/*长度、面积、体积、质量、温度、压力、功率...*/
	
	
	/**
     * 格式化存储单位。
	 * $size=0;		//字节数
     * $config=array(
	 * 'byte'=>'B',		字节
	 * 'KByte'=>'KB',		千字节
	 * 'MByte'=>'M',		兆
	 * 'GByte'=>'G',
	 * 'TByte'=>'TB',
	 * 'PByte'=>'PB'
	 * )
	 * 
     * @param  int				$size
	 * @param  array			$config
     * @return	string
     */ 	
	static protected function _storage_( $size = 0, $config=array() ){
		if( empty($size) ){
			return $size;
			}
		
		$unit = array();//单位
		$unit[0] = empty($config['byte'])? 'byte' : $config['byte'];
		$unit[1] = empty($config['KByte'])? 'KByte' : $config['KByte'];
		$unit[2] = empty($config['MByte'])? 'MByte' : $config['MByte'];
		$unit[3] = empty($config['GByte'])? 'GByte' : $config['GByte'];
		$unit[4] = empty($config['TByte'])? 'TByte' : $config['TByte'];
		$unit[5] = empty($config['PByte'])? 'PByte' : $config['PByte'];
		//round()对浮点数进行四舍五入。pow()指数表达式。返回 base 的 exp 次方的幂。如果可能，本函数会返回 integer 
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i]; 		
		}
	
	
	
	
	
	
	
}
?>