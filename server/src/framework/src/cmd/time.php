<?php
namespace framework\src\cmd;
use framework\cao as cao;
final class time extends cao {
	
	
	
	
	/*
	$data='时间戳';
	$config=array(
			'format'=>'Y-m-d H:i:s',	格式
			'field'=>NULL,				字段。字段不为空时，传入的数据必须是二维数组
		)
	--------------------------------------
	时间格式。[年月日时分秒]
	时间格式。[年月日]'Y-m-d'
	如果是二维数组。$config['field']Time是Key键，如:logon_time 注册时间data_time($data,'Y-m-d','logon_time')
	如果是字符串或者整型。$config['field']Time为NULL。如:data($data,'Y-m-d',NULL) 或者 data($data,'Y-m-d')
	*/
	
	
	/**
	 * 将时间戳格式化。支持二维数组和字符串、整型等
	 * 如果是二维数组。$field 是Key键
	 * 
	 * @param	mixed				$data
	 * @param	string				$format				格式
	 * @param	string				$field				字段。字段不为空时，传入的数据必须是二维数组
	 * @return	string
	 */
	static protected function _date_( $data = NULL, $format = NULL, $field = NULL ){
		//如果格式为空，那么默认'Y-m-d H:i:s' [年-月-日 时:分:秒]
		if( !isset($format)){
			$format = 'Y-m-d H:i:s';
			} 

		if( is_array($data) && isset($field) ){
			//如果是二维数组：
			$count_data = count($data);
			for( $i = 0; $i < $count_data; $i ++ ){
				//验证是否为数字(是否为时间戳，是，则格式化时间戳)
				if( isset($data[$i][$field]) && is_numeric($data[$i][$field]) ){
					$data[$i][$field] = date($format, $data[$i][$field]);
					} 
			   }
			}else 
		if( is_numeric($data) ){
			//或者是字符串、整数等。验证是否为数字(是否为时间戳，是，则格式化时间戳)
			$data = date($format, $data);
			}

		return $data;
		}

	
	
	
	
	/**
	 * 传入一个时间戳。
	 * 格式化时间戳，刚刚、几分钟前、几小时前、几天前、几月前、几年前
	 * 
	 * @param	mixed				$data
	 * @return	string
	 */
	static protected function _format_( $data = NULL ){
		//intval — 获取变量的整数值
		$string='';
		
		$now_time = time();
		$difference_time = $now_time-$data;
		$difference_day=intval(date("Ymd",$now_time)) - intval(date("Ymd",$data));
		$difference_month=intval(date("m",$now_time)) - intval(date("m",$data));
		$difference_year=intval(date("Y",$now_time)) - intval(date("Y",$data));
		
		if($difference_time<3){
			$string='刚刚';
			}else
		if($difference_time<60){
			$string=$difference_time.'秒前';
			}else
		if($difference_time<3600){
			$string=intval($difference_time/60)."分钟前";
			}else
		if($difference_time>=3600 && $difference_day==0){		
			$string=intval($difference_time/3600)."小时前";
			}else
		if($difference_day==1 && $difference_month==0 && $difference_year==0){
			$string="昨天".date("H:i",$data);
			}else		
		if($difference_day==2 && $difference_month==0 && $difference_year==0){
			$string="前天".date("H:i",$data);
			}else
		if($difference_day>2 && $difference_month==0 && $difference_year==0){
			$string=$difference_day."天前";
			}else	
		if($difference_month==1 && $difference_year==0){
			$string="上个月".date("d日 H:i",$data);
			}else			
		if($difference_month>1 && $difference_year==0){
			$string=$difference_month."个月前";
			}else
		if($difference_year>0){
			$string=$difference_year."年前";
			}
		
		return $string;
		}
	
	
	
	
	
