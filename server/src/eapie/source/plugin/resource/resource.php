<?php
/** ---- eapie ----
 * 优狐积木框架，让开发就像组装积木一样简单！
 * 解放千千万万程序员！这只是1.0版本，后续版本势如破竹！
 * 
 * QQ群：523668865
 * 开源地址 https://gitee.com/lxh888/openshop
 * 官网 http://eonfox.com/
 * 后端框架文档 http://cao.php.eonfox.com
 * 
 * 作者：绵阳市优狐网络科技有限公司
 * 电话/微信：18981181942
 * QQ：294520544
 */



namespace eapie\source\plugin\resource;
class resource {
	
	/* 资源插件 */
	
	
		
	/**
	 * 错误提示
	 * 
	 * @param	int			$errno		错误码
	 * @return	array
	 */
	private function _error( $errno = 1 ) {
		if( !is_numeric($errno) ){
			return array('errno'=>110, 'error'=> $errno);//不是数字，那么就是错误信息
		}
		$return = array();
		switch( $errno ){
			case 1: $return = array('errno'=>1, 'error'=>'资源名称 异常!'); break;
			case 2: $return = array('errno'=>2, 'error'=>'资源不存在!'); break;
			default: $return = array('errno'=>'default', 'error'=>'['.$errno.']未知错误'); break;
			}
		return $return;
	}	
	
	
		
	/**
	 * 成功时，返回的数据
	 */
	private function _success( $data = NULL ){
		return array(
			'errno' => 0,
			'data' => $data
		);
	}
	
	
	
	
	/**
	 * 获取字体文件绝对路径
	 * Undocumented function
	 *
	 * @param string $file_name 文件名称及后缀
	 * @return void
	 */
	public function get_ttf_path($file_name = ""){
		if( empty($file_name) ){
			return $this->_error(1);
		}
		$file_path = __DIR__.'/ttf/'.$file_name;
		if( !is_file($file_path) ){
			return $this->_error(2);
		}
		
		return $this->_success($file_path);
	}
	
	
	
	
	
	
}
?>