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



namespace eapie\source\plugin\qiniu;
use Qiniu\Auth;    					//引入鉴权类
use Qiniu\Config;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;	// 引入上传类
class qiniu {
	
	
	public function  __construct (){
		require_once __DIR__."/autoload.php";
   	}
	
	
	
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
			case 1: $return = array('errno'=>1, 'error'=>'accessKey 异常!'); break;
			case 2: $return = array('errno'=>2, 'error'=>'secretKey 异常!'); break;
			case 3: $return = array('errno'=>3, 'error'=>'bucket 储存空间异常!'); break;
			case 4: $return = array('errno'=>4, 'error'=>'expires 有效时间异常!'); break;
			case 5: $return = array('errno'=>5, 'error'=>'Key 不能为空!'); break;
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
	 * 获取七牛的uptoken
	 * {"id":"accessKey","secret":"secretKey","bucket":"储存空间","expires":"有效时间"}
	 * $config = array(
	 * 		"id"	 =>	"accessKey",
	 * 		"secret" =>	"secretKey",
	 *		"bucket" => 要上传的空间,
	 *		"expires"=> 有效期,
	 * );
	 * 
	 * @param	array	$config
	 * @return UpToken
	 */
	public function uptoken($config = array()){
		if( empty($config["id"]) || !is_string($config["id"]) ){
			return $this->_error(1);
		}
		if( empty($config["secret"]) || !is_string($config["secret"]) ){
			return $this->_error(2);
		}
		// 要上传的空间
		if( empty($config["bucket"]) || !is_string($config["bucket"]) ){
			return $this->_error(3);
		}
		//有效期
		if( empty($config["expires"]) || !is_numeric($config["expires"]) ){
			return $this->_error(4);
		}
		
		$auth = new Auth($config["id"], $config["secret"]);
		
		//自定义返回值
		if( empty($config["policy"]) || !is_array($config["policy"]) ){
			$config["policy"] = null;
		}
        // 生成上传 Token  :	accessKey  secretKey
        $token = $auth->uploadToken($config["bucket"], null, $config["expires"], $config["policy"], true);
		return $this->_success($token);
	}
	
	
	
	
	
	/**
	 * 删除空间中的文件
	 * $config = array(
	 *		"key" => key,
	 * 		"id"	 =>	"accessKey",
	 * 		"secret" =>	"secretKey",
	 *		"bucket" => 上传的空间,
	 * )
	 * 
	 * @param	array	$config
	 * @return	error | null
	 */
	public function delete($config = array()){
		if( empty($config["key"]) || !is_string($config["key"]) ){
			return $this->_error(5);
		}
		
		if( empty($config["id"]) || !is_string($config["id"]) ){
			return $this->_error(1);
		}
		if( empty($config["secret"]) || !is_string($config["secret"]) ){
			return $this->_error(2);
		}
		// 要上传的空间
		if( empty($config["bucket"]) || !is_string($config["bucket"]) ){
			return $this->_error(3);
		}
		
		$auth = new Auth($config["id"], $config["secret"]);
		$bucket_manager = new BucketManager($auth, new Config());
		$e = $bucket_manager->delete($config["bucket"], $config["key"]);
		//更新镜像空间中存储的文件内容
		//$bucket_manager->prefetch($config["bucket"], $config["key"]);
		if( !empty($e) ){
			//612 	指定资源不存在或已被删除。
			if( $e->code() === 612 ){
				return $this->_success(true);//说明已经删除
			}else{
				return $this->_error( $e->message() );
			}
		}else{
			return $this->_success(true);
		}
	}
	
	
	
	
	/**
	 * 上传文件或者字节组上传
	 * 
	 * $config = array(
	 *		"key" => key,
	 * 		"id"	 =>	"accessKey",
	 * 		"secret" =>	"secretKey",
	 *		"bucket" => 上传的空间,
	 * 		"file_path" => 要上传的文件路径
	 * 		"content" => 字节流
	 * 		"binary" => false
	 * )
	 * 
	 * @param	array	$config
	 * @return	error | true
	 */
	public function upload($config = array()){
		$uptoken = $this->uptoken($config);
		if( !empty($uptoken['errno']) ){
			return $uptoken;
		}
		
		// 上传到七牛后保存的文件名
		if( empty($config["key"]) || !is_string($config["key"]) ){
			return $this->_error(5);
		}
		
		// 初始化 UploadManager 对象并进行文件的上传。
		$upload_manager = new UploadManager();
		
		if( empty($config['binary']) ){
			// 要上传文件的本地路径
			if( empty($config['file_path']) || !is_string($config["file_path"]) ){
				return $this->_error("上传文件地址异常");
			}
			
			// 调用 UploadManager 的 putFile 方法进行文件的上传。
			list($ret, $err) = $upload_manager->putFile($uptoken['data'], $config["key"], $config['file_path']);
		}else{
			// 要上传的二进制
			if( empty($config['content']) || !is_string($config["content"]) ){
				return $this->_error("字节组异常");
			}
			
			// 调用 UploadManager 的 putFile 方法进行文件的上传。
			list($ret, $err) = $upload_manager->put($uptoken['data'], $config["key"], $config['content']);
		}
		
		
		if ($err !== null) {
			return $this->_error($err);
		} else {
			return $this->_success($ret);
		}
		
	}
	
	
	
	
	
}
?>