	/**
	 * 传入一个时间戳。
	 * 格式化时间戳，刚刚、几分钟前、几小时前、几天前、几月前、几年前
	 * 那么默认'Y-m-d H:i:s'
	 * $data='2015-02-16';						格式如2015-02-16的字符串 或者 $data='2016-11-23 00:00:00';	
	 * 如：将2015-02-16  转换为精确度为秒的时间戳
	 * 
	 * @param	mixed				$data
	 * @param	string				$ymd_delimiter			年月日界定符 
	 * @param	string				$his_delimiter			时分秒界定符
	 * @return	string
	 */
	static protected function _mktime_($data=NULL, $ymd_delimiter = '-', $his_delimiter = ':'){
		if(empty($data)){
			return $data;
			}
		
		if(!empty($data) && is_string($data)){
			$split_array = preg_split('/\s+/i',$data);
			$ymd_array = explode($ymd_delimiter, $split_array[0]);
			if(!isset($ymd_array[2])){
				return $data;//不合法则直接返回这个数据
				}
			
			if(empty($split_array[1])){
				$his_array[0] = 0;//H
				$his_array[1] = 0;//i
				$his_array[2] = 0;//s
			}else{
				$his_array=explode($his_delimiter, $split_array[1]);
			}
			//printexit($split_array,$ymd_array,$his_array);
			
			//Array([0] => 2016 年[1] => 02 月[2] => 16日) mktime( H 小时 , i 分钟数 , s 秒数 , n 月份 , j 日 ,Y 年份 )
			$data = mktime($his_array[0],$his_array[1],$his_array[2],$ymd_array[1],$ymd_array[2],$ymd_array[0]);
			}
		
		return $data;
		}

	
	
	
	
	
	

	
	/**
	 * 获取今日最初的秒数(今日的0时0分0秒时间戳)
	 * 
	 * @param	mixed				$data					需要获取的时间戳 默认是当前时间戳
	 * @return	string
	 */
	static protected function _day_first_( $data = NULL ){
		if( !isset($data) ){
			$data = time();
			} 
		return mktime(0, 0, 0, date('m',$data), date('d',$data), date('Y',$data));
		}
	
	
	
	/**
	 * 获取今日最后的秒数(今日的23时59分59秒时间戳)
	 * 
	 * @param	mixed				$data					需要获取的时间戳 默认是当前时间戳
	 * @return	string
	 */
	static protected function _day_end_( $data = NULL ){
		if( !isset($data) ){
			$data = time();
			}  
		return mktime(23, 59, 59, date('m',$data), date('d',$data), date('Y',$data));
		}
	





	
	/**
	 * 获取本月的第一天的秒数(当月第一天的0时0分0秒时间戳)
	 * 
	 * @param	mixed				$data					需要获取的时间戳 默认是当前时间戳
	 * @return	string
	 */
	static protected function _month_first_( $data = NULL ){
		if( !isset($data) ){
			$data = time();
			}
		
		return mktime(0, 0, 0, date('m',$data), 1, date('Y',$data));
		}
	
	
	
	
	/**
	 * 获取本月的最后一天的秒数(当月最后一天的23时59分59秒时间戳)
	 * 
	 * @param	mixed				$data					需要获取的时间戳 默认是当前时间戳
	 * @return	string
	 */
	static protected function _month_end_( $data = NULL ){
		if( !isset($data) ){
			$data = time();
			}
		
		return mktime(23, 59, 59, date('m',$data), date('t',$data), date('Y',$data));
		}






	
	/**
	 * 获取本周第一天的秒数(星期一的0时0分0秒时间戳)
	 * 
	 * @param	mixed				$data					需要获取的时间戳 默认是当前时间戳
	 * @return	string
	 */
	static protected function _week_first_( $data = NULL ){
		if( !isset($data) ){
			$data = time();
			}
		//3600 是一个小时，如果是0表示星期7。如今日星期3,则-1天，乘以每天的秒数。减去时间戳就剩下本周第一天的时间戳。最后格式化
		$data = $data - ((date('w', $data) == 0 ? 7 : date('w', $data)) - 1) * 24 * 3600;
		return mktime(0, 0, 0, date('m',$data), date('d',$data), date('Y',$data));
		}
	
	
	
	
	
	/**
	 * 获取本周最后一天的秒数(星期日的23时59分59秒时间戳)
	 * 
	 * @param	mixed				$data					需要获取的时间戳 默认是当前时间戳
	 * @return	string
	 */
	static protected function _week_end_( $data = NULL ){
		if( !isset($data) ){
			$data = time();
			}
		
		//3600 是一个小时，如果是0表示星期7。如今日星期3,则去减以7天，乘以每天的秒数。加上时间戳就获得本周日的时间戳。最后格式化
		$data = $data + (7 - (date('w', $data) == 0 ? 7 : date('w', $data)) ) * 24 * 3600;
		return mktime(23, 59, 59, date('m', $data), date('d', $data), date('Y', $data));
		}





	/**
	 * 获取本年份的第一个月的第一天的秒数(0时0分0秒时间戳)
	 * 
	 * @param	mixed				$data					需要获取的时间戳 默认是当前时间戳
	 * @return	string
	 */
	static protected function _year_first_( $data = NULL ){
		if( !isset($data) ){
			$data = time();
			}
		
		return mktime(0, 0, 0, 1, 1, date('Y', $data));
		}
	
	
	
	
	/**
	 * 获取本年份的最后一个月的最后一天的秒数(23时59分59秒时间戳)
	 * 
	 * @param	mixed				$data					需要获取的时间戳 默认是当前时间戳
	 * @return	string
	 */
	static protected function _year_end_( $data = NULL ){
		if( !isset($data) ){
			$data = time();
			}
		return mktime(23, 59, 59, 12, date('t',$data), date('Y',$data));
		}






	
	
	
	
	
	
	
	
	
	
	
}
?>