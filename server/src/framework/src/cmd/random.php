<?php
namespace framework\src\cmd;
use framework\cao as cao;
final class random extends cao {
	
	
	/* 获取随机数 */
	
		
	/**
	 * 随着时间戳自增的字符串
	 * 其中的长度，并不是返回数据的长度，而是时间戳前面随机数的长度
	 * 
	 * @param  string		$length			随机数长度。默认为0，注意这里的长度不是限制整个长度。只是加在时间戳上的随机数长度
	 * @param  bool			$int			是否是全数字。默认是FALSE,表示不是。否则全是数字
	 * @param  bool			$position		为TRUE时，时间戳在前面。默认是FALSE，时间戳在后面
	 * @return string || int
	 */
	static protected function _autoincrement_( $length = 0, $int = false, $position = false ){
		
		if( !empty($int) ){
			$hash = "012345678901234567890123456789";
		}else{
			$data = '';
			$uniqid = uniqid("",TRUE);
			if( !empty($_SERVER['REQUEST_TIME']) ) $data .= $_SERVER['REQUEST_TIME'];//程序响应时间
			if( !empty($_SERVER['HTTP_USER_AGENT']) ) $data .= $_SERVER['HTTP_USER_AGENT'];//浏览器信息
			if( !empty($_SERVER['REQUEST_URI']) ) $data .= $_SERVER['REQUEST_URI'];//浏览的页面链接
			if( !empty($_SERVER['REMOTE_ADDR']) ) $data .= $_SERVER['REMOTE_ADDR'];//浏览当前页面的用户的 IP 地址
			$hash = hash('ripemd128', $uniqid.md5($data) );
			}
		
		//配置长度
		$data = '';
		if( !empty($length) ){
			for( $i = 0; $i < $length; $i ++ ){
				$data .= mb_substr($hash, mt_rand(0, mb_strlen($hash) - 1), 1);
				}
			}
		
		$microtime = str_replace(".", "", microtime(true));
		//为TRUE时，时间戳在前面。默认是FALSE，时间戳在后面
		if( empty($position) ){
			$data = $data.$microtime;
		}else{
			$data = $microtime.$data;
			}
		
		return $data;
		}
	
	
	
	/**
	 * 获取时间的随机数字。
	 * 超出14位数，后面都是随机数。
	 * 
	 * @param  int	$length 默认是15
	 * @return int
	 */
	static protected function _ymdhis_( $length = 15 ){
		if( !is_numeric( $length ) ){
			$length = 15;
			}
		
		$data = date('YmdHis');//年月日时分秒
		$length_strlen = $length - mb_strlen($data);
		//等于0说明相等
		if( $length_strlen === 0 ){
			return $data;
			}
		
		//获取更少时
		if( $length_strlen < 0 ){
			$data = mb_substr($data, 0, $length_strlen );
			}
		
		//获取更多时
		if( $length_strlen > 0 ){
			//str_shuffle随机地打乱字符串中的所有字符
			$data .= substr(str_shuffle("012345678901234567890123456789"), 0, $length_strlen);
			}
		
		return $data;
		}
	
	
	
	
	
		
	/**
	 * 普通的字符串id
	 * 
	 * @param  string		$length			随机数长度。默认为0，注意这里的长度不是限制整个长度。只是加在时间戳上的随机数长度
	 * @param  bool			$str			其他标示字符串
	 * @return string
	 */
	static protected function _string_( $length = 0, $str = '' ){
		$chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$str;
		//配置长度
		$data = '';
		if( !empty($length) ){
			for( $i=0;$i<$length;$i++ ){
				$data .= mb_substr($chars, mt_rand(0, mb_strlen($chars) - 1), 1);
				}
			}
		return $data;
		}
	
	
	
	
	/**
	 * 获取随机数字
	 * 
	 * @param  int	$length
	 * @return int
	 */
	static protected function _number_( $length = 0 ){
		if( empty($length) ){
			return $length;
			}
			
		//str_shuffle随机地打乱字符串中的所有字符
		return substr(str_shuffle("012345678901234567890123456789"), 0, $length);
		}
	
	
	
		
	/**
	 * 获取uuid
	 * 
	 * @param  string		$length			随机数长度。默认为0，注意这里的长度不是限制整个长度。只是加在时间戳上的随机数长度
	 * @param  bool			$space			设置分隔符。默认是空，不需要
	 * @return string
	 */
	static protected function _uuid_( $length = 0, $space = '-' ){
		$data = '';
		if(!empty($_SERVER['REQUEST_TIME'])) $data.=$_SERVER['REQUEST_TIME'];//程序响应时间
		if(!empty($_SERVER['HTTP_USER_AGENT'])) $data.=$_SERVER['HTTP_USER_AGENT'];//浏览器信息
		if(!empty($_SERVER['REMOTE_PORT'])) $data.=$_SERVER['REMOTE_PORT'];//用户机器上连接到 Web 服务器所使用的端口号
		if( !empty($_SERVER['REMOTE_ADDR']) ) $data .= $_SERVER['REMOTE_ADDR'];//浏览当前页面的用户的 IP 地址
		$hash = hash('ripemd128', uniqid("", TRUE).md5($data) );
		
		$id = '';
		if( !empty($space) ){
			$id = substr($hash,0,8).$space.substr($hash,8,4).$space.substr($hash,12,4).$space.substr($hash,16,4).$space.substr($hash,20,12);
		}else{
			$id = $hash;
			}
		
		return $id;
		}
	
	
	
	
	
	
	
	
	
	
	
	
}
?>