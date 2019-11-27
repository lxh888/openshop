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



namespace eapie\source\request\administrator;
use eapie\main;
use eapie\error;
class admin_program_error extends \eapie\source\request\administrator {
	
	
	
	/**
	 * 获取程序错误日志列表数据
	 * 
	 * ADMINISTRATORADMINPROGRAMERRORDATA
	 * {"class":"administrator/admin_program_error","method":"api_data"}
	 * 
	 * $data = array(
	 * 	"sort" true 正序，否则默认倒序
	 * )
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_data( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PROGRAM_ERROR_READ);
		
		$error_config = config('error');
		if( empty($error_config['cache_folder_name']) ){
			return array();
		}
		$error_dir = CACHE_PATH.DIRECTORY_SEPARATOR.$error_config['cache_folder_name'];
		if( !is_dir($error_dir) ){
			return array();
		}
		
		//获取文件路径信息
		$backups = cmd(array($error_dir), 'disk file_path');
		//printexit($backups);
		if( empty($data['sort']) ){
			//倒序
			rsort($backups, SORT_NATURAL);//以降序来排序
			}else{
				sort($backups, SORT_NATURAL);
				}
			
		$file_count = 0;
		$file_size = 0;
		$backups_file = array();
		if(!empty($backups)){
			foreach($backups as $key => $value){
				$filemtime = filemtime($error_dir .DIRECTORY_SEPARATOR. $value);
				$backups_file[$key]['filemtime'] = $filemtime;
				$backups_file[$key]['filemtime_format'] = cmd(array($backups_file[$key]['filemtime'], 'Y年m月d日 H:i'), 'time date');
				
				$backups_file[$key]['file_name'] = $value;
				$backups_file[$key]['size'] = filesize($error_dir .DIRECTORY_SEPARATOR. $value);
				$file_size += $backups_file[$key]['size'];
				$file_count ++;
				
				$backups_file[$key]['size_unit'] = cmd(array($backups_file[$key]['size']), 'unit storage');
			}
			
		}
		
		return array(
			//'dir' => $error_dir,
			'count' => $file_count,
			'size' => $file_size,
			'size_unit' => cmd(array($file_size), 'unit storage'),
			'data' => $backups_file
		);	
		
	}
	
	
	
	
			
	/**
	 * 删除日志文件
	 * 
	 * ADMINISTRATORADMINPROGRAMERRORREMOVE
	 * {"class":"administrator/admin_program_error","method":"api_remove"}
	 * 
	 * $data = array(
	 * 	file_name	文件名称
	 * )
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_remove( $data = array() ){
		//检查权限
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PROGRAM_ERROR_REMOVE);
		
		if( !isset($data['file_name']) ){
			throw new error("文件名称不存在");
		}
		if( !is_string($data['file_name']) && !is_numeric($data['file_name']) ){
			throw new error("文件名称的格式不合法");
		}
		
		$error_config = config('error');
		if( empty($error_config['cache_folder_name']) ){
			throw new error("储存目录名称异常");
		}
		$error_file = CACHE_PATH.DIRECTORY_SEPARATOR.$error_config['cache_folder_name'].DIRECTORY_SEPARATOR.$data['file_name'];
		if( !is_file($error_file) ){
			throw new error("文件不存在");
		}
		
		if( unlink($error_file) ){
			//插入操作日志
			object(parent::TABLE_LOG)->insert($data, $data);
			return true;
		}else{
			throw new error("操作失败");
		}
		
	}
	
	
	
	
	
	/**
	 * 查看日志文件详情
	 * 
	 * ADMINISTRATORADMINPROGRAMERRORDETAILS
	 * {"class":"administrator/admin_program_error","method":"api_details"}
	 * 
	 * $data = array(
	 * 	file_name	文件名称
	 * )
	 * 
	 * @param	array	$data
	 * @return 	bool
	 */
	public function api_details( $data = array() ){
		object(parent::REQUEST_ADMIN)->check(parent::AUTHORITY_PROGRAM_ERROR_READ);
		
		if( !isset($data['file_name']) ){
			throw new error("文件名称不存在");
		}
		if( !is_string($data['file_name']) && !is_numeric($data['file_name']) ){
			throw new error("文件名称的格式不合法");
		}
		
		$error_config = config('error');
		if( empty($error_config['cache_folder_name']) ){
			throw new error("储存目录名称异常");
		}
		$error_file = CACHE_PATH.DIRECTORY_SEPARATOR.$error_config['cache_folder_name'].DIRECTORY_SEPARATOR.$data['file_name'];
		if( !is_file($error_file) ){
			throw new error("文件不存在");
		}
		
		$file_contents = file_get_contents($error_file);
		$file_contents = trim($file_contents, '--\r\n');
		$contents_array = explode("--\r\n--", $file_contents);
		$contents_array = array_reverse($contents_array);//倒序
		
		return $contents_array;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